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

// Query data siswa
 $query = "SELECT 
            s.npsn, 
            s.nama_sekolah, 
            s.jenjang_pendidikan, 
            s.status_sekolah, 
            k.nama_kecamatan, 
            kab.nama_kabupaten, 
            p.nama_provinsi,
            SUM(r.jumlah_laki_laki) AS total_pd_laki,
            SUM(r.jumlah_perempuan) AS total_pd_perempuan,
            SUM(r.jumlah_total) AS total_pd_total
          FROM sekolah_identitas s
          LEFT JOIN kecamatan k ON s.id_kecamatan_fk = k.id_kecamatan
          LEFT JOIN kabupaten_kota kab ON k.id_kabupaten_fk = kab.id_kabupaten
          LEFT JOIN provinsi p ON kab.id_provinsi_fk = p.id_provinsi
          LEFT JOIN rekap_rombel r ON s.npsn = r.npsn_fk
          $where_clause
          GROUP BY s.npsn, s.nama_sekolah, k.nama_kecamatan
          ORDER BY s.nama_sekolah";

 $result = mysqli_query($connection, $query);

if (!$result) {
    die('Query Error: ' . mysqli_error($connection));
}

 $data_siswa = [];
while ($row = mysqli_fetch_assoc($result)) {
    $data_siswa[] = $row;
}

// Nama kecamatan dan jenjang untuk ditampilkan di laporan
 $nama_kecamatan = !empty($kecamatan) ? $kecamatan : 'Semua Kecamatan';
 $nama_jenjang = !empty($jenjang) ? $jenjang : 'Semua Jenjang';

// Buat objek Spreadsheet
 $spreadsheet = new Spreadsheet();
 $sheet = $spreadsheet->getActiveSheet();
 $sheet->setTitle('Laporan Siswa');

// Set header
 $sheet->setCellValue('A1', 'PEMERINTAH PROVINSI KALIMANTAN SELATAN');
 $sheet->setCellValue('A2', 'DINAS PENDIDIKAN DAN KEBUDAYAAN');
 $sheet->setCellValue('A3', 'BALAI TEKNOLOGI INFORMASI DAN KOMUNIKASI PENDIDIKAN');
 $sheet->setCellValue('A4', 'Jl. Perdagangan Komplek Bumi Indah Lestari II');

// Merge cell untuk header
 $sheet->mergeCells('A1:J1');
 $sheet->mergeCells('A2:J2');
 $sheet->mergeCells('A3:J3');
 $sheet->mergeCells('A4:J4');

// Style header
 $sheet->getStyle('A1:A4')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
 $sheet->getStyle('A1:A4')->getFont()->setBold(true);

// Garis bawah setelah header
 $sheet->getStyle('A5:J5')->getBorders()->getBottom()->setBorderStyle(Border::BORDER_THICK);

// Judul laporan
 $sheet->setCellValue('A7', 'Rekapitulasi Data Siswa (Peserta Didik)');
 $sheet->mergeCells('A7:J7');
 $sheet->getStyle('A7')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
 $sheet->getStyle('A7')->getFont()->setBold(true);

// Informasi filter
 $sheet->setCellValue('A8', 'Berdasarkan Filter: Kecamatan ' . $nama_kecamatan . ' - Jenjang ' . $nama_jenjang);
 $sheet->setCellValue('A9', 'Tanggal Cetak: ' . date('d F Y'));

// Cek apakah ada data
if (empty($data_siswa)) {
    // Jika tidak ada data, tampilkan pesan
    $sheet->setCellValue('A11', 'Tidak ada data siswa yang sesuai dengan filter yang dipilih');
    $sheet->mergeCells('A11:J11');
    $sheet->getStyle('A11')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
    $sheet->getStyle('A11')->getFont()->setBold(true);
} else {
    // Header tabel
    $sheet->setCellValue('A11', 'No');
    $sheet->setCellValue('B11', 'NPSN');
    $sheet->setCellValue('C11', 'Nama Sekolah');
    $sheet->setCellValue('D11', 'Jenjang');
    $sheet->setCellValue('E11', 'Status');
    $sheet->setCellValue('F11', 'Kecamatan');
    $sheet->setCellValue('G11', 'Kabupaten/Kota');
    $sheet->setCellValue('H11', 'Total PD Laki-laki');
    $sheet->setCellValue('I11', 'Total PD Perempuan');
    $sheet->setCellValue('J11', 'Total Keseluruhan PD');

    // Style header tabel
    $sheet->getStyle('A11:J11')->getFont()->setBold(true);
    $sheet->getStyle('A11:J11')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
        ->getStartColor()->setARGB('FFE0E0E0');

    // Border untuk header tabel
    $sheet->getStyle('A11:J11')->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);

    // Isi data
    $row = 12;
    $no = 1;
    foreach ($data_siswa as $siswa) {
        $sheet->setCellValue('A' . $row, $no++);
        $sheet->setCellValue('B' . $row, $siswa['npsn']);
        $sheet->setCellValue('C' . $row, $siswa['nama_sekolah']);
        $sheet->setCellValue('D' . $row, $siswa['jenjang_pendidikan']);
        $sheet->setCellValue('E' . $row, $siswa['status_sekolah']);
        $sheet->setCellValue('F' . $row, $siswa['nama_kecamatan']);
        $sheet->setCellValue('G' . $row, $siswa['nama_kabupaten']);
        $sheet->setCellValue('H' . $row, $siswa['total_pd_laki']);
        $sheet->setCellValue('I' . $row, $siswa['total_pd_perempuan']);
        $sheet->setCellValue('J' . $row, $siswa['total_pd_total']);
        
        // Border untuk setiap baris
        $sheet->getStyle('A' . $row . ':J' . $row)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
        
        $row++;
    }
}

// Auto width kolom
foreach (range('A', 'J') as $column) {
    $sheet->getColumnDimension($column)->setAutoSize(true);
}

// Set writer
 $writer = new Xlsx($spreadsheet);

// Output ke browser
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="laporan_siswa.xlsx"');
header('Cache-Control: max-age=0');

 $writer->save('php://output');
exit;