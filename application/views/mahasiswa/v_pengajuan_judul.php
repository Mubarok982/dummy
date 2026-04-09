<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0 text-dark">Pengajuan Judul Skripsi</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="<?php echo base_url('dashboard'); ?>">Home</a></li>
                        <li class="breadcrumb-item active">Pengajuan Judul</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">
            <div class="row justify-content-center">
                <div class="col-md-10">

                    <?php 
                    // ============================================================
                    // LOGIKA PENGUNCIAN FORM (TANPA MENGUBAH TAMPILAN)
                    // ============================================================
                    
                    $is_exist = isset($skripsi) && $skripsi != NULL;
                    $status_acc = $is_exist ? $skripsi['status_acc_kaprodi'] : null;
                    // Pastikan variabel status_ujian dikirim dari controller
                    $status_ujian = isset($status_ujian) ? $status_ujian : null; 

                    $is_locked = false; // Default Terbuka
                    $pesan_status = "";
                    $warna_alert = "";

                    // 1. Cek Status Judul (Menunggu/Diterima)
                    if ($is_exist) {
                        if ($status_acc == 'menunggu') {
                            $is_locked = true;
                            $pesan_status = "Pengajuan Anda sedang diperiksa Kaprodi. Form terkunci.";
                            $warna_alert = "alert-warning";
                        } 
                        elseif ($status_acc == 'diterima') {
                            $is_locked = true;
                            $pesan_status = "Judul sudah disetujui (ACC).";
                            $warna_alert = "alert-success";
                        }
                    }

                    // 2. Cek Status Ujian (MENGULANG) -> Override (Buka Paksa)
                    if (strtolower($status_ujian) == 'mengulang') {
                        $is_locked = false; // Buka Gembok
                        $pesan_status = "<b>STATUS MENGULANG:</b> Silakan ajukan judul baru.";
                        $warna_alert = "alert-danger";
                        
                        // Opsional: Kosongkan data lama agar inputan bersih
                        $skripsi['judul'] = '';
                        $skripsi['tema'] = '';
                        $skripsi['alasan_p1'] = '';
                        $skripsi['alasan_p2'] = '';
                        $is_exist = false; // Anggap input baru
                    }
                    ?>

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

                    <?php if ($pesan_status): ?>
                        <div class="alert <?= $warna_alert; ?> alert-dismissible fade show shadow-sm">
                            <h5><i class="icon fas fa-info-circle"></i> Status Pengajuan:</h5>
                            <?= $pesan_status; ?>
                        </div>
                    <?php endif; ?>

                    <?php if (validation_errors()): ?>
                        <div class="alert alert-warning alert-dismissible fade show shadow-sm">
                            <button type="button" class="close" data-dismiss="alert">&times;</button>
                            <h5><i class="icon fas fa-exclamation-triangle"></i> Perhatian!</h5>
                            <?php echo validation_errors(); ?>
                        </div>
                    <?php endif; ?>

                    <div class="card card-primary card-outline shadow-sm mb-4">
                        <div class="card-header bg-white">
                            <h3 class="card-title font-weight-bold text-primary">
                                <i class="fas fa-file-signature mr-1"></i>
                                <?php echo $is_exist ? 'Data Pengajuan Saat Ini' : 'Form Pengajuan Judul Baru'; ?>
                            </h3>
                        </div>
                        
                        <?php echo form_open('mahasiswa/submit_judul'); ?>
                        <div class="card-body bg-light">
                            
                            <div class="callout callout-info bg-white border-info shadow-sm mb-4">
                                <p class="mb-0 text-sm text-dark">
                                    <i class="fas fa-info-circle mr-1 text-info"></i> 
                                    Silakan isi judul, tema penelitian, dan <b>alasan pemilihan dosen</b> yang telah disetujui secara offline pada tahap pra-proposal.
                                </p>
                            </div>

                            <div class="bg-white p-4 rounded border shadow-sm mb-4">
                                <h5 class="text-primary mb-3 font-weight-bold border-bottom pb-2"><i class="fas fa-book mr-1"></i> Detail Penelitian</h5>
                                <div class="form-group">
                                    <label for="judul">Judul Skripsi <span class="text-danger">*</span></label>
                                    <textarea name="judul" id="judul" class="form-control" rows="3" placeholder="Masukkan judul lengkap skripsi..." required <?= $is_locked ? 'readonly' : ''; ?>><?php echo set_value('judul', ($is_exist && isset($skripsi['judul'])) ? $skripsi['judul'] : ''); ?></textarea>
                                </div>

                                <div class="form-group mb-0">
                                    <label for="tema">Tema Penelitian <span class="text-danger">*</span></label>
                                    <select name="tema" id="tema" class="form-control" required <?= $is_locked ? 'disabled' : ''; ?>>
                                        <option value="">-- Pilih Tema --</option>
                                        <option value="Software Engineering" <?php echo set_select('tema', 'Software Engineering', ($is_exist && isset($skripsi['tema']) && $skripsi['tema'] == 'Software Engineering')); ?>>Software Engineering</option>
                                        <option value="Networking" <?php echo set_select('tema', 'Networking', ($is_exist && isset($skripsi['tema']) && $skripsi['tema'] == 'Networking')); ?>>Networking</option>
                                        <option value="Artificial Intelligence" <?php echo set_select('tema', 'Artificial Intelligence', ($is_exist && isset($skripsi['tema']) && $skripsi['tema'] == 'Artificial Intelligence')); ?>>Artificial Intelligence</option>
                                    </select>
                                    <?php if($is_locked): ?>
                                        <input type="hidden" name="tema" value="<?php echo isset($skripsi['tema']) ? $skripsi['tema'] : ''; ?>">
                                    <?php endif; ?>
                                </div>
                            </div>
                            
                            <div class="bg-white p-4 rounded border shadow-sm mb-2">
                                <h5 class="text-primary mb-3 font-weight-bold border-bottom pb-2"><i class="fas fa-user-tie mr-1"></i> Usulan Dosen Pembimbing</h5>
                                <p class="text-muted text-sm mb-3">Pilih dosen dan berikan alasan kuat mengapa Anda mengusulkan beliau berdasarkan topik penelitian Anda.</p>

                                <div class="row">
                                    <div class="col-md-6 border-right">
                                        <div class="form-group">
                                            <label>Pembimbing 1 <span class="text-danger">*</span></label>
                                            <div class="input-group mb-2">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text bg-primary text-white"><i class="fas fa-user"></i></span>
                                                </div>
                                                <select name="pembimbing1" class="form-control" required <?= $is_locked ? 'disabled' : ''; ?>>
                                                    <option value="">-- Pilih Dosen Utama --</option>
                                                    <?php foreach ($dosen_list as $dsn): ?>
                                                        <option value="<?php echo $dsn['id']; ?>" <?php echo set_select('pembimbing1', $dsn['id'], ($is_exist && isset($skripsi['pembimbing1']) && $skripsi['pembimbing1'] == $dsn['id'])); ?>>
                                                            <?php echo $dsn['nama']; ?>
                                                        </option>
                                                    <?php endforeach; ?>
                                                </select>
                                                <?php if($is_locked): ?>
                                                    <input type="hidden" name="pembimbing1" value="<?php echo isset($skripsi['pembimbing1']) ? $skripsi['pembimbing1'] : ''; ?>">
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                        <div class="form-group mb-0">
                                            <label class="text-muted small">Alasan Memilih Pembimbing 1 <span class="text-danger">*</span></label>
                                            <textarea name="alasan_p1" class="form-control" rows="4" placeholder="Jelaskan alasan Anda memilih dosen ini (cth: keahlian sesuai, hasil diskusi awal...)" required <?= $is_locked ? 'readonly' : ''; ?>><?php echo set_value('alasan_p1', ($is_exist && isset($skripsi['alasan_p1'])) ? $skripsi['alasan_p1'] : ''); ?></textarea>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Pembimbing 2 <span class="text-danger">*</span></label>
                                            <div class="input-group mb-2">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text bg-info text-white"><i class="fas fa-user"></i></span>
                                                </div>
                                                <select name="pembimbing2" class="form-control" required <?= $is_locked ? 'disabled' : ''; ?>>
                                                    <option value="">-- Pilih Dosen Pendamping --</option>
                                                    <?php foreach ($dosen_list as $dsn): ?>
                                                        <option value="<?php echo $dsn['id']; ?>" <?php echo set_select('pembimbing2', $dsn['id'], ($is_exist && isset($skripsi['pembimbing2']) && $skripsi['pembimbing2'] == $dsn['id'])); ?>>
                                                            <?php echo $dsn['nama']; ?>
                                                        </option>
                                                    <?php endforeach; ?>
                                                </select>
                                                <?php if($is_locked): ?>
                                                    <input type="hidden" name="pembimbing2" value="<?php echo isset($skripsi['pembimbing2']) ? $skripsi['pembimbing2'] : ''; ?>">
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                        <div class="form-group mb-0">
                                            <label class="text-muted small">Alasan Memilih Pembimbing 2 <span class="text-danger">*</span></label>
                                            <textarea name="alasan_p2" class="form-control" rows="4" placeholder="Jelaskan alasan Anda memilih dosen pendamping ini..." required <?= $is_locked ? 'readonly' : ''; ?>><?php echo set_value('alasan_p2', ($is_exist && isset($skripsi['alasan_p2'])) ? $skripsi['alasan_p2'] : ''); ?></textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>
                        
                        <div class="card-footer bg-white border-top text-right p-3">
                            <?php if ($is_locked): ?>
                                <?php if ($is_exist && $status_acc !== 'diterima'): ?>
                                    <button type="button" class="btn btn-warning px-4 shadow-sm" data-toggle="modal" data-target="#modalEditJudul">
                                        <i class="fas fa-edit mr-1"></i> Buka Edit Judul
                                    </button>
                                <?php else: ?>
                                    <button type="button" class="btn btn-secondary px-4 shadow-sm" disabled>
                                        <i class="fas fa-lock mr-1"></i> Form Dikunci Permanen
                                    </button>
                                <?php endif; ?>
                            <?php else: ?>
                                <button type="submit" class="btn btn-primary px-5 shadow-sm font-weight-bold">
                                    <i class="fas fa-paper-plane mr-1"></i> 
                                    Kirim Pengajuan Judul
                                </button>
                            <?php endif; ?>
                        </div>
                        <?php echo form_close(); ?>
                    </div>

                    <?php if (!empty($riwayat_judul)): ?>
                    <div class="card card-outline shadow-sm mb-5">
                        <div class="card-header bg-navy text-white">
                            <h3 class="card-title font-weight-bold m-0">
                                <i class="fas fa-history mr-1"></i> Riwayat Pengajuan Anda
                            </h3>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover table-striped mb-0">
                                    <thead class="bg-light">
                                        <tr>
                                            <th class="text-center" style="width: 5%;">No</th>
                                            <th style="width: 15%;">Tgl Pengajuan</th>
                                            <th style="width: 30%;">Judul & Tema</th>
                                            <th style="width: 35%;">Usulan & Alasan Pembimbing</th>
                                            <th class="text-center" style="width: 15%;">Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php $no = 1; foreach ($riwayat_judul as $index => $row): 
                                            // Data dengan Index 0 otomatis adalah data PALING BARU
                                            $is_old = ($index > 0); 
                                            $row_class = $is_old ? 'bg-light' : '';
                                        ?>
                                            <tr class="<?php echo $row_class; ?>">
                                                <td class="text-center text-muted font-weight-bold pt-3"><?php echo $no++; ?></td>
                                                <td class="pt-3 text-sm">
                                                    <i class="far fa-calendar-alt text-muted mr-1"></i>
                                                    <?php echo date('d M Y', strtotime($row['tgl_pengajuan_judul'])); ?>
                                                </td>
                                                <td class="pt-3">
                                                    <span class="font-weight-bold d-block <?php echo $is_old ? 'text-secondary' : 'text-dark'; ?>" style="line-height: 1.3;"><?php echo $row['judul']; ?></span>
                                                    <span class="badge badge-info mt-2"><i class="fas fa-tag mr-1"></i> <?php echo $row['tema']; ?></span>
                                                    <span class="badge badge-light border mt-2 ml-1 text-muted"><?php echo $row['skema']; ?></span>
                                                </td>
                                                <td class="pt-3 text-sm">
                                                    <div class="mb-2 pb-2 border-bottom">
                                                        <strong class="<?php echo $is_old ? 'text-secondary' : 'text-primary'; ?>"><i class="fas fa-user-tie mr-1"></i> P1: <?php echo $row['nama_p1'] ?: '-'; ?></strong>
                                                        <div class="text-muted font-italic mt-1 pl-3 border-left" style="border-color: #ddd !important;">
                                                            "<?php echo !empty($row['alasan_p1']) ? htmlspecialchars($row['alasan_p1']) : 'Tidak ada alasan.'; ?>"
                                                        </div>
                                                    </div>
                                                    <div>
                                                        <strong class="text-secondary"><i class="fas fa-user-tie mr-1"></i> P2: <?php echo $row['nama_p2'] ?: '-'; ?></strong>
                                                        <div class="text-muted font-italic mt-1 pl-3 border-left" style="border-color: #ddd !important;">
                                                            "<?php echo !empty($row['alasan_p2']) ? htmlspecialchars($row['alasan_p2']) : 'Tidak ada alasan.'; ?>"
                                                        </div>
                                                    </div>
                                                </td>
                                                <td class="text-center pt-3 align-middle">
                                                    <?php 
                                                    $status = strtolower($row['status_acc_kaprodi']);
                                                    
                                                    // Jika judul lama, paksa warna badgenya abu-abu agar tidak memecah fokus
                                                    if($is_old) {
                                                        $badge = 'secondary';
                                                    } else {
                                                        $badge = 'secondary';
                                                        if($status == 'diterima') $badge = 'success';
                                                        elseif($status == 'ditolak') $badge = 'danger';
                                                        elseif($status == 'menunggu') $badge = 'warning';
                                                    }
                                                    ?>
                                                    <span class="badge badge-<?php echo $badge; ?> badge-pill px-3 py-2 shadow-sm" style="font-size: 0.85rem;">
                                                        <?php echo strtoupper($status); ?>
                                                    </span>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>

                </div>
            </div>
        </div>
    </section>
</div>

<?php if ($is_exist): ?>
<div class="modal fade" id="modalEditJudul" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered" role="document">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-warning">
                <h5 class="modal-title font-weight-bold text-dark">
                    <i class="fas fa-edit mr-2"></i> Edit Pengajuan Judul
                </h5>
                <button type="button" class="close text-dark" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            
            <?php echo form_open('mahasiswa/update_judul/'.$skripsi['id']); ?>
            <div class="modal-body bg-light p-4">
                
                <div class="alert alert-info border-info shadow-sm mb-4">
                    <i class="fas fa-info-circle mr-2"></i>
                    <b>Perhatian:</b> Judul dan form lama akan dipindahkan ke "Riwayat Pengajuan" jika Anda menekan tombol simpan perubahan.
                </div>

                <div class="bg-white p-4 rounded border shadow-sm mb-4">
                    <h6 class="text-primary font-weight-bold mb-3"><i class="fas fa-book mr-1"></i> Data Penelitian Baru</h6>
                    <div class="form-group">
                        <label for="edit_judul">Judul Skripsi Baru <span class="text-danger">*</span></label>
                        <textarea name="judul" id="edit_judul" class="form-control" rows="3" required><?php echo htmlspecialchars($skripsi['judul']); ?></textarea>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group mb-0">
                                <label for="edit_tema">Tema Penelitian Baru <span class="text-danger">*</span></label>
                                <select name="tema" id="edit_tema" class="form-control" required>
                                    <option value="">-- Pilih Tema --</option>
                                    <option value="Software Engineering" <?php echo ($skripsi['tema'] == 'Software Engineering') ? 'selected' : ''; ?>>Software Engineering</option>
                                    <option value="Networking" <?php echo ($skripsi['tema'] == 'Networking') ? 'selected' : ''; ?>>Networking</option>
                                    <option value="Artificial Intelligence" <?php echo ($skripsi['tema'] == 'Artificial Intelligence') ? 'selected' : ''; ?>>Artificial Intelligence</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group mb-0">
                                <label for="edit_tgl">Tanggal Pengajuan Ulang<span class="text-danger">*</span></label>
                                <input type="text" class="form-control bg-light" value="<?php echo date('d M Y'); ?>" readonly disabled>
                                <input type="hidden" name="tgl_pengajuan_judul" value="<?php echo date('Y-m-d H:i:s'); ?>">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white p-4 rounded border shadow-sm">
                    <h6 class="text-primary font-weight-bold mb-3"><i class="fas fa-user-tie mr-1"></i> Usulan Pembimbing & Alasan Baru</h6>
                    <div class="row">
                        <div class="col-md-6 border-right">
                            <div class="form-group">
                                <label>Pembimbing 1 <span class="text-danger">*</span></label>
                                <select name="pembimbing1" class="form-control" required>
                                    <option value="">-- Pilih Dosen --</option>
                                    <?php foreach ($dosen_list as $dsn): ?>
                                        <option value="<?php echo $dsn['id']; ?>" <?php echo ($skripsi['pembimbing1'] == $dsn['id']) ? 'selected' : ''; ?>>
                                            <?php echo $dsn['nama']; ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="form-group mb-0">
                                <label class="text-muted small">Alasan Baru Pilih P1 <span class="text-danger">*</span></label>
                                <textarea name="alasan_p1" class="form-control" rows="3" required><?php echo htmlspecialchars(isset($skripsi['alasan_p1']) ? $skripsi['alasan_p1'] : ''); ?></textarea>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Pembimbing 2 <span class="text-danger">*</span></label>
                                <select name="pembimbing2" class="form-control" required>
                                    <option value="">-- Pilih Dosen --</option>
                                    <?php foreach ($dosen_list as $dsn): ?>
                                        <option value="<?php echo $dsn['id']; ?>" <?php echo ($skripsi['pembimbing2'] == $dsn['id']) ? 'selected' : ''; ?>>
                                            <?php echo $dsn['nama']; ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="form-group mb-0">
                                <label class="text-muted small">Alasan Baru Pilih P2 <span class="text-danger">*</span></label>
                                <textarea name="alasan_p2" class="form-control" rows="3" required><?php echo htmlspecialchars(isset($skripsi['alasan_p2']) ? $skripsi['alasan_p2'] : ''); ?></textarea>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

            <div class="modal-footer bg-white">
                <button type="button" class="btn btn-secondary px-4 font-weight-bold" data-dismiss="modal">Batalkan</button>
                <button type="submit" class="btn btn-warning px-5 font-weight-bold shadow-sm">
                    <i class="fas fa-save mr-1"></i> Simpan & Kirim Ulang
                </button>
            </div>
            <?php echo form_close(); ?>
        </div>
    </div>
</div>
<?php endif; ?>