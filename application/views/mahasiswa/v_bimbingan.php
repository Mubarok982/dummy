<div class="content-wrapper">
    
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0 text-dark">Bimbingan Skripsi</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="<?php echo base_url('dashboard'); ?>">Home</a></li>
                        <li class="breadcrumb-item active">Bimbingan</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">
            
            <?php 
            // ============================================================
            // 1. INISIALISASI DATA & LOGIKA UTAMA
            // ============================================================
            
            $skripsi = isset($skripsi) ? $skripsi : null;
            $status_acc = isset($skripsi['status_acc_kaprodi']) ? $skripsi['status_acc_kaprodi'] : '';
            $status_ujian = isset($status_ujian) ? trim($status_ujian) : null; 
            $status_sempro_db = isset($skripsi['status_sempro']) ? $skripsi['status_sempro'] : '';
            $is_acc_diterima = ($status_acc == 'diterima');

            $max_bab_prodi = isset($max_bab) ? $max_bab : 6; 
            ?>

            <?php if (!$skripsi): ?>
                <div class="alert alert-danger shadow-sm">
                    <h5><i class="icon fas fa-ban"></i> Belum Mengajukan Judul!</h5>
                    Anda harus mengajukan judul skripsi terlebih dahulu.
                </div>
                <?php return; ?>
            <?php endif; ?>

            <?php if ($this->session->flashdata('pesan_sukses')): ?>
                <div class="alert alert-success alert-dismissible shadow-sm">
                    <button type="button" class="close" data-dismiss="alert">&times;</button>
                    <i class="icon fas fa-check"></i> <?= $this->session->flashdata('pesan_sukses'); ?>
                </div>
            <?php endif; ?>

            <div class="row">
                
                <div class="col-md-4">
                    <div class="card card-primary card-outline shadow mb-4">
                        <div class="card-body box-profile">
                            <div class="text-center">
                                <?php 
                                    $foto_profil = !empty($skripsi['foto_mahasiswa']) ? $skripsi['foto_mahasiswa'] : 'default-avatar.png';
                                    $foto_path = base_url('uploads/profile/' . $foto_profil);
                                ?>
                                <img class="profile-user-img img-fluid" style="border-radius: 50%; width: 128px; height: 128px; object-fit: cover;" src="<?php echo $foto_path; ?>" alt="Foto Profil" onerror="this.src='https://ui-avatars.com/api/?name=Student&background=007bff&color=fff&size=128'">
                            </div>
                            <h3 class="profile-username text-center mt-3"><?php echo $skripsi['nama_mahasiswa']; ?></h3>
                            <p class="text-muted text-center text-sm">
                                <strong><?php echo $skripsi['tema']; ?></strong>
                            </p>

                            <div class="text-center mb-3">
                                <span class="badge badge-secondary">Target: Sampai Bab <?= $max_bab_prodi; ?></span>
                            </div>

                            <ul class="list-group list-group-unbordered mb-3 mt-4">
                                <li class="list-group-item">
                                    <b>Pembimbing 1</b> <a class="float-right"><?php echo $skripsi['nama_p1']; ?></a>
                                </li>
                                <li class="list-group-item">
                                    <b>Pembimbing 2</b> <a class="float-right"><?php echo $skripsi['nama_p2']; ?></a>
                                </li>
                                <li class="list-group-item">
                                    <b>Status Bimbingan</b> 
                                    <a class="float-right">
                                        <?php 
                                        // Logika Status Bimbingan dengan 4 tahapan (Robust Detection)
                                        $status_bimbingan_label = "BIMBINGAN"; // default
                                        
                                        // Jika dinyatakan Mengulang atau judul ditolak -> tampilkan Mengulang
                                        if ($status_ujian == 'Mengulang' || strtolower($status_acc) == 'ditolak') {
                                            $status_bimbingan_label = "MENGULANG";
                                        }
                                        // Tahapan 0: Menunggu Cek Plagiarisme
                                        elseif ($status_sempro_db == 'Menunggu Plagiarisme') {
                                            $status_bimbingan_label = "MENUNGGU CEK PLAGIARISME";
                                        }
                                        // Jika sudah punya progress, gunakan logic yang lebih akurat
                                        elseif (isset($last_progres) && !empty($last_progres)) {
                                            $lp = (object) $last_progres;
                                            $bab_terakhir = $lp->bab;
                                            $p1 = $lp->progres_dosen1;
                                            $p2 = $lp->progres_dosen2;
                                            $is_last_bab_acc = ($p1 == 100 && $p2 == 100);
                                            
                                            // Tahapan 4: SIAP PENDADARAN - Jika bab terakhir sudah ACC
                                            if ($is_last_bab_acc && $bab_terakhir >= $max_bab_prodi) {
                                                $status_bimbingan_label = "SIAP PENDADARAN";
                                            }
                                            // Tahapan 3: SIAP SEMPRO - Jika bab 3 ACC dan belum bab 4
                                            elseif ($is_last_bab_acc && $bab_terakhir == 3) {
                                                $status_bimbingan_label = "SIAP SEMPRO";
                                            }
                                            // Tahapan 2: BIMBINGAN - Jika sudah bab 4 atau lebih (kembali dari siap sempro)
                                            elseif ($bab_terakhir >= 4) {
                                                $status_bimbingan_label = "BIMBINGAN";
                                            }
                                            // Default: BIMBINGAN (sedang proses revisi atau menunggu approval)
                                            else {
                                                $status_bimbingan_label = "BIMBINGAN";
                                            }
                                        }
                                        // Fallback ke status_sempro_db jika ada
                                        elseif ($status_sempro_db == 'Siap Pendadaran') {
                                            $status_bimbingan_label = "SIAP PENDADARAN";
                                        }
                                        elseif ($status_sempro_db == 'Siap Sempro') {
                                            $status_bimbingan_label = "SIAP SEMPRO";
                                        }
                                        
                                        echo $status_bimbingan_label;
                                        ?>
                                    </a>
                                </li>
                            </ul>
                            
                            <a href="<?php echo base_url('mahasiswa/riwayat_progres'); ?>" class="btn btn-primary btn-block">
                                <i class="fas fa-history mr-1"></i> Lihat Riwayat Revisi
                            </a>
                        </div>
                    </div>
                </div>

                <div class="col-md-8">
                    
                    <?php if (!$is_acc_diterima): ?>
                        <div class="alert alert-warning shadow">
                            <h5><i class="icon fas fa-lock"></i> Bimbingan Ditunda!</h5>
                            <p>Pengajuan Dosen Pembimbing Anda masih berstatus <b><?php echo strtoupper($status_acc); ?></b>.</p>
                        </div>
                    <?php else: ?>
                        
                        <?php 
                        // ============================================================
                        // LOGIKA PENENTUAN BAB DEFAULT
                        // ============================================================
                        
                        $target_bab = 1; 
                        $is_revisi = false;
                        
                        $status_card = 'card-primary';
                        $text_header = 'Upload Progres Baru';
                        $alert_style = 'callout-info';
                        $pesan_info = 'Silakan upload file untuk melanjutkan progres.';
                        $is_locked = false;
                        $notif_type = ""; 

                        if (isset($last_progres) && !empty($last_progres)) {
                            $lp = (object) $last_progres;
                            if ($lp->progres_dosen1 == 100 && $lp->progres_dosen2 == 100) {
                                $target_bab = $lp->bab + 1; // Normalnya naik bab
                            } else {
                                $target_bab = $lp->bab; 
                                $is_revisi = true;
                                $status_card = 'card-warning';
                                $text_header = 'Upload Revisi';
                                $pesan_info = 'Silakan upload revisi untuk bab ini.';
                            }
                        }

                        // ============================================================
                        // GATEKEEPER 1: SEMPRO (Meniru Pendadaran)
                        // Logika: Kunci total semua aksi saat target_bab mencapai angka 4
                        // ============================================================
                        if ($target_bab == 4) {
                            
                            // Kasus A: Jika sudah lulus, biarkan form terbuka dan masuk Bab 4
                            if ($status_ujian == 'Diterima' || $status_ujian == 'Lulus' || $status_ujian == 'Selesai') {
                                $is_locked = false; 
                            } 
                            
                            // Kasus B: Jika Perbaikan, Form dibuka, TAPI paksa mundur target_bab ke 3
                            elseif ($status_ujian == 'Perbaikan') {
                                $target_bab = 3; 
                                $is_revisi = true;
                                $is_locked = false; 
                                $notif_type = "revisi_sempro";
                                $status_card = 'card-warning';
                                $pesan_info = '<b>STATUS: PERBAIKAN SEMPRO.</b> Silakan upload revisi naskah (Bab 1-3).';
                            }
                            
                            // Kasus C: Mengulang -> Tutup Form
                            elseif ($status_ujian == 'Mengulang') {
                                $is_locked = true;
                                $notif_type = "mengulang";
                            }
                            
                            // Kasus D: Sedang Berlangsung / Menunggu Ujian -> Tutup Form
                            elseif ($status_ujian == 'Berlangsung' || $status_ujian == 'Menunggu') {
                                $is_locked = true;
                                $notif_type = "sempro_berlangsung";
                            }
                            
                            // Kasus E: Default (Baru saja ACC Bab 3, belum ngapa-ngapain) -> Tutup Form Paksa
                            else {
                                $is_locked = true;
                                $notif_type = "siap_sempro";
                            }
                        }

                        // Pengaman: Jika admin tiba-tiba mengubah status jadi 'Mengulang' di tengah jalan
                        if ($status_ujian == 'Mengulang') {
                            $is_locked = true;
                            $notif_type = "mengulang";
                        }

                        // ============================================================
                        // GATEKEEPER 2: PENDADARAN
                        // ============================================================
                        if ($target_bab > $max_bab_prodi) {
                            $notif_type = "siap_pendadaran";
                            $is_locked = true; 
                            $target_bab = $max_bab_prodi; 
                        }
                        
                        if ($target_bab > 6) $target_bab = 6;
                        ?>

                        <?php if ($notif_type == 'siap_sempro'): ?>
                            <div class="card bg-gradient-info shadow-lg mb-4">
                                <div class="card-body text-center p-4">
                                    <div class="mb-3"><i class="fas fa-bullhorn fa-4x text-white"></i></div>
                                    <h3 class="font-weight-bold text-white">Siap Seminar Proposal</h3>
                                    <p class="text-white">Selamat, Bab 1-3 telah disetujui! Silakan daftar sidang.</p>
                                    <a href="http://website-administrasi.com" target="_blank" class="btn btn-light font-weight-bold shadow pulse-button">
                                        <i class="fas fa-external-link-alt mr-2"></i> Daftar Sempro
                                    </a>
                                </div>
                            </div>

                        <?php elseif ($notif_type == 'sempro_berlangsung'): ?>
                            <div class="card bg-gradient-primary shadow-lg mb-4">
                                <div class="card-body text-center p-4">
                                    <div class="mb-3"><i class="fas fa-calendar-check fa-4x text-white"></i></div>
                                    <h3 class="font-weight-bold text-white">Seminar Proposal Sedang Diproses</h3>
                                    <p class="text-white">Selamat! Anda telah mendaftar/sedang menempuh Seminar Proposal.</p>
                                    <div class="alert alert-light d-inline-block p-3 mt-2 shadow-sm text-dark text-left">
                                        <h6 class="font-weight-bold mb-2"><i class="fas fa-info-circle text-primary mr-1"></i> Instruksi:</h6>
                                        <ul class="mb-0 pl-3 text-sm">
                                            <li>Selesaikan administrasi di website administrasi.</li>
                                            <li>Pantau jadwal dan hasil sidang Anda.</li>
                                            <li>Form bimbingan dikunci sementara hingga hasil keluar.</li>
                                        </ul>
                                    </div>
                                    <div class="mt-4">
                                        <a href="http://website-administrasi.com" target="_blank" class="btn btn-light font-weight-bold shadow pulse-button">
                                            <i class="fas fa-external-link-alt mr-2"></i> Buka Website Administrasi
                                        </a>
                                    </div>
                                </div>
                            </div>

                        <?php elseif ($notif_type == 'mengulang'): ?>
                            <div class="card bg-gradient-danger shadow-lg mb-4">
                                <div class="card-body text-center p-4">
                                    <div class="mb-3"><i class="fas fa-times-circle fa-4x text-white"></i></div>
                                    <h3 class="font-weight-bold text-white">Status: Mengulang</h3>
                                    <p class="text-white">Mohon maaf, berdasarkan hasil sidang, Anda dinyatakan harus <b>MENGULANG</b>.</p>
                                    <p class="text-white small">Akses upload ditutup. Silakan ajukan judul baru.</p>
                                    <a href="<?php echo base_url('mahasiswa/pengajuan_judul'); ?>" class="btn btn-light font-weight-bold shadow">
                                        <i class="fas fa-edit mr-2"></i> Ajukan Judul Baru
                                    </a>
                                </div>
                            </div>

                        <?php elseif ($notif_type == 'siap_pendadaran'): ?>
                            <div class="card bg-gradient-success shadow-lg mb-4">
                                <div class="card-body text-center p-4">
                                    <div class="mb-3"><i class="fas fa-graduation-cap fa-4x text-white"></i></div>
                                    <h3 class="font-weight-bold text-white">Luar Biasa! Siap Pendadaran</h3>
                                    <p class="text-white lead">Selamat! Anda telah menyelesaikan seluruh bimbingan materi.</p>
                                    <div class="mt-4">
                                        <a href="http://website-administrasi.com" target="_blank" class="btn btn-warning btn-lg text-dark font-weight-bold shadow pulse-button">
                                            <i class="fas fa-external-link-alt mr-2"></i> Daftar Pendadaran Sekarang
                                        </a>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>


                        <?php if ($is_locked): ?>
                            
                            <?php if (!in_array($notif_type, ['siap_pendadaran', 'siap_sempro', 'mengulang', 'sempro_berlangsung'])): ?>
                                <div class="card shadow mb-4 border-left-secondary">
                                    <div class="card-body text-center text-muted py-5">
                                        <div class="mb-3"><i class="fas fa-lock fa-4x text-gray-300"></i></div>
                                        <h5>Akses Upload Terkunci</h5>
                                    </div>
                                </div>
                            <?php endif; ?>

                        <?php else: ?>

                            <div class="card <?= $status_card ?> card-outline shadow mb-4">
                                <div class="card-header">
                                    <h3 class="card-title font-weight-bold">
                                        <i class="fas fa-upload mr-2"></i> <?= $text_header ?>
                                    </h3>
                                </div>
                                
                                <?php echo form_open_multipart('mahasiswa/upload_progres_bab'); ?>
                                <div class="card-body">
                                    
                                    <div class="callout <?= $alert_style ?>">
                                        <h5>Target: <b>BAB <?= $target_bab ?></b> <?= $is_revisi ? '(Revisi)' : '' ?></h5>
                                        <p><?= $pesan_info ?></p>
                                    </div>

                                    <input type="hidden" name="bab" value="<?= $target_bab ?>">
                                    <input type="hidden" name="is_revisi" value="<?= $is_revisi ? '1' : '0' ?>">

                                    <!-- BAGIAN EDIT JUDUL (OPSIONAL) -->
                                    <div class="card card-outline card-info mb-3">
                                        <div class="card-header">
                                            <h5 class="card-title">
                                                <div class="custom-control custom-checkbox d-inline-block">
                                                    <input type="checkbox" class="custom-control-input" id="gunakan_judul_lama" name="gunakan_judul_lama" checked>
                                                    <label class="custom-control-label" for="gunakan_judul_lama">
                                                        Gunakan Judul Sebelumnya
                                                    </label>
                                                </div>
                                            </h5>
                                        </div>
                                        <div class="card-body" id="judul_section" style="display: none;">
                                            <div class="alert alert-info">
                                                <small><i class="fas fa-info-circle mr-1"></i>
                                                    Anda dapat mengubah judul skripsi. Judul lama akan disimpan di riwayat perubahan.
                                                </small>
                                            </div>

                                            <div class="form-group">
                                                <label for="edit_judul">Judul Skripsi Baru</label>
                                                <textarea name="judul" id="edit_judul" class="form-control" rows="3"><?php echo $skripsi['judul']; ?></textarea>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- END EDIT JUDUL -->
                                    
                                    <div class="form-group">
                                        <label>File Laporan (PDF)</label>
                                        <div class="custom-file">
                                            <input type="file" class="custom-file-input" id="file_progres" name="file_progres" required accept=".pdf">
                                            <label class="custom-file-label" for="file_progres">Pilih file...</label>
                                        </div>
                                        <small class="text-muted mt-2 d-block">
                                            Format file: <code>.pdf</code>. Ukuran maks: 5MB.
                                        </small>
                                    </div>
                                </div>
                                <div class="card-footer bg-white">
                                    <button type="submit" class="btn btn-success float-right font-weight-bold px-4 shadow-sm">
                                        <i class="fas fa-paper-plane mr-1"></i> Kirim File
                                    </button>
                                </div>
                                <?php echo form_close(); ?>
                            </div>

                        <?php endif; ?>

                        <div class="card shadow mb-4">
                            <div class="card-header py-3 d-flex justify-content-between align-items-center">
                                <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-history mr-2"></i> Riwayat Bimbingan</h6>
                            </div>
                            
                            <div class="card-body p-0 table-responsive" style="max-height: 500px; overflow-y: auto;">
                                <table class="table table-striped table-hover mb-0 align-middle small">
                                    <thead class="bg-light sticky-top">
                                        <tr>
                                            <th style="width: 20%;">Tanggal & Status</th>
                                            <th style="width: 20%;">Judul Skripsi</th>
                                            <th style="width: 15%;">Bab</th>
                                            <th style="width: 35%;">Catatan Dosen</th>
                                            <th style="width: 10%;">File</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (isset($progres_riwayat) && !empty($progres_riwayat)): ?>
                                            <?php foreach ($progres_riwayat as $pr): 
                                                $tgl_upload = strtotime($pr->created_at);
                                                $tgl_judul_skrg = strtotime($skripsi['tgl_pengajuan_judul']);
                                                $format_tgl = date('d M Y H:i', $tgl_upload);
                                                
                                                // Logika Deteksi Judul Lama
                                                $is_old_title = ($tgl_upload < $tgl_judul_skrg);

                                                $p1 = $pr->progres_dosen1;
                                                $p2 = $pr->progres_dosen2;
                                                $is_acc = ($p1 == 100 && $p2 == 100);
                                                
                                                $k1 = $pr->komentar_dosen1 ? "<b>P1:</b> ".$pr->komentar_dosen1 : "";
                                                $k2 = $pr->komentar_dosen2 ? "<b>P2:</b> ".$pr->komentar_dosen2 : "";
                                                $komentar = $k1 . ($k1 && $k2 ? "<br>" : "") . $k2;
                                                if(empty($komentar)) $komentar = "<span class='text-muted font-italic'>- Menunggu koreksi -</span>";
                                            ?>
                                                <tr class="<?= $is_old_title ? 'bg-light text-muted' : '' ?>">
                                                    <td>
                                                        <div><?= $format_tgl ?></div>
                                                        <?php if ($is_old_title): ?>
                                                            <span class="badge badge-secondary mt-1">Riwayat Judul Lama</span>
                                                        <?php else: ?>
                                                            <span class="badge badge-success mt-1">Judul Saat Ini</span>
                                                        <?php endif; ?>
                                                    </td>
                                                    <td>
                                                        <strong><?= $skripsi['judul'] ?></strong>
                                                    </td>
                                                    <?php
                                                        $is_revisi_file = false;
                                                        if (!empty($pr->file) && stripos($pr->file, '_REVISI') !== false) {
                                                            $version_count = $this->M_Dosen->count_progres_versions($skripsi['npm'], $pr->bab);
                                                            if ($version_count > 1) $is_revisi_file = true;
                                                        }
                                                    ?>
                                                    <td>
                                                        <span class="font-weight-bold">BAB <?= $pr->bab ?></span>
                                                        <br>
                                                        <?php
                                                        // Logika badge status baru: Menunggu, Berlangsung, Dibatalkan, Selesai
                                                        $badge_status = "Menunggu"; // default
                                                        $badge_class = "badge-secondary";
                                                        
                                                        // Jika riwayat judul lama, tampilkan "Dibatalkan"
                                                        if ($is_old_title) {
                                                            $badge_status = "Dibatalkan";
                                                            $badge_class = "badge-danger";
                                                        }
                                                        // Jika judul current dan sudah semua ACC, tampilkan "Selesai"
                                                        elseif (!$is_old_title && $is_acc) {
                                                            $badge_status = "Selesai";
                                                            $badge_class = "badge-success";
                                                        }
                                                        // Jika judul current dan belum semua ACC, lihat status plagiarisme
                                                        elseif (!$is_old_title && !$is_acc) {
                                                            // Jika plagiarisme belum lulus, status "Menunggu"
                                                            if ($status_sempro_db == 'Menunggu Plagiarisme') {
                                                                $badge_status = "Menunggu";
                                                                $badge_class = "badge-secondary";
                                                            }
                                                            // Jika plagiarisme sudah lulus, status "Berlangsung"
                                                            else {
                                                                $badge_status = "Berlangsung";
                                                                $badge_class = "badge-info";
                                                            }
                                                        }
                                                        ?>
                                                        <span class="badge <?= $badge_class ?>"><?= $badge_status ?></span>
                                                    </td>
                                                    <td><?= $komentar ?></td>
                                                    <td>
                                                        <a href="<?= base_url('uploads/progres/' . $pr->file) ?>" target="_blank" class="btn btn-sm btn-info shadow-sm">
                                                            <i class="fas fa-file-pdf"></i>
                                                        </a>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <tr><td colspan="5" class="text-center text-muted py-4">Belum ada riwayat bimbingan.</td></tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                    <?php endif; ?>
                </div>
            </div>
        </div>
    </section>
</div>

<style>
    .pulse-button { animation: pulse 2s infinite; }
    @keyframes pulse {
        0% { transform: scale(1); box-shadow: 0 0 0 0 rgba(255, 193, 7, 0.7); }
        70% { transform: scale(1.05); box-shadow: 0 0 0 10px rgba(255, 193, 7, 0); }
        100% { transform: scale(1); box-shadow: 0 0 0 0 rgba(255, 193, 7, 0); }
    }
    .bg-gradient-success { background: linear-gradient(45deg, #28a745, #20c997); }
    .bg-gradient-info { background: linear-gradient(45deg, #17a2b8, #117a8b); }
    .bg-gradient-primary { background: linear-gradient(45deg, #007bff, #0056b3); }
    .bg-gradient-danger { background: linear-gradient(45deg, #dc3545, #e4606d); }
</style>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        var fileInput = document.getElementById('file_progres');
        if(fileInput){
            fileInput.addEventListener('change', function (e) {
                if(e.target.files.length > 0){
                    e.target.nextElementSibling.innerText = e.target.files[0].name;
                }
            });
        }

        // Toggle form edit judul berdasarkan checkbox
        var checkboxJudul = document.getElementById('gunakan_judul_lama');
        var judulSection = document.getElementById('judul_section');

        if(checkboxJudul) {
            checkboxJudul.addEventListener('change', function() {
                if(this.checked) {
                    judulSection.style.display = 'none';
                } else {
                    judulSection.style.display = 'block';
                }
            });
        }
    });
</script>