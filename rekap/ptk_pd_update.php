<?php
session_start();
require_once '../helper/connection.php';

 $id = $_POST['id'];
 $npsn_fk = $_POST['npsn_fk'];
// Normalisasi deskripsi dengan menghilangkan spasi berlebih
 $deskripsi = trim($_POST['deskripsi']); // Menggunakan trim untuk menghilangkan spasi
 $guru = (int)$_POST['guru'];
 $tendik = (int)$_POST['tendik'];
 $ptk_total = (int)$_POST['ptk_total'];
 $pd_total = (int)$_POST['pd_total'];

// Cek apakah ada duplikasi data selain data yang sedang diupdate
 $check_query = "SELECT id FROM rekap_ptk_pd WHERE npsn_fk = ? AND TRIM(deskripsi) = ? AND id != ?";
 $check_stmt = mysqli_prepare($connection, $check_query);
mysqli_stmt_bind_param($check_stmt, "ssi", $npsn_fk, $deskripsi, $id);
mysqli_stmt_execute($check_stmt);
mysqli_stmt_store_result($check_stmt);

if (mysqli_stmt_num_rows($check_stmt) > 0) {
    $_SESSION['info'] = [
        'status' => 'failed',
        'message' => 'Data untuk jenis kelamin ' . $deskripsi . ' di sekolah ini sudah ada.'
    ];
    header('Location: ./ptk_pd_edit.php?id=' . $id);
    exit();
}
mysqli_stmt_close($check_stmt);

// Gunakan prepared statement untuk update
 $update_query = "UPDATE rekap_ptk_pd SET 
                npsn_fk = ?,
                deskripsi = ?,
                guru = ?,
                tendik = ?,
                ptk_total = ?,
                pd_total = ?
                WHERE id = ?";
 $update_stmt = mysqli_prepare($connection, $update_query);
mysqli_stmt_bind_param($update_stmt, "ssiiiii", $npsn_fk, $deskripsi, $guru, $tendik, $ptk_total, $pd_total, $id);

if (mysqli_stmt_execute($update_stmt)) {
  $_SESSION['info'] = [
    'status' => 'success',
    'message' => 'Berhasil mengubah data PTK PD'
  ];
  header('Location: ./ptk_pd.php');
} else {
  $_SESSION['info'] = [
    'status' => 'failed',
    'message' => mysqli_error($connection)
  ];
  header('Location: ./ptk_pd_edit.php?id=' . $id);
}
mysqli_stmt_close($update_stmt);
mysqli_close($connection);
?>