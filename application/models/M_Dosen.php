<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class M_Dosen extends CI_Model {

    // --- Daftar Bimbingan ---

    public function get_mahasiswa_bimbingan($id_dosen)
    {
        $this->db->select('S.id AS id_skripsi, S.judul, M.npm, A.nama AS nama_mhs, A.id AS id_mhs, 
                           P1.nama AS nama_p1, P2.nama AS nama_p2');
        $this->db->from('skripsi S');
        $this->db->join('data_mahasiswa M', 'S.id_mahasiswa = M.id', 'inner');
        $this->db->join('mstr_akun A', 'M.id = A.id', 'inner');
        $this->db->join('mstr_akun P1', 'S.pembimbing1 = P1.id', 'left');
        $this->db->join('mstr_akun P2', 'S.pembimbing2 = P2.id', 'left');
        
        // Filter di mana dosen ini adalah Pembimbing 1 atau Pembimbing 2
        $this->db->where("S.pembimbing1 = $id_dosen OR S.pembimbing2 = $id_dosen");
        $this->db->order_by('A.nama', 'ASC');
        return $this->db->get()->result_array();
    }

    // --- Detail Skripsi dan Progres ---

    public function get_skripsi_details($id_skripsi)
    {
        $this->db->select('S.*, M.npm');
        $this->db->from('skripsi S');
        $this->db->join('data_mahasiswa M', 'S.id_mahasiswa = M.id', 'inner');
        $this->db->where('S.id', $id_skripsi);
        return $this->db->get()->row_array();
    }

    public function get_all_progres_skripsi($npm)
    {
        $this->db->order_by('bab', 'ASC');
        return $this->db->get_where('progres_skripsi', ['npm' => $npm])->result_array();
    }
    
    public function get_progres_by_id($id_progres)
    {
        return $this->db->get_where('progres_skripsi', ['id' => $id_progres])->row_array();
    }
    
    public function update_progres($id_progres, $data)
    {
        $this->db->where('id', $id_progres);
        return $this->db->update('progres_skripsi', $data);
    }
    
    // --- Kaprodi Monitoring ---
    
    public function get_all_mahasiswa_prodi($prodi)
    {
        $this->db->select('A.nama, M.npm, M.angkatan, S.judul, P1.nama AS p1, P2.nama AS p2');
        $this->db->from('data_mahasiswa M');
        $this->db->join('mstr_akun A', 'M.id = A.id', 'inner');
        $this->db->join('skripsi S', 'M.id = S.id_mahasiswa', 'left');
        $this->db->join('mstr_akun P1', 'S.pembimbing1 = P1.id', 'left');
        $this->db->join('mstr_akun P2', 'S.pembimbing2 = P2.id', 'left');
        $this->db->where('M.prodi', $prodi);
        $this->db->where('A.role', 'mahasiswa');
        $this->db->order_by('M.angkatan', 'ASC');
        return $this->db->get()->result_array();
    }

    public function count_total_bimbingan($id_dosen)
    {
        // Menghitung total mahasiswa yang dosen ini menjadi P1 atau P2
        $this->db->where("pembimbing1 = $id_dosen OR pembimbing2 = $id_dosen");
        return $this->db->get('skripsi')->num_rows();
    }

    public function get_plagiarisme_result($id_progres)
    {
        return $this->db->get_where('hasil_plagiarisme', ['id_progres' => $id_progres])->row_array();
    }
    
    // public function insert_plagiarisme_mockup($id_progres)
    // {
    //     // === MOCKUP LOGIC ===
    //     // Ambang batas kelulusan kita tetapkan 25%.
    //     $kemiripan = rand(10, 40); // Hasil acak antara 10% dan 40%
    //     $status = ($kemiripan <= 25) ? 'Lulus' : 'Tolak';

    //     $data = [
    //         'id_progres' => $id_progres,
    //         'tanggal_cek' => date('Y-m-d'),
    //         'persentase_kemiripan' => $kemiripan,
    //         'status' => $status,
    //         'dokumen_laporan' => 'laporan_plagiarisme_' . $id_progres . '.pdf' // Dummy file
    //     ];
        
    //     $this->db->insert('hasil_plagiarisme', $data);
    //     return $data; // Kembalikan data yang diinsert
    // }
}