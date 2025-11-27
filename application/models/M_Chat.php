<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class M_Chat extends CI_Model {

    // Ambil pesan antara dua user (user login & lawan bicara)
    public function get_chat($id_user, $id_lawan)
    {
        // Query: Ambil pesan dimana (pengirim=SAYA dan penerima=DIA) ATAU (pengirim=DIA dan penerima=SAYA)
        $this->db->where("(id_pengirim = '$id_user' AND id_penerima = '$id_lawan') OR (id_pengirim = '$id_lawan' AND id_penerima = '$id_user')");
        $this->db->order_by('waktu', 'ASC');
        return $this->db->get('tbl_pesan')->result_array();
    }

    // Kirim pesan
    public function send_message($data)
    {
        return $this->db->insert('tbl_pesan', $data);
    }

    // Ambil daftar orang yang pernah chat (List Kontak)
    public function get_kontak_chat($id_user, $role_user)
    {
        // Jika Dosen, ambil daftar Mahasiswa bimbingannya
        if ($role_user == 'dosen') {
            $this->db->select('A.id, A.nama, A.foto, M.npm AS sub_info');
            $this->db->from('skripsi S');
            $this->db->join('data_mahasiswa M', 'S.id_mahasiswa = M.id');
            $this->db->join('mstr_akun A', 'M.id = A.id');
            $this->db->where("S.pembimbing1 = $id_user OR S.pembimbing2 = $id_user");
            return $this->db->get()->result_array();
        } 
        // Jika Mahasiswa, ambil Pembimbing 1 & 2
        else {
            $this->db->select('A.id, A.nama, A.foto, D.nidk AS sub_info');
            $this->db->from('skripsi S');
            $this->db->join('mstr_akun A', 'S.pembimbing1 = A.id OR S.pembimbing2 = A.id');
            $this->db->join('data_dosen D', 'A.id = D.id');
            $this->db->where('S.id_mahasiswa', $id_user);
            return $this->db->get()->result_array();
        }
    }
}