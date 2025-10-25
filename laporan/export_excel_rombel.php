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
 $tingkat = isset($_GET['tingkat']) ? mysqli_real_escape_string($connection, $_GET['tingkat']) : '';

// Buat kondisi WHERE
 $where_conditions = [];
if (!empty($kecamatan)) {
    $where_conditions[] = "k.nama_kecamatan = '$kecamatan'";
}
if (!empty($jenjang)) {
    $where_conditions[] = "s.jenjang_pendidikan = '$jenjang'";
}
if (!empty($tingkat)) {
    $where_conditions[] = "rr.tingkat_kelas = '$tingkat'";
}

 $where_clause = '';
if (!empty($where_conditions)) {
    $where_clause = 'WHERE ' . implode(' AND ', $where_conditions);
}

// Query data rombel
 $query = "SELECT 
            s.npsn, 
            s.nama_sekolah, 
            s.jenjang_pendidikan, 
            k.nama_kecamatan,
            rr.tingkat_kelas,
            SUM(rr.jumlah_laki_laki) AS laki_laki,
            SUM(rr.jumlah_perempuan) AS perempuan,
            SUM(rr.jumlah_laki_laki + rr.jumlah_perempuan) AS total
          FROM rekap_rombel rr
          LEFT JOIN sekolah_identitas s ON rr.npsn_fk = s.npsn
          LEFT JOIN kecamatan k ON s.id_kecamatan_fk = k.id_kecamatan
          $where_clause
          GROUP BY s.npsn, rr.tingkat_kelas
          ORDER BY s.nama_sekolah, rr.tingkat_kelas";

 $result = mysqli_query($connection, $query);

if (!$result) {
    die('Query Error: ' . mysqli_error($connection));
}

 $data_rombel = [];
while ($row = mysqli_fetch_assoc($result)) {
    $data_rombel[] = $row;
}

// Nama kecamatan, jenjang, dan tingkat untuk ditampilkan di laporan
 $nama_kecamatan = !empty($kecamatan) ? $kecamatan : 'Semua Kecamatan';
 $nama_jenjang = !empty($jenjang) ? $jenjang : 'Semua Jenjang';
 $nama_tingkat = !empty($tingkat) ? $tingkat : 'Semua Tingkat';

// Buat objek Spreadsheet
 $spreadsheet = new Spreadsheet();
 $sheet = $spreadsheet->getActiveSheet();
 $sheet->setTitle('Laporan Rombongan Belajar');

// Set header
 $sheet->setCellValue('A1', 'PEMERINTAH PROVINSI KALIMANTAN SELATAN');
 $sheet->setCellValue('A2', 'DINAS PENDIDIKAN DAN KEBUDAYAAN');
 $sheet->setCellValue('A3', 'BALAI TEKNOLOGI INFORMASI DAN KOMUNIKASI PENDIDIKAN');
 $sheet->setCellValue('A4', 'Jl. Perdagangan Komplek Bumi Indah Lestari II');

// Merge cell untuk header
 $sheet->mergeCells('A1:H1');
 $sheet->mergeCells('A2:H2');
 $sheet->mergeCells('A3:H3');
 $sheet->mergeCells('A4:H4');

// Style header
 $sheet->getStyle('A1:A4')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
 $sheet->getStyle('A1:A4')->getFont()->setBold(true);

// Garis bawah setelah header
 $sheet->getStyle('A5:H5')->getBorders()->getBottom()->setBorderStyle(Border::BORDER_THICK);

// Judul laporan
 $sheet->setCellValue('A7', 'Rekapitulasi Data Rombongan Belajar');
 $sheet->mergeCells('A7:H7');
 $sheet->getStyle('A7')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
 $sheet->getStyle('A7')->getFont()->setBold(true);

// Informasi filter
 $sheet->setCellValue('A8', 'Berdasarkan Filter: Kecamatan ' . $nama_kecamatan . ' - Jenjang ' . $nama_jenjang . ' - Tingkat ' . $nama_tingkat);
 $sheet->setCellValue('A9', 'Tanggal Cetak: ' . date('d F Y'));

// Cek apakah ada data
if (empty($data_rombel)) {
    // Jika tidak ada data, tampilkan pesan
    $sheet->setCellValue('A11', 'Tidak ada data rombongan belajar yang sesuai dengan filter yang dipilih');
    $sheet->mergeCells('A11:H11');
    $sheet->getStyle('A11')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
    $sheet->getStyle('A11')->getFont()->setBold(true);
} else {
    // Header tabel
    $sheet->setCellValue('A11', 'No');
    $sheet->setCellValue('B11', 'NPSN');
    $sheet->setCellValue('C11', 'Nama Sekolah');
    $sheet->setCellValue('D11', 'Jenjang');
    $sheet->setCellValue('E11', 'Tingkat');
    $sheet->setCellValue('F11', 'Laki-laki');
    $sheet->setCellValue('G11', 'Perempuan');
    $sheet->setCellValue('H11', 'Total');

    // Style header tabel
    $sheet->getStyle('A11:H11')->getFont()->setBold(true);
    $sheet->getStyle('A11:H11')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
        ->getStartColor()->setARGB('FFE0E0E0');

    // Border untuk header tabel
    $sheet->getStyle('A11:H11')->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);

    // Isi data
    $row = 12;
    $no = 1;
    foreach ($data_rombel as $rombel) {
        $sheet->setCellValue('A' . $row, $no++);
        $sheet->setCellValue('B' . $row, $rombel['npsn']);
        $sheet->setCellValue('C' . $row, $rombel['nama_sekolah']);
        $sheet->setCellValue('D' . $row, $rombel['jenjang_pendidikan']);
        $sheet->setCellValue('E' . $row, $rombel['tingkat_kelas']);
        $sheet->setCellValue('F' . $row, $rombel['laki_laki']);
        $sheet->setCellValue('G' . $row, $rombel['perempuan']);
        $sheet->setCellValue('H' . $row, $rombel['total']);
        
        // Border untuk setiap baris
        $sheet->getStyle('A' . $row . ':H' . $row)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
        
        $row++;
    }
}

// Auto width kolom
foreach (range('A', 'H') as $column) {
    $sheet->getColumnDimension($column)->setAutoSize(true);
}

// Set writer
 $writer = new Xlsx($spreadsheet);

// Output ke browser
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="laporan_rombel.xlsx"');
header('Cache-Control: max-age=0');

 $writer->save('php://output');
exit;