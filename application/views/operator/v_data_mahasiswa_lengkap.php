<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Data Mahasiswa</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="<?= base_url('dashboard') ?>">Home</a></li>
                        <li class="breadcrumb-item active">Data Mahasiswa</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">
            
            <div class="card card-outline card-primary shadow-sm">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-filter mr-1"></i> Filter Data</h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-plus"></i></button>
                    </div>
                </div>
                <div class="card-body">
                    <form action="<?= base_url('operator/data_mahasiswa') ?>" method="get">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Program Studi</label>
                                    <select class="form-control" name="prodi">
                                        <option value="">-- Semua Prodi --</option>
                                        <option value="Teknik Informatika S1" <?= (isset($f_prodi) && $f_prodi == 'Teknik Informatika S1') ? 'selected' : '' ?>>Teknik Informatika S1</option>
                                        <option value="Sistem Informasi S1" <?= (isset($f_prodi) && $f_prodi == 'Sistem Informasi S1') ? 'selected' : '' ?>>Sistem Informasi S1</option>
                                        <option value="Teknologi Informasi S1" <?= (isset($f_prodi) && $f_prodi == 'Teknologi Informasi S1') ? 'selected' : '' ?>>Teknologi Informasi S1</option>
                                    </select>
                                </div>
                            </div>
                            
                            <!-- Angkatan filter removed as requested -->

                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Status Skripsi</label>
                                    <select class="form-control" name="status">
                                        <option value="">-- Semua Status --</option>
                                        <option value="1" <?= (isset($f_status) && $f_status == '1') ? 'selected' : '' ?>>Sedang Skripsi</option>
                                        <option value="0" <?= (isset($f_status) && $f_status == '0') ? 'selected' : '' ?>>Belum Skripsi</option>
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Cari Nama / NPM</label>
                                    <input type="text" class="form-control" name="keyword" value="<?= isset($f_keyword) ? $f_keyword : '' ?>" placeholder="Masukkan Nama atau NPM...">
                                </div>
                            </div>

                            <div class="col-md-1 d-flex align-items-end mb-3">
                                <button type="submit" class="btn btn-primary btn-block"><i class="fas fa-search"></i></button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <div class="card shadow">
                <div class="card-header">
                    <h3 class="card-title">
                        Daftar Mahasiswa 
                        <span class="badge badge-info ml-2"><?= isset($total_rows) ? $total_rows : 0 ?> Data</span>
                    </h3>
                    <div class="card-tools">
                        <a href="<?= base_url('operator/tambah_akun?tipe=mahasiswa') ?>" class="btn btn-primary btn-sm">
                            <i class="fas fa-plus"></i> Tambah Mahasiswa
                        </a>
                    </div>
                </div>
                
                <div class="card-body table-responsive p-0">
                    <table class="table table-hover text-nowrap">
                        <thead class="bg-light">
                            <tr>
                                <th style="width: 10px">No</th>
                                <th>Nama Mahasiswa</th> 
                                <th>NPM</th>
                                <th>Program Studi</th>
                                <th class="text-center">Angkatan</th>
                                <th class="text-center">Status</th>
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if(empty($mahasiswa)): ?>
                                <tr>
                                    <td colspan="7" class="text-center text-muted py-5">
                                        <i class="fas fa-search fa-3x mb-3 text-gray-300"></i><br>
                                        Data mahasiswa tidak ditemukan dengan filter tersebut.
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php 
                                $no = 1;
                                foreach($mahasiswa as $m): 
                                ?>
                                <tr>
                                    <td><?= $no++ ?></td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <?php 
                                            $foto_path = 'uploads/profile/' . $m['foto'];
                                            $src_foto = ($m['foto'] && file_exists(FCPATH . $foto_path)) 
                                                ? base_url($foto_path) 
                                                : 'https://ui-avatars.com/api/?name='.urlencode($m['nama']).'&size=64&background=random';
                                            ?>
                                            <img src="<?= $src_foto ?>" class="img-circle mr-2 border" style="width: 35px; height: 35px; object-fit: cover;">
                                            <span class="font-weight-bold"><?= $m['nama'] ?></span>
                                        </div>
                                    </td>
                                    <td><?= $m['npm'] ?></td>
                                    <td><?= $m['prodi'] ?></td>
                                    <td class="text-center"><?= $m['angkatan'] ?></td>
                                    <td class="text-center">
                                        <?php if ($m['is_skripsi'] == 1): ?>
                                            <span class="badge badge-success px-2 py-1"><i class="fas fa-check mr-1"></i> Skripsi</span>
                                        <?php else: ?>
                                            <span class="badge badge-secondary px-2 py-1">Belum</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-center">
                                        <a href="<?= base_url('operator/edit_akun/' . $m['id_user'] . '?source=data_mahasiswa') ?>" class="btn btn-sm btn-warning" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="<?= base_url('operator/hapus_akun/' . $m['id_user']) ?>" class="btn btn-sm btn-danger" onclick="return confirm('Yakin hapus data mahasiswa ini?')" title="Hapus">
                                            <i class="fas fa-trash"></i>
                                        </a>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <div class="card-footer bg-white clearfix">
                    <?= isset($pagination) ? $pagination : '' ?>
                </div>
            </div>

        </div>
    </section>
</div>