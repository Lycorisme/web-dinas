<?php
// Ambil data dari database (sudah dilakukan di export_pdf.php)
// Variabel yang tersedia: $data_sekolah, $nama_kecamatan, $nama_jenjang
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Data Sekolah</title>
    <style>
        body { 
            font-family: 'Times New Roman', serif; 
            font-size: 12pt;
            margin: 0;
            padding: 20px;
        }
        .kop-surat {
            margin-bottom: 20px;
        }
        .kop-surat .header-row {
            display: table;
            width: 100%;
            margin-bottom: 10px;
        }
        .kop-surat .logo-col {
            display: table-cell;
            width: 100px;
            vertical-align: middle;
            text-align: center;
        }
        .kop-surat .header-text {
            display: table-cell;
            vertical-align: middle;
            text-align: center;
        }
        .kop-surat .header-text h5, 
        .kop-surat .header-text h6 { 
            margin: 2px 0; 
            font-weight: bold;
        }
        .kop-surat .header-text p { 
            font-size: 10pt; 
            margin: 2px 0; 
        }
        .garis-kop { 
            border-bottom: 2px solid black; 
            margin: 20px 0;
        }
        .content {
            margin-top: 20px;
        }
        .judul {
            text-align: center;
            font-size: 16pt;
            font-weight: bold;
            margin: 20px 0;
        }
        .info {
            margin-bottom: 20px;
            font-size: 12pt;
        }
        .info p {
            margin: 5px 0;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 11pt;
            margin: 20px 0;
        }
        table th,
        table td {
            border: 1px solid black;
            padding: 8px;
            text-align: center;
        }
        table th {
            background-color: #f8f9fa;
            font-weight: bold;
            font-size: 11pt;
        }
        .text-center {
            text-align: center;
        }
        .ttd {
            margin-top: 50px;
            width: 100%;
        }
        .ttd-table {
            width: 100%;
            border: none;
        }
        .ttd-table td {
            border: none;
            padding: 5px;
        }
        .ttd-content {
            text-align: center;
        }
        .logo-img {
            max-width: 80px;
            height: auto;
        }
    </style>
</head>
<body>
    <!-- Kop Surat -->
    <div class="kop-surat">
        <div class="header-row">
            <div class="logo-col">
                <?php
                // Path absolut ke logo
                $pathToImage = __DIR__ . '/../../assets/img/logo.png';
                if (file_exists($pathToImage)) {
                    $type = pathinfo($pathToImage, PATHINFO_EXTENSION);
                    $data = file_get_contents($pathToImage);
                    $base64Image = 'data:image/' . $type . ';base64,' . base64_encode($data);
                    echo '<img src="' . $base64Image . '" class="logo-img">';
                }
                ?>
            </div>
            <div class="header-text">
                <h5>PEMERINTAH PROVINSI KALIMANTAN SELATAN</h5>
                <h5>DINAS PENDIDIKAN DAN KEBUDAYAAN</h5>
                <h6>BALAI TEKNOLOGI INFORMASI DAN KOMUNIKASI PENDIDIKAN</h6>
                <p>Jl. Perdagangan Komplek Bumi Indah Lestari II<br>
                Website: http://www.disdik-kalsel.org E-mail: btikp@yahoo.co.id</p>
            </div>
        </div>
        <hr class="garis-kop">
    </div>

    <!-- Isi Laporan -->
    <div class="content">
        <div class="judul">REKAPITULASI DATA SEKOLAH</div>
        
        <div class="info">
            <p><strong>Berdasarkan Filter:</strong> Kecamatan <?php echo htmlspecialchars($nama_kecamatan); ?> - Jenjang <?php echo htmlspecialchars($nama_jenjang); ?></p>
            <p><strong>Tanggal Cetak:</strong> <?php echo date('d F Y'); ?></p>
            <p><strong>Jumlah Data:</strong> <?php echo count($data_sekolah); ?> sekolah</p>
        </div>

        <!-- Tabel Data -->
        <table>
            <thead>
                <tr>
                    <th width="5%">No</th>
                    <th width="10%">NPSN</th>
                    <th width="25%">Nama Sekolah</th>
                    <th width="8%">Jenjang</th>
                    <th width="8%">Status</th>
                    <th width="15%">Alamat</th>
                    <th width="12%">Kecamatan</th>
                    <th width="12%">Kabupaten/Kota</th>
                    <th width="8%">Total PTK</th>
                    <th width="8%">Total PD</th>
                    <th width="8%">Total Sarana</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($data_sekolah)): ?>
                <tr>
                    <td colspan="11" class="text-center">Tidak ada data sekolah yang sesuai dengan filter</td>
                </tr>
                <?php else: ?>
                <?php $no = 1; foreach ($data_sekolah as $sekolah): ?>
                <tr>
                    <td class="text-center"><?php echo $no++; ?></td>
                    <td><?php echo htmlspecialchars($sekolah['npsn']); ?></td>
                    <td><?php echo htmlspecialchars($sekolah['nama_sekolah']); ?></td>
                    <td><?php echo htmlspecialchars($sekolah['jenjang_pendidikan']); ?></td>
                    <td><?php echo htmlspecialchars($sekolah['status_sekolah']); ?></td>
                    <td><?php echo htmlspecialchars($sekolah['alamat_jalan']); ?></td>
                    <td><?php echo htmlspecialchars($sekolah['nama_kecamatan']); ?></td>
                    <td><?php echo htmlspecialchars($sekolah['nama_kabupaten']); ?></td>
                    <td><?php echo $sekolah['total_ptk']; ?></td>
                    <td><?php echo $sekolah['total_pd']; ?></td>
                    <td><?php echo $sekolah['total_sarana']; ?></td>
                </tr>
                <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>

        <!-- Tanda Tangan -->
        <div class="ttd">
            <table class="ttd-table">
                <tr>
                    <td width="60%">&nbsp;</td>
                    <td width="40%" class="ttd-content">
                        <p>Kepala Balai,</p>
                        <br><br><br>
                        <p><strong><u>Nama Pejabat</u></strong><br>NIP. 1234567890</p>
                    </td>
                </tr>
            </table>
        </div>
    </div>
</body>
</html>