<?php
// FILE: sekolah/index.php - VERSI YANG DIPERBAIKI
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

    <?php if ($has_active_urls) : ?>
        <div class="row mb-3">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex flex-column flex-md-row justify-content-between align-items-center">
                            <div class="mb-3 mb-md-0">
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
                            <div class="d-flex flex-column flex-md-row">
                                <?php if ($has_running_process) : ?>
                                    <button type="button" id="btnMonitor" class="btn btn-info mr-0 mr-md-2 mb-2 mb-md-0">
                                        <i class="fas fa-eye"></i> Monitor
                                    </button>
                                <?php endif; ?>
                                <button type="button" id="btnUpdateData" class="btn btn-warning w-100 w-md-auto" data-toggle="modal" data-target="#scrapingModal" <?= $has_running_process ? 'disabled' : '' ?>>
                                    <i class="fas fa-sync-alt"></i>
                                    <?= $has_running_process ? 'Processing...' : 'Update Data' ?>
                                </button>
                                <button type="button" id="btnCancel" class="btn btn-danger ml-0 ml-md-2 mt-2 mt-md-0" style="display: none;">
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
                    <div class="table-responsive">
                        <table class="table table-striped table-hover" id="table-1">
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
                                    <th class="text-center" style="min-width: 120px;">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $no = 1;
                                while ($data = mysqli_fetch_array($result)) :
                                ?>
                                    <tr>
                                        <td class="text-center"><?= $no++ ?></td>
                                        <td>
                                            <span class="badge badge-primary"><?= htmlspecialchars($data['npsn']) ?></span>
                                        </td>
                                        <td>
                                            <strong><?= htmlspecialchars($data['nama_sekolah']) ?></strong>
                                        </td>
                                        <td>
                                            <span class="badge badge-info">
                                                <i class="fas fa-graduation-cap mr-1"></i>
                                                <?= htmlspecialchars($data['jenjang_pendidikan']) ?>
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge <?= $data['status_sekolah'] == 'Negeri' ? 'badge-success' : 'badge-warning' ?> badge-pill px-3 py-2">
                                                <i class="fas fa-school mr-1"></i>
                                                <?= htmlspecialchars($data['status_sekolah']) ?>
                                            </span>
                                        </td>
                                        <td>
                                            <?php if ($data['alamat_jalan']) : ?>
                                                <div class="d-flex align-items-center">
                                                    <i class="fas fa-map-marker-alt text-danger mr-2"></i>
                                                    <span><?= htmlspecialchars($data['alamat_jalan']) ?></span>
                                                </div>
                                            <?php else : ?>
                                                <span class="text-muted font-italic">Belum ada data</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if ($data['nama_kecamatan']) : ?>
                                                <div class="d-flex align-items-center">
                                                    <i class="fas fa-location-arrow text-info mr-2"></i>
                                                    <span><?= htmlspecialchars($data['nama_kecamatan']) ?></span>
                                                </div>
                                            <?php else : ?>
                                                <span class="text-muted font-italic">Belum ada data</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if ($data['nama_kabupaten']) : ?>
                                                <div class="d-flex align-items-center">
                                                    <i class="fas fa-city text-primary mr-2"></i>
                                                    <span><?= htmlspecialchars($data['nama_kabupaten']) ?></span>
                                                </div>
                                            <?php else : ?>
                                                <span class="text-muted font-italic">Belum ada data</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
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
                                                <a href="edit.php?npsn=<?= $data['npsn'] ?>" class="btn btn-info" title="Edit Data" data-toggle="tooltip">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <a href="delete.php?npsn=<?= $data['npsn'] ?>" class="btn btn-danger" title="Hapus Data" data-toggle="tooltip" onclick="return confirm('Yakin ingin menghapus data sekolah <?= htmlspecialchars($data['nama_sekolah']) ?>?')">
                                                    <i class="fas fa-trash"></i>
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
            </div>
        </div>
    </div>
</section>

<!-- Modal untuk memilih URL -->
<div class="modal fade" id="scrapingModal" tabindex="-1" role="dialog" aria-labelledby="scrapingModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header bg-primary text-white">
        <h5 class="modal-title" id="scrapingModalLabel">
          <i class="fas fa-tasks mr-2"></i> Pilih URL untuk di-Update
        </h5>
        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body p-4">
        <div class="row mb-4">
            <div class="col-md-6">
                <div class="form-group">
                    <label for="kecamatanFilter" class="font-weight-bold">Filter Kecamatan</label>
                    <select class="form-control" id="kecamatanFilter">
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
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label for="searchUrlInput" class="font-weight-bold">Cari Sekolah</label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text bg-info text-white">
                                <i class="fas fa-search"></i>
                            </span>
                        </div>
                        <input type="text" id="searchUrlInput" class="form-control" placeholder="Nama sekolah atau NPSN..." autocomplete="off">
                    </div>
                </div>
            </div>
        </div>
        
        <div class="row mb-3">
            <div class="col-12">
                <div class="card">
                    <div class="card-header bg-white py-2">
                        <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input" id="checkAllUrls">
                            <label class="custom-control-label font-weight-bold" for="checkAllUrls">
                                <i class="fas fa-check-square mr-2"></i> Pilih Semua
                            </label>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        <div id="urlListContainer" style="max-height: 400px; overflow-y: auto;">
                            <div class="d-flex align-items-center justify-content-center py-5">
                                <div class="text-center">
                                    <div class="spinner-border text-primary mb-3" role="status" style="width: 3rem; height: 3rem;">
                                        <span class="sr-only">Loading...</span>
                                    </div>
                                    <h6 class="text-muted">Memuat data sekolah...</h6>
                                </div>
                            </div>
                        </div>
                        <div id="noResultsMessage" class="alert alert-warning m-3" style="display: none;">
                            <div class="d-flex align-items-center">
                                <i class="fas fa-exclamation-triangle mr-3 fa-2x text-warning"></i>
                                <div>
                                    <h6 class="mb-1 font-weight-bold">Tidak Ada Hasil</h6>
                                    <p class="mb-0">Tidak ditemukan sekolah yang sesuai dengan pencarian Anda.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
      </div>
        <div class="modal-footer bg-light">
          <div class="d-flex flex-column flex-sm-row justify-content-between align-items-stretch w-100 gap-2">
            
            <!-- Info jumlah sekolah -->
            <div class="url-count-info mb-2 mb-sm-0">
              <span class="badge badge-info w-100 text-center">0 sekolah dipilih</span>
            </div>

            <!-- Tombol aksi -->
            <div class="d-flex flex-column flex-sm-row gap-2 w-100 w-sm-auto">
              <button type="button" class="btn btn-secondary w-100 w-sm-auto" data-dismiss="modal">
                <i class="fas fa-times mr-1"></i> Tutup
              </button>
              <button type="button" class="btn btn-primary w-100 w-sm-auto" id="btnStartScrapingSelected" disabled>
                <i class="fas fa-sync-alt mr-1"></i> Update Terpilih
              </button>
              <button type="button" class="btn btn-warning w-100 w-sm-auto" id="btnStartScrapingAll">
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
$(document).ready(function() {
    // Inisialisasi DataTables
    $('#table-1').DataTable({
        responsive: true,
        language: {
            "processing": "Sedang memproses...",
            "search": "Pencarian:",
            "lengthMenu": "Tampilkan _MENU_ entri",
            "info": "Menampilkan _START_ sampai _END_ dari _TOTAL_ entri",
            "infoEmpty": "Menampilkan 0 sampai 0 dari 0 entri",
            "infoFiltered": "(disaring dari _MAX_ entri keseluruhan)",
            "infoPostFix": "",
            "loadingRecords": "Sedang memuat...",
            "zeroRecords": "Tidak ditemukan data yang sesuai",
            "emptyTable": "Tidak ada data yang tersedia pada tabel ini",
            "paginate": {
                "first": "Pertama",
                "previous": "Sebelumnya",
                "next": "Selanjutnya",
                "last": "Terakhir"
            },
            "aria": {
                "sortAscending": ": aktifkan untuk mengurutkan kolom secara ascending",
                "sortDescending": ": aktifkan untuk mengurutkan kolom secara descending"
            }
        },
        dom: '<"row"<"col-sm-6"l><"col-sm-6"f>>' +
             '<"row"<"col-sm-12"tr>>' +
             '<"row"<"col-sm-5"i><"col-sm-7"p>>',
        buttons: [
            'copy', 'csv', 'excel', 'pdf', 'print'
        ]
    });

    // Initialize tooltips
    $('[data-toggle="tooltip"]').tooltip();

    // Event handler untuk button "Update Data"
    $('#btnUpdateData').click(function(e) {
        e.preventDefault();
        
        // Cek apakah button disabled
        if ($(this).prop('disabled')) {
            return false;
        }
        
        // Tampilkan modal untuk memilih URL
        $('#scrapingModal').modal('show');
    });
    
    // Event handler untuk button "Update Terpilih"
    $('#btnStartScrapingSelected').click(function() {
        // Ambil semua URL yang dipilih
        const selectedUrls = [];
        $('.url-checkbox:checked').each(function() {
            selectedUrls.push($(this).val());
        });
        
        if (selectedUrls.length === 0) {
            alert('Pilih setidaknya satu sekolah untuk di-update.');
            return;
        }
        
        // Jalankan proses scraping
        startScraping('selected', selectedUrls);
    });
    
    // Event handler untuk button "Update Semua"
    $('#btnStartScrapingAll').click(function() {
        startScraping('all');
    });

    // Event handler untuk button "Monitor"
    $('#btnMonitor').click(function() {
        checkRunningProcess();
    });

    // Event handler untuk button "Cancel"
    $('#btnCancel').click(function() {
        stopScraping();
    });

    // Saat modal dibuka
    $('#scrapingModal').on('shown.bs.modal', function() {
        const kecamatanName = $('#kecamatanFilter').val();
        const searchKeyword = $('#searchUrlInput').val();
        console.log('Modal opened. Kecamatan:', kecamatanName, 'Search:', searchKeyword);
        loadSekolah(kecamatanName, searchKeyword);
    });

    // Saat kecamatan berubah
    $('#kecamatanFilter').change(function() {
        const kecamatanName = $(this).val();
        const searchKeyword = $('#searchUrlInput').val();
        console.log('Kecamatan changed to:', kecamatanName);
        loadSekolah(kecamatanName, searchKeyword);
    });

    // Saat pencarian berubah (dengan debounce)
    let searchTimeout;
    $('#searchUrlInput').on('input', function() {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(function() {
            const kecamatanName = $('#kecamatanFilter').val();
            const searchKeyword = $('#searchUrlInput').val();
            console.log('Search changed to:', searchKeyword);
            loadSekolah(kecamatanName, searchKeyword);
        }, 500);
    });

    // Checkbox pilih semua
    $(document).on('change', '#checkAllUrls', function() {
        const isChecked = $(this).prop('checked');
        $('.url-checkbox').prop('checked', isChecked);
        const totalCount = $('.url-checkbox').length;
        updateUrlCount(totalCount);
    });

    // Saat salah satu checkbox berubah
    $(document).on('change', '.url-checkbox', function() {
        const totalCount = $('.url-checkbox').length;
        const selectedCount = $('.url-checkbox:checked').length;
        updateUrlCount(totalCount);
        $('#btnStartScrapingSelected').prop('disabled', selectedCount === 0);
    });
});

// Variabel global untuk menyimpan log ID
let currentLogId = null;
let progressInterval = null;

function startScraping(mode, selectedUrls = []) {
    // Tampilkan progress bar
    $('#progressContainer').show();
    $('#progressBar').css('width', '0%');
    $('#progressText').text('0%');
    $('#statusText').text('Memulai proses...');
    
    // Sembunyikan modal
    $('#scrapingModal').modal('hide');
    
    // Disable button update
    $('#btnUpdateData').prop('disabled', true);
    $('#btnUpdateData').html('<i class="fas fa-sync-alt fa-spin"></i> Processing...');
    
    // Tampilkan button cancel
    $('#btnCancel').show();
    
    // Kirim request ke server
    $.ajax({
        url: 'run_scraper.php',
        type: 'POST',
        dataType: 'json',
        contentType: 'application/json',
        data: JSON.stringify({
            mode: mode,
            urls: selectedUrls
        }),
        success: function(response) {
            if (response.success) {
                // Simpan log ID untuk monitoring dan cancel
                currentLogId = response.log_id;
                
                // Mulai monitoring progress
                monitorProgress(response.log_id);
            } else {
                alert('Gagal memulai proses: ' + response.message);
                resetButtons();
            }
        },
        error: function(xhr, status, error) {
            console.error('AJAX Error:', status, error);
            console.error('Response:', xhr.responseText);
            alert('Terjadi kesalahan: ' + error);
            resetButtons();
        }
    });
}

function monitorProgress(logId) {
    // Hentikan interval sebelumnya jika ada
    if (progressInterval) {
        clearInterval(progressInterval);
    }
    
    progressInterval = setInterval(function() {
        $.ajax({
            url: 'check_scraping_progress.php',
            type: 'GET',
            dataType: 'json',
            data: { log_id: logId },
            success: function(response) {
                if (response.success) {
                    // Update progress bar
                    const percentage = response.progress_percentage;
                    $('#progressBar').css('width', percentage + '%');
                    $('#progressText').text(percentage + '%');
                    
                    // Update status text
                    $('#statusText').text(
                        `Memproses: ${response.processed_urls}/${response.total_urls} | ` +
                        `Sukses: ${response.success_count} | Gagal: ${response.failed_count}`
                    );
                    
                    // Cek apakah proses selesai
                    if (response.status === 'completed') {
                        clearInterval(progressInterval);
                        alert('Proses selesai! Semua data berhasil di-update.');
                        resetButtons();
                        // Reload halaman untuk menampilkan data terbaru
                        window.location.reload();
                    } else if (response.status === 'failed') {
                        clearInterval(progressInterval);
                        alert('Proses gagal: ' + response.error_message);
                        resetButtons();
                    } else if (response.status === 'cancelled') {
                        clearInterval(progressInterval);
                        alert('Proses dibatalkan oleh pengguna.');
                        resetButtons();
                    }
                } else {
                    clearInterval(progressInterval);
                    alert('Error checking progress: ' + response.message);
                    resetButtons();
                }
            },
            error: function(xhr, status, error) {
                clearInterval(progressInterval);
                console.error('Progress Check Error:', status, error);
                console.error('Response:', xhr.responseText);
                alert('Error checking progress. Silakan refresh halaman.');
                resetButtons();
            }
        });
    }, 2000); // Cek setiap 2 detik
}

function resetButtons() {
    $('#progressContainer').hide();
    $('#btnUpdateData').prop('disabled', false);
    $('#btnUpdateData').html('<i class="fas fa-sync-alt"></i> Update Data');
    $('#btnCancel').hide();
    
    // Reset log ID
    currentLogId = null;
}

function stopScraping() {
    if (!currentLogId) {
        alert('Tidak ada proses yang berjalan.');
        return;
    }
    
    if (confirm('Apakah Anda yakin ingin membatalkan proses ini?')) {
        $.ajax({
            url: 'stop_scraper.php',
            type: 'POST',
            dataType: 'json',
            contentType: 'application/json',
            data: JSON.stringify({
                log_id: currentLogId
            }),
            success: function(response) {
                if (response.success) {
                    alert('Proses berhasil dibatalkan.');
                    resetButtons();
                } else {
                    alert('Gagal membatalkan proses: ' + response.message);
                }
            },
            error: function(xhr, status, error) {
                console.error('Stop Scraping Error:', status, error);
                console.error('Response:', xhr.responseText);
                alert('Terjadi kesalahan saat membatalkan proses.');
            }
        });
    }
}

function checkRunningProcess() {
    $.ajax({
        url: 'check_scraping_progress.php',
        type: 'GET',
        dataType: 'json',
        success: function(response) {
            if (response.success && response.status === 'running') {
                currentLogId = response.log_id;
                $('#progressContainer').show();
                $('#btnUpdateData').prop('disabled', true);
                $('#btnUpdateData').html('<i class="fas fa-sync-alt fa-spin"></i> Processing...');
                $('#btnCancel').show();
                monitorProgress(response.log_id);
            } else {
                alert('Tidak ada proses yang berjalan saat ini.');
            }
        },
        error: function() {
            alert('Error checking running process.');
        }
    });
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
                    updateUrlCount(response.data.length);
                } else {
                    $('#urlListContainer').html('');
                    $('#noResultsMessage').show();
                    updateUrlCount(0);
                }
            } else {
                $('#urlListContainer').html(`<div class="alert alert-danger m-3">${response.message}</div>`);
                updateUrlCount(0);
            }
        },
        error: function(xhr, status, error) {
            console.error('Error:', error);
            console.log('Status:', status);
            console.log('Response Text:', xhr.responseText);
            $('#urlListContainer').html('<div class="alert alert-danger m-3">Terjadi kesalahan saat memuat data.</div>');
            updateUrlCount(0);
        }
    });
}

// Fungsi untuk memperbarui informasi jumlah sekolah
function updateUrlCount(count) {
    const selectedCount = $('.url-checkbox:checked').length;
    $('.url-count-info').html(`<span class="badge badge-info">${selectedCount} dari ${count} sekolah dipilih</span>`);
}
</script>