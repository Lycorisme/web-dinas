<?php
session_start();
require_once '../helper/connection.php';

$id = $_POST['id'];
$npsn_fk = $_POST['npsn_fk'];
$kepala_sekolah = $_POST['kepala_sekolah'];
$operator_pendataan = $_POST['operator_pendataan'];
$akreditasi = $_POST['akreditasi'];
$kurikulum = $_POST['kurikulum'];

$query = mysqli_query($connection, "UPDATE sekolah_lainnya SET 
                                   npsn_fk = '$npsn_fk',
                                   kepala_sekolah = '$kepala_sekolah',
                                   operator_pendataan = '$operator_pendataan',
                                   akreditasi = '$akreditasi',
                                   kurikulum = '$kurikulum'
                                   WHERE id = '$id'");

if ($query) {
  $_SESSION['info'] = [
    'status' => 'success',
    'message' => 'Berhasil mengubah data lainnya sekolah'
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