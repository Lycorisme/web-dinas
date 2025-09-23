<?php
session_start();
require_once '../helper/connection.php';

$npsn_fk = $_POST['npsn_fk'];
$nomor_telepon = $_POST['nomor_telepon'];
$nomor_fax = $_POST['nomor_fax'];
$email = $_POST['email'];
$website = $_POST['website'];

$query = mysqli_query($connection, "INSERT INTO sekolah_kontak 
                                   (npsn_fk, nomor_telepon, nomor_fax, email, website) 
                                   VALUES 
                                   ('$npsn_fk', '$nomor_telepon', '$nomor_fax', '$email', '$website')");

if ($query) {
  $_SESSION['info'] = [
    'status' => 'success',
    'message' => 'Berhasil menambah data kontak sekolah'
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