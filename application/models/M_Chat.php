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
    // 1. Cek status Kaprodi dan ambil Prodinya
    $check = $this->db->get_where('data_dosen', ['id' => $id_user, 'is_kaprodi' => 1])->row();
    
    // ... bagian awal fungsi
    if ($check) {
        // KAPRODI: Lihat SEMUA DOSEN & MAHASISWA di prodinya
        $prodi_saya = $check->prodi;

        // Tambahkan parameter FALSE di akhir select agar tidak error backtick
        $this->db->select('A.id, A.nama, A.foto, A.role, 
                        (CASE 
                            WHEN A.role = "dosen" THEN D.nidk 
                            ELSE M.npm 
                        END) as sub_info', FALSE); 
        
        $this->db->from('mstr_akun A');
        $this->db->join('data_dosen D', 'A.id = D.id', 'left');
        $this->db->join('data_mahasiswa M', 'A.id = M.id', 'left');
        
        // Filter: Harus di prodi yang sama
        $this->db->group_start();
            $this->db->where('D.prodi', $prodi_saya);
            $this->db->or_where('M.prodi', $prodi_saya);
        $this->db->group_end();
        
        $this->db->where('A.id !=', $id_user);
        $this->db->order_by('A.role', 'ASC');
        $this->db->order_by('A.nama', 'ASC');
        
        return $this->db->get()->result_array();
    
    // ... sisa fungsi

    } else {
        // DOSEN BIASA: Tetap cuma lihat mahasiswa bimbingannya saja
        $this->db->select('A.id, A.nama, A.foto, A.role, M.npm AS sub_info');
        $this->db->from('skripsi S');
        $this->db->join('data_mahasiswa M', 'S.id_mahasiswa = M.id');
        $this->db->join('mstr_akun A', 'M.id = A.id');
        $this->db->where("(S.pembimbing1 = $id_user OR S.pembimbing2 = $id_user)");
        $this->db->group_by('A.id'); // Jaga-jaga kalau ada data double
        
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

    public function get_kontak_filtered($allowed_ids) {
    if (empty($allowed_ids)) return [];
    
    $this->db->select('id, nama, foto, role');
    $this->db->from('mstr_akun');
    $this->db->where_in('id', $allowed_ids);
    $query = $this->db->get();
    
    return $query->result_array();
}

public function get_list_angkatan($prodi)
    {
        $this->db->distinct();
        $this->db->select('angkatan');
        $this->db->from('data_mahasiswa');
        $this->db->where('prodi', $prodi);
        $this->db->order_by('angkatan', 'DESC');
        return $this->db->get()->result_array();
    }

    // 2. Update fungsi ini untuk menerima parameter filtering angkatan
    public function get_all_mahasiswa_prodi($prodi, $angkatan_filter = null)
    {
        $this->db->select('A.nama, A.id as id_user, M.npm, M.angkatan, S.judul, S.status_acc_kaprodi, S.id as id_skripsi, P1.nama AS p1, P2.nama AS p2');
        $this->db->from('data_mahasiswa M');
        $this->db->join('mstr_akun A', 'M.id = A.id', 'inner');
        $this->db->join('skripsi S', 'M.id = S.id_mahasiswa', 'left');
        $this->db->join('mstr_akun P1', 'S.pembimbing1 = P1.id', 'left');
        $this->db->join('mstr_akun P2', 'S.pembimbing2 = P2.id', 'left');
        
        $this->db->where('M.prodi', $prodi);
        $this->db->where('A.role', 'mahasiswa');

        // TAMBAHAN: Logika Filter Angkatan
        if ($angkatan_filter && $angkatan_filter != 'all') {
            $this->db->where('M.angkatan', $angkatan_filter);
        }

        $this->db->order_by('M.angkatan', 'ASC');
        $this->db->order_by('M.npm', 'ASC');
        
        return $this->db->get()->result_array();
    }

    // ... (Sisa fungsi update_status_judul, count_dosen, dll TETAP SAMA) ...
    
    public function update_status_judul($id_skripsi, $status)
    {
        $this->db->where('id', $id_skripsi);
        return $this->db->update('skripsi', ['status_acc_kaprodi' => $status]);
    }
}
