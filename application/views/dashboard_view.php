<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0 text-dark font-weight-bold">Dashboard</h1>
                </div>
            </div>
        </div>
    </div>

    <section class="content">
        <div class="container-fluid">
            
            <div class="row">

                <?php if ($role == 'operator' || $role == 'tata_usaha'): ?>

                    <div class="col-lg-3 col-6">
                        <div class="small-box bg-info shadow-sm">
                            <div class="inner">
                                <h3><?php echo $statistik['total_mhs']; ?></h3>
                                <p>Total Mahasiswa</p>
                            </div>
                            <div class="icon"><i class="fas fa-users"></i></div>
                            <a href="<?php echo base_url('operator/data_mahasiswa'); ?>" class="small-box-footer">
                                Lihat Detail <i class="fas fa-arrow-circle-right"></i>
                            </a>
                        </div>
                    </div>

                    <div class="col-lg-3 col-6">
                        <div class="small-box bg-success shadow-sm">
                            <div class="inner">
                                <h3><?php echo $statistik['mhs_skripsi']; ?></h3>
                                <p>Sedang Skripsi</p>
                            </div>
                            <div class="icon"><i class="fas fa-book-reader"></i></div>
                            <a href="<?php echo base_url('operator/monitoring_progres'); ?>" class="small-box-footer">
                                Monitoring <i class="fas fa-arrow-circle-right"></i>
                            </a>
                        </div>
                    </div>

                    <div class="col-lg-3 col-6">
                        <div class="small-box bg-danger shadow-sm">
                            <div class="inner">
                                <h3><?php echo $statistik['total_dosen']; ?></h3>
                                <p>Total Dosen</p>
                            </div>
                            <div class="icon"><i class="fas fa-chalkboard-teacher"></i></div>
                            <a href="<?php echo base_url('operator/kinerja_dosen'); ?>" class="small-box-footer">
                                Cek Kinerja <i class="fas fa-arrow-circle-right"></i>
                            </a>
                        </div>
                    </div>

                    <div class="col-lg-3 col-6">
                        <div class="small-box bg-warning shadow-sm">
                            <div class="inner">
                                <h3><?php echo $statistik['mhs_ready_sempro']; ?></h3>
                                <p>Siap Sempro</p>
                            </div>
                            <div class="icon"><i class="fas fa-graduation-cap"></i></div>
                            <a href="<?php echo base_url('operator/mahasiswa_siap_sempro'); ?>" class="small-box-footer">
                                Cek Data <i class="fas fa-arrow-circle-right"></i>
                            </a>
                        </div>
                    </div>

                <?php elseif ($role == 'dosen'): ?>

                    <div class="<?php echo $dosen_col_class; ?>">
                        <div class="small-box bg-primary shadow-sm">
                            <div class="inner">
                                <h3><?php echo $statistik['total_bimbingan']; ?></h3>
                                <p>Mahasiswa Bimbingan Anda</p>
                            </div>
                            <div class="icon"><i class="fas fa-user-graduate"></i></div>
                            <a href="<?php echo base_url('dosen/bimbingan_list'); ?>" class="small-box-footer">
                                Lihat Daftar <i class="fas fa-arrow-circle-right"></i>
                            </a>
                        </div>
                    </div>

                    <?php if ($is_kaprodi == 1): ?>
                        <div class="col-lg-4 col-sm-6">
                            <div class="small-box bg-info shadow-sm">
                                <div class="inner">
                                    <h3><?php echo $stats_kaprodi['total_dosen']; ?></h3>
                                    <p>Total Dosen Prodi</p>
                                </div>
                                <div class="icon"><i class="fas fa-chalkboard-teacher"></i></div>
                                <a href="<?php echo base_url('dosen/kinerja_dosen'); ?>" class="small-box-footer">
                                    Cek Kinerja <i class="fas fa-arrow-circle-right"></i>
                                </a>
                            </div>
                        </div>
                        <div class="col-lg-4 col-sm-6">
                            <div class="small-box bg-warning shadow-sm text-white">
                                <div class="inner">
                                    <h3 class="text-white"><?php echo $stats_kaprodi['judul_pending']; ?></h3>
                                    <p class="text-white">Pengajuan Judul (Pending)</p>
                                </div>
                                <div class="icon"><i class="fas fa-file-signature"></i></div>
                                <a href="<?php echo base_url('dosen/monitoring_prodi'); ?>" class="small-box-footer" style="color:rgba(255,255,255,0.8) !important">
                                    ACC Sekarang <i class="fas fa-arrow-circle-right"></i>
                                </a>
                            </div>
                        </div>
                    <?php endif; ?>

                    <!-- Mahasiswa Siap Sempro -->
                    <?php if (!empty($mahasiswa_siap_sempro)): ?>
                    <div class="col-12 mt-4">
                        <div class="card card-outline card-primary shadow-sm">
                            <div class="card-header">
                                <h3 class="card-title"><i class="fas fa-graduation-cap mr-2"></i>Data Sempro Mahasiswa Bimbingan</h3>
                            </div>
                            <div class="card-body p-0">
                                <div class="table-responsive">
                                    <table class="table table-hover table-striped mb-0">
                                        <thead class="bg-light">
                                            <tr>
                                                <th class="text-center" style="width: 5%">No</th>
                                                <th style="width: 20%">Mahasiswa</th>
                                                <th style="width: 30%">Judul Skripsi</th>
                                                <th style="width: 15%">Tanggal Daftar</th>
                                                <th style="width: 15%">File BAB 3</th>
                                                <th class="text-center" style="width: 15%">Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php $no = 1; foreach ($mahasiswa_siap_sempro as $mhs): ?>
                                            <tr>
                                                <td class="text-center"><?php echo $no++; ?></td>
                                                <td>
                                                    <strong><?php echo $mhs['nama_mhs']; ?></strong><br>
                                                    <small class="text-muted"><?php echo $mhs['npm']; ?></small>
                                                </td>
                                                <td><?php echo $mhs['judul']; ?></td>
                                                <td><?php echo date('d M Y', strtotime($mhs['tgl_daftar_sempro'])); ?></td>
                                                <td>
                                                    <?php if (!empty($mhs['file_bab3'])): ?>
                                                        <a href="<?php echo base_url('uploads/progres/' . $mhs['file_bab3']); ?>" 
                                                           target="_blank" class="btn btn-sm btn-outline-primary">
                                                            <i class="fas fa-file-pdf"></i> Lihat PDF
                                                        </a>
                                                    <?php else: ?>
                                                        <span class="text-muted">-</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td class="text-center">
                                                    <a href="<?php echo base_url('dosen/progres_detail/' . $mhs['id_skripsi']); ?>" 
                                                       class="btn btn-sm btn-success">
                                                        <i class="fas fa-eye"></i> Detail
                                                    </a>
                                                </td>
                                            </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>

                    <div class="col-12 mt-4">
                        <div class="card card-outline card-info shadow-sm">
                            <div class="card-body d-flex flex-wrap align-items-center justify-content-between">
                                <div>
                                    <h5 class="font-weight-bold mb-1"><i class="fas fa-hdd mr-1"></i> Plotting Jadwal Sempro</h5>
                                    <p class="mb-0 text-muted">Silakan klik link Google Drive yang disediakan operator untuk melihat jadwal seminar proposal.</p>
                                </div>
                                <?php if (!empty($google_drive_dosen)): ?>
                                    <a href="<?php echo $google_drive_dosen; ?>" target="_blank" class="btn btn-info btn-sm shadow-sm">
                                        <i class="fas fa-external-link-alt mr-1"></i> Buka Google Drive
                                    </a>
                                <?php else: ?>
                                    <button type="button" class="btn btn-secondary btn-sm shadow-sm" disabled>
                                        <i class="fas fa-exclamation-circle mr-1"></i> Belum Tersedia
                                    </button>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                <?php elseif ($role == 'mahasiswa'): ?>

                    <div class="row w-100 m-0">
                        <div class="col-md-4 mb-3">
                            <div class="info-box bg-white shadow-sm h-100 m-0"> 
                                <span class="info-box-icon bg-info elevation-1"><i class="fas fa-file-signature"></i></span>
                                <div class="info-box-content d-flex flex-column justify-content-center">
                                    <span class="info-box-text text-muted text-uppercase text-sm font-weight-bold">Status Bimbingan</span>
                                    <span class="info-box-number text-lg mb-2 text-primary">
                                        <?php echo $status_bimbingan; ?>
                                    </span>
                                    
                                    <?php if(isset($skripsi) && $skripsi): ?>
                                        <div class="text-sm border-top pt-2 mt-auto">
                                            <div class="text-truncate mb-1" title="<?php echo $skripsi['nama_p1']; ?>">
                                                <i class="fas fa-user-tie text-muted mr-1"></i> <strong>P1:</strong> <?php echo $skripsi['nama_p1']; ?>
                                            </div>
                                            <div class="text-truncate" title="<?php echo $skripsi['nama_p2']; ?>">
                                                <i class="fas fa-user-tie text-muted mr-1"></i> <strong>P2:</strong> <?php echo $skripsi['nama_p2']; ?>
                                            </div>
                                        </div>
                                    <?php else: ?>
                                        <a href="<?php echo base_url('mahasiswa/pengajuan_judul'); ?>" class="btn btn-sm btn-outline-info mt-auto">
                                            Ajukan Judul Baru <i class="fas fa-arrow-right ml-1"></i>
                                        </a>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-8 mb-3">
                            <div class="card card-primary card-outline shadow-sm h-100 m-0">
                                <div class="card-header border-0 pb-0">
                                    <h3 class="card-title font-weight-bold text-primary"><i class="fas fa-tasks mr-1"></i> Progres Skripsi</h3>
                                </div>
                                <div class="card-body d-flex flex-column justify-content-between">
                                    <div class="mb-3">
                                        <?php if(isset($skripsi) && $skripsi): ?>
                                            <h5 class="mb-1 font-weight-bold text-dark" style="line-height: 1.3;"><?php echo $skripsi['judul']; ?></h5>
                                            <span class="badge badge-light border text-muted"><i class="fas fa-tag mr-1"></i> <?php echo $skripsi['tema']; ?></span>
                                        <?php else: ?>
                                            <div class="alert alert-light border text-center p-3 mb-0">
                                                <i class="fas fa-book-open fa-2x text-muted mb-2"></i>
                                                <p class="mb-0 text-muted">Belum ada judul skripsi yang aktif.</p>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                    
                                    <div>
                                        <div class="d-flex justify-content-between align-items-end mb-1">
                                            <span>Posisi Saat Ini: <b>BAB <?php echo $current_bab; ?></b> <small class="text-muted">dari <?php echo $total_bab; ?> BAB</small></span>
                                            <span class="badge <?php echo $bg_class; ?>"><?php echo $progress_percent; ?>% Selesai</span>
                                        </div>
                                        <div class="progress mb-3 shadow-sm" style="height: 12px; border-radius: 10px;">
                                            <div class="progress-bar <?php echo $bg_class; ?> progress-bar-striped progress-bar-animated" role="progressbar" style="width: <?php echo $progress_percent; ?>%"></div>
                                        </div>
                                        <a href="<?php echo base_url('mahasiswa/bimbingan'); ?>" class="btn btn-primary btn-block shadow-sm font-weight-bold">
                                            <i class="fas fa-upload mr-2"></i> Lanjut Bimbingan / Upload File
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row mt-3 w-100 m-0">
                        <div class="col-12 p-0">
                            <div class="card card-outline card-info shadow-sm">
                                <div class="card-body d-flex flex-wrap align-items-center justify-content-between">
                                    <div>
                                        <h5 class="font-weight-bold mb-1"><i class="fas fa-calendar-alt mr-1"></i> Form Jadwal Sempro</h5>
                                        <p class="mb-0 text-muted">Silakan gunakan link Google Form yang disediakan operator untuk pendaftaran jadwal seminar proposal.</p>
                                    </div>
                                    <?php if (!empty($google_form_sempro)): ?>
                                        <a href="<?php echo $google_form_sempro; ?>" target="_blank" class="btn btn-info btn-sm shadow-sm">
                                            <i class="fas fa-external-link-alt mr-1"></i> Buka Google Form
                                        </a>
                                    <?php else: ?>
                                        <button type="button" class="btn btn-secondary btn-sm shadow-sm" disabled>
                                            <i class="fas fa-exclamation-circle mr-1"></i> Belum Tersedia
                                        </button>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row mt-3 w-100 m-0">
                        <div class="col-12 p-0">
                            <div class="card card-outline card-info shadow-sm">
                                <div class="card-body d-flex flex-wrap align-items-center justify-content-between">
                                    <div>
                                        <h5 class="font-weight-bold mb-1"><i class="fas fa-calendar-alt mr-1"></i> Plotting Jadwal Sempro</h5>
                                        <p class="mb-0 text-muted">Silakan klik link Google Drive yang disediakan operator untuk melihat jadwal seminar proposal.</p>
                                    </div>
                                    <?php if (!empty($google_drive_sempro)): ?>
                                        <a href="<?php echo $google_drive_sempro; ?>" target="_blank" class="btn btn-info btn-sm shadow-sm">
                                            <i class="fas fa-external-link-alt mr-1"></i> Buka Google Drive
                                        </a>
                                    <?php else: ?>
                                        <button type="button" class="btn btn-secondary btn-sm shadow-sm" disabled>
                                            <i class="fas fa-exclamation-circle mr-1"></i> Belum Tersedia
                                        </button>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>

                    <?php if (!empty($google_drive_dosen)): ?>
                    <div class="row mt-3 w-100 m-0">
                        <div class="col-12 p-0">
                            <div class="card card-outline card-primary shadow-sm">
                                <div class="card-body d-flex flex-wrap align-items-center justify-content-between">
                                    <div>
                                        <h5 class="font-weight-bold mb-1"><i class="fas fa-hdd mr-1"></i> Google Drive Plotting</h5>
                                        <p class="mb-0 text-muted">Akses Google Drive untuk dokumen plotting jadwal sempro.</p>
                                    </div>
                                    <a href="<?php echo $google_drive_dosen; ?>" target="_blank" class="btn btn-primary btn-sm shadow-sm">
                                        <i class="fas fa-external-link-alt mr-1"></i> Buka Drive
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>

                    <?php if (!empty($sempro_schedule_pdf)): ?>
                    <div class="row mt-3 w-100 m-0">
                        <div class="col-12 p-0">
                            <div class="card card-outline card-success shadow-sm">
                                <div class="card-body d-flex flex-wrap align-items-center justify-content-between">
                                    <div>
                                        <h5 class="font-weight-bold mb-1"><i class="fas fa-file-pdf mr-1"></i> Plotting Jadwal Sempro</h5>
                                        <p class="mb-0 text-muted">Lihat jadwal plotting seminar proposal yang telah disusun oleh operator.</p>
                                    </div>
                                    <a href="<?php echo $sempro_schedule_pdf; ?>" target="_blank" class="btn btn-success btn-sm shadow-sm">
                                        <i class="fas fa-external-link-alt mr-1"></i> Lihat Jadwal PDF
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>

                    <div class="row mt-2 w-100 m-0">
                        <div class="col-12 p-0">
                            <div class="card card-info card-outline shadow-sm">
                                <div class="card-header border-0">
                                    <h3 class="card-title font-weight-bold"><i class="fas fa-history mr-1"></i> Riwayat Pengajuan Judul Skripsi</h3>
                                </div>
                                <div class="card-body p-0">
                                    <div class="table-responsive">
                                        <table class="table table-hover mb-0 align-middle text-nowrap">
                                            <thead class="bg-light">
                                                <tr>
                                                    <th class="text-center" style="width: 5%;">No</th>
                                                    <th style="width: 15%;">Tgl Pengajuan</th>
                                                    <th style="width: 35%;">Judul & Tema</th>
                                                    <th style="width: 25%;">Usulan Pembimbing</th>
                                                    <th class="text-center" style="width: 10%;">Skema</th>
                                                    <th class="text-center" style="width: 10%;">Status</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php if (!empty($riwayat_judul)): ?>
                                                    <?php $no = 1; foreach ($riwayat_judul as $row): ?>
                                                        <tr class="<?php echo $row['row_class']; ?>">
                                                            <td class="text-center align-middle">
                                                                <span class="font-weight-bold"><?php echo $no++; ?></span>
                                                                <br>
                                                                <?php if($row['is_active']): ?>
                                                                    <span class="badge badge-success" style="font-size: 10px;">Aktif</span>
                                                                <?php else: ?>
                                                                    <span class="badge badge-secondary" style="font-size: 10px;">Lama</span>
                                                                <?php endif; ?>
                                                            </td>
                                                            <td class="align-middle text-muted">
                                                                <?php echo date('d M Y', strtotime($row['tgl_pengajuan_judul'])); ?>
                                                            </td>
                                                            <td class="align-middle text-wrap" style="min-width: 250px;">
                                                                <span class="font-weight-bold d-block <?php echo !$row['is_active'] ? 'text-secondary' : 'text-dark'; ?>" style="line-height: 1.3;">
                                                                    <?php echo $row['judul']; ?>
                                                                </span>
                                                                <small class="<?php echo !$row['is_active'] ? 'text-muted' : 'text-info'; ?> mt-1 d-block">
                                                                    <i class="fas fa-tag mr-1"></i> <?php echo $row['tema']; ?>
                                                                </small>
                                                            </td>
                                                            <td class="align-middle">
                                                                <div class="text-sm">
                                                                    <span class="d-block mb-1 text-truncate" style="max-width: 250px;"><i class="fas fa-user-tie <?php echo !$row['is_active'] ? 'text-secondary' : 'text-primary'; ?> mr-1"></i> P1: <?php echo $row['nama_p1'] ?: '-'; ?></span>
                                                                    <span class="d-block text-truncate" style="max-width: 250px;"><i class="fas fa-user-tie text-secondary mr-1"></i> P2: <?php echo $row['nama_p2'] ?: '-'; ?></span>
                                                                </div>
                                                            </td>
                                                            <td class="text-center align-middle">
                                                                <span class="badge badge-light border text-muted"><?php echo $row['skema']; ?></span>
                                                            </td>
                                                            <td class="text-center align-middle">
                                                                <span class="badge badge-<?php echo $row['badge_color']; ?> px-3 py-2 shadow-sm">
                                                                    <?php echo $row['status_label']; ?>
                                                                </span>
                                                            </td>
                                                        </tr>
                                                    <?php endforeach; ?>
                                                <?php else: ?>
                                                    <tr>
                                                        <td colspan="6" class="text-center py-5 text-muted">
                                                            <i class="fas fa-folder-open fa-3x mb-3 text-light"></i><br>
                                                            Belum ada riwayat pengajuan judul.
                                                        </td>
                                                    </tr>
                                                <?php endif; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                <?php endif; ?>
            </div>

            <?php if ($role == 'dosen' && $is_kaprodi == 1): ?>
                
                <div class="row mt-4">
                    <div class="col-md-8">
                        <div class="card card-outline card-info shadow-sm">
                            <div class="card-header">
                                <h3 class="card-title font-weight-bold">
                                    <i class="fas fa-chart-pie mr-1 text-info"></i> 
                                    Grafik Sebaran Progres Mahasiswa
                                </h3>
                                <div class="card-tools">
                                    <button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-minus"></i></button>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="chart">
                                    <canvas id="sebaranChart" style="min-height: 300px; height: 300px; max-height: 300px; max-width: 100%;"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="card card-outline card-secondary shadow-sm">
                            <div class="card-header border-0">
                                <h3 class="card-title font-weight-bold text-secondary"><i class="fas fa-list-ul mr-1"></i> Detail Jumlah</h3>
                            </div>
                            <div class="card-body p-0">
                                <ul class="nav flex-column">
                                    <?php 
                                    $colors = ['bg-primary', 'bg-info', 'bg-success', 'bg-warning', 'bg-danger', 'bg-purple'];
                                    $i = 0;
                                    if(!empty($stats_kaprodi['bab_stats'])):
                                        foreach($stats_kaprodi['bab_stats'] as $bab => $total): 
                                            $total_mhs = $stats_kaprodi['total_mhs'];
                                            $percent = ($total_mhs > 0) ? round(($total / $total_mhs) * 100) : 0;
                                            $color = isset($colors[$i]) ? $colors[$i] : 'bg-secondary';
                                    ?>
                                    <li class="nav-item">
                                        <a href="<?php echo base_url('dosen/monitoring_prodi'); ?>" class="nav-link text-dark">
                                            <b>BAB <?php echo $bab; ?></b>
                                            <span class="float-right badge <?php echo $color; ?>"><?php echo $total; ?> Mhs</span>
                                            <div class="progress progress-xs mt-2 mb-1 shadow-sm">
                                                <div class="progress-bar <?php echo $color; ?>" style="width: <?php echo $percent; ?>%"></div>
                                            </div>
                                        </a>
                                    </li>
                                    <?php $i++; endforeach; endif; ?>
                                </ul>
                            </div>
                            <div class="card-footer p-3 text-center bg-light border-0">
                                <a href="<?php echo base_url('dosen/monitoring_prodi'); ?>" class="btn btn-sm btn-outline-secondary font-weight-bold px-4">
                                    Lihat Data Lengkap <i class="fas fa-arrow-right ml-1"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

            <?php endif; ?>

        </div>
    </section>
</div>

<?php if ($role == 'dosen' && $is_kaprodi == 1): ?>
<script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
<script>
document.addEventListener("DOMContentLoaded", function() {
    var babData = <?php echo json_encode(array_values($stats_kaprodi['bab_stats'])); ?>;
    
    var donutData = {
        labels: ['BAB 1', 'BAB 2', 'BAB 3', 'BAB 4', 'BAB 5', 'BAB 6'],
        datasets: [{
            data: babData,
            backgroundColor : ['#007bff', '#17a2b8', '#28a745', '#ffc107', '#dc3545', '#6f42c1'],
            hoverOffset: 4
        }]
    };

    var pieOptions = {
        maintainAspectRatio : false,
        responsive : true,
        plugins: {
            legend: {
                display: true,
                position: 'left',
                labels: { boxWidth: 20, padding: 20 }
            }
        }
    };

    var canvasElement = document.getElementById('sebaranChart');
    if (canvasElement) {
        new Chart(canvasElement.getContext('2d'), {
            type: 'doughnut',
            data: donutData,
            options: pieOptions
        });
    }
});
</script>
<?php endif; ?>