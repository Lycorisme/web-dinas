<?php
// FILE: sekolah/get_scraping_urls.php
header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-cache, must-revalidate');

require_once '../helper/connection.php';

$response = [
    'success' => false,
    'message' => 'Terjadi kesalahan tidak diketahui.',
    'data' => []
];

try {
    if (!isset($connection) || !$connection instanceof mysqli) {
        throw new Exception('Koneksi database tidak valid.');
    }

    $kecamatan_name = isset($_GET['kecamatan_name']) ? mysqli_real_escape_string($connection, $_GET['kecamatan_name']) : '';
    $search = isset($_GET['search']) ? mysqli_real_escape_string($connection, $_GET['search']) : '';

    // Query untuk mendapatkan data scraping_urls dengan informasi sekolah dan kecamatan_scrape
    $query = "
        SELECT 
            su.id,
            su.url,
            su.description,
            ss.npsn,
            ss.nama_sekolah,
            ks.nama_kecamatan,
            ks.kode_kecamatan,
            ks.id as kecamatan_scrape_id
        FROM 
            scraping_urls su
        LEFT JOIN 
            sekolah_scrape ss ON su.sekolah_scrape_id = ss.id
        LEFT JOIN 
            kecamatan_scrape ks ON ss.kecamatan_scrape_id = ks.id
        WHERE 
            su.status = 'active'
    ";

    // Filter berdasarkan kecamatan_scrape jika dipilih
    if (!empty($kecamatan_name) && $kecamatan_name !== '0') {
        $query .= " AND ks.nama_kecamatan = '$kecamatan_name'";
    }

    // Filter berdasarkan kata kunci pencarian
    if (!empty($search)) {
        $query .= " AND (ss.nama_sekolah LIKE '%$search%' OR ss.npsn LIKE '%$search%' OR su.description LIKE '%$search%')";
    }

    // Urutkan berdasarkan nama sekolah
    $query .= " ORDER BY ss.nama_sekolah ASC";

    // Debug query (untuk pengembangan, bisa dihapus nanti)
    // error_log("Query: " . $query);

    $result = mysqli_query($connection, $query);

    if (!$result) {
        throw new Exception('Query error: ' . mysqli_error($connection));
    }

    $data = [];
    while ($row = mysqli_fetch_assoc($result)) {
        // Jika tidak ada nama sekolah, gunakan deskripsi sebagai gantinya
        if (empty($row['nama_sekolah']) && !empty($row['description'])) {
            $row['nama_sekolah'] = $row['description'];
        }
        
        // Jika tidak ada NPSN, beri nilai default
        if (empty($row['npsn'])) {
            $row['npsn'] = 'N/A';
        }
        
        // Jika tidak ada kecamatan, beri nilai default
        if (empty($row['nama_kecamatan'])) {
            $row['nama_kecamatan'] = 'Tidak diketahui';
        }
        
        $data[] = $row;
    }

    $response['success'] = true;
    $response['data'] = $data;
    $response['message'] = count($data) > 0 ? 'Berhasil memuat ' . count($data) . ' sekolah.' : 'Tidak ada sekolah yang ditemukan.';
    $response['debug_query'] = $query; // Untuk debugging, bisa dihapus nanti

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