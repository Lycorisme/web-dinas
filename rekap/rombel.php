<?php
// FILE: rekap/rombel.php - Rombel Recap Page
ob_start();
require_once '../layout/_top.php';
require_once '../helper/connection.php';

// Query untuk mendapatkan data total Rombel per sekolah (tanpa duplikasi)
 $result = mysqli_query($connection, "SELECT 
                                    r.npsn_fk, 
                                    s.nama_sekolah, 
                                    s.jenjang_pendidikan,
                                    SUM(r.jumlah_laki_laki) AS total_laki_laki,
                                    SUM(r.jumlah_perempuan) AS total_perempuan,
                                    SUM(r.jumlah_total) AS total_total
                                FROM rekap_rombel r
                                LEFT JOIN sekolah_identitas s ON r.npsn_fk = s.npsn
                                GROUP BY r.npsn_fk, s.nama_sekolah, s.jenjang_pendidikan
                                ORDER BY s.nama_sekolah");
?>

<section class="section">
    <div class="section-header d-flex justify-content-between align-items-center">
        <h1>Rekapitulasi Rombongan Belajar</h1>
    </div>
    <div class="d-flex justify-content-end mb-3">
        <a href="./rombel_create.php" class="btn btn-primary">
            <i class="fas fa-plus mr-2"></i>Tambah Data
        </a>
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
                                        <th style="min-width: 80px;">No</th>
                                        <th style="min-width: 100px;">NPSN</th>
                                        <th style="min-width: 200px;">Nama Sekolah</th>
                                        <th style="min-width: 100px;">Jenjang</th>
                                        <th style="min-width: 100px;">Laki-laki</th>
                                        <th style="min-width: 100px;">Perempuan</th>
                                        <th style="min-width: 80px;">Total</th>
                                        <th style="min-width: 100px;">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $no = 1;
                                    while ($data = mysqli_fetch_array($result)) :
                                    ?>
                                        <tr>
                                            <td class="text-center"><?= $no++ ?></td>
                                            <td title="NPSN: <?= $data['npsn_fk'] ?>">
                                                <span class="badge badge-primary"><?= htmlspecialchars($data['npsn_fk']) ?></span>
                                            </td>
                                            <td title="<?= htmlspecialchars($data['nama_sekolah']) ?>" class="school-name">
                                                <strong><?= htmlspecialchars($data['nama_sekolah']) ?></strong>
                                            </td>
                                            <td title="Jenjang: <?= htmlspecialchars($data['jenjang_pendidikan']) ?>">
                                                <span class="badge badge-info">
                                                    <i class="fas fa-graduation-cap mr-1"></i>
                                                    <?= htmlspecialchars($data['jenjang_pendidikan']) ?>
                                                </span>
                                            </td>
                                            <td title="Jumlah Laki-laki: <?= $data['total_laki_laki'] ?>">
                                                <span class="badge badge-primary badge-pill px-3 py-2">
                                                    <i class="fas fa-male mr-1"></i>
                                                    <?= $data['total_laki_laki'] ?>
                                                </span>
                                            </td>
                                            <td title="Jumlah Perempuan: <?= $data['total_perempuan'] ?>">
                                                <span class="badge badge-danger badge-pill px-3 py-2">
                                                    <i class="fas fa-female mr-1"></i>
                                                    <?= $data['total_perempuan'] ?>
                                                </span>
                                            </td>
                                            <td title="Total: <?= $data['total_total'] ?>">
                                                <span class="badge badge-success badge-pill px-3 py-2">
                                                    <i class="fas fa-users mr-1"></i>
                                                    <?= $data['total_total'] ?>
                                                </span>
                                            </td>
                                            <td class="text-center">
                                                <div class="btn-group btn-group-sm" role="group">
                                                    <button type="button" class="btn btn-info" 
                                                            onclick="showDetail('<?= $data['npsn_fk'] ?>', '<?= htmlspecialchars($data['nama_sekolah']) ?>')"
                                                            title="Lihat Detail" data-toggle="tooltip" data-placement="top">
                                                        <i class="fas fa-eye"></i>
                                                    </button>
                                                    <button type="button" class="btn btn-danger" 
                                                            onclick="confirmDeleteByNPSN('<?= $data['npsn_fk'] ?>', '<?= htmlspecialchars($data['nama_sekolah']) ?>')"
                                                            title="Hapus Data" data-toggle="tooltip" data-placement="top">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php
                                    endwhile;
                                    ?>
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

<!-- Modal for Detail -->
<div class="modal fade" id="detailModal" tabindex="-1" role="dialog" aria-labelledby="detailModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="detailModalLabel">
                    <i class="fas fa-school mr-2"></i>Detail Data Sekolah
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" id="detailContent">
                <div class="text-center py-4">
                    <div class="spinner-border text-primary" role="status">
                        <span class="sr-only">Loading...</span>
                    </div>
                    <p class="mt-2">Memuat detail data...</p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

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

// Fungsi untuk menampilkan detail dalam modal
function showDetail(npsn, namaSekolah) {
    // Update modal title
    $('#detailModalLabel').html('<i class="fas fa-school mr-2"></i>Detail Data: ' + namaSekolah);
    
    // Show modal
    $('#detailModal').modal('show');
    
    // Load detail content via AJAX
    $.ajax({
        url: 'rombel_detail_inline.php',
        type: 'GET',
        data: { npsn: npsn },
        success: function(response) {
            $('#detailContent').html(response);
            
            // Re-initialize tooltips in the loaded content
            $('#detailContent [data-toggle="tooltip"]').tooltip();
        },
        error: function(xhr, status, error) {
            $('#detailContent').html(`
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-triangle mr-2"></i>
                    Gagal memuat data: ${error}
                </div>
            `);
        }
    });
}

// Fungsi untuk konfirmasi hapus berdasarkan NPSN
function confirmDeleteByNPSN(npsn, namaSekolah) {
    Swal.fire({
        title: 'Apakah Anda yakin?',
        text: "Anda akan menghapus semua data untuk " + namaSekolah + "!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Ya, hapus semua data!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            // Redirect ke halaman hapus dengan parameter NPSN
            window.location.href = `rombel_hapus.php?npsn=${npsn}`;
        }
    })
}

// Fungsi untuk konfirmasi hapus individual
function confirmDelete(id, namaSekolah, tingkat_kelas) {
    Swal.fire({
        title: 'Apakah Anda yakin?',
        text: "Anda akan menghapus data " + tingkat_kelas + " untuk " + namaSekolah + "!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Ya, hapus!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = `rombel_hapus.php?id=${id}`;
        }
    })
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
    $('#table-1_filter input').attr('placeholder', 'Cari berdasarkan nama sekolah atau jenjang...');

    // Add custom search for badges
    $.fn.dataTable.ext.search.push(function(settings, data, dataIndex) {
        let searchTerm = $('#table-1_filter input').val().toLowerCase();
        if (!searchTerm) return true;

        // Search in all visible columns
        for (let i = 0; i < data.length; i++) {
            if (data[i].toLowerCase().indexOf(searchTerm) !== -1) {
                return true;
            }
        }
        return false;
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
                targets: [0, 7] // Kolom no dan aksi tidak bisa di-sort
            },
            {
                className: "text-center",
                targets: [0, 7] // Kolom no dan aksi di tengah
            }
        ],
        order: [[2, 'asc']], // Default sort by nama sekolah
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
                title: 'Data Rombel - ' + new Date().toLocaleDateString('id-ID')
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
                title: 'Data Rombel - ' + new Date().toLocaleDateString('id-ID')
            },
            {
                extend: 'print',
                text: '<i class="fas fa-print"></i> Cetak',
                className: 'btn btn-sm btn-info',
                exportOptions: {
                    columns: ':visible:not(.not-export)'
                },
                title: 'Data Rombel - ' + new Date().toLocaleDateString('id-ID')
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
            
            // Update nomor urut setelah pagination
            var api = this.api();
            var start = api.page.info().start;
            api.column(0, {page:'current'}).nodes().each(function(cell, i) {
                cell.innerHTML = start + i + 1;
            });
        },
        initComplete: function() {
            // Pindahkan elemen setelah inisialisasi dengan delay
            setTimeout(() => {
                moveDataTableElements();
                
                // Tambahkan class ke elemen
                $('.dataTables_filter input').addClass('form-control').attr('placeholder', 'Cari data Rombel...');
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