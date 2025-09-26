<?php
// Cek login
require_once __DIR__ . '/../helper/auth.php';
isLogin();

// Koneksi database
require_once __DIR__ . '/../helper/connection.php';

// Perbaikan path untuk autoload
$autoloadPath = __DIR__ . '/../assets/vendor/autoload.php';
if (file_exists($autoloadPath)) {
    require_once $autoloadPath;
} else {
    die("Error: Autoload file tidak ditemukan di path: " . $autoloadPath);
}

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;

// Ambil parameter filter
$kecamatan = isset($_GET['kecamatan']) ? mysqli_real_escape_string($connection, $_GET['kecamatan']) : '';
$jenjang = isset($_GET['jenjang']) ? mysqli_real_escape_string($connection, $_GET['jenjang']) : '';

// Buat kondisi WHERE
$where_conditions = [];
if (!empty($kecamatan)) {
    $where_conditions[] = "k.nama_kecamatan = '$kecamatan'";
}
if (!empty($jenjang)) {
    $where_conditions[] = "s.jenjang_pendidikan = '$jenjang'";
}

$where_clause = '';
if (!empty($where_conditions)) {
    $where_clause = 'WHERE ' . implode(' AND ', $where_conditions);
}

// Query data dengan JOIN yang benar
$query = "SELECT s.npsn, s.nama_sekolah, s.jenjang_pendidikan, s.status_sekolah, 
                 s.alamat_jalan, k.nama_kecamatan, kab.nama_kabupaten, p.nama_provinsi
          FROM sekolah_identitas s
          LEFT JOIN kecamatan k ON s.id_kecamatan_fk = k.id_kecamatan
          LEFT JOIN kabupaten_kota kab ON k.id_kabupaten_fk = kab.id_kabupaten
          LEFT JOIN provinsi p ON kab.id_provinsi_fk = p.id_provinsi
          $where_clause
          ORDER BY s.nama_sekolah";

$result = mysqli_query($connection, $query);

if (!$result) {
    die('Query Error: ' . mysqli_error($connection));
}

$data_sekolah = [];
while ($row = mysqli_fetch_assoc($result)) {
    $data_sekolah[] = $row;
}

// Nama kecamatan dan jenjang untuk ditampilkan di laporan
$nama_kecamatan = !empty($kecamatan) ? $kecamatan : 'Semua Kecamatan';
$nama_jenjang = !empty($jenjang) ? $jenjang : 'Semua Jenjang';

// Ambil NPSN untuk query data tambahan jika ada data sekolah
if (!empty($data_sekolah)) {
    $npsn_list = array_column($data_sekolah, 'npsn');
    $npsn_string = "'" . implode("','", $npsn_list) . "'";
    
    // Query data rekap PTK per sekolah
    $ptk_query = "SELECT npsn_fk, 
                         SUM(guru + tendik) AS total_ptk
                  FROM rekap_ptk_pd 
                  WHERE npsn_fk IN ($npsn_string)
                  GROUP BY npsn_fk";
    $ptk_result = mysqli_query($connection, $ptk_query);
    
    $ptk_data = [];
    while ($row = mysqli_fetch_assoc($ptk_result)) {
        $ptk_data[$row['npsn_fk']] = $row['total_ptk'];
    }
    
    // Query data rekap PD per sekolah
    $pd_query = "SELECT npsn_fk, 
                         SUM(jumlah_laki_laki + jumlah_perempuan) AS total_pd
                  FROM rekap_rombel 
                  WHERE npsn_fk IN ($npsn_string)
                  GROUP BY npsn_fk";
    $pd_result = mysqli_query($connection, $pd_query);
    
    $pd_data = [];
    while ($row = mysqli_fetch_assoc($pd_result)) {
        $pd_data[$row['npsn_fk']] = $row['total_pd'];
    }
    
    // Query data rekap sarana per sekolah
    $sarana_query = "SELECT npsn_fk, 
                            SUM(jumlah) AS total_sarana
                     FROM rekap_sarpras 
                     WHERE npsn_fk IN ($npsn_string)
                     GROUP BY npsn_fk";
    $sarana_result = mysqli_query($connection, $sarana_query);
    
    $sarana_data = [];
    while ($row = mysqli_fetch_assoc($sarana_result)) {
        $sarana_data[$row['npsn_fk']] = $row['total_sarana'];
    }
    
    // Gabungkan data tambahan ke data sekolah
    foreach ($data_sekolah as &$sekolah) {
        $npsn = $sekolah['npsn'];
        $sekolah['total_ptk'] = isset($ptk_data[$npsn]) ? $ptk_data[$npsn] : 0;
        $sekolah['total_pd'] = isset($pd_data[$npsn]) ? $pd_data[$npsn] : 0;
        $sekolah['total_sarana'] = isset($sarana_data[$npsn]) ? $sarana_data[$npsn] : 0;
    }
}

// Buat objek Spreadsheet
$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();
$sheet->setTitle('Laporan Sekolah');

// Set header
$sheet->setCellValue('A1', 'PEMERINTAH PROVINSI KALIMANTAN SELATAN');
$sheet->setCellValue('A2', 'DINAS PENDIDIKAN DAN KEBUDAYAAN');
$sheet->setCellValue('A3', 'BALAI TEKNOLOGI INFORMASI DAN KOMUNIKASI PENDIDIKAN');
$sheet->setCellValue('A4', 'Jl. Perdagangan Komplek Bumi Indah Lestari II');

// Merge cell untuk header
$sheet->mergeCells('A1:K1');
$sheet->mergeCells('A2:K2');
$sheet->mergeCells('A3:K3');
$sheet->mergeCells('A4:K4');

// Style header
$sheet->getStyle('A1:A4')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
$sheet->getStyle('A1:A4')->getFont()->setBold(true);

// Garis bawah setelah header
$sheet->getStyle('A5:K5')->getBorders()->getBottom()->setBorderStyle(Border::BORDER_THICK);

// Judul laporan
$sheet->setCellValue('A7', 'Rekapitulasi Data Sekolah');
$sheet->mergeCells('A7:K7');
$sheet->getStyle('A7')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
$sheet->getStyle('A7')->getFont()->setBold(true);

// Informasi filter
$sheet->setCellValue('A8', 'Berdasarkan Filter: Kecamatan ' . $nama_kecamatan . ' - Jenjang ' . $nama_jenjang);
$sheet->setCellValue('A9', 'Tanggal Cetak: ' . date('d F Y'));

// Cek apakah ada data
if (empty($data_sekolah)) {
    // Jika tidak ada data, tampilkan pesan
    $sheet->setCellValue('A11', 'Tidak ada data sekolah yang sesuai dengan filter yang dipilih');
    $sheet->mergeCells('A11:K11');
    $sheet->getStyle('A11')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
    $sheet->getStyle('A11')->getFont()->setBold(true);
} else {
    // Header tabel
    $sheet->setCellValue('A11', 'No');
    $sheet->setCellValue('B11', 'NPSN');
    $sheet->setCellValue('C11', 'Nama Sekolah');
    $sheet->setCellValue('D11', 'Jenjang');
    $sheet->setCellValue('E11', 'Status');
    $sheet->setCellValue('F11', 'Alamat');
    $sheet->setCellValue('G11', 'Kecamatan');
    $sheet->setCellValue('H11', 'Kabupaten/Kota');
    $sheet->setCellValue('I11', 'Total PTK');
    $sheet->setCellValue('J11', 'Total PD');
    $sheet->setCellValue('K11', 'Total Sarana');

    // Style header tabel
    $sheet->getStyle('A11:K11')->getFont()->setBold(true);
    $sheet->getStyle('A11:K11')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
        ->getStartColor()->setARGB('FFE0E0E0');

    // Border untuk header tabel
    $sheet->getStyle('A11:K11')->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);

    // Isi data
    $row = 12;
    $no = 1;
    foreach ($data_sekolah as $sekolah) {
        $sheet->setCellValue('A' . $row, $no++);
        $sheet->setCellValue('B' . $row, $sekolah['npsn']);
        $sheet->setCellValue('C' . $row, $sekolah['nama_sekolah']);
        $sheet->setCellValue('D' . $row, $sekolah['jenjang_pendidikan']);
        $sheet->setCellValue('E' . $row, $sekolah['status_sekolah']);
        $sheet->setCellValue('F' . $row, $sekolah['alamat_jalan']);
        $sheet->setCellValue('G' . $row, $sekolah['nama_kecamatan']);
        $sheet->setCellValue('H' . $row, $sekolah['nama_kabupaten']);
        $sheet->setCellValue('I' . $row, $sekolah['total_ptk']);
        $sheet->setCellValue('J' . $row, $sekolah['total_pd']);
        $sheet->setCellValue('K' . $row, $sekolah['total_sarana']);
        
        // Border untuk setiap baris
        $sheet->getStyle('A' . $row . ':K' . $row)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
        
        $row++;
    }
}

// Auto width kolom
foreach (range('A', 'K') as $column) {
    $sheet->getColumnDimension($column)->setAutoSize(true);
}

// Tanda tangan
$ttdRow = $row + 3;
$sheet->setCellValue('I' . $ttdRow, 'Kepala Balai,');
$sheet->setCellValue('I' . ($ttdRow + 3), 'Nama Pejabat');
$sheet->setCellValue('I' . ($ttdRow + 4), 'NIP. 1234567890');

// Set writer
$writer = new Xlsx($spreadsheet);

// Output ke browser
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="laporan_sekolah.xlsx"');
header('Cache-Control: max-age=0');

$writer->save('php://output');
exit;