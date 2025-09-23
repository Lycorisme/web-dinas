<?php
session_start();
require_once '../helper/connection.php';

$id = $_POST['id'];
$npsn_fk = $_POST['npsn_fk'];
$nomor_telepon = $_POST['nomor_telepon'];
$nomor_fax = $_POST['nomor_fax'];
$email = $_POST['email'];
$website = $_POST['website'];

$query = mysqli_query($connection, "UPDATE sekolah_kontak SET 
                                   npsn_fk = '$npsn_fk',
                                   nomor_telepon = '$nomor_telepon',
                                   nomor_fax = '$nomor_fax',
                                   email = '$email',
                                   website = '$website'
                                   WHERE id = '$id'");

if ($query) {
  $_SESSION['info'] = [
    'status' => 'success',
    'message' => 'Berhasil mengubah data kontak sekolah'
  ];
  header('Location: ./kontak.php');
} else {
  $_SESSION['info'] = [
    'status' => 'failed',
    'message' => mysqli_error($connection)
  ];
  header('Location: ./kontak.php');
}
?>