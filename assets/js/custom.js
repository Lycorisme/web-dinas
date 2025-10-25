/**
 * Handle logout confirmation using SweetAlert
 */
$(document).ready(function() {
  $('#logout-btn').on('click', function(e) {
    // Mencegah link default berfungsi
    e.preventDefault();

    Swal.fire({
      title: 'Apakah Anda yakin?',
      text: "Anda akan keluar dari sesi ini!",
      icon: 'warning',
      showCancelButton: true,
      confirmButtonColor: '#3085d6',
      cancelButtonColor: '#d33',
      confirmButtonText: 'Ya, keluar!',
      cancelButtonText: 'Batal'
    }).then((result) => {
      // Jika pengguna menekan tombol "Ya, keluar!"
      if (result.isConfirmed) {
        // Arahkan ke halaman logout
        window.location.href = '../logout.php';
      }
    });
  });
});