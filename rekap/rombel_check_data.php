<?php
// FILE: rekap/rombel_check_data.php - Check existing data for a school
require_once '../helper/connection.php';

header('Content-Type: application/json');

// Ambil parameter NPSN dan tingkat_kelas (jika ada)
 $npsn = isset($_GET['npsn']) ? $_GET['npsn'] : '';
 $tingkat_kelas = isset($_GET['tingkat_kelas']) ? $_GET['tingkat_kelas'] : '';

// Validasi NPSN
if (empty($npsn)) {
    echo json_encode([
        'status' => 'error',
        'message' => 'NPSN tidak boleh kosong'
    ]);
    exit;
}

// Ambil jenjang pendidikan sekolah
 $sekolah_query = mysqli_query($connection, "SELECT jenjang_pendidikan FROM sekolah_identitas WHERE npsn = '$npsn'");
 $sekolah_data = mysqli_fetch_assoc($sekolah_query);
 $jenjang = $sekolah_data['jenjang_pendidikan'];

// Tentukan tingkat kelas yang valid berdasarkan jenjang
 $valid_tingkat = [];
switch ($jenjang) {
    case 'SD':
        $valid_tingkat = ['Kelas 1', 'Kelas 2', 'Kelas 3', 'Kelas 4', 'Kelas 5', 'Kelas 6'];
        break;
    case 'SMP':
        $valid_tingkat = ['Kelas 7', 'Kelas 8', 'Kelas 9'];
        break;
    case 'SMA':
    case 'SMK':
        $valid_tingkat = ['Kelas 10', 'Kelas 11', 'Kelas 12'];
        break;
    default:
        $valid_tingkat = ['Kelas 1', 'Kelas 2', 'Kelas 3', 'Kelas 4', 'Kelas 5', 'Kelas 6'];
}

// Jika hanya cek semua tingkat_kelas untuk sekolah tertentu
if (empty($tingkat_kelas)) {
    $result = mysqli_query($connection, "SELECT tingkat_kelas FROM rekap_rombel WHERE npsn_fk = '$npsn'");
    $data = [];
    
    while ($row = mysqli_fetch_assoc($result)) {
        // Normalisasi tingkat_kelas
        $normalized = normalizeTingkatKelas($row['tingkat_kelas']);
        $data[] = [
            'tingkat_kelas' => trim($row['tingkat_kelas']),
            'normalized' => $normalized
        ];
    }
    
    // Cek apakah sudah ada semua tingkat kelas yang valid
    $hasAllTingkat = true;
    foreach ($valid_tingkat as $tingkat) {
        $found = false;
        foreach ($data as $item) {
            if (normalizeTingkatKelas($item['tingkat_kelas']) === normalizeTingkatKelas($tingkat)) {
                $found = true;
                break;
            }
        }
        if (!$found) {
            $hasAllTingkat = false;
            break;
        }
    }
    
    echo json_encode([
        'status' => 'success',
        'data' => $data,
        'jenjang' => $jenjang,
        'valid_tingkat' => $valid_tingkat,
        'hasAllTingkat' => $hasAllTingkat
    ]);
} else {
    // Jika cek tingkat_kelas tertentu sudah ada atau belum
    $normalized = normalizeTingkatKelas($tingkat_kelas);
    
    // Cek apakah tingkat_kelas valid untuk jenjang ini
    $isValid = false;
    foreach ($valid_tingkat as $valid) {
        if (normalizeTingkatKelas($valid) === $normalized) {
            $isValid = true;
            break;
        }
    }
    
    if (!$isValid) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Tingkat kelas ' . $tingkat_kelas . ' tidak valid untuk jenjang ' . $jenjang
        ]);
        exit;
    }
    
    // Cek apakah tingkat_kelas sudah ada untuk sekolah ini
    $query = "SELECT id FROM rekap_rombel 
              WHERE npsn_fk = '$npsn' 
              AND TRIM(tingkat_kelas) = '" . mysqli_real_escape_string($connection, trim($tingkat_kelas)) . "'";
    
    $result = mysqli_query($connection, $query);
    $exists = mysqli_num_rows($result) > 0;
    
    // Cek jumlah data untuk sekolah ini
    $countQuery = "SELECT COUNT(*) as total FROM rekap_rombel WHERE npsn_fk = '$npsn'";
    $countResult = mysqli_query($connection, $countQuery);
    $countData = mysqli_fetch_assoc($countResult);
    $totalCount = $countData['total'];
    
    echo json_encode([
        'status' => 'success',
        'exists' => $exists,
        'tingkat_kelas' => $tingkat_kelas,
        'normalized' => $normalized,
        'totalCount' => $totalCount,
        'maxCount' => count($valid_tingkat),
        'canAdd' => $totalCount < count($valid_tingkat) && !$exists
    ]);
}

// Fungsi untuk normalisasi tingkat_kelas
function normalizeTingkatKelas($str) {
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