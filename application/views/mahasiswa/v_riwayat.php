<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            
            <div class="card card-outline card-navy shadow">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-history mr-1"></i> Riwayat & Catatan Revisi</h3>
                    <div class="card-tools">
                        <a href="<?php echo base_url('mahasiswa/bimbingan'); ?>" class="btn btn-sm btn-primary">
                            <i class="fas fa-arrow-left mr-1"></i> Kembali ke Upload
                        </a>
                    </div>
                </div>
                
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover table-striped align-middle">
                            <thead class="bg-light">
                                <tr>
                                    <th class="text-center" style="width: 8%;">BAB</th>
                                    <th style="width: 25%;">Status Kelulusan</th>
                                    <th style="width: 47%;">Catatan Dosen</th>
                                    <th class="text-center" style="width: 20%;">Detail</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($progres)): ?>
                                    <tr>
                                        <td colspan="4" class="text-center py-5 text-muted">Belum ada riwayat bimbingan.</td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($progres as $p): ?>
                                    <tr>
                                        <td class="text-center align-top pt-3">
                                            <span class="badge badge-info badge-pill px-3 py-2" style="font-size: 1rem;">
                                                <?php echo $p['bab']; ?>
                                            </span>
                                        </td>

                                        <td class="align-top pt-3">
                                            <div class="mb-2">
                                                <small class="text-muted font-weight-bold d-block">Pembimbing 1</small>
                                                <?php 
                                                $badge1 = ($p['progres_dosen1'] == 100) ? 'success' : (($p['progres_dosen1'] == 50) ? 'warning' : 'danger');
                                                $icon1 = ($p['progres_dosen1'] == 100) ? 'check' : (($p['progres_dosen1'] == 50) ? 'exclamation' : 'times');
                                                ?>
                                                <span class="badge badge-<?php echo $badge1; ?>">
                                                    <i class="fas fa-<?php echo $icon1; ?>"></i> <?php echo $p['nilai_dosen1'] ?: 'Menunggu'; ?>
                                                </span>
                                            </div>
                                            <div>
                                                <small class="text-muted font-weight-bold d-block">Pembimbing 2</small>
                                                <?php 
                                                $badge2 = ($p['progres_dosen2'] == 100) ? 'success' : (($p['progres_dosen2'] == 50) ? 'warning' : 'danger');
                                                $icon2 = ($p['progres_dosen2'] == 100) ? 'check' : (($p['progres_dosen2'] == 50) ? 'exclamation' : 'times');
                                                ?>
                                                <span class="badge badge-<?php echo $badge2; ?>">
                                                    <i class="fas fa-<?php echo $icon2; ?>"></i> <?php echo $p['nilai_dosen2'] ?: 'Menunggu'; ?>
                                                </span>
                                            </div>
                                        </td>

                                        <td class="align-top pt-3">
                                            <?php if($p['komentar_dosen1']): ?>
                                                <div class="callout callout-info py-2 px-3 mb-2 text-sm">
                                                    <strong class="text-primary">P1:</strong> <?php echo $p['komentar_dosen1']; ?>
                                                </div>
                                            <?php endif; ?>
                                            <?php if($p['komentar_dosen2']): ?>
                                                <div class="callout callout-secondary py-2 px-3 mb-0 text-sm">
                                                    <strong class="text-secondary">P2:</strong> <?php echo $p['komentar_dosen2']; ?>
                                                </div>
                                            <?php endif; ?>
                                            <?php if(!$p['komentar_dosen1'] && !$p['komentar_dosen2']) echo '<span class="text-muted font-italic">- Tidak ada komentar -</span>'; ?>
                                        </td>

                                        <td class="text-center align-top pt-3">
                                            <a href="<?php echo base_url('uploads/progres/' . $p['file']); ?>" target="_blank" class="btn btn-outline-danger btn-sm btn-block shadow-sm">
                                                <i class="fas fa-file-pdf mr-1"></i> Lihat File
                                            </a>
                                            <small class="text-muted d-block mt-1">
                                                <?php echo date('d/m/Y H:i', strtotime($p['created_at'])); ?>
                                            </small>
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
</div>