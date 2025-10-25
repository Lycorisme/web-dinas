<?php
// Cek login
require_once __DIR__ . '/../helper/auth.php';
isLogin();

// Koneksi database
require_once __DIR__ . '/../helper/connection.php';

// Panggil autoload composer
require __DIR__ . '/../assets/vendor/autoload.php';

// PERUBAHAN 1: Gunakan mPDF
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

// Query data PTK
 $query = "SELECT 
            s.npsn, 
            s.nama_sekolah, 
            s.jenjang_pendidikan, 
            s.status_sekolah, 
            k.nama_kecamatan, 
            kab.nama_kabupaten, 
            p.nama_provinsi,
            SUM(r.guru) AS total_guru,
            SUM(r.tendik) AS total_tendik,
            SUM(r.guru + r.tendik) AS total_ptk_guru_tendik,
            SUM(CASE WHEN r.deskripsi = 'Laki - Laki' THEN r.ptk_total ELSE 0 END) AS ptk_laki,
            SUM(CASE WHEN r.deskripsi = 'Perempuan' THEN r.ptk_total ELSE 0 END) AS ptk_perempuan
         FROM sekolah_identitas s
         LEFT JOIN kecamatan k ON s.id_kecamatan_fk = k.id_kecamatan
         LEFT JOIN kabupaten_kota kab ON k.id_kabupaten_fk = kab.id_kabupaten
         LEFT JOIN provinsi p ON kab.id_provinsi_fk = p.id_provinsi
         LEFT JOIN rekap_ptk_pd r ON s.npsn = r.npsn_fk
         $where_clause
         GROUP BY s.npsn, s.nama_sekolah, k.nama_kecamatan
         ORDER BY s.nama_sekolah";

 $result = mysqli_query($connection, $query);

if (!$result) {
    die('Query Error: ' . mysqli_error($connection));
}

 $data_ptk = [];
while ($row = mysqli_fetch_assoc($result)) {
    $data_ptk[] = $row;
}

// Nama kecamatan dan jenjang untuk ditampilkan di laporan
 $nama_kecamatan = !empty($kecamatan) ? $kecamatan : 'Semua Kecamatan';
 $nama_jenjang = !empty($jenjang) ? $jenjang : 'Semua Jenjang';

// Cek apakah ada data
if (empty($data_ptk)) {
    // Jika tidak ada data, tampilkan pesan
    echo "<h1 style='text-align: center; margin-top: 50px;'>Tidak ada data PTK yang sesuai dengan filter yang dipilih.</h1>";
    exit;
}

// PERUBAHAN 2: Inisialisasi mPDF dengan format A4-Landscape
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
include 'templates/laporan_ptk_pdf.php';
 $html = ob_get_clean();

// PERUBAHAN 3: Tulis HTML ke mPDF dan output ke browser
 $mpdf->WriteHTML($html);
 $mpdf->Output('laporan_ptk.pdf', 'I');