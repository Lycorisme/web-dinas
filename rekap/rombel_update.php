<?php
session_start();
require_once '../helper/connection.php';

 $id = $_POST['id'];
 $npsn_fk = $_POST['npsn_fk'];
// Normalisasi tingkat_kelas dengan menghilangkan spasi berlebih
 $tingkat_kelas = trim($_POST['tingkat_kelas']); // Menggunakan trim untuk menghilangkan spasi
 $jumlah_laki_laki = (int)$_POST['jumlah_laki_laki'];
 $jumlah_perempuan = (int)$_POST['jumlah_perempuan'];

// Hitung jumlah_total otomatis
 $jumlah_total = $jumlah_laki_laki + $jumlah_perempuan;

// Cek apakah ada duplikasi data selain data yang sedang diupdate
 $check_query = "SELECT id FROM rekap_rombel WHERE npsn_fk = ? AND TRIM(tingkat_kelas) = ? AND id != ?";
 $check_stmt = mysqli_prepare($connection, $check_query);
mysqli_stmt_bind_param($check_stmt, "ssi", $npsn_fk, $tingkat_kelas, $id);
mysqli_stmt_execute($check_stmt);
mysqli_stmt_store_result($check_stmt);

if (mysqli_stmt_num_rows($check_stmt) > 0) {
    $_SESSION['info'] = [
        'status' => 'failed',
        'message' => 'Data untuk tingkat kelas ' . $tingkat_kelas . ' di sekolah ini sudah ada.'
    ];
    header('Location: ./rombel_edit.php?id=' . $id);
    exit();
}
mysqli_stmt_close($check_stmt);

// Gunakan prepared statement untuk update
 $update_query = "UPDATE rekap_rombel SET 
                npsn_fk = ?,
                tingkat_kelas = ?,
                jumlah_laki_laki = ?,
                jumlah_perempuan = ?,
                jumlah_total = ?
                WHERE id = ?";
 $update_stmt = mysqli_prepare($connection, $update_query);
mysqli_stmt_bind_param($update_stmt, "ssiiii", $npsn_fk, $tingkat_kelas, $jumlah_laki_laki, $jumlah_perempuan, $jumlah_total, $id);

if (mysqli_stmt_execute($update_stmt)) {
  $_SESSION['info'] = [
    'status' => 'success',
    'message' => 'Berhasil mengubah data Rombel'
  ];
  header('Location: ./rombel.php');
} else {
  $_SESSION['info'] = [
    'status' => 'failed',
    'message' => mysqli_error($connection)
  ];
  header('Location: ./rombel_edit.php?id=' . $id);
}
mysqli_stmt_close($update_stmt);
mysqli_close($connection);
?>