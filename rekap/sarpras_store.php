<?php
session_start();
require_once '../helper/connection.php';

// Pastikan semua data POST ada
if (isset($_POST['npsn_fk'], $_POST['sarana'], $_POST['jumlah'])) {
    
    // Normalisasi input dengan menghilangkan spasi berlebih
    $npsn_fk = htmlspecialchars($_POST['npsn_fk']);
    $sarana = trim(htmlspecialchars($_POST['sarana'])); // Menggunakan trim untuk menghilangkan spasi
    $jumlah = (int)$_POST['jumlah'];

    // 1. Cek apakah data dengan NPSN dan sarana ini sudah ada
    $check_stmt = mysqli_prepare($connection, "SELECT id FROM rekap_sarpras WHERE npsn_fk = ? AND TRIM(sarana) = ?");
    mysqli_stmt_bind_param($check_stmt, "ss", $npsn_fk, $sarana);
    mysqli_stmt_execute($check_stmt);
    mysqli_stmt_store_result($check_stmt);

    // 2. Jika sudah ada, berikan notifikasi error
    if (mysqli_stmt_num_rows($check_stmt) > 0) {
        $_SESSION['info'] = [
            'status' => 'failed',
            'message' => 'Data untuk sarana ' . $sarana . ' di sekolah ini sudah ada. Silakan gunakan fitur edit untuk mengubah data yang sudah ada.'
        ];
        header('Location: ./sarpras_create.php?npsn=' . $npsn_fk);
        exit();
    }
    mysqli_stmt_close($check_stmt);

    // 3. Cek jumlah data untuk sekolah ini (maksimal 10)
    $count_stmt = mysqli_prepare($connection, "SELECT COUNT(*) as total FROM rekap_sarpras WHERE npsn_fk = ?");
    mysqli_stmt_bind_param($count_stmt, "s", $npsn_fk);
    mysqli_stmt_execute($count_stmt);
    $count_result = mysqli_stmt_get_result($count_stmt);
    $count_data = mysqli_fetch_assoc($count_result);
    
    if ($count_data['total'] >= 10) {
        $_SESSION['info'] = [
            'status' => 'failed',
            'message' => 'Sekolah ini sudah memiliki 10 jenis sarana. Tidak bisa menambah data lagi.'
        ];
        header('Location: ./sarpras_create.php?npsn=' . $npsn_fk);
        exit();
    }
    mysqli_stmt_close($count_stmt);

    // 4. Jika belum ada dan belum mencapai batas, lakukan INSERT data baru
    $query = "INSERT INTO rekap_sarpras (npsn_fk, sarana, jumlah) VALUES (?, ?, ?)";
    $stmt = mysqli_prepare($connection, $query);
    mysqli_stmt_bind_param($stmt, "ssi", $npsn_fk, $sarana, $jumlah);
    
    if (mysqli_stmt_execute($stmt)) {
        $_SESSION['info'] = [
            'status' => 'success',
            'message' => 'Berhasil menambah data Sarpras.'
        ];
        header('Location: ./sarpras.php');
    } else {
        $_SESSION['info'] = [
            'status' => 'failed',
            'message' => 'Gagal menambah data: ' . mysqli_error($connection)
        ];
        header('Location: ./sarpras_create.php?npsn=' . $npsn_fk);
    }
    mysqli_stmt_close($stmt);

} else {
    // Jika ada data POST yang kurang
    $_SESSION['info'] = [
        'status' => 'failed',
        'message' => 'Semua kolom harus diisi.'
    ];
    header('Location: ./sarpras_create.php');
}

mysqli_close($connection);
?>