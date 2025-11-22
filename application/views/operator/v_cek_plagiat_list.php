<h3><i class="fas fa-search"></i> Input Hasil Cek Plagiarisme Manual</h3>
<p>Daftar progres bimbingan yang menunggu Operator untuk menginput persentase kemiripan dan status akhir dari sistem cek plagiat eksternal.</p>

<?php 
if ($this->session->flashdata('pesan_sukses')) {
    echo '<div class="alert-info" style="background-color: #d4edda; color: #155724;">' . $this->session->flashdata('pesan_sukses') . '</div>';
}
if ($this->session->flashdata('pesan_error')) {
    echo '<div class="alert-info" style="background-color: #f8d7da; color: #721c24;">' . $this->session->flashdata('pesan_error') . '</div>';
}
?>

<table style="width: 100%;">
    <thead>
        <tr>
            <th>No.</th>
            <th>Mahasiswa (NPM)</th>
            <th>Judul Skripsi</th>
            <th>Bab</th>
            <th>Tgl Upload</th>
            <th>File Progres</th> <th>Status</th>
            <th>Aksi</th>
        </tr>
    </thead>
    <tbody>
        <?php if (empty($plagiat_list)): ?>
            <tr><td colspan="8" style="text-align: center;">Tidak ada tugas Cek Plagiarisme yang menunggu verifikasi.</td></tr>
        <?php else: ?>
            <?php foreach ($plagiat_list as $key => $plagiat): ?>
            <tr>
                <td><?php echo $key + 1; ?></td>
                <td><?php echo $plagiat['nama']; ?> (<?php echo $plagiat['npm']; ?>)</td>
                <td><?php echo $plagiat['judul'] ?: '-'; ?></td>
                <td>BAB <?php echo $plagiat['bab']; ?></td>
                <td><?php echo date('d M Y', strtotime($plagiat['tanggal_cek'])); ?></td>
                <td><a href="<?php echo base_url('uploads/progres/' . $plagiat['progres_file']); ?>" target="_blank"><i class="fas fa-download"></i> Unduh</a></td>
                <td style="color: orange; font-weight: bold;">Menunggu</td>
                
                <td>
                    <a href="<?php echo base_url('operator/verifikasi_plagiarisme/' . $plagiat['id'] . '/acc'); ?>" class="btn btn-success" style="padding: 8px 15px;" onclick="return confirm('Verifikasi LULUS (ACC) Plagiat?');">ACC</a>
                    <a href="<?php echo base_url('operator/verifikasi_plagiarisme/' . $plagiat['id'] . '/tolak'); ?>" class="btn btn-danger" style="padding: 8px 15px;" onclick="return confirm('Verifikasi TOLAK (REVISI) Plagiat?');">TOLAK</a>
                </td>
            </tr>
            <?php endforeach; ?>
        <?php endif; ?>
    </tbody>
</table>