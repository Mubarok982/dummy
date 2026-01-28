<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0 text-dark">Pengajuan Judul Skripsi</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="<?php echo base_url('dashboard'); ?>">Home</a></li>
                        <li class="breadcrumb-item active">Pengajuan Judul</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">
            <div class="row justify-content-center">
                <div class="col-md-10">

                    <?php 
                    $is_exist = isset($skripsi) && $skripsi != NULL;
                    ?>

                    <?php if ($this->session->flashdata('pesan_sukses')): ?>
                        <div class="alert alert-success alert-dismissible fade show">
                            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                            <h5><i class="icon fas fa-check"></i> Berhasil!</h5>
                            <?php echo $this->session->flashdata('pesan_sukses'); ?>
                        </div>
                    <?php endif; ?>

                    <?php if ($this->session->flashdata('pesan_error')): ?>
                        <div class="alert alert-danger alert-dismissible fade show">
                            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                            <h5><i class="icon fas fa-ban"></i> Gagal!</h5>
                            <?php echo $this->session->flashdata('pesan_error'); ?>
                        </div>
                    <?php endif; ?>

                    <?php if (validation_errors()): ?>
                        <div class="alert alert-warning alert-dismissible fade show">
                            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                            <h5><i class="icon fas fa-exclamation-triangle"></i> Perhatian!</h5>
                            <?php echo validation_errors(); ?>
                        </div>
                    <?php endif; ?>

                    <div class="card card-primary card-outline shadow-sm">
                        <div class="card-header">
                            <h3 class="card-title font-weight-bold">
                                <i class="fas fa-file-signature mr-1"></i>
                                <?php echo $is_exist ? 'Edit Data Pengajuan' : 'Form Pengajuan Judul Baru'; ?>
                            </h3>
                        </div>
                        
                        <?php echo form_open('mahasiswa/submit_judul'); ?>
                        <div class="card-body">
                            
                            <div class="callout callout-info">
                                <p class="mb-0 text-sm">
                                    <i class="fas fa-info-circle mr-1"></i> 
                                    Silakan isi judul dan tema penelitian yang telah disetujui secara offline oleh calon pembimbing Anda.
                                </p>
                            </div>

                            <div class="form-group">
                                <label for="judul">Judul Skripsi <span class="text-danger">*</span></label>
                                <textarea name="judul" id="judul" class="form-control" rows="3" placeholder="Masukkan judul lengkap skripsi..." required><?php echo set_value('judul', $is_exist ? $skripsi['judul'] : ''); ?></textarea>
                            </div>

                            <div class="form-group">
                                <label for="tema">Tema Penelitian <span class="text-danger">*</span></label>
                                <select name="tema" id="tema" class="form-control" required>
                                    <option value="">-- Pilih Tema --</option>
                                    <option value="Software Engineering" <?php echo set_select('tema', 'Software Engineering', $is_exist && $skripsi['tema'] == 'Software Engineering'); ?>>Software Engineering</option>
                                    <option value="Networking" <?php echo set_select('tema', 'Networking', $is_exist && $skripsi['tema'] == 'Networking'); ?>>Networking</option>
                                    <option value="Artificial Intelligence" <?php echo set_select('tema', 'Artificial Intelligence', $is_exist && $skripsi['tema'] == 'Artificial Intelligence'); ?>>Artificial Intelligence</option>
                                    <option value="Data Science" <?php echo set_select('tema', 'Data Science', $is_exist && $skripsi['tema'] == 'Data Science'); ?>>Data Science</option>
                                    <option value="IoT" <?php echo set_select('tema', 'IoT', $is_exist && $skripsi['tema'] == 'IoT'); ?>>Internet of Things (IoT)</option>
                                </select>
                            </div>

                            <hr class="my-4">
                            
                            <h5 class="text-primary mb-3 font-weight-bold"><i class="fas fa-user-tie mr-1"></i> Usulan Pembimbing</h5>
                            <p class="text-muted text-sm mb-3">Pilih dosen yang telah menyetujui untuk membimbing Anda (Hasil bimbingan pra-proposal).</p>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Pembimbing 1 <span class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text bg-light"><i class="fas fa-user"></i></span>
                                            </div>
                                            <select name="pembimbing1" class="form-control" required>
                                                <option value="">-- Pilih Dosen --</option>
                                                <?php foreach ($dosen_list as $dsn): ?>
                                                    <option value="<?php echo $dsn['id']; ?>" <?php echo set_select('pembimbing1', $dsn['id'], $is_exist && $skripsi['pembimbing1'] == $dsn['id']); ?>>
                                                        <?php echo $dsn['nama']; ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Pembimbing 2 <span class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text bg-light"><i class="fas fa-user"></i></span>
                                            </div>
                                            <select name="pembimbing2" class="form-control" required>
                                                <option value="">-- Pilih Dosen --</option>
                                                <?php foreach ($dosen_list as $dsn): ?>
                                                    <option value="<?php echo $dsn['id']; ?>" <?php echo set_select('pembimbing2', $dsn['id'], $is_exist && $skripsi['pembimbing2'] == $dsn['id']); ?>>
                                                        <?php echo $dsn['nama']; ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>
                        
                        <div class="card-footer bg-white text-right border-top-0 pb-4 pr-4">
                            <a href="<?php echo base_url('dashboard'); ?>" class="btn btn-default mr-2">Batal</a>
                            <button type="submit" class="btn btn-primary px-4 shadow-sm">
                                <i class="fas fa-paper-plane mr-1"></i> 
                                <?php echo $is_exist ? 'Simpan Perubahan' : 'Ajukan Judul'; ?>
                            </button>
                        </div>
                        <?php echo form_close(); ?>
                    </div>

                </div>
            </div>
        </div>
    </section>
</div>