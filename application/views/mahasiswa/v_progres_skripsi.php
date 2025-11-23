<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-12">

            <?php 
            if (!$skripsi) {
                echo '<div class="alert alert-danger">
                        <h5><i class="icon fas fa-ban"></i> Belum Mengajukan Judul!</h5>
                        Anda harus mengajukan judul skripsi terlebih dahulu di menu <a href="'.base_url('mahasiswa/pengajuan_judul').'">Pengajuan Judul</a>.
                      </div>';
                return;
            }
            ?>

            <?php if ($this->session->flashdata('pesan_sukses')): ?>
                <div class="alert alert-success alert-dismissible">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                    <h5><i class="icon fas fa-check"></i> Berhasil!</h5>
                    <?php echo $this->session->flashdata('pesan_sukses'); ?>
                </div>
            <?php endif; ?>

            <?php if ($this->session->flashdata('pesan_error')): ?>
                <div class="alert alert-danger alert-dismissible">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                    <h5><i class="icon fas fa-ban"></i> Gagal!</h5>
                    <?php echo $this->session->flashdata('pesan_error'); ?>
                </div>
            <?php endif; ?>

            <div class="callout callout-info mb-4 shadow-sm">
                <h5 class="text-info"><i class="fas fa-book mr-1"></i> Detail Skripsi</h5>
                <p class="mb-1"><strong>Judul:</strong> <?php echo $skripsi['judul']; ?></p>
                <div class="row mt-2">
                    <div class="col-md-6">
                        <i class="fas fa-user-tie mr-1"></i> <strong>Pembimbing 1:</strong> <?php echo $skripsi['nama_p1']; ?>
                    </div>
                    <div class="col-md-6">
                        <i class="fas fa-user-tie mr-1"></i> <strong>Pembimbing 2:</strong> <?php echo $skripsi['nama_p2']; ?>
                    </div>
                </div>
            </div>

            <div class="card card-primary card-outline">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-history mr-1"></i> Riwayat Progres Bimbingan
                    </h3>
                </div>
                
                <div class="card-body table-responsive p-0">
                    <table class="table table-hover table-striped">
                        <thead>
                            <tr class="bg-light text-center">
                                <th style="width: 10%;">Bab</th>
                                <th style="width: 15%;">File</th>
                                <th style="width: 15%;">Tanggal</th>
                                <th style="width: 15%;">Status P1</th>
                                <th style="width: 15%;">Status P2</th>
                                <th style="width: 30%;">Komentar Terbaru</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($progres)): ?>
                                <tr><td colspan="6" class="text-center py-4 text-muted">Belum ada riwayat bimbingan. Silakan upload Bab 1.</td></tr>
                            <?php else: ?>
                                <?php foreach ($progres as $p): ?>
                                <tr>
                                    <td class="text-center align-middle">
                                        <span class="badge badge-secondary">BAB <?php echo $p['bab']; ?></span>
                                    </td>
                                    <td class="text-center align-middle">
                                        <a href="<?php echo base_url('uploads/progres/' . $p['file']); ?>" target="_blank" class="btn btn-xs btn-default border">
                                            <i class="fas fa-file-pdf text-danger"></i> Download
                                        </a>
                                    </td>
                                    <td class="text-center align-middle small">
                                        <?php echo date('d M Y', strtotime($p['created_at'])); ?>
                                    </td>
                                    
                                    <td class="text-center align-middle">
                                        <?php 
                                        $badge = 'secondary'; $icon = 'clock';
                                        if ($p['progres_dosen1'] == 100) { $badge = 'success'; $icon = 'check-circle'; }
                                        elseif ($p['progres_dosen1'] == 50) { $badge = 'warning'; $icon = 'exclamation-circle'; }
                                        elseif ($p['progres_dosen1'] == 0 && $p['nilai_dosen1'] == 'Revisi') { $badge = 'danger'; $icon = 'times-circle'; }
                                        ?>
                                        <span class="badge badge-<?php echo $badge; ?>">
                                            <i class="fas fa-<?php echo $icon; ?>"></i> <?php echo $p['nilai_dosen1'] ?: 'Menunggu'; ?>
                                        </span>
                                    </td>

                                    <td class="text-center align-middle">
                                        <?php 
                                        $badge = 'secondary'; $icon = 'clock';
                                        if ($p['progres_dosen2'] == 100) { $badge = 'success'; $icon = 'check-circle'; }
                                        elseif ($p['progres_dosen2'] == 50) { $badge = 'warning'; $icon = 'exclamation-circle'; }
                                        elseif ($p['progres_dosen2'] == 0 && $p['nilai_dosen2'] == 'Revisi') { $badge = 'danger'; $icon = 'times-circle'; }
                                        ?>
                                        <span class="badge badge-<?php echo $badge; ?>">
                                            <i class="fas fa-<?php echo $icon; ?>"></i> <?php echo $p['nilai_dosen2'] ?: 'Menunggu'; ?>
                                        </span>
                                    </td>

                                    <td class="small align-middle">
                                        <?php if($p['komentar_dosen1']): ?>
                                            <strong>P1:</strong> <span class="text-muted"><?php echo $p['komentar_dosen1']; ?></span><br>
                                        <?php endif; ?>
                                        <?php if($p['komentar_dosen2']): ?>
                                            <strong>P2:</strong> <span class="text-muted"><?php echo $p['komentar_dosen2']; ?></span>
                                        <?php endif; ?>
                                        <?php if(!$p['komentar_dosen1'] && !$p['komentar_dosen2']) echo '<span class="text-muted font-italic">- Tidak ada komentar -</span>'; ?>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <?php 
            $is_upload_allowed = TRUE;
            $block_message = '';
            
            if ($next_bab > 1) {
                if ($last_progres['progres_dosen1'] != 100 || $last_progres['progres_dosen2'] != 100) {
                    $is_upload_allowed = FALSE;
                    $block_message = 'Anda belum bisa melanjutkan ke <b>BAB '.$next_bab.'</b>.<br>Pastikan <b>BAB '.($next_bab-1).'</b> sudah mendapatkan status <b>ACC Penuh (100%)</b> dari kedua Pembimbing.';
                }
            }
            ?>

            <?php if ($is_upload_allowed): ?>
                <div class="card card-success card-outline mt-4">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fas fa-upload mr-1"></i> Upload Progres: <b>BAB <?php echo $next_bab; ?></b></h3>
                    </div>
                    <?php echo form_open_multipart('mahasiswa/upload_progres_bab'); ?>
                    <div class="card-body">
                        <input type="hidden" name="bab" value="<?php echo $next_bab; ?>">
                        
                        <div class="form-group">
                            <label for="file_progres">Pilih File PDF (Maks. 5MB)</label>
                            <div class="input-group">
                                <div class="custom-file">
                                    <input type="file" class="custom-file-input" id="file_progres" name="file_progres" required accept=".pdf">
                                    <label class="custom-file-label" for="file_progres">Choose file</label>
                                </div>
                            </div>
                            <small class="text-muted">* Pastikan file yang diupload adalah versi terbaru.</small>
                        </div>
                    </div>
                    <div class="card-footer">
                        <button type="submit" class="btn btn-success"><i class="fas fa-cloud-upload-alt mr-1"></i> Upload BAB <?php echo $next_bab; ?></button>
                    </div>
                    <?php echo form_close(); ?>
                </div>
            
            <?php else: ?>
                <div class="alert alert-warning mt-4">
                    <h5><i class="icon fas fa-exclamation-triangle"></i> Akses Upload Terkunci!</h5>
                    <?php echo $block_message; ?>
                </div>
            <?php endif; ?>

        </div>
    </div>
</div>

<script>
    document.querySelector('.custom-file-input').addEventListener('change', function (e) {
        var fileName = document.getElementById("file_progres").files[0].name;
        var nextSibling = e.target.nextElementSibling;
        nextSibling.innerText = fileName;
    });
</script>