<?php
require_once '../layout/_top.php';
require_once '../helper/connection.php';

 $id = $_GET['id'];
 $query = mysqli_query($connection, "SELECT r.*, s.nama_sekolah 
                                   FROM rekap_sarpras r
                                   LEFT JOIN sekolah_identitas s ON r.npsn_fk = s.npsn
                                   WHERE r.id='$id'");
 $sekolah = mysqli_query($connection, "SELECT npsn, nama_sekolah FROM sekolah_identitas ORDER BY nama_sekolah");
?>

<section class="section">
  <div class="section-header d-flex justify-content-between">
    <h1>Ubah Data Sarana Prasarana</h1>
  </div>
  <div class="row">
    <div class="col-12">
      <div class="card">
        <div class="card-body">
          <?php
          while ($row = mysqli_fetch_array($query)) {
          ?>
          <form action="./sarpras_update.php" method="post" id="formEditData">
            <input type="hidden" name="id" value="<?= $row['id'] ?>">
            <div class="row">
              <div class="col-md-6">
                <div class="form-group">
                  <label>Sekolah</label>
                  <select class="form-control" name="npsn_fk" id="npsn_fk" required>
                    <option value="">--Pilih Sekolah--</option>
                    <?php 
                    mysqli_data_seek($sekolah, 0); // Reset pointer result set
                    while ($sch = mysqli_fetch_array($sekolah)) : ?>
                      <option value="<?= $sch['npsn'] ?>" <?= $row['npsn_fk'] == $sch['npsn'] ? 'selected' : '' ?>>
                        <?= $sch['npsn'] ?> - <?= $sch['nama_sekolah'] ?>
                      </option>
                    <?php endwhile; ?>
                  </select>
                </div>
                
                <div class="form-group">
                  <label>Sarana</label>
                  <select class="form-control" name="sarana" id="sarana" required>
                    <option value="">--Pilih Sarana--</option>
                    <option value="Ruang Kelas" <?= $row['sarana'] == 'Ruang Kelas' ? 'selected' : '' ?>>Ruang Kelas</option>
                    <option value="Ruang Lab" <?= $row['sarana'] == 'Ruang Lab' ? 'selected' : '' ?>>Ruang Lab</option>
                    <option value="Ruang Perpus" <?= $row['sarana'] == 'Ruang Perpus' ? 'selected' : '' ?>>Ruang Perpus</option>
                    <option value="Ruang Guru" <?= $row['sarana'] == 'Ruang Guru' ? 'selected' : '' ?>>Ruang Guru</option>
                    <option value="Ruang Kepala Sekolah" <?= $row['sarana'] == 'Ruang Kepala Sekolah' ? 'selected' : '' ?>>Ruang Kepala Sekolah</option>
                    <option value="Ruang TU" <?= $row['sarana'] == 'Ruang TU' ? 'selected' : '' ?>>Ruang TU</option>
                    <option value="Ruang UKS" <?= $row['sarana'] == 'Ruang UKS' ? 'selected' : '' ?>>Ruang UKS</option>
                    <option value="Ruang Toilet" <?= $row['sarana'] == 'Ruang Toilet' ? 'selected' : '' ?>>Ruang Toilet</option>
                    <option value="Ruang Gudang" <?= $row['sarana'] == 'Ruang Gudang' ? 'selected' : '' ?>>Ruang Gudang</option>
                    <option value="Lainnya" <?= $row['sarana'] == 'Lainnya' ? 'selected' : '' ?>>Lainnya</option>
                  </select>
                </div>
              </div>
              
              <div class="col-md-6">
                <div class="form-group">
                  <label>Jumlah</label>
                  <input class="form-control" type="number" name="jumlah" min="0" value="<?= $row['jumlah'] ?>" required>
                </div>
              </div>
            </div>
            
            <div class="form-group">
              <input class="btn btn-primary" type="submit" name="proses" value="Simpan">
              <a href="./sarpras.php" class="btn btn-danger ml-1">Batal</a>
            </div>
          </form>
          <?php } ?>
        </div>
      </div>
    </div>
  </div>
</section>

<?php
require_once '../layout/_bottom.php';
?>

<script>
// Fungsi normalisasi sarana di JavaScript
function normalizeSarana(str) {
    // Ubah ke huruf kecil
    str = str.toLowerCase();
    
    // Hapus spasi berlebih di awal dan akhir
    str = str.trim();
    
    // Hapus spasi berlebih di tengah dan hapus tanda hubung
    str = str.replace(/[\s\-]+/g, '');
    
    // Hapus karakter khusus kecuali huruf dan angka
    str = str.replace(/[^a-z0-9]/g, '');
    
    return str;
}

 $(document).ready(function() {
    // Cek data yang sudah ada saat sekolah dipilih
    $('#npsn_fk').change(function() {
        const npsn = $(this).val();
        const currentId = $('input[name="id"]').val();
        const currentSarana = $('#sarana').val();
        
        if (npsn) {
            $.get('sarpras_check_data.php', { npsn: npsn }, function(data) {
                const existingData = JSON.parse(data);
                
                // Reset opsi sarana
                $('#sarana').html('<option value="">--Pilih Sarana--</option>');
                
                // Tambahkan opsi yang belum ada atau sedang diedit
                let hasRuangKelas = false;
                let hasRuangLab = false;
                let hasRuangPerpus = false;
                let hasRuangGuru = false;
                let hasRuangKepalaSekolah = false;
                let hasRuangTU = false;
                let hasRuangUKS = false;
                let hasRuangToilet = false;
                let hasRuangGudang = false;
                let hasLainnya = false;
                
                existingData.data.forEach(function(item) {
                    const normalized = normalizeSarana(item.sarana);
                    if (normalized === normalizeSarana('Ruang Kelas')) hasRuangKelas = true;
                    if (normalized === normalizeSarana('Ruang Lab')) hasRuangLab = true;
                    if (normalized === normalizeSarana('Ruang Perpus')) hasRuangPerpus = true;
                    if (normalized === normalizeSarana('Ruang Guru')) hasRuangGuru = true;
                    if (normalized === normalizeSarana('Ruang Kepala Sekolah')) hasRuangKepalaSekolah = true;
                    if (normalized === normalizeSarana('Ruang TU')) hasRuangTU = true;
                    if (normalized === normalizeSarana('Ruang UKS')) hasRuangUKS = true;
                    if (normalized === normalizeSarana('Ruang Toilet')) hasRuangToilet = true;
                    if (normalized === normalizeSarana('Ruang Gudang')) hasRuangGudang = true;
                    if (normalized === normalizeSarana('Lainnya')) hasLainnya = true;
                });
                
                // Jika sedang mengedit data Ruang Kelas, maka opsi Ruang Kelas harus tetap tersedia
                if (normalizeSarana(currentSarana) === normalizeSarana('Ruang Kelas')) {
                    $('#sarana').append('<option value="Ruang Kelas" selected>Ruang Kelas</option>');
                } else if (!hasRuangKelas) {
                    $('#sarana').append('<option value="Ruang Kelas">Ruang Kelas</option>');
                }
                
                // Jika sedang mengedit data Ruang Lab, maka opsi Ruang Lab harus tetap tersedia
                if (normalizeSarana(currentSarana) === normalizeSarana('Ruang Lab')) {
                    $('#sarana').append('<option value="Ruang Lab" selected>Ruang Lab</option>');
                } else if (!hasRuangLab) {
                    $('#sarana').append('<option value="Ruang Lab">Ruang Lab</option>');
                }
                
                // Jika sedang mengedit data Ruang Perpus, maka opsi Ruang Perpus harus tetap tersedia
                if (normalizeSarana(currentSarana) === normalizeSarana('Ruang Perpus')) {
                    $('#sarana').append('<option value="Ruang Perpus" selected>Ruang Perpus</option>');
                } else if (!hasRuangPerpus) {
                    $('#sarana').append('<option value="Ruang Perpus">Ruang Perpus</option>');
                }
                
                // Jika sedang mengedit data Ruang Guru, maka opsi Ruang Guru harus tetap tersedia
                if (normalizeSarana(currentSarana) === normalizeSarana('Ruang Guru')) {
                    $('#sarana').append('<option value="Ruang Guru" selected>Ruang Guru</option>');
                } else if (!hasRuangGuru) {
                    $('#sarana').append('<option value="Ruang Guru">Ruang Guru</option>');
                }
                
                // Jika sedang mengedit data Ruang Kepala Sekolah, maka opsi Ruang Kepala Sekolah harus tetap tersedia
                if (normalizeSarana(currentSarana) === normalizeSarana('Ruang Kepala Sekolah')) {
                    $('#sarana').append('<option value="Ruang Kepala Sekolah" selected>Ruang Kepala Sekolah</option>');
                } else if (!hasRuangKepalaSekolah) {
                    $('#sarana').append('<option value="Ruang Kepala Sekolah">Ruang Kepala Sekolah</option>');
                }
                
                // Jika sedang mengedit data Ruang TU, maka opsi Ruang TU harus tetap tersedia
                if (normalizeSarana(currentSarana) === normalizeSarana('Ruang TU')) {
                    $('#sarana').append('<option value="Ruang TU" selected>Ruang TU</option>');
                } else if (!hasRuangTU) {
                    $('#sarana').append('<option value="Ruang TU">Ruang TU</option>');
                }
                
                // Jika sedang mengedit data Ruang UKS, maka opsi Ruang UKS harus tetap tersedia
                if (normalizeSarana(currentSarana) === normalizeSarana('Ruang UKS')) {
                    $('#sarana').append('<option value="Ruang UKS" selected>Ruang UKS</option>');
                } else if (!hasRuangUKS) {
                    $('#sarana').append('<option value="Ruang UKS">Ruang UKS</option>');
                }
                
                // Jika sedang mengedit data Ruang Toilet, maka opsi Ruang Toilet harus tetap tersedia
                if (normalizeSarana(currentSarana) === normalizeSarana('Ruang Toilet')) {
                    $('#sarana').append('<option value="Ruang Toilet" selected>Ruang Toilet</option>');
                } else if (!hasRuangToilet) {
                    $('#sarana').append('<option value="Ruang Toilet">Ruang Toilet</option>');
                }
                
                // Jika sedang mengedit data Ruang Gudang, maka opsi Ruang Gudang harus tetap tersedia
                if (normalizeSarana(currentSarana) === normalizeSarana('Ruang Gudang')) {
                    $('#sarana').append('<option value="Ruang Gudang" selected>Ruang Gudang</option>');
                } else if (!hasRuangGudang) {
                    $('#sarana').append('<option value="Ruang Gudang">Ruang Gudang</option>');
                }
                
                // Jika sedang mengedit data Lainnya, maka opsi Lainnya harus tetap tersedia
                if (normalizeSarana(currentSarana) === normalizeSarana('Lainnya')) {
                    $('#sarana').append('<option value="Lainnya" selected>Lainnya</option>');
                } else if (!hasLainnya) {
                    $('#sarana').append('<option value="Lainnya">Lainnya</option>');
                }
            });
        }
    });
    
    // Validasi sebelum submit
    $('#formEditData').submit(function(e) {
        e.preventDefault();
        
        const npsn = $('#npsn_fk').val();
        const sarana = $('#sarana').val();
        const id = $('input[name="id"]').val();
        
        // Cek duplikasi sebelum submit
        $.ajax({
            url: 'sarpras_check_data.php',
            type: 'GET',
            data: { 
                npsn: npsn,
                sarana: sarana
            },
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success') {
                    if (response.exists) {
                        // Cek apakah ini data yang sedang diedit
                        $.ajax({
                            url: 'sarpras_check_data.php',
                            type: 'GET',
                            data: { 
                                npsn: npsn,
                                sarana: sarana
                            },
                            dataType: 'json',
                            success: function(checkResponse) {
                                // Jika data sudah ada, cek apakah ini data yang sedang diedit
                                $.get('sarpras_detail_inline.php?npsn=' + npsn, function(detailData) {
                                    // Cek apakah ada data dengan sarana yang sama
                                    let isCurrentData = false;
                                    
                                    $(detailData).find('tbody tr').each(function() {
                                        const rowSarana = $(this).find('td:first span').text().trim();
                                        if (normalizeSarana(rowSarana) === normalizeSarana(sarana)) {
                                            // Cek apakah ada tombol edit dengan ID yang sama
                                            const editButton = $(this).find('a[href*="sarpras_edit.php?id=' + id + '"]');
                                            if (editButton.length > 0) {
                                                isCurrentData = true;
                                            }
                                        }
                                    });
                                    
                                    if (isCurrentData) {
                                        // Lanjutkan submit form
                                        Swal.fire({
                                            title: 'Konfirmasi',
                                            text: "Apakah Anda yakin ingin mengubah data ini?",
                                            icon: 'question',
                                            showCancelButton: true,
                                            confirmButtonColor: '#3085d6',
                                            cancelButtonColor: '#d33',
                                            confirmButtonText: 'Ya, ubah!',
                                            cancelButtonText: 'Batal'
                                        }).then((result) => {
                                            if (result.isConfirmed) {
                                                $('#formEditData').off('submit').submit();
                                            }
                                        });
                                    } else {
                                        // Tampilkan pesan error jika duplikat
                                        Swal.fire({
                                            icon: 'error',
                                            title: 'Duplikasi Data',
                                            text: 'Data untuk sarana "' + sarana + '" di sekolah ini sudah ada.'
                                        });
                                    }
                                });
                            }
                        });
                    } else {
                        // Lanjutkan submit form
                        Swal.fire({
                            title: 'Konfirmasi',
                            text: "Apakah Anda yakin ingin mengubah data ini?",
                            icon: 'question',
                            showCancelButton: true,
                            confirmButtonColor: '#3085d6',
                            cancelButtonColor: '#d33',
                            confirmButtonText: 'Ya, ubah!',
                            cancelButtonText: 'Batal'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                $('#formEditData').off('submit').submit();
                            }
                        });
                    }
                } else {
                    // Tampilkan pesan error jika ada masalah
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: response.message || 'Terjadi kesalahan saat mengecek data'
                    });
                }
            },
            error: function(xhr, status, error) {
                console.error('Error:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Gagal terhubung ke server'
                });
            }
        });
    });
});
</script>