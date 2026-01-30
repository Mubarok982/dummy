<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0 text-dark"><?php echo isset($user) ? 'Edit Akun' : 'Tambah Akun'; ?> Pengguna</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="<?php echo base_url('dashboard'); ?>">Home</a></li>
                        <li class="breadcrumb-item"><a href="<?php echo base_url('dosen/manajemen_akun'); ?>">Manajemen Akun</a></li>
                        <li class="breadcrumb-item active"><?php echo isset($user) ? 'Edit' : 'Tambah'; ?> Akun</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-8">
                    <div class="card card-primary shadow-sm">
                        <div class="card-header">
                            <h3 class="card-title">Form <?php echo isset($user) ? 'Edit' : 'Tambah'; ?> Akun</h3>
                        </div>

                        <form action="<?php echo isset($user) ? base_url('dosen/edit_akun/' . $user['id']) : base_url('dosen/tambah_akun'); ?>" method="POST">
                            <div class="card-body">

                                <!-- Role Selection -->
                                <div class="form-group">
                                    <label for="role">Role <span class="text-danger">*</span></label>
                                    <select name="role" id="role" class="form-control" required onchange="toggleFields()">
                                        <option value="">-- Pilih Role --</option>
                                        <option value="dosen" <?php echo (isset($user) && $user['role'] == 'dosen') ? 'selected' : ''; ?>>Dosen</option>
                                        <option value="mahasiswa" <?php echo (isset($user) && $user['role'] == 'mahasiswa') ? 'selected' : ''; ?>>Mahasiswa</option>
                                        <option value="operator" <?php echo (isset($user) && $user['role'] == 'operator') ? 'selected' : ''; ?>>Operator</option>
                                    </select>
                                </div>

                                <!-- Username -->
                                <div class="form-group">
                                    <label for="username">Username <span class="text-danger">*</span></label>
                                    <input type="text" name="username" id="username" class="form-control" required
                                           value="<?php echo isset($user) ? $user['username'] : ''; ?>"
                                           placeholder="Masukkan username">
                                </div>

                                <!-- Password -->
                                <div class="form-group">
                                    <label for="password">Password <?php echo isset($user) ? '(Kosongkan jika tidak diubah)' : '<span class="text-danger">*</span>'; ?></label>
                                    <input type="password" name="password" id="password" class="form-control"
                                           <?php echo !isset($user) ? 'required' : ''; ?>
                                           placeholder="Masukkan password">
                                </div>

                                <!-- Nama -->
                                <div class="form-group">
                                    <label for="nama">Nama Lengkap <span class="text-danger">*</span></label>
                                    <input type="text" name="nama" id="nama" class="form-control" required
                                           value="<?php echo isset($user) ? $user['nama'] : ''; ?>"
                                           placeholder="Masukkan nama lengkap">
                                </div>

                                <!-- Fields for Dosen -->
                                <div id="dosen-fields" style="display: none;">
                                    <div class="form-group">
                                        <label for="nidk">NIDK <span class="text-danger">*</span></label>
                                        <input type="text" name="nidk" id="nidk" class="form-control"
                                               value="<?php echo isset($user) && $user['role'] == 'dosen' ? $user['nidk'] : ''; ?>"
                                               placeholder="Masukkan NIDK">
                                    </div>
                                    <div class="form-group">
                                        <label for="prodi_dosen">Program Studi <span class="text-danger">*</span></label>
                                        <select name="prodi_dosen" id="prodi_dosen" class="form-control">
                                            <option value="">-- Pilih Program Studi --</option>
                                            <option value="Teknik Informatika S1" <?php echo (isset($user) && $user['role'] == 'dosen' && $user['prodi_dsn'] == 'Teknik Informatika S1') ? 'selected' : ''; ?>>Teknik Informatika S1</option>
                                            <option value="Teknologi Informasi D3" <?php echo (isset($user) && $user['role'] == 'dosen' && $user['prodi_dsn'] == 'Teknologi Informasi D3') ? 'selected' : ''; ?>>Teknologi Informasi D3</option>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <div class="custom-control custom-checkbox">
                                            <input type="checkbox" class="custom-control-input" id="is_kaprodi" name="is_kaprodi" value="1"
                                                   <?php echo (isset($user) && $user['role'] == 'dosen' && isset($user['is_kaprodi']) && $user['is_kaprodi'] == 1) ? 'checked' : ''; ?>>
                                            <label class="custom-control-label" for="is_kaprodi">Jadikan sebagai Kaprodi</label>
                                        </div>
                                    </div>
                                </div>

                                <!-- Fields for Mahasiswa -->
                                <div id="mahasiswa-fields" style="display: none;">
                                    <div class="form-group">
                                        <label for="npm">NPM <span class="text-danger">*</span></label>
                                        <input type="text" name="npm" id="npm" class="form-control"
                                               value="<?php echo isset($user) && $user['role'] == 'mahasiswa' ? $user['npm'] : ''; ?>"
                                               placeholder="Masukkan NPM">
                                    </div>
                                    <div class="form-group">
                                        <label for="prodi_mhs">Program Studi <span class="text-danger">*</span></label>
                                        <select name="prodi_mhs" id="prodi_mhs" class="form-control">
                                            <option value="">-- Pilih Program Studi --</option>
                                            <option value="Teknik Informatika S1" <?php echo (isset($user) && $user['role'] == 'mahasiswa' && $user['prodi_mhs'] == 'Teknik Informatika S1') ? 'selected' : ''; ?>>Teknik Informatika S1</option>
                                            <option value="Teknologi Informasi D3" <?php echo (isset($user) && $user['role'] == 'mahasiswa' && $user['prodi_mhs'] == 'Teknologi Informasi D3') ? 'selected' : ''; ?>>Teknologi Informasi D3</option>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label for="angkatan">Angkatan <span class="text-danger">*</span></label>
                                        <input type="number" name="angkatan" id="angkatan" class="form-control" min="2000" max="<?php echo date('Y') + 1; ?>"
                                               value="<?php echo isset($user) && $user['role'] == 'mahasiswa' ? $user['angkatan'] : ''; ?>"
                                               placeholder="Masukkan tahun angkatan">
                                    </div>
                                </div>

                            </div>

                            <div class="card-footer">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save mr-1"></i> Simpan
                                </button>
                                <a href="<?php echo base_url('dosen/manajemen_akun'); ?>" class="btn btn-secondary">
                                    <i class="fas fa-arrow-left mr-1"></i> Kembali
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<script>
function toggleFields() {
    var role = document.getElementById('role').value;
    var dosenFields = document.getElementById('dosen-fields');
    var mahasiswaFields = document.getElementById('mahasiswa-fields');

    // Hide all fields first
    dosenFields.style.display = 'none';
    mahasiswaFields.style.display = 'none';

    // Show relevant fields based on role
    if (role === 'dosen') {
        dosenFields.style.display = 'block';
        // Make required
        document.getElementById('nidk').required = true;
        document.getElementById('prodi_dosen').required = true;
        // Remove required from mahasiswa fields
        document.getElementById('npm').required = false;
        document.getElementById('prodi_mhs').required = false;
        document.getElementById('angkatan').required = false;
    } else if (role === 'mahasiswa') {
        mahasiswaFields.style.display = 'block';
        // Make required
        document.getElementById('npm').required = true;
        document.getElementById('prodi_mhs').required = true;
        document.getElementById('angkatan').required = true;
        // Remove required from dosen fields
        document.getElementById('nidk').required = false;
        document.getElementById('prodi_dosen').required = false;
    } else {
        // Remove required from all fields
        document.getElementById('nidk').required = false;
        document.getElementById('prodi_dosen').required = false;
        document.getElementById('npm').required = false;
        document.getElementById('prodi_mhs').required = false;
        document.getElementById('angkatan').required = false;
    }
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    toggleFields();
});
</script>
