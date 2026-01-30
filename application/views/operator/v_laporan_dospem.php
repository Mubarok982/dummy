<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0"><?php echo $title; ?></h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="<?php echo base_url('operator/dashboard'); ?>">Dashboard</a></li>
                        <li class="breadcrumb-item active"><?php echo $title; ?></li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <section class="content">
        <div class="container-fluid">
            <!-- Filter -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Filter Data</h3>
                </div>
                <div class="card-body">
                    <form method="GET" action="<?php echo base_url('operator/laporan_dospem_semester'); ?>">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Semester</label>
                                    <select name="semester" class="form-control">
                                        <option value="">Pilih Semester</option>
                                        <option value="2025/2026 Genap" <?php echo ($this->input->get('semester') == '2025/2026 Genap') ? 'selected' : ''; ?>>2025/2026 Genap</option>
                                        <option value="2025/2026 Ganjil" <?php echo ($this->input->get('semester') == '2025/2026 Ganjil') ? 'selected' : ''; ?>>2025/2026 Ganjil</option>
                                        <option value="2024/2025 Genap" <?php echo ($this->input->get('semester') == '2024/2025 Genap') ? 'selected' : ''; ?>>2024/2025 Genap</option>
                                        <option value="2024/2025 Ganjil" <?php echo ($this->input->get('semester') == '2024/2025 Ganjil') ? 'selected' : ''; ?>>2024/2025 Ganjil</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Program Studi</label>
                                    <select name="prodi" class="form-control">
                                        <option value="">Semua Prodi</option>
                                        <option value="Teknik Informatika S1" <?php echo ($this->input->get('prodi') == 'Teknik Informatika S1') ? 'selected' : ''; ?>>Teknik Informatika S1</option>
                                        <option value="Teknologi Informasi D3" <?php echo ($this->input->get('prodi') == 'Teknologi Informasi D3') ? 'selected' : ''; ?>>Teknologi Informasi D3</option>
                                        <option value="Teknik Industri S1" <?php echo ($this->input->get('prodi') == 'Teknik Industri S1') ? 'selected' : ''; ?>>Teknik Industri S1</option>
                                        <option value="Teknik Mesin S1" <?php echo ($this->input->get('prodi') == 'Teknik Mesin S1') ? 'selected' : ''; ?>>Teknik Mesin S1</option>
                                        <option value="Mesin Otomotif D3" <?php echo ($this->input->get('prodi') == 'Mesin Otomotif D3') ? 'selected' : ''; ?>>Mesin Otomotif D3</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>&nbsp;</label><br>
                                    <button type="submit" class="btn btn-primary"><i class="fas fa-search"></i> Filter</button>
                                    <a href="<?php echo base_url('operator/laporan_dospem_semester'); ?>" class="btn btn-secondary"><i class="fas fa-undo"></i> Reset</a>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Tabel Laporan -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Laporan Kinerja Dosen Pembimbing</h3>
                </div>
                <div class="card-body table-responsive">
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Nama Dosen</th>
                                <th>NIDK</th>
                                <th>Prodi</th>
                                <th>Jumlah Mahasiswa Dibimbing</th>
                                <th>Jumlah Bimbingan</th>
                                <th>Detail Mahasiswa</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($kinerja_dospem)): ?>
                                <?php $no = 1; foreach ($kinerja_dospem as $dosen): ?>
                                    <tr>
                                        <td><?php echo $no++; ?></td>
                                        <td><?php echo $dosen['nama']; ?></td>
                                        <td><?php echo $dosen['nidk']; ?></td>
                                        <td><?php echo $dosen['prodi']; ?></td>
                                        <td><?php echo $dosen['jumlah_mahasiswa']; ?></td>
                                        <td><?php echo $dosen['jumlah_bimbingan']; ?></td>
                                        <td>
                                            <?php if (!empty($dosen['mahasiswa_dibimbing'])): ?>
                                                <ul>
                                                    <?php foreach ($dosen['mahasiswa_dibimbing'] as $mhs): ?>
                                                        <li><?php echo $mhs['nama'] . ' (' . $mhs['npm'] . ') - ' . $mhs['judul']; ?></li>
                                                    <?php endforeach; ?>
                                                </ul>
                                            <?php else: ?>
                                                Tidak ada mahasiswa
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="7" class="text-center">Tidak ada data kinerja dosen ditemukan.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </section>
</div>
