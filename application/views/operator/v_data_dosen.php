<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Data Dosen</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="<?= base_url('dashboard') ?>">Home</a></li>
                        <li class="breadcrumb-item active">Data Dosen</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">
            
            <div class="card card-outline card-primary collapsed-card shadow-sm">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-filter mr-1"></i> Filter Data</h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-plus"></i></button>
                    </div>
                </div>
                <div class="card-body">
                    <form action="<?= base_url('operator/data_dosen') ?>" method="get">
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
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Jabatan</label>
                                    <select class="form-control" name="jabatan">
                                        <option value="">-- Semua Jabatan --</option>
                                        <option value="kaprodi" <?= (isset($f_jabatan) && $f_jabatan == 'kaprodi') ? 'selected' : '' ?>>Kaprodi</option>
                                        <option value="dosen" <?= (isset($f_jabatan) && $f_jabatan == 'dosen') ? 'selected' : '' ?>>Dosen Pembimbing</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Cari Nama / NIDK</label>
                                    <input type="text" class="form-control" name="keyword" value="<?= isset($f_keyword) ? $f_keyword : '' ?>" placeholder="Masukkan Nama atau NIDK...">
                                </div>
                            </div>
                            <div class="col-md-2 d-flex align-items-end mb-3">
                                <button type="submit" class="btn btn-primary btn-block"><i class="fas fa-search mr-1"></i> Filter</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <div class="card shadow">
                <div class="card-header">
                    <h3 class="card-title">
                        Daftar Dosen & Kaprodi 
                        <span class="badge badge-info ml-2"><?= isset($total_rows) ? $total_rows : 0 ?> Data</span>
                    </h3>
                    <div class="card-tools">
                        <a href="<?= base_url('operator/tambah_akun') ?>" class="btn btn-primary btn-sm">
                            <i class="fas fa-plus"></i> Tambah Dosen
                        </a>
                    </div>
                </div>
                
                <div class="card-body table-responsive p-0">
                    <table class="table table-hover text-nowrap">
                        <thead class="bg-light">
                            <tr>
                                <th style="width: 10px">No</th>
                                <th>Nama Dosen</th> <th>NIDK</th>
                                <th>Program Studi</th>
                                <th>Telepon</th>
                                <th>Jabatan</th>
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if(empty($dosen)): ?>
                                <tr>
                                    <td colspan="7" class="text-center text-muted py-5">
                                        <i class="fas fa-search fa-3x mb-3 text-gray-300"></i><br>
                                        Data tidak ditemukan.
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php 
                                $no = isset($start) ? $start + 1 : 1; 
                                foreach($dosen as $d): 
                                ?>
                                <tr>
                                    <td><?= $no++ ?></td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <?php 
                                            // Cek Foto
                                            $foto_path = 'uploads/profile/'.$d['foto'];
                                            $src_foto = ($d['foto'] && file_exists(FCPATH . $foto_path)) 
                                                ? base_url($foto_path) 
                                                : 'https://ui-avatars.com/api/?name='.urlencode($d['nama']).'&size=64&background=random';
                                            ?>
                                            <img src="<?= $src_foto ?>" class="img-circle mr-2 border" style="width: 35px; height: 35px; object-fit: cover;">
                                            
                                            <div>
                                                <span class="font-weight-bold d-block"><?= $d['nama'] ?></span>
                                            </div>
                                            </div>
                                    </td>
                                    <td><?= $d['nidk'] ? $d['nidk'] : '-' ?></td>
                                    <td><?= $d['prodi'] ? $d['prodi'] : '-' ?></td>
                                    <td>
                                        <?php if($d['telepon']): ?>
                                            <a href="https://wa.me/<?= preg_replace('/^0/', '62', $d['telepon']) ?>" target="_blank" class="badge badge-success">
                                                <i class="fab fa-whatsapp"></i> <?= $d['telepon'] ?>
                                            </a>
                                        <?php else: ?>
                                            <span class="text-muted text-xs">Belum diisi</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if($d['is_kaprodi'] == 1): ?>
                                            <span class="badge badge-warning border border-warning text-dark p-1">
                                                <i class="fas fa-crown mr-1"></i> KAPRODI
                                            </span>
                                        <?php else: ?>
                                            <span class="badge badge-info p-1">Dosen Pembimbing</span>
                                        <?php endif; ?>
                                    </td>
                                  
                                    <td class="text-center">
                                        <a href="<?= base_url('operator/edit_akun/'.$d['id'].'?source=data_dosen') ?>" class="btn btn-sm btn-warning" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="<?= base_url('operator/hapus_akun/'.$d['id']) ?>" class="btn btn-sm btn-danger" onclick="return confirm('Yakin hapus data dosen ini? Data terkait skripsi mungkin akan error.')" title="Hapus">
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