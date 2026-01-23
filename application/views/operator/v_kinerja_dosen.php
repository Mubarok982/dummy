<div class="content-wrapper">
    
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0 text-dark">Kinerja Dosen (Operator)</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="<?php echo base_url('dashboard'); ?>">Home</a></li>
                        <li class="breadcrumb-item active">Kinerja Dosen</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">
            
            <div class="card shadow-sm mb-4">
                <div class="card-body p-2">
                    <form action="<?php echo base_url('operator/kinerja_dosen'); ?>" method="GET">
                        <div class="d-flex justify-content-between align-items-center flex-wrap">
                            
                            <div class="d-flex align-items-center m-1">
                                <span class="text-muted font-weight-bold mr-2 ml-2"><i class="fas fa-filter"></i> Filter:</span>
                                <div class="input-group input-group-sm" style="width: 250px;">
                                    <input type="text" name="keyword" class="form-control" placeholder="Cari Nama / NIDK..." value="<?php echo $this->input->get('keyword'); ?>">
                                    <div class="input-group-append">
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fas fa-search"></i> Cari
                                        </button>
                                    </div>
                                </div>
                                <?php if($this->input->get('keyword')): ?>
                                    <a href="<?php echo base_url('operator/kinerja_dosen'); ?>" class="btn btn-outline-danger btn-sm ml-2" title="Reset">
                                        <i class="fas fa-times"></i>
                                    </a>
                                <?php endif; ?>
                            </div>

                            <div class="d-flex align-items-center m-1">
                                <a href="<?php echo base_url('operator/kinerja_dosen_csv?keyword=' . $this->input->get('keyword')); ?>" target="_blank" class="btn btn-success btn-sm mr-3">
                                    <i class="fas fa-file-csv mr-1"></i> Export CSV
                                </a>
                                <div class="text-muted small">
                                    Total Data: <b><?php echo $total_rows; ?></b>
                                </div>
                            </div>

                        </div>
                    </form>
                </div>
            </div>

            <div class="row">
                <div class="col-12">
                    <div class="card card-outline card-primary shadow-sm">
                        <div class="card-header border-0">
                            <h3 class="card-title font-weight-bold text-primary">
                                <i class="fas fa-list-alt mr-1"></i> Rekapitulasi Kinerja
                            </h3>
                        </div>
                        
                        <div class="card-body table-responsive p-0">
                            <table class="table table-hover table-striped text-nowrap align-middle">
                                <thead class="bg-light">
                                    <tr class="text-center">
                                        <th style="width: 5%;">No</th>
                                        <th style="width: 35%;" class="text-left">Nama Dosen</th>
                                        <th style="width: 20%;">NIDK</th>
                                        <th style="width: 20%;">Total Aktivitas Koreksi</th>
                                        <th style="width: 20%;">Riwayat</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (empty($dosen_list)): ?>
                                        <tr>
                                            <td colspan="5" class="text-center py-5 text-muted">
                                                <i class="fas fa-user-slash fa-3x mb-3 opacity-50"></i><br>
                                                Data dosen tidak ditemukan.
                                            </td>
                                        </tr>
                                    <?php else: ?>
                                        <?php 
                                        $no = $start_index + 1;
                                        foreach ($dosen_list as $dosen): 
                                        ?>
                                        <tr>
                                            <td class="text-center align-middle"><?php echo $no++; ?></td>
                                            
                                            <td class="align-middle">
                                                <div class="d-flex align-items-center">
                                                    <img src="https://ui-avatars.com/api/?name=<?php echo urlencode($dosen['nama']); ?>&background=random&size=35" class="img-circle mr-2" alt="Avatar">
                                                    <span class="font-weight-bold text-dark"><?php echo $dosen['nama']; ?></span>
                                                </div>
                                            </td>
                                            
                                            <td class="text-center align-middle">
                                                <span class="badge badge-secondary font-weight-normal px-2 py-1">
                                                    <?php echo $dosen['nidk']; ?>
                                                </span>
                                            </td>
                                            
                                            <td class="text-center align-middle">
                                                <?php if($dosen['total_aksi'] > 0): ?>
                                                    <span class="badge badge-success px-3 py-2" style="font-size: 0.9rem;">
                                                        <?php echo $dosen['total_aksi']; ?> Kali
                                                    </span>
                                                <?php else: ?>
                                                    <span class="badge badge-light text-muted px-3 py-2 border">
                                                        0 Kali
                                                    </span>
                                                <?php endif; ?>
                                            </td>
                                            
                                            <td class="text-center align-middle">
                                                <button type="button" class="btn btn-info btn-sm shadow-sm" data-toggle="modal" data-target="#modal-history-<?php echo $dosen['id']; ?>">
                                                    <i class="fas fa-eye mr-1"></i> Lihat Detail
                                                </button>

                                                <div class="modal fade" id="modal-history-<?php echo $dosen['id']; ?>" tabindex="-1" aria-hidden="true">
                                                    <div class="modal-dialog modal-dialog-scrollable">
                                                        <div class="modal-content">
                                                            <div class="modal-header bg-info">
                                                                <h5 class="modal-title text-white">
                                                                    <i class="fas fa-history mr-1"></i> Riwayat: <?php echo substr($dosen['nama'], 0, 20); ?>...
                                                                </h5>
                                                                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                                                                    <span aria-hidden="true">&times;</span>
                                                                </button>
                                                            </div>
                                                            <div class="modal-body p-0">
                                                                <?php if (empty($dosen['aktivitas'])): ?>
                                                                    <div class="text-center py-5">
                                                                        <i class="fas fa-inbox fa-3x text-muted mb-2 opacity-50"></i>
                                                                        <p class="text-muted">Belum ada data aktivitas.</p>
                                                                    </div>
                                                                <?php else: ?>
                                                                    <table class="table table-sm table-striped mb-0">
                                                                        <thead class="bg-light">
                                                                            <tr>
                                                                                <th class="pl-4">Tanggal</th>
                                                                                <th class="text-center">Jumlah Aksi</th>
                                                                            </tr>
                                                                        </thead>
                                                                        <tbody>
                                                                            <?php foreach ($dosen['aktivitas'] as $act): ?>
                                                                            <tr>
                                                                                <td class="pl-4 align-middle">
                                                                                    <?php echo date('d M Y', strtotime($act['tanggal'])); ?>
                                                                                </td>
                                                                                <td class="text-center align-middle">
                                                                                    <span class="font-weight-bold text-info"><?php echo $act['total_aksi']; ?></span>
                                                                                </td>
                                                                            </tr>
                                                                            <?php endforeach; ?>
                                                                        </tbody>
                                                                    </table>
                                                                <?php endif; ?>
                                                            </div>
                                                            <div class="modal-footer bg-light justify-content-between py-2">
                                                                <small class="text-muted">Total Keseluruhan: <b><?php echo $dosen['total_aksi']; ?></b></small>
                                                                <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Tutup</button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                </td>
                                        </tr>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>

                        <div class="card-footer py-2">
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="text-muted small">
                                    Halaman <b><?php echo $this->input->get('page') ? ($this->input->get('page') / $per_page) + 1 : 1; ?></b> 
                                    dari Total <b><?php echo ceil($total_rows / $per_page); ?></b> Halaman
                                </div>
                                <div>
                                    <?php echo $pagination; ?>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>

        </div>
    </section>
</div>