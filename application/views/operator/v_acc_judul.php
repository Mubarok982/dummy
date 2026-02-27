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
                                <p class="text-muted text-sm mb-0">Halaman ini khusus untuk memvalidasi (ACC), menolak, atau <b>mengganti dosen pembimbing</b> mahasiswa.</p>
                            </div>
                            <div class="text-right d-none d-md-block">
                                 <h3 class="mb-0 text-dark font-weight-bold"><?php echo isset($total_rows) ? $total_rows : count($mahasiswa); ?></h3>
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
                        
                        <div class="card-body">
                            <form method="GET" action="<?php echo base_url('operator/acc_judul'); ?>" class="mb-3">
                                <div class="row">
                                    <div class="col-md-4">
                                        <input type="text" name="keyword" class="form-control" placeholder="Cari nama/NPM/judul..." value="<?php echo isset($keyword) ? $keyword : ''; ?>">
                                    </div>
                                    <div class="col-md-3">
                                        <select name="status" class="form-control">
                                            <option value="all">Semua Status</option>
                                            <?php if(isset($list_status_acc)): ?>
                                                <?php foreach ($list_status_acc as $status_option): ?>
                                                    <option value="<?php echo $status_option['status']; ?>" <?php echo (isset($status) && $status == $status_option['status']) ? 'selected' : ''; ?>><?php echo $status_option['status']; ?></option>
                                                <?php endforeach; ?>
                                            <?php endif; ?>
                                        </select>
                                    </div>
                                    <div class="col-md-3">
                                        <select name="prodi" class="form-control">
                                            <option value="all">Semua Prodi</option>
                                            <?php if(isset($list_prodi)): ?>
                                                <?php foreach ($list_prodi as $prodi_option): ?>
                                                    <option value="<?php echo $prodi_option['prodi']; ?>" <?php echo (isset($prodi) && $prodi == $prodi_option['prodi']) ? 'selected' : ''; ?>><?php echo $prodi_option['prodi']; ?></option>
                                                <?php endforeach; ?>
                                            <?php endif; ?>
                                        </select>
                                    </div>
                                    <div class="col-md-2">
                                        <button type="submit" class="btn btn-primary btn-block"><i class="fas fa-search"></i> Filter</button>
                                    </div>
                                </div>
                            </form>

                            <div class="table-responsive" style="height: 600px;">
                            <table class="table table-head-fixed table-hover text-nowrap table-striped align-middle" id="tableAcc">
                                <thead>
                                <tr class="text-center">
                                        <th style="width: 5%;">No</th>
                                        <th style="width: 10%;" class="sortable" data-sort="npm">NPM</th>
                                        <th class="text-left sortable" style="width: 20%;" data-sort="nama">Nama Mahasiswa</th>
                                        <th class="text-left sortable" style="width: 25%;" data-sort="judul">Judul Skripsi</th>
                                        <th class="text-left" style="width: 20%;">Pembimbing</th>
                                        <th style="width: 10%;">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php 
                                    // ============================================================
                                    // LOGIKA ANTI-DUPLIKAT: Menyaring entri ganda dari database
                                    // Hanya mengambil ID Skripsi terbesar (Terbaru) untuk tiap NPM
                                    // ============================================================
                                    $filtered_mahasiswa = [];
                                    if (!empty($mahasiswa)) {
                                        $temp_mhs = [];
                                        foreach ($mahasiswa as $m) {
                                            $npm = $m['npm'];
                                            // Jika NPM belum dicatat, ATAU jika sudah dicatat tapi baris yang sedang dilooping ini 
                                            // punya id_skripsi yang lebih besar (lebih baru), maka timpa data lamanya!
                                            if (!isset($temp_mhs[$npm]) || $m['id_skripsi'] > $temp_mhs[$npm]['id_skripsi']) {
                                                $temp_mhs[$npm] = $m;
                                            }
                                        }
                                        // Kembalikan menjadi array berindeks angka biasa
                                        $filtered_mahasiswa = array_values($temp_mhs);
                                    }
                                    ?>

                                    <?php if (empty($filtered_mahasiswa)): ?>
                                        <tr>
                                            <td colspan="7" class="text-center py-5 text-muted">
                                                <i class="fas fa-users-slash fa-3x mb-3 text-gray-300"></i><br>
                                                Belum ada data pengajuan.
                                            </td>
                                        </tr>
                                    <?php else: ?>
                                        <?php $no = 1; foreach ($filtered_mahasiswa as $m): ?>
                                        <tr>
                                            <td class="align-middle text-center"><?php echo $no++; ?></td>
                                            <td class="align-middle text-center"><span class="badge badge-light border"><?php echo $m['npm']; ?></span></td>
                                            <td class="align-middle">
                                                <span class="font-weight-bold text-dark"><?php echo $m['nama']; ?></span><br>
                                                <small class="text-muted">Angkatan: <?php echo isset($m['angkatan']) ? $m['angkatan'] : '-'; ?></small>
                                            </td>
                                            
                                            <td class="align-middle text-wrap" style="min-width: 250px; max-width: 400px;">
                                                <?php if($m['judul']): ?>
                                                    <span class="text-sm font-italic"><?php echo $m['judul']; ?></span>
                                                <?php else: ?>
                                                    <span class="badge badge-danger"><i class="fas fa-times-circle mr-1"></i> Belum Ada Judul</span>
                                                <?php endif; ?>
                                            </td>

                                            <td class="align-middle text-sm">
                                                <div class="text-muted mb-1 d-flex justify-content-between">
                                                    <span><i class="fas fa-user-tie text-primary mr-1"></i> P1: <?php echo $m['p1'] ?: '-'; ?></span>
                                                </div>
                                                <div class="text-muted d-flex justify-content-between">
                                                    <span><i class="fas fa-user-tie text-secondary mr-1"></i> P2: <?php echo $m['p2'] ?: '-'; ?></span>
                                                </div>
                                            </td>

                                            <td class="align-middle text-center">
                                                <?php if($m['id_skripsi']): ?>
                                                    <div class="btn-group">
                                                        
                                                        <?php if($m['status_acc_kaprodi'] == 'menunggu'): ?>
                                                            <a href="<?= base_url('operator/setuju_judul/'.$m['id_skripsi']) ?>" class="btn btn-sm btn-success shadow-sm mb-1" onclick="return confirm('Pastikan pembimbing sudah benar. Lanjutkan ACC?')" title="ACC">
                                                                <i class="fas fa-check mr-1"></i> Setujui
                                                            </a>
                                                            <a href="<?= base_url('operator/tolak_judul/'.$m['id_skripsi']) ?>" class="btn btn-sm btn-danger shadow-sm" onclick="return confirm('Tolak Judul?')" title="Tolak">
                                                                <i class="fas fa-times mr-1"></i> Tolak
                                                            </a>
                                                        <?php else: ?>
                                                            <span class="badge badge-<?= ($m['status_acc_kaprodi'] == 'diterima') ? 'success' : 'danger' ?> p-2 mb-1">
                                                                STATUS: <?= strtoupper($m['status_acc_kaprodi']) ?>
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

                                        <div class="modal fade" id="modalEditPembimbing<?= $m['id_skripsi']; ?>" tabindex="-1" aria-hidden="true">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header bg-warning">
                                                        <h5 class="modal-title"><i class="fas fa-user-edit mr-2"></i> Ganti Pembimbing</h5>
                                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                            <span aria-hidden="true">&times;</span>
                                                        </button>
                                                    </div>
                                                    <form action="<?= base_url('operator/update_pembimbing'); ?>" method="POST">
                                                        <div class="modal-body">
                                                            <input type="hidden" name="id_skripsi" value="<?= $m['id_skripsi']; ?>">
                                                            
                                                            <div class="form-group">
                                                                <label>Pembimbing 1</label>
                                                                <select name="pembimbing1" class="form-control select2" style="width: 100%;" required>
                                                                    <option value="">-- Pilih Dosen --</option>
                                                                    <?php if(isset($dosen_list)): foreach($dosen_list as $d): ?>
                                                                        <option value="<?= $d['id']; ?>" <?= ($d['nama'] == $m['p1']) ? 'selected' : ''; ?>>
                                                                            <?= $d['nama']; ?>
                                                                        </option>
                                                                    <?php endforeach; endif; ?>
                                                                </select>
                                                            </div>

                                                            <div class="form-group">
                                                                <label>Pembimbing 2</label>
                                                                <select name="pembimbing2" class="form-control select2" style="width: 100%;" required>
                                                                    <option value="">-- Pilih Dosen --</option>
                                                                    <?php if(isset($dosen_list)): foreach($dosen_list as $d): ?>
                                                                        <option value="<?= $d['id']; ?>" <?= ($d['nama'] == $m['p2']) ? 'selected' : ''; ?>>
                                                                            <?= $d['nama']; ?>
                                                                        </option>
                                                                    <?php endforeach; endif; ?>
                                                                </select>
                                                            </div>
                                                            
                                                            <div class="alert alert-light text-sm border">
                                                                <i class="fas fa-info-circle text-info"></i> Pastikan Pembimbing 1 dan 2 berbeda.
                                                            </div>
                                                        </div>
                                                        <div class="modal-footer justify-content-between">
                                                            <button type="button" class="btn btn-default" data-dismiss="modal">Batal</button>
                                                            <button type="submit" class="btn btn-primary"><i class="fas fa-save mr-1"></i> Simpan Perubahan</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                        <div class="card-footer py-2 bg-white">
                            <div class="row align-items-center">
                                <div class="col-sm-6 text-muted small">
                                    Total Data: <b><?php echo count($filtered_mahasiswa); ?></b>
                                </div>
                                <div class="col-sm-6">
                                    <div class="float-right">
                                        <?php echo isset($pagination) ? $pagination : ''; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-footer bg-white text-muted text-sm">
                            <i class="fas fa-info-circle mr-1"></i> Anda dapat mengganti pembimbing sebelum atau sesudah menyetujui (ACC) judul.
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