<div class="container-fluid">
    
    <div class="row">
        <?php 
        $role = $this->session->userdata('role');
        $stats = $statistik;
        ?>

        <?php if ($role == 'operator' || $role == 'tata_usaha'): ?>

            <div class="col-lg-3 col-6">
                <div class="small-box bg-info">
                    <div class="inner">
                        <h3><?php echo $stats['total_mhs']; ?></h3>
                        <p>Total Mahasiswa</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-users"></i>
                    </div>
                    <a href="<?php echo base_url('operator/manajemen_akun'); ?>" class="small-box-footer">
                        Lihat Detail <i class="fas fa-arrow-circle-right"></i>
                    </a>
                </div>
            </div>

            <div class="col-lg-3 col-6">
                <div class="small-box bg-success">
                    <div class="inner">
                        <h3><?php echo $stats['mhs_skripsi']; ?></h3>
                        <p>Mhs. Sedang Skripsi</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-book-reader"></i>
                    </div>
                    <a href="<?php echo base_url('operator/monitoring_progres'); ?>" class="small-box-footer">
                        Monitoring <i class="fas fa-arrow-circle-right"></i>
                    </a>
                </div>
            </div>

            <div class="col-lg-3 col-6">
                <div class="small-box bg-danger">
                    <div class="inner">
                        <h3><?php echo $stats['total_dosen']; ?></h3>
                        <p>Total Dosen</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-chalkboard-teacher"></i>
                    </div>
                    <a href="<?php echo base_url('operator/kinerja_dosen'); ?>" class="small-box-footer">
                        Cek Kinerja <i class="fas fa-arrow-circle-right"></i>
                    </a>
                </div>
            </div>

            <div class="col-lg-3 col-6">
                <div class="small-box bg-warning">
                    <div class="inner">
                        <h3><?php echo $stats['mhs_ready_sempro']; ?></h3>
                        <p>Siap Sempro (Bab 3 ACC)</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-graduation-cap"></i>
                    </div>
                    <a href="#" class="small-box-footer">
                        Cek Data <i class="fas fa-arrow-circle-right"></i>
                    </a>
                </div>
            </div>

        <?php elseif ($role == 'dosen'): ?>

            <div class="col-lg-4 col-12">
                <div class="small-box bg-primary">
                    <div class="inner">
                        <h3><?php echo $stats['total_bimbingan']; ?></h3>
                        <p>Mahasiswa Bimbingan Anda</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-user-graduate"></i>
                    </div>
                    <a href="<?php echo base_url('dosen/bimbingan_list'); ?>" class="small-box-footer">
                        Lihat Daftar <i class="fas fa-arrow-circle-right"></i>
                    </a>
                </div>
            </div>

            <?php if ($this->session->userdata('is_kaprodi') == 1): ?>
            <div class="col-lg-8 col-12">
                <div class="callout callout-warning">
                    <h5><i class="fas fa-crown text-warning"></i> Mode Kaprodi Aktif</h5>
                    <p>Anda memiliki akses penuh untuk memantau progres seluruh mahasiswa di Program Studi <b><?php echo $this->session->userdata('prodi'); ?></b>.</p>
                    <a href="<?php echo base_url('dosen/monitoring_prodi'); ?>" class="btn btn-warning text-white btn-sm text-decoration-none">
                        Buka Dashboard Kaprodi
                    </a>
                </div>
            </div>
            <?php endif; ?>

        <?php elseif ($role == 'mahasiswa'): ?>

            <?php
            $current_bab = $stats['last_bab'];
            $total_bab = 5; 
            $progress_percent = min(100, round(($current_bab / $total_bab) * 100)); 
            
            // Warna progress bar
            $bg_class = 'bg-danger';
            if($progress_percent >= 50) $bg_class = 'bg-warning';
            if($progress_percent >= 80) $bg_class = 'bg-success';
            ?>

            <div class="col-md-4">
                <div class="info-box mb-3 bg-white">
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
                <div class="card card-primary card-outline">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-tasks mr-1"></i>
                            Progres Skripsi Anda
                        </h3>
                    </div>
                    <div class="card-body">
                        <h5 class="mb-2">Saat ini: <b>BAB <?php echo $current_bab; ?></b> <small class="float-right badge badge-<?php echo str_replace('bg-', '', $bg_class); ?>"><?php echo $progress_percent; ?>% Selesai</small></h5>
                        
                        <div class="progress mb-3" style="height: 20px;">
                            <div class="progress-bar <?php echo $bg_class; ?> progress-bar-striped progress-bar-animated" role="progressbar" style="width: <?php echo $progress_percent; ?>%"></div>
                        </div>

                        <a href="<?php echo base_url('mahasiswa/progres_skripsi'); ?>" class="btn btn-primary btn-block">
                            <i class="fas fa-upload"></i> Lanjut Bimbingan / Upload File
                        </a>
                    </div>
                </div>
            </div>

        <?php endif; ?>
    </div>
    <div class="row mt-4">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header border-0">
                    <h3 class="card-title">
                        <i class="fas fa-id-card-alt mr-1"></i> Informasi Akun Pengguna
                    </h3>
                </div>
                <div class="card-body pt-0">
                    <div class="d-flex align-items-center">
                        <div class="mr-4">
                            <img src="https://ui-avatars.com/api/?name=<?php echo urlencode($this->session->userdata('nama')); ?>&background=random&size=64" class="img-circle elevation-1" alt="User Image">
                        </div>
                        <div>
                            <h4 class="mb-1 text-primary"><?php echo $this->session->userdata('nama'); ?></h4>
                            <div class="text-muted">
                                <span class="badge badge-secondary"><?php echo ucfirst($role); ?></span>
                                <?php if ($this->session->userdata('npm')): ?>
                                    <span class="ml-2"><i class="fas fa-id-badge"></i> NPM: <?php echo $this->session->userdata('npm'); ?></span>
                                <?php endif; ?>
                                <?php if ($this->session->userdata('nidk')): ?>
                                    <span class="ml-2"><i class="fas fa-id-badge"></i> NIDK: <?php echo $this->session->userdata('nidk'); ?></span>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>