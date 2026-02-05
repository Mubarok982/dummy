<div class="content-wrapper">

    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0 text-dark"><?php echo $title; ?></h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="<?php echo base_url('dashboard'); ?>">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="<?php echo base_url('dosen/monitoring_prodi'); ?>">Monitoring Prodi</a></li>
                        <li class="breadcrumb-item active"><?php echo $title; ?></li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">

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

            <div class="card shadow mb-4 card-outline card-primary">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-edit mr-1"></i> Edit Dosen Pembimbing
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="card border-left-info shadow h-100">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                                Mahasiswa
                                            </div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                                <?php echo htmlspecialchars($skripsi['nama_mahasiswa']); ?>
                                            </div>
                                            <div class="text-xs text-muted">
                                                NPM: <?php echo htmlspecialchars($skripsi['npm']); ?>
                                            </div>
                                        </div>
                                        <div class="col-auto">
                                            <i class="fas fa-user-graduate fa-2x text-gray-300"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card border-left-success shadow h-100">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                                Judul Skripsi
                                            </div>
                                            <div class="h6 mb-0 font-weight-bold text-gray-800">
                                                <?php echo htmlspecialchars($skripsi['judul']); ?>
                                            </div>
                                        </div>
                                        <div class="col-auto">
                                            <i class="fas fa-book fa-2x text-gray-300"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <form action="<?php echo base_url('dosen/update_dospem'); ?>" method="post" class="mt-4">
                        <input type="hidden" name="id_skripsi" value="<?php echo $skripsi['id']; ?>">

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="pembimbing1">Pembimbing 1</label>
                                    <select name="pembimbing1" id="pembimbing1" class="form-control" required>
                                        <option value="">Pilih Pembimbing 1</option>
                                        <?php foreach ($dosen_list as $dosen): ?>
                                            <option value="<?php echo $dosen['id']; ?>" <?php echo ($dosen['id'] == $skripsi['pembimbing1']) ? 'selected' : ''; ?>>
                                                <?php echo htmlspecialchars($dosen['nama']); ?> (<?php echo htmlspecialchars($dosen['nidk']); ?>)
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="pembimbing2">Pembimbing 2</label>
                                    <select name="pembimbing2" id="pembimbing2" class="form-control" required>
                                        <option value="">Pilih Pembimbing 2</option>
                                        <?php foreach ($dosen_list as $dosen): ?>
                                            <option value="<?php echo $dosen['id']; ?>" <?php echo ($dosen['id'] == $skripsi['pembimbing2']) ? 'selected' : ''; ?>>
                                                <?php echo htmlspecialchars($dosen['nama']); ?> (<?php echo htmlspecialchars($dosen['nidk']); ?>)
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save mr-1"></i> Simpan Perubahan
                            </button>
                            <a href="<?php echo base_url('dosen/monitoring_prodi'); ?>" class="btn btn-secondary">
                                <i class="fas fa-arrow-left mr-1"></i> Kembali
                            </a>
                        </div>
                    </form>
                </div>
            </div>

        </div>
    </section>
</div>
