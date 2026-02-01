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
            // 1. Inisialisasi Data dengan Aman
            $skripsi = isset($skripsi) ? $skripsi : null;
            $status_acc = isset($skripsi['status_acc_kaprodi']) ? $skripsi['status_acc_kaprodi'] : '';
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

            <?php if ($this->session->flashdata('pesan_error')): ?>
                <div class="alert alert-danger alert-dismissible shadow-sm">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                    <h5><i class="icon fas fa-ban"></i> Gagal!</h5>
                    <?php echo $this->session->flashdata('pesan_error'); ?>
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
                                    <b>Status Kaprodi</b> 
                                    <a class="float-right badge badge-<?php echo $is_acc_diterima ? 'success' : 'warning'; ?>">
                                        <?php echo strtoupper($status_acc); ?>
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
                                <?php if (isset($valid_recipients['kaprodi']) && !empty($valid_recipients['kaprodi'])): ?>
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        <span><i class="fas fa-user-tie mr-2 text-primary"></i> Kaprodi</span>
                                        <a href="<?php echo base_url('chat?id_lawan=' . $valid_recipients['kaprodi']); ?>" class="badge badge-info">
                                            <i class="fas fa-comment"></i> Chat
                                        </a>
                                    </li>
                                <?php endif; ?>

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
                            <p>Mohon tunggu persetujuan Kaprodi sebelum memulai upload draft.</p>
                        </div>
                    <?php else: ?>
                        
                        <?php 
                        // ============================================================
                        // LOGIKA PENENTUAN BAB & STATUS (REVISI / BARU)
                        // ============================================================
                        
                        $target_bab = 1; // Default Bab 1
                        $is_revisi = false;
                        $status_card = 'card-success';
                        $icon_card = 'fa-plus-circle';
                        $text_header = 'Upload Progres Baru';
                        $alert_style = 'callout-success';
                        $pesan_info = 'Silakan upload file untuk melanjutkan progres.';

                        // Variable flag untuk notifikasi SEMPRO
                        $siap_sempro = false;

                        // Cek data terakhir
                        if (isset($last_progres) && !empty($last_progres)) {
                            // Konversi ke object jika array
                            $lp = (object) $last_progres;
                            
                            $p1 = $lp->progres_dosen1;
                            $p2 = $lp->progres_dosen2;
                            $bab_terakhir = $lp->bab;

                            if ($p1 == 100 && $p2 == 100) {
                                // Jika KEDUANYA ACC (100) -> Masuk Bab Selanjutnya
                                $target_bab = $bab_terakhir + 1;
                                $is_revisi = false;
                                
                                $status_card = 'card-success';
                                $icon_card = 'fa-arrow-circle-up';
                                $text_header = 'Lanjut ke BAB ' . $target_bab;
                                $pesan_info = 'Selamat! Bab sebelumnya telah di-ACC. Silakan upload <b>BAB ' . $target_bab . '</b>.';

                                // --- LOGIKA JIKA BAB 3 SELESAI (Masuk Target Bab 4) ---
                                if ($target_bab == 4) {
                                    $siap_sempro = true;
                                }
                                // ------------------------------------------

                            } else {
                                // Jika BELUM ACC Keduanya -> Masuk Mode Revisi Bab Tersebut
                                $target_bab = $bab_terakhir;
                                $is_revisi = true;
                                
                                $status_card = 'card-warning';
                                $icon_card = 'fa-sync-alt';
                                $text_header = 'Upload Revisi BAB ' . $target_bab;
                                $alert_style = 'callout-warning';
                                $pesan_info = 'Bab ini belum disetujui sepenuhnya (ACC Penuh). File yang Anda upload akan tercatat sebagai <b>REVISI</b>.';
                            }
                        }
                        
                        // Batasi Maksimal Bab 6
                        if ($target_bab > 6) { $target_bab = 6; }
                        ?>

                        <?php if ($siap_sempro): ?>
                            <div class="card bg-gradient-success shadow-lg mb-4">
                                <div class="card-body text-center p-4">
                                    <div class="mb-3">
                                        <i class="fas fa-check-circle fa-4x text-white"></i>
                                    </div>
                                    <h3 class="font-weight-bold text-white mb-2">Selamat! Anda Siap Seminar Proposal</h3>
                                    <p class="text-white lead">
                                        Seluruh progres bimbingan dari <strong>BAB 1 hingga BAB 3</strong> telah disetujui oleh kedua dosen pembimbing.
                                    </p>
                                    
                                    <div class="alert alert-light d-inline-block p-3 mt-2 shadow-sm text-dark text-left" style="border-radius: 8px; max-width: 90%;">
                                        <h6 class="font-weight-bold mb-2"><i class="fas fa-info-circle text-info mr-1"></i> Instruksi Selanjutnya:</h6>
                                        <ul class="mb-0 pl-3 text-sm">
                                            <li>Segera daftarkan diri Anda untuk <strong>Seminar Proposal (Sempro)</strong>.</li>
                                            <li>Selesaikan persyaratan administrasi di website akademik.</li>
                                            <li>Anda baru diperbolehkan mengupload <strong>BAB 4</strong> setelah Seminar Proposal selesai.</li>
                                        </ul>
                                    </div>

                                    <div class="mt-4">
                                        <a href="http://website-administrasi-kampus.com" target="_blank" class="btn btn-warning btn-lg text-dark font-weight-bold shadow pulse-button">
                                            <i class="fas fa-external-link-alt mr-2"></i> Ke Website Administrasi
                                        </a>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>
                        <div class="card <?php echo $status_card; ?> card-outline shadow mb-4">
                            <div class="card-header">
                                <h3 class="card-title font-weight-bold">
                                    <i class="fas <?php echo $icon_card; ?> mr-2"></i> <?php echo $text_header; ?>
                                </h3>
                            </div>
                            
                            <?php echo form_open_multipart('mahasiswa/upload_progres_bab'); ?>
                            <div class="card-body">
                                
                                <div class="callout <?php echo $alert_style; ?>">
                                    <h5>Status: <b><?php echo $is_revisi ? 'REVISI' : 'BARU'; ?></b></h5>
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
                                        File akan otomatis dinamai: 
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
                                        <th style="width: 30%;">Bab</th>
                                        <th style="width: 40%;">Tanggal Upload</th>
                                        <th style="width: 30%;">File</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (isset($progres_riwayat) && !empty($progres_riwayat)): ?>
                                        <?php 
                                        // Ambil 3 data teratas
                                        $limit_riwayat = array_slice($progres_riwayat, 0, 3); 
                                        
                                        foreach ($limit_riwayat as $pr): 
                                            // LOGIKA DETEKSI FILE REVISI
                                            $is_revisi_file = (stripos($pr->file, '_REVISI') !== false);
                                        ?>
                                            <tr>
                                                <td>
                                                    <span class="font-weight-bold">BAB <?= $pr->bab ?></span>
                                                    
                                                    <?php if ($is_revisi_file): ?>
                                                        <span class="badge badge-warning ml-2">Revisi</span>
                                                    <?php endif; ?>
                                                </td> 
                                                
                                                <td>
                                                    <i class="far fa-calendar-alt text-muted mr-1"></i>
                                                    <?= date('d M Y H:i', strtotime($pr->created_at)) ?>
                                                </td>
                                                
                                                <td>
                                                    <a href="<?= base_url('uploads/progres/' . $pr->file) ?>" target="_blank" class="btn btn-sm btn-info shadow-sm">
                                                        <i class="fas fa-file-pdf mr-1"></i> Lihat
                                                    </a>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="3" class="text-center text-muted py-4">
                                                Belum ada file yang diunggah.
                                            </td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                            <div class="card-footer text-center bg-white">
                                <a href="<?php echo base_url('mahasiswa/riwayat_progres'); ?>" class="small font-weight-bold">Lihat Semua Riwayat <i class="fas fa-arrow-circle-right"></i></a>
                            </div>
                        </div>
                    </div>

                </div>
            </div>

        </div>
    </section>
</div>

<style>
    @keyframes pulse {
        0% { transform: scale(1); box-shadow: 0 0 0 0 rgba(255, 255, 255, 0.7); }
        70% { transform: scale(1.05); box-shadow: 0 0 0 10px rgba(255, 255, 255, 0); }
        100% { transform: scale(1); box-shadow: 0 0 0 0 rgba(255, 255, 255, 0); }
    }
    .pulse-button {
        animation: pulse 2s infinite;
    }
    /* Warna gradien hijau agar terlihat lebih resmi/sukses */
    .bg-gradient-success {
        background: linear-gradient(45deg, #28a745, #218838);
    }
</style>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        // Handle nama file input custom bootstrap
        var fileInput = document.getElementById('file_progres');
        if(fileInput){
            fileInput.addEventListener('change', function (e) {
                if(e.target.files.length > 0){
                    var fileName = e.target.files[0].name;
                    var nextSibling = e.target.nextElementSibling;
                    nextSibling.innerText = fileName;
                }
            });
        }
    });
</script>