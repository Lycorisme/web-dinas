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

// Query data sarpras
 $query = "SELECT 
            s.npsn, 
            s.nama_sekolah, 
            s.jenjang_pendidikan, 
            k.nama_kecamatan, 
            SUM(CASE WHEN rs.sarana LIKE '%kelas%' THEN rs.jumlah ELSE 0 END) AS ruang_kelas,
            SUM(CASE WHEN rs.sarana LIKE '%lab%' THEN rs.jumlah ELSE 0 END) AS ruang_lab,
            SUM(CASE WHEN rs.sarana LIKE '%perpus%' THEN rs.jumlah ELSE 0 END) AS ruang_perpus,
            SUM(rs.jumlah) AS total_sarpras
          FROM rekap_sarpras rs
          LEFT JOIN sekolah_identitas s ON rs.npsn_fk = s.npsn
          LEFT JOIN kecamatan k ON s.id_kecamatan_fk = k.id_kecamatan
          $where_clause
          GROUP BY s.npsn
          ORDER BY s.nama_sekolah";

 $result = mysqli_query($connection, $query);

if (!$result) {
    die('Query Error: ' . mysqli_error($connection));
}

 $data_sarpras = [];
while ($row = mysqli_fetch_assoc($result)) {
    $data_sarpras[] = $row;
}

// Nama kecamatan dan jenjang untuk ditampilkan di laporan
 $nama_kecamatan = !empty($kecamatan) ? $kecamatan : 'Semua Kecamatan';
 $nama_jenjang = !empty($jenjang) ? $jenjang : 'Semua Jenjang';

// Buat objek Spreadsheet
 $spreadsheet = new Spreadsheet();
 $sheet = $spreadsheet->getActiveSheet();
 $sheet->setTitle('Laporan Sarana & Prasarana');

// Set header
 $sheet->setCellValue('A1', 'PEMERINTAH PROVINSI KALIMANTAN SELATAN');
 $sheet->setCellValue('A2', 'DINAS PENDIDIKAN DAN KEBUDAYAAN');
 $sheet->setCellValue('A3', 'BALAI TEKNOLOGI INFORMASI DAN KOMUNIKASI PENDIDIKAN');
 $sheet->setCellValue('A4', 'Jl. Perdagangan Komplek Bumi Indah Lestari II');

// Merge cell untuk header
 $sheet->mergeCells('A1:I1');
 $sheet->mergeCells('A2:I2');
 $sheet->mergeCells('A3:I3');
 $sheet->mergeCells('A4:I4');

// Style header
 $sheet->getStyle('A1:A4')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
 $sheet->getStyle('A1:A4')->getFont()->setBold(true);

// Garis bawah setelah header
 $sheet->getStyle('A5:I5')->getBorders()->getBottom()->setBorderStyle(Border::BORDER_THICK);

// Judul laporan
 $sheet->setCellValue('A7', 'Rekapitulasi Data Sarana & Prasarana');
 $sheet->mergeCells('A7:I7');
 $sheet->getStyle('A7')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
 $sheet->getStyle('A7')->getFont()->setBold(true);

// Informasi filter
 $sheet->setCellValue('A8', 'Berdasarkan Filter: Kecamatan ' . $nama_kecamatan . ' - Jenjang ' . $nama_jenjang);
 $sheet->setCellValue('A9', 'Tanggal Cetak: ' . date('d F Y'));

// Cek apakah ada data
if (empty($data_sarpras)) {
    // Jika tidak ada data, tampilkan pesan
    $sheet->setCellValue('A11', 'Tidak ada data sarana & prasarana yang sesuai dengan filter yang dipilih');
    $sheet->mergeCells('A11:I11');
    $sheet->getStyle('A11')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
    $sheet->getStyle('A11')->getFont()->setBold(true);
} else {
    // Header tabel
    $sheet->setCellValue('A11', 'No');
    $sheet->setCellValue('B11', 'NPSN');
    $sheet->setCellValue('C11', 'Nama Sekolah');
    $sheet->setCellValue('D11', 'Jenjang');
    $sheet->setCellValue('E11', 'Kecamatan');
    $sheet->setCellValue('F11', 'Ruang Kelas');
    $sheet->setCellValue('G11', 'Ruang Lab');
    $sheet->setCellValue('H11', 'Ruang Perpus');
    $sheet->setCellValue('I11', 'Total Sarpras');

    // Style header tabel
    $sheet->getStyle('A11:I11')->getFont()->setBold(true);
    $sheet->getStyle('A11:I11')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
        ->getStartColor()->setARGB('FFE0E0E0');

    // Border untuk header tabel
    $sheet->getStyle('A11:I11')->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);

    // Isi data
    $row = 12;
    $no = 1;
    foreach ($data_sarpras as $sarpras) {
        $sheet->setCellValue('A' . $row, $no++);
        $sheet->setCellValue('B' . $row, $sarpras['npsn']);
        $sheet->setCellValue('C' . $row, $sarpras['nama_sekolah']);
        $sheet->setCellValue('D' . $row, $sarpras['jenjang_pendidikan']);
        $sheet->setCellValue('E' . $row, $sarpras['nama_kecamatan']);
        $sheet->setCellValue('F' . $row, $sarpras['ruang_kelas']);
        $sheet->setCellValue('G' . $row, $sarpras['ruang_lab']);
        $sheet->setCellValue('H' . $row, $sarpras['ruang_perpus']);
        $sheet->setCellValue('I' . $row, $sarpras['total_sarpras']);
        
        // Border untuk setiap baris
        $sheet->getStyle('A' . $row . ':I' . $row)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
        
        $row++;
    }
}

// Auto width kolom
foreach (range('A', 'I') as $column) {
    $sheet->getColumnDimension($column)->setAutoSize(true);
}

// Set writer
 $writer = new Xlsx($spreadsheet);

// Output ke browser
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="laporan_sarpras.xlsx"');
header('Cache-Control: max-age=0');

 $writer->save('php://output');
exit;