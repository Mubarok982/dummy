<div class="container-fluid">
    <h1 class="h3 mb-4 text-gray-800"><?php echo $title; ?></h1>

    <?php if ($this->session->flashdata('pesan_sukses')): ?>
        <div class="alert alert-success shadow"><?php echo $this->session->flashdata('pesan_sukses'); ?></div>
    <?php endif; ?>
    <?php if ($this->session->flashdata('pesan_error')): ?>
        <div class="alert alert-danger shadow"><?php echo $this->session->flashdata('pesan_error'); ?></div>
    <?php endif; ?>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Daftar Pengajuan Dosen Pembimbing</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Mahasiswa (NPM)</th>
                            <th>Judul Skripsi</th>
                            <th>Pembimbing 1</th>
                            <th>Pembimbing 2</th>
                            <th>Tgl Pengajuan</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $no = 1; foreach ($pengajuan as $p): ?>
                            <tr>
                                <td><?php echo $no++; ?></td>
                                <td><?php echo htmlspecialchars($p['nama_mahasiswa']); ?><br><small class="text-muted"><?php echo $p['npm']; ?></small></td>
                                <td><?php echo htmlspecialchars($p['judul']); ?></td>
                                <td><?php echo htmlspecialchars($p['nama_p1']); ?></td>
                                <td><?php echo htmlspecialchars($p['nama_p2']); ?></td>
                                <td><?php echo date('d M Y', strtotime($p['tgl_pengajuan_judul'])); ?></td>
                                <td>
                                    <a href="<?php echo base_url('operator/proses_acc_dospem/' . $p['id'] . '/setujui'); ?>" 
                                       onclick="return confirm('Setujui pengajuan ini? Mahasiswa akan dapat mulai bimbingan.')"
                                       class="btn btn-success btn-sm"><i class="fas fa-check"></i> Setujui</a>
                                    
                                    <a href="<?php echo base_url('operator/proses_acc_dospem/' . $p['id'] . '/tolak'); ?>"
                                       onclick="return confirm('Tolak pengajuan ini? Mahasiswa harus mengajukan ulang.')"
                                       class="btn btn-danger btn-sm"><i class="fas fa-times"></i> Tolak</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        <?php if (empty($pengajuan)): ?>
                            <tr>
                                <td colspan="7" class="text-center">Tidak ada pengajuan dosen pembimbing yang perlu disetujui.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>