<?php
session_start();
require_once '../helper/connection.php';

// Pastikan semua data POST ada
if (isset($_POST['npsn_fk'], $_POST['deskripsi'], $_POST['guru'], $_POST['tendik'], $_POST['ptk_total'], $_POST['pd_total'])) {
    
    // Normalisasi input dengan menghilangkan spasi berlebih
    $npsn_fk = htmlspecialchars($_POST['npsn_fk']);
    $deskripsi = trim(htmlspecialchars($_POST['deskripsi'])); // Menggunakan trim untuk menghilangkan spasi
    $guru = (int)$_POST['guru'];
    $tendik = (int)$_POST['tendik'];
    $ptk_total = (int)$_POST['ptk_total'];
    $pd_total = (int)$_POST['pd_total'];

    // 1. Cek apakah data dengan NPSN dan deskripsi ini sudah ada
    $check_stmt = mysqli_prepare($connection, "SELECT id FROM rekap_ptk_pd WHERE npsn_fk = ? AND TRIM(deskripsi) = ?");
    mysqli_stmt_bind_param($check_stmt, "ss", $npsn_fk, $deskripsi);
    mysqli_stmt_execute($check_stmt);
    mysqli_stmt_store_result($check_stmt);

    if (mysqli_stmt_num_rows($check_stmt) > 0) {
        $_SESSION['info'] = [
            'status' => 'failed',
            'message' => 'Data untuk jenis kelamin ' . $deskripsi . ' di sekolah ini sudah ada. Silakan gunakan fitur edit untuk mengubah data yang sudah ada.'
        ];
        header('Location: ./ptk_pd_create.php?npsn=' . $npsn_fk);
        exit();
    }
    mysqli_stmt_close($check_stmt);

    // 2. Cek jumlah data untuk sekolah ini (maksimal 2)
    $count_stmt = mysqli_prepare($connection, "SELECT COUNT(*) as total FROM rekap_ptk_pd WHERE npsn_fk = ?");
    mysqli_stmt_bind_param($count_stmt, "s", $npsn_fk);
    mysqli_stmt_execute($count_stmt);
    $count_result = mysqli_stmt_get_result($count_stmt);
    $count_data = mysqli_fetch_assoc($count_result);
    
    if ($count_data['total'] >= 2) {
        $_SESSION['info'] = [
            'status' => 'failed',
            'message' => 'Sekolah ini sudah memiliki 2 jenis kelamin (Laki-laki dan Perempuan). Tidak bisa menambah data lagi.'
        ];
        header('Location: ./ptk_pd_create.php?npsn=' . $npsn_fk);
        exit();
    }
    mysqli_stmt_close($count_stmt);

    // 3. Jika belum ada dan belum mencapai batas, lakukan INSERT data baru
    $query = "INSERT INTO rekap_ptk_pd (npsn_fk, deskripsi, guru, tendik, ptk_total, pd_total) VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = mysqli_prepare($connection, $query);
    mysqli_stmt_bind_param($stmt, "ssiiii", $npsn_fk, $deskripsi, $guru, $tendik, $ptk_total, $pd_total);
    
    if (mysqli_stmt_execute($stmt)) {
        $_SESSION['info'] = [
            'status' => 'success',
            'message' => 'Berhasil menambah data PTK PD.'
        ];
        header('Location: ./ptk_pd.php');
    } else {
        $_SESSION['info'] = [
            'status' => 'failed',
            'message' => 'Gagal menambah data: ' . mysqli_error($connection)
        ];
        header('Location: ./ptk_pd_create.php?npsn=' . $npsn_fk);
    }
    mysqli_stmt_close($stmt);

} else {
    // Jika ada data POST yang kurang
    $_SESSION['info'] = [
        'status' => 'failed',
        'message' => 'Semua kolom harus diisi.'
    ];
    header('Location: ./ptk_pd_create.php');
}

mysqli_close($connection);
?>