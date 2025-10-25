<?php
session_start();
require_once '../helper/connection.php';

 $id = $_GET['id'];

// Ambil data untuk ditampilkan di konfirmasi
 $query_data = mysqli_query($connection, "SELECT l.*, s.nama_sekolah 
                                       FROM sekolah_lainnya l
                                       LEFT JOIN sekolah_identitas s ON l.npsn_fk = s.npsn
                                       WHERE l.id = '$id'");
 $data = mysqli_fetch_assoc($query_data);

if (!$data) {
    $_SESSION['info'] = [
        'status' => 'failed',
        'message' => 'Data tidak ditemukan'
    ];
    header('Location: ./lainnya.php');
    exit();
}

 $result = mysqli_query($connection, "DELETE FROM sekolah_lainnya WHERE id='$id'");

if (mysqli_affected_rows($connection) > 0) {
$_SESSION['info'] = [
    'status' => 'success',
    'message' => 'Data lainnya untuk ' . $data['nama_sekolah'] . ' berhasil dihapus.'
];
    header('Location: ./lainnya.php');
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
    header('Location: ./lainnya.php');
}
?>