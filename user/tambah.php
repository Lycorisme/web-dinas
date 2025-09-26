<?php
require_once '../layout/_top.php';
?>

<section class="section">
    <div class="section-header d-flex justify-content-between align-items-center">
        <h1>Tambah User Baru</h1>
        <div class="d-flex align-items-center">
            <div class="breadcrumb-item mr-3">
                <a href="../dashboard/index.php">Dashboard</a>
                <i class="fas fa-chevron-right mx-2"></i>
                <a href="index.php">User Management</a>
                <i class="fas fa-chevron-right mx-2"></i>
                <span>Tambah User</span>
            </div>
            <a href="index.php" class="btn btn-light">
                <i class="fas fa-arrow-left mr-2"></i>Kembali
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-12 col-md-8 col-lg-6 mx-auto">
            <div class="card">
                <div class="card-header">
                    <h4>Form Tambah User</h4>
                </div>
                <div class="card-body">
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle mr-2"></i>
                        <strong>Perhatian:</strong> Password akan disimpan sebagai plain text (tanpa enkripsi). 
                        Pastikan menggunakan password yang kuat dan unik.
                    </div>
                    
                    <form action="proses_tambah.php" method="POST">
                        <div class="form-group">
                            <label>Username <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <div class="input-group-text">
                                        <i class="fas fa-user"></i>
                                    </div>
                                </div>
                                <input type="text" class="form-control" name="username" required>
                            </div>
                            <small class="form-text text-muted">Username akan digunakan untuk login</small>
                        </div>
                        
                        <div class="form-group">
                            <label>Password <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <div class="input-group-text">
                                        <i class="fas fa-lock"></i>
                                    </div>
                                </div>
                                <input type="text" class="form-control" name="password" required>
                            </div>
                            <small class="form-text text-muted">Minimal 6 karakter, disimpan sebagai plain text</small>
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
                            <a href="index.php" class="btn btn-light btn-lg ml-2">
                                <i class="fas fa-times mr-2"></i> Batal
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
});
</script>