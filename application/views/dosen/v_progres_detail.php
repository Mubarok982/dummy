<?php 
// Pastikan variabel $progres sudah berisi data dan $skripsi berisi detail skripsi
if ($this->session->flashdata('pesan_sukses')) {
    echo '<div class="alert-info" style="background-color: #d4edda; color: #155724;">' . $this->session->flashdata('pesan_sukses') . '</div>';
}
if ($this->session->flashdata('pesan_error')) {
    echo '<div class="alert-info" style="background-color: #f8d7da; color: #721c24;">' . $this->session->flashdata('pesan_error') . '</div>';
}
if ($this->session->flashdata('pesan_info')) {
    echo '<div class="alert-info" style="background-color: #ffc107; color: #343a40;">' . $this->session->flashdata('pesan_info') . '</div>';
}

$dosen_pembimbing = $is_p1 ? $skripsi['pembimbing1'] : $skripsi['pembimbing2'];
?>

<h3>Progres Bimbingan: <?php echo $skripsi['judul']; ?></h3>
<p>Mahasiswa: **<?php echo $skripsi['npm']; ?>**</p>
<p>Posisi Anda: **Pembimbing <?php echo $is_p1 ? '1' : '2'; ?>**</p>

<table style="width: 100%;">
    <thead>
        <tr>
            <th>Bab</th>
            <th>Tgl Upload</th>
            <th>Cek Plagiat</th> <th>Status P1</th>
            <th>Status P2</th>
            <th>File Progres</th>
            <th>Koreksi & Status</th>
        </tr>
    </thead>
    <tbody>
        <?php if (empty($progres)): ?>
        <tr><td colspan="7" style="text-align: center;">Mahasiswa belum mengunggah progres.</td></tr>
        <?php else: ?>
            <?php foreach ($progres as $p): 
                // --- Logic Plagiarisme ---
                $plagiat = $this->M_Dosen->get_plagiarisme_result($p['id']);
                $plagiat_status = $plagiat ? $plagiat['status'] : 'Menunggu';
                $plagiat_percent = $plagiat ? $plagiat['persentase_kemiripan'] . '%' : '-';
                $plagiat_color = $plagiat_status == 'Lulus' ? 'var(--color-success)' : ($plagiat_status == 'Tolak' ? 'var(--color-danger)' : 'orange');

                // --- Logic Koreksi ---
                $komentar_field = $is_p1 ? 'komentar_dosen1' : 'komentar_dosen2';
                $progres_field = $is_p1 ? 'progres_dosen1' : 'progres_dosen2';
            ?>
            <tr>
                <td>BAB <?php echo $p['bab']; ?></td>
                <td><?php echo date('d M Y', strtotime($p['created_at'])); ?></td>
                
                <td style="color: <?php echo $plagiat_color; ?>; font-weight: bold;">
                    <?php echo $plagiat_status; ?> (<?php echo $plagiat_percent; ?>)
                    <?php if ($plagiat && $plagiat_status != 'Menunggu'): ?>
                        <br><a href="<?php echo base_url('uploads/laporan_plagiarisme/' . $plagiat['dokumen_laporan']); ?>" target="_blank" style="font-size: 0.8em; font-weight: normal;"><i class="fas fa-file-pdf"></i> Laporan</a>
                    <?php endif; ?>
                </td>

                <td style="color: <?php echo ($p['progres_dosen1'] == 100) ? 'var(--color-success)' : (($p['progres_dosen1'] == 50) ? 'orange' : 'var(--color-danger)'); ?>;"><?php echo $p['nilai_dosen1']; ?> (<?php echo $p['progres_dosen1']; ?>%)</td>
                <td style="color: <?php echo ($p['progres_dosen2'] == 100) ? 'var(--color-success)' : (($p['progres_dosen2'] == 50) ? 'orange' : 'var(--color-danger)'); ?>;"><?php echo $p['nilai_dosen2']; ?> (<?php echo $p['progres_dosen2']; ?>%)</td>

                <td><a href="<?php echo base_url('uploads/progres/' . $p['file']); ?>" target="_blank"><i class="fas fa-download"></i> File</a></td>
                
                <td>
                    <?php if ($plagiat_status == 'Lulus' || $plagiat_status == 'Tolak'): ?>
                        <button onclick="document.getElementById('form_<?php echo $p['id']; ?>').style.display='block'" class="btn btn-secondary" style="padding: 8px 15px;">Beri Koreksi</button>
                    <?php else: ?>
                        <span style="color: orange;">Menunggu Cek Plagiat</span>
                    <?php endif; ?>

                    <div id="form_<?php echo $p['id']; ?>" style="display:none; border: 1px solid #ccc; padding: 15px; margin-top: 10px; background-color: #f9f9f9; border-radius: 8px;">
                        <?php echo form_open('dosen/submit_koreksi'); ?>
                            <input type="hidden" name="id_progres" value="<?php echo $p['id']; ?>">
                            <input type="hidden" name="id_skripsi" value="<?php echo $skripsi['id']; ?>">
                            <input type="hidden" name="is_p1" value="<?php echo $is_p1 ? 1 : 0; ?>">
                            
                            <?php
                            $saran_komentar = [
                                "Revisi Bab Pendahuluan: Fokus pada gap penelitian.",
                                "Tinjauan Pustaka: Tambahkan 5 referensi terbaru (5 tahun terakhir).",
                                "Metode Penelitian: Jelaskan langkah pengujian/validasi data lebih rinci.",
                                "Hasil dan Pembahasan: Perlu interpretasi hasil yang lebih mendalam.",
                                "Bab ini sudah baik, segera lanjutkan ke Bab berikutnya (ACC).",
                                "Perbaiki tata bahasa dan format penulisan (Typo)."
                            ];
                            ?>

                            <label>Saran Cepat:</label>
                            <select onchange="document.getElementById('komentar_<?php echo $p['id']; ?>').value += this.value + '\n'" style="width: 100%; padding: 8px; margin-bottom: 10px;">
                                <option value="">-- Pilih Saran Cepat --</option>
                                <?php foreach ($saran_komentar as $saran): ?>
                                    <option value="<?php echo $saran; ?>"><?php echo $saran; ?></option>
                                <?php endforeach; ?>
                            </select>
                            
                            <label>Komentar/Revisi:</label>
                            <textarea name="komentar" id="komentar_<?php echo $p['id']; ?>" style="width: 100%; height: 100px; margin-bottom: 10px;"><?php echo $p[$komentar_field]; ?></textarea>
                            
                            <label>Status Progres:</label>
                            <select name="status_progres" required style="width: 100%; padding: 8px; margin-bottom: 15px;">
                                <option value="0" <?php echo set_select('status_progres', '0', $p[$progres_field] == 0); ?>>0% (Revisi)</option>
                                <option value="50" <?php echo set_select('status_progres', '50', $p[$progres_field] == 50); ?>>50% (ACC Sebagian)</option>
                                <option value="100" <?php echo set_select('status_progres', '100', $p[$progres_field] == 100); ?>>100% (ACC Penuh)</option>
                            </select>
                            
                            <button type="submit" class="btn btn-success" style="width: 100%;">Simpan Koreksi</button>
                        <?php echo form_close(); ?>
                    </div>
                </td>
            </tr>
            <?php endforeach; ?>
        <?php endif; ?>
    </tbody>
</table>

<?php
// Tampilkan tombol ACC Sempro
$is_ready_sempro = FALSE;
if (!empty($progres)) {
    $last_bab = end($progres);
    if ($last_bab['bab'] == 3 && $last_bab['progres_dosen1'] == 100 && $last_bab['progres_dosen2'] == 100) {
        $is_ready_sempro = TRUE;
    }
}
?>

<?php if ($is_ready_sempro): ?>
<div style="margin-top: 25px; padding: 20px; border: 2px solid var(--color-success); background-color: #e9f7ef; border-radius: 12px;">
    <h4>Aksi Lanjut: Mahasiswa Siap Sempro</h4>
    <p>Mahasiswa telah menyelesaikan bimbingan Bab 1-3 dengan status ACC Penuh dari kedua pembimbing.</p>
    
    <a href="https://sita.contoh.ac.id/pendaftaran" target="_blank" class="btn btn-success" style="margin-top: 10px;">
        ✅ Instruksikan Mahasiswa Daftar Sempro (ke SITA)
    </a>
</div>
<?php endif; ?>