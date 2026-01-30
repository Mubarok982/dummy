<div class="content-wrapper">
    <section class="content">
        <div class="container-fluid">
            
            <div class="alert alert-info">
                <i class="fas fa-info-circle mr-1"></i> Data di bawah ini adalah mahasiswa yang progres bimbingan <b>BAB 3</b>-nya telah disetujui penuh (ACC) oleh kedua dosen pembimbing.
            </div>

            <div class="card card-success card-outline shadow-sm">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-user-graduate mr-1"></i> Daftar Calon Peserta Sempro
                    </h3>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="example1" class="table table-bordered table-striped table-hover">
                            <thead>
                                <tr class="text-center">
                                    <th width="5%">No</th>
                                    <th>Mahasiswa</th>
                                    <th>Prodi</th>
                                    <th width="30%">Judul Skripsi</th>
                                    <th>Pembimbing</th>
                                    <th>Tanggal ACC</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $no = 1; foreach ($mahasiswa as $m): ?>
                                <tr>
                                    <td class="text-center"><?php echo $no++; ?></td>
                                    <td>
                                        <div class="user-block">
                                            <?php 
                                            $foto_path = FCPATH . 'uploads/profile/' . $m['foto'];
                                            $src_foto = (file_exists($foto_path) && $m['foto']) 
                                                ? base_url('uploads/profile/'.$m['foto']) 
                                                : base_url('assets/image/default.png');
                                            ?>
                                            <img class="img-circle img-bordered-sm" src="<?php echo $src_foto; ?>" alt="user image">
                                            <span class="username">
                                                <a href="#"><?php echo $m['nama']; ?></a>
                                            </span>
                                            <span class="description"><?php echo $m['npm']; ?> - <?php echo $m['angkatan']; ?></span>
                                        </div>
                                    </td>
                                    <td class="text-center"><?php echo $m['prodi']; ?></td>
                                    <td>
                                        <small><?php echo $m['judul']; ?></small>
                                    </td>
                                    <td class="text-sm">
                                        <b>P1:</b> <?php echo $m['nama_p1']; ?><br>
                                        <b>P2:</b> <?php echo $m['nama_p2']; ?>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge badge-success">
                                            <i class="far fa-calendar-check mr-1"></i>
                                            <?php echo date('d M Y', strtotime($m['tgl_acc'])); ?>
                                        </span>
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