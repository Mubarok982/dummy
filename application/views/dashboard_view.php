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
                <?php 
                $role = $this->session->userdata('role');
                
                // DATA DEFAULT AGAR TIDAK ERROR
                $stats = isset($statistik) ? $statistik : [
                    'total_mhs' => 0, 'mhs_skripsi' => 0, 'total_dosen' => 0,
                    'mhs_ready_sempro' => 0, 'total_bimbingan' => 0,
                    'last_bab' => 0, 'judul_status' => '-'
                ];
                
                $stats_kaprodi = isset($stats_kaprodi) ? $stats_kaprodi : [
                    'total_dosen' => 0, 'judul_pending' => 0, 
                    'bab_stats' => [1=>0, 2=>0, 3=>0, 4=>0, 5=>0], 
                    'total_mhs' => 0
                ];
                ?>

                <?php if ($role == 'operator' || $role == 'tata_usaha'): ?>

                    <div class="col-lg-3 col-6">
                        <div class="small-box bg-info shadow-sm">
                            <div class="inner">
                                <h3><?php echo $stats['total_mhs']; ?></h3>
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
                                <h3><?php echo $stats['mhs_skripsi']; ?></h3>
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
                                <h3><?php echo $stats['total_dosen']; ?></h3>
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
                                <h3><?php echo $stats['mhs_ready_sempro']; ?></h3>
                                <p>Siap Sempro</p>
                            </div>
                            <div class="icon"><i class="fas fa-graduation-cap"></i></div>
                            <a href="<?php echo base_url('operator/mahasiswa_siap_sempro'); ?>" class="small-box-footer">
                                Cek Data <i class="fas fa-arrow-circle-right"></i>
                            </a>
                        </div>
                    </div>

                <?php elseif ($role == 'dosen'): ?>

                    <div class="col-lg-4 col-12">
                        <div class="small-box bg-primary shadow-sm">
                            <div class="inner">
                                <h3><?php echo $stats['total_bimbingan']; ?></h3>
                                <p>Mahasiswa Bimbingan Anda</p>
                            </div>
                            <div class="icon"><i class="fas fa-user-graduate"></i></div>
                            <a href="<?php echo base_url('dosen/bimbingan_list'); ?>" class="small-box-footer">
                                Lihat Daftar <i class="fas fa-arrow-circle-right"></i>
                            </a>
                        </div>
                    </div>

                    <?php if ($this->session->userdata('is_kaprodi') == 1): ?>
                        <div class="col-lg-4 col-6">
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
                        <div class="col-lg-4 col-6">
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

                <?php elseif ($role == 'mahasiswa'): ?>

                    <?php
                    $current_bab = $stats['last_bab'];
                    $total_bab = 5; 
                    $progress_percent = ($total_bab > 0) ? min(100, round(($current_bab / $total_bab) * 100)) : 0;
                    
                    $bg_class = 'bg-danger';
                    if($progress_percent >= 50) $bg_class = 'bg-warning';
                    if($progress_percent >= 80) $bg_class = 'bg-success';
                    ?>

                    <div class="col-md-4">
                        <div class="info-box mb-3 bg-white shadow-sm">
                            <span class="info-box-icon bg-info elevation-1"><i class="fas fa-file-signature"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">Status Judul</span>
                                <span class="info-box-number"><?php echo $stats['judul_status']; ?></span>
                                <a href="<?php echo base_url('mahasiswa/pengajuan_judul'); ?>" class="small-box-footer text-muted text-sm">
                                    Lihat Detail <i class="fas fa-arrow-circle-right"></i>
                                </a>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-8">
                        <div class="card card-primary card-outline shadow-sm">
                            <div class="card-header">
                                <h3 class="card-title"><i class="fas fa-tasks mr-1"></i> Progres Skripsi Anda</h3>
                            </div>
                            <div class="card-body">
                                <h5 class="mb-2">Saat ini: <b>BAB <?php echo $current_bab; ?></b> <small class="float-right badge badge-<?php echo str_replace('bg-', '', $bg_class); ?>"><?php echo $progress_percent; ?>% Selesai</small></h5>
                                <div class="progress mb-3" style="height: 20px;">
                                    <div class="progress-bar <?php echo $bg_class; ?> progress-bar-striped progress-bar-animated" role="progressbar" style="width: <?php echo $progress_percent; ?>%"></div>
                                </div>
                                <a href="<?php echo base_url('mahasiswa/bimbingan'); ?>" class="btn btn-primary btn-block">
                                    <i class="fas fa-upload"></i> Lanjut Bimbingan / Upload File
                                </a>
                            </div>
                        </div>
                    </div>

                <?php endif; ?>
            </div>

            <?php if ($role == 'dosen' && $this->session->userdata('is_kaprodi') == 1): ?>
                
                <div class="row">
                    <div class="col-md-8">
                        <div class="card card-outline card-info shadow-sm">
                            <div class="card-header">
                                <h3 class="card-title">
                                    <i class="fas fa-chart-pie mr-1"></i> 
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
                            <div class="card-header">
                                <h3 class="card-title">Detail Jumlah</h3>
                            </div>
                            <div class="card-body p-0">
                                <ul class="nav flex-column">
                                    <?php 
                                    $labels = ['BAB 1', 'BAB 2', 'BAB 3', 'BAB 4', 'BAB 5'];
                                    $colors = ['bg-primary', 'bg-info', 'bg-success', 'bg-warning', 'bg-danger'];
                                    $i = 0;
                                    if(!empty($stats_kaprodi['bab_stats'])):
                                        foreach($stats_kaprodi['bab_stats'] as $bab => $total): 
                                            $total_mhs = $stats_kaprodi['total_mhs'];
                                            $percent = ($total_mhs > 0) ? round(($total / $total_mhs) * 100) : 0;
                                    ?>
                                    <li class="nav-item">
                                        <a href="#" class="nav-link">
                                            <b>BAB <?php echo $bab; ?></b>
                                            <span class="float-right badge <?php echo $colors[$i]; ?>"><?php echo $total; ?> Mhs</span>
                                            <div class="progress progress-xs mt-1">
                                                <div class="progress-bar <?php echo $colors[$i]; ?>" style="width: <?php echo $percent; ?>%"></div>
                                            </div>
                                        </a>
                                    </li>
                                    <?php $i++; endforeach; endif; ?>
                                </ul>
                            </div>
                            <div class="card-footer p-2 text-center">
                                <a href="<?php echo base_url('dosen/monitoring_prodi'); ?>" class="text-secondary font-weight-bold">
                                    Lihat Data Lengkap <i class="fas fa-arrow-circle-right"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

            <?php endif; ?>

        </div>
    </section>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>

<?php if ($role == 'dosen' && $this->session->userdata('is_kaprodi') == 1): ?>
<script>
// Gunakan event listener agar script jalan setelah HTML ready
document.addEventListener("DOMContentLoaded", function() {
    
    // --- Ambil Data dari PHP ---
    // Menggunakan array_values untuk memastikan format array JS [1, 2, 3...]
    var babData = <?php echo json_encode(array_values($stats_kaprodi['bab_stats'])); ?>;
    
    // Cek di Console browser apakah data masuk
    console.log("Data Chart:", babData);

    // --- Konfigurasi Data ---
    var donutData = {
        labels: [
            'BAB 1', 
            'BAB 2', 
            'BAB 3', 
            'BAB 4', 
            'BAB 5'
        ],
        datasets: [
            {
                data: babData,
                backgroundColor : ['#007bff', '#17a2b8', '#28a745', '#ffc107', '#dc3545'], // Biru, Teal, Hijau, Kuning, Merah
                hoverOffset: 4
            }
        ]
    };

    // --- Konfigurasi Opsi (Chart.js v3) ---
    var pieOptions = {
        maintainAspectRatio : false,
        responsive : true,
        plugins: {
            legend: {
                display: true,
                position: 'left', // Posisi legenda di kiri
                labels: {
                    boxWidth: 20,
                    padding: 20
                }
            },
            tooltip: {
                callbacks: {
                    label: function(context) {
                        let label = context.label || '';
                        if (label) {
                            label += ': ';
                        }
                        label += context.raw + ' Mahasiswa';
                        return label;
                    }
                }
            }
        }
    };

    // --- Render Chart ---
    // Pastikan elemen canvas ada sebelum digambar
    var canvasElement = document.getElementById('sebaranChart');
    if (canvasElement) {
        var pieChartCanvas = canvasElement.getContext('2d');
        new Chart(pieChartCanvas, {
            type: 'doughnut', // Gunakan 'doughnut' atau 'pie'
            data: donutData,
            options: pieOptions
        });
    } else {
        console.error("Canvas element #sebaranChart tidak ditemukan!");
    }
});
</script>
<?php endif; ?>