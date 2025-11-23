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
                        <i class="fas fa-search mr-1"></i> Verifikasi Hasil Cek Plagiarisme
                    </h3>
                    <div class="card-tools">
                        <span class="badge badge-warning">Menunggu: <?php echo count($plagiat_list); ?></span>
                    </div>
                </div>
                
                <div class="card-body table-responsive p-0">
                    <table class="table table-hover table-striped text-nowrap">
                        <thead>
                            <tr class="text-center bg-light">
                                <th style="width: 5%">No</th>
                                <th style="width: 25%">Mahasiswa</th>
                                <th style="width: 30%">Judul Skripsi</th>
                                <th style="width: 10%">Bab</th>
                                <th style="width: 15%">File Progres</th>
                                <th style="width: 15%">Aksi Verifikasi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($plagiat_list)): ?>
                                <tr>
                                    <td colspan="6" class="text-center py-5 text-muted">
                                        <i class="fas fa-check-circle fa-3x mb-3 text-success"></i><br>
                                        <h5>Tidak ada tugas pending!</h5>
                                        <p>Semua file progres telah diverifikasi.</p>
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($plagiat_list as $key => $plagiat): ?>
                                <tr>
                                    <td class="align-middle text-center"><?php echo $key + 1; ?></td>
                                    
                                    <td class="align-middle">
                                        <span class="font-weight-bold text-dark d-block"><?php echo $plagiat['nama']; ?></span>
                                        <small class="text-muted"><i class="fas fa-id-badge mr-1"></i> <?php echo $plagiat['npm']; ?></small>
                                        <br>
                                        <small class="text-muted"><i class="far fa-calendar-alt mr-1"></i> <?php echo date('d M Y', strtotime($plagiat['tanggal_cek'])); ?></small>
                                    </td>
                                    
                                    <td class="align-middle text-wrap">
                                        <?php echo $plagiat['judul'] ?: '<span class="text-muted font-italic">- Belum ada judul -</span>'; ?>
                                    </td>
                                    
                                    <td class="align-middle text-center">
                                        <span class="badge badge-info p-2">BAB <?php echo $plagiat['bab']; ?></span>
                                    </td>
                                    
                                    <td class="align-middle text-center">
                                        <a href="<?php echo base_url('uploads/progres/' . $plagiat['progres_file']); ?>" target="_blank" class="btn btn-default btn-sm border shadow-sm">
                                            <i class="fas fa-file-pdf text-danger mr-1"></i> Unduh PDF
                                        </a>
                                    </td>
                                    
                                    <td class="align-middle text-center">
                                        <div class="btn-group">
                                            <a href="<?php echo base_url('operator/verifikasi_plagiarisme/' . $plagiat['id'] . '/acc'); ?>" 
                                               class="btn btn-success btn-sm" 
                                               onclick="return confirm('Yakin ingin meluluskan file ini? (Status: Lulus)');"
                                               title="Setujui (Lulus Plagiat)">
                                                <i class="fas fa-check mr-1"></i> Lulus
                                            </a>
                                            <a href="<?php echo base_url('operator/verifikasi_plagiarisme/' . $plagiat['id'] . '/tolak'); ?>" 
                                               class="btn btn-danger btn-sm" 
                                               onclick="return confirm('Yakin ingin menolak file ini? (Status: Tolak/Revisi)');"
                                               title="Tolak (Revisi)">
                                                <i class="fas fa-times mr-1"></i> Tolak
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
                <div class="card-footer clearfix">
                    <small class="text-muted font-italic">
                        * Pastikan Anda telah memeriksa file PDF di situs Turnitin/cek plagiat eksternal sebelum memberikan status.
                    </small>
                </div>
            </div>
            </div>
    </div>
</div>