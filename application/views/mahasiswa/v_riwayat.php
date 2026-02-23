<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0 text-dark">Riwayat & Catatan Revisi</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="<?php echo base_url('dashboard'); ?>">Home</a></li>
                        <li class="breadcrumb-item active">Riwayat Revisi</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">
            
            <div class="row">
                <div class="col-12">
                    
                    <div class="card card-outline card-navy shadow-sm">
                        <div class="card-header">
                            <h3 class="card-title font-weight-bold">
                                <i class="fas fa-history mr-1"></i> Daftar Riwayat Bimbingan
                            </h3>
                            <div class="card-tools">
                                <a href="<?php echo base_url('mahasiswa/bimbingan'); ?>" class="btn btn-sm btn-primary shadow-sm">
                                    <i class="fas fa-arrow-left mr-1"></i> Kembali ke Upload
                                </a>
                            </div>
                        </div>
                        
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover table-striped align-middle mb-0">
                                    <thead class="bg-light">
                                        <tr>
                                            <th class="text-center" style="width: 8%;">BAB</th>
                                            <th style="width: 20%;">Judul Skripsi</th>
                                            <th style="width: 25%;">Status Kelulusan</th>
                                            <th style="width: 37%;">Catatan Dosen</th>
                                               <th class="text-center sortable" data-sort="bab" style="width: 8%">BAB</th>
                                            <th class="text-center" style="width: 10%;">Detail</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (empty($progres)): ?>
                                            <tr>
                                                <td colspan="5" class="text-center py-5 text-muted">
                                                    <i class="fas fa-clipboard-list fa-3x mb-3 text-gray"></i><br>
                                                    <span class="font-weight-bold">Belum ada riwayat bimbingan.</span>
                                                </td>
                                            </tr>
                                        <?php else: ?>
                                            <?php foreach ($progres as $p): ?>
                                            <tr>
                                                <td class="text-center align-top pt-3">
                                                    <span class="badge badge-info badge-pill px-3 py-2 shadow-sm" style="font-size: 1rem;">
                                                        <?php echo $p['bab']; ?>
                                                    </span>
                                                </td>

                                                <td class="align-top pt-3">
                                                    <strong><?php echo $p['judul']; ?></strong>
                                                </td>

                                                <td class="align-top pt-3">
                                                    <div class="mb-2">
                                                        <small class="text-muted font-weight-bold d-block mb-1">Pembimbing 1</small>
                                                        <?php 
                                                        $badge1 = ($p['progres_dosen1'] == 100) ? 'success' : (($p['progres_dosen1'] == 50) ? 'warning' : 'danger');
                                                        $icon1 = ($p['progres_dosen1'] == 100) ? 'check' : (($p['progres_dosen1'] == 50) ? 'exclamation' : 'times');
                                                        if ($p['nilai_dosen1'] == 'Menunggu' || $p['nilai_dosen1'] == '') { $badge1 = 'secondary'; $icon1 = 'clock'; }
                                                        ?>
                                                        <span class="badge badge-<?php echo $badge1; ?> px-2 py-1">
                                                            <i class="fas fa-<?php echo $icon1; ?> mr-1"></i> <?php echo $p['nilai_dosen1'] ?: 'Menunggu'; ?>
                                                        </span>
                                                    </div>
                                                    
                                                    <div>
                                                        <small class="text-muted font-weight-bold d-block mb-1">Pembimbing 2</small>
                                                        <?php 
                                                        $badge2 = ($p['progres_dosen2'] == 100) ? 'success' : (($p['progres_dosen2'] == 50) ? 'warning' : 'danger');
                                                        $icon2 = ($p['progres_dosen2'] == 100) ? 'check' : (($p['progres_dosen2'] == 50) ? 'exclamation' : 'times');
                                                        if ($p['nilai_dosen2'] == 'Menunggu' || $p['nilai_dosen2'] == '') { $badge2 = 'secondary'; $icon2 = 'clock'; }
                                                        ?>
                                                        <span class="badge badge-<?php echo $badge2; ?> px-2 py-1">
                                                            <i class="fas fa-<?php echo $icon2; ?> mr-1"></i> <?php echo $p['nilai_dosen2'] ?: 'Menunggu'; ?>
                                                        </span>
                                                    </div>
                                                </td>

                                                <td class="align-top pt-3">
                                                    <?php if($p['komentar_dosen1'] || $p['komentar_dosen2']): ?>
                                                        
                                                        <?php if($p['komentar_dosen1']): ?>
                                                            <div class="callout callout-info py-2 px-3 mb-2 text-sm bg-white shadow-sm border-left-info">
                                                                <strong class="text-primary d-block mb-1">P1:</strong> 
                                                                <?php echo $p['komentar_dosen1']; ?>
                                                            </div>
                                                        <?php endif; ?>

                                                        <?php if($p['komentar_dosen2']): ?>
                                                            <div class="callout callout-secondary py-2 px-3 mb-0 text-sm bg-white shadow-sm border-left-secondary">
                                                                <strong class="text-secondary d-block mb-1">P2:</strong> 
                                                                <?php echo $p['komentar_dosen2']; ?>
                                                            </div>
                                                        <?php endif; ?>

                                                    <?php else: ?>
                                                        <span class="text-muted font-italic text-sm">- Tidak ada komentar -</span>
                                                    <?php endif; ?>
                                                </td>

                                                <td class="text-center align-top pt-3">
                                                    <div class="mb-2">
                                                        <a href="<?php echo base_url('uploads/progres/' . $p['file']); ?>" target="_blank" class="btn btn-outline-danger btn-sm btn-block shadow-sm">
                                                            <i class="fas fa-file-pdf mr-1"></i> Lihat File
                                                        </a>
                                                    </div>
                                                    <small class="text-muted d-block mt-1">
                                                        <i class="far fa-calendar-alt mr-1"></i> <?php echo date('d/m/Y', strtotime($p['created_at'])); ?>
                                                    </small>
                                                    <small class="text-muted">
                                                        <i class="far fa-clock mr-1"></i> <?php echo date('H:i', strtotime($p['created_at'])); ?>
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
    </section>
</div>

<style>
    .border-left-info { border-left: 3px solid #17a2b8 !important; }
    .border-left-secondary { border-left: 3px solid #6c757d !important; }
    .callout { border-radius: 0.25rem; }
</style>