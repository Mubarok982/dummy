<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class M_Akun extends CI_Model {

    // Fungsi untuk memverifikasi login (mencocokkan username)
    public function cek_login($username)
    {
        // Ambil data akun berdasarkan username
        $this->db->select('*');
        $this->db->from('mstr_akun');
        $this->db->where('username', $username);
        // Gabungkan dengan data dosen/mahasiswa jika ditemukan
        $this->db->or_where('id', $username); // Jika user input ID/NIDN/NPM
        
        $query = $this->db->get();

        if ($query->num_rows() > 0) {
            return $query->row_array();
        } else {
            return FALSE;
        }
    }

    // Fungsi untuk mendapatkan detail user berdasarkan ID
    public function get_user_details($id, $role)
    {
        // Kita hanya mengambil data yang relevan dengan role-nya
        if ($role == 'dosen') {
            $this->db->select('A.*, D.nidk, D.prodi, D.is_kaprodi, D.is_praktisi');
            $this->db->from('mstr_akun A');
            $this->db->join('data_dosen D', 'A.id = D.id', 'left');
            $this->db->where('A.id', $id);
            return $this->db->get()->row_array();

        } elseif ($role == 'mahasiswa') {
            $this->db->select('A.*, M.npm, M.prodi, M.angkatan, M.jenis_kelamin');
            $this->db->from('mstr_akun A');
            $this->db->join('data_mahasiswa M', 'A.id = M.id', 'left');
            $this->db->where('A.id', $id);
            return $this->db->get()->row_array();

        } else { // operator/tata_usaha
            $this->db->select('A.*');
            $this->db->from('mstr_akun A');
            $this->db->where('A.id', $id);
            return $this->db->get()->row_array();
        }
    }
}