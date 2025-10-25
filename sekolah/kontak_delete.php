<?php
session_start();
require_once '../helper/connection.php';

 $id = $_GET['id'];

// Ambil data untuk ditampilkan di konfirmasi
 $query_data = mysqli_query($connection, "SELECT k.*, s.nama_sekolah 
                                       FROM sekolah_kontak k
                                       LEFT JOIN sekolah_identitas s ON k.npsn_fk = s.npsn
                                       WHERE k.id = '$id'");
 $data = mysqli_fetch_assoc($query_data);

if (!$data) {
    $_SESSION['info'] = [
        'status' => 'failed',
        'message' => 'Data tidak ditemukan'
    ];
    header('Location: ./kontak.php');
    exit();
}

 $result = mysqli_query($connection, "DELETE FROM sekolah_kontak WHERE id='$id'");

if (mysqli_affected_rows($connection) > 0) {
$_SESSION['info'] = [
    'status' => 'success',
    'message' => 'Data kontak untuk ' . $data['nama_sekolah'] . ' berhasil dihapus.'
];
    header('Location: ./kontak.php');
} else {
    $_SESSION['info'] = [
        'status' => 'failed',
        'message' => mysqli_error($connection),
        'swal' => [
            'title' => 'Gagal!',
            'text' => 'Terjadi kesalahan saat menghapus data',
            'icon' => 'error'
        ]
    ];
    header('Location: ./kontak.php');
}
?>