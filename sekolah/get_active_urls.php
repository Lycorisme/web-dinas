<?php
// FILE: sekolah/get_active_urls.php
header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-cache, must-revalidate');

require_once '../helper/connection.php';

$response = [
    'success' => false,
    'message' => 'Terjadi kesalahan tidak diketahui.',
    'urls' => []
];

try {
    if (!isset($connection) || !$connection instanceof mysqli) {
        throw new Exception('Koneksi database tidak valid.');
    }

    $sql = "SELECT id, url, description FROM scraping_urls WHERE status = ? ORDER BY id ASC";
    $stmt = $connection->prepare($sql);
    if (!$stmt) {
        throw new Exception('Gagal menyiapkan statement: ' . $connection->error);
    }
    
    $status = 'active';
    $stmt->bind_param('s', $status);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $urls = [];
    while ($row = $result->fetch_assoc()) {
        $urls[] = [
            'id' => (int)$row['id'],
            'url' => trim($row['url']),
            'description' => !empty($row['description']) ? trim($row['description']) : "URL #{$row['id']}"
        ];
    }
    
    $stmt->close();
    
    $response['success'] = true;
    $response['urls'] = $urls;
    $response['total_count'] = count($urls);
    $response['message'] = count($urls) > 0 ? 'Berhasil memuat ' . count($urls) . ' URL aktif.' : 'Tidak ada URL aktif yang ditemukan.';

} catch (Exception $e) {
    http_response_code(500);
    $response['message'] = $e->getMessage();
}

if (isset($connection) && $connection instanceof mysqli) {
    $connection->close();
}

echo json_encode($response, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
exit;