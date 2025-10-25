<?php
// FILE: laporan/laporan_siswa.php - Siswa Report Page
ob_start();
require_once '../layout/_top.php';
require_once '../helper/connection.php';

// Ambil data kecamatan dan jenjang untuk dropdown
 $kecamatan_query = "SELECT DISTINCT k.nama_kecamatan 
                   FROM kecamatan k 
                   INNER JOIN sekolah_identitas s ON k.id_kecamatan = s.id_kecamatan_fk 
                   ORDER BY k.nama_kecamatan";
 $kecamatan_result = mysqli_query($connection, $kecamatan_query);

 $jenjang_query = "SELECT DISTINCT jenjang_pendidikan 
                 FROM sekolah_identitas 
                 WHERE jenjang_pendidikan IS NOT NULL 
                 ORDER BY jenjang_pendidikan";
 $jenjang_result = mysqli_query($connection, $jenjang_query);

// Proses filter jika ada
 $where_conditions = [];
 $kecamatan_filter = '';
 $jenjang_filter = '';

if (isset($_GET['kecamatan']) && !empty($_GET['kecamatan'])) {
    $kecamatan_filter = mysqli_real_escape_string($connection, $_GET['kecamatan']);
    $where_conditions[] = "k.nama_kecamatan = '$kecamatan_filter'";
}

if (isset($_GET['jenjang']) && !empty($_GET['jenjang'])) {
    $jenjang_filter = mysqli_real_escape_string($connection, $_GET['jenjang']);
    $where_conditions[] = "s.jenjang_pendidikan = '$jenjang_filter'";
}

 $where_clause = '';
if (!empty($where_conditions)) {
    $where_clause = 'WHERE ' . implode(' AND ', $where_conditions);
}

// Query data siswa jika ada filter
 $siswa_data = [];
 $show_data = false;

if (isset($_GET['kecamatan']) || isset($_GET['jenjang'])) {
    $show_data = true;
    
    $siswa_query = "SELECT 
                    s.npsn, 
                    s.nama_sekolah, 
                    s.jenjang_pendidikan, 
                    s.status_sekolah, 
                    k.nama_kecamatan, 
                    kab.nama_kabupaten, 
                    p.nama_provinsi,
                    SUM(r.jumlah_laki_laki) AS total_pd_laki,
                    SUM(r.jumlah_perempuan) AS total_pd_perempuan,
                    SUM(r.jumlah_total) AS total_pd_total
                  FROM sekolah_identitas s
                  LEFT JOIN kecamatan k ON s.id_kecamatan_fk = k.id_kecamatan
                  LEFT JOIN kabupaten_kota kab ON k.id_kabupaten_fk = kab.id_kabupaten
                  LEFT JOIN provinsi p ON kab.id_provinsi_fk = p.id_provinsi
                  LEFT JOIN rekap_rombel r ON s.npsn = r.npsn_fk
                  $where_clause
                  GROUP BY s.npsn, s.nama_sekolah, k.nama_kecamatan
                  ORDER BY s.nama_sekolah";
    
    $siswa_result = mysqli_query($connection, $siswa_query);
    
    if (!$siswa_result) {
        echo "<!-- Database Error: " . mysqli_error($connection) . " -->";
    } else {
        while ($row = mysqli_fetch_assoc($siswa_result)) {
            $siswa_data[] = $row;
        }
    }
}

// Nama untuk display
 $nama_kecamatan = !empty($kecamatan_filter) ? $kecamatan_filter : 'Semua Kecamatan';
 $nama_jenjang = !empty($jenjang_filter) ? $jenjang_filter : 'Semua Jenjang';
?>

<section class="section">
    <div class="section-header d-flex justify-content-between align-items-center">
        <h1>Laporan & Ekspor Data Siswa</h1>
    </div>
    
    <!-- Filter Form -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4><i class="fas fa-filter"></i> Filter Data</h4>
                </div>
                <div class="card-body">
                    <form method="get" action="">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="kecamatan">Kecamatan</label>
                                    <select name="kecamatan" id="kecamatan" class="form-control">
                                        <option value="">Semua Kecamatan</option>
                                        <?php 
                                        // Reset pointer untuk hasil query
                                        mysqli_data_seek($kecamatan_result, 0);
                                        while ($row = mysqli_fetch_assoc($kecamatan_result)): ?>
                                            <option value="<?php echo htmlspecialchars($row['nama_kecamatan']); ?>" 
                                                    <?php echo ($kecamatan_filter == $row['nama_kecamatan']) ? 'selected' : ''; ?>>
                                                <?php echo htmlspecialchars($row['nama_kecamatan']); ?>
                                            </option>
                                        <?php endwhile; ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="jenjang">Jenjang</label>
                                    <select name="jenjang" id="jenjang" class="form-control">
                                        <option value="">Semua Jenjang</option>
                                        <?php 
                                        // Reset pointer untuk hasil query
                                        mysqli_data_seek($jenjang_result, 0);
                                        while ($row = mysqli_fetch_assoc($jenjang_result)): ?>
                                            <option value="<?php echo htmlspecialchars($row['jenjang_pendidikan']); ?>"
                                                    <?php echo ($jenjang_filter == $row['jenjang_pendidikan']) ? 'selected' : ''; ?>>
                                                <?php echo htmlspecialchars($row['jenjang_pendidikan']); ?>
                                            </option>
                                        <?php endwhile; ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>&nbsp;</label>
                                    <div class="d-flex flex-column">
                                        <button type="submit" class="btn btn-primary mb-2">
                                            <i class="fas fa-search"></i> Tampilkan Data
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <?php if (!empty($siswa_data)): ?>
                        <div class="row mt-3">
                            <div class="col-12">
                                <div class="btn-group" role="group">
                                    <a href="export_pdf_siswa.php?<?php echo http_build_query($_GET); ?>" target="_blank" class="btn btn-danger">
                                        <i class="fas fa-file-pdf"></i> Export PDF
                                    </a>
                                    <button type="submit" formaction="export_excel_siswa.php" class="btn btn-success">
                                        <i class="fas fa-file-excel"></i> Export Excel
                                    </button>
                                </div>
                            </div>
                        </div>
                        <?php endif; ?>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <?php if ($show_data && empty($siswa_data)): ?>
    <!-- Pesan jika tidak ada data -->
    <div class="row">
        <div class="col-12">
            <div class="alert alert-warning">
                <i class="fas fa-exclamation-triangle"></i> Tidak ada data siswa yang sesuai dengan filter yang dipilih.
            </div>
        </div>
    </div>
    <?php endif; ?>

    <?php if (!empty($siswa_data)): ?>
    <!-- Data Table -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4><i class="fas fa-table"></i> Data Siswa</h4>
                    <div class="card-header-action">
                        <span class="badge badge-primary">
                            <i class="fas fa-database"></i> 
                            <?php echo count($siswa_data); ?> Sekolah
                        </span>
                    </div>
                </div>
                <div class="card-body">
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i>
                        <strong>Filter Aktif:</strong> 
                        Kecamatan: <?php echo htmlspecialchars($nama_kecamatan); ?> | 
                        Jenjang: <?php echo htmlspecialchars($nama_jenjang); ?>
                    </div>
                    
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
                                        <th style="min-width: 100px;">Status</th>
                                        <th style="min-width: 150px;">Kecamatan</th>
                                        <th style="min-width: 150px;">Kabupaten/Kota</th>
                                        <th style="min-width: 100px;">Total PD Laki-laki</th>
                                        <th style="min-width: 100px;">Total PD Perempuan</th>
                                        <th style="min-width: 100px;">Total Keseluruhan PD</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $no = 1;
                                    foreach ($siswa_data as $data):
                                    ?>
                                        <tr>
                                            <td><?= $no++ ?></td>
                                            <td title="NPSN: <?= $data['npsn'] ?>">
                                                <span class="badge badge-primary"><?= htmlspecialchars($data['npsn']) ?></span>
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
                                            <td title="Status: <?= htmlspecialchars($data['status_sekolah']) ?>">
                                                <span class="badge <?= $data['status_sekolah'] == 'Negeri' ? 'badge-success' : 'badge-warning' ?>">
                                                    <i class="fas fa-building mr-1"></i>
                                                    <?= htmlspecialchars($data['status_sekolah']) ?>
                                                </span>
                                            </td>
                                            <td title="Kecamatan: <?= htmlspecialchars($data['nama_kecamatan']) ?>">
                                                <span class="badge badge-secondary">
                                                    <i class="fas fa-map mr-1"></i>
                                                    <?= htmlspecialchars($data['nama_kecamatan']) ?>
                                                </span>
                                            </td>
                                            <td title="Kabupaten/Kota: <?= htmlspecialchars($data['nama_kabupaten']) ?>">
                                                <span class="badge badge-dark">
                                                    <i class="fas fa-city mr-1"></i>
                                                    <?= htmlspecialchars($data['nama_kabupaten']) ?>
                                                </span>
                                            </td>
                                            <td title="Total PD Laki-laki: <?= $data['total_pd_laki'] ?>">
                                                <span class="badge badge-info">
                                                    <i class="fas fa-male mr-1"></i>
                                                    <?= $data['total_pd_laki'] ?>
                                                </span>
                                            </td>
                                            <td title="Total PD Perempuan: <?= $data['total_pd_perempuan'] ?>">
                                                <span class="badge badge-danger">
                                                    <i class="fas fa-female mr-1"></i>
                                                    <?= $data['total_pd_perempuan'] ?>
                                                </span>
                                            </td>
                                            <td title="Total Keseluruhan PD: <?= $data['total_pd_total'] ?>">
                                                <span class="badge badge-success">
                                                    <i class="fas fa-child mr-1"></i>
                                                    <?= $data['total_pd_total'] ?>
                                                </span>
                                            </td>
                                        </tr>
                                    <?php
                                    endforeach;
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
    <?php endif; ?>
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

    // Only initialize DataTable if table exists
    if ($('#table-1').length) {
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
        $('#table-1_filter input').attr('placeholder', 'Cari berdasarkan nama sekolah, NPSN, atau kecamatan...');

        // Add row click to view details (optional)
        $('tbody tr').click(function(e) {
            $(this).toggleClass('table-info');
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
                    title: 'Data Siswa - ' + new Date().toLocaleDateString('id-ID')
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
                    title: 'Data Siswa - ' + new Date().toLocaleDateString('id-ID')
                },
                {
                    extend: 'print',
                    text: '<i class="fas fa-print"></i> Cetak',
                    className: 'btn btn-sm btn-info',
                    exportOptions: {
                        columns: ':visible:not(.not-export)'
                    },
                    title: 'Data Siswa - ' + new Date().toLocaleDateString('id-ID')
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
                    $('.dataTables_filter input').addClass('form-control').attr('placeholder', 'Cari data siswa...');
                    $('.dataTables_length select').addClass('form-control');
                    
                    // Custom styling untuk pagination
                    $('.dataTables_paginate .paginate_button').addClass('page-item').find('a').addClass('page-link');
                    $('.dataTables_paginate .paginate_button.current').addClass('active');
                    $('.dataTables_paginate .paginate_button.disabled').addClass('disabled');
                }, 500);
            }
        });
    }
});
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