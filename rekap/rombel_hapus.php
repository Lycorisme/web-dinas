<?php
session_start();
require_once '../helper/connection.php';

// Cek apakah parameter ID atau NPSN yang diberikan
if (isset($_GET['id'])) {
    // Hapus berdasarkan ID (untuk hapus individual di dalam detail)
    $id = $_GET['id'];
    
    // Ambil data untuk ditampilkan di konfirmasi
    $query_data = mysqli_query($connection, "SELECT r.*, s.nama_sekolah 
                                           FROM rekap_rombel r
                                           LEFT JOIN sekolah_identitas s ON r.npsn_fk = s.npsn
                                           WHERE r.id = '$id'");
    $data = mysqli_fetch_assoc($query_data);

    if (!$data) {
        $_SESSION['info'] = [
            'status' => 'failed',
            'message' => 'Data tidak ditemukan'
        ];
        header('Location: ./rombel.php');
        exit();
    }

    $result = mysqli_query($connection, "DELETE FROM rekap_rombel WHERE id='$id'");

    if (mysqli_affected_rows($connection) > 0) {
        $_SESSION['info'] = [
            'status' => 'success',
            'message' => 'Data Rombel untuk ' . $data['nama_sekolah'] . ' (' . $data['tingkat_kelas'] . ') berhasil dihapus.'
        ];
        header('Location: ./rombel.php');
    } else {
        $_SESSION['info'] = [
            'status' => 'failed',
            'message' => mysqli_error($connection)
        ];
        header('Location: ./rombel.php');
    }
} elseif (isset($_GET['npsn'])) {
    // Hapus berdasarkan NPSN (untuk hapus semua data sekolah)
    $npsn = $_GET['npsn'];
    
    // Ambil nama sekolah untuk ditampilkan di konfirmasi
    $query_sekolah = mysqli_query($connection, "SELECT nama_sekolah FROM sekolah_identitas WHERE npsn = '$npsn'");
    $sekolah = mysqli_fetch_assoc($query_sekolah);

    if (!$sekolah) {
        $_SESSION['info'] = [
            'status' => 'failed',
            'message' => 'Sekolah tidak ditemukan'
        ];
        header('Location: ./rombel.php');
        exit();
    }

    // Hapus semua data dengan NPSN tersebut
    $result = mysqli_query($connection, "DELETE FROM rekap_rombel WHERE npsn_fk='$npsn'");

    if (mysqli_affected_rows($connection) > 0) {
        $_SESSION['info'] = [
            'status' => 'success',
            'message' => 'Semua data Rombel untuk ' . $sekolah['nama_sekolah'] . ' berhasil dihapus.'
        ];
        header('Location: ./rombel.php');
    } else {
        $_SESSION['info'] = [
            'status' => 'failed',
            'message' => mysqli_error($connection)
        ];
        header('Location: ./rombel.php');
    }
} else {
    $_SESSION['info'] = [
        'status' => 'failed',
        'message' => 'Parameter tidak valid'
    ];
    header('Location: ./rombel.php');
}
?>