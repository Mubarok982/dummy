<div class="content-wrapper">
    
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0 text-dark">Cek Plagiarisme</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="<?php echo base_url('dashboard'); ?>">Home</a></li>
                        <li class="breadcrumb-item active">Cek Plagiarisme</li>
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
                                <i class="fas fa-search mr-1"></i> Verifikasi Hasil Cek Plagiarisme
                            </h3>
                            <div class="card-tools">
                                <span class="badge badge-warning" style="font-size: 0.9rem;">
                                    Menunggu: <?php echo count($plagiat_list); ?>
                                </span>
                            </div>
                        </div>
                        
                        <div class="card-body table-responsive p-0">
                            <table class="table table-hover table-striped text-nowrap align-middle">
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
                                                <p class="mb-0">Semua file progres telah diverifikasi.</p>
                                            </td>
                                        </tr>
                                    <?php else: ?>
                                        <?php foreach ($plagiat_list as $key => $plagiat): ?>
                                        <tr>
                                            <td class="align-middle text-center text-muted"><?php echo $key + 1; ?></td>
                                            
                                            <td class="align-middle">
                                                <div class="user-block">
                                                    <span class="username ml-0">
                                                        <a href="#" class="text-dark font-weight-bold"><?php echo $plagiat['nama']; ?></a>
                                                    </span>
                                                    <span class="description ml-0">
                                                        <i class="fas fa-id-badge mr-1"></i> <?php echo $plagiat['npm']; ?>
                                                        <br>
                                                        <i class="far fa-calendar-alt mr-1"></i> <?php echo date('d M Y', strtotime($plagiat['tanggal_cek'])); ?>
                                                    </span>
                                                </div>
                                            </td>
                                            
                                            <td class="align-middle text-wrap" style="min-width: 250px;">
                                                <?php if($plagiat['judul']): ?>
                                                    <span class="d-block" style="line-height: 1.4;"><?php echo $plagiat['judul']; ?></span>
                                                <?php else: ?>
                                                    <span class="text-muted font-italic">- Belum ada judul -</span>
                                                <?php endif; ?>
                                            </td>
                                            
                                            <td class="align-middle text-center">
                                                <span class="badge badge-info p-2" style="font-size: 0.9rem;">BAB <?php echo $plagiat['bab']; ?></span>
                                            </td>
                                            
                                            <td class="align-middle text-center">
                                                <a href="<?php echo base_url('uploads/progres/' . $plagiat['progres_file']); ?>" target="_blank" class="btn btn-default btn-sm border shadow-sm">
                                                    <i class="fas fa-file-pdf text-danger mr-1"></i> Unduh PDF
                                                </a>
                                            </td>
                                            
                                            <td class="align-middle text-center">
                                                <div class="btn-group">
                                                    <a href="<?php echo base_url('operator/verifikasi_plagiarisme/' . $plagiat['id'] . '/acc'); ?>" 
                                                       class="btn btn-success btn-sm shadow-sm" 
                                                       onclick="return confirm('Yakin ingin meluluskan file ini? (Status: Lulus)');"
                                                       title="Lulus Plagiat">
                                                        <i class="fas fa-check mr-1"></i> Lulus
                                                    </a>
                                                    <a href="<?php echo base_url('operator/verifikasi_plagiarisme/' . $plagiat['id'] . '/tolak'); ?>" 
                                                       class="btn btn-danger btn-sm shadow-sm" 
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
                        <div class="card-footer clearfix bg-white">
                            <small class="text-muted font-italic">
                                * Pastikan Anda telah memeriksa file PDF di situs Turnitin/cek plagiat eksternal sebelum memberikan status.
                            </small>
                        </div>
                    </div>

                </div>
            </div>

        </div>
    </section>
</div>