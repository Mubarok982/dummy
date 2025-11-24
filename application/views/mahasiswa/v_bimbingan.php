<div class="container-fluid">
    
    <?php if ($this->session->flashdata('pesan_sukses')): ?>
        <div class="alert alert-success alert-dismissible shadow-sm">
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
            <h5><i class="icon fas fa-check"></i> Berhasil!</h5>
            <?php echo $this->session->flashdata('pesan_sukses'); ?>
        </div>
    <?php endif; ?>

    <div class="row justify-content-center">
        
        <div class="col-md-5">
            <div class="card card-primary card-outline shadow">
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
                            <b>Status Saat Ini</b> 
                            <a class="float-right badge badge-info">
                                <?php echo ($last_progres) ? 'BAB ' . $last_progres['bab'] : 'Belum Mulai'; ?>
                            </a>
                        </li>
                    </ul>
                    
                    <a href="<?php echo base_url('mahasiswa/riwayat_progres'); ?>" class="btn btn-primary btn-block">
                        <i class="fas fa-history mr-1"></i> Lihat Riwayat Revisi
                    </a>
                </div>
            </div>
        </div>

        <div class="col-md-7">
            <?php 
            $is_upload_allowed = TRUE;
            $block_message = '';
            
            if ($next_bab > 1) {
                if ($last_progres['progres_dosen1'] != 100 || $last_progres['progres_dosen2'] != 100) {
                    $is_upload_allowed = FALSE;
                    $block_message = 'Anda belum bisa melanjutkan ke <b>BAB '.$next_bab.'</b>.<br>Selesaikan revisi <b>BAB '.($next_bab-1).'</b> hingga mendapatkan ACC Penuh dari kedua pembimbing.';
                }
            }
            ?>

            <?php if ($is_upload_allowed): ?>
                <div class="card card-success shadow">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fas fa-cloud-upload-alt mr-2"></i> Upload Progres Bimbingan</h3>
                    </div>
                    <?php echo form_open_multipart('mahasiswa/upload_progres_bab'); ?>
                    <div class="card-body">
                        <div class="callout callout-success">
                            <h5>Target: <b>BAB <?php echo $next_bab; ?></b></h5>
                            <p>Silakan upload file laporan skripsi terbaru Anda dalam format PDF.</p>
                        </div>

                        <input type="hidden" name="bab" value="<?php echo $next_bab; ?>">
                        
                        <div class="form-group">
                            <label>File Laporan (PDF)</label>
                            <div class="custom-file">
                                <input type="file" class="custom-file-input" id="file_progres" name="file_progres" required accept=".pdf">
                                <label class="custom-file-label" for="file_progres">Pilih file...</label>
                            </div>
                            <small class="text-muted mt-1 d-block">* Maksimal ukuran file 5MB.</small>
                        </div>
                    </div>
                    <div class="card-footer">
                        <button type="submit" class="btn btn-success float-right font-weight-bold px-4">
                            <i class="fas fa-paper-plane mr-1"></i> Kirim
                        </button>
                    </div>
                    <?php echo form_close(); ?>
                </div>
            <?php else: ?>
                <div class="alert alert-warning shadow">
                    <h5><i class="icon fas fa-lock"></i> Upload Terkunci!</h5>
                    <p><?php echo $block_message; ?></p>
                    <hr>
                    <a href="<?php echo base_url('mahasiswa/riwayat_progres'); ?>" class="btn btn-outline-dark text-dark text-decoration-none">Cek Riwayat Revisi</a>
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