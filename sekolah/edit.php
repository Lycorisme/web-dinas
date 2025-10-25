<?php
require_once '../layout/_top.php';
require_once '../helper/connection.php';
$npsn = $_GET['npsn'];
$query = mysqli_query($connection, "SELECT * FROM sekolah_identitas WHERE npsn='$npsn'");
$kecamatan = mysqli_query($connection, "SELECT k.*, kab.nama_kabupaten, p.nama_provinsi 
                                       FROM kecamatan k
                                       LEFT JOIN kabupaten_kota kab ON k.id_kabupaten_fk = kab.id_kabupaten
                                       LEFT JOIN provinsi p ON kab.id_provinsi_fk = p.id_provinsi
                                       ORDER BY p.nama_provinsi, kab.nama_kabupaten, k.nama_kecamatan");
// Check if there's an associated URL for this school
$url_query = mysqli_query($connection, "SELECT * FROM scraping_urls WHERE description LIKE '%$npsn%' OR url LIKE '%$npsn%' ORDER BY created_at DESC LIMIT 1");
$existing_url = mysqli_num_rows($url_query) > 0 ? mysqli_fetch_array($url_query) : null;
?>
<section class="section">
    <div class="section-header d-flex justify-content-between align-items-center">
        <h1>Ubah Data Sekolah</h1>
    </div>
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <!-- // Form -->
                    <form action="./update.php" method="post" id="editForm">
                        <?php
                        while ($row = mysqli_fetch_array($query)) {
                        ?>
                            <input type="hidden" name="npsn" value="<?= $row['npsn'] ?>">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="npsn_display">NPSN</label>
                                        <input class="form-control" type="text" value="<?= $row['npsn'] ?>" id="npsn_display" disabled>
                                    </div>
                                    
                                    <div class="form-group">
                                        <label for="nama_sekolah">Nama Sekolah</label>
                                        <input class="form-control" type="text" name="nama_sekolah" id="nama_sekolah" value="<?= $row['nama_sekolah'] ?>" required>
                                    </div>
                                    
                                    <div class="form-group">
                                        <label for="jenjang_pendidikan">Jenjang Pendidikan</label>
                                        <select class="form-control" name="jenjang_pendidikan" id="jenjang_pendidikan" required>
                                            <option value="">--Pilih Jenjang--</option>
                                            <option value="TK" <?= $row['jenjang_pendidikan'] == 'TK' ? 'selected' : '' ?>>TK</option>
                                            <option value="SD" <?= $row['jenjang_pendidikan'] == 'SD' ? 'selected' : '' ?>>SD</option>
                                            <option value="SMP" <?= $row['jenjang_pendidikan'] == 'SMP' ? 'selected' : '' ?>>SMP</option>
                                            <option value="SMA" <?= $row['jenjang_pendidikan'] == 'SMA' ? 'selected' : '' ?>>SMA</option>
                                            <option value="SMK" <?= $row['jenjang_pendidikan'] == 'SMK' ? 'selected' : '' ?>>SMK</option>
                                            <option value="SLB" <?= $row['jenjang_pendidikan'] == 'SLB' ? 'selected' : '' ?>>SLB</option>
                                        </select>
                                    </div>
                                    
                                    <div class="form-group">
                                        <label for="status_sekolah">Status Sekolah</label>
                                        <select class="form-control" name="status_sekolah" id="status_sekolah" required>
                                            <option value="">--Pilih Status--</option>
                                            <option value="Negeri" <?= $row['status_sekolah'] == 'Negeri' ? 'selected' : '' ?>>Negeri</option>
                                            <option value="Swasta" <?= $row['status_sekolah'] == 'Swasta' ? 'selected' : '' ?>>Swasta</option>
                                        </select>
                                    </div>
                                    
                                    <div class="form-group">
                                        <label for="alamat_jalan">Alamat Jalan</label>
                                        <textarea class="form-control" name="alamat_jalan" id="alamat_jalan" rows="3" required><?= $row['alamat_jalan'] ?></textarea>
                                    </div>
                                </div>
                                
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="rt">RT</label>
                                        <input class="form-control" type="text" name="rt" id="rt" value="<?= $row['rt'] ?>" maxlength="5">
                                    </div>
                                    
                                    <div class="form-group">
                                        <label for="rw">RW</label>
                                        <input class="form-control" type="text" name="rw" id="rw" value="<?= $row['rw'] ?>" maxlength="5">
                                    </div>
                                    
                                    <div class="form-group">
                                        <label for="kode_pos">Kode Pos</label>
                                        <input class="form-control" type="text" name="kode_pos" id="kode_pos" value="<?= $row['kode_pos'] ?>" maxlength="10">
                                    </div>
                                    
                                    <div class="form-group">
                                        <label for="kelurahan">Kelurahan</label>
                                        <input class="form-control" type="text" name="kelurahan" id="kelurahan" value="<?= $row['kelurahan'] ?>">
                                    </div>
                                    
                                    <div class="form-group">
                                        <label for="id_kecamatan_fk">Kecamatan</label>
                                        <select class="form-control" name="id_kecamatan_fk" id="id_kecamatan_fk">
                                            <option value="">--Pilih Kecamatan--</option>
                                            <?php mysqli_data_seek($kecamatan, 0); // Reset pointer to beginning ?>
                                            <?php while ($kec = mysqli_fetch_array($kecamatan)) : ?>
                                                <option value="<?= $kec['id_kecamatan'] ?>" <?= $row['id_kecamatan_fk'] == $kec['id_kecamatan'] ? 'selected' : '' ?>>
                                                    <?= $kec['nama_kecamatan'] ?> - <?= $kec['nama_kabupaten'] ?> - <?= $kec['nama_provinsi'] ?>
                                                </option>
                                            <?php endwhile; ?>
                                        </select>
                                    </div>
                                    
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="lintang">Lintang</label>
                                                <input class="form-control" type="text" name="lintang" id="lintang" value="<?= $row['lintang'] ?>" placeholder="Koordinat Lintang">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="bujur">Bujur</label>
                                                <input class="form-control" type="text" name="bujur" id="bujur" value="<?= $row['bujur'] ?>" placeholder="Koordinat Bujur">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- URL Scraping Section -->
                            <div class="row mt-4">
                                <div class="col-12">
                                    <div class="card">
                                        <div class="card-header">
                                            <h6 class="card-title mb-0">
                                                <i class="fas fa-link"></i> URL Scraping (Opsional)
                                            </h6>
                                        </div>
                                        <div class="card-body">
                                            <p class="text-muted small">
                                                Tambahkan URL dari Dapodik untuk scraping otomatis data sekolah ini. 
                                                Contoh: https://dapo.kemendikdasmen.go.id/sekolah/XXXXXX
                                            </p>
                                            
                                            <?php if ($existing_url): ?>
                                                <div class="alert alert-info">
                                                    <strong>URL Tersimpan:</strong><br>
                                                    <small class="text-muted">Status: <?= $existing_url['status'] ?></small><br>
                                                    <a href="<?= htmlspecialchars($existing_url['url']) ?>" target="_blank" class="text-primary">
                                                        <?= htmlspecialchars($existing_url['url']) ?>
                                                    </a><br>
                                                    <small class="text-muted">Deskripsi: <?= htmlspecialchars($existing_url['description']) ?></small>
                                                </div>
                                            <?php endif; ?>
                                            
                                            <div class="form-group">
                                                <label for="scraping_url">URL Dapodik</label>
                                                <input type="url" 
                                                       id="scraping_url" 
                                                       class="form-control" 
                                                       value="<?= $existing_url ? htmlspecialchars($existing_url['url']) : '' ?>"
                                                       placeholder="https://dapo.kemendikdasmen.go.id/sekolah/...">
                                                <small class="form-text text-muted">
                                                    Masukkan URL halaman detail sekolah dari Dapodik untuk scraping otomatis
                                                </small>
                                            </div>
                                            
                                            <div class="form-group">
                                                <label for="url_description">Deskripsi URL</label>
                                                <input type="text" 
                                                       id="url_description" 
                                                       class="form-control" 
                                                       value="<?= $existing_url ? htmlspecialchars($existing_url['description']) : $row['nama_sekolah'] . ' (NPSN: ' . $row['npsn'] . ')' ?>"
                                                       placeholder="Deskripsi untuk URL ini">
                                            </div>
                                            
                                            <div class="form-group">
                                                <button type="button" id="saveUrlBtn" class="btn btn-info btn-sm">
                                                    <i class="fas fa-save"></i> <?= $existing_url ? 'Update URL' : 'Simpan URL' ?>
                                                </button>
                                                <?php if ($existing_url): ?>
                                                    <button type="button" id="deleteUrlBtn" class="btn btn-danger btn-sm ml-2" data-url-id="<?= $existing_url['id'] ?>">
                                                        <i class="fas fa-trash"></i> Hapus URL
                                                    </button>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="form-group mt-3">
                                <button type="submit" class="btn btn-primary" id="submitBtn">
                                    <i class="fas fa-save"></i> Ubah Data
                                </button>
                                <a href="./index.php" class="btn btn-danger ml-1">Batal</a>
                            </div>
                        <?php } ?>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>
<script>
// Form submission confirmation
document.getElementById('editForm').addEventListener('submit', function(e) {
  e.preventDefault();
  
  // Show confirmation dialog
  if (confirm('Apakah Anda yakin ingin mengubah data sekolah ini?')) {
    // Show loading state
    const submitBtn = document.getElementById('submitBtn');
    const originalText = submitBtn.innerHTML;
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Menyimpan...';
    
    // Submit the form
    this.submit();
  }
});

// Save/Update URL functionality
document.getElementById('saveUrlBtn').addEventListener('click', function() {
    const url = document.getElementById('scraping_url').value.trim();
    const description = document.getElementById('url_description').value.trim();
    const npsn = '<?= $row['npsn'] ?>';
    const existingUrlId = <?= $existing_url ? $existing_url['id'] : 'null' ?>;
    
    if (!url) {
        iziToast.warning({
            title: 'Peringatan',
            message: 'URL tidak boleh kosong',
            position: 'topCenter',
            timeout: 5000
        });
        return;
    }
    
    // Validate URL format
    if (!url.startsWith('https://dapo.kemendikdasmen.go.id/sekolah/')) {
        iziToast.warning({
            title: 'Peringatan',
            message: 'URL harus dari domain dapo.kemendikdasmen.go.id/sekolah/',
            position: 'topCenter',
            timeout: 5000
        });
        return;
    }
    
    const btn = this;
    const originalText = btn.innerHTML;
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Menyimpan...';
    
    const data = {
        url: url,
        description: description,
        npsn: npsn,
        existing_url_id: existingUrlId
    };
    
    fetch('save_url_from_edit.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify(data)
    })
    .then(response => response.json())
    .then(data => {
        btn.disabled = false;
        btn.innerHTML = originalText;
        
        if (data.success) {
            iziToast.success({
                title: 'Sukses',
                message: data.message,
                position: 'topCenter',
                timeout: 3000,
                backgroundColor: '#1cc88a',
                progressBarColor: '#0f6848'
            });
            
            // Reload page after short delay to show updated data
            setTimeout(() => {
                window.location.reload();
            }, 1500);
        } else {
            iziToast.error({
                title: 'Error',
                message: data.message,
                position: 'topCenter',
                backgroundColor: '#e74a3b',
                progressBarColor: '#a02622'
            });
        }
    })
    .catch(error => {
        btn.disabled = false;
        btn.innerHTML = originalText;
        
        iziToast.error({
            title: 'Error',
            message: 'Terjadi kesalahan: ' + error.message,
            position: 'topCenter',
            backgroundColor: '#e74a3b',
            progressBarColor: '#a02622'
        });
    });
});

// Delete URL functionality
<?php if ($existing_url): ?>
document.getElementById('deleteUrlBtn').addEventListener('click', function() {
    if (!confirm('Yakin ingin menghapus URL scraping ini?')) {
        return;
    }
    
    const urlId = this.dataset.urlId;
    const btn = this;
    const originalText = btn.innerHTML;
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Menghapus...';
    
    fetch('delete_url.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({id: urlId})
    })
    .then(response => response.json())
    .then(data => {
        btn.disabled = false;
        btn.innerHTML = originalText;
        
        if (data.success) {
            iziToast.success({
                title: 'Sukses',
                message: 'URL berhasil dihapus',
                position: 'topCenter',
                backgroundColor: '#1cc88a',
                progressBarColor: '#0f6848'
            });
            
            // Reload page to show updated state
            setTimeout(() => {
                window.location.reload();
            }, 1500);
        } else {
            iziToast.error({
                title: 'Error',
                message: data.message,
                position: 'topCenter',
                backgroundColor: '#e74a3b',
                progressBarColor: '#a02622'
            });
        }
    })
    .catch(error => {
        btn.disabled = false;
        btn.innerHTML = originalText;
        
        iziToast.error({
            title: 'Error',
            message: 'Terjadi kesalahan: ' + error.message,
            position: 'topCenter',
            backgroundColor: '#e74a3b',
            progressBarColor: '#a02622'
        });
    });
});
<?php endif; ?>
</script>
<?php
require_once '../layout/_bottom.php';
?>