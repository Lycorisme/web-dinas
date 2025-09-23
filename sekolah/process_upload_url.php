<?php
header('Content-Type: application/json');
require_once '../helper/connection.php';

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Method tidak diizinkan');
    }
    
    $urls = $_POST['urls'] ?? [];
    
    if (empty($urls)) {
        throw new Exception('Tidak ada URL yang dikirim');
    }
    
    $inserted_count = 0;
    $duplicate_count = 0;
    $error_count = 0;
    $errors = [];
    
    // Start transaction
    mysqli_autocommit($connection, false);
    
    foreach ($urls as $index => $url_data) {
        $url = trim($url_data['url'] ?? '');
        $description = trim($url_data['description'] ?? '');
        
        // Skip empty URLs
        if (empty($url)) {
            continue;
        }
        
        // Validate URL format
        if (!filter_var($url, FILTER_VALIDATE_URL)) {
            $errors[] = "URL #{$index}: Format tidak valid";
            $error_count++;
            continue;
        }
        
        // Check if URL already exists
        $check_query = "SELECT id FROM scraping_urls WHERE url = ?";
        $check_stmt = mysqli_prepare($connection, $check_query);
        mysqli_stmt_bind_param($check_stmt, 's', $url);
        mysqli_stmt_execute($check_stmt);
        $check_result = mysqli_stmt_get_result($check_stmt);
        
        if (mysqli_num_rows($check_result) > 0) {
            $duplicate_count++;
            continue;
        }
        
        // Insert new URL
        $insert_query = "INSERT INTO scraping_urls (url, description, status, created_at, updated_at) VALUES (?, ?, 'active', NOW(), NOW())";
        $insert_stmt = mysqli_prepare($connection, $insert_query);
        mysqli_stmt_bind_param($insert_stmt, 'ss', $url, $description);
        
        if (mysqli_stmt_execute($insert_stmt)) {
            $inserted_count++;
        } else {
            $errors[] = "URL #{$index}: Gagal menyimpan - " . mysqli_error($connection);
            $error_count++;
        }
    }
    
    // Commit transaction
    mysqli_commit($connection);
    mysqli_autocommit($connection, true);
    
    $message = "{$inserted_count} URL berhasil disimpan";
    if ($duplicate_count > 0) {
        $message .= ", {$duplicate_count} URL duplikat diabaikan";
    }
    if ($error_count > 0) {
        $message .= ", {$error_count} URL gagal disimpan";
    }
    
    echo json_encode([
        'success' => true,
        'message' => $message,
        'inserted_count' => $inserted_count,
        'duplicate_count' => $duplicate_count,
        'error_count' => $error_count,
        'errors' => $errors
    ]);
    
} catch (Exception $e) {
    // Rollback on error
    mysqli_rollback($connection);
    mysqli_autocommit($connection, true);
    
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>