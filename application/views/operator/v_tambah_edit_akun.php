<?php 
// Cek apakah mode edit atau tambah
$is_edit = isset($user);
$action_url = $is_edit ? base_url('operator/edit_akun/' . $user['id']) : base_url('operator/tambah_akun');

// Tampilkan error validasi
echo validation_errors('<div class="alert-info" style="background-color: #f8d7da; color: #721c24; border-color: #f5c6cb;">', '</div>');
?>

<div style="width: 50%;">
    <?php echo form_open($action_url); ?>
        <h3>Informasi Akun Dasar</h3>
        
        <label for="nama">Nama Lengkap:</label>
        <input type="text" name="nama" id="nama" class="form-control" value="<?php echo set_value('nama', $is_edit ? $user['nama'] : ''); ?>" required>
        <br>
        
        <label for="role">Role:</label>
        <select name="role" id="role" class="form-control" onchange="toggleRoleFields(this.value)" required <?php echo $is_edit ? 'disabled' : ''; ?>>
            <option value="">-- Pilih Role --</option>
            <option value="operator" <?php echo set_select('role', 'operator', $is_edit && $user['role'] == 'operator'); ?>>Operator</option>
            <option value="dosen" <?php echo set_select('role', 'dosen', $is_edit && $user['role'] == 'dosen'); ?>>Dosen</option>
            <option value="mahasiswa" <?php echo set_select('role', 'mahasiswa', $is_edit && $user['role'] == 'mahasiswa'); ?>>Mahasiswa</option>
        </select>
        <?php if ($is_edit): ?>
            <input type="hidden" name="role" value="<?php echo $user['role']; ?>">
        <?php endif; ?>
        <br>

        <?php if (!$is_edit): // Username hanya bisa diubah saat tambah ?>
        <label for="username">Username (Login):</label>
        <input type="text" name="username" id="username" class="form-control" value="<?php echo set_value('username', $is_edit ? $user['username'] : ''); ?>" required>
        <br>
        <?php endif; ?>
        
        <label for="password"><?php echo $is_edit ? 'Password Baru (Kosongkan jika tidak diubah)' : 'Password:'; ?></label>
        <input type="password" name="password" id="password" class="form-control">
        <br>

        <div id="dosen-fields" style="border: 1px dashed #ccc; padding: 10px; margin-bottom: 15px; display: none;">
            <h3>Detail Dosen</h3>
            <label for="nidk">NIDK:</label>
            <input type="text" name="nidk" value="<?php echo set_value('nidk', $is_edit ? $user['nidk'] : ''); ?>">
            <br>
            <label for="prodi_dosen">Prodi:</label>
            <select name="prodi_dosen">
                <option value="Teknik Informatika S1" <?php echo set_select('prodi_dosen', 'Teknik Informatika S1', $is_edit && $user['prodi'] == 'Teknik Informatika S1'); ?>>Teknik Informatika S1</option>
                <option value="Teknologi Informasi D3" <?php echo set_select('prodi_dosen', 'Teknologi Informasi D3', $is_edit && $user['prodi'] == 'Teknologi Informasi D3'); ?>>Teknologi Informasi D3</option>
                </select>
            <br>
            <label>Kaprodi: </label>
            <input type="checkbox" name="is_kaprodi" value="1" <?php echo set_checkbox('is_kaprodi', '1', $is_edit && isset($user['is_kaprodi']) && $user['is_kaprodi'] == 1); ?>> Ya
        </div>

        <div id="mahasiswa-fields" style="border: 1px dashed #ccc; padding: 10px; margin-bottom: 15px; display: none;">
            <h3>Detail Mahasiswa</h3>
            <label for="npm">NPM:</label>
            <input type="text" name="npm" value="<?php echo set_value('npm', $is_edit ? $user['npm'] : ''); ?>">
            <br>
            <label for="prodi_mhs">Prodi:</label>
            <select name="prodi_mhs">
                <option value="Teknik Informatika S1" <?php echo set_select('prodi_mhs', 'Teknik Informatika S1', $is_edit && $user['prodi'] == 'Teknik Informatika S1'); ?>>Teknik Informatika S1</option>
                <option value="Teknologi Informasi D3" <?php echo set_select('prodi_mhs', 'Teknologi Informasi D3', $is_edit && $user['prodi'] == 'Teknologi Informasi D3'); ?>>Teknologi Informasi D3</option>
            </select>
            <br>
            <label for="angkatan">Angkatan (Tahun):</label>
            <input type="number" name="angkatan" min="2000" max="<?php echo date('Y'); ?>" value="<?php echo set_value('angkatan', $is_edit ? $user['angkatan'] : date('Y')); ?>">
        </div>
        
        <button type="submit" class="btn btn-primary" style="margin-top: 15px;">
            <?php echo $is_edit ? 'Simpan Perubahan' : 'Tambah Akun'; ?>
        </button>
        <a href="<?php echo base_url('operator/manajemen_akun'); ?>" style="margin-left: 10px;">Batal</a>

    <?php echo form_close(); ?>
</div>

<script>
    // Fungsi JavaScript untuk menampilkan/menyembunyikan field tambahan
    function toggleRoleFields(role) {
        document.getElementById('dosen-fields').style.display = 'none';
        document.getElementById('mahasiswa-fields').style.display = 'none';
        
        if (role === 'dosen') {
            document.getElementById('dosen-fields').style.display = 'block';
        } else if (role === 'mahasiswa') {
            document.getElementById('mahasiswa-fields').style.display = 'block';
        }
    }
    
    // Panggil fungsi saat halaman dimuat (untuk mode edit)
    document.addEventListener('DOMContentLoaded', function() {
        var initialRole = document.getElementById('role').value;
        toggleRoleFields(initialRole);
    });
</script>