<?php
// FILE: sekolah/check_scraping_progress.php - (TETAP SAMA)
header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-cache, must-revalidate');
header('Access-Control-Allow-Origin: *');

error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once '../helper/connection.php';

try {
    if (!isset($connection) || !$connection) {
        throw new Exception('Koneksi database gagal.');
    }

    $log_id = intval($_GET['log_id'] ?? 0);
    
    if ($log_id == 0) {
        $stmt = $connection->prepare("SELECT * FROM scraping_logs WHERE status = 'running' ORDER BY started_at DESC LIMIT 1");
    } else {
        $stmt = $connection->prepare("SELECT * FROM scraping_logs WHERE id = ?");
        $stmt->bind_param("i", $log_id);
    }
    
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows == 0) {
        if ($log_id == 0) {
             echo json_encode(['success' => true, 'status' => 'not_running', 'message' => 'Tidak ada proses yang berjalan.']);
             exit;
        }
        throw new Exception('Log dengan ID ' . $log_id . ' tidak ditemukan');
    }
    
    $log = $result->fetch_assoc();
    $stmt->close();

    $response = [
        'success' => true,
        'log_id' => intval($log['id']),
        'batch_name' => $log['batch_name'] ?? '',
        'total_urls' => intval($log['total_urls']),
        'processed_urls' => intval($log['processed_urls']),
        'success_count' => intval($log['success_count']),
        'failed_count' => intval($log['failed_count']),
        'status' => $log['status'] ?? 'unknown',
        'started_at' => $log['started_at'],
        'completed_at' => $log['completed_at'],
        'error_message' => $log['error_message'],
        'progress_percentage' => $log['total_urls'] > 0 ? round((intval($log['processed_urls']) / intval($log['total_urls'])) * 100, 2) : 0
    ];

    if ($log['status'] === 'completed' && $log['started_at'] && $log['completed_at']) {
        $start = new DateTime($log['started_at']);
        $end = new DateTime($log['completed_at']);
        $duration = $start->diff($end);
        
        $response['duration_formatted'] = sprintf('%02d:%02d:%02d', 
            ($duration->days * 24) + $duration->h,
            $duration->i,
            $duration->s
        );
    }

    echo json_encode($response, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage(),
        'error_type' => get_class($e)
    ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
} finally {
    if (isset($connection) && $connection instanceof mysqli) {
        $connection->close();
    }
}
?>