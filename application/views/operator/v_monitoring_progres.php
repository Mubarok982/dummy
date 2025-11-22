<h3>Laporan Progres Bimbingan Seluruh Mahasiswa</h3>

<table border="1" style="width: 100%; border-collapse: collapse;">
    <thead>
        <tr style="background-color: #eee;">
            <th>NPM</th>
            <th>Nama Mahasiswa</th>
            <th>Prodi</th>
            <th>Judul Skripsi</th>
            <th>P. 1</th>
            <th>P. 2</th>
            <th>Progres Terakhir</th>
            <th>Status P1</th>
            <th>Status P2</th>
        </tr>
    </thead>
    <tbody>
        <?php if (empty($laporan)): ?>
            <tr><td colspan="9" style="text-align: center;">Tidak ada data mahasiswa.</td></tr>
        <?php else: ?>
            <?php foreach ($laporan as $mhs): ?>
            <tr>
                <td><?php echo $mhs['npm']; ?></td>
                <td><?php echo $mhs['nama']; ?></td>
                <td><?php echo $mhs['prodi']; ?></td>
                <td><?php echo $mhs['judul'] ?: 'Belum Ada'; ?></td>
                <td><?php echo $mhs['p1'] ?: '-'; ?></td>
                <td><?php echo $mhs['p2'] ?: '-'; ?></td>
                <td><?php echo $mhs['last_bab']; ?></td>
                <td><?php echo $mhs['status_p1']; ?></td>
                <td><?php echo $mhs['status_p2']; ?></td>
            </tr>
            <?php endforeach; ?>
        <?php endif; ?>
    </tbody>
</table>