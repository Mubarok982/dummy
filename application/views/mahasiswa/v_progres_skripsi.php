<?php 
if (!$skripsi) {
    echo '<div class="alert-info" style="background-color: #f8d7da; color: #721c24;">Anda harus mengajukan judul skripsi terlebih dahulu di halaman Pengajuan Judul.</div>';
    return;
}

if ($this->session->flashdata('pesan_sukses')) {
    echo '<div class="alert-info" style="background-color: #d4edda; color: #155724;">' . $this->session->flashdata('pesan_sukses') . '</div>';
}
if ($this->session->flashdata('pesan_error')) {
    echo '<div class="alert-info" style="background-color: #f8d7da; color: #721c24;">' . $this->session->flashdata('pesan_error') . '</div>';
}
?>

<h3>Detail Skripsi</h3>
<div style="border: 1px solid #007bff; padding: 10px; margin-bottom: 20px;">
    <strong>Judul:</strong> <?php echo $skripsi['judul']; ?><br>
    <strong>Pembimbing 1:</strong> <?php echo $skripsi['nama_p1']; ?><br>
    <strong>Pembimbing 2:</strong> <?php echo $skripsi['nama_p2']; ?><br>
</div>

<h3>Riwayat Progres Bimbingan</h3>
<table border="1" style="width: 100%; border-collapse: collapse;">
    <thead>
        <tr style="background-color: #eee;">
            <th>Bab</th>
            <th>File Progres</th>
            <th>Tgl Upload</th>
            <th>P1 (<?php echo $skripsi['nama_p1']; ?>)</th>
            <th>P2 (<?php echo $skripsi['nama_p2']; ?>)</th>
            <th>Komentar P1</th>
            <th>Komentar P2</th>
        </tr>
    </thead>
    <tbody>
        <?php if (empty($progres)): ?>
        <tr><td colspan="7" style="text-align: center;">Belum ada progres bimbingan.</td></tr>
        <?php else: ?>
            <?php foreach ($progres as $p): ?>
            <tr>
                <td>BAB <?php echo $p['bab']; ?></td>
                <td><a href="<?php echo base_url('uploads/progres/' . $p['file']); ?>" target="_blank">Download</a></td>
                <td><?php echo date('d M Y', strtotime($p['created_at'])); ?></td>
                <td style="color: <?php echo ($p['progres_dosen1'] == 100) ? 'green' : (($p['progres_dosen1'] == 50) ? 'orange' : 'red'); ?>;">
                    <?php echo $p['nilai_dosen1'] . ' (' . $p['progres_dosen1'] . '%)'; ?>
                </td>
                <td style="color: <?php echo ($p['progres_dosen2'] == 100) ? 'green' : (($p['progres_dosen2'] == 50) ? 'orange' : 'red'); ?>;">
                    <?php echo $p['nilai_dosen2'] . ' (' . $p['progres_dosen2'] . '%)'; ?>
                </td>
                <td><?php echo $p['komentar_dosen1'] ?: '-'; ?></td>
                <td><?php echo $p['komentar_dosen2'] ?: '-'; ?></td>
            </tr>
            <?php endforeach; ?>
        <?php endif; ?>
    </tbody>
</table>

<h3 style="margin-top: 30px;">Upload Progres Selanjutnya (BAB <?php echo $next_bab; ?>)</h3>

<?php 
// Logika Kriteria Upload BAB Selanjutnya
$is_upload_allowed = TRUE;
if ($next_bab > 1) {
    // Cek apakah BAB sebelumnya (last_progres) sudah ACC Penuh (100) dari kedua dosen
    if ($last_progres['progres_dosen1'] != 100 || $last_progres['progres_dosen2'] != 100) {
        $is_upload_allowed = FALSE;
        echo '<div class="alert-info" style="background-color: #f8d7da; color: #721c24;">Anda harus mendapatkan ACC Penuh (100%) dari kedua Pembimbing pada BAB ' . ($next_bab - 1) . ' sebelum melanjutkan.</div>';
    }
}
?>

<?php if ($is_upload_allowed): ?>
    <div style="border: 1px dashed #ccc; padding: 20px; margin-top: 15px; border-radius: 8px;">
        <?php echo form_open_multipart('mahasiswa/upload_progres_bab'); ?>
            <input type="hidden" name="bab" value="<?php echo $next_bab; ?>">
            
            <label for="file_progres">Upload File BAB <?php echo $next_bab; ?> (PDF Maks 5MB):</label>
            <input type="file" name="file_progres" required style="margin-top: 10px;">
            <br>
            
            <button type="submit" class="btn btn-success" style="margin-top: 15px;">
                Upload Progres BAB <?php echo $next_bab; ?>
            </button>
        <?php echo form_close(); ?>
    </div>
<?php endif; ?>