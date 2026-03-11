<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Akun extends CI_Controller {

    public function __construct()
    {
        parent::__construct();
        if ($this->session->userdata('role') != 'operator') redirect('auth/login');
        $this->load->model('operator/M_akun_opt'); 
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
        
        // Styling Pagination
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
        
        // Aturan validasi username dengan pesan bahasa Indonesia
        $this->form_validation->set_rules('username', 'Username', 'required|is_unique[mstr_akun.username]|trim', [
            'is_unique' => 'Username telah digunakan user lain!'
        ]);
        $this->form_validation->set_rules('password', 'Password', 'required|min_length[3]');
        $this->form_validation->set_rules('nama', 'Nama', 'required');
        $this->form_validation->set_rules('role', 'Role', 'required');

        // =====================================================================
        // VALIDASI DINAMIS ANTI DUPLIKAT (NIDK & NPM) SAAT TAMBAH
        // =====================================================================
        $role = $this->input->post('role');
        if ($role == 'dosen') {
            $this->form_validation->set_rules('nidk', 'NIDN/NIDK', 'required|is_unique[data_dosen.nidk]|trim', [
                'is_unique' => 'NIDN/NIDK telah digunakan user lain!'
            ]);
        } elseif ($role == 'mahasiswa') {
            $this->form_validation->set_rules('npm', 'NPM', 'required|is_unique[data_mahasiswa.npm]|trim', [
                'is_unique' => 'NPM telah digunakan user lain!'
            ]);
        }
        // =====================================================================

        if ($this->form_validation->run() == FALSE) {
            $this->load->view('template/header', $data);
            $this->load->view('template/sidebar', $data);
            $this->load->view('operator/v_tambah_edit_akun', $data);
            $this->load->view('template/footer');
        } else {
            $this->_proses_tambah($role);
        }
    }

    private function _proses_tambah($role) 
    {
        $akun_data = [
            'username' => $this->input->post('username'),
            'password' => password_hash($this->input->post('password'), PASSWORD_DEFAULT), 
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
        $role = $data['user']['role'];
        
        $this->form_validation->set_rules('nama', 'Nama', 'required');
        
        if ($this->input->post('password')) {
            $this->form_validation->set_rules('password', 'Password', 'min_length[3]');
        }

        // =====================================================================
        // VALIDASI DINAMIS ANTI DUPLIKAT (NIDK & NPM) SAAT EDIT
        // =====================================================================
        if ($role == 'dosen') {
            $current_nidk = $this->db->get_where('data_dosen', ['id' => $id])->row('nidk');
            $post_nidk = $this->input->post('nidk');
            
            // Aturan: Hanya jalankan is_unique jika admin MENGUBAH NIDK ke nomor lain
            $rule_nidk = 'required|trim';
            if ($post_nidk != $current_nidk) {
                $rule_nidk .= '|is_unique[data_dosen.nidk]';
            }
            
            $this->form_validation->set_rules('nidk', 'NIDN/NIDK', $rule_nidk, [
                'is_unique' => 'NIDN/NIDK telah digunakan user lain!'
            ]);

        } elseif ($role == 'mahasiswa') {
            $current_npm = $this->db->get_where('data_mahasiswa', ['id' => $id])->row('npm');
            $post_npm = $this->input->post('npm');
            
            // Aturan: Hanya jalankan is_unique jika admin MENGUBAH NPM ke nomor lain
            $rule_npm = 'required|trim';
            if ($post_npm != $current_npm) {
                $rule_npm .= '|is_unique[data_mahasiswa.npm]';
            }

            $this->form_validation->set_rules('npm', 'NPM', $rule_npm, [
                'is_unique' => 'NPM telah digunakan user lain!'
            ]);
        }
        // =====================================================================

        if ($this->form_validation->run() == FALSE) {
            // Jika gagal (ada yg duplikat), kembalikan langsung ke form edit tanpa reload halaman index
            $this->load->view('template/header', $data);
            $this->load->view('template/sidebar', $data);
            $this->load->view('operator/v_tambah_edit_akun', $data);
            $this->load->view('template/footer');
        } else {
            $this->_proses_edit($id, $role);
        }
    }

    private function _proses_edit($id, $role) 
    {
        $akun_data = ['nama' => $this->input->post('nama')];
        
        if ($this->input->post('password')) {
            $akun_data['password'] = password_hash($this->input->post('password'), PASSWORD_DEFAULT);
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
        $res = $this->M_akun_opt->delete_user($id);
        if ($res === 'blocked') {
            $this->session->set_flashdata('pesan_error', 'Penghapusan diblokir: pengguna ini memiliki riwayat skripsi/progres/ujian.');
        } elseif ($res) {
            $this->session->set_flashdata('pesan_sukses', 'Akun berhasil dihapus!');
        } else {
            $this->session->set_flashdata('pesan_error', 'Gagal menghapus akun.');
        }
        redirect('operator/akun');
    }
}