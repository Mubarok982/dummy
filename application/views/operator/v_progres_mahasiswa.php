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
            
            <div class="alert alert-info shadow-sm">
                <div class="d-flex align-items-center">
                    <i class="fas fa-info-circle fa-2x mr-3"></i>
                    <div>
                        <h5 class="mb-1 font-weight-bold">Informasi Riwayat Progres</h5>
                        <p class="mb-0">
                            Halaman ini menampilkan seluruh riwayat progres bimbingan skripsi mahasiswa beserta status koreksi dari dosen pembimbing. Anda juga dapat melihat detail komentar atau melakukan koreksi manual jika diperlukan.
                        </p>
                    </div>
                </div>
            </div>

            <div class="card card-outline card-primary shadow-sm">
                <div class="card-header">
                    <h3 class="card-title"> <i class="fas fa-history mr-1"></i> Daftar Riwayat Progres Bimbingan</h3>
                </div>
                <div class="card-body">
                    
                    <form method="GET" action="<?php echo base_url('operator/list_revisi'); ?>" class="mb-3">
                        <div class="row">
                            <div class="col-md-4">
                                <label>Pencarian</label>
                                <input type="text" name="keyword" class="form-control" placeholder="Cari nama/NPM/judul..." value="<?php echo isset($keyword) ? $keyword : ''; ?>">
                            </div>
                            <div class="col-md-3">
                                <label>Prodi</label>
                                <select name="prodi" class="form-control">
                                    <option value="all">Semua Prodi</option>
                                    <?php if(!empty($list_prodi)): ?>
                                        <?php foreach ($list_prodi as $prodi_option): ?>
                                            <option value="<?php echo $prodi_option['prodi']; ?>" <?php echo (isset($prodi) && $prodi == $prodi_option['prodi']) ? 'selected' : ''; ?>><?php echo $prodi_option['prodi']; ?></option>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label>&nbsp;</label>
                                <button type="submit" class="btn btn-primary btn-block"><i class="fas fa-search mr-1"></i> Filter</button>
                            </div>
                            <?php if((isset($keyword) && $keyword) || (isset($prodi) && $prodi != 'all')): ?>
                            <div class="col-md-2">
                                <label>&nbsp;</label>
                                <a href="<?php echo base_url('operator/list_revisi'); ?>" class="btn btn-secondary btn-block"><i class="fas fa-undo"></i> Reset</a>
                            </div>
                            <?php endif; ?>
                        </div>
                    </form>

                    <div class="table-responsive">
                        <table class="table table-bordered table-striped table-hover align-middle">
                            <thead class="bg-light">
                                <tr class="text-center">
                                    <th style="width: 5%;">No</th>
                                    <th style="width: 20%;">Mahasiswa</th>
                                    <th style="width: 25%;">Judul Skripsi</th>
                                    <th style="width: 8%;">BAB</th>
                                    <th style="width: 12%;">Tgl Upload</th>
                                    <th style="width: 12%;">Tgl Verifikasi</th>
                                    <th style="width: 8%;">File</th>
                                    <th style="width: 10%;">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($list_revisi)): ?>
                                    <?php 
                                    // Start index dari controller agar penomoran lanjut ke halaman berikutnya (tidak reset ke 1)
                                    $no = isset($start_index) ? $start_index + 1 : 1; 
                                    foreach ($list_revisi as $revisi): 
                                    ?>
                                        <tr>
                                            <td class="text-center font-weight-bold text-muted align-middle"><?php echo $no++; ?></td>
                                            
                                            <td class="align-middle">
                                                <strong class="d-block mb-1"><?php echo $revisi['nama_mhs']; ?></strong>
                                                <div class="d-flex align-items-center">
                                                    <span class="text-muted small mr-2"><i class="fas fa-id-card mr-1"></i><?php echo $revisi['npm']; ?></span>
                                                    <span class="badge badge-info"><?php echo $revisi['prodi']; ?></span>
                                                </div>
                                            </td>

                                            <td class="align-middle text-wrap">
                                                <span class="text-muted text-sm font-italic"><?php echo !empty($revisi['judul']) ? $revisi['judul'] : '- Belum ada judul -'; ?></span>
                                            </td>
                                            
                                            <td class="text-center align-middle">
                                                <span class="badge badge-primary px-3 py-2" style="font-size: 14px;">BAB <?php echo $revisi['bab']; ?></span>
                                            </td>

                                            <td class="text-center small align-middle">
                                                <span class="text-dark font-weight-bold"><?php echo date('d M Y', strtotime($revisi['tgl_upload'])); ?></span><br>
                                                <span class="text-muted"><?php echo date('H:i', strtotime($revisi['tgl_upload'])); ?> WIB</span>
                                            </td>

                                            <td class="text-center small align-middle">
                                                <?php if($revisi['tgl_verifikasi']): ?>
                                                    <span class="text-success font-weight-bold"><i class="fas fa-check-double mr-1"></i> <?php echo date('d M Y', strtotime($revisi['tgl_verifikasi'])); ?></span><br>
                                                    <span class="text-muted"><?php echo date('H:i', strtotime($revisi['tgl_verifikasi'])); ?> WIB</span>
                                                <?php else: ?>
                                                    -
                                                <?php endif; ?>
                                            </td>

                                            <td class="text-center align-middle">
                                                <?php if (!empty($revisi['file'])): ?>
                                                    <a href="<?php echo base_url('uploads/progres/' . $revisi['file']); ?>" target="_blank" class="btn btn-sm btn-outline-danger shadow-sm">
                                                        <i class="fas fa-file-pdf mr-1"></i> PDF
                                                    </a>
                                                <?php else: ?>
                                                    -
                                                <?php endif; ?>
                                            </td>

                                            <td class="text-center align-middle">
                                                <div class="btn-group-vertical w-100 shadow-sm">
                                                    <button type="button" class="btn btn-sm btn-info font-weight-bold" data-toggle="modal" data-target="#modalDetail<?php echo $revisi['id']; ?>">
                                                        <i class="fas fa-search mr-1"></i> Detail
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
                                        <td colspan="8" class="text-center py-5 text-muted">
                                            <i class="fas fa-search-minus fa-3x mb-3 opacity-50"></i><br>
                                            Tidak ada riwayat progres bimbingan.
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="card-footer py-2 bg-white">
                        <div class="row align-items-center">
                            <div class="col-sm-6 text-muted small">
                                Total Data: <b><?php echo isset($total_rows) ? $total_rows : 0; ?></b>
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
        </div>
    </section>
</div>

<?php if (!empty($list_revisi)): ?>
    <?php foreach ($list_revisi as $revisi): ?>
        
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
    .table td { vertical-align: middle !important; }
    .custom-control-label::before { width: 1.25rem; height: 1.25rem; border: 2px solid #adb5bd;}
    .custom-control-label::after { width: 1.25rem; height: 1.25rem; }
    .custom-control-label { padding-left: 0.5rem; padding-top: 0.15rem; cursor: pointer;}
    .custom-control-input-success:checked ~ .custom-control-label::before { border-color: #28a745; background-color: #28a745; }
    .custom-control-input-danger:checked ~ .custom-control-label::before { border-color: #dc3545; background-color: #dc3545; }
</style>