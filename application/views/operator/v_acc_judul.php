<div class="content-wrapper">
    
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0 text-dark">Persetujuan Judul & Pembimbing</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="<?php echo base_url('operator/dashboard'); ?>">Home</a></li>
                        <li class="breadcrumb-item active">ACC Judul</li>
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
                                    <i class="fas fa-tasks mr-2"></i> Konfirmasi Pengajuan Judul
                                </h5>
                                <p class="text-muted text-sm mb-0">Halaman ini khusus untuk memvalidasi (ACC) atau menolak pengajuan judul dan dosen pembimbing dari mahasiswa.</p>
                            </div>
                            <div class="text-right d-none d-md-block">
                                 <h3 class="mb-0 text-dark font-weight-bold"><?php echo count($mahasiswa); ?></h3>
                                 <small class="text-muted">Total Data</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-12">
                    <div class="card card-outline card-purple shadow-sm">
                        <div class="card-header">
                            <h3 class="card-title mt-2"><i class="fas fa-list-alt mr-1"></i> Daftar Pengajuan</h3>
                            
                            <div class="card-tools">
                                <div class="input-group input-group-sm" style="width: 250px;">
                                    <input type="text" id="searchMhs" class="form-control float-right" placeholder="Cari Nama / NPM / Judul...">
                                    <div class="input-group-append">
                                        <button type="submit" class="btn btn-default"><i class="fas fa-search"></i></button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="card-body table-responsive p-0" style="height: 600px;">
                            <table class="table table-head-fixed table-hover text-nowrap table-striped align-middle" id="tableAcc">
                                <thead>
                                <tr class="text-center">
                                        <th style="width: 5%;">No</th>
                                        <th style="width: 10%;">NPM</th>
                                        <th class="text-left" style="width: 20%;">Nama Mahasiswa</th>
                                        <th style="width: 10%;">Angkatan</th>
                                        <th class="text-left" style="width: 25%;">Judul Skripsi</th>
                                        <th class="text-left" style="width: 20%;">Pembimbing</th>
                                        <th style="width: 10%;">Aksi</th>
                                        <th style="width: 10%;">Edit</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (empty($mahasiswa)): ?>
                                        <tr>
                                            <td colspan="8" class="text-center py-5 text-muted">
                                                <i class="fas fa-users-slash fa-3x mb-3 text-gray-300"></i><br>
                                                Belum ada data pengajuan.
                                            </td>
                                        </tr>
                                    <?php else: ?>
                                        <?php $no = 1; foreach ($mahasiswa as $m): ?>
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
                                                <div class="text-muted mb-1"><i class="fas fa-user-tie text-primary mr-1"></i> <b>P1:</b> <?php echo $m['p1'] ?: '-'; ?></div>
                                                <div class="text-muted"><i class="fas fa-user-tie text-secondary mr-1"></i> <b>P2:</b> <?php echo $m['p2'] ?: '-'; ?></div>
                                            </td>

                                            <td class="align-middle text-center">
                                                <?php if($m['id_skripsi']): ?>
                                                    <div class="btn-group">

                                                        <?php if($m['status_acc_kaprodi'] == 'menunggu'): ?>
                                                            <a href="<?= base_url('operator/setuju_judul/'.$m['id_skripsi']) ?>" class="btn btn-sm btn-success shadow-sm" onclick="return confirm('Setujui Judul & Pembimbing?')" title="ACC">
                                                                <i class="fas fa-check"></i> ACC
                                                            </a>
                                                            <a href="<?= base_url('operator/tolak_judul/'.$m['id_skripsi']) ?>" class="btn btn-sm btn-danger shadow-sm ml-1" onclick="return confirm('Tolak Judul?')" title="Tolak">
                                                                <i class="fas fa-times"></i>
                                                            </a>
                                                        <?php else: ?>
                                                            <span class="badge badge-<?= ($m['status_acc_kaprodi'] == 'diterima') ? 'success' : 'danger' ?> p-2">
                                                                <?= strtoupper($m['status_acc_kaprodi']) ?>
                                                            </span>
                                                        <?php endif; ?>
                                                    </div>
                                                <?php else: ?>
                                                    <span class="text-muted font-italic text-sm">Menunggu Input</span>
                                                <?php endif; ?>
                                            </td>

                                            <td class="align-middle text-center">
                                                <?php if($m['id_skripsi']): ?>
                                                    <a href="<?php echo base_url('operator/edit_dospem/' . $m['id_skripsi']); ?>"
                                                       class="btn btn-warning btn-sm shadow-sm">
                                                        <i class="fas fa-edit mr-1"></i> Edit
                                                    </a>
                                                <?php else: ?>
                                                    <span class="text-muted font-italic text-sm">-</span>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                        <div class="card-footer bg-white text-muted text-sm">
                            <i class="fas fa-info-circle mr-1"></i> Gunakan kolom pencarian untuk memfilter data mahasiswa.
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </section>
</div>

<script>
document.addEventListener("DOMContentLoaded", function() {
    // Fitur Pencarian Cepat
    var searchInput = document.getElementById('searchMhs');
    if(searchInput){
        searchInput.addEventListener('keyup', function() {
            var val = this.value.toLowerCase();
            var rows = document.querySelectorAll('#tableAcc tbody tr');
            
            rows.forEach(function(row) {
                var text = row.textContent.toLowerCase();
                row.style.display = text.indexOf(val) > -1 ? '' : 'none';
            });
        });
    }
});
</script>

<style>
    .border-left-info { border-left: 4px solid #6f42c1 !important; } /* Warna Purple */
    .table-head-fixed th { background-color: #f8f9fa; border-bottom: 2px solid #dee2e6; z-index: 10; }
</style>