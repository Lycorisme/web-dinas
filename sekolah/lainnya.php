<?php
require_once '../layout/_top.php';
require_once '../helper/connection.php';

$result = mysqli_query($connection, "SELECT l.*, s.nama_sekolah, s.npsn
FROM sekolah_lainnya l
LEFT JOIN sekolah_identitas s ON l.npsn_fk = s.npsn
ORDER BY s.nama_sekolah");
?>

<section class="section">
    <div class="section-header d-flex justify-content-between align-items-center">
        <h1>Data Lainnya Sekolah</h1>
    </div>
    <div class="d-flex justify-content-end mb-3">
        <a href="./lainnya_create.php" class="btn btn-primary">
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
                                        <th style="min-width: 180px;">Kepala Sekolah</th>
                                        <th style="min-width: 180px;">Operator</th>
                                        <th style="min-width: 120px;">Akreditasi</th>
                                        <th style="min-width: 150px;">Kurikulum</th>
                                        <th class="text-center not-export" style="min-width: 120px;">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    // Function to get badge class for akreditasi
                                    function getAkreditasiBadge($akreditasi) {
                                        switch(strtoupper($akreditasi)) {
                                            case 'A': return 'badge-success';
                                            case 'B': return 'badge-primary';
                                            case 'C': return 'badge-warning';
                                            default: return 'badge-secondary';
                                        }
                                    }

                                    // Function to get kurikulum badge
                                    function getKurikulumBadge($kurikulum) {
                                        if (strpos(strtolower($kurikulum), '2013') !== false) {
                                            return 'badge-info';
                                        } elseif (strpos(strtolower($kurikulum), 'merdeka') !== false) {
                                            return 'badge-success';
                                        } else {
                                            return 'badge-secondary';
                                        }
                                    }
                                    
                                    while ($data = mysqli_fetch_array($result)) :
                                    ?>
                                        <tr>
                                            <td title="NPSN: <?= $data['npsn_fk'] ?>"><?= $data['npsn_fk'] ?></td>
                                            <td title="<?= htmlspecialchars($data['nama_sekolah']) ?>" class="school-name">
                                                <strong><?= htmlspecialchars($data['nama_sekolah']) ?></strong>
                                            </td>
                                            <td title="Kepala Sekolah: <?= $data['kepala_sekolah'] ? htmlspecialchars($data['kepala_sekolah']) : 'Belum ada data' ?>">
                                                <?php if ($data['kepala_sekolah']) : ?>
                                                    <div class="d-flex align-items-center">
                                                        <i class="fas fa-user-tie text-success mr-2"></i>
                                                        <span><?= htmlspecialchars($data['kepala_sekolah']) ?></span>
                                                    </div>
                                                <?php else : ?>
                                                    <span class="text-muted font-italic">Belum ada data</span>
                                                <?php endif; ?>
                                            </td>
                                            <td title="Operator: <?= $data['operator_pendataan'] ? htmlspecialchars($data['operator_pendataan']) : 'Belum ada data' ?>">
                                                <?php if ($data['operator_pendataan']) : ?>
                                                    <div class="d-flex align-items-center">
                                                        <i class="fas fa-user-cog text-info mr-2"></i>
                                                        <span><?= htmlspecialchars($data['operator_pendataan']) ?></span>
                                                    </div>
                                                <?php else : ?>
                                                    <span class="text-muted font-italic">Belum ada data</span>
                                                <?php endif; ?>
                                            </td>
                                            <td title="Akreditasi: <?= $data['akreditasi'] ? $data['akreditasi'] : 'Belum terakreditasi' ?>" class="text-center">
                                                <?php if ($data['akreditasi']) : ?>
                                                    <span class="badge <?= getAkreditasiBadge($data['akreditasi']) ?> badge-pill px-3 py-2">
                                                        <i class="fas fa-certificate mr-1"></i>
                                                        <?= strtoupper($data['akreditasi']) ?>
                                                    </span>
                                                <?php else : ?>
                                                    <span class="badge badge-secondary badge-pill px-3 py-2">
                                                        <i class="fas fa-question mr-1"></i>
                                                        Belum Ada
                                                    </span>
                                                <?php endif; ?>
                                            </td>
                                            <td title="Kurikulum: <?= $data['kurikulum'] ? htmlspecialchars($data['kurikulum']) : 'Belum ditetapkan' ?>">
                                                <?php if ($data['kurikulum']) : ?>
                                                    <span class="badge <?= getKurikulumBadge($data['kurikulum']) ?> badge-pill px-3 py-2">
                                                        <i class="fas fa-book-open mr-1"></i>
                                                        <?= htmlspecialchars($data['kurikulum']) ?>
                                                    </span>
                                                <?php else : ?>
                                                    <span class="text-muted font-italic">Belum ditetapkan</span>
                                                <?php endif; ?>
                                            </td>
                                            <td class="text-center">
                                                <div class="btn-group btn-group-sm" role="group">
                                                    <a class="btn btn-info" href="lainnya_edit.php?id=<?= $data['id'] ?>"
                                                       title="Edit Data" data-toggle="tooltip" data-placement="top">
                                                        <i class="fas fa-edit fa-fw"></i>
                                                    </a>
                                                    <a class="btn btn-danger" href="lainnya_delete.php?id=<?= $data['id'] ?>"
                                                       onclick="return confirm('Yakin ingin menghapus data lainnya untuk <?= htmlspecialchars($data['nama_sekolah']) ?>?\n\nData yang akan dihapus:\n- Kepala Sekolah: <?= $data['kepala_sekolah'] ? htmlspecialchars($data['kepala_sekolah']) : 'Tidak ada' ?>\n- Operator: <?= $data['operator_pendataan'] ? htmlspecialchars($data['operator_pendataan']) : 'Tidak ada' ?>\n- Akreditasi: <?= $data['akreditasi'] ? $data['akreditasi'] : 'Tidak ada' ?>\n- Kurikulum: <?= $data['kurikulum'] ? htmlspecialchars($data['kurikulum']) : 'Tidak ada' ?>')"
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

    // Add animated counters for statistics
    function animateValue(element, start, end, duration) {
        let startTimestamp = null;
        const step = (timestamp) => {
            if (!startTimestamp) startTimestamp = timestamp;
            const progress = Math.min((timestamp - startTimestamp) / duration, 1);
            element.innerHTML = Math.floor(progress * (end - start) + start);
            if (progress < 1) {
                window.requestAnimationFrame(step);
            }
        };
        window.requestAnimationFrame(step);
    }

    // Count statistics
    let totalSekolah = $('tbody tr').length;
    let terakreditasi = $('.badge:not(.badge-secondary)').length;
    let belumTerakreditasi = $('.badge.badge-secondary').length;

    console.log(`Total Sekolah: ${totalSekolah}, Terakreditasi: ${terakreditasi}, Belum: ${belumTerakreditasi}`);

    // Enhanced search functionality
    $('#table-1_filter input').attr('placeholder', 'Cari berdasarkan nama sekolah, kepala sekolah, atau akreditasi...');

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
            window.location.href = './lainnya_create.php';
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

    // Performance indicator
    let performanceData = {
        total: totalSekolah,
        akreditasiA: $('.badge-success').length,
        akreditasiB: $('.badge-primary').length,
        akreditasiC: $('.badge-warning').length,
        belumAkreditasi: $('.badge-secondary').length
    };

    console.log('Performance Data:', performanceData);
    
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
                title: 'Data Lainnya Sekolah - ' + new Date().toLocaleDateString('id-ID')
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
                title: 'Data Lainnya Sekolah - ' + new Date().toLocaleDateString('id-ID')
            },
            {
                extend: 'print',
                text: '<i class="fas fa-print"></i> Cetak',
                className: 'btn btn-sm btn-info',
                exportOptions: {
                    columns: ':visible:not(.not-export)'
                },
                title: 'Data Lainnya Sekolah - ' + new Date().toLocaleDateString('id-ID')
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
                $('.dataTables_filter input').addClass('form-control').attr('placeholder', 'Cari data sekolah...');
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