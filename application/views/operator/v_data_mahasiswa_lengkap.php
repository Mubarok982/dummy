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
                                        
                                        <?php if(isset($list_prodi) && !empty($list_prodi)): ?>
                                            <?php foreach($list_prodi as $p): ?>
                                                <option value="<?= $p['prodi'] ?>" <?= (isset($f_prodi) && $f_prodi == $p['prodi']) ? 'selected' : '' ?>>
                                                    <?= $p['prodi'] ?>
                                                </option>
                                            <?php endforeach; ?>
                                        <?php endif; ?>

                                    </select>
                                </div>
                            </div>
                            
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Kelengkapan Data</label>
                                    <select class="form-control" name="kelengkapan">
                                        <option value="">-- Semua Data --</option>
                                        <option value="lengkap" <?= (isset($f_kelengkapan) && $f_kelengkapan == 'lengkap') ? 'selected' : '' ?>>Lengkap (100%)</option>
                                        <option value="sebagian" <?= (isset($f_kelengkapan) && $f_kelengkapan == 'sebagian') ? 'selected' : '' ?>>Sebagian (50-99%)</option>
                                        <option value="belum" <?= (isset($f_kelengkapan) && $f_kelengkapan == 'belum') ? 'selected' : '' ?>>Belum Lengkap (<50%)</option>
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
                                <th>Telepon</th>
                                <th class="text-center">Kelengkapan Data</th>
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if(empty($mahasiswa)): ?>
                                <tr>
                                    <td colspan="8" class="text-center text-muted py-5">
                                        <i class="fas fa-search fa-3x mb-3 text-gray-300"></i><br>
                                        Data mahasiswa tidak ditemukan dengan filter tersebut.
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php 
                                // Penomoran lanjut sesuai paginasi
                                $no = isset($start_index) ? $start_index + 1 : 1; 
                                foreach($mahasiswa as $m): 
                                ?>
                                <tr>
                                    <td class="align-middle"><?= $no++ ?></td>
                                    <td class="align-middle">
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
                                    <td class="align-middle"><?= $m['npm'] ?></td>
                                    <td class="align-middle"><?= $m['prodi'] ?></td>
                                    <td class="text-center align-middle"><?= $m['angkatan'] ?></td>
                                    <td class="align-middle">
                                        <?php if($m['telepon']): ?>
                                            <a href="https://wa.me/<?= preg_replace('/^0/', '62', $m['telepon']) ?>" target="_blank" class="badge badge-success">
                                                <i class="fab fa-whatsapp"></i> <?= $m['telepon'] ?>
                                            </a>
                                        <?php else: ?>
                                            <span class="text-muted text-xs">Belum diisi</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-center align-middle">
                                        <?php
                                            $lengkap = 0;
                                            $total_checks = 4;
                                            
                                            // Check: Foto
                                            if ($m['foto']) $lengkap++;
                                            
                                            // Check: Telepon
                                            if ($m['telepon']) $lengkap++;
                                            
                                            // Check: Judul
                                            if ($m['judul']) $lengkap++;
                                            
                                            // Check: Pembimbing
                                            if ($m['p1'] && $m['p2']) $lengkap++;
                                            
                                            $persentase = ($lengkap / $total_checks) * 100;
                                            $badge_class = $persentase == 100 ? 'badge-success' : ($persentase >= 50 ? 'badge-warning' : 'badge-danger');
                                        ?>
                                        <div class="d-flex align-items-center justify-content-center gap-2">
                                            <span class="badge <?= $badge_class ?> px-2 py-1"><?= round($persentase) ?>%</span>
                                            <div class="dropdown">
                                                <button class="btn btn-xs btn-outline-secondary" type="button" data-toggle="dropdown" title="Detail">
                                                    <i class="fas fa-list"></i>
                                                </button>
                                                <div class="dropdown-menu dropdown-menu-right text-left" style="min-width: 200px;">
                                                    <h6 class="dropdown-header">Status Kelengkapan:</h6>
                                                    <a class="dropdown-item" href="#">
                                                        <?php if($m['foto']): ?>
                                                            <i class="fas fa-check text-success mr-2"></i>
                                                        <?php else: ?>
                                                            <i class="fas fa-times text-danger mr-2"></i>
                                                        <?php endif; ?>
                                                        Foto Profil
                                                    </a>
                                                    <a class="dropdown-item" href="#">
                                                        <?php if($m['telepon']): ?>
                                                            <i class="fas fa-check text-success mr-2"></i>
                                                        <?php else: ?>
                                                            <i class="fas fa-times text-danger mr-2"></i>
                                                        <?php endif; ?>
                                                        Nomor Telepon
                                                    </a>
                                                    <a class="dropdown-item" href="#">
                                                        <?php if($m['judul']): ?>
                                                            <i class="fas fa-check text-success mr-2"></i>
                                                        <?php else: ?>
                                                            <i class="fas fa-times text-danger mr-2"></i>
                                                        <?php endif; ?>
                                                        Judul Skripsi
                                                    </a>
                                                    <a class="dropdown-item" href="#">
                                                        <?php if($m['p1'] && $m['p2']): ?>
                                                            <i class="fas fa-check text-success mr-2"></i>
                                                        <?php else: ?>
                                                            <i class="fas fa-times text-danger mr-2"></i>
                                                        <?php endif; ?>
                                                        Pembimbing (1 & 2)
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="text-center align-middle">
                                        <a href="<?= base_url('operator/edit_profil_mahasiswa/' . $m['id_user']) ?>" class="btn btn-sm btn-warning" title="Edit Profil">
                                            <i class="fas fa-edit"></i>
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