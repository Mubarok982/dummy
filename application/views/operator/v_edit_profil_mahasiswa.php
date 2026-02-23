<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0 text-dark">Edit Profil Mahasiswa</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="<?= base_url('dashboard') ?>">Home</a></li>
                        <li class="breadcrumb-item"><a href="<?= base_url('operator/data_mahasiswa') ?>">Data Mahasiswa</a></li>
                        <li class="breadcrumb-item active">Edit Profil</li>
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
                                    <img id="preview_foto" src="<?= $src_foto ?>" 
                                         alt="Foto Profil" 
                                         style="width: 100%; height: 100%; object-fit: cover; object-position: center;">
                                </div>
                            </div>

                            <h3 class="profile-username text-center mt-3"><?= $user['nama'] ?></h3>
                            <p class="text-muted text-center mb-1"><?= $user['npm'] ?></p>
                            
                            <hr>
                            
                            <div class="text-center text-sm text-muted mb-2">
                                <small><strong>ID Mahasiswa:</strong> <?= $user['id'] ?></small>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-8">
                    <div class="card shadow-sm">
                        <div class="card-header p-2 bg-white border-bottom-0">
                            <h3 class="card-title pl-2 font-weight-bold"><i class="fas fa-edit mr-2"></i>Edit Data Profil</h3>
                        </div>
                        <div class="card-body">
                            
                            <?php if ($this->session->flashdata('pesan_sukses')): ?>
                                <div class="alert alert-success alert-dismissible fade show">
                                    <button type="button" class="close" data-dismiss="alert">&times;</button>
                                    <i class="fas fa-check mr-1"></i> <?= $this->session->flashdata('pesan_sukses') ?>
                                </div>
                            <?php endif; ?>

                            <?php if ($this->session->flashdata('pesan_error')): ?>
                                <div class="alert alert-danger alert-dismissible fade show">
                                    <button type="button" class="close" data-dismiss="alert">&times;</button>
                                    <i class="fas fa-exclamation-circle mr-1"></i> <?= $this->session->flashdata('pesan_error') ?>
                                </div>
                            <?php endif; ?>

                            <?php if (validation_errors()): ?>
                                <div class="alert alert-warning alert-dismissible fade show">
                                    <button type="button" class="close" data-dismiss="alert">&times;</button>
                                    <i class="fas fa-exclamation-triangle mr-1"></i>
                                    <?= validation_errors() ?>
                                </div>
                            <?php endif; ?>

                            <?= form_open_multipart('operator/save_profil_mahasiswa/' . $user['id']) ?>
                                
                                <div class="form-group row">
                                    <label class="col-sm-3 col-form-label"><strong>Nama Lengkap</strong></label>
                                    <div class="col-sm-9">
                                        <input type="text" class="form-control" name="nama" value="<?= $user['nama'] ?>" required>
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label class="col-sm-3 col-form-label"><strong>Jenis Kelamin</strong></label>
                                    <div class="col-sm-9">
                                        <select class="form-control" name="jenis_kelamin">
                                            <option value="">-- Pilih --</option>
                                            <option value="Laki-laki" <?= ($user['jenis_kelamin'] == 'Laki-laki') ? 'selected' : '' ?>>Laki-laki</option>
                                            <option value="Perempuan" <?= ($user['jenis_kelamin'] == 'Perempuan') ? 'selected' : '' ?>>Perempuan</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label class="col-sm-3 col-form-label"><strong>Email</strong></label>
                                    <div class="col-sm-9">
                                        <input type="email" class="form-control" name="email" value="<?= $user['email'] ?>">
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label class="col-sm-3 col-form-label"><strong>No. Telepon / WhatsApp</strong></label>
                                    <div class="col-sm-9">
                                        <input type="text" class="form-control" name="telepon" value="<?= $user['telepon'] ?>" placeholder="Contoh: 082123456789">
                                        <small class="text-muted form-text">Gunakan format nomor dengan awalan 0 atau kode negara +62</small>
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label class="col-sm-3 col-form-label"><strong>Alamat</strong></label>
                                    <div class="col-sm-9">
                                        <textarea class="form-control" name="alamat" rows="3"><?= $user['alamat'] ?></textarea>
                                    </div>
                                </div>

                                <hr>

                                <div class="form-group row">
                                    <label class="col-sm-3 col-form-label"><strong>Foto Profil</strong></label>
                                    <div class="col-sm-9">
                                        <div class="custom-file">
                                            <input type="file" class="custom-file-input" id="foto" name="foto" accept="image/*" onchange="previewFoto(event)">
                                            <label class="custom-file-label" for="foto">Pilih File...</label>
                                        </div>
                                        <small class="text-muted form-text">Format: JPG, PNG, WEBP | Ukuran max: 5MB</small>
                                    </div>
                                </div>

                                <hr>

                                <div class="form-group row">
                                    <div class="col-sm-9 offset-sm-3">
                                        <a href="<?= base_url('operator/data_mahasiswa') ?>" class="btn btn-secondary">
                                            <i class="fas fa-arrow-left mr-1"></i> Kembali
                                        </a>
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fas fa-save mr-1"></i> Simpan Perubahan
                                        </button>
                                    </div>
                                </div>

                            <?= form_close() ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<script>
function previewFoto(event) {
    const file = event.target.files[0];
    const preview = document.getElementById('preview_foto');
    const reader = new FileReader();

    reader.onload = function() {
        preview.src = reader.result;
    };

    if (file) {
        reader.readAsDataURL(file);
    }
}
</script>
