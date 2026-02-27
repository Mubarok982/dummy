<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0"><?php echo isset($title) ? $title : 'Riwayat Progres Mahasiswa'; ?></h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="<?php echo base_url('operator/dashboard'); ?>">Dashboard</a></li>
                        <li class="breadcrumb-item active"><?php echo isset($title) ? $title : 'Progress'; ?></li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <section class="content">
        <div class="container-fluid">
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-white">
                    <h3 class="card-title font-weight-bold"><i class="fas fa-filter text-primary mr-1"></i> Filter Data</h3>
                </div>
                <div class="card-body">
                    <form method="GET" action="<?php echo base_url('operator/list_revisi'); ?>">
                        <div class="row">
                            <div class="col-md-5">
                                <div class="form-group mb-md-0">
                                    <label class="text-muted small text-uppercase">Cari Mahasiswa/Judul</label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="fas fa-search"></i></span>
                                        </div>
                                        <input type="text" name="keyword" class="form-control" placeholder="Nama, NPM, atau Judul..." value="<?php echo isset($keyword) ? $keyword : ''; ?>">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group mb-md-0">
                                    <label class="text-muted small text-uppercase">Program Studi</label>
                                    <select name="prodi" class="form-control">
                                        <option value="all">Semua Prodi</option>
                                        <?php if(!empty($list_prodi)): ?>
                                            <?php foreach ($list_prodi as $prodi_option): ?>
                                                <option value="<?php echo $prodi_option['prodi']; ?>" <?php echo (isset($prodi) && $prodi == $prodi_option['prodi']) ? 'selected' : ''; ?>><?php echo $prodi_option['prodi']; ?></option>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3 d-flex align-items-end">
                                <div class="btn-group w-100 mt-2 mt-md-0">
                                    <button type="submit" class="btn btn-primary"><i class="fas fa-search"></i> Filter</button>
                                    <a href="<?php echo base_url('operator/list_revisi'); ?>" class="btn btn-secondary"><i class="fas fa-undo"></i> Reset</a>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <div class="card shadow-sm border-top-primary">
                <div class="card-header bg-white">
                    <h3 class="card-title font-weight-bold"><i class="fas fa-history text-primary mr-1"></i> Riwayat Progress Mahasiswa</h3>
                </div>
                <div class="card-body p-0 table-responsive">
                    <table class="table table-hover table-striped align-middle mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th style="width: 5%;" class="text-center">No</th>
                                <th style="width: 25%;">Mahasiswa & Skripsi</th>
                                <th style="width: 10%;" class="text-center">BAB</th>
                                <th style="width: 15%;" class="text-center">Tgl Upload</th>
                                <th style="width: 15%;" class="text-center">Tgl Verifikasi</th>
                                <th style="width: 15%;" class="text-center">File Dokumen</th>
                                <th style="width: 15%;" class="text-center"><i class="fas fa-cogs"></i> Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            // ============================================================
                            // LOGIKA ANTI-DUPLIKAT (Menyaring baris file yang sama)
                            // ============================================================
                            $seen_files = []; // Array pelacak nama file yang sudah ditampilkan
                            $filtered_revisi = []; 
                            
                            if (!empty($list_revisi)) {
                                foreach ($list_revisi as $revisi) {
                                    // Jadikan nama file sebagai kunci unik.
                                    // Jika file ini sudah pernah dirender, lewati baris ini.
                                    $unique_key = $revisi['file'];
                                    
                                    if (empty($unique_key) || !in_array($unique_key, $seen_files)) {
                                        $seen_files[] = $unique_key;
                                        $filtered_revisi[] = $revisi;
                                    }
                                }
                            }
                            ?>

                            <?php if (!empty($filtered_revisi)): ?>
                                <?php $no = isset($start_index) ? $start_index + 1 : 1; foreach ($filtered_revisi as $revisi): ?>
                                    <tr>
                                        <td class="text-center font-weight-bold text-muted"><?php echo $no++; ?></td>
                                        
                                        <td>
                                            <span class="font-weight-bold text-dark d-block mb-1"><?php echo $revisi['nama_mhs']; ?></span>
                                            <span class="badge badge-info mb-1"><i class="fas fa-id-card mr-1"></i><?php echo $revisi['npm']; ?></span>
                                            <span class="badge badge-secondary mb-2"><?php echo $revisi['prodi']; ?></span>
                                            <div class="small text-muted text-truncate" style="max-width: 250px;" title="<?php echo $revisi['judul']; ?>">
                                                <strong>Judul:</strong> <?php echo $revisi['judul']; ?>
                                            </div>
                                        </td>
                                        
                                        <td class="text-center">
                                            <span class="badge badge-primary px-3 py-2" style="font-size: 14px;">BAB <?php echo $revisi['bab']; ?></span>
                                        </td>

                                        <td class="text-center small">
                                            <span class="text-dark font-weight-bold"><?php echo date('d M Y', strtotime($revisi['tgl_upload'])); ?></span><br>
                                            <span class="text-muted"><?php echo date('H:i', strtotime($revisi['tgl_upload'])); ?> WIB</span>
                                        </td>

                                        <td class="text-center small">
                                            <?php if($revisi['tgl_verifikasi']): ?>
                                                <span class="text-success font-weight-bold"><i class="fas fa-check-double mr-1"></i> <?php echo date('d M Y', strtotime($revisi['tgl_verifikasi'])); ?></span><br>
                                                <span class="text-muted"><?php echo date('H:i', strtotime($revisi['tgl_verifikasi'])); ?> WIB</span>
                                            <?php else: ?>
                                                <span class="badge badge-light text-muted border">-</span>
                                            <?php endif; ?>
                                        </td>

                                        <td class="text-center">
                                            <?php if (!empty($revisi['file'])): ?>
                                                <a href="<?php echo base_url('uploads/progres/' . $revisi['file']); ?>" target="_blank" class="btn btn-sm btn-outline-danger shadow-sm">
                                                    <i class="fas fa-file-pdf mr-1"></i> Buka File
                                                </a>
                                            <?php else: ?>
                                                <span class="badge badge-light text-muted border">Tidak ada file</span>
                                            <?php endif; ?>
                                        </td>

                                        <td class="text-center align-middle">
                                            <div class="btn-group-vertical w-100 shadow-sm">
                                                <button type="button" class="btn btn-sm btn-info font-weight-bold" data-toggle="modal" data-target="#modalDetail<?php echo $revisi['id']; ?>">
                                                    <i class="fas fa-search mr-1"></i> Lihat Detail
                                                </button>
                                                <button type="button" class="btn btn-sm btn-warning font-weight-bold text-dark" data-toggle="modal" data-target="#modalKoreksi<?php echo $revisi['id']; ?>">
                                                    <i class="fas fa-edit mr-1"></i> Koreksi
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="7" class="text-center py-5">
                                        <i class="fas fa-search-minus fa-3x text-muted mb-3 opacity-50"></i><br>
                                        <span class="text-muted">Tidak ada riwayat progres bimbingan.</span>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
                <div class="card-footer py-2 bg-white">
                    <div class="row align-items-center">
                        <div class="col-sm-6 text-muted small">
                            Total Data: <b><?php echo count($filtered_revisi); ?></b>
                        </div>
                        <div class="col-sm-6">
                            <div class="float-right m-0">
                                <?php echo isset($pagination) ? $pagination : ''; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<?php if (!empty($filtered_revisi)): ?>
    <?php foreach ($filtered_revisi as $revisi): ?>
        
        <div class="modal fade" id="modalDetail<?php echo $revisi['id']; ?>" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
                <div class="modal-content shadow-lg border-0">
                    <div class="modal-header bg-info">
                        <h5 class="modal-title text-white font-weight-bold"><i class="fas fa-info-circle mr-2"></i> Detail Koreksi Pembimbing</h5>
                        <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
                    </div>
                    <div class="modal-body bg-light p-4">
                        
                        <div class="row">
                            <div class="col-md-6 mb-3 mb-md-0">
                                <div class="card h-100 border-primary shadow-sm">
                                    <div class="card-header bg-white border-bottom-primary">
                                        <small class="text-muted text-uppercase font-weight-bold d-block">Pembimbing 1</small>
                                        <h6 class="text-primary font-weight-bold m-0"><i class="fas fa-user-tie mr-1"></i> <?php echo isset($revisi['nama_p1']) ? $revisi['nama_p1'] : '-'; ?></h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="mb-3">
                                            <span class="text-muted small">Status Nilai:</span><br>
                                            <?php $bg_p1 = ($revisi['progres_dosen1'] == 100) ? 'success' : 'warning'; ?>
                                            <span class="badge badge-<?php echo $bg_p1; ?> px-3 py-2" style="font-size: 14px;">
                                                <?php echo !empty($revisi['nilai_dosen1']) ? $revisi['nilai_dosen1'] : 'Menunggu'; ?> (<?php echo $revisi['progres_dosen1']; ?>%)
                                            </span>
                                        </div>
                                        <div>
                                            <span class="text-muted small">Komentar/Catatan:</span>
                                            <div class="p-3 bg-light rounded border mt-1" style="min-height: 100px; max-height: 200px; overflow-y: auto;">
                                                <span class="font-italic text-dark"><?php echo !empty($revisi['komentar_dosen1']) ? nl2br($revisi['komentar_dosen1']) : '- Belum ada komentar -'; ?></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="card h-100 border-info shadow-sm">
                                    <div class="card-header bg-white border-bottom-info">
                                        <small class="text-muted text-uppercase font-weight-bold d-block">Pembimbing 2</small>
                                        <h6 class="text-info font-weight-bold m-0"><i class="fas fa-user-tie mr-1"></i> <?php echo isset($revisi['nama_p2']) ? $revisi['nama_p2'] : '-'; ?></h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="mb-3">
                                            <span class="text-muted small">Status Nilai:</span><br>
                                            <?php $bg_p2 = ($revisi['progres_dosen2'] == 100) ? 'success' : 'warning'; ?>
                                            <span class="badge badge-<?php echo $bg_p2; ?> px-3 py-2" style="font-size: 14px;">
                                                <?php echo !empty($revisi['nilai_dosen2']) ? $revisi['nilai_dosen2'] : 'Menunggu'; ?> (<?php echo $revisi['progres_dosen2']; ?>%)
                                            </span>
                                        </div>
                                        <div>
                                            <span class="text-muted small">Komentar/Catatan:</span>
                                            <div class="p-3 bg-light rounded border mt-1" style="min-height: 100px; max-height: 200px; overflow-y: auto;">
                                                <span class="font-italic text-dark"><?php echo !empty($revisi['komentar_dosen2']) ? nl2br($revisi['komentar_dosen2']) : '- Belum ada komentar -'; ?></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                    <div class="modal-footer bg-white">
                        <button type="button" class="btn btn-secondary px-4" data-dismiss="modal">Tutup</button>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="modalKoreksi<?php echo $revisi['id']; ?>" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-xl modal-dialog-centered" role="document">
                <div class="modal-content text-left shadow-lg border-0">
                    <div class="modal-header bg-warning">
                        <h5 class="modal-title text-dark font-weight-bold"><i class="fas fa-edit mr-1"></i> Koreksi Bimbingan: BAB <?php echo $revisi['bab']; ?> - <?php echo $revisi['nama_mhs']; ?> (<?php echo $revisi['npm']; ?>)</h5>
                        <button type="button" class="close text-dark" data-dismiss="modal">&times;</button>
                    </div>

                    <?php echo form_open('operator/submit_koreksi_operator'); ?>
                    <div class="modal-body bg-light">
                        <input type="hidden" name="id_progres" value="<?php echo $revisi['id']; ?>">
                        <input type="hidden" name="id_skripsi" value="<?php echo isset($revisi['id_skripsi']) ? $revisi['id_skripsi'] : ''; ?>">

                        <div class="row">
                            <div class="col-md-6 border-right border-secondary">
                                <div class="d-flex align-items-center mb-3">
                                    <i class="fas fa-user-tie fa-2x text-primary mr-3"></i>
                                    <div>
                                        <small class="text-muted d-block text-uppercase font-weight-bold">Pembimbing 1</small>
                                        <h6 class="text-primary font-weight-bold m-0"><?php echo isset($revisi['nama_p1']) ? $revisi['nama_p1'] : '-'; ?></h6>
                                    </div>
                                </div>
                                <div class="form-group mb-3">
                                    <label class="text-muted font-weight-bold small text-uppercase"><i class="fas fa-magic text-info mr-1"></i> Template Komentar Cepat:</label>
                                    <select class="form-control form-control-sm shadow-sm border-primary" onchange="document.getElementById('komentar_dosen1_<?php echo $revisi['id']; ?>').value += this.value + '\n\n'">
                                        <option value="">-- Pilih Template --</option>
                                        <option value="Revisi Bab Pendahuluan: Fokus pada gap penelitian.">Revisi Pendahuluan</option>
                                        <option value="Tinjauan Pustaka: Tambahkan 5 referensi terbaru (5 tahun terakhir).">Refrensi Kurang</option>
                                        <option value="Metode Penelitian: Jelaskan langkah pengujian data lebih rinci.">Metode Kurang Jelas</option>
                                        <option value="Hasil dan Pembahasan: Perlu interpretasi hasil yang lebih mendalam.">Pembahasan Dangkal</option>
                                        <option value="Perbaiki tata bahasa dan format penulisan (Typo).">Banyak Typo</option>
                                        <option value="Bab ini sudah baik, segera lanjutkan ke Bab berikutnya (ACC).">ACC Lanjut</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label class="font-weight-bold">Komentar Detail / Revisi:</label>
                                    <textarea name="komentar_dosen1" id="komentar_dosen1_<?php echo $revisi['id']; ?>" class="form-control shadow-sm border-primary" rows="6" placeholder="Tuliskan revisi..." required><?php echo $revisi['komentar_dosen1']; ?></textarea>
                                </div>
                                <div class="form-group mb-0 p-3 bg-white rounded shadow-sm border-left-primary" style="border-left: 4px solid #007bff;">
                                    <label class="font-weight-bold mb-2 d-block">Status Keputusan (P1):</label>
                                    <div class="d-flex justify-content-start">
                                        <div class="custom-control custom-radio mr-4">
                                            <input class="custom-control-input custom-control-input-danger" type="radio" id="st0_<?php echo $revisi['id']; ?>_1" name="status_progres1" value="0" <?php echo ($revisi['progres_dosen1'] == 0) ? 'checked' : ''; ?>>
                                            <label for="st0_<?php echo $revisi['id']; ?>_1" class="custom-control-label text-danger font-weight-bold">
                                                Revisi (0%)
                                            </label>
                                        </div>
                                        <div class="custom-control custom-radio">
                                            <input class="custom-control-input custom-control-input-success" type="radio" id="st100_<?php echo $revisi['id']; ?>_1" name="status_progres1" value="100" <?php echo ($revisi['progres_dosen1'] == 100) ? 'checked' : ''; ?>>
                                            <label for="st100_<?php echo $revisi['id']; ?>_1" class="custom-control-label text-success font-weight-bold">
                                                ACC Penuh (100%)
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="d-flex align-items-center mb-3">
                                    <i class="fas fa-user-tie fa-2x text-info mr-3"></i>
                                    <div>
                                        <small class="text-muted d-block text-uppercase font-weight-bold">Pembimbing 2</small>
                                        <h6 class="text-info font-weight-bold m-0"><?php echo isset($revisi['nama_p2']) ? $revisi['nama_p2'] : '-'; ?></h6>
                                    </div>
                                </div>
                                <div class="form-group mb-3">
                                    <label class="text-muted font-weight-bold small text-uppercase"><i class="fas fa-magic text-info mr-1"></i> Template Komentar Cepat:</label>
                                    <select class="form-control form-control-sm shadow-sm border-info" onchange="document.getElementById('komentar_dosen2_<?php echo $revisi['id']; ?>').value += this.value + '\n\n'">
                                        <option value="">-- Pilih Template --</option>
                                        <option value="Revisi Bab Pendahuluan: Fokus pada gap penelitian.">Revisi Pendahuluan</option>
                                        <option value="Tinjauan Pustaka: Tambahkan 5 referensi terbaru (5 tahun terakhir).">Refrensi Kurang</option>
                                        <option value="Metode Penelitian: Jelaskan langkah pengujian data lebih rinci.">Metode Kurang Jelas</option>
                                        <option value="Hasil dan Pembahasan: Perlu interpretasi hasil yang lebih mendalam.">Pembahasan Dangkal</option>
                                        <option value="Perbaiki tata bahasa dan format penulisan (Typo).">Banyak Typo</option>
                                        <option value="Bab ini sudah baik, segera lanjutkan ke Bab berikutnya (ACC).">ACC Lanjut</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label class="font-weight-bold">Komentar Detail / Revisi:</label>
                                    <textarea name="komentar_dosen2" id="komentar_dosen2_<?php echo $revisi['id']; ?>" class="form-control shadow-sm border-info" rows="6" placeholder="Tuliskan revisi..." required><?php echo $revisi['komentar_dosen2']; ?></textarea>
                                </div>
                                <div class="form-group mb-0 p-3 bg-white rounded shadow-sm border-left-info" style="border-left: 4px solid #17a2b8;">
                                    <label class="font-weight-bold mb-2 d-block">Status Keputusan (P2):</label>
                                    <div class="d-flex justify-content-start">
                                        <div class="custom-control custom-radio mr-4">
                                            <input class="custom-control-input custom-control-input-danger" type="radio" id="st0_<?php echo $revisi['id']; ?>_2" name="status_progres2" value="0" <?php echo ($revisi['progres_dosen2'] == 0) ? 'checked' : ''; ?>>
                                            <label for="st0_<?php echo $revisi['id']; ?>_2" class="custom-control-label text-danger font-weight-bold">
                                                Revisi (0%)
                                            </label>
                                        </div>
                                        <div class="custom-control custom-radio">
                                            <input class="custom-control-input custom-control-input-success" type="radio" id="st100_<?php echo $revisi['id']; ?>_2" name="status_progres2" value="100" <?php echo ($revisi['progres_dosen2'] == 100) ? 'checked' : ''; ?>>
                                            <label for="st100_<?php echo $revisi['id']; ?>_2" class="custom-control-label text-success font-weight-bold">
                                                ACC Penuh (100%)
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer justify-content-between bg-white border-top">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Batalkan</button>
                        <button type="submit" class="btn btn-primary px-5 font-weight-bold shadow-sm"><i class="fas fa-save mr-1"></i> Simpan Hasil Koreksi</button>
                    </div>
                    <?php echo form_close(); ?>
                </div>
            </div>
        </div>

    <?php endforeach; ?>
<?php endif; ?>

<style>
    /* Styling tambahan untuk merapikan UI Tabel & Modal */
    .table td { vertical-align: middle !important; }
    .custom-control-label::before { width: 1.25rem; height: 1.25rem; border: 2px solid #adb5bd;}
    .custom-control-label::after { width: 1.25rem; height: 1.25rem; }
    .custom-control-label { padding-left: 0.5rem; padding-top: 0.15rem; cursor: pointer;}
    .custom-control-input-success:checked ~ .custom-control-label::before { border-color: #28a745; background-color: #28a745; }
    .custom-control-input-danger:checked ~ .custom-control-label::before { border-color: #dc3545; background-color: #dc3545; }
</style>