<div class="content-wrapper">
    
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0 text-dark">Profil Saya</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="<?php echo base_url('dashboard'); ?>">Home</a></li>
                        <li class="breadcrumb-item active">Profil Dosen</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">
            <div class="row justify-content-center">
                
                <div class="col-md-4">
                    <div class="card card-primary card-outline shadow-sm">
                        <div class="card-body box-profile">
                            <div class="text-center">
                                <img class="profile-user-img img-fluid img-circle"
                                     src="<?php echo $user['foto'] ? base_url('uploads/profile/'.$user['foto']) : 'https://ui-avatars.com/api/?name='.urlencode($user['nama']); ?>"
                                     alt="User profile picture" style="width: 100px; height: 100px; object-fit: cover;">
                            </div>
                            <h3 class="profile-username text-center mt-3"><?php echo $user['nama']; ?></h3>
                            <p class="text-muted text-center mb-1">NIDK: <?php echo $user['nidk']; ?></p>
                            
                            <?php if(isset($user['prodi_dsn'])): ?>
                                <p class="text-muted text-center text-sm"><i class="fas fa-university mr-1"></i> <?php echo $user['prodi_dsn']; ?></p>
                            <?php endif; ?>

                            <hr>
                            
                            <div class="text-center">
                                <h6 class="text-muted text-sm font-weight-bold mb-2">TANDA TANGAN DIGITAL</h6>
                                <?php if($user['ttd']): ?>
                                    <img src="<?php echo base_url('uploads/ttd/'.$user['ttd']); ?>" class="img-fluid border p-2 bg-light" style="max-height: 80px;">
                                <?php else: ?>
                                    <span class="badge badge-warning">Belum upload TTD</span>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-8">
                    <div class="card shadow-sm">
                        <div class="card-header p-2 bg-white border-bottom-0">
                            <ul class="nav nav-pills">
                                <li class="nav-item"><a class="nav-link active font-weight-bold" href="#settings" data-toggle="tab">Edit Informasi</a></li>
                            </ul>
                        </div>
                        <div class="card-body">
                            
                            <?php if ($this->session->flashdata('pesan_sukses')): ?>
                                <div class="alert alert-success alert-dismissible fade show">
                                    <button type="button" class="close" data-dismiss="alert">&times;</button>
                                    <i class="fas fa-check mr-1"></i> <?php echo $this->session->flashdata('pesan_sukses'); ?>
                                </div>
                            <?php endif; ?>

                            <?php echo form_open_multipart('dosen/update_profil', ['class' => 'form-horizontal']); ?>
                                <div class="form-group row">
                                    <label class="col-sm-3 col-form-label">Nama Lengkap (Gelar)</label>
                                    <div class="col-sm-9">
                                        <input type="text" class="form-control" name="nama" value="<?php echo $user['nama']; ?>" required>
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label class="col-sm-3 col-form-label">Ganti Foto Profil</label>
                                    <div class="col-sm-9">
                                        <div class="custom-file">
                                            <input type="file" class="custom-file-input" id="customFileFoto" name="foto">
                                            <label class="custom-file-label" for="customFileFoto">Pilih file foto...</label>
                                        </div>
                                        <small class="text-muted mt-1 d-block">Format JPG/PNG, Max 2MB.</small>
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label class="col-sm-3 col-form-label">Upload TTD Digital</label>
                                    <div class="col-sm-9">
                                        <div class="custom-file">
                                            <input type="file" class="custom-file-input" id="customFileTTD" name="ttd">
                                            <label class="custom-file-label" for="customFileTTD">Pilih file TTD...</label>
                                        </div>
                                        <small class="text-muted mt-1 d-block">
                                            <i class="fas fa-info-circle mr-1"></i> File ini akan digunakan untuk ACC Digital pada lembar revisi. Format PNG Transparan disarankan.
                                        </small>
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

<script>
    document.addEventListener("DOMContentLoaded", function() {
        $('.custom-file-input').on('change', function() {
            let fileName = $(this).val().split('\\').pop();
            $(this).next('.custom-file-label').addClass("selected").html(fileName);
        });
    });
</script>