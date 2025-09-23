<?php
require_once '../layout/_top.php';
require_once '../helper/connection.php';

// Ambil URL yang sudah ada untuk ditampilkan di sisi kanan halaman
$existing_urls = mysqli_query($connection, "SELECT * FROM scraping_urls ORDER BY created_at DESC");

// Cek apakah ada URL induk yang tersimpan
$saved_url_induk = mysqli_query($connection, "SELECT * FROM url_induk_scrape WHERE status = 'active' ORDER BY created_at DESC LIMIT 1");
$url_induk_data = mysqli_fetch_assoc($saved_url_induk);

// Query untuk filter kabupaten
$kabupaten_query = mysqli_query($connection, "SELECT id, nama_kabupaten FROM kabupaten_scrape WHERE status = 'active' ORDER BY nama_kabupaten ASC");

// Query untuk filter kecamatan
$kecamatan_query = mysqli_query($connection, "SELECT id, nama_kecamatan, kabupaten_scrape_id FROM kecamatan_scrape WHERE status = 'active' ORDER BY nama_kecamatan ASC");
?>

<section class="section">
    <div class="section-header d-flex flex-wrap justify-content-between align-items-center">
        <h1 class="mb-2 mb-md-0"><i class="fas fa-link"></i> Upload URL</h1>
        <a href="./index.php" class="btn btn-light">
            <i class="fas fa-arrow-left"></i> <span class="d-none d-sm-inline">Kembali</span>
        </a>
    </div>

    <div class="row">
        <div class="col-12 col-xl-8 mb-4">
            
            <div class="card card-primary shadow-sm mb-4">
                <div class="card-header">
                    <h4 class="mb-0"><i class="fas fa-sliders-h"></i> Konfigurasi Scraping Otomatis</h4>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-12 col-lg-8">
                            <label for="urlInduk" class="form-label font-weight-bold">1. Set URL Induk</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-link"></i></span>
                                <input type="url" class="form-control" id="urlInduk" 
                                       placeholder="https://dapo.kemendikdasmen.go.id/sp/1/150000"
                                       value="<?= $url_induk_data ? htmlspecialchars($url_induk_data['url']) : '' ?>">
                                <button class="btn btn-primary" id="simpanUrlBtn">
                                    <i class="fas fa-save me-1"></i> <span class="d-none d-sm-inline">Simpan</span>
                                </button>
                            </div>
                        </div>
                        <div class="col-12 col-lg-4">
                            <label for="filterData" class="form-label font-weight-bold">2. Pilih Jenis Data</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-filter"></i></span>
                                <select class="form-select" id="filterData">
                                    <option value="">Pilih Data...</option>
                                    <option value="kabupaten">Kabupaten</option>
                                    <option value="kecamatan">Kecamatan</option>
                                    <option value="sekolah">Sekolah</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card card-success shadow-sm mb-4">
                <div class="card-header d-flex flex-wrap justify-content-between align-items-center">
                    <h4 class="mb-2 mb-md-0"><i class="fas fa-table"></i> Data Hasil Scraping</h4>
                    <div class="text-muted small">
                        Total Item: <span id="dataCount" class="badge bg-secondary">0</span> |
                        Terpilih: <span id="selectedCount" class="badge bg-info">0</span>
                    </div>
                </div>
                <div class="card-body p-0">
                    
                    <div id="filtersContainer" class="p-3 border-bottom" style="display: none;">
                        <div class="row g-3">
                            <div class="col-12 col-md-6" id="kabupatenFilterContainer" style="display: none;">
                                <label for="kabupatenFilter" class="form-label font-weight-bold">Filter Kabupaten</label>
                                <select class="form-select" id="kabupatenFilter">
                                    <option value="">Semua Kabupaten</option>
                                    <?php 
                                    while ($kab = mysqli_fetch_array($kabupaten_query)) : 
                                    ?>
                                        <option value="<?= $kab['id'] ?>"><?= htmlspecialchars($kab['nama_kabupaten']) ?></option>
                                    <?php endwhile; ?>
                                </select>
                            </div>
                            
                            <div class="col-12 col-md-6" id="kecamatanFilterContainer" style="display: none;">
                                <label for="kecamatanFilter" class="form-label font-weight-bold">Filter Kecamatan</label>
                                <select class="form-select" id="kecamatanFilter">
                                    <option value="">Semua Kecamatan</option>
                                    <?php
                                    mysqli_data_seek($kecamatan_query, 0); 
                                    while ($kec = mysqli_fetch_array($kecamatan_query)) : 
                                    ?>
                                        <option value="<?= $kec['id'] ?>" data-kabupaten-id="<?= $kec['kabupaten_scrape_id'] ?>"><?= htmlspecialchars($kec['nama_kecamatan']) ?></option>
                                    <?php endwhile; ?>
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <div class="table-responsive" style="max-height: 450px; overflow-y: auto;">
                        <table class="table table-striped table-hover mb-0">
                            <thead class="table-light sticky-top">
                                <tr>
                                    <th class="text-center align-middle" width="50">
                                        <div class="form-check d-flex justify-content-center align-items-center h-100">
                                            <input type="checkbox" class="form-check-input" id="selectAll">
                                        </div>
                                    </th>
                                    <th width="50">No</th>
                                    <th>Nama</th>
                                    <th class="d-none d-md-table-cell">URL</th>
                                </tr>
                            </thead>
                            <tbody id="tableBody">
                                <tr>
                                    <td colspan="4" class="text-center text-muted p-5">
                                        <i class="fas fa-info-circle fa-2x mb-2 d-block"></i>
                                        Silakan atur Konfigurasi di atas untuk memulai.
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="card-footer">
                    <div id="progressContainer" class="mb-3" style="display: none;">
                        <label class="form-label small text-muted">Progress Proses</label>
                        <div class="progress" style="height: 20px;">
                            <div class="progress-bar progress-bar-striped progress-bar-animated bg-info" 
                                 id="progressBar" role="progressbar" style="width: 0%"
                                 aria-valuenow="0" aria-valuemin="0" aria-valuemax="100">
                                <span id="progressText" class="font-weight-bold">0%</span>
                            </div>
                        </div>
                    </div>
                    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2">
                        <div class="btn-group flex-wrap">
                            <button class="btn btn-info btn-sm btn-md-normal" id="scrapeBtn" disabled>
                                <i class="fas fa-search-plus me-1"></i> 
                                <span class="d-none d-sm-inline">Scrape Data Terpilih</span>
                                <span class="d-sm-none">Scrape</span>
                            </button>
                            <button class="btn btn-success btn-sm btn-md-normal" id="importTerpilihBtn" disabled>
                                <i class="fas fa-file-import me-1"></i> 
                                <span class="d-none d-sm-inline">Import Sekolah Terpilih</span>
                                <span class="d-sm-none">Import</span>
                            </button>
                        </div>
                        <button class="btn btn-danger btn-sm btn-md-normal" id="batalBtn" disabled>
                            <i class="fas fa-times-circle me-1"></i> 
                            <span class="d-none d-sm-inline">Batalkan Proses</span>
                            <span class="d-sm-none">Batal</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-xl-4">
            <div class="card card-warning shadow-sm mb-4">
                <div class="card-header">
                    <h4 class="mb-0"><i class="fas fa-pencil-alt"></i> Tambah URL Manual</h4>
                </div>
                <div class="card-body">
                    <form id="urlForm">
                        <div id="urlContainer">
                            <div class="url-input-group mb-3" data-index="1">
                                <label class="form-label small">URL Sekolah #1</label>
                                <div class="input-group">
                                    <input type="url" name="urls[1][url]" class="form-control form-control-sm" placeholder="URL Lengkap Sekolah" required>
                                    <button type="button" class="btn btn-danger btn-sm remove-url" style="display: none;">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                                <input type="text" name="urls[1][description]" class="form-control form-control-sm mt-1" placeholder="Nama sekolah (opsional)">
                            </div>
                        </div>
                        
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <button type="button" id="addUrlBtn" class="btn btn-secondary btn-sm">
                                <i class="fas fa-plus"></i> <span class="d-none d-sm-inline">Tambah Field</span>
                            </button>
                            <small class="text-muted">Maks. 30 URL</small>
                        </div>
                        
                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary" id="submitBtn">
                                <i class="fas fa-upload"></i> Upload Semua URL
                            </button>
                        </div>
                    </form>
                    
                    <div id="uploadProgress" class="mt-3" style="display: none;">
                        <div class="progress mb-1" style="height: 10px;">
                            <div id="uploadProgressBar" class="progress-bar bg-success" role="progressbar" style="width: 0%"></div>
                        </div>
                        <div id="uploadStatusText" class="text-center text-muted small">Memulai upload...</div>
                    </div>
                </div>
            </div>

            <div class="card card-info shadow-sm">
                <div class="card-header">
                    <h4 class="mb-0"><i class="fas fa-history"></i> URL Tersimpan</h4>
                </div>
                <div class="card-body p-0">
                    <div class="list-group list-group-flush" style="max-height: 450px; overflow-y: auto;">
                        <?php if (mysqli_num_rows($existing_urls) > 0): ?>
                            <?php while ($url = mysqli_fetch_array($existing_urls)): ?>
                                <div class="list-group-item list-group-item-action">
                                    <div class="d-flex w-100 justify-content-between align-items-start">
                                        <div class="flex-grow-1" style="overflow: hidden;">
                                            <h6 class="mb-1 text-truncate" title="<?= htmlspecialchars($url['description'] ?: 'Tidak ada deskripsi') ?>">
                                                <?= htmlspecialchars($url['description'] ?: 'Tidak ada deskripsi') ?>
                                            </h6>
                                            <p class="mb-1 text-primary small text-truncate d-none d-md-block" title="<?= htmlspecialchars($url['url']) ?>">
                                                <?= htmlspecialchars($url['url']) ?>
                                            </p>
                                            <small>
                                                Status:
                                                <span class="badge bg-<?= $url['status'] == 'active' ? 'success' : 'secondary' ?>">
                                                    <?= ucfirst($url['status']) ?>
                                                </span>
                                            </small>
                                        </div>
                                        <button class="btn btn-sm btn-outline-danger delete-url ms-2" data-id="<?= $url['id'] ?>" title="Hapus URL">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </div>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <div class="text-center text-muted p-5">
                                <i class="fas fa-box-open fa-2x mb-2 d-block"></i>
                                <p class="mb-0">Belum ada URL yang tersimpan.</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

        </div>
    </div>
</section>

<div class="modal fade" id="loadingModal" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-body text-center py-4">
                <div class="spinner-border text-primary mb-3" role="status">
                    <span class="visually-hidden"></span>
                </div>
                <h6 id="loadingText">Memproses...</h6>
                <p class="text-muted mb-0" id="loadingDetail">Mohon tunggu sebentar</p>
            </div>
        </div>
    </div>
</div>

<style>
/* Responsive improvements */
@media (max-width: 768px) {
    .section-header h1 {
        font-size: 1.25rem;
    }
    
    .card-header h4 {
        font-size: 1rem;
    }
    
    .btn-group {
        flex-direction: column;
        width: 100%;
    }
    
    .btn-group .btn {
        border-radius: 0.375rem !important;
        margin-bottom: 0.25rem;
    }
    
    .btn-group .btn:last-child {
        margin-bottom: 0;
    }
    
    .table-responsive {
        font-size: 0.875rem;
    }
}

@media (max-width: 576px) {
    .input-group .btn {
        padding: 0.375rem 0.75rem;
        font-size: 0.875rem;
    }
    
    .progress {
        height: 1.5rem !important;
    }
    
    .list-group-item h6 {
        font-size: 0.875rem;
    }
    
    .btn-sm-responsive {
        padding: 0.25rem 0.5rem;
        font-size: 0.75rem;
    }
}

/* Custom button size for medium screens */
@media (min-width: 768px) {
    .btn-md-normal {
        padding: 0.5rem 1rem;
        font-size: 0.875rem;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Variabel Global untuk state management
    let urlCounter = 1;
    const maxUrls = 30;
    let currentUrlIndukId = <?= $url_induk_data ? $url_induk_data['id'] : 'null' ?>;
    let currentDataType = '';
    let selectedKabupatenId = '';
    let selectedKecamatanId = '';
    let importProgressInterval = null;
    let loadingModalInstance = new bootstrap.Modal(document.getElementById('loadingModal'));
    
    // Simpan semua opsi kecamatan di awal untuk filtering di sisi client
    const allKecamatanOptions = Array.from(document.querySelectorAll('#kecamatanFilter option'));

    // Event listener untuk add URL manual
    const addUrlBtn = document.getElementById('addUrlBtn');
    if(addUrlBtn) {
        addUrlBtn.addEventListener('click', function() {
            if (urlCounter >= maxUrls) {
                iziToast.warning({
                    title: 'Peringatan',
                    message: `Maksimal ${maxUrls} URL per upload.`,
                    position: 'topCenter',
                    timeout: 5000
                });
                return;
            }
            
            urlCounter++;
            const urlContainer = document.getElementById('urlContainer');
            const newUrlGroup = document.createElement('div');
            newUrlGroup.className = 'url-input-group mb-3';
            newUrlGroup.setAttribute('data-index', urlCounter);
            newUrlGroup.innerHTML = `
                <label class="form-label small">URL Sekolah #${urlCounter}</label>
                <div class="input-group">
                    <input type="url" name="urls[${urlCounter}][url]" class="form-control form-control-sm" placeholder="URL Lengkap Sekolah" required>
                    <button type="button" class="btn btn-danger btn-sm remove-url">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
                <input type="text" name="urls[${urlCounter}][description]" class="form-control form-control-sm mt-1" placeholder="Nama sekolah (opsional)">
            `;
            urlContainer.appendChild(newUrlGroup);
            
            updateRemoveButtons();
        });
    }

    // Event listener untuk remove URL
    document.addEventListener('click', function(e) {
        if (e.target && e.target.closest('.remove-url')) {
            e.target.closest('.url-input-group').remove();
            updateRemoveButtons();
            renumberUrlGroups();
        }
    });

    // Event listener untuk submit form URL manual
    const urlForm = document.getElementById('urlForm');
    if(urlForm) {
        urlForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            const urls = [];
            
            // Kumpulkan semua URL
            for (let i = 1; i <= urlCounter; i++) {
                const url = formData.get(`urls[${i}][url]`);
                const description = formData.get(`urls[${i}][description]`);
                
                if (url && url.trim()) {
                    urls.push({
                        url: url.trim(),
                        description: description ? description.trim() : null
                    });
                }
            }
            
            if (urls.length === 0) {
                iziToast.warning({
                    title: 'Peringatan',
                    message: 'Masukkan setidaknya satu URL.',
                    position: 'topCenter',
                    timeout: 5000
                });
                return;
            }
            
            // Tampilkan progress
            document.getElementById('uploadProgress').style.display = 'block';
            document.getElementById('submitBtn').disabled = true;
            
            // Upload URLs satu per satu
            uploadUrls(urls, 0);
        });
    }

    // Event listener untuk simpan URL induk
    const simpanUrlBtn = document.getElementById('simpanUrlBtn');
    if(simpanUrlBtn) {
        simpanUrlBtn.addEventListener('click', function() {
            const urlInduk = document.getElementById('urlInduk').value.trim();
            
            if (!urlInduk) {
                iziToast.warning({
                    title: 'Peringatan',
                    message: 'Masukkan URL induk terlebih dahulu.',
                    position: 'topCenter',
                    timeout: 5000
                });
                return;
            }
            
            // Validasi URL
            try {
                new URL(urlInduk);
            } catch (e) {
                iziToast.error({
                    title: 'Error',
                    message: 'URL tidak valid.',
                    position: 'topCenter',
                    backgroundColor: '#e74a3b',
                    progressBarColor: '#a02622',
                    timeout: 5000
                });
                return;
            }
            
            this.disabled = true;
            this.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> Menyimpan...';
            
            fetch('import_handler.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ action: 'save_url_induk', url: urlInduk })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    currentUrlIndukId = data.url_induk_id;
                    iziToast.success({
                        title: 'Sukses',
                        message: 'URL induk berhasil disimpan.',
                        position: 'topCenter',
                        backgroundColor: '#1cc88a',
                        progressBarColor: '#0f6848'
                    });
                    
                    // Auto-scrape kabupaten jika diperlukan
                    const currentFilter = document.getElementById('filterData').value;
                    if (currentFilter) {
                        loadTableData(currentFilter, true);
                    }
                } else {
                    iziToast.error({
                        title: 'Error',
                        message: data.message || 'Gagal menyimpan URL induk.',
                        position: 'topCenter',
                        backgroundColor: '#e74a3b',
                        progressBarColor: '#a02622'
                    });
                }
            })
            .catch(error => {
                iziToast.error({
                    title: 'Error',
                    message: 'Terjadi kesalahan: ' + error.message,
                    position: 'topCenter',
                    backgroundColor: '#e74a3b',
                    progressBarColor: '#a02622'
                });
            })
            .finally(() => {
                this.disabled = false;
                this.innerHTML = '<i class="fas fa-save me-1"></i> <span class="d-none d-sm-inline">Simpan</span>';
            });
        });
    }

    // Event listener untuk delete URL tersimpan
    document.addEventListener('click', function(e) {
        if (e.target && e.target.closest('.delete-url')) {
            const urlId = e.target.closest('.delete-url').getAttribute('data-id');
            
            if (confirm('Apakah Anda yakin ingin menghapus URL ini?')) {
                fetch('import_handler.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ action: 'delete_url', url_id: urlId })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        iziToast.success({
                            title: 'Sukses',
                            message: 'URL berhasil dihapus.',
                            position: 'topCenter',
                            backgroundColor: '#1cc88a',
                            progressBarColor: '#0f6848'
                        });
                        setTimeout(() => window.location.reload(), 1000);
                    } else {
                        iziToast.error({
                            title: 'Error',
                            message: data.message || 'Gagal menghapus URL.',
                            position: 'topCenter',
                            backgroundColor: '#e74a3b',
                            progressBarColor: '#a02622'
                        });
                    }
                });
            }
        }
    });

    // **PERBAIKAN UTAMA**: Event listener untuk filter data
    const filterData = document.getElementById('filterData');
    if(filterData) {
        filterData.addEventListener('change', function() {
            currentDataType = this.value;
            
            // Reset semua filter saat tipe data utama berubah
            selectedKabupatenId = '';
            selectedKecamatanId = '';
            document.getElementById('kabupatenFilter').value = '';
            document.getElementById('kecamatanFilter').value = '';

            const filtersContainer = document.getElementById('filtersContainer');
            const kabFilterContainer = document.getElementById('kabupatenFilterContainer');
            const kecFilterContainer = document.getElementById('kecamatanFilterContainer');
            
            // **FIX**: Logika filter yang diperbaiki
            if (currentDataType === 'kecamatan') {
                filtersContainer.style.display = 'block';
                kabFilterContainer.style.display = 'block';
                kecFilterContainer.style.display = 'none'; // Tidak tampil untuk kecamatan
            } else if (currentDataType === 'sekolah') {
                filtersContainer.style.display = 'block';
                kabFilterContainer.style.display = 'none'; // Tidak tampil untuk sekolah
                kecFilterContainer.style.display = 'block';
                updateKecamatanFilterOptions(); // Reset ke semua kecamatan
            } else {
                filtersContainer.style.display = 'none';
                kabFilterContainer.style.display = 'none';
                kecFilterContainer.style.display = 'none';
            }
            
            document.getElementById('progressContainer').style.display = 'none';

            if (!currentDataType) {
                document.getElementById('tableBody').innerHTML = `
                    <tr>
                      <td colspan="4" class="text-center text-muted p-5">
                        <i class="fas fa-info-circle fa-2x mb-2 d-block"></i>
                        Silakan atur Konfigurasi di atas untuk memulai.
                      </td>
                    </tr>
                `;
                updateDataCount();
                updateButtonStates();
                return;
            }
            
            loadTableData(currentDataType);
        });
    }

    // **PERBAIKAN**: Fungsi untuk mengupdate opsi filter kecamatan (untuk level sekolah saja)
    function updateKecamatanFilterOptions() {
        const kecamatanSelect = document.getElementById('kecamatanFilter');
        const currentKecValue = kecamatanSelect.value;
        
        kecamatanSelect.innerHTML = '';
        kecamatanSelect.add(new Option('Semua Kecamatan', ''));

        // Tampilkan semua kecamatan untuk level sekolah
        allKecamatanOptions.forEach(option => {
            if (option.value === '') return;
            kecamatanSelect.add(new Option(option.text, option.value));
        });
        
        kecamatanSelect.value = currentKecValue;
        if(kecamatanSelect.value !== currentKecValue){
             kecamatanSelect.value = '';
        }
    }

    // Event listener untuk filter kabupaten (untuk level kecamatan)
    const kabupatenFilter = document.getElementById('kabupatenFilter');
    if(kabupatenFilter) {
        kabupatenFilter.addEventListener('change', function() {
            selectedKabupatenId = this.value;
            
            // Hanya berlaku untuk level kecamatan
            if (currentDataType === 'kecamatan') {
                loadTableData(currentDataType);
            }
        });
    }
    
    // Event listener untuk filter kecamatan (untuk level sekolah)
    const kecamatanFilter = document.getElementById('kecamatanFilter');
    if(kecamatanFilter) {
        kecamatanFilter.addEventListener('change', function() {
            selectedKecamatanId = this.value;
            
            // Hanya berlaku untuk level sekolah
            if (currentDataType === 'sekolah') {
                loadTableData('sekolah');
            }
        });
    }
    
    function loadTableData(dataType, autoScrape = false) {
        if (!currentUrlIndukId) {
            document.getElementById('tableBody').innerHTML = `
                <tr>
                    <td colspan="4" class="text-center text-warning p-5">
                        <i class="fas fa-exclamation-triangle fa-2x mb-2 d-block"></i>
                        Simpan URL Induk terlebih dahulu.
                    </td>
                </tr>
            `;
            updateDataCount();
            return;
        }
        
        document.getElementById('tableBody').innerHTML = `
            <tr>
                <td colspan="4" class="text-center p-5">
                    <div class="spinner-border text-primary" role="status"></div>
                    <p class="mt-2 mb-0">Memuat data ${dataType}...</p>
                </td>
            </tr>
        `;
        
        // Build request data berdasarkan level dan filter yang aktif
        const requestData = {
            action: 'check_data', 
            data_type: dataType, 
            url_induk_id: currentUrlIndukId
        };
        
        // Tambahkan filter yang sesuai berdasarkan level data
        if (dataType === 'kecamatan' && selectedKabupatenId) {
            requestData.kabupaten_id = selectedKabupatenId;
        }
        if (dataType === 'sekolah' && selectedKecamatanId) {
            requestData.kecamatan_id = selectedKecamatanId;
        }
        
        fetch('import_handler.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(requestData)
        })
        .then(response => response.json())
        .then(data => {
            if (data.success && data.has_data) {
                renderTableData(dataType, data.data);
            } else {
                if(autoScrape && dataType === 'kabupaten'){
                    triggerScraper('kabupaten');
                } else {
                    document.getElementById('tableBody').innerHTML = `
                        <tr>
                            <td colspan="4" class="text-center text-muted p-5">
                                <i class="fas fa-inbox fa-2x mb-2 d-block"></i>
                                Data ${dataType} tidak ditemukan untuk filter ini. <br>
                                Coba scrape data dari tingkat sebelumnya atau ubah filter.
                            </td>
                        </tr>
                    `;
                }
            }
        })
        .catch(error => {
            document.getElementById('tableBody').innerHTML = `
                <tr>
                    <td colspan="4" class="text-center text-danger p-5"><i class="fas fa-exclamation-circle me-2"></i>Error memuat data</td>
                </tr>
            `;
        })
        .finally(() => {
            updateDataCount();
            updateSelectedCount();
            updateButtonStates();
        });
    }

    // Function untuk upload URLs manual
    function uploadUrls(urls, index) {
        if (index >= urls.length) {
            // Semua URL berhasil diupload
            document.getElementById('uploadProgress').style.display = 'none';
            document.getElementById('submitBtn').disabled = false;
            document.getElementById('urlForm').reset();
            
            // Reset counter dan hapus field tambahan
            urlCounter = 1;
            const urlContainer = document.getElementById('urlContainer');
            urlContainer.innerHTML = `
                <div class="url-input-group mb-3" data-index="1">
                    <label class="form-label small">URL Sekolah #1</label>
                    <div class="input-group">
                        <input type="url" name="urls[1][url]" class="form-control form-control-sm" placeholder="URL Lengkap Sekolah" required>
                        <button type="button" class="btn btn-danger btn-sm remove-url" style="display: none;">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                    <input type="text" name="urls[1][description]" class="form-control form-control-sm mt-1" placeholder="Nama sekolah (opsional)">
                </div>
            `;
            
            iziToast.success({
                title: 'Sukses',
                message: `${urls.length} URL berhasil diupload.`,
                position: 'topCenter',
                backgroundColor: '#1cc88a',
                progressBarColor: '#0f6848'
            });
            
            setTimeout(() => window.location.reload(), 2000);
            return;
        }
        
        const url = urls[index];
        const progress = Math.round(((index + 1) / urls.length) * 100);
        
        // Update progress
        document.getElementById('uploadProgressBar').style.width = progress + '%';
        document.getElementById('uploadStatusText').textContent = `Upload ${index + 1}/${urls.length}: ${url.description || url.url}`;
        
        fetch('import_handler.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ action: 'add_url', url: url.url, description: url.description })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Lanjut ke URL berikutnya
                setTimeout(() => uploadUrls(urls, index + 1), 500);
            } else {
                document.getElementById('uploadProgress').style.display = 'none';
                document.getElementById('submitBtn').disabled = false;
                iziToast.error({
                    title: 'Error',
                    message: `Gagal upload URL ${index + 1}: ${data.message}`,
                    position: 'topCenter',
                    backgroundColor: '#e74a3b',
                    progressBarColor: '#a02622'
                });
            }
        })
        .catch(error => {
            document.getElementById('uploadProgress').style.display = 'none';
            document.getElementById('submitBtn').disabled = false;
            iziToast.error({
                title: 'Error',
                message: `Error upload URL ${index + 1}: ${error.message}`,
                position: 'topCenter',
                backgroundColor: '#e74a3b',
                progressBarColor: '#a02622'
            });
        });
    }

    function updateRemoveButtons() {
        const urlGroups = document.querySelectorAll('.url-input-group');
        urlGroups.forEach(group => {
            const removeBtn = group.querySelector('.remove-url');
            if (urlGroups.length > 1) {
                removeBtn.style.display = 'block';
            } else {
                removeBtn.style.display = 'none';
            }
        });
    }

    function renumberUrlGroups() {
        const urlGroups = document.querySelectorAll('.url-input-group');
        urlCounter = 0;
        
        urlGroups.forEach((group, index) => {
            urlCounter++;
            const newIndex = index + 1;
            
            group.setAttribute('data-index', newIndex);
            group.querySelector('label').textContent = `URL Sekolah #${newIndex}`;
            
            const urlInput = group.querySelector('input[type="url"]');
            const descInput = group.querySelector('input[type="text"]');
            
            urlInput.name = `urls[${newIndex}][url]`;
            descInput.name = `urls[${newIndex}][description]`;
        });
    }

    // Event listener untuk checkbox di body
    document.addEventListener('change', function(e) {
        if (e.target && e.target.classList.contains('row-checkbox')) {
            updateSelectedCount();
            updateSelectAllState();
            updateButtonStates();
        }
    });
    
    const scrapeBtn = document.getElementById('scrapeBtn');
    if(scrapeBtn) {
        scrapeBtn.addEventListener('click', function() {
            if (!currentDataType) {
                iziToast.warning({ title: 'Peringatan', message: 'Pilih tipe data terlebih dahulu.', position: 'topCenter', timeout: 5000 });
                return;
            }
            
            const selectedIds = getSelectedIds();
            if (selectedIds.length === 0) {
                iziToast.warning({ title: 'Peringatan', message: 'Pilih setidaknya satu data untuk di-scrape.', position: 'topCenter', timeout: 5000 });
                return;
            }
            
            let nextScraperType = '';
            if(currentDataType === 'kabupaten') nextScraperType = 'kecamatan';
            else if(currentDataType === 'kecamatan') nextScraperType = 'sekolah';
            
            if(!nextScraperType) {
                iziToast.info({ title: 'Info', message: 'Tidak ada data turunan untuk di-scrape dari data sekolah.', position: 'topCenter', timeout: 5000 });
                return;
            }

            // Panggil triggerScraper dengan ID yang dipilih
            triggerScraper(nextScraperType, selectedIds);
        });
    }
    
    const importTerpilihBtn = document.getElementById('importTerpilihBtn');
    if(importTerpilihBtn) {
        importTerpilihBtn.addEventListener('click', function() {
            if (currentDataType !== 'sekolah') {
                iziToast.warning({ title: 'Peringatan', message: 'Import hanya tersedia untuk data sekolah.', position: 'topCenter', timeout: 5000 });
                return;
            }
            
            const selectedIds = getSelectedIds();
            if (selectedIds.length === 0) {
                iziToast.warning({ title: 'Peringatan', message: 'Pilih setidaknya satu sekolah untuk diimport.', position: 'topCenter', timeout: 5000 });
                return;
            }
            
            importData('selected', selectedIds);
        });
    }
    
    const batalBtn = document.getElementById('batalBtn');
    if(batalBtn) {
        batalBtn.addEventListener('click', function() {
            cancelCurrentProcess();
        });
    }

    // Event listener untuk select all checkbox
    const selectAllCheckbox = document.getElementById('selectAll');
    if(selectAllCheckbox) {
        selectAllCheckbox.addEventListener('change', function() {
            document.querySelectorAll('.row-checkbox').forEach(cb => {
                cb.checked = this.checked;
            });
            updateSelectedCount();
            updateButtonStates();
        });
    }

    function renderTableData(dataType, data) {
        const tableBody = document.getElementById('tableBody');
        tableBody.innerHTML = '';
        
        if (data.length === 0) {
            tableBody.innerHTML = `
                <tr>
                    <td colspan="4" class="text-center text-muted p-5"><i class="fas fa-inbox me-2"></i>Tidak ada data ${dataType} yang tersedia</td>
                </tr>
            `;
            return;
        }
        
        data.forEach((item, index) => {
            const nama = item.nama_kabupaten || item.nama_kecamatan || item.nama_sekolah || 'N/A';
            const url = item.url || '#';
            const row = `
                <tr>
                  <td class="text-center align-middle">
                    <div class="form-check d-flex justify-content-center align-items-center h-100">
                      <input type="checkbox" class="form-check-input row-checkbox" value="${item.id}">
                    </div>
                  </td>
                  <td>${index + 1}</td>
                  <td>${escapeHtml(nama)}</td>
                  <td class="d-none d-md-table-cell">
                    <a href="${escapeHtml(url)}" target="_blank" class="text-decoration-none small" title="${escapeHtml(url)}">
                      <i class="fas fa-external-link-alt me-1"></i>
                      ${escapeHtml(url.substring(0, 60)) + (url.length > 60 ? '...' : '')}
                    </a>
                  </td>
                </tr>
              `;
            tableBody.innerHTML += row;
        });
        document.getElementById('selectAll').checked = false;
    }
    
    function triggerScraper(scraperType, selectedIds = []) {
        showLoadingModal(`Memulai Proses Scraping`, `Mengambil data ${scraperType}...`);
        // Tampilkan progress bar
        document.getElementById('progressContainer').style.display = 'block';
        updateImportProgress(0, `Memulai scraping ${scraperType}...`);
        document.getElementById('batalBtn').disabled = false;
        
        fetch('import_handler.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ 
                action: 'trigger_scraper', 
                scraper_type: scraperType, 
                url_induk_id: currentUrlIndukId, 
                selected_ids: selectedIds 
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                monitorProgress(scraperType, `Proses scraping ${scraperType} selesai.`);
            } else {
                hideLoadingModal();
                iziToast.error({title: 'Error', message: data.message || `Gagal memulai scraping ${scraperType}.`, backgroundColor: '#e74a3b', progressBarColor: '#a02622'});
                updateImportProgress(0, 'Error: ' + (data.message || `Scraping ${scraperType} gagal.`));
                document.getElementById('batalBtn').disabled = true;
                setTimeout(() => { document.getElementById('progressContainer').style.display = 'none'; }, 3000);
            }
        })
        .catch(error => {
            hideLoadingModal();
            updateImportProgress(0, 'Error: ' + error.message);
            document.getElementById('batalBtn').disabled = true;
            setTimeout(() => { document.getElementById('progressContainer').style.display = 'none'; }, 3000);
        });
    }
    
    function importData(type, selectedIds = []) {
        showLoadingModal('Import Data Sekolah', 'Memindahkan data sekolah terpilih...');
        document.getElementById('progressContainer').style.display = 'block';
        updateImportProgress(0, 'Memulai import data sekolah...');
        document.getElementById('batalBtn').disabled = false;
        
        fetch('import_handler.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ action: 'import_to_scraping_urls', import_type: type, selected_ids: selectedIds, data_type: currentDataType, url_induk_id: currentUrlIndukId })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                monitorProgress('transfer', 'Proses import selesai. Halaman akan dimuat ulang.');
            } else {
                hideLoadingModal();
                updateImportProgress(0, 'Error: ' + (data.message || 'Import gagal.'));
                document.getElementById('batalBtn').disabled = true;
                setTimeout(() => { document.getElementById('progressContainer').style.display = 'none'; }, 3000);
            }
        })
        .catch(error => {
            hideLoadingModal();
            updateImportProgress(0, 'Error: ' + error.message);
            document.getElementById('batalBtn').disabled = true;
            setTimeout(() => { document.getElementById('progressContainer').style.display = 'none'; }, 3000);
        });
    }
    
    function monitorProgress(processType, successMessage) {
        if (importProgressInterval) clearInterval(importProgressInterval);
        
        importProgressInterval = setInterval(() => {
            fetch('import_handler.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ action: 'get_progress', process_type: processType, url_induk_id: currentUrlIndukId })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const progress = data.progress;
                    updateImportProgress(progress.percentage, progress.status);
                    updateLoadingModal(null, progress.status);
                    
                    if (progress.completed) {
                        clearInterval(importProgressInterval);
                        hideLoadingModal();
                        document.getElementById('batalBtn').disabled = true;
                        
                        setTimeout(() => { document.getElementById('progressContainer').style.display = 'none'; }, 5000);

                        if (progress.success) {
                            iziToast.success({ title: 'Sukses', message: successMessage, position: 'topCenter', backgroundColor: '#1cc88a', progressBarColor: '#0f6848' });
                            if (processType === 'transfer') {
                                setTimeout(() => window.location.reload(), 2000);
                            } else {
                                // Refresh data according to the current view
                                const currentFilter = document.getElementById('filterData').value;
                                if (currentFilter) {
                                   loadTableData(currentFilter);
                                }
                            }
                        } else {
                           iziToast.error({ title: 'Error', message: `Proses ${processType} gagal: ${progress.error}`, position: 'topCenter', backgroundColor: '#e74a3b', progressBarColor: '#a02622' });
                        }
                    }
                } else {
                    clearInterval(importProgressInterval);
                    hideLoadingModal();
                    setTimeout(() => { document.getElementById('progressContainer').style.display = 'none'; }, 3000);
                    iziToast.error({title:'Error', message: 'Gagal memonitor progress.', backgroundColor: '#e74a3b', progressBarColor: '#a02622'});
                }
            })
            .catch(error => {
                console.error('Error monitoring progress:', error);
                clearInterval(importProgressInterval);
                hideLoadingModal();
                setTimeout(() => { document.getElementById('progressContainer').style.display = 'none'; }, 3000);
            });
        }, 2000);
    }
    
    function updateImportProgress(percentage, statusText) {
        const progressBar = document.getElementById('progressBar');
        const progressText = document.getElementById('progressText');
        
        percentage = Math.round(percentage);
        if(progressBar) {
            progressBar.style.width = percentage + '%';
            progressBar.setAttribute('aria-valuenow', percentage);
            progressBar.classList.remove('bg-info', 'bg-success', 'bg-danger');
            if (statusText && statusText.toLowerCase().includes('error')) {
                progressBar.classList.add('bg-danger');
            } else if (percentage >= 100) {
                progressBar.classList.add('bg-success');
            } else {
                progressBar.classList.add('bg-info');
            }
        }
        if(progressText) progressText.textContent = percentage + '%';
    }
    
    function cancelCurrentProcess() {
        fetch('import_handler.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ action: 'cancel_process', url_induk_id: currentUrlIndukId })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                clearInterval(importProgressInterval);
                hideLoadingModal();
                updateImportProgress(0, 'Dibatalkan oleh pengguna');
                document.getElementById('batalBtn').disabled = true;
                document.getElementById('progressContainer').style.display = 'none';
                iziToast.info({ title: 'Info', message: 'Proses berhasil dibatalkan.', position: 'topCenter', backgroundColor: '#1cc88a', progressBarColor: '#0f6848' });
            } else {
                iziToast.error({ title: 'Error', message: 'Gagal membatalkan proses: ' + (data.message || 'Unknown error'), position: 'topCenter', backgroundColor: '#e74a3b', progressBarColor: '#a02622' });
            }
        })
        .catch(error => {
            iziToast.error({ title: 'Error', message: 'Terjadi kesalahan: ' + error.message, position: 'topCenter', backgroundColor: '#e74a3b', progressBarColor: '#a02622' });
        });
    }

    function updateButtonStates() {
        const hasData = document.querySelectorAll('#tableBody .row-checkbox').length > 0;
        const hasSelection = document.querySelectorAll('.row-checkbox:checked').length > 0;
        const isSekolahSelected = currentDataType === 'sekolah';
        
        document.getElementById('scrapeBtn').disabled = !(hasData && hasSelection && !isSekolahSelected);
        document.getElementById('importTerpilihBtn').disabled = !(isSekolahSelected && hasSelection);
    }
    
    function updateDataCount() {
        document.getElementById('dataCount').textContent = document.querySelectorAll('#tableBody .row-checkbox').length;
    }
    
    function updateSelectedCount() {
        document.getElementById('selectedCount').textContent = document.querySelectorAll('.row-checkbox:checked').length;
    }
    
    function updateSelectAllState() {
        const totalCheckboxes = document.querySelectorAll('.row-checkbox').length;
        const checkedCheckboxes = document.querySelectorAll('.row-checkbox:checked').length;
        const selectAllCheckbox = document.getElementById('selectAll');
        
        if (totalCheckboxes === 0) {
            selectAllCheckbox.indeterminate = false;
            selectAllCheckbox.checked = false;
        } else if (checkedCheckboxes === 0) {
            selectAllCheckbox.indeterminate = false;
            selectAllCheckbox.checked = false;
        } else if (checkedCheckboxes === totalCheckboxes) {
            selectAllCheckbox.indeterminate = false;
            selectAllCheckbox.checked = true;
        } else {
            selectAllCheckbox.indeterminate = true;
        }
    }
    
    function getSelectedIds() {
        return Array.from(document.querySelectorAll('.row-checkbox:checked')).map(cb => cb.value);
    }
    
    function showLoadingModal(title = 'Memproses...', detail = 'Mohon tunggu sebentar') {
        document.getElementById('loadingText').textContent = title;
        document.getElementById('loadingDetail').textContent = detail;
        loadingModalInstance.show();
    }
    
    function hideLoadingModal() {
        loadingModalInstance.hide();
    }
    
    function updateLoadingModal(title, detail) {
        if (title) document.getElementById('loadingText').textContent = title;
        if (detail) document.getElementById('loadingDetail').textContent = detail;
    }
    
    function escapeHtml(text) {
        if(typeof text !== 'string') return '';
        const map = {'&': '&amp;','<': '&lt;','>': '&gt;','"': '&quot;',"'": '&#039;'};
        return text.replace(/[&<>"']/g, function(m) { return map[m]; });
    }
    
    // Initialize
    updateButtonStates();
    updateDataCount();
    updateSelectedCount();
});
</script>

<?php
require_once '../layout/_bottom.php';
?>