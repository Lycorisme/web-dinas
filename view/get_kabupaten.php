<?php
require_once '../helper/connection.php';
header('Content-Type: application/json');

$kabupaten = [];
$query = "SELECT id_kabupaten, nama_kabupaten FROM kabupaten_kota ORDER BY nama_kabupaten ASC";
$result = mysqli_query($connection, $query);

if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $kabupaten[] = $row;
    }
}

echo json_encode(['success' => true, 'data' => $kabupaten]);
?>