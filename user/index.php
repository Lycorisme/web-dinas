<?php
require_once '../layout/_top.php';
require_once '../helper/connection.php';

// Query untuk mendapatkan data user
$query = "SELECT * FROM login ORDER BY id DESC";
$users = mysqli_query($connection, $query);
?>

<section class="section">
    <div class="section-header d-flex justify-content-between align-items-center">
        <h1>User Management</h1>
        <div class="d-flex align-items-center">
            <a href="tambah.php" class="btn btn-primary">
                <i class="fas fa-plus mr-2"></i>Tambah User
            </a>
        </div>
    </div>
    
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <!-- DataTable Controls Fixed Area -->
                    <div class="datatable-controls mb-3">
                        <div class="row">
                            <div class="col-md-6 col-sm-12 mb-2">
                                <div id="table-1_length" class="dataTables_length"></div>
                            </div>
                            <div class="col-md-6 col-sm-12 mb-2">
                                <div id="table-1_filter" class="dataTables_filter"></div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12">
                                <div id="table-1_buttons" class="dt-buttons mb-3"></div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Scrollable Table Container -->
                    <div class="table-responsive-wrapper">
                        <div class="table-container">
                            <table class="table table-hover table-striped w-100" id="table-1">
                                <thead>
                                    <tr>
                                        <th style="min-width: 50px;">ID</th>
                                        <th style="min-width: 200px;">Username</th>
                                        <th style="min-width: 250px;">Nama Pengguna</th>
                                        <th style="min-width: 120px;">Role</th>
                                        <th class="text-center not-export" style="min-width: 150px;">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php while($user = mysqli_fetch_array($users)) { ?>
                                        <tr>
                                            <td title="ID: <?= $user['id'] ?>">
                                                <span class="badge badge-light"><?= $user['id'] ?></span>
                                            </td>
                                            <td title="Username: <?= $user['username'] ?>">
                                                <div class="d-flex align-items-center">
                                                    <i class="fas fa-user-circle text-primary mr-2"></i>
                                                    <strong><?= $user['username'] ?></strong>
                                                </div>
                                            </td>
                                            <td title="Nama Pengguna: <?= $user['nama_pengguna'] ?>">
                                                <div class="d-flex align-items-center">
                                                    <i class="fas fa-id-badge text-info mr-2"></i>
                                                    <span><?= $user['nama_pengguna'] ?></span>
                                                </div>
                                            </td>
                                            <td class="text-center">
                                                <span class="badge badge-primary badge-pill px-3 py-2">
                                                    <i class="fas fa-user-shield mr-1"></i>
                                                    Admin
                                                </span>
                                            </td>
                                            <td class="text-center">
                                                <div class="btn-group btn-group-sm" role="group">
                                                    <a class="btn btn-info" href="edit.php?id=<?= $user['id'] ?>"
                                                       title="Edit Data" data-toggle="tooltip" data-placement="top">
                                                        <i class="fas fa-edit fa-fw"></i>
                                                    </a>
                                                    <?php if ($user['id'] != 1) { ?>
                                                        <a class="btn btn-danger" href="hapus.php?id=<?= $user['id'] ?>"
                                                           onclick="return confirm('Yakin ingin menghapus user <?= htmlspecialchars($user['nama_pengguna']) ?>?')"
                                                           title="Hapus Data" data-toggle="tooltip" data-placement="top">
                                                            <i class="fas fa-trash fa-fw"></i>
                                                        </a>
                                                    <?php } else { ?>
                                                        <button class="btn btn-danger" disabled title="User default tidak dapat dihapus" data-toggle="tooltip" data-placement="top">
                                                            <i class="fas fa-trash fa-fw"></i>
                                                        </button>
                                                    <?php } ?>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php } ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    
                    <!-- DataTable Info and Pagination Fixed Area -->
                    <div class="datatable-footer mt-3">
                        <div class="row align-items-center">
                            <div class="col-md-6 col-sm-12">
                                <div id="table-1_info" class="dataTables_info"></div>
                            </div>
                            <div class="col-md-6 col-sm-12">
                                <div id="table-1_paginate" class="dataTables_paginate"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php
require_once '../layout/_bottom.php';
?>

<!-- Page Specific JS File -->
<script>
// Fungsi untuk memindahkan elemen DataTable ke container yang tepat
function moveDataTableElements() {
    // Pastikan container datatable-controls ada
    const controlsContainer = $('.datatable-controls');
    if (!controlsContainer.length) {
        console.error('Container .datatable-controls tidak ditemukan!');
        return;
    }
    
    // Kosongkan container terlebih dahulu
    controlsContainer.find('.row:first-child .col-md-6:first-child').empty();
    controlsContainer.find('.row:first-child .col-md-6:last-child').empty();
    controlsContainer.find('.row:last-child .col-12').empty();
    
    // Tampilkan dan pindahkan length control
    const lengthElement = $('#table-1_length');
    if (lengthElement.length) {
        lengthElement.show().detach().appendTo('.datatable-controls .row:first-child .col-md-6:first-child');
    }
    
    // Tampilkan dan pindahkan filter control  
    const filterElement = $('#table-1_filter');
    if (filterElement.length) {
        filterElement.show().detach().appendTo('.datatable-controls .row:first-child .col-md-6:last-child');
    }
    
    // Tampilkan dan pindahkan buttons
    const buttonsElement = $('.dt-buttons');
    if (buttonsElement.length) {
        buttonsElement.detach().appendTo('.datatable-controls .row:last-child .col-12');
    }
    
    // Tampilkan dan pindahkan info
    const infoElement = $('#table-1_info');
    if (infoElement.length) {
        infoElement.show().detach().appendTo('.datatable-footer .col-md-6:first-child');
    }
    
    // Tampilkan dan pindahkan pagination
    const paginateElement = $('#table-1_paginate');
    if (paginateElement.length) {
        paginateElement.show().detach().appendTo('.datatable-footer .col-md-6:last-child');
    }
}

// Initialize tooltips and additional features
$(document).ready(function() {
    // Initialize tooltips
    $('[data-toggle="tooltip"]').tooltip();

    // Add hover effects for better UX
    $('tbody tr').hover(
        function() {
            $(this).addClass('table-active');
            $(this).find('.badge').addClass('shadow-sm');
        },
        function() {
            $(this).removeClass('table-active');
            $(this).find('.badge').removeClass('shadow-sm');
        }
    );

    // Enhanced search functionality
    $('#table-1_filter input').attr('placeholder', 'Cari berdasarkan username atau nama pengguna...');

    // Add keyboard shortcuts
    $(document).keydown(function(e) {
        // Ctrl + N for new data
        if (e.ctrlKey && e.keyCode === 78) {
            e.preventDefault();
            window.location.href = './tambah.php';
        }
        // Ctrl + F for focus search
        if (e.ctrlKey && e.keyCode === 70) {
            e.preventDefault();
            $('#table-1_filter input').focus();
        }
    });

    // Add row click to view details (optional)
    $('tbody tr').click(function(e) {
        if (!$(e.target).closest('.btn').length) {
            $(this).toggleClass('table-info');
        }
    });
    
    // Initialize DataTables with custom configuration
    $('#table-1').DataTable({
        responsive: false,
        scrollX: false,
        language: {
            url: "../../vendor/datatables/i18n/Indonesian.json"
        },
        pageLength: 10,
        lengthMenu: [[10, 25, 50, -1], [10, 25, 50, "Semua"]],
        columnDefs: [
            { 
                orderable: false, 
                targets: [-1] // Kolom aksi tidak bisa di-sort
            },
            {
                className: "text-center",
                targets: [-1] // Kolom aksi di tengah
            }
        ],
        order: [[2, 'asc']], // Default sort by nama pengguna
        dom: '<"top"Blf>rt<"bottom"ip><"clear">',
        buttons: [
            {
                extend: 'copy',
                text: '<i class="fas fa-copy"></i> Salin',
                className: 'btn btn-sm btn-secondary',
                exportOptions: {
                    columns: ':visible:not(.not-export)'
                }
            },
            {
                extend: 'excel',
                text: '<i class="fas fa-file-excel"></i> Excel',
                className: 'btn btn-sm btn-success',
                exportOptions: {
                    columns: ':visible:not(.not-export)'
                },
                title: 'Data User Administrator - ' + new Date().toLocaleDateString('id-ID')
            },
            {
                extend: 'pdf',
                text: '<i class="fas fa-file-pdf"></i> PDF',
                className: 'btn btn-sm btn-danger',
                exportOptions: {
                    columns: ':visible:not(.not-export)'
                },
                orientation: 'landscape',
                pageSize: 'A4',
                title: 'Data User Administrator - ' + new Date().toLocaleDateString('id-ID')
            },
            {
                extend: 'print',
                text: '<i class="fas fa-print"></i> Cetak',
                className: 'btn btn-sm btn-info',
                exportOptions: {
                    columns: ':visible:not(.not-export)'
                },
                title: 'Data User Administrator - ' + new Date().toLocaleDateString('id-ID')
            },
            {
                extend: 'colvis',
                text: '<i class="fas fa-eye"></i> Kolom',
                className: 'btn btn-sm btn-primary',
                columns: ':not(.not-colvis)'
            }
        ],
        drawCallback: function() {
            // Re-initialize tooltips setelah draw
            $('[data-toggle="tooltip"]').tooltip();
        },
        initComplete: function() {
            // Pindahkan elemen setelah inisialisasi dengan delay
            setTimeout(() => {
                moveDataTableElements();
                
                // Tambahkan class ke elemen
                $('.dataTables_filter input').addClass('form-control').attr('placeholder', 'Cari data user...');
                $('.dataTables_length select').addClass('form-control');
                
                // Custom styling untuk pagination
                $('.dataTables_paginate .paginate_button').addClass('page-item').find('a').addClass('page-link');
                $('.dataTables_paginate .paginate_button.current').addClass('active');
                $('.dataTables_paginate .paginate_button.disabled').addClass('disabled');
            }, 500);
        }
    });
});

// Function to export data (optional enhancement)
function exportData() {
    // This could be enhanced to export table data
    console.log('Export functionality can be added here');
}
</script>

<?php
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
    $_SESSION['info'] = null;
endif;
?>