<div class="content-wrapper">
    
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0 text-dark">Kinerja Dosen</h1>
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
                    <form action="<?php echo base_url('dosen/kinerja_dosen'); ?>" method="GET">
                        <div class="d-flex justify-content-between align-items-center flex-wrap">
                            
                            <div class="d-flex align-items-center m-1">
                                <span class="text-muted font-weight-bold mr-2 ml-2"><i class="fas fa-filter"></i> Filter:</span>
                                <div class="input-group input-group-sm" style="width: 250px;">
                                    <input type="text" name="keyword" class="form-control" placeholder="Cari Nama Dosen..." value="<?php echo $this->input->get('keyword'); ?>">
                                    <div class="input-group-append">
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fas fa-search"></i>
                                        </button>
                                    </div>
                                </div>
                                <?php if($this->input->get('keyword')): ?>
                                    <a href="<?php echo base_url('dosen/kinerja_dosen'); ?>" class="btn btn-link btn-sm text-danger ml-2">
                                        <i class="fas fa-times"></i> Reset
                                    </a>
                                <?php endif; ?>
                            </div>

                            <div class="d-flex align-items-center m-1">
                                <div class="text-muted small">
                                    Total Dosen Prodi: <b><?php echo $total_rows; ?></b>
                                </div>
                            </div>

                        </div>
                    </form>
                </div>
            </div>

            <div class="card card-outline card-warning shadow-sm">
                <div class="card-header border-0">
                    <h3 class="card-title font-weight-bold">
                        <i class="fas fa-chart-line mr-1 text-warning"></i> Kinerja Dosen - <?php echo $this->session->userdata('prodi'); ?>
                    </h3>
                </div>
                
                <div class="card-body table-responsive p-0">
                    <table class="table table-hover table-striped text-nowrap align-middle">
                        <thead class="bg-light">
                            <tr>
                                <th style="width: 5%;" class="text-center">#</th>
                                <th style="width: 40%;">Nama Dosen</th>
                                <th style="width: 20%;">NIDK</th>
                                <th style="width: 20%;" class="text-center">Total Aktivitas</th>
                                <th style="width: 15%;" class="text-center">Detail</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($dosen_list)): ?>
                                <tr>
                                    <td colspan="5" class="text-center py-5 text-muted">
                                        <i class="fas fa-users-slash fa-2x mb-2 opacity-50"></i><br>
                                        Tidak ada data dosen ditemukan di Prodi ini.
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php 
                                $no = $start_index + 1;
                                foreach ($dosen_list as $dosen): 
                                ?>
                                <tr>
                                    <td class="text-center text-muted"><?php echo $no++; ?></td>
                                    
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <img src="https://ui-avatars.com/api/?name=<?php echo urlencode($dosen['nama']); ?>&background=random&size=32" class="img-circle mr-3" alt="Avatar">
                                            <span class="font-weight-bold text-dark"><?php echo $dosen['nama']; ?></span>
                                        </div>
                                    </td>
                                    
                                    <td><span class="text-muted"><?php echo $dosen['nidk']; ?></span></td>
                                    
                                    <td class="text-center">
                                        <?php if($dosen['total_aksi'] > 0): ?>
                                            <span class="badge badge-success px-3 py-1 shadow-sm">
                                                <?php echo $dosen['total_aksi']; ?> Koreksi
                                            </span>
                                        <?php else: ?>
                                            <span class="badge badge-light text-muted border px-3 py-1">0</span>
                                        <?php endif; ?>
                                    </td>
                                    
                                    <td class="text-center">
                                        <button type="button" class="btn btn-default btn-sm border" data-toggle="modal" data-target="#modal-history-<?php echo $dosen['id']; ?>">
                                            <i class="fas fa-eye text-warning"></i>
                                        </button>

                                        <div class="modal fade" id="modal-history-<?php echo $dosen['id']; ?>" tabindex="-1" aria-hidden="true">
                                            <div class="modal-dialog modal-dialog-centered modal-sm">
                                                <div class="modal-content border-0 shadow-lg">
                                                    <div class="modal-header bg-warning border-0">
                                                        <h6 class="modal-title font-weight-bold text-dark">Aktivitas Dosen</h6>
                                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                            <span aria-hidden="true">&times;</span>
                                                        </button>
                                                    </div>
                                                    <div class="modal-body pt-2 text-left">
                                                        <div class="text-center mb-3 mt-2">
                                                            <h6 class="font-weight-bold text-truncate px-3 mb-0"><?php echo $dosen['nama']; ?></h6>
                                                            <small class="text-muted"><?php echo $dosen['nidk']; ?></small>
                                                        </div>
                                                        
                                                        <div class="list-group list-group-flush border-top" style="max-height: 250px; overflow-y: auto;">
                                                            <?php if (empty($dosen['aktivitas'])): ?>
                                                                <div class="list-group-item text-center text-muted small py-4">
                                                                    Belum ada aktivitas.
                                                                </div>
                                                            <?php else: ?>
                                                                <?php foreach ($dosen['aktivitas'] as $act): ?>
                                                                <div class="list-group-item d-flex justify-content-between align-items-center py-2 px-3">
                                                                    <span class="text-muted small">
                                                                        <?php echo date('d M Y', strtotime($act['tanggal'])); ?>
                                                                    </span>
                                                                    <span class="badge badge-warning badge-pill"><?php echo $act['total_aksi']; ?></span>
                                                                </div>
                                                                <?php endforeach; ?>
                                                            <?php endif; ?>
                                                        </div>
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

                <div class="card-footer bg-white py-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <small class="text-muted">
                            Halaman <b><?php echo $this->input->get('page') ? ($this->input->get('page') / $per_page) + 1 : 1; ?></b> 
                            dari <b><?php echo ceil($total_rows / $per_page); ?></b>
                        </small>
                        <nav>
                            <?php echo $pagination; ?>
                        </nav>
                    </div>
                </div>

            </div>

        </div>
    </section>
</div>