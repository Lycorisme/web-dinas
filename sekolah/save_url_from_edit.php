<?php
header('Content-Type: application/json');
require_once '../helper/connection.php';

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Method not allowed');
    }
    
    $input = json_decode(file_get_contents('php://input'), true);
    $url = trim($input['url'] ?? '');
    $description = trim($input['description'] ?? '');
    $npsn = trim($input['npsn'] ?? '');
    $existing_url_id = intval($input['existing_url_id'] ?? 0);
    
    // Validate inputs
    if (empty($url)) {
        throw new Exception('URL tidak boleh kosong');
    }
    
    if (empty($npsn)) {
        throw new Exception('NPSN tidak valid');
    }
    
    // Validate URL format
    if (!filter_var($url, FILTER_VALIDATE_URL)) {
        throw new Exception('Format URL tidak valid');
    }
    
    // Validate domain
    $parsed_url = parse_url($url);
    if ($parsed_url['host'] !== 'dapo.kemendikdasmen.go.id') {
        throw new Exception('URL harus dari domain dapo.kemendikdasmen.go.id');
    }
    
    // Escape strings for SQL
    $escaped_url = mysqli_real_escape_string($connection, $url);
    $escaped_description = mysqli_real_escape_string($connection, $description);
    $escaped_npsn = mysqli_real_escape_string($connection, $npsn);
    
    if ($existing_url_id > 0) {
        // Update existing URL
        
        // Check if URL already exists for other records
        $check_query = mysqli_query($connection, 
            "SELECT id FROM scraping_urls 
             WHERE url = '$escaped_url' AND id != $existing_url_id");
        
        if (mysqli_num_rows($check_query) > 0) {
            throw new Exception('URL ini sudah digunakan untuk sekolah lain');
        }
        
        // Update the existing record
        $update_query = "UPDATE scraping_urls 
                        SET url = '$escaped_url', 
                            description = '$escaped_description',
                            updated_at = NOW()
                        WHERE id = $existing_url_id";
        
        if (mysqli_query($connection, $update_query)) {
            echo json_encode([
                'success' => true,
                'message' => 'URL berhasil diperbarui'
            ]);
        } else {
            throw new Exception('Gagal memperbarui URL: ' . mysqli_error($connection));
        }
        
    } else {
        // Create new URL
        
        // Check if URL already exists
        $check_query = mysqli_query($connection, 
            "SELECT id FROM scraping_urls WHERE url = '$escaped_url'");
        
        if (mysqli_num_rows($check_query) > 0) {
            throw new Exception('URL ini sudah ada di database');
        }
        
        // Insert new URL
        $insert_query = "INSERT INTO scraping_urls (url, description, status) 
                        VALUES ('$escaped_url', '$escaped_description', 'active')";
        
        if (mysqli_query($connection, $insert_query)) {
            echo json_encode([
                'success' => true,
                'message' => 'URL berhasil disimpan'
            ]);
        } else {
            throw new Exception('Gagal menyimpan URL: ' . mysqli_error($connection));
        }
    }
    
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>