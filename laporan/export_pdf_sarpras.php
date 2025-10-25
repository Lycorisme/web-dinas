<?php
// Cek login
require_once __DIR__ . '/../helper/auth.php';
isLogin();

// Koneksi database
require_once __DIR__ . '/../helper/connection.php';

// Panggil autoload composer
require __DIR__ . '/../assets/vendor/autoload.php';

// Gunakan mPDF
use Mpdf\Mpdf;

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

// Cek apakah ada data
if (empty($data_sarpras)) {
    // Jika tidak ada data, tampilkan pesan
    echo "<h1 style='text-align: center; margin-top: 50px;'>Tidak ada data sarana & prasarana yang sesuai dengan filter yang dipilih.</h1>";
    exit;
}

// Inisialisasi mPDF dengan format A4-Landscape
 $mpdf = new Mpdf([
    'mode' => 'utf-8',
    'format' => 'A4-L',
    'margin_left' => 10,
    'margin_right' => 10,
    'margin_top' => 20,
    'margin_bottom' => 20,
    'margin_header' => 10,
    'margin_footer' => 10
]);

// Load template
ob_start();
include 'templates/laporan_sarpras_pdf.php';
 $html = ob_get_clean();

// Tulis HTML ke mPDF dan output ke browser
 $mpdf->WriteHTML($html);
 $mpdf->Output('laporan_sarpras.pdf', 'I');