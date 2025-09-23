<?php
require_once '../layout/_top.php';
require_once '../helper/connection.php';

$sekolah = mysqli_query($connection, "SELECT npsn, nama_sekolah FROM sekolah_identitas ORDER BY nama_sekolah");
?>

<section class="section">
  <div class="section-header d-flex justify-content-between">
    <h1>Tambah Data Lainnya Sekolah</h1>
    <a href="./lainnya.php" class="btn btn-light">Kembali</a>
  </div>
  <div class="row">
    <div class="col-12">
      <div class="card">
        <div class="card-body">
          <form action="./lainnya_store.php" method="POST">
            <div class="row">
              <div class="col-md-6">
                <div class="form-group">
                  <label>Sekolah</label>
                  <select class="form-control" name="npsn_fk" required>
                    <option value="">--Pilih Sekolah--</option>
                    <?php while ($sch = mysqli_fetch_array($sekolah)) : ?>
                      <option value="<?= $sch['npsn'] ?>"><?= $sch['npsn'] ?> - <?= $sch['nama_sekolah'] ?></option>
                    <?php endwhile; ?>
                  </select>
                </div>
                
                <div class="form-group">
                  <label>Kepala Sekolah</label>
                  <input class="form-control" type="text" name="kepala_sekolah" maxlength="150">
                </div>
                
                <div class="form-group">
                  <label>Operator Pendataan</label>
                  <input class="form-control" type="text" name="operator_pendataan" maxlength="150">
                </div>
              </div>
              
              <div class="col-md-6">
                <div class="form-group">
                  <label>Akreditasi</label>
                  <select class="form-control" name="akreditasi">
                    <option value="">--Pilih Akreditasi--</option>
                    <option value="A">A (Sangat Baik)</option>
                    <option value="B">B (Baik)</option>
                    <option value="C">C (Cukup)</option>
                    <option value="Belum Terakreditasi">Belum Terakreditasi</option>
                  </select>
                </div>
                
                <div class="form-group">
                  <label>Kurikulum</label>
                  <select class="form-control" name="kurikulum">
                    <option value="">--Pilih Kurikulum--</option>
                    <option value="Kurikulum 2013">Kurikulum 2013</option>
                    <option value="Kurikulum Merdeka">Kurikulum Merdeka</option>
                    <option value="KTSP">KTSP</option>
                    <option value="Kurikulum Darurat">Kurikulum Darurat</option>
                  </select>
                </div>
              </div>
            </div>
            
            <div class="form-group">
              <input class="btn btn-primary" type="submit" name="proses" value="Simpan">
              <input class="btn btn-danger" type="reset" name="batal" value="Bersihkan">
            </div>
          </form>
        </div>
      </div>
    </div>
</section>

<?php
require_once '../layout/_bottom.php';
?>