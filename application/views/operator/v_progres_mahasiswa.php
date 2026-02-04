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
            <!-- Filter -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Filter Data</h3>
                </div>
                <div class="card-body">
                    <form method="GET" action="<?php echo base_url('operator/list_revisi'); ?>">
                        <div class="row">
                            <div class="col-md-8">
                                <div class="form-group">
                                    <label>Cari (Nama Mahasiswa/NPM/Judul Skripsi)</label>
                                    <input type="text" name="keyword" class="form-control" placeholder="Masukkan nama mahasiswa, NPM, atau judul skripsi..." value="<?php echo $this->input->get('keyword'); ?>">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>&nbsp;</label><br>
                                    <button type="submit" class="btn btn-primary"><i class="fas fa-search"></i> Cari</button>
                                    <a href="<?php echo base_url('operator/list_revisi'); ?>" class="btn btn-secondary"><i class="fas fa-undo"></i> Reset</a>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Tabel Riwayat Progress Mahasiswa -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Riwayat Progress Mahasiswa</h3>
                </div>
                <div class="card-body table-responsive">
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>NPM</th>
                                <th>Nama Mahasiswa</th>
                                <th>Prodi</th>
                                <th>Judul Skripsi</th>
                                <th>BAB</th>
                                <th>File</th>
                                <th>Komentar Dosen 1</th>
                                <th>Komentar Dosen 2</th>
                                <th>Nilai Dosen 1</th>
                                <th>Nilai Dosen 2</th>
                                <th>Progres Dosen 1 (%)</th>
                                <th>Progres Dosen 2 (%)</th>
                                <th>Tanggal Upload</th>
                                <th>Tanggal Verifikasi</th>
                                <th>Pembimbing 1</th>
                                <th>Pembimbing 2</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($list_revisi)): ?>
                                <?php $no = 1; foreach ($list_revisi as $revisi): ?>
                                    <tr>
                                        <td><?php echo $no++; ?></td>
                                        <td><?php echo $revisi['npm']; ?></td>
                                        <td><?php echo $revisi['nama_mhs']; ?></td>
                                        <td><?php echo $revisi['prodi']; ?></td>
                                        <td><?php echo $revisi['judul']; ?></td>
                                        <td><?php echo $revisi['bab']; ?></td>
                                        <td>
                                            <?php if (!empty($revisi['file'])): ?>
                                                <a href="<?php echo base_url('uploads/progres/' . $revisi['file']); ?>" target="_blank" class="btn btn-sm btn-primary">Lihat File</a>
                                            <?php else: ?>
                                                -
                                            <?php endif; ?>
                                        </td>
                                        <td><?php echo $revisi['komentar_dosen1']; ?></td>
                                        <td><?php echo $revisi['komentar_dosen2']; ?></td>
                                        <td><?php echo $revisi['nilai_dosen1']; ?></td>
                                        <td><?php echo $revisi['nilai_dosen2']; ?></td>
                                        <td><?php echo $revisi['progres_dosen1']; ?>%</td>
                                        <td><?php echo $revisi['progres_dosen2']; ?>%</td>
                                        <td><?php echo date('d/m/Y H:i', strtotime($revisi['tgl_upload'])); ?></td>
                                        <td><?php echo $revisi['tgl_verifikasi'] ? date('d/m/Y H:i', strtotime($revisi['tgl_verifikasi'])) : '-'; ?></td>
                                        <td><?php echo $revisi['nama_p1']; ?></td>
                                        <td><?php echo $revisi['nama_p2']; ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="17" class="text-center">Silahkan cari data mahasiswa di atas.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </section>
</div>
