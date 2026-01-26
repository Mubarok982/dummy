<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Auth extends CI_Controller {

    public function __construct()
    {
        parent::__construct();
        $this->load->model('M_Akun');
    }

    public function index()
    {
        if ($this->session->userdata('is_login')) {
            $this->_redirect_logic();
        } else {
            $this->login();
        }
    }

    public function login()
    {
        $this->load->view('v_login');
    }

    public function login_aksi()
    {
        $this->form_validation->set_rules('username', 'Username', 'required|trim');
        $this->form_validation->set_rules('password', 'Password', 'required|trim');

        if ($this->form_validation->run() == FALSE) {
            $this->login(); 
        } else {
            $username = $this->input->post('username');
            $password = $this->input->post('password');

            $user = $this->M_Akun->cek_login($username);

            if ($user) {
                // Debug: Cek format password
                log_message('debug', 'Password from DB: ' . substr($user['password'], 0, 20) . '...');
                log_message('debug', 'Password input: ' . $password);

                // Cek Password (Hash)
                if (password_verify($password, $user['password'])) {
                    
                    // Ambil detail lengkap
                    $detail = $this->M_Akun->get_user_details($user['id'], $user['role']);

                    // Set Session Dasar
                    $session_data = array(
                        'id'        => $detail['id'],
                        'username'  => $detail['username'],
                        'nama'      => $detail['nama'],
                        'foto'      => $detail['foto'],
                        'role'      => $detail['role'],
                        'is_login'  => TRUE
                    );

                    // Set Session Khusus
                    if ($detail['role'] == 'dosen') {
                        $session_data['nidk'] = $detail['nidk'];
                        $session_data['prodi'] = $detail['prodi'];
                        $session_data['is_kaprodi'] = $detail['is_kaprodi'];
                    } elseif ($detail['role'] == 'mahasiswa') {
                        $session_data['npm'] = $detail['npm'];
                        $session_data['prodi'] = $detail['prodi'];
                    }
                    
                    $this->session->set_userdata($session_data);
                    
                    // Panggil fungsi redirect khusus
                    $this->_redirect_logic();

                } else {
                    $this->session->set_flashdata('pesan_error', 'Password salah!');
                    redirect('auth/login');
                }
            } else {
                $this->session->set_flashdata('pesan_error', 'Akun tidak ditemukan.');
                redirect('auth/login');
            }
        }
    }

    // Fungsi Private untuk Logika Redirect "Pintar"
    private function _redirect_logic()
    {
        $role = $this->session->userdata('role');
        $id_user = $this->session->userdata('id');

        if ($role == 'mahasiswa') {
            // Ambil data detail mahasiswa terbaru
            $mhs = $this->db->get_where('data_mahasiswa', ['id' => $id_user])->row_array();

            // --- LOGIKA CEK KELENGKAPAN DATA MAHASISWA ---
            // Cek apakah data penting masih kosong?
            // Misal: Alamat, Telepon, Jenis Kelamin, TTD
            if (empty($mhs['alamat']) || empty($mhs['telepon']) || empty($mhs['jenis_kelamin']) || empty($mhs['ttd']) || $mhs['ttd'] == 'dummy_ttd.png') {
                $this->session->set_flashdata('pesan_error', '👋 Halo! Silakan lengkapi <b>Biodata</b> dan <b>Tanda Tangan</b> Anda terlebih dahulu.');
                redirect('mahasiswa/biodata');
            } else {
                redirect('dashboard');
            }

        } elseif ($role == 'dosen') {
            // Ambil data detail dosen terbaru
            $dsn = $this->db->get_where('data_dosen', ['id' => $id_user])->row_array();
            $akun = $this->db->get_where('mstr_akun', ['id' => $id_user])->row_array(); // Cek foto di mstr_akun

            // --- LOGIKA CEK KELENGKAPAN DATA DOSEN ---
            // Cek apakah TTD atau Foto Profil masih kosong/default?
            if (empty($dsn['ttd']) || empty($akun['foto']) || $dsn['ttd'] == 'default.png') {
                $this->session->set_flashdata('pesan_error', '👋 Mohon lengkapi <b>Foto Profil</b> dan <b>Tanda Tangan Digital</b> Anda.');
                redirect('dosen/profil');
            } else {
                redirect('dashboard');
            }

        } elseif ($role == 'operator') {
            redirect('dashboard');
        } else {
            // Role tidak dikenal
            $this->session->sess_destroy();
            redirect('auth/login');
        }
    }

    public function logout()
    {
        $this->session->sess_destroy();
        redirect('auth/login');
    }
}