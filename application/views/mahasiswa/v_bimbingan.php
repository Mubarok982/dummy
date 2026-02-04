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
            
            // Ambil Status Ujian (Berlangsung/Diterima/Perbaikan/Mengulang)
            $status_ujian = isset($status_ujian) ? $status_ujian : null;
            
            // Ambil Status Sempro dari Tabel Skripsi (Siap Sempro/Menunggu Syarat)
            $status_sempro_db = isset($skripsi['status_sempro']) ? $skripsi['status_sempro'] : '';

            $is_acc_diterima = ($status_acc == 'diterima');
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

            <?php if ($this->session->flashdata('pesan_error')): ?>
                <div class="alert alert-danger alert-dismissible shadow-sm">
                    <button type="button" class="close" data-dismiss="alert">&times;</button>
                    <i class="icon fas fa-ban"></i> <?= $this->session->flashdata('pesan_error'); ?>
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
                                        <?php 
                                        if ($status_ujian) {
                                            echo strtoupper($status_ujian);
                                        } elseif ($status_sempro_db == 'Siap Sempro') {
                                            echo "SIAP SEMPRO";
                                        } else {
                                            echo "BELUM DAFTAR";
                                        }
                                        ?>
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
                        // LOGIKA INTI (CORE LOGIC)
                        // ============================================================
                        
                        $target_bab = 1; 
                        $is_revisi = false;
                        
                        // Default UI
                        $status_card = 'card-primary';
                        $text_header = 'Upload Progres Baru';
                        $alert_style = 'callout-info';
                        $pesan_info = 'Silakan upload file untuk melanjutkan progres.';
                        $is_locked = false;
                        $lock_msg = "";
                        $show_congrats_sempro = false; // Flag khusus ucapan selamat

                        // 1. Cek Progres Terakhir (Normal)
                        if (isset($last_progres) && !empty($last_progres)) {
                            $lp = (object) $last_progres;
                            if ($lp->progres_dosen1 == 100 && $lp->progres_dosen2 == 100) {
                                $target_bab = $lp->bab + 1;
                            } else {
                                $target_bab = $lp->bab;
                                $is_revisi = true;
                                $status_card = 'card-warning';
                                $text_header = 'Upload Revisi';
                                $pesan_info = 'Silakan upload revisi untuk bab ini.';
                            }
                        }

                        // 2. LOGIKA SPESIAL SEMPRO (Siap Sempro / Ujian)
                        
                        // A. Cek Status "Siap Sempro" dari Skripsi (Belum Ujian tapi sudah ACC Bab 3)
                        if ($status_sempro_db == 'Siap Sempro' && $status_ujian == null) {
                            $show_congrats_sempro = true;
                            $is_locked = true; // Kunci upload karena harus daftar dulu
                            $lock_msg = "Form upload dikunci. Silakan lakukan pendaftaran Seminar Proposal.";
                        }

                        // B. Cek Status Ujian (Jika sudah ada data di tabel ujian_skripsi)
                        if ($status_ujian != null) {
                            
                            switch ($status_ujian) {
                                case 'Berlangsung':
                                    $is_locked = true;
                                    $lock_msg = "Form upload <b>DIKUNCI</b> karena status Ujian Anda sedang <b>BERLANGSUNG</b>. Harap tunggu hasil sidang keluar.";
                                    break;

                                case 'Diterima':
                                    $target_bab = 4;
                                    $is_revisi = false;
                                    $status_card = 'card-success';
                                    $text_header = 'Lanjut Penelitian (BAB 4)';
                                    $alert_style = 'callout-success';
                                    $pesan_info = 'Selamat! Ujian <b>DITERIMA</b>. Silakan lanjutkan penelitian ke <b>BAB 4</b>.';
                                    $is_locked = false;
                                    break;

                                case 'Perbaikan':
                                    // Asumsi revisi Bab 1-3
                                    $target_bab = 3; 
                                    $is_revisi = true;
                                    $status_card = 'card-warning';
                                    $text_header = 'Upload Revisi Pasca Ujian';
                                    $alert_style = 'callout-warning';
                                    $pesan_info = 'Status: <b>PERBAIKAN</b>. Silakan upload revisi naskah (Bab 1-3) sesuai masukan penguji.';
                                    $is_locked = false;
                                    break;

                                case 'Mengulang':
                                    $target_bab = 1;
                                    $is_revisi = true; 
                                    $status_card = 'card-danger';
                                    $text_header = 'Upload Ulang Naskah';
                                    $alert_style = 'callout-danger';
                                    $pesan_info = 'Status: <b>MENGULANG</b>. Anda diwajibkan mengulang proses dari <b>BAB 1</b>.';
                                    $is_locked = false;
                                    break;
                            }
                        }
                        
                        if ($target_bab > 6) $target_bab = 6;
                        ?>

                        <?php if ($show_congrats_sempro): ?>
                            <div class="card bg-gradient-success shadow-lg mb-4">
                                <div class="card-body text-center p-4">
                                    <div class="mb-3">
                                        <i class="fas fa-trophy fa-4x text-white"></i>
                                    </div>
                                    <h3 class="font-weight-bold text-white mb-2">Selamat! Anda Siap Seminar Proposal</h3>
                                    <p class="text-white lead">
                                        Seluruh persyaratan akademik Bab 1-3 telah terpenuhi.
                                    </p>
                                    
                                    <div class="alert alert-light d-inline-block p-3 mt-2 shadow-sm text-dark text-left" style="border-radius: 8px; max-width: 90%;">
                                        <h6 class="font-weight-bold mb-2"><i class="fas fa-info-circle text-info mr-1"></i> Langkah Selanjutnya:</h6>
                                        <ul class="mb-0 pl-3 text-sm">
                                            <li>Silakan mendaftar Seminar Proposal melalui website Administrasi.</li>
                                            <li>Tunggu jadwal ujian keluar.</li>
                                            <li>Upload Bab 4 baru akan dibuka setelah Anda dinyatakan <b>Lulus Sempro</b>.</li>
                                        </ul>
                                    </div>

                                    <div class="mt-4">
                                        <a href="http://website-administrasi-kampus.com" target="_blank" class="btn btn-warning btn-lg text-dark font-weight-bold shadow pulse-button">
                                            <i class="fas fa-external-link-alt mr-2"></i> Daftar Sempro Sekarang
                                        </a>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>


                        <?php if ($is_locked): ?>
                            
                            <div class="card shadow mb-4 border-left-secondary">
                                <div class="card-body text-center text-muted py-5">
                                    <div class="mb-3">
                                        <i class="fas fa-lock fa-4x text-gray-300"></i>
                                    </div>
                                    <h5>Akses Upload Terkunci</h5>
                                    <p class="mb-0"><?= $lock_msg ?></p>
                                </div>
                            </div>

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
                            <div class="card-header py-3">
                                <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-history mr-2"></i> Riwayat Upload Terakhir</h6>
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
                                            $limit_riwayat = array_slice($progres_riwayat, 0, 5); 
                                            foreach ($limit_riwayat as $pr): 
                                                $is_rev = (stripos($pr->file, '_REVISI') !== false);
                                            ?>
                                                <tr>
                                                    <td>
                                                        <b>BAB <?= $pr->bab ?></b>
                                                        <?php if ($is_rev): ?>
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
        0% { transform: scale(1); box-shadow: 0 0 0 0 rgba(255, 193, 7, 0.7); }
        70% { transform: scale(1.05); box-shadow: 0 0 0 10px rgba(255, 193, 7, 0); }
        100% { transform: scale(1); box-shadow: 0 0 0 0 rgba(255, 193, 7, 0); }
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