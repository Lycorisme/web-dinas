<?php
require_once '../helper/connection.php';
header('Content-Type: application/json');

$id_kabupaten = isset($_GET['id_kabupaten']) ? intval($_GET['id_kabupaten']) : 0;
$kecamatan = [];

if ($id_kabupaten > 0) {
    $query = "SELECT id_kecamatan, nama_kecamatan FROM kecamatan WHERE id_kabupaten_fk = ? ORDER BY nama_kecamatan ASC";
    $stmt = mysqli_prepare($connection, $query);
    mysqli_stmt_bind_param($stmt, "i", $id_kabupaten);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    while ($row = mysqli_fetch_assoc($result)) {
        $kecamatan[] = $row;
    }
}

echo json_encode(['success' => true, 'data' => $kecamatan]);
?>