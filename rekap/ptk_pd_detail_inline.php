<?php
// FILE: rekap/ptk_pd_detail_inline.php - Detail Data PTK PD per Sekolah
require_once '../helper/connection.php';
 $npsn = $_GET['npsn'];

// Ambil data sekolah
 $sekolah = mysqli_fetch_assoc(mysqli_query($connection, "SELECT nama_sekolah FROM sekolah_identitas WHERE npsn = '$npsn'"));

// Ambil data gender untuk sekolah ini dengan menghilangkan spasi berlebih
 $result = mysqli_query($connection, "SELECT * FROM rekap_ptk_pd WHERE npsn_fk = '$npsn' ORDER BY TRIM(deskripsi)");
?>

<div class="card shadow-sm mb-3">
    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
        <h5 class="mb-0"><i class="fas fa-school mr-2"></i>Detail Data: <?= htmlspecialchars($sekolah['nama_sekolah']) ?></h5>
        <a href="ptk_pd_create.php?npsn=<?= $npsn ?>" class="btn btn-sm btn-light">
            <i class="fas fa-plus mr-1"></i> Tambah Data
        </a>
    </div>
    <div class="card-body p-0">
        <?php if (mysqli_num_rows($result) > 0): ?>
            <div class="table-responsive">
                <table class="table table-sm table-hover table-striped mb-0">
                    <thead class="thead-light">
                        <tr>
                            <th>Deskripsi</th>
                            <th class="text-center">Guru</th>
                            <th class="text-center">Tendik</th>
                            <th class="text-center">Total PTK</th>
                            <th class="text-center">Total PD</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = mysqli_fetch_assoc($result)): ?>
                            <?php 
                            // Normalisasi deskripsi untuk menghindari duplikasi karena spasi
                            $deskripsi = trim($row['deskripsi']);
                            
                            // Normalisasi lebih lanjut untuk mengatasi perbedaan format
                            $normalized_deskripsi = strtolower(str_replace([' ', '-'], '', $deskripsi));
                            
                            // Tentukan tampilan badge berdasarkan jenis kelamin
                            if ($normalized_deskripsi == 'lakilaki') {
                                $badge_class = 'badge-light border border-info text-info';
                                $icon = 'fas fa-mars';
                                $label = 'Laki-laki';
                            } elseif ($normalized_deskripsi == 'perempuan') {
                                $badge_class = 'badge-light border border-danger text-danger';
                                $icon = 'fas fa-venus';
                                $label = 'Perempuan';
                            } else {
                                $badge_class = 'badge-secondary';
                                $icon = 'fas fa-user';
                                $label = htmlspecialchars($deskripsi);
                            }
                            ?>
                            <tr>
                                <td>
                                    <span class="badge <?= $badge_class ?>"><i class="<?= $icon ?> mr-1"></i> <?= $label ?></span>
                                </td>
                                <td class="text-center"><?= $row['guru'] ?></td>
                                <td class="text-center"><?= $row['tendik'] ?></td>
                                <td class="text-center">
                                    <span class="badge badge-success"><?= $row['ptk_total'] ?></span>
                                </td>
                                <td class="text-center">
                                    <span class="badge badge-danger"><?= $row['pd_total'] ?></span>
                                </td>
                                <td class="text-center">
                                    <div class="btn-group btn-group-sm" role="group">
                                        <a class="btn btn-info" href="ptk_pd_edit.php?id=<?= $row['id'] ?>"
                                           title="Edit Data" data-toggle="tooltip" data-placement="top">
                                            <i class="fas fa-edit fa-fw"></i>
                                        </a>
                                        <a href="javascript:void(0);" class="btn btn-danger" 
                                           title="Hapus Data" data-toggle="tooltip" data-placement="top"
                                           onclick="confirmDeleteDetail('<?= $row['id'] ?>', '<?= $deskripsi ?>')">
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
                <h5 class="text-muted">Belum ada data gender untuk sekolah ini</h5>
                <a href="ptk_pd_create.php?npsn=<?= $npsn ?>" class="btn btn-sm btn-primary mt-2">
                    <i class="fas fa-plus mr-1"></i> Tambah Data
                </a>
            </div>
        <?php endif; ?>
    </div>
</div>

<div class="row mt-3">
    <div class="col-md-6">
        <div class="card bg-light border-0">
            <div class="card-body text-center">
                <h5 class="text-primary"><i class="fas fa-users-cog mr-2"></i>Total PTK</h5>
                <?php 
                $total_ptk = mysqli_fetch_assoc(mysqli_query($connection, "SELECT SUM(ptk_total) as total FROM rekap_ptk_pd WHERE npsn_fk = '$npsn'"));
                echo '<h2 class="font-weight-bold">' . ($total_ptk['total'] ?: 0) . '</h2>';
                ?>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card bg-light border-0">
            <div class="card-body text-center">
                <h5 class="text-danger"><i class="fas fa-child mr-2"></i>Total Peserta Didik</h5>
                <?php 
                $total_pd = mysqli_fetch_assoc(mysqli_query($connection, "SELECT SUM(pd_total) as total FROM rekap_ptk_pd WHERE npsn_fk = '$npsn'"));
                echo '<h2 class="font-weight-bold">' . ($total_pd['total'] ?: 0) . '</h2>';
                ?>
            </div>
        </div>
    </div>
</div>

<script>
// Fungsi untuk konfirmasi hapus di dalam detail
function confirmDeleteDetail(id, deskripsi) {
    Swal.fire({
        title: 'Apakah Anda yakin?',
        text: "Anda akan menghapus data " + deskripsi + "!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Ya, hapus!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = `ptk_pd_hapus.php?id=${id}`;
        }
    })
}
</script>