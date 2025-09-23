<?php
// Ambil data dari database (sudah dilakukan di export_pdf.php)
// Variabel yang tersedia: $data_sekolah, $nama_kecamatan, $jenjang
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Data Sekolah</title>
    <!-- Menggunakan Bootstrap dari proyek -->
    <link rel="stylesheet" href="<?php echo $_SERVER['DOCUMENT_ROOT'] . '/dapodik3/assets/modules/bootstrap/css/bootstrap.min.css'; ?>">
    <style>
        body { 
            font-family: 'Times New Roman', serif; 
            font-size: 12pt;
        }
        .kop-surat .header-text { 
            text-align: center; 
        }
        .kop-surat .header-text h5, .kop-surat .header-text h6 { 
            margin: 0; 
            font-weight: bold;
        }
        .kop-surat .header-text p { 
            font-size: 10pt; 
            margin: 0; 
        }
        .garis-kop { 
            border-bottom: 2px solid black; 
            margin-bottom: 20px;
        }
        .table {
            font-size: 10pt;
        }
        .table th {
            background-color: #f8f9fa;
        }
        .ttd {
            margin-top: 50px;
        }
    </style>
</head>
<body>
    <!-- Kop Surat -->
    <div class="container-fluid kop-surat">
        <div class="row">
            <div class="col-2 text-center">
                <?php
                // Path absolut ke logo
                $pathToImage = $_SERVER['DOCUMENT_ROOT'] . '/dapodik3/assets/img/logo.png';
                if (file_exists($pathToImage)) {
                    $type = pathinfo($pathToImage, PATHINFO_EXTENSION);
                    $data = file_get_contents($pathToImage);
                    $base64Image = 'data:image/' . $type . ';base64,' . base64_encode($data);
                    echo '<img src="' . $base64Image . '" width="85px">';
                }
                ?>
            </div>
            <div class="col-10 header-text">
                <h5>PEMERINTAH PROVINSI KALIMANTAN SELATAN</h5>
                <h5>DINAS PENDIDIKAN DAN KEBUDAYAAN</h5>
                <h6>BALAI TEKNOLOGI INFORMASI DAN KOMUNIKASI PENDIDIKAN</h6>
                <p>Jl. Perdagangan Komplek Bumi Indah Lestari II Website: http//www.disdik-kalsel.org E-mail:btikp@yahoo.co.id</p>
            </div>
        </div>
        <hr class="garis-kop">
    </div>

    <!-- Isi Laporan -->
    <div class="container-fluid">
        <h4 class="text-center mt-4 mb-3">Rekapitulasi Data Sekolah</h4>
        <p><strong>Berdasarkan Filter:</strong> Kecamatan <?php echo htmlspecialchars($nama_kecamatan); ?> - Jenjang <?php echo htmlspecialchars($jenjang); ?></p>
        <p class="text-right"><strong>Tanggal Cetak:</strong> <?php echo date('d F Y'); ?></p>

        <!-- Tabel Data -->
        <table class="table table-bordered table-sm mt-3">
            <thead class="thead-light">
                <tr>
                    <th width="5%">No</th>
                    <th width="10%">NPSN</th>
                    <th>Nama Sekolah</th>
                    <th>Alamat</th>
                    <th width="10%">Status</th>
                    <th width="10%">Jenjang</th>
                </tr>
            </thead>
            <tbody>
                <?php $no = 1; foreach ($data_sekolah as $sekolah): ?>
                <tr>
                    <td><?php echo $no++; ?></td>
                    <td><?php echo htmlspecialchars($sekolah['npsn']); ?></td>
                    <td><?php echo htmlspecialchars($sekolah['nama']); ?></td>
                    <td><?php echo htmlspecialchars($sekolah['alamat']); ?></td>
                    <td><?php echo htmlspecialchars($sekolah['status']); ?></td>
                    <td><?php echo htmlspecialchars($sekolah['jenjang']); ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <!-- Tanda Tangan -->
        <div class="row ttd">
            <div class="col-7"></div>
            <div class="col-5 text-center">
                <p>Kepala Balai,</p>
                <br><br><br>
                <p><strong><u>Nama Pejabat</u></strong><br>NIP. 1234567890</p>
            </div>
        </div>
    </div>
</body>
</html>