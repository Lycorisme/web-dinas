<?php
session_start();
require_once '../helper/connection.php';

// Memeriksa apakah semua data yang diperlukan ada
if (isset($_POST['npsn_fk'], $_POST['sk_pendirian'], $_POST['tgl_sk_pendirian'], $_POST['status_kepemilikan'], $_POST['sk_izin_operasional'], $_POST['tgl_sk_izin_operasional'], $_POST['kebutuhan_khusus_dilayani'], $_POST['nama_bank'], $_POST['nomor_rekening'], $_POST['rekening_atas_nama'], $_POST['npwp'])) {

    $npsn_fk = htmlspecialchars($_POST['npsn_fk']);
    $sk_pendirian = htmlspecialchars($_POST['sk_pendirian']);
    $tgl_sk_pendirian = !empty($_POST['tgl_sk_pendirian']) ? htmlspecialchars($_POST['tgl_sk_pendirian']) : null;
    $status_kepemilikan = htmlspecialchars($_POST['status_kepemilikan']);
    $sk_izin_operasional = htmlspecialchars($_POST['sk_izin_operasional']);
    $tgl_sk_izin_operasional = !empty($_POST['tgl_sk_izin_operasional']) ? htmlspecialchars($_POST['tgl_sk_izin_operasional']) : null;
    $kebutuhan_khusus_dilayani = htmlspecialchars($_POST['kebutuhan_khusus_dilayani']);
    $nama_bank = htmlspecialchars($_POST['nama_bank']);
    $nomor_rekening = htmlspecialchars($_POST['nomor_rekening']);
    $rekening_atas_nama = htmlspecialchars($_POST['rekening_atas_nama']);
    $npwp = htmlspecialchars($_POST['npwp']);

    // 1. Cek apakah data dengan NPSN ini sudah ada
    $check_stmt = mysqli_prepare($connection, "SELECT npsn_fk FROM sekolah_pelengkap WHERE npsn_fk = ?");
    mysqli_stmt_bind_param($check_stmt, "s", $npsn_fk);
    mysqli_stmt_execute($check_stmt);
    mysqli_stmt_store_result($check_stmt);

    // 2. Jika sudah ada, berikan notifikasi error
    if (mysqli_stmt_num_rows($check_stmt) > 0) {
        $_SESSION['info'] = [
            'status' => 'failed',
            'message' => 'Data Pelengkap untuk sekolah dengan NPSN ' . $npsn_fk . ' sudah ada. Silakan gunakan fitur edit.',
        ];
        header('Location: ./pelengkap_create.php');
        exit();
    }
    mysqli_stmt_close($check_stmt);

    // 3. Jika belum ada, lakukan INSERT data baru
    $query = "INSERT INTO sekolah_pelengkap (npsn_fk, sk_pendirian, tgl_sk_pendirian, status_kepemilikan, sk_izin_operasional, tgl_sk_izin_operasional, kebutuhan_khusus_dilayani, nama_bank, nomor_rekening, rekening_atas_nama, npwp) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = mysqli_prepare($connection, $query);
    mysqli_stmt_bind_param($stmt, "sssssssssss", $npsn_fk, $sk_pendirian, $tgl_sk_pendirian, $status_kepemilikan, $sk_izin_operasional, $tgl_sk_izin_operasional, $kebutuhan_khusus_dilayani, $nama_bank, $nomor_rekening, $rekening_atas_nama, $npwp);

    if (mysqli_stmt_execute($stmt)) {
        $_SESSION['info'] = [
            'status' => 'success',
            'message' => 'Berhasil menambah data pelengkap sekolah.'
        ];
        header('Location: ./pelengkap.php');
    } else {
        $_SESSION['info'] = [
            'status' => 'failed',
            'message' => 'Gagal menambah data: ' . mysqli_error($connection)
        ];
        header('Location: ./pelengkap_create.php');
    }
    mysqli_stmt_close($stmt);

} else {
    $_SESSION['info'] = [
        'status' => 'failed',
        'message' => 'Terjadi kesalahan, data form tidak lengkap.'
    ];
    header('Location: ./pelengkap_create.php');
}

mysqli_close($connection);
?>