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

  /* Mode collapse: perbesar logo */
  .sidebar-mini .sidebar-brand img {
    width: 40px !important;
    height: auto;
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
  .logo-btikp-sm {
    width: 40px;
    height: auto;
  }

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

  .floating {
    animation: float 20s infinite ease-in-out;
  }
  .rotating {
    animation: rotate 25s infinite linear;
  }
  .pulsing {
    animation: pulse 8s infinite ease-in-out;
  }
  .zigzag {
    animation: zigzag 30s infinite ease-in-out;
  }
  .morphing {
    animation: morph 15s infinite ease-in-out;
  }

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
    <!-- Animasi SVG Background (pertahankan dari kode pertama) -->
    <svg class="svg-bg" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 800 1200">
      <!-- (isi SVG sama persis dengan kode pertama Anda) -->
    </svg>

    <!-- Logo -->
    <div class="sidebar-brand">
      <a href="../dashboard/index.php" class="logo-container">
        <img src="../assets/img/logo.png" alt="BTIKP" class="logo-btikp">
        <span class="btikp-text">BTIKP</span>
      </a>
    </div>
    <div class="sidebar-brand sidebar-brand-sm">
      <a href="../dashboard/index.php">
        <img src="../assets/img/logo.png" alt="BTIKP" class="logo-btikp-sm">
      </a>
    </div>

    <ul class="sidebar-menu">
      <li class="menu-header">Dashboard</li>

      <!-- Home -->
      <li class="<?= (basename($currentPath) == 'index.php' && strpos($currentPath, '/dashboard/') !== false) ? 'active' : '' ?>">
        <a class="nav-link" href="../dashboard/index.php">
          <i class="fas fa-fire"></i><span>Home</span>
        </a>
      </li>

      <!-- Analytics -->
      <li class="<?= (basename($currentPath) == 'analytics.php' && strpos($currentPath, '/dashboard/') !== false) ? 'active' : '' ?>">
        <a class="nav-link" href="../dashboard/analytics.php">
          <i class="fas fa-chart-line"></i><span>Analytics</span>
        </a>
      </li>

      <li class="menu-header">Data Pokok</li>

      <!-- Sekolah -->
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

      <!-- Rekapitulasi -->
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

      <!-- Wilayah -->
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

      <!-- ========== LAPORAN (baru ditambahkan) ========== -->
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
    </ul>

    <!-- Sidebar Footer -->
    <div class="sidebar-footer text-center">
      <small class="text-muted">
        <i class="fas fa-database"></i> 
        <?php
        require_once '../helper/connection.php';
        $quick_count = mysqli_query($connection, "SELECT COUNT(*) FROM sekolah_identitas");
        $total = mysqli_fetch_array($quick_count)[0];
        echo number_format($total) . ' Sekolah';
        ?>
      </small>
    </div>
  </aside>
</div>