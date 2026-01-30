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
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Program Studi</label>
                                    <select name="prodi" class="form-control">
                                        <option value="">Semua Prodi</option>
                                        <option value="Teknik Informatika S1" <?php echo ($this->input->get('prodi') == 'Teknik Informatika S1') ? 'selected' : ''; ?>>Teknik Informatika S1</option>
                                        <option value="Teknologi Informasi D3" <?php echo ($this->input->get('prodi') == 'Teknologi Informasi D3') ? 'selected' : ''; ?>>Teknologi Informasi D3</option>
                                        <option value="Teknik Industri S1" <?php echo ($this->input->get('prodi') == 'Teknik Industri S1') ? 'selected' : ''; ?>>Teknik Industri S1</option>
                                        <option value="Teknik Mesin S1" <?php echo ($this->input->get('prodi') == 'Teknik Mesin S1') ? 'selected' : ''; ?>>Teknik Mesin S1</option>
                                        <option value="Mesin Otomotif D3" <?php echo ($this->input->get('prodi') == 'Mesin Otomotif D3') ? 'selected' : ''; ?>>Mesin Otomotif D3</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Cari (Nama/NPM/Judul)</label>
                                    <input type="text" name="keyword" class="form-control" placeholder="Kata kunci..." value="<?php echo $this->input->get('keyword'); ?>">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>&nbsp;</label><br>
                                    <button type="submit" class="btn btn-primary"><i class="fas fa-search"></i> Filter</button>
                                    <a href="<?php echo base_url('operator/list_revisi'); ?>" class="btn btn-secondary"><i class="fas fa-undo"></i> Reset</a>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Tabel List Revisi -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Daftar Revisi Progres Skripsi</h3>
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
                                <th>Komentar Dosen 1</th>
                                <th>Komentar Dosen 2</th>
                                <th>Tanggal Upload</th>
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
                                        <td><?php echo $revisi['komentar_dosen1']; ?></td>
                                        <td><?php echo $revisi['komentar_dosen2']; ?></td>
                                        <td><?php echo date('d/m/Y H:i', strtotime($revisi['tgl_upload'])); ?></td>
                                        <td><?php echo $revisi['nama_p1']; ?></td>
                                        <td><?php echo $revisi['nama_p2']; ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="11" class="text-center">Tidak ada data revisi ditemukan.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </section>
</div>
