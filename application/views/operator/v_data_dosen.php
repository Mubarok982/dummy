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
            
            <div class="card card-outline card-primary shadow-sm">
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
                </div>
                
                <div class="card-body table-responsive p-0">
                    <table class="table table-hover text-nowrap">
                        <thead class="bg-light">
                            <tr>
                                <th style="width: 10px">No</th>
                                <th>Nama Dosen</th> <th>NIDK</th>
                                <th>Program Studi</th>
                                <th>Telepon</th>
                                <th class="text-center">Jabatan</th>
                                <th class="text-center">Kelengkapan Data</th>
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if(empty($dosen)): ?>
                                <tr>
                                    <td colspan="8" class="text-center text-muted py-5">
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
                                    <td class="text-center">
                                        <span class="badge badge-primary p-1">
                                            <i class="fas fa-chalkboard-user mr-1"></i> Dosen
                                        </span>
                                        <?php if($d['is_kaprodi'] == 1): ?>
                                            <span class="badge badge-warning text-dark p-1 ml-1">
                                                <i class="fas fa-chalkboard-user mr-1"></i> Kaprodi
                                            </span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-center">
                                        <?php
                                            $lengkap = 0;
                                            $total_checks = 2;
                                            
                                            // Check: Telepon
                                            if ($d['telepon']) $lengkap++;
                                            
                                            // Check: TTD
                                            if ($d['ttd']) $lengkap++;
                                            
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
                                                        <?php if($d['telepon']): ?>
                                                            <i class="fas fa-check text-success mr-2"></i>
                                                        <?php else: ?>
                                                            <i class="fas fa-times text-danger mr-2"></i>
                                                        <?php endif; ?>
                                                        Nomor Telepon
                                                    </a>
                                                    <a class="dropdown-item" href="#">
                                                        <?php if($d['ttd']): ?>
                                                            <i class="fas fa-check text-success mr-2"></i>
                                                        <?php else: ?>
                                                            <i class="fas fa-times text-danger mr-2"></i>
                                                        <?php endif; ?>
                                                        TTD Digital
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        <a href="<?= base_url('operator/edit_profil_dosen/'.$d['id']) ?>" class="btn btn-sm btn-warning" title="Edit Profil">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
                <div class="card-body table-responsive p-0">
                    </div>

                <div class="card-footer bg-white clearfix">
                    <ul class="pagination pagination-sm m-0 float-right">
                        <?= isset($pagination) ? $pagination : '' ?>
                    </ul>
                </div>
                </div>
            </div>

        </div>
    </section>
</div>