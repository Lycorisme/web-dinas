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

// Cek apakah ada data
if (empty($data_sekolah)) {
    // Jika tidak ada data, tampilkan pesan
    echo "<h1 style='text-align: center; margin-top: 50px;'>Tidak ada data sekolah yang sesuai dengan filter yang dipilih.</h1>";
    exit;
}

// Ambil NPSN untuk query data tambahan
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
include 'templates/laporan_template.php';
 $html = ob_get_clean();

// Tulis HTML ke mPDF dan output ke browser
 $mpdf->WriteHTML($html);
 $mpdf->Output('laporan_sekolah.pdf', 'I');