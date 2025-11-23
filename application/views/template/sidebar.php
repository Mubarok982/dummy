<nav class="mt-2">
        <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
          
          <li class="nav-item">
            <a href="<?php echo base_url('dashboard'); ?>" class="nav-link <?php echo ($this->uri->segment(1) == 'dashboard') ? 'active' : ''; ?>">
              <i class="nav-icon fas fa-tachometer-alt"></i>
              <p>Dashboard</p>
            </a>
          </li>

          <?php $role = $this->session->userdata('role'); ?>

          <?php if ($role == 'operator'): ?>
            <li class="nav-header">ADMINISTRASI</li>
            <li class="nav-item">
              <a href="<?php echo base_url('operator/manajemen_akun'); ?>" class="nav-link <?php echo ($this->uri->segment(2) == 'manajemen_akun') ? 'active' : ''; ?>">
                <i class="nav-icon fas fa-users"></i>
                <p>Manajemen Akun</p>
              </a>
            </li>
            <li class="nav-item">
              <a href="<?php echo base_url('operator/penugasan_pembimbing'); ?>" class="nav-link <?php echo ($this->uri->segment(2) == 'penugasan_pembimbing') ? 'active' : ''; ?>">
                <i class="nav-icon fas fa-user-tie"></i>
                <p>Atur Pembimbing</p>
              </a>
            </li>
            <li class="nav-item">
              <a href="<?php echo base_url('operator/cek_plagiarisme_list'); ?>" class="nav-link <?php echo ($this->uri->segment(2) == 'cek_plagiarisme_list') ? 'active' : ''; ?>">
                <i class="nav-icon fas fa-search"></i>
                <p>Cek Plagiarisme</p>
              </a>
            </li>
            <li class="nav-header">LAPORAN</li>
            <li class="nav-item">
              <a href="<?php echo base_url('operator/monitoring_progres'); ?>" class="nav-link <?php echo ($this->uri->segment(2) == 'monitoring_progres') ? 'active' : ''; ?>">
                <i class="nav-icon fas fa-chart-line"></i>
                <p>Monitoring Progres</p>
              </a>
            </li>
            <li class="nav-item">
              <a href="<?php echo base_url('operator/kinerja_dosen'); ?>" class="nav-link <?php echo ($this->uri->segment(2) == 'kinerja_dosen') ? 'active' : ''; ?>">
                <i class="nav-icon fas fa-history"></i>
                <p>Kinerja Dosen</p>
              </a>
            </li>
          <?php endif; ?>

          <?php if ($role == 'dosen'): ?>
            <li class="nav-header">PEMBIMBING</li>
            <li class="nav-item">
              <a href="<?php echo base_url('dosen/bimbingan_list'); ?>" class="nav-link <?php echo ($this->uri->segment(2) == 'bimbingan_list') ? 'active' : ''; ?>">
                <i class="nav-icon fas fa-chalkboard-teacher"></i>
                <p>Mahasiswa Bimbingan</p>
              </a>
            </li>
            <?php if ($this->session->userdata('is_kaprodi') == 1): ?>
              <li class="nav-item">
                <a href="<?php echo base_url('dosen/monitoring_prodi'); ?>" class="nav-link bg-warning text-dark">
                  <i class="nav-icon fas fa-eye"></i>
                  <p><b>Monitoring Kaprodi</b></p>
                </a>
              </li>
            <?php endif; ?>
          <?php endif; ?>

          <?php if ($role == 'mahasiswa'): ?>
            <li class="nav-header">SKRIPSI SAYA</li>
            <li class="nav-item">
              <a href="<?php echo base_url('mahasiswa/pengajuan_judul'); ?>" class="nav-link <?php echo ($this->uri->segment(2) == 'pengajuan_judul') ? 'active' : ''; ?>">
                <i class="nav-icon fas fa-file-signature"></i>
                <p>Pengajuan Judul</p>
              </a>
            </li>
            <li class="nav-item">
              <a href="<?php echo base_url('mahasiswa/progres_skripsi'); ?>" class="nav-link <?php echo ($this->uri->segment(2) == 'progres_skripsi') ? 'active' : ''; ?>">
                <i class="nav-icon fas fa-upload"></i>
                <p>Upload & Bimbingan</p>
              </a>
            </li>
          <?php endif; ?>

        </ul>
      </nav>
      </div>
    </aside>

  <div class="content-wrapper">
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 class="m-0"><?php echo isset($title) ? $title : 'Dashboard'; ?></h1>
          </div>
        </div>
      </div>
    </div>
    <section class="content">
      <div class="container-fluid">
        <div class="card">
          <div class="card-body">