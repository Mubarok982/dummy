<?php 
if ($this->session->flashdata('pesan_error')) {
    echo '<div class="alert-info" style="background-color: #f8d7da; color: #721c24;">' . $this->session->flashdata('pesan_error') . '</div>';
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
        <?php if (empty($bimbingan)): ?>
            <tr><td colspan="6" style="text-align: center;">Tidak ada mahasiswa dalam daftar bimbingan Anda.</td></tr>
        <?php else: ?>
            <?php foreach ($bimbingan as $b): ?>
            <tr>
                <td><?php echo $b['npm']; ?></td>
                <td><?php echo $b['nama_mhs']; ?></td>
                <td><?php echo $b['judul'] ?: 'Belum Ada Judul'; ?></td>
                <td><?php echo $b['nama_p1']; ?></td>
                <td><?php echo $b['nama_p2']; ?></td>
                <td>
                    <?php if ($b['judul']): ?>
                        <a href="<?php echo base_url('dosen/progres_detail/' . $b['id_skripsi']); ?>" style="padding: 5px; background-color: #007bff; color: white; text-decoration: none;">Cek Progres</a>
                    <?php else: ?>
                        Menunggu Pengajuan
                    <?php endif; ?>
                </td>
            </tr>
            <?php endforeach; ?>
        <?php endif; ?>
    </tbody>
</table>