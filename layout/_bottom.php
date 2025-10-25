</div>
<footer class="main-footer">
  <div class="footer-left">
    Copyright &copy; BTIKP 2025. All rights reserved.</a>
  </div>
  <div class="footer-right">

  </div>
</footer>
</div>
</div>

<!-- General JS Scripts -->
<script src="../assets/vendor/jquery/jquery.min.js"></script>
<script src="../assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script> 
<script src="../assets/vendor/jquery-nicescroll/jquery.nicescroll.min.js"></script>
<script src="../assets/vendor/moment/moment.min.js"></script>
<script src="../assets/js/stisla.js"></script>

<!-- JS Libraies -->
<script src="../assets/modules/jquery.sparkline.min.js"></script>
<script src="../assets/modules/Chart.min.js"></script>
<script src="../assets/modules/owlcarousel2/dist/owl.carousel.min.js"></script>
<script src="../assets/modules/summernote/summernote-bs4.js"></script>
<script src="../assets/modules/chocolat/dist/js/jquery.chocolat.min.js"></script>
<script src="../assets/modules/datatables/datatables.min.js"></script>
<script src="../assets/modules/datatables/DataTables-1.10.16/js/dataTables.bootstrap4.min.js"></script>
<script src="../assets/modules/datatables/Select-1.2.4/js/dataTables.select.min.js"></script>
<script src="../assets/modules/jquery-ui/jquery-ui.min.js"></script>
<script src="../assets/modules/izitoast/js/iziToast.min.js"></script>
<script src="../assets/modules/sweetalert2/sweetalert2.min.js"></script>

<!-- Template JS File -->
<script src="../assets/js/scripts.js"></script>
<script src="../assets/js/custom.js"></script>

<!-- Page Specific JS File -->
<script src="../assets/js/page/index.js"></script>
<script src="../assets/vendor/izitoast/js/iziToast.min.js"></script>
<script src="../assets/vendor/datatables/js/jquery.dataTables.min.js"></script>
<script src="../assets/vendor/datatables/js/dataTables.bootstrap4.min.js"></script>
<script src="../assets/modules/sweetalert2/sweetalert2.min.js"></script> 
<script src="../assets/modules/jquery.sparkline.min.js"></script>

<!-- Custom Script for SweetAlert2 and iziToast -->
<!-- Custom Script for SweetAlert2 and iziToast -->
<script>
// Fungsi untuk menampilkan SweetAlert2 dari session
function showSweetAlertFromSession() {
    <?php if (isset($_SESSION['info']['swal'])): ?>
        Swal.fire({
            title: `<?= $_SESSION['info']['swal']['title'] ?>`,
            text: `<?= $_SESSION['info']['swal']['text'] ?>`,
            icon: `<?= $_SESSION['info']['swal']['icon'] ?>`,
            timer: 5000,
            timerProgressBar: true,
            showConfirmButton: false
        });
        <?php 
            unset($_SESSION['info']['swal']);
        endif; 
    ?>
}

// Fungsi konfirmasi delete dengan SweetAlert2
function confirmDelete(id, name, type) {
    Swal.fire({
        title: 'Apakah Anda yakin?',
        text: "Data " + type + " untuk '" + name + "' akan dihapus!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Ya, hapus!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            if (type === 'sekolah') {
                // Khusus untuk 'sekolah', arahkan ke delete.php dengan parameter npsn
                window.location.href = 'delete.php?npsn=' + id;
            } else {
                // Untuk tipe lain (kontak, lainnya, pelengkap), gunakan pola lama
                window.location.href = type + '_delete.php?id=' + id;
            }
        }
    });
}

// Fungsi konfirmasi logout dengan SweetAlert2
function confirmLogout() {
    Swal.fire({
        title: 'Konfirmasi Logout',
        text: "Apakah Anda yakin ingin keluar dari sistem?",
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Ya, keluar',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            // Arahkan ke halaman logout jika dikonfirmasi
            window.location.href = '../logout.php';
        }
    });
}

// Fungsi konfirmasi aksi lain dengan SweetAlert2
function confirmAction(title, text, confirmCallback, confirmText = 'Ya, lanjutkan!', cancelText = 'Batal') {
    Swal.fire({
        title: title,
        text: text,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: confirmText,
        cancelButtonText: cancelText
    }).then((result) => {
        if (result.isConfirmed) {
            if (typeof confirmCallback === 'function') {
                confirmCallback();
            } else {
                window.location.href = confirmCallback;
            }
        }
    });
}

// Fungsi untuk menampilkan notifikasi sukses dengan SweetAlert2
function showSuccessAlert(title, text, reload = false) {
    Swal.fire({
        title: title,
        text: text,
        icon: 'success',
        timer: 3000,
        timerProgressBar: true,
        showConfirmButton: false
    }).then(() => {
        if (reload) {
            window.location.reload();
        }
    });
}

// Fungsi untuk menampilkan notifikasi error dengan SweetAlert2
function showErrorAlert(title, text) {
    Swal.fire({
        title: title,
        text: text,
        icon: 'error',
        timer: 5000,
        timerProgressBar: true,
        showConfirmButton: false
    });
}

// Fungsi untuk menampilkan notifikasi warning dengan SweetAlert2
function showWarningAlert(title, text) {
    Swal.fire({
        title: title,
        text: text,
        icon: 'warning',
        timer: 4000,
        timerProgressBar: true,
        showConfirmButton: false
    });
}

// Fungsi untuk menampilkan notifikasi info dengan SweetAlert2
function showInfoAlert(title, text) {
    Swal.fire({
        title: title,
        text: text,
        icon: 'info',
        timer: 4000,
        timerProgressBar: true,
        showConfirmButton: false
    });
}

// Fungsi untuk menampilkan prompt dengan SweetAlert2
function showPrompt(title, inputLabel, confirmCallback) {
    Swal.fire({
        title: title,
        input: 'text',
        inputLabel: inputLabel,
        showCancelButton: true,
        confirmButtonText: 'Simpan',
        cancelButtonText: 'Batal',
        inputValidator: (value) => {
            if (!value) {
                return 'Anda harus memasukkan nilai!'
            }
        }
    }).then((result) => {
        if (result.isConfirmed) {
            confirmCallback(result.value);
        }
    });
}

// Fungsi untuk menampilkan konfirmasi dengan opsi reload
function showConfirmReloadAlert(title, text, confirmText = 'Ya', cancelText = 'Tidak', reload = false) {
    Swal.fire({
        title: title,
        text: text,
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: confirmText,
        cancelButtonText: cancelText
    }).then((result) => {
        if (result.isConfirmed) {
            if (reload) {
                window.location.reload();
            }
        }
    });
}

// Jalankan fungsi saat halaman dimuat
 $(document).ready(function() {
    // Tampilkan SweetAlert dari session jika ada
    showSweetAlertFromSession();
    
    // Inisialisasi tooltips
    $('[data-toggle="tooltip"]').tooltip();
    
    // Inisialisasi popovers
    $('[data-toggle="popover"]').popover();
});
</script>

</body>
</html>