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
        $this->db->select('A.nama, M.npm, M.prodi, S.judul, P1.nama AS p1, P2.nama AS p2');
        $this->_filter_laporan($prodi, $keyword);
        $this->db->order_by('M.prodi', 'ASC');
        $this->db->order_by('A.nama', 'ASC');
        if ($limit) $this->db->limit($limit, $offset);
        
        $result = $this->db->get()->result_array();
        
        foreach ($result as $key => $mhs) {
            $progres = $this->db->order_by('bab', 'DESC')->limit(1)->get_where('progres_skripsi', ['npm' => $mhs['npm']])->row_array();
            if ($progres) {
                $result[$key]['last_bab'] = 'BAB ' . $progres['bab'];
                $result[$key]['status_p1'] = $progres['nilai_dosen1'];
                $result[$key]['status_p2'] = $progres['nilai_dosen2'];
            } else {
                $result[$key]['last_bab'] = 'Belum Mulai';
                $result[$key]['status_p1'] = '-';
                $result[$key]['status_p2'] = '-';
            }
        }
        return $result;
    }

    // Helper Filter Dosen
    private function _filter_dosen($keyword) {
        $this->db->from('mstr_akun A');
        $this->db->join('data_dosen D', 'A.id = D.id');
        $this->db->where('A.role', 'dosen');
        if ($keyword) {
            $this->db->group_start();
            $this->db->like('A.nama', $keyword);
            $this->db->or_like('D.nidk', $keyword);
            $this->db->group_end();
        }
    }

    public function count_dosen_pembimbing($keyword) {
        $this->_filter_dosen($keyword);
        return $this->db->count_all_results();
    }

    public function get_dosen_pembimbing_list($keyword, $limit, $offset) {
        $this->db->select('A.id, A.nama, D.nidk');
        $this->_filter_dosen($keyword);
        $this->db->order_by('A.nama', 'ASC');
        if ($limit) $this->db->limit($limit, $offset);
        return $this->db->get()->result_array();
    }

// Ambil detail kinerja dosen berdasarkan filter
    public function get_detail_kinerja($id_dosen, $start_date, $end_date, $prodi = null)
    {
        // 1. Query Total Mahasiswa (Logic Tetap)
        $this->db->select('COUNT(DISTINCT s.id_mahasiswa) as total_mhs');
        $this->db->from('skripsi s');
        $this->db->join('data_mahasiswa m', 's.id_mahasiswa = m.id');
        $this->db->where("('$start_date' <= s.tgl_pengajuan_judul)");
        $this->db->group_start();
            $this->db->where('s.pembimbing1', $id_dosen);
            $this->db->or_where('s.pembimbing2', $id_dosen);
        $this->db->group_end();
        
        if (!empty($prodi)) {
            $this->db->where('m.prodi', $prodi);
        }
        $data_mhs = $this->db->get()->row_array();

        // 2. Query Riwayat Aktivitas
        $this->db->select('
            ps.bab, 
            ps.created_at, 
            ps.progres_dosen1 AS status_p1,  
            ps.progres_dosen2 AS status_p2, 
            m.nama_ortu_dengan_gelar as nama_mahasiswa, 
            ma.nama as nama_asli_mahasiswa, 
            m.npm,
            m.prodi
        ');
        $this->db->from('progres_skripsi ps');
        
        // PERBAIKAN ALUR JOIN:
        // progres_skripsi -> data_mahasiswa (via NPM)
        $this->db->join('data_mahasiswa m', 'ps.npm = m.npm');
        // data_mahasiswa -> mstr_akun (via ID, untuk ambil Nama)
        $this->db->join('mstr_akun ma', 'm.id = ma.id'); 
        // data_mahasiswa -> skripsi (via ID Mahasiswa, untuk cek pembimbing)
        $this->db->join('skripsi s', 'm.id = s.id_mahasiswa');
        
        // Filter Waktu
        $this->db->where('ps.created_at >=', $start_date . ' 00:00:00');
        $this->db->where('ps.created_at <=', $end_date . ' 23:59:59');

        // Filter Dosen (Cek apakah dosen ini pembimbing skripsi mahasiswa tsb)
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

        // Mapping nama mahasiswa agar konsisten dengan view
        foreach ($riwayat as $key => $val) {
            // Gunakan nama asli dari akun jika ada, jika tidak gunakan dari data mahasiswa
            $riwayat[$key]['nama_mahasiswa'] = !empty($val['nama_asli_mahasiswa']) ? $val['nama_asli_mahasiswa'] : $val['nama_mahasiswa'];
        }

        return [
            'total_mhs_bimbingan' => $data_mhs['total_mhs'],
            'riwayat_aktivitas' => $riwayat
        ];
    }
    // --- TAMBAHAN BARU: Ambil List Semua Prodi ---
    public function get_all_prodi()
    {
        $this->db->distinct();
        $this->db->select('prodi');
        $this->db->from('data_mahasiswa');
        $this->db->where("prodi != ''"); // Pastikan tidak mengambil data kosong
        $this->db->order_by('prodi', 'ASC');
        return $this->db->get()->result_array();
    }

    // --- TAMBAHAN BARU: Ambil Daftar Semester Dinamis dari Database ---
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
                $month = date('n', $timestamp); // 1-12
                $year = date('Y', $timestamp);
                
                // LOGIKA PEMBAGIAN SEMESTER (Sesuai Controller)
                // Ganjil: September (9) s.d Februari (2)
                // Genap: Maret (3) s.d Agustus (8)
                
                if ($month >= 9) { // Sept - Des
                    $start_year = $year;
                    $end_year = $year + 1;
                    $type = 'Ganjil';
                } elseif ($month <= 2) { // Jan - Feb
                    $start_year = $year - 1;
                    $end_year = $year;
                    $type = 'Ganjil';
                } else { // Maret - Agustus
                    $start_year = $year - 1;
                    $end_year = $year;
                    $type = 'Genap';
                }
                
                $sem_label = $start_year . '/' . $end_year . ' ' . $type;
                
                // Masukkan ke array jika belum ada (agar unik)
                if (!in_array($sem_label, $semesters)) {
                    $semesters[] = $sem_label;
                }
            }
        }
        
        // Jika data kosong, berikan default
        if (empty($semesters)) {
            $semesters[] = date('Y').'/'.(date('Y')+1).' Ganjil';
        }

        return $semesters;
    }
}