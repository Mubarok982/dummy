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
            if (!$skripsi) {
                echo '<div class="alert alert-danger shadow-sm">
                        <h5><i class="icon fas fa-ban"></i> Belum Mengajukan Judul!</h5>
                        Anda harus mengajukan judul skripsi terlebih dahulu di menu <a href="'.base_url('mahasiswa/pengajuan_judul').'" class="text-bold text-white" style="text-decoration: underline;">Pengajuan Judul</a>.
                      </div>';
                return;
            }
            ?>

            <?php if ($this->session->flashdata('pesan_sukses')): ?>
                <div class="alert alert-success alert-dismissible fade show shadow-sm">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                    <h5><i class="icon fas fa-check"></i> Berhasil!</h5>
                    <?php echo $this->session->flashdata('pesan_sukses'); ?>
                </div>
            <?php endif; ?>

            <?php if ($this->session->flashdata('pesan_error')): ?>
                <div class="alert alert-danger alert-dismissible fade show shadow-sm">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                    <h5><i class="icon fas fa-ban"></i> Gagal!</h5>
                    <?php echo $this->session->flashdata('pesan_error'); ?>
                </div>
            <?php endif; ?>

            <div class="row">
                <div class="col-md-4">
                    
                    <div class="card shadow-sm border-left-primary">
                        <div class="card-body">
                            <h6 class="text-muted text-uppercase font-weight-bold" style="letter-spacing: 1px; font-size: 0.8rem;">Judul Skripsi</h6>
                            <h5 class="font-weight-bold text-dark mt-2 mb-0" style="line-height: 1.5;">
                                <?php echo $skripsi['judul']; ?>
                            </h5>
                        </div>
                    </div>

                    <div class="card shadow-sm">
                        <div class="card-header bg-light py-2">
                            <h3 class="card-title text-muted text-sm font-weight-bold mb-0">TIM PEMBIMBING</h3>
                        </div>
                        <div class="card-body p-0">
                            <ul class="products-list product-list-in-card pl-2 pr-2">
                                <li class="item">
                                    <div class="product-img">
                                        <img src="https://ui-avatars.com/api/?name=<?php echo urlencode($skripsi['nama_p1']); ?>&background=007bff&color=fff" alt="P1" class="img-circle img-size-50">
                                    </div>
                                    <div class="product-info">
                                        <a href="javascript:void(0)" class="product-title text-dark"><?php echo $skripsi['nama_p1']; ?></a>
                                        <span class="product-description text-primary text-sm font-weight-bold">
                                            Pembimbing 1
                                        </span>
                                    </div>
                                </li>
                                <li class="item">
                                    <div class="product-img">
                                        <img src="https://ui-avatars.com/api/?name=<?php echo urlencode($skripsi['nama_p2']); ?>&background=6c757d&color=fff" alt="P2" class="img-circle img-size-50">
                                    </div>
                                    <div class="product-info">
                                        <a href="javascript:void(0)" class="product-title text-dark"><?php echo $skripsi['nama_p2']; ?></a>
                                        <span class="product-description text-secondary text-sm font-weight-bold">
                                            Pembimbing 2
                                        </span>
                                    </div>
                                </li>
                            </ul>
                        </div>
                    </div>

                    <?php 
                    $is_upload_allowed = TRUE;
                    $block_message = '';
                    
                    if ($next_bab > 1) {
                        // Cek apakah bab sebelumnya sudah ACC semua
                        if ($last_progres['progres_dosen1'] != 100 || $last_progres['progres_dosen2'] != 100) {
                            $is_upload_allowed = FALSE;
                            $block_message = 'Selesaikan revisi <b>BAB '.($next_bab-1).'</b> (ACC Penuh) sebelum lanjut upload BAB baru.';
                        }
                    }
                    ?>

                    <?php if ($is_upload_allowed): ?>
                        <div class="card card-success card-outline shadow">
                            <div class="card-header">
                                <h3 class="card-title font-weight-bold"><i class="fas fa-cloud-upload-alt mr-1"></i> Upload BAB <?php echo $next_bab; ?></h3>
                            </div>
                            <?php echo form_open_multipart('mahasiswa/upload_progres_bab'); ?>
                            <div class="card-body bg-light">
                                <input type="hidden" name="bab" value="<?php echo $next_bab; ?>">
                                
                                <div class="form-group mb-0">
                                    <label class="text-sm text-muted mb-2">File PDF (Maks. 5MB)</label>
                                    <div class="custom-file">
                                        <input type="file" class="custom-file-input" id="file_progres" name="file_progres" required accept=".pdf">
                                        <label class="custom-file-label" for="file_progres">Pilih file...</label>
                                    </div>
                                </div>
                            </div>
                            <div class="card-footer bg-white">
                                <button type="submit" class="btn btn-success btn-block font-weight-bold shadow-sm">
                                    <i class="fas fa-paper-plane mr-1"></i> KIRIM PROGRES
                                </button>
                            </div>
                            <?php echo form_close(); ?>
                        </div>
                    <?php else: ?>
                        <div class="callout callout-warning shadow-sm bg-white">
                            <h5 class="text-warning"><i class="icon fas fa-lock"></i> Terkunci!</h5>
                            <p class="text-sm"><?php echo $block_message; ?></p>
                        </div>
                    <?php endif; ?>

                </div>

                <div class="col-md-8">
                    <div class="card card-outline card-navy shadow-sm">
                        <div class="card-header border-0 bg-white">
                            <h3 class="card-title font-weight-bold text-dark">
                                <i class="fas fa-history mr-1 text-navy"></i> Riwayat Bimbingan
                            </h3>
                        </div>
                        
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover table-striped align-middle mb-0">
                                    <thead class="bg-light">
                                        <tr>
                                            <th class="text-center" style="width: 10%;">BAB</th>
                                            <th style="width: 25%;">Status Pembimbing</th>
                                            <th style="width: 45%;">Catatan / Revisi</th>
                                            <th class="text-center" style="width: 20%;">File & Tanggal</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (empty($progres)): ?>
                                            <tr>
                                                <td colspan="4" class="text-center py-5 text-muted">
                                                    <i class="far fa-folder-open fa-3x mb-3 text-gray"></i><br>
                                                    <span class="font-weight-bold">Belum ada data progres.</span><br>
                                                    <small>Silakan upload file Bab 1 Anda pada panel di sebelah kiri.</small>
                                                </td>
                                            </tr>
                                        <?php else: ?>
                                            <?php foreach ($progres as $p): ?>
                                            <tr>
                                                <td class="text-center align-top pt-3">
                                                    <div class="badge badge-info badge-pill px-3 py-2 shadow-sm" style="font-size: 1rem;">
                                                        <?php echo $p['bab']; ?>
                                                    </div>
                                                </td>

                                                <td class="align-top pt-3">
                                                    <div class="mb-2">
                                                        <small class="text-muted font-weight-bold d-block mb-1">Pembimbing 1</small>
                                                        <?php 
                                                        $badge1 = 'secondary'; $icon1 = 'clock'; 
                                                        if ($p['progres_dosen1'] == 100) { $badge1 = 'success'; $icon1 = 'check'; }
                                                        elseif ($p['progres_dosen1'] == 50) { $badge1 = 'warning'; $icon1 = 'exclamation'; }
                                                        elseif ($p['nilai_dosen1'] == 'Revisi') { $badge1 = 'danger'; $icon1 = 'times'; }
                                                        ?>
                                                        <span class="badge badge-<?php echo $badge1; ?> px-2 py-1">
                                                            <i class="fas fa-<?php echo $icon1; ?> mr-1"></i> <?php echo $p['nilai_dosen1'] ?: 'Menunggu'; ?>
                                                        </span>
                                                    </div>

                                                    <div>
                                                        <small class="text-muted font-weight-bold d-block mb-1">Pembimbing 2</small>
                                                        <?php 
                                                        $badge2 = 'secondary'; $icon2 = 'clock'; 
                                                        if ($p['progres_dosen2'] == 100) { $badge2 = 'success'; $icon2 = 'check'; }
                                                        elseif ($p['progres_dosen2'] == 50) { $badge2 = 'warning'; $icon2 = 'exclamation'; }
                                                        elseif ($p['nilai_dosen2'] == 'Revisi') { $badge2 = 'danger'; $icon2 = 'times'; }
                                                        ?>
                                                        <span class="badge badge-<?php echo $badge2; ?> px-2 py-1">
                                                            <i class="fas fa-<?php echo $icon2; ?> mr-1"></i> <?php echo $p['nilai_dosen2'] ?: 'Menunggu'; ?>
                                                        </span>
                                                    </div>
                                                </td>

                                                <td class="align-top pt-3">
                                                    <?php if($p['komentar_dosen1'] || $p['komentar_dosen2']): ?>
                                                        
                                                        <?php if($p['komentar_dosen1']): ?>
                                                            <div class="callout callout-info py-2 px-3 mb-2 text-sm bg-white shadow-sm border-left-info">
                                                                <strong class="text-primary d-block mb-1">P1:</strong> 
                                                                <?php echo $p['komentar_dosen1']; ?>
                                                            </div>
                                                        <?php endif; ?>

                                                        <?php if($p['komentar_dosen2']): ?>
                                                            <div class="callout callout-secondary py-2 px-3 mb-0 text-sm bg-white shadow-sm border-left-secondary">
                                                                <strong class="text-secondary d-block mb-1">P2:</strong> 
                                                                <?php echo $p['komentar_dosen2']; ?>
                                                            </div>
                                                        <?php endif; ?>

                                                    <?php else: ?>
                                                        <span class="text-muted font-italic text-sm">Belum ada catatan revisi.</span>
                                                    <?php endif; ?>
                                                </td>

                                                <td class="text-center align-top pt-3">
                                                    <div class="mb-2">
                                                        <a href="<?php echo base_url('uploads/progres/' . $p['file']); ?>" target="_blank" class="btn btn-outline-danger btn-sm btn-block shadow-sm">
                                                            <i class="fas fa-file-pdf mr-1"></i> Lihat File
                                                        </a>
                                                    </div>
                                                    <small class="text-muted d-block">
                                                        <i class="far fa-calendar-alt mr-1"></i> <?php echo date('d M Y', strtotime($p['created_at'])); ?>
                                                    </small>
                                                    <small class="text-muted">
                                                        <i class="far fa-clock mr-1"></i> <?php echo date('H:i', strtotime($p['created_at'])); ?>
                                                    </small>
                                                </td>
                                            </tr>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </section>
</div>

<style>
    .border-left-primary { border-left: 4px solid #007bff !important; }
    .border-left-info { border-left: 3px solid #17a2b8 !important; }
    .border-left-secondary { border-left: 3px solid #6c757d !important; }
    .callout { border-radius: 0.25rem; }
</style>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        const fileInput = document.querySelector('.custom-file-input');
        if(fileInput){
            fileInput.addEventListener('change', function (e) {
                var fileName = document.getElementById("file_progres").files[0].name;
                var nextSibling = e.target.nextElementSibling;
                nextSibling.innerText = fileName;
            });
        }
    });
</script>