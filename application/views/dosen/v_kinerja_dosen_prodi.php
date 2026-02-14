<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0 text-dark">Kinerja Dosen (Kaprodi)</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="<?php echo base_url('dosen/dashboard'); ?>">Home</a></li>
                        <li class="breadcrumb-item active">Kinerja Dosen</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">

            <div class="card shadow-sm mb-4">
                <div class="card-body p-2">
                    <form action="<?php echo base_url('dosen/kinerja_dosen_kaprodi'); ?>" method="GET">
                        <div class="d-flex justify-content-between align-items-center flex-wrap">
                            <div class="d-flex align-items-center m-1">
                                <span class="text-muted font-weight-bold mr-2 ml-2"><i class="fas fa-filter"></i> Cari Dosen:</span>
                                <div class="input-group input-group-sm" style="width: 250px;">
                                    <input type="text" name="keyword" class="form-control" placeholder="Nama / NIDK..." value="<?php echo $this->input->get('keyword'); ?>">
                                    <div class="input-group-append">
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fas fa-search"></i>
                                        </button>
                                    </div>
                                </div>
                                <?php if($this->input->get('keyword')): ?>
                                    <a href="<?php echo base_url('dosen/kinerja_dosen_kaprodi'); ?>" class="btn btn-outline-danger btn-sm ml-2" title="Reset">
                                        <i class="fas fa-times"></i>
                                    </a>
                                <?php endif; ?>
                            </div>

                            <div class="d-flex align-items-center m-1">
                                <div class="text-muted small">
                                    Total Data: <b><?php echo $total_rows; ?></b> Dosen
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <div class="row">
                <div class="col-12">
                    <div class="card card-outline card-primary shadow-sm">
                        <div class="card-header border-0">
                            <h3 class="card-title font-weight-bold text-primary">
                                <i class="fas fa-chalkboard-teacher mr-1"></i> Rekapitulasi Kinerja Prodi <?php echo $this->session->userdata('prodi'); ?>
                            </h3>
                        </div>

                        <div class="card-body table-responsive p-0">
                            <table class="table table-hover table-striped text-nowrap align-middle">
                                <thead class="bg-light">
                                    <tr class="text-center">
                                        <th style="width: 5%;">No</th>
                                        <th style="width: 35%;" class="text-left">Nama Dosen</th>
                                        <th style="width: 20%;">NIDK</th>
                                        <th style="width: 20%;">Total Aktivitas</th>
                                        <th style="width: 20%;">Detail</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (empty($dosen_list)): ?>
                                        <tr>
                                            <td colspan="5" class="text-center py-5 text-muted">
                                                <i class="fas fa-user-slash fa-3x mb-3 opacity-50"></i><br>
                                                Data dosen tidak ditemukan.
                                            </td>
                                        </tr>
                                    <?php else: ?>
                                        <?php
                                        $no = $start_index + 1;
                                        foreach ($dosen_list as $dosen):
                                        ?>
                                        <tr>
                                            <td class="text-center align-middle"><?php echo $no++; ?></td>

                                            <td class="align-middle">
                                                <div class="d-flex align-items-center">
                                                    <img src="https://ui-avatars.com/api/?name=<?php echo urlencode($dosen['nama']); ?>&background=random&size=35" class="img-circle mr-2" alt="Avatar">
                                                    <span class="font-weight-bold text-dark"><?php echo $dosen['nama']; ?></span>
                                                </div>
                                            </td>

                                            <td class="text-center align-middle">
                                                <span class="badge badge-secondary font-weight-normal px-2 py-1">
                                                    <?php echo $dosen['nidk']; ?>
                                                </span>
                                            </td>

                                            <td class="text-center align-middle">
                                                <?php if($dosen['total_aksi'] > 0): ?>
                                                    <span class="badge badge-success px-3 py-2" style="font-size: 0.9rem;">
                                                        <?php echo $dosen['total_aksi']; ?> Kali
                                                    </span>
                                                <?php else: ?>
                                                    <span class="badge badge-light text-muted px-3 py-2 border">
                                                        0 Kali
                                                    </span>
                                                <?php endif; ?>
                                            </td>

                                            <td class="text-center align-middle">
                                                <button type="button" class="btn btn-info btn-sm shadow-sm" data-toggle="modal" data-target="#modal-detail-<?php echo $dosen['id']; ?>">
                                                    <i class="fas fa-eye mr-1"></i> Lihat Laporan
                                                </button>

                                                <div class="modal fade" id="modal-detail-<?php echo $dosen['id']; ?>" tabindex="-1" aria-hidden="true">
                                                    <div class="modal-dialog modal-lg">
                                                        <div class="modal-content">
                                                            <div class="modal-header bg-info">
                                                                <h5 class="modal-title text-white">
                                                                    <i class="fas fa-chart-line mr-1"></i> Laporan Kinerja: <?php echo substr($dosen['nama'], 0, 30); ?>...
                                                                </h5>
                                                                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                                                                    <span aria-hidden="true">&times;</span>
                                                                </button>
                                                            </div>
                                                            <div class="modal-body text-left">
                                                                
                                                                <form method="GET" action="#" class="mb-3" id="filter-form-<?php echo $dosen['id']; ?>">
                                                                    <div class="row">
                                                                        <div class="col-md-9">
                                                                            <div class="form-group">
                                                                                <label class="font-weight-bold">Semester</label>
                                                                                <select name="semester" class="form-control" onchange="loadSemesterReport(<?php echo $dosen['id']; ?>)">
                                                                                    <?php 
                                                                                    // Looping Data Semester dari Controller
                                                                                    if(!empty($list_semester)): 
                                                                                        foreach($list_semester as $sem): 
                                                                                    ?>
                                                                                        <option value="<?php echo $sem; ?>"><?php echo $sem; ?></option>
                                                                                    <?php 
                                                                                        endforeach; 
                                                                                    else:
                                                                                    ?>
                                                                                        <option value="">Data Semester Kosong</option>
                                                                                    <?php endif; ?>
                                                                                </select>
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-md-3">
                                                                            <div class="form-group">
                                                                                <label>&nbsp;</label> <button type="button" class="btn btn-primary btn-block" onclick="loadSemesterReport(<?php echo $dosen['id']; ?>)">
                                                                                    <i class="fas fa-filter mr-1"></i> Terapkan
                                                                                </button>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </form>
                                                                
                                                                <hr>

                                                                <div id="report-content-<?php echo $dosen['id']; ?>" class="px-2">
                                                                    <div class="text-center py-5">
                                                                        <i class="fas fa-mouse-pointer fa-3x text-muted mb-3 opacity-50"></i>
                                                                        <p class="text-muted">Pilih semester lalu klik <b>Terapkan</b>.</p>
                                                                    </div>
                                                                </div>

                                                            </div>
                                                            <div class="modal-footer bg-light py-2">
                                                                <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Tutup</button>
                                                            </div>
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

                        <div class="card-footer py-2">
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="text-muted small">
                                    Halaman <b><?php echo $this->input->get('page') ? ($this->input->get('page') / $per_page) + 1 : 1; ?></b>
                                </div>
                                <div>
                                    <?php echo $pagination; ?>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>

        </div>
    </section>
</div>

<script>
// Event Listener: Load otomatis saat modal dibuka
document.addEventListener("DOMContentLoaded", function(){
    // Menggunakan jQuery untuk event bootstrap modal
    $('[id^="modal-detail-"]').on('shown.bs.modal', function (e) {
        var modalId = $(this).attr('id');
        var dosenId = modalId.split('-')[2];
        var contentDiv = $('#report-content-' + dosenId);
        
        // Hanya load otomatis jika konten masih kosong/default
        // Agar tidak me-reload berulang kali jika user hanya menutup dan membuka lagi
        if (contentDiv.text().trim().includes('Pilih semester')) {
             loadSemesterReport(dosenId);
        }
    });
});

function loadSemesterReport(dosenId) {
    const form = document.getElementById('filter-form-' + dosenId);
    const formData = new FormData(form);
    const params = new URLSearchParams(formData);

    const reportContent = document.getElementById('report-content-' + dosenId);
    
    // Tampilkan Loading
    reportContent.innerHTML = `
        <div class="text-center py-4">
            <i class="fas fa-spinner fa-spin fa-2x text-info"></i>
            <p class="mt-2">Memuat data kinerja...</p>
        </div>
    `;

    // AJAX Request
    fetch('<?php echo base_url("dosen/get_semester_report/"); ?>' + dosenId + '?' + params.toString())
        .then(response => response.text())
        .then(data => {
            reportContent.innerHTML = data;
        })
        .catch(error => {
            console.error('Error:', error);
            reportContent.innerHTML = `
                <div class="text-center py-4 text-danger">
                    <i class="fas fa-exclamation-triangle fa-2x"></i>
                    <p class="mt-2">Gagal memuat data. Periksa koneksi atau coba lagi.</p>
                </div>
            `;
        });
}
</script>