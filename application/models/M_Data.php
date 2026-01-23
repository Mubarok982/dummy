<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class M_Data extends CI_Model
{

   
// --- MODIFIKASI BAGIAN INI DI M_Data.php ---

    // 1. Helper Private untuk Filter (Agar tidak koding ulang)
    private function _filter_users_query($role, $prodi, $keyword)
    {
        $this->db->from('mstr_akun A');
        $this->db->join('data_dosen D', 'A.id = D.id', 'left');
        $this->db->join('data_mahasiswa M', 'A.id = M.id', 'left');

        if ($role && $role != '') {
            $this->db->where('A.role', $role);
        }

        if ($prodi && $prodi != '') {
            $this->db->group_start();
                $this->db->where('D.prodi', $prodi);
                $this->db->or_where('M.prodi', $prodi);
            $this->db->group_end();
        }

        if ($keyword && $keyword != '') {
            $this->db->group_start();
                $this->db->like('A.nama', $keyword);
                $this->db->or_like('A.username', $keyword);
                $this->db->or_like('D.nidk', $keyword);
                $this->db->or_like('M.npm', $keyword);
            $this->db->group_end();
        }
    }

    // 2. Method Hitung Total Data (Untuk Pagination)
    public function count_all_users($role = NULL, $prodi = NULL, $keyword = NULL)
    {
        $this->_filter_users_query($role, $prodi, $keyword);
        return $this->db->count_all_results();
    }

    // 3. Method Ambil Data dengan Limit & Offset
    public function get_all_users_with_details($role = NULL, $prodi = NULL, $keyword = NULL, $limit = NULL, $offset = NULL)
    {
        $this->db->select('A.id, A.username, A.nama, A.role, D.nidk, M.npm, M.prodi AS prodi_mhs, D.prodi AS prodi_dsn');
        
        $this->_filter_users_query($role, $prodi, $keyword);
        
        $this->db->order_by('A.role', 'ASC');
        $this->db->order_by('A.nama', 'ASC');

        // Tambahkan Limit untuk Paginasi
        if ($limit) {
            $this->db->limit($limit, $offset);
        }
        
        return $this->db->get()->result_array();
    }

    public function get_user_by_id($id)
    {
        $this->db->select('A.*, D.*, M.*, D.prodi AS prodi_dsn, M.prodi AS prodi_mhs');
        
        $this->db->from('mstr_akun A');
        $this->db->join('data_dosen D', 'A.id = D.id', 'left');
        $this->db->join('data_mahasiswa M', 'A.id = M.id', 'left');
        $this->db->where('A.id', $id);
        return $this->db->get()->row_array();
    }

    // Fungsi untuk menambah data akun dan detailnya
    public function insert_user($akun_data, $role, $detail_data = NULL)
    {
        $this->db->trans_start();

        // 1. Masukkan ke mstr_akun
        $this->db->insert('mstr_akun', $akun_data);
        $id = $this->db->insert_id();

        // 2. Masukkan ke tabel detail (data_dosen atau data_mahasiswa)
        if ($detail_data && ($role == 'dosen' || $role == 'mahasiswa')) {
            $detail_data['id'] = $id;

            if ($role == 'dosen') {
                $this->db->insert('data_dosen', $detail_data);
            } else if ($role == 'mahasiswa') {
                $this->db->insert('data_mahasiswa', $detail_data);
            }
        }

        $this->db->trans_complete();
        return $this->db->trans_status();
    }

    public function update_user($id, $akun_data, $role, $detail_data = NULL)
    {
        $this->db->trans_start();

        // 1. Update mstr_akun
        $this->db->where('id', $id);
        $this->db->update('mstr_akun', $akun_data);

        // 2. Update atau Insert ke tabel detail
        if ($detail_data && ($role == 'dosen' || $role == 'mahasiswa')) {
            $this->db->where('id', $id);
            // Cek apakah data detail sudah ada, jika belum, lakukan insert.
            if ($role == 'dosen' && $this->db->get('data_dosen')->num_rows() > 0) {
                $this->db->where('id', $id)->update('data_dosen', $detail_data);
            } elseif ($role == 'mahasiswa' && $this->db->get('data_mahasiswa')->num_rows() > 0) {
                $this->db->where('id', $id)->update('data_mahasiswa', $detail_data);
            } else {
                $detail_data['id'] = $id;
                if ($role == 'dosen')
                    $this->db->insert('data_dosen', $detail_data);
                if ($role == 'mahasiswa')
                    $this->db->insert('data_mahasiswa', $detail_data);
            }
        }

        $this->db->trans_complete();
        return $this->db->trans_status();
    }

    // Fungsi delete akun (menghapus dari semua tabel terkait)
    public function delete_user($id)
    {
        $this->db->where('id', $id);
        return $this->db->delete('mstr_akun');
    }


    // --- Penugasan Pembimbing ---

    public function get_all_mahasiswa_skripsi()
    {
        // PERBAIKAN DI SINI: Menambahkan S.pembimbing1 dan S.pembimbing2
        $this->db->select('A.id AS id_mhs, A.nama, M.npm, M.prodi, S.id AS id_skripsi, S.judul, 
                           S.pembimbing1, S.pembimbing2, 
                           P1.nama AS nama_p1, P2.nama AS nama_p2');

        $this->db->from('mstr_akun A');
        $this->db->join('data_mahasiswa M', 'A.id = M.id', 'inner');
        $this->db->join('skripsi S', 'A.id = S.id_mahasiswa', 'left');
        $this->db->join('mstr_akun P1', 'S.pembimbing1 = P1.id', 'left');
        $this->db->join('mstr_akun P2', 'S.pembimbing2 = P2.id', 'left');
        $this->db->where('A.role', 'mahasiswa');
        $this->db->order_by('M.npm', 'ASC');
        return $this->db->get()->result_array();
    }

    // --- UPDATE: Kinerja Dosen dengan Filter & Pagination ---

    // Helper private untuk query dosen
    private function _filter_dosen_query($keyword) {
        $this->db->from('mstr_akun A');
        $this->db->join('data_dosen D', 'A.id = D.id');
        $this->db->where('A.role', 'dosen');

        if ($keyword && $keyword != '') {
            $this->db->group_start();
                $this->db->like('A.nama', $keyword);
                $this->db->or_like('D.nidk', $keyword);
            $this->db->group_end();
        }
    }

    // 1. Hitung Total Dosen (Untuk Pagination)
    public function count_dosen_pembimbing($keyword = NULL)
    {
        $this->_filter_dosen_query($keyword);
        return $this->db->count_all_results();
    }

    // 2. Ambil List Dosen (Support Limit & Offset)
    public function get_dosen_pembimbing_list($keyword = NULL, $limit = NULL, $offset = NULL)
    {
        $this->db->select('A.id, A.nama, D.nidk');
        $this->_filter_dosen_query($keyword);
        
        $this->db->order_by('A.nama', 'ASC');

        if ($limit) {
            $this->db->limit($limit, $offset);
        }
        
        return $this->db->get()->result_array();
    }

    public function assign_pembimbing($id_skripsi, $pembimbing1, $pembimbing2)
    {
        $data = [
            'pembimbing1' => $pembimbing1,
            'pembimbing2' => $pembimbing2
        ];
        $this->db->where('id', $id_skripsi);
        return $this->db->update('skripsi', $data);
    }

    public function get_laporan_progres_semua_mhs()
    {
        $this->db->select('A.nama, M.npm, M.prodi, S.judul, P1.nama AS p1, P2.nama AS p2');
        $this->db->from('mstr_akun A');
        $this->db->join('data_mahasiswa M', 'A.id = M.id', 'inner');
        $this->db->join('skripsi S', 'A.id = S.id_mahasiswa', 'left');
        $this->db->join('mstr_akun P1', 'S.pembimbing1 = P1.id', 'left');
        $this->db->join('mstr_akun P2', 'S.pembimbing2 = P2.id', 'left');
        $this->db->where('A.role', 'mahasiswa');
        $this->db->order_by('M.prodi, A.nama', 'ASC');

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

    public function count_users_by_role($role)
    {
        $this->db->where('role', $role);
        return $this->db->get('mstr_akun')->num_rows();
    }

    public function count_mahasiswa()
    {
        return $this->db->get_where('mstr_akun', ['role' => 'mahasiswa'])->num_rows();
    }

    public function count_dosen()
    {
        return $this->db->get_where('mstr_akun', ['role' => 'dosen'])->num_rows();
    }

    public function count_mahasiswa_with_skripsi()
    {
        $this->db->select('COUNT(DISTINCT id_mahasiswa) as total');
        return $this->db->get('skripsi')->row()->total;
    }

    public function count_mahasiswa_ready_sempro()
    {
        $sql = "SELECT COUNT(DISTINCT ps.npm) AS total
                FROM progres_skripsi ps
                JOIN skripsi s ON ps.npm = (SELECT npm FROM data_mahasiswa WHERE id = s.id_mahasiswa)
                WHERE ps.bab = 3 AND ps.progres_dosen1 = 100 AND ps.progres_dosen2 = 100";
        return $this->db->query($sql)->row()->total;
    }

    public function get_plagiarisme_tasks()
    {
        $this->db->select('HP.*, PS.npm, PS.bab, A.nama, S.judul, PS.file AS progres_file');
        $this->db->from('hasil_plagiarisme HP');
        $this->db->join('progres_skripsi PS', 'HP.id_progres = PS.id');
        $this->db->join('data_mahasiswa DM', 'PS.npm = DM.npm');
        $this->db->join('mstr_akun A', 'DM.id = A.id');
        $this->db->join('skripsi S', 'DM.id = S.id_mahasiswa', 'left');
        $this->db->where('HP.status', 'Menunggu');
        $this->db->order_by('HP.tanggal_cek', 'ASC');
        return $this->db->get()->result_array();
    }

    public function update_plagiarisme_status($id_plagiat, $status_baru)
    {
        $data = [
            'status' => $status_baru,
            'tanggal_cek' => date('Y-m-d'),
        ];

        $this->db->where('id', $id_plagiat);
        return $this->db->update('hasil_plagiarisme', $data);
    }

    // --- FUNGSI BARU: Monitoring Progres dengan Filter & Pagination ---

    // 1. Helper Private untuk Query Filter
    private function _filter_laporan_query($prodi, $keyword)
    {
        $this->db->from('mstr_akun A');
        $this->db->join('data_mahasiswa M', 'A.id = M.id', 'inner');
        $this->db->join('skripsi S', 'A.id = S.id_mahasiswa', 'left');
        $this->db->join('mstr_akun P1', 'S.pembimbing1 = P1.id', 'left');
        $this->db->join('mstr_akun P2', 'S.pembimbing2 = P2.id', 'left');
        $this->db->where('A.role', 'mahasiswa');

        // Filter Prodi
        if ($prodi && $prodi != '') {
            $this->db->where('M.prodi', $prodi);
        }

        // Filter Keyword (Nama, NPM, atau Judul Skripsi)
        if ($keyword && $keyword != '') {
            $this->db->group_start();
                $this->db->like('A.nama', $keyword);
                $this->db->or_like('M.npm', $keyword);
                $this->db->or_like('S.judul', $keyword);
            $this->db->group_end();
        }
    }

    // 2. Hitung Total Data (Untuk Paginasi)
    public function count_laporan_progres($prodi = NULL, $keyword = NULL)
    {
        $this->_filter_laporan_query($prodi, $keyword);
        return $this->db->count_all_results();
    }

    // 3. Ambil Data dengan Limit
    public function get_laporan_progres($prodi = NULL, $keyword = NULL, $limit = NULL, $offset = NULL)
    {
        $this->db->select('A.nama, M.npm, M.prodi, S.judul, P1.nama AS p1, P2.nama AS p2');
        
        $this->_filter_laporan_query($prodi, $keyword);
        
        $this->db->order_by('M.prodi', 'ASC');
        $this->db->order_by('A.nama', 'ASC');

        if ($limit) {
            $this->db->limit($limit, $offset);
        }
        
        $result = $this->db->get()->result_array();
        
        // Logic Bab Terakhir (Looping hanya pada 15 data yang diambil)
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
}