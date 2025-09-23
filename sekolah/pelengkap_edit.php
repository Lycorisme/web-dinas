<?php
require_once '../layout/_top.php';
require_once '../helper/connection.php';

$id = $_GET['id'];
$query = mysqli_query($connection, "SELECT p.*, s.nama_sekolah 
                                   FROM sekolah_pelengkap p
                                   LEFT JOIN sekolah_identitas s ON p.npsn_fk = s.npsn
                                   WHERE p.id='$id'");
$sekolah = mysqli_query($connection, "SELECT npsn, nama_sekolah FROM sekolah_identitas ORDER BY nama_sekolah");
?>

<section class="section">
  <div class="section-header d-flex justify-content-between">
    <h1>Ubah Data Pelengkap Sekolah</h1>
    <a href="./pelengkap.php" class="btn btn-light">Kembali</a>
  </div>
  <div class="row">
    <div class="col-12">
      <div class="card">
        <div class="card-body">
          <form action="./pelengkap_update.php" method="post">
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
                    <label>SK Pendirian</label>
                    <input class="form-control" type="text" name="sk_pendirian" value="<?= $row['sk_pendirian'] ?>">
                  </div>
                  
                  <div class="form-group">
                    <label>Tanggal SK Pendirian</label>
                    <input class="form-control" type="date" name="tgl_sk_pendirian" value="<?= $row['tgl_sk_pendirian'] ?>">
                  </div>
                  
                  <div class="form-group">
                    <label>Status Kepemilikan</label>
                    <select class="form-control" name="status_kepemilikan">
                      <option value="">--Pilih Status--</option>
                      <option value="Milik Sendiri" <?= $row['status_kepemilikan'] == 'Milik Sendiri' ? 'selected' : '' ?>>Milik Sendiri</option>
                      <option value="Sewa" <?= $row['status_kepemilikan'] == 'Sewa' ? 'selected' : '' ?>>Sewa</option>
                      <option value="Pinjam" <?= $row['status_kepemilikan'] == 'Pinjam' ? 'selected' : '' ?>>Pinjam</option>
                      <option value="Hibah" <?= $row['status_kepemilikan'] == 'Hibah' ? 'selected' : '' ?>>Hibah</option>
                    </select>
                  </div>
                  
                  <div class="form-group">
                    <label>SK Izin Operasional</label>
                    <input class="form-control" type="text" name="sk_izin_operasional" value="<?= $row['sk_izin_operasional'] ?>">
                  </div>
                  
                  <div class="form-group">
                    <label>Tanggal SK Izin Operasional</label>
                    <input class="form-control" type="date" name="tgl_sk_izin_operasional" value="<?= $row['tgl_sk_izin_operasional'] ?>">
                  </div>
                  
                  <div class="form-group">
                    <label>Kebutuhan Khusus Dilayani</label>
                    <input class="form-control" type="text" name="kebutuhan_khusus_dilayani" value="<?= $row['kebutuhan_khusus_dilayani'] ?>">
                  </div>
                  
                  <div class="form-group">
                    <label>MBS</label>
                    <select class="form-control" name="mbs">
                      <option value="">--Pilih--</option>
                      <option value="Ya" <?= $row['mbs'] == 'Ya' ? 'selected' : '' ?>>Ya</option>
                      <option value="Tidak" <?= $row['mbs'] == 'Tidak' ? 'selected' : '' ?>>Tidak</option>
                    </select>
                  </div>
                </div>
                
                <div class="col-md-6">
                  <div class="form-group">
                    <label>Nomor Rekening</label>
                    <input class="form-control" type="text" name="nomor_rekening" value="<?= $row['nomor_rekening'] ?>">
                  </div>
                  
                  <div class="form-group">
                    <label>Nama Bank</label>
                    <input class="form-control" type="text" name="nama_bank" value="<?= $row['nama_bank'] ?>">
                  </div>
                  
                  <div class="form-group">
                    <label>Cabang KCP Unit</label>
                    <input class="form-control" type="text" name="cabang_kcp_unit" value="<?= $row['cabang_kcp_unit'] ?>">
                  </div>
                  
                  <div class="form-group">
                    <label>Rekening Atas Nama</label>
                    <input class="form-control" type="text" name="rekening_atas_nama" value="<?= $row['rekening_atas_nama'] ?>">
                  </div>
                  
                  <div class="form-group">
                    <label>Luas Tanah Milik (m²)</label>
                    <input class="form-control" type="number" name="luas_tanah_milik_m2" value="<?= $row['luas_tanah_milik_m2'] ?>">
                  </div>
                  
                  <div class="form-group">
                    <label>Luas Tanah Bukan Milik (m²)</label>
                    <input class="form-control" type="number" name="luas_tanah_bukan_milik_m2" value="<?= $row['luas_tanah_bukan_milik_m2'] ?>">
                  </div>
                  
                  <div class="form-group">
                    <label>Nama Wajib Pajak</label>
                    <input class="form-control" type="text" name="nama_wajib_pajak" value="<?= $row['nama_wajib_pajak'] ?>">
                  </div>
                  
                  <div class="form-group">
                    <label>NPWP</label>
                    <input class="form-control" type="text" name="npwp" value="<?= $row['npwp'] ?>" maxlength="25">
                  </div>
                </div>
              </div>
              
              <div class="form-group">
                <input class="btn btn-primary d-inline" type="submit" name="proses" value="Ubah">
                <a href="./pelengkap.php" class="btn btn-danger ml-1">Batal</a>
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