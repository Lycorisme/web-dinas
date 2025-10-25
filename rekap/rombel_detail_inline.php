<?php
// FILE: rekap/rombel_detail_inline.php - Detail Data Rombel per Sekolah
require_once '../helper/connection.php';
 $npsn = $_GET['npsn'];

// Ambil data sekolah
 $sekolah = mysqli_fetch_assoc(mysqli_query($connection, "SELECT nama_sekolah, jenjang_pendidikan FROM sekolah_identitas WHERE npsn = '$npsn'"));

// Ambil data rombel untuk sekolah ini
 $result = mysqli_query($connection, "SELECT * FROM rekap_rombel WHERE npsn_fk = '$npsn' ORDER BY tingkat_kelas");
?>

<div class="card shadow-sm mb-3">
    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
        <h5 class="mb-0"><i class="fas fa-school mr-2"></i>Detail Data: <?= htmlspecialchars($sekolah['nama_sekolah']) ?></h5>
        <a href="rombel_create.php?npsn=<?= $npsn ?>" class="btn btn-sm btn-light">
            <i class="fas fa-plus mr-1"></i> Tambah Data
        </a>
    </div>
    <div class="card-body p-0">
        <?php if (mysqli_num_rows($result) > 0): ?>
            <div class="table-responsive">
                <table class="table table-sm table-hover table-striped mb-0">
                    <thead class="thead-light">
                        <tr>
                            <th>Tingkat Kelas</th>
                            <th class="text-center">Laki-laki</th>
                            <th class="text-center">Perempuan</th>
                            <th class="text-center">Total</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = mysqli_fetch_assoc($result)): ?>
                            <?php 
                            // Tentukan tampilan badge berdasarkan tingkat kelas
                            $tingkat_kelas = $row['tingkat_kelas'];
                            
                            // Badge class berdasarkan tingkat kelas
                            switch($tingkat_kelas) {
                                case 'Kelas 1':
                                case 'Kelas 7':
                                case 'Kelas 10':
                                    $badge_class = 'badge-primary';
                                    break;
                                case 'Kelas 2':
                                case 'Kelas 8':
                                case 'Kelas 11':
                                    $badge_class = 'badge-info';
                                    break;
                                case 'Kelas 3':
                                case 'Kelas 9':
                                case 'Kelas 12':
                                    $badge_class = 'badge-success';
                                    break;
                                case 'Kelas 4':
                                    $badge_class = 'badge-warning';
                                    break;
                                case 'Kelas 5':
                                    $badge_class = 'badge-danger';
                                    break;
                                case 'Kelas 6':
                                    $badge_class = 'badge-secondary';
                                    break;
                                default:
                                    $badge_class = 'badge-light border border-dark text-dark';
                            }
                            ?>
                            <tr>
                                <td>
                                    <span class="badge <?= $badge_class ?>"><i class="fas fa-layer-group mr-1"></i> <?= htmlspecialchars($tingkat_kelas) ?></span>
                                </td>
                                <td class="text-center">
                                    <span class="badge badge-primary badge-pill px-3 py-2">
                                        <i class="fas fa-male mr-1"></i>
                                        <?= $row['jumlah_laki_laki'] ?>
                                    </span>
                                </td>
                                <td class="text-center">
                                    <span class="badge badge-danger badge-pill px-3 py-2">
                                        <i class="fas fa-female mr-1"></i>
                                        <?= $row['jumlah_perempuan'] ?>
                                    </span>
                                </td>
                                <td class="text-center">
                                    <span class="badge badge-success badge-pill px-3 py-2">
                                        <i class="fas fa-users mr-1"></i>
                                        <?= $row['jumlah_total'] ?>
                                    </span>
                                </td>
                                <td class="text-center">
                                    <div class="btn-group btn-group-sm" role="group">
                                        <a class="btn btn-info" href="rombel_edit.php?id=<?= $row['id'] ?>"
                                           title="Edit Data" data-toggle="tooltip" data-placement="top">
                                            <i class="fas fa-edit fa-fw"></i>
                                        </a>
                                        <a href="javascript:void(0);" class="btn btn-danger" 
                                           title="Hapus Data" data-toggle="tooltip" data-placement="top"
                                           onclick="confirmDeleteDetail('<?= $row['id'] ?>', '<?= $tingkat_kelas ?>')">
                                            <i class="fas fa-trash fa-fw"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <div class="text-center py-4">
                <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                <h5 class="text-muted">Belum ada data rombel untuk sekolah ini</h5>
                <a href="rombel_create.php?npsn=<?= $npsn ?>" class="btn btn-sm btn-primary mt-2">
                    <i class="fas fa-plus mr-1"></i> Tambah Data
                </a>
            </div>
        <?php endif; ?>
    </div>
</div>

<div class="row mt-3">
    <div class="col-md-4">
        <div class="card bg-light border-0">
            <div class="card-body text-center">
                <h5 class="text-primary"><i class="fas fa-male mr-2"></i>Total Laki-laki</h5>
                <?php 
                $total_laki = mysqli_fetch_assoc(mysqli_query($connection, "SELECT SUM(jumlah_laki_laki) as total FROM rekap_rombel WHERE npsn_fk = '$npsn'"));
                echo '<h2 class="font-weight-bold">' . ($total_laki['total'] ?: 0) . '</h2>';
                ?>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card bg-light border-0">
            <div class="card-body text-center">
                <h5 class="text-danger"><i class="fas fa-female mr-2"></i>Total Perempuan</h5>
                <?php 
                $total_perempuan = mysqli_fetch_assoc(mysqli_query($connection, "SELECT SUM(jumlah_perempuan) as total FROM rekap_rombel WHERE npsn_fk = '$npsn'"));
                echo '<h2 class="font-weight-bold">' . ($total_perempuan['total'] ?: 0) . '</h2>';
                ?>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card bg-light border-0">
            <div class="card-body text-center">
                <h5 class="text-success"><i class="fas fa-users mr-2"></i>Total Siswa</h5>
                <?php 
                $total_siswa = mysqli_fetch_assoc(mysqli_query($connection, "SELECT SUM(jumlah_total) as total FROM rekap_rombel WHERE npsn_fk = '$npsn'"));
                echo '<h2 class="font-weight-bold">' . ($total_siswa['total'] ?: 0) . '</h2>';
                ?>
            </div>
        </div>
    </div>
</div>

<script>
// Fungsi untuk konfirmasi hapus di dalam detail
function confirmDeleteDetail(id, tingkat_kelas) {
    Swal.fire({
        title: 'Apakah Anda yakin?',
        text: "Anda akan menghapus data " + tingkat_kelas + "!",
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

// Buat chart untuk visualisasi data
 $(document).ready(function() {
    // Ambil data untuk chart
    let labels = [];
    let dataLaki = [];
    let dataPerempuan = [];
    
    $('tbody tr').each(function() {
        const tingkat = $(this).find('td:first span').text();
        const laki = parseInt($(this).find('td:nth-child(2) span').text());
        const perempuan = parseInt($(this).find('td:nth-child(3) span').text());
        
        labels.push(tingkat);
        dataLaki.push(laki);
        dataPerempuan.push(perempuan);
    });
    
    // Buat chart
    const ctx = document.getElementById('rombelChart').getContext('2d');
    const rombelChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [
                {
                    label: 'Laki-laki',
                    data: dataLaki,
                    backgroundColor: 'rgba(54, 162, 235, 0.7)',
                    borderColor: 'rgba(54, 162, 235, 1)',
                    borderWidth: 1
                },
                {
                    label: 'Perempuan',
                    data: dataPerempuan,
                    backgroundColor: 'rgba(255, 99, 132, 0.7)',
                    borderColor: 'rgba(255, 99, 132, 1)',
                    borderWidth: 1
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        precision: 0
                    }
                }
            },
            plugins: {
                legend: {
                    position: 'top',
                },
                title: {
                    display: true,
                    text: 'Distribusi Siswa per Tingkat Kelas'
                }
            }
        }
    });
});
</script>