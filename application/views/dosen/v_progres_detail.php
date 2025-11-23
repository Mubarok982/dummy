<div class="container-fluid">
    <div class="row">
        <div class="col-12">

            <?php if ($this->session->flashdata('pesan_sukses')): ?>
                <div class="alert alert-success alert-dismissible">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                    <h5><i class="icon fas fa-check"></i> Berhasil!</h5>
                    <?php echo $this->session->flashdata('pesan_sukses'); ?>
                </div>
            <?php endif; ?>

            <?php if ($this->session->flashdata('pesan_error')): ?>
                <div class="alert alert-danger alert-dismissible">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                    <h5><i class="icon fas fa-ban"></i> Gagal!</h5>
                    <?php echo $this->session->flashdata('pesan_error'); ?>
                </div>
            <?php endif; ?>

            <div class="callout callout-info shadow-sm">
                <div class="row">
                    <div class="col-md-8">
                        <h5 class="text-primary"><i class="fas fa-book-reader mr-1"></i> <?php echo $skripsi['judul']; ?></h5>
                        <p class="mb-0">
                            <strong>Mahasiswa:</strong> <?php echo $skripsi['nama_mhs']; ?> (<?php echo $skripsi['npm']; ?>)
                        </p>
                    </div>
                    <div class="col-md-4 text-md-right align-self-center">
                        <span class="badge badge-warning text-md p-2">
                            <i class="fas fa-user-tag"></i> Anda sebagai: Pembimbing <?php echo $is_p1 ? '1' : '2'; ?>
                        </span>
                    </div>
                </div>
            </div>

            <div class="card card-primary card-outline">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-history mr-1"></i> Riwayat Bimbingan</h3>
                </div>
                
                <div class="card-body table-responsive p-0">
                    <table class="table table-hover table-striped text-nowrap">
                        <thead>
                            <tr class="bg-light text-center">
                                <th style="width: 10%">Bab</th>
                                <th style="width: 15%">Tanggal</th>
                                <th style="width: 15%">Cek Plagiat</th>
                                <th style="width: 15%">Status P1</th>
                                <th style="width: 15%">Status P2</th>
                                <th style="width: 10%">File</th>
                                <th style="width: 20%">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($progres)): ?>
                                <tr>
                                    <td colspan="7" class="text-center py-5 text-muted">
                                        <i class="fas fa-folder-open fa-3x mb-3" style="opacity: 0.3;"></i><br>
                                        Mahasiswa belum mengunggah progres bimbingan.
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($progres as $p): 
                                    // --- Logic Variables ---
                                    $plagiat = $this->M_Dosen->get_plagiarisme_result($p['id']);
                                    $plagiat_status = $plagiat ? $plagiat['status'] : 'Menunggu';
                                    $plagiat_percent = $plagiat ? $plagiat['persentase_kemiripan'] . '%' : '-';
                                    
                                    $komentar_field = $is_p1 ? 'komentar_dosen1' : 'komentar_dosen2';
                                    $progres_field = $is_p1 ? 'progres_dosen1' : 'progres_dosen2';
                                    $nilai_field = $is_p1 ? 'nilai_dosen1' : 'nilai_dosen2';
                                ?>
                                <tr>
                                    <td class="align-middle text-center">
                                        <span class="badge badge-secondary px-3 py-2">BAB <?php echo $p['bab']; ?></span>
                                    </td>
                                    <td class="align-middle text-center text-muted small">
                                        <?php echo date('d M Y', strtotime($p['created_at'])); ?>
                                    </td>
                                    
                                    <td class="align-middle text-center">
                                        <?php if ($plagiat_status == 'Lulus'): ?>
                                            <span class="badge badge-success"><i class="fas fa-check"></i> Lulus (<?php echo $plagiat_percent; ?>)</span>
                                        <?php elseif ($plagiat_status == 'Tolak'): ?>
                                            <span class="badge badge-danger"><i class="fas fa-times"></i> Ditolak</span>
                                        <?php else: ?>
                                            <span class="badge badge-warning text-white"><i class="fas fa-clock"></i> Menunggu Operator</span>
                                        <?php endif; ?>
                                        
                                        <?php if ($plagiat && $plagiat_status != 'Menunggu'): ?>
                                            <br><a href="<?php echo base_url('uploads/laporan_plagiarisme/' . $plagiat['dokumen_laporan']); ?>" target="_blank" class="text-xs"><i class="fas fa-external-link-alt"></i> Cek Laporan</a>
                                        <?php endif; ?>
                                    </td>

                                    <td class="align-middle text-center">
                                        <?php 
                                        $badge = 'secondary'; 
                                        if ($p['progres_dosen1'] == 100) $badge = 'success';
                                        elseif ($p['progres_dosen1'] == 50) $badge = 'warning';
                                        elseif ($p['nilai_dosen1'] == 'Revisi') $badge = 'danger';
                                        ?>
                                        <span class="badge badge-<?php echo $badge; ?>"><?php echo $p['nilai_dosen1'] ?: '-'; ?></span>
                                    </td>

                                    <td class="align-middle text-center">
                                        <?php 
                                        $badge = 'secondary'; 
                                        if ($p['progres_dosen2'] == 100) $badge = 'success';
                                        elseif ($p['progres_dosen2'] == 50) $badge = 'warning';
                                        elseif ($p['nilai_dosen2'] == 'Revisi') $badge = 'danger';
                                        ?>
                                        <span class="badge badge-<?php echo $badge; ?>"><?php echo $p['nilai_dosen2'] ?: '-'; ?></span>
                                    </td>

                                    <td class="align-middle text-center">
                                        <a href="<?php echo base_url('uploads/progres/' . $p['file']); ?>" target="_blank" class="btn btn-default btn-sm shadow-sm">
                                            <i class="fas fa-file-pdf text-danger"></i> PDF
                                        </a>
                                    </td>
                                    
                                    <td class="align-middle text-center">
                                        <?php if ($plagiat_status == 'Lulus' || $plagiat_status == 'Tolak'): ?>
                                            <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#modal-koreksi-<?php echo $p['id']; ?>">
                                                <i class="fas fa-edit"></i> Beri Koreksi
                                            </button>
                                        <?php else: ?>
                                            <span class="text-muted text-sm font-italic">Tunggu Plagiasi</span>
                                        <?php endif; ?>

                                        <div class="modal fade" id="modal-koreksi-<?php echo $p['id']; ?>" tabindex="-1" role="dialog">
                                            <div class="modal-dialog modal-lg" role="document">
                                                <div class="modal-content text-left">
                                                    <div class="modal-header bg-primary">
                                                        <h5 class="modal-title"><i class="fas fa-edit mr-1"></i> Koreksi Bimbingan: BAB <?php echo $p['bab']; ?></h5>
                                                        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                                                            <span aria-hidden="true">&times;</span>
                                                        </button>
                                                    </div>
                                                    
                                                    <?php echo form_open('dosen/submit_koreksi'); ?>
                                                    <div class="modal-body">
                                                        <input type="hidden" name="id_progres" value="<?php echo $p['id']; ?>">
                                                        <input type="hidden" name="id_skripsi" value="<?php echo $skripsi['id']; ?>">
                                                        <input type="hidden" name="is_p1" value="<?php echo $is_p1 ? 1 : 0; ?>">

                                                        <div class="row">
                                                            <div class="col-md-4">
                                                                <label>Saran Cepat (Template):</label>
                                                                <select class="form-control" onchange="document.getElementById('komentar_<?php echo $p['id']; ?>').value += this.value + '\n\n'">
                                                                    <option value="">-- Pilih Template --</option>
                                                                    <option value="Revisi Bab Pendahuluan: Fokus pada gap penelitian.">Revisi Pendahuluan</option>
                                                                    <option value="Tinjauan Pustaka: Tambahkan 5 referensi terbaru (5 tahun terakhir).">Refrensi Kurang</option>
                                                                    <option value="Metode Penelitian: Jelaskan langkah pengujian data lebih rinci.">Metode Kurang Jelas</option>
                                                                    <option value="Hasil dan Pembahasan: Perlu interpretasi hasil yang lebih mendalam.">Pembahasan Dangkal</option>
                                                                    <option value="Perbaiki tata bahasa dan format penulisan (Typo).">Banyak Typo</option>
                                                                    <option value="Bab ini sudah baik, segera lanjutkan ke Bab berikutnya (ACC).">ACC Lanjut</option>
                                                                </select>
                                                                <small class="text-muted">Pilih untuk menambahkan teks otomatis.</small>
                                                            </div>
                                                            <div class="col-md-8">
                                                                <div class="form-group">
                                                                    <label>Komentar Detail / Revisi:</label>
                                                                    <textarea name="komentar" id="komentar_<?php echo $p['id']; ?>" class="form-control" rows="6" placeholder="Tuliskan detail revisi di sini..."><?php echo $p[$komentar_field]; ?></textarea>
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <hr>

                                                        <div class="form-group">
                                                            <label>Status Keputusan:</label>
                                                            <div class="d-flex">
                                                                <div class="custom-control custom-radio mr-4">
                                                                    <input class="custom-control-input" type="radio" id="status0_<?php echo $p['id']; ?>" name="status_progres" value="0" <?php echo ($p[$progres_field] == 0) ? 'checked' : ''; ?>>
                                                                    <label for="status0_<?php echo $p['id']; ?>" class="custom-control-label text-danger font-weight-bold">Perlu Revisi (0%)</label>
                                                                </div>
                                                                <div class="custom-control custom-radio mr-4">
                                                                    <input class="custom-control-input" type="radio" id="status50_<?php echo $p['id']; ?>" name="status_progres" value="50" <?php echo ($p[$progres_field] == 50) ? 'checked' : ''; ?>>
                                                                    <label for="status50_<?php echo $p['id']; ?>" class="custom-control-label text-warning font-weight-bold">ACC Sebagian (50%)</label>
                                                                </div>
                                                                <div class="custom-control custom-radio">
                                                                    <input class="custom-control-input" type="radio" id="status100_<?php echo $p['id']; ?>" name="status_progres" value="100" <?php echo ($p[$progres_field] == 100) ? 'checked' : ''; ?>>
                                                                    <label for="status100_<?php echo $p['id']; ?>" class="custom-control-label text-success font-weight-bold">ACC Penuh (100%)</label>
                                                                </div>
                                                            </div>
                                                        </div>

                                                    </div>
                                                    <div class="modal-footer justify-content-between">
                                                        <button type="button" class="btn btn-default" data-dismiss="modal">Batal</button>
                                                        <button type="submit" class="btn btn-primary"><i class="fas fa-save mr-1"></i> Simpan Hasil Koreksi</button>
                                                    </div>
                                                    <?php echo form_close(); ?>
                                                </div>
                                            </div>
                                        </div>
                                        </td>
                                </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <?php
            $is_ready_sempro = FALSE;
            if (!empty($progres)) {
                $last_bab = end($progres); // Ambil elemen terakhir
                // Logika Sempro: Bab 3 sudah ACC 100% oleh KEDUA pembimbing
                if ($last_bab['bab'] == 3 && $last_bab['progres_dosen1'] == 100 && $last_bab['progres_dosen2'] == 100) {
                    $is_ready_sempro = TRUE;
                }
            }
            ?>

            <?php if ($is_ready_sempro): ?>
            <div class="callout callout-success shadow-sm mt-3">
                <h5><i class="fas fa-graduation-cap mr-1"></i> Mahasiswa Siap Seminar Proposal!</h5>
                <p>Mahasiswa ini telah menyelesaikan bimbingan BAB 1 sampai BAB 3 dengan status <strong>ACC Penuh</strong> dari kedua pembimbing.</p>
                <hr>
                <a href="https://sita.contoh.ac.id/pendaftaran" target="_blank" class="btn btn-success">
                    <i class="fas fa-check-double mr-1"></i> Instruksikan Daftar Sempro di SITA
                </a>
            </div>
            <?php endif; ?>

        </div>
    </div>
</div>