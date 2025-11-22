<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Operator extends CI_Controller {

    public function __construct()
    {
        parent::__construct();
        // Cek akses: hanya Operator yang boleh mengakses controller ini
        if ($this->session->userdata('role') != 'operator' || !$this->session->userdata('is_login')) {
            redirect('auth/login');
        }
        $this->load->model('M_Data');
        $this->load->model('M_Log');
    }

    // --- Manajemen Akun (CRUD) ---

    public function manajemen_akun()
    {
        $data['title'] = 'Manajemen Akun';
        $data['users'] = $this->M_Data->get_all_users_with_details();

        $this->load->view('template/header', $data);
        $this->load->view('template/sidebar', $data);
        $this->load->view('operator/v_manajemen_akun', $data);
        $this->load->view('template/footer');
    }
    
    // (Fungsi Tambah, Edit, Hapus akan dibuat sesuai kebutuhan di View)
    
    // --- Penugasan Pembimbing ---

    public function penugasan_pembimbing()
    {
        $data['title'] = 'Penugasan Pembimbing Skripsi';
        $data['mahasiswa'] = $this->M_Data->get_all_mahasiswa_skripsi();
        $data['dosen_list'] = $this->M_Data->get_dosen_pembimbing_list();

        $this->load->view('template/header', $data);
        $this->load->view('template/sidebar', $data);
        $this->load->view('operator/v_penugasan_pembimbing', $data);
        $this->load->view('template/footer');
    }
    
    public function assign_pembimbing_aksi()
    {
        $id_skripsi = $this->input->post('id_skripsi');
        $pembimbing1 = $this->input->post('pembimbing1');
        $pembimbing2 = $this->input->post('pembimbing2');
        
        if ($id_skripsi && $pembimbing1 && $pembimbing2) {
            $this->M_Data->assign_pembimbing($id_skripsi, $pembimbing1, $pembimbing2);
            $this->session->set_flashdata('pesan_sukses', 'Penugasan pembimbing berhasil diperbarui.');
            $this->M_Log->record('Penugasan', 'Mengatur Pembimbing Skripsi ID: ' . $id_skripsi, $id_skripsi);
        } else {
             $this->session->set_flashdata('pesan_error', 'Gagal: Data tidak lengkap atau skripsi belum diajukan.');
        }
        redirect('operator/penugasan_pembimbing');
    }

    public function tambah_akun()
    {
        $data['title'] = 'Tambah Akun Baru';
        
        // Aturan validasi (Disini kita biarkan 'password' tidak di-hash)
        $this->form_validation->set_rules('username', 'Username', 'required|is_unique[mstr_akun.username]|trim');
        $this->form_validation->set_rules('password', 'Password', 'required|min_length[3]');
        $this->form_validation->set_rules('nama', 'Nama', 'required');
        $this->form_validation->set_rules('role', 'Role', 'required');

        if ($this->form_validation->run() == FALSE) {
            $this->load->view('template/header', $data);
            $this->load->view('template/sidebar', $data);
            $this->load->view('operator/v_tambah_edit_akun', $data); // Menggunakan view yang sama
            $this->load->view('template/footer');
        } else {
            $role = $this->input->post('role');
            
            // Data mstr_akun
            $akun_data = [
                'username' => $this->input->post('username'),
                'password' => $this->input->post('password'), // Masih plain text
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
                    'status_beasiswa' => 'Tidak Aktif', // Default
                    'status_mahasiswa' => 'Murni',      // Default
                    'ttd' => 'dummy_ttd.png',           // Dummy file
                    'dokumen_identitas' => 'dummy_doc.pdf', // Dummy file
                    'sertifikat_office_puskom' => 'dummy_cert.pdf', // Dummy file
                    'sertifikat_btq_ibadah' => 'dummy_cert.pdf',    // Dummy file
                    'sertifikat_bahasa' => 'dummy_cert.pdf',        // Dummy file
                    'sertifikat_kompetensi_ujian_komprehensif' => 'dummy_cert.pdf', // Dummy file
                    'sertifikat_semaba_ppk_masta' => 'dummy_cert.pdf',           // Dummy file
                    'sertifikat_kkn' => 'dummy_cert.pdf',                       // Dummy file
                ];
            }

            if ($this->M_Data->insert_user($akun_data, $detail_data, $role)) {
                $this->session->set_flashdata('pesan_sukses', 'Akun ' . $role . ' berhasil ditambahkan!');
                $this->M_Log->record('Akun', 'Menambahkan akun baru: ' . $role . ' (' . $akun_data['nama'] . ')');
            } else {
                $this->session->set_flashdata('pesan_error', 'Gagal menambahkan akun. Cek log database.');
            }
            redirect('operator/manajemen_akun');
        }
    }
    
    public function edit_akun($id)
    {
        $data['user'] = $this->M_Data->get_user_by_id($id);
        
        if (!$data['user']) {
            redirect('operator/manajemen_akun'); // Akun tidak ditemukan
        }

        $data['title'] = 'Edit Akun: ' . $data['user']['nama'];
        
        $this->form_validation->set_rules('nama', 'Nama', 'required');
        $this->form_validation->set_rules('role', 'Role', 'required');
        // Validasi password hanya jika diisi
        if ($this->input->post('password')) {
            $this->form_validation->set_rules('password', 'Password', 'min_length[3]');
        }

        if ($this->form_validation->run() == FALSE) {
            $this->load->view('template/header', $data);
            $this->load->view('template/sidebar', $data);
            $this->load->view('operator/v_tambah_edit_akun', $data);
            $this->load->view('template/footer');
        } else {
            $role = $this->input->post('role');
            $akun_data = [
                'nama' => $this->input->post('nama'),
                'role' => $role,
            ];
            
            // Update password hanya jika diisi
            if ($this->input->post('password')) {
                $akun_data['password'] = $this->input->post('password'); // Plain text
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
                    // field lain diabaikan karena fokus di bimbingan
                ];
            }

            if ($this->M_Data->update_user($id, $akun_data, $detail_data, $role)) {
                $this->session->set_flashdata('pesan_sukses', 'Akun ' . $role . ' berhasil diperbarui!');
            } else {
                $this->session->set_flashdata('pesan_error', 'Gagal memperbarui akun.');
            }
            redirect('operator/manajemen_akun');
        }
    }

    public function delete_akun($id)
    {
        if ($this->M_Data->delete_user($id)) {
            $this->session->set_flashdata('pesan_sukses', 'Akun berhasil dihapus!');
        } else {
            $this->session->set_flashdata('pesan_error', 'Gagal menghapus akun.');
        }
        redirect('operator/manajemen_akun');
    }

    // --- Monitoring ---
    // (Akan diimplementasikan di tahap selanjutnya)
    public function monitoring_progres()
    {
        $data['title'] = 'Laporan Monitoring Progres Bimbingan';
        $data['laporan'] = $this->M_Data->get_laporan_progres_semua_mhs();

        $this->load->view('template/header', $data);
        $this->load->view('template/sidebar', $data);
        $this->load->view('operator/v_monitoring_progres', $data);
        $this->load->view('template/footer');
    }

    public function kinerja_dosen()
    {
        $data['title'] = 'Laporan Kinerja Dosen Pembimbing';
        $data['dosen_list'] = $this->M_Data->get_dosen_pembimbing_list(); // Dapatkan semua dosen
        
        // Loop untuk mengambil ringkasan aktivitas per dosen
        foreach ($data['dosen_list'] as $key => $dosen) {
            $data['dosen_list'][$key]['aktivitas'] = $this->M_Log->get_dosen_activity_summary($dosen['id']);
        }

        $this->load->view('template/header', $data);
        $this->load->view('template/sidebar', $data);
        $this->load->view('operator/v_kinerja_dosen', $data);
        $this->load->view('template/footer');
    }

    public function cek_plagiarisme_list()
    {
        $data['title'] = 'Cek Plagiarisme (Input Hasil Manual)';
        $data['plagiat_list'] = $this->M_Data->get_plagiarisme_tasks();

        $this->load->view('template/header', $data);
        $this->load->view('template/sidebar', $data);
        $this->load->view('operator/v_cek_plagiat_list', $data);
        $this->load->view('template/footer');
    }

    public function verifikasi_plagiarisme($id_plagiat, $action)
    {
        $status_baru = ($action == 'acc') ? 'Lulus' : 'Tolak';
        
        $this->M_Data->update_plagiarisme_status($id_plagiat, $status_baru);
        
        // Catat Log
        $this->M_Log->record('Plagiarisme', 'Memverifikasi cek plagiat ID: ' . $id_plagiat . ' dengan status: ' . $status_baru, $id_plagiat);

        $this->session->set_flashdata('pesan_sukses', 'Verifikasi Plagiarisme berhasil diinput: Status ' . $status_baru . '.');
        redirect('operator/cek_plagiarisme_list');
    }
}