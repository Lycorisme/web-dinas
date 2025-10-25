<?php
// Ambil data dari database (sudah dilakukan di export_pdf_sarpras.php)
// Variabel yang tersedia: $data_sarpras, $nama_kecamatan, $nama_jenjang
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Data Sarana & Prasarana</title>
    <style>
        body { 
            font-family: 'Times New Roman', serif; 
            font-size: 12pt;
            margin: 0;
            padding: 20px;
        }
        .kop-surat {
            margin-bottom: 15px;
        }
        .header-container {
            width: 100%;
            margin-bottom: 15px;
        }
        .header-container::after {
            content: "";
            display: table;
            clear: both;
        }
        .logo-col {
            float: left;
            width: 120px;
            margin-right: 20px;
        }
        .logo-img {
            height: 180px;
            display: block;
        }
        .header-text {
            font-family: 'Times New Roman', serif;
            text-align: center;
            margin: 0 auto;
            width: fit-content;
            padding-left: 30px;
        }
        .kop-surat .header-text h5.instansi-utama { 
            margin: 3px 0; 
            font-weight: bold;
            font-size: 18pt;
            text-transform: uppercase;
        }
        .kop-surat .header-text h5.unit-kerja { 
            margin: 3px 0; 
            font-weight: bold;
            font-size: 18pt;
            text-transform: uppercase;
        }
        .kop-surat .header-text h6.sub-unit { 
            margin: 3px 0; 
            font-weight: bold;
            font-size: 14pt;
            text-transform: uppercase;
        }
        .kop-surat .header-text p { 
            font-size: 11pt; 
            margin: 2px 0; 
            font-weight: 500;
        }
        .garis-kop { 
            border-bottom: 3px solid black; 
            margin: 2px 0;
        }
        .content {
            margin-top: 20px;
        }
        .judul {
            text-align: center;
            font-size: 16pt;
            font-weight: bold;
            margin: 20px 0;
            text-transform: uppercase;
        }
        .info {
            margin-bottom: 20px;
            font-size: 12pt;
        }
        .info p {
            margin: 6px 0;
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
    </style>
</head>
<body>
    <!-- Kop Surat -->
    <div class="kop-surat">
        <div class="header-container">
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
                <h5 class="instansi-utama">Pemerintah Provinsi Kalimantan Selatan</h5>
                <h5 class="unit-kerja">Dinas Pendidikan Dan Kebudayaan</h5>
                <h6 class="sub-unit">Balai Teknologi Informasi Dan Komunikasi Pendidikan</h6>
                <p>Jl. Perdagangan Komplek Bumi Indah Lestari II<br>
                Website: http://www.disdik-kalsel.org E-mail: btikp@yahoo.co.id</p>
            </div>
        </div>
        <hr class="garis-kop">
    </div>

    <!-- Isi Laporan -->
    <div class="content">
        <div class="judul">Rekapitulasi Data Sarana & Prasarana</div>
        
        <div class="info">
            <p><strong><?php echo htmlspecialchars($nama_kecamatan); ?></strong> <?php echo date('d F Y'); ?></p>
            <p><strong>Jumlah Data:</strong> <?php echo count($data_sarpras); ?> sekolah</p>
        </div>

        <!-- Tabel Data -->
        <table>
            <thead>
                <tr>
                    <th width="5%">No</th>
                    <th width="10%">NPSN</th>
                    <th width="25%">Nama Sekolah</th>
                    <th width="8%">Jenjang</th>
                    <th width="12%">Kecamatan</th>
                    <th width="10%">Ruang Kelas</th>
                    <th width="10%">Ruang Lab</th>
                    <th width="10%">Ruang Perpus</th>
                    <th width="10%">Total Sarpras</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($data_sarpras)): ?>
                <tr>
                    <td colspan="9" class="text-center">Tidak ada data sarana & prasarana yang sesuai dengan filter</td>
                </tr>
                <?php else: ?>
                <?php $no = 1; foreach ($data_sarpras as $sarpras): ?>
                <tr>
                    <td class="text-center"><?php echo $no++; ?></td>
                    <td><?php echo htmlspecialchars($sarpras['npsn']); ?></td>
                    <td><?php echo htmlspecialchars($sarpras['nama_sekolah']); ?></td>
                    <td><?php echo htmlspecialchars($sarpras['jenjang_pendidikan']); ?></td>
                    <td><?php echo htmlspecialchars($sarpras['nama_kecamatan']); ?></td>
                    <td><?php echo $sarpras['ruang_kelas']; ?></td>
                    <td><?php echo $sarpras['ruang_lab']; ?></td>
                    <td><?php echo $sarpras['ruang_perpus']; ?></td>
                    <td><?php echo $sarpras['total_sarpras']; ?></td>
                </tr>
                <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</body>
</html>