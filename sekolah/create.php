<?php
require_once '../layout/_top.php';
require_once '../helper/connection.php';

$kecamatan = mysqli_query($connection, "SELECT k.*, kab.nama_kabupaten, p.nama_provinsi 
                                       FROM kecamatan k
                                       LEFT JOIN kabupaten_kota kab ON k.id_kabupaten_fk = kab.id_kabupaten
                                       LEFT JOIN provinsi p ON kab.id_provinsi_fk = p.id_provinsi
                                       ORDER BY p.nama_provinsi, kab.nama_kabupaten, k.nama_kecamatan");
?>

<section class="section">
    <div class="section-header d-flex justify-content-between align-items-center">
        <h1>Tambah Data Sekolah</h1>
        <a href="./index.php" class="btn btn-light">
            <i class="fas fa-arrow-left"></i> Kembali
        </a>
    </div>
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <form action="./store.php" method="post">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="npsn">NPSN *</label>
                                    <input class="form-control" type="text" name="npsn" id="npsn" required>
                                </div>
                                
                                <div class="form-group">
                                    <label for="nama_sekolah">Nama Sekolah *</label>
                                    <input class="form-control" type="text" name="nama_sekolah" id="nama_sekolah" required>
                                </div>
                                
                                <div class="form-group">
                                    <label for="jenjang_pendidikan">Jenjang Pendidikan *</label>
                                    <select class="form-control" name="jenjang_pendidikan" id="jenjang_pendidikan" required>
                                        <option value="">--Pilih Jenjang--</option>
                                        <option value="TK">TK</option>
                                        <option value="SD">SD</option>
                                        <option value="SMP">SMP</option>
                                        <option value="SMA">SMA</option>
                                        <option value="SMK">SMK</option>
                                        <option value="SLB">SLB</option>
                                    </select>
                                </div>
                                
                                <div class="form-group">
                                    <label for="status_sekolah">Status Sekolah *</label>
                                    <select class="form-control" name="status_sekolah" id="status_sekolah" required>
                                        <option value="">--Pilih Status--</option>
                                        <option value="Negeri">Negeri</option>
                                        <option value="Swasta">Swasta</option>
                                    </select>
                                </div>
                                
                                <div class="form-group">
                                    <label for="alamat_jalan">Alamat Jalan *</label>
                                    <textarea class="form-control" name="alamat_jalan" id="alamat_jalan" rows="3" required></textarea>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="rt">RT</label>
                                    <input class="form-control" type="text" name="rt" id="rt" maxlength="5">
                                </div>
                                
                                <div class="form-group">
                                    <label for="rw">RW</label>
                                    <input class="form-control" type="text" name="rw" id="rw" maxlength="5">
                                </div>
                                
                                <div class="form-group">
                                    <label for="kode_pos">Kode Pos</label>
                                    <input class="form-control" type="text" name="kode_pos" id="kode_pos" maxlength="10">
                                </div>
                                
                                <div class="form-group">
                                    <label for="kelurahan">Kelurahan</label>
                                    <input class="form-control" type="text" name="kelurahan" id="kelurahan">
                                </div>
                                
                                <div class="form-group">
                                    <label for="id_kecamatan_fk">Kecamatan *</label>
                                    <select class="form-control" name="id_kecamatan_fk" id="id_kecamatan_fk" required>
                                        <option value="">--Pilih Kecamatan--</option>
                                        <?php while ($kec = mysqli_fetch_array($kecamatan)) : ?>
                                            <option value="<?= $kec['id_kecamatan'] ?>">
                                                <?= $kec['nama_kecamatan'] ?> - <?= $kec['nama_kabupaten'] ?> - <?= $kec['nama_provinsi'] ?>
                                            </option>
                                        <?php endwhile; ?>
                                    </select>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="lintang">Lintang</label>
                                            <input class="form-control" type="text" name="lintang" id="lintang" placeholder="Koordinat Lintang">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="bujur">Bujur</label>
                                            <input class="form-control" type="text" name="bujur" id="bujur" placeholder="Koordinat Bujur">
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
                                        
                                        <div class="form-group">
                                            <label for="scraping_url">URL Dapodik</label>
                                            <input type="url" 
                                                   id="scraping_url" 
                                                   name="scraping_url"
                                                   class="form-control" 
                                                   placeholder="https://dapo.kemendikdasmen.go.id/sekolah/...">
                                            <small class="form-text text-muted">
                                                Masukkan URL halaman detail sekolah dari Dapodik untuk scraping otomatis
                                            </small>
                                        </div>
                                        
                                        <div class="form-group">
                                            <label for="url_description">Deskripsi URL</label>
                                            <input type="text" 
                                                   id="url_description" 
                                                   name="url_description"
                                                   class="form-control" 
                                                   placeholder="Deskripsi untuk URL ini">
                                            <small class="form-text text-muted">
                                                Deskripsi akan otomatis terisi berdasarkan nama sekolah jika kosong
                                            </small>
                                        </div>
                                        
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="save_url_checkbox" name="save_url" value="1">
                                            <label class="form-check-label" for="save_url_checkbox">
                                                Simpan URL untuk scraping otomatis
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="form-group mt-3">
                            <input class="btn btn-primary d-inline" type="submit" name="proses" value="Simpan">
                            <a href="./index.php" class="btn btn-danger ml-1">Batal</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
// Auto-fill deskripsi URL berdasarkan nama sekolah
document.querySelector('input[name="nama_sekolah"]').addEventListener('input', function() {
    const namaSekolah = this.value.trim();
    const npsn = document.querySelector('input[name="npsn"]').value.trim();
    const deskripsiField = document.getElementById('url_description');
    
    if (namaSekolah && npsn) {
        deskripsiField.value = `${namaSekolah} (NPSN: ${npsn})`;
    } else if (namaSekolah) {
        deskripsiField.value = namaSekolah;
    }
});

document.querySelector('input[name="npsn"]').addEventListener('input', function() {
    const npsn = this.value.trim();
    const namaSekolah = document.querySelector('input[name="nama_sekolah"]').value.trim();
    const deskripsiField = document.getElementById('url_description');
    
    if (namaSekolah && npsn) {
        deskripsiField.value = `${namaSekolah} (NPSN: ${npsn})`;
    }
});

// Validasi URL domain
document.getElementById('scraping_url').addEventListener('blur', function() {
    const url = this.value.trim();
    
    if (url && !url.startsWith('https://dapo.kemendikdasmen.go.id/sekolah/')) {
        iziToast.warning({
            title: 'Peringatan',
            message: 'URL harus dari domain dapo.kemendikdasmen.go.id/sekolah/',
            position: 'topCenter',
            timeout: 5000
        });
        this.focus();
    }
});
</script>

<?php
require_once '../layout/_bottom.php';
?>