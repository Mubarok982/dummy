<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class M_skripsi_opt extends CI_Model {

    public function get_all_mahasiswa_skripsi()
    {
        $this->db->select('A.id AS id_mhs, A.nama, M.npm, M.prodi, S.id AS id_skripsi, S.judul, S.pembimbing1, S.pembimbing2, P1.nama AS nama_p1, P2.nama AS nama_p2');
        $this->db->from('mstr_akun A');
        $this->db->join('data_mahasiswa M', 'A.id = M.id', 'inner');
        $this->db->join('skripsi S', 'A.id = S.id_mahasiswa', 'left');
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

    public function assign_pembimbing($id_skripsi, $p1, $p2)
    {
        $data = ['pembimbing1' => $p1, 'pembimbing2' => $p2];
        $this->db->where('id', $id_skripsi);
        return $this->db->update('skripsi', $data);
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

    public function update_plagiarisme_status($id, $status)
    {
        $data = ['status' => $status, 'tanggal_cek' => date('Y-m-d')];
        $this->db->where('id', $id);
        return $this->db->update('hasil_plagiarisme', $data);
    }
}