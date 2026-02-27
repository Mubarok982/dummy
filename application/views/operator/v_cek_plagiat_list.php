<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0 text-dark">Cek Plagiarisme (BAB 1)</h1>
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
                            <div class="col-md-4">
                                <input type="text" name="keyword" class="form-control" placeholder="Cari nama/NPM..." value="<?php echo isset($keyword) ? $keyword : ''; ?>">
                            </div>
                            <div class="col-md-3">
                                <select name="status" class="form-control">
                                    <option value="all">Semua Status</option>
                                    <option value="Menunggu" <?php echo (isset($status) && $status == 'Menunggu') ? 'selected' : ''; ?>>Menunggu</option>
                                    <option value="Lulus" <?php echo (isset($status) && $status == 'Lulus') ? 'selected' : ''; ?>>Lulus</option>
                                    <option value="Tolak" <?php echo (isset($status) && $status == 'Tolak') ? 'selected' : ''; ?>>Tolak</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <button type="submit" class="btn btn-primary btn-block"><i class="fas fa-search"></i> Filter</button>
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
                            <?php 
                            if (empty($list_plagiasi)): ?>
                                <?php else: 
                                // ============================================================
                                // LOGIKA ANTI-DUPLIKAT (Menyaring entri ganda dari hasil JOIN model)
                                // ============================================================
                                $seen_files = []; // Array untuk melacak file yang sudah dirender
                                $no_urut = 1;
                                
                                foreach ($list_plagiasi as $row): 
                                    
                                    // Kunci unik: Kombinasi ID Progres dan Nama File
                                    // Jika kunci ini sudah pernah muncul di looping sebelumnya, SKIP!
                                    $unique_key = $row['id'] . '_' . $row['progres_file'];
                                    
                                    if (in_array($unique_key, $seen_files)) {
                                        continue; 
                                    }
                                    
                                    // Tandai file ini sebagai "sudah ditampilkan"
                                    $seen_files[] = $unique_key;
                            ?>
                                <tr>
                                    <td class="text-center"><?php echo $no_urut++; ?></td>
                                    
                                    <td>
                                        <div class="user-block">
                                            <span class="username ml-0"><a href="#" class="text-dark font-weight-bold"><?php echo $row['nama']; ?></a></span>
                                            <span class="description ml-0">
                                                <?php echo $row['npm']; ?> 
                                            </span>
                                        </div>
                                    </td>

                                    <td>
                                        <strong><?php echo !empty($row['judul']) ? $row['judul'] : '-'; ?></strong>
                                    </td>

                                    <td class="text-center text-muted">
                                        <?php echo !empty($row['tgl_upload']) ? date('d M Y H:i', strtotime($row['tgl_upload'])) : '-'; ?>
                                    </td>

                                    <td class="text-center">
                                        <a href="<?php echo base_url('uploads/progres/' . $row['progres_file']); ?>" target="_blank" class="btn btn-sm btn-default border shadow-sm" title="Lihat PDF">
                                            <i class="fas fa-file-pdf text-danger"></i> PDF
                                        </a>
                                    </td>

                                    <td class="text-center">
                                        <?php if($row['status_plagiasi'] == 'Menunggu'): ?>
                                            <span class="badge badge-warning px-3 py-2">PENDING</span>
                                        <?php elseif($row['status_plagiasi'] == 'Lulus'): ?>
                                            <span class="badge badge-success px-3 py-2">LULUS</span>
                                        <?php else: ?>
                                            <span class="badge badge-danger px-3 py-2">DITOLAK</span>
                                        <?php endif; ?>
                                    </td>

                                    <td class="text-center font-weight-bold">
                                        <?php if($row['status_plagiasi'] == 'Menunggu'): ?>
                                            -
                                        <?php else: ?>
                                            <?php echo $row['persentase_kemiripan']; ?>%
                                        <?php endif; ?>
                                    </td>

                                    <td class="text-center">
                                        <?php if($row['status_plagiasi'] == 'Menunggu'): ?>
                                            <button type="button" 
                                                    class="btn btn-primary btn-sm shadow-sm btn-verifikasi"
                                                    data-id="<?php echo $row['id']; ?>"
                                                    data-nama="<?php echo $row['nama']; ?>"
                                                    data-bab="<?php echo $row['bab']; ?>">
                                                <i class="fas fa-edit mr-1"></i> Input Hasil
                                            </button>
                                        <?php else: ?>
                                            <small class="text-muted font-italic">
                                                Diverifikasi:<br>
                                                <?php echo date('d/m/Y H:i', strtotime($row['tgl_verifikasi'])); ?>
                                                
                                                <br>
                                                <a href="#" class="text-primary btn-verifikasi" 
                                                   data-id="<?php echo $row['id']; ?>"
                                                   data-nama="<?php echo $row['nama']; ?>"
                                                   data-bab="<?php echo $row['bab']; ?>">
                                                   [Edit]
                                                </a>
                                            </small>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
                <div class="card-footer py-2 bg-white">
                    <div class="row align-items-center">
                        <div class="col-sm-6 text-muted small">
                            Total Data: <b><?php echo isset($total_rows) ? $total_rows : 0; ?></b>
                            <br><small>* Data dengan status <b>PENDING</b> selalu di urutan teratas.</small>
                        </div>
                        <div class="col-sm-6">
                            <div class="float-right">
                                <?php echo isset($pagination) ? $pagination : ''; ?>
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
        <div class="modal-content">
            <div class="modal-header bg-primary">
                <h5 class="modal-title text-white"><i class="fas fa-check-double mr-1"></i> Input Hasil Plagiarisme</h5>
                <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
            </div>
            
            <form action="<?php echo base_url('operator/proses_verifikasi_plagiarisme'); ?>" method="POST">
                <div class="modal-body">
                    <input type="hidden" name="id_progres" id="modal_id_progres">
                    
                    <div class="form-group">
                        <label>Mahasiswa</label>
                        <input type="text" class="form-control bg-light" id="modal_nama_mhs" readonly>
                    </div>

                    <div class="form-group">
                        <label>Persentase Kemiripan (%) <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <input type="number" name="persentase" class="form-control" placeholder="0 - 100" min="0" max="100" required>
                            <div class="input-group-append">
                                <span class="input-group-text font-weight-bold">%</span>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="d-block">Keputusan Akhir <span class="text-danger">*</span></label>
                        <div class="row">
                            <div class="col-6">
                                <div class="card border-success shadow-none">
                                    <div class="card-body p-2 text-center">
                                        <div class="custom-control custom-radio">
                                            <input class="custom-control-input" type="radio" id="stLulus" name="status_plagiasi" value="Lulus" required>
                                            <label for="stLulus" class="custom-control-label text-success font-weight-bold">
                                                LULUS
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="card border-danger shadow-none">
                                    <div class="card-body p-2 text-center">
                                        <div class="custom-control custom-radio">
                                            <input class="custom-control-input" type="radio" id="stTolak" name="status_plagiasi" value="Tolak">
                                            <label for="stTolak" class="custom-control-label text-danger font-weight-bold">
                                                TOLAK
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary font-weight-bold"><i class="fas fa-save mr-1"></i> SIMPAN DATA</button>
                </div>
            </form>
        </div>
    </div>
</div>

<link rel="stylesheet" href="https://cdn.datatables.net/1.10.25/css/dataTables.bootstrap4.min.css">
<script src="https://cdn.datatables.net/1.10.25/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.10.25/js/dataTables.bootstrap4.min.js"></script>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        
        // 1. Inisialisasi DataTable
        try {
            $('#tabelPlagiasi').DataTable({
                "responsive": true,
                "autoWidth": false,
                "ordering": false, // Matikan sorting JS agar sorting PHP (Pending di atas) tidak rusak
                "language": {
                    "url": "//cdn.datatables.net/plug-ins/1.10.21/i18n/Indonesian.json"
                }
            });
        } catch (e) { console.warn(e); }

        // 2. Event Listener Tombol Modal
        $('body').on('click', '.btn-verifikasi', function(e) {
            e.preventDefault();
            var btn = $(this);

            var id   = btn.attr('data-id');
            var nama = btn.attr('data-nama');
            var bab  = btn.attr('data-bab');

            if(!id) { alert("ID tidak ditemukan!"); return; }

            // Isi Modal
            $('#modal_id_progres').val(id);
            $('#modal_nama_mhs').val(nama + ' (BAB ' + bab + ')');
            
            // Reset Form
            $('input[name="persentase"]').val('');
            $('input[name="status_plagiasi"]').prop('checked', false);

            $('#modalVerifikasi').modal('show');
        });
    });
</script>