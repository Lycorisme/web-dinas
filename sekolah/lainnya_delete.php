<?php
session_start();
require_once '../helper/connection.php';

$id = $_GET['id'];

$result = mysqli_query($connection, "DELETE FROM sekolah_lainnya WHERE id='$id'");

if (mysqli_affected_rows($connection) > 0) {
  $_SESSION['info'] = [
    'status' => 'success',
    'message' => 'Berhasil menghapus data lainnya sekolah'
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