<?php
require_once '../helper/connection.php';
if (!defined('BASE_URL')) { define('BASE_URL', '.'); }
$page_title = 'Selamat Datang - Dapodik Kalimantan Selatan';
$totalSekolah = mysqli_fetch_array(mysqli_query($connection, "SELECT COUNT(*) as total FROM sekolah_identitas"))['total'] ?? 0;
$totalSiswa = mysqli_fetch_array(mysqli_query($connection, "SELECT SUM(pd_total) as total FROM rekap_ptk_pd"))['total'] ?? 0;
$totalGuru = mysqli_fetch_array(mysqli_query($connection, "SELECT SUM(ptk_total) as total FROM rekap_ptk_pd"))['total'] ?? 0;
$totalKabupaten = mysqli_fetch_array(mysqli_query($connection, "SELECT COUNT(*) as total FROM kabupaten_kota"))['total'] ?? 0;
?>
<!DOCTYPE html>
<html class="no-js" lang="id">
<head>
    <meta charset="utf-8" />
    <meta http-equiv="x-ua-compatible" content="ie=edge" />
    <title><?= $page_title ?></title>
    <meta name="description" content="Sistem Informasi Data Pokok Pendidikan Provinsi Kalimantan Selatan" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <link rel="shortcut icon" type="image/x-icon" href="<?= BASE_URL ?>/assets/img/logo/logo.png" />
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/bootstrap.min.css" />
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/lineicons.css" />
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/animate.css" />
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/main.css" />
    <style>
        .blue-btn { background-color: #0066cc !important; border-color: #0066cc !important; }
        .blue-btn:hover { background-color: #0052a3 !important; border-color: #0052a3 !important; }
        .navbar-area .logo-icon, .navbar-area .logo-text { fill: #ffffff; transition: fill 0.4s ease; }
        .navbar-area.sticky .logo-icon { fill: url(#newLogoGrad); }
        .navbar-area.sticky .logo-text { fill: #333; }
        .hero-content-center { text-align: center; }
    </style>
</head>
<body>
    <div class="preloader"><div class="loader"><div class="spinner"><div class="spinner-container"><div class="spinner-rotator"><div class="spinner-left"><div class="spinner-circle"></div></div><div class="spinner-right"><div class="spinner-circle"></div></div></div></div></div></div></div>

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
                                    <li class="nav-item"><a class="page-scroll active" href="#home">Beranda</a></li>
                                    <li class="nav-item"><a class="page-scroll" href="#statistik">Statistik</a></li>
                                    <li class="nav-item"><a class="page-scroll" href="#layanan">Layanan</a></li>
                                    <li class="nav-item"><a class="page-scroll" href="#tentang">Tentang</a></li>
                                    <li class="nav-item"><a class="nav-link" href="../login.php">Login</a></li>
                                </ul>
                            </div>
                        </nav>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <section id="home" class="hero-section">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-xl-8 col-lg-10 col-md-12">
                    <div class="hero-content hero-content-center">
                        <img src="assets/img/logo/logo.png" alt="Logo Dapodik Kalimantan Selatan" style="width:120px; height:auto; margin-bottom:20px; animation: fadeInDown 1s ease;">
                        <h1 class="wow fadeInDown" data-wow-delay=".2s">BTIKP Kalimantan Selatan</h1>
                        <p class="wow fadeInLeft" data-wow-delay=".4s">Sistem Informasi Data Pokok Pendidikan Provinsi Kalimantan Selatan. Akurat, terkini, dan mudah diakses.</p>
                        <a href="#layanan" class="main-btn btn-hover blue-btn wow fadeInUp page-scroll" data-wow-delay=".6s">Jelajahi Layanan</a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section id="statistik" class="feature-section pt-120">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-6"><div class="section-title text-center mb-60"><h2 class="wow fadeInUp" data-wow-delay=".2s">Statistik Pendidikan</h2><p class="wow fadeInUp" data-wow-delay=".4s">Data terkini pendidikan di Provinsi Kalimantan Selatan</p></div></div>
            </div>
            <div class="row justify-content-center">
                <div class="col-lg-3 col-md-6 col-sm-10"><div class="single-feature"><div class="icon"><i class="lni lni-graduation"></i></div><div class="content"><h3><?= number_format($totalSekolah) ?></h3><p>Sekolah</p></div></div></div>
                <div class="col-lg-3 col-md-6 col-sm-10"><div class="single-feature"><div class="icon"><i class="lni lni-users"></i></div><div class="content"><h3><?= number_format($totalSiswa) ?></h3><p>Peserta Didik</p></div></div></div>
                <div class="col-lg-3 col-md-6 col-sm-10"><div class="single-feature"><div class="icon"><i class="lni lni-briefcase"></i></div><div class="content"><h3><?= number_format($totalGuru) ?></h3><p>Pendidik & Tendik</p></div></div></div>
                <div class="col-lg-3 col-md-6 col-sm-10"><div class="single-feature"><div class="icon"><i class="lni lni-map"></i></div><div class="content"><h3><?= number_format($totalKabupaten) ?></h3><p>Kabupaten/Kota</p></div></div></div>
            </div>
        </div>
    </section>

    <section id="layanan" class="pt-120 pb-120">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-6"><div class="section-title text-center mb-60"><h2 class="wow fadeInUp" data-wow-delay=".2s">Layanan Kami</h2><p class="wow fadeInUp" data-wow-delay=".4s">Akses informasi pendidikan dengan mudah dan cepat melalui layanan-layanan unggulan kami.</p></div></div>
            </div>
            <div class="row justify-content-center">
                <div class="col-lg-4 col-md-6"><div class="box-style color-1 wow fadeInUp" data-wow-delay=".2s"><div class="icon"><i class="lni lni-database"></i></div><div class="content"><h3>Data Sekolah</h3><p>Informasi lengkap tentang sekolah dari jenjang SD hingga SMK se-Provinsi Kalimantan Selatan.</p><a href="data-sekolah.php" class="main-btn border-btn btn-hover">Lihat Data</a></div></div></div>
                <div class="col-lg-4 col-md-6"><div class="box-style color-2 wow fadeInUp" data-wow-delay=".4s"><div class="icon"><i class="lni lni-map"></i></div><div class="content"><h3>Data Wilayah</h3><p>Database wilayah administratif meliputi Kabupaten/Kota dan Kecamatan di Kalimantan Selatan.</p><a href="data-wilayah.php" class="main-btn border-btn btn-hover">Lihat Data</a></div></div></div>
                <div class="col-lg-4 col-md-6"><div class="box-style color-3 wow fadeInUp" data-wow-delay=".6s"><div class="icon"><i class="lni lni-bar-chart"></i></div><div class="content"><h3>Statistik</h3><p>Visualisasi data pendidikan dalam bentuk grafik interaktif dan peta sebaran sekolah.</p><a href="statistik.php" class="main-btn border-btn btn-hover">Lihat Statistik</a></div></div></div>
            </div>
        </div>
    </section>

    <section id="tentang" class="pt-120 pb-120">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-8"><div class="section-title text-center mb-60"><h2 class="wow fadeInUp" data-wow-delay=".2s">Tentang Dapodik Kalsel</h2><p class="wow fadeInUp" data-wow-delay=".4s">Sistem Informasi Data Pokok Pendidikan (Dapodik) Kalimantan Selatan adalah platform digital yang menyediakan informasi lengkap mengenai data pendidikan di Provinsi Kalimantan Selatan. Kami berkomitmen untuk menyediakan data yang akurat, terkini, dan mudah diakses untuk mendukung perencanaan dan pengambilan keputusan di bidang pendidikan.</p></div></div>
            </div>
            <div class="row justify-content-center">
                <div class="col-lg-4 col-md-6"><div class="box-style color-1 text-center wow fadeInUp" data-wow-delay=".2s"><div class="icon"><i class="lni lni-target"></i></div><div class="content"><h3>Visi Kami</h3><p>Menjadi sistem informasi pendidikan terdepan yang menyediakan data akurat dan terpercaya untuk kemajuan pendidikan Kalimantan Selatan.</p></div></div></div>
                <div class="col-lg-4 col-md-6"><div class="box-style color-2 text-center wow fadeInUp" data-wow-delay=".4s"><div class="icon"><i class="lni lni-rocket"></i></div><div class="content"><h3>Misi Kami</h3><p>Menyediakan platform data pendidikan yang terintegrasi, transparan, dan mudah diakses untuk seluruh pemangku kepentingan pendidikan.</p></div></div></div>
                <div class="col-lg-4 col-md-6"><div class="box-style color-3 text-center wow fadeInUp" data-wow-delay=".6s"><div class="icon"><i class="lni lni-shield"></i></div><div class="content"><h3>Komitmen Kami</h3><p>Berkomitmen untuk Menjaga keamanan, integritas, dan keakuratan data pendidikan serta memberikan layanan terbaik kepada semua pengguna sistem.</p></div></div></div>
            </div>
        </div>
    </section>

    <?php require_once 'footer.php'; ?>

    <a href="#" class="scroll-top btn-hover"><i class="lni lni-chevron-up"></i></a>

    <script src="<?= BASE_URL ?>/assets/js/bootstrap.bundle.min.js"></script>
    <script src="<?= BASE_URL ?>/assets/js/wow.min.js"></script>
    <script src="<?= BASE_URL ?>/assets/js/main.js"></script>
</body>
</html>