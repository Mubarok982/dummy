<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Helper untuk Status Bimbingan - Consistent across all roles
 * 
 * Mengintegrasikan perhitungan status bimbingan di semua role:
 * - Mahasiswa: v_bimbingan.php
 * - Dosen: v_progres_detail.php
 * - Operator: v_monitoring_progres.php
 */

if ( ! function_exists('get_status_bimbingan_label'))
{
    /**
     * Menghitung label status bimbingan berdasarkan data skripsi
     * 
     * @param array $data Array yang berisi:
     *   - status_ujian (optional)
     *   - status_acc_kaprodi (optional)
     *   - status_sempro (optional)
     *   - last_progres (optional) - array dengan keys: bab, progres_dosen1, progres_dosen2
     *   - progres_dosen1 (optional) - direct value
     *   - progres_dosen2 (optional) - direct value
     *   - last_bab (optional) - direct value
     *   - max_bab (optional) - default 6
     * @return string Status bimbingan label (MENGULANG, MENUNGGU CEK PLAGIARISME, SIAP PENDADARAN, SIAP SEMPRO, BIMBINGAN)
     */
    function get_status_bimbingan_label($data)
    {
        // Set default values
        $data = is_array($data) ? $data : [];
        
        $status_ujian = isset($data['status_ujian']) ? trim($data['status_ujian']) : '';
        $status_acc = isset($data['status_acc_kaprodi']) ? strtolower(trim($data['status_acc_kaprodi'])) : '';
        $status_sempro = isset($data['status_sempro']) ? trim($data['status_sempro']) : '';
        $max_bab = isset($data['max_bab']) ? intval($data['max_bab']) : 6;
        
        // Get progress values - could come from last_progres array or direct properties
        $p1 = null;
        $p2 = null;
        $bab_terakhir = 0;
        
        if (isset($data['last_progres']) && is_array($data['last_progres'])) {
            $lp = $data['last_progres'];
            $bab_terakhir = isset($lp['bab']) ? intval($lp['bab']) : 0;
            $p1 = isset($lp['progres_dosen1']) ? intval($lp['progres_dosen1']) : null;
            $p2 = isset($lp['progres_dosen2']) ? intval($lp['progres_dosen2']) : null;
        } else {
            // Get direct values
            $bab_terakhir = isset($data['last_bab']) ? intval($data['last_bab']) : 0;
            $p1 = isset($data['progres_dosen1']) ? intval($data['progres_dosen1']) : null;
            $p2 = isset($data['progres_dosen2']) ? intval($data['progres_dosen2']) : null;
        }
        
        $is_last_bab_acc = ($p1 === 100 && $p2 === 100);
        
        // ===============================================================
        // LOGIKA UTAMA (Robust Detection dengan Priority)
        // ===============================================================
        
        // Priority 1: Jika dinyatakan Mengulang atau judul ditolak -> MENGULANG
        if (strtolower($status_ujian) == 'mengulang' || $status_acc == 'ditolak') {
            return "MENGULANG";
        }
        
        // Priority 2: Menunggu Cek Plagiarisme
        if ($status_sempro == 'Menunggu Plagiarisme') {
            return "MENUNGGU CEK PLAGIARISME";
        }
        
        // Priority 3: Based on progress stages (most accurate)
        if ($bab_terakhir > 0) {
            // Tahapan 4: SIAP PENDADARAN - Jika bab terakhir sudah ACC dan >= max_bab
            if ($is_last_bab_acc && $bab_terakhir >= $max_bab) {
                return "SIAP PENDADARAN";
            }
            
            // Tahapan 3: SIAP SEMPRO - Jika bab 3 ACC dan belum bab 4
            if ($is_last_bab_acc && $bab_terakhir == 3) {
                return "SIAP SEMPRO";
            }
            
            // Tahapan 2: BIMBINGAN - Jika sudah bab 4 atau lebih (kembali dari siap sempro)
            if ($bab_terakhir >= 4) {
                return "BIMBINGAN";
            }
        }
        
        // Priority 4: Fallback ke status_sempro database value
        if ($status_sempro == 'Siap Pendadaran') {
            return "SIAP PENDADARAN";
        }
        if ($status_sempro == 'Siap Sempro') {
            return "SIAP SEMPRO";
        }
        
        // Default: BIMBINGAN (sedang proses revisi atau menunggu approval)
        return "BIMBINGAN";
    }
}

if ( ! function_exists('get_status_bimbingan_badge'))
{
    /**
     * Menghitung label dan CSS class untuk status bimbingan badge
     * 
     * @param array $data Array dengan struktur sama seperti get_status_bimbingan_label()
     * @return array Array dengan keys: 'label' dan 'class'
     */
    function get_status_bimbingan_badge($data)
    {
        $label = get_status_bimbingan_label($data);
        
        // Tentukan CSS class berdasarkan label
        $class_map = [
            'MENGULANG' => 'badge-danger',
            'MENUNGGU CEK PLAGIARISME' => 'badge-secondary',
            'SIAP PENDADARAN' => 'badge-success',
            'SIAP SEMPRO' => 'badge-info',
            'BIMBINGAN' => 'badge-primary',
        ];
        
        $class = isset($class_map[$label]) ? $class_map[$label] : 'badge-primary';
        
        return [
            'label' => $label,
            'class' => $class
        ];
    }
}

if ( ! function_exists('get_status_badge'))
{
    /**
     * Helper untuk menampilkan badge status progres (ACC/Revisi/Proses)
     * 
     * @param string|int $status Status value
     * @return string HTML badge
     */
    function get_status_badge($status)
    {
        $status = strtolower($status);
        if ($status == 'acc' || $status == '100') {
            return '<span class="badge badge-success px-2"><i class="fas fa-check"></i> ACC</span>';
        }
        if ($status == 'revisi' || $status == '0') {
            return '<span class="badge badge-danger px-2"><i class="fas fa-times"></i> Revisi</span>';
        }
        if ($status == 'menunggu' || $status == '50') {
            return '<span class="badge badge-warning text-white px-2"><i class="fas fa-clock"></i> Proses</span>';
        }
        return '<span class="badge badge-secondary px-2">-</span>';
    }
}

if ( ! function_exists('get_next_bab'))
{
    /**
     * Menentukan bab target berikutnya berdasarkan progress terakhir
     * 
     * @param array $last_progres Array progres terakhir dengan keys: bab, progres_dosen1, progres_dosen2
     * @param int $max_bab Maximum bab (default 6)
     * @return array Array dengan keys: 'target_bab', 'is_revisi'
     */
    function get_next_bab($last_progres = null, $max_bab = 6)
    {
        $target_bab = 1;
        $is_revisi = false;
        
        if (isset($last_progres) && is_array($last_progres) && !empty($last_progres)) {
            $lp = $last_progres;
            $p1 = isset($lp['progres_dosen1']) ? intval($lp['progres_dosen1']) : 0;
            $p2 = isset($lp['progres_dosen2']) ? intval($lp['progres_dosen2']) : 0;
            $bab_sekarang = isset($lp['bab']) ? intval($lp['bab']) : 1;
            
            if ($p1 == 100 && $p2 == 100) {
                $target_bab = min($bab_sekarang + 1, $max_bab);
            } else {
                $target_bab = $bab_sekarang;
                $is_revisi = true;
            }
        }
        
        return [
            'target_bab' => $target_bab,
            'is_revisi' => $is_revisi
        ];
    }
}

?>
