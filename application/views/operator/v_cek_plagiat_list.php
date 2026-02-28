<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0 text-dark">Cek Plagiarisme BAB 1</h1>
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
            
            <?php if ($this->session->flashdata('pesan_sukses')): ?>
                <div class="alert alert-success alert-dismissible fade show shadow-sm">
                    <button type="button" class="close" data-dismiss="alert">&times;</button>
                    <i class="icon fas fa-check"></i> <?php echo $this->session->flashdata('pesan_sukses'); ?>
                </div>
            <?php endif; ?>

            <?php if ($this->session->flashdata('pesan_error')): ?>
                <div class="alert alert-danger alert-dismissible fade show shadow-sm">
                    <button type="button" class="close" data-dismiss="alert">&times;</button>
                    <i class="icon fas fa-ban"></i> <?php echo $this->session->flashdata('pesan_error'); ?>
                </div>
            <?php endif; ?>

            <div class="card card-outline card-primary shadow-sm">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-list-alt mr-1"></i> Daftar Pengajuan Cek Plagiarisme</h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-tool" data-card-widget="collapse">
                            <i class="fas fa-minus"></i>
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <form method="GET" action="<?php echo base_url('operator/cek_plagiarisme_list'); ?>" class="mb-3">
                        <div class="row">
                            <div class="col-md-4 mb-2 mb-md-0">
                                <input type="text" name="keyword" class="form-control" placeholder="Cari nama/NPM..." value="<?php echo isset($keyword) ? $keyword : ''; ?>">
                            </div>
                            <div class="col-md-3 mb-2 mb-md-0">
                                <select name="status" class="form-control">
                                    <option value="all">Semua Status</option>
                                    <option value="Menunggu" <?php echo (isset($status) && $status == 'Menunggu') ? 'selected' : ''; ?>>Menunggu</option>
                                    <option value="Lulus" <?php echo (isset($status) && $status == 'Lulus') ? 'selected' : ''; ?>>Lulus</option>
                                    <option value="Tolak" <?php echo (isset($status) && $status == 'Tolak') ? 'selected' : ''; ?>>Tolak</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <button type="submit" class="btn btn-primary btn-block shadow-sm"><i class="fas fa-search"></i> Filter</button>
                            </div>
                        </div>
                    </form>

                    <div class="table-responsive">
                        <table id="tabelPlagiasi" class="table table-hover table-striped text-nowrap align-middle">
                            <thead class="bg-light">
                                <tr class="text-center">
                                    <th width="5%">No</th>
                                    <th class="sortable" data-sort="nama">Mahasiswa</th>
                                    <th class="sortable" data-sort="judul">Judul Skripsi</th>
                                    <th class="sortable" data-sort="tgl_upload">Tanggal Upload</th>
                                    <th>File</th>
                                    <th>Status</th>
                                    <th class="sortable" data-sort="persentase_kemiripan">Nilai (%)</th>
                                    <th>Aksi / Info</th>
                                </tr>
                            </thead>
                            
                            <tbody>
                                <?php if (empty($list_plagiasi)): ?>
                                    <tr>
                                        <td colspan="8" class="text-center py-5 text-muted">
                                            <div class="mb-3"><i class="fas fa-clipboard-list fa-4x opacity-50 text-light"></i></div>
                                            <h5 class="font-weight-light">Belum ada data progres yang sesuai filter.</h5>
                                        </td>
                                    </tr>
                                <?php else: ?>
                                    <?php 
                                    $no_urut = isset($start_index) ? $start_index + 1 : 1;
                                    foreach ($list_plagiasi as $row): 
                                    ?>
                                    <tr>
                                        <td class="text-center align-middle font-weight-bold text-muted"><?php echo $no_urut++; ?></td>
                                        
                                        <td class="align-middle">
                                            <div class="user-block">
                                                <span class="username ml-0"><a href="#" class="text-dark font-weight-bold"><?php echo $row['nama']; ?></a></span>
                                                <span class="description ml-0 font-weight-normal text-muted" style="font-size: 13px;">
                                                    NPM: <span class="badge badge-light border shadow-sm"><?php echo $row['npm']; ?></span>
                                                </span>
                                            </div>
                                        </td>

                                        <td class="align-middle" style="min-width: 250px;">
                                            <span class="d-block" style="line-height: 1.3; font-size: 14px; font-weight: 500;">
                                                <?php echo !empty($row['judul']) ? ((strlen($row['judul']) > 60) ? substr($row['judul'], 0, 60).'...' : $row['judul']) : '<span class="text-muted font-italic">- Belum ada judul -</span>'; ?>
                                            </span>
                                        </td>

                                        <td class="text-center align-middle text-muted small">
                                            <?php echo !empty($row['tgl_upload']) ? date('d M Y', strtotime($row['tgl_upload'])) . '<br>' . date('H:i', strtotime($row['tgl_upload'])) : '-'; ?>
                                        </td>

                                        <td class="text-center align-middle">
                                            <a href="<?php echo base_url('uploads/progres/' . $row['progres_file']); ?>" target="_blank" class="btn btn-sm btn-outline-danger shadow-sm font-weight-bold" title="Buka Dokumen PDF">
                                                <i class="fas fa-file-pdf mr-1"></i> Buka
                                            </a>
                                        </td>

                                        <td class="text-center align-middle">
                                            <?php if($row['status_plagiasi'] == 'Menunggu'): ?>
                                                <span class="badge badge-warning text-dark px-3 py-2 shadow-sm"><i class="fas fa-clock mr-1"></i> PENDING</span>
                                            <?php elseif($row['status_plagiasi'] == 'Lulus'): ?>
                                                <span class="badge badge-success px-3 py-2 shadow-sm"><i class="fas fa-check-circle mr-1"></i> LULUS</span>
                                            <?php else: ?>
                                                <span class="badge badge-danger px-3 py-2 shadow-sm"><i class="fas fa-times-circle mr-1"></i> DITOLAK</span>
                                            <?php endif; ?>
                                        </td>

                                        <td class="text-center align-middle">
                                            <?php if($row['status_plagiasi'] == 'Menunggu'): ?>
                                                <span class="text-muted">-</span>
                                            <?php else: ?>
                                                <span class="font-weight-bold text-<?php echo ($row['status_plagiasi'] == 'Lulus') ? 'success' : 'danger'; ?>" style="font-size: 16px;">
                                                    <?php echo $row['persentase_kemiripan']; ?>%
                                                </span>
                                            <?php endif; ?>
                                        </td>

                                        <td class="text-center align-middle">
                                            <?php if($row['status_plagiasi'] == 'Menunggu'): ?>
                                                <button type="button" 
                                                        class="btn btn-primary btn-sm shadow-sm btn-verifikasi font-weight-bold px-3"
                                                        data-id="<?php echo $row['id']; ?>"
                                                        data-nama="<?php echo htmlspecialchars($row['nama'], ENT_QUOTES); ?>"
                                                        data-bab="<?php echo $row['bab']; ?>">
                                                    <i class="fas fa-edit mr-1"></i> Input Hasil
                                                </button>
                                            <?php else: ?>
                                                <small class="d-block text-muted mb-1">Diverifikasi:<br><b><?php echo date('d/m/Y H:i', strtotime($row['tgl_verifikasi'])); ?></b></small>
                                                <button class="btn btn-xs btn-outline-info shadow-sm btn-verifikasi" 
                                                    data-id="<?php echo $row['id']; ?>"
                                                    data-nama="<?php echo htmlspecialchars($row['nama'], ENT_QUOTES); ?>"
                                                    data-bab="<?php echo $row['bab']; ?>" title="Edit Hasil">
                                                    <i class="fas fa-pencil-alt"></i> Edit
                                                </button>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                
                <div class="card-footer py-3 bg-light border-top-0">
                    <div class="row align-items-center">
                        <div class="col-sm-6 text-muted small mb-2 mb-sm-0">
                            Total Data: <b><?php echo isset($total_rows) ? $total_rows : 0; ?></b>
                            <br><i class="fas fa-info-circle text-info"></i> <small>Data dengan status <b>PENDING</b> selalu di urutan teratas.</small>
                        </div>
                        <div class="col-sm-6">
                            <div class="float-right m-0">
                                <ul class="pagination pagination-sm m-0">
                                    <?php echo isset($pagination) ? $pagination : ''; ?>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </section>
</div>

<div class="modal fade" id="modalVerifikasi" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-primary border-0">
                <h5 class="modal-title text-white font-weight-bold"><i class="fas fa-check-double mr-2"></i> Input Hasil Plagiarisme</h5>
                <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
            </div>
            
            <form action="<?php echo base_url('operator/proses_verifikasi_plagiarisme'); ?>" method="POST">
                <div class="modal-body bg-light">
                    <input type="hidden" name="id_progres" id="modal_id_progres">
                    
                    <div class="form-group">
                        <label class="text-muted text-uppercase text-xs font-weight-bold">Nama Mahasiswa</label>
                        <input type="text" class="form-control bg-white font-weight-bold text-dark" id="modal_nama_mhs" readonly style="border: 1px solid #dee2e6;">
                    </div>

                    <div class="form-group">
                        <label class="text-muted text-uppercase text-xs font-weight-bold">Persentase Kemiripan (%) <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <input type="number" name="persentase" class="form-control font-weight-bold text-center" placeholder="0 - 100" min="0" max="100" style="font-size: 1.2rem;" required>
                            <div class="input-group-append">
                                <span class="input-group-text font-weight-bold bg-white"><i class="fas fa-percentage"></i></span>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="text-muted text-uppercase text-xs font-weight-bold mb-2 d-block">Keputusan Akhir <span class="text-danger">*</span></label>
                        <div class="row">
                            <div class="col-6">
                                <div class="card border-success shadow-sm" style="cursor: pointer;">
                                    <div class="card-body p-3 text-center">
                                        <div class="custom-control custom-radio">
                                            <input class="custom-control-input" type="radio" id="stLulus" name="status_plagiasi" value="Lulus" required>
                                            <label for="stLulus" class="custom-control-label text-success font-weight-bold" style="font-size: 1.1rem; cursor: pointer;">
                                                LULUS
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="card border-danger shadow-sm" style="cursor: pointer;">
                                    <div class="card-body p-3 text-center">
                                        <div class="custom-control custom-radio">
                                            <input class="custom-control-input" type="radio" id="stTolak" name="status_plagiasi" value="Tolak">
                                            <label for="stTolak" class="custom-control-label text-danger font-weight-bold" style="font-size: 1.1rem; cursor: pointer;">
                                                TOLAK
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer justify-content-between bg-white border-top-0">
                    <button type="button" class="btn btn-default shadow-sm" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary font-weight-bold shadow-sm px-4"><i class="fas fa-save mr-1"></i> SIMPAN HASIL</button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
    /* Styling agar Pagination CodeIgniter menyatu dengan Bootstrap */
    .pagination { margin: 0; }
    .page-item.active .page-link { background-color: #007bff; border-color: #007bff; color: white;}
    .page-link { color: #007bff; border-radius: 4px; margin: 0 2px; border: 1px solid #dee2e6;}
    .page-link:hover { background-color: #e9ecef; }
</style>

<link rel="stylesheet" href="https://cdn.datatables.net/1.10.25/css/dataTables.bootstrap4.min.css">
<script src="https://cdn.datatables.net/1.10.25/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.10.25/js/dataTables.bootstrap4.min.js"></script>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        
        // 1. Inisialisasi DataTable (hanya untuk tampilan estetika UI tanpa merusak sorting server-side)
        try {
            $('#tabelPlagiasi').DataTable({
                "responsive": true,
                "autoWidth": false,
                "ordering": false, // MATIKAN sorting JS agar sorting dari Controller (PHP) tidak tertumpuk
                "paging": false,   // MATIKAN paging JS karena kita menggunakan Paginasi CodeIgniter
                "info": false,     // MATIKAN info JS karena info total data sudah kita buat manual
                "searching": false // MATIKAN search box bawaan JS
            });
        } catch (e) { console.warn(e); }

        // 2. Event Listener Tombol Modal Edit/Input
        $('body').on('click', '.btn-verifikasi', function(e) {
            e.preventDefault();
            var btn = $(this);

            var id   = btn.attr('data-id');
            var nama = btn.attr('data-nama');
            var bab  = btn.attr('data-bab');

            if(!id) { alert("ID tidak ditemukan!"); return; }

            // Isi nilai inputan di dalam Modal
            $('#modal_id_progres').val(id);
            $('#modal_nama_mhs').val(nama + ' (BAB ' + bab + ')');
            
            // Bersihkan form setiap kali modal dibuka
            $('input[name="persentase"]').val('');
            $('input[name="status_plagiasi"]').prop('checked', false);

            // Tampilkan Modal
            $('#modalVerifikasi').modal('show');
        });
    });
</script>