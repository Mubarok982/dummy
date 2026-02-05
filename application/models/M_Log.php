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

    // Untuk Detail Laporan Kinerja Dosen per Semester
    public function get_dosen_activity_by_semester($id_dosen, $semester, $prodi)
    {
        // Konversi semester ke date range
        $start_date = null;
        $end_date = null;

        if (!empty($semester)) {
            $parts = explode(' ', $semester);
            if (count($parts) >= 2) {
                $years = explode('/', $parts[0]);
                $type = strtolower($parts[1]);

                $start_year = $years[0];
                $end_year = isset($years[1]) ? $years[1] : $start_year;

                if ($type == 'ganjil') {
                    // Ganjil: 1 Sept - 28 Feb
                    $start_date = $start_year . '-09-01';
                    $end_date = $end_year . '-02-28';
                } else {
                    // Genap: 1 Maret - 31 Agust
                    $start_date = $end_year . '-03-01';
                    $end_date = $end_year . '-08-31';
                }
            }
        }

        $this->db->select('DATE(timestamp) as tanggal, COUNT(id) as total_aksi, GROUP_CONCAT(deskripsi SEPARATOR "; ") as detail');
        $this->db->from('log_aktivitas');
        $this->db->where('id_user', $id_dosen);
        $this->db->where('user_role', 'dosen');
        $this->db->where_in('kategori', ['Koreksi', 'ACC Sempro']);

        if ($start_date && $end_date) {
            $this->db->where('DATE(timestamp) >=', $start_date);
            $this->db->where('DATE(timestamp) <=', $end_date);
        }

        // Jika prodi disediakan, filter berdasarkan prodi (asumsi ada relasi)
        // Untuk sekarang, skip filter prodi karena log tidak memiliki kolom prodi langsung

        $this->db->group_by('DATE(timestamp)');
        $this->db->order_by('tanggal', 'DESC');
        return $this->db->get()->result_array();
    }
}