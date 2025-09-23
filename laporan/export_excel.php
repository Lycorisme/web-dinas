<?php
// Cek login
require_once __DIR__ . '/../helper/auth.php';
isLogin();

// Koneksi database
require_once __DIR__ . '/../helper/connection.php';
require_once __DIR__ . '/../vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;

// Ambil parameter filter
$kecamatan = isset($_GET['kecamatan']) ? mysqli_real_escape_string($connection, $_GET['kecamatan']) : '';
$jenjang = isset($_GET['jenjang']) ? mysqli_real_escape_string($connection, $_GET['jenjang']) : '';

// Query data
$query = "SELECT * FROM dapodik.sekolah WHERE 1=1";
if (!empty($kecamatan)) $query .= " AND kecamatan = '$kecamatan'";
if (!empty($jenjang)) $query .= " AND jenjang = '$jenjang'";
$result = mysqli_query($connection, $query);
$data_sekolah = mysqli_fetch_all($result, MYSQLI_ASSOC);

// Nama kecamatan dan jenjang untuk ditampilkan di laporan
$nama_kecamatan = !empty($kecamatan) ? $kecamatan : 'Semua Kecamatan';
$jenjang = !empty($jenjang) ? $jenjang : 'Semua Jenjang';

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
$sheet->mergeCells('A1:F1');
$sheet->mergeCells('A2:F2');
$sheet->mergeCells('A3:F3');
$sheet->mergeCells('A4:F4');

// Style header
$sheet->getStyle('A1:A4')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
$sheet->getStyle('A1:A4')->getFont()->setBold(true);

// Garis bawah setelah header
$sheet->getStyle('A5:F5')->getBorders()->getBottom()->setBorderStyle(Border::BORDER_THICK);

// Judul laporan
$sheet->setCellValue('A7', 'Rekapitulasi Data Sekolah');
$sheet->mergeCells('A7:F7');
$sheet->getStyle('A7')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
$sheet->getStyle('A7')->getFont()->setBold(true);

// Informasi filter
$sheet->setCellValue('A8', 'Berdasarkan Filter: Kecamatan ' . $nama_kecamatan . ' - Jenjang ' . $jenjang);
$sheet->setCellValue('A9', 'Tanggal Cetak: ' . date('d F Y'));

// Header tabel
$sheet->setCellValue('A11', 'No');
$sheet->setCellValue('B11', 'NPSN');
$sheet->setCellValue('C11', 'Nama Sekolah');
$sheet->setCellValue('D11', 'Alamat');
$sheet->setCellValue('E11', 'Status');
$sheet->setCellValue('F11', 'Jenjang');

// Style header tabel
$sheet->getStyle('A11:F11')->getFont()->setBold(true);
$sheet->getStyle('A11:F11')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
    ->getStartColor()->setARGB('FFE0E0E0');

// Border untuk header tabel
$sheet->getStyle('A11:F11')->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);

// Isi data
$row = 12;
$no = 1;
foreach ($data_sekolah as $sekolah) {
    $sheet->setCellValue('A' . $row, $no++);
    $sheet->setCellValue('B' . $row, $sekolah['npsn']);
    $sheet->setCellValue('C' . $row, $sekolah['nama']);
    $sheet->setCellValue('D' . $row, $sekolah['alamat']);
    $sheet->setCellValue('E' . $row, $sekolah['status']);
    $sheet->setCellValue('F' . $row, $sekolah['jenjang']);
    
    // Border untuk setiap baris
    $sheet->getStyle('A' . $row . ':F' . $row)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
    
    $row++;
}

// Auto width kolom
foreach (range('A', 'F') as $column) {
    $sheet->getColumnDimension($column)->setAutoSize(true);
}

// Tanda tangan
$ttdRow = $row + 3;
$sheet->setCellValue('E' . $ttdRow, 'Kepala Balai,');
$sheet->setCellValue('E' . ($ttdRow + 3), 'Nama Pejabat');
$sheet->setCellValue('E' . ($ttdRow + 4), 'NIP. 1234567890');

// Set writer
$writer = new Xlsx($spreadsheet);

// Output ke browser
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="laporan_sekolah.xlsx"');
header('Cache-Control: max-age=0');

$writer->save('php://output');
exit;