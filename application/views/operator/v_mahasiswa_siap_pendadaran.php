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
            
            <div class="alert alert-info">
                <i class="fas fa-info-circle mr-1"></i> Data di bawah ini adalah mahasiswa yang progres bimbingan <b>BAB TERAKHIR</b> telah di ACC oleh kedua dosen pembimbing. Mereka dinyatakan <b>Lulus Bimbingan</b> dan siap mendaftar <b>Ujian Pendadaran</b>.
            </div>

            <!-- Filter Form similar to siap sempro -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Filter Data</h3>
                </div>
                <div class="card-body">
                    <form method="GET" action="<?php echo base_url('operator/mahasiswa_siap_pendadaran'); ?>" class="mb-3">
                        <div class="row">
                            <div class="col-md-4">
                                <input type="text" name="keyword" class="form-control" placeholder="Cari nama/NPM/tema/judul..." value="<?php echo $keyword; ?>">
                            </div>
                            <div class="col-md-3">
                                <select name="prodi" class="form-control">
                                    <option value="all">Semua Prodi</option>
                                    <?php foreach ($list_prodi as $prodi_option): ?>
                                        <option value="<?php echo $prodi_option['prodi']; ?>" <?php echo ($prodi == $prodi_option['prodi']) ? 'selected' : ''; ?>><?php echo $prodi_option['prodi']; ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <!-- Angkatan filter removed as per request -->
                            <div class="col-md-2">
                                <button type="submit" class="btn btn-primary btn-block"><i class="fas fa-search"></i> Filter</button>
                            </div>
                        </div>
                        <div class="row mt-2">
                            <?php if($keyword || ($prodi && $prodi != 'all')): ?>
                            <div class="col-md-2">
                                <a href="<?php echo base_url('operator/mahasiswa_siap_pendadaran'); ?>" class="btn btn-secondary btn-block"><i class="fas fa-undo"></i> Reset</a>
                            </div>
                            <?php endif; ?>
                        </div>
                    </form>
                </div>
            </div>

            <div class="card card-success card-outline shadow-sm">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-user-graduate mr-1"></i> Daftar Calon Peserta Pendadaran
                    </h3>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped table-hover">
                            <thead>
                                <tr class="text-center">
                                    <th width="5%">No</th>
                                    <th class="sortable" data-sort="nama">Mahasiswa</th>
                                    <th class="sortable" data-sort="prodi" width="10%">Prodi</th>
                                    <th class="sortable" data-sort="judul" width="30%">Judul Skripsi</th>
                                    <th class="sortable" data-sort="nama_p1">Pembimbing</th>
                                    <th class="sortable" data-sort="tgl_daftar">Tanggal Daftar</th>
                                    <th class="sortable" data-sort="status_ujian">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($mahasiswa)): ?>
                                    <?php $no = isset($start_index) ? $start_index + 1 : 1; foreach ($mahasiswa as $mhs): ?>
                                    <tr>
                                        <td class="text-center">
                                            <?php echo $no++; ?>
                                            <br>
                                            <small class="text-muted">ID: <?php echo isset($mhs['id_skripsi']) ? $mhs['id_skripsi'] : '-'; ?></small>
                                        </td>
                                        <td>
                                            <div class="user-block">
                                                <span class="username">
                                                    <a href="#"><?php echo $mhs['nama']; ?></a>
                                                </span>
                                                <span class="description"><?php echo $mhs['npm']; ?> - <?php echo $mhs['angkatan']; ?></span>
                                            </div>
                                        </td>
                                        <td>
                                            <small><?php echo $mhs['prodi']; ?></small>
                                        </td>
                                        <td>
                                            <small><?php echo $mhs['judul']; ?></small>
                                        </td>
                                        <td class="text-sm">
                                            <b>P1:</b> <?php echo $mhs['nama_p1']; ?><br>
                                            <b>P2:</b> <?php echo $mhs['nama_p2']; ?>
                                        </td>
                                        <td class="text-center">
                                            <?php if (!empty($mhs['tgl_daftar'])): ?>
                                                <span class="badge badge-success">
                                                    <i class="far fa-calendar-check mr-1"></i>
                                                    <?php echo date('d M Y', strtotime($mhs['tgl_daftar'])); ?>
                                                </span>
                                            <?php else: ?>
                                                -
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-center">
                                            <?php 
                                                if ((isset($mhs['status_acc_kaprodi']) && strtolower($mhs['status_acc_kaprodi']) == 'ditolak') || (isset($mhs['status_ujian']) && strtolower($mhs['status_ujian']) == 'mengulang')) {
                                                    echo '<span class="badge badge-danger">MENGULANG</span>';
                                                } else {
                                                    $st = $mhs['status_ujian'] ?? '-';
                                                    if ($st == 'Berlangsung') echo '<span class="badge badge-primary">'.$st.'</span>';
                                                    elseif ($st == 'Diterima') echo '<span class="badge badge-success">'.$st.'</span>';
                                                    else echo '<span class="badge badge-secondary">'.$st.'</span>';
                                                }
                                            ?>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="7" class="text-center">Belum ada mahasiswa yang siap pendadaran.</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="card-footer py-2 bg-white">
                        <div class="row align-items-center">
                            <div class="col-sm-6 text-muted small">
                                Total Data: <b><?php echo isset($total_rows) ? $total_rows : 0; ?></b>
                            </div>
                            <div class="col-sm-6">
                                <div class="float-right">
                                    <?php echo isset($pagination) ? $pagination : ''; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
