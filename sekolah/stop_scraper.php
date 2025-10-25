<?php
// FILE: sekolah/stop_scraper.php - UPDATED WITH LOG_ID HANDLING
header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-cache, must-revalidate');

error_reporting(E_ALL);
ini_set('display_errors', 1);

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

 $response = [
    'success' => false,
    'message' => 'Gagal memproses permintaan.'
];

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        http_response_code(405); // Method Not Allowed
        throw new Exception('Metode permintaan tidak valid. Harusnya POST.');
    }
    
    if (!isset($connection) || !$connection instanceof mysqli) {
        throw new Exception('Koneksi database tidak valid.');
    }

    $input = json_decode(file_get_contents('php://input'), true);

    if (json_last_error() !== JSON_ERROR_NONE) {
        http_response_code(400);
        throw new Exception('Data JSON tidak valid: ' . json_last_error_msg());
    }

    // Jika log_id tidak dikirim atau 0, cari proses yang sedang running
    $logId = 0;
    if (isset($input['log_id']) && is_numeric($input['log_id']) && $input['log_id'] > 0) {
        $logId = intval($input['log_id']);
    } else {
        // Cari proses yang sedang running untuk user ini
        $stmt_find = $connection->prepare("SELECT id FROM scraping_logs WHERE status = 'running' AND user_id = ? ORDER BY started_at DESC LIMIT 1");
        $stmt_find->bind_param('i', $user_id);
        $stmt_find->execute();
        $result = $stmt_find->get_result();
        if ($running_process = $result->fetch_assoc()) {
            $logId = $running_process['id'];
        }
        $stmt_find->close();
        
        if ($logId === 0) {
            throw new Exception('Tidak ada proses scraping yang sedang berjalan.');
        }
    }
    
    $stmt_get = $connection->prepare("SELECT pid, status FROM scraping_logs WHERE id = ? AND user_id = ?");
    $stmt_get->bind_param('ii', $logId, $user_id);
    $stmt_get->execute();
    $result = $stmt_get->get_result();
    $logData = $result->fetch_assoc();
    $stmt_get->close();

    if (!$logData) {
        throw new Exception('Log proses tidak ditemukan dengan ID: ' . $logId);
    }
    
    if ($logData['status'] !== 'running') {
        $response['success'] = true;
        $response['message'] = 'Proses sudah tidak berjalan (status: ' . $logData['status'] . ').';
        echo json_encode($response, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        exit;
    }

    // UPDATE status di database menjadi 'cancelled'
    $stmt_update = $connection->prepare("UPDATE scraping_logs SET status = 'cancelled', completed_at = NOW(), error_message = 'Proses dibatalkan oleh pengguna.' WHERE id = ? AND user_id = ? AND status = 'running'");
    $stmt_update->bind_param('ii', $logId, $user_id);
    $stmt_update->execute();
    
    if ($stmt_update->affected_rows > 0) {
        $response['success'] = true;
        $response['message'] = 'Status proses berhasil diubah menjadi dibatalkan.';

        // Coba hentikan proses fisik jika PID ada
        if ($logData && array_key_exists('pid', $logData) && !empty($logData['pid'])) {
            $pid = $logData['pid'];
            if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
                @shell_exec("taskkill /F /PID " . $pid . " > nul 2>&1");
            } else {
                @shell_exec("kill -9 " . $pid . " > /dev/null 2>&1");
            }
            $response['message'] .= ' Proses dibatalkan.';
        }

    } else {
        $response['message'] = 'Gagal memperbarui status, proses mungkin sudah selesai.';
    }
    
    $stmt_update->close();

} catch (Exception $e) {
    if (http_response_code() === 200) {
        http_response_code(500);
    }
    $response['message'] = $e->getMessage();
}

if (isset($connection) && $connection instanceof mysqli) {
    $connection->close();
}

echo json_encode($response, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
exit;