<?php
// FILE: sekolah/run_scraper.php
header('Content-Type: application/json; charset=utf-8');
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Set timeout yang lebih pendek untuk response ke browser
set_time_limit(30);
ini_set('max_execution_time', 30);

require_once '../helper/connection.php';

// Mulai session dan cek user_id
session_start();
if (!isset($_SESSION['login']['id'])) {
    echo json_encode([
        'success' => false,
        'message' => 'Sesi tidak valid. Silakan login kembali.'
    ]);
    exit();
}
$user_id = $_SESSION['login']['id'];

$response = ['success' => false, 'message' => 'Terjadi kesalahan tidak diketahui.'];
$logId = 0;

try {
    // 1. Pastikan koneksi DB valid
    if (!isset($connection) || !$connection instanceof mysqli) {
        throw new Exception('Koneksi database tidak valid.');
    }

    // 2. Baca input JSON dari browser
    $input = json_decode(file_get_contents('php://input'), true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        throw new Exception('Input JSON tidak valid: ' . json_last_error_msg());
    }

    $mode = $input['mode'] ?? 'all';
    $selectedUrlIds = $input['urls'] ?? [];

    // 3. Validasi dan tentukan ID URL yang akan diproses
    $urlIdsToProcess = [];
    $batchName = '';

    if ($mode === 'selected' && !empty($selectedUrlIds)) {
        // Ambil URL pilihan (global, tanpa user_id)
        $placeholders = implode(',', array_fill(0, count($selectedUrlIds), '?'));
        $stmt = $connection->prepare("SELECT id FROM scraping_urls WHERE id IN ($placeholders) AND status = 'active'");
        $types = str_repeat('i', count($selectedUrlIds));
        $params = array_map('intval', $selectedUrlIds);
        $stmt->bind_param($types, ...$params);
        $stmt->execute();
        $result = $stmt->get_result();

        while ($row = $result->fetch_assoc()) {
            $urlIdsToProcess[] = $row['id'];
        }
        $stmt->close();

        if (empty($urlIdsToProcess)) {
            throw new Exception('Tidak ada URL yang dipilih yang valid atau aktif.');
        }

        $batchName = 'Update Pilihan - ' . count($urlIdsToProcess) . ' URL';

    } else {
        // Ambil semua URL aktif (global, tanpa user_id)
        $result = $connection->query("SELECT id FROM scraping_urls WHERE status = 'active' LIMIT 100");
        if (!$result) {
            throw new Exception('Gagal mengambil URL dari database: ' . $connection->error);
        }

        while ($row = $result->fetch_assoc()) {
            $urlIdsToProcess[] = $row['id'];
        }
        $batchName = 'Update Semua URL Aktif';
    }

    if (empty($urlIdsToProcess)) {
        throw new Exception('Tidak ada URL aktif yang ditemukan untuk diproses.');
    }

    // 4. Pastikan scraping_logs.id adalah AUTO_INCREMENT
    $checkTableQuery = "SHOW COLUMNS FROM scraping_logs WHERE Field = 'id'";
    $result = $connection->query($checkTableQuery);
    if (!$result) {
        throw new Exception('Gagal mengecek struktur tabel scraping_logs: ' . $connection->error);
    }
    $columnInfo = $result->fetch_assoc();
    $result->close();

    if (strpos($columnInfo['Extra'], 'auto_increment') === false) {
        $alterQuery = "ALTER TABLE scraping_logs MODIFY COLUMN id INT(11) NOT NULL AUTO_INCREMENT";
        if (!$connection->query($alterQuery)) {
            throw new Exception('Gagal memperbaiki struktur tabel scraping_logs: ' . $connection->error);
        }
    }

    // 5. Buat log baru
    $totalUrls = count($urlIdsToProcess);
    $urlIdsJson = json_encode($urlIdsToProcess);

    $stmt = $connection->prepare(
        "INSERT INTO scraping_logs (user_id, batch_name, total_urls, url_ids, status, started_at) 
         VALUES (?, ?, ?, ?, 'running', NOW())"
    );
    if (!$stmt) {
        throw new Exception('Gagal menyiapkan query insert log: ' . $connection->error);
    }
    $stmt->bind_param('isis', $user_id, $batchName, $totalUrls, $urlIdsJson);
    if (!$stmt->execute()) {
        throw new Exception('Gagal membuat log proses di database: ' . $stmt->error);
    }
    $logId = $connection->insert_id;
    $stmt->close();

    // 6. Cari Python
    $possiblePythonPaths = [
        'C:\\Users\\Antimateri\\AppData\\Local\\Programs\\Python\\Python313\\python.exe',
        'C:\\Python313\\python.exe',
        'C:\\Program Files\\Python313\\python.exe',
        'python.exe',
        'python3.exe',
        'python'
    ];
    $pythonPath = null;
    foreach ($possiblePythonPaths as $path) {
        if (file_exists($path) || in_array($path, ['python.exe', 'python3.exe', 'python'])) {
            $output = shell_exec(escapeshellarg($path) . ' --version 2>&1');
            if ($output && strpos(strtolower($output), 'python') !== false) {
                $pythonPath = $path;
                break;
            }
        }
    }
    if (!$pythonPath) {
        throw new Exception('Python executable tidak ditemukan.');
    }

    // Path ke skrip
    $scraperScriptPath = realpath(__DIR__ . '/../main_scraper.pyw');
    if (!$scraperScriptPath || !file_exists($scraperScriptPath)) {
        throw new Exception('Skrip scraper Python tidak ditemukan.');
    }

    // 7. Jalankan Python
    $command = sprintf(
        'start /B "" %s %s --log_id=%d --user_id=%d',
        escapeshellarg($pythonPath),
        escapeshellarg($scraperScriptPath),
        $logId,
        $user_id
    );

    if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
        pclose(popen($command, 'r'));
        sleep(1);
    } else {
        exec($command . " > /dev/null 2>&1 &");
    }

    // 8. Verifikasi log
    $stmt = $connection->prepare("SELECT status FROM scraping_logs WHERE id = ? AND user_id = ?");
    $stmt->bind_param('ii', $logId, $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $logData = $result->fetch_assoc();
    $stmt->close();

    if ($logData && $logData['status'] === 'running') {
        $response['success'] = true;
        $response['message'] = 'Proses scraping telah dimulai di latar belakang.';
        $response['log_id'] = $logId;
        $response['total_urls'] = $totalUrls;
        $response['python_path'] = $pythonPath;
        $response['script_path'] = $scraperScriptPath;
    } else {
        throw new Exception('Proses Python gagal dimulai.');
    }

} catch (Exception $e) {
    http_response_code(500);
    $response['message'] = $e->getMessage();

    if ($logId > 0 && isset($connection) && $connection instanceof mysqli) {
        $stmt = $connection->prepare(
            "UPDATE scraping_logs SET status = 'failed', error_message = ?, completed_at = NOW() WHERE id = ? AND user_id = ?"
        );
        if ($stmt) {
            $stmt->bind_param('sii', $response['message'], $logId, $user_id);
            $stmt->execute();
            $stmt->close();
        }
    }
}

if (isset($connection) && $connection instanceof mysqli) {
    $connection->close();
}

ob_clean();
echo json_encode($response, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
exit;
