<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Akun extends CI_Controller {

    public function __construct()
    {
        parent::__construct();
        if ($this->session->userdata('role') != 'operator') redirect('auth/login');
        $this->load->model('operator/M_akun_opt'); // Load Model Khusus
        $this->load->model('M_Log');
    }

    public function index()
    {
        $data['title'] = 'Manajemen Akun';
        $this->load->library('pagination');

        $role = $this->input->get('role');
        $prodi = $this->input->get('prodi');
        $keyword = $this->input->get('keyword');

        $config['base_url'] = base_url('operator/akun/index');
        $config['total_rows'] = $this->M_akun_opt->count_all_users($role, $prodi, $keyword);
        $config['per_page'] = 15;
        $config['reuse_query_string'] = TRUE;
        $config['page_query_string'] = TRUE;
        $config['query_string_segment'] = 'page';
        
        // Styling Pagination AdminLTE
        $config['full_tag_open']    = '<ul class="pagination pagination-sm m-0 float-right">';
        $config['full_tag_close']   = '</ul>';
        $config['first_link']       = '&laquo;';
        $config['last_link']        = '&raquo;';
        $config['first_tag_open']   = '<li class="page-item">';
        $config['first_tag_close']  = '</li>';
        $config['prev_link']        = '&lsaquo;';
        $config['prev_tag_open']    = '<li class="page-item">';
        $config['prev_tag_close']   = '</li>';
        $config['next_link']        = '&rsaquo;';
        $config['next_tag_open']    = '<li class="page-item">';
        $config['next_tag_close']   = '</li>';
        $config['last_tag_open']    = '<li class="page-item">';
        $config['last_tag_close']   = '</li>';
        $config['cur_tag_open']     = '<li class="page-item active"><span class="page-link">';
        $config['cur_tag_close']    = '</span></li>';
        $config['num_tag_open']     = '<li class="page-item">';
        $config['num_tag_close']    = '</li>';
        $config['attributes']       = array('class' => 'page-link');

        $this->pagination->initialize($config);

        $page = $this->input->get('page') ? $this->input->get('page') : 0;
        $data['users'] = $this->M_akun_opt->get_all_users_with_details($role, $prodi, $keyword, $config['per_page'], $page);
        
        $data['pagination'] = $this->pagination->create_links();
        $data['total_rows'] = $config['total_rows'];
        $data['start_index'] = $page;

        $this->load->view('template/header', $data);
        $this->load->view('template/sidebar', $data);
        $this->load->view('operator/v_manajemen_akun', $data);
        $this->load->view('template/footer');
    }

    public function tambah()
    {
        $data['title'] = 'Tambah Akun Baru';
        
        $this->form_validation->set_rules('username', 'Username', 'required|is_unique[mstr_akun.username]|trim');
        $this->form_validation->set_rules('password', 'Password', 'required|min_length[3]');
        $this->form_validation->set_rules('nama', 'Nama', 'required');
        $this->form_validation->set_rules('role', 'Role', 'required');

        if ($this->form_validation->run() == FALSE) {
            $this->load->view('template/header', $data);
            $this->load->view('template/sidebar', $data);
            $this->load->view('operator/v_tambah_edit_akun', $data);
            $this->load->view('template/footer');
        } else {
            $this->_proses_tambah();
        }
    }

    private function _proses_tambah() {
        $role = $this->input->post('role');
        $akun_data = [
            'username' => $this->input->post('username'),
            'password' => $this->input->post('password'), 
            'nama'     => $this->input->post('nama'),
            'role'     => $role,
        ];

        $detail_data = [];
        if ($role == 'dosen') {
            $detail_data = [
                'nidk' => $this->input->post('nidk'),
                'prodi' => $this->input->post('prodi_dosen'),
                'is_kaprodi' => $this->input->post('is_kaprodi') ? 1 : 0
            ];
        } elseif ($role == 'mahasiswa') {
            $detail_data = [
                'npm' => $this->input->post('npm'),
                'prodi' => $this->input->post('prodi_mhs'),
                'angkatan' => $this->input->post('angkatan'),
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
        }

        if ($this->M_akun_opt->insert_user($akun_data, $detail_data, $role)) {
            $this->session->set_flashdata('pesan_sukses', 'Akun ' . $role . ' berhasil ditambahkan!');
            $this->M_Log->record('Akun', 'Menambahkan akun baru: ' . $role . ' (' . $akun_data['nama'] . ')');
        } else {
            $this->session->set_flashdata('pesan_error', 'Gagal menambahkan akun.');
        }
        redirect('operator/akun');
    }
    
    public function edit($id)
    {
        $data['user'] = $this->M_akun_opt->get_user_by_id($id);
        if (!$data['user']) redirect('operator/akun');

        $data['title'] = 'Edit Akun: ' . $data['user']['nama'];
        
        $this->form_validation->set_rules('nama', 'Nama', 'required');
        $this->form_validation->set_rules('role', 'Role', 'required');
        
        if ($this->input->post('password')) {
            $this->form_validation->set_rules('password', 'Password', 'min_length[3]');
        }

        if ($this->form_validation->run() == FALSE) {
            $this->load->view('template/header', $data);
            $this->load->view('template/sidebar', $data);
            $this->load->view('operator/v_tambah_edit_akun', $data);
            $this->load->view('template/footer');
        } else {
            $this->_proses_edit($id);
        }
    }

    private function _proses_edit($id) {
        $role = $this->input->post('role');
        $akun_data = ['nama' => $this->input->post('nama'), 'role' => $role];
        
        if ($this->input->post('password')) {
            $akun_data['password'] = $this->input->post('password');
        }

        $detail_data = [];
        if ($role == 'dosen') {
            $detail_data = [
                'nidk' => $this->input->post('nidk'),
                'prodi' => $this->input->post('prodi_dosen'),
                'is_kaprodi' => $this->input->post('is_kaprodi') ? 1 : 0
            ];
        } elseif ($role == 'mahasiswa') {
            $detail_data = [
                'npm' => $this->input->post('npm'),
                'prodi' => $this->input->post('prodi_mhs'),
                'angkatan' => $this->input->post('angkatan'),
            ];
        }

        if ($this->M_akun_opt->update_user($id, $akun_data, $detail_data, $role)) {
            $this->session->set_flashdata('pesan_sukses', 'Akun berhasil diperbarui!');
        } else {
            $this->session->set_flashdata('pesan_error', 'Gagal memperbarui akun.');
        }
        redirect('operator/akun');
    }

    public function delete($id)
    {
        if ($this->M_akun_opt->delete_user($id)) {
            $this->session->set_flashdata('pesan_sukses', 'Akun berhasil dihapus!');
        } else {
            $this->session->set_flashdata('pesan_error', 'Gagal menghapus akun.');
        }
        redirect('operator/akun');
    }
}