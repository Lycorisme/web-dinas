<?php
// FILE: sekolah/stop_scraper.php - KODE INI SUDAH BENAR SETELAH DATABASE DIPERBAIKI
header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-cache, must-revalidate');

error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once '../helper/connection.php';

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

    if (!isset($input['log_id']) || !is_numeric($input['log_id'])) {
        http_response_code(400);
        throw new Exception('Log ID tidak valid atau tidak ditemukan.');
    }
    
    $logId = intval($input['log_id']);
    
    $stmt_get = $connection->prepare("SELECT pid, status FROM scraping_logs WHERE id = ?");
    $stmt_get->bind_param('i', $logId);
    $stmt_get->execute();
    $result = $stmt_get->get_result();
    $logData = $result->fetch_assoc();
    $stmt_get->close();

    if (!$logData) {
        throw new Exception('Log proses tidak ditemukan.');
    }
    
    if ($logData['status'] !== 'running') {
        $response['success'] = true;
        $response['message'] = 'Proses sudah tidak berjalan (status: ' . $logData['status'] . ').';
        echo json_encode($response, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        exit;
    }

    // UPDATE status di database menjadi 'cancelled'. Ini akan berhasil setelah Anda menjalankan ALTER TABLE.
    $stmt_update = $connection->prepare("UPDATE scraping_logs SET status = 'cancelled', completed_at = NOW(), error_message = 'Proses dibatalkan oleh pengguna.' WHERE id = ? AND status = 'running'");
    $stmt_update->bind_param('i', $logId);
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