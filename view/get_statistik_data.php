<?php
require_once '../helper/connection.php';
header('Content-Type: application/json');

$id_kabupaten = isset($_GET['id_kabupaten']) ? intval($_GET['id_kabupaten']) : 0;
$id_kecamatan = isset($_GET['id_kecamatan']) ? intval($_GET['id_kecamatan']) : 0;

$whereClause = "";
$joinClause = " JOIN kecamatan k ON si.id_kecamatan_fk = k.id_kecamatan";

if ($id_kecamatan > 0) {
    $whereClause = " WHERE si.id_kecamatan_fk = $id_kecamatan ";
} elseif ($id_kabupaten > 0) {
    $whereClause = " WHERE k.id_kabupaten_fk = $id_kabupaten ";
}

// 1. Statistik Umum
$totalSekolah = mysqli_fetch_assoc(mysqli_query($connection, "SELECT COUNT(si.npsn) as total FROM sekolah_identitas si $joinClause $whereClause"))['total'] ?? 0;

$rekapQuery = "SELECT SUM(rpd.pd_total) as total_siswa, SUM(rpd.ptk_total) as total_guru FROM rekap_ptk_pd rpd JOIN sekolah_identitas si ON rpd.npsn_fk = si.npsn $joinClause $whereClause";
$rekapResult = mysqli_fetch_assoc(mysqli_query($connection, $rekapQuery));
$totalSiswa = $rekapResult['total_siswa'] ?? 0;
$totalGuru = $rekapResult['total_guru'] ?? 0;
$rasioSiswaGuru = ($totalGuru > 0) ? round($totalSiswa / $totalGuru, 1) : 0;

// 2. Data Chart
$sekolahPerJenjang = mysqli_query($connection, "SELECT si.jenjang_pendidikan, COUNT(*) as total FROM sekolah_identitas si $joinClause $whereClause GROUP BY si.jenjang_pendidikan ORDER BY FIELD(si.jenjang_pendidikan, 'SD', 'SMP', 'SMA', 'SMK')");
$sekolahPerStatus = mysqli_query($connection, "SELECT si.status_sekolah, COUNT(*) as total FROM sekolah_identitas si $joinClause $whereClause GROUP BY si.status_sekolah ORDER BY si.status_sekolah");

// Format data untuk JSON
$jenjangData = []; while ($d = mysqli_fetch_assoc($sekolahPerJenjang)) { $jenjangData[] = $d; }
$statusData = []; while ($d = mysqli_fetch_assoc($sekolahPerStatus)) { $statusData[] = $d; }

// Kirim response
echo json_encode([
    'success' => true,
    'stats' => ['totalSekolah' => $totalSekolah, 'totalSiswa' => $totalSiswa, 'totalGuru' => $totalGuru, 'rasioSiswaGuru' => $rasioSiswaGuru],
    'charts' => ['jenjang' => $jenjangData, 'status' => $statusData]
]);
?>