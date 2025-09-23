<?php
// Cek login
require_once __DIR__ . '/../helper/auth.php';
isLogin();

// Koneksi database
require_once __DIR__ . '/../helper/connection.php';

// Ambil data kecamatan dan jenjang untuk dropdown
$kecamatan_result = mysqli_query($connection, "SELECT DISTINCT kecamatan FROM dapodik.sekolah ORDER BY kecamatan");
$jenjang_result = mysqli_query($connection, "SELECT DISTINCT jenjang FROM dapodik.sekolah ORDER BY jenjang");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan & Ekspor Data</title>
    <!-- Stisla Admin Template CSS -->
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="../assets/modules/bootstrap/css/bootstrap.min.css">
</head>
<body>
    <div id="app">
        <div class="main-wrapper">
            <!-- Main Content -->
            <div class="main-content">
                <section class="section">
                    <div class="section-header">
                        <h1>Laporan & Ekspor Data Sekolah</h1>
                    </div>
                    <div class="section-body">
                        <div class="card">
                            <div class="card-header">
                                <h4>Filter Data</h4>
                            </div>
                            <div class="card-body">
                                <form method="get" action="">
                                    <div class="form-group">
                                        <label>Kecamatan</label>
                                        <select name="kecamatan" class="form-control">
                                            <option value="">Semua Kecamatan</option>
                                            <?php while ($row = mysqli_fetch_assoc($kecamatan_result)): ?>
                                                <option value="<?php echo $row['kecamatan']; ?>"><?php echo $row['kecamatan']; ?></option>
                                            <?php endwhile; ?>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label>Jenjang</label>
                                        <select name="jenjang" class="form-control">
                                            <option value="">Semua Jenjang</option>
                                            <?php while ($row = mysqli_fetch_assoc($jenjang_result)): ?>
                                                <option value="<?php echo $row['jenjang']; ?>"><?php echo $row['jenjang']; ?></option>
                                            <?php endwhile; ?>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <button type="submit" class="btn btn-primary">Tampilkan Data</button>
                                        <button type="submit" formaction="export_pdf.php" class="btn btn-success">Export PDF</button>
                                        <button type="submit" formaction="export_excel.php" class="btn btn-info">Export Excel</button>
                                    </div>
                                </form>
                            </div>
                        </div>

                        <!-- Tabel Data (opsional, jika ingin menampilkan data di halaman ini) -->
                        <?php
                        if (isset($_GET['kecamatan']) || isset($_GET['jenjang'])) {
                            $kecamatan = isset($_GET['kecamatan']) ? mysqli_real_escape_string($connection, $_GET['kecamatan']) : '';
                            $jenjang = isset($_GET['jenjang']) ? mysqli_real_escape_string($connection, $_GET['jenjang']) : '';

                            $query = "SELECT * FROM dapodik.sekolah WHERE 1=1";
                            if (!empty($kecamatan)) $query .= " AND kecamatan = '$kecamatan'";
                            if (!empty($jenjang)) $query .= " AND jenjang = '$jenjang'";
                            $result = mysqli_query($connection, $query);
                        ?>
                        <div class="card">
                            <div class="card-header">
                                <h4>Data Sekolah</h4>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-striped table-bordered">
                                        <thead>
                                            <tr>
                                                <th>No</th>
                                                <th>NPSN</th>
                                                <th>Nama Sekolah</th>
                                                <th>Alamat</th>
                                                <th>Status</th>
                                                <th>Jenjang</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php $no = 1; while ($row = mysqli_fetch_assoc($result)): ?>
                                            <tr>
                                                <td><?php echo $no++; ?></td>
                                                <td><?php echo $row['npsn']; ?></td>
                                                <td><?php echo $row['nama']; ?></td>
                                                <td><?php echo $row['alamat']; ?></td>
                                                <td><?php echo $row['status']; ?></td>
                                                <td><?php echo $row['jenjang']; ?></td>
                                            </tr>
                                            <?php endwhile; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <?php } ?>
                    </div>
                </section>
            </div>
        </div>
    </div>

    <!-- Stisla Admin Template JS -->
    <script src="../assets/js/app.js"></script>
</body>
</html>