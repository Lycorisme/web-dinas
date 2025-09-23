<?php
require_once '../layout/_top.php';
require_once '../helper/connection.php';

$sekolah = mysqli_query($connection, "SELECT npsn, nama_sekolah FROM sekolah_identitas ORDER BY nama_sekolah");
?>

<section class="section">
  <div class="section-header d-flex justify-content-between">
    <h1>Tambah Data Pelengkap Sekolah</h1>
    <a href="./pelengkap.php" class="btn btn-light">Kembali</a>
  </div>
  <div class="row">
    <div class="col-12">
      <div class="card">
        <div class="card-body">
          <form action="./pelengkap_store.php" method="POST">
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
                  <label>SK Pendirian</label>
                  <input class="form-control" type="text" name="sk_pendirian">
                </div>
                
                <div class="form-group">
                  <label>Tanggal SK Pendirian</label>
                  <input class="form-control" type="date" name="tgl_sk_pendirian">
                </div>
                
                <div class="form-group">
                  <label>Status Kepemilikan</label>
                  <select class="form-control" name="status_kepemilikan">
                    <option value="">--Pilih Status--</option>
                    <option value="Milik Sendiri">Milik Sendiri</option>
                    <option value="Sewa">Sewa</option>
                    <option value="Pinjam">Pinjam</option>
                    <option value="Hibah">Hibah</option>
                  </select>
                </div>
                
                <div class="form-group">
                  <label>SK Izin Operasional</label>
                  <input class="form-control" type="text" name="sk_izin_operasional">
                </div>
                
                <div class="form-group">
                  <label>Tanggal SK Izin Operasional</label>
                  <input class="form-control" type="date" name="tgl_sk_izin_operasional">
                </div>
                
                <div class="form-group">
                  <label>Kebutuhan Khusus Dilayani</label>
                  <input class="form-control" type="text" name="kebutuhan_khusus_dilayani">
                </div>
                
                <div class="form-group">
                  <label>MBS</label>
                  <select class="form-control" name="mbs">
                    <option value="">--Pilih--</option>
                    <option value="Ya">Ya</option>
                    <option value="Tidak">Tidak</option>
                  </select>
                </div>
              </div>
              
              <div class="col-md-6">
                <div class="form-group">
                  <label>Nomor Rekening</label>
                  <input class="form-control" type="text" name="nomor_rekening">
                </div>
                
                <div class="form-group">
                  <label>Nama Bank</label>
                  <input class="form-control" type="text" name="nama_bank">
                </div>
                
                <div class="form-group">
                  <label>Cabang KCP Unit</label>
                  <input class="form-control" type="text" name="cabang_kcp_unit">
                </div>
                
                <div class="form-group">
                  <label>Rekening Atas Nama</label>
                  <input class="form-control" type="text" name="rekening_atas_nama">
                </div>
                
                <div class="form-group">
                  <label>Luas Tanah Milik (m²)</label>
                  <input class="form-control" type="number" name="luas_tanah_milik_m2">
                </div>
                
                <div class="form-group">
                  <label>Luas Tanah Bukan Milik (m²)</label>
                  <input class="form-control" type="number" name="luas_tanah_bukan_milik_m2">
                </div>
                
                <div class="form-group">
                  <label>Nama Wajib Pajak</label>
                  <input class="form-control" type="text" name="nama_wajib_pajak">
                </div>
                
                <div class="form-group">
                  <label>NPWP</label>
                  <input class="form-control" type="text" name="npwp" maxlength="25">
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