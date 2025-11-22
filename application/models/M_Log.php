<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class M_Log extends CI_Model {

    public function record($kategori, $deskripsi, $id_data_terkait = NULL)
    {
        $data = [
            'id_user'         => $this->session->userdata('id'),
            'user_role'       => $this->session->userdata('role'),
            'kategori'        => $kategori,
            'deskripsi'       => $deskripsi,
            'id_data_terkait' => $id_data_terkait,
        ];
        return $this->db->insert('log_aktivitas', $data);
    }
    
    // Untuk Laporan Kinerja Dosen
    public function get_dosen_activity_summary($id_dosen)
    {
        $this->db->select('DATE(timestamp) as tanggal, COUNT(id) as total_aksi');
        $this->db->from('log_aktivitas');
        $this->db->where('id_user', $id_dosen);
        $this->db->where('user_role', 'dosen');
        $this->db->where_in('kategori', ['Koreksi', 'ACC Sempro']); // Hanya hitung aksi bimbingan utama
        $this->db->group_by('tanggal');
        $this->db->order_by('tanggal', 'DESC');
        return $this->db->get()->result_array();
    }
}