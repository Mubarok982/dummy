<div class="content-wrapper">
    
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0 text-dark">Validasi Pengajuan Dosen</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="<?php echo base_url('dashboard'); ?>">Home</a></li>
                        <li class="breadcrumb-item active">Validasi Pengajuan</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">

            <?php if ($this->session->flashdata('pesan_sukses')): ?>
                <div class="alert alert-success alert-dismissible fade show shadow-sm">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                    <h5><i class="icon fas fa-check"></i> Berhasil!</h5>
                    <?php echo $this->session->flashdata('pesan_sukses'); ?>
                </div>
            <?php endif; ?>

            <?php if ($this->session->flashdata('pesan_error')): ?>
                <div class="alert alert-danger alert-dismissible fade show shadow-sm">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                    <h5><i class="icon fas fa-ban"></i> Gagal!</h5>
                    <?php echo $this->session->flashdata('pesan_error'); ?>
                </div>
            <?php endif; ?>

            <div class="card shadow mb-4 card-outline card-primary">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-file-signature mr-1"></i> Daftar Pengajuan Dosen Pembimbing
                    </h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover" id="dataTable" width="100%" cellspacing="0">
                            <thead class="bg-light text-center">
                                <tr>
                                    <th style="width: 5%">No</th>
                                    <th style="width: 20%">Mahasiswa</th>
                                    <th style="width: 25%">Judul Skripsi</th>
                                    <th style="width: 15%">Usulan Pembimbing</th>
                                    <th style="width: 10%">Tgl Pengajuan</th>
                                    <th style="width: 15%">Aksi</th>
                                    <th style="width: 10%">Edit</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($pengajuan)): ?>
                                    <tr>
                                        <td colspan="7" class="text-center py-5 text-muted">
                                            <i class="fas fa-inbox fa-3x mb-3 text-gray-300"></i><br>
                                            Tidak ada pengajuan dosen pembimbing yang perlu disetujui saat ini.
                                        </td>
                                    </tr>
                                <?php else: ?>
                                    <?php $no = 1; foreach ($pengajuan as $p): ?>
                                        <tr>
                                            <td class="align-middle text-center"><?php echo $no++; ?></td>
                                            
                                            <td class="align-middle">
                                                <div class="font-weight-bold"><?php echo htmlspecialchars($p['nama_mahasiswa']); ?></div>
                                                <div class="small text-muted">NPM: <?php echo $p['npm']; ?></div>
                                            </td>
                                            
                                            <td class="align-middle text-wrap">
                                                <span class="d-block" style="font-size: 0.9rem; line-height: 1.4;">
                                                    <?php echo htmlspecialchars($p['judul']); ?>
                                                </span>
                                            </td>
                                            
                                            <td class="align-middle small">
                                                <ul class="list-unstyled mb-0">
                                                    <li class="mb-1"><span class="badge badge-info p-1">P1</span> <?php echo htmlspecialchars($p['nama_p1']); ?></li>
                                                    <li><span class="badge badge-secondary p-1">P2</span> <?php echo htmlspecialchars($p['nama_p2']); ?></li>
                                                </ul>
                                            </td>
                                            
                                            <td class="align-middle text-center">
                                                <?php echo date('d M Y', strtotime($p['tgl_pengajuan_judul'])); ?>
                                            </td>

                                            <td class="align-middle text-center">
                                                <div class="btn-group-vertical btn-block">
                                                    <a href="<?php echo base_url('operator/proses_acc_dospem/' . $p['id'] . '/setujui'); ?>"
                                                       onclick="return confirm('Setujui pengajuan ini? Mahasiswa akan dapat mulai bimbingan.')"
                                                       class="btn btn-success btn-sm mb-1 shadow-sm">
                                                        <i class="fas fa-check mr-1"></i> Setujui
                                                    </a>

                                                    <a href="<?php echo base_url('operator/proses_acc_dospem/' . $p['id'] . '/tolak'); ?>"
                                                       onclick="return confirm('Tolak pengajuan ini? Mahasiswa harus mengajukan ulang.')"
                                                       class="btn btn-danger btn-sm shadow-sm">
                                                        <i class="fas fa-times mr-1"></i> Tolak
                                                    </a>
                                                </div>
                                            </td>

                                            <td class="align-middle text-center">
                                                <a href="<?php echo base_url('operator/edit_dospem/' . $p['id']); ?>"
                                                   class="btn btn-warning btn-sm shadow-sm">
                                                    <i class="fas fa-edit mr-1"></i> Edit
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div></section></div>```

### Corrections Made:
1.  **Wrapper:** Added `<div class="content-wrapper">`, `<section class="content-header">`, and `<section class="content">` to properly align content to the right of the sidebar.
2.  **Breadcrumb:** Added navigation breadcrumbs for better UX.
3.  **Table Layout:** Improved the table layout by combining the advisor columns into a single "Usulan Pembimbing" column for cleaner display, and vertically aligning the "Aksi" buttons.
4.  **Empty State:** Added a clearer empty state icon and message when there are no pending submissions.