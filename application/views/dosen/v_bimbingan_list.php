<div class="content-wrapper">
    
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0 text-dark">Mahasiswa Bimbingan</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="<?php echo base_url('dashboard'); ?>">Home</a></li>
                        <li class="breadcrumb-item active">Mahasiswa Bimbingan</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">
            
            <div class="row">
                <div class="col-12">

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
                                <i class="fas fa-chalkboard-teacher mr-1"></i> Daftar Mahasiswa Bimbingan
                            </h3>
                            <div class="card-tools">
                                <span class="badge badge-primary" style="font-size: 0.9rem;">
                                    <?php echo count($bimbingan); ?> Mahasiswa
                                </span>
                            </div>
                        </div>
                        
                        <div class="card-body table-responsive p-0">
                            <table class="table table-hover table-striped text-nowrap align-middle">
                                <thead>
                                    <tr class="bg-light text-center">
                                        <th style="width: 5%">No</th>
                                        <th style="width: 25%">Mahasiswa</th>
                                        <th style="width: 30%">Judul Skripsi</th>
                                        <th style="width: 25%">Tim Pembimbing</th>
                                        <th style="width: 15%">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (empty($bimbingan)): ?>
                                        <tr>
                                            <td colspan="5" class="text-center py-5 text-muted">
                                                <i class="fas fa-user-graduate fa-3x mb-3 text-gray-300"></i><br>
                                                <span class="font-weight-bold">Belum ada mahasiswa yang ditugaskan.</span>
                                            </td>
                                        </tr>
                                    <?php else: ?>
                                        <?php foreach ($bimbingan as $key => $b): ?>
                                        <tr>
                                            <td class="align-middle text-center"><?php echo $key + 1; ?></td>
                                            
                                            <td class="align-middle">
                                                <div class="user-block">
                                                    <img class="img-circle img-bordered-sm" src="https://ui-avatars.com/api/?name=<?php echo urlencode($b['nama_mhs']); ?>&background=random" alt="User Image">
                                                    <span class="username">
                                                        <span class="text-primary font-weight-bold"><?php echo $b['nama_mhs']; ?></span>
                                                    </span>
                                                    <span class="description">NPM: <?php echo $b['npm']; ?></span>
                                                </div>
                                            </td>

                                            <td class="align-middle text-wrap">
                                                <?php if ($b['judul']): ?>
                                                    <span class="font-weight-bold d-block" style="line-height: 1.4;"><?php echo $b['judul']; ?></span>
                                                <?php else: ?>
                                                    <span class="badge badge-warning text-white"><i class="fas fa-clock mr-1"></i> Belum Mengajukan Judul</span>
                                                <?php endif; ?>
                                            </td>

                                            <td class="align-middle small">
                                                <ul class="list-unstyled mb-0">
                                                    <li class="<?php echo ($b['nama_p1'] == $this->session->userdata('nama')) ? 'text-primary font-weight-bold' : 'text-muted'; ?>">
                                                        <i class="fas fa-user-tie fa-fw mr-1"></i> P1: <?php echo $b['nama_p1']; ?>
                                                    </li>
                                                    <li class="<?php echo ($b['nama_p2'] == $this->session->userdata('nama')) ? 'text-primary font-weight-bold' : 'text-muted'; ?>">
                                                        <i class="fas fa-user-tie fa-fw mr-1"></i> P2: <?php echo $b['nama_p2']; ?>
                                                    </li>
                                                </ul>
                                            </td>

                                            <td class="align-middle text-center">
                                                <?php if ($b['judul']): ?>
                                                    <a href="<?php echo base_url('dosen/progres_detail/' . $b['id_skripsi']); ?>" class="btn btn-info btn-sm shadow-sm px-3">
                                                        <i class="fas fa-eye mr-1"></i> Cek Progres
                                                    </a>
                                                <?php else: ?>
                                                    <button class="btn btn-secondary btn-sm disabled" disabled>
                                                        <i class="fas fa-hourglass-half mr-1"></i> Menunggu
                                                    </button>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                        
                        <div class="card-footer clearfix bg-white">
                            <small class="text-muted font-italic float-right">* Nama Anda dicetak tebal pada kolom Tim Pembimbing.</small>
                        </div>
                    </div>

                </div>
            </div>

        </div>
    </section>
</div>