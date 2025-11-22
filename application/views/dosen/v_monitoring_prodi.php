<?php 
$prodi = $this->session->userdata('prodi');
?>

<h3>Monitoring Progres Mahasiswa Prodi: <?php echo $prodi; ?></h3>

<table border="1" style="width: 100%; border-collapse: collapse;">
    <thead>
        <tr style="background-color: #eee;">
            <th>NPM</th>
            <th>Nama</th>
            <th>Angkatan</th>
            <th>Judul Skripsi</th>
            <th>P. 1</th>
            <th>P. 2</th>
            <th>Progres Terakhir</th>
        </tr>
    </thead>
    <tbody>
        <?php if (empty($mahasiswa_prodi)): ?>
            <tr><td colspan="7" style="text-align: center;">Tidak ada data mahasiswa di prodi ini.</td></tr>
        <?php else: ?>
            <?php foreach ($mahasiswa_prodi as $m): ?>
            <tr>
                <td><?php echo $m['npm']; ?></td>
                <td><?php echo $m['nama']; ?></td>
                <td><?php echo $m['angkatan']; ?></td>
                <td><?php echo $m['judul'] ?: 'Belum Ada Judul'; ?></td>
                <td><?php echo $m['p1'] ?: '-'; ?></td>
                <td><?php echo $m['p2'] ?: '-'; ?></td>
                <td>
                    <?php 
                    // Logika sederhana: ambil progres_skripsi terakhir
                    $progres_terakhir = $this->M_Dosen->get_all_progres_skripsi($m['npm']);
                    if ($progres_terakhir) {
                        $last = end($progres_terakhir);
                        echo 'BAB ' . $last['bab'] . ' (' . $last['nilai_dosen1'] . '/' . $last['nilai_dosen2'] . ')';
                    } else {
                        echo 'Belum Ada Progres';
                    }
                    ?>
                </td>
            </tr>
            <?php endforeach; ?>
        <?php endif; ?>
    </tbody>
</table>