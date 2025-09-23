<?php
session_start();
require_once '../helper/connection.php';

$npsn_fk = $_POST['npsn_fk'];
$sk_pendirian = $_POST['sk_pendirian'];
$tgl_sk_pendirian = $_POST['tgl_sk_pendirian'] ?: null;
$status_kepemilikan = $_POST['status_kepemilikan'];
$sk_izin_operasional = $_POST['sk_izin_operasional'];
$tgl_sk_izin_operasional = $_POST['tgl_sk_izin_operasional'] ?: null;
$kebutuhan_khusus_dilayani = $_POST['kebutuhan_khusus_dilayani'];
$nomor_rekening = $_POST['nomor_rekening'];
$nama_bank = $_POST['nama_bank'];
$cabang_kcp_unit = $_POST['cabang_kcp_unit'];
$rekening_atas_nama = $_POST['rekening_atas_nama'];
$mbs = $_POST['mbs'];
$luas_tanah_milik_m2 = $_POST['luas_tanah_milik_m2'] ?: null;
$luas_tanah_bukan_milik_m2 = $_POST['luas_tanah_bukan_milik_m2'] ?: null;
$nama_wajib_pajak = $_POST['nama_wajib_pajak'];
$npwp = $_POST['npwp'];

$query = mysqli_query($connection, "INSERT INTO sekolah_pelengkap 
                                   (npsn_fk, sk_pendirian, tgl_sk_pendirian, status_kepemilikan, 
                                    sk_izin_operasional, tgl_sk_izin_operasional, kebutuhan_khusus_dilayani, 
                                    nomor_rekening, nama_bank, cabang_kcp_unit, rekening_atas_nama, mbs, 
                                    luas_tanah_milik_m2, luas_tanah_bukan_milik_m2, nama_wajib_pajak, npwp) 
                                   VALUES 
                                   ('$npsn_fk', '$sk_pendirian', " . ($tgl_sk_pendirian ? "'$tgl_sk_pendirian'" : "NULL") . ", 
                                    '$status_kepemilikan', '$sk_izin_operasional', " . ($tgl_sk_izin_operasional ? "'$tgl_sk_izin_operasional'" : "NULL") . ", 
                                    '$kebutuhan_khusus_dilayani', '$nomor_rekening', '$nama_bank', '$cabang_kcp_unit', 
                                    '$rekening_atas_nama', '$mbs', " . ($luas_tanah_milik_m2 ? "'$luas_tanah_milik_m2'" : "NULL") . ", 
                                    " . ($luas_tanah_bukan_milik_m2 ? "'$luas_tanah_bukan_milik_m2'" : "NULL") . ", 
                                    '$nama_wajib_pajak', '$npwp')");

if ($query) {
  $_SESSION['info'] = [
    'status' => 'success',
    'message' => 'Berhasil menambah data pelengkap sekolah'
  ];
  header('Location: ./pelengkap.php');
} else {
  $_SESSION['info'] = [
    'status' => 'failed',
    'message' => mysqli_error($connection)
  ];
  header('Location: ./pelengkap.php');
}
?>