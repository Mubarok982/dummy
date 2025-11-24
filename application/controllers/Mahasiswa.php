<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Mahasiswa extends CI_Controller {

    public function __construct()
    {
        parent::__construct();
        // Cek akses: hanya Mahasiswa yang boleh mengakses controller ini
        if ($this->session->userdata('role') != 'mahasiswa' || !$this->session->userdata('is_login')) {
            redirect('auth/login');
        }
        $this->load->model('M_Data'); 
        $this->load->model('M_Mahasiswa'); 
        $this->load->model('M_Log');
        $this->load->model('M_Dosen'); // Perlu dimuat untuk get_plagiarisme_result di view/controller lain
    }

    public function index()
    {
        redirect('mahasiswa/progres_skripsi');
    }

    // --- Pengajuan Judul Skripsi ---

    public function pengajuan_judul()
    {
        $id_mahasiswa = $this->session->userdata('id');
        $data['title'] = 'Pengajuan Judul Skripsi';
        $data['skripsi'] = $this->M_Mahasiswa->get_skripsi_by_mhs($id_mahasiswa);
        $data['dosen_list'] = $this->M_Data->get_dosen_pembimbing_list();

        $this->load->view('template/header', $data);
        $this->load->view('template/sidebar', $data);
        $this->load->view('mahasiswa/v_pengajuan_judul', $data);
        $this->load->view('template/footer');
    }

    public function submit_judul()
    {
        $id_mahasiswa = $this->session->userdata('id');
        $this->form_validation->set_rules('tema', 'Tema', 'required');
        $this->form_validation->set_rules('judul', 'Judul Skripsi', 'required|trim');
        $this->form_validation->set_rules('pembimbing1', 'Pembimbing 1', 'required');
        $this->form_validation->set_rules('pembimbing2', 'Pembimbing 2', 'required');

        if ($this->form_validation->run() == FALSE) {
            $this->pengajuan_judul();
        } else {
            $data = [
                'id_mahasiswa' => $id_mahasiswa,
                'tema' => $this->input->post('tema'),
                'judul' => $this->input->post('judul'),
                'pembimbing1' => $this->input->post('pembimbing1'),
                'pembimbing2' => $this->input->post('pembimbing2'),
                'tgl_pengajuan_judul' => date('Y-m-d'),
                'skema' => 'Reguler'
            ];

            $is_update = $this->M_Mahasiswa->get_skripsi_by_mhs($id_mahasiswa);
            
            if ($is_update) {
                $this->M_Mahasiswa->update_skripsi_judul($id_mahasiswa, $data);
                $this->session->set_flashdata('pesan_sukses', 'Judul skripsi berhasil diperbarui.');
                $this->M_Log->record('Judul', 'Memperbarui judul skripsi: ' . $data['judul']);
            } else {
                $this->M_Mahasiswa->insert_skripsi($data);
                $this->session->set_flashdata('pesan_sukses', 'Pengajuan judul skripsi berhasil. Menunggu penetapan Operator.');
                $this->M_Log->record('Judul', 'Mengajukan judul skripsi: ' . $data['judul']);
            }

            redirect('mahasiswa/pengajuan_judul');
        }
    }

    // --- Progres Bimbingan Skripsi (Upload BAB) ---

    public function progres_skripsi()
    {
        $id_mahasiswa = $this->session->userdata('id');
        $data['title'] = 'Progres Bimbingan Skripsi';
        $data['skripsi'] = $this->M_Mahasiswa->get_skripsi_by_mhs($id_mahasiswa);

        if ($data['skripsi']) {
            $data['progres'] = $this->M_Mahasiswa->get_progres_by_skripsi($data['skripsi']['id']);
            $data['last_progres'] = end($data['progres']);
            $data['next_bab'] = count($data['progres']) + 1;
        }

        $this->load->view('template/header', $data);
        $this->load->view('template/sidebar', $data);
        $this->load->view('mahasiswa/v_progres_skripsi', $data);
        $this->load->view('template/footer');
    }

    public function upload_progres_bab()
    {
        $id_mahasiswa = $this->session->userdata('id');
        $skripsi = $this->M_Mahasiswa->get_skripsi_by_mhs($id_mahasiswa);

        if (!$skripsi) {
            $this->session->set_flashdata('pesan_error', 'Anda belum mengajukan judul skripsi.');
            redirect('mahasiswa/pengajuan_judul');
        }

        $id_skripsi = $skripsi['id'];
        $npm = $this->session->userdata('npm');
        $nama = $this->session->userdata('nama');
        $bab = $this->input->post('bab');

        // Pengecekan Kriteria Lanjut BAB (ACC Penuh 100% dari kedua dosen)
        $progres_list = $this->M_Mahasiswa->get_progres_by_skripsi($id_skripsi);
        $previous_bab = $bab - 1;

        if ($previous_bab > 0) {
            $progres_sebelumnya = array_filter($progres_list, function ($p) use ($previous_bab) {
                return $p['bab'] == $previous_bab;
            });

            $progres_sebelumnya = reset($progres_sebelumnya);

            if (!$progres_sebelumnya || $progres_sebelumnya['progres_dosen1'] != 100 || $progres_sebelumnya['progres_dosen2'] != 100) {
                $this->session->set_flashdata('pesan_error', 'Gagal: Bab sebelumnya (BAB ' . $previous_bab . ') belum disetujui penuh oleh kedua Pembimbing (status 100).');
                redirect('mahasiswa/progres_skripsi');
            }
        }

        // --- Konfigurasi Upload File ---
        $config['upload_path']   = './uploads/progres/';
        $config['allowed_types'] = 'pdf'; 
        $config['max_size']      = 5000;
        $config['file_name']     = 'Progres_' . str_replace(' ', '_', $nama) . '_' . $npm . '_BAB' . $bab . '_' . time();

        if (!is_dir($config['upload_path'])) {
            mkdir($config['upload_path'], 0777, TRUE);
        }

        $this->load->library('upload', $config);

        if (!$this->upload->do_upload('file_progres')) {
            $error = array('error' => $this->upload->display_errors());
            $this->session->set_flashdata('pesan_error', 'Gagal Upload: ' . strip_tags($error['error']));
        } else {
            $file_data = $this->upload->data();

            $progres_data = [
                'npm' => $npm,
                'bab' => $bab,
                'file' => $file_data['file_name'],
                'progres_dosen1' => 0,
                'progres_dosen2' => 0,
                'nilai_dosen1' => 'Menunggu',
                'nilai_dosen2' => 'Menunggu',
                'created_at' => date('Y-m-d H:i:s')
            ];
            
            // --- 1. INSERT PROGRES ---
            $this->M_Mahasiswa->insert_progres($progres_data);
            $id_progres_baru = $this->db->insert_id(); // ID harus diambil setelah insert

            // --- 2. INISIALISASI PLAGIARISME MANUAL ---
            $data_plagiat_awal = [
                'id_progres' => $id_progres_baru,
                'tanggal_cek' => date('Y-m-d'),
                'persentase_kemiripan' => 0.00,
                'status' => 'Menunggu',
                'dokumen_laporan' => NULL
            ];
            $this->db->insert('hasil_plagiarisme', $data_plagiat_awal);
            
            // --- 3. CATAT LOG & PESAN ---
            $this->M_Log->record('Progres', 'Mengunggah file Bab ' . $bab . ' dan menunggu verifikasi plagiat oleh Operator.', $id_progres_baru);
            $this->session->set_flashdata('pesan_sukses', 'Progres BAB ' . $bab . ' berhasil diunggah. Sedang **menunggu verifikasi plagiarisme oleh Operator**.');
        }
        
        // --- FINAL REDIRECT ---
        redirect('mahasiswa/progres_skripsi');
    }

    // --- FITUR BIODATA ---

    public function biodata()
    {
        $id_user = $this->session->userdata('id');
        $data['title'] = 'Biodata Saya';
        
        // Menggunakan M_Data yang sudah ada karena function get_user_by_id sudah lengkap (JOIN tabel)
        $this->load->model('M_Data');
        $data['user'] = $this->M_Data->get_user_by_id($id_user);

        $this->load->view('template/header', $data);
        $this->load->view('template/sidebar', $data);
        $this->load->view('mahasiswa/v_biodata', $data);
        $this->load->view('template/footer');
    }

    public function update_biodata()
    {
        $id_user = $this->session->userdata('id');
        $this->load->model('M_Data');

        // 1. Konfigurasi Upload
        $config['upload_path'] = './uploads/profile/';
        $config['allowed_types'] = 'jpg|jpeg|png';
        $config['max_size'] = 2048; // 2MB
        $config['encrypt_name'] = TRUE;

        // Buat folder jika belum ada
        if (!is_dir($config['upload_path'])) mkdir($config['upload_path'], 0777, true);

        $this->load->library('upload', $config);

        // Data untuk tabel mstr_akun
        $akun_data = [
            'nama' => $this->input->post('nama'),
        ];

        // Cek Upload Foto Profil
        if (!empty($_FILES['foto']['name'])) {
            if ($this->upload->do_upload('foto')) {
                $uploadData = $this->upload->data();
                $akun_data['foto'] = $uploadData['file_name'];
            }
        }

        // Data untuk tabel data_mahasiswa
        $detail_data = [
            'jenis_kelamin' => $this->input->post('jenis_kelamin'),
            'tempat_tgl_lahir' => $this->input->post('tempat_tgl_lahir'),
            'email' => $this->input->post('email'),
            'telepon' => $this->input->post('telepon'), // PENTING UNTUK NOTIFIKASI WA
            'alamat' => $this->input->post('alamat'),
            'nik' => $this->input->post('nik'),
            'nama_ortu_dengan_gelar' => $this->input->post('nama_ortu'),
        ];

        // Cek Upload TTD (Tanda Tangan)
        if (!empty($_FILES['ttd']['name'])) {
            // Ubah path untuk TTD
            $config['upload_path'] = './uploads/ttd/';
            if (!is_dir($config['upload_path'])) mkdir($config['upload_path'], 0777, true);
            
            $this->upload->initialize($config); // Re-initialize config

            if ($this->upload->do_upload('ttd')) {
                $uploadData = $this->upload->data();
                $detail_data['ttd'] = $uploadData['file_name'];
            }
        }

        // Eksekusi Update via M_Data
        if ($this->M_Data->update_user($id_user, $akun_data, 'mahasiswa', $detail_data)) {
            $this->session->set_flashdata('pesan_sukses', 'Biodata berhasil diperbarui!');
            
            // Update Session Nama jika berubah
            $this->session->set_userdata('nama', $akun_data['nama']);
        } else {
            $this->session->set_flashdata('pesan_error', 'Gagal memperbarui biodata.');
        }

        redirect('mahasiswa/biodata');
    }
}