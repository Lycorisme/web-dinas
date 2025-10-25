<?php
require_once '../helper/connection.php';
header('Content-Type: application/json');

try {
    if (!$connection) throw new Exception('Database connection failed');

    $id_kabupaten = isset($_GET['id_kabupaten']) ? intval($_GET['id_kabupaten']) : 0;
    $id_kecamatan = isset($_GET['id_kecamatan']) ? intval($_GET['id_kecamatan']) : 0;

    $query = "SELECT 
              si.npsn, si.nama_sekolah, si.jenjang_pendidikan, si.status_sekolah,
              si.alamat_jalan, k.nama_kecamatan, si.lintang, si.bujur,
              COALESCE(rpd.pd_total, 0) as total_siswa,
              COALESCE(rpd.ptk_total, 0) as total_ptk
            FROM sekolah_identitas si
            LEFT JOIN kecamatan k ON si.id_kecamatan_fk = k.id_kecamatan
            LEFT JOIN rekap_ptk_pd rpd ON si.npsn = rpd.npsn_fk
            WHERE si.lintang IS NOT NULL AND si.bujur IS NOT NULL AND si.lintang != 0 AND si.bujur != 0";

    if ($id_kecamatan > 0) {
        $query .= " AND si.id_kecamatan_fk = $id_kecamatan";
    } elseif ($id_kabupaten > 0) {
        $query .= " AND k.id_kabupaten_fk = $id_kabupaten";
    }

    $result = mysqli_query($connection, $query);
    if (!$result) throw new Exception('Database query failed: ' . mysqli_error($connection));

    $schools = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $schools[] = ['npsn' => $row['npsn'], 'nama' => $row['nama_sekolah'], 'jenjang' => $row['jenjang_pendidikan'], 'status' => $row['status_sekolah'], 'lat' => floatval($row['lintang']), 'lng' => floatval($row['bujur']), 'alamat' => $row['alamat_jalan'], 'kecamatan' => $row['nama_kecamatan'], 'siswa' => intval($row['total_siswa']), 'ptk' => intval($row['total_ptk'])];
    }

    echo json_encode(['success' => true, 'data' => $schools, 'count' => count($schools)]);

} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage(), 'count' => 0]);
}
?>