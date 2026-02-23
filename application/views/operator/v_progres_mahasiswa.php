<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0"><?php echo $title; ?></h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="<?php echo base_url('operator/dashboard'); ?>">Dashboard</a></li>
                        <li class="breadcrumb-item active"><?php echo $title; ?></li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <section class="content">
        <div class="container-fluid">
            <!-- Filter -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Filter Data</h3>
                </div>
                <div class="card-body">
                    <form method="GET" action="<?php echo base_url('operator/list_revisi'); ?>">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Cari (Nama Mahasiswa/NPM/Judul Skripsi)</label>
                                    <input type="text" name="keyword" class="form-control" placeholder="Masukkan nama mahasiswa, NPM, atau judul skripsi..." value="<?php echo $keyword; ?>">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Program Studi</label>
                                    <select name="prodi" class="form-control">
                                        <option value="all">Semua Prodi</option>
                                        <?php if(!empty($list_prodi)): ?>
                                            <?php foreach ($list_prodi as $prodi_option): ?>
                                                <option value="<?php echo $prodi_option['prodi']; ?>" <?php echo ($prodi == $prodi_option['prodi']) ? 'selected' : ''; ?>><?php echo $prodi_option['prodi']; ?></option>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </select>
                                </div>
                            </div>
                            <!-- Angkatan filter removed as requested -->
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label>&nbsp;</label><br>
                                    <button type="submit" class="btn btn-primary btn-block"><i class="fas fa-search"></i> Filter</button>
                                </div>
                            </div>
                        </div>
                        <!-- Top sort controls removed; sorting via table header clicks -->
                    </form>
                </div>
            </div>

            <!-- Tabel Riwayat Progress Mahasiswa -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Riwayat Progress Mahasiswa</h3>
                </div>
                <div class="card-body table-responsive">
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th class="sortable" data-sort="npm">NPM</th>
                                <th class="sortable" data-sort="nama_mhs">Nama Mahasiswa</th>
                                <th class="sortable" data-sort="prodi">Prodi</th>
                                <th class="sortable" data-sort="judul">Judul Skripsi</th>
                                <th class="sortable" data-sort="bab">BAB</th>
                                <th>File</th>
                                <th>Komentar Dosen 1</th>
                                <th>Komentar Dosen 2</th>
                                <th>Nilai Dosen 1</th>
                                <th>Nilai Dosen 2</th>
                                <th>Progres Dosen 1 (%)</th>
                                <th>Progres Dosen 2 (%)</th>
                                <th class="sortable" data-sort="tgl_upload">Tanggal Upload</th>
                                <th>Tanggal Verifikasi</th>
                                <th class="sortable" data-sort="nama_p1">Pembimbing 1</th>
                                <th class="sortable" data-sort="nama_p2">Pembimbing 2</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($list_revisi)): ?>
                                <?php $no = 1; foreach ($list_revisi as $revisi): ?>
                                    <tr>
                                        <td><?php echo $no++; ?></td>
                                        <td><?php echo $revisi['npm']; ?></td>
                                        <td><?php echo $revisi['nama_mhs']; ?></td>
                                        <td><?php echo $revisi['prodi']; ?></td>
                                        <td><?php echo $revisi['judul']; ?></td>
                                        <td><?php echo $revisi['bab']; ?></td>
                                        <td>
                                            <?php if (!empty($revisi['file'])): ?>
                                                <a href="<?php echo base_url('uploads/progres/' . $revisi['file']); ?>" target="_blank" class="btn btn-sm btn-primary">Lihat File</a>
                                            <?php else: ?>
                                                -
                                            <?php endif; ?>
                                        </td>
                                        <td><?php echo $revisi['komentar_dosen1']; ?></td>
                                        <td><?php echo $revisi['komentar_dosen2']; ?></td>
                                        <td><?php echo $revisi['nilai_dosen1']; ?></td>
                                        <td><?php echo $revisi['nilai_dosen2']; ?></td>
                                        <td><?php echo $revisi['progres_dosen1']; ?>%</td>
                                        <td><?php echo $revisi['progres_dosen2']; ?>%</td>
                                        <td><?php echo date('d/m/Y H:i', strtotime($revisi['tgl_upload'])); ?></td>
                                        <td><?php echo $revisi['tgl_verifikasi'] ? date('d/m/Y H:i', strtotime($revisi['tgl_verifikasi'])) : '-'; ?></td>
                                        <td><?php echo $revisi['nama_p1']; ?></td>
                                        <td><?php echo $revisi['nama_p2']; ?></td>
                                        <td class="text-center">
                                            <button type="button" class="btn btn-sm btn-warning" data-toggle="modal" data-target="#modalKoreksi<?php echo $revisi['id']; ?>">
                                                <i class="fas fa-edit"></i> Koreksi
                                            </button>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="17" class="text-center">Silahkan cari data mahasiswa di atas.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </section>
</div>

<!-- Modal Koreksi untuk Operator -->
<?php if (!empty($list_revisi)): ?>
    <?php foreach ($list_revisi as $revisi): ?>
        <div class="modal fade" id="modalKoreksi<?php echo $revisi['id']; ?>" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
                <div class="modal-content text-left shadow-lg border-0">
                    <div class="modal-header bg-primary">
                        <h5 class="modal-title text-white"><i class="fas fa-edit mr-1"></i> Koreksi Bimbingan: BAB <?php echo $revisi['bab']; ?> - <?php echo $revisi['nama_mhs']; ?> (<?php echo $revisi['npm']; ?>)</h5>
                        <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
                    </div>

                    <?php echo form_open('operator/submit_koreksi_operator'); ?>
                    <div class="modal-body bg-light">
                        <input type="hidden" name="id_progres" value="<?php echo $revisi['id']; ?>">
                        <input type="hidden" name="id_skripsi" value="<?php echo isset($revisi['id_skripsi']) ? $revisi['id_skripsi'] : ''; ?>">

                        <div class="row">
                            <div class="col-md-6">
                                <h6 class="text-primary font-weight-bold">Pembimbing 1: <?php echo $revisi['nama_p1']; ?></h6>
                                <div class="row">
                                    <div class="col-md-4 mb-3">
                                        <label class="text-muted font-weight-bold small text-uppercase">Template Komentar:</label>
                                        <select class="form-control form-control-sm shadow-sm" onchange="document.getElementById('komentar_dosen1_<?php echo $revisi['id']; ?>').value += this.value + '\n\n'">
                                            <option value="">-- Pilih Template --</option>
                                            <option value="Revisi Bab Pendahuluan: Fokus pada gap penelitian.">Revisi Pendahuluan</option>
                                            <option value="Tinjauan Pustaka: Tambahkan 5 referensi terbaru (5 tahun terakhir).">Refrensi Kurang</option>
                                            <option value="Metode Penelitian: Jelaskan langkah pengujian data lebih rinci.">Metode Kurang Jelas</option>
                                            <option value="Hasil dan Pembahasan: Perlu interpretasi hasil yang lebih mendalam.">Pembahasan Dangkal</option>
                                            <option value="Perbaiki tata bahasa dan format penulisan (Typo).">Banyak Typo</option>
                                            <option value="Bab ini sudah baik, segera lanjutkan ke Bab berikutnya (ACC).">ACC Lanjut</option>
                                        </select>
                                        <small class="text-muted d-block mt-1">Pilih untuk menambahkan teks otomatis.</small>
                                    </div>
                                    <div class="col-md-8">
                                        <div class="form-group">
                                            <label class="font-weight-bold">Komentar Detail / Revisi:</label>
                                            <textarea name="komentar_dosen1" id="komentar_dosen1_<?php echo $revisi['id']; ?>" class="form-control shadow-sm" rows="6" placeholder="Tuliskan revisi..." required><?php echo $revisi['komentar_dosen1']; ?></textarea>
                                        </div>
                                    </div>
                                </div>

                                <hr>

                                <div class="form-group mb-0">
                                    <label class="font-weight-bold mb-3">Status Keputusan:</label>
                                    <div class="d-flex justify-content-start">
                                        <div class="custom-control custom-radio mr-5">
                                            <input class="custom-control-input" type="radio" id="st0_<?php echo $revisi['id']; ?>_1" name="status_progres1" value="0" <?php echo ($revisi['progres_dosen1'] == 0) ? 'checked' : ''; ?>>
                                            <label for="st0_<?php echo $revisi['id']; ?>_1" class="custom-control-label text-danger font-weight-bold">
                                                <i class="fas fa-times-circle mr-1"></i> Revisi (0%)
                                            </label>
                                        </div>
                                        <div class="custom-control custom-radio">
                                            <input class="custom-control-input" type="radio" id="st100_<?php echo $revisi['id']; ?>_1" name="status_progres1" value="100" <?php echo ($revisi['progres_dosen1'] == 100) ? 'checked' : ''; ?>>
                                            <label for="st100_<?php echo $revisi['id']; ?>_1" class="custom-control-label text-success font-weight-bold">
                                                <i class="fas fa-check-circle mr-1"></i> ACC Penuh (100%)
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <h6 class="text-primary font-weight-bold">Pembimbing 2: <?php echo $revisi['nama_p2']; ?></h6>
                                <div class="row">
                                    <div class="col-md-4 mb-3">
                                        <label class="text-muted font-weight-bold small text-uppercase">Template Komentar:</label>
                                        <select class="form-control form-control-sm shadow-sm" onchange="document.getElementById('komentar_dosen2_<?php echo $revisi['id']; ?>').value += this.value + '\n\n'">
                                            <option value="">-- Pilih Template --</option>
                                            <option value="Revisi Bab Pendahuluan: Fokus pada gap penelitian.">Revisi Pendahuluan</option>
                                            <option value="Tinjauan Pustaka: Tambahkan 5 referensi terbaru (5 tahun terakhir).">Refrensi Kurang</option>
                                            <option value="Metode Penelitian: Jelaskan langkah pengujian data lebih rinci.">Metode Kurang Jelas</option>
                                            <option value="Hasil dan Pembahasan: Perlu interpretasi hasil yang lebih mendalam.">Pembahasan Dangkal</option>
                                            <option value="Perbaiki tata bahasa dan format penulisan (Typo).">Banyak Typo</option>
                                            <option value="Bab ini sudah baik, segera lanjutkan ke Bab berikutnya (ACC).">ACC Lanjut</option>
                                        </select>
                                        <small class="text-muted d-block mt-1">Pilih untuk menambahkan teks otomatis.</small>
                                    </div>
                                    <div class="col-md-8">
                                        <div class="form-group">
                                            <label class="font-weight-bold">Komentar Detail / Revisi:</label>
                                            <textarea name="komentar_dosen2" id="komentar_dosen2_<?php echo $revisi['id']; ?>" class="form-control shadow-sm" rows="6" placeholder="Tuliskan revisi..." required><?php echo $revisi['komentar_dosen2']; ?></textarea>
                                        </div>
                                    </div>
                                </div>

                                <hr>

                                <div class="form-group mb-0">
                                    <label class="font-weight-bold mb-3">Status Keputusan:</label>
                                    <div class="d-flex justify-content-start">
                                        <div class="custom-control custom-radio mr-5">
                                            <input class="custom-control-input" type="radio" id="st0_<?php echo $revisi['id']; ?>_2" name="status_progres2" value="0" <?php echo ($revisi['progres_dosen2'] == 0) ? 'checked' : ''; ?>>
                                            <label for="st0_<?php echo $revisi['id']; ?>_2" class="custom-control-label text-danger font-weight-bold">
                                                <i class="fas fa-times-circle mr-1"></i> Revisi (0%)
                                            </label>
                                        </div>
                                        <div class="custom-control custom-radio">
                                            <input class="custom-control-input" type="radio" id="st100_<?php echo $revisi['id']; ?>_2" name="status_progres2" value="100" <?php echo ($revisi['progres_dosen2'] == 100) ? 'checked' : ''; ?>>
                                            <label for="st100_<?php echo $revisi['id']; ?>_2" class="custom-control-label text-success font-weight-bold">
                                                <i class="fas fa-check-circle mr-1"></i> ACC Penuh (100%)
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer justify-content-between bg-white">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary px-4"><i class="fas fa-save mr-1"></i> Simpan Hasil</button>
                    </div>
                    <?php echo form_close(); ?>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
<?php endif; ?>

<style>
    .border-left-info { border-left: 4px solid #17a2b8 !important; }
    .custom-control-label::before { width: 1.25rem; height: 1.25rem; }
    .custom-control-label::after { width: 1.25rem; height: 1.25rem; }
    .custom-control-label { padding-left: 0.5rem; padding-top: 0.1rem; }
</style>
