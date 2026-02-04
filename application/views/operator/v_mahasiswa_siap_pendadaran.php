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
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Mahasiswa Siap Pendadaran (BAB 4 ACC)</h3>
                </div>
                <div class="card-body table-responsive">
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Foto</th>
                                <th>NPM</th>
                                <th>Nama</th>
                                <th>Prodi</th>
                                <th>Angkatan</th>
                                <th>Judul Skripsi</th>
                                <th>Pembimbing 1</th>
                                <th>Pembimbing 2</th>
                                <th>Tanggal ACC</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($mahasiswa)): ?>
                                <?php $no = 1; foreach ($mahasiswa as $mhs): ?>
                                    <tr>
                                        <td><?php echo $no++; ?></td>
                                        <td>
                                            <?php if (!empty($mhs['foto'])): ?>
                                                <img src="<?php echo base_url('uploads/profile/' . $mhs['foto']); ?>" alt="Foto" class="img-thumbnail" width="50">
                                            <?php else: ?>
                                                <img src="<?php echo base_url('assets/image/default.png'); ?>" alt="Foto" class="img-thumbnail" width="50">
                                            <?php endif; ?>
                                        </td>
                                        <td><?php echo $mhs['npm']; ?></td>
                                        <td><?php echo $mhs['nama']; ?></td>
                                        <td><?php echo $mhs['prodi']; ?></td>
                                        <td><?php echo $mhs['angkatan']; ?></td>
                                        <td><?php echo $mhs['judul']; ?></td>
                                        <td><?php echo $mhs['nama_p1']; ?></td>
                                        <td><?php echo $mhs['nama_p2']; ?></td>
                                        <td><?php echo date('d/m/Y H:i', strtotime($mhs['tgl_acc'])); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="10" class="text-center">Belum ada mahasiswa yang siap pendadaran.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </section>
</div>
