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
$running_check = mysqli_query($connection, "SELECT id FROM scraping_logs WHERE status = 'running' ORDER BY started_at DESC LIMIT 1");
$has_running_process = mysqli_num_rows($running_check) > 0;

$result = mysqli_query($connection, "SELECT si.*, k.nama_kecamatan, kab.nama_kabupaten, p.nama_provinsi
FROM sekolah_identitas si
LEFT JOIN kecamatan k ON si.id_kecamatan_fk = k.id_kecamatan
LEFT JOIN kabupaten_kota kab ON k.id_kabupaten_fk = kab.id_kabupaten
LEFT JOIN provinsi p ON kab.id_provinsi_fk = p.id_provinsi
ORDER BY si.nama_sekolah ASC");
?>

<section class="section">
    <div class="section-header d-flex justify-content-between align-items-center">
        <h1>Identitas Sekolah</h1>
        <div>
            <a href="./tambah_url.php" class="btn btn-info mr-2">
                <i class="fas fa-link"></i> Upload URL
            </a>
            <a href="./create.php" class="btn btn-primary">
                <i class="fas fa-plus"></i> Tambah Sekolah
            </a>
        </div>
    </div>

    <?php if ($has_active_urls) : ?>
        <div class="row mb-3">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="mb-1 font-weight-bold text-primary">Update Data Otomatis</h6>
                                <small class="text-muted">
                                    Tersedia <span class="badge badge-success"><?= $url_data['total'] ?></span> URL aktif untuk scraping data sekolah.
                                    <?php if ($has_running_process) : ?>
                                        <br><span class="text-warning font-weight-bold">
                                            <i class="fas fa-exclamation-triangle"></i> Ada proses yang sedang berjalan.
                                        </span>
                                    <?php endif; ?>
                                </small>
                            </div>
                            <div>
                                <?php if ($has_running_process) : ?>
                                    <button id="btnMonitor" class="btn btn-info mr-2" onclick="checkRunningProcess()">
                                        <i class="fas fa-eye"></i> Monitor
                                    </button>
                                <?php endif; ?>
                                <button id="btnUpdateData" class="btn btn-warning" data-toggle="modal" data-target="#scrapingModal" <?= $has_running_process ? 'disabled' : '' ?>>
                                    <i class="fas fa-sync-alt"></i>
                                    <?= $has_running_process ? 'Processing...' : 'Update Data' ?>
                                </button>
                                <button id="btnCancel" class="btn btn-danger ml-2" style="display: none;" onclick="stopScraping()">
                                    <i class="fas fa-stop"></i> Batal
                                </button>
                            </div>
                        </div>
                        <div id="progressContainer" class="mt-3" style="display: none;">
                            <div class="progress mb-2" style="height: 25px;">
                                <div id="progressBar" class="progress-bar progress-bar-striped progress-bar-animated bg-success" role="progressbar" style="width: 0%" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100">
                                    <span id="progressText" class="font-weight-bold">0%</span>
                                </div>
                            </div>
                            <div id="statusText" class="text-center text-muted">Menunggu memulai proses...</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <?php else : ?>
        <div class="row mb-3">
            <div class="col-12">
                <div class="alert alert-info border-left-info shadow-sm">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-info-circle fa-2x text-info mr-3"></i>
                        <div>
                            <h6 class="mb-2 font-weight-bold">Tidak Ada URL Aktif</h6>
                            <p class="mb-1">Tidak ada URL aktif untuk scraping data sekolah.</p>
                            <small class="text-muted">
                                Silakan <a href="./tambah_url.php" class="alert-link font-weight-bold">upload URL</a> terlebih dahulu untuk memulai proses scraping.
                            </small>
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
                                                    <a href="delete.php?npsn=<?= $data['npsn'] ?>" class="btn btn-danger" 
                                                       title="Hapus Data" data-toggle="tooltip" data-placement="top"
                                                       onclick="return confirm('Yakin ingin menghapus data sekolah <?= htmlspecialchars($data['nama_sekolah']) ?>?')">
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

<div class="modal fade" id="scrapingModal" tabindex="-1" role="dialog" aria-labelledby="scrapingModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog-centered" role="document">
    <div class="modal-content shadow-lg">
      <div class="modal-header bg-gradient-primary text-white">
        <h5 class="modal-title" id="scrapingModalLabel">
          <i class="fas fa-tasks mr-2"></i> Pilih URL untuk di-Update
        </h5>
        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body p-4">
        <div class="row mb-3">
          <div class="col-12">
            <div class="card border-left-info shadow-sm">
              <div class="card-body py-3">
                <div class="input-group">
                  <div class="input-group-prepend">
                    <span class="input-group-text bg-info text-white">
                      <i class="fas fa-search"></i>
                    </span>
                  </div>
                  <input type="text" id="searchUrlInput" class="form-control" placeholder="Cari URL berdasarkan deskripsi atau URL..." autocomplete="off">
                </div>
                <small class="text-muted mt-1 d-block">
                  <i class="fas fa-info-circle"></i> Ketik untuk mencari URL berdasarkan deskripsi atau alamat URL
                </small>
              </div>
            </div>
          </div>
        </div>
        <div class="row mb-3">
          <div class="col-12">
            <div class="card border-left-primary shadow-sm">
              <div class="card-body py-3">
                <div class="custom-control custom-checkbox">
                  <input type="checkbox" class="custom-control-input" id="checkAllUrls">
                  <label class="custom-control-label font-weight-bold text-primary" for="checkAllUrls">
                    <i class="fas fa-check-double mr-2"></i> Pilih Semua / Hapus Pilihan
                  </label>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-12">
            <div id="urlListContainer" class="border rounded shadow-sm bg-white" style="max-height: 400px; overflow-y: auto; min-height: 200px;">
              <div class="d-flex align-items-center justify-content-center py-5">
                <div class="text-center">
                  <div class="spinner-border text-primary mb-3" role="status" style="width: 3rem; height: 3rem;">
                    <span class="sr-only">Loading...</span>
                  </div>
                  <h6 class="text-muted">Memuat URL aktif dari database...</h6>
                </div>
              </div>
            </div>
            <div id="noResultsMessage" class="alert alert-warning mt-3" style="display: none;">
              <div class="d-flex align-items-center">
                <i class="fas fa-search mr-3 fa-2x text-warning"></i>
                <div>
                  <h6 class="mb-1 font-weight-bold">Tidak Ada Hasil</h6>
                  <p class="mb-0">Tidak ditemukan URL yang sesuai dengan pencarian Anda.</p>
                  <small class="text-muted">Coba gunakan kata kunci yang berbeda atau hapus filter pencarian.</small>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="modal-footer bg-light border-top">
        <div class="d-flex w-100 align-items-center justify-content-between">
          <div class="mr-auto">
            <small class="text-muted url-count-info">
              <i class="fas fa-link mr-1"></i> Memuat...
            </small>
          </div>
          <div class="btn-group">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">
              <i class="fas fa-times mr-1"></i> Batal
            </button>
            <button type="button" class="btn btn-primary" id="btnStartScrapingSelected" onclick="startScraping('selected')" disabled>
              <i class="fas fa-tasks mr-1"></i> Update Terpilih
            </button>
            <button type="button" class="btn btn-warning" id="btnStartScrapingAll" onclick="startScraping('all')">
              <i class="fas fa-globe mr-1"></i> Update Semua
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
</script>

<script src="../assets/js/page/sekolah.js"></script>