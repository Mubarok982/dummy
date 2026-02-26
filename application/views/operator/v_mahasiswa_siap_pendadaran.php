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
                            <div class="col-md-2">
                                <label>&nbsp;</label>
                                <button type="submit" class="btn btn-primary btn-block"><i class="fas fa-search mr-1"></i> Filter</button>
                            </div>
                        </div>
                        </form>

                    <div class="table-responsive">
                        <table class="table table-bordered table-striped table-hover">
                            <thead class="bg-light">
                                <tr class="text-center">
                                    <th style="width: 5%">No</th>
                                    <th class="sortable" data-sort="nama" style="width: 20%">Mahasiswa</th>
                                    <th class="sortable" data-sort="prodi" style="width: 15%">Program Studi</th>
                                    <th class="sortable" data-sort="judul" style="width: 25%">Judul Skripsi</th>
                                    <th class="sortable" data-sort="nama_p1" style="width: 15%">Pembimbing</th>
                                    <th class="sortable" data-sort="tgl_daftar" style="width: 10%">Tanggal Daftar</th>
                                    <th class="sortable" data-sort="status_ujian" style="width: 10%">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($mahasiswa)): ?>
                                    <?php $no = isset($start_index) ? $start_index + 1 : 1; foreach ($mahasiswa as $mhs): ?>
                                        <tr>
                                            <td class="text-center align-middle">
                                                <?php echo $no++; ?>
                                                <br>
                                                <small class="text-muted">ID: <?php echo isset($mhs['id_skripsi']) ? $mhs['id_skripsi'] : '-'; ?></small>
                                            </td>
                                            
                                            <td class="align-middle">
                                                <strong class="d-block mb-1"><?php echo $mhs['nama']; ?></strong>
                                                <span class="text-muted small"><i class="fas fa-id-card mr-1"></i><?php echo $mhs['npm']; ?></span>
                                                <span class="badge badge-secondary ml-1"><?php echo $mhs['angkatan']; ?></span>
                                            </td>
                                            
                                            <td class="align-middle text-center">
                                                <span class="badge badge-info px-2 py-1"><?php echo $mhs['prodi']; ?></span>
                                            </td>
                                            
                                            <td class="align-middle"><?php echo $mhs['judul']; ?></td>
                                            
                                            <td class="align-middle small">
                                                1. <?php echo $mhs['nama_p1']; ?><br>
                                                2. <?php echo $mhs['nama_p2']; ?>
                                            </td>
                                            
                                            <td class="text-center align-middle">
                                                <?php if (!empty($mhs['tgl_daftar'])): ?>
                                                    <span class="badge badge-success px-2 py-1">
                                                        <i class="fas fa-check-circle mr-1"></i>
                                                        <?php echo date('d/m/Y', strtotime($mhs['tgl_daftar'])); ?>
                                                    </span>
                                                <?php else: ?>
                                                    -
                                                <?php endif; ?>
                                            </td>
                                            
                                            <td class="text-center align-middle">
                                                <?php 
                                                    if ((isset($mhs['status_ujian']) && strtolower($mhs['status_ujian']) == 'mengulang') || (isset($mhs['status_acc_kaprodi']) && strtolower($mhs['status_acc_kaprodi']) == 'ditolak')) {
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
                                            <td colspan="7" class="text-center py-5 text-muted">
                                            <i class="fas fa-user-graduate fa-3x mb-3 opacity-50"></i><br>
                                            Belum ada mahasiswa yang siap pendadaran.
                                        </td>
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