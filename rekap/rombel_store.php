<?php
session_start();
require_once '../helper/connection.php';

// Pastikan semua data POST ada
if (isset($_POST['npsn_fk'], $_POST['tingkat_kelas'], $_POST['jumlah_laki_laki'], $_POST['jumlah_perempuan'])) {
    
    // Normalisasi input dengan menghilangkan spasi berlebih
    $npsn_fk = htmlspecialchars($_POST['npsn_fk']);
    $tingkat_kelas = trim(htmlspecialchars($_POST['tingkat_kelas'])); // Menggunakan trim untuk menghilangkan spasi
    $jumlah_laki_laki = (int)$_POST['jumlah_laki_laki'];
    $jumlah_perempuan = (int)$_POST['jumlah_perempuan'];
    
    // Hitung jumlah_total otomatis
    $jumlah_total = $jumlah_laki_laki + $jumlah_perempuan;

    // 1. Cek apakah data dengan NPSN dan tingkat_kelas ini sudah ada
    $check_stmt = mysqli_prepare($connection, "SELECT id FROM rekap_rombel WHERE npsn_fk = ? AND TRIM(tingkat_kelas) = ?");
    mysqli_stmt_bind_param($check_stmt, "ss", $npsn_fk, $tingkat_kelas);
    mysqli_stmt_execute($check_stmt);
    mysqli_stmt_store_result($check_stmt);

    // 2. Jika sudah ada, berikan notifikasi error
    if (mysqli_stmt_num_rows($check_stmt) > 0) {
        $_SESSION['info'] = [
            'status' => 'failed',
            'message' => 'Data untuk tingkat kelas ' . $tingkat_kelas . ' di sekolah ini sudah ada. Silakan gunakan fitur edit untuk mengubah data yang sudah ada.'
        ];
        header('Location: ./rombel_create.php?npsn=' . $npsn_fk);
        exit();
    }
    mysqli_stmt_close($check_stmt);

    // 3. Cek jumlah data untuk sekolah ini (maksimal 6)
    $count_stmt = mysqli_prepare($connection, "SELECT COUNT(*) as total FROM rekap_rombel WHERE npsn_fk = ?");
    mysqli_stmt_bind_param($count_stmt, "s", $npsn_fk);
    mysqli_stmt_execute($count_stmt);
    $count_result = mysqli_stmt_get_result($count_stmt);
    $count_data = mysqli_fetch_assoc($count_result);
    
    if ($count_data['total'] >= 6) {
        $_SESSION['info'] = [
            'status' => 'failed',
            'message' => 'Sekolah ini sudah memiliki 6 tingkat kelas (Kelas 1-6). Tidak bisa menambah data lagi.'
        ];
        header('Location: ./rombel_create.php?npsn=' . $npsn_fk);
        exit();
    }
    mysqli_stmt_close($count_stmt);

    // 4. Jika belum ada dan belum mencapai batas, lakukan INSERT data baru
    $query = "INSERT INTO rekap_rombel (npsn_fk, tingkat_kelas, jumlah_laki_laki, jumlah_perempuan, jumlah_total) VALUES (?, ?, ?, ?, ?)";
    $stmt = mysqli_prepare($connection, $query);
    mysqli_stmt_bind_param($stmt, "ssiii", $npsn_fk, $tingkat_kelas, $jumlah_laki_laki, $jumlah_perempuan, $jumlah_total);
    
    if (mysqli_stmt_execute($stmt)) {
        $_SESSION['info'] = [
            'status' => 'success',
            'message' => 'Berhasil menambah data Rombel.'
        ];
        header('Location: ./rombel.php');
    } else {
        $_SESSION['info'] = [
            'status' => 'failed',
            'message' => 'Gagal menambah data: ' . mysqli_error($connection)
        ];
        header('Location: ./rombel_create.php?npsn=' . $npsn_fk);
    }
    mysqli_stmt_close($stmt);

} else {
    // Jika ada data POST yang kurang
    $_SESSION['info'] = [
        'status' => 'failed',
        'message' => 'Semua kolom harus diisi.'
    ];
    header('Location: ./rombel_create.php');
}

mysqli_close($connection);
?>