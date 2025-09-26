<?php
/* Mendeteksi halaman yang sedang dibuka */
$currentPath = $_SERVER['PHP_SELF'];

/* Fungsi bantu: apakah kita sedang berada di salah satu anak menu? */
function isChildActive($parentPath)
{
    return strpos($_SERVER['PHP_SELF'], $parentPath) !== false;
}
?>
<style>
    /* Fix sidebar agar tetap di kiri */
    .main-sidebar {
        position: fixed !important;
        top: 0;
        left: 0;
        bottom: 0;
        height: 100%;
        overflow-y: auto;
        z-index: 1000;
    }

    /* Bagian logo tidak ikut scroll */
    .sidebar-brand,
    .sidebar-brand-sm {
        position: sticky;
        top: 0;
        background: #fff;
        z-index: 1100;
        padding: 10px 0;
        display: flex;
        align-items: center;
        justify-content: center;
        height: 60px; /* Menyamakan tinggi agar rapi */
    }

    /* Bagian footer (Sekolah count) tidak ikut scroll */
    .sidebar-footer {
        position: sticky !important;
        bottom: 0;
        background: #fff;
        padding: 10px 20px;
        z-index: 1100;
    }

    /* Collapse mode: ganti teks Sekolah jadi icon */
    .sidebar-mini .sidebar-footer small {
        display: none;
    }
    .sidebar-mini .sidebar-footer i {
        display: inline-block !important;
        font-size: 16px;
    }

    /* Custom untuk logo BTIKP */
    .logo-container {
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .logo-btikp {
        width: 40px;
        height: auto;
        margin-right: 10px;
    }
    .btikp-text {
        font-size: 18px;
        font-weight: bold;
        color: #333;
    }
    .sidebar-mini .btikp-text {
        display: none;
    }
    .sidebar-mini .logo-btikp {
        margin-right: 0;
    }

    /* ============ STYLE BARU UNTUK TEKS BTIKP SAAT COLLAPSE ============ */
    .btikp-text-sm {
        font-size: 14px;
        font-weight: 700;
        color: #6c757d;
        text-transform: uppercase;
        letter-spacing: 1.5px;
    }
    /* ==================================================================== */

    /* Animasi SVG Background */
    .svg-bg {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        z-index: -1;
        opacity: 0.07;
        overflow: hidden;
    }

    .floating { animation: float 20s infinite ease-in-out; }
    .rotating { animation: rotate 25s infinite linear; }
    .pulsing { animation: pulse 8s infinite ease-in-out; }
    .zigzag { animation: zigzag 30s infinite ease-in-out; }
    .morphing { animation: morph 15s infinite ease-in-out; }

    @keyframes float {
        0%, 100% { transform: translateY(0) translateX(0) scale(1); }
        25% { transform: translateY(-120px) translateX(60px) scale(1.1); }
        50% { transform: translateY(-240px) translateX(-40px) scale(0.9); }
        75% { transform: translateY(-80px) translateX(100px) scale(1.05); }
    }
    @keyframes rotate {
        0% { transform: rotate(0deg) translateX(0) translateY(0); }
        100% { transform: rotate(360deg) translateX(50px) translateY(-50px); }
    }
    @keyframes pulse {
        0%, 100% { transform: scale(1); opacity: 0.07; }
        50% { transform: scale(1.2); opacity: 0.12; }
    }
    @keyframes zigzag {
        0%, 100% { transform: translateX(0) translateY(0); }
        25% { transform: translateX(80px) translateY(-100px); }
        50% { transform: translateX(-60px) translateY(-200px); }
        75% { transform: translateX(100px) translateY(-80px); }
    }
    @keyframes morph {
        0%, 100% { border-radius: 30% 70% 70% 30% / 30% 30% 70% 70%; }
        25% { border-radius: 58% 42% 75% 25% / 76% 46% 54% 24%; }
        50% { border-radius: 50% 50% 33% 67% / 55% 27% 73% 45%; }
        75% { border-radius: 33% 67% 58% 42% / 63% 68% 32% 37%; }
    }
</style>

<div class="main-sidebar sidebar-style-2">
    <aside id="sidebar-wrapper">
        <svg class="svg-bg" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 800 1200">
            <defs>
                <linearGradient id="grad1" x1="0%" y1="0%" x2="100%" y2="100%"><stop offset="0%" style="stop-color:#667eea;stop-opacity:1" /><stop offset="100%" style="stop-color:#764ba2;stop-opacity:1" /></linearGradient>
                <linearGradient id="grad2" x1="0%" y1="0%" x2="100%" y2="100%"><stop offset="0%" style="stop-color:#f093fb;stop-opacity:1" /><stop offset="100%" style="stop-color:#f5576c;stop-opacity:1" /></linearGradient>
                <linearGradient id="grad3" x1="0%" y1="0%" x2="100%" y2="100%"><stop offset="0%" style="stop-color:#4facfe;stop-opacity:1" /><stop offset="100%" style="stop-color:#00f2fe;stop-opacity:1" /></linearGradient>
                <linearGradient id="grad4" x1="0%" y1="0%" x2="100%" y2="100%"><stop offset="0%" style="stop-color:#43e97b;stop-opacity:1" /><stop offset="100%" style="stop-color:#38f9d7;stop-opacity:1" /></linearGradient>
                <linearGradient id="grad5" x1="0%" y1="0%" x2="100%" y2="100%"><stop offset="0%" style="stop-color:#fa709a;stop-opacity:1" /><stop offset="100%" style="stop-color:#fee140;stop-opacity:1" /></linearGradient>
                <pattern id="pattern" x="0" y="0" width="100" height="100" patternUnits="userSpaceOnUse"><circle cx="25" cy="25" r="2" fill="url(#grad3)" opacity="0.3"/><circle cx="75" cy="75" r="2" fill="url(#grad1)" opacity="0.3"/></pattern>
            </defs>
            <rect width="100%" height="100%" fill="url(#pattern)" />
            <circle class="floating" cx="150" cy="300" r="60" fill="url(#grad1)" /><circle class="rotating" cx="600" cy="500" r="80" fill="url(#grad2)" /><circle class="pulsing" cx="300" cy="800" r="70" fill="url(#grad3)" /><circle class="zigzag" cx="500" cy="200" r="50" fill="url(#grad4)" /><circle class="floating" cx="700" cy="900" r="90" fill="url(#grad5)" /><path class="morphing" d="M100,100 Q200,50 300,100 T500,100 Q600,150 700,100 T900,100 L900,300 Q800,350 700,300 T500,300 Q400,250 300,300 T100,300 Z" fill="url(#grad1)" opacity="0.5" /><line class="floating" x1="50" y1="400" x2="750" y2="450" stroke="url(#grad2)" stroke-width="3" opacity="0.7" /><line class="rotating" x1="100" y1="600" x2="700" y2="650" stroke="url(#grad3)" stroke-width="2" opacity="0.6" /><line class="zigzag" x1="200" y1="900" x2="600" y2="950" stroke="url(#grad4)" stroke-width="4" opacity="0.8" /><polygon class="pulsing" points="250,150 300,250 200,250" fill="url(#grad5)" opacity="0.6" /><polygon class="rotating" points="550,350 600,450 500,450" fill="url(#grad1)" opacity="0.5" /><polygon class="morphing" points="150,550 200,650 100,650" fill="url(#grad2)" opacity="0.7" /><circle class="floating" cx="80" cy="150" r="15" fill="url(#grad3)" /><circle class="floating" cx="720" cy="250" r="20" fill="url(#grad4)" /><circle class="floating" cx="180" cy="750" r="18" fill="url(#grad5)" /><circle class="floating" cx="620" cy="850" r="22" fill="url(#grad1)" /><circle class="floating" cx="380" cy="450" r="16" fill="url(#grad2)" /><ellipse class="morphing" cx="400" cy="600" rx="120" ry="80" fill="url(#grad3)" opacity="0.4" /><ellipse class="pulsing" cx="200" cy="400" rx="90" ry="60" fill="url(#grad4)" opacity="0.5" /><ellipse class="zigzag" cx="600" cy="750" rx="110" ry="70" fill="url(#grad5)" opacity="0.6" />
        </svg>

        <div class="sidebar-brand">
            <a href="../dashboard/index.php" class="logo-container">
                <img src="../assets/img/logo.png" alt="BTIKP" class="logo-btikp">
                <span class="btikp-text">BTIKP</span>
            </a>
        </div>

        <div class="sidebar-brand sidebar-brand-sm">
            <a href="../dashboard/index.php">
                <span class="btikp-text-sm">BTIKP</span>
            </a>
        </div>

        <ul class="sidebar-menu">
            <li class="menu-header">Dashboard</li>

            <li class="<?= (basename($currentPath) == 'index.php' && strpos($currentPath, '/dashboard/') !== false) ? 'active' : '' ?>">
                <a class="nav-link" href="../dashboard/index.php">
                    <i class="fas fa-fire"></i><span>Home</span>
                </a>
            </li>

            <li class="<?= (basename($currentPath) == 'analytics.php' && strpos($currentPath, '/dashboard/') !== false) ? 'active' : '' ?>">
                <a class="nav-link" href="../dashboard/analytics.php">
                    <i class="fas fa-chart-line"></i><span>Analytics</span>
                </a>
            </li>

            <li class="menu-header">Data Pokok</li>

            <li class="dropdown <?= isChildActive('/sekolah/') ? 'active' : '' ?>">
                <a href="#" class="nav-link has-dropdown">
                    <i class="fas fa-school"></i><span>Sekolah</span>
                </a>
                <ul class="dropdown-menu">
                    <li class="<?= (basename($currentPath) == 'index.php' && isChildActive('/sekolah/')) ? 'active' : '' ?>">
                        <a class="nav-link" href="../sekolah/index.php">Identitas Sekolah</a>
                    </li>
                    <li class="<?= (basename($currentPath) == 'kontak.php') ? 'active' : '' ?>">
                        <a class="nav-link" href="../sekolah/kontak.php">Kontak Sekolah</a>
                    </li>
                    <li class="<?= (basename($currentPath) == 'pelengkap.php') ? 'active' : '' ?>">
                        <a class="nav-link" href="../sekolah/pelengkap.php">Data Pelengkap</a>
                    </li>
                    <li class="<?= (basename($currentPath) == 'lainnya.php') ? 'active' : '' ?>">
                        <a class="nav-link" href="../sekolah/lainnya.php">Data Lainnya</a>
                    </li>
                </ul>
            </li>

            <li class="dropdown <?= isChildActive('/rekap/') ? 'active' : '' ?>">
                <a href="#" class="nav-link has-dropdown">
                    <i class="fas fa-chart-bar"></i><span>Rekapitulasi</span>
                </a>
                <ul class="dropdown-menu">
                    <li class="<?= (basename($currentPath) == 'ptk_pd.php') ? 'active' : '' ?>">
                        <a class="nav-link" href="../rekap/ptk_pd.php">PTK & Peserta Didik</a>
                    </li>
                    <li class="<?= (basename($currentPath) == 'rombel.php') ? 'active' : '' ?>">
                        <a class="nav-link" href="../rekap/rombel.php">Rombongan Belajar</a>
                    </li>
                    <li class="<?= (basename($currentPath) == 'sarpras.php') ? 'active' : '' ?>">
                        <a class="nav-link" href="../rekap/sarpras.php">Sarana Prasarana</a>
                    </li>
                </ul>
            </li>

            <li class="dropdown <?= isChildActive('/wilayah/') ? 'active' : '' ?>">
                <a href="#" class="nav-link has-dropdown">
                    <i class="fas fa-map-marked-alt"></i><span>Wilayah</span>
                </a>
                <ul class="dropdown-menu">
                    <li class="<?= (basename($currentPath) == 'negara.php') ? 'active' : '' ?>">
                        <a class="nav-link" href="../wilayah/negara.php">Negara</a>
                    </li>
                    <li class="<?= (basename($currentPath) == 'provinsi.php') ? 'active' : '' ?>">
                        <a class="nav-link" href="../wilayah/provinsi.php">Provinsi</a>
                    </li>
                    <li class="<?= (basename($currentPath) == 'kabupaten.php') ? 'active' : '' ?>">
                        <a class="nav-link" href="../wilayah/kabupaten.php">Kabupaten/Kota</a>
                    </li>
                    <li class="<?= (basename($currentPath) == 'kecamatan.php') ? 'active' : '' ?>">
                        <a class="nav-link" href="../wilayah/kecamatan.php">Kecamatan</a>
                    </li>
                </ul>
            </li>

            <li class="dropdown <?= isChildActive('/laporan/') ? 'active' : '' ?>">
                <a href="#" class="nav-link has-dropdown">
                    <i class="fas fa-file-export"></i><span>Laporan</span>
                </a>
                <ul class="dropdown-menu">
                    <li class="<?= (basename($currentPath) == 'index.php' && isChildActive('/laporan/')) ? 'active' : '' ?>">
                        <a class="nav-link" href="../laporan/index.php">Laporan & Ekspor</a>
                    </li>
                </ul>
            </li>

            <li class="menu-header">Pengaturan</li>
            <li class="dropdown <?= isChildActive('/user/') ? 'active' : '' ?>">
                <a href="#" class="nav-link has-dropdown">
                    <i class="fas fa-users-cog"></i><span>User Management</span>
                </a>
                <ul class="dropdown-menu">
                    <li class="<?= (basename($currentPath) == 'index.php' && isChildActive('/user/')) ? 'active' : '' ?>">
                        <a class="nav-link" href="../user/index.php">Daftar User</a>
                    </li>
                    <li class="<?= (basename($currentPath) == 'tambah.php') ? 'active' : '' ?>">
                        <a class="nav-link" href="../user/tambah.php">Tambah User</a>
                    </li>
                </ul>
            </li>

            <!-- Menu Keluar -->
            <li class="menu-header">exit</li>
            <li class="dropdown">
                <a href="../logout.php" class="nav-link">
                    <i class="fas fa-sign-out-alt text-danger"></i><span style="color: red;">Keluar</span>
                </a>
            </li>
        </ul>

        <div class="sidebar-footer text-center">
            <small class="text-muted">
                <i class="fas fa-database"></i> 
                <?php
                require_once '../helper/connection.php';
                $quick_count = mysqli_query($connection, "SELECT COUNT(*) FROM sekolah_identitas");
                if ($quick_count) {
                    $total = mysqli_fetch_array($quick_count)[0];
                    echo number_format($total) . ' Sekolah';
                } else {
                    echo '0 Sekolah';
                }
                ?>
            </small>
        </div>
    </aside>
</div>