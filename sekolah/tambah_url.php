<?php
require_once '../layout/_top.php';
require_once '../helper/connection.php';

// Ambil URL yang sudah ada untuk ditampilkan di sisi kanan halaman
 $existing_urls = mysqli_query($connection, "SELECT * FROM scraping_urls ORDER BY created_at DESC");

// Cek apakah ada URL induk yang tersimpan
 $saved_url_induk = mysqli_query($connection, "SELECT * FROM url_induk_scrape WHERE status = 'active' ORDER BY created_at DESC LIMIT 1");
 $url_induk_data = mysqli_fetch_assoc($saved_url_induk);
 $url_induk_value = $url_induk_data ? htmlspecialchars($url_induk_data['url']) : ''; // Tambahkan ini

// Query untuk filter kabupaten
 $kabupaten_query = mysqli_query($connection, "SELECT id, nama_kabupaten FROM kabupaten_scrape WHERE status = 'active' ORDER BY nama_kabupaten ASC");

// Query untuk filter kecamatan
 $kecamatan_query = mysqli_query($connection, "SELECT id, nama_kecamatan, kabupaten_scrape_id FROM kecamatan_scrape WHERE status = 'active' ORDER BY nama_kecamatan ASC");
?>

<section class="section">
    <div class="section-header d-flex flex-wrap justify-content-between align-items-center">
        <h1 class="mb-2 mb-md-0"><i class="fas fa-upload"></i> Kelola URL Scraping</h1>
    </div>
    <div class="d-flex justify-content-end mb-3">
        <a href="index.php" class="btn btn-light"> <i class="fas fa-arrow-left"></i> <span class="d-none d-sm-inline">Kembali</span>
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
                            <label for="urlInduk" class="form-label font-weight-bold">1. Set URL Induk Provinsi</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-link"></i></span>
                                <input type="url" class="form-control" id="urlInduk" 
                                       placeholder="Contoh: https://dapo.kemdikdasmen.go.id/sp/1/150000"
                                       value="<?= $url_induk_value ?>"> <button class="btn btn-primary" id="simpanUrlBtn">
                                    <i class="fas fa-save me-1"></i> <span class="d-none d-sm-inline">Simpan & Mulai</span>
                                </button>
                            </div>
                            <small class="text-muted">Masukkan URL halaman provinsi di Dapodik.</small>
                        </div>
                        <div class="col-12 col-lg-4">
                            <label for="filterData" class="form-label font-weight-bold">2. Tampilkan Data</label>
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
                                    <option value="">Pilih Kabupaten...</option>
                                    <option value="semua">-- Semua Kabupaten --</option>
                                    <?php 
                                    mysqli_data_seek($kabupaten_query, 0); // Reset pointer query
                                    while ($kab = mysqli_fetch_array($kabupaten_query)) : 
                                    ?>
                                        <option value="<?= $kab['id'] ?>"><?= htmlspecialchars($kab['nama_kabupaten']) ?></option>
                                    <?php endwhile; ?>
                                </select>
                            </div>
                            
                            <div class="col-12 col-md-6" id="kecamatanFilterContainer" style="display: none;">
                                <label for="kecamatanFilter" class="form-label font-weight-bold">Filter Kecamatan</label>
                                <select class="form-select" id="kecamatanFilter">
                                    <option value="">Pilih Kecamatan...</option>
                                    <option value="semua">-- Semua Kecamatan --</option>
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
                        <small id="progressStatusText" class="text-muted d-block mt-1">Menunggu...</small> </div>
                    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2">
                        <div class="btn-group flex-wrap">
                            <button class="btn btn-info btn-sm btn-md-normal" id="scrapeBtn" disabled>
                                <i class="fas fa-search-plus me-1"></i> 
                                <span class="d-none d-sm-inline">Ambil Data Turunan</span> <span class="d-sm-none">Scrape</span>
                            </button>
                            <button class="btn btn-success btn-sm btn-md-normal" id="importTerpilihBtn" disabled>
                                <i class="fas fa-file-import me-1"></i> 
                                <span class="d-none d-sm-inline">Import Sekolah Terpilih</span> <span class="d-sm-none">Import</span>
                            </button>
                            <button class="btn btn-danger btn-sm btn-md-normal" id="batalBtn" disabled>
                                <i class="fas fa-times-circle me-1"></i> 
                                <span class="d-none d-sm-inline">Batalkan Proses</span>
                                <span class="d-sm-none">Batal</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-xl-4">
            <div class="card card-warning shadow-sm mb-4">
                <div class="card-header">
                    <h4 class="mb-0"><i class="fas fa-pencil-alt"></i> Tambah URL Sekolah Manual</h4>
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
                    <h4 class="mb-0"><i class="fas fa-history"></i> URL Sekolah Tersimpan</h4>
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
                                        <button class="btn btn-sm btn-outline-danger delete-url" data-id="<?= $url['id'] ?>" title="Hapus URL">
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
    .section-header h1 { font-size: 1.25rem; }
    .card-header h4 { font-size: 1rem; }
    .btn-group { flex-direction: column; width: 100%; }
    .btn-group .btn { border-radius: 0.375rem !important; margin-bottom: 0.25rem; }
    .btn-group .btn:last-child { margin-bottom: 0; }
    .table-responsive { font-size: 0.875rem; }
}
@media (max-width: 576px) {
    .input-group .btn { padding: 0.375rem 0.75rem; font-size: 0.875rem; }
    .progress { height: 1.5rem !important; }
    .list-group-item h6 { font-size: 0.875rem; }
}
@media (min-width: 768px) {
    .btn-md-normal { padding: 0.5rem 1rem; font-size: 0.875rem; }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Variabel Global
    let urlCounter = 1;
    const maxUrls = 30;
    let currentUrlIndukId = <?= $url_induk_data ? $url_induk_data['id'] : 'null' ?>;
    let currentDataType = '';
    let selectedKabupatenId = '';
    let selectedKecamatanId = '';
    let importProgressInterval = null;
    let loadingModalInstance = new bootstrap.Modal(document.getElementById('loadingModal'));
    let currentLogId = null; // <- Simpan log_id di sini

    const allKecamatanOptions = Array.from(document.querySelectorAll('#kecamatanFilter option')).filter(opt => opt.value !== '' && opt.value !== 'semua');

    // --- EVENT LISTENERS (ADD URL, REMOVE URL, SUBMIT URL, DELETE URL) ---
    // (Kode event listener ini tidak berubah dari file asli Anda)
    // Event listener untuk add URL manual
    const addUrlBtn = document.getElementById('addUrlBtn');
    if(addUrlBtn) {
        addUrlBtn.addEventListener('click', function() {
            if (urlCounter >= maxUrls) {
                iziToast.warning({ title: 'Peringatan', message: `Maksimal ${maxUrls} URL per upload.`, position: 'topCenter'});
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
                    <button type="button" class="btn btn-danger btn-sm remove-url"><i class="fas fa-trash"></i></button>
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
            for (let i = 1; i <= urlCounter; i++) {
                const url = formData.get(`urls[${i}][url]`);
                const description = formData.get(`urls[${i}][description]`);
                if (url && url.trim()) {
                    urls.push({ url: url.trim(), description: description ? description.trim() : null });
                }
            }
            if (urls.length === 0) {
                iziToast.warning({ title: 'Peringatan', message: 'Masukkan setidaknya satu URL.', position: 'topCenter'});
                return;
            }
            document.getElementById('uploadProgress').style.display = 'block';
            document.getElementById('submitBtn').disabled = true;
            uploadUrls(urls, 0); // Fungsi uploadUrls perlu didefinisikan
        });
    }

     // Event listener untuk delete URL tersimpan
    document.addEventListener('click', function(e) { 
        if (e.target && e.target.closest('.delete-url')) {
            const urlId = e.target.closest('.delete-url').getAttribute('data-id');
            Swal.fire({
                title: 'Apakah Anda yakin?', text: "URL ini akan dihapus permanen!", icon: 'warning',
                showCancelButton: true, confirmButtonColor: '#3085d6', cancelButtonColor: '#d33',
                confirmButtonText: 'Ya, hapus!', cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    fetch('delete_url.php', { // Pastikan ada file delete_url.php
                        method: 'POST', headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify({ id: urlId })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            Swal.fire('Berhasil!', data.message || 'URL berhasil dihapus.', 'success');
                            setTimeout(() => window.location.reload(), 1500);
                        } else {
                            Swal.fire('Gagal!', data.message || 'Gagal menghapus URL.', 'error');
                        }
                    })
                    .catch(() => Swal.fire('Error!', 'Terjadi kesalahan server.', 'error'));
                }
            });
        }
    });

    // --- EVENT LISTENER UTAMA ---

    // Event listener untuk simpan URL induk
    const simpanUrlBtn = document.getElementById('simpanUrlBtn');
    if(simpanUrlBtn) {
        simpanUrlBtn.addEventListener('click', function() {
            const urlIndukInput = document.getElementById('urlInduk');
            const urlInduk = urlIndukInput.value.trim();
            
            if (!urlInduk) {
                iziToast.warning({ title: 'Peringatan', message: 'Masukkan URL induk terlebih dahulu.', position: 'topCenter'});
                return;
            }
            try { new URL(urlInduk); } catch (e) {
                iziToast.error({ title: 'Error', message: 'URL tidak valid.', position: 'topCenter'});
                return;
            }
            
            this.disabled = true;
            this.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> Menyimpan...';
            
            fetch('import_handler.php', {
                method: 'POST', headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ action: 'save_url_induk', url: urlInduk })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    currentUrlIndukId = data.url_induk_id;
                    iziToast.success({ title: 'Sukses', message: 'URL induk disimpan. Memulai ambil data kabupaten...', position: 'topCenter'});
                    urlIndukInput.value = urlInduk; // Pastikan input terupdate
                    
                    // Otomatis set filter ke kabupaten & scrape
                    document.getElementById('filterData').value = 'kabupaten';
                    currentDataType = 'kabupaten';
                    triggerScraper('kabupaten'); // Langsung scrape kabupaten
                } else {
                    iziToast.error({ title: 'Error', message: data.message || 'Gagal menyimpan URL induk.', position: 'topCenter'});
                }
            })
            .catch(error => iziToast.error({ title: 'Error', message: 'Terjadi kesalahan: ' + error.message, position: 'topCenter'}))
            .finally(() => {
                this.disabled = false;
                this.innerHTML = '<i class="fas fa-save me-1"></i> <span class="d-none d-sm-inline">Simpan & Mulai</span>';
            });
        });
    }

    // Event listener untuk filter jenis data
    const filterDataSelect = document.getElementById('filterData');
    if(filterDataSelect) {
        filterDataSelect.addEventListener('change', function() {
            currentDataType = this.value;
            selectedKabupatenId = ''; // Reset filter bawah
            selectedKecamatanId = '';
            document.getElementById('kabupatenFilter').value = '';
            document.getElementById('kecamatanFilter').value = '';

            const filtersContainer = document.getElementById('filtersContainer');
            const kabFilterContainer = document.getElementById('kabupatenFilterContainer');
            const kecFilterContainer = document.getElementById('kecamatanFilterContainer');
            const tableBody = document.getElementById('tableBody');

            // Sembunyikan semua filter dulu
            filtersContainer.style.display = 'none';
            kabFilterContainer.style.display = 'none';
            kecFilterContainer.style.display = 'none';
            tableBody.innerHTML = ''; // Kosongkan tabel

            if (currentDataType === 'kabupaten') {
                loadTableData('kabupaten');
            } else if (currentDataType === 'kecamatan') {
                filtersContainer.style.display = 'block';
                kabFilterContainer.style.display = 'block';
                tableBody.innerHTML = `<tr><td colspan="4" class="text-center text-muted p-5"><i class="fas fa-filter fa-2x mb-2 d-block"></i>Pilih Kabupaten.</td></tr>`;
            } else if (currentDataType === 'sekolah') {
                filtersContainer.style.display = 'block';
                kabFilterContainer.style.display = 'block'; // Tampilkan juga filter kab
                kecFilterContainer.style.display = 'block'; // Tampilkan filter kec
                filterKecamatanDropdown(''); // Kosongkan kec dulu
                tableBody.innerHTML = `<tr><td colspan="4" class="text-center text-muted p-5"><i class="fas fa-filter fa-2x mb-2 d-block"></i>Pilih Kabupaten lalu Kecamatan.</td></tr>`;
            } else {
                 tableBody.innerHTML = `<tr><td colspan="4" class="text-center text-muted p-5"><i class="fas fa-info-circle fa-2x mb-2 d-block"></i>Pilih Jenis Data.</td></tr>`;
            }
            
            updateDataCount();
            updateSelectedCount();
            updateButtonStates();
            document.getElementById('progressContainer').style.display = 'none'; // Sembunyikan progress
            document.getElementById('selectAll').checked = false; // Uncheck select all
        });
    }
    
    // Event listener untuk filter kabupaten
    const kabupatenFilterSelect = document.getElementById('kabupatenFilter');
    if(kabupatenFilterSelect) {
        kabupatenFilterSelect.addEventListener('change', function() {
            selectedKabupatenId = this.value;
            selectedKecamatanId = ''; // Reset kecamatan
            document.getElementById('kecamatanFilter').value = '';
            filterKecamatanDropdown(selectedKabupatenId); // Filter opsi kecamatan

            if (currentDataType === 'kecamatan') {
                if (selectedKabupatenId) { loadTableData('kecamatan'); } 
                else { 
                    document.getElementById('tableBody').innerHTML = `<tr><td colspan="4" class="text-center text-muted p-5">Pilih Kabupaten.</td></tr>`; 
                    updateDataCount(); updateButtonStates(); 
                }
            } else if (currentDataType === 'sekolah') {
                 // Untuk sekolah, jangan load data dulu, tunggu kecamatan dipilih
                 document.getElementById('tableBody').innerHTML = `<tr><td colspan="4" class="text-center text-muted p-5">Pilih Kecamatan.</td></tr>`;
                 updateDataCount(); updateButtonStates();
            }
            document.getElementById('selectAll').checked = false;
        });
    }
    
    // Event listener untuk filter kecamatan
    const kecamatanFilterSelect = document.getElementById('kecamatanFilter');
    if(kecamatanFilterSelect) {
        kecamatanFilterSelect.addEventListener('change', function() {
            selectedKecamatanId = this.value;
            if (currentDataType === 'sekolah') {
                if (selectedKecamatanId) { loadTableData('sekolah'); } 
                else { 
                    document.getElementById('tableBody').innerHTML = `<tr><td colspan="4" class="text-center text-muted p-5">Pilih Kecamatan.</td></tr>`; 
                    updateDataCount(); updateButtonStates(); 
                }
            }
             document.getElementById('selectAll').checked = false;
        });
    }

    // Event listener untuk checkbox di body tabel
    document.getElementById('tableBody').addEventListener('change', function(e) {
        if (e.target && e.target.classList.contains('row-checkbox')) {
            updateSelectedCount();
            updateSelectAllState();
            updateButtonStates();
        }
    });

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
    
    // Event listener tombol Scrape
    const scrapeBtn = document.getElementById('scrapeBtn');
    if(scrapeBtn) {
        scrapeBtn.addEventListener('click', function() {
            if (!currentDataType) {
                iziToast.warning({ title: 'Peringatan', message: 'Pilih tipe data dulu.', position: 'topCenter'});
                return;
            }
            const selectedIds = getSelectedIds();
            if (selectedIds.length === 0) {
                iziToast.warning({ title: 'Peringatan', message: 'Pilih minimal satu item.', position: 'topCenter'});
                return;
            }
            
            let nextScraperType = '';
            if(currentDataType === 'kabupaten') nextScraperType = 'kecamatan';
            else if(currentDataType === 'kecamatan') nextScraperType = 'sekolah';
            
            if(!nextScraperType) {
                iziToast.info({ title: 'Info', message: 'Tidak ada data turunan dari sekolah.', position: 'topCenter'});
                return;
            }

            // Panggil fungsi trigger yang sesuai
            triggerScraper(nextScraperType, selectedIds);
        });
    }
    
    // Event listener tombol Import
    const importTerpilihBtn = document.getElementById('importTerpilihBtn');
    if(importTerpilihBtn) {
        importTerpilihBtn.addEventListener('click', function() {
            if (currentDataType !== 'sekolah') {
                iziToast.warning({ title: 'Peringatan', message: 'Import hanya untuk data sekolah.', position: 'topCenter'});
                return;
            }
            const selectedIds = getSelectedIds();
            if (selectedIds.length === 0) {
                iziToast.warning({ title: 'Peringatan', message: 'Pilih minimal satu sekolah.', position: 'topCenter'});
                return;
            }
            importData('selected', selectedIds);
        });
    }
    
    // Event listener tombol Batal
    const batalBtn = document.getElementById('batalBtn');
    if(batalBtn) {
        batalBtn.addEventListener('click', function() {
            cancelCurrentProcess();
        });
    }

    // --- FUNGSI-FUNGSI UTILITY ---

    function loadTableData(dataType) {
        if (!currentUrlIndukId && dataType !== 'manual') { // Kecuali manual
            document.getElementById('tableBody').innerHTML = `<tr><td colspan="4" class="text-center text-warning p-5">Simpan URL Induk dulu.</td></tr>`;
            updateDataCount(); updateButtonStates();
            return;
        }
        
        document.getElementById('tableBody').innerHTML = `<tr><td colspan="4" class="text-center p-5"><div class="spinner-border text-primary"></div><p class="mt-2 mb-0">Memuat data ${dataType}...</p></td></tr>`;
        
        const requestData = { action: 'check_data', data_type: dataType, url_induk_id: currentUrlIndukId };
        
        if (dataType === 'kecamatan' && selectedKabupatenId && selectedKabupatenId !== 'semua') { requestData.kabupaten_id = selectedKabupatenId; }
        if (dataType === 'sekolah') {
             if (selectedKabupatenId && selectedKabupatenId !== 'semua') { requestData.kabupaten_id = selectedKabupatenId; } // Kirim juga kab_id
             if (selectedKecamatanId && selectedKecamatanId !== 'semua') { requestData.kecamatan_id = selectedKecamatanId; }
        }
        
        fetch('import_handler.php', {
            method: 'POST', headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(requestData)
        })
        .then(response => response.json())
        .then(data => {
            if (data.success && data.has_data) {
                renderTableData(dataType, data.data);
            } else {
                 document.getElementById('tableBody').innerHTML = `<tr><td colspan="4" class="text-center text-muted p-5"><i class="fas fa-inbox fa-2x mb-2 d-block"></i>Data ${dataType} tidak ditemukan.</td></tr>`;
            }
        })
        .catch(error => {
            document.getElementById('tableBody').innerHTML = `<tr><td colspan="4" class="text-center text-danger p-5"><i class="fas fa-exclamation-circle me-2"></i>Error: ${error.message}</td></tr>`;
        })
        .finally(() => {
            updateDataCount(); updateSelectedCount(); updateButtonStates();
             document.getElementById('selectAll').checked = false; // Uncheck select all
        });
    }

    function renderTableData(dataType, data) {
        const tableBody = document.getElementById('tableBody');
        tableBody.innerHTML = ''; // Kosongkan dulu
        if (data.length === 0) {
            tableBody.innerHTML = `<tr><td colspan="4" class="text-center text-muted p-5">Tidak ada data.</td></tr>`;
            return;
        }
        data.forEach((item, index) => {
            const nama = item.nama_kabupaten || item.nama_kecamatan || item.nama_sekolah || 'N/A';
            const url = item.url || '#';
            const row = `
                <tr>
                  <td class="text-center align-middle"><div class="form-check d-flex justify-content-center align-items-center h-100"><input type="checkbox" class="form-check-input row-checkbox" value="${item.id}"></div></td>
                  <td>${index + 1}</td>
                  <td>${escapeHtml(nama)}</td>
                  <td class="d-none d-md-table-cell"><a href="${escapeHtml(url)}" target="_blank" class="text-decoration-none small" title="${escapeHtml(url)}"><i class="fas fa-external-link-alt me-1"></i>${escapeHtml(url.substring(0, 60)) + (url.length > 60 ? '...' : '')}</a></td>
                </tr>`;
            tableBody.innerHTML += row;
        });
    }

    function triggerScraper(scraperType, selectedIds = []) {
        if (!currentUrlIndukId && scraperType !== 'kecamatan' && scraperType !== 'sekolah') {
             iziToast.error({ title: 'Error', message: 'URL Induk belum disimpan.', position: 'topCenter'});
             return;
        }
        
        showLoadingModal(`Mengambil data ${scraperType}`, `Proses mungkin perlu waktu...`);
        document.getElementById('progressContainer').style.display = 'block';
        updateImportProgress(0, `Memulai ${scraperType}...`, `Memulai ${scraperType}...`);
        document.getElementById('batalBtn').disabled = false;
        
        // Siapkan payload
        let payload = { 
            action: 'trigger_scraper', 
            scraper_type: scraperType, 
            user_id: 1 // Ganti jika perlu
        };
        
        // Tambahkan ID yang relevan
        if (scraperType === 'kabupaten' || scraperType === 'transfer') {
             payload.url_induk_id = currentUrlIndukId;
        } else { // kecamatan atau sekolah
             payload.selected_ids = selectedIds;
             // url_induk_id tidak dikirim langsung, PHP akan mencarinya
        }
        
        fetch('import_handler.php', {
            method: 'POST', headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(payload)
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                currentLogId = data.log_id; // <- Simpan log_id dari respons
                monitorProgress(scraperType, `Proses ${scraperType} selesai.`);
            } else {
                hideLoadingModal();
                iziToast.error({title: 'Error', message: data.message || `Gagal memulai ${scraperType}.`});
                updateImportProgress(0, 'Gagal', 'Error: ' + (data.message || 'Gagal memulai.'));
                document.getElementById('batalBtn').disabled = true;
                setTimeout(() => { document.getElementById('progressContainer').style.display = 'none'; }, 3000);
            }
        })
        .catch(error => {
            hideLoadingModal();
            updateImportProgress(0, 'Gagal', 'Error: ' + error.message);
            document.getElementById('batalBtn').disabled = true;
            setTimeout(() => { document.getElementById('progressContainer').style.display = 'none'; }, 3000);
        });
    }

    function importData(type, selectedIds = []) {
         if (!currentUrlIndukId) {
             iziToast.error({ title: 'Error', message: 'URL Induk belum disimpan.', position: 'topCenter'});
             return;
         }
        showLoadingModal('Import Data Sekolah', 'Proses ini mungkin perlu waktu.');
        document.getElementById('progressContainer').style.display = 'block';
        updateImportProgress(0, 'Memulai import...', 'Memulai import...');
        document.getElementById('batalBtn').disabled = false; // Aktifkan batal
        
        // Untuk import, kita perlu url_induk_id
        let payload = { 
            action: 'import_to_scraping_urls', 
            import_type: type, 
            selected_ids: selectedIds, 
            data_type: currentDataType, 
            url_induk_id: currentUrlIndukId,
            user_id: 1
        };
        
        fetch('import_handler.php', {
            method: 'POST', headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(payload)
        })
        .then(response => response.json())
        .then(data => {
            hideLoadingModal(); // Sembunyikan modal setelah selesai
            document.getElementById('batalBtn').disabled = true; // Nonaktifkan batal
            if (data.success) {
                updateImportProgress(100, 'Selesai', `Import berhasil: ${data.inserted} baru, ${data.updated} update.`);
                iziToast.success({ title: 'Sukses', message: data.message || 'Import berhasil.', position: 'topCenter'});
                 setTimeout(() => window.location.reload(), 2000); // Reload halaman
            } else {
                 updateImportProgress(0, 'Gagal', 'Error: ' + (data.message || 'Import gagal.'));
                 iziToast.error({ title: 'Error', message: data.message || 'Import gagal.', position: 'topCenter'});
            }
             setTimeout(() => { document.getElementById('progressContainer').style.display = 'none'; }, 5000);
        })
        .catch(error => {
            hideLoadingModal();
            updateImportProgress(0, 'Gagal', 'Error: ' + error.message);
            document.getElementById('batalBtn').disabled = true;
            setTimeout(() => { document.getElementById('progressContainer').style.display = 'none'; }, 3000);
            iziToast.error({ title: 'Error', message: 'Terjadi kesalahan jaringan.', position: 'topCenter'});
        });
    }
    
    // --- FUNGSI monitorProgress (YANG SUDAH DIPERBAIKI) ---
    function monitorProgress(processType, successMessage) {
        if (importProgressInterval) clearInterval(importProgressInterval);
        
        // Pastikan kita punya log_id jika dibutuhkan
        if ((processType === 'kecamatan' || processType === 'sekolah') && !currentLogId) {
            console.error("Error: log_id tidak ditemukan untuk memonitor proses", processType);
            iziToast.error({ title: 'Error Internal', message: 'Log ID tidak ditemukan.', position: 'topCenter' });
            hideLoadingModal();
            document.getElementById('batalBtn').disabled = true;
            setTimeout(() => { document.getElementById('progressContainer').style.display = 'none'; }, 3000);
            return; 
        }

        importProgressInterval = setInterval(() => {
            // --- PERBAIKAN DI SINI ---
            let payload = {
                action: 'get_progress', 
                process_type: processType
            };
            
            // Kirim log_id untuk kecamatan/sekolah, url_induk_id untuk lainnya
            if (processType === 'kecamatan' || processType === 'sekolah') {
                payload.log_id = currentLogId; 
            } else { // kabupaten atau transfer
                // Pastikan currentUrlIndukId ada
                 if (!currentUrlIndukId) {
                     console.error("Error: currentUrlIndukId tidak ada untuk proses", processType);
                     clearInterval(importProgressInterval);
                     // Mungkin tambahkan notifikasi error di sini
                     return;
                 }
                payload.url_induk_id = currentUrlIndukId;
            }
            // --- AKHIR PERBAIKAN ---

            fetch('import_handler.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(payload) // Gunakan payload yang sudah disiapkan
            })
            .then(response => response.json())
            .then(data => {
                if (data.success && data.progress) { // Pastikan data.progress ada
                    const progress = data.progress;
                    updateImportProgress(progress.percentage, progress.status, progress.status); // Update bar & teks status
                    updateLoadingModal(null, progress.status); // Update detail modal
                    
                    if (progress.completed) {
                        clearInterval(importProgressInterval);
                        hideLoadingModal();
                        document.getElementById('batalBtn').disabled = true;
                        
                        // Pastikan progress 100% saat selesai
                        updateImportProgress(100, progress.status, progress.status); 
                        
                        setTimeout(() => { 
                            document.getElementById('progressContainer').style.display = 'none'; 
                            // Reset progress bar untuk eksekusi berikutnya
                             updateImportProgress(0, "Menunggu...", "Menunggu..."); 
                        }, 5000); // Sembunyikan setelah 5 detik

                        if (progress.success) {
                            iziToast.success({ title: 'Sukses', message: successMessage, position: 'topCenter'});
                            // Muat ulang data tabel setelah scraping KECUALI transfer
                            if (processType !== 'transfer') {
                                 const currentFilter = document.getElementById('filterData').value;
                                 if (currentFilter) {
                                    setTimeout(() => loadTableData(currentFilter), 1000); 
                                 }
                            } else {
                                // Reload halaman setelah transfer (import sekolah)
                                setTimeout(() => window.location.reload(), 2000);
                            }
                        } else {
                           const errorMessage = progress.error || `Proses ${processType} gagal.`;
                           iziToast.error({ title: 'Gagal', message: errorMessage, position: 'topCenter', timeout: 7000 });
                        }
                         currentLogId = null; // Reset currentLogId setelah proses selesai
                    }
                } else {
                    clearInterval(importProgressInterval);
                    hideLoadingModal();
                    document.getElementById('batalBtn').disabled = true;
                    setTimeout(() => { document.getElementById('progressContainer').style.display = 'none'; }, 3000);
                    const errorMsg = data.message || 'Gagal mendapatkan status progress.';
                    iziToast.error({title:'Error Polling', message: errorMsg});
                    currentLogId = null; 
                }
            })
            .catch(error => {
                console.error('Error monitoring progress:', error);
                clearInterval(importProgressInterval);
                hideLoadingModal();
                document.getElementById('batalBtn').disabled = true;
                setTimeout(() => { document.getElementById('progressContainer').style.display = 'none'; }, 3000);
                iziToast.error({ title: 'Error Jaringan', message: 'Gagal menghubungi server.', position: 'topCenter' });
                currentLogId = null; 
            });
        }, 2000); 
    }
    
    // Fungsi update progress (bar dan teks status)
    function updateImportProgress(percentage, barText, statusText) {
        const progressBar = document.getElementById('progressBar');
        const progressTextSpan = document.getElementById('progressText'); // Span di dalam bar
        const progressStatusText = document.getElementById('progressStatusText'); // Teks di bawah bar
        
        percentage = Math.max(0, Math.min(100, Math.round(percentage))); // Pastikan 0-100

        if(progressBar) {
            progressBar.style.width = percentage + '%';
            progressBar.setAttribute('aria-valuenow', percentage);
            // Ganti warna berdasarkan status
            progressBar.classList.remove('bg-info', 'bg-success', 'bg-danger');
            if (statusText && statusText.toLowerCase().includes('gagal') || statusText.toLowerCase().includes('error')) {
                progressBar.classList.add('bg-danger');
            } else if (percentage >= 100) {
                progressBar.classList.add('bg-success');
            } else {
                progressBar.classList.add('bg-info');
            }
        }
        if(progressTextSpan) {
             progressTextSpan.textContent = barText || (percentage + '%'); // Tampilkan teks status di bar jika ada
        }
         if(progressStatusText) {
             progressStatusText.textContent = statusText || 'Memproses...'; // Tampilkan teks status di bawah bar
         }
    }
    
    function cancelCurrentProcess() {
        // Ambil ID yang relevan (log_id atau url_induk_id)
        let cancelPayload = { action: 'cancel_process' };
        if (currentLogId && (currentDataType === 'kecamatan' || currentDataType === 'sekolah')) {
            cancelPayload.log_id = currentLogId; 
        } else if (currentUrlIndukId) {
             cancelPayload.url_induk_id = currentUrlIndukId;
        } else {
             iziToast.warning({ title: 'Info', message: 'Tidak ada proses aktif untuk dibatalkan.', position: 'topCenter'});
             return;
        }

        fetch('import_handler.php', {
            method: 'POST', headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(cancelPayload)
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                clearInterval(importProgressInterval);
                hideLoadingModal();
                updateImportProgress(0, 'Dibatalkan', 'Dibatalkan oleh pengguna');
                document.getElementById('batalBtn').disabled = true;
                setTimeout(() => { document.getElementById('progressContainer').style.display = 'none'; }, 3000);
                iziToast.info({ title: 'Info', message: 'Proses berhasil dibatalkan.', position: 'topCenter'});
                currentLogId = null; // Reset log id
            } else {
                iziToast.error({ title: 'Error', message: 'Gagal membatalkan proses: ' + (data.message || 'Error tidak diketahui')});
            }
        })
        .catch(error => iziToast.error({ title: 'Error', message: 'Kesalahan jaringan: ' + error.message}));
    }

    // --- Fungsi Update UI Lainnya (tidak berubah) ---
    function updateButtonStates() {
        const hasData = document.querySelectorAll('#tableBody .row-checkbox').length > 0;
        const hasSelection = document.querySelectorAll('.row-checkbox:checked').length > 0;
        const isSekolahSelected = currentDataType === 'sekolah';
        
        // Tombol Scrape aktif jika: ada data, ada yg dipilih, DAN BUKAN data sekolah
        document.getElementById('scrapeBtn').disabled = !(hasData && hasSelection && !isSekolahSelected);
        
        // Tombol Import aktif jika: tipe data sekolah DAN ada yg dipilih
        document.getElementById('importTerpilihBtn').disabled = !(isSekolahSelected && hasSelection);
        
        // Tombol Batal diatur oleh monitorProgress
    }
    function updateDataCount() { document.getElementById('dataCount').textContent = document.querySelectorAll('#tableBody .row-checkbox').length; }
    function updateSelectedCount() { document.getElementById('selectedCount').textContent = document.querySelectorAll('.row-checkbox:checked').length; }
    function updateSelectAllState() {
        const total = document.querySelectorAll('.row-checkbox').length;
        const checked = document.querySelectorAll('.row-checkbox:checked').length;
        const selectAll = document.getElementById('selectAll');
        if (total === 0) { selectAll.indeterminate = false; selectAll.checked = false; }
        else if (checked === 0) { selectAll.indeterminate = false; selectAll.checked = false; }
        else if (checked === total) { selectAll.indeterminate = false; selectAll.checked = true; }
        else { selectAll.indeterminate = true; }
    }
    function getSelectedIds() { return Array.from(document.querySelectorAll('.row-checkbox:checked')).map(cb => cb.value); }
    function showLoadingModal(title = 'Memproses...', detail = 'Mohon tunggu') {
        document.getElementById('loadingText').textContent = title;
        document.getElementById('loadingDetail').textContent = detail;
        loadingModalInstance.show();
    }
    function hideLoadingModal() { loadingModalInstance.hide(); }
    function updateLoadingModal(title, detail) {
        if (title) document.getElementById('loadingText').textContent = title;
        if (detail) document.getElementById('loadingDetail').textContent = detail;
    }
    function escapeHtml(text) {
        if(typeof text !== 'string') return '';
        const map = {'&': '&amp;','<': '&lt;','>': '&gt;','"': '&quot;',"'": '&#039;'};
        return text.replace(/[&<>"']/g, function(m) { return map[m]; });
    }
     function filterKecamatanDropdown(selectedKabId) {
        const kecamatanSelect = document.getElementById('kecamatanFilter');
        const currentKecValue = kecamatanSelect.value;
        kecamatanSelect.innerHTML = '<option value="">Pilih Kecamatan...</option><option value="semua">-- Semua Kecamatan --</option>';
        allKecamatanOptions.forEach(option => {
            const kabIdForKec = option.getAttribute('data-kabupaten-id');
            if (selectedKabId === 'semua' || kabIdForKec === selectedKabId || !selectedKabId) {
                kecamatanSelect.appendChild(option.cloneNode(true));
            }
        });
        kecamatanSelect.value = currentKecValue; // Coba set nilai lama
    }
     function updateRemoveButtons() {
        const urlGroups = document.querySelectorAll('.url-input-group');
        urlGroups.forEach(group => {
            const removeBtn = group.querySelector('.remove-url');
            removeBtn.style.display = (urlGroups.length > 1) ? 'block' : 'none';
        });
    }
     function renumberUrlGroups() {
        const urlGroups = document.querySelectorAll('.url-input-group');
        urlCounter = 0;
        urlGroups.forEach((group, index) => {
            urlCounter++; const newIndex = index + 1;
            group.setAttribute('data-index', newIndex);
            group.querySelector('label').textContent = `URL Sekolah #${newIndex}`;
            group.querySelector('input[type="url"]').name = `urls[${newIndex}][url]`;
            group.querySelector('input[type="text"]').name = `urls[${newIndex}][description]`;
        });
    }
     function uploadUrls(urls, index) { // Fungsi rekursif untuk upload manual
        if (index >= urls.length) {
            document.getElementById('uploadProgress').style.display = 'none';
            document.getElementById('submitBtn').disabled = false;
            document.getElementById('urlForm').reset();
            urlCounter = 1; document.getElementById('urlContainer').innerHTML = `<div class="url-input-group mb-3" data-index="1"><label class="form-label small">URL Sekolah #1</label><div class="input-group"><input type="url" name="urls[1][url]" class="form-control form-control-sm" placeholder="URL Lengkap Sekolah" required><button type="button" class="btn btn-danger btn-sm remove-url" style="display: none;"><i class="fas fa-trash"></i></button></div><input type="text" name="urls[1][description]" class="form-control form-control-sm mt-1" placeholder="Nama sekolah (opsional)"></div>`;
            iziToast.success({ title: 'Sukses', message: `${urls.length} URL diupload.`, position: 'topCenter'});
            setTimeout(() => window.location.reload(), 2000);
            return;
        }
        const url = urls[index]; const progress = Math.round(((index + 1) / urls.length) * 100);
        document.getElementById('uploadProgressBar').style.width = progress + '%';
        document.getElementById('uploadStatusText').textContent = `Upload ${index + 1}/${urls.length}: ${url.description || url.url}`;
        
        fetch('import_handler.php', { // Ganti dengan endpoint PHP Anda untuk menambah URL
            method: 'POST', headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ action: 'add_manual_url', url: url.url, description: url.description, user_id: 1 }) // Sesuaikan action & payload
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) { setTimeout(() => uploadUrls(urls, index + 1), 300); } // Jeda sedikit
            else { document.getElementById('uploadProgress').style.display = 'none'; document.getElementById('submitBtn').disabled = false; iziToast.error({ title: 'Error', message: `Gagal upload URL ${index + 1}: ${data.message}`, position: 'topCenter'}); }
        })
        .catch(error => { document.getElementById('uploadProgress').style.display = 'none'; document.getElementById('submitBtn').disabled = false; iziToast.error({ title: 'Error', message: `Error upload URL ${index + 1}: ${error.message}`, position: 'topCenter'}); });
    }
    
    // --- INISIALISASI ---
    updateButtonStates();
    updateDataCount();
    updateSelectedCount();
    // Jika URL Induk sudah ada, otomatis muat data kabupaten saat halaman dibuka
    if (currentUrlIndukId) {
         document.getElementById('filterData').value = 'kabupaten';
         currentDataType = 'kabupaten';
         loadTableData('kabupaten');
    }
});
</script>

<?php
require_once '../layout/_bottom.php';
?>