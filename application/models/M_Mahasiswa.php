<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class M_Mahasiswa extends CI_Model {

    // --- Skripsi (Judul) ---

    public function get_skripsi_by_mhs($id_mahasiswa)
    {
        $this->db->select('S.*, P1.nama AS nama_p1, P2.nama AS nama_p2');
        $this->db->from('skripsi S');
        $this->db->join('mstr_akun P1', 'S.pembimbing1 = P1.id', 'left');
        $this->db->join('mstr_akun P2', 'S.pembimbing2 = P2.id', 'left');
        $this->db->where('S.id_mahasiswa', $id_mahasiswa);
        return $this->db->get()->row_array();
    }
    
    public function insert_skripsi($data)
    {
        return $this->db->insert('skripsi', $data);
    }
    
    public function update_skripsi_judul($id_mahasiswa, $data)
    {
        $this->db->where('id_mahasiswa', $id_mahasiswa);
        // Kita hanya mengizinkan update judul, tema, dan pembimbing awal
        $update_data = [
            'tema' => $data['tema'],
            'judul' => $data['judul'],
            'pembimbing1' => $data['pembimbing1'],
            'pembimbing2' => $data['pembimbing2'],
        ];
        return $this->db->update('skripsi', $update_data);
    }

    // --- Progres Bimbingan ---
    
   // --- PERBAIKAN LOGIKA PENGAMBILAN PROGRES ---
    public function get_progres_by_skripsi($id_skripsi)
    {
        // Langkah 1: Cari tahu dulu Siapa pemilik skripsi ini (Ambil NPM-nya)
        // Kita JOIN tabel 'skripsi' dengan 'data_mahasiswa'
        $this->db->select('M.npm');
        $this->db->from('skripsi S');
        $this->db->join('data_mahasiswa M', 'S.id_mahasiswa = M.id'); // Hubungkan via ID Mahasiswa
        $this->db->where('S.id', $id_skripsi);
        
        $data_mhs = $this->db->get()->row_array();

        // Jika data tidak ditemukan (misal ID skripsi salah), kembalikan array kosong
        if (!$data_mhs) {
            return [];
        }

        $npm_mahasiswa = $data_mhs['npm'];

        // Langkah 2: Ambil progres berdasarkan NPM yang sudah didapat
        $this->db->order_by('bab', 'ASC');
        return $this->db->get_where('progres_skripsi', ['npm' => $npm_mahasiswa])->result_array();
    }
    
    public function insert_progres($data)
    {
        return $this->db->insert('progres_skripsi', $data);
    }
    
    public function update_progres($id_progres, $data)
    {
        $this->db->where('id', $id_progres);
        return $this->db->update('progres_skripsi', $data);
    }
}