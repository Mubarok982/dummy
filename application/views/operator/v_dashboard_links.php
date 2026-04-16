<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0 text-dark">Plotting Jadwal Sempro</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="<?php echo base_url('dashboard'); ?>">Home</a></li>
                        <li class="breadcrumb-item active">Plotting Jadwal</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <section class="content">
        <div class="container-fluid">
            <?php if ($this->session->flashdata('pesan_sukses')): ?>
                <div class="alert alert-success alert-dismissible fade show shadow-sm">
                    <button type="button" class="close" data-dismiss="alert">&times;</button>
                    <?php echo $this->session->flashdata('pesan_sukses'); ?>
                </div>
            <?php endif; ?>

            <div class="card card-outline card-primary shadow-sm">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-link mr-2"></i> Pengaturan Plotting Jadwal Sempro</h3>
                </div>
                <div class="card-body">
                    <form method="POST" action="<?php echo base_url('operator/update_dashboard_links'); ?>">
                        <div class="form-group">
                            <label>Link Google Form Jadwal Sempro</label>
                            <input type="url" name="google_form_sempro" class="form-control" placeholder="https://forms.gle/..." value="<?php echo htmlspecialchars($google_form_sempro); ?>">
                            <small class="form-text text-muted">Link ini akan ditampilkan di dashboard mahasiswa untuk pendaftaran jadwal seminar proposal.</small>
                        </div>
                        <div class="form-group">
                            <label>Link Google Drive Jadwal Sempro</label>
                            <input type="url" name="google_drive_dosen" class="form-control" placeholder="https://drive.google.com/..." value="<?php echo htmlspecialchars($google_drive_dosen); ?>">
                            <small class="form-text text-muted">Link ini akan ditampilkan di dashboard dosen dan mahasiswa untuk akses plotting jadwal.</small>
                        </div>
                        <button type="submit" class="btn btn-primary"><i class="fas fa-save mr-1"></i> Simpan Pengaturan</button>
                    </form>
                </div>
            </div>
        </div>
    </section>
</div>
