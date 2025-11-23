<div class="container-fluid">
    <div class="row">
        <div class="col-12">

            <?php if ($this->session->flashdata('pesan_sukses')): ?>
                <div class="alert alert-success alert-dismissible">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                    <h5><i class="icon fas fa-check"></i> Berhasil!</h5>
                    <?php echo $this->session->flashdata('pesan_sukses'); ?>
                </div>
            <?php endif; ?>

            <?php if ($this->session->flashdata('pesan_error')): ?>
                <div class="alert alert-danger alert-dismissible">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                    <h5><i class="icon fas fa-ban"></i> Gagal!</h5>
                    <?php echo $this->session->flashdata('pesan_error'); ?>
                </div>
            <?php endif; ?>

            <div class="card card-primary card-outline">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-user-tie mr-1"></i> Daftar Penugasan Pembimbing Skripsi
                    </h3>
                </div>
                
                <div class="card-body table-responsive p-0">
                    <table class="table table-hover table-striped text-nowrap">
                        <thead>
                            <tr class="text-center bg-light">
                                <th style="width: 10%;">NPM</th>
                                <th style="width: 20%;">Nama Mahasiswa</th>
                                <th style="width: 30%;">Judul Skripsi</th>
                                <th style="width: 15%;">Pembimbing 1</th>
                                <th style="width: 15%;">Pembimbing 2</th>
                                <th style="width: 10%;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($mahasiswa as $mhs): ?>
                            <tr>
                                <td class="align-middle text-center"><?php echo $mhs['npm']; ?></td>
                                <td class="align-middle font-weight-bold"><?php echo $mhs['nama']; ?></td>
                                <td class="align-middle">
                                    <?php if ($mhs['judul']): ?>
                                        <span title="<?php echo $mhs['judul']; ?>">
                                            <?php echo (strlen($mhs['judul']) > 50) ? substr($mhs['judul'], 0, 50) . '...' : $mhs['judul']; ?>
                                        </span>
                                    <?php else: ?>
                                        <span class="badge badge-secondary">Belum Mengajukan</span>
                                    <?php endif; ?>
                                </td>
                                
                                <td class="align-middle">
                                    <?php if ($mhs['nama_p1']): ?>
                                        <i class="fas fa-user-check text-success mr-1"></i> <?php echo $mhs['nama_p1']; ?>
                                    <?php else: ?>
                                        <span class="text-muted text-sm"><i class="fas fa-minus-circle"></i> Belum Ada</span>
                                    <?php endif; ?>
                                </td>

                                <td class="align-middle">
                                    <?php if ($mhs['nama_p2']): ?>
                                        <i class="fas fa-user-check text-success mr-1"></i> <?php echo $mhs['nama_p2']; ?>
                                    <?php else: ?>
                                        <span class="text-muted text-sm"><i class="fas fa-minus-circle"></i> Belum Ada</span>
                                    <?php endif; ?>
                                </td>

                                <td class="align-middle text-center">
                                    <?php if ($mhs['id_skripsi']): ?>
                                        <button type="button" class="btn btn-primary btn-sm shadow-sm" data-toggle="modal" data-target="#modal-assign-<?php echo $mhs['id_skripsi']; ?>">
                                            <i class="fas fa-edit"></i> Atur
                                        </button>

                                        <div class="modal fade" id="modal-assign-<?php echo $mhs['id_skripsi']; ?>" tabindex="-1" role="dialog" aria-hidden="true">
                                            <div class="modal-dialog" role="document">
                                                <div class="modal-content text-left">
                                                    <div class="modal-header bg-primary">
                                                        <h5 class="modal-title text-white">
                                                            <i class="fas fa-user-tie mr-1"></i> Penugasan Pembimbing
                                                        </h5>
                                                        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                                                            <span aria-hidden="true">&times;</span>
                                                        </button>
                                                    </div>
                                                    
                                                    <?php echo form_open('operator/assign_pembimbing_aksi'); ?>
                                                    <div class="modal-body">
                                                        <input type="hidden" name="id_skripsi" value="<?php echo $mhs['id_skripsi']; ?>">
                                                        
                                                        <div class="callout callout-info py-2 mb-3">
                                                            <small class="text-muted">Mahasiswa:</small><br>
                                                            <strong><?php echo $mhs['nama']; ?> (<?php echo $mhs['npm']; ?>)</strong>
                                                        </div>

                                                        <div class="form-group">
                                                            <label>Pembimbing 1</label>
                                                            <select name="pembimbing1" class="form-control" required>
                                                                <option value="">-- Pilih Dosen --</option>
                                                                <?php foreach ($dosen_list as $dsn): ?>
                                                                    <option value="<?php echo $dsn['id']; ?>" <?php echo ($mhs['pembimbing1'] == $dsn['id']) ? 'selected' : ''; ?>>
                                                                        <?php echo $dsn['nama']; ?> (<?php echo $dsn['nidk']; ?>)
                                                                    </option>
                                                                <?php endforeach; ?>
                                                            </select>
                                                        </div>

                                                        <div class="form-group">
                                                            <label>Pembimbing 2</label>
                                                            <select name="pembimbing2" class="form-control" required>
                                                                <option value="">-- Pilih Dosen --</option>
                                                                <?php foreach ($dosen_list as $dsn): ?>
                                                                    <option value="<?php echo $dsn['id']; ?>" <?php echo ($mhs['pembimbing2'] == $dsn['id']) ? 'selected' : ''; ?>>
                                                                        <?php echo $dsn['nama']; ?> (<?php echo $dsn['nidk']; ?>)
                                                                    </option>
                                                                <?php endforeach; ?>
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer justify-content-between">
                                                        <button type="button" class="btn btn-default" data-dismiss="modal">Batal</button>
                                                        <button type="submit" class="btn btn-primary"><i class="fas fa-save mr-1"></i> Simpan Penugasan</button>
                                                    </div>
                                                    <?php echo form_close(); ?>
                                                </div>
                                            </div>
                                        </div>
                                        <?php else: ?>
                                        <span class="badge badge-warning p-2"><i class="fas fa-clock"></i> Menunggu Judul</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>

                            <?php if (empty($mahasiswa)): ?>
                                <tr>
                                    <td colspan="6" class="text-center py-5 text-muted">
                                        <i class="fas fa-users-slash fa-2x mb-2"></i><br>
                                        Belum ada mahasiswa yang terdaftar untuk skripsi.
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
                
                <div class="card-footer clearfix">
                    <small class="text-muted float-right">Total Mahasiswa: <?php echo count($mahasiswa); ?></small>
                </div>
            </div>

        </div>
    </div>
</div>