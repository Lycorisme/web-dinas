<?php
// FILE: sekolah/run_scraper.php - VERSI BARU YANG LEBIH BAIK DAN STABIL
header('Content-Type: application/json; charset=utf-8');
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once '../helper/connection.php';

$response = ['success' => false, 'message' => 'Terjadi kesalahan tidak diketahui.'];
$logId = 0; // Inisialisasi logId

try {
    // 1. Pastikan koneksi DB valid
    if (!isset($connection) || !$connection instanceof mysqli) {
        throw new Exception('Koneksi database tidak valid.');
    }

    // 2. Baca input JSON dari browser
    $input = json_decode(file_get_contents('php://input'), true);
    $mode = $input['mode'] ?? 'all';
    $selectedUrlIds = $input['urls'] ?? [];

    // 3. Tentukan ID URL yang akan diproses
    $urlIdsToProcess = [];
    if ($mode === 'selected' && !empty($selectedUrlIds)) {
        // Gunakan ID yang dipilih oleh pengguna
        $urlIdsToProcess = array_map('intval', $selectedUrlIds);
        $batchName = 'Update Pilihan - ' . count($urlIdsToProcess) . ' URL';
    } else {
        // Ambil SEMUA URL aktif dari database
        $result = $connection->query("SELECT id FROM scraping_urls WHERE status = 'active'");
        while ($row = $result->fetch_assoc()) {
            $urlIdsToProcess[] = $row['id'];
        }
        $batchName = 'Update Semua URL Aktif';
    }

    if (empty($urlIdsToProcess)) {
        throw new Exception('Tidak ada URL yang dipilih atau URL aktif tidak ditemukan.');
    }

    // 4. Buat log baru di database, simpan daftar ID URL dalam format JSON
    $totalUrls = count($urlIdsToProcess);
    $urlIdsJson = json_encode($urlIdsToProcess);

    $stmt = $connection->prepare(
        "INSERT INTO scraping_logs (batch_name, total_urls, url_ids, status, started_at) VALUES (?, ?, ?, 'running', NOW())"
    );
    $stmt->bind_param('sis', $batchName, $totalUrls, $urlIdsJson);
    
    if (!$stmt->execute()) {
        throw new Exception('Gagal membuat log proses di database: ' . $stmt->error);
    }
    $logId = $connection->insert_id;
    $stmt->close();

    // 5. Konfigurasi path ke Python dan skrip scraper
    $pythonPath = 'C:\\Users\\Antimateri\\AppData\\Local\\Programs\\Python\\Python313\\python.exe';
    $scraperScriptPath = realpath(__DIR__ . '/../main_scraper.pyw'); // Disesuaikan untuk .pyw di root

    if (!file_exists($pythonPath)) {
        throw new Exception('File eksekusi Python tidak ditemukan di: ' . $pythonPath);
    }
    if (!$scraperScriptPath || !file_exists($scraperScriptPath)) {
        throw new Exception('Skrip scraper Python tidak ditemukan.');
    }

    // 6. Jalankan skrip Python di latar belakang dengan argumen --log_id
    $command = sprintf(
        '%s %s --log_id=%d',
        escapeshellarg($pythonPath),
        escapeshellarg($scraperScriptPath),
        $logId
    );

    if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
        // Windows: Jalankan di background tanpa jendela konsol
        pclose(popen("start /B " . $command, "r"));
    } else {
        // Linux/Mac: Jalankan di background
        shell_exec($command . " > /dev/null 2>&1 &");
    }

    // 7. Kirim respons sukses ke browser
    // Ini akan "melepaskan" popup dan memulai progress bar
    $response['success'] = true;
    $response['message'] = 'Proses scraping telah dimulai di latar belakang.';
    $response['log_id'] = $logId;

} catch (Exception $e) {
    http_response_code(500);
    $response['message'] = $e->getMessage();
    
    // Jika eror terjadi setelah log dibuat, update log menjadi 'failed'
    if ($logId > 0 && isset($connection) && $connection instanceof mysqli) {
        $stmt = $connection->prepare("UPDATE scraping_logs SET status = 'failed', error_message = ? WHERE id = ?");
        $errorMessage = $e->getMessage();
        $stmt->bind_param('si', $errorMessage, $logId);
        $stmt->execute();
        $stmt->close();
    }
}

// Selalu tutup koneksi dan kirim respons JSON
if (isset($connection) && $connection instanceof mysqli) {
    $connection->close();
}
echo json_encode($response, JSON_PRETTY_PRINT);