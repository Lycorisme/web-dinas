<?php
// dashboard/api/filter-data.php
require_once '../../helper/connection.php';
header('Content-Type: application/json');

$type = $_GET['type'] ?? '';
$wilayah = $_GET['wilayah'] ?? '';
$jenjang = $_GET['jenjang'] ?? '';

try {
    switch ($type) {
        case 'wilayah':
            $sql = "SELECT DISTINCT kec.nama_kecamatan 
                    FROM kecamatan kec 
                    INNER JOIN sekolah_identitas s ON kec.id_kecamatan = s.id_kecamatan_fk 
                    WHERE kec.nama_kecamatan IS NOT NULL AND kec.nama_kecamatan != ''
                    ORDER BY kec.nama_kecamatan";
            break;
            
        case 'jenjang':
            $where_conditions = [];
            $params = [];
            $types = '';
            
            if ($wilayah) {
                $where_conditions[] = "kec.nama_kecamatan = ?";
                $params[] = $wilayah;
                $types .= 's';
            }
            
            $where_clause = !empty($where_conditions) ? "AND " . implode(" AND ", $where_conditions) : "";
            
            $sql = "SELECT DISTINCT s.jenjang_pendidikan 
                    FROM sekolah_identitas s 
                    LEFT JOIN kecamatan kec ON s.id_kecamatan_fk = kec.id_kecamatan 
                    WHERE s.jenjang_pendidikan IS NOT NULL AND s.jenjang_pendidikan != '' 
                    $where_clause
                    ORDER BY s.jenjang_pendidikan";
            break;
            
        case 'status':
            $where_conditions = [];
            $params = [];
            $types = '';
            
            if ($wilayah) {
                $where_conditions[] = "kec.nama_kecamatan = ?";
                $params[] = $wilayah;
                $types .= 's';
            }
            
            if ($jenjang) {
                $where_conditions[] = "s.jenjang_pendidikan = ?";
                $params[] = $jenjang;
                $types .= 's';
            }
            
            $where_clause = !empty($where_conditions) ? "AND " . implode(" AND ", $where_conditions) : "";
            
            $sql = "SELECT DISTINCT s.status_sekolah 
                    FROM sekolah_identitas s 
                    LEFT JOIN kecamatan kec ON s.id_kecamatan_fk = kec.id_kecamatan 
                    WHERE s.status_sekolah IS NOT NULL AND s.status_sekolah != '' 
                    $where_clause
                    ORDER BY s.status_sekolah";
            break;
            
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
        $data[] = array_values($row)[0];
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