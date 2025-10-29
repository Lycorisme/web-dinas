<?php
// FILE: sekolah/run_scraper.php - VERSI REFACTOR (Konsisten v4 - Fix Venv & SQL)
header('Content-Type: application/json; charset=utf-8');
error_reporting(E_ALL);
ini_set('display_errors', 1);
set_time_limit(30); 
ini_set('max_execution_time', 30);

require_once '../helper/connection.php'; // Path ke koneksi DB

// --- Fungsi Helper dari import_handler.php (disalin) ---
function getPythonPath() {
    $venvPath = __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR .
                'venv' . DIRECTORY_SEPARATOR . 'Scripts' . DIRECTORY_SEPARATOR . 'python.exe';
    if (file_exists($venvPath)) return realpath($venvPath);
    return 'python'; // Fallback
}

function getChromeDriverPath() {
    $driverPath = __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR .
                  'driver' . DIRECTORY_SEPARATOR . 'chromedriver.exe';
    if (file_exists($driverPath)) return realpath($driverPath);
    return null;
}

function executeBackgroundCommand($pythonPath, $scriptRelativePath, $args) { 
    if (strtoupper(substr(PHP_OS, 0, 3)) !== 'WIN') {
        throw new Exception("Metode eksekusi ini hanya untuk Windows.");
    }
    $projectRoot = realpath(__DIR__ . '/..');
    $scriptAbsolutePath = $projectRoot . DIRECTORY_SEPARATOR . $scriptRelativePath;
    if (!file_exists($scriptAbsolutePath)) {
         throw new Exception("Script Python tidak ditemukan di: " . $scriptAbsolutePath);
    }

    $batchFile = sys_get_temp_dir() . '\\run_main_scraper_' . time() . '_' . rand(1000,9999) . '.bat';
    $logOutput = $projectRoot . '\\log_main_scraper_output.log'; 
    $cmdArgs = [];
    foreach ($args as $arg) { $cmdArgs[] = escapeshellarg($arg); }
    $argString = implode(' ', $cmdArgs);
    
    // === PERBAIKAN VENV DI SINI ===
    $batchContent = "@echo off\n";
    $batchContent .= "echo Activating venv... >> \"$logOutput\" 2>&1\n";
    $venvActivateScript = realpath($projectRoot . '\\venv\\Scripts\\activate.bat'); 
    if ($venvActivateScript) {
        $batchContent .= "call \"$venvActivateScript\" >> \"$logOutput\" 2>&1\n"; // Gunakan 'call'
    } else {
        $batchContent .= "echo WARNING: venv activate script not found! >> \"$logOutput\" 2>&1\n";
    }
    $batchContent .= "echo Running script... >> \"$logOutput\" 2>&1\n";
    $batchContent .= "cd /d \"$projectRoot\"\n"; 
    $batchContent .= "\"$pythonPath\" \"$scriptRelativePath\" $argString >> \"$logOutput\" 2>&1\n"; 
    $batchContent .= "echo Script finished. >> \"$logOutput\" 2>&1\n"; 
    // === AKHIR PERBAIKAN VENV ===
    
    if (file_put_contents($batchFile, $batchContent) === false) {
        throw new Exception("Gagal membuat file batch sementara.");
    }
    $exec = "start /B cmd /C \"$batchFile\"";

    $logFile = $projectRoot . '\\log_php_exec_main.log'; 
    $logContent = date('[Y-m-d H:i:s] ') . "=== EXECUTING MAIN SCRAPER ===" . PHP_EOL;
    $logContent .= "Batch File: $batchFile" . PHP_EOL;
    $logContent .= "Command: $exec" . PHP_EOL;
    $logContent .= "Args: " . $argString . PHP_EOL; 
    $logContent .= "Batch Content:" . PHP_EOL . $batchContent . PHP_EOL;
    $logContent .= "================================" . PHP_EOL;
    file_put_contents($logFile, $logContent, FILE_APPEND);

    pclose(popen($exec, "r"));
    sleep(2);
    return true;
}
// --- Akhir Fungsi Helper ---


session_start();
if (!isset($_SESSION['login']['id'])) {
    echo json_encode(['success' => false, 'message' => 'Sesi tidak valid.']);
    exit();
}
$user_id = $_SESSION['login']['id'];

$response = ['success' => false, 'message' => 'Terjadi kesalahan.'];
$logId = 0; 

try {
    if (!isset($connection) || !$connection instanceof mysqli) {
        throw new Exception('Koneksi database tidak valid.');
    }

    $input = json_decode(file_get_contents('php://input'), true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        throw new Exception('Input JSON tidak valid: ' . json_last_error_msg());
    }

    $mode = $input['mode'] ?? 'all'; 
    $selectedUrlIds = $input['urls'] ?? []; 

    $urlIdsToProcess = [];
    $batchName = '';

    if ($mode === 'selected' && !empty($selectedUrlIds)) {
        $selectedUrlIds = array_filter(array_map('intval', $selectedUrlIds));
        if (empty($selectedUrlIds)) throw new Exception('Tidak ada ID URL valid yang dipilih.');
        
        $placeholders = implode(',', array_fill(0, count($selectedUrlIds), '?'));
        $stmt = $connection->prepare("SELECT id FROM scraping_urls WHERE id IN ($placeholders) AND status = 'active'");
        if(!$stmt) throw new Exception("Prepare statement gagal: ".$connection->error);
        $types = str_repeat('i', count($selectedUrlIds));
        $stmt->bind_param($types, ...$selectedUrlIds);
        $stmt->execute();
        $result = $stmt->get_result();
        while($row = $result->fetch_assoc()){ $urlIdsToProcess[] = $row['id']; }
        $stmt->close();
        
        if (empty($urlIdsToProcess)) throw new Exception('ID URL yang dipilih tidak ditemukan atau tidak aktif.');
        $batchName = 'Update Pilihan - ' . count($urlIdsToProcess) . ' URL';
        
    } else { // Mode 'all'
        $result = $connection->query("SELECT id FROM scraping_urls WHERE status = 'active' ORDER BY id ASC"); 
        if (!$result) throw new Exception('Gagal query URL aktif: ' . $connection->error);
        while ($row = $result->fetch_assoc()) { $urlIdsToProcess[] = $row['id']; }
        $batchName = 'Update Semua URL Aktif (' . count($urlIdsToProcess) . ' URL)';
    }

    if (empty($urlIdsToProcess)) {
        throw new Exception('Tidak ada URL aktif yang ditemukan untuk diproses.');
    }

    $totalUrls = count($urlIdsToProcess);
    $urlIdsJson = json_encode($urlIdsToProcess); 

    // === PERBAIKAN SQL INSERT (HAPUS updated_at) ===
    $stmt = $connection->prepare(
        "INSERT INTO scraping_logs (user_id, batch_name, total_urls, url_ids, status, started_at) 
         VALUES (?, ?, ?, ?, 'running', NOW())"
    );
    if (!$stmt) throw new Exception('Gagal prepare insert log: ' . $connection->error);
    
    $stmt->bind_param('isis', $user_id, $batchName, $totalUrls, $urlIdsJson);
    if (!$stmt->execute()) throw new Exception('Gagal insert log: ' . $stmt->error);
    // === AKHIR PERBAIKAN SQL INSERT ===
    
    $logId = $connection->insert_id; 
    $stmt->close();

    $python_path = getPythonPath();
    if (!$python_path || !file_exists($python_path)) throw new Exception("Python venv tidak ditemukan di " . $python_path);
    
    $driver_path = getChromeDriverPath();
    if (!$driver_path) throw new Exception("ChromeDriver tidak ditemukan di folder 'driver'.");
    
    $script_path_relative = 'main_scraper.pyw'; // Nama skrip di root

    $args = [
        "--log_id=" . $logId,
        "--user_id=" . intval($user_id),
        "--driver_path=" . $driver_path
    ];

    $success_exec = executeBackgroundCommand($python_path, $script_path_relative, $args);

    if (!$success_exec) {
        throw new Exception("Gagal menjalankan skrip scraper di background.");
    }

    $response['success'] = true;
    $response['message'] = 'Proses scraping detail sekolah telah dimulai.';
    $response['log_id'] = $logId; 
    $response['total_urls'] = $totalUrls;

} catch (Exception $e) {
    http_response_code(500);
    $response['success'] = false; 
    $response['message'] = $e->getMessage();

    if ($logId > 0 && isset($connection) && $connection instanceof mysqli) {
        $stmt = $connection->prepare(
            "UPDATE scraping_logs SET status = 'failed', error_message = ?, completed_at = NOW() 
             WHERE id = ? AND user_id = ?"
        );
        if ($stmt) {
            $errorMsg = $response['message'];
            $stmt->bind_param('sii', $errorMsg, $logId, $user_id);
            $stmt->execute();
            $stmt->close();
        } else {
             error_log("Gagal prepare update log status ke failed: " . $connection->error);
        }
    } else {
         error_log("Log ID tidak valid atau koneksi DB bermasalah saat handle error.");
    }
} finally {
    if (isset($connection) && $connection instanceof mysqli) {
        $connection->close();
    }
}

ob_clean(); 
echo json_encode($response, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
exit;
?>