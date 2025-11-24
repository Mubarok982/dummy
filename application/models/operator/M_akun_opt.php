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

    public function update_user($id, $akun_data, $detail_data, $role)
    {
        $this->db->trans_start();
        $this->db->where('id', $id);
        $this->db->update('mstr_akun', $akun_data);

        if ($detail_data) {
            $this->db->where('id', $id);
            if ($role == 'dosen') $this->db->update('data_dosen', $detail_data);
            elseif ($role == 'mahasiswa') $this->db->update('data_mahasiswa', $detail_data);
        }
        $this->db->trans_complete();
        return $this->db->trans_status();
    }

    public function delete_user($id)
    {
        $this->db->where('id', $id);
        return $this->db->delete('mstr_akun');
    }
}