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

// Query data rasio
 $query = "SELECT 
            s.npsn, 
            s.nama_sekolah, 
            s.jenjang_pendidikan, 
            k.nama_kecamatan,
            SUM(rp.guru + rp.tendik) AS total_ptk,
            SUM(rr.jumlah_laki_laki + rr.jumlah_perempuan) AS total_siswa,
            ROUND(
                SUM(rr.jumlah_laki_laki + rr.jumlah_perempuan) / NULLIF(SUM(rp.guru + rp.tendik), 0),
                2
            ) AS rasio_siswa_ptk
         FROM sekolah_identitas s
         LEFT JOIN rekap_ptk_pd rp ON s.npsn = rp.npsn_fk
         LEFT JOIN rekap_rombel rr ON s.npsn = rr.npsn_fk
         LEFT JOIN kecamatan k ON s.id_kecamatan_fk = k.id_kecamatan
         $where_clause
         GROUP BY s.npsn
         ORDER BY s.nama_sekolah";

 $result = mysqli_query($connection, $query);

if (!$result) {
    die('Query Error: ' . mysqli_error($connection));
}

 $data_rasio = [];
while ($row = mysqli_fetch_assoc($result)) {
    $data_rasio[] = $row;
}

// Nama kecamatan dan jenjang untuk ditampilkan di laporan
 $nama_kecamatan = !empty($kecamatan) ? $kecamatan : 'Semua Kecamatan';
 $nama_jenjang = !empty($jenjang) ? $jenjang : 'Semua Jenjang';

// Cek apakah ada data
if (empty($data_rasio)) {
    // Jika tidak ada data, tampilkan pesan
    echo "<h1 style='text-align: center; margin-top: 50px;'>Tidak ada data rasio PTK terhadap peserta didik yang sesuai dengan filter yang dipilih.</h1>";
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
include 'templates/laporan_rasio_pdf.php';
 $html = ob_get_clean();

// Tulis HTML ke mPDF dan output ke browser
 $mpdf->WriteHTML($html);
 $mpdf->Output('laporan_rasio.pdf', 'I');