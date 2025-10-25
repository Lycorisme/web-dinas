<?php
require_once '../layout/_top.php';
require_once '../helper/connection.php';

 $id = $_GET['id'];
 $query = mysqli_query($connection, "SELECT r.*, s.nama_sekolah 
                                   FROM rekap_ptk_pd r
                                   LEFT JOIN sekolah_identitas s ON r.npsn_fk = s.npsn
                                   WHERE r.id='$id'");
 $sekolah = mysqli_query($connection, "SELECT npsn, nama_sekolah FROM sekolah_identitas ORDER BY nama_sekolah");
?>

<section class="section">
  <div class="section-header d-flex justify-content-between">
    <h1>Ubah Data PTK & Peserta Didik</h1>
  </div>
  <div class="row">
    <div class="col-12">
      <div class="card">
        <div class="card-body">
          <?php
          while ($row = mysqli_fetch_array($query)) {
          ?>
          <form action="./ptk_pd_update.php" method="post" id="formEditData">
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
                  <label>Deskripsi</label>
                  <select class="form-control" name="deskripsi" id="deskripsi" required>
                    <option value="">--Pilih Deskripsi--</option>
                    <option value="Laki-laki" <?= $row['deskripsi'] == 'Laki-laki' ? 'selected' : '' ?>>Laki-laki</option>
                    <option value="Perempuan" <?= $row['deskripsi'] == 'Perempuan' ? 'selected' : '' ?>>Perempuan</option>
                  </select>
                </div>
                
                <div class="form-group">
                  <label>Jumlah Guru</label>
                  <input class="form-control" type="number" name="guru" min="0" value="<?= $row['guru'] ?>" required>
                </div>
              </div>
              
              <div class="col-md-6">
                <div class="form-group">
                  <label>Jumlah Tendik</label>
                  <input class="form-control" type="number" name="tendik" min="0" value="<?= $row['tendik'] ?>" required>
                </div>
                
                <div class="form-group">
                  <label>Total PTK</label>
                  <input class="form-control" type="number" name="ptk_total" min="0" value="<?= $row['ptk_total'] ?>" required>
                </div>
                
                <div class="form-group">
                  <label>Total Peserta Didik</label>
                  <input class="form-control" type="number" name="pd_total" min="0" value="<?= $row['pd_total'] ?>" required>
                </div>
              </div>
            </div>
            
            <div class="form-group">
              <input class="btn btn-primary" type="submit" name="proses" value="Simpan">
              <a href="./ptk_pd.php" class="btn btn-danger ml-1">Batal</a>
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
// Fungsi normalisasi deskripsi di JavaScript
function normalizeDeskripsi(str) {
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
        const currentDeskripsi = $('#deskripsi').val();
        
        if (npsn) {
            $.get('ptk_pd_check_data.php', { npsn: npsn }, function(data) {
                const existingData = JSON.parse(data);
                
                // Reset opsi deskripsi
                $('#deskripsi').html('<option value="">--Pilih Deskripsi--</option>');
                
                // Tambahkan opsi yang belum ada atau sedang diedit
                let hasLaki = false;
                let hasPerempuan = false;
                
                existingData.data.forEach(function(item) {
                    const normalized = normalizeDeskripsi(item.deskripsi);
                    if (normalized === normalizeDeskripsi('Laki-laki')) hasLaki = true;
                    if (normalized === normalizeDeskripsi('Perempuan')) hasPerempuan = true;
                });
                
                // Jika sedang mengedit data Laki-laki, maka opsi Laki-laki harus tetap tersedia
                if (normalizeDeskripsi(currentDeskripsi) === normalizeDeskripsi('Laki-laki')) {
                    $('#deskripsi').append('<option value="Laki-laki" selected>Laki-laki</option>');
                } else if (!hasLaki) {
                    $('#deskripsi').append('<option value="Laki-laki">Laki-laki</option>');
                }
                
                // Jika sedang mengedit data Perempuan, maka opsi Perempuan harus tetap tersedia
                if (normalizeDeskripsi(currentDeskripsi) === normalizeDeskripsi('Perempuan')) {
                    $('#deskripsi').append('<option value="Perempuan" selected>Perempuan</option>');
                } else if (!hasPerempuan) {
                    $('#deskripsi').append('<option value="Perempuan">Perempuan</option>');
                }
            });
        }
    });
    
    // Validasi sebelum submit
    $('#formEditData').submit(function(e) {
        e.preventDefault();
        
        const npsn = $('#npsn_fk').val();
        const deskripsi = $('#deskripsi').val();
        const id = $('input[name="id"]').val();
        
        // Cek duplikasi sebelum submit
        $.ajax({
            url: 'ptk_pd_check_data.php',
            type: 'GET',
            data: { 
                npsn: npsn,
                deskripsi: deskripsi
            },
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success') {
                    if (response.exists) {
                        // Cek apakah ini data yang sedang diedit
                        $.ajax({
                            url: 'ptk_pd_check_data.php',
                            type: 'GET',
                            data: { 
                                npsn: npsn,
                                deskripsi: deskripsi
                            },
                            dataType: 'json',
                            success: function(checkResponse) {
                                // Jika data sudah ada, cek apakah ini data yang sedang diedit
                                $.get('ptk_pd_detail_inline.php?npsn=' + npsn, function(detailData) {
                                    // Cek apakah ada data dengan deskripsi yang sama
                                    let isCurrentData = false;
                                    
                                    $(detailData).find('tbody tr').each(function() {
                                        const rowDeskripsi = $(this).find('td:first span').text().trim();
                                        if (normalizeDeskripsi(rowDeskripsi) === normalizeDeskripsi(deskripsi)) {
                                            // Cek apakah ada tombol edit dengan ID yang sama
                                            const editButton = $(this).find('a[href*="ptk_pd_edit.php?id=' + id + '"]');
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
                                            text: 'Data untuk jenis kelamin "' + deskripsi + '" di sekolah ini sudah ada.'
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