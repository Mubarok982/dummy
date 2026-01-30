<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0 text-dark">Detail Bimbingan</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="<?php echo base_url('dashboard'); ?>">Home</a></li>
                        <li class="breadcrumb-item"><a href="<?php echo base_url('dosen/bimbingan_list'); ?>">Daftar Bimbingan</a></li>
                        <li class="breadcrumb-item active">Detail Progres</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">
            
            <div class="row">
                <div class="col-12">

                    <?php if ($this->session->flashdata('pesan_sukses')): ?>
                        <div class="alert alert-success alert-dismissible fade show shadow-sm">
                            <button type="button" class="close" data-dismiss="alert">&times;</button>
                            <h5><i class="icon fas fa-check"></i> Berhasil!</h5>
                            <?php echo $this->session->flashdata('pesan_sukses'); ?>
                        </div>
                    <?php endif; ?>

                    <?php if ($this->session->flashdata('pesan_error')): ?>
                        <div class="alert alert-danger alert-dismissible fade show shadow-sm">
                            <button type="button" class="close" data-dismiss="alert">&times;</button>
                            <h5><i class="icon fas fa-ban"></i> Gagal!</h5>
                            <?php echo $this->session->flashdata('pesan_error'); ?>
                        </div>
                    <?php endif; ?>

                    <div class="callout callout-info shadow-sm bg-white border-left-info">
                        <div class="row">
                            <div class="col-md-8">
                                <h5 class="text-primary font-weight-bold"><i class="fas fa-book-reader mr-1"></i> <?php echo $skripsi['judul']; ?></h5>
                                <p class="mb-0 text-muted">
                                    <strong>Mahasiswa:</strong> <?php echo $skripsi['nama_mhs']; ?> <span class="badge badge-light border ml-1"><?php echo $skripsi['npm']; ?></span>
                                </p>
                            </div>
                            <div class="col-md-4 text-md-right align-self-center mt-3 mt-md-0">
                                <span class="badge badge-warning text-md p-2 shadow-sm">
                                    <i class="fas fa-user-tag mr-1"></i> Anda sebagai: Pembimbing <?php echo $is_p1 ? '1' : '2'; ?>
                                </span>
                            </div>
                        </div>
                    </div>

                    <div class="card card-primary card-outline shadow-sm">
                        <div class="card-header border-0">
                            <h3 class="card-title font-weight-bold text-primary">
                                <i class="fas fa-history mr-1"></i> Riwayat Bimbingan
                            </h3>
                        </div>
                        
                        <div class="card-body table-responsive p-0">
                            <table class="table table-hover table-striped text-nowrap align-middle">
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
                                                <i class="fas fa-folder-open fa-3x mb-3 text-gray-300"></i><br>
                                                Mahasiswa belum mengunggah progres bimbingan.
                                            </td>
                                        </tr>
                                    <?php else: ?>
                                        <?php foreach ($progres as $p): 
                                            // --- LOGIC PLAGIASI ---
                                            $plagiat = $this->M_Dosen->get_plagiarisme_result($p['id']); 
                                            $plagiat_status = isset($plagiat['status_plagiasi']) ? $plagiat['status_plagiasi'] : 'Menunggu';
                                            $plagiat_percent = isset($plagiat['persentase_kemiripan']) ? $plagiat['persentase_kemiripan'] . '%' : '-';
                                            
                                            // --- VARIABEL FIELD ---
                                            $komentar_field = $is_p1 ? 'komentar_dosen1' : 'komentar_dosen2';
                                            $progres_field = $is_p1 ? 'progres_dosen1' : 'progres_dosen2';
                                            $nilai_field = $is_p1 ? 'nilai_dosen1' : 'nilai_dosen2';

                                            // --- DETEKSI FILE REVISI (SAMA SEPERTI MAHASISWA) ---
                                            $is_revisi_file = (stripos($p['file'], '_REVISI') !== false);
                                        ?>
                                        <tr>
                                            <td class="align-middle text-center">
                                                <span class="badge badge-secondary px-3 py-2">BAB <?php echo $p['bab']; ?></span>
                                                
                                                <?php if ($is_revisi_file): ?>
                                                    <div class="mt-1">
                                                        <span class="badge badge-warning text-dark border border-warning shadow-sm" style="font-size: 0.75rem;">
                                                            <i class="fas fa-sync-alt mr-1"></i> Revisi
                                                        </span>
                                                    </div>
                                                <?php endif; ?>
                                            </td>
                                            
                                            <td class="align-middle text-center text-muted small">
                                                <i class="far fa-calendar-alt mr-1"></i> <?php echo date('d M Y', strtotime($p['created_at'])); ?>
                                            </td>
                                            
                                            <td class="align-middle text-center">
                                                <?php if ($p['bab'] == 1): ?>
                                                    <?php if ($plagiat_status == 'Lulus'): ?>
                                                        <span class="badge badge-success p-2"><i class="fas fa-check mr-1"></i> Lulus (<?php echo $plagiat_percent; ?>)</span>
                                                    <?php elseif ($plagiat_status == 'Tolak'): ?>
                                                        <span class="badge badge-danger p-2"><i class="fas fa-times mr-1"></i> Ditolak</span>
                                                    <?php else: ?>
                                                        <span class="badge badge-warning text-white p-2"><i class="fas fa-clock mr-1"></i> Menunggu Admin</span>
                                                    <?php endif; ?>
                                                <?php else: ?>
                                                    <span class="text-muted text-xs">-</span>
                                                <?php endif; ?>
                                            </td>

                                            <td class="align-middle text-center">
                                                <?php 
                                                $badge1 = ($p['progres_dosen1'] == 100) ? 'success' : 'secondary';
                                                if ($p['nilai_dosen1'] == 'Revisi') $badge1 = 'danger';
                                                ?>
                                                <span class="badge badge-<?php echo $badge1; ?> p-2"><?php echo $p['nilai_dosen1'] ?: '-'; ?></span>
                                            </td>

                                            <td class="align-middle text-center">
                                                <?php 
                                                $badge2 = ($p['progres_dosen2'] == 100) ? 'success' : 'secondary';
                                                if ($p['nilai_dosen2'] == 'Revisi') $badge2 = 'danger';
                                                ?>
                                                <span class="badge badge-<?php echo $badge2; ?> p-2"><?php echo $p['nilai_dosen2'] ?: '-'; ?></span>
                                            </td>

                                            <td class="align-middle text-center">
                                                <a href="<?php echo base_url('uploads/progres/' . $p['file']); ?>" target="_blank" class="btn btn-default btn-sm shadow-sm border" title="Unduh File">
                                                    <i class="fas fa-file-pdf text-danger"></i>
                                                </a>
                                            </td>
                                            
                                            <td class="align-middle text-center">
                                                <?php 
                                                $disabled = '';
                                                $tooltip = '';
                                                // Disable tombol jika Bab 1 belum lulus plagiasi
                                                if ($p['bab'] == 1 && $plagiat_status == 'Menunggu') {
                                                    $disabled = 'disabled';
                                                    $tooltip = 'title="Menunggu hasil Cek Plagiarisme dari Admin"';
                                                }
                                                ?>
                                                
                                                <button type="button" class="btn btn-primary btn-sm shadow-sm px-3" 
                                                        data-toggle="modal" data-target="#modal-koreksi-<?php echo $p['id']; ?>" 
                                                        <?php echo $disabled; ?> <?php echo $tooltip; ?>>
                                                    <i class="fas fa-edit mr-1"></i> Beri Koreksi
                                                </button>

                                                <div class="modal fade" id="modal-koreksi-<?php echo $p['id']; ?>" tabindex="-1" role="dialog" aria-hidden="true">
                                                    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
                                                        <div class="modal-content text-left shadow-lg border-0">
                                                            <div class="modal-header bg-primary">
                                                                <h5 class="modal-title text-white"><i class="fas fa-edit mr-1"></i> Koreksi Bimbingan: BAB <?php echo $p['bab']; ?></h5>
                                                                <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
                                                            </div>
                                                            
                                                            <?php echo form_open('dosen/submit_koreksi'); ?>
                                                            <div class="modal-body bg-light">
                                                                <input type="hidden" name="id_progres" value="<?php echo $p['id']; ?>">
                                                                <input type="hidden" name="id_skripsi" value="<?php echo $skripsi['id']; ?>">
                                                                <input type="hidden" name="is_p1" value="<?php echo $is_p1 ? 1 : 0; ?>">

                                                                <div class="row">
                                                                    <div class="col-md-4 mb-3">
                                                                        <label class="text-muted font-weight-bold small text-uppercase">Template Komentar:</label>
                                                                        <select class="form-control form-control-sm shadow-sm" onchange="document.getElementById('komentar_<?php echo $p['id']; ?>').value += this.value + '\n\n'">
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
                                                                            <textarea name="komentar" id="komentar_<?php echo $p['id']; ?>" class="form-control shadow-sm" rows="6" placeholder="Tuliskan revisi..." required><?php echo $p[$komentar_field]; ?></textarea>
                                                                        </div>
                                                                    </div>
                                                                </div>

                                                                <hr>

                                                                <div class="form-group mb-0">
                                                                    <label class="font-weight-bold mb-3">Status Keputusan:</label>
                                                                    <div class="d-flex justify-content-start">
                                                                        
                                                                        <div class="custom-control custom-radio mr-5">
                                                                            <input class="custom-control-input" type="radio" id="st0_<?php echo $p['id']; ?>" name="status_progres" value="0" <?php echo ($p[$progres_field] == 0) ? 'checked' : ''; ?>>
                                                                            <label for="st0_<?php echo $p['id']; ?>" class="custom-control-label text-danger font-weight-bold">
                                                                                <i class="fas fa-times-circle mr-1"></i> Revisi (0%)
                                                                            </label>
                                                                        </div>
                                                                        
                                                                        <div class="custom-control custom-radio">
                                                                            <input class="custom-control-input" type="radio" id="st100_<?php echo $p['id']; ?>" name="status_progres" value="100" <?php echo ($p[$progres_field] == 100) ? 'checked' : ''; ?>>
                                                                            <label for="st100_<?php echo $p['id']; ?>" class="custom-control-label text-success font-weight-bold">
                                                                                <i class="fas fa-check-circle mr-1"></i> ACC Penuh (100%)
                                                                            </label>
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
                        $last_bab = end($progres); 
                        if ($last_bab['bab'] == 3 && $last_bab['progres_dosen1'] == 100 && $last_bab['progres_dosen2'] == 100) {
                            $is_ready_sempro = TRUE;
                        }
                    }
                    ?>

                    <?php if ($is_ready_sempro): ?>
                    <div class="alert alert-success shadow-sm mt-3 d-flex align-items-center">
                        <i class="fas fa-graduation-cap fa-2x mr-3"></i>
                        <div>
                            <h5 class="alert-heading font-weight-bold mb-1">Mahasiswa Siap Seminar Proposal!</h5>
                            <p class="mb-0">Mahasiswa ini telah menyelesaikan bimbingan BAB 1-3 dengan status <strong>ACC Penuh</strong>.</p>
                        </div>
                    </div>
                    <?php endif; ?>

                </div>
            </div>
        </div>
    </section>
</div>

<style>
    .border-left-info { border-left: 4px solid #17a2b8 !important; }
    .custom-control-label::before { width: 1.25rem; height: 1.25rem; }
    .custom-control-label::after { width: 1.25rem; height: 1.25rem; }
    .custom-control-label { padding-left: 0.5rem; padding-top: 0.1rem; }
</style>