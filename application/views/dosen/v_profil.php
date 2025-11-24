<div class="container-fluid">
    <div class="row justify-content-center">
        
        <div class="col-md-4">
            <div class="card card-primary card-outline">
                <div class="card-body box-profile">
                    <div class="text-center">
                        <img class="profile-user-img img-fluid img-circle"
                             src="<?php echo $user['foto'] ? base_url('uploads/profile/'.$user['foto']) : 'https://ui-avatars.com/api/?name='.urlencode($user['nama']); ?>"
                             alt="User profile picture" style="width: 100px; height: 100px; object-fit: cover;">
                    </div>
                    <h3 class="profile-username text-center"><?php echo $user['nama']; ?></h3>
                    <p class="text-muted text-center">NIDK: <?php echo $user['nidk']; ?></p>
                    <p class="text-muted text-center text-sm"><?php echo $user['prodi_dsn']; ?></p>
                </div>
            </div>
        </div>

        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Edit Informasi</h3>
                </div>
                <div class="card-body">
                    <?php if ($this->session->flashdata('pesan_sukses')): ?>
                        <div class="alert alert-success"><?php echo $this->session->flashdata('pesan_sukses'); ?></div>
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
                                <input type="file" class="form-control border-0 p-0" name="foto">
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-sm-3 col-form-label">Upload TTD Digital</label>
                            <div class="col-sm-9">
                                <input type="file" class="form-control border-0 p-0" name="ttd">
                                <small class="text-muted">File ini akan digunakan untuk ACC Digital pada lembar revisi.</small>
                                <?php if($user['ttd']): ?>
                                    <br><img src="<?php echo base_url('uploads/ttd/'.$user['ttd']); ?>" height="50" class="mt-2 border">
                                <?php endif; ?>
                            </div>
                        </div>

                        <div class="form-group row">
                            <div class="offset-sm-3 col-sm-9">
                                <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                            </div>
                        </div>
                    <?php echo form_close(); ?>
                </div>
            </div>
        </div>

    </div>
</div>