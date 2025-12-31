<?php 
$prodi = $this->session->userdata('prodi');
?>

<div class="container-fluid">
    <div class="row mb-3">
        <div class="col-md-12">
            <div class="callout callout-info shadow-sm bg-white">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="text-info font-weight-bold mb-1"><i class="fas fa-university mr-1"></i> Program Studi: <?php echo $prodi; ?></h5>
                        <p class="text-muted text-sm mb-0">Memantau progres skripsi seluruh mahasiswa angkatan aktif.</p>
                    </div>
                    <div class="text-right">
                         <h3 class="mb-0 text-dark font-weight-bold"><?php echo count($mahasiswa_prodi); ?></h3>
                         <small>Total Mahasiswa</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card card-outline card-purple shadow-sm">
                <div class="card-header">
                    <h3 class="card-title mt-1"><i class="fas fa-list-alt mr-1"></i> Data Mahasiswa</h3>
                    <div class="card-tools">
                        <div class="input-group input-group-sm" style="width: 250px;">
                            <input type="text" id="searchMhs" class="form-control float-right" placeholder="Cari Nama / NPM...">
                            <div class="input-group-append">
                                <button type="submit" class="btn btn-default"><i class="fas fa-search"></i></button>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="card-body table-responsive p-0" style="height: 500px;">
                    <table class="table table-head-fixed table-hover text-nowrap table-striped" id="tableMonitoring">
                        <thead>
                            <tr class="text-center">
                                <th>No</th>
                                <th>NPM</th>
                                <th class="text-left">Nama Mahasiswa</th>
                                <th>Angkatan</th>
                                <th class="text-left">Judul Skripsi</th>
                                <th>Pembimbing</th>
                                <th>Status / Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($mahasiswa_prodi)): ?>
                                <tr>
                                    <td colspan="7" class="text-center py-5 text-muted">
                                        <i class="fas fa-users-slash fa-3x mb-3 opacity-50"></i><br>
                                        Tidak ada data mahasiswa di prodi ini.
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php $no = 1; foreach ($mahasiswa_prodi as $m): ?>
                                <tr>
                                    <td class="align-middle text-center"><?php echo $no++; ?></td>
                                    <td class="align-middle text-center"><span class="badge badge-light border"><?php echo $m['npm']; ?></span></td>
                                    <td class="align-middle font-weight-bold"><?php echo $m['nama']; ?></td>
                                    <td class="align-middle text-center"><?php echo $m['angkatan']; ?></td>
                                    <td class="align-middle text-wrap" style="min-width: 200px;">
                                        <?php if($m['judul']): ?>
                                            <small class="d-block"><?php echo $m['judul']; ?></small>
                                        <?php else: ?>
                                            <span class="badge badge-danger">Belum Ada Judul</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="align-middle text-sm">
                                        <div class="text-muted text-xs">P1: <?php echo $m['p1'] ?: '-'; ?></div>
                                        <div class="text-muted text-xs">P2: <?php echo $m['p2'] ?: '-'; ?></div>
                                    </td>
                                    <td class="align-middle text-center">
                                        <?php if($m['id_skripsi']): ?>
                                            <div class="btn-group">
                                                <?php if($m['status_acc_kaprodi'] == 'menunggu'): ?>
                                                    <a href="<?= base_url('dosen/setuju_judul/'.$m['id_skripsi']) ?>" class="btn btn-xs btn-success" onclick="return confirm('Setujui Judul & Pembimbing?')">ACC</a>
                                                    <a href="<?= base_url('dosen/tolak_judul/'.$m['id_skripsi']) ?>" class="btn btn-xs btn-danger" onclick="return confirm('Tolak Judul?')">Tolak</a>
                                                <?php else: ?>
                                                    <span class="badge badge-<?= ($m['status_acc_kaprodi'] == 'diterima') ? 'success' : 'danger' ?> mr-1">
                                                        <?= strtoupper($m['status_acc_kaprodi']) ?>
                                                    </span>
                                                <?php endif; ?>
                                                <a href="<?= base_url('chat?id_lawan='.$m['id_user']) ?>" class="btn btn-xs btn-info"><i class="fas fa-comment"></i></a>
                                            </div>
                                        <?php else: ?>
                                            <span class="text-muted">-</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.getElementById('searchMhs').addEventListener('keyup', function() {
    var val = this.value.toLowerCase();
    var rows = document.querySelectorAll('#tableMonitoring tbody tr');
    rows.forEach(function(row) {
        row.style.display = row.textContent.toLowerCase().indexOf(val) > -1 ? '' : 'none';
    });
});
</script>