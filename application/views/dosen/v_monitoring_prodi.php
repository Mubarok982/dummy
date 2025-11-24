<?php 
$prodi = $this->session->userdata('prodi');
?>

<div class="container-fluid">
    
    <div class="row mb-3">
        <div class="col-md-12">
            <div class="callout callout-info shadow-sm bg-white">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="text-info font-weight-bold mb-1"><i class="fas fa-university mr-1"></i> Program Studi: <?php echo $prodi; ?></h5>
                        <p class="text-muted text-sm mb-0">Memantau progres skripsi seluruh mahasiswa angkatan aktif.</p>
                    </div>
                    <div class="text-right">
                         <h3 class="mb-0 text-dark font-weight-bold"><?php echo count($mahasiswa_prodi); ?></h3>
                         <small>Total Mahasiswa</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card card-outline card-purple shadow-sm">
                <div class="card-header">
                    <h3 class="card-title mt-1">
                        <i class="fas fa-list-alt mr-1"></i> Data Mahasiswa
                    </h3>
                    <div class="card-tools">
                        <div class="input-group input-group-sm" style="width: 250px;">
                            <input type="text" id="searchMhs" class="form-control float-right" placeholder="Cari Nama / NPM...">
                            <div class="input-group-append">
                                <button type="submit" class="btn btn-default">
                                    <i class="fas fa-search"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="card-body table-responsive p-0" style="height: 500px;">
                    <table class="table table-head-fixed table-hover text-nowrap table-striped" id="tableMonitoring">
                        <thead>
                            <tr class="text-center">
                                <th style="width: 5%;">No</th>
                                <th style="width: 10%;">NPM</th>
                                <th style="width: 20%;" class="text-left">Nama Mahasiswa</th>
                                <th style="width: 10%;">Angkatan</th>
                                <th style="width: 25%;" class="text-left">Judul Skripsi</th>
                                <th style="width: 20%;">Pembimbing</th>
                                <th style="width: 10%;">Progres Terakhir</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($mahasiswa_prodi)): ?>
                                <tr>
                                    <td colspan="7" class="text-center py-5 text-muted">
                                        <i class="fas fa-users-slash fa-3x mb-3 opacity-50"></i><br>
                                        Tidak ada data mahasiswa di prodi ini.
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php $no = 1; foreach ($mahasiswa_prodi as $m): ?>
                                <tr>
                                    <td class="align-middle text-center"><?php echo $no++; ?></td>
                                    <td class="align-middle text-center"><span class="badge badge-light border"><?php echo $m['npm']; ?></span></td>
                                    <td class="align-middle">
                                        <span class="font-weight-bold text-dark"><?php echo $m['nama']; ?></span>
                                    </td>
                                    <td class="align-middle text-center"><?php echo $m['angkatan']; ?></td>
                                    
                                    <td class="align-middle text-wrap" style="min-width: 250px;">
                                        <?php if($m['judul']): ?>
                                            <span class="text-sm"><?php echo $m['judul']; ?></span>
                                        <?php else: ?>
                                            <span class="badge badge-danger"><i class="fas fa-times-circle"></i> Belum Ada Judul</span>
                                        <?php endif; ?>
                                    </td>

                                    <td class="align-middle text-sm">
                                        <?php if($m['p1'] || $m['p2']): ?>
                                            <div class="text-muted"><i class="fas fa-user-tie text-primary"></i> P1: <?php echo $m['p1'] ?: '-'; ?></div>
                                            <div class="text-muted"><i class="fas fa-user-tie text-secondary"></i> P2: <?php echo $m['p2'] ?: '-'; ?></div>
                                        <?php else: ?>
                                            <span class="text-muted font-italic">- Belum ditentukan -</span>
                                        <?php endif; ?>
                                    </td>

                                    <td class="align-middle text-center">
                                        <?php 
                                        // Logic ambil progres
                                        $progres_terakhir = $this->M_Dosen->get_all_progres_skripsi($m['npm']);
                                        
                                        if ($progres_terakhir) {
                                            $last = end($progres_terakhir);
                                            
                                            // Styling Badge Bab
                                            echo '<div class="mb-1"><span class="badge badge-primary badge-pill px-3">BAB ' . $last['bab'] . '</span></div>';
                                            
                                            // Info ACC/Revisi P1 & P2 (Simple)
                                            $icon1 = ($last['progres_dosen1'] == 100) ? 'text-success fas fa-check-circle' : 'text-warning fas fa-clock';
                                            $icon2 = ($last['progres_dosen2'] == 100) ? 'text-success fas fa-check-circle' : 'text-warning fas fa-clock';
                                            
                                            echo '<small class="text-xs">';
                                            echo 'P1 <i class="'.$icon1.'"></i> | ';
                                            echo 'P2 <i class="'.$icon2.'"></i>';
                                            echo '</small>';

                                        } else {
                                            echo '<span class="badge badge-secondary">0%</span>';
                                        }
                                        ?>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
                <div class="card-footer clearfix bg-white">
                    <small class="text-muted float-right">Data diperbarui real-time.</small>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.getElementById('searchMhs').addEventListener('keyup', function() {
    var val = this.value.toLowerCase();
    var rows = document.querySelectorAll('#tableMonitoring tbody tr');
    
    rows.forEach(function(row) {
        var text = row.textContent.toLowerCase();
        row.style.display = text.indexOf(val) > -1 ? '' : 'none';
    });
});
</script>

<style>
    /* Custom Scrollbar */
    .table-responsive::-webkit-scrollbar { width: 6px; height: 6px; }
    .table-responsive::-webkit-scrollbar-thumb { background: #ccc; border-radius: 4px; }
    .table-responsive::-webkit-scrollbar-thumb:hover { background: #999; }
</style>