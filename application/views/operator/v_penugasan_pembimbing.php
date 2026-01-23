<div class="content-wrapper">
    
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0 text-dark">Penugasan Pembimbing</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="<?php echo base_url('dashboard'); ?>">Home</a></li>
                        <li class="breadcrumb-item active">Assign Pembimbing</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">
            
            <div class="row">
                <div class="col-12">

                    <?php if ($this->session->flashdata('pesan_sukses')): ?>
                        <div class="alert alert-success alert-dismissible fade show shadow-sm">
                            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                            <h5><i class="icon fas fa-check"></i> Berhasil!</h5>
                            <?php echo $this->session->flashdata('pesan_sukses'); ?>
                        </div>
                    <?php endif; ?>

                    <?php if ($this->session->flashdata('pesan_error')): ?>
                        <div class="alert alert-danger alert-dismissible fade show shadow-sm">
                            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                            <h5><i class="icon fas fa-ban"></i> Gagal!</h5>
                            <?php echo $this->session->flashdata('pesan_error'); ?>
                        </div>
                    <?php endif; ?>

                    <div class="card card-primary card-outline shadow-sm">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-user-tie mr-1"></i> Daftar Penugasan Pembimbing Skripsi
                            </h3>
                            <div class="card-tools">
                                <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                    <i class="fas fa-minus"></i>
                                </button>
                            </div>
                        </div>
                        
                        <div class="card-body table-responsive p-0">
                            <table class="table table-hover table-striped text-nowrap align-middle">
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
                                    <?php if (empty($mahasiswa)): ?>
                                        <tr>
                                            <td colspan="6" class="text-center py-5 text-muted">
                                                <i class="fas fa-users-slash fa-3x mb-3 text-gray-300"></i><br>
                                                Belum ada mahasiswa yang mengajukan judul / perlu assigned.
                                            </td>
                                        </tr>
                                    <?php else: ?>
                                        <?php foreach ($mahasiswa as $mhs): ?>
                                        <tr>
                                            <td class="align-middle text-center"><?php echo $mhs['npm']; ?></td>
                                            
                                            <td class="align-middle font-weight-bold text-dark"><?php echo $mhs['nama']; ?></td>
                                            
                                            <td class="align-middle text-wrap" style="min-width: 250px;">
                                                <?php if ($mhs['judul']): ?>
                                                    <span data-toggle="tooltip" title="<?php echo $mhs['judul']; ?>">
                                                        <?php echo (strlen($mhs['judul']) > 60) ? substr($mhs['judul'], 0, 60) . '...' : $mhs['judul']; ?>
                                                    </span>
                                                <?php else: ?>
                                                    <span class="badge badge-secondary font-weight-normal">Belum Mengajukan</span>
                                                <?php endif; ?>
                                            </td>
                                            
                                            <td class="align-middle small">
                                                <?php if ($mhs['nama_p1']): ?>
                                                    <i class="fas fa-user-check text-success mr-1"></i> <?php echo $mhs['nama_p1']; ?>
                                                <?php else: ?>
                                                    <span class="text-muted font-italic">- Belum Ada -</span>
                                                <?php endif; ?>
                                            </td>

                                            <td class="align-middle small">
                                                <?php if ($mhs['nama_p2']): ?>
                                                    <i class="fas fa-user-check text-success mr-1"></i> <?php echo $mhs['nama_p2']; ?>
                                                <?php else: ?>
                                                    <span class="text-muted font-italic">- Belum Ada -</span>
                                                <?php endif; ?>
                                            </td>

                                            <td class="align-middle text-center">
                                                <?php if ($mhs['id_skripsi']): ?>
                                                    <button type="button" class="btn btn-primary btn-sm shadow-sm px-3" data-toggle="modal" data-target="#modal-assign-<?php echo $mhs['id_skripsi']; ?>">
                                                        <i class="fas fa-edit mr-1"></i> Atur
                                                    </button>

                                                    <div class="modal fade" id="modal-assign-<?php echo $mhs['id_skripsi']; ?>" tabindex="-1" role="dialog" aria-hidden="true">
                                                        <div class="modal-dialog modal-dialog-centered" role="document">
                                                            <div class="modal-content text-left shadow-lg border-0">
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
                                                                    
                                                                    <div class="callout callout-info py-2 mb-3 bg-light border-left-info">
                                                                        <small class="text-muted text-uppercase font-weight-bold">Mahasiswa:</small><br>
                                                                        <span class="text-dark" style="font-size: 1.1em;"><?php echo $mhs['nama']; ?></span> 
                                                                        <span class="badge badge-info ml-1"><?php echo $mhs['npm']; ?></span>
                                                                        <hr class="my-2">
                                                                        <small class="text-muted text-uppercase font-weight-bold">Judul:</small><br>
                                                                        <span class="font-italic text-dark small"><?php echo $mhs['judul']; ?></span>
                                                                    </div>

                                                                    <div class="form-group">
                                                                        <label class="font-weight-bold">Pembimbing 1 <span class="text-danger">*</span></label>
                                                                        <select name="pembimbing1" class="form-control select2" required style="width: 100%;">
                                                                            <option value="">-- Pilih Dosen --</option>
                                                                            <?php foreach ($dosen_list as $dsn): ?>
                                                                                <option value="<?php echo $dsn['id']; ?>" <?php echo ($mhs['pembimbing1'] == $dsn['id']) ? 'selected' : ''; ?>>
                                                                                    <?php echo $dsn['nama']; ?> (<?php echo $dsn['nidk']; ?>)
                                                                                </option>
                                                                            <?php endforeach; ?>
                                                                        </select>
                                                                    </div>

                                                                    <div class="form-group">
                                                                        <label class="font-weight-bold">Pembimbing 2 <span class="text-danger">*</span></label>
                                                                        <select name="pembimbing2" class="form-control select2" required style="width: 100%;">
                                                                            <option value="">-- Pilih Dosen --</option>
                                                                            <?php foreach ($dosen_list as $dsn): ?>
                                                                                <option value="<?php echo $dsn['id']; ?>" <?php echo ($mhs['pembimbing2'] == $dsn['id']) ? 'selected' : ''; ?>>
                                                                                    <?php echo $dsn['nama']; ?> (<?php echo $dsn['nidk']; ?>)
                                                                                </option>
                                                                            <?php endforeach; ?>
                                                                        </select>
                                                                    </div>
                                                                </div>
                                                                <div class="modal-footer bg-light justify-content-between">
                                                                    <button type="button" class="btn btn-default" data-dismiss="modal">Batal</button>
                                                                    <button type="submit" class="btn btn-primary shadow-sm"><i class="fas fa-save mr-1"></i> Simpan Penugasan</button>
                                                                </div>
                                                                <?php echo form_close(); ?>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <?php else: ?>
                                                    <span class="badge badge-warning p-2 shadow-sm"><i class="fas fa-clock mr-1"></i> Menunggu Judul</span>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                        
                        <div class="card-footer clearfix bg-white">
                            <small class="text-muted float-right">Total Mahasiswa: <b><?php echo count($mahasiswa); ?></b></small>
                        </div>
                    </div>

                </div>
            </div>

        </div>
    </section>
</div>

<style>
    .border-left-info { border-left: 4px solid #17a2b8 !important; }
    .select2-container .select2-selection--single { height: 38px !important; } /* Fix height select2 jika dipakai */
</style>