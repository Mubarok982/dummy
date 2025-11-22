<?php 
$is_exist = $skripsi != NULL;

if ($this->session->flashdata('pesan_sukses')) {
    echo '<div class="alert-info" style="background-color: #d4edda; color: #155724;">' . $this->session->flashdata('pesan_sukses') . '</div>';
}
if ($this->session->flashdata('pesan_error')) {
    echo '<div class="alert-info" style="background-color: #f8d7da; color: #721c24;">' . $this->session->flashdata('pesan_error') . '</div>';
}

echo validation_errors('<div class="alert-info" style="background-color: #f8d7da; color: #721c24;">', '</div>');
?>

<h3><?php echo $is_exist ? 'Judul Anda Saat Ini' : 'Form Pengajuan/Revisi Judul'; ?></h3>

<div style="border: 1px solid #ccc; padding: 20px; border-radius: 8px;">
    <?php echo form_open('mahasiswa/submit_judul'); ?>
        
        <label>Judul Skripsi:</label>
        <textarea name="judul" style="width: 90%; height: 80px;" required><?php echo set_value('judul', $is_exist ? $skripsi['judul'] : ''); ?></textarea>
        <br><br>
        
        <label>Tema Penelitian:</label>
        <select name="tema" required>
            <option value="Software Engineering" <?php echo set_select('tema', 'Software Engineering', $is_exist && $skripsi['tema'] == 'Software Engineering'); ?>>Software Engineering</option>
            <option value="Networking" <?php echo set_select('tema', 'Networking', $is_exist && $skripsi['tema'] == 'Networking'); ?>>Networking</option>
            <option value="Artificial Intelligence" <?php echo set_select('tema', 'Artificial Intelligence', $is_exist && $skripsi['tema'] == 'Artificial Intelligence'); ?>>Artificial Intelligence</option>
        </select>
        <br><br>

        <h4>Persetujuan Pembimbing (Hasil Bimbingan Offline)</h4>
        
        <label>Pembimbing 1:</label>
        <select name="pembimbing1" required>
            <option value="">-- Pilih Pembimbing 1 --</option>
            <?php foreach ($dosen_list as $dsn): ?>
                <option value="<?php echo $dsn['id']; ?>" <?php echo set_select('pembimbing1', $dsn['id'], $is_exist && $skripsi['pembimbing1'] == $dsn['id']); ?>>
                    <?php echo $dsn['nama']; ?>
                </option>
            <?php endforeach; ?>
        </select>
        <br><br>
        
        <label>Pembimbing 2:</label>
        <select name="pembimbing2" required>
            <option value="">-- Pilih Pembimbing 2 --</option>
            <?php foreach ($dosen_list as $dsn): ?>
                <option value="<?php echo $dsn['id']; ?>" <?php echo set_select('pembimbing2', $dsn['id'], $is_exist && $skripsi['pembimbing2'] == $dsn['id']); ?>>
                    <?php echo $dsn['nama']; ?>
                </option>
            <?php endforeach; ?>
        </select>
        <br><br>

        <button type="submit" class="btn btn-primary" style="margin-top: 20px;">
            <?php echo $is_exist ? 'Update Judul Skripsi' : 'Ajukan Judul Skripsi'; ?>
        </button>
        
    <?php echo form_close(); ?>
</div>