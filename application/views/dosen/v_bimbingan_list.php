<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0 text-dark"><?php echo $title; ?></h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="<?php echo base_url('dosen/dashboard'); ?>">Home</a></li>
                        <li class="breadcrumb-item active">Bimbingan</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Daftar Mahasiswa Bimbingan</h3>
                </div>
                <div class="card-body">
                    <!-- Filter Form -->
                    <form method="GET" action="<?php echo base_url('dosen/bimbingan_list'); ?>" class="mb-3">
                        <div class="row">
                            <div class="col-md-4">
                                <input type="text" name="keyword" class="form-control" placeholder="Cari nama/NPM/judul..." value="<?php echo $keyword; ?>">
                            </div>
                            <div class="col-md-3">
                                <select name="prodi" class="form-control">
                                    <option value="all">Semua Prodi</option>
                                    <?php foreach ($list_prodi as $prodi_option): ?>
                                        <option value="<?php echo $prodi_option['prodi']; ?>" <?php echo ($prodi == $prodi_option['prodi']) ? 'selected' : ''; ?>><?php echo $prodi_option['prodi']; ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <!-- Angkatan filter removed as requested -->
                            <div class="col-md-2">
                                <button type="submit" class="btn btn-primary btn-block"><i class="fas fa-search"></i> Filter</button>
                            </div>
                        </div>
                        <!-- Top sort controls removed; use header-click sorting -->
                    </form>

                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th class="sortable" data-sort="npm">NPM</th>
                                    <th class="sortable" data-sort="nama_mhs">Nama</th>
                                    <th class="sortable" data-sort="prodi">Prodi</th>
                                    <th class="sortable" data-sort="angkatan">Angkatan</th>
                                    <th class="sortable" data-sort="judul">Judul Skripsi</th>
                                    <th class="sortable" data-sort="nama_p1">Pembimbing 1</th>
                                    <th class="sortable" data-sort="nama_p2">Pembimbing 2</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($bimbingan)): ?>
                                    <?php $no = 1; foreach ($bimbingan as $mhs): ?>
                                        <tr>
                                            <td><?php echo $no++; ?></td>
                                            <td><?php echo $mhs['npm']; ?></td>
                                            <td><?php echo $mhs['nama_mhs']; ?></td>
                                            <td><?php echo $mhs['prodi']; ?></td>
                                            <td><?php echo $mhs['angkatan']; ?></td>
                                            <td><?php echo $mhs['judul']; ?></td>
                                            <td><?php echo $mhs['nama_p1']; ?></td>
                                            <td><?php echo $mhs['nama_p2']; ?></td>
                                            <td>
                                                <a href="<?php echo base_url('dosen/progres_detail/' . $mhs['id_skripsi']); ?>" class="btn btn-primary btn-sm">Detail Progres</a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="10" class="text-center">Belum ada mahasiswa bimbingan.</td>
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
