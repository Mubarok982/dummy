<div class="content-wrapper">
    
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0 text-dark">Persetujuan Judul & Pembimbing</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="<?php echo base_url('dashboard'); ?>">Home</a></li>
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
                                        <th class="sortable" data-sort="npm" style="width: 8%;">NPM</th>
                                        <th class="sortable text-left" data-sort="nama" style="width: 15%;">Nama Mahasiswa</th>
                                        <th class="sortable text-left" data-sort="judul" style="width: 25%;">Judul Skripsi</th>
                                        <th class="text-left" style="width: 25%;">Pembimbing</th>
                                        <th class="sortable" data-sort="status_acc_kaprodi" style="width: 10%;">Status</th>
                                        <th style="width: 12%;">Aksi</th>
                                    </tr>
                                </thead>
                             <tbody>
                                    <?php if (empty($mahasiswa)): ?>
                                        <tr>
                                            <td colspan="7" class="text-center py-5 text-muted">
                                                <i class="fas fa-users-slash fa-3x mb-3 text-gray-300"></i><br>
                                                Belum ada data pengajuan.
                                            </td>
                                        </tr>
                                    <?php else: ?>
                                        <?php 
                                        $no = isset($start_index) ? $start_index + 1 : 1; 
                                        foreach ($mahasiswa as $m): 
                                        ?>
                                        <tr>
                                            <td class="align-middle text-center font-weight-bold text-muted"><?php echo $no++; ?></td>
                                            <td class="align-middle text-center"><span class="badge badge-light border px-2 py-1"><?php echo $m['npm']; ?></span></td>
                                            <td class="align-middle">
                                                <span class="font-weight-bold text-dark text-sm"><?php echo $m['nama']; ?></span>
                                            </td>
                                            
                                            <td class="align-middle text-wrap" style="min-width: 200px; max-width: 250px; line-height: 1.2;">
                                                <?php if($m['judul']): ?>
                                                    <span class="text-sm font-weight-bold text-dark"><?php echo $m['judul']; ?></span>
                                                <?php else: ?>
                                                    <span class="badge badge-danger"><i class="fas fa-times-circle mr-1"></i> Belum Ada Judul</span>
                                                <?php endif; ?>
                                            </td>

                                            <td class="align-middle text-sm" style="min-width: 200px;">
                                                <div class="text-muted mb-1 pb-1 border-bottom text-truncate">
                                                    <strong><i class="fas fa-user-tie text-primary mr-1"></i> P1:</strong> <?php echo $m['p1'] ?: '-'; ?>
                                                </div>
                                                <div class="text-muted text-truncate">
                                                    <strong><i class="fas fa-user-tie text-secondary mr-1"></i> P2:</strong> <?php echo $m['p2'] ?: '-'; ?>
                                                </div>
                                            </td>

                                            <td class="align-middle text-center">
                                                <?php if($m['id_skripsi']): ?>
                                                    <?php if($m['status_acc_kaprodi'] == 'menunggu'): ?>
                                                        <div class="d-flex flex-row justify-content-center" style="gap: 4px;">
                                                            <a href="<?= base_url('operator/setuju_judul/'.$m['id_skripsi']) ?>" class="btn btn-sm btn-success px-2 py-1 shadow-sm" onclick="return confirm('Lanjutkan ACC?')" title="Setujui">
                                                                <i class="fas fa-check"></i>
                                                            </a>
                                                            <a href="<?= base_url('operator/tolak_judul/'.$m['id_skripsi']) ?>" class="btn btn-sm btn-danger px-2 py-1 shadow-sm" onclick="return confirm('Tolak Judul?')" title="Tolak">
                                                                <i class="fas fa-times"></i>
                                                            </a>
                                                        </div>
                                                    <?php else: ?>
                                                        <span class="badge badge-<?= ($m['status_acc_kaprodi'] == 'diterima') ? 'success' : 'danger' ?> px-2 py-1 shadow-sm" style="font-size: 0.75rem;">
                                                            <?= strtoupper($m['status_acc_kaprodi']) ?>
                                                        </span>
                                                    <?php endif; ?>
                                                <?php else: ?>
                                                    <span class="text-muted font-italic text-sm">Belum Input</span>
                                                <?php endif; ?>
                                            </td>

                                            <td class="align-middle text-center">
                                                <?php if($m['id_skripsi']): ?>
                                                    <div class="d-flex flex-row justify-content-center" style="gap: 4px;">
                                                        <button type="button" class="btn btn-info btn-sm px-2 py-1 shadow-sm" data-toggle="modal" data-target="#modalDetail<?= $m['id_skripsi']; ?>" title="Lihat Detail & Alasan">
                                                            <i class="fas fa-eye"></i>
                                                        </button>
                                                        <button type="button" class="btn btn-warning btn-sm px-2 py-1 shadow-sm" data-toggle="modal" data-target="#modalEditPembimbing<?= $m['id_skripsi']; ?>" title="Edit Pembimbing">
                                                            <i class="fas fa-edit"></i>
                                                        </button>
                                                    </div>
                                                <?php else: ?>
                                                    <span class="text-muted font-italic text-sm">-</span>
                                                <?php endif; ?>
                                            </td>
                                        </tr>

                                        <div class="modal fade" id="modalDetail<?= $m['id_skripsi']; ?>" tabindex="-1" aria-hidden="true">
                                            <div class="modal-dialog modal-lg modal-dialog-centered">
                                                <div class="modal-content border-0 shadow-lg text-left">
                                                    <div class="modal-header bg-info">
                                                        <h5 class="modal-title text-white font-weight-bold"><i class="fas fa-info-circle mr-2"></i> Detail Pengajuan Judul</h5>
                                                        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                                                            <span aria-hidden="true">&times;</span>
                                                        </button>
                                                    </div>
                                                    <div class="modal-body bg-light p-4">
                                                        
                                                        <div class="bg-white p-3 rounded border shadow-sm mb-3">
                                                            <div class="row">
                                                                <div class="col-sm-3 text-muted font-weight-bold small text-uppercase">Mahasiswa</div>
                                                                <div class="col-sm-9 font-weight-bold text-dark"><?= $m['nama']; ?> (<?= $m['npm']; ?>)</div>
                                                            </div>
                                                            <hr class="my-2">
                                                            <div class="row">
                                                                <div class="col-sm-3 text-muted font-weight-bold small text-uppercase">Tgl Pengajuan</div>
                                                                <div class="col-sm-9 text-dark"><?= date('d F Y - H:i', strtotime($m['tgl_pengajuan_judul'] ?? date('Y-m-d H:i'))); ?> WIB</div>
                                                            </div>
                                                        </div>

                                                        <div class="bg-white p-4 rounded border shadow-sm mb-3">
                                                            <h6 class="text-info font-weight-bold mb-2 border-bottom pb-2"><i class="fas fa-book mr-1"></i> Data Penelitian</h6>
                                                            <h5 class="font-weight-bold text-dark mt-3" style="line-height: 1.4;"><?= $m['judul']; ?></h5>
                                                            <span class="badge badge-info mt-1 px-2 py-1"><i class="fas fa-tag mr-1"></i> <?= isset($m['tema']) ? $m['tema'] : '-'; ?></span>
                                                        </div>

                                                        <div class="row">
                                                            <div class="col-md-6 mb-3 mb-md-0">
                                                                <div class="card h-100 border-primary shadow-sm m-0">
                                                                    <div class="card-header bg-white border-bottom-primary pb-2 pt-3">
                                                                        <small class="text-muted font-weight-bold text-uppercase d-block mb-1">Usulan Pembimbing 1</small>
                                                                        <h6 class="text-primary font-weight-bold m-0"><i class="fas fa-user-tie mr-1"></i> <?= $m['p1'] ?: '-'; ?></h6>
                                                                    </div>
                                                                    <div class="card-body bg-light p-3">
                                                                        <small class="text-muted font-weight-bold d-block mb-1">Alasan Pemilihan:</small>
                                                                        <div class="bg-white p-2 rounded border" style="min-height: 80px;">
                                                                            <span class="font-italic text-sm text-dark">"<?= isset($m['alasan_p1']) && !empty($m['alasan_p1']) ? nl2br(htmlspecialchars($m['alasan_p1'])) : 'Tidak ada alasan tertulis.'; ?>"</span>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>

                                                            <div class="col-md-6">
                                                                <div class="card h-100 border-secondary shadow-sm m-0">
                                                                    <div class="card-header bg-white border-bottom-secondary pb-2 pt-3">
                                                                        <small class="text-muted font-weight-bold text-uppercase d-block mb-1">Usulan Pembimbing 2</small>
                                                                        <h6 class="text-secondary font-weight-bold m-0"><i class="fas fa-user-tie mr-1"></i> <?= $m['p2'] ?: '-'; ?></h6>
                                                                    </div>
                                                                    <div class="card-body bg-light p-3">
                                                                        <small class="text-muted font-weight-bold d-block mb-1">Alasan Pemilihan:</small>
                                                                        <div class="bg-white p-2 rounded border" style="min-height: 80px;">
                                                                            <span class="font-italic text-sm text-dark">"<?= isset($m['alasan_p2']) && !empty($m['alasan_p2']) ? nl2br(htmlspecialchars($m['alasan_p2'])) : 'Tidak ada alasan tertulis.'; ?>"</span>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>

                                                    </div>
                                                    <div class="modal-footer bg-white border-top-0">
                                                        <button type="button" class="btn btn-secondary px-4 font-weight-bold" data-dismiss="modal">Tutup</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="modal fade" id="modalEditPembimbing<?= $m['id_skripsi']; ?>" tabindex="-1" aria-hidden="true">
                                            <div class="modal-dialog modal-dialog-centered">
                                                <div class="modal-content text-left shadow-lg border-0">
                                                    <div class="modal-header bg-warning">
                                                        <h5 class="modal-title text-dark font-weight-bold"><i class="fas fa-user-edit mr-2"></i> Ganti Pembimbing</h5>
                                                        <button type="button" class="close text-dark" data-dismiss="modal" aria-label="Close">
                                                            <span aria-hidden="true">&times;</span>
                                                        </button>
                                                    </div>
                                                    <form action="<?= base_url('operator/update_pembimbing'); ?>" method="POST">
                                                        <div class="modal-body bg-light p-4">
                                                            <input type="hidden" name="id_skripsi" value="<?= $m['id_skripsi']; ?>">
                                                            
                                                            <div class="alert alert-info border-info shadow-sm text-sm">
                                                                <i class="fas fa-info-circle mr-1"></i> Jika Anda merasa usulan dosen dari mahasiswa tidak relevan dengan alasannya, silakan ubah di sini sebelum melakukan ACC.
                                                            </div>

                                                            <div class="form-group bg-white p-3 rounded border shadow-sm">
                                                                <label class="text-primary"><i class="fas fa-user mr-1"></i> Pembimbing 1 Baru</label>
                                                                <select name="pembimbing1" class="form-control select2" style="width: 100%;" required>
                                                                    <option value="">-- Pilih Dosen --</option>
                                                                    <?php if(isset($dosen_list)): foreach($dosen_list as $d): ?>
                                                                        <option value="<?= $d['id']; ?>" <?= ($d['nama'] == $m['p1']) ? 'selected' : ''; ?>>
                                                                            <?= $d['nama']; ?>
                                                                        </option>
                                                                    <?php endforeach; endif; ?>
                                                                </select>
                                                            </div>

                                                            <div class="form-group bg-white p-3 rounded border shadow-sm mb-0">
                                                                <label class="text-secondary"><i class="fas fa-user mr-1"></i> Pembimbing 2 Baru</label>
                                                                <select name="pembimbing2" class="form-control select2" style="width: 100%;" required>
                                                                    <option value="">-- Pilih Dosen --</option>
                                                                    <?php if(isset($dosen_list)): foreach($dosen_list as $d): ?>
                                                                        <option value="<?= $d['id']; ?>" <?= ($d['nama'] == $m['p2']) ? 'selected' : ''; ?>>
                                                                            <?= $d['nama']; ?>
                                                                        </option>
                                                                    <?php endforeach; endif; ?>
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="modal-footer bg-white justify-content-between">
                                                            <button type="button" class="btn btn-secondary font-weight-bold" data-dismiss="modal">Batalkan</button>
                                                            <button type="submit" class="btn btn-warning font-weight-bold px-4 shadow-sm"><i class="fas fa-save mr-1"></i> Simpan Perubahan</button>
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
                        <div class="card-footer py-2 bg-white border-top">
                            <div class="row align-items-center">
                                <div class="col-sm-6 text-muted small">
                                    Total Data Pengajuan: <b class="text-dark"><?php echo isset($total_rows) ? $total_rows : 0; ?></b>
                                </div>
                                <div class="col-sm-6">
                                    <div class="float-right m-0">
                                        <?php echo isset($pagination) ? $pagination : ''; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-footer bg-light text-muted text-sm text-center">
                            <i class="fas fa-lightbulb text-warning mr-1"></i> <b>Tips:</b> Gunakan tombol <b>Detail</b> untuk melihat argumen mahasiswa sebelum menekan tombol ACC.
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
    .border-left-info { border-left: 4px solid #6f42c1 !important; }
    .table-head-fixed th { background-color: #f8f9fa; border-bottom: 2px solid #dee2e6; z-index: 10; }
    .border-bottom-primary { border-bottom: 3px solid #007bff !important; }
    .border-bottom-secondary { border-bottom: 3px solid #6c757d !important; }
</style>