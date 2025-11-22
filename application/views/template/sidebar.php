<div class="sidebar">
    <h3>Menu <?php echo ucfirst($this->session->userdata('role')); ?></h3>

    <?php
    // Ambil data role dari session untuk menentukan menu
    $role = $this->session->userdata('role');
    ?>

    <ul>
                <li><a href="<?php echo base_url('dashboard'); ?>"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
                
                <?php if ($role == 'operator'): ?>
                <li class="menu-heading">MANAJEMEN SISTEM</li>
                <li><a href="<?php echo base_url('operator/manajemen_akun'); ?>"><i class="fas fa-users"></i> Manajemen Akun</a></li>
                <li><a href="<?php echo base_url('operator/penugasan_pembimbing'); ?>"><i class="fas fa-user-tie"></i> Atur Pembimbing</a></li>
                <li><a href="<?php echo base_url('operator/monitoring_progres'); ?>"><i class="fas fa-chart-bar"></i> Monitoring Progres</a></li>
                <li><a href="<?php echo base_url('operator/kinerja_dosen'); ?>"><i class="fas fa-history"></i> Laporan Kinerja Dosen</a></li>
                <li><a href="<?php echo base_url('operator/cek_plagiarisme_list'); ?>"><i class="fas fa-search"></i> Cek Plagiarisme</a></li>
                <?php endif; ?>

                <?php if ($role == 'dosen'): ?>
                <li class="menu-heading">DOSEN</li>
                <li><a href="<?php echo base_url('dosen/bimbingan_list'); ?>"><i class="fas fa-book-open"></i> Daftar Mahasiswa Bimbingan</a></li>
                <?php if ($this->session->userdata('is_kaprodi') == 1): ?>
                <li><a href="<?php echo base_url('dosen/monitoring_prodi'); ?>"><i class="fas fa-eye"></i> Monitoring Prodi (Kaprodi)</a></li>
                <?php endif; ?>
                <?php endif; ?>

                <?php if ($role == 'mahasiswa'): ?>
                <li class="menu-heading">MAHASISWA</li>
                <li><a href="<?php echo base_url('mahasiswa/pengajuan_judul'); ?>"><i class="fas fa-pencil-alt"></i> Pengajuan Judul Skripsi</a></li>
                <li><a href="<?php echo base_url('mahasiswa/progres_skripsi'); ?>"><i class="fas fa-tasks"></i> Progres Bimbingan</a></li>
                <?php endif; ?>

                <li class="menu-heading">AKUN</li>
                <li><a href="<?php echo base_url('auth/logout'); ?>"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
            </ul>
</div>

<div class="content-area">
    <h2><?php echo $title; ?></h2>