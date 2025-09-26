<?php
require_once '../layout/_top.php';
require_once '../helper/connection.php';

$id = $_GET['id'];
$query = mysqli_query($connection, "SELECT l.*, s.nama_sekolah 
                                   FROM sekolah_lainnya l
                                   LEFT JOIN sekolah_identitas s ON l.npsn_fk = s.npsn
                                   WHERE l.id='$id'");
$sekolah = mysqli_query($connection, "SELECT npsn, nama_sekolah FROM sekolah_identitas ORDER BY nama_sekolah");
?>

<section class="section">
  <div class="section-header d-flex justify-content-between">
    <h1>Ubah Data Lainnya Sekolah</h1>
  </div>
  <div class="row">
    <div class="col-12">
      <div class="card">
        <div class="card-body">
          <form action="./lainnya_update.php" method="post">
            <?php
            while ($row = mysqli_fetch_array($query)) {
            ?>
              <input type="hidden" name="id" value="<?= $row['id'] ?>">
              <div class="row">
                <div class="col-md-6">
                  <div class="form-group">
                    <label>Sekolah</label>
                    <select class="form-control" name="npsn_fk" required>
                      <option value="">--Pilih Sekolah--</option>
                      <?php while ($sch = mysqli_fetch_array($sekolah)) : ?>
                        <option value="<?= $sch['npsn'] ?>" <?= $row['npsn_fk'] == $sch['npsn'] ? 'selected' : '' ?>>
                          <?= $sch['npsn'] ?> - <?= $sch['nama_sekolah'] ?>
                        </option>
                      <?php endwhile; ?>
                    </select>
                  </div>
                  
                  <div class="form-group">
                    <label>Kepala Sekolah</label>
                    <input class="form-control" type="text" name="kepala_sekolah" value="<?= $row['kepala_sekolah'] ?>" maxlength="150">
                  </div>
                  
                  <div class="form-group">
                    <label>Operator Pendataan</label>
                    <input class="form-control" type="text" name="operator_pendataan" value="<?= $row['operator_pendataan'] ?>" maxlength="150">
                  </div>
                </div>
                
                <div class="col-md-6">
                  <div class="form-group">
                    <label>Akreditasi</label>
                    <select class="form-control" name="akreditasi">
                      <option value="">--Pilih Akreditasi--</option>
                      <option value="A" <?= $row['akreditasi'] == 'A' ? 'selected' : '' ?>>A (Sangat Baik)</option>
                      <option value="B" <?= $row['akreditasi'] == 'B' ? 'selected' : '' ?>>B (Baik)</option>
                      <option value="C" <?= $row['akreditasi'] == 'C' ? 'selected' : '' ?>>C (Cukup)</option>
                      <option value="Belum Terakreditasi" <?= $row['akreditasi'] == 'Belum Terakreditasi' ? 'selected' : '' ?>>Belum Terakreditasi</option>
                    </select>
                  </div>
                  
                  <div class="form-group">
                    <label>Kurikulum</label>
                    <select class="form-control" name="kurikulum">
                      <option value="">--Pilih Kurikulum--</option>
                      <option value="Kurikulum 2013" <?= $row['kurikulum'] == 'Kurikulum 2013' ? 'selected' : '' ?>>Kurikulum 2013</option>
                      <option value="Kurikulum Merdeka" <?= $row['kurikulum'] == 'Kurikulum Merdeka' ? 'selected' : '' ?>>Kurikulum Merdeka</option>
                      <option value="KTSP" <?= $row['kurikulum'] == 'KTSP' ? 'selected' : '' ?>>KTSP</option>
                      <option value="Kurikulum Darurat" <?= $row['kurikulum'] == 'Kurikulum Darurat' ? 'selected' : '' ?>>Kurikulum Darurat</option>
                    </select>
                  </div>
                </div>
              </div>
              
              <div class="form-group">
              <input class="btn btn-primary" type="submit" name="proses" value="Simpan">
                <a href="./lainnya.php" class="btn btn-danger ml-1">Batal</a>
              </div>

            <?php } ?>
          </form>
        </div>
      </div>
    </div>
</section>

<?php
require_once '../layout/_bottom.php';
?>