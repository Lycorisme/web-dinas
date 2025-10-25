<?php
require_once '../helper/connection.php';
if (!defined('BASE_URL')) { define('BASE_URL', '.'); }

$perPage = 10;
$pageKab = isset($_GET['page_kab']) ? intval($_GET['page_kab']) : 1;
$offsetKab = ($pageKab - 1) * $perPage;
$countKabQuery = "SELECT COUNT(*) as total FROM kabupaten_kota";
$totalKabupaten = mysqli_fetch_assoc(mysqli_query($connection, $countKabQuery))['total'];
$totalPagesKab = ceil($totalKabupaten / $perPage);
$kabupatenQuery = "SELECT kk.id_kabupaten, kk.nama_kabupaten, (SELECT COUNT(*) FROM kecamatan WHERE id_kabupaten_fk = kk.id_kabupaten) as total_kecamatan, (SELECT COUNT(*) FROM sekolah_identitas si JOIN kecamatan k ON si.id_kecamatan_fk = k.id_kecamatan WHERE k.id_kabupaten_fk = kk.id_kabupaten) as total_sekolah FROM kabupaten_kota kk ORDER BY kk.nama_kabupaten LIMIT $perPage OFFSET $offsetKab";
$kabupatenResult = mysqli_query($connection, $kabupatenQuery);

$pageKec = isset($_GET['page_kec']) ? intval($_GET['page_kec']) : 1;
$offsetKec = ($pageKec - 1) * $perPage;
$countKecQuery = "SELECT COUNT(*) as total FROM kecamatan";
$totalKecamatan = mysqli_fetch_assoc(mysqli_query($connection, $countKecQuery))['total'];
$totalPagesKec = ceil($totalKecamatan / $perPage);
$kecamatanQuery = "SELECT k.id_kecamatan, k.nama_kecamatan, kk.nama_kabupaten, (SELECT COUNT(*) FROM sekolah_identitas WHERE id_kecamatan_fk = k.id_kecamatan) as total_sekolah FROM kecamatan k JOIN kabupaten_kota kk ON k.id_kabupaten_fk = kk.id_kabupaten ORDER BY kk.nama_kabupaten, k.nama_kecamatan LIMIT $perPage OFFSET $offsetKec";
$kecamatanResult = mysqli_query($connection, $kecamatanQuery);
?>
<!DOCTYPE html>
<html class="no-js" lang="id">
<head>
    <meta charset="utf-8" />
    <meta http-equiv="x-ua-compatible" content="ie=edge" />
    <title>Data Wilayah - Dapodik Kalimantan Selatan</title>
    <meta name="description" content="Informasi lengkap wilayah administratif meliputi Kabupaten/Kota dan Kecamatan di Provinsi Kalimantan Selatan." />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <link rel="shortcut icon" type="image/x-icon" href="<?= BASE_URL ?>/assets/img/logo/logo.png" />
    
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/bootstrap.min.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" />
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/lineicons.css" />
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/animate.css" />
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/main.css" />
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/data-wilayah-style.css" />
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
                                    <li class="nav-item"><a class="active" href="data-wilayah.php">Data Wilayah</a></li>
                                    <li class="nav-item"><a href="statistik.php">Statistik</a></li>
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
                <div class="col-md-6"><h1 class="page-title wow fadeInLeft" data-wow-delay=".2s">Data Wilayah</h1></div>
                <div class="col-md-6"><ul class="breadcrumb-nav wow fadeInRight" data-wow-delay=".4s"><li><a href="index.php">Beranda</a></li><li>Data Wilayah</li></ul></div>
            </div>
        </div>
    </section>

    <section id="data-wilayah" class="data-wilayah-section pt-120 pb-120">
        <div class="container">
            <div class="row wow fadeInUp" data-wow-delay="0.2s">
                <div class="col-md-6 mb-4"><div class="stat-card"><div class="stat-icon icon-1"><i class="lni lni-world-alt"></i></div><div class="stat-content"><h3 class="stat-number"><?= number_format($totalKabupaten) ?></h3><p class="stat-title">Total Kabupaten/Kota</p></div></div></div>
                <div class="col-md-6 mb-4"><div class="stat-card"><div class="stat-icon icon-2"><i class="lni lni-map"></i></div><div class="stat-content"><h3 class="stat-number"><?= number_format($totalKecamatan) ?></h3><p class="stat-title">Total Kecamatan</p></div></div></div>
            </div>

            <div class="card table-card wow fadeInUp" data-wow-delay="0.4s">
                <div class="card-header">
                    <ul class="nav nav-tabs card-header-tabs" id="wilayahTabs" role="tablist">
                        <li class="nav-item" role="presentation"><button class="nav-link active" id="kabupaten-tab-link" data-bs-toggle="tab" data-bs-target="#kabupaten-tab" type="button"><i class="lni lni-world-alt"></i> Kabupaten/Kota</button></li>
                        <li class="nav-item" role="presentation"><button class="nav-link" id="kecamatan-tab-link" data-bs-toggle="tab" data-bs-target="#kecamatan-tab" type="button"><i class="lni lni-map-marker"></i> Kecamatan</button></li>
                    </ul>
                </div>
                <div class="card-body">
                    <div class="tab-content" id="wilayahTabsContent">
                        <div class="tab-pane fade show active" id="kabupaten-tab" role="tabpanel">
                            <div class="table-responsive"><table class="table table-hover align-middle">
                                <thead><tr><th>No</th><th>Kabupaten/Kota</th><th class="text-center">Jml. Kecamatan</th><th class="text-center">Jml. Sekolah</th></tr></thead>
                                <tbody>
                                    <?php $no = $offsetKab + 1; mysqli_data_seek($kabupatenResult, 0); while ($data = mysqli_fetch_array($kabupatenResult)) : ?>
                                    <tr><td><strong><?= $no++ ?></strong></td><td><strong><?= htmlspecialchars($data['nama_kabupaten']) ?></strong></td><td class="text-center"><span class="badge-custom badge-info"><?= number_format($data['total_kecamatan']) ?></span></td><td class="text-center"><span class="badge-custom badge-success"><?= number_format($data['total_sekolah']) ?></span></td></tr>
                                    <?php endwhile; ?>
                                </tbody>
                            </table></div>
                            <?php if ($totalPagesKab > 1): ?><nav class="mt-4"><ul class="pagination justify-content-center">
                                <li class="page-item <?= ($pageKab <= 1) ? 'disabled' : '' ?>"><a class="page-link" href="?page_kab=<?= $pageKab - 1 ?>#kabupaten-tab">&laquo;</a></li>
                                <?php for ($i = max(1, $pageKab - 2); $i <= min($totalPagesKab, $pageKab + 2); $i++): ?><li class="page-item <?= ($i == $pageKab) ? 'active' : '' ?>"><a class="page-link" href="?page_kab=<?= $i ?>#kabupaten-tab"><?= $i ?></a></li><?php endfor; ?>
                                <li class="page-item <?= ($pageKab >= $totalPagesKab) ? 'disabled' : '' ?>"><a class="page-link" href="?page_kab=<?= $pageKab + 1 ?>#kabupaten-tab">&raquo;</a></li>
                            </ul></nav><?php endif; ?>
                        </div>

                        <div class="tab-pane fade" id="kecamatan-tab" role="tabpanel">
                             <div class="table-responsive"><table class="table table-hover align-middle">
                                <thead><tr><th>No</th><th>Kecamatan</th><th>Kabupaten/Kota</th><th class="text-center">Jml. Sekolah</th><th class="text-center">Aksi</th></tr></thead>
                                <tbody>
                                    <?php $no = $offsetKec + 1; mysqli_data_seek($kecamatanResult, 0); while ($data = mysqli_fetch_array($kecamatanResult)) : ?>
                                    <tr><td><strong><?= $no++ ?></strong></td><td><strong><?= htmlspecialchars($data['nama_kecamatan']) ?></strong></td><td><?= htmlspecialchars($data['nama_kabupaten']) ?></td><td class="text-center"><span class="badge-custom badge-primary"><?= number_format($data['total_sekolah']) ?></span></td><td class="text-center"><a href="data-sekolah.php?id_kecamatan=<?= $data['id_kecamatan'] ?>" class="main-btn border-btn btn-hover btn-sm">Lihat Sekolah</a></td></tr>
                                    <?php endwhile; ?>
                                </tbody>
                            </table></div>
                            <?php if ($totalPagesKec > 1): ?><nav class="mt-4"><ul class="pagination justify-content-center">
                                <li class="page-item <?= ($pageKec <= 1) ? 'disabled' : '' ?>"><a class="page-link" href="?page_kec=<?= $pageKec - 1 ?>#kecamatan-tab">&laquo;</a></li>
                                <?php for ($i = max(1, $pageKec - 2); $i <= min($totalPagesKec, $pageKec + 2); $i++): ?><li class="page-item <?= ($i == $pageKec) ? 'active' : '' ?>"><a class="page-link" href="?page_kec=<?= $i ?>#kecamatan-tab"><?= $i ?></a></li><?php endfor; ?>
                                <li class="page-item <?= ($pageKec >= $totalPagesKec) ? 'disabled' : '' ?>"><a class="page-link" href="?page_kec=<?= $pageKec + 1 ?>#kecamatan-tab">&raquo;</a></li>
                            </ul></nav><?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <?php require_once 'footer.php'; ?>

    <a href="#" class="scroll-top btn-hover"><i class="lni lni-chevron-up"></i></a>

    <script src="<?= BASE_URL ?>/assets/js/bootstrap.bundle.min.js"></script>
    <script src="<?= BASE_URL ?>/assets/js/wow.min.js"></script>
    <script src="<?= BASE_URL ?>/assets/js/main.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            let hash = window.location.hash;
            if (hash) {
                let tabTrigger = document.querySelector('button[data-bs-target="' + hash + '"]');
                if (tabTrigger) { new bootstrap.Tab(tabTrigger).show(); }
            }
            const tabElms = document.querySelectorAll('button[data-bs-toggle="tab"]');
            tabElms.forEach(function (tabElm) {
                tabElm.addEventListener('shown.bs.tab', function (event) {
                    let newHash = event.target.getAttribute('data-bs-target');
                     if(history.pushState) { history.pushState(null, null, newHash); } else { window.location.hash = newHash; }
                });
            });
        });
    </script>
</body>
</html>