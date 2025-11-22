<div class="row" style="display: flex; flex-wrap: wrap; gap: 20px; margin-top: 20px;">
    
    <?php 
    $role = $this->session->userdata('role');
    $stats = $statistik;
    ?>

    <?php if ($role == 'operator' || $role == 'tata_usaha'): ?>

        <div class="card-stat" style="flex: 1; min-width: 250px; background-color: #f7f7f7; padding: 20px; border-radius: 8px; border-left: 5px solid var(--color-primary);">
            <h4><i class="fas fa-users"></i> Total Mahasiswa</h4>
            <p style="font-size: 2.5em; font-weight: bold; color: var(--color-dark);"><?php echo $stats['total_mhs']; ?></p>
        </div>
        
        <div class="card-stat" style="flex: 1; min-width: 250px; background-color: #f7f7f7; padding: 20px; border-radius: 8px; border-left: 5px solid var(--color-success);">
            <h4><i class="fas fa-book-reader"></i> Mhs. Ajukan Skripsi</h4>
            <p style="font-size: 2.5em; font-weight: bold; color: var(--color-dark);"><?php echo $stats['mhs_skripsi']; ?></p>
        </div>
        
        <div class="card-stat" style="flex: 1; min-width: 250px; background-color: #f7f7f7; padding: 20px; border-radius: 8px; border-left: 5px solid var(--color-primary);">
            <h4>Total Mahasiswa</h4>
            <p style="font-size: 2.5em; font-weight: bold; color: var(--color-dark);"><?php echo $stats['total_mhs']; ?></p>
        </div>
        
        <div class="card-stat" style="flex: 1; min-width: 250px; background-color: #f7f7f7; padding: 20px; border-radius: 8px; border-left: 5px solid var(--color-success);">
            <h4>Mhs. Ajukan Skripsi</h4>
            <p style="font-size: 2.5em; font-weight: bold; color: var(--color-dark);"><?php echo $stats['mhs_skripsi']; ?></p>
        </div>
        
        <div class="card-stat" style="flex: 1; min-width: 250px; background-color: #f7f7f7; padding: 20px; border-radius: 8px; border-left: 5px solid var(--color-danger);">
            <h4>Total Dosen</h4>
            <p style="font-size: 2.5em; font-weight: bold; color: var(--color-dark);"><?php echo $stats['total_dosen']; ?></p>
        </div>

        <div class="card-stat" style="flex: 1; min-width: 250px; background-color: #f7f7f7; padding: 20px; border-radius: 8px; border-left: 5px solid #ffc107;">
            <h4>Siap Sempro (Bab 3 ACC)</h4>
            <p style="font-size: 2.5em; font-weight: bold; color: var(--color-dark);"><?php echo $stats['mhs_ready_sempro']; ?></p>
        </div>

    <?php elseif ($role == 'dosen'): ?>

        <div class="card-stat" style="flex: 1; min-width: 300px; background-color: #f7f7f7; padding: 25px; border-radius: 8px; border-left: 5px solid var(--color-primary);">
            <h4>Total Mahasiswa Bimbingan Anda</h4>
            <p style="font-size: 3em; font-weight: bold; color: var(--color-dark);"><?php echo $stats['total_bimbingan']; ?></p>
            <a href="<?php echo base_url('dosen/bimbingan_list'); ?>" style="display: block; margin-top: 10px;">Lihat Daftar Mahasiswa &rarr;</a>
        </div>
        
        <?php if ($this->session->userdata('is_kaprodi') == 1): ?>
        <div class="card-stat" style="flex: 1; min-width: 300px; background-color: #fff8e1; padding: 25px; border-radius: 8px; border-left: 5px solid #ffc107;">
            <h4>Anda adalah KAPRODI</h4>
            <p style="font-size: 1.2em; margin-top: 10px;">Akses penuh ke monitoring progres seluruh mahasiswa Prodi **<?php echo $this->session->userdata('prodi'); ?>**.</p>
            <a href="<?php echo base_url('dosen/monitoring_prodi'); ?>" style="display: block; margin-top: 10px;">Akses Monitoring Kaprodi &rarr;</a>
        </div>
        <?php endif; ?>

    <?php elseif ($role == 'mahasiswa'): ?>

        <?php
        $current_bab = $stats['last_bab'];
        $total_bab = 5; // Target 5 Bab (misal: Bab 1-5 untuk sidang)
        $progress_percent = min(100, round(($current_bab / $total_bab) * 100)); // Batasi di 100%
        $progress_color = $progress_percent < 50 ? '#dc3545' : ($progress_percent < 100 ? '#ffc107' : '#28a745');
        ?>

        <div class="card-stat" style="flex: 1; min-width: 300px; background-color: #e9f7ef; padding: 25px; border-radius: 8px; border-left: 5px solid var(--color-success);">
            <h4><i class="fas fa-file-signature"></i> Status Pengajuan Judul</h4>
            <p style="font-size: 2em; font-weight: bold; color: var(--color-dark); margin-top: 5px;"><?php echo $stats['judul_status']; ?></p>
            <a href="<?php echo base_url('mahasiswa/pengajuan_judul'); ?>" style="display: block; margin-top: 10px;">Kelola Judul &rarr;</a>
        </div>

        <div class="card-stat" style="flex: 2; min-width: 400px; background-color: #f7f7f7; padding: 25px; border-radius: 8px; border-left: 5px solid var(--color-primary);">
            <h4><i class="fas fa-tasks"></i> Progres Bimbingan Keseluruhan (Bab 1 - 5)</h4>
            
            <div style="font-size: 2em; font-weight: bold; color: var(--color-dark); margin-top: 10px; margin-bottom: 5px;">
                BAB <?php echo $current_bab; ?> (<?php echo $progress_percent; ?>%)
            </div>
            
            <div style="width: 100%; background-color: #e0e0e0; border-radius: 4px; height: 15px; margin-bottom: 15px;">
                <div style="width: <?php echo $progress_percent; ?>%; background-color: <?php echo $progress_color; ?>; height: 15px; border-radius: 4px; transition: width 1s;"></div>
            </div>

            <a href="<?php echo base_url('mahasiswa/progres_skripsi'); ?>" style="display: block; margin-top: 10px;">Lanjut Bimbingan &rarr;</a>
        </div>

    <?php endif; ?>
</div>

<div style="margin-top: 30px;">
    <h3>Informasi Akun</h3>
    <p>Nama: **<?php echo $this->session->userdata('nama'); ?>**</p>
    <p>Role: **<?php echo ucfirst($role); ?>**</p>
    <?php if ($this->session->userdata('npm')): ?>
        <p>NPM: **<?php echo $this->session->userdata('npm'); ?>**</p>
    <?php endif; ?>
    <?php if ($this->session->userdata('nidk')): ?>
        <p>NIDK: **<?php echo $this->session->userdata('nidk'); ?>**</p>
    <?php endif; ?>
</div>