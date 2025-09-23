<?php
session_start();
require_once '../helper/connection.php';

$npsn = $_POST['npsn'];
$nama_sekolah = $_POST['nama_sekolah'];
$jenjang_pendidikan = $_POST['jenjang_pendidikan'];
$status_sekolah = $_POST['status_sekolah'];
$alamat_jalan = $_POST['alamat_jalan'];
$rt = $_POST['rt'];
$rw = $_POST['rw'];
$kode_pos = $_POST['kode_pos'];
$kelurahan = $_POST['kelurahan'];
$lintang = $_POST['lintang'];
$bujur = $_POST['bujur'];
$id_kecamatan_fk = $_POST['id_kecamatan_fk'] ? $_POST['id_kecamatan_fk'] : null;

$query = mysqli_query($connection, "UPDATE sekolah_identitas SET 
                                   nama_sekolah = '$nama_sekolah',
                                   jenjang_pendidikan = '$jenjang_pendidikan',
                                   status_sekolah = '$status_sekolah',
                                   alamat_jalan = '$alamat_jalan',
                                   rt = '$rt',
                                   rw = '$rw',
                                   kode_pos = '$kode_pos',
                                   kelurahan = '$kelurahan',
                                   lintang = '$lintang',
                                   bujur = '$bujur',
                                   id_kecamatan_fk = " . ($id_kecamatan_fk ? "'$id_kecamatan_fk'" : "NULL") . "
                                   WHERE npsn = '$npsn'");

if ($query) {
  $_SESSION['info'] = [
    'status' => 'success',
    'message' => 'Berhasil mengubah data sekolah'
  ];
  header('Location: ./index.php');
} else {
  $_SESSION['info'] = [
    'status' => 'failed',
    'message' => mysqli_error($connection)
  ];
  header('Location: ./index.php');
}
?>