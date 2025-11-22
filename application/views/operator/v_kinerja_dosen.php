<h3>Laporan Kinerja Koreksi Dosen</h3>
<p>Laporan ini menampilkan ringkasan aktivitas koreksi (ACC, Revisi) yang dilakukan dosen per hari.</p>

<div style="display: flex; flex-wrap: wrap; gap: 20px; margin-top: 20px;">
    <?php foreach ($dosen_list as $dosen): ?>
    
    <div style="border: 1px solid #ddd; padding: 20px; border-radius: 8px; width: 45%; min-width: 300px; background-color: #ffffff;">
        <h4><i class="fas fa-user-check"></i> <?php echo $dosen['nama']; ?> (<?php echo $dosen['nidk']; ?>)</h4>
        
        <?php if (empty($dosen['aktivitas'])): ?>
            <p style="color: #6c757d; margin-top: 10px;">Dosen ini belum mencatat aktivitas koreksi bimbingan.</p>
        <?php else: ?>
            <table style="width: 100%; font-size: 0.9em; margin-top: 15px;">
                <thead>
                    <tr style="background-color: #f0f0f0;">
                        <th style="padding: 8px;">Tanggal</th>
                        <th style="padding: 8px;">Total Aksi Koreksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $total_aksi_semua = 0;
                    foreach ($dosen['aktivitas'] as $aktivitas): 
                        $total_aksi_semua += $aktivitas['total_aksi'];
                    ?>
                    <tr>
                        <td style="padding: 8px;"><?php echo date('d M Y', strtotime($aktivitas['tanggal'])); ?></td>
                        <td style="padding: 8px;"><?php echo $aktivitas['total_aksi']; ?> kali</td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
                <tfoot>
                    <tr style="font-weight: bold; background-color: #e9ecef;">
                        <td style="padding: 8px;">TOTAL</td>
                        <td style="padding: 8px;"><?php echo $total_aksi_semua; ?> kali</td>
                    </tr>
                </tfoot>
            </table>
        <?php endif; ?>
    </div>
    
    <?php endforeach; ?>
</div>