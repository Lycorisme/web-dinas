<?php
require_once '../layout/_top.php';
require_once '../helper/connection.php';

 $sekolah = mysqli_query($connection, "SELECT npsn, nama_sekolah FROM sekolah_identitas ORDER BY nama_sekolah");
 $npsn_default = isset($_GET['npsn']) ? $_GET['npsn'] : '';
?>

<section class="section">
  <div class="section-header d-flex justify-content-between">
    <h1>Tambah Data Sarana Prasarana</h1>
  </div>
  <div class="row">
    <div class="col-12">
      <div class="card">
        <div class="card-body">
          <form action="./sarpras_store.php" method="POST" id="formTambahData">
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
                  <label>Sarana</label>
                  <select class="form-control" name="sarana" id="sarana" required>
                    <option value="">--Pilih Sarana--</option>
                    <option value="Ruang Kelas">Ruang Kelas</option>
                    <option value="Ruang Lab">Ruang Lab</option>
                    <option value="Ruang Perpus">Ruang Perpus</option>
                    <option value="Ruang Guru">Ruang Guru</option>
                    <option value="Ruang Kepala Sekolah">Ruang Kepala Sekolah</option>
                    <option value="Ruang TU">Ruang TU</option>
                    <option value="Ruang UKS">Ruang UKS</option>
                    <option value="Ruang Toilet">Ruang Toilet</option>
                    <option value="Ruang Gudang">Ruang Gudang</option>
                    <option value="Lainnya">Lainnya</option>
                  </select>
                </div>
              </div>
              
              <div class="col-md-6">
                <div class="form-group">
                  <label>Jumlah</label>
                  <input class="form-control" type="number" name="jumlah" min="0" value="0" required>
                </div>
              </div>
            </div>
            
            <div class="form-group">
              <input class="btn btn-primary" type="submit" name="proses" value="Simpan">
              <a href="./sarpras.php" class="btn btn-danger">Kembali</a>
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
        if (npsn) {
            $.get('sarpras_check_data.php', { npsn: npsn }, function(data) {
                const response = JSON.parse(data);
                
                // Reset opsi sarana
                $('#sarana').html('<option value="">--Pilih Sarana--</option>');
                
                // Tambahkan opsi yang belum ada
                let hasRuangKelas = response.hasRuangKelas || false;
                let hasRuangLab = response.hasRuangLab || false;
                let hasRuangPerpus = response.hasRuangPerpus || false;
                let hasRuangGuru = response.hasRuangGuru || false;
                let hasRuangKepalaSekolah = response.hasRuangKepalaSekolah || false;
                let hasRuangTU = response.hasRuangTU || false;
                let hasRuangUKS = response.hasRuangUKS || false;
                let hasRuangToilet = response.hasRuangToilet || false;
                let hasRuangGudang = response.hasRuangGudang || false;
                let hasLainnya = response.hasLainnya || false;
                
                if (!hasRuangKelas) {
                    $('#sarana').append('<option value="Ruang Kelas">Ruang Kelas</option>');
                }
                
                if (!hasRuangLab) {
                    $('#sarana').append('<option value="Ruang Lab">Ruang Lab</option>');
                }
                
                if (!hasRuangPerpus) {
                    $('#sarana').append('<option value="Ruang Perpus">Ruang Perpus</option>');
                }
                
                if (!hasRuangGuru) {
                    $('#sarana').append('<option value="Ruang Guru">Ruang Guru</option>');
                }
                
                if (!hasRuangKepalaSekolah) {
                    $('#sarana').append('<option value="Ruang Kepala Sekolah">Ruang Kepala Sekolah</option>');
                }
                
                if (!hasRuangTU) {
                    $('#sarana').append('<option value="Ruang TU">Ruang TU</option>');
                }
                
                if (!hasRuangUKS) {
                    $('#sarana').append('<option value="Ruang UKS">Ruang UKS</option>');
                }
                
                if (!hasRuangToilet) {
                    $('#sarana').append('<option value="Ruang Toilet">Ruang Toilet</option>');
                }
                
                if (!hasRuangGudang) {
                    $('#sarana').append('<option value="Ruang Gudang">Ruang Gudang</option>');
                }
                
                if (!hasLainnya) {
                    $('#sarana').append('<option value="Lainnya">Lainnya</option>');
                }
                
                // Jika semua opsi sudah ada
                if (hasRuangKelas && hasRuangLab && hasRuangPerpus && hasRuangGuru && hasRuangKepalaSekolah && hasRuangTU && hasRuangUKS && hasRuangToilet && hasRuangGudang && hasLainnya) {
                    $('#sarana').html('<option value="">Semua sarana sudah ada untuk sekolah ini</option>');
                    $('#sarana').prop('disabled', true);
                    $('input[type="submit"]').prop('disabled', true);
                    
                    // Tampilkan pesan
                    Swal.fire({
                        icon: 'info',
                        title: 'Informasi',
                        text: 'Sekolah ini sudah memiliki data untuk semua jenis sarana. Gunakan fitur edit untuk mengubah data yang sudah ada.'
                    });
                } else {
                    $('#sarana').prop('disabled', false);
                    $('input[type="submit"]').prop('disabled', false);
                }
            });
        } else {
            $('#sarana').html('<option value="">--Pilih Sarana--</option><option value="Ruang Kelas">Ruang Kelas</option><option value="Ruang Lab">Ruang Lab</option><option value="Ruang Perpus">Ruang Perpus</option><option value="Ruang Guru">Ruang Guru</option><option value="Ruang Kepala Sekolah">Ruang Kepala Sekolah</option><option value="Ruang TU">Ruang TU</option><option value="Ruang UKS">Ruang UKS</option><option value="Ruang Toilet">Ruang Toilet</option><option value="Ruang Gudang">Ruang Gudang</option><option value="Lainnya">Lainnya</option>');
            $('#sarana').prop('disabled', false);
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
        const sarana = $('#sarana').val();
        
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
                        // Tampilkan pesan error jika duplikat
                        Swal.fire({
                            icon: 'error',
                            title: 'Duplikasi Data',
                            text: 'Data untuk sarana "' + response.sarana + '" di sekolah ini sudah ada!'
                        });
                    } else if (response.totalCount >= 10) {
                        // Tampilkan pesan error jika sudah ada 10 data
                        Swal.fire({
                            icon: 'error',
                            title: 'Data Lengkap',
                            text: 'Sekolah ini sudah memiliki 10 jenis sarana. Tidak bisa menambah data lagi.'
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