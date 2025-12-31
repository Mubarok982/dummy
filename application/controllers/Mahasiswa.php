<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Mahasiswa extends CI_Controller {

    public function __construct()
    {
        parent::__construct();
        // Cek login dan role
        if ($this->session->userdata('role') != 'mahasiswa' || !$this->session->userdata('is_login')) {
            redirect('auth/login');
        }
        // Load model yang dibutuhkan
        $this->load->model(['M_Data', 'M_Mahasiswa', 'M_Log', 'M_Dosen', 'M_Chat']);
    }

    public function index()
    {
        redirect('mahasiswa/bimbingan');
    }

    public function pengajuan_judul()
    {
        $id_mahasiswa = $this->session->userdata('id');
        $data['title'] = 'Pengajuan Judul Skripsi';
        // Ambil data skripsi yang sudah di-join dengan NPM di model
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
                'skema' => 'Reguler',
                'status_acc_kaprodi' => 'menunggu'
            ];

            $is_update = $this->M_Mahasiswa->get_skripsi_by_mhs($id_mahasiswa);
            
            if ($is_update) {
                $this->M_Mahasiswa->update_skripsi_judul($id_mahasiswa, $data);
                $this->session->set_flashdata('pesan_sukses', 'Judul skripsi berhasil diperbarui. Menunggu persetujuan Kaprodi.');
            } else {
                $this->M_Mahasiswa->insert_skripsi($data);
                $this->session->set_flashdata('pesan_sukses', 'Pengajuan judul skripsi berhasil. Menunggu persetujuan Kaprodi.');
            }
            
            $this->M_Log->record('Judul', 'Input/Update judul: ' . $data['judul']);
            redirect('mahasiswa/pengajuan_judul');
        }
    }

    public function bimbingan()
    {
        $id_mahasiswa = $this->session->userdata('id');
        $data['title'] = 'Bimbingan Skripsi';
        
        $data['skripsi'] = $this->M_Mahasiswa->get_skripsi_by_mhs($id_mahasiswa);

        if (!$data['skripsi']) {
            $this->session->set_flashdata('pesan_error', 'Anda belum mengajukan judul skripsi.');
            redirect('mahasiswa/pengajuan_judul');
        }

        // Ambil NPM & Status ACC Kaprodi
        $npm_mhs = $data['skripsi']['npm'] ?? $this->session->userdata('npm');
        $data['status_acc'] = $data['skripsi']['status_acc_kaprodi'] ?? 'menunggu';
        
        // Logika Chat: Default NULL agar view tidak error 'Undefined Index'
        $recipients = $this->M_Chat->get_valid_chat_recipients_mhs($npm_mhs);
        $data['valid_recipients'] = $recipients ? $recipients : [
            'kaprodi'     => null, 
            'pembimbing1' => null, 
            'pembimbing2' => null
        ];

        // Logika Penguncian BAB Selanjutnya
        $progres = $this->M_Mahasiswa->get_progres_by_skripsi($data['skripsi']['id']);
        
        if (empty($progres)) {
            $data['next_bab'] = 1;
            $data['last_progres'] = NULL;
        } else {
            $last = end($progres); 
            $data['last_progres'] = $last;
            
            // Cek progres dosen (Wajib 100 untuk lanjut bab)
            $p1 = $last['progres_dosen1'] ?? 0;
            $p2 = $last['progres_dosen2'] ?? 0;

            if ($p1 == 100 && $p2 == 100) {
                $data['next_bab'] = $last['bab'] + 1;
            } else {
                $data['next_bab'] = $last['bab'];
            }
        }

        $data['progres_riwayat'] = $this->M_Mahasiswa->get_riwayat_progres($npm_mhs); 
        
        $this->load->view('template/header', $data);
        $this->load->view('template/sidebar', $data);
        $this->load->view('mahasiswa/v_bimbingan', $data);
        $this->load->view('template/footer');
    }

    public function upload_progres_bab()
    {
        $id_mahasiswa = $this->session->userdata('id');
        $skripsi = $this->M_Mahasiswa->get_skripsi_by_mhs($id_mahasiswa);

        if (!$skripsi || $skripsi['status_acc_kaprodi'] != 'diterima') {
            $this->session->set_flashdata('pesan_error', 'Gagal: Judul belum disetujui Kaprodi.');
            redirect('mahasiswa/bimbingan');
        }
        
        $npm = $this->session->userdata('npm');
        $nama = $this->session->userdata('nama');
        $bab = $this->input->post('bab');

        // Config Upload
        $config['upload_path']   = './uploads/progres/';
        $config['allowed_types'] = 'pdf'; 
        $config['max_size']      = 5120; // 5MB
        $config['file_name']     = 'Progres_' . str_replace(' ', '_', $nama) . '_' . $npm . '_BAB' . $bab . '_' . time();

        $this->load->library('upload', $config);

        if (!$this->upload->do_upload('file_progres')) {
            $this->session->set_flashdata('pesan_error', strip_tags($this->upload->display_errors()));
            redirect('mahasiswa/bimbingan');
        } else {
            $file_data = $this->upload->data();
            $progres_data = [
                'npm' => $npm,
                'bab' => $bab,
                'file' => $file_data['file_name'], // Gunakan kolom 'file' secara konsisten
                'progres_dosen1' => 0,
                'progres_dosen2' => 0,
                'nilai_dosen1' => 'Menunggu',
                'nilai_dosen2' => 'Menunggu',
                'created_at' => date('Y-m-d H:i:s'),
                'tgl_upload' => date('Y-m-d H:i:s')
            ];
            
            $this->M_Mahasiswa->insert_progres($progres_data);
            $id_baru = $this->db->insert_id();

            // Insert placeholder untuk cek plagiat
            $this->db->insert('hasil_plagiarisme', [
                'id_progres' => $id_baru,
                'tanggal_cek' => date('Y-m-d'),
                'status' => 'Menunggu'
            ]);
            
            $this->M_Log->record('Progres', "Unggah BAB $bab", $id_baru);
            $this->session->set_flashdata('pesan_sukses', "BAB $bab Berhasil diunggah.");
            redirect('mahasiswa/bimbingan');
        }
    }

    public function upload_draft()
    {
        $npm = $this->session->userdata('npm');
        $nama = $this->session->userdata('nama');
        $bab = $this->input->post('bab');

        $config['upload_path']   = './uploads/progres/';
        $config['allowed_types'] = 'pdf';
        $config['max_size']      = 2048;
        $config['file_name']     = 'Draft_' . str_replace(' ', '_', $nama) . '_' . $npm . '_BAB' . $bab . '_' . time();

        $this->load->library('upload', $config);

        if (!$this->upload->do_upload('draft_file')) {
            $this->session->set_flashdata('pesan_error', strip_tags($this->upload->display_errors()));
            redirect('mahasiswa/bimbingan');
        } else {
            $file_name = $this->upload->data('file_name');
            $data_db = [
                'npm' => $npm,
                'bab' => $bab,
                'file' => $file_name, // Ganti file_draft ke file agar sinkron dengan tabel riwayat
                'created_at' => date('Y-m-d H:i:s'),
                'tgl_upload' => date('Y-m-d H:i:s'),
            ];

            $this->M_Mahasiswa->simpan_draft_skripsi($data_db);
            $this->session->set_flashdata('pesan_sukses', 'Draft revisi berhasil dikirim.');
            redirect('mahasiswa/bimbingan');
        }
    }

    public function lihat_file($file) 
    {
        $path = FCPATH . 'uploads/progres/' . $file;
        if (file_exists($path)) {
            header('Content-Type: application/pdf');
            readfile($path);
        } else {
            show_404();
        }
    }

    public function biodata()
    {
        $id_user = $this->session->userdata('id');
        $data['title'] = 'Biodata Saya';
        $data['user'] = $this->M_Data->get_user_by_id($id_user);

        $this->load->view('template/header', $data);
        $this->load->view('template/sidebar', $data);
        $this->load->view('mahasiswa/v_biodata', $data);
        $this->load->view('template/footer');
    }
}