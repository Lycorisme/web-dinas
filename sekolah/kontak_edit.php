<?php
require_once '../layout/_top.php';
require_once '../helper/connection.php';

$id = $_GET['id'];
$query = mysqli_query($connection, "SELECT k.*, s.nama_sekolah 
                                   FROM sekolah_kontak k
                                   LEFT JOIN sekolah_identitas s ON k.npsn_fk = s.npsn
                                   WHERE k.id='$id'");
$sekolah = mysqli_query($connection, "SELECT npsn, nama_sekolah FROM sekolah_identitas ORDER BY nama_sekolah");
?>

<section class="section">
  <div class="section-header d-flex justify-content-between">
    <h1>Ubah Kontak Sekolah</h1>
    <a href="./kontak.php" class="btn btn-light">Kembali</a>
  </div>
  <div class="row">
    <div class="col-12">
      <div class="card">
        <div class="card-body">
          <form action="./kontak_update.php" method="post">
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
                    <label>Nomor Telepon</label>
                    <input class="form-control" type="text" name="nomor_telepon" value="<?= $row['nomor_telepon'] ?>">
                  </div>
                  
                  <div class="form-group">
                    <label>Nomor Fax</label>
                    <input class="form-control" type="text" name="nomor_fax" value="<?= $row['nomor_fax'] ?>">
                  </div>
                </div>
                
                <div class="col-md-6">
                  <div class="form-group">
                    <label>Email</label>
                    <input class="form-control" type="email" name="email" value="<?= $row['email'] ?>">
                  </div>
                  
                  <div class="form-group">
                    <label>Website</label>
                    <input class="form-control" type="url" name="website" value="<?= $row['website'] ?>" placeholder="https://example.com">
                  </div>
                </div>
              </div>
              
              <div class="form-group">
                <input class="btn btn-primary d-inline" type="submit" name="proses" value="Ubah">
                <a href="./kontak.php" class="btn btn-danger ml-1">Batal</a>
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