<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0 text-dark">Pengaturan Kaprodi</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="<?php echo base_url('dashboard'); ?>">Home</a></li>
                        <li class="breadcrumb-item"><a href="<?php echo base_url('dosen/manajemen_akun'); ?>">Manajemen Akun</a></li>
                        <li class="breadcrumb-item active">Pengaturan Kaprodi</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-8">
                    <div class="card card-warning shadow-sm">
                        <div class="card-header">
                            <h3 class="card-title"><i class="fas fa-crown mr-2"></i>Kelola Kaprodi</h3>
                        </div>

                        <div class="card-body">
                            <p class="text-muted">Pilih dosen yang akan diangkat sebagai Kaprodi untuk setiap program studi.</p>

                            <?php
                            // Ambil data dosen per prodi
                            $prodi_list = ['Teknik Informatika S1', 'Teknologi Informasi D3'];
                            ?>

                            <?php foreach ($prodi_list as $prodi): ?>
                                <div class="mb-4">
                                    <h5 class="text-primary"><i class="fas fa-university mr-2"></i><?php echo $prodi; ?></h5>

                                    <?php
                                    // Ambil dosen untuk prodi ini
                                    $dosen_prodi = $this->db->select('a.id, a.nama, d.nidk, d.is_kaprodi')
                                                           ->from('mstr_akun a')
                                                           ->join('data_dosen d', 'a.id = d.id')
                                                           ->where('d.prodi', $prodi)
                                                           ->get()
                                                           ->result_array();
                                    ?>

                                    <form action="<?php echo base_url('dosen/update_kaprodi'); ?>" method="POST" class="mb-3">
                                        <input type="hidden" name="prodi" value="<?php echo $prodi; ?>">

                                        <div class="form-group">
                                            <label>Pilih Kaprodi:</label>
                                            <select name="kaprodi_id" class="form-control" onchange="this.form.submit()">
                                                <option value="">-- Tidak ada Kaprodi --</option>
                                                <?php foreach ($dosen_prodi as $dosen): ?>
                                                    <option value="<?php echo $dosen['id']; ?>" <?php echo ($dosen['is_kaprodi'] == 1) ? 'selected' : ''; ?>>
                                                        <?php echo $dosen['nama'] . ' (' . $dosen['nidk'] . ')'; ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                    </form>

                                    <div class="table-responsive">
                                        <table class="table table-sm table-striped">
                                            <thead class="bg-light">
                                                <tr>
                                                    <th>Nama Dosen</th>
                                                    <th>NIDK</th>
                                                    <th>Status</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php if (empty($dosen_prodi)): ?>
                                                    <tr>
                                                        <td colspan="3" class="text-center text-muted">Belum ada dosen di program studi ini</td>
                                                    </tr>
                                                <?php else: ?>
                                                    <?php foreach ($dosen_prodi as $dosen): ?>
                                                        <tr>
                                                            <td><?php echo $dosen['nama']; ?></td>
                                                            <td><?php echo $dosen['nidk']; ?></td>
                                                            <td>
                                                                <?php if ($dosen['is_kaprodi'] == 1): ?>
                                                                    <span class="badge badge-warning">
                                                                        <i class="fas fa-crown mr-1"></i>Kaprodi
                                                                    </span>
                                                                <?php else: ?>
                                                                    <span class="text-muted">Dosen</span>
                                                                <?php endif; ?>
                                                            </td>
                                                        </tr>
                                                    <?php endforeach; ?>
                                                <?php endif; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>

                                <hr>
                            <?php endforeach; ?>

                        </div>

                        <div class="card-footer">
                            <a href="<?php echo base_url('dosen/manajemen_akun'); ?>" class="btn btn-secondary">
                                <i class="fas fa-arrow-left mr-1"></i> Kembali ke Manajemen Akun
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
