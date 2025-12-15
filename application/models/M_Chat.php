<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class M_Chat extends CI_Model {

    public function get_chat($id_user, $id_lawan)
    {
        $this->db->where("(id_pengirim = '$id_user' AND id_penerima = '$id_lawan') OR (id_pengirim = '$id_lawan' AND id_penerima = '$id_user')");
        $this->db->order_by('waktu', 'ASC');
        return $this->db->get('tbl_pesan')->result_array();
    }

    public function send_message($data)
    {
        return $this->db->insert('tbl_pesan', $data);
    }

    public function get_valid_chat_recipients_mhs($npm)
    {
        $this->db->select('S.pembimbing1, S.pembimbing2, S.status_acc_kaprodi, DM.prodi');
        $this->db->from('skripsi S');
        $this->db->join('data_mahasiswa DM', 'S.id_mahasiswa = DM.id');
        $this->db->where('DM.npm', $npm);
        $skripsi = $this->db->get()->row_array();

        $recipients = [];
        
        // 1. Ambil ID Kaprodi (selalu bisa di-chat jika belum ACC)
        $kaprodi = $this->db->get_where('mstr_akun', ['role' => 'kaprodi'])->row(); // Asumsi hanya ada 1 kaprodi

        if ($kaprodi) {
             $recipients['kaprodi'] = $kaprodi->id;
        }

        // 2. Jika sudah di ACC, tambahkan Dospem 1 dan Dospem 2
        if (!empty($skripsi) && $skripsi['status_acc_kaprodi'] == 'diterima') {
            $recipients['pembimbing1'] = $skripsi['pembimbing1'];
            $recipients['pembimbing2'] = $skripsi['pembimbing2'];
        }

        return array_values($recipients); // Mengembalikan array ID pengguna yang valid
    }

    public function get_kontak_chat($id_user, $role_user)
    {
        if ($role_user == 'dosen') {
            $this->db->select('A.id, A.nama, A.foto, M.npm AS sub_info');
            $this->db->from('skripsi S');
            $this->db->join('data_mahasiswa M', 'S.id_mahasiswa = M.id');
            $this->db->join('mstr_akun A', 'M.id = A.id');
            $this->db->where("S.pembimbing1 = $id_user OR S.pembimbing2 = $id_user");
            return $this->db->get()->result_array();
        } 
        else if ($role_user == 'mahasiswa') {
            $npm = $this->session->userdata('npm');
            $valid_ids = $this->get_valid_chat_recipients_mhs($npm);
            
            if (empty($valid_ids)) {
                return [];
            }
            
            // Ambil detail akun yang valid
            $this->db->select('A.id, A.nama, A.foto, D.nidk AS sub_info, A.role');
            $this->db->from('mstr_akun A');
            $this->db->where_in('A.id', $valid_ids);
            $this->db->join('data_dosen D', 'A.id = D.id', 'left');
            
            // Logika custom untuk Kaprodi (tambahkan info 'Kaprodi' jika ada)
            $kontak = $this->db->get()->result_array();

            foreach ($kontak as $key => $k) {
                 if ($k['role'] == 'kaprodi') {
                    $kontak[$key]['sub_info'] = 'Kaprodi';
                 } else if (empty($k['sub_info'])) {
                    $kontak[$key]['sub_info'] = 'Dosen';
                 }
            }
            return $kontak;
        }
        // Jika role lain, bisa ditambahkan di sini
        return [];
    }
}