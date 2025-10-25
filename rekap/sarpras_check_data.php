<?php
// FILE: rekap/sarpras_check_data.php - Check existing data for a school
require_once '../helper/connection.php';

header('Content-Type: application/json');

// Ambil parameter NPSN dan sarana (jika ada)
 $npsn = isset($_GET['npsn']) ? $_GET['npsn'] : '';
 $sarana = isset($_GET['sarana']) ? $_GET['sarana'] : '';

// Validasi NPSN
if (empty($npsn)) {
    echo json_encode([
        'status' => 'error',
        'message' => 'NPSN tidak boleh kosong'
    ]);
    exit;
}

// Jika hanya cek semua sarana untuk sekolah tertentu
if (empty($sarana)) {
    $result = mysqli_query($connection, "SELECT sarana FROM rekap_sarpras WHERE npsn_fk = '$npsn'");
    $data = [];
    
    while ($row = mysqli_fetch_assoc($result)) {
        // Normalisasi sarana
        $normalized = normalizeSarana($row['sarana']);
        $data[] = [
            'sarana' => trim($row['sarana']),
            'normalized' => $normalized
        ];
    }
    
    // Cek apakah sudah ada semua jenis sarana
    $hasRuangKelas = false;
    $hasRuangLab = false;
    $hasRuangPerpus = false;
    $hasRuangGuru = false;
    $hasRuangKepalaSekolah = false;
    $hasRuangTU = false;
    $hasRuangUKS = false;
    $hasRuangToilet = false;
    $hasRuangGudang = false;
    $hasLainnya = false;
    
    foreach ($data as $item) {
        if ($item['normalized'] === 'ruangkelas') $hasRuangKelas = true;
        if ($item['normalized'] === 'ruanglab') $hasRuangLab = true;
        if ($item['normalized'] === 'ruangperpus') $hasRuangPerpus = true;
        if ($item['normalized'] === 'ruangguru') $hasRuangGuru = true;
        if ($item['normalized'] === 'ruangkepalasekolah') $hasRuangKepalaSekolah = true;
        if ($item['normalized'] === 'ruangtu') $hasRuangTU = true;
        if ($item['normalized'] === 'ruanguks') $hasRuangUKS = true;
        if ($item['normalized'] === 'ruangtoilet') $hasRuangToilet = true;
        if ($item['normalized'] === 'ruanggudang') $hasRuangGudang = true;
        if ($item['normalized'] === 'lainnya') $hasLainnya = true;
    }
    
    echo json_encode([
        'status' => 'success',
        'data' => $data,
        'hasRuangKelas' => $hasRuangKelas,
        'hasRuangLab' => $hasRuangLab,
        'hasRuangPerpus' => $hasRuangPerpus,
        'hasRuangGuru' => $hasRuangGuru,
        'hasRuangKepalaSekolah' => $hasRuangKepalaSekolah,
        'hasRuangTU' => $hasRuangTU,
        'hasRuangUKS' => $hasRuangUKS,
        'hasRuangToilet' => $hasRuangToilet,
        'hasRuangGudang' => $hasRuangGudang,
        'hasLainnya' => $hasLainnya,
        'isComplete' => $hasRuangKelas && $hasRuangLab && $hasRuangPerpus && $hasRuangGuru && $hasRuangKepalaSekolah && $hasRuangTU && $hasRuangUKS && $hasRuangToilet && $hasRuangGudang && $hasLainnya
    ]);
} else {
    // Jika cek sarana tertentu sudah ada atau belum
    $normalized = normalizeSarana($sarana);
    
    // Cek apakah sarana sudah ada untuk sekolah ini
    $query = "SELECT id FROM rekap_sarpras 
              WHERE npsn_fk = '$npsn' 
              AND TRIM(sarana) = '" . mysqli_real_escape_string($connection, trim($sarana)) . "'";
    
    $result = mysqli_query($connection, $query);
    $exists = mysqli_num_rows($result) > 0;
    
    // Cek jumlah data untuk sekolah ini
    $countQuery = "SELECT COUNT(*) as total FROM rekap_sarpras WHERE npsn_fk = '$npsn'";
    $countResult = mysqli_query($connection, $countQuery);
    $countData = mysqli_fetch_assoc($countResult);
    $totalCount = $countData['total'];
    
    echo json_encode([
        'status' => 'success',
        'exists' => $exists,
        'sarana' => $sarana,
        'normalized' => $normalized,
        'totalCount' => $totalCount,
        'canAdd' => $totalCount < 10 && !$exists
    ]);
}

// Fungsi untuk normalisasi sarana
function normalizeSarana($str) {
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