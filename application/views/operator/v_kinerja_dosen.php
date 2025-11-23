<div class="container-fluid">
    
    <div class="card shadow-sm mb-4">
        <div class="card-body p-2">
            <form action="<?php echo base_url('operator/kinerja_dosen'); ?>" method="GET">
                <div class="d-flex justify-content-between align-items-center">
                    
                    <div class="d-flex align-items-center">
                        <span class="text-muted font-weight-bold mr-2 ml-2"><i class="fas fa-filter"></i> Filter:</span>
                        <div class="input-group input-group-sm" style="width: 250px;">
                            <input type="text" name="keyword" class="form-control" placeholder="Cari Nama Dosen..." value="<?php echo $this->input->get('keyword'); ?>">
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

                    <div class="mr-2 text-muted small">
                        Total Dosen: <b><?php echo $total_rows; ?></b>
                    </div>

                </div>
            </form>
        </div>
    </div>

    <div class="row">
        <?php if (empty($dosen_list)): ?>
            <div class="col-12 text-center py-5">
                <div class="text-muted">
                    <i class="fas fa-user-slash fa-3x mb-3"></i>
                    <h5>Data dosen tidak ditemukan.</h5>
                </div>
            </div>
        <?php else: ?>
            
            <?php foreach ($dosen_list as $dosen): ?>
            <div class="col-12 col-md-6 col-lg-4 mb-4">
                <div class="card card-outline card-primary h-100 shadow-sm hover-card">
                    
                    <div class="card-header border-bottom-0 pb-0">
                        <div class="d-flex align-items-center">
                            <div class="mr-3">
                                <img class="img-circle elevation-1" src="https://ui-avatars.com/api/?name=<?php echo urlencode($dosen['nama']); ?>&background=random&size=50" alt="Avatar">
                            </div>
                            <div style="overflow: hidden;">
                                <h6 class="mb-0 font-weight-bold text-truncate" title="<?php echo $dosen['nama']; ?>">
                                    <?php echo $dosen['nama']; ?>
                                </h6>
                                <small class="text-muted"><i class="fas fa-id-badge mr-1"></i> <?php echo $dosen['nidk']; ?></small>
                            </div>
                        </div>
                    </div>

                    <div class="card-body pt-3 px-2 pb-0">
                        <?php if (empty($dosen['aktivitas'])): ?>
                            <div class="text-center py-4 bg-light rounded mx-2 mb-3">
                                <small class="text-muted font-italic">Belum ada aktivitas koreksi.</small>
                            </div>
                        <?php else: ?>
                            <div class="table-responsive border rounded" style="height: 180px; overflow-y: auto;">
                                <table class="table table-sm table-striped table-head-fixed text-nowrap mb-0">
                                    <thead>
                                        <tr>
                                            <th class="pl-3"><small>Tanggal</small></th>
                                            <th class="text-center"><small>Aktivitas</small></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php 
                                        $total_aksi = 0;
                                        foreach ($dosen['aktivitas'] as $log): 
                                            $total_aksi += $log['total_aksi'];
                                        ?>
                                        <tr>
                                            <td class="pl-3 align-middle"><small><?php echo date('d/m/Y', strtotime($log['tanggal'])); ?></small></td>
                                            <td class="text-center align-middle">
                                                <span class="badge badge-info"><?php echo $log['total_aksi']; ?> aksi</span>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php endif; ?>
                    </div>

                    <div class="card-footer bg-white border-top mt-auto">
                        <div class="d-flex justify-content-between align-items-center">
                            <small class="text-muted font-weight-bold">TOTAL KOREKSI</small>
                            <span class="badge badge-primary p-2" style="font-size: 0.9em;">
                                <?php echo isset($total_aksi) ? $total_aksi : 0; ?> Kali
                            </span>
                        </div>
                    </div>

                </div>
            </div>
            <?php 
                unset($total_aksi); // Reset variabel untuk loop berikutnya
            endforeach; 
            ?>

        <?php endif; ?>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body py-2">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="text-muted small">
                            Halaman <b><?php echo $this->input->get('page') ? ($this->input->get('page')/$this->pagination->per_page)+1 : 1; ?></b> 
                            dari total data.
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

<style>
    /* Efek Hover Halus pada Kartu */
    .hover-card { transition: transform 0.2s, box-shadow 0.2s; }
    .hover-card:hover { transform: translateY(-3px); box-shadow: 0 5px 15px rgba(0,0,0,0.1) !important; }
    
    /* Scrollbar Custom Ramping */
    .table-responsive::-webkit-scrollbar { width: 4px; }
    .table-responsive::-webkit-scrollbar-track { background: #f1f1f1; }
    .table-responsive::-webkit-scrollbar-thumb { background: #ccc; border-radius: 4px; }
    .table-responsive::-webkit-scrollbar-thumb:hover { background: #bbb; }
</style>