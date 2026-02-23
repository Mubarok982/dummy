<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0 text-dark">Biodata Saya</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="<?php echo base_url('dashboard'); ?>">Home</a></li>
                        <li class="breadcrumb-item active">Biodata</li>
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
                                <div style="width: 120px; height: 120px; margin: 0 auto; border-radius: 50%; overflow: hidden; border: 3px solid #adb5bd;">
                                    <img src="<?php echo $src_foto; ?>" 
                                         alt="Foto Profil" 
                                         style="width: 100%; height: 100%; object-fit: cover; object-position: center;">
                                </div>
                            </div>

                            <h3 class="profile-username text-center mt-3"><?php echo $user['nama']; ?></h3>
                            <p class="text-muted text-center mb-1"><?php echo $user['npm']; ?></p>
                            
                            <?php if(isset($user['prodi_mhs'])): ?>
                                <p class="text-muted text-center text-sm">
                                    <i class="fas fa-graduation-cap mr-1"></i> <?php echo $user['prodi_mhs']; ?>
                                    <br>Angkatan <?php echo $user['angkatan']; ?>
                                </p>
                            <?php endif; ?>

                            <hr>
                            
                            <div class="text-center">
                                <h6 class="text-muted text-sm font-weight-bold mb-2">TANDA TANGAN SAYA</h6>
                                <?php 
                                $ttd_path = FCPATH . 'uploads/ttd/' . $user['ttd'];
                                if($user['ttd'] && file_exists($ttd_path)): 
                                ?>
                                    <img src="<?php echo base_url('uploads/ttd/'.$user['ttd']); ?>" class="img-fluid border p-2 bg-light" style="max-height: 80px;">
                                    <br>
                                    <small class="text-muted font-italic">Tersimpan</small>
                                <?php else: ?>
                                    <span class="badge badge-warning">Belum ada TTD</span>
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

                            <?php echo form_open_multipart('mahasiswa/update_biodata', ['class' => 'form-horizontal', 'id' => 'formBiodata']); ?>
                                
                                <div class="form-group row">
                                    <label class="col-sm-3 col-form-label">Nama Lengkap</label>
                                    <div class="col-sm-9">
                                        <input type="text" class="form-control" name="nama" value="<?php echo $user['nama']; ?>" required>
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label class="col-sm-3 col-form-label">Jenis Kelamin</label>
                                    <div class="col-sm-9">
                                        <select class="form-control" name="jenis_kelamin">
                                            <option value="">- Pilih -</option>
                                            <option value="Laki-laki" <?php echo ($user['jenis_kelamin'] == 'Laki-laki') ? 'selected' : ''; ?>>Laki-laki</option>
                                            <option value="Perempuan" <?php echo ($user['jenis_kelamin'] == 'Perempuan') ? 'selected' : ''; ?>>Perempuan</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label class="col-sm-3 col-form-label">Tempat, Tgl Lahir</label>
                                    <div class="col-sm-9">
                                        <input type="text" class="form-control" name="tempat_tgl_lahir" value="<?php echo $user['tempat_tgl_lahir']; ?>" placeholder="Contoh: Jakarta, 17 Agustus 2000">
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label class="col-sm-3 col-form-label">No. WhatsApp <span class="text-danger">*</span></label>
                                    <div class="col-sm-9">
                                        <input type="number" class="form-control" name="telepon" value="<?php echo $user['telepon']; ?>" required placeholder="08xxxxxxxxxx">
                                        <small class="text-muted">Wajib diisi aktif untuk notifikasi bimbingan.</small>
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label class="col-sm-3 col-form-label">Email</label>
                                    <div class="col-sm-9">
                                        <input type="email" class="form-control" name="email" value="<?php echo $user['email']; ?>">
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label class="col-sm-3 col-form-label">Alamat</label>
                                    <div class="col-sm-9">
                                        <textarea class="form-control" name="alamat" rows="2"><?php echo $user['alamat']; ?></textarea>
                                    </div>
                                </div>

                                <hr>

                                <div class="form-group row">
                                    <label class="col-sm-3 col-form-label">Ganti Foto Profil</label>
                                    <div class="col-sm-9">
                                        <div class="custom-file">
                                            <input type="file" class="custom-file-input" id="customFileFoto" name="foto" accept="image/x-png,image/gif,image/jpeg,image/webp">
                                            <label class="custom-file-label" for="customFileFoto">Pilih file foto...</label>
                                        </div>
                                        <small class="text-muted mt-1 d-block">
                                            <i class="fas fa-info-circle mr-1"></i> Format: JPG, PNG, WEBP. Maksimal: <b>5 MB</b>.
                                        </small>
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label class="col-sm-3 col-form-label">Buat Tanda Tangan</label>
                                    <div class="col-sm-9">
                                        <div style="border: 2px dashed #ccc; background-color: #f9f9f9; border-radius: 5px; touch-action: none;">
                                            <canvas id="signature-pad" class="signature-pad" width="450" height="200" style="width: 100%;"></canvas>
                                        </div>
                                        
                                        <div class="mt-2 d-flex justify-content-between align-items-center">
                                            <small class="text-muted"><i class="fas fa-pen-alt mr-1"></i> Silakan coret/gambar tanda tangan di kotak atas.</small>
                                            <button type="button" class="btn btn-outline-danger btn-sm" id="clear-signature">
                                                <i class="fas fa-eraser mr-1"></i> Hapus Coretan
                                            </button>
                                        </div>

                                        <input type="hidden" name="ttd_base64" id="ttd_base64">
                                    </div>
                                </div>

                                <div class="form-group row mt-4">
                                    <div class="offset-sm-3 col-sm-9">
                                        <button type="submit" class="btn btn-primary px-4 btn-block-xs">
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
        
        // 1. Logic Custom File Input (Foto)
        $('.custom-file-input').on('change', function() {
            let fileName = $(this).val().split('\\').pop();
            $(this).next('.custom-file-label').addClass("selected").html(fileName);
        });

        // 2. Logic Signature Pad (Tanda Tangan)
        var canvas = document.getElementById('signature-pad');
        var signaturePad = new SignaturePad(canvas, {
            backgroundColor: 'rgba(255, 255, 255, 0)', // Transparan
            penColor: 'rgb(0, 0, 0)' // Warna tinta hitam
        });

        // Tombol Hapus Coretan
        document.getElementById('clear-signature').addEventListener('click', function () {
            signaturePad.clear();
        });

        // Resize Canvas agar responsif (penting untuk mobile)
        function resizeCanvas() {
            var ratio =  Math.max(window.devicePixelRatio || 1, 1);
            canvas.width = canvas.offsetWidth * ratio;
            canvas.height = canvas.offsetHeight * ratio;
            canvas.getContext("2d").scale(ratio, ratio);
            signaturePad.clear(); // Bersihkan ulang saat resize agar tinta tidak pecah
        }
        window.addEventListener("resize", resizeCanvas);
        resizeCanvas();

        // Saat Form Submit -> Konversi Canvas ke Base64 -> Masukkan ke Hidden Input
        document.getElementById('formBiodata').addEventListener('submit', function(e) {
            if (!signaturePad.isEmpty()) {
                var dataURL = signaturePad.toDataURL('image/png');
                document.getElementById('ttd_base64').value = dataURL;
            }
        });
    });
    
</script>