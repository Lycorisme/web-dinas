<?php
// Cek login
require_once __DIR__ . '/../helper/auth.php';
isLogin();

// Koneksi database
require_once __DIR__ . '/../helper/connection.php';
require_once __DIR__ . '/../vendor/autoload.php';

use Dompdf\Dompdf;
use Dompdf\Options;

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

// Konfigurasi Dompdf
$options = new Options();
// Atur chroot ke direktori root proyek
$options->set('chroot', realpath(__DIR__ . '/..'));
$options->set('isRemoteEnabled', true);
$options->set('defaultFont', 'Times New Roman');
$options->set('isHtml5ParserEnabled', true);

$dompdf = new Dompdf($options);

// Load template
ob_start();
include 'templates/laporan_template.php';
$html = ob_get_clean();

$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'portrait');

// Render PDF
try {
    $dompdf->render();
    // Stream ke browser (Attachment=0 untuk preview, 1 untuk download)
    $dompdf->stream("laporan_sekolah.pdf", ["Attachment" => 0]);
} catch (Exception $e) {
    echo "Error generating PDF: " . $e->getMessage();
}