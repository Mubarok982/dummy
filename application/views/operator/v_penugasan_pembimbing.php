<?php 
if ($this->session->flashdata('pesan_sukses')) {
    echo '<div class="alert-info" style="background-color: #d4edda; color: #155724; border-color: #c3e6cb;">' . $this->session->flashdata('pesan_sukses') . '</div>';
}
if ($this->session->flashdata('pesan_error')) {
    echo '<div class="alert-info" style="background-color: #f8d7da; color: #721c24; border-color: #f5c6cb;">' . $this->session->flashdata('pesan_error') . '</div>';
}
?>

<table border="1" style="width: 100%; border-collapse: collapse;">
    <thead>
        <tr style="background-color: #eee;">
            <th>NPM</th>
            <th>Nama Mahasiswa</th>
            <th>Judul Skripsi</th>
            <th>Pembimbing 1</th>
            <th>Pembimbing 2</th>
            <th>Aksi</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($mahasiswa as $mhs): ?>
        <tr>
            <td><?php echo $mhs['npm']; ?></td>
            <td><?php echo $mhs['nama']; ?></td>
            <td><?php echo $mhs['judul'] ?: 'Belum Mengajukan Judul'; ?></td>
            <td><?php echo $mhs['nama_p1'] ?: 'Belum Ditugaskan'; ?></td>
            <td><?php echo $mhs['nama_p2'] ?: 'Belum Ditugaskan'; ?></td>
            <td>
                <?php if ($mhs['id_skripsi']): ?>
                    <button onclick="document.getElementById('form_<?php echo $mhs['id_skripsi']; ?>').style.display='block'" class="btn btn-primary">Atur/Ubah</button>
                    
                    <div id="form_<?php echo $mhs['id_skripsi']; ?>" style="display:none; border: 1px solid #ccc; padding: 10px; margin-top: 5px; background-color: #f9f9f9; border-radius: 8px;">
                        <h4>Atur Pembimbing: <?php echo $mhs['nama']; ?></h4>
                        <?php echo form_open('operator/assign_pembimbing_aksi'); ?>
                            <input type="hidden" name="id_skripsi" value="<?php echo $mhs['id_skripsi']; ?>">
                            
                            <label>Pembimbing 1:</label>
                            <select name="pembimbing1" required>
                                <option value="">-- Pilih Dosen --</option>
                                <?php foreach ($dosen_list as $dsn): ?>
                                    <option value="<?php echo $dsn['id']; ?>" <?php echo ($mhs['pembimbing1'] == $dsn['id']) ? 'selected' : ''; ?>>
                                        <?php echo $dsn['nama'] . ' (' . $dsn['nidk'] . ')'; ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            
                            <label>Pembimbing 2:</label>
                            <select name="pembimbing2" required>
                                <option value="">-- Pilih Dosen --</option>
                                <?php foreach ($dosen_list as $dsn): ?>
                                    <option value="<?php echo $dsn['id']; ?>" <?php echo ($mhs['pembimbing2'] == $dsn['id']) ? 'selected' : ''; ?>>
                                        <?php echo $dsn['nama'] . ' (' . $dsn['nidk'] . ')'; ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            
                            <button type="submit" style="background-color: #007bff; color: white;">Simpan</button>
                        <?php echo form_close(); ?>
                    </div>
                <?php else: ?>
                    <span style="color: gray;">Menunggu Pengajuan</span>
                <?php endif; ?>
            </td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>