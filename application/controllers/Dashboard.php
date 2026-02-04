<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Dashboard extends CI_Controller {

    public function __construct()
    {
        parent::__construct();
        // --- AKTIFKAN CEK LOGIN ---
        if (!$this->session->userdata('id')) {
            redirect('auth/login'); // Redirect ke login jika sesi tidak ada
        }
        // --- END CEK LOGIN ---
        $this->load->model('M_Akun'); // Tambahkan pemuatan model
        $this->load->model('M_Data'); // Panggil M_Data untuk statistik global/operator
        $this->load->model('M_Mahasiswa'); // Panggil M_Mahasiswa untuk detail mhs
        $this->load->model('M_Dosen');
    }

    public function index()
    // Di dalam method index() controller Dashboard
   
    {   
         if ($this->session->userdata('is_kaprodi') == 1) {
        $prodi = $this->session->userdata('prodi');
        $data['stats_kaprodi'] = $this->M_Dosen->get_stats_kaprodi($prodi);
    }
        $data['title'] = 'Dashboard Utama';
        $role = $this->session->userdata('role');
        $id_user = $this->session->userdata('id');
        $npm = $this->session->userdata('npm');
        
        $data['statistik'] = [];

        if ($role == 'operator' || $role == 'tata_usaha') {
            // Statistik untuk Operator/Tata Usaha
            $data['statistik']['total_mhs'] = $this->M_Data->count_mahasiswa();
            $data['statistik']['total_dosen'] = $this->M_Data->count_dosen();
            $data['statistik']['mhs_skripsi'] = $this->M_Data->count_mahasiswa_with_skripsi();
            $data['statistik']['mhs_ready_sempro'] = $this->M_Data->count_mahasiswa_ready_sempro();

        } elseif ($role == 'dosen') {
            // Statistik untuk Dosen
            $data['statistik']['total_bimbingan'] = $this->M_Dosen->count_total_bimbingan($id_user);
            // Anda bisa tambahkan statistik lain, misal: bimbingan yang butuh koreksi

        } elseif ($role == 'mahasiswa') {
            // Statistik untuk Mahasiswa
            $skripsi = $this->M_Mahasiswa->get_skripsi_by_mhs($id_user);
            $data['statistik']['judul_status'] = $skripsi ? 'Sudah Diajukan' : 'Belum Diajukan';
            
            if ($skripsi) {
                $progres = $this->M_Mahasiswa->get_progres_by_skripsi($skripsi['id']);
                $data['statistik']['last_bab'] = count($progres) > 0 ? end($progres)['bab'] : 0;
            } else {
                $data['statistik']['last_bab'] = 0;
            }

            // Ambil Riwayat Pengajuan Judul Lengkap
            $this->db->select('s.*, d1.nama as nama_p1, d2.nama as nama_p2');
            $this->db->from('skripsi s');
            // Join ke tabel akun untuk dapat nama Dosen Pembimbing 1 & 2
            $this->db->join('mstr_akun d1', 's.pembimbing1 = d1.id', 'left');
            $this->db->join('mstr_akun d2', 's.pembimbing2 = d2.id', 'left');
            $this->db->where('s.id_mahasiswa', $this->session->userdata('id'));
            $this->db->order_by('s.tgl_pengajuan_judul', 'DESC'); // Yang terbaru paling atas
            $data['riwayat_judul'] = $this->db->get()->result_array();
        }
        
        $this->load->view('template/header', $data);
        $this->load->view('template/sidebar', $data);
        $this->load->view('dashboard_view', $data); 
        $this->load->view('template/footer');
    }
}