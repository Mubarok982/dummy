<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class M_laporan_opt extends CI_Model {

    // Helper Filter Laporan
    private function _filter_laporan($prodi, $keyword) {
        $this->db->from('mstr_akun A');
        $this->db->join('data_mahasiswa M', 'A.id = M.id', 'inner');
        $this->db->join('skripsi S', 'A.id = S.id_mahasiswa', 'left');
        $this->db->join('mstr_akun P1', 'S.pembimbing1 = P1.id', 'left');
        $this->db->join('mstr_akun P2', 'S.pembimbing2 = P2.id', 'left');
        $this->db->where('A.role', 'mahasiswa');
        if ($prodi) $this->db->where('M.prodi', $prodi);
        if ($keyword) {
            $this->db->group_start();
            $this->db->like('A.nama', $keyword);
            $this->db->or_like('M.npm', $keyword);
            $this->db->group_end();
        }
    }

    public function count_laporan_progres($prodi, $keyword) {
        $this->_filter_laporan($prodi, $keyword);
        return $this->db->count_all_results();
    }

    public function get_laporan_progres($prodi, $keyword, $limit, $offset) {
        // Tambahkan field yang diperlukan untuk status_bimbingan di helper
        $this->db->select('
            A.id,
            A.nama, 
            M.npm, 
            M.prodi, 
            M.angkatan,
            S.id as id_skripsi,
            S.judul, 
            S.status_acc_kaprodi,
            S.status_sempro,
            P1.nama AS p1, 
            P2.nama AS p2
        ');
        $this->_filter_laporan($prodi, $keyword);
        $this->db->order_by('M.prodi', 'ASC');
        $this->db->order_by('A.nama', 'ASC');
        if ($limit) $this->db->limit($limit, $offset);
        
        $result = $this->db->get()->result_array();
        
        foreach ($result as $key => $mhs) {
            // Ambil status ujian terakhir
            $ujian = $this->db->select('status')
                             ->from('ujian_skripsi')
                             ->where('id_skripsi', $mhs['id_skripsi'])
                             ->order_by('id', 'DESC')
                             ->limit(1)
                             ->get()
                             ->row_array();
            
            $result[$key]['status_ujian'] = isset($ujian['status']) ? $ujian['status'] : null;
            
            // Ambil progres terakhir
            $progres = $this->db->order_by('bab', 'DESC')
                                ->limit(1)
                                ->get_where('progres_skripsi', ['npm' => $mhs['npm']])
                                ->row_array();
            
            if ($progres) {
                $result[$key]['last_bab'] = intval($progres['bab']);
                
                // Cek ketersediaan kolom (Anti Error)
                $val_p1 = isset($progres['progres_dosen1']) ? intval($progres['progres_dosen1']) : (isset($progres['status_p1']) ? intval($progres['status_p1']) : 0);
                $val_p2 = isset($progres['progres_dosen2']) ? intval($progres['progres_dosen2']) : (isset($progres['status_p2']) ? intval($progres['status_p2']) : 0);
                
                $result[$key]['progres_dosen1'] = $val_p1;
                $result[$key]['progres_dosen2'] = $val_p2;
                $result[$key]['status_p1'] = $val_p1 . '%';
                $result[$key]['status_p2'] = $val_p2 . '%';
            } else {
                $result[$key]['last_bab'] = 0;
                $result[$key]['progres_dosen1'] = 0;
                $result[$key]['progres_dosen2'] = 0;
                $result[$key]['status_p1'] = '-';
                $result[$key]['status_p2'] = '-';
            }
            
            // Tentukan max_bab berdasarkan prodi
            $result[$key]['max_bab'] = 6; // Default S1
            if (stripos($mhs['prodi'], 'D3') !== false || stripos($mhs['prodi'], 'Diploma 3') !== false) {
                $result[$key]['max_bab'] = 5; // D3
            }
        }
        return $result;
    }

    // Helper Filter Dosen
    private function _filter_dosen($keyword, $prodi = null) {
        $this->db->from('mstr_akun A');
        $this->db->join('data_dosen D', 'A.id = D.id');
        $this->db->where('A.role', 'dosen');
        if ($prodi) {
            $this->db->where('D.prodi', $prodi);
        }
        if ($keyword) {
            $this->db->group_start();
            $this->db->like('A.nama', $keyword);
            $this->db->or_like('D.nidk', $keyword);
            $this->db->group_end();
        }
    }

    public function count_dosen_pembimbing($keyword, $prodi = null) {
        $this->_filter_dosen($keyword, $prodi);
        return $this->db->count_all_results();
    }

    public function get_dosen_pembimbing_list($keyword, $limit, $offset, $prodi = null) {
        $this->db->select('A.id, A.nama, D.nidk');
        $this->_filter_dosen($keyword, $prodi);
        $this->db->order_by('A.nama', 'ASC');
        if ($limit) $this->db->limit($limit, $offset);
        return $this->db->get()->result_array();
    }

  public function get_detail_kinerja($id_dosen, $start_date, $end_date, $prodi = null)
    {
        // 1. Hitung Total Mahasiswa (Tetap)
        $this->db->select('COUNT(DISTINCT s.id_mahasiswa) as total_mhs');
        $this->db->from('skripsi s');
        $this->db->join('data_mahasiswa m', 's.id_mahasiswa = m.id');
        $this->db->where("s.tgl_pengajuan_judul IS NOT NULL");
        $this->db->group_start();
            $this->db->where('s.pembimbing1', $id_dosen);
            $this->db->or_where('s.pembimbing2', $id_dosen);
        $this->db->group_end();
        
        if (!empty($prodi)) {
            $this->db->where('m.prodi', $prodi);
        }
        $data_mhs = $this->db->get()->row_array();

        // 2. Ambil Riwayat (PERBAIKAN: Join ke mstr_akun untuk ambil Nama)
        $this->db->select('
            ps.*, 
            ma.nama as nama_mahasiswa,  -- Ambil nama dari tabel akun (ma)
            m.npm,
            m.prodi,
            s.pembimbing1,
            s.pembimbing2
        ');
        $this->db->from('progres_skripsi ps');
        
        // --- JOIN LOGIC ---
        $this->db->join('data_mahasiswa m', 'ps.npm = m.npm');       // Hubungkan Progres ke Mhs via NPM
        $this->db->join('mstr_akun ma', 'm.id = ma.id');             // TAMBAHAN: Hubungkan Mhs ke Akun via ID (untuk dapat Nama)
        $this->db->join('skripsi s', 'm.id = s.id_mahasiswa');       // Hubungkan Mhs ke Skripsi
        
        // Filter Waktu
        $this->db->where('ps.created_at >=', $start_date . ' 00:00:00');
        $this->db->where('ps.created_at <=', $end_date . ' 23:59:59');

        // Filter Dosen
        $this->db->group_start();
            $this->db->where('s.pembimbing1', $id_dosen);
            $this->db->or_where('s.pembimbing2', $id_dosen);
        $this->db->group_end();

        // Filter Prodi
        if (!empty($prodi)) {
            $this->db->where('m.prodi', $prodi);
        }

        $this->db->order_by('ps.created_at', 'DESC');
        $riwayat = $this->db->get()->result_array();

        return [
            'total_mhs_bimbingan' => $data_mhs['total_mhs'],
            'riwayat_aktivitas' => $riwayat
        ];
    }
    
    public function get_all_prodi()
    {
        $this->db->distinct();
        $this->db->select('prodi');
        $this->db->from('data_mahasiswa');
        $this->db->where("prodi != ''"); 
        $this->db->order_by('prodi', 'ASC');
        return $this->db->get()->result_array();
    }

    public function get_all_semesters()
    {
        $this->db->select('tgl_pengajuan_judul');
        $this->db->from('skripsi');
        $this->db->where('tgl_pengajuan_judul !=', '0000-00-00');
        $this->db->where('tgl_pengajuan_judul IS NOT NULL');
        $this->db->order_by('tgl_pengajuan_judul', 'DESC');
        $query = $this->db->get();
        
        $semesters = [];
        
        if ($query->num_rows() > 0) {
            foreach ($query->result_array() as $row) {
                $date = $row['tgl_pengajuan_judul'];
                $timestamp = strtotime($date);
                $month = date('n', $timestamp);
                $year = date('Y', $timestamp);
                
                if ($month >= 9) { 
                    $sem_label = 'Gasal ' . $year . '-' . ($year + 1);
                } elseif ($month <= 2) { 
                    $sem_label = 'Gasal ' . ($year - 1) . '-' . $year;
                } else { 
                    $sem_label = 'Genap ' . ($year - 1) . '-' . $year;
                }
                
                if (!in_array($sem_label, $semesters)) {
                    $semesters[] = $sem_label;
                }
            }
        }
        
        if (empty($semesters)) {
            $semesters[] = 'Gasal ' . date('Y') . '-' . (date('Y')+1);
        }

        return $semesters;
    }
}