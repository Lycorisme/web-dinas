<?php
// FILE: sekolah/index.php - VERSI YANG DIPERBAIKI DENGAN DESAIN KONSISTEN
ob_start();
require_once '../layout/_top.php';
require_once '../helper/connection.php';

// Cek apakah ada URL aktif untuk scraping
 $url_count_query = mysqli_query($connection, "SELECT COUNT(*) as total FROM scraping_urls WHERE status = 'active'");
 $url_data = mysqli_fetch_array($url_count_query);
 $has_active_urls = $url_data['total'] > 0;

// Cek apakah ada proses yang sedang berjalan
 $user_id = $_SESSION['login']['id'] ?? 0;

// Cek apakah ada proses yang sedang berjalan untuk user ini saja
 $running_check = mysqli_query(
    $connection,
    "SELECT id, batch_name, started_at, status 
     FROM scraping_logs 
     WHERE status = 'running' AND user_id = $user_id
     ORDER BY started_at DESC 
     LIMIT 1"
);
 $has_running_process = mysqli_num_rows($running_check) > 0;
 $running_process_data = null;
if ($has_running_process) {
    $running_process_data = mysqli_fetch_assoc($running_check);
}

// Ambil data kecamatan_scrape untuk dropdown
 $kecamatan_query = mysqli_query($connection, "SELECT DISTINCT nama_kecamatan FROM kecamatan_scrape ORDER BY nama_kecamatan ASC");

 $result = mysqli_query($connection, "SELECT si.*, k.nama_kecamatan, kab.nama_kabupaten, p.nama_provinsi
FROM sekolah_identitas si
LEFT JOIN kecamatan k ON si.id_kecamatan_fk = k.id_kecamatan
LEFT JOIN kabupaten_kota kab ON k.id_kabupaten_fk = kab.id_kabupaten
LEFT JOIN provinsi p ON kab.id_provinsi_fk = p.id_provinsi
ORDER BY si.nama_sekolah ASC");
?>

<section class="section">
    <div class="section-header">
        <h1>Identitas Sekolah</h1>
    </div>

    <div class="row mb-3">
        <div class="col-12">
            <div class="d-flex justify-content-end">
                <a href="./tambah_url.php" class="btn btn-info mr-2">
                    <i class="fas fa-link"></i> <span class="d-none d-sm-inline">Upload URL</span>
                </a>
                <a href="./create.php" class="btn btn-primary">
                    <i class="fas fa-plus"></i> <span class="d-none d-sm-inline">Tambah Sekolah</span>
                </a>
            </div>
        </div>
    </div>

    <!-- Ada URL aktif -->
    <?php if (!empty($url_data['total'])): ?>
    <div class="row">
      <div class="col-12">
        <div class="card shadow-sm border-0">
          <div class="card-body p-3 p-md-4">
            <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center">
              <div class="mb-3 mb-lg-0">
                <h6 class="text-primary font-weight-bold mb-1">Update Data Otomatis</h6>
                <small class="text-muted">
                  Tersedia <span class="badge badge-success"><?= $url_data['total'] ?></span> URL aktif.
                  <?php if ($has_running_process): ?>
                    <span class="text-warning font-weight-bold d-block mt-1">
                      <i class="fas fa-exclamation-triangle fa-fw"></i> Proses berjalan.
                    </span>
                  <?php endif; ?>
                </small>
              </div>

              <div class="btn-group flex-wrap" role="group">
                <?php if ($has_running_process): ?>
                  <button class="btn btn-sm btn-info" onclick="checkRunningProcess()">
                    <i class="fas fa-eye fa-fw"></i> Monitor
                  </button>
                <?php endif; ?>

                <button id="btnUpdateData"
                        class="btn btn-sm btn-warning <?= $has_running_process ? 'disabled' : '' ?>"
                        data-toggle="modal"
                        data-target="#scrapingModal"
                        <?= $has_running_process ? 'disabled' : '' ?>>
                  <i class="fas fa-sync-alt fa-fw"></i>
                  <?= $has_running_process ? 'Processing...' : 'Update Data' ?>
                </button>

                <button id="btnCancel"
                        class="btn btn-sm btn-danger"
                        style="display:none;"
                        onclick="stopScraping()">
                  <i class="fas fa-stop fa-fw"></i> Batal
                </button>
              </div>
            </div>

            <!-- Progress bar -->
            <div id="progressContainer" class="mt-3" style="display:none;">
              <div class="progress progress-sm">
                <div id="progressBar"
                     class="progress-bar progress-bar-striped progress-bar-animated bg-success"
                     role="progressbar"
                     style="width:0%">
                  <span id="progressText" class="font-weight-bold small">0%</span>
                </div>
              </div>
              <div id="statusText" class="small text-center text-muted mt-1">Menunggu...</div>
            </div>

          </div>
        </div>
      </div>
    </div>

    <?php else: ?>
    <!-- Tidak ada URL -->
    <div class="row">
      <div class="col-12">
        <div class="alert alert-info border-0 shadow-sm">
          <div class="d-flex align-items-center">
            <i class="fas fa-info-circle fa-2x text-info mr-3"></i>
            <div>
              <h6 class="font-weight-bold mb-1">Tidak Ada URL Aktif</h6>
              <p class="mb-1">Silakan <a href="./tambah_url.php" class="alert-link font-weight-bold">upload URL</a> terlebih dahulu.</p>
            </div>
          </div>
        </div>
      </div>
    </div>
    <?php endif; ?>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <!-- DataTable Controls Fixed Area -->
                    <div class="datatable-controls mb-3">
                        <div class="row">
                            <div class="col-md-6 col-sm-12 mb-2">
                                <div id="table-1_length" class="dataTables_length"></div>
                            </div>
                            <div class="col-md-6 col-sm-12 mb-2">
                                <div id="table-1_filter" class="dataTables_filter"></div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12">
                                <div id="table-1_buttons" class="dt-buttons mb-3"></div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Scrollable Table Container -->
                    <div class="table-responsive-wrapper">
                        <div class="table-container">
                            <table class="table table-hover table-striped w-100" id="table-1">
                                <thead>
                                    <tr>
                                        <th style="min-width: 80px;">No</th>
                                        <th style="min-width: 100px;">NPSN</th>
                                        <th style="min-width: 200px;">Nama Sekolah</th>
                                        <th style="min-width: 100px;">Jenjang</th>
                                        <th style="min-width: 80px;">Status</th>
                                        <th style="min-width: 180px;">Alamat</th>
                                        <th style="min-width: 120px;">Kecamatan</th>
                                        <th style="min-width: 140px;">Kabupaten/Kota</th>
                                        <th style="min-width: 120px;">Provinsi</th>
                                        <th class="text-center not-export" style="min-width: 120px;">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $no = 1;
                                    while ($data = mysqli_fetch_array($result)) :
                                    ?>
                                        <tr>
                                            <td class="text-center"><?= $no++ ?></td>
                                            <td title="NPSN: <?= $data['npsn'] ?>">
                                                <span class="badge badge-primary"><?= htmlspecialchars($data['npsn']) ?></span>
                                            </td>
                                            <td title="<?= htmlspecialchars($data['nama_sekolah']) ?>" class="school-name">
                                                <strong><?= htmlspecialchars($data['nama_sekolah']) ?></strong>
                                            </td>
                                            <td title="Jenjang: <?= htmlspecialchars($data['jenjang_pendidikan']) ?>">
                                                <span class="badge badge-info">
                                                    <i class="fas fa-graduation-cap mr-1"></i>
                                                    <?= htmlspecialchars($data['jenjang_pendidikan']) ?>
                                                </span>
                                            </td>
                                            <td title="Status: <?= htmlspecialchars($data['status_sekolah']) ?>">
                                                <span class="badge <?= $data['status_sekolah'] == 'Negeri' ? 'badge-success' : 'badge-warning' ?> badge-pill px-3 py-2">
                                                    <i class="fas fa-school mr-1"></i>
                                                    <?= htmlspecialchars($data['status_sekolah']) ?>
                                                </span>
                                            </td>
                                            <td title="Alamat: <?= htmlspecialchars($data['alamat_jalan']) ?>">
                                                <?php if ($data['alamat_jalan']) : ?>
                                                    <div class="d-flex align-items-center">
                                                        <i class="fas fa-map-marker-alt text-danger mr-2"></i>
                                                        <span><?= htmlspecialchars($data['alamat_jalan']) ?></span>
                                                    </div>
                                                <?php else : ?>
                                                    <span class="text-muted font-italic">Belum ada data</span>
                                                <?php endif; ?>
                                            </td>
                                            <td title="Kecamatan: <?= htmlspecialchars($data['nama_kecamatan']) ?>">
                                                <?php if ($data['nama_kecamatan']) : ?>
                                                    <div class="d-flex align-items-center">
                                                        <i class="fas fa-location-arrow text-info mr-2"></i>
                                                        <span><?= htmlspecialchars($data['nama_kecamatan']) ?></span>
                                                    </div>
                                                <?php else : ?>
                                                    <span class="text-muted font-italic">Belum ada data</span>
                                                <?php endif; ?>
                                            </td>
                                            <td title="Kabupaten/Kota: <?= htmlspecialchars($data['nama_kabupaten']) ?>">
                                                <?php if ($data['nama_kabupaten']) : ?>
                                                    <div class="d-flex align-items-center">
                                                        <i class="fas fa-city text-primary mr-2"></i>
                                                        <span><?= htmlspecialchars($data['nama_kabupaten']) ?></span>
                                                    </div>
                                                <?php else : ?>
                                                    <span class="text-muted font-italic">Belum ada data</span>
                                                <?php endif; ?>
                                            </td>
                                            <td title="Provinsi: <?= htmlspecialchars($data['nama_provinsi']) ?>">
                                                <?php if ($data['nama_provinsi']) : ?>
                                                    <div class="d-flex align-items-center">
                                                        <i class="fas fa-map text-warning mr-2"></i>
                                                        <span><?= htmlspecialchars($data['nama_provinsi']) ?></span>
                                                    </div>
                                                <?php else : ?>
                                                    <span class="text-muted font-italic">Belum ada data</span>
                                                <?php endif; ?>
                                            </td>
                                            <td class="text-center">
                                                <div class="btn-group btn-group-sm" role="group">
                                                    <a href="edit.php?npsn=<?= $data['npsn'] ?>" class="btn btn-info" 
                                                       title="Edit Data" data-toggle="tooltip" data-placement="top">
                                                        <i class="fas fa-edit fa-fw"></i>
                                                    </a>
                                                    <a href="javascript:void(0);" class="btn btn-danger" 
                                                       title="Hapus Data" data-toggle="tooltip" data-placement="top"
                                                       onclick="confirmDelete('<?= $data['npsn'] ?>', '<?= htmlspecialchars($data['nama_sekolah']) ?>', 'sekolah')">
                                                        <i class="fas fa-trash fa-fw"></i>
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php
                                    endwhile;
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    
                    <!-- DataTable Info and Pagination Fixed Area -->
                    <div class="datatable-footer mt-3">
                        <div class="row align-items-center">
                            <div class="col-md-6 col-sm-12">
                                <div id="table-1_info" class="dataTables_info"></div>
                            </div>
                            <div class="col-md-6 col-sm-12">
                                <div id="table-1_paginate" class="dataTables_paginate"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Modal untuk memilih URL -->
<div class="modal fade" id="scrapingModal" tabindex="-1" role="dialog" aria-labelledby="scrapingModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
    <div class="modal-content">
      <!-- Header Modal -->
      <div class="modal-header bg-primary text-white">
        <h5 class="modal-title" id="scrapingModalLabel">
          <i class="fas fa-tasks mr-2"></i> Pilih URL untuk di-Update
        </h5>
        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      
      <!-- Body Modal -->
      <div class="modal-body p-3">
        <!-- Filter dan Pencarian -->
        <div class="row mb-3">
          <div class="col-md-6 mb-2 mb-md-0">
            <label for="kecamatanFilter" class="small font-weight-bold">Filter Kecamatan</label>
            <select class="form-control form-control-sm" id="kecamatanFilter">
              <option value="0">Semua Kecamatan</option>
              <?php
                // Reset pointer kecamatan_query
                mysqli_data_seek($kecamatan_query, 0);
                while ($kec = mysqli_fetch_array($kecamatan_query)) {
                    echo '<option value="'.$kec['nama_kecamatan'].'">'.$kec['nama_kecamatan'].'</option>';
                }
              ?>
            </select>
          </div>
          <div class="col-md-6">
            <label for="searchUrlInput" class="small font-weight-bold">Cari Sekolah</label>
            <div class="input-group input-group-sm">
              <div class="input-group-prepend">
                <span class="input-group-text bg-light border">
                  <i class="fas fa-search text-muted"></i>
                </span>
              </div>
              <input type="text" id="searchUrlInput" class="form-control" placeholder="Nama sekolah atau NPSN...">
            </div>
          </div>
        </div>
        
        <!-- Daftar URL -->
        <div class="card border-0 shadow-sm">
          <div class="card-header bg-light py-2">
            <div class="custom-control custom-checkbox custom-control-inline">
              <input type="checkbox" class="custom-control-input" id="checkAllUrls">
              <label class="custom-control-label small font-weight-bold" for="checkAllUrls">
                Pilih Semua
              </label>
            </div>
          </div>
          <div class="card-body p-0">
            <div id="urlListContainer" class="p-2" style="max-height: 300px; overflow-y: auto;">
              <div class="text-center py-4">
                <div class="spinner-border text-primary mb-2" role="status">
                  <span class="sr-only">Loading...</span>
                </div>
                <p class="small text-muted mb-0">Memuat data sekolah...</p>
              </div>
            </div>
            <div id="noResultsMessage" class="alert alert-warning m-2 text-center py-2" style="display: none;">
              <i class="fas fa-exclamation-triangle mr-1"></i>
              <span class="small">Tidak ditemukan sekolah yang sesuai.</span>
            </div>
          </div>
        </div>
      </div>
      
      <!-- Footer Modal -->
      <div class="modal-footer bg-light py-2">
        <div class="d-flex flex-column flex-md-row w-100 align-items-center justify-content-between">
          <!-- Info jumlah sekolah -->
          <div class="mb-2 mb-md-0">
            <span class="badge badge-info p-2 small url-count-info">0 sekolah terdaftar</span>
          </div>
          
          <!-- Tombol aksi -->
          <div class="d-flex flex-column flex-md-row w-100 w-md-auto">
            <button type="button" class="btn btn-sm btn-outline-secondary mb-1 mb-md-0 mr-md-1 w-100" data-dismiss="modal">
              <i class="fas fa-times mr-1"></i> Tutup
            </button>
            <button type="button" class="btn btn-sm btn-primary mb-1 mb-md-0 mr-md-1 w-100" id="btnStartScrapingSelected" onclick="startScraping('selected')" disabled>
              <i class="fas fa-sync-alt mr-1"></i> Update Terpilih
            </button>
            <button type="button" class="btn btn-sm btn-warning w-100" id="btnStartScrapingAll" onclick="startScraping('all')">
              <i class="fas fa-globe mr-1"></i> Update Semua
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Modal untuk monitoring proses scraping -->
<div class="modal fade" id="monitoringModal" tabindex="-1" role="dialog" aria-labelledby="monitoringModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header bg-info text-white">
        <h5 class="modal-title" id="monitoringModalLabel">
          <i class="fas fa-chart-line mr-2"></i> Monitoring Proses Scraping
        </h5>
        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body p-4">
        <div class="row mb-4">
          <div class="col-md-6">
            <div class="card bg-light">
              <div class="card-body">
                <h6 class="card-title">Informasi Proses</h6>
                <div class="info-item">
                  <span class="font-weight-bold">Batch Name:</span> 
                  <span id="monitorBatchName">-</span>
                </div>
                <div class="info-item">
                  <span class="font-weight-bold">Status:</span> 
                  <span id="monitorStatus" class="badge badge-info">Running</span>
                </div>
                <div class="info-item">
                  <span class="font-weight-bold">Waktu Mulai:</span> 
                  <span id="monitorStartTime">-</span>
                </div>
                <div class="info-item">
                  <span class="font-weight-bold">Durasi:</span> 
                  <span id="monitorDuration">-</span>
                </div>
              </div>
            </div>
          </div>
          <div class="col-md-6">
            <div class="card bg-light">
              <div class="card-body">
                <h6 class="card-title">Progress</h6>
                <div class="progress mb-2" style="height: 25px;">
                  <div id="monitorProgressBar" class="progress-bar progress-bar-striped progress-bar-animated bg-info" role="progressbar" style="width: 0%" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100">
                    <span id="monitorProgressText" class="font-weight-bold">0%</span>
                  </div>
                </div>
                <div class="d-flex justify-content-between mt-2">
                  <div>
                    <small class="text-muted">Processed:</small>
                    <span id="monitorProcessed" class="font-weight-bold">0</span>
                  </div>
                  <div>
                    <small class="text-muted">Total:</small>
                    <span id="monitorTotal" class="font-weight-bold">0</span>
                  </div>
                </div>
                <div class="d-flex justify-content-between mt-1">
                  <div>
                    <small class="text-muted">Success:</small>
                    <span id="monitorSuccess" class="text-success font-weight-bold">0</span>
                  </div>
                  <div>
                    <small class="text-muted">Failed:</small>
                    <span id="monitorFailed" class="text-danger font-weight-bold">0</span>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
        
        <div class="row">
          <div class="col-12">
            <div class="card">
              <div class="card-header bg-white">
                <h6 class="mb-0">Log Status</h6>
              </div>
              <div class="card-body p-0">
                <div id="monitorLogContainer" style="max-height: 300px; overflow-y: auto; font-family: monospace; font-size: 0.85rem; background-color: #f8f9fa; padding: 10px;">
                  <div class="text-center text-muted py-3">Menunggu data log...</div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="modal-footer bg-light">
        <div class="d-flex justify-content-between w-100">
          <button type="button" class="btn btn-danger" onclick="stopScraping()">
            <i class="fas fa-stop mr-1"></i> Hentikan Proses
          </button>
          <div>
            <button type="button" class="btn btn-secondary mr-2" data-dismiss="modal">
              <i class="fas fa-times mr-1"></i> Tutup
            </button>
            <button type="button" class="btn btn-info" onclick="refreshMonitoring()">
              <i class="fas fa-sync-alt mr-1"></i> Refresh
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<?php
require_once '../layout/_bottom.php';
?>
<?php
// Notifikasi iziToast
if (isset($_SESSION['info'])) :
  if ($_SESSION['info']['status'] == 'success') {
?>
    <script>
      iziToast.success({
        title: 'Sukses',
        message: `<?= $_SESSION['info']['message'] ?>`,
        position: 'topCenter',
        timeout: 5000,
        icon: 'fas fa-check-circle',
        backgroundColor: '#1cc88a',
        progressBarColor: '#0f6848'
      });
    </script>
  <?php
  } else {
  ?>
    <script>
      iziToast.error({
        title: 'Gagal',
        message: `<?= $_SESSION['info']['message'] ?>`,
        timeout: 5000,
        position: 'topCenter',
        icon: 'fas fa-exclamation-circle',
        backgroundColor: '#e74a3b',
        progressBarColor: '#a02622'
      });
    </script>
<?php
  }
  unset($_SESSION['info']);
endif;
?>

<script>
// Fungsi untuk memindahkan elemen DataTable ke container yang tepat
function moveDataTableElements() {
    // Pastikan container datatable-controls ada
    const controlsContainer = $('.datatable-controls');
    if (!controlsContainer.length) {
        console.error('Container .datatable-controls tidak ditemukan!');
        return;
    }
    
    // Kosongkan container terlebih dahulu
    controlsContainer.find('.row:first-child .col-md-6:first-child').empty();
    controlsContainer.find('.row:first-child .col-md-6:last-child').empty();
    controlsContainer.find('.row:last-child .col-12').empty();
    
    // Tampilkan dan pindahkan length control
    const lengthElement = $('#table-1_length');
    if (lengthElement.length) {
        lengthElement.show().detach().appendTo('.datatable-controls .row:first-child .col-md-6:first-child');
    }
    
    // Tampilkan dan pindahkan filter control  
    const filterElement = $('#table-1_filter');
    if (filterElement.length) {
        filterElement.show().detach().appendTo('.datatable-controls .row:first-child .col-md-6:last-child');
    }
    
    // Tampilkan dan pindahkan buttons
    const buttonsElement = $('.dt-buttons');
    if (buttonsElement.length) {
        buttonsElement.detach().appendTo('.datatable-controls .row:last-child .col-12');
    }
    
    // Tampilkan dan pindahkan info
    const infoElement = $('#table-1_info');
    if (infoElement.length) {
        infoElement.show().detach().appendTo('.datatable-footer .col-md-6:first-child');
    }
    
    // Tampilkan dan pindahkan pagination
    const paginateElement = $('#table-1_paginate');
    if (paginateElement.length) {
        paginateElement.show().detach().appendTo('.datatable-footer .col-md-6:last-child');
    }
}

// Initialize tooltips and additional features
 $(document).ready(function() {
    // Initialize tooltips
    $('[data-toggle="tooltip"]').tooltip();

    // Add hover effects for better UX
    $('tbody tr').hover(
        function() {
            $(this).addClass('table-active');
            $(this).find('.badge').addClass('shadow-sm');
        },
        function() {
            $(this).removeClass('table-active');
            $(this).find('.badge').removeClass('shadow-sm');
        }
    );

    // Enhanced search functionality
    $('#table-1_filter input').attr('placeholder', 'Cari berdasarkan nama sekolah, jenjang, atau status...');

    // Add custom search for badges
    $.fn.dataTable.ext.search.push(function(settings, data, dataIndex) {
        let searchTerm = $('#table-1_filter input').val().toLowerCase();
        if (!searchTerm) return true;

        // Search in all visible columns
        for (let i = 0; i < data.length - 1; i++) { // -1 to exclude action column
            if (data[i].toLowerCase().indexOf(searchTerm) !== -1) {
                return true;
            }
        }
        return false;
    });

    // Add keyboard shortcuts
    $(document).keydown(function(e) {
        // Ctrl + N for new data
        if (e.ctrlKey && e.keyCode === 78) {
            e.preventDefault();
            window.location.href = './create.php';
        }
        // Ctrl + F for focus search
        if (e.ctrlKey && e.keyCode === 70) {
            e.preventDefault();
            $('#table-1_filter input').focus();
        }
    });

    // Add row click to view details (optional)
    $('tbody tr').click(function(e) {
        if (!$(e.target).closest('.btn').length) {
            $(this).toggleClass('table-info');
        }
    });
});

// Function to export data (optional enhancement)
function exportData() {
    // This could be enhanced to export table data
    console.log('Export functionality can be added here');
}

// Fungsi untuk memuat data sekolah berdasarkan kecamatan dan pencarian
function loadSekolah(kecamatanName = '', searchKeyword = '') {
    // Tampilkan loading
    $('#urlListContainer').html(`
        <div class="d-flex align-items-center justify-content-center py-5">
            <div class="text-center">
                <div class="spinner-border text-primary mb-3" role="status" style="width: 3rem; height: 3rem;">
                    <span class="sr-only">Loading...</span>
                </div>
                <h6 class="text-muted">Memuat data sekolah...</h6>
            </div>
        </div>
    `);

    // Sembunyikan pesan tidak ada hasil
    $('#noResultsMessage').hide();

    // Kirim request ke server
    $.ajax({
        url: 'get_scraping_urls.php',
        type: 'GET',
        data: {
            kecamatan_name: kecamatanName,
            search: searchKeyword
        },
        dataType: 'json',
        success: function(response) {
            // Debug: tampilkan response di console
            console.log('Response:', response);
            
            if (response.success) {
                if (response.data.length > 0) {
                    let html = '<div class="list-group list-group-flush">';
                    response.data.forEach(function(url) {
                        html += `
                            <div class="list-group-item">
                                <div class="custom-control custom-checkbox float-left">
                                    <input type="checkbox" class="custom-control-input url-checkbox" id="url-${url.id}" value="${url.id}">
                                    <label class="custom-control-label" for="url-${url.id}"></label>
                                </div>
                                <div class="ml-5">
                                    <div class="d-flex w-100 justify-content-between">
                                        <h6 class="mb-1">${url.nama_sekolah}</h6>
                                        <span class="badge badge-primary">${url.npsn}</span>
                                    </div>
                                    <small class="text-muted d-block">${url.nama_kecamatan}</small>
                                </div>
                            </div>
                        `;
                    });
                    html += '</div>';
                    $('#urlListContainer').html(html);
                    // Panggil updateUrlCount tanpa parameter
                    updateUrlCount();
                } else {
                    $('#urlListContainer').html('');
                    $('#noResultsMessage').show();
                    // Panggil updateUrlCount tanpa parameter
                    updateUrlCount();
                }
            } else {
                $('#urlListContainer').html(`<div class="alert alert-danger m-3">${response.message}</div>`);
                // Panggil updateUrlCount tanpa parameter
                updateUrlCount();
            }
        },
        error: function(xhr, status, error) {
            console.error('Error:', error);
            console.log('Status:', status);
            console.log('Response Text:', xhr.responseText);
            $('#urlListContainer').html('<div class="alert alert-danger m-3">Terjadi kesalahan saat memuat data.</div>');
            // Panggil updateUrlCount tanpa parameter
            updateUrlCount();
        }
    });
}

// Fungsi untuk memperbarui informasi jumlah sekolah (PERBAIKAN)
function updateUrlCount() {
    // Hitung jumlah checkbox yang dicentang (selected)
    const selectedCount = $('.url-checkbox:checked').length;
    // Hitung jumlah semua checkbox yang tampil (total terdaftar/terfilter)
    const totalCount = $('.url-checkbox').length;

    // Tampilkan informasi: "X dipilih dari Y sekolah terdaftar"
    $('.modal-footer .url-count-info').html(
        `<i class="fas fa-link mr-1"></i> <strong>${selectedCount}</strong> dipilih dari <strong>${totalCount}</strong> sekolah terdaftar`
    );

    // Memperbarui status tombol "Update Terpilih"
    updateStartButton();
}

// Fungsi untuk memperbarui tombol Update Terpilih
function updateStartButton() {
    const checkedCount = $('.url-checkbox:checked').length;
    $('#btnStartScrapingSelected').prop('disabled', checkedCount === 0);
}

// Fungsi untuk memulai proses scraping
function startScraping(mode) {
    // Tampilkan progress bar
    $('#progressContainer').show();
    $('#btnUpdateData').prop('disabled', true);
    
    // Ambil URL yang dipilih
    let selectedUrls = [];
    if (mode === 'selected') {
        $('.url-checkbox:checked').each(function() {
            selectedUrls.push($(this).val());
        });
    }
    
    // Kirim request ke server
    $.ajax({
        url: 'run_scraper.php',
        type: 'POST',
        contentType: 'application/json',
        data: JSON.stringify({
            mode: mode,
            urls: selectedUrls
        }),
        success: function(response) {
            console.log('Response:', response);
            
            if (response.success) {
                // Tampilkan tombol batal
                $('#btnCancel').show();
                
                // Mulai monitoring progress
                monitorProgress(response.log_id);
                
                // Tampilkan notifikasi
                iziToast.success({
                    title: 'Proses Dimulai',
                    message: 'Proses scraping telah dimulai di latar belakang.',
                    position: 'topCenter',
                    timeout: 5000
                });
                
                // Tutup modal
                $('#scrapingModal').modal('hide');
            } else {
                // Sembunyikan progress bar
                $('#progressContainer').hide();
                $('#btnUpdateData').prop('disabled', false);
                
                // Tampilkan notifikasi error
                iziToast.error({
                    title: 'Gagal',
                    message: response.message,
                    position: 'topCenter',
                    timeout: 5000
                });
            }
        },
        error: function(xhr, status, error) {
            console.error('Error:', error);
            console.log('Status:', status);
            console.log('Response Text:', xhr.responseText);
            
            // Sembunyikan progress bar
            $('#progressContainer').hide();
            $('#btnUpdateData').prop('disabled', false);
            
            // Tampilkan notifikasi error
            iziToast.error({
                title: 'Gagal',
                message: 'Terjadi kesalahan saat memulai proses scraping.',
                position: 'topCenter',
                timeout: 5000
            });
        }
    });
}

// Fungsi untuk memantau progress scraping
function monitorProgress(logId) {
    // Cek status setiap 2 detik
    const interval = setInterval(function() {
        $.ajax({
            url: 'get_scraping_status.php',
            type: 'GET',
            data: { log_id: logId },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    const data = response.data;
                    
                    // Update progress bar
                    const progress = data.total_urls > 0 ? Math.round((data.processed_urls / data.total_urls) * 100) : 0;
                    $('#progressBar').css('width', progress + '%');
                    $('#progressBar').attr('aria-valuenow', progress);
                    $('#progressText').text(progress + '%');
                    
                    // Update status text
                    $('#statusText').text(`Memproses ${data.processed_urls} dari ${data.total_urls} URL (${data.success_count} berhasil, ${data.failed_count} gagal)`);
                    
                    // Cek jika proses sudah selesai
                    if (data.status === 'completed' || data.status === 'failed' || data.status === 'cancelled') {
                        clearInterval(interval);
                        
                        // Sembunyikan progress bar setelah 3 detik
                        setTimeout(function() {
                            $('#progressContainer').hide();
                            $('#btnUpdateData').prop('disabled', false);
                            $('#btnCancel').hide();
                            
                            // Tampilkan notifikasi
                            if (data.status === 'completed') {
                                iziToast.success({
                                    title: 'Proses Selesai',
                                    message: 'Proses scraping telah selesai.',
                                    position: 'topCenter',
                                    timeout: 5000
                                });
                            } else if (data.status === 'failed') {
                                iziToast.error({
                                    title: 'Proses Gagal',
                                    message: 'Proses scraping gagal: ' + (data.error_message || 'Unknown error'),
                                    position: 'topCenter',
                                    timeout: 5000
                                });
                            } else if (data.status === 'cancelled') {
                                iziToast.warning({
                                    title: 'Proses Dibatalkan',
                                    message: 'Proses scraping telah dibatalkan.',
                                    position: 'topCenter',
                                    timeout: 5000
                                });
                            }
                            
                            // Refresh halaman setelah 1 detik
                            setTimeout(function() {
                                location.reload();
                            }, 1000);
                        }, 3000);
                    }
                } else {
                    console.error('Error monitoring progress:', response.message);
                }
            },
            error: function(xhr, status, error) {
                console.error('Error monitoring progress:', error);
            }
        });
    }, 2000);
}

// Fungsi untuk menghentikan proses scraping
function stopScraping() {
    if (confirm('Apakah Anda yakin ingin menghentikan proses scraping?')) {
        $.ajax({
            url: 'stop_scraping.php',
            type: 'POST',
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    iziToast.warning({
                        title: 'Proses Dihentikan',
                        message: 'Proses scraping sedang dihentikan.',
                        position: 'topCenter',
                        timeout: 5000
                    });
                    
                    // Refresh halaman setelah 2 detik
                    setTimeout(function() {
                        location.reload();
                    }, 2000);
                } else {
                    iziToast.error({
                        title: 'Gagal',
                        message: response.message,
                        position: 'topCenter',
                        timeout: 5000
                    });
                }
            },
            error: function(xhr, status, error) {
                console.error('Error stopping scraping:', error);
                iziToast.error({
                    title: 'Gagal',
                    message: 'Terjadi kesalahan saat menghentikan proses.',
                    position: 'topCenter',
                    timeout: 5000
                });
            }
        });
    }
}

// Fungsi untuk mengecek proses yang sedang berjalan
function checkRunningProcess() {
    // Buka modal monitoring
    $('#monitoringModal').modal('show');
    
    // Mulai monitoring
    startMonitoring();
}

// Fungsi untuk memulai monitoring
function startMonitoring() {
    // Update informasi proses
    updateMonitoringInfo();
    
    // Update progress
    updateMonitoringProgress();
    
    // Update log
    updateMonitoringLog();
}

// Fungsi untuk memperbarui informasi monitoring
function updateMonitoringInfo() {
    $.ajax({
        url: 'get_scraping_status.php',
        type: 'GET',
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                const data = response.data;
                
                $('#monitorBatchName').text(data.batch_name);
                
                // Update status badge
                const statusBadge = $('#monitorStatus');
                statusBadge.removeClass('badge-info badge-success badge-danger badge-warning');
                
                if (data.status === 'running') {
                    statusBadge.addClass('badge-info');
                    statusBadge.text('Running');
                } else if (data.status === 'completed') {
                    statusBadge.addClass('badge-success');
                    statusBadge.text('Completed');
                } else if (data.status === 'failed') {
                    statusBadge.addClass('badge-danger');
                    statusBadge.text('Failed');
                } else if (data.status === 'cancelled') {
                    statusBadge.addClass('badge-warning');
                    statusBadge.text('Cancelled');
                }
                
                $('#monitorStartTime').text(data.started_at);
                
                // Hitung durasi
                const startTime = new Date(data.started_at);
                const now = new Date();
                const diff = now - startTime;
                
                const hours = Math.floor(diff / (1000 * 60 * 60));
                const minutes = Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60));
                const seconds = Math.floor((diff % (1000 * 60)) / 1000);
                
                $('#monitorDuration').text(`${hours.toString().padStart(2, '0')}:${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`);
            }
        },
        error: function(xhr, status, error) {
            console.error('Error updating monitoring info:', error);
        }
    });
}

// Fungsi untuk memperbarui progress monitoring
function updateMonitoringProgress() {
    $.ajax({
        url: 'get_scraping_status.php',
        type: 'GET',
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                const data = response.data;
                
                // Update progress bar
                const progress = data.total_urls > 0 ? Math.round((data.processed_urls / data.total_urls) * 100) : 0;
                $('#monitorProgressBar').css('width', progress + '%');
                $('#monitorProgressBar').attr('aria-valuenow', progress);
                $('#monitorProgressText').text(progress + '%');
                
                // Update counters
                $('#monitorProcessed').text(data.processed_urls);
                $('#monitorTotal').text(data.total_urls);
                $('#monitorSuccess').text(data.success_count);
                $('#monitorFailed').text(data.failed_count);
            }
        },
        error: function(xhr, status, error) {
            console.error('Error updating monitoring progress:', error);
        }
    });
}

// Fungsi untuk memperbarui log monitoring
function updateMonitoringLog() {
    $.ajax({
        url: 'get_scraping_log.php',
        type: 'GET',
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                const logs = response.data;
                let logHtml = '';
                
                if (logs.length > 0) {
                    logs.forEach(function(log) {
                        const timestamp = new Date(log.timestamp);
                        const timeStr = timestamp.toLocaleTimeString();
                        
                        logHtml += `<div class="log-entry">`;
                        logHtml += `<span class="text-muted">[${timeStr}]</span> `;
                        logHtml += `<span class="${log.level}">${log.message}</span>`;
                        logHtml += `</div>`;
                    });
                } else {
                    logHtml = '<div class="text-center text-muted py-3">Tidak ada log tersedia.</div>';
                }
                
                $('#monitorLogContainer').html(logHtml);
                
                // Auto scroll to bottom
                const logContainer = $('#monitorLogContainer');
                logContainer.scrollTop(logContainer[0].scrollHeight);
            }
        },
        error: function(xhr, status, error) {
            console.error('Error updating monitoring log:', error);
        }
    });
}

// Fungsi untuk refresh monitoring
function refreshMonitoring() {
    startMonitoring();
}

// Event handler untuk modal dan filter
 $(document).ready(function() {
    // Saat modal dibuka
    $('#scrapingModal').on('shown.bs.modal', function() {
        const kecamatanName = $('#kecamatanFilter').val();
        const searchKeyword = $('#searchUrlInput').val();
        console.log('Modal opened. Kecamatan:', kecamatanName, 'Search:', searchKeyword); // Debug
        loadSekolah(kecamatanName, searchKeyword);
    });

    // Saat kecamatan berubah
    $('#kecamatanFilter').change(function() {
        const kecamatanName = $(this).val();
        const searchKeyword = $('#searchUrlInput').val();
        console.log('Kecamatan changed to:', kecamatanName); // Debug
        loadSekolah(kecamatanName, searchKeyword);
    });

    // Saat pencarian berubah (dengan debounce)
    let searchTimeout;
    $('#searchUrlInput').on('input', function() {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(function() {
            const kecamatanName = $('#kecamatanFilter').val();
            const searchKeyword = $('#searchUrlInput').val();
            console.log('Search changed to:', searchKeyword); // Debug
            loadSekolah(kecamatanName, searchKeyword);
        }, 500);
    });

    // Checkbox pilih semua
    $(document).on('change', '#checkAllUrls', function() {
        const isChecked = $(this).prop('checked');
        $('.url-checkbox').prop('checked', isChecked);
        // Panggil updateUrlCount tanpa parameter
        updateUrlCount();
    });

    // Saat salah satu checkbox berubah
    $(document).on('change', '.url-checkbox', function() {
        // Panggil updateUrlCount tanpa parameter
        updateUrlCount();
    });
    
    // Auto refresh monitoring setiap 5 detik jika modal monitoring terbuka
    setInterval(function() {
        if ($('#monitoringModal').hasClass('show')) {
            startMonitoring();
        }
    }, 5000);
});
</script>

<script src="../assets/js/page/sekolah.js"></script>