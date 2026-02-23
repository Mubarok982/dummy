<div class="content-wrapper">
    
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0 text-dark">Monitoring Progres Mahasiswa</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="<?php echo base_url('dashboard'); ?>">Home</a></li>
                        <li class="breadcrumb-item active">Monitoring Progres</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">
            
            <?php
            // --- HELPER FUNCTION (Aman dari error redeclare) ---
            if (!function_exists('get_status_badge')) {
                function get_status_badge($status) {
                    $status = strtolower($status);
                    if ($status == 'acc' || $status == '100') return '<span class="badge badge-success px-2"><i class="fas fa-check"></i> ACC</span>';
                    if ($status == 'revisi' || $status == '0') return '<span class="badge badge-danger px-2"><i class="fas fa-times"></i> Revisi</span>';
                    if ($status == 'menunggu' || $status == '50') return '<span class="badge badge-warning text-white px-2"><i class="fas fa-clock"></i> Proses</span>';
                    return '<span class="badge badge-secondary px-2">-</span>';
                }
            }

            if (!function_exists('get_status_bimbingan')) {
                function get_status_bimbingan($m) {
                    $status_sempro = isset($m['status_sempro']) ? $m['status_sempro'] : '';
                    $status_ujian = isset($m['status_ujian']) ? $m['status_ujian'] : '';
                    $status_acc = isset($m['status_acc_kaprodi']) ? $m['status_acc_kaprodi'] : '';
                    $p1 = isset($m['progres_dosen1']) ? intval($m['progres_dosen1']) : null;
                    $p2 = isset($m['progres_dosen2']) ? intval($m['progres_dosen2']) : null;
                    $last_bab = isset($m['last_bab']) ? intval($m['last_bab']) : 0;
                    $max_bab = isset($m['max_bab']) ? intval($m['max_bab']) : 6;

                    // Jika dinyatakan mengulang dari sempro atau judul ditolak -> Mengulang
                    if (strtolower($status_ujian) == 'mengulang' || strtolower($status_acc) == 'ditolak') {
                        return ['label' => 'MENGULANG', 'class' => 'badge-danger'];
                    }

                    if ($status_sempro == 'Menunggu Plagiarisme') return ['label' => 'MENUNGGU CEK PLAGIARISME', 'class' => 'badge-secondary'];

                    if ($p1 === 100 && $p2 === 100) {
                        if ($last_bab >= $max_bab) return ['label' => 'SIAP PENDADARAN', 'class' => 'badge-success'];
                        if ($last_bab == 3) return ['label' => 'SIAP SEMPRO', 'class' => 'badge-info'];
                        if ($last_bab >= 4) return ['label' => 'BIMBINGAN', 'class' => 'badge-primary'];
                    }

                    if ($status_sempro == 'Siap Pendadaran') return ['label' => 'SIAP PENDADARAN', 'class' => 'badge-success'];
                    if ($status_sempro == 'Siap Sempro') return ['label' => 'SIAP SEMPRO', 'class' => 'badge-info'];

                    return ['label' => 'BIMBINGAN', 'class' => 'badge-primary'];
                }
            }
            ?>

            <div class="card shadow-sm mb-3">
                <div class="card-body p-2"> 
                    <form action="<?php echo base_url('operator/monitoring_progres'); ?>" method="GET">
                        <div class="form-row align-items-center">
                            
                            <div class="col-auto">
                                <span class="text-muted font-weight-bold mr-2 ml-2"><i class="fas fa-filter"></i> Filter:</span>
                            </div>
                            
                            <div class="col-md-3 col-sm-6 my-1">
                                <select name="prodi" class="form-control form-control-sm">
                                    <option value="all">- Semua Program Studi -</option>
                                    <?php if(!empty($list_prodi)): ?>
                                        <?php foreach ($list_prodi as $prodi_option): ?>
                                            <option value="<?php echo $prodi_option['prodi']; ?>" <?php echo ($prodi == $prodi_option['prodi']) ? 'selected' : ''; ?>><?php echo $prodi_option['prodi']; ?></option>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </select>
                            </div>

                            <!-- Angkatan filter removed as requested -->

                            <div class="col-md-4 col-sm-12 my-1">
                                <div class="input-group input-group-sm">
                                    <input type="text" name="keyword" class="form-control" placeholder="Cari Nama / NPM / Judul..." value="<?php echo $keyword; ?>">
                                    <div class="input-group-append">
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fas fa-search"></i> Cari
                                        </button>
                                    </div>
                                </div>
                            </div>

                        </div>
                        <div class="form-row align-items-center mt-1">
                            
                            <!-- Top sort controls removed; header sorting enabled -->

                            <?php if($prodi || $keyword || $angkatan || ($sort_by != 'nama') || ($sort_order != 'asc')): ?>
                            <div class="col-auto my-1">
                                <a href="<?php echo base_url('operator/monitoring_progres'); ?>" class="btn btn-outline-danger btn-sm" title="Reset Filter">
                                    <i class="fas fa-undo"></i> Reset
                                </a>
                            </div>
                            <?php endif; ?>

                        </div>
                    </form>
                </div>
            </div>

            <div class="row">
                <div class="col-12">

                    <div class="card card-info card-outline shadow-sm">
                        <div class="card-header border-0">
                            <h3 class="card-title font-weight-bold text-info">
                                <i class="fas fa-chart-bar mr-1"></i> Data Progres
                            </h3>
                        </div>

                        <div class="card-body table-responsive p-0">
                            <table class="table table-hover table-striped text-nowrap table-sm align-middle">
                                <thead>
                                    <tr class="text-center bg-light">
                                            <th style="width: 7%">No</th>
                                            <th style="width: 10%" class="sortable" data-sort="npm">NPM</th>
                                            <th style="width: 20%" class="text-left sortable" data-sort="nama">Nama Mahasiswa</th>
                                            <th style="width: 15%" class="sortable" data-sort="prodi">Prodi</th>
                                            <th style="width: 20%" class="text-left sortable" data-sort="judul">Judul Skripsi</th>
                                            <th style="width: 10%">Posisi Bab</th>
                                            <th style="width: 11%">Status Bimbingan</th>
                                        </tr>
                                </thead>
                                <tbody>
                                    <?php if (empty($laporan)): ?>
                                        <tr>
                                            <td colspan="7" class="text-center py-5 text-muted">
                                                <i class="fas fa-clipboard-list fa-3x mb-3 opacity-50"></i><br>
                                                Data tidak ditemukan dengan filter tersebut.
                                            </td>
                                        </tr>
                                    <?php else: ?>
                                        <?php 
                                        $no = $start_index + 1;
                                        foreach ($laporan as $mhs): 
                                        ?>
                                        <tr>
                                            <td class="align-middle text-center text-muted">
                                                <?php echo $no++; ?>
                                                <br>
                                                <small class="text-muted">ID: <?php echo isset($mhs['id_skripsi']) ? $mhs['id_skripsi'] : '-'; ?></small>
                                            </td>
                                            <td class="align-middle text-center"><span class="badge badge-light border"><?php echo $mhs['npm']; ?></span></td>
                                            
                                            <td class="align-middle">
                                                <span class="font-weight-bold text-dark"><?php echo $mhs['nama']; ?></span>
                                            </td>
                                            
                                            <td class="align-middle text-muted small text-center"><?php echo $mhs['prodi']; ?></td>
                                            
                                            <td class="align-middle text-wrap" style="min-width: 250px;">
                                                <?php if ($mhs['judul']): ?>
                                                    <span class="d-block text-sm" style="line-height: 1.3;" title="<?php echo $mhs['judul']; ?>">
                                                        <?php echo (strlen($mhs['judul']) > 50) ? substr($mhs['judul'], 0, 50) . '...' : $mhs['judul']; ?>
                                                    </span>
                                                    <div class="text-xs text-muted mt-1">
                                                        <i class="fas fa-user-tie text-primary"></i> P1: <?php echo $mhs['p1'] ?: '-'; ?> &nbsp;|&nbsp; 
                                                        <i class="fas fa-user-tie text-secondary"></i> P2: <?php echo $mhs['p2'] ?: '-'; ?>
                                                    </div>
                                                <?php else: ?>
                                                    <span class="badge badge-secondary font-weight-normal">Belum Ada Judul</span>
                                                <?php endif; ?>
                                            </td>

                                            <td class="align-middle text-center">
                                                <?php if ($mhs['last_bab'] == 'Belum Mulai' || $mhs['last_bab'] == 0): ?>
                                                    <span class="badge badge-secondary">Belum Mulai</span>
                                                <?php else: ?>
                                                    <span class="badge badge-info px-2 py-1">
                                                        BAB <?php echo $mhs['last_bab']; ?>
                                                    </span>
                                                <?php endif; ?>
                                            </td>

                                            <td class="align-middle text-center">
                                                <?php 
                                                    // Prefer model-provided unified status if available
                                                    if (isset($mhs['status_bimbingan'])) {
                                                        $label = $mhs['status_bimbingan'];
                                                        $cls = isset($mhs['status_class']) ? $mhs['status_class'] : 'badge-primary';
                                                        echo '<span class="badge '. $cls . ' px-2 py-1 font-weight-bold">' . $label . '</span>';
                                                    } else {
                                                        $sb = get_status_bimbingan($mhs);
                                                        echo '<span class="badge '. $sb['class'] . ' px-2 py-1 font-weight-bold">' . $sb['label'] . '</span>';
                                                    }
                                                ?>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                        
                        <div class="card-footer py-2 bg-white">
                            <div class="row align-items-center">
                                <div class="col-sm-6 text-muted small">
                                    Total Data: <b><?php echo $total_rows; ?></b> Mahasiswa
                                </div>
                                <div class="col-sm-6">
                                    <div class="float-right">
                                        <?php echo $pagination; ?>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>

                </div>
            </div>

        </div>
    </section>
</div>