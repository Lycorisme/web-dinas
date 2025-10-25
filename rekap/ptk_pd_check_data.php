<?php
// FILE: rekap/ptk_pd_check_data.php - Check existing data for a school
require_once '../helper/connection.php';

header('Content-Type: application/json');

// Ambil parameter NPSN dan deskripsi (jika ada)
 $npsn = isset($_GET['npsn']) ? $_GET['npsn'] : '';
 $deskripsi = isset($_GET['deskripsi']) ? $_GET['deskripsi'] : '';

// Validasi NPSN
if (empty($npsn)) {
    echo json_encode([
        'status' => 'error',
        'message' => 'NPSN tidak boleh kosong'
    ]);
    exit;
}

// Jika hanya cek semua deskripsi untuk sekolah tertentu
if (empty($deskripsi)) {
    $result = mysqli_query($connection, "SELECT deskripsi FROM rekap_ptk_pd WHERE npsn_fk = '$npsn'");
    $data = [];
    
    while ($row = mysqli_fetch_assoc($result)) {
        // Normalisasi deskripsi
        $normalized = normalizeDeskripsi($row['deskripsi']);
        $data[] = [
            'deskripsi' => trim($row['deskripsi']),
            'normalized' => $normalized
        ];
    }
    
    // Cek apakah sudah ada 2 jenis kelamin
    $hasLaki = false;
    $hasPerempuan = false;
    
    foreach ($data as $item) {
        if ($item['normalized'] === 'lakilaki') $hasLaki = true;
        if ($item['normalized'] === 'perempuan') $hasPerempuan = true;
    }
    
    echo json_encode([
        'status' => 'success',
        'data' => $data,
        'hasLaki' => $hasLaki,
        'hasPerempuan' => $hasPerempuan,
        'isComplete' => $hasLaki && $hasPerempuan
    ]);
} else {
    // Jika cek deskripsi tertentu sudah ada atau belum
    $normalized = normalizeDeskripsi($deskripsi);
    
    // Cek apakah deskripsi sudah ada untuk sekolah ini
    $query = "SELECT id FROM rekap_ptk_pd 
              WHERE npsn_fk = '$npsn' 
              AND TRIM(deskripsi) = '" . mysqli_real_escape_string($connection, trim($deskripsi)) . "'";
    
    $result = mysqli_query($connection, $query);
    $exists = mysqli_num_rows($result) > 0;
    
    // Cek jumlah data untuk sekolah ini
    $countQuery = "SELECT COUNT(*) as total FROM rekap_ptk_pd WHERE npsn_fk = '$npsn'";
    $countResult = mysqli_query($connection, $countQuery);
    $countData = mysqli_fetch_assoc($countResult);
    $totalCount = $countData['total'];
    
    echo json_encode([
        'status' => 'success',
        'exists' => $exists,
        'deskripsi' => $deskripsi,
        'normalized' => $normalized,
        'totalCount' => $totalCount,
        'canAdd' => $totalCount < 2 && !$exists
    ]);
}

// Fungsi untuk normalisasi deskripsi
function normalizeDeskripsi($str) {
    // Ubah ke huruf kecil
    $str = strtolower($str);
    
    // Hapus spasi berlebih di awal dan akhir
    $str = trim($str);
    
    // Hapus spasi berlebih di tengah dan hapus tanda hubung
    $str = preg_replace('/[\s\-]+/', '', $str);
    
    // Hapus karakter khusus kecuali huruf dan angka
    $str = preg_replace('/[^a-z0-9]/', '', $str);
    
    return $str;
}
?>