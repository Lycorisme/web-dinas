<?php
require_once '../layout/_top.php';
require_once '../helper/connection.php';

// Tambahkan custom CSS
echo '<link rel="stylesheet" href="../assets/css/custom.css">';

// Ambil data statistik
$query = "SELECT 
    COALESCE((SELECT COUNT(*) FROM sekolah_identitas), 0) as total_sekolah,
    COALESCE((SELECT SUM(COALESCE(ptk_total, 0)) FROM rekap_ptk_pd), 0) as total_ptk,
    COALESCE((SELECT SUM(COALESCE(jumlah_total, 0)) FROM rekap_rombel), 0) as total_siswa,
    COALESCE((SELECT COUNT(*) FROM rekap_rombel), 0) as total_rombel";

$result = mysqli_query($connection, $query);
$stats = mysqli_fetch_assoc($result);

// Pastikan semua nilai tidak null
$stats['total_sekolah'] = intval($stats['total_sekolah'] ?? 0);
$stats['total_ptk'] = intval($stats['total_ptk'] ?? 0);
$stats['total_siswa'] = intval($stats['total_siswa'] ?? 0);
$stats['total_rombel'] = intval($stats['total_rombel'] ?? 0);

// Ambil data jenjang pendidikan
$jenjang_query = "SELECT jenjang_pendidikan, COUNT(*) as jumlah 
                  FROM sekolah_identitas 
                  WHERE jenjang_pendidikan IS NOT NULL AND jenjang_pendidikan != ''
                  GROUP BY jenjang_pendidikan 
                  ORDER BY jumlah DESC";

$jenjang_result = mysqli_query($connection, $jenjang_query);
$jenjang_data = [];

if ($jenjang_result) {
    while ($row = mysqli_fetch_assoc($jenjang_result)) {
        $jenjang_data[] = [
            'jenjang_pendidikan' => $row['jenjang_pendidikan'],
            'jumlah' => intval($row['jumlah'])
        ];
    }
}

// Ambil data aktivitas scraping terbaru (tanpa batasan LIMIT)
$scraping_query = "SELECT batch_name, status, started_at, success_count, failed_count 
                   FROM scraping_logs 
                   ORDER BY started_at DESC";
$scraping_result = mysqli_query($connection, $scraping_query);
$scraping_logs = [];

if ($scraping_result) {
    while ($row = mysqli_fetch_assoc($scraping_result)) {
        $scraping_logs[] = $row;
    }
}
?>

<!-- Chart.js Library -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.9.1/chart.min.js"></script>

<section class="section">
  <div class="section-header">
    <h1><i class=""></i> Dashboard Utama</h1>
  </div>

  <!-- Hero Section -->
  <div class="row mb-4">
    <div class="col-12">
      <div class="hero-card">
        <div class="hero-content">
          <div class="hero-text">
            <h2 class="hero-title">Selamat Datang di DAPODIK Analytics!</h2>
            <p class="hero-subtitle">
              Platform analisis data pendidikan yang komprehensif. 
              Kelola dan visualisasikan data sekolah, PTK, dan siswa dengan mudah.
            </p>
            <div class="hero-actions">
              <a href="analytics.php" class="btn btn-primary btn-lg hero-btn">
                <i class="fas fa-chart-line"></i> Buka Analytics Dashboard
              </a>
              <a href="../sekolah" class="btn btn-outline-light btn-lg hero-btn">
                <i class="fas fa-school"></i> Kelola Data Sekolah
              </a>
            </div>
          </div>
          <div class="hero-graphic">
            <div class="floating-card">
              <i class="fas fa-chart-bar"></i>
              <span>Analytics</span>
            </div>
            <div class="floating-card delay-1">
              <i class="fas fa-users"></i>
              <span>Data PTK</span>
            </div>
            <div class="floating-card delay-2">
              <i class="fas fa-user-graduate"></i>
              <span>Data Siswa</span>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Statistics Cards -->
  <div class="row mb-4">
    <div class="col-lg-3 col-md-6">
      <div class="stats-card primary">
        <div class="stats-icon">
          <i class="fas fa-school"></i>
        </div>
        <div class="stats-content">
          <h3 class="stats-number" data-count="<?= $stats['total_sekolah'] ?>">0</h3>
          <p class="stats-label">Total Sekolah</p>
          <div class="stats-badge">
            <i class="fas fa-arrow-up"></i> Aktif
          </div>
        </div>
      </div>
    </div>
    
    <div class="col-lg-3 col-md-6">
      <div class="stats-card success">
        <div class="stats-icon">
          <i class="fas fa-users"></i>
        </div>
        <div class="stats-content">
          <h3 class="stats-number" data-count="<?= $stats['total_ptk'] ?>">0</h3>
          <p class="stats-label">Total PTK</p>
          <div class="stats-badge">
            <i class="fas fa-chalkboard-teacher"></i> Guru & Tendik
          </div>
        </div>
      </div>
    </div>
    
    <div class="col-lg-3 col-md-6">
      <div class="stats-card warning">
        <div class="stats-icon">
          <i class="fas fa-user-graduate"></i>
        </div>
        <div class="stats-content">
          <h3 class="stats-number" data-count="<?= $stats['total_siswa'] ?>">0</h3>
          <p class="stats-label">Total Siswa</p>
          <div class="stats-badge">
            <i class="fas fa-graduation-cap"></i> Peserta Didik
          </div>
        </div>
      </div>
    </div>
    
    <div class="col-lg-3 col-md-6">
      <div class="stats-card info">
        <div class="stats-icon">
          <i class="fas fa-door-open"></i>
        </div>
        <div class="stats-content">
          <h3 class="stats-number" data-count="<?= $stats['total_rombel'] ?>">0</h3>
          <p class="stats-label">Total Rombel</p>
          <div class="stats-badge">
            <i class="fas fa-users-class"></i> Kelas
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Dashboard Layout dengan 2 Kolom -->
  <div class="dashboard-layout">
    <!-- Left Column -->
    <div class="dashboard-left">
      <!-- Quick Actions -->
      <div class="card modern-card">
        <div class="card-header">
          <h4><i class="fas fa-bolt"></i> Quick Actions</h4>
        </div>
        <div class="card-body">
          <div class="quick-actions-container">
            <div class="quick-action-item">
              <a href="../sekolah/create.php" class="quick-action-link">
                <div class="quick-action-icon add-school">
                  <i class="fas fa-plus"></i>
                </div>
                <div class="quick-action-content">
                  <h5>Tambah Sekolah</h5>
                  <p>Tambah data sekolah baru</p>
                </div>
              </a>
            </div>
            
            <div class="quick-action-item">
              <a href="analytics.php" class="quick-action-link">
                <div class="quick-action-icon view-analytics">
                  <i class="fas fa-chart-line"></i>
                </div>
                <div class="quick-action-content">
                  <h5>Lihat Analytics</h5>
                  <p>Analisis data pendidikan</p>
                </div>
              </a>
            </div>
            
            <div class="quick-action-item dropdown-container" data-dropdown="rekap">
              <div class="quick-action-link" id="rekapDropdown">
                <div class="quick-action-icon recap">
                  <i class="fas fa-file-alt"></i>
                </div>
                <div class="quick-action-content">
                  <h5>Rekapitulasi</h5>
                  <p>Lihat laporan rekap</p>
                  <i class="fas fa-chevron-down dropdown-icon"></i>
                </div>
              </div>
            </div>
            
            <div class="quick-action-item dropdown-container" data-dropdown="wilayah">
              <div class="quick-action-link" id="wilayahDropdown">
                <div class="quick-action-icon manage-region">
                  <i class="fas fa-map"></i>
                </div>
                <div class="quick-action-content">
                  <h5>Kelola Wilayah</h5>
                  <p>Atur data wilayah</p>
                  <i class="fas fa-chevron-down dropdown-icon"></i>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Distribusi Jenjang Pendidikan - Donut Chart dengan Legend yang Clean -->
      <div class="card modern-card">
        <div class="card-header">
          <h4><i class="fas fa-chart-pie"></i> Distribusi Jenjang Pendidikan</h4>
          <div class="card-header-action">
            <a href="analytics.php" class="btn btn-primary btn-sm">
              <i class="fas fa-external-link-alt"></i> Lihat Detail
            </a>
          </div>
        </div>
        <div class="card-body">
          <div class="jenjang-distribution">
            <div class="jenjang-header">  
            </div>
            
            <div class="jenjang-content">
              <div class="chart-container">
                <canvas id="jenjangChart"></canvas>
                <div class="chart-center-text">
                  <h3><?= count($jenjang_data) ?></h3>
                  <p>Jenjang</p>
                </div>
              </div>
              
              <div class="legend-container">
                <?php 
                $colors = [
                    'rgba(103, 119, 239, 0.8)',  // #6777ef
                    'rgba(40, 167, 69, 0.8)',     // #28a745
                    'rgba(255, 193, 7, 0.8)',     // #ffc107
                    'rgba(23, 162, 184, 0.8)',    // #17a2b8
                    'rgba(220, 53, 69, 0.8)',     // #dc3545
                    'rgba(102, 16, 242, 0.8)'     // #6610f2
                ];
                $total_sekolah = array_sum(array_column($jenjang_data, 'jumlah'));
                
                foreach ($jenjang_data as $index => $jenjang): 
                  $percentage = $total_sekolah > 0 ? round(($jenjang['jumlah'] / $total_sekolah) * 100, 1) : 0;
                  $color = $colors[$index % count($colors)];
                ?>
                  <div class="legend-item">
                    <div class="legend-color" style="background-color: <?= $color ?>"></div>
                    <div class="legend-info">
                      <div class="legend-name"><?= htmlspecialchars($jenjang['jenjang_pendidikan']) ?></div>
                      <div class="legend-stats">
                        <span class="legend-count"><?= number_format($jenjang['jumlah']) ?> sekolah</span>
                        <span class="legend-percentage"><?= $percentage ?>%</span>
                      </div>
                    </div>
                  </div>
                <?php endforeach; ?>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Right Column: Recent Activity -->
    <div class="dashboard-right">
      <div class="card modern-card aktivitas-scraping">
        <div class="card-header">
          <h4><i class="fas fa-history"></i> Aktivitas Scraping</h4>
        </div>
        <div class="card-body p-0">
          <?php if (!empty($scraping_logs)): ?>
            <div class="activity-list">
              <?php foreach ($scraping_logs as $log): ?>
                <div class="activity-item">
                  <div class="activity-icon <?= $log['status'] == 'completed' ? 'success' : ($log['status'] == 'failed' ? 'danger' : 'warning') ?>">
                    <i class="fas <?= $log['status'] == 'completed' ? 'fa-check' : ($log['status'] == 'failed' ? 'fa-times' : 'fa-clock') ?>"></i>
                  </div>
                  <div class="activity-content">
                    <h6><?= ucfirst(htmlspecialchars($log['status'])) ?></h6>
                    <p class="mb-1"><?= htmlspecialchars(substr($log['batch_name'], 0, 30)) ?>...</p>
                    <small class="text-muted">
                      <?= date('d M Y H:i', strtotime($log['started_at'])) ?>
                    </small>
                    <?php if ($log['status'] == 'completed'): ?>
                      <div class="activity-stats">
                        <span class="badge badge-success"><?= $log['success_count'] ?> Berhasil</span>
                        <?php if ($log['failed_count'] > 0): ?>
                          <span class="badge badge-danger"><?= $log['failed_count'] ?> Gagal</span>
                        <?php endif; ?>
                      </div>
                    <?php endif; ?>
                  </div>
                </div>
              <?php endforeach; ?>
            </div>
          <?php else: ?>
            <div class="empty-state p-4">
              <i class="fas fa-history fa-2x"></i>
              <h6>Belum ada aktivitas</h6>
              <p class="mb-0">Log scraping akan muncul di sini</p>
            </div>
          <?php endif; ?>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- Dropdown menus di luar container -->
<div id="rekapMenu" class="dropdown-menu-custom">
  <a href="../rekap/ptk_pd.php" class="dropdown-item-custom">
    <i class="fas fa-users"></i>
    <span>PTK & Peserta Didik</span>
  </a>
  <a href="../rekap/rombel.php" class="dropdown-item-custom">
    <i class="fas fa-door-open"></i>
    <span>Rombongan Belajar</span>
  </a>
  <a href="../rekap/sarpras.php" class="dropdown-item-custom">
    <i class="fas fa-building"></i>
    <span>Sarana Prasarana</span>
  </a>
</div>

<div id="wilayahMenu" class="dropdown-menu-custom">
  <a href="../wilayah/negara.php" class="dropdown-item-custom">
    <i class="fas fa-globe"></i>
    <span>Negara</span>
  </a>
  <a href="../wilayah/provinsi.php" class="dropdown-item-custom">
    <i class="fas fa-map-marked-alt"></i>
    <span>Provinsi</span>
  </a>
  <a href="../wilayah/kabupaten.php" class="dropdown-item-custom">
    <i class="fas fa-city"></i>
    <span>Kabupaten/Kota</span>
  </a>
  <a href="../wilayah/kecamatan.php" class="dropdown-item-custom">
    <i class="fas fa-map-pin"></i>
    <span>Kecamatan</span>
  </a>
</div>

<!-- Data untuk Chart dalam format JSON yang aman -->
<script id="chart-data" type="application/json"><?= json_encode($jenjang_data, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) ?></script>

<!-- Custom JavaScript Inline -->
<script>
// Tunggu Chart.js dan DOM siap
document.addEventListener('DOMContentLoaded', function() {
    console.log('Dashboard starting...');
    
    // Fungsi untuk format angka Indonesia
    function formatNumber(num) {
        return new Intl.NumberFormat('id-ID').format(num);
    }
    
    // Counter animation untuk statistics
    setTimeout(() => {
        const counters = document.querySelectorAll('.stats-number');
        console.log('Found counters:', counters.length);
        
        counters.forEach((counter, index) => {
            const target = parseInt(counter.getAttribute('data-count')) || 0;
            console.log(`Counter ${index}: target = ${target}`);
            
            const duration = 2000;
            const frameRate = 60;
            const totalFrames = (duration / 1000) * frameRate;
            const increment = target / totalFrames;
            
            let currentCount = 0;
            let frame = 0;
            
            function updateCounter() {
                if (frame < totalFrames) {
                    currentCount += increment;
                    const displayValue = Math.floor(currentCount);
                    counter.innerText = formatNumber(displayValue);
                    frame++;
                    requestAnimationFrame(updateCounter);
                } else {
                    counter.innerText = formatNumber(target);
                    console.log(`Counter ${index} completed: ${target}`);
                }
            }
            
            setTimeout(() => {
                updateCounter();
            }, index * 300);
        });
    }, 500);
    
    // Chart setup
    setTimeout(() => {
        if (typeof Chart === 'undefined') {
            console.error('Chart.js not loaded');
            return;
        }
        
        const chartDataElement = document.getElementById('chart-data');
        let jenjangData = null;
        
        if (chartDataElement) {
            try {
                const jsonText = chartDataElement.textContent || chartDataElement.innerText;
                jenjangData = JSON.parse(jsonText);
                console.log('Chart data loaded:', jenjangData);
            } catch (e) {
                console.error('Error parsing chart data:', e);
            }
        }
        
        if (jenjangData && jenjangData.length > 0) {
            const ctx = document.getElementById('jenjangChart');
            if (ctx) {
                console.log('Creating chart...');
                
                const chartColors = [
                    'rgba(103, 119, 239, 0.8)',  // #6777ef
                    'rgba(40, 167, 69, 0.8)',     // #28a745
                    'rgba(255, 193, 7, 0.8)',     // #ffc107
                    'rgba(23, 162, 184, 0.8)',    // #17a2b8
                    'rgba(220, 53, 69, 0.8)',     // #dc3545
                    'rgba(102, 16, 242, 0.8)'     // #6610f2
                ];
                
                try {
                    const chartCtx = ctx.getContext('2d');
                    const jenjangChart = new Chart(chartCtx, {
                        type: 'doughnut',
                        data: {
                            labels: jenjangData.map(item => item.jenjang_pendidikan),
                            datasets: [{
                                data: jenjangData.map(item => item.jumlah),
                                backgroundColor: chartColors.slice(0, jenjangData.length),
                                borderWidth: 0,
                                hoverOffset: 15,
                                hoverBorderWidth: 3,
                                hoverBorderColor: '#fff'
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            cutout: '70%',
                            plugins: {
                                legend: {
                                    display: false
                                },
                                tooltip: {
                                    callbacks: {
                                        label: function(context) {
                                            const label = context.label || '';
                                            const value = context.raw || 0;
                                            const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                            const percentage = total > 0 ? Math.round((value / total) * 100) : 0;
                                            return `${label}: ${formatNumber(value)} sekolah (${percentage}%)`;
                                        }
                                    },
                                    backgroundColor: 'rgba(0,0,0,0.8)',
                                    titleColor: '#fff',
                                    bodyColor: '#fff',
                                    borderColor: 'rgba(255,255,255,0.2)',
                                    borderWidth: 1,
                                    cornerRadius: 8,
                                    displayColors: true
                                }
                            },
                            animation: {
                                animateRotate: true,
                                duration: 2000,
                                easing: 'easeOutQuart'
                            }
                        }
                    });
                    
                    window.jenjangChart = jenjangChart;
                    console.log('Chart created successfully');
                    
                } catch (chartError) {
                    console.error('Error creating chart:', chartError);
                }
            } else {
                console.error('Chart canvas not found');
            }
        } else {
            console.log('No chart data available');
        }
    }, 1000);
    
    // Dropdown functionality
    function setupDropdowns() {
        console.log('Setting up dropdowns...');
        
        function toggleDropdown(container, menu, trigger) {
            const isActive = container.classList.contains('active');
            console.log('Toggle dropdown, isActive:', isActive);
            
            // Close all dropdowns
            document.querySelectorAll('.dropdown-container').forEach(dropdown => {
                dropdown.classList.remove('active');
            });
            
            document.querySelectorAll('.dropdown-menu-custom').forEach(dropdownMenu => {
                dropdownMenu.style.opacity = '0';
                dropdownMenu.style.visibility = 'hidden';
                dropdownMenu.style.transform = 'translateY(-10px) scale(0.98)';
            });
            
            if (!isActive) {
                container.classList.add('active');
                
                const triggerRect = trigger.getBoundingClientRect();
                const menuWidth = 320;
                
                let left = triggerRect.left;
                let top = triggerRect.bottom + 8;
                
                if (left + menuWidth > window.innerWidth) {
                    left = window.innerWidth - menuWidth - 20;
                }
                
                if (left < 20) {
                    left = 20;
                }
                
                if (top + 200 > window.innerHeight) {
                    top = triggerRect.top - 200 - 8;
                }
                
                menu.style.left = `${left}px`;
                menu.style.top = `${top}px`;
                menu.style.width = `${menuWidth}px`;
                
                setTimeout(() => {
                    menu.style.opacity = '1';
                    menu.style.visibility = 'visible';
                    menu.style.transform = 'translateY(0) scale(1)';
                    console.log('Dropdown shown');
                }, 10);
            }
        }
        
        // Setup Rekap dropdown
        const rekapTrigger = document.getElementById('rekapDropdown');
        const rekapMenu = document.getElementById('rekapMenu');
        
        if (rekapTrigger && rekapMenu) {
            console.log('Rekap dropdown elements found');
            rekapTrigger.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                console.log('Rekap dropdown clicked');
                
                const container = rekapTrigger.closest('.dropdown-container');
                toggleDropdown(container, rekapMenu, rekapTrigger);
            });
        } else {
            console.error('Rekap dropdown elements missing');
        }
        
        // Setup Wilayah dropdown
        const wilayahTrigger = document.getElementById('wilayahDropdown');
        const wilayahMenu = document.getElementById('wilayahMenu');
        
        if (wilayahTrigger && wilayahMenu) {
            console.log('Wilayah dropdown elements found');
            wilayahTrigger.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                console.log('Wilayah dropdown clicked');
                
                const container = wilayahTrigger.closest('.dropdown-container');
                toggleDropdown(container, wilayahMenu, wilayahTrigger);
            });
        } else {
            console.error('Wilayah dropdown elements missing');
        }
    }
    
    // Initialize dropdowns after delay
    setTimeout(setupDropdowns, 100);
    
    // Close dropdowns when clicking outside
    document.addEventListener('click', function(e) {
        if (!e.target.closest('.dropdown-container') && !e.target.closest('.dropdown-menu-custom')) {
            document.querySelectorAll('.dropdown-container').forEach(container => {
                container.classList.remove('active');
            });
            
            document.querySelectorAll('.dropdown-menu-custom').forEach(menu => {
                menu.style.opacity = '0';
                menu.style.visibility = 'hidden';
                menu.style.transform = 'translateY(-10px) scale(0.98)';
            });
        }
    });
    
    // Prevent dropdown close when clicking inside menu
    document.querySelectorAll('.dropdown-menu-custom').forEach(menu => {
        menu.addEventListener('click', function(e) {
            e.stopPropagation();
        });
    });
    
    // Hover effects
    document.querySelectorAll('.stats-card').forEach(card => {
        card.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-5px)';
        });
        
        card.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0)';
        });
    });
    
    console.log('Dashboard initialization completed');
});
</script>

<?php
require_once '../layout/_bottom.php';
?>