<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class M_akun_opt extends CI_Model {

    // --- 1. GET USER DENGAN JOIN YANG LEBIH AMAN ---
    public function get_user_by_id($id)
    {
        // Ambil role dulu untuk menentukan join
        $role = $this->db->select('role')->where('id', $id)->get('mstr_akun')->row()->role;

        $this->db->select('A.id, A.username, A.nama, A.role, A.foto, A.password'); // Ambil data akun
        $this->db->from('mstr_akun A');

        if ($role == 'dosen') {
            // Ambil spesifik kolom dosen agar tidak bentrok
            $this->db->select('D.nidk, D.prodi as prodi, D.is_kaprodi, D.telepon');
            $this->db->join('data_dosen D', 'A.id = D.id', 'left');
        } elseif ($role == 'mahasiswa') {
            // Ambil spesifik kolom mahasiswa agar tidak bentrok
            $this->db->select('M.npm, M.prodi as prodi, M.angkatan, M.telepon');
            $this->db->join('data_mahasiswa M', 'A.id = M.id', 'left');
        }

        $this->db->where('A.id', $id);
        return $this->db->get()->row_array();
    }

    // --- 2. UPDATE USER (FIXED) ---
    public function update_user($id, $akun_data, $detail_data, $role)
    {
        $this->db->trans_start();
        
        // A. Update data Akun Utama (Nama, Password, dll)
        if (!empty($akun_data)) {
            $this->db->where('id', $id);
            $this->db->update('mstr_akun', $akun_data);
        }

        // B. Update Data Detail (NIDK, NPM, Prodi, dll)
        if (!empty($detail_data)) {
            $table = '';
            if ($role == 'dosen') {
                $table = 'data_dosen';
            } elseif ($role == 'mahasiswa') {
                $table = 'data_mahasiswa';
            }

            if ($table != '') {
                // Cek apakah data detail untuk ID ini sudah ada?
                $this->db->where('id', $id);
                $exists = $this->db->count_all_results($table);

                if ($exists > 0) {
                    // JIKA ADA -> UPDATE
                    $this->db->where('id', $id);
                    $this->db->update($table, $detail_data);
                } else {
                    // JIKA TIDAK ADA -> INSERT (Recovery Data)
                    // Masukkan ID agar relasi terjaga
                    $detail_data['id'] = $id; 

                    // Default values untuk kolom NOT NULL di data_mahasiswa (jika diperlukan)
                    if ($role == 'mahasiswa') {
                       // Isi default dummy agar insert tidak gagal karena constraint DB
                       $defaults = [
                           'status_mahasiswa' => 'Aktif', // Sesuaikan default DB
                           // Tambahkan kolom lain jika error "Field 'x' doesn't have a default value"
                       ];
                       $detail_data = array_merge($defaults, $detail_data);
                    }
                    
                    // Kita coba insert. Jika gagal karena kolom lain kosong, Transaksi akan rollback otomatis.
                    $this->db->insert($table, $detail_data);
                }
            }
        }
        
        $this->db->trans_complete();
        return $this->db->trans_status();
    }

    // --- FUNGSI LAINNYA (TETAP SAMA / PENYESUAIAN KECIL) ---

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
        $this->db->select('
            A.id, 
            A.username, 
            A.nama, 
            A.role, 
            D.nidk, 
            M.npm, 
            D.prodi AS prodi_dsn, 
            M.prodi AS prodi_mhs,
            COALESCE(D.is_kaprodi, 0) as is_kaprodi
        ');
        
        $this->_filter_users_query($role, $prodi, $keyword);
        
        $this->db->order_by('is_kaprodi', 'DESC');
        $this->db->order_by('A.role', 'ASC');
        $this->db->order_by('A.nama', 'ASC');

        if ($limit) $this->db->limit($limit, $offset);

        return $this->db->get()->result_array();
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

    public function delete_user($id)
    {
        $this->db->trans_start();
        $this->db->where('id', $id)->delete('data_dosen');
        $this->db->where('id', $id)->delete('data_mahasiswa');
        $this->db->where('id', $id)->delete('mstr_akun');
        $this->db->trans_complete();
        return $this->db->trans_status();
    }

    // --- Pagination Dosen ---
    private function _query_dosen_filter($prodi, $jabatan, $keyword)
    {
        $this->db->from('mstr_akun ma');
        $this->db->join('data_dosen dd', 'ma.id = dd.id', 'inner'); // Gunakan Inner agar data pasti dosen valid
        $this->db->where('ma.role', 'dosen');

        if (!empty($prodi)) {
            $this->db->where('dd.prodi', $prodi);
        }

        if (!empty($jabatan)) {
            if ($jabatan == 'kaprodi') {
                $this->db->where('dd.is_kaprodi', 1);
            } elseif ($jabatan == 'dosen') {
                $this->db->where('dd.is_kaprodi', 0);
            }
        }

        if (!empty($keyword)) {
            $this->db->group_start();
            $this->db->like('ma.nama', $keyword);
            $this->db->or_like('dd.nidk', $keyword);
            $this->db->group_end();
        }
    }

    public function count_dosen_filtered($prodi = null, $jabatan = null, $keyword = null)
    {
        $this->_query_dosen_filter($prodi, $jabatan, $keyword);
        return $this->db->count_all_results();
    }

    public function get_dosen_paginated($limit, $start, $prodi = null, $jabatan = null, $keyword = null)
    {
        $this->_query_dosen_filter($prodi, $jabatan, $keyword);
        $this->db->select('ma.id, ma.nama, ma.foto, dd.nidk, dd.prodi, dd.telepon, dd.is_kaprodi');
        $this->db->order_by('ma.nama', 'ASC');
        $this->db->limit($limit, $start);
        return $this->db->get()->result_array();
    }
}