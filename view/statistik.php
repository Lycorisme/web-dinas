<?php
require_once '../helper/connection.php';
if (!defined('BASE_URL')) { define('BASE_URL', '.'); }

// Data awal (keseluruhan) untuk ditampilkan saat halaman pertama kali dimuat
$totalSekolah = mysqli_fetch_array(mysqli_query($connection, "SELECT COUNT(*) as total FROM sekolah_identitas"))['total'] ?? 0;
$rekapResult = mysqli_fetch_assoc(mysqli_query($connection, "SELECT SUM(pd_total) as total_siswa, SUM(ptk_total) as total_guru FROM rekap_ptk_pd"));
$totalSiswa = $rekapResult['total_siswa'] ?? 0;
$totalGuru = $rekapResult['total_guru'] ?? 0;
$rasioSiswaGuru = ($totalGuru > 0) ? round($totalSiswa / $totalGuru, 1) : 0;
?>
<!DOCTYPE html>
<html class="no-js" lang="id">
<head>
    <meta charset="utf-8" />
    <meta http-equiv="x-ua-compatible" content="ie=edge" />
    <title>Statistik Pendidikan - Dapodik Kalimantan Selatan</title>
    <meta name="description" content="Visualisasi data pendidikan dalam bentuk grafik interaktif dan peta sebaran sekolah." />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <link rel="shortcut icon" type="image/x-icon" href="<?= BASE_URL ?>/assets/img/logo/logo.png" />
    
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/bootstrap.min.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" />
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/lineicons.css" />
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/animate.css" />
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/main.css" />
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css" />
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/statistik-style.css" />
</head>
<body>
    <div class="preloader"><div class="loader"><div class="ytp-spinner"><div class="ytp-spinner-container"><div class="ytp-spinner-rotator"><div class="ytp-spinner-left"><div class="ytp-spinner-circle"></div></div><div class="ytp-spinner-right"><div class="ytp-spinner-circle"></div></div></div></div></div></div></div>

    <header class="header">
        <div class="navbar-area">
            <div class="container">
                <div class="row align-items-center">
                    <div class="col-lg-12">
                        <nav class="navbar navbar-expand-lg">
                            <a class="navbar-brand d-flex align-items-center" href="index.php">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 200 40" style="height: 40px;"><defs><linearGradient id="newLogoGrad" x1="0%" y1="0%" x2="100%" y2="100%"><stop offset="0%" style="stop-color:#13f1fc;" /><stop offset="100%" style="stop-color:#0470dc;" /></linearGradient></defs><g transform="translate(0, -2.5)"><path class="logo-icon" d="M36.1,13.4c-3.1-3.1-7.2-5-11.7-5c-9.2,0-16.6,7.4-16.6,16.6c0,4.5,1.8,8.6,5,11.7l0,0c3.1,3.1,7.2,5,11.7,5 c9.2,0,16.6-7.4,16.6-16.6C41.1,20.6,39.2,16.5,36.1,13.4z M24.4,36.6c-6.9,0-12.5-5.6-12.5-12.5c0-6.9,5.6-12.5,12.5-12.5 c2.9,0,5.6,1,7.8,2.7c-2.4,2.1-4,5.2-4,8.7c0,3.5,1.5,6.6,4,8.7C29.9,35.6,27.3,36.6,24.4,36.6z" /></g><text class="logo-text" x="52" y="26" font-family="'Segoe UI', Arial, sans-serif" font-size="18" font-weight="600">Infinity Catch</text></svg>
                            </a>
                            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent"><span class="toggler-icon"></span><span class="toggler-icon"></span><span class="toggler-icon"></span></button>
                            <div class="collapse navbar-collapse sub-menu-bar" id="navbarSupportedContent">
                                <ul id="nav" class="navbar-nav ms-auto">
                                    <li class="nav-item"><a href="index.php">Beranda</a></li>
                                    <li class="nav-item"><a href="data-sekolah.php">Data Sekolah</a></li>
                                    <li class="nav-item"><a href="data-wilayah.php">Data Wilayah</a></li>
                                    <li class="nav-item"><a class="active" href="statistik.php">Statistik</a></li>
                                    <li class="nav-item"><a href="../login.php">Login</a></li>
                                </ul>
                            </div>
                        </nav>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <section class="page-title-area">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-6"><h1 class="page-title wow fadeInLeft" data-wow-delay=".2s">Statistik Pendidikan</h1></div>
                <div class="col-md-6"><ul class="breadcrumb-nav wow fadeInRight" data-wow-delay=".4s"><li><a href="index.php">Beranda</a></li><li>Statistik</li></ul></div>
            </div>
        </div>
    </section>

    <section id="statistik" class="statistik-section pt-120 pb-120">
        <div class="container">
            
            <div class="card filter-card wow fadeInUp" data-wow-delay="0.2s">
                <div class="card-header"><h5 class="card-title"><i class="lni lni-funnel"></i> Filter Statistik Wilayah</h5></div>
                <div class="card-body">
                    <div class="row g-3 align-items-end">
                        <div class="col-lg-5 col-md-6">
                            <label for="filterKabupaten" class="form-label">Kabupaten/Kota</label>
                            <select id="filterKabupaten" class="form-select"><option value="0">Memuat...</option></select>
                        </div>
                        <div class="col-lg-5 col-md-6">
                            <label for="filterKecamatan" class="form-label">Kecamatan</label>
                            <select id="filterKecamatan" class="form-select" disabled><option value="0">Pilih Kabupaten Dulu</option></select>
                        </div>
                        <div class="col-lg-2 col-md-12">
                            <button id="filterButton" class="main-btn btn-hover w-100"><i class="lni lni-search-alt"></i> Terapkan</button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row wow fadeInUp" data-wow-delay="0.3s">
                <div class="col-lg-3 col-md-6 mb-4"><div class="stat-card"><div class="stat-icon icon-1"><i class="lni lni-apartment"></i></div><div class="stat-content"><h3 id="totalSekolah" class="stat-number"><?= number_format($totalSekolah) ?></h3><p class="stat-title">Total Sekolah</p></div></div></div>
                <div class="col-lg-3 col-md-6 mb-4"><div class="stat-card"><div class="stat-icon icon-2"><i class="lni lni-users"></i></div><div class="stat-content"><h3 id="totalSiswa" class="stat-number"><?= number_format($totalSiswa) ?></h3><p class="stat-title">Total Siswa</p></div></div></div>
                <div class="col-lg-3 col-md-6 mb-4"><div class="stat-card"><div class="stat-icon icon-3"><i class="lni lni-briefcase"></i></div><div class="stat-content"><h3 id="totalGuru" class="stat-number"><?= number_format($totalGuru) ?></h3><p class="stat-title">Total PTK</p></div></div></div>
                <div class="col-lg-3 col-md-6 mb-4"><div class="stat-card"><div class="stat-icon icon-4"><i class="lni lni-stats-up"></i></div><div class="stat-content"><h3 id="rasioSiswaGuru" class="stat-number">1 : <?= $rasioSiswaGuru ?></h3><p class="stat-title">Rasio Siswa/Guru</p></div></div></div>
            </div>

            <div class="card map-card wow fadeInUp" data-wow-delay="0.4s">
                <div class="card-header"><h5 class="card-title"><i class="lni lni-map-marker"></i> Peta Sebaran Sekolah</h5></div>
                <div class="card-body">
                    <div id="school-map"><div class="map-loading"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div></div></div>
                    <div class="map-legend"><span class="legend-item" style="--color: #28a745;">SD</span><span class="legend-item" style="--color: #ffc107;">SMP</span><span class="legend-item" style="--color: #007bff;">SMA</span><span class="legend-item" style="--color: #6f42c1;">SMK</span></div>
                </div>
            </div>

            <div class="card charts-card wow fadeInUp" data-wow-delay="0.6s">
                <div class="card-header"><h5 class="card-title"><i class="lni lni-bar-chart"></i> Visualisasi Data</h5></div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-lg-6 mb-4"><h6 class="chart-title">Sekolah per Jenjang</h6><div class="chart-container"><canvas id="chartJenjang"></canvas></div></div>
                        <div class="col-lg-6 mb-4"><h6 class="chart-title">Sekolah per Status</h6><div class="chart-container"><canvas id="chartStatus"></canvas></div></div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <?php require_once 'footer.php'; ?>

    <a href="#" class="scroll-top btn-hover"><i class="lni lni-chevron-up"></i></a>
    
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js"></script>
    <script src="<?= BASE_URL ?>/assets/js/bootstrap.bundle.min.js"></script>
    <script src="<?= BASE_URL ?>/assets/js/wow.min.js"></script>
    <script src="<?= BASE_URL ?>/assets/js/main.js"></script>
    <script src="<?= BASE_URL ?>/assets/js/statistik.js" defer></script>
</body>
</html>