<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class M_Chat extends CI_Model {

    public function get_chat($id_user, $id_lawan) {
        $this->db->where("(id_pengirim = '$id_user' AND id_penerima = '$id_lawan') OR (id_pengirim = '$id_lawan' AND id_penerima = '$id_user')");
        $this->db->order_by('waktu', 'ASC');
        return $this->db->get('tbl_pesan')->result_array();
    }

    public function send_message($data) {
        return $this->db->insert('tbl_pesan', $data);
    }

    public function get_valid_chat_recipients_mhs($npm) {
        // 1. Cari ID Kaprodi (Dosen yang is_kaprodi = 1)
        $kaprodi = $this->db->get_where('data_dosen', ['is_kaprodi' => 1])->row_array();
        $id_kaprodi = $kaprodi ? $kaprodi['id'] : null;

        // 2. Cari ID Pembimbing dari tabel skripsi
        $this->db->select('S.pembimbing1, S.pembimbing2, S.status_acc_kaprodi');
        $this->db->from('skripsi S');
        $this->db->join('data_mahasiswa M', 'S.id_mahasiswa = M.id');
        $this->db->where('M.npm', $npm);
        $skripsi = $this->db->get()->row_array();

        $valid_ids = [];
        if ($id_kaprodi) $valid_ids[] = $id_kaprodi;

        if ($skripsi && $skripsi['status_acc_kaprodi'] == 'diterima') {
            if ($skripsi['pembimbing1']) $valid_ids[] = $skripsi['pembimbing1'];
            if ($skripsi['pembimbing2']) $valid_ids[] = $skripsi['pembimbing2'];
        }
        return $valid_ids;
    }

    public function get_kontak_chat($id_user, $role_user) {
        if ($role_user == 'dosen') {
            // Cek apakah user ini Kaprodi
            $check = $this->db->get_where('data_dosen', ['id' => $id_user, 'is_kaprodi' => 1])->row();
            
            if ($check) {
                // KAPRODI: Lihat semua mahasiswa di prodinya
                $this->db->select('A.id, A.nama, A.foto, M.npm as sub_info');
                $this->db->from('mstr_akun A');
                $this->db->join('data_mahasiswa M', 'A.id = M.id');
                $this->db->where('M.prodi', $check->prodi);
                $this->db->where('A.id !=', $id_user);
                return $this->db->get()->result_array();
            } else {
                // DOSEN BIASA: Lihat mahasiswa bimbingannya saja
                $this->db->select('A.id, A.nama, A.foto, M.npm AS sub_info');
                $this->db->from('skripsi S');
                $this->db->join('data_mahasiswa M', 'S.id_mahasiswa = M.id');
                $this->db->join('mstr_akun A', 'M.id = A.id');
                $this->db->where("(S.pembimbing1 = $id_user OR S.pembimbing2 = $id_user)");
                return $this->db->get()->result_array();
            }
        } 
        else if ($role_user == 'mahasiswa') {
            $npm = $this->session->userdata('npm');
            $valid_ids = $this->get_valid_chat_recipients_mhs($npm);
            if (empty($valid_ids)) return [];

            $this->db->select('A.id, A.nama, A.foto, D.nidk AS sub_info, A.role');
            $this->db->from('mstr_akun A');
            $this->db->join('data_dosen D', 'A.id = D.id', 'left');
            $this->db->where_in('A.id', $valid_ids);
            $kontak = $this->db->get()->result_array();

            foreach ($kontak as $key => $k) {
                $check_kaprodi = $this->db->get_where('data_dosen', ['id' => $k['id'], 'is_kaprodi' => 1])->row();
                $kontak[$key]['sub_info'] = $check_kaprodi ? 'Kaprodi' : ($k['sub_info'] ?? 'Dosen');
            }
            return $kontak;
        }
        return [];
    }
}