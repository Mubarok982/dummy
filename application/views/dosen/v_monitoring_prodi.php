<div class="content-wrapper">
    
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0 text-dark">Monitoring Mahasiswa Prodi</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="<?php echo base_url('dashboard'); ?>">Home</a></li>
                        <li class="breadcrumb-item active">Monitoring Prodi</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">
            
            <?php if ($this->session->flashdata('pesan_sukses')): ?>
                <div class="alert alert-success alert-dismissible fade show shadow-sm">
                    <button type="button" class="close" data-dismiss="alert">&times;</button>
                    <i class="fas fa-check mr-1"></i> <?= $this->session->flashdata('pesan_sukses'); ?>
                </div>
            <?php endif; ?>

            <?php if ($this->session->flashdata('pesan_error')): ?>
                <div class="alert alert-danger alert-dismissible fade show shadow-sm">
                    <button type="button" class="close" data-dismiss="alert">&times;</button>
                    <i class="fas fa-ban mr-1"></i> <?= $this->session->flashdata('pesan_error'); ?>
                </div>
            <?php endif; ?>

            <div class="row mb-3">
                <div class="col-md-12">
                    <div class="callout callout-info shadow-sm bg-white border-left-info">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h5 class="text-info font-weight-bold mb-1">
                                    <i class="fas fa-university mr-2"></i> Program Studi: <?php echo $this->session->userdata('prodi'); ?>
                                </h5>
                                <p class="text-muted text-sm mb-0">Memantau progres skripsi seluruh mahasiswa angkatan aktif.</p>
                            </div>
                            <div class="text-right d-none d-md-block">
                                 <h3 class="mb-0 text-dark font-weight-bold"><?php echo count($mahasiswa_prodi); ?></h3>
                                 <small class="text-muted">Total Mahasiswa</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-12">
                    <div class="card card-outline card-purple shadow-sm">
                        <div class="card-header">
                            <h3 class="card-title mt-2"><i class="fas fa-list-alt mr-1"></i> Data Mahasiswa</h3>
                            
                            <div class="card-tools d-flex align-items-center">
                                <!-- Angkatan filter removed as requested -->

                                <div class="input-group input-group-sm" style="width: 200px;">
                                    <input type="text" id="searchMhs" class="form-control float-right" placeholder="Cari Nama / NPM...">
                                    <div class="input-group-append">
                                        <button type="submit" class="btn btn-default"><i class="fas fa-search"></i></button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="card-body table-responsive p-0" style="height: 550px;">
                            <table class="table table-head-fixed table-hover text-nowrap table-striped align-middle" id="tableMonitoring">
                                <thead>
                                    <tr class="text-center">
                                        <th style="width: 5%;">No</th>
                                        <th style="width: 10%;" class="sortable" data-sort="npm">NPM</th>
                                        <th class="text-left sortable" style="width: 20%;" data-sort="nama">Nama Mahasiswa</th>
                                        <th style="width: 10%;" class="sortable" data-sort="angkatan">Angkatan</th>
                                        <th class="text-left sortable" style="width: 25%;" data-sort="judul">Judul Skripsi</th>
                                        <th class="text-left" style="width: 20%;">Pembimbing</th>
                                        <th style="width: 10%;">Aksi / Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (empty($mahasiswa_prodi)): ?>
                                        <tr>
                                            <td colspan="7" class="text-center py-5 text-muted">
                                                <i class="fas fa-users-slash fa-3x mb-3 text-gray-300"></i><br>
                                                Tidak ada data mahasiswa untuk filter ini.
                                            </td>
                                        </tr>
                                    <?php else: ?>
                                        <?php $no = 1; foreach ($mahasiswa_prodi as $m): ?>
                                        <tr>
                                            <td class="align-middle text-center"><?php echo $no++; ?></td>
                                            <td class="align-middle text-center"><span class="badge badge-light border"><?php echo $m['npm']; ?></span></td>
                                            <td class="align-middle font-weight-bold text-dark"><?php echo $m['nama']; ?></td>
                                            <td class="align-middle text-center"><?php echo $m['angkatan']; ?></td>
                                            
                                            <td class="align-middle text-wrap" style="min-width: 300px; max-width: 400px;">
                                                <?php if($m['judul']): ?>
                                                    <span class="text-sm font-italic"><?php echo $m['judul']; ?></span>
                                                <?php else: ?>
                                                    <span class="badge badge-danger"><i class="fas fa-times-circle mr-1"></i> Belum Ada Judul</span>
                                                <?php endif; ?>
                                            </td>

                                            <td class="align-middle text-sm">
                                                <div class="text-muted mb-1 d-flex justify-content-between">
                                                    <span><i class="fas fa-user-tie text-primary mr-1"></i> <b>P1:</b> <?php echo $m['p1'] ?: '-'; ?></span>
                                                </div>
                                                <div class="text-muted d-flex justify-content-between">
                                                    <span><i class="fas fa-user-tie text-secondary mr-1"></i> <b>P2:</b> <?php echo $m['p2'] ?: '-'; ?></span>
                                                </div>
                                                
                                                <?php if($m['id_skripsi']): ?>
                                                <a href="<?php echo base_url('dosen/edit_dospem/' . $m['id_skripsi']); ?>" class="btn btn-xs btn-outline-warning mt-2 btn-block">
                                                    <i class="fas fa-edit mr-1"></i> Ganti Pembimbing
                                                </a>
                                                <?php endif; ?>
                                            </td>

                                            <td class="align-middle text-center">
                                                <?php if($m['id_skripsi']): ?>
                                                    <div class="btn-group">
                                                        <?php if($m['status_acc_kaprodi'] == 'menunggu'): ?>
                                                            <a href="<?= base_url('dosen/setuju_judul/'.$m['id_skripsi']) ?>" class="btn btn-xs btn-success shadow-sm" onclick="return confirm('Setujui Judul & Pembimbing?')" title="Setujui">
                                                                <i class="fas fa-check"></i>
                                                            </a>
                                                            <a href="<?= base_url('dosen/tolak_judul/'.$m['id_skripsi']) ?>" class="btn btn-xs btn-danger shadow-sm ml-1" onclick="return confirm('Tolak Judul?')" title="Tolak">
                                                                <i class="fas fa-times"></i>
                                                            </a>
                                                        <?php else: ?>
                                                            <span class="badge badge-<?= ($m['status_acc_kaprodi'] == 'diterima') ? 'success' : 'danger' ?> p-2">
                                                                <?= strtoupper($m['status_acc_kaprodi']) ?>
                                                            </span>
                                                        <?php endif; ?>
                                                        
                                                        <a href="<?= base_url('chat?id_lawan='.$m['id_user']) ?>" class="btn btn-xs btn-info shadow-sm ml-2" title="Chat Mahasiswa">
                                                            <i class="fas fa-comment"></i>
                                                        </a>
                                                    </div>
                                                <?php else: ?>
                                                    <span class="text-muted font-italic text-sm">Menunggu Input</span>
                                                <?php endif; ?>
                                            </td>
                                        </tr>


                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </section>
</div>

<script>
document.addEventListener("DOMContentLoaded", function() {
    var searchInput = document.getElementById('searchMhs');
    if(searchInput){
        searchInput.addEventListener('keyup', function() {
            var val = this.value.toLowerCase();
            var rows = document.querySelectorAll('#tableMonitoring tbody tr');
            
            rows.forEach(function(row) {
                var text = row.textContent.toLowerCase();
                row.style.display = text.indexOf(val) > -1 ? '' : 'none';
            });
        });
    }
});
</script>

<style>
    .border-left-info { border-left: 4px solid #17a2b8 !important; }
    .table-head-fixed th { background-color: #f8f9fa; border-bottom: 2px solid #dee2e6; }
</style>