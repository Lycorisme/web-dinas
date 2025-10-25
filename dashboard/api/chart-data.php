<?php
// dashboard/api/chart-data.php
require_once '../../helper/connection.php';
header('Content-Type: application/json');

$type = $_GET['type'] ?? '';
$jenjang = $_GET['jenjang'] ?? '';
$status = $_GET['status'] ?? '';
$wilayah = $_GET['wilayah'] ?? '';

try {
    // Build WHERE clause
    $where_conditions = [];
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

    $where_clause = empty($where_conditions) ? "" : "WHERE " . implode(" AND ", $where_conditions);

    switch ($type) {
        case 'ptk':
            $sql = "SELECT 
                        SUM(r.guru) as total_guru,
                        SUM(r.tendik) as total_tendik,
                        s.jenjang_pendidikan
                    FROM rekap_ptk_pd r
                    LEFT JOIN sekolah_identitas s ON r.npsn_fk = s.npsn
                    LEFT JOIN kecamatan kec ON s.id_kecamatan_fk = kec.id_kecamatan
                    $where_clause
                    GROUP BY s.jenjang_pendidikan
                    ORDER BY s.jenjang_pendidikan";
            break;
            
        case 'pd':
            $sql = "SELECT 
                        SUM(r.jumlah_laki_laki) as total_laki,
                        SUM(r.jumlah_perempuan) as total_perempuan,
                        s.jenjang_pendidikan
                    FROM rekap_rombel r
                    LEFT JOIN sekolah_identitas s ON r.npsn_fk = s.npsn
                    LEFT JOIN kecamatan kec ON s.id_kecamatan_fk = kec.id_kecamatan
                    $where_clause
                    GROUP BY s.jenjang_pendidikan
                    ORDER BY s.jenjang_pendidikan";
            break;
            
        case 'stats':
            // Query untuk statistik dengan filter
            $stats_sql = [
                'sekolah' => "SELECT COUNT(DISTINCT s.npsn) as total FROM sekolah_identitas s LEFT JOIN kecamatan kec ON s.id_kecamatan_fk = kec.id_kecamatan $where_clause",
                'ptk' => "SELECT COALESCE(SUM(p.ptk_total), 0) as total FROM rekap_ptk_pd p LEFT JOIN sekolah_identitas s ON p.npsn_fk = s.npsn LEFT JOIN kecamatan kec ON s.id_kecamatan_fk = kec.id_kecamatan $where_clause",
                'siswa' => "SELECT COALESCE(SUM(r.jumlah_total), 0) as total FROM rekap_rombel r LEFT JOIN sekolah_identitas s ON r.npsn_fk = s.npsn LEFT JOIN kecamatan kec ON s.id_kecamatan_fk = kec.id_kecamatan $where_clause",
                'rombel' => "SELECT COUNT(r.id) as total FROM rekap_rombel r LEFT JOIN sekolah_identitas s ON r.npsn_fk = s.npsn LEFT JOIN kecamatan kec ON s.id_kecamatan_fk = kec.id_kecamatan $where_clause"
            ];
            
            $stats = [];
            foreach ($stats_sql as $key => $query) {
                if (!empty($params)) {
                    $stmt = mysqli_prepare($connection, $query);
                    mysqli_stmt_bind_param($stmt, $types, ...$params);
                    mysqli_stmt_execute($stmt);
                    $result = mysqli_stmt_get_result($stmt);
                } else {
                    $result = mysqli_query($connection, $query);
                }
                $row = mysqli_fetch_assoc($result);
                $stats['total_' . $key] = $row['total'];
            }
            
            echo json_encode([
                'success' => true,
                'data' => [$stats]
            ]);
            return;
            
        default:
            throw new Exception('Invalid type parameter');
    }

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

    $data = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $data[] = $row;
    }

    echo json_encode([
        'success' => true,
        'data' => $data
    ]);

} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}
?>