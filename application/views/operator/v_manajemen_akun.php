<a href="<?php echo base_url('operator/tambah_akun'); ?>" class="btn btn-success" style="margin-bottom: 20px;">+ Tambah Akun Baru</a><br><br>

<?php 
if ($this->session->flashdata('pesan_sukses')) {
    echo '<div class="alert-info" style="background-color: #d4edda; color: #155724; border-color: #c3e6cb;">' . $this->session->flashdata('pesan_sukses') . '</div>';
}
?>

<table border="1" style="width: 100%; border-collapse: collapse;">
    <thead>
        <tr style="background-color: #eee;">
            <th>ID</th>
            <th>Nama</th>
            <th>Username</th>
            <th>Role</th>
            <th>NIDN/NPM</th>
            <th>Prodi</th>
            <th>Aksi</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($users as $user): ?>
        <tr>
            <td><?php echo $user['id']; ?></td>
            <td><?php echo $user['nama']; ?></td>
            <td><?php echo $user['username']; ?></td>
            <td><?php echo ucfirst($user['role']); ?></td>
            <td>
                <?php 
                // Tampilkan NIDK untuk Dosen atau NPM untuk Mahasiswa
                if ($user['role'] == 'dosen') echo $user['nidk'];
                else if ($user['role'] == 'mahasiswa') echo $user['npm'];
                else echo '-';
                ?>
            </td>
            <td>
                <?php 
                // Tampilkan Prodi Dosen atau Mahasiswa
                if ($user['role'] == 'dosen') echo $user['prodi_dsn'];
                else if ($user['role'] == 'mahasiswa') echo $user['prodi_mhs'];
                else echo '-';
                ?>
            </td>
            <td>
                <a href="<?php echo base_url('operator/edit_akun/' . $user['id']); ?>">Edit</a> | 
                <a href="<?php echo base_url('operator/delete_akun/' . $user['id']); ?>" onclick="return confirm('Yakin ingin menghapus akun ini?');">Hapus</a>
            </td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<?php // Catatan: Anda perlu membuat view untuk Tambah Akun (v_tambah_akun.php) dan Controller-nya (tambah_akun, edit_akun, delete_akun) nanti. ?>