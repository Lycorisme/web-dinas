<?php
require_once '../layout/_top.php';
?>

<section class="section">
    <div class="section-header d-flex justify-content-between align-items-center">
        <h1>Tambah User Baru</h1>
    </div>

    <div class="row">
        <div class="col-12 col-md-8 col-lg-6 mx-auto">
            <div class="card">
                <div class="card-header">
                    <h4>Form Tambah User</h4>
                </div>
                <div class="card-body">
                    
                    <form action="proses_tambah.php" method="POST">
                        <div class="form-group">
                            <label>Email <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <div class="input-group-text">
                                        <i class="fas fa-user"></i>
                                    </div>
                                </div>
                                <input type="text" class="form-control" name="username" required>
                            </div>
                            <small class="form-text text-muted">Email akan digunakan untuk login</small>
                        </div>
                        
                        <div class="form-group">
                            <label>Password <span class="text-danger">*</span></label>
                            <div class="input-group password-input-group">
                                <div class="input-group-prepend">
                                    <div class="input-group-text">
                                        <i class="fas fa-lock"></i>
                                    </div>
                                </div>
                                <input type="password" class="form-control" name="password" id="passwordInput" required>
                                <button class="password-toggle-btn" type="button" id="togglePassword" tabindex="-1">
                                    <i class="fas fa-eye" id="toggleIcon"></i>
                                </button>
                            </div>
                            <small class="form-text text-muted">Minimal 6 karakter. Password akan dienkripsi secara otomatis</small>
                        </div>
                        
                        <div class="form-group">
                            <label>Nama Pengguna <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <div class="input-group-text">
                                        <i class="fas fa-id-badge"></i>
                                    </div>
                                </div>
                                <input type="text" class="form-control" name="nama_pengguna" required>
                            </div>
                            <small class="form-text text-muted">Nama lengkap pengguna</small>
                        </div>
                        
                        <div class="form-group">
                            <label>Role</label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <div class="input-group-text">
                                        <i class="fas fa-user-shield"></i>
                                    </div>
                                </div>
                                <select class="form-control" name="role" disabled>
                                    <option value="admin" selected>Admin</option>
                                </select>
                            </div>
                            <small class="form-text text-muted">Hanya role admin yang tersedia</small>
                        </div>
                        
                        <div class="form-group mt-4">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="fas fa-save mr-2"></i> Simpan User
                            </button>
                            <a href="index.php" class="btn btn-danger btn-lg ml-2">
                                <i class="fas fa-times mr-2"></i> Kembali
                            </a>
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
 $(document).ready(function() {
    // Add validation for password length
    $('input[name="password"]').on('input', function() {
        if ($(this).val().length < 6) {
            $(this).addClass('is-invalid');
        } else {
            $(this).removeClass('is-invalid');
        }
    });
    
    // Add validation for form submission
    $('form').on('submit', function(e) {
        if ($('input[name="password"]').val().length < 6) {
            e.preventDefault();
            iziToast.error({
                title: 'Error',
                message: 'Password minimal 6 karakter!',
                position: 'topCenter'
            });
        }
    });
    
    // Toggle password visibility with modern animation
    $("#togglePassword").click(function() {
        var passwordInput = $("#passwordInput");
        var passwordIcon = $("#toggleIcon");
        var toggleButton = $(this);
        
        if (passwordInput.attr("type") === "password") {
            passwordInput.attr("type", "text");
            passwordIcon.removeClass("fa-eye");
            passwordIcon.addClass("fa-eye-slash");
            toggleButton.addClass('active');
        } else {
            passwordInput.attr("type", "password");
            passwordIcon.removeClass("fa-eye-slash");
            passwordIcon.addClass("fa-eye");
            toggleButton.removeClass('active');
        }
    });
});
</script>

<?php
// Notifikasi iziToast
if (isset($_SESSION['info'])) :
  if ($_SESSION['info']['status'] == 'success') {
?>
    <script>
      iziToast.success({
        title: 'Sukses',
        message: `<?= $_SESSION['info']['message'] ?>`,
        position: 'topCenter',
        timeout: 5000,
        icon: 'fas fa-check-circle',
        backgroundColor: '#1cc88a',
        progressBarColor: '#0f6848'
      });
    </script>
  <?php
  } else {
  ?>
    <script>
      iziToast.error({
        title: 'Gagal',
        message: `<?= $_SESSION['info']['message'] ?>`,
        timeout: 5000,
        position: 'topCenter',
        icon: 'fas fa-exclamation-circle',
        backgroundColor: '#e74a3b',
        progressBarColor: '#a02622'
      });
    </script>
<?php
  }
  unset($_SESSION['info']);
endif;
?>

<?php
// Handle error parameter
if (isset($_GET['error'])) :
    $error = $_GET['error'];
    $message = '';
    
    switch ($error) {
        case 1:
            $message = 'Password minimal 6 karakter!';
            break;
        case 2:
            $message = 'Email sudah digunakan!';
            break;
        default:
            $message = 'Terjadi kesalahan. Silakan coba lagi!';
    }
?>
    <script>
      iziToast.error({
        title: 'Gagal',
        message: `<?= $message ?>`,
        timeout: 5000,
        position: 'topCenter',
        icon: 'fas fa-exclamation-circle',
        backgroundColor: '#e74a3b',
        progressBarColor: '#a02622'
      });
    </script>
<?php
endif;
?>