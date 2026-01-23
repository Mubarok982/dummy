<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0 text-dark">Profil Dosen</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="<?php echo base_url('dashboard'); ?>">Home</a></li>
                        <li class="breadcrumb-item active">Profil</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">
            <div class="row">
                
                <div class="col-md-4">
                    <div class="card card-primary card-outline shadow-sm">
                        <div class="card-body box-profile">
                            <div class="text-center">
                                <?php 
                                $foto_path = FCPATH . 'uploads/profile/' . $user['foto'];
                                $src_foto = (file_exists($foto_path) && $user['foto']) 
                                    ? base_url('uploads/profile/'.$user['foto']) 
                                    : 'https://ui-avatars.com/api/?name='.urlencode($user['nama']).'&background=random&size=128';
                                ?>
                                <img class="profile-user-img img-fluid img-circle"
                                     src="<?php echo $src_foto; ?>"
                                     alt="User profile picture" 
                                     style="width: 120px; height: 120px; object-fit: cover; border: 3px solid #adb5bd;">
                            </div>

                            <h3 class="profile-username text-center mt-3"><?php echo $user['nama']; ?></h3>
                            <p class="text-muted text-center"><?php echo $user['nidk']; ?></p>

                            <ul class="list-group list-group-unbordered mb-3">
                                <li class="list-group-item">
                                    <b>Program Studi</b> <a class="float-right"><?php echo $user['prodi']; ?></a>
                                </li>
                                <li class="list-group-item">
                                    <b>Jabatan</b> <a class="float-right">
                                        <?php echo ($user['is_kaprodi'] == 1) ? '<span class="badge badge-warning">Kaprodi</span>' : 'Dosen Pembimbing'; ?>
                                    </a>
                                </li>
                            </ul>
                            
                            <hr>
                            <div class="text-center">
                                <h6 class="text-muted text-sm font-weight-bold mb-2">TANDA TANGAN DIGITAL</h6>
                                <?php 
                                $ttd_path = FCPATH . 'uploads/ttd/' . $user['ttd'];
                                if($user['ttd'] && file_exists($ttd_path)): 
                                ?>
                                    <img src="<?php echo base_url('uploads/ttd/'.$user['ttd']); ?>" class="img-fluid border p-2 bg-light" style="max-height: 80px;">
                                    <br><small class="text-muted text-xs">Tersimpan</small>
                                    
                                <?php else: ?>
                                    <span class="badge badge-secondary">Belum ada TTD</span>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-8">
                    <div class="card shadow-sm">
                        <div class="card-header p-2 bg-white border-bottom-0">
                            <h3 class="card-title pl-2 font-weight-bold">Edit Informasi</h3>
                        </div>
                        <div class="card-body">
                            
                            <?php if ($this->session->flashdata('pesan_sukses')): ?>
                                <div class="alert alert-success alert-dismissible fade show">
                                    <button type="button" class="close" data-dismiss="alert">&times;</button>
                                    <i class="fas fa-check mr-1"></i> <?php echo $this->session->flashdata('pesan_sukses'); ?>
                                </div>
                            <?php endif; ?>

                            <?php if ($this->session->flashdata('pesan_error')): ?>
                                <div class="alert alert-danger alert-dismissible fade show">
                                    <button type="button" class="close" data-dismiss="alert">&times;</button>
                                    <i class="fas fa-ban mr-1"></i> <?php echo $this->session->flashdata('pesan_error'); ?>
                                </div>
                            <?php endif; ?>

                            <?php echo form_open_multipart('dosen/update_profil', ['class' => 'form-horizontal', 'id' => 'formProfil']); ?>
                                
                                <div class="form-group row">
                                    <label class="col-sm-3 col-form-label">Nama Lengkap</label>
                                    <div class="col-sm-9">
                                        <input type="text" class="form-control" name="nama" value="<?php echo $user['nama']; ?>" required>
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label class="col-sm-3 col-form-label">NIDK / NIDN</label>
                                    <div class="col-sm-9">
                                        <input type="text" class="form-control" value="<?php echo $user['nidk']; ?>" readonly disabled>
                                        <small class="text-muted">NIDK tidak dapat diubah sendiri.</small>
                                    </div>
                                </div>

                                <hr>

                                <div class="form-group row">
                                    <label class="col-sm-3 col-form-label">Ganti Foto Profil</label>
                                    <div class="col-sm-9">
                                        <div class="custom-file">
                                            <input type="file" class="custom-file-input" id="customFileFoto" name="foto" accept="image/*">
                                            <label class="custom-file-label" for="customFileFoto">Pilih file foto...</label>
                                        </div>
                                        <small class="text-muted d-block mt-1">Format: JPG, PNG, WEBP. Maks: 5MB.</small>
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label class="col-sm-3 col-form-label">Update Tanda Tangan</label>
                                    <div class="col-sm-9">
                                        <div style="border: 2px dashed #ccc; background-color: #f9f9f9; border-radius: 5px; touch-action: none;">
                                            <canvas id="signature-pad" class="signature-pad" width="450" height="200" style="width: 100%;"></canvas>
                                        </div>
                                        
                                        <div class="mt-2 d-flex justify-content-between align-items-center">
                                            <small class="text-muted"><i class="fas fa-pen-alt mr-1"></i> Coret/gambar TTD di kotak atas.</small>
                                            <button type="button" class="btn btn-outline-danger btn-sm" id="clear-signature">
                                                <i class="fas fa-eraser mr-1"></i> Hapus Coretan
                                            </button>
                                        </div>

                                        <input type="hidden" name="ttd_base64" id="ttd_base64">
                                    </div>
                                </div>

                                <div class="form-group row mt-4">
                                    <div class="offset-sm-3 col-sm-9">
                                        <button type="submit" class="btn btn-primary px-4">
                                            <i class="fas fa-save mr-1"></i> Simpan Perubahan
                                        </button>
                                    </div>
                                </div>
                            <?php echo form_close(); ?>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </section>
</div>

<script src="https://cdn.jsdelivr.net/npm/signature_pad@4.0.0/dist/signature_pad.umd.min.js"></script>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        
        // 1. Custom File Input Label
        $('.custom-file-input').on('change', function() {
            let fileName = $(this).val().split('\\').pop();
            $(this).next('.custom-file-label').addClass("selected").html(fileName);
        });

        // 2. Setup Canvas Signature Pad
        var canvas = document.getElementById('signature-pad');
        var signaturePad = new SignaturePad(canvas, {
            backgroundColor: 'rgba(255, 255, 255, 0)', // Transparan
            penColor: 'rgb(0, 0, 0)' // Warna Tinta Hitam
        });

        // Tombol Clear
        document.getElementById('clear-signature').addEventListener('click', function () {
            signaturePad.clear();
        });

        // Resize Canvas function (Responsive)
        function resizeCanvas() {
            var ratio =  Math.max(window.devicePixelRatio || 1, 1);
            canvas.width = canvas.offsetWidth * ratio;
            canvas.height = canvas.offsetHeight * ratio;
            canvas.getContext("2d").scale(ratio, ratio);
            signaturePad.clear(); // Bersihkan ulang saat resize agar tidak pecah
        }
        window.addEventListener("resize", resizeCanvas);
        resizeCanvas();

        // Saat Form Submit -> Ambil data Canvas -> Masukkan ke Input Hidden
        document.getElementById('formProfil').addEventListener('submit', function(e) {
            if (!signaturePad.isEmpty()) {
                // Ambil data gambar (Format PNG)
                var dataURL = signaturePad.toDataURL('image/png');
                document.getElementById('ttd_base64').value = dataURL;
            }
        });
    });
</script>