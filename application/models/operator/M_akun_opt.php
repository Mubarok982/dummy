<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class M_akun_opt extends CI_Model {

    // Helper Private
    private function _filter_users_query($role, $prodi, $keyword)
    {
        $this->db->from('mstr_akun A');
        $this->db->join('data_dosen D', 'A.id = D.id', 'left');
        $this->db->join('data_mahasiswa M', 'A.id = M.id', 'left');

        if ($role && $role != '') $this->db->where('A.role', $role);
        if ($prodi && $prodi != '') {
            $this->db->group_start();
                $this->db->where('D.prodi', $prodi);
                $this->db->or_where('M.prodi', $prodi);
            $this->db->group_end();
        }
        if ($keyword && $keyword != '') {
            $this->db->group_start();
                $this->db->like('A.nama', $keyword);
                $this->db->or_like('A.username', $keyword);
                $this->db->or_like('D.nidk', $keyword);
                $this->db->or_like('M.npm', $keyword);
            $this->db->group_end();
        }
    }

    public function count_all_users($role = NULL, $prodi = NULL, $keyword = NULL)
    {
        $this->_filter_users_query($role, $prodi, $keyword);
        return $this->db->count_all_results();
    }

    public function get_all_users_with_details($role = NULL, $prodi = NULL, $keyword = NULL, $limit = NULL, $offset = NULL)
    {
        $this->db->select('A.id, A.username, A.nama, A.role, D.nidk, M.npm, M.prodi AS prodi_mhs, D.prodi AS prodi_dsn');
        $this->_filter_users_query($role, $prodi, $keyword);
        $this->db->order_by('A.role', 'ASC');
        $this->db->order_by('A.nama', 'ASC');

        if ($limit) $this->db->limit($limit, $offset);
        
        return $this->db->get()->result_array();
    }

    public function get_user_by_id($id)
    {
        $this->db->select('A.*, D.*, M.*, D.prodi AS prodi_dsn, M.prodi AS prodi_mhs');
        $this->db->from('mstr_akun A');
        $this->db->join('data_dosen D', 'A.id = D.id', 'left');
        $this->db->join('data_mahasiswa M', 'A.id = M.id', 'left');
        $this->db->where('A.id', $id);
        return $this->db->get()->row_array();
    }

    public function insert_user($akun_data, $detail_data, $role)
    {
        $this->db->trans_start();
        $this->db->insert('mstr_akun', $akun_data);
        $id = $this->db->insert_id();

        if ($detail_data) {
            $detail_data['id'] = $id;
            if ($role == 'dosen') $this->db->insert('data_dosen', $detail_data);
            elseif ($role == 'mahasiswa') $this->db->insert('data_mahasiswa', $detail_data);
        }
        $this->db->trans_complete();
        return $this->db->trans_status();
    }

    // --- PERBAIKAN PENTING ADA DI SINI ---
    public function update_user($id, $akun_data, $detail_data, $role)
    {
        $this->db->trans_start();
        
        // 1. Update data akun utama (mstr_akun)
        // Kolom 'nama' akan terupdate di sini
        $this->db->where('id', $id);
        $this->db->update('mstr_akun', $akun_data);

        // 2. Update atau Insert data detail (data_dosen / data_mahasiswa)
        if ($detail_data) {
            $table = '';
            if ($role == 'dosen') {
                $table = 'data_dosen';
            } elseif ($role == 'mahasiswa') {
                $table = 'data_mahasiswa';
            }

            if ($table != '') {
                // Cek apakah data detail sudah ada?
                $exists = $this->db->where('id', $id)->count_all_results($table);

                if ($exists > 0) {
                    // KASUS A: Data sudah ada -> Lakukan UPDATE biasa
                    // Kolom NPM, Prodi, Angkatan akan terupdate di sini
                    $this->db->where('id', $id);
                    $this->db->update($table, $detail_data);
                } else {
                    // KASUS B: Data belum ada (hilang/kosong) -> Lakukan INSERT (Recovery)
                    $detail_data['id'] = $id; 
                    
                    // FIX: Kita wajib menambahkan nilai default untuk kolom NOT NULL
                    // karena form Edit tidak mengirimkan data ini.
                    if ($role == 'mahasiswa') {
                        $defaults = [
                            'status_beasiswa' => 'Tidak Aktif',
                            'status_mahasiswa' => 'Murni',
                            'ttd' => 'dummy_ttd.png',
                            'dokumen_identitas' => 'dummy_doc.pdf',
                            'sertifikat_office_puskom' => 'dummy_cert.pdf',
                            'sertifikat_btq_ibadah' => 'dummy_cert.pdf',
                            'sertifikat_bahasa' => 'dummy_cert.pdf',
                            'sertifikat_kompetensi_ujian_komprehensif' => 'dummy_cert.pdf',
                            'sertifikat_semaba_ppk_masta' => 'dummy_cert.pdf',
                            'sertifikat_kkn' => 'dummy_cert.pdf',
                        ];
                        // Gabungkan data form (NPM, Prodi) dengan Default (TTD, Sertifikat)
                        $detail_data = array_merge($defaults, $detail_data);
                    }
                    
                    $this->db->insert($table, $detail_data);
                }
            }
        }
        
        $this->db->trans_complete();
        return $this->db->trans_status();
    }

    public function delete_user($id)
    {
        $this->db->trans_start();
        $this->db->where('id', $id)->delete('data_dosen');
        $this->db->where('id', $id)->delete('data_mahasiswa');
        $this->db->where('id', $id)->delete('mstr_akun');
        $this->db->trans_complete();
        return $this->db->trans_status();
    }
}