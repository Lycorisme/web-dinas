<?php
// FILE: laporan/laporan_rombel.php - Rombongan Belajar Report Page
ob_start();
require_once '../layout/_top.php';
require_once '../helper/connection.php';

// Ambil data kecamatan, jenjang, dan tingkat untuk dropdown
 $kecamatan_query = "SELECT DISTINCT k.nama_kecamatan 
                   FROM kecamatan k 
                   INNER JOIN sekolah_identitas s ON k.id_kecamatan = s.id_kecamatan_fk 
                   ORDER BY k.nama_kecamatan";
 $kecamatan_result = mysqli_query($connection, $kecamatan_query);

$jenjang_query = "SELECT DISTINCT jenjang_pendidikan 
                 FROM sekolah_identitas 
                 WHERE jenjang_pendidikan IS NOT NULL";
$jenjang_result = mysqli_query($connection, $jenjang_query);

// Urutan manual
$urutan_jenjang = ['SD', 'SMP', 'SMA', 'SMK'];

// Ambil hasil query ke array biasa
$jenjang_list = [];
while ($row = mysqli_fetch_assoc($jenjang_result)) {
    $jenjang_list[] = $row['jenjang_pendidikan'];
}

// Urutkan sesuai array urutan_jenjang
usort($jenjang_list, function($a, $b) use ($urutan_jenjang) {
    $posA = array_search($a, $urutan_jenjang);
    $posB = array_search($b, $urutan_jenjang);
    // Jika tidak ditemukan dalam urutan, letakkan di akhir
    $posA = $posA === false ? PHP_INT_MAX : $posA;
    $posB = $posB === false ? PHP_INT_MAX : $posB;
    return $posA <=> $posB;
});


 $tingkat_query = "SELECT DISTINCT tingkat_kelas 
                 FROM rekap_rombel 
                 WHERE tingkat_kelas IS NOT NULL 
                 ORDER BY tingkat_kelas";
 $tingkat_result = mysqli_query($connection, $tingkat_query);

// Proses filter jika ada
 $where_conditions = [];
 $kecamatan_filter = '';
 $jenjang_filter = '';
 $tingkat_filter = '';

if (isset($_GET['kecamatan']) && !empty($_GET['kecamatan'])) {
    $kecamatan_filter = mysqli_real_escape_string($connection, $_GET['kecamatan']);
    $where_conditions[] = "k.nama_kecamatan = '$kecamatan_filter'";
}

if (isset($_GET['jenjang']) && !empty($_GET['jenjang'])) {
    $jenjang_filter = mysqli_real_escape_string($connection, $_GET['jenjang']);
    $where_conditions[] = "s.jenjang_pendidikan = '$jenjang_filter'";
}

if (isset($_GET['tingkat']) && !empty($_GET['tingkat'])) {
    $tingkat_filter = mysqli_real_escape_string($connection, $_GET['tingkat']);
    $where_conditions[] = "rr.tingkat_kelas = '$tingkat_filter'";
}

 $where_clause = '';
if (!empty($where_conditions)) {
    $where_clause = 'WHERE ' . implode(' AND ', $where_conditions);
}

// Query data rombel jika ada filter
 $rombel_data = [];
 $show_data = false;

if (isset($_GET['kecamatan']) || isset($_GET['jenjang']) || isset($_GET['tingkat'])) {
    $show_data = true;
    
    $rombel_query = "SELECT 
                        s.npsn, 
                        s.nama_sekolah, 
                        s.jenjang_pendidikan, 
                        k.nama_kecamatan,
                        rr.tingkat_kelas,
                        SUM(rr.jumlah_laki_laki) AS laki_laki,
                        SUM(rr.jumlah_perempuan) AS perempuan,
                        SUM(rr.jumlah_laki_laki + rr.jumlah_perempuan) AS total
                      FROM rekap_rombel rr
                      LEFT JOIN sekolah_identitas s ON rr.npsn_fk = s.npsn
                      LEFT JOIN kecamatan k ON s.id_kecamatan_fk = k.id_kecamatan
                      $where_clause
                      GROUP BY s.npsn, rr.tingkat_kelas
                      ORDER BY s.nama_sekolah, rr.tingkat_kelas";
    
    $rombel_result = mysqli_query($connection, $rombel_query);
    
    if (!$rombel_result) {
        echo "<!-- Database Error: " . mysqli_error($connection) . " -->";
    } else {
        while ($row = mysqli_fetch_assoc($rombel_result)) {
            $rombel_data[] = $row;
        }
    }
}

// Nama untuk display
 $nama_kecamatan = !empty($kecamatan_filter) ? $kecamatan_filter : 'Semua Kecamatan';
 $nama_jenjang = !empty($jenjang_filter) ? $jenjang_filter : 'Semua Jenjang';
 $nama_tingkat = !empty($tingkat_filter) ? $tingkat_filter : 'Semua Tingkat';
?>

<section class="section">
    <div class="section-header d-flex justify-content-between align-items-center">
        <h1>Laporan & Ekspor Data Rombongan Belajar</h1>
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
                            <div class="col-md-3">
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
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="jenjang">Jenjang</label>
                                        <select name="jenjang" id="jenjang" class="form-control">
                                            <option value="">Semua Jenjang</option>
                                            <?php foreach ($jenjang_list as $jenjang): ?>
                                                <option value="<?php echo htmlspecialchars($jenjang); ?>"
                                                        <?php echo ($jenjang_filter == $jenjang) ? 'selected' : ''; ?>>
                                                    <?php echo htmlspecialchars($jenjang); ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="tingkat">Tingkat</label>
                                    <select name="tingkat" id="tingkat" class="form-control">
                                        <option value="">Semua Tingkat</option>
                                        <?php 
                                        // Reset pointer untuk hasil query
                                        mysqli_data_seek($tingkat_result, 0);
                                        while ($row = mysqli_fetch_assoc($tingkat_result)): ?>
                                            <option value="<?php echo htmlspecialchars($row['tingkat_kelas']); ?>"
                                                    <?php echo ($tingkat_filter == $row['tingkat_kelas']) ? 'selected' : ''; ?>>
                                                <?php echo htmlspecialchars($row['tingkat_kelas']); ?>
                                            </option>
                                        <?php endwhile; ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
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
                        
                        <?php if (!empty($rombel_data)): ?>
                        <div class="row mt-3">
                            <div class="col-12">
                                <div class="btn-group" role="group">
                                    <a href="export_pdf_rombel.php?<?php echo http_build_query($_GET); ?>" target="_blank" class="btn btn-danger">
                                        <i class="fas fa-file-pdf"></i> Export PDF
                                    </a>
                                    <button type="submit" formaction="export_excel_rombel.php" class="btn btn-success">
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

    <?php if ($show_data && empty($rombel_data)): ?>
    <!-- Pesan jika tidak ada data -->
    <div class="row">
        <div class="col-12">
            <div class="alert alert-warning">
                <i class="fas fa-exclamation-triangle"></i> Tidak ada data rombongan belajar yang sesuai dengan filter yang dipilih.
            </div>
        </div>
    </div>
    <?php endif; ?>

    <?php if (!empty($rombel_data)): ?>
    <!-- Data Table -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4><i class="fas fa-table"></i> Data Rombongan Belajar</h4>
                    <div class="card-header-action">
                        <span class="badge badge-primary">
                            <i class="fas fa-database"></i> 
                            <?php echo count($rombel_data); ?> Rombel
                        </span>
                    </div>
                </div>
                <div class="card-body">
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i>
                        <strong>Filter Aktif:</strong> 
                        Kecamatan: <?php echo htmlspecialchars($nama_kecamatan); ?> | 
                        Jenjang: <?php echo htmlspecialchars($nama_jenjang); ?> | 
                        Tingkat: <?php echo htmlspecialchars($nama_tingkat); ?>
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
                                        <th style="min-width: 100px;">Tingkat</th>
                                        <th style="min-width: 100px;">Laki-laki</th>
                                        <th style="min-width: 100px;">Perempuan</th>
                                        <th style="min-width: 100px;">Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $no = 1;
                                    foreach ($rombel_data as $data):
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
                                            <td title="Tingkat: <?= htmlspecialchars($data['tingkat_kelas']) ?>">
                                                <span class="badge badge-warning">
                                                    <i class="fas fa-layer-group mr-1"></i>
                                                    <?= htmlspecialchars($data['tingkat_kelas']) ?>
                                                </span>
                                            </td>
                                            <td title="Laki-laki: <?= $data['laki_laki'] ?>">
                                                <span class="badge badge-primary">
                                                    <i class="fas fa-male mr-1"></i>
                                                    <?= $data['laki_laki'] ?>
                                                </span>
                                            </td>
                                            <td title="Perempuan: <?= $data['perempuan'] ?>">
                                                <span class="badge badge-danger">
                                                    <i class="fas fa-female mr-1"></i>
                                                    <?= $data['perempuan'] ?>
                                                </span>
                                            </td>
                                            <td title="Total: <?= $data['total'] ?>">
                                                <span class="badge badge-success">
                                                    <i class="fas fa-users mr-1"></i>
                                                    <?= $data['total'] ?>
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
// Data tingkat berdasarkan jenjang
const tingkatByJenjang = {
    'SD': ['Kelas 1', 'Kelas 2', 'Kelas 3', 'Kelas 4', 'Kelas 5', 'Kelas 6'],
    'SMP': ['Kelas 7', 'Kelas 8', 'Kelas 9'],
    'SMA': ['Kelas 10', 'Kelas 11', 'Kelas 12'],
    'SMK': ['Kelas 10', 'Kelas 11', 'Kelas 12']
};

// Fungsi untuk memperbarui opsi tingkat berdasarkan jenjang
function updateTingkatOptions() {
    const jenjang = $('#jenjang').val();
    const $tingkatDropdown = $('#tingkat');
    
    // Simpan nilai lama
    const oldValue = $tingkatDropdown.val();

    // Kosongkan semua opsi
    $tingkatDropdown.empty();
    
    // Tambahkan opsi default
    $tingkatDropdown.append('<option value="">Semua Tingkat</option>');
    
    if (jenjang && tingkatByJenjang[jenjang]) {
        // Tambahkan opsi sesuai jenjang
        tingkatByJenjang[jenjang].forEach(function(tingkat) {
            $tingkatDropdown.append(`<option value="${tingkat}">${tingkat}</option>`);
        });
    } else {
        // Jika jenjang tidak dipilih, tampilkan semua kemungkinan tingkat
        Object.values(tingkatByJenjang).flat().forEach(function(tingkat) {
            $tingkatDropdown.append(`<option value="${tingkat}">${tingkat}</option>`);
        });
    }

    // Kembalikan ke nilai sebelumnya jika masih valid
    if ($tingkatDropdown.find(`option[value="${oldValue}"]`).length > 0) {
        $tingkatDropdown.val(oldValue);
    } else {
        $tingkatDropdown.val('');
    }
}

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

    // Inisialisasi dropdown tingkat saat halaman dimuat
    updateTingkatOptions();

    // Event listener untuk perubahan jenjang
    $('#jenjang').on('change', function() {
        updateTingkatOptions();
    });

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
                    title: 'Data Rombongan Belajar - ' + new Date().toLocaleDateString('id-ID')
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
                    title: 'Data Rombongan Belajar - ' + new Date().toLocaleDateString('id-ID')
                },
                {
                    extend: 'print',
                    text: '<i class="fas fa-print"></i> Cetak',
                    className: 'btn btn-sm btn-info',
                    exportOptions: {
                        columns: ':visible:not(.not-export)'
                    },
                    title: 'Data Rombongan Belajar - ' + new Date().toLocaleDateString('id-ID')
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
                    $('.dataTables_filter input').addClass('form-control').attr('placeholder', 'Cari data rombongan belajar...');
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