<?php
// --- HELPER FUNCTION (Aman dari error redeclare) ---
if (!function_exists('get_status_badge')) {
    function get_status_badge($status) {
        $status = strtolower($status);
        if ($status == 'acc' || $status == '100') return '<span class="badge badge-success"><i class="fas fa-check"></i> ACC</span>';
        if ($status == 'revisi' || $status == '0') return '<span class="badge badge-danger"><i class="fas fa-times"></i> Revisi</span>';
        if ($status == 'menunggu' || $status == '50') return '<span class="badge badge-warning text-white"><i class="fas fa-clock"></i> Proses</span>';
        return '<span class="badge badge-secondary">-</span>';
    }
}
?>

<div class="container-fluid">
    
    <div class="card shadow-sm mb-3">
        <div class="card-body p-2"> 
            <form action="<?php echo base_url('operator/monitoring_progres'); ?>" method="GET">
                <div class="form-row align-items-center">
                    
                    <div class="col-auto">
                        <span class="text-muted font-weight-bold mr-2"><i class="fas fa-filter"></i> Filter:</span>
                    </div>
                    
                    <div class="col-md-4 col-sm-6 my-1">
                        <select name="prodi" class="form-control form-control-sm">
                            <option value="">- Semua Program Studi -</option>
                            <option value="Teknik Informatika S1" <?php echo ($this->input->get('prodi') == 'Teknik Informatika S1') ? 'selected' : ''; ?>>Teknik Informatika S1</option>
                            <option value="Teknologi Informasi D3" <?php echo ($this->input->get('prodi') == 'Teknologi Informasi D3') ? 'selected' : ''; ?>>Teknologi Informasi D3</option>
                        </select>
                    </div>

                    <div class="col-md-4 col-sm-12 my-1">
                        <div class="input-group input-group-sm">
                            <input type="text" name="keyword" class="form-control" placeholder="Cari Nama / NPM / Judul..." value="<?php echo $this->input->get('keyword'); ?>">
                            <div class="input-group-append">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-search"></i> Cari
                                </button>
                            </div>
                        </div>
                    </div>

                    <?php if($this->input->get('prodi') || $this->input->get('keyword')): ?>
                    <div class="col-auto my-1">
                        <a href="<?php echo base_url('operator/monitoring_progres'); ?>" class="btn btn-outline-danger btn-sm" title="Reset Filter">
                            <i class="fas fa-times"></i>
                        </a>
                    </div>
                    <?php endif; ?>

                </div>
            </form>
        </div>
    </div>

    <div class="row">
        <div class="col-12">

            <div class="card card-info card-outline">
                <div class="card-body table-responsive p-0">
                    <table class="table table-hover table-striped text-nowrap table-sm">
                        <thead>
                            <tr class="text-center bg-light">
                                <th style="width: 5%">No</th>
                                <th style="width: 10%">NPM</th>
                                <th style="width: 20%">Nama Mahasiswa</th>
                                <th style="width: 15%">Prodi</th>
                                <th style="width: 20%">Judul Skripsi</th>
                                <th style="width: 10%">Posisi Bab</th>
                                <th style="width: 10%">Status P1</th>
                                <th style="width: 10%">Status P2</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($laporan)): ?>
                                <tr>
                                    <td colspan="8" class="text-center py-5 text-muted">
                                        <i class="fas fa-clipboard-list fa-3x mb-2"></i><br>
                                        Data tidak ditemukan dengan filter tersebut.
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php 
                                $no = $start_index + 1;
                                foreach ($laporan as $mhs): 
                                ?>
                                <tr>
                                    <td class="align-middle text-center"><?php echo $no++; ?></td>
                                    <td class="align-middle text-center"><?php echo $mhs['npm']; ?></td>
                                    <td class="align-middle">
                                        <span class="font-weight-bold text-dark"><?php echo $mhs['nama']; ?></span>
                                    </td>
                                    <td class="align-middle text-muted small"><?php echo $mhs['prodi']; ?></td>
                                    
                                    <td class="align-middle">
                                        <?php if ($mhs['judul']): ?>
                                            <span data-toggle="tooltip" title="<?php echo $mhs['judul']; ?>">
                                                <?php echo (strlen($mhs['judul']) > 35) ? substr($mhs['judul'], 0, 35) . '...' : $mhs['judul']; ?>
                                            </span>
                                            <div class="text-xs text-muted mt-1">
                                                P1: <?php echo $mhs['p1'] ?: '-'; ?> | P2: <?php echo $mhs['p2'] ?: '-'; ?>
                                            </div>
                                        <?php else: ?>
                                            <span class="badge badge-secondary font-weight-normal">Belum Ada Judul</span>
                                        <?php endif; ?>
                                    </td>

                                    <td class="align-middle text-center">
                                        <?php if ($mhs['last_bab'] == 'Belum Mulai'): ?>
                                            <span class="badge badge-secondary">0</span>
                                        <?php else: ?>
                                            <span class="badge badge-info px-2">
                                                <?php echo $mhs['last_bab']; ?>
                                            </span>
                                        <?php endif; ?>
                                    </td>

                                    <td class="align-middle text-center">
                                        <?php echo get_status_badge($mhs['status_p1']); ?>
                                    </td>
                                    <td class="align-middle text-center">
                                        <?php echo get_status_badge($mhs['status_p2']); ?>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
                
                <div class="card-footer py-2">
                    <div class="row align-items-center">
                        <div class="col-sm-6 text-muted small">
                            Total Data: <b><?php echo $total_rows; ?></b> Mahasiswa
                        </div>
                        <div class="col-sm-6">
                            <?php echo $pagination; ?>
                        </div>
                    </div>
                </div>

            </div>

        </div>
    </div>
</div>