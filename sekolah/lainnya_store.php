<?php
session_start();
require_once '../helper/connection.php';

$npsn_fk = $_POST['npsn_fk'];
$kepala_sekolah = $_POST['kepala_sekolah'];
$operator_pendataan = $_POST['operator_pendataan'];
$akreditasi = $_POST['akreditasi'];
$kurikulum = $_POST['kurikulum'];

$query = mysqli_query($connection, "INSERT INTO sekolah_lainnya 
                                   (npsn_fk, kepala_sekolah, operator_pendataan, akreditasi, kurikulum) 
                                   VALUES 
                                   ('$npsn_fk', '$kepala_sekolah', '$operator_pendataan', '$akreditasi', '$kurikulum')");

if ($query) {
  $_SESSION['info'] = [
    'status' => 'success',
    'message' => 'Berhasil menambah data lainnya sekolah'
  ];
  header('Location: ./lainnya.php');
} else {
  $_SESSION['info'] = [
    'status' => 'failed',
    'message' => mysqli_error($connection)
  ];
  header('Location: ./lainnya.php');
}
?>