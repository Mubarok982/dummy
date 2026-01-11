<nav class="mt-2">
  <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">

    <li class="nav-item">
      <a href="<?php echo base_url('dashboard'); ?>"
        class="nav-link <?php echo ($this->uri->segment(1) == 'dashboard') ? 'active' : ''; ?>">
        <i class="nav-icon fas fa-tachometer-alt"></i>
        <p>Dashboard</p>
      </a>
    </li>

    <?php $role = $this->session->userdata('role'); ?>

    <?php if ($role == 'operator'): ?>
      <li class="nav-header">ADMINISTRASI</li>
      <li class="nav-item">
        <a href="<?php echo base_url('operator/manajemen_akun'); ?>"
          class="nav-link <?php echo ($this->uri->segment(2) == 'manajemen_akun') ? 'active' : ''; ?>">
          <i class="nav-icon fas fa-users"></i>
          <p>Manajemen Akun</p>
        </a>
      </li>
      <li class="nav-item">
        <a href="<?php echo base_url('operator/cek_plagiarisme_list'); ?>"
          class="nav-link <?php echo ($this->uri->segment(2) == 'cek_plagiarisme_list') ? 'active' : ''; ?>">
          <i class="nav-icon fas fa-search"></i>
          <p>Cek Plagiarisme</p>
        </a>
      </li>
      <li class="nav-header">LAPORAN</li>
      <li class="nav-item">
        <a href="<?php echo base_url('operator/monitoring_progres'); ?>"
          class="nav-link <?php echo ($this->uri->segment(2) == 'monitoring_progres') ? 'active' : ''; ?>">
          <i class="nav-icon fas fa-chart-line"></i>
          <p>Monitoring Progres</p>
        </a>
      </li>
      <li class="nav-item">
        <a href="<?php echo base_url('operator/kinerja_dosen'); ?>"
          class="nav-link <?php echo ($this->uri->segment(2) == 'kinerja_dosen') ? 'active' : ''; ?>">
          <i class="nav-icon fas fa-history"></i>
          <p>Kinerja Dosen</p>
        </a>
      </li>
    <?php endif; ?>

    <?php if ($role == 'dosen'): ?>

      <li class="nav-header">DOSEN PEMBIMBING</li>
      <li class="nav-item">
        <a href="<?php echo base_url('dosen/bimbingan_list'); ?>"
          class="nav-link <?php echo ($this->uri->segment(2) == 'bimbingan_list') ? 'active' : ''; ?>">
          <i class="nav-icon fas fa-chalkboard-teacher"></i>
          <p>Mahasiswa Bimbingan</p>
        </a>
      </li>
      <li class="nav-item">
        <a href="<?php echo base_url('chat'); ?>"
          class="nav-link <?php echo ($this->uri->segment(1) == 'chat') ? 'active' : ''; ?>">
          <i class="nav-icon fas fa-comments"></i>
          <p>Ruang Diskusi</p>
        </a>
      </li>

      <?php if ($this->session->userdata('is_kaprodi') == 1): ?>
        <li class="nav-header text-warning">AREA KAPRODI</li>

        <li class="nav-item">
          <a href="<?php echo base_url('dosen/monitoring_prodi'); ?>"
            class="nav-link <?php echo ($this->uri->segment(2) == 'monitoring_prodi') ? 'active' : ''; ?>">
            <i class="nav-icon fas fa-eye text-warning"></i>
            <p>Monitoring Mahasiswa</p>
          </a>
        </li>

        <li class="nav-item">
          <a href="<?php echo base_url('dosen/kinerja_dosen'); ?>"
            class="nav-link <?php echo ($this->uri->segment(2) == 'kinerja_dosen') ? 'active' : ''; ?>">
            <i class="nav-icon fas fa-chart-bar text-warning"></i>
            <p>Kinerja Dosen Prodi</p>
          </a>
        </li>
      <?php endif; ?>

      <li class="nav-header">PENGATURAN</li>
      <li class="nav-item">
        <a href="<?php echo base_url('dosen/profil'); ?>"
          class="nav-link <?php echo ($this->uri->segment(2) == 'profil') ? 'active' : ''; ?>">
          <i class="nav-icon fas fa-id-card"></i>
          <p>Profil Saya</p>
        </a>
      </li>
    <?php endif; ?>

    <?php if ($role == 'mahasiswa'): ?>
      <li class="nav-header">AKUN SAYA</li>
      <li class="nav-item">
        <a href="<?php echo base_url('mahasiswa/biodata'); ?>"
          class="nav-link <?php echo ($this->uri->segment(2) == 'biodata') ? 'active' : ''; ?>">
          <i class="nav-icon fas fa-id-card"></i>
          <p>Biodata Mahasiswa</p>
        </a>
      </li>

      <li class="nav-header">PROGRES SKRIPSI</li>
      <li class="nav-item">
        <a href="<?php echo base_url('mahasiswa/pengajuan_judul'); ?>"
          class="nav-link <?php echo ($this->uri->segment(2) == 'pengajuan_judul') ? 'active' : ''; ?>">
          <i class="nav-icon fas fa-file-signature"></i>
          <p>Pengajuan Judul</p>
        </a>
      </li>
      <li class="nav-item">
        <a href="<?php echo base_url('mahasiswa/bimbingan'); ?>"
          class="nav-link <?php echo ($this->uri->segment(2) == 'bimbingan') ? 'active' : ''; ?>">
          <i class="nav-icon fas fa-upload"></i>
          <p>Bimbingan & Upload</p>
        </a>
      </li>
      <li class="nav-item">
        <a href="<?php echo base_url('mahasiswa/riwayat_progres'); ?>"
          class="nav-link <?php echo ($this->uri->segment(2) == 'riwayat_progres') ? 'active' : ''; ?>">
          <i class="nav-icon fas fa-history"></i>
          <p>Riwayat Revisi</p>
        </a>
      </li>

      <li class="nav-header">KOMUNIKASI</li>
      <li class="nav-item">
        <a href="<?php echo base_url('chat'); ?>"
          class="nav-link <?php echo ($this->uri->segment(1) == 'chat') ? 'active' : ''; ?>">
          <i class="nav-icon fas fa-comments"></i>
          <p>Ruang Diskusi</p>
        </a>
      </li>
    <?php endif; ?>

  </ul>
</nav>
</div>
</aside>




        