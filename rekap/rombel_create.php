<?php
require_once '../layout/_top.php';
require_once '../helper/connection.php';

 $sekolah = mysqli_query($connection, "SELECT npsn, nama_sekolah, jenjang_pendidikan FROM sekolah_identitas ORDER BY nama_sekolah");
 $npsn_default = isset($_GET['npsn']) ? $_GET['npsn'] : '';
?>

<section class="section">
  <div class="section-header d-flex justify-content-between">
    <h1>Tambah Data Rombongan Belajar</h1>
  </div>
  <div class="row">
    <div class="col-12">
      <div class="card">
        <div class="card-body">
          <form action="./rombel_store.php" method="POST" id="formTambahData">
            <div class="row">
              <div class="col-md-6">
                <div class="form-group">
                  <label>Sekolah</label>
                  <select class="form-control" name="npsn_fk" id="npsn_fk" required>
                    <option value="">--Pilih Sekolah--</option>
                    <?php while ($sch = mysqli_fetch_array($sekolah)) : ?>
                      <option value="<?= $sch['npsn'] ?>" data-jenjang="<?= $sch['jenjang_pendidikan'] ?>" <?= $sch['npsn'] == $npsn_default ? 'selected' : '' ?>>
                        <?= $sch['npsn'] ?> - <?= $sch['nama_sekolah'] ?> (<?= $sch['jenjang_pendidikan'] ?>)
                      </option>
                    <?php endwhile; ?>
                  </select>
                </div>
                
                <div class="form-group">
                  <label>Tingkat Kelas</label>
                  <select class="form-control" name="tingkat_kelas" id="tingkat_kelas" required>
                    <option value="">--Pilih Tingkat Kelas--</option>
                  </select>
                  <small class="form-text text-muted">Opsi tingkat kelas akan muncul setelah memilih sekolah</small>
                </div>
              </div>
              
              <div class="col-md-6">
                <div class="form-group">
                  <label>Jumlah Laki-laki</label>
                  <input class="form-control" type="number" name="jumlah_laki_laki" id="jumlah_laki_laki" min="0" value="0" required>
                </div>
                
                <div class="form-group">
                  <label>Jumlah Perempuan</label>
                  <input class="form-control" type="number" name="jumlah_perempuan" id="jumlah_perempuan" min="0" value="0" required>
                </div>
                
                <div class="form-group">
                  <label>Jumlah Total</label>
                  <input class="form-control" type="number" name="jumlah_total" id="jumlah_total" min="0" value="0" readonly>
                  <small class="form-text text-muted">Otomatis terhitung dari Jumlah Laki-laki + Jumlah Perempuan</small>
                </div>
              </div>
            </div>
            
            <div class="form-group">
              <input class="btn btn-primary" type="submit" name="proses" value="Simpan">
              <a href="./rombel.php" class="btn btn-danger">Kembali</a>
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
// Fungsi normalisasi tingkat_kelas di JavaScript
function normalizeTingkatKelas(str) {
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

// Fungsi untuk menghitung total
function hitungTotal() {
    const laki = parseInt($('#jumlah_laki_laki').val()) || 0;
    const perempuan = parseInt($('#jumlah_perempuan').val()) || 0;
    $('#jumlah_total').val(laki + perempuan);
}

// Fungsi untuk mendapatkan opsi tingkat kelas berdasarkan jenjang
function getTingkatKelasOptions(jenjang) {
    let options = [];
    
    switch(jenjang) {
        case 'SD':
            options = [
                { value: 'Kelas 1', text: 'Kelas 1' },
                { value: 'Kelas 2', text: 'Kelas 2' },
                { value: 'Kelas 3', text: 'Kelas 3' },
                { value: 'Kelas 4', text: 'Kelas 4' },
                { value: 'Kelas 5', text: 'Kelas 5' },
                { value: 'Kelas 6', text: 'Kelas 6' }
            ];
            break;
        case 'SMP':
            options = [
                { value: 'Kelas 7', text: 'Kelas 7' },
                { value: 'Kelas 8', text: 'Kelas 8' },
                { value: 'Kelas 9', text: 'Kelas 9' }
            ];
            break;
        case 'SMA':
        case 'SMK':
            options = [
                { value: 'Kelas 10', text: 'Kelas 10' },
                { value: 'Kelas 11', text: 'Kelas 11' },
                { value: 'Kelas 12', text: 'Kelas 12' }
            ];
            break;
        default:
            options = [
                { value: 'Kelas 1', text: 'Kelas 1' },
                { value: 'Kelas 2', text: 'Kelas 2' },
                { value: 'Kelas 3', text: 'Kelas 3' },
                { value: 'Kelas 4', text: 'Kelas 4' },
                { value: 'Kelas 5', text: 'Kelas 5' },
                { value: 'Kelas 6', text: 'Kelas 6' }
            ];
    }
    
    return options;
}

 $(document).ready(function() {
    // Hitung total saat input berubah
    $('#jumlah_laki_laki, #jumlah_perempuan').on('input', function() {
        hitungTotal();
    });
    
    // Hitung total awal
    hitungTotal();
    
    // Cek data yang sudah ada saat sekolah dipilih
    $('#npsn_fk').change(function() {
        const npsn = $(this).val();
        const jenjang = $(this).find('option:selected').data('jenjang');
        
        // Reset opsi tingkat kelas
        $('#tingkat_kelas').html('<option value="">--Pilih Tingkat Kelas--</option>');
        
        if (npsn) {
            // Tambahkan opsi tingkat kelas berdasarkan jenjang
            const options = getTingkatKelasOptions(jenjang);
            options.forEach(option => {
                $('#tingkat_kelas').append(`<option value="${option.value}">${option.text}</option>`);
            });
            
            // Cek data yang sudah ada
            $.get('rombel_check_data.php', { npsn: npsn }, function(data) {
                const response = JSON.parse(data);
                
                // Tandai opsi yang sudah ada
                response.data.forEach(existingData => {
                    const normalizedExisting = normalizeTingkatKelas(existingData.tingkat_kelas);
                    $('#tingkat_kelas option').each(function() {
                        const normalizedOption = normalizeTingkatKelas($(this).val());
                        if (normalizedExisting === normalizedOption) {
                            $(this).prop('disabled', true);
                        }
                    });
                });
                
                // Jika semua opsi sudah ada
                if (response.hasAllTingkat) {
                    $('#tingkat_kelas').html('<option value="">Semua tingkat kelas sudah ada untuk sekolah ini</option>');
                    $('#tingkat_kelas').prop('disabled', true);
                    $('input[type="submit"]').prop('disabled', true);
                    
                    // Tampilkan pesan
                    Swal.fire({
                        icon: 'info',
                        title: 'Informasi',
                        text: 'Sekolah ini sudah memiliki data untuk semua tingkat kelas. Gunakan fitur edit untuk mengubah data yang sudah ada.'
                    });
                } else {
                    $('#tingkat_kelas').prop('disabled', false);
                    $('input[type="submit"]').prop('disabled', false);
                }
            });
        } else {
            // Tambahkan opsi default jika tidak ada sekolah yang dipilih
            const defaultOptions = getTingkatKelasOptions('SD');
            defaultOptions.forEach(option => {
                $('#tingkat_kelas').append(`<option value="${option.value}">${option.text}</option>`);
            });
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
        const tingkat_kelas = $('#tingkat_kelas').val();
        
        // Cek duplikasi sebelum submit
        $.ajax({
            url: 'rombel_check_data.php',
            type: 'GET',
            data: { 
                npsn: npsn,
                tingkat_kelas: tingkat_kelas
            },
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success') {
                    if (response.exists) {
                        // Tampilkan pesan error jika duplikat
                        Swal.fire({
                            icon: 'error',
                            title: 'Duplikasi Data',
                            text: 'Data untuk tingkat kelas "' + response.tingkat_kelas + '" di sekolah ini sudah ada!'
                        });
                    } else if (response.totalCount >= response.maxCount) {
                        // Tampilkan pesan error jika sudah mencapai batas
                        Swal.fire({
                            icon: 'error',
                            title: 'Data Lengkap',
                            text: 'Sekolah ini sudah memiliki semua tingkat kelas. Tidak bisa menambah data lagi.'
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