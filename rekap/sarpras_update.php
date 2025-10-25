<?php
session_start();
require_once '../helper/connection.php';

 $id = $_POST['id'];
 $npsn_fk = $_POST['npsn_fk'];
// Normalisasi sarana dengan menghilangkan spasi berlebih
 $sarana = trim($_POST['sarana']); // Menggunakan trim untuk menghilangkan spasi
 $jumlah = (int)$_POST['jumlah'];

// Cek apakah ada duplikasi data selain data yang sedang diupdate
 $check_query = "SELECT id FROM rekap_sarpras WHERE npsn_fk = ? AND TRIM(sarana) = ? AND id != ?";
 $check_stmt = mysqli_prepare($connection, $check_query);
mysqli_stmt_bind_param($check_stmt, "ssi", $npsn_fk, $sarana, $id);
mysqli_stmt_execute($check_stmt);
mysqli_stmt_store_result($check_stmt);

if (mysqli_stmt_num_rows($check_stmt) > 0) {
    $_SESSION['info'] = [
        'status' => 'failed',
        'message' => 'Data untuk sarana ' . $sarana . ' di sekolah ini sudah ada.'
    ];
    header('Location: ./sarpras_edit.php?id=' . $id);
    exit();
}
mysqli_stmt_close($check_stmt);

// Gunakan prepared statement untuk update
 $update_query = "UPDATE rekap_sarpras SET 
                npsn_fk = ?,
                sarana = ?,
                jumlah = ?
                WHERE id = ?";
 $update_stmt = mysqli_prepare($connection, $update_query);
mysqli_stmt_bind_param($update_stmt, "ssii", $npsn_fk, $sarana, $jumlah, $id);

if (mysqli_stmt_execute($update_stmt)) {
  $_SESSION['info'] = [
    'status' => 'success',
    'message' => 'Berhasil mengubah data Sarpras'
  ];
  header('Location: ./sarpras.php');
} else {
  $_SESSION['info'] = [
    'status' => 'failed',
    'message' => mysqli_error($connection)
  ];
  header('Location: ./sarpras_edit.php?id=' . $id);
}
mysqli_stmt_close($update_stmt);
mysqli_close($connection);
?>