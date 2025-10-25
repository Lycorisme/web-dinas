<?php
session_start();
require_once '../helper/connection.php';

if (isset($_POST['npsn_fk'], $_POST['nomor_telepon'], $_POST['nomor_fax'], $_POST['email'], $_POST['website'])) {

    $npsn_fk = htmlspecialchars($_POST['npsn_fk']);
    $nomor_telepon = htmlspecialchars($_POST['nomor_telepon']);
    $nomor_fax = htmlspecialchars($_POST['nomor_fax']);
    $email = htmlspecialchars($_POST['email']);
    $website = htmlspecialchars($_POST['website']);

    // 1. Cek apakah data dengan NPSN ini sudah ada
    $check_stmt = mysqli_prepare($connection, "SELECT npsn_fk FROM sekolah_kontak WHERE npsn_fk = ?");
    mysqli_stmt_bind_param($check_stmt, "s", $npsn_fk);
    mysqli_stmt_execute($check_stmt);
    mysqli_stmt_store_result($check_stmt);

    // 2. Jika sudah ada, berikan notifikasi error
    if (mysqli_stmt_num_rows($check_stmt) > 0) {
        $_SESSION['info'] = [
            'status' => 'failed',
            'message' => 'Data Kontak untuk sekolah dengan NPSN ' . $npsn_fk . ' sudah ada. Silakan gunakan fitur edit.',
        ];
        header('Location: ./kontak_create.php');
        exit();
    }
    mysqli_stmt_close($check_stmt);

    // 3. Jika belum ada, lakukan INSERT data baru
    $query = "INSERT INTO sekolah_kontak (npsn_fk, nomor_telepon, nomor_fax, email, website) VALUES (?, ?, ?, ?, ?)";
    $stmt = mysqli_prepare($connection, $query);
    mysqli_stmt_bind_param($stmt, "sssss", $npsn_fk, $nomor_telepon, $nomor_fax, $email, $website);

    if (mysqli_stmt_execute($stmt)) {
        $_SESSION['info'] = [
            'status' => 'success',
            'message' => 'Berhasil menambah data kontak sekolah.'
        ];
        header('Location: ./kontak.php');
    } else {
        $_SESSION['info'] = [
            'status' => 'failed',
            'message' => 'Gagal menambah data: ' . mysqli_error($connection)
        ];
        header('Location: ./kontak_create.php');
    }
    mysqli_stmt_close($stmt);

} else {
    $_SESSION['info'] = [
        'status' => 'failed',
        'message' => 'Semua kolom harus diisi.'
    ];
    header('Location: ./kontak_create.php');
}

mysqli_close($connection);
?>