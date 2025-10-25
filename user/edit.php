<?php
require_once '../layout/_top.php';
require_once '../helper/connection.php';

 $id = $_GET['id'];

// Cek apakah user yang sedang login bukan ID 1 dan mencoba mengedit user ID 1
if ($_SESSION['login']['id'] != 1 && $id == 1) {
    $_SESSION['info'] = [
        'status' => 'error',
        'message' => 'Anda tidak memiliki izin untuk mengedit user ini!'
    ];
    header("Location: index.php");
    exit;
}

 $query = "SELECT * FROM login WHERE id = $id";
 $user = mysqli_query($connection, $query);
 $data = mysqli_fetch_array($user);
?>

<section class="section">
    <div class="section-header d-flex justify-content-between align-items-center">
        <h1>Edit User</h1>
        <div class="d-flex align-items-center">
            <?php if ($id == 1): ?>
                <span class="badge badge-warning p-2">
                    <i class="fas fa-shield-alt mr-1"></i> Super Admin
                </span>
            <?php endif; ?>
        </div>
    </div>

    <div class="row">
        <div class="col-12 col-md-8 col-lg-6 mx-auto">
            <div class="card">
                <div class="card-header">
                    <h4>Edit User</h4>
                </div>
                <div class="card-body">
                    
                    <form action="proses_edit.php" method="POST">
                        <input type="hidden" name="id" value="<?= $data['id'] ?>">
                        
                        <div class="form-group">
                            <label>Email <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <div class="input-group-text">
                                        <i class="fas fa-user"></i>
                                    </div>
                                </div>
                                <input type="text" class="form-control" name="username" value="<?= $data['username'] ?>" required 
                                    <?php if ($id == 1 && $_SESSION['login']['id'] != 1): ?> readonly <?php endif; ?>>
                            </div>
                            <?php if ($id == 1 && $_SESSION['login']['id'] != 1): ?>
                                <small class="form-text text-muted">Email super admin tidak dapat diubah</small>
                            <?php endif; ?>
                        </div>
                        
                        <div class="form-group">
                            <label>Password</label>
                            <div class="input-group password-input-group">
                                <div class="input-group-prepend">
                                    <div class="input-group-text">
                                        <i class="fas fa-lock"></i>
                                    </div>
                                </div>
                                <input type="password" class="form-control" name="password" id="passwordInput"
                                    <?php if ($id == 1 && $_SESSION['login']['id'] != 1): ?> readonly <?php endif; ?>>
                                <button class="password-toggle-btn" type="button" id="togglePassword" tabindex="-1"
                                    <?php if ($id == 1 && $_SESSION['login']['id'] != 1): ?> disabled <?php endif; ?>>
                                    <i class="fas fa-eye" id="toggleIcon"></i>
                                </button>
                            </div>
                            <small class="form-text text-muted">Kosongkan jika tidak ingin mengubah password. Password akan dienkripsi secara otomatis</small>
                            <?php if ($id == 1 && $_SESSION['login']['id'] != 1): ?>
                                <small class="form-text text-danger">Password super admin tidak dapat diubah</small>
                            <?php endif; ?>
                        </div>
                        
                        <div class="form-group">
                            <label>Nama Pengguna <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <div class="input-group-text">
                                        <i class="fas fa-id-badge"></i>
                                    </div>
                                </div>
                                <input type="text" class="form-control" name="nama_pengguna" value="<?= $data['nama_pengguna'] ?>" required>
                            </div>
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
                            <button type="submit" class="btn btn-primary btn-lg" 
                                <?php if ($id == 1 && $_SESSION['login']['id'] != 1): ?> disabled <?php endif; ?>>
                                <i class="fas fa-save mr-2"></i> Update User
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
    // Add validation for password length if filled
    $('input[name="password"]').on('input', function() {
        if ($(this).val().length > 0 && $(this).val().length < 6) {
            $(this).addClass('is-invalid');
        } else {
            $(this).removeClass('is-invalid');
        }
    });
    
    // Add validation for form submission
    $('form').on('submit', function(e) {
        var password = $('input[name="password"]').val();
        if (password.length > 0 && password.length < 6) {
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