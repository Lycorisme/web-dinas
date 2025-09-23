<?php
require_once '../layout/_top.php';
require_once '../helper/connection.php';

$result = mysqli_query($connection, "SELECT p.*, s.nama_sekolah, s.npsn
FROM sekolah_pelengkap p
LEFT JOIN sekolah_identitas s ON p.npsn_fk = s.npsn
ORDER BY s.nama_sekolah");
?>

<section class="section">
    <div class="section-header d-flex justify-content-between align-items-center">
        <h1>Data Pelengkap Sekolah</h1>
        <a href="./pelengkap_create.php" class="btn btn-primary">
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
                                        <th style="min-width: 100px;">NPSN</th>
                                        <th style="min-width: 200px;">Nama Sekolah</th>
                                        <th style="min-width: 150px;">SK Pendirian</th>
                                        <th style="min-width: 120px;">Tanggal SK</th>
                                        <th style="min-width: 150px;">Status Kepemilikan</th>
                                        <th style="min-width: 150px;">Bank</th>
                                        <th style="min-width: 150px;">Rekening</th>
                                        <th style="min-width: 150px;">NPWP</th>
                                        <th class="text-center not-export" style="min-width: 120px;">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    while ($data = mysqli_fetch_array($result)) :
                                    // Format tanggal Indonesia
                                    $tanggal_sk = $data['tgl_sk_pendirian'] ? date('d/m/Y', strtotime($data['tgl_sk_pendirian'])) : '-';
                                    ?>
                                        <tr>
                                            <td title="NPSN: <?= $data['npsn_fk'] ?>">
                                                <span class="badge badge-primary"><?= htmlspecialchars($data['npsn_fk']) ?></span>
                                            </td>
                                            <td title="<?= htmlspecialchars($data['nama_sekolah']) ?>" class="school-name">
                                                <strong><?= htmlspecialchars($data['nama_sekolah']) ?></strong>
                                            </td>
                                            <td title="SK Pendirian: <?= $data['sk_pendirian'] ? htmlspecialchars($data['sk_pendirian']) : 'Tidak ada' ?>">
                                                <?php if ($data['sk_pendirian']) : ?>
                                                    <div class="d-flex align-items-center">
                                                        <i class="fas fa-file-alt text-info mr-2"></i>
                                                        <span><?= htmlspecialchars($data['sk_pendirian']) ?></span>
                                                    </div>
                                                <?php else : ?>
                                                    <span class="text-muted font-italic">Belum ada data</span>
                                                <?php endif; ?>
                                            </td>
                                            <td title="Tanggal SK: <?= $tanggal_sk ?>" class="text-center">
                                                <?php if ($tanggal_sk != '-') : ?>
                                                    <span class="badge badge-light">
                                                        <i class="fas fa-calendar-alt mr-1"></i>
                                                        <?= $tanggal_sk ?>
                                                    </span>
                                                <?php else : ?>
                                                    <span class="text-muted font-italic">-</span>
                                                <?php endif; ?>
                                            </td>
                                            <td title="Status: <?= $data['status_kepemilikan'] ? htmlspecialchars($data['status_kepemilikan']) : 'Tidak ada' ?>">
                                                <?php if ($data['status_kepemilikan']) : ?>
                                                    <span class="badge <?= ($data['status_kepemilikan'] == 'Pemerintah') ? 'badge-success' : 'badge-warning' ?> badge-pill px-3 py-2">
                                                        <i class="fas fa-building mr-1"></i>
                                                        <?= htmlspecialchars($data['status_kepemilikan']) ?>
                                                    </span>
                                                <?php else : ?>
                                                    <span class="text-muted font-italic">Belum ada data</span>
                                                <?php endif; ?>
                                            </td>
                                            <td title="Bank: <?= $data['nama_bank'] ? htmlspecialchars($data['nama_bank']) : 'Tidak ada' ?>">
                                                <?php if ($data['nama_bank']) : ?>
                                                    <div class="d-flex align-items-center">
                                                        <i class="fas fa-university text-primary mr-2"></i>
                                                        <span><?= htmlspecialchars($data['nama_bank']) ?></span>
                                                    </div>
                                                <?php else : ?>
                                                    <span class="text-muted font-italic">Belum ada data</span>
                                                <?php endif; ?>
                                            </td>
                                            <td title="Rekening: <?= $data['nomor_rekening'] ? $data['nomor_rekening'] : 'Tidak ada' ?>" class="font-monospace">
                                                <?php if ($data['nomor_rekening']) : ?>
                                                    <div class="d-flex align-items-center">
                                                        <i class="fas fa-credit-card text-success mr-2"></i>
                                                        <span><?= htmlspecialchars($data['nomor_rekening']) ?></span>
                                                    </div>
                                                <?php else : ?>
                                                    <span class="text-muted font-italic">Belum ada data</span>
                                                <?php endif; ?>
                                            </td>
                                            <td title="NPWP: <?= $data['npwp'] ? $data['npwp'] : 'Tidak ada' ?>" class="font-monospace">
                                                <?php if ($data['npwp']) : ?>
                                                    <div class="d-flex align-items-center">
                                                        <i class="fas fa-id-card text-danger mr-2"></i>
                                                        <span><?= htmlspecialchars($data['npwp']) ?></span>
                                                    </div>
                                                <?php else : ?>
                                                    <span class="text-muted font-italic">Belum ada data</span>
                                                <?php endif; ?>
                                            </td>
                                            <td class="text-center">
                                                <div class="btn-group btn-group-sm" role="group">
                                                    <a class="btn btn-info" href="pelengkap_edit.php?id=<?= $data['id'] ?>"
                                                       title="Edit Data" data-toggle="tooltip" data-placement="top">
                                                        <i class="fas fa-edit fa-fw"></i>
                                                    </a>
                                                    <a class="btn btn-danger" href="pelengkap_delete.php?id=<?= $data['id'] ?>"
                                                       onclick="return confirm('Yakin ingin menghapus data pelengkap untuk <?= htmlspecialchars($data['nama_sekolah']) ?>?')"
                                                       title="Hapus Data" data-toggle="tooltip" data-placement="top">
                                                        <i class="fas fa-trash fa-fw"></i>
                                                    </a>
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

    // Format NPWP and Rekening display
    $('.font-monospace').each(function() {
        let text = $(this).text().trim();
        if (text !== '-' && text !== '') {
            // Add spacing for better readability
            if (text.length > 10) {
                let formatted = text.replace(/(\d{2})(\d{3})(\d{3})(\d{1})(\d{3})(\d{3})/g, '$1.$2.$3.$4-$5.$6');
                if (formatted !== text) {
                    $(this).html(formatted);
                }
            }
        }
    });

    // Add loading animation for buttons
    $('.btn:not(.btn-danger)').on('click', function(e) {
        let $this = $(this);
        let originalHtml = $this.html();
        $this.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i>');

        // Re-enable button after short delay (in case of navigation)
        setTimeout(() => {
            $this.prop('disabled', false).html(originalHtml);
        }, 2000);
    });

    // Enhanced search functionality
    $('#table-1_filter input').attr('placeholder', 'Cari berdasarkan nama sekolah, SK, atau status...');

    // Add custom search for badges
    $.fn.dataTable.ext.search.push(function(settings, data, dataIndex) {
        let searchTerm = $('#table-1_filter input').val().toLowerCase();
        if (!searchTerm) return true;

        // Search in all visible columns
        for (let i = 0; i < data.length - 1; i++) { // -1 to exclude action column
            if (data[i].toLowerCase().indexOf(searchTerm) !== -1) {
                return true;
            }
        }
        return false;
    });

    // Add keyboard shortcuts
    $(document).keydown(function(e) {
        // Ctrl + N for new data
        if (e.ctrlKey && e.keyCode === 78) {
            e.preventDefault();
            window.location.href = './pelengkap_create.php';
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
        order: [[1, 'asc']], // Default sort by nama sekolah
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
                title: 'Data Pelengkap Sekolah - ' + new Date().toLocaleDateString('id-ID')
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
                title: 'Data Pelengkap Sekolah - ' + new Date().toLocaleDateString('id-ID')
            },
            {
                extend: 'print',
                text: '<i class="fas fa-print"></i> Cetak',
                className: 'btn btn-sm btn-info',
                exportOptions: {
                    columns: ':visible:not(.not-export)'
                },
                title: 'Data Pelengkap Sekolah - ' + new Date().toLocaleDateString('id-ID')
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
                $('.dataTables_filter input').addClass('form-control').attr('placeholder', 'Cari data pelengkap sekolah...');
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