<?php
header('Content-Type: application/json');
require_once '../helper/connection.php';

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Method tidak diizinkan');
    }
    
    $input = json_decode(file_get_contents('php://input'), true);
    $id = $input['id'] ?? 0;
    
    if (empty($id)) {
        throw new Exception('ID URL tidak valid');
    }
    
    // Check if URL exists
    $check_query = "SELECT id, url FROM scraping_urls WHERE id = ?";
    $check_stmt = mysqli_prepare($connection, $check_query);
    mysqli_stmt_bind_param($check_stmt, 'i', $id);
    mysqli_stmt_execute($check_stmt);
    $result = mysqli_stmt_get_result($check_stmt);
    
    if (mysqli_num_rows($result) === 0) {
        throw new Exception('URL tidak ditemukan');
    }
    
    // Delete the URL
    $delete_query = "DELETE FROM scraping_urls WHERE id = ?";
    $delete_stmt = mysqli_prepare($connection, $delete_query);
    mysqli_stmt_bind_param($delete_stmt, 'i', $id);
    
    if (mysqli_stmt_execute($delete_stmt)) {
        if (mysqli_stmt_affected_rows($delete_stmt) > 0) {
            echo json_encode([
                'success' => true,
                'message' => 'URL berhasil dihapus'
            ]);
        } else {
            throw new Exception('Gagal menghapus URL');
        }
    } else {
        throw new Exception('Error database: ' . mysqli_error($connection));
    }
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>