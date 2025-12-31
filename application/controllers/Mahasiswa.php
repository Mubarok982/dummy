<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Mahasiswa extends CI_Controller {

    public function __construct()
    {
        parent::__construct();
        if ($this->session->userdata('role') != 'mahasiswa' || !$this->session->userdata('is_login')) {
            redirect('auth/login');
        }
        $this->load->model('M_Data'); 
        $this->load->model('M_Mahasiswa'); 
        $this->load->model('M_Log');
        $this->load->model('M_Dosen'); 
        $this->load->model('M_Chat'); // Tambahkan M_Chat
    }

    public function index()
    {
        redirect('mahasiswa/bimbingan');
    }

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
                'skema' => 'Reguler',
                'status_acc_kaprodi' => 'menunggu' // SET DEFAULT STATUS
            ];

            $is_update = $this->M_Mahasiswa->get_skripsi_by_mhs($id_mahasiswa);
            
            if ($is_update) {
                $this->M_Mahasiswa->update_skripsi_judul($id_mahasiswa, $data);
                $this->session->set_flashdata('pesan_sukses', 'Judul skripsi berhasil diperbarui. Menunggu persetujuan Kaprodi.');
                $this->M_Log->record('Judul', 'Memperbarui judul skripsi: ' . $data['judul']);
            } else {
                $this->M_Mahasiswa->insert_skripsi($data);
                $this->session->set_flashdata('pesan_sukses', 'Pengajuan judul skripsi berhasil. Menunggu persetujuan Kaprodi.');
                $this->M_Log->record('Judul', 'Mengajukan judul skripsi: ' . $data['judul']);
            }

            redirect('mahasiswa/pengajuan_judul');
        }
    }

   public function bimbingan()
{
    $id_mahasiswa = $this->session->userdata('id');
    $data['title'] = 'Bimbingan Skripsi';
    
    // 1. Ambil data skripsi (Pastikan model sudah melakukan JOIN untuk mendapatkan kolom 'npm')
    $data['skripsi'] = $this->M_Mahasiswa->get_skripsi_by_mhs($id_mahasiswa);

    if (!$data['skripsi']) {
        $this->session->set_flashdata('pesan_error', 'Anda belum mengajukan judul skripsi.');
        redirect('mahasiswa/pengajuan_judul');
    }

    // --- Logika Status ACC Kaprodi & Chat ---
    // Gunakan null coalescing operator (??) untuk fallback ke 'menunggu'
    $data['status_acc'] = $data['skripsi']['status_acc_kaprodi'] ?? 'menunggu';
    
    // Pastikan NPM diambil dari skripsi, jika tidak ada fallback ke session
    $npm_mhs = $data['skripsi']['npm'] ?? $this->session->userdata('npm');
    
    // 2. KOREKSI KRITIS: Berikan default array agar View tidak error Undefined Index
    $recipients = $this->M_Chat->get_valid_chat_recipients_mhs($npm_mhs);
    $data['valid_recipients'] = $recipients ? $recipients : [
        'kaprodi'     => null, 
        'pembimbing1' => null, 
        'pembimbing2' => null
    ];
    // --- End Logika ---

    // 3. Logika Progres (Tambahkan pengecekan isset untuk progres_dosen)
    $progres = $this->M_Mahasiswa->get_progres_by_skripsi($data['skripsi']['id']);
    
    if (empty($progres)) {
        $data['next_bab'] = 1;
        $data['last_progres'] = NULL;
    } else {
        $last = end($progres); 
        $data['last_progres'] = $last;
        
        // Cek apakah progres dosen ada dan bernilai 100
        $p1 = $last['progres_dosen1'] ?? 0;
        $p2 = $last['progres_dosen2'] ?? 0;

        if ($p1 == 100 && $p2 == 100) {
            $data['next_bab'] = $last['bab'] + 1;
        } else {
            $data['next_bab'] = $last['bab'];
        }
    }

    // Ambil riwayat progres menggunakan NPM
    $data['progres_riwayat'] = $this->M_Mahasiswa->get_riwayat_progres($npm_mhs); 
    
    $this->load->view('template/header', $data);
    $this->load->view('template/sidebar', $data);
    $this->load->view('mahasiswa/v_bimbingan', $data);
    $this->load->view('template/footer');
}

    public function riwayat_progres()
    {
        $id_mahasiswa = $this->session->userdata('id');
        $data['title'] = 'Riwayat Progres';
        
        $data['skripsi'] = $this->M_Mahasiswa->get_skripsi_by_mhs($id_mahasiswa);
        
        if (!$data['skripsi']) {
            redirect('mahasiswa/pengajuan_judul');
        }

        $data['progres'] = $this->M_Mahasiswa->get_progres_by_skripsi($data['skripsi']['id']);

        $this->load->view('template/header', $data);
        $this->load->view('template/sidebar', $data);
        $this->load->view('mahasiswa/v_riwayat', $data); 
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

        // Cek apakah sudah di ACC Kaprodi
        if ($skripsi['status_acc_kaprodi'] != 'diterima') {
            $this->session->set_flashdata('pesan_error', 'Gagal Upload: Pengajuan Dosen Pembimbing belum disetujui Kaprodi.');
            redirect('mahasiswa/bimbingan');
        }
        
        $id_skripsi = $skripsi['id'];
        $npm = $this->session->userdata('npm');
        $nama = $this->session->userdata('nama');
        $bab = $this->input->post('bab');

        $progres_list = $this->M_Mahasiswa->get_progres_by_skripsi($id_skripsi);
        $previous_bab = $bab - 1;

        if ($previous_bab > 0) {
            $progres_sebelumnya = array_filter($progres_list, function ($p) use ($previous_bab) {
                return $p['bab'] == $previous_bab;
            });
            $progres_sebelumnya = reset($progres_sebelumnya);

            if (!$progres_sebelumnya || $progres_sebelumnya['progres_dosen1'] != 100 || $progres_sebelumnya['progres_dosen2'] != 100) {
                $this->session->set_flashdata('pesan_error', 'Gagal: Bab sebelumnya (BAB ' . $previous_bab . ') belum disetujui penuh.');
                redirect('mahasiswa/bimbingan');
            }
        }

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
            redirect('mahasiswa/bimbingan');
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
            
            $this->M_Mahasiswa->insert_progres($progres_data);
            $id_progres_baru = $this->db->insert_id();

            $data_plagiat_awal = [
                'id_progres' => $id_progres_baru,
                'tanggal_cek' => date('Y-m-d'),
                'persentase_kemiripan' => 0.00,
                'status' => 'Menunggu',
                'dokumen_laporan' => NULL
            ];
            $this->db->insert('hasil_plagiarisme', $data_plagiat_awal);
            
            $this->M_Log->record('Progres', 'Mengunggah file Bab ' . $bab . ' dan menunggu verifikasi plagiat oleh Operator.', $id_progres_baru);
            $this->session->set_flashdata('pesan_sukses', 'Progres BAB ' . $bab . ' berhasil diunggah. Silakan cek status di menu Riwayat.');
        }
        
        redirect('mahasiswa/riwayat_progres');
    }

    public function upload_draft()
    {
        if (!$this->session->userdata('is_logged_in') || $this->session->userdata('role') != 'mahasiswa') {
            redirect('auth');
        }

        $npm = $this->session->userdata('npm');
        $nama = $this->session->userdata('nama');
        $bab = $this->input->post('bab');

        $config['upload_path']   = './uploads/progres/';
        $config['allowed_types'] = 'pdf';
        $config['max_size']      = 2048;
        $config['file_name']     = 'Progres_' . str_replace(' ', '_', $nama) . '_' . $npm . '_BAB' . $bab . '_' . time();

        $this->load->library('upload', $config);

        if (!$this->upload->do_upload('draft_file')) {
            $error = array('error' => $this->upload->display_errors());
            $this->session->set_flashdata('error_upload', $error['error']);
            redirect('mahasiswa/bimbingan');
        } else {
            $data_upload = $this->upload->data();
            $file_name = $data_upload['file_name'];

            $data_db = array(
                'npm' => $npm,
                'bab' => $bab,
                'file_draft' => $file_name,
                'tgl_upload' => date('Y-m-d H:i:s'),
            );

            $this->M_Mahasiswa->simpan_draft_skripsi($data_db);

            $this->session->set_flashdata('success', 'Draft BAB ' . $bab . ' berhasil diunggah dan siap direview oleh Pembimbing.');
            redirect('mahasiswa/bimbingan');
        }
    }

    public function biodata()
    {
        $id_user = $this->session->userdata('id');
        $data['title'] = 'Biodata Saya';
        
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

        $config['upload_path'] = './uploads/profile/';
        $config['allowed_types'] = 'jpg|jpeg|png';
        $config['max_size'] = 2048;
        $config['encrypt_name'] = TRUE;

        if (!is_dir($config['upload_path'])) mkdir($config['upload_path'], 0777, true);

        $this->load->library('upload', $config);

        $akun_data = [
            'nama' => $this->input->post('nama'),
        ];

        if (!empty($_FILES['foto']['name'])) {
            if ($this->upload->do_upload('foto')) {
                $uploadData = $this->upload->data();
                $akun_data['foto'] = $uploadData['file_name'];
            }
        }

        $detail_data = [
            'jenis_kelamin' => $this->input->post('jenis_kelamin'),
            'tempat_tgl_lahir' => $this->input->post('tempat_tgl_lahir'),
            'email' => $this->input->post('email'),
            'telepon' => $this->input->post('telepon'),
            'alamat' => $this->input->post('alamat'),
            'nik' => $this->input->post('nik'),
            'nama_ortu_dengan_gelar' => $this->input->post('nama_ortu'),
        ];

        if (!empty($_FILES['ttd']['name'])) {
            $config['upload_path'] = './uploads/ttd/';
            if (!is_dir($config['upload_path'])) mkdir($config['upload_path'], 0777, true);
            
            $this->upload->initialize($config);

            if ($this->upload->do_upload('ttd')) {
                $uploadData = $this->upload->data();
                $detail_data['ttd'] = $uploadData['file_name'];
            }
        }

        if ($this->M_Data->update_user($id_user, $akun_data, 'mahasiswa', $detail_data)) {
            $this->session->set_flashdata('pesan_sukses', 'Biodata berhasil diperbarui!');
            $this->session->set_userdata('nama', $akun_data['nama']);
        } else {
            $this->session->set_flashdata('pesan_error', 'Gagal memperbarui biodata.');
        }

        redirect('mahasiswa/biodata');
    }

    public function lihat_file($file) {
    $path = FCPATH . 'uploads/progres/' . $file;
    if (file_exists($path)) {
        header('Content-Type: application/pdf');
        readfile($path);
    } else {
        show_404();
    }
}
}