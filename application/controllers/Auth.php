<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Auth extends CI_Controller {

    public function __construct()
    {
        parent::__construct();
        // Memuat model akun
        $this->load->model('M_Akun');
    }

    public function index()
    {
        // Jika sudah login, redirect ke dashboard
        if ($this->session->userdata('id')) {
            redirect('dashboard');
        }
        $this->login();
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
            $this->login(); // Kembali ke halaman login jika validasi gagal
        } else {
            $username = $this->input->post('username');
            $password = $this->input->post('password');

            $user = $this->M_Akun->cek_login($username);

            if ($user) {
                
                // === KODE YANG DIUBAH UNTUK UJI COBA (PLAIN TEXT PASSWORD) ===
                // Bandingkan input password dengan password database secara langsung
                if ($password == $user['password']) {
                // ==============================================================
                    
                    // Ambil detail lengkap user (termasuk NIDK/NPM/Prodi)
                    $detail = $this->M_Akun->get_user_details($user['id'], $user['role']);

                    // Data yang akan disimpan ke sesi
                    $session_data = array(
                        'id'        => $detail['id'],
                        'username'  => $detail['username'],
                        'nama'      => $detail['nama'],
                        'role'      => $detail['role'],
                        'is_login'  => TRUE
                    );

                    // Tambahkan detail khusus role
                    if ($detail['role'] == 'dosen') {
                        $session_data['nidk'] = $detail['nidk'];
                        $session_data['prodi'] = $detail['prodi'];
                        $session_data['is_kaprodi'] = $detail['is_kaprodi'];
                    } elseif ($detail['role'] == 'mahasiswa') {
                        $session_data['npm'] = $detail['npm'];
                        $session_data['prodi'] = $detail['prodi'];
                    }
                    
                    // Set sesi
                    $this->session->set_userdata($session_data);
                    redirect('dashboard');

                } else {
                    $this->session->set_flashdata('pesan_error', 'Password salah!');
                    redirect('auth/login');
                }
            } else {
                $this->session->set_flashdata('pesan_error', 'Username/NIDN/NPM tidak ditemukan.');
                redirect('auth/login');
            }
        }
    }

    public function logout()
    {
        // Hapus semua data sesi
        $this->session->sess_destroy();
        redirect('auth/login');
    }
}