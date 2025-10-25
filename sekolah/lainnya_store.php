<?php
session_start();
require_once '../helper/connection.php';

// Pastikan semua data POST ada
if (isset($_POST['npsn_fk'], $_POST['kepala_sekolah'], $_POST['operator_pendataan'], $_POST['akreditasi'], $_POST['kurikulum'])) {
    
    $npsn_fk = htmlspecialchars($_POST['npsn_fk']);
    $kepala_sekolah = htmlspecialchars($_POST['kepala_sekolah']);
    $operator_pendataan = htmlspecialchars($_POST['operator_pendataan']);
    $akreditasi = htmlspecialchars($_POST['akreditasi']);
    $kurikulum = htmlspecialchars($_POST['kurikulum']);

    // 1. Cek apakah data dengan NPSN ini sudah ada
    $check_stmt = mysqli_prepare($connection, "SELECT npsn_fk FROM sekolah_lainnya WHERE npsn_fk = ?");
    mysqli_stmt_bind_param($check_stmt, "s", $npsn_fk);
    mysqli_stmt_execute($check_stmt);
    mysqli_stmt_store_result($check_stmt);

    // 2. Jika sudah ada, berikan notifikasi error
    if (mysqli_stmt_num_rows($check_stmt) > 0) {
        $_SESSION['info'] = [
            'status' => 'failed',
            'message' => 'Data "Lainnya" untuk sekolah dengan NPSN ' . $npsn_fk . ' sudah ada. Silakan gunakan fitur edit.',
        ];
        header('Location: ./lainnya_create.php');
        exit();
    }
    mysqli_stmt_close($check_stmt);

    // 3. Jika belum ada, lakukan INSERT data baru
    $query = "INSERT INTO sekolah_lainnya (npsn_fk, kepala_sekolah, operator_pendataan, akreditasi, kurikulum) VALUES (?, ?, ?, ?, ?)";
    $stmt = mysqli_prepare($connection, $query);
    mysqli_stmt_bind_param($stmt, "sssss", $npsn_fk, $kepala_sekolah, $operator_pendataan, $akreditasi, $kurikulum);
    
    if (mysqli_stmt_execute($stmt)) {
        $_SESSION['info'] = [
            'status' => 'success',
            'message' => 'Berhasil menambah data lainnya sekolah.'
        ];
        header('Location: ./lainnya.php');
    } else {
        $_SESSION['info'] = [
            'status' => 'failed',
            'message' => 'Gagal menambah data: ' . mysqli_error($connection)
        ];
        header('Location: ./lainnya_create.php');
    }
    mysqli_stmt_close($stmt);

} else {
    // Jika ada data POST yang kurang
    $_SESSION['info'] = [
        'status' => 'failed',
        'message' => 'Semua kolom harus diisi.'
    ];
    header('Location: ./lainnya_create.php');
}

mysqli_close($connection);
?>