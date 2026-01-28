<div class="content-wrapper">

    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0 text-dark">Pengaturan Kaprodi</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="<?php echo base_url('dashboard'); ?>">Home</a></li>
                        <li class="breadcrumb-item"><a href="<?php echo base_url('operator/manajemen_akun'); ?>">Manajemen Akun</a></li>
                        <li class="breadcrumb-item active">Pengaturan Kaprodi</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">

            <?php if ($this->session->flashdata('pesan_sukses')): ?>
                <div class="alert alert-success alert-dismissible fade show shadow-sm py-2">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                    <i class="icon fas fa-check mr-1"></i> <?php echo $this->session->flashdata('pesan_sukses'); ?>
                </div>
            <?php endif; ?>

            <?php if ($this->session->flashdata('pesan_error')): ?>
                <div class="alert alert-danger alert-dismissible fade show shadow-sm py-2">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                    <i class="icon fas fa-exclamation-triangle mr-1"></i> <?php echo $this->session->flashdata('pesan_error'); ?>
                </div>
            <?php endif; ?>

            <div class="row">
                <?php foreach ($prodi_list as $prodi): ?>
                    <div class="col-md-6 col-lg-4 mb-4">
                        <div class="card shadow-sm">
                            <div class="card-header bg-primary text-white">
                                <h5 class="card-title mb-0">
                                    <i class="fas fa-university mr-2"></i><?php echo $prodi['prodi']; ?>
                                </h5>
                            </div>
                            <div class="card-body">
                                <form action="<?php echo base_url('operator/simpan_kaprodi'); ?>" method="POST">
                                    <input type="hidden" name="prodi" value="<?php echo $prodi['prodi']; ?>">

                                    <div class="form-group">
                                        <label for="kaprodi_<?php echo str_replace(' ', '_', $prodi['prodi']); ?>">Pilih Kaprodi:</label>
                                        <select name="kaprodi" id="kaprodi_<?php echo str_replace(' ', '_', $prodi['prodi']); ?>" class="form-control" required>
                                            <option value="">-- Pilih Dosen --</option>
                                            <?php foreach ($dosen_per_prodi[$prodi['prodi']] as $dosen): ?>
                                                <option value="<?php echo $dosen['id']; ?>" <?php echo ($dosen['is_kaprodi'] == 1) ? 'selected' : ''; ?>>
                                                    <?php echo $dosen['nama']; ?> (NIDK: <?php echo $dosen['nidk']; ?>)
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>

                                    <button type="submit" class="btn btn-success btn-block">
                                        <i class="fas fa-save mr-1"></i> Simpan Kaprodi
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <?php if (empty($prodi_list)): ?>
                <div class="text-center py-5">
                    <i class="fas fa-info-circle fa-3x text-muted mb-3"></i>
                    <h4 class="text-muted">Tidak ada program studi yang ditemukan</h4>
                    <p class="text-muted">Pastikan ada dosen yang terdaftar dengan program studi.</p>
                </div>
            <?php endif; ?>

        </div>
    </section>
</div>
