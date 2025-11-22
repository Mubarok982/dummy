<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class M_Data extends CI_Model {

    // --- CRUD Akun Dasar ---

    public function get_all_users_with_details($role = NULL)
    {
        $this->db->select('A.id, A.username, A.nama, A.role, D.nidk, M.npm, M.prodi AS prodi_mhs, D.prodi AS prodi_dsn');
        $this->db->from('mstr_akun A');
        $this->db->join('data_dosen D', 'A.id = D.id', 'left');
        $this->db->join('data_mahasiswa M', 'A.id = M.id', 'left');
        
        if ($role) {
            $this->db->where('A.role', $role);
        }
        
        $this->db->order_by('A.role', 'ASC');
        return $this->db->get()->result_array();
    }
    
    // Fungsi untuk mendapatkan detail akun berdasarkan ID
    public function get_user_by_id($id)
    {
        $this->db->select('A.*, D.*, M.*');
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

    // Baris 61: Urutan parameter diubah.
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
                if ($role == 'dosen') $this->db->insert('data_dosen', $detail_data);
                if ($role == 'mahasiswa') $this->db->insert('data_mahasiswa', $detail_data);
            }
        }
        
        $this->db->trans_complete();
        return $this->db->trans_status();
    }
    
    // Fungsi delete akun (menghapus dari semua tabel terkait)
    public function delete_user($id)
    {
        // Ini adalah delete Cascade. Karena kita menggunakan Foreign Key ON DELETE CASCADE
        // di SQL dump, menghapus dari tabel mstr_akun seharusnya
        // otomatis menghapus dari data_dosen/data_mahasiswa.
        // Kita tetap pastikan hapus di mstr_akun.
        $this->db->where('id', $id);
        return $this->db->delete('mstr_akun');
    }


    // --- Penugasan Pembimbing ---

    public function get_all_mahasiswa_skripsi()
    {
        $this->db->select('A.id AS id_mhs, A.nama, M.npm, M.prodi, S.id AS id_skripsi, S.judul, 
                           P1.nama AS nama_p1, P2.nama AS nama_p2');
        $this->db->from('mstr_akun A');
        $this->db->join('data_mahasiswa M', 'A.id = M.id', 'inner');
        $this->db->join('skripsi S', 'A.id = S.id_mahasiswa', 'left'); // left join karena mungkin belum ada skripsi
        $this->db->join('mstr_akun P1', 'S.pembimbing1 = P1.id', 'left');
        $this->db->join('mstr_akun P2', 'S.pembimbing2 = P2.id', 'left');
        $this->db->where('A.role', 'mahasiswa');
        $this->db->order_by('M.npm', 'ASC');
        return $this->db->get()->result_array();
    }
    
    public function get_dosen_pembimbing_list()
    {
        $this->db->select('A.id, A.nama, D.nidk');
        $this->db->from('mstr_akun A');
        $this->db->join('data_dosen D', 'A.id = D.id');
        $this->db->where('A.role', 'dosen');
        $this->db->order_by('A.nama', 'ASC');
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
        
        // Ambil progres terakhir untuk setiap mahasiswa
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
        // Menghitung mahasiswa yang BAB 3-nya sudah ACC Penuh (progres 100) oleh kedua dosen
        $sql = "SELECT COUNT(DISTINCT ps.npm) AS total
                FROM progres_skripsi ps
                JOIN skripsi s ON ps.npm = (SELECT npm FROM data_mahasiswa WHERE id = s.id_mahasiswa)
                WHERE ps.bab = 3 AND ps.progres_dosen1 = 100 AND ps.progres_dosen2 = 100";
        return $this->db->query($sql)->row()->total;
    }

    public function get_plagiarisme_tasks()
    {
        // Query ini tetap sama untuk mendapatkan daftar file yang perlu diverifikasi
        $this->db->select('HP.*, PS.npm, PS.bab, A.nama, S.judul, PS.file AS progres_file'); // Tambahkan progres_file
        $this->db->from('hasil_plagiarisme HP');
        $this->db->join('progres_skripsi PS', 'HP.id_progres = PS.id');
        $this->db->join('data_mahasiswa DM', 'PS.npm = DM.npm');
        $this->db->join('mstr_akun A', 'DM.id = A.id');
        $this->db->join('skripsi S', 'DM.id = S.id_mahasiswa', 'left'); 
        $this->db->where('HP.status', 'Menunggu'); // Hanya tampilkan yang Menunggu aksi
        $this->db->order_by('HP.tanggal_cek', 'ASC');
        return $this->db->get()->result_array();
    }
    
    public function update_plagiarisme_status($id_plagiat, $status_baru)
    {
        $data = [
            'status' => $status_baru,
            'tanggal_cek' => date('Y-m-d'),
        ];
        
        // Hanya update status, persentase kemiripan diabaikan (diasumsikan sudah dicek Operator secara eksternal)
        $this->db->where('id', $id_plagiat);
        return $this->db->update('hasil_plagiarisme', $data);
    }
}