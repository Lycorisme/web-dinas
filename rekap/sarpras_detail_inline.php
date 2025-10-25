<?php
// FILE: rekap/sarpras_detail_inline.php - Detail Data Sarpras per Sekolah
require_once '../helper/connection.php';
 $npsn = $_GET['npsn'];

// Ambil data sekolah
 $sekolah = mysqli_fetch_assoc(mysqli_query($connection, "SELECT nama_sekolah FROM sekolah_identitas WHERE npsn = '$npsn'"));

// Ambil data sarpras untuk sekolah ini
 $result = mysqli_query($connection, "SELECT * FROM rekap_sarpras WHERE npsn_fk = '$npsn' ORDER BY sarana");
?>

<div class="card shadow-sm mb-3">
    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
        <h5 class="mb-0"><i class="fas fa-school mr-2"></i>Detail Data: <?= htmlspecialchars($sekolah['nama_sekolah']) ?></h5>
        <a href="sarpras_create.php?npsn=<?= $npsn ?>" class="btn btn-sm btn-light">
            <i class="fas fa-plus mr-1"></i> Tambah Data
        </a>
    </div>
    <div class="card-body p-0">
        <?php if (mysqli_num_rows($result) > 0): ?>
            <div class="table-responsive">
                <table class="table table-sm table-hover table-striped mb-0">
                    <thead class="thead-light">
                        <tr>
                            <th>Sarana</th>
                            <th class="text-center">Jumlah</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = mysqli_fetch_assoc($result)): ?>
                            <?php 
                            // Tentukan tampilan badge berdasarkan jenis sarana
                            $sarana = $row['sarana'];
                            
                            // Badge class berdasarkan jenis sarana
                            switch($sarana) {
                                case 'Ruang Kelas':
                                    $badge_class = 'badge-primary';
                                    $icon = 'fas fa-door-open';
                                    break;
                                case 'Ruang Lab':
                                    $badge_class = 'badge-info';
                                    $icon = 'fas fa-flask';
                                    break;
                                case 'Ruang Perpus':
                                    $badge_class = 'badge-success';
                                    $icon = 'fas fa-book';
                                    break;
                                case 'Ruang Guru':
                                    $badge_class = 'badge-warning';
                                    $icon = 'fas fa-chalkboard-teacher';
                                    break;
                                case 'Ruang Kepala Sekolah':
                                    $badge_class = 'badge-danger';
                                    $icon = 'fas fa-user-tie';
                                    break;
                                case 'Ruang TU':
                                    $badge_class = 'badge-secondary';
                                    $icon = 'fas fa-file-invoice';
                                    break;
                                case 'Ruang UKS':
                                    $badge_class = 'badge-info';
                                    $icon = 'fas fa-first-aid';
                                    break;
                                case 'Ruang Toilet':
                                    $badge_class = 'badge-warning';
                                    $icon = 'fas fa-restroom';
                                    break;
                                case 'Ruang Gudang':
                                    $badge_class = 'badge-secondary';
                                    $icon = 'fas fa-warehouse';
                                    break;
                                case 'Lainnya':
                                    $badge_class = 'badge-light border border-dark text-dark';
                                    $icon = 'fas fa-ellipsis-h';
                                    break;
                                default:
                                    $badge_class = 'badge-light border border-dark text-dark';
                                    $icon = 'fas fa-cube';
                            }
                            ?>
                            <tr>
                                <td>
                                    <span class="badge <?= $badge_class ?>"><i class="<?= $icon ?> mr-1"></i> <?= htmlspecialchars($sarana) ?></span>
                                </td>
                                <td class="text-center">
                                    <span class="badge badge-success badge-pill px-3 py-2">
                                        <i class="fas fa-hashtag mr-1"></i>
                                        <?= $row['jumlah'] ?>
                                    </span>
                                </td>
                                <td class="text-center">
                                    <div class="btn-group btn-group-sm" role="group">
                                        <a class="btn btn-info" href="sarpras_edit.php?id=<?= $row['id'] ?>"
                                           title="Edit Data" data-toggle="tooltip" data-placement="top">
                                            <i class="fas fa-edit fa-fw"></i>
                                        </a>
                                        <a href="javascript:void(0);" class="btn btn-danger" 
                                           title="Hapus Data" data-toggle="tooltip" data-placement="top"
                                           onclick="confirmDeleteDetail('<?= $row['id'] ?>', '<?= $sarana ?>')">
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
                <h5 class="text-muted">Belum ada data sarana prasarana untuk sekolah ini</h5>
                <a href="sarpras_create.php?npsn=<?= $npsn ?>" class="btn btn-sm btn-primary mt-2">
                    <i class="fas fa-plus mr-1"></i> Tambah Data
                </a>
            </div>
        <?php endif; ?>
    </div>
</div>

<div class="row mt-3">
    <div class="col-md-12">
        <div class="card bg-light border-0">
            <div class="card-body text-center">
                <h5 class="text-primary"><i class="fas fa-cube mr-2"></i>Total Sarana Prasarana</h5>
                <?php 
                $total_sarpras = mysqli_fetch_assoc(mysqli_query($connection, "SELECT COUNT(*) as total FROM rekap_sarpras WHERE npsn_fk = '$npsn'"));
                echo '<h2 class="font-weight-bold">' . ($total_sarpras['total'] ?: 0) . '</h2>';
                ?>
            </div>
        </div>
    </div>
</div>

<script>
// Fungsi untuk konfirmasi hapus di dalam detail
function confirmDeleteDetail(id, sarana) {
    Swal.fire({
        title: 'Apakah Anda yakin?',
        text: "Anda akan menghapus data " + sarana + "!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Ya, hapus!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = `sarpras_hapus.php?id=${id}`;
        }
    })
}
</script>