<?php
// FILE: sekolah/get_kecamatan_options.php
header('Content-Type: application/json; charset=utf-8');

require_once '../helper/connection.php';

$response = [
    'success' => false,
    'message' => 'Terjadi kesalahan tidak diketahui.',
    'options' => []
];

try {
    // Ambil data dari tabel kecamatan_scrape
    $query = "SELECT DISTINCT nama_kecamatan FROM kecamatan_scrape ORDER BY nama_kecamatan ASC";
    $result = mysqli_query($connection, $query);
    
    if (!$result) {
        throw new Exception('Query error: ' . mysqli_error($connection));
    }
    
    $options = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $options[] = [
            'id' => $row['nama_kecamatan'],
            'text' => $row['nama_kecamatan']
        ];
    }
    
    $response['success'] = true;
    $response['options'] = $options;
    $response['message'] = 'Berhasil memuat ' . count($options) . ' kecamatan.';
    
} catch (Exception $e) {
    http_response_code(500);
    $response['message'] = $e->getMessage();
}

if (isset($connection) && $connection instanceof mysqli) {
    $connection->close();
}

echo json_encode($response, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
exit;
?>