<?php
// dashboard/api/map-data.php
require_once '../../helper/connection.php';
header('Content-Type: application/json');

$jenjang = $_GET['jenjang'] ?? '';
$status = $_GET['status'] ?? '';
$wilayah = $_GET['wilayah'] ?? '';

try {
    $where_conditions = [
        "s.lintang IS NOT NULL", 
        "s.bujur IS NOT NULL",
        "s.lintang != '0.0'",
        "s.bujur != '0.0'"
    ];
    $params = [];
    $types = '';

    if ($jenjang) {
        $where_conditions[] = "s.jenjang_pendidikan = ?";
        $params[] = $jenjang;
        $types .= 's';
    }

    if ($status) {
        $where_conditions[] = "s.status_sekolah = ?";
        $params[] = $status;
        $types .= 's';
    }

    if ($wilayah) {
        $where_conditions[] = "kec.nama_kecamatan = ?";
        $params[] = $wilayah;
        $types .= 's';
    }

    $where_clause = "WHERE " . implode(" AND ", $where_conditions);

    $sql = "SELECT 
                s.npsn,
                s.nama_sekolah,
                s.jenjang_pendidikan,
                s.status_sekolah,
                s.lintang,
                s.bujur,
                s.alamat_jalan,
                kec.nama_kecamatan,
                COALESCE(COUNT(r.id), 0) as jumlah_rombel,
                COALESCE(SUM(r.jumlah_total), 0) as total_siswa,
                COALESCE(SUM(p.ptk_total), 0) as total_ptk
            FROM sekolah_identitas s
            LEFT JOIN kecamatan kec ON s.id_kecamatan_fk = kec.id_kecamatan
            LEFT JOIN rekap_rombel r ON s.npsn = r.npsn_fk
            LEFT JOIN rekap_ptk_pd p ON s.npsn = p.npsn_fk
            $where_clause
            GROUP BY s.npsn, s.nama_sekolah, s.jenjang_pendidikan, s.status_sekolah, 
                     s.lintang, s.bujur, s.alamat_jalan, kec.nama_kecamatan";

    if (!empty($params)) {
        $stmt = mysqli_prepare($connection, $sql);
        mysqli_stmt_bind_param($stmt, $types, ...$params);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
    } else {
        $result = mysqli_query($connection, $sql);
    }
    
    if (!$result) {
        throw new Exception('Database query failed: ' . mysqli_error($connection));
    }

    $schools = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $schools[] = [
            'npsn' => $row['npsn'],
            'nama' => $row['nama_sekolah'],
            'jenjang' => $row['jenjang_pendidikan'],
            'status' => $row['status_sekolah'],
            'lat' => floatval($row['lintang']),
            'lng' => floatval($row['bujur']),
            'alamat' => $row['alamat_jalan'],
            'kecamatan' => $row['nama_kecamatan'],
            'rombel' => intval($row['jumlah_rombel']),
            'siswa' => intval($row['total_siswa']),
            'ptk' => intval($row['total_ptk'])
        ];
    }

    echo json_encode([
        'success' => true,
        'data' => $schools,
        'count' => count($schools)
    ]);

} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage(),
        'count' => 0
    ]);
}
?>