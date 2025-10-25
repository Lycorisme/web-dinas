<?php
require_once '../helper/connection.php';
if (!defined('BASE_URL')) { define('BASE_URL', '.'); }

$filterKecamatan = isset($_GET['id_kecamatan']) ? intval($_GET['id_kecamatan']) : 0;
$filterJenjang = isset($_GET['jenjang']) ? $_GET['jenjang'] : '';

$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
$perPage = 10;
$offset = ($page - 1) * $perPage;

$sqlKec = "SELECT id_kecamatan, nama_kecamatan FROM kecamatan ORDER BY nama_kecamatan ASC";
$resultKec = mysqli_query($connection, $sqlKec);

$baseQuery = " FROM sekolah_identitas si LEFT JOIN kecamatan k ON si.id_kecamatan_fk = k.id_kecamatan LEFT JOIN sekolah_kontak sk ON si.npsn = sk.npsn_fk LEFT JOIN sekolah_lainnya sl ON si.npsn = sl.npsn_fk LEFT JOIN rekap_ptk_pd rpd ON si.npsn = rpd.npsn_fk WHERE 1=1";
if ($filterKecamatan > 0) { $baseQuery .= " AND k.id_kecamatan = $filterKecamatan"; }
if (!empty($filterJenjang)) { $baseQuery .= " AND si.jenjang_pendidikan = '" . mysqli_real_escape_string($connection, $filterJenjang) . "'"; }

$countQuery = "SELECT COUNT(DISTINCT si.npsn) as total_records" . $baseQuery;
$countResult = mysqli_query($connection, $countQuery);
$totalRecords = $countResult ? mysqli_fetch_assoc($countResult)['total_records'] : 0;
$totalPages = ceil($totalRecords / $perPage);

$dataQuery = "SELECT si.npsn, si.nama_sekolah, si.jenjang_pendidikan, si.status_sekolah, si.alamat_jalan, si.kelurahan, k.nama_kecamatan, sl.kepala_sekolah, sl.kurikulum, sl.akreditasi, sk.email, sk.nomor_telepon, COALESCE(SUM(rpd.pd_total), 0) as total_siswa, COALESCE(SUM(rpd.ptk_total), 0) as total_ptk " . $baseQuery . " GROUP BY si.npsn ORDER BY si.nama_sekolah ASC LIMIT $perPage OFFSET $offset";
$result = mysqli_query($connection, $dataQuery);

$selectedKecamatanName = "Semua Kecamatan";
if ($filterKecamatan > 0 && $resultKec) {
    mysqli_data_seek($resultKec, 0);
    while ($rowKec = mysqli_fetch_assoc($resultKec)) {
        if ($rowKec['id_kecamatan'] == $filterKecamatan) {
            $selectedKecamatanName = $rowKec['nama_kecamatan'];
            break;
        }
    }
}
$selectedJenjangName = empty($filterJenjang) ? "Semua Jenjang" : $filterJenjang;
?>
<!DOCTYPE html>
<html class="no-js" lang="id">
<head>
    <meta charset="utf-8" />
    <meta http-equiv="x-ua-compatible" content="ie=edge" />
    <title>Data Sekolah - Dapodik Kalimantan Selatan</title>
    <meta name="description" content="Database lengkap sekolah dari jenjang SD hingga SMK se-Provinsi Kalimantan Selatan" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <link rel="shortcut icon" type="image/x-icon" href="<?= BASE_URL ?>/assets/img/logo/logo.png" />
    
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/bootstrap.min.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" />
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/animate.css" />
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/lineicons.css" />
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/main.css" />
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/data-sekolah-style.css" />
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
                                    <li class="nav-item"><a class="active" href="data-sekolah.php">Data Sekolah</a></li>
                                    <li class="nav-item"><a href="data-wilayah.php">Data Wilayah</a></li>
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
                <div class="col-md-6"><h1 class="page-title wow fadeInLeft" data-wow-delay=".2s">Data Sekolah Kalsel</h1></div>
                <div class="col-md-6"><ul class="breadcrumb-nav wow fadeInRight" data-wow-delay=".4s"><li><a href="index.php">Beranda</a></li><li>Data Sekolah</li></ul></div>
            </div>
        </div>
    </section>

    <section id="data-sekolah" class="data-sekolah-section pt-120 pb-120">
        <div class="container">
            <div class="card filter-card wow fadeInUp" data-wow-delay="0.2s">
                <div class="card-header"><h5 class="card-title"><i class="lni lni-funnel"></i> Filter & Pencarian</h5></div>
                <div class="card-body">
                    <form method="GET" action="" id="filterForm">
                        <div class="row g-3 align-items-end">
                            <div class="col-lg-5 col-md-6">
                                <label for="kecamatanFilter" class="form-label">Kecamatan</label>
                                <select id="kecamatanFilter" name="id_kecamatan" class="form-select">
                                    <option value="0">Semua Kecamatan</option>
                                    <?php mysqli_data_seek($resultKec, 0); while ($rowKec = mysqli_fetch_assoc($resultKec)) { $selected = ($filterKecamatan == $rowKec['id_kecamatan']) ? 'selected' : ''; echo "<option value='{$rowKec['id_kecamatan']}' {$selected}>" . htmlspecialchars($rowKec['nama_kecamatan']) . "</option>"; } ?>
                                </select>
                            </div>
                            <div class="col-lg-5 col-md-6">
                                <label for="jenjangFilter" class="form-label">Jenjang Pendidikan</label>
                                <select id="jenjangFilter" name="jenjang" class="form-select">
                                    <option value="">Semua Jenjang</option>
                                    <option value="SD" <?= $filterJenjang == 'SD' ? 'selected' : '' ?>>SD</option>
                                    <option value="SMP" <?= $filterJenjang == 'SMP' ? 'selected' : '' ?>>SMP</option>
                                    <option value="SMA" <?= $filterJenjang == 'SMA' ? 'selected' : '' ?>>SMA</option>
                                    <option value="SMK" <?= $filterJenjang == 'SMK' ? 'selected' : '' ?>>SMK</option>
                                </select>
                            </div>
                            <div class="col-lg-2 col-md-12"><button type="submit" class="main-btn btn-hover w-100"><i class="lni lni-search-alt"></i> Terapkan</button></div>
                        </div>
                    </form>
                </div>
            </div>

            <div class="row wow fadeInUp" data-wow-delay="0.4s">
                <div class="col-md-4 mb-4"><div class="stat-card"><div class="stat-icon icon-1"><i class="lni lni-apartment"></i></div><div class="stat-content"><h3 class="stat-number"><?= number_format($totalRecords) ?></h3><p class="stat-title">Sekolah Ditemukan</p></div></div></div>
                <div class="col-md-4 mb-4"><div class="stat-card"><div class="stat-icon icon-2"><i class="lni lni-map-marker"></i></div><div class="stat-content"><h3 class="stat-text"><?= htmlspecialchars($selectedKecamatanName) ?></h3><p class="stat-title">Filter Kecamatan</p></div></div></div>
                <div class="col-md-4 mb-4"><div class="stat-card"><div class="stat-icon icon-3"><i class="lni lni-graduation"></i></div><div class="stat-content"><h3 class="stat-text"><?= htmlspecialchars($selectedJenjangName) ?></h3><p class="stat-title">Filter Jenjang</p></div></div></div>
            </div>

            <div class="card table-card wow fadeInUp" data-wow-delay="0.6s">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0"><i class="lni lni-table"></i> Hasil Data Sekolah</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <?php if ($totalRecords > 0): ?>
                        <table class="table table-hover align-middle" id="table-sekolah">
                            <thead><tr><th>No</th><th>NPSN</th><th>Nama Sekolah</th><th>Jenjang</th><th>Status</th><th class="text-center">Siswa</th><th class="text-center">PTK</th><th class="text-center">Akreditasi</th><th>Kecamatan</th><th>Detail</th></tr></thead>
                            <tbody>
                                <?php $no = $offset + 1; mysqli_data_seek($result, 0); while ($row = mysqli_fetch_assoc($result)) {
                                    $jenjangColor = ['SD' => 'success', 'SMP' => 'warning', 'SMA' => 'primary', 'SMK' => 'purple'][$row['jenjang_pendidikan']] ?? 'secondary';
                                    $akreditasiValue = trim($row['akreditasi'] ?? '-');
                                    $akreditasiColor = ['A' => 'success', 'B' => 'primary', 'C' => 'warning'][$akreditasiValue] ?? 'secondary';
                                    echo "<tr><td><strong>{$no}</strong></td><td><span class='badge-custom badge-npsn'>{$row['npsn']}</span></td><td><strong>" . htmlspecialchars($row['nama_sekolah']) . "</strong></td><td><span class='badge-custom badge-{$jenjangColor}'>{$row['jenjang_pendidikan']}</span></td><td><span class='badge-custom badge-" . ($row['status_sekolah'] == 'Negeri' ? 'info' : 'dark') . "'>{$row['status_sekolah']}</span></td><td class='text-center'>" . number_format($row['total_siswa']) . "</td><td class='text-center'>" . number_format($row['total_ptk']) . "</td><td class='text-center'><span class='badge-custom badge-{$akreditasiColor}'>{$akreditasiValue}</span></td><td>" . htmlspecialchars($row['nama_kecamatan']) . "</td><td><button type='button' class='btn btn-sm btn-outline-primary btn-hover' data-bs-toggle='modal' data-bs-target='#detailModal{$row['npsn']}'><i class='lni lni-eye'></i></button></td></tr>";
                                    $no++;
                                } ?>
                            </tbody>
                        </table>
                        <?php else: ?>
                        <div class="no-data-found"><i class="lni lni-dropbox"></i><h4>Tidak Ada Data Ditemukan</h4><p>Silakan coba ubah kriteria filter Anda.</p><a href="data-sekolah.php" class="main-btn border-btn btn-hover mt-3">Reset Filter</a></div>
                        <?php endif; ?>
                    </div>
                </div>
                <?php if ($totalPages > 1): ?>
                <div class="card-footer">
                    <nav><ul class="pagination justify-content-center">
                        <li class="page-item <?= ($page <= 1) ? 'disabled' : '' ?>"><a class="page-link" href="?id_kecamatan=<?= $filterKecamatan ?>&jenjang=<?= urlencode($filterJenjang) ?>&page=<?= $page - 1 ?>">&laquo;</a></li>
                        <?php for ($i = max(1, $page - 2); $i <= min($totalPages, $page + 2); $i++): ?>
                        <li class="page-item <?= ($i == $page) ? 'active' : '' ?>"><a class="page-link" href="?id_kecamatan=<?= $filterKecamatan ?>&jenjang=<?= urlencode($filterJenjang) ?>&page=<?= $i ?>"><?= $i ?></a></li>
                        <?php endfor; ?>
                        <li class="page-item <?= ($page >= $totalPages) ? 'disabled' : '' ?>"><a class="page-link" href="?id_kecamatan=<?= $filterKecamatan ?>&jenjang=<?= urlencode($filterJenjang) ?>&page=<?= $page + 1 ?>">&raquo;</a></li>
                    </ul></nav>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <?php mysqli_data_seek($result, 0); while ($row = mysqli_fetch_assoc($result)) { ?>
    <div class="modal fade" id="detailModal<?= $row['npsn'] ?>" tabindex="-1"><div class="modal-dialog modal-lg modal-dialog-centered"><div class="modal-content">
        <div class="modal-header"><h5 class="modal-title"><i class="lni lni-apartment"></i> Detail Sekolah</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
        <div class="modal-body">
            <h4><?= htmlspecialchars($row['nama_sekolah']) ?></h4><p class="text-muted"><?= htmlspecialchars($row['npsn']) ?></p><hr>
            <div class="row">
                <div class="col-md-6"><ul class="list-unstyled">
                    <li><strong>Kepala Sekolah:</strong><br> <?= htmlspecialchars($row['kepala_sekolah'] ?? '-') ?></li>
                    <li class="mt-3"><strong>Kurikulum:</strong><br> <?= htmlspecialchars($row['kurikulum'] ?? '-') ?></li>
                    <li class="mt-3"><strong>Alamat:</strong><br> <?= htmlspecialchars($row['alamat_jalan'] . ', ' . $row['kelurahan'] . ', ' . $row['nama_kecamatan']) ?></li>
                </ul></div>
                <div class="col-md-6"><ul class="list-unstyled">
                    <li><strong>Telepon:</strong><br> <a href="tel:<?= htmlspecialchars($row['nomor_telepon'] ?? '') ?>"><?= htmlspecialchars($row['nomor_telepon'] ?? '-') ?></a></li>
                    <li class="mt-3"><strong>Email:</strong><br> <a href="mailto:<?= htmlspecialchars($row['email'] ?? '') ?>"><?= htmlspecialchars($row['email'] ?? '-') ?></a></li>
                </ul></div>
            </div>
        </div>
    </div></div></div>
    <?php } ?>
    
    <?php require_once 'footer.php'; ?>

    <a href="#" class="scroll-top btn-hover"><i class="lni lni-chevron-up"></i></a>

    <script src="<?= BASE_URL ?>/assets/js/bootstrap.bundle.min.js"></script>
    <script src="<?= BASE_URL ?>/assets/js/wow.min.js"></script>
    <script src="<?= BASE_URL ?>/assets/js/main.js"></script>
</body>
</html>