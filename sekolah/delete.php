<?php
// FILE: sekolah/delete.php - VERSI YANG DIPERBAIKI
require_once '../helper/connection.php';

// Ambil NPSN dari parameter URL
$npsn = $_GET['npsn'];

if (empty($npsn)) {
    header("Location: index.php");
    exit();
}

// Mulai transaksi untuk memastikan semua operasi berhasil atau gagal bersama-sama
mysqli_begin_transaction($connection);

try {
    // Ambil data sekolah yang akan dihapus untuk mendapatkan ID kecamatan
    $query_sekolah = "SELECT id_kecamatan_fk FROM sekolah_identitas WHERE npsn = ?";
    $stmt_sekolah = mysqli_prepare($connection, $query_sekolah);
    mysqli_stmt_bind_param($stmt_sekolah, "s", $npsn);
    mysqli_stmt_execute($stmt_sekolah);
    $result_sekolah = mysqli_stmt_get_result($stmt_sekolah);
    
    if (mysqli_num_rows($result_sekolah) == 0) {
        throw new Exception("Data sekolah tidak ditemukan");
    }
    
    $data_sekolah = mysqli_fetch_assoc($result_sekolah);
    $id_kecamatan = $data_sekolah['id_kecamatan_fk'];
    
    // Hapus data sekolah
    $query_delete_sekolah = "DELETE FROM sekolah_identitas WHERE npsn = ?";
    $stmt_delete_sekolah = mysqli_prepare($connection, $query_delete_sekolah);
    mysqli_stmt_bind_param($stmt_delete_sekolah, "s", $npsn);
    mysqli_stmt_execute($stmt_delete_sekolah);
    
    // Cek apakah masih ada sekolah lain yang menggunakan kecamatan yang sama
    $query_check_kecamatan = "SELECT COUNT(*) as total FROM sekolah_identitas WHERE id_kecamatan_fk = ?";
    $stmt_check_kecamatan = mysqli_prepare($connection, $query_check_kecamatan);
    mysqli_stmt_bind_param($stmt_check_kecamatan, "i", $id_kecamatan);
    mysqli_stmt_execute($stmt_check_kecamatan);
    $result_check_kecamatan = mysqli_stmt_get_result($stmt_check_kecamatan);
    $data_check_kecamatan = mysqli_fetch_assoc($result_check_kecamatan);
    
    // Jika tidak ada sekolah lain yang menggunakan kecamatan ini, hapus kecamatan dan data terkaitnya
    if ($data_check_kecamatan['total'] == 0) {
        // Ambil data kecamatan untuk mendapatkan ID kabupaten
        $query_kecamatan = "SELECT id_kabupaten_fk FROM kecamatan WHERE id_kecamatan = ?";
        $stmt_kecamatan = mysqli_prepare($connection, $query_kecamatan);
        mysqli_stmt_bind_param($stmt_kecamatan, "i", $id_kecamatan);
        mysqli_stmt_execute($stmt_kecamatan);
        $result_kecamatan = mysqli_stmt_get_result($stmt_kecamatan);
        
        if (mysqli_num_rows($result_kecamatan) > 0) {
            $data_kecamatan = mysqli_fetch_assoc($result_kecamatan);
            $id_kabupaten = $data_kecamatan['id_kabupaten_fk'];
            
            // Hapus kecamatan
            $query_delete_kecamatan = "DELETE FROM kecamatan WHERE id_kecamatan = ?";
            $stmt_delete_kecamatan = mysqli_prepare($connection, $query_delete_kecamatan);
            mysqli_stmt_bind_param($stmt_delete_kecamatan, "i", $id_kecamatan);
            mysqli_stmt_execute($stmt_delete_kecamatan);
            
            // Cek apakah masih ada kecamatan lain yang menggunakan kabupaten yang sama
            $query_check_kabupaten = "SELECT COUNT(*) as total FROM kecamatan WHERE id_kabupaten_fk = ?";
            $stmt_check_kabupaten = mysqli_prepare($connection, $query_check_kabupaten);
            mysqli_stmt_bind_param($stmt_check_kabupaten, "i", $id_kabupaten);
            mysqli_stmt_execute($stmt_check_kabupaten);
            $result_check_kabupaten = mysqli_stmt_get_result($stmt_check_kabupaten);
            $data_check_kabupaten = mysqli_fetch_assoc($result_check_kabupaten);
            
            // Jika tidak ada kecamatan lain yang menggunakan kabupaten ini, hapus kabupaten dan data terkaitnya
            if ($data_check_kabupaten['total'] == 0) {
                // Ambil data kabupaten untuk mendapatkan ID provinsi
                $query_kabupaten = "SELECT id_provinsi_fk FROM kabupaten_kota WHERE id_kabupaten = ?";
                $stmt_kabupaten = mysqli_prepare($connection, $query_kabupaten);
                mysqli_stmt_bind_param($stmt_kabupaten, "i", $id_kabupaten);
                mysqli_stmt_execute($stmt_kabupaten);
                $result_kabupaten = mysqli_stmt_get_result($stmt_kabupaten);
                
                if (mysqli_num_rows($result_kabupaten) > 0) {
                    $data_kabupaten = mysqli_fetch_assoc($result_kabupaten);
                    $id_provinsi = $data_kabupaten['id_provinsi_fk'];
                    
                    // Hapus kabupaten
                    $query_delete_kabupaten = "DELETE FROM kabupaten_kota WHERE id_kabupaten = ?";
                    $stmt_delete_kabupaten = mysqli_prepare($connection, $query_delete_kabupaten);
                    mysqli_stmt_bind_param($stmt_delete_kabupaten, "i", $id_kabupaten);
                    mysqli_stmt_execute($stmt_delete_kabupaten);
                    
                    // Cek apakah masih ada kabupaten lain yang menggunakan provinsi yang sama
                    $query_check_provinsi = "SELECT COUNT(*) as total FROM kabupaten_kota WHERE id_provinsi_fk = ?";
                    $stmt_check_provinsi = mysqli_prepare($connection, $query_check_provinsi);
                    mysqli_stmt_bind_param($stmt_check_provinsi, "i", $id_provinsi);
                    mysqli_stmt_execute($stmt_check_provinsi);
                    $result_check_provinsi = mysqli_stmt_get_result($stmt_check_provinsi);
                    $data_check_provinsi = mysqli_fetch_assoc($result_check_provinsi);
                    
                    // Jika tidak ada kabupaten lain yang menggunakan provinsi ini, hapus provinsi
                    if ($data_check_provinsi['total'] == 0) {
                        $query_delete_provinsi = "DELETE FROM provinsi WHERE id_provinsi = ?";
                        $stmt_delete_provinsi = mysqli_prepare($connection, $query_delete_provinsi);
                        mysqli_stmt_bind_param($stmt_delete_provinsi, "i", $id_provinsi);
                        mysqli_stmt_execute($stmt_delete_provinsi);
                    }
                }
            }
        }
    }
    
    // Commit transaksi jika semua operasi berhasil
    mysqli_commit($connection);
    
    // Set session notifikasi
    $_SESSION['info'] = [
        'status' => 'success',
        'message' => 'Data sekolah beserta data terkait berhasil dihapus'
    ];
    
    // Redirect ke halaman index
    header("Location: index.php");
    exit();
    
} catch (Exception $e) {
    // Rollback transaksi jika terjadi kesalahan
    mysqli_rollback($connection);
    
    // Set session notifikasi error
    $_SESSION['info'] = [
        'status' => 'error',
        'message' => 'Gagal menghapus data: ' . $e->getMessage()
    ];
    
    // Redirect ke halaman index
    header("Location: index.php");
    exit();
}
?>