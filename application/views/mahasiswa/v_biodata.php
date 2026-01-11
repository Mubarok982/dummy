<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0 text-dark">Biodata Mahasiswa</h1>
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
                                <img class="profile-user-img img-fluid img-circle"
                                     src="<?php echo $user['foto'] ? base_url('uploads/profile/'.$user['foto']) : 'https://ui-avatars.com/api/?name='.urlencode($user['nama']); ?>"
                                     alt="User profile picture" style="width: 100px; height: 100px; object-fit: cover;">
                            </div>

                            <h3 class="profile-username text-center mt-3"><?php echo $user['nama']; ?></h3>
                            <p class="text-muted text-center"><?php echo $user['npm']; ?></p>

                            <ul class="list-group list-group-unbordered mb-3">
                                <li class="list-group-item">
                                    <b>Prodi</b> <a class="float-right"><?php echo $user['prodi_mhs']; ?></a>
                                </li>
                                <li class="list-group-item">
                                    <b>Angkatan</b> <a class="float-right"><?php echo $user['angkatan']; ?></a>
                                </li>
                                <li class="list-group-item">
                                    <b>Status</b> <a class="float-right badge badge-success"><?php echo $user['status_mahasiswa']; ?></a>
                                </li>
                            </ul>
                        </div>
                    </div>
                    
                    <div class="card shadow-sm">
                        <div class="card-header">
                            <h3 class="card-title"><i class="fas fa-signature mr-1"></i> Tanda Tangan Digital</h3>
                        </div>
                        <div class="card-body text-center bg-light">
                            <?php if($user['ttd']): ?>
                                <img src="<?php echo base_url('uploads/ttd/'.$user['ttd']); ?>" class="img-fluid border bg-white p-2" style="max-height: 80px;">
                            <?php else: ?>
                                <p class="text-muted small mb-0 font-italic">Belum ada TTD yang diupload.</p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <div class="col-md-8">
                    <div class="card shadow-sm">
                        <div class="card-header p-2 bg-white border-bottom-0">
                            <ul class="nav nav-pills">
                                <li class="nav-item"><a class="nav-link active font-weight-bold" href="#settings" data-toggle="tab">Edit Biodata</a></li>
                            </ul>
                        </div>
                        <div class="card-body">
                            <div class="tab-content">
                                <div class="active tab-pane" id="settings">
                                    
                                    <?php if ($this->session->flashdata('pesan_sukses')): ?>
                                        <div class="alert alert-success alert-dismissible fade show">
                                            <button type="button" class="close" data-dismiss="alert">&times;</button>
                                            <i class="icon fas fa-check"></i> <?php echo $this->session->flashdata('pesan_sukses'); ?>
                                        </div>
                                    <?php endif; ?>

                                    <?php echo form_open_multipart('mahasiswa/update_biodata', ['class' => 'form-horizontal']); ?>
                                        
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
                                                <div class="input-group">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text"><i class="fab fa-whatsapp"></i></span>
                                                    </div>
                                                    <input type="text" class="form-control" name="telepon" value="<?php echo $user['telepon']; ?>" placeholder="08123xxxxx" required>
                                                </div>
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
                                        
                                        <hr class="my-4">

                                        <div class="form-group row">
                                            <label class="col-sm-3 col-form-label">Ganti Foto Profil</label>
                                            <div class="col-sm-9">
                                                <div class="custom-file">
                                                    <input type="file" class="custom-file-input" id="customFileFoto" name="foto">
                                                    <label class="custom-file-label" for="customFileFoto">Pilih file foto...</label>
                                                </div>
                                                <small class="text-muted d-block mt-1">Format JPG/PNG, Max 2MB.</small>
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <label class="col-sm-3 col-form-label">Upload TTD (Scan)</label>
                                            <div class="col-sm-9">
                                                <div class="custom-file">
                                                    <input type="file" class="custom-file-input" id="customFileTTD" name="ttd">
                                                    <label class="custom-file-label" for="customFileTTD">Pilih file TTD...</label>
                                                </div>
                                                <small class="text-muted d-block mt-1">Format PNG (Transparan lebih baik), Max 2MB. Digunakan untuk lembar pengesahan.</small>
                                            </div>
                                        </div>

                                        <div class="form-group row mt-4">
                                            <div class="offset-sm-3 col-sm-9">
                                                <button type="submit" class="btn btn-primary"><i class="fas fa-save mr-1"></i> Simpan Perubahan</button>
                                            </div>
                                        </div>
                                    <?php echo form_close(); ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        // Menampilkan nama file yang dipilih pada input file custom
        $('.custom-file-input').on('change', function() {
            let fileName = $(this).val().split('\\').pop();
            $(this).next('.custom-file-label').addClass("selected").html(fileName);
        });
    });
</script>