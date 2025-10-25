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

// Cek apakah ada data
if (empty($data_rombel)) {
    // Jika tidak ada data, tampilkan pesan
    echo "<h1 style='text-align: center; margin-top: 50px;'>Tidak ada data rombongan belajar yang sesuai dengan filter yang dipilih.</h1>";
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
include 'templates/laporan_rombel_pdf.php';
 $html = ob_get_clean();

// Tulis HTML ke mPDF dan output ke browser
 $mpdf->WriteHTML($html);
 $mpdf->Output('laporan_rombel.pdf', 'I');