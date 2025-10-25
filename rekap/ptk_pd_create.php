<?php
require_once '../layout/_top.php';
require_once '../helper/connection.php';

 $sekolah = mysqli_query($connection, "SELECT npsn, nama_sekolah FROM sekolah_identitas ORDER BY nama_sekolah");
 $npsn_default = isset($_GET['npsn']) ? $_GET['npsn'] : '';
?>

<section class="section">
  <div class="section-header d-flex justify-content-between">
    <h1>Tambah Data PTK & Peserta Didik</h1>
  </div>
  <div class="row">
    <div class="col-12">
      <div class="card">
        <div class="card-body">
          <form action="./ptk_pd_store.php" method="POST" id="formTambahData">
            <div class="row">
              <div class="col-md-6">
                <div class="form-group">
                  <label>Sekolah</label>
                  <select class="form-control" name="npsn_fk" id="npsn_fk" required>
                    <option value="">--Pilih Sekolah--</option>
                    <?php while ($sch = mysqli_fetch_array($sekolah)) : ?>
                      <option value="<?= $sch['npsn'] ?>" <?= $sch['npsn'] == $npsn_default ? 'selected' : '' ?>>
                        <?= $sch['npsn'] ?> - <?= $sch['nama_sekolah'] ?>
                      </option>
                    <?php endwhile; ?>
                  </select>
                </div>
                
                <div class="form-group">
                  <label>Deskripsi</label>
                  <select class="form-control" name="deskripsi" id="deskripsi" required>
                    <option value="">--Pilih Deskripsi--</option>
                    <option value="Laki-laki">Laki-laki</option>
                    <option value="Perempuan">Perempuan</option>
                  </select>
                </div>
                
                <div class="form-group">
                  <label>Jumlah Guru</label>
                  <input class="form-control" type="number" name="guru" min="0" value="0" required>
                </div>
              </div>
              
              <div class="col-md-6">
                <div class="form-group">
                  <label>Jumlah Tendik</label>
                  <input class="form-control" type="number" name="tendik" min="0" value="0" required>
                </div>
                
                <div class="form-group">
                  <label>Total PTK</label>
                  <input class="form-control" type="number" name="ptk_total" min="0" value="0" required>
                </div>
                
                <div class="form-group">
                  <label>Total Peserta Didik</label>
                  <input class="form-control" type="number" name="pd_total" min="0" value="0" required>
                </div>
              </div>
            </div>
            
            <div class="form-group">
              <input class="btn btn-primary" type="submit" name="proses" value="Simpan">
              <a href="./ptk_pd.php" class="btn btn-danger">Kembali</a>
            </div>
          </form>
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
        if (npsn) {
            $.get('ptk_pd_check_data.php', { npsn: npsn }, function(data) {
                const response = JSON.parse(data);
                
                // Reset opsi deskripsi
                $('#deskripsi').html('<option value="">--Pilih Deskripsi--</option>');
                
                // Tambahkan opsi yang belum ada
                let hasLaki = response.hasLaki || false;
                let hasPerempuan = response.hasPerempuan || false;
                
                if (!hasLaki) {
                    $('#deskripsi').append('<option value="Laki-laki">Laki-laki</option>');
                }
                
                if (!hasPerempuan) {
                    $('#deskripsi').append('<option value="Perempuan">Perempuan</option>');
                }
                
                // Jika semua opsi sudah ada
                if (hasLaki && hasPerempuan) {
                    $('#deskripsi').html('<option value="">Semua data gender sudah ada untuk sekolah ini</option>');
                    $('#deskripsi').prop('disabled', true);
                    $('input[type="submit"]').prop('disabled', true);
                    
                    // Tampilkan pesan
                    Swal.fire({
                        icon: 'info',
                        title: 'Informasi',
                        text: 'Sekolah ini sudah memiliki data untuk Laki-laki dan Perempuan. Gunakan fitur edit untuk mengubah data yang sudah ada.'
                    });
                } else {
                    $('#deskripsi').prop('disabled', false);
                    $('input[type="submit"]').prop('disabled', false);
                }
            });
        } else {
            $('#deskripsi').html('<option value="">--Pilih Deskripsi--</option><option value="Laki-laki">Laki-laki</option><option value="Perempuan">Perempuan</option>');
            $('#deskripsi').prop('disabled', false);
            $('input[type="submit"]').prop('disabled', false);
        }
    });
    
    // Trigger change jika ada npsn default
    if ($('#npsn_fk').val()) {
        $('#npsn_fk').trigger('change');
    }
    
    // Validasi sebelum submit
    $('#formTambahData').submit(function(e) {
        e.preventDefault();
        
        const npsn = $('#npsn_fk').val();
        const deskripsi = $('#deskripsi').val();
        
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
                        // Tampilkan pesan error jika duplikat
                        Swal.fire({
                            icon: 'error',
                            title: 'Duplikasi Data',
                            text: 'Data untuk jenis kelamin "' + response.deskripsi + '" di sekolah ini sudah ada!'
                        });
                    } else if (response.totalCount >= 2) {
                        // Tampilkan pesan error jika sudah ada 2 data
                        Swal.fire({
                            icon: 'error',
                            title: 'Data Lengkap',
                            text: 'Sekolah ini sudah memiliki 2 jenis kelamin. Tidak bisa menambah data lagi.'
                        });
                    } else {
                        // Lanjutkan submit form jika tidak duplikat
                        Swal.fire({
                            title: 'Konfirmasi',
                            text: "Apakah Anda yakin ingin menyimpan data ini?",
                            icon: 'question',
                            showCancelButton: true,
                            confirmButtonColor: '#3085d6',
                            cancelButtonColor: '#d33',
                            confirmButtonText: 'Ya, simpan!',
                            cancelButtonText: 'Batal'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                $('#formTambahData').off('submit').submit();
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