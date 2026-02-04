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
        // Query 1: Menghitung Total Mahasiswa Bimbingan Aktif di Periode Tersebut
        $this->db->select('COUNT(DISTINCT s.id_mahasiswa) as total_mhs');
        $this->db->from('skripsi s');
        $this->db->join('data_mahasiswa m', 's.id_mahasiswa = m.id');
        $this->db->where("('$start_date' <= s.tgl_pengajuan_judul)"); // Asumsi aktif jika judul diajukan sebelum akhir semester
        $this->db->group_start();
            $this->db->where('s.pembimbing1', $id_dosen);
            $this->db->or_where('s.pembimbing2', $id_dosen);
        $this->db->group_end();
        
        if (!empty($prodi)) {
            $this->db->where('m.prodi', $prodi);
        }
        $data_mhs = $this->db->get()->row_array();


        // Query 2: Mengambil Riwayat Aktivitas (Revisi/ACC) dari tabel progres_skripsi
        // Kita asumsikan tabel 'progres_skripsi' mencatat kapan revisi terjadi (created_at)
        $this->db->select('
            ps.bab, 
            ps.created_at, 
            ps.status_p1, 
            ps.status_p2, 
            m.nama as nama_mahasiswa,
            m.npm,
            m.prodi
        ');
        $this->db->from('progres_skripsi ps');
        $this->db->join('skripsi s', 'ps.id_skripsi = s.id');
        $this->db->join('data_mahasiswa m', 's.id_mahasiswa = m.id');
        
        // Filter Waktu (Semester)
        $this->db->where('ps.created_at >=', $start_date . ' 00:00:00');
        $this->db->where('ps.created_at <=', $end_date . ' 23:59:59');

        // Filter Dosen (Apakah dia P1 atau P2 di skripsi tersebut)
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
}