<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0"><?php echo $title; ?></h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="<?php echo base_url('operator/dashboard'); ?>">Dashboard</a></li>
                        <li class="breadcrumb-item active"><?php echo $title; ?></li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <section class="content">
        <div class="container-fluid">
            
            <div class="alert alert-info shadow-sm">
                <div class="d-flex align-items-center">
                    <i class="fas fa-info-circle fa-2x mr-3"></i>
                    <div>
                        <h5 class="mb-1 font-weight-bold">Informasi Data Pendadaran</h5>
                        <p class="mb-0">
                            Data di bawah ini adalah mahasiswa yang progres bimbingan <b>BAB TERAKHIR </b> telah di ACC oleh kedua dosen pembimbing. 
                            Mahasiswa dalam daftar ini dinyatakan <b>Lulus Bimbingan</b> dan siap mendaftar <b>Ujian Pendadaran</b>.
                        </p>
                    </div>
                </div>
            </div>
            <div class="card card-outline card-primary">
                <div class="card-header">
                    <h3 class="card-title"> <i class="fas fa-user-graduate mr-1"></i> Daftar Calon Peserta Pendadaran</h3>
                </div>
                <div class="card-body">
                    <form method="GET" action="<?php echo base_url('operator/mahasiswa_siap_pendadaran'); ?>" class="mb-3">
                        <div class="row">
                            <div class="col-md-4">
                                <label>Pencarian</label>
                                <input type="text" name="keyword" class="form-control" placeholder="Cari nama/NPM/judul..." value="<?php echo $keyword; ?>">
                            </div>
                            <div class="col-md-3">
                                <label>Prodi</label>
                                <select name="prodi" class="form-control">
                                    <option value="all">Semua Prodi</option>
                                    <?php foreach ($list_prodi as $prodi_option): ?>
                                        <option value="<?php echo $prodi_option['prodi']; ?>" <?php echo ($prodi == $prodi_option['prodi']) ? 'selected' : ''; ?>><?php echo $prodi_option['prodi']; ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label>Angkatan</label>
                                <select name="angkatan" class="form-control">
                                    <option value="all">Semua Angkatan</option>
                                    <?php foreach ($list_angkatan as $angkatan_option): ?>
                                        <option value="<?php echo $angkatan_option['angkatan']; ?>" <?php echo ($angkatan == $angkatan_option['angkatan']) ? 'selected' : ''; ?>><?php echo $angkatan_option['angkatan']; ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label>&nbsp;</label>
                                <button type="submit" class="btn btn-primary btn-block"><i class="fas fa-search mr-1"></i> Filter</button>
                            </div>
                        </div>
                        <div class="row mt-2">
                            <div class="col-md-3">
                                <select name="sort_by" class="form-control form-control-sm">
                                    <option value="nama" <?php echo ($sort_by == 'nama') ? 'selected' : ''; ?>>Urut: Nama</option>
                                    <option value="npm" <?php echo ($sort_by == 'npm') ? 'selected' : ''; ?>>Urut: NPM</option>
                                    <option value="angkatan" <?php echo ($sort_by == 'angkatan') ? 'selected' : ''; ?>>Urut: Angkatan</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <select name="sort_order" class="form-control form-control-sm">
                                    <option value="asc" <?php echo ($sort_order == 'asc') ? 'selected' : ''; ?>>Ascending (A-Z)</option>
                                    <option value="desc" <?php echo ($sort_order == 'desc') ? 'selected' : ''; ?>>Descending (Z-A)</option>
                                </select>
                            </div>
                        </div>
                    </form>

                    <div class="table-responsive">
                        <table class="table table-bordered table-striped table-hover">
                            <thead class="bg-light">
                                <tr class="text-center">
                                    <th style="width: 5%">No</th>
                                    <th style="width: 10%">Foto</th>
                                    <th>Mahasiswa</th>
                                    <th>Judul Skripsi</th>
                                    <th>Pembimbing</th>
                                    <th>Tanggal ACC</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($mahasiswa)): ?>
                                    <?php $no = 1; foreach ($mahasiswa as $mhs): ?>
                                        <tr>
                                            <td class="text-center align-middle"><?php echo $no++; ?></td>
                                            <td class="text-center align-middle">
                                                <?php if (!empty($mhs['foto'])): ?>
                                                    <img src="<?php echo base_url('uploads/profile/' . $mhs['foto']); ?>" alt="Foto" class="img-thumbnail rounded-circle" style="width: 50px; height: 50px; object-fit: cover;">
                                                <?php else: ?>
                                                    <img src="<?php echo base_url('assets/image/default.png'); ?>" alt="Foto" class="img-thumbnail rounded-circle" style="width: 50px; height: 50px; object-fit: cover;">
                                                <?php endif; ?>
                                            </td>
                                            <td class="align-middle">
                                                <strong><?php echo $mhs['nama']; ?></strong><br>
                                                <small class="text-muted"><?php echo $mhs['npm']; ?></small><br>
                                                <span class="badge badge-info"><?php echo $mhs['prodi']; ?></span>
                                                <span class="badge badge-secondary"><?php echo $mhs['angkatan']; ?></span>
                                            </td>
                                            <td class="align-middle"><?php echo $mhs['judul']; ?></td>
                                            <td class="align-middle small">
                                                1. <?php echo $mhs['nama_p1']; ?><br>
                                                2. <?php echo $mhs['nama_p2']; ?>
                                            </td>
                                            <td class="text-center align-middle">
                                                <span class="badge badge-success px-2 py-1">
                                                    <i class="fas fa-check-circle mr-1"></i>
                                                    <?php echo date('d/m/Y', strtotime($mhs['tgl_acc'])); ?>
                                                </span>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="6" class="text-center py-5 text-muted">
                                            <i class="fas fa-user-graduate fa-3x mb-3 opacity-50"></i><br>
                                            Belum ada mahasiswa yang siap pendadaran.
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>