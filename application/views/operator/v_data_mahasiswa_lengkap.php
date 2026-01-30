<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0 text-dark">Data Lengkap Mahasiswa</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="<?php echo base_url('dashboard'); ?>">Home</a></li>
                        <li class="breadcrumb-item active">Data Mahasiswa</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">
            <div class="card card-primary card-outline shadow-sm">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-users mr-1"></i> Daftar Mahasiswa
                    </h3>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="example1" class="table table-bordered table-striped table-hover">
                            <thead>
                                <tr class="text-center">
                                    <th width="5%">No</th>
                                    <th width="10%">Foto</th>
                                    <th>Nama Mahasiswa</th>
                                    <th>NPM</th>
                                    <th>Prodi</th>
                                    <th>Angkatan</th>
                                    <th>Status Skripsi</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $no = 1; foreach ($mahasiswa as $m): ?>
                                <tr>
                                    <td class="text-center"><?php echo $no++; ?></td>
                                    <td class="text-center">
                                        <?php 
                                        $foto_path = FCPATH . 'uploads/profile/' . $m['foto'];
                                        $src_foto = (file_exists($foto_path) && $m['foto']) 
                                            ? base_url('uploads/profile/'.$m['foto']) 
                                            : base_url('assets/image/default.png'); 
                                        ?>
                                        <img src="<?php echo $src_foto; ?>" class="img-circle elevation-1" style="width: 40px; height: 40px; object-fit: cover;">
                                    </td>
                                    <td><?php echo $m['nama']; ?></td>
                                    <td><?php echo $m['npm']; ?></td>
                                    <td><?php echo $m['prodi']; ?></td>
                                    <td class="text-center"><?php echo $m['angkatan']; ?></td>
                                    <td class="text-center">
                                        <?php if ($m['is_skripsi'] == 1): ?>
                                            <span class="badge badge-success"><i class="fas fa-check"></i> Sedang Skripsi</span>
                                        <?php else: ?>
                                            <span class="badge badge-secondary">Belum</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-center">
                                        <a href="<?php echo base_url('operator/edit_akun/' . $m['id'] . '?source=data_mahasiswa'); ?>" class="btn btn-xs btn-info" title="Edit Detail">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<script>
    $(function () {
        $("#example1").DataTable({
            "responsive": true,
            "autoWidth": false,
            "language": {
                "url": "//cdn.datatables.net/plug-ins/1.10.21/i18n/Indonesian.json"
            }
        });
    });
</script>