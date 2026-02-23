<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class M_Akun extends CI_Model {

    // Fungsi untuk memverifikasi login (mencocokkan username)
    public function cek_login($username)
    {
        // Ambil data akun dan gabungkan dengan tabel detail dosen/mahasiswa
        $this->db->select('A.*');
        $this->db->from('mstr_akun A');
        $this->db->join('data_dosen D', 'A.id = D.id', 'left');
        $this->db->join('data_mahasiswa M', 'A.id = M.id', 'left');

        // Mencari berdasarkan beberapa kemungkinan input:
        // - username (A.username)
        // - id akun (A.id)
        // - nidk di tabel data_dosen (D.nidk)
        // - npm di tabel data_mahasiswa (M.npm)
        $this->db->group_start();
            // Make username check case-sensitive using BINARY comparison
            $this->db->where("BINARY A.username = " . $this->db->escape($username), NULL, FALSE);
            $this->db->or_where('A.id', $username);
            $this->db->or_where('D.nidk', $username);
            $this->db->or_where('M.npm', $username);
        $this->db->group_end();

        $query = $this->db->get();

        if ($query->num_rows() > 0) {
            return $query->row_array();
        }

        return FALSE;
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