<div class="container-fluid">
    <div class="row">
        
        <div class="col-md-4">
            <div class="card card-primary card-outline">
                <div class="card-body box-profile">
                    <div class="text-center">
                        <img class="profile-user-img img-fluid img-circle"
                             src="<?php echo $user['foto'] ? base_url('uploads/profile/'.$user['foto']) : 'https://ui-avatars.com/api/?name='.urlencode($user['nama']); ?>"
                             alt="User profile picture" style="width: 100px; height: 100px; object-fit: cover;">
                    </div>

                    <h3 class="profile-username text-center"><?php echo $user['nama']; ?></h3>
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
            
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Tanda Tangan Digital</h3>
                </div>
                <div class="card-body text-center">
                    <?php if($user['ttd']): ?>
                        <img src="<?php echo base_url('uploads/ttd/'.$user['ttd']); ?>" class="img-fluid border p-2" style="max-height: 100px;">
                    <?php else: ?>
                        <p class="text-muted small">Belum ada TTD. Silakan upload.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div class="col-md-8">
            <div class="card">
                <div class="card-header p-2">
                    <ul class="nav nav-pills">
                        <li class="nav-item"><a class="nav-link active" href="#settings" data-toggle="tab">Edit Biodata</a></li>
                    </ul>
                </div>
                <div class="card-body">
                    <div class="tab-content">
                        <div class="active tab-pane" id="settings">
                            
                            <?php if ($this->session->flashdata('pesan_sukses')): ?>
                                <div class="alert alert-success"><?php echo $this->session->flashdata('pesan_sukses'); ?></div>
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
                                    <label class="col-sm-3 col-form-label">No. WhatsApp <small class="text-danger">*Wajib</small></label>
                                    <div class="col-sm-9">
                                        <input type="text" class="form-control" name="telepon" value="<?php echo $user['telepon']; ?>" placeholder="08123xxxxx" required>
                                        <small class="text-muted">Digunakan untuk notifikasi bimbingan otomatis.</small>
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
                                        <textarea class="form-control" name="alamat"><?php echo $user['alamat']; ?></textarea>
                                    </div>
                                </div>
                                
                                <hr>

                                <div class="form-group row">
                                    <label class="col-sm-3 col-form-label">Ganti Foto Profil</label>
                                    <div class="col-sm-9">
                                        <input type="file" class="form-control border-0 p-0" name="foto">
                                        <small class="text-muted">Format JPG/PNG, Max 2MB.</small>
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label class="col-sm-3 col-form-label">Upload TTD (Scan)</label>
                                    <div class="col-sm-9">
                                        <input type="file" class="form-control border-0 p-0" name="ttd">
                                        <small class="text-muted">Format PNG (Transparan lebih baik), Max 2MB. Digunakan untuk lembar pengesahan.</small>
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
    </div>
</div>