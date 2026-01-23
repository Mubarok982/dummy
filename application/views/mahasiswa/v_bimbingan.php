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
            // Menggunakan Null Coalescing agar tidak error jika variabel tidak ada
            $skripsi = isset($skripsi) ? $skripsi : null;
            
            // Cek apakah user sudah punya judul yang di-ACC
            // Kita asumsikan $skripsi adalah ARRAY (row_array). Jika error di sini, ubah jadi object ->
            $status_acc = isset($skripsi['status_acc_kaprodi']) ? $skripsi['status_acc_kaprodi'] : '';
            $is_acc_diterima = ($status_acc == 'diterima');
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
                    
                    <div class="card card-primary card-outline shadow">
                        <div class="card-header">
                            <h3 class="card-title"><i class="fas fa-comments mr-2"></i> Ruang Diskusi</h3>
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
                    <?php endif; ?>
                    
                    <?php if ($is_acc_diterima): ?>
                        
                        <?php 
                        $is_upload_allowed = TRUE;
                        $block_message = '';
                        
                        if (isset($next_bab) && $next_bab > 1 && isset($last_progres)) {
                            // Cek apakah last_progres object atau array (untuk jaga-jaga)
                            $p1 = is_array($last_progres) ? $last_progres['progres_dosen1'] : $last_progres->progres_dosen1;
                            $p2 = is_array($last_progres) ? $last_progres['progres_dosen2'] : $last_progres->progres_dosen2;

                            if ($p1 != 100 || $p2 != 100) {
                                $is_upload_allowed = FALSE;
                                $block_message = 'Anda belum bisa melanjutkan ke <b>BAB '.$next_bab.'</b>.<br>Selesaikan revisi <b>BAB '.($next_bab-1).'</b> hingga mendapatkan ACC Penuh.';
                            }
                        }
                        ?>
                        
                        <?php if ($is_upload_allowed): ?>
                            <div class="card card-success shadow mb-4">
                                <div class="card-header">
                                    <h3 class="card-title"><i class="fas fa-cloud-upload-alt mr-2"></i> Upload Progres (Target BAB)</h3>
                                </div>
                                <?php echo form_open_multipart('mahasiswa/upload_progres_bab'); ?>
                                <div class="card-body">
                                    <div class="callout callout-success">
                                        <h5>Target: <b>BAB <?php echo isset($next_bab) ? $next_bab : 1; ?></b></h5>
                                        <p>Silakan upload file laporan skripsi terbaru Anda (PDF).</p>
                                    </div>

                                    <input type="hidden" name="bab" value="<?php echo isset($next_bab) ? $next_bab : 1; ?>">
                                    
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
                            <div class="alert alert-warning shadow mb-4">
                                <h5><i class="icon fas fa-lock"></i> Upload Terkunci!</h5>
                                <p><?php echo $block_message; ?></p>
                            </div>
                        <?php endif; ?>
                        
                        <div class="card shadow mb-4">
                            <div class="card-header py-3 bg-navy">
                                <h6 class="m-0 font-weight-bold text-white"><i class="fas fa-file-upload mr-2"></i> Upload Revisi Ulang (Bebas Pilih Bab)</h6>
                            </div>
                            <div class="card-body">
                                <?php echo form_open_multipart('mahasiswa/upload_draft'); ?>
                                    <div class="form-group row">
                                        <label for="bab_bebas" class="col-sm-3 col-form-label">Pilih Bab</label>
                                        <div class="col-sm-9">
                                            <select name="bab" id="bab_bebas" class="form-control" required>
                                                <option value="">-- Pilih Bab --</option>
                                                <option value="1">BAB 1</option>
                                                <option value="2">BAB 2</option>
                                                <option value="3">BAB 3</option>
                                                <option value="4">BAB 4</option>
                                                <option value="5">BAB 5</option>
                                                <option value="6">BAB 6 (Penutup)</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label for="draft_file_bebas" class="col-sm-3 col-form-label">File PDF</label>
                                        <div class="col-sm-9">
                                            <div class="custom-file">
                                                <input type="file" name="draft_file" class="custom-file-input" id="draft_file_bebas" required accept=".pdf">
                                                <label class="custom-file-label" for="draft_file_bebas">Pilih file...</label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group row mt-3">
                                        <div class="col-sm-12 text-right">
                                            <button type="submit" class="btn btn-navy"><i class="fas fa-paper-plane"></i> Kirim Revisi</button>
                                        </div>
                                    </div>
                                <?php echo form_close(); ?>
                            </div>
                        </div>
                        
                    <?php endif; ?>

                </div>
            </div> <div class="row">
                <div class="col-md-12">
                    <div class="card shadow mb-4">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-list-alt mr-2"></i> Riwayat Upload Terakhir</h6>
                        </div>
                        <div class="card-body p-0">
                            <table class="table table-striped mb-0">
                                <thead>
                                    <tr>
                                        <th>Bab</th>
                                        <th>Tanggal Upload</th>
                                        <th>File</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (isset($progres_riwayat) && !empty($progres_riwayat)): ?>
                                        <?php 
                                        // Gunakan array_slice untuk ambil 3 data teratas
                                        $limit_riwayat = array_slice($progres_riwayat, 0, 3); 
                                        foreach ($limit_riwayat as $pr): 
                                        ?>
                                            <tr>
                                                <td>BAB <?= $pr->bab ?></td> 
                                                <td><?= date('d M Y H:i', strtotime($pr->created_at)) ?></td>
                                                <td><a href="<?= base_url('uploads/progres/' . $pr->file) ?>" target="_blank" class="btn btn-xs btn-info"><i class="fas fa-file-pdf"></i> Lihat</a></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr><td colspan="3" class="text-center text-muted">Belum ada file yang diunggah.</td></tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                            <div class="card-footer text-center">
                                <a href="<?php echo base_url('mahasiswa/riwayat_progres'); ?>" class="small font-weight-bold">Lihat Semua Riwayat <i class="fas fa-arrow-circle-right"></i></a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div></section></div><script>
    document.addEventListener("DOMContentLoaded", function() {
        // Handle nama file input utama
        var fileInput1 = document.getElementById('file_progres');
        if(fileInput1){
            fileInput1.addEventListener('change', function (e) {
                var fileName = e.target.files[0].name;
                var nextSibling = e.target.nextElementSibling;
                nextSibling.innerText = fileName;
            });
        }

        // Handle nama file input bebas
        var fileInput2 = document.getElementById('draft_file_bebas');
        if(fileInput2){
            fileInput2.addEventListener('change', function (e) {
                var fileName = e.target.files[0].name;
                var nextSibling = e.target.nextElementSibling;
                nextSibling.innerText = fileName;
            });
        }
    });
</script>