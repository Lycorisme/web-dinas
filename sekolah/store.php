<?php
session_start();
require_once '../helper/connection.php';

if (isset($_POST['proses'])) {
    $npsn = $_POST['npsn'];
    $nama_sekolah = $_POST['nama_sekolah'];
    $jenjang_pendidikan = $_POST['jenjang_pendidikan'];
    $status_sekolah = $_POST['status_sekolah'];
    $alamat_jalan = $_POST['alamat_jalan'];
    $rt = $_POST['rt'] ?: null;
    $rw = $_POST['rw'] ?: null;
    $kode_pos = $_POST['kode_pos'] ?: null;
    $kelurahan = $_POST['kelurahan'] ?: null;
    $id_kecamatan_fk = $_POST['id_kecamatan_fk'];
    $lintang = $_POST['lintang'] ?: null;
    $bujur = $_POST['bujur'] ?: null;
    
    // URL scraping data
    $scraping_url = trim($_POST['scraping_url'] ?? '');
    $url_description = trim($_POST['url_description'] ?? '');
    $save_url = isset($_POST['save_url']) ? 1 : 0;

    // Cek apakah NPSN sudah ada
    $check_npsn = mysqli_query($connection, "SELECT npsn FROM sekolah_identitas WHERE npsn = '$npsn'");
    
    if (mysqli_num_rows($check_npsn) > 0) {
        $_SESSION['info'] = [
            'status' => 'failed',
            'message' => 'NPSN sudah ada di database!'
        ];
        header('Location: ./create.php');
        exit();
    }

    try {
        // Begin transaction
        mysqli_begin_transaction($connection);

        // Insert data sekolah
        $query = "INSERT INTO sekolah_identitas (
            npsn, nama_sekolah, jenjang_pendidikan, status_sekolah, 
            alamat_jalan, rt, rw, kode_pos, kelurahan, 
            id_kecamatan_fk, lintang, bujur
        ) VALUES (
            '$npsn', '$nama_sekolah', '$jenjang_pendidikan', '$status_sekolah',
            '$alamat_jalan', " . ($rt ? "'$rt'" : 'NULL') . ", " . ($rw ? "'$rw'" : 'NULL') . ",
            " . ($kode_pos ? "'$kode_pos'" : 'NULL') . ", " . ($kelurahan ? "'$kelurahan'" : 'NULL') . ",
            '$id_kecamatan_fk', " . ($lintang ? "'$lintang'" : 'NULL') . ", " . ($bujur ? "'$bujur'" : 'NULL') . "
        )";

        $result = mysqli_query($connection, $query);

        if (!$result) {
            throw new Exception('Gagal menyimpan data sekolah: ' . mysqli_error($connection));
        }

        // Simpan URL scraping jika diminta
        if ($save_url && !empty($scraping_url)) {
            // Validate URL
            if (!filter_var($scraping_url, FILTER_VALIDATE_URL)) {
                throw new Exception('Format URL tidak valid');
            }

            // Check domain
            $parsed_url = parse_url($scraping_url);
            if ($parsed_url['host'] !== 'dapo.kemendikdasmen.go.id') {
                throw new Exception('URL harus dari domain dapo.kemendikdasmen.go.id');
            }

            // Check if URL already exists
            $escaped_url = mysqli_real_escape_string($connection, $scraping_url);
            $check_url = mysqli_query($connection, "SELECT id FROM scraping_urls WHERE url = '$escaped_url'");
            
            if (mysqli_num_rows($check_url) > 0) {
                throw new Exception('URL sudah ada di database');
            }

            // Auto-generate description if empty
            if (empty($url_description)) {
                $url_description = "$nama_sekolah (NPSN: $npsn)";
            }

            $escaped_description = mysqli_real_escape_string($connection, $url_description);
            
            $url_query = "INSERT INTO scraping_urls (url, description, status) 
                         VALUES ('$escaped_url', '$escaped_description', 'active')";
            
            $url_result = mysqli_query($connection, $url_query);
            
            if (!$url_result) {
                throw new Exception('Gagal menyimpan URL scraping: ' . mysqli_error($connection));
            }
        }

        // Commit transaction
        mysqli_commit($connection);

        $_SESSION['info'] = [
            'status' => 'success',
            'message' => 'Data sekolah berhasil ditambahkan' . ($save_url && !empty($scraping_url) ? ' beserta URL scraping' : '')
        ];
        
        header('Location: ./index.php');
        exit();

    } catch (Exception $e) {
        // Rollback transaction
        mysqli_rollback($connection);
        
        $_SESSION['info'] = [
            'status' => 'failed',
            'message' => $e->getMessage()
        ];
        
        header('Location: ./create.php');
        exit();
    }
} else {
    header('Location: ./index.php');
    exit();
}
?>