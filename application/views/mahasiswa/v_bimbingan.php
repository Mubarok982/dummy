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
                    <i class="icon fas fa-exclamation-triangle"></i> <?= $this->session->flashdata('pesan_error'); ?>
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
                            
                            <p class="text-muted text-center text-sm mb-1">
                                <strong><i class="fas fa-tag mr-1"></i> <?php echo $skripsi['tema']; ?></strong>
                            </p>
                            
                            <?php if (isset($status_acc) && strtolower($status_acc) == 'diterima'): ?>
                                <p class="text-center font-weight-bold px-2 mb-3 text-dark" style="font-size: 15px; line-height: 1.4;">
                                    <?php echo $skripsi['judul']; ?>
                                </p>
                            <?php else: ?>
                                <p class="text-center text-muted px-2 mb-3 font-italic" style="font-size: 13px;">
                                    (Judul skripsi sedang diproses Kaprodi)
                                </p>
                            <?php endif; ?>

                            <div class="text-center mb-3">
                                <span class="badge badge-secondary">Target: Sampai Bab <?= isset($max_bab) ? $max_bab : 6; ?></span>
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
                                        $label = "BIMBINGAN"; 
                                        if (isset($status_acc) && strtolower($status_acc) == 'ditolak') {
                                            $label = "DITOLAK";
                                        } else {
                                            if (isset($notif_type) && $notif_type != '') {
                                                if ($notif_type == 'mengulang') $label = "MENGULANG";
                                                elseif ($notif_type == 'siap_sempro' || $notif_type == 'siap_sempro_ulang') $label = "SIAP SEMPRO";
                                                elseif ($notif_type == 'siap_pendadaran' || $notif_type == 'siap_pendadaran_ulang') $label = "SIAP PENDADARAN";
                                                elseif ($notif_type == 'sempro_berlangsung') $label = "PROSES SEMPRO";
                                                elseif ($notif_type == 'pendadaran_berlangsung') $label = "PROSES PENDADARAN";
                                                elseif ($notif_type == 'lulus_akhir') $label = "LULUS SKRIPSI";
                                            } elseif (isset($is_revisi) && $is_revisi && isset($is_locked) && !$is_locked) {
                                                if (isset($target_bab) && isset($max_bab) && $target_bab >= $max_bab) $label = "REVISI PENDADARAN";
                                                elseif (isset($target_bab) && $target_bab == 3) $label = "REVISI SEMPRO";
                                                else $label = "REVISI BAB " . (isset($target_bab) ? $target_bab : '');
                                            }
                                        }
                                        echo $label;
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
                    
                    <?php if (isset($status_acc) && strtolower($status_acc) != 'diterima'): ?>
                        <div class="alert alert-warning shadow">
                            <h5><i class="icon fas fa-lock"></i> Bimbingan Ditunda!</h5>
                            <p>Pengajuan Dosen Pembimbing Anda masih berstatus <b><?php echo strtoupper($status_acc); ?></b>.</p>
                        </div>
                    <?php else: ?>

                        <?php if (isset($notif_type) && $notif_type != ''): ?>
                            
                            <?php if ($notif_type == 'siap_sempro' || $notif_type == 'siap_sempro_ulang'): ?>
                                <div class="card bg-gradient-info shadow-lg mb-4">
                                    <div class="card-body text-center p-4">
                                        <div class="mb-3"><i class="fas fa-bullhorn fa-4x text-white"></i></div>
                                        <h3 class="font-weight-bold text-white">Siap Seminar Proposal</h3>
                                        <?php if ($notif_type == 'siap_sempro_ulang'): ?>
                                            <p class="text-white">File Revisi Pasca-Sidang Anda telah di-ACC. Silakan hubungi Administrasi untuk konfirmasi kelulusan / penjadwalan ulang.</p>
                                        <?php else: ?>
                                            <p class="text-white">Selamat, Bimbingan BAB 3 telah disetujui Dosen! Silakan daftar Seminar Proposal sekarang.</p>
                                        <?php endif; ?>
                                        <a href="http://website-administrasi.com" target="_blank" class="btn btn-light font-weight-bold shadow pulse-button mt-2">
                                            <i class="fas fa-external-link-alt mr-2"></i> Pendaftaran / Konfirmasi Administrasi
                                        </a>
                                    </div>
                                </div>

                            <?php elseif ($notif_type == 'sempro_berlangsung'): ?>
                               <div class="card bg-gradient-primary shadow-lg mb-4">
                                    <div class="card-body text-center p-4">
                                        <div class="mb-3"><i class="fas fa-calendar-check fa-4x text-white"></i></div>
                                        <h3 class="font-weight-bold text-white">Selamat Anda telah ACC BAB 3</h3>
                                        <p class="text-white">Silakan mendaftar sempro di website Sita</p>
                                        
                                        <a href="https://sita.domainkampus.ac.id" target="_blank" class="btn btn-light font-weight-bold shadow pulse-button mt-2">
                                            <i class="fas fa-external-link-alt mr-2"></i> Menuju Website SITA
                                        </a>
                                    </div>
                                </div>

                            <?php elseif ($notif_type == 'siap_pendadaran' || $notif_type == 'siap_pendadaran_ulang'): ?>
                                <div class="card bg-gradient-info shadow-lg mb-4">
                                    <div class="card-body text-center p-4">
                                        <div class="mb-3"><i class="fas fa-graduation-cap fa-4x text-white"></i></div>
                                        <h3 class="font-weight-bold text-white">Siap Ujian Pendadaran</h3>
                                        <?php if ($notif_type == 'siap_pendadaran_ulang'): ?>
                                            <p class="text-white lead">Revisi Akhir telah di-ACC. Silakan Konfirmasi Ujian Ulang / Pengesahan ke Administrasi.</p>
                                        <?php else: ?>
                                            <p class="text-white lead">Selamat! Anda telah menyelesaikan seluruh bimbingan materi skripsi. Silakan daftar Ujian Pendadaran sekarang.</p>
                                        <?php endif; ?>
                                        <a href="http://website-administrasi.com" target="_blank" class="btn btn-warning btn-lg text-dark font-weight-bold shadow pulse-button mt-3">
                                            <i class="fas fa-external-link-alt mr-2"></i> Pendaftaran / Konfirmasi Administrasi
                                        </a>
                                    </div>
                                </div>
                                
                            <?php elseif ($notif_type == 'pendadaran_berlangsung'): ?>
                                <div class="card bg-gradient-primary shadow-lg mb-4">
                                    <div class="card-body text-center p-4">
                                        <div class="mb-3"><i class="fas fa-calendar-check fa-4x text-white"></i></div>
                                        <h3 class="font-weight-bold text-white">Sidang Pendadaran Berlangsung</h3>
                                        <p class="text-white">Anda sedang dalam masa Sidang Pendadaran Skripsi. Persiapkan diri Anda!</p>
                                        <a href="http://website-administrasi.com" target="_blank" class="btn btn-warning btn-lg text-dark font-weight-bold shadow pulse-button mt-3">
                                            <i class="fas fa-external-link-alt mr-2"></i> Pendaftaran / Konfirmasi Administrasi
                                        </a>
                                    </div>
                                </div>

                            <?php elseif ($notif_type == 'lulus_akhir'): ?>
                                <div class="card bg-gradient-success shadow-lg mb-4">
                                    <div class="card-body text-center p-4">
                                        <div class="mb-3"><i class="fas fa-trophy fa-4x text-white"></i></div>
                                        <h3 class="font-weight-bold text-white">SELAMAT! ANDA TELAH LULUS</h3>
                                        <p class="text-white lead">Anda telah resmi Lulus Sidang Pendadaran. <br>Proses Bimbingan ditutup secara permanen.</p>
                                    </div>
                                </div>

                            <?php elseif ($notif_type == 'mengulang'): ?>
                                <div class="card bg-gradient-danger shadow-lg mb-4">
                                    <div class="card-body text-center p-4">
                                        <div class="mb-3"><i class="fas fa-times-circle fa-4x text-white"></i></div>
                                        <h3 class="font-weight-bold text-white">Status: Mengulang</h3>
                                        <p class="text-white">Mohon maaf, berdasarkan hasil sidang, Anda dinyatakan harus <b>MENGULANG</b>.</p>
                                        <p class="text-white small">Akses upload ditutup. Silakan ajukan judul baru untuk memecah status blokir ini dan memulai kembali dari Bab 1.</p>
                                        <a href="<?php echo base_url('mahasiswa/pengajuan_judul'); ?>" class="btn btn-light font-weight-bold shadow">
                                            <i class="fas fa-edit mr-2"></i> Ajukan Judul Baru
                                        </a>
                                    </div>
                                </div>
                            <?php endif; ?>
                            
                        <?php endif; ?>

                        <?php if (isset($is_locked) && $is_locked): ?>
                            <?php if (!isset($notif_type) || $notif_type == ''): ?>
                                <div class="card shadow mb-4 border-left-secondary">
                                    <div class="card-body text-center text-muted py-5">
                                        <div class="mb-3"><i class="fas fa-lock fa-4x text-gray-300"></i></div>
                                        <h5>Akses Upload Terkunci</h5>
                                    </div>
                                </div>
                            <?php endif; ?>
                        <?php else: ?>

                            <div class="card <?= isset($status_card) ? $status_card : 'card-primary' ?> card-outline shadow mb-4">
                                <div class="card-header">
                                    <h3 class="card-title font-weight-bold">
                                        <i class="fas fa-upload mr-2"></i> <?= isset($text_header) ? $text_header : 'Upload Progres Baru' ?>
                                    </h3>
                                </div>
                                
                                <?php echo form_open_multipart('mahasiswa/upload_progres_bab'); ?>
                                <div class="card-body">
                                    
                                    <div class="callout callout-info">
                                        <h5>Target: <b>BAB <?= isset($target_bab) ? $target_bab : 1 ?></b> <?= (isset($is_revisi) && $is_revisi) ? '(Revisi)' : '' ?></h5>
                                        <p><?= isset($pesan_info) ? $pesan_info : 'Silakan upload file untuk melanjutkan progres.' ?></p>
                                    </div>

                                    <input type="hidden" name="bab" value="<?= isset($target_bab) ? $target_bab : 1 ?>">
                                    <input type="hidden" name="is_revisi" value="<?= (isset($is_revisi) && $is_revisi) ? '1' : '0' ?>">
                                    <input type="hidden" name="id_skripsi" value="<?= $skripsi['id'] ?>">

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
                                <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-history mr-2"></i> Semua Riwayat Upload</h6>
                            </div>
                            
                            <div class="card-body p-0 table-responsive" style="max-height: 500px; overflow-y: auto;">
                                <table class="table table-hover mb-0 align-middle small">
                                    <thead class="bg-light sticky-top">
                                        <tr>
                                            <th style="width: 20%;">Tanggal & Judul</th>
                                            <th style="width: 15%;">Bab</th>
                                            <th style="width: 15%;">Status ACC</th>
                                            <th style="width: 40%;">Catatan Dosen</th>
                                            <th style="width: 10%;">File</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php 
                                        // AMBIL SEMUA RIWAYAT, URUTKAN DARI YANG PALING BARU DIUPLOAD KE PALING LAMA
                                        $semua_riwayat = [];
                                        if(isset($progres_riwayat) && !empty($progres_riwayat)) {
                                            $semua_riwayat = $progres_riwayat;
                                            
                                            // Sortir data berdasarkan waktu upload (created_at) dari yang terbaru ke terlama
                                            usort($semua_riwayat, function($a, $b) {
                                                return strtotime($b->created_at) - strtotime($a->created_at);
                                            });
                                        }
                                        ?>
                                        
                                        <?php if (!empty($semua_riwayat)): ?>
                                            <?php foreach ($semua_riwayat as $pr): 
                                                $format_tgl = date('d M Y H:i', strtotime($pr->created_at));
                                                
                                                // Cek apakah ini progres dari judul skripsi yang aktif saat ini, atau judul lama
                                                $is_old_title = ($pr->id_skripsi != $skripsi['id']);
                                                $row_class = $is_old_title ? 'bg-light text-muted' : '';

                                                $p1 = $pr->progres_dosen1;
                                                $p2 = $pr->progres_dosen2;
                                                $is_acc = ($p1 == 100 && $p2 == 100);
                                                
                                                $k1 = $pr->komentar_dosen1 ? "<b>P1:</b> ".$pr->komentar_dosen1 : "";
                                                $k2 = $pr->komentar_dosen2 ? "<b>P2:</b> ".$pr->komentar_dosen2 : "";
                                                $komentar = $k1 . ($k1 && $k2 ? "<br>" : "") . $k2;
                                                if(empty($komentar)) $komentar = "<span class='text-muted font-italic'>- Menunggu koreksi -</span>";
                                            ?>
                                                <tr class="<?= $row_class ?>">
                                                    <td>
                                                        <div class="<?= $is_old_title ? 'text-muted' : 'text-dark font-weight-bold' ?>"><?= $format_tgl ?></div>
                                                        <?php if ($is_old_title): ?>
                                                            <span class="badge badge-secondary mt-1">Riwayat Lama</span>
                                                        <?php else: ?>
                                                            <span class="badge badge-success mt-1">Judul Aktif</span>
                                                        <?php endif; ?>
                                                    </td>
                                                    <td>
                                                        <span class="font-weight-bold <?= $is_old_title ? 'text-secondary' : 'text-dark' ?>">BAB <?= $pr->bab ?></span>
                                                    </td>
                                                    <td>
                                                        <?php
                                                        $badge_status = "Menunggu"; 
                                                        $badge_class = "badge-secondary";
                                                        
                                                        if ($is_acc) {
                                                            $badge_status = "ACC Selesai";
                                                            $badge_class = "badge-success";
                                                        } elseif (!$is_old_title && !$is_acc) {
                                                            $badge_status = "Koreksi";
                                                            $badge_class = "badge-info";
                                                        } elseif ($is_old_title) {
                                                            $badge_status = "Diabaikan (Lama)";
                                                            $badge_class = "badge-secondary";
                                                        }
                                                        ?>
                                                        <span class="badge <?= $badge_class ?>"><?= $badge_status ?></span>
                                                    </td>
                                                    <td><?= $komentar ?></td>
                                                    <td>
                                                        <a href="<?= base_url('uploads/progres/' . $pr->file) ?>" target="_blank" class="btn btn-sm <?= $is_old_title ? 'btn-secondary' : 'btn-info' ?> shadow-sm">
                                                            <i class="fas fa-file-pdf"></i>
                                                        </a>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <tr><td colspan="5" class="text-center text-muted py-4">Belum ada riwayat bimbingan untuk Anda.</td></tr>
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