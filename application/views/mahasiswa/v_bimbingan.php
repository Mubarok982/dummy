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
            // 1. Inisialisasi Data
            $skripsi = isset($skripsi) ? $skripsi : null;
            $status_acc = isset($skripsi['status_acc_kaprodi']) ? $skripsi['status_acc_kaprodi'] : '';
            // Ambil Status Sempro (Default: menunggu syarat jika null)
            $status_sempro = isset($skripsi['status_sempro']) ? $skripsi['status_sempro'] : 'menunggu syarat';
            
            $is_acc_diterima = ($status_acc == 'diterima');
            $valid_recipients = isset($valid_recipients) ? $valid_recipients : [];
            ?>

            <?php if (!$skripsi): ?>
                <div class="alert alert-danger shadow-sm">
                    <h5><i class="icon fas fa-ban"></i> Belum Mengajukan Judul!</h5>
                    Anda harus mengajukan judul skripsi terlebih dahulu di menu <a href="<?php echo base_url('mahasiswa/pengajuan_judul'); ?>" class="text-bold text-white" style="text-decoration: underline;">Pengajuan Judul</a>.
                </div>
                <?php return; ?>
            <?php endif; ?>

            <?php if ($this->session->flashdata('pesan_sukses')): ?>
                <div class="alert alert-success alert-dismissible shadow-sm">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                    <h5><i class="icon fas fa-check"></i> Berhasil!</h5>
                    <?php echo $this->session->flashdata('pesan_sukses'); ?>
                </div>
            <?php endif; ?>

            <div class="row">
                
                <div class="col-md-5">
                    <div class="card card-primary card-outline shadow mb-4">
                        <div class="card-body box-profile">
                            <div class="text-center">
                                <img class="profile-user-img img-fluid img-circle" src="https://ui-avatars.com/api/?name=Skripsi&background=007bff&color=fff&size=128" alt="Skripsi Icon">
                            </div>
                            <h3 class="profile-username text-center mt-3">Skripsi Saya</h3>
                            <p class="text-muted text-center text-sm"><?php echo $skripsi['judul']; ?></p>

                            <ul class="list-group list-group-unbordered mb-3 mt-4">
                                <li class="list-group-item">
                                    <b>Pembimbing 1</b> <a class="float-right"><?php echo $skripsi['nama_p1']; ?></a>
                                </li>
                                <li class="list-group-item">
                                    <b>Pembimbing 2</b> <a class="float-right"><?php echo $skripsi['nama_p2']; ?></a>
                                </li>
                                <li class="list-group-item">
                                    <b>Status Sempro</b> 
                                    <a class="float-right badge badge-info">
                                        <?php echo strtoupper($status_sempro); ?>
                                    </a>
                                </li>
                            </ul>
                            
                            <a href="<?php echo base_url('mahasiswa/riwayat_progres'); ?>" class="btn btn-primary btn-block">
                                <i class="fas fa-history mr-1"></i> Lihat Riwayat Revisi
                            </a>
                        </div>
                    </div>
                    
                    <div class="card card-primary card-outline shadow collapsed-card">
                        <div class="card-header">
                            <h3 class="card-title"><i class="fas fa-comments mr-2"></i> Kontak Pembimbing</h3>
                            <div class="card-tools">
                                <button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-plus"></i></button>
                            </div>
                        </div>
                        <div class="card-body p-0">
                            <ul class="list-group list-group-flush">
                                <?php if ($is_acc_diterima): ?>
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        <span><i class="fas fa-user-graduate mr-2 text-success"></i> Pembimbing 1</span>
                                        <a href="<?php echo base_url('chat?id_lawan=' . $skripsi['pembimbing1']); ?>" class="badge badge-info"><i class="fas fa-comment"></i> Chat</a>
                                    </li>
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        <span><i class="fas fa-user-graduate mr-2 text-success"></i> Pembimbing 2</span>
                                        <a href="<?php echo base_url('chat?id_lawan=' . $skripsi['pembimbing2']); ?>" class="badge badge-info"><i class="fas fa-comment"></i> Chat</a>
                                    </li>
                                <?php endif; ?>
                            </ul>
                        </div>
                    </div>
                </div>

                <div class="col-md-7">
                    
                    <?php if (!$is_acc_diterima): ?>
                        <div class="alert alert-warning shadow">
                            <h5><i class="icon fas fa-lock"></i> Bimbingan Ditunda!</h5>
                            <p>Pengajuan Dosen Pembimbing Anda masih berstatus <b><?php echo strtoupper($status_acc); ?></b>.</p>
                        </div>
                    <?php else: ?>
                        
                        <?php 
                        // ============================================================
                        // LOGIKA PENENTUAN BAB & STATUS (CORE LOGIC)
                        // ============================================================
                        
                        $target_bab = 1; 
                        $is_revisi = false;
                        
                        // Default UI Variables
                        $status_card = 'card-success';
                        $icon_card = 'fa-plus-circle';
                        $text_header = 'Upload Progres Baru';
                        $alert_style = 'callout-success';
                        $pesan_info = 'Silakan upload file untuk melanjutkan progres.';
                        
                        // Flag khusus Sempro
                        $is_sempro_gate_locked = false; // Kunci jika sedang masa sidang
                        $sempro_lock_message = "";
                        $notif_type = ""; // untuk menampilkan banner khusus

                        // 1. Cek Progres Terakhir
                        if (isset($last_progres) && !empty($last_progres)) {
                            $lp = (object) $last_progres;
                            $p1 = $lp->progres_dosen1;
                            $p2 = $lp->progres_dosen2;
                            $bab_terakhir = $lp->bab;

                            if ($p1 == 100 && $p2 == 100) {
                                // ACC -> Lanjut Bab Selanjutnya
                                $target_bab = $bab_terakhir + 1;
                                $is_revisi = false;
                                $text_header = 'Lanjut ke BAB ' . $target_bab;
                                $icon_card = 'fa-arrow-circle-up';
                                $pesan_info = 'Selamat! Bab sebelumnya telah di-ACC. Silakan upload <b>BAB ' . $target_bab . '</b>.';
                            } else {
                                // Belum ACC -> Revisi
                                $target_bab = $bab_terakhir;
                                $is_revisi = true;
                                $status_card = 'card-warning';
                                $icon_card = 'fa-sync-alt';
                                $text_header = 'Upload Revisi BAB ' . $target_bab;
                                $alert_style = 'callout-warning';
                                $pesan_info = 'Bab ini belum disetujui sepenuhnya. File akan tercatat sebagai <b>REVISI</b>.';
                            }
                        }

                        // ============================================================
                        // LOGIKA GATEKEEPER SEMPRO (ANTARA BAB 3 DAN 4)
                        // ============================================================
                        if ($target_bab == 4) {
                            
                            // Cek Status Sempro dari Database
                            switch ($status_sempro) {
                                case 'menunggu syarat':
                                    // Sudah selesai Bab 3, tapi belum daftar/belum di-acc admin buat sempro
                                    // Tampilkan Notifikasi "Siap Sempro" tapi LOCK Bab 4
                                    $notif_type = "siap_sempro"; 
                                    $is_sempro_gate_locked = true;
                                    $sempro_lock_message = "Anda belum bisa lanjut ke Bab 4 sebelum status Seminar Proposal Anda disetujui.";
                                    break;

                                case 'siap sempro':
                                case 'dijadwalkan':
                                    // Sedang proses daftar / menunggu sidang
                                    $notif_type = "waiting_sempro";
                                    $is_sempro_gate_locked = true;
                                    $sempro_lock_message = "Upload dikunci karena Anda sedang dalam masa persiapan/pelaksanaan Seminar Proposal.";
                                    break;

                                case 'revisi sempro':
                                    // Selesai sidang tapi harus revisi
                                    // PAKSA MUNDUR KE BAB 3 (REVISI)
                                    $target_bab = 3;
                                    $is_revisi = true;
                                    $notif_type = "revisi_sempro";
                                    $is_sempro_gate_locked = false; // Buka gate tapi arahkan ke revisi
                                    
                                    // Override UI
                                    $status_card = 'card-warning';
                                    $text_header = 'Upload Revisi Pasca Sempro';
                                    $alert_style = 'callout-danger';
                                    $pesan_info = '<b>STATUS: REVISI SEMPRO.</b><br>Silakan upload naskah perbaikan hasil seminar proposal (Bab 1-3) untuk diperiksa dosen pembimbing.';
                                    break;

                                case 'mengulang': // Jika ada status ini
                                    $target_bab = 3; // Atau 1 tergantung kebijakan
                                    $is_revisi = true;
                                    $notif_type = "mengulang";
                                    $is_sempro_gate_locked = false;
                                    
                                    $status_card = 'card-danger';
                                    $text_header = 'Upload Ulang Naskah (Mengulang)';
                                    $alert_style = 'callout-danger';
                                    $pesan_info = '<b>STATUS: MENGULANG.</b><br>Anda dinyatakan Mengulang. Silakan perbaiki total naskah Anda.';
                                    break;

                                case 'disetujui sempro':
                                    // Lolos! Boleh lanjut Bab 4
                                    $is_sempro_gate_locked = false;
                                    // UI Normal Bab 4
                                    $pesan_info = 'Selamat! Seminar Proposal telah disetujui. Anda dapat melanjutkan penelitian <b>BAB 4</b>.';
                                    break;
                                
                                default:
                                    // Fallback jika status aneh, kunci saja biar aman
                                    $is_sempro_gate_locked = true;
                                    $sempro_lock_message = "Status Sempro tidak valid. Hubungi admin.";
                                    break;
                            }
                        }

                        // Batasi Maksimal Bab 6
                        if ($target_bab > 6) { $target_bab = 6; }
                        ?>

                        <?php if ($notif_type == 'siap_sempro'): ?>
                            <div class="card bg-gradient-success shadow-lg mb-4">
                                <div class="card-body text-center p-4">
                                    <div class="mb-3"><i class="fas fa-trophy fa-4x text-white"></i></div>
                                    <h3 class="font-weight-bold text-white">Bab 1-3 Selesai!</h3>
                                    <p class="text-white lead">Anda sudah memenuhi syarat akademik untuk <strong>Seminar Proposal</strong>.</p>
                                    <a href="http://website-administrasi.com" target="_blank" class="btn btn-warning font-weight-bold pulse-button">
                                        <i class="fas fa-external-link-alt mr-2"></i> Daftar Sempro Sekarang
                                    </a>
                                </div>
                            </div>
                        
                        <?php elseif ($notif_type == 'waiting_sempro'): ?>
                            <div class="card bg-gradient-info shadow-lg mb-4">
                                <div class="card-body text-center p-4">
                                    <div class="mb-3"><i class="fas fa-clock fa-4x text-white"></i></div>
                                    <h3 class="font-weight-bold text-white">Menunggu Pelaksanaan Sempro</h3>
                                    <p class="text-white">Semoga sukses ujiannya! Upload Bab 4 akan dibuka setelah hasil Sempro keluar dan status Anda menjadi <b>Disetujui Sempro</b>.</p>
                                </div>
                            </div>

                        <?php elseif ($notif_type == 'revisi_sempro'): ?>
                            <div class="alert alert-warning shadow-lg border-left-danger" role="alert">
                                <h4 class="alert-heading"><i class="fas fa-exclamation-triangle mr-2"></i> Perlu Revisi Pasca Sidang!</h4>
                                <p>Berdasarkan hasil Seminar Proposal, Anda diminta untuk melakukan <b>REVISI</b>. Silakan perbaiki naskah Bab 1-3 Anda dan upload ulang pada form di bawah ini untuk mendapatkan ACC Dosen Pembimbing kembali.</p>
                            </div>

                        <?php endif; ?>


                        <?php if ($is_sempro_gate_locked): ?>
                            
                            <div class="card shadow mb-4 border-left-secondary">
                                <div class="card-body text-center text-muted py-5">
                                    <i class="fas fa-lock fa-3x mb-3 text-gray-300"></i>
                                    <h5>Akses Upload Terkunci</h5>
                                    <p class="mb-0"><?php echo $sempro_lock_message; ?></p>
                                </div>
                            </div>

                        <?php else: ?>

                            <div class="card <?php echo $status_card; ?> card-outline shadow mb-4">
                                <div class="card-header">
                                    <h3 class="card-title font-weight-bold">
                                        <i class="fas <?php echo $icon_card; ?> mr-2"></i> <?php echo $text_header; ?>
                                    </h3>
                                </div>
                                
                                <?php echo form_open_multipart('mahasiswa/upload_progres_bab'); ?>
                                <div class="card-body">
                                    
                                    <div class="callout <?php echo $alert_style; ?>">
                                        <h5>Status Upload: <b><?php echo $is_revisi ? 'REVISI' : 'BARU'; ?></b></h5>
                                        <p><?php echo $pesan_info; ?></p>
                                    </div>

                                    <input type="hidden" name="bab" value="<?php echo $target_bab; ?>">
                                    <input type="hidden" name="is_revisi" value="<?php echo $is_revisi ? '1' : '0'; ?>"> 
                                    
                                    <div class="form-group">
                                        <label>File Laporan Skripsi (PDF)</label>
                                        <div class="custom-file">
                                            <input type="file" class="custom-file-input" id="file_progres" name="file_progres" required accept=".pdf">
                                            <label class="custom-file-label" for="file_progres">Pilih file...</label>
                                        </div>
                                        <small class="text-muted mt-2 d-block">
                                            <i class="fas fa-info-circle"></i> 
                                            File otomatis dinamai: 
                                            <code>[NPM]_BAB_<?php echo $target_bab; ?><?php echo $is_revisi ? '_REVISI' : ''; ?>.pdf</code>
                                        </small>
                                    </div>
                                </div>
                                <div class="card-footer bg-white">
                                    <button type="submit" class="btn btn-<?php echo $is_revisi ? 'warning' : 'success'; ?> float-right font-weight-bold px-4 shadow-sm">
                                        <i class="fas fa-paper-plane mr-1"></i> Kirim <?php echo $is_revisi ? 'Revisi' : 'Progres'; ?>
                                    </button>
                                </div>
                                <?php echo form_close(); ?>
                            </div>

                        <?php endif; ?>

                        <div class="card shadow mb-4">
                            <div class="card-header py-3">
                                <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-list-alt mr-2"></i> Riwayat Upload Terakhir</h6>
                            </div>
                            <div class="card-body p-0">
                                <table class="table table-striped mb-0 align-middle">
                                    <thead class="bg-light">
                                        <tr>
                                            <th>Bab</th>
                                            <th>Tanggal</th>
                                            <th>File</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (isset($progres_riwayat) && !empty($progres_riwayat)): ?>
                                            <?php 
                                            $limit_riwayat = array_slice($progres_riwayat, 0, 3); 
                                            foreach ($limit_riwayat as $pr): 
                                                $is_revisi_file = (stripos($pr->file, '_REVISI') !== false);
                                            ?>
                                                <tr>
                                                    <td>
                                                        <b>BAB <?= $pr->bab ?></b>
                                                        <?php if ($is_revisi_file): ?>
                                                            <span class="badge badge-warning ml-1">Revisi</span>
                                                        <?php endif; ?>
                                                    </td> 
                                                    <td><?= date('d M Y H:i', strtotime($pr->created_at)) ?></td>
                                                    <td>
                                                        <a href="<?= base_url('uploads/progres/' . $pr->file) ?>" target="_blank" class="btn btn-sm btn-info">
                                                            <i class="fas fa-file-pdf"></i>
                                                        </a>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <tr><td colspan="3" class="text-center text-muted">Belum ada file.</td></tr>
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
        0% { transform: scale(1); box-shadow: 0 0 0 0 rgba(40, 167, 69, 0.7); }
        70% { transform: scale(1.05); box-shadow: 0 0 0 10px rgba(40, 167, 69, 0); }
        100% { transform: scale(1); box-shadow: 0 0 0 0 rgba(40, 167, 69, 0); }
    }
    .bg-gradient-success { background: linear-gradient(45deg, #28a745, #20c997); }
    .bg-gradient-info { background: linear-gradient(45deg, #17a2b8, #117a8b); }
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
    });
</script>