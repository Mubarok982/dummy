<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Dosen extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        // Cek akses: hanya Dosen yang boleh mengakses controller ini
        if ($this->session->userdata('role') != 'dosen' || !$this->session->userdata('is_login')) {
            redirect('auth/login');
        }
        $this->load->model('M_Dosen');
        $this->load->model('M_Log');
    }

    // --- Menu Utama Dosen: Daftar Mahasiswa Bimbingan ---

    public function bimbingan_list()
    {
        $id_dosen = $this->session->userdata('id');
        $data['title'] = 'Daftar Mahasiswa Bimbingan';
        $data['bimbingan'] = $this->M_Dosen->get_mahasiswa_bimbingan($id_dosen);

        $this->load->view('template/header', $data);
        $this->load->view('template/sidebar', $data);
        $this->load->view('dosen/v_bimbingan_list', $data);
        $this->load->view('template/footer');
    }

    // --- Detail Progres Bimbingan dan Pemberian Nilai ---

    public function progres_detail($id_skripsi)
    {
        $id_dosen = $this->session->userdata('id');
        $data['title'] = 'Detail Progres Bimbingan';
        $data['skripsi'] = $this->M_Dosen->get_skripsi_details($id_skripsi);

        // Pastikan dosen adalah pembimbing untuk skripsi ini
        if ($data['skripsi']['pembimbing1'] != $id_dosen && $data['skripsi']['pembimbing2'] != $id_dosen) {
            $this->session->set_flashdata('pesan_error', 'Anda bukan dosen pembimbing untuk skripsi ini.');
            redirect('dosen/bimbingan_list');
        }

        $data['progres'] = $this->M_Dosen->get_all_progres_skripsi($data['skripsi']['npm']);
        $data['is_p1'] = ($data['skripsi']['pembimbing1'] == $id_dosen);

        $this->load->view('template/header', $data);
        $this->load->view('template/sidebar', $data);
        $this->load->view('dosen/v_progres_detail', $data);
        $this->load->view('template/footer');
    }

    public function submit_koreksi()
    {
        $id_progres = $this->input->post('id_progres');
        $is_p1 = $this->input->post('is_p1');
        $komentar = $this->input->post('komentar');
        $status_progres = $this->input->post('status_progres'); // Nilai: 0, 50, 100
        $id_skripsi = $this->input->post('id_skripsi');

        $plagiat_result = $this->M_Dosen->get_plagiarisme_result($id_progres);

        if (!$plagiat_result || $plagiat_result['status'] == 'Menunggu') {
            // Karena hasil mockup langsung muncul, ini hanya guard jika ada error
            $this->session->set_flashdata('pesan_error', 'Gagal: Hasil cek plagiarisme masih Menunggu. Mohon tunggu laporan selesai.');
            redirect('dosen/progres_detail/' . $id_skripsi);
        }

        if ($plagiat_result['status'] == 'Tolak') {
            // Jika Tolak, Dosen hanya bisa memberi status Revisi (0%)
            $status_progres = 0;
            $komentar .= "\n[Sistem] : Hasil Plagiarisme Ditolak (" . $plagiat_result['persentase_kemiripan'] . "%). Wajib Revisi dan Cek Ulang!";
            $this->M_Log->record('Plagiarisme', 'Otomatis menetapkan status revisi karena persentase plagiat (' . $plagiat_result['persentase_kemiripan'] . '%) melebihi ambang batas.', $id_progres);
        }

        $data = [];
        if ($is_p1) {
            $data['komentar_dosen1'] = $komentar;
            $data['progres_dosen1'] = $status_progres;
            $data['nilai_dosen1'] = ($status_progres == 100) ? 'ACC' : (($status_progres == 50) ? 'ACC Sebagian' : 'Revisi');
        } else {
            $data['komentar_dosen2'] = $komentar;
            $data['progres_dosen2'] = $status_progres;
            $data['nilai_dosen2'] = ($status_progres == 100) ? 'ACC' : (($status_progres == 50) ? 'ACC Sebagian' : 'Revisi');
        }

        if ($this->M_Dosen->update_progres($id_progres, $data)) {
            $this->session->set_flashdata('pesan_sukses', 'Koreksi dan status progres berhasil disimpan!');
            $dosen_label = $is_p1 ? 'P1' : 'P2';
            $status_text = ($status_progres == 100) ? 'ACC Penuh' : (($status_progres == 50) ? 'ACC Sebagian' : 'Revisi');
            $this->M_Log->record('Koreksi', 'Memberikan status **' . $status_text . '** Bab ' . $progres_terkini['bab'] . ' sebagai ' . $dosen_label, $id_progres);

            // Cek jika Bab 3 sudah di ACC penuh oleh kedua pembimbing
            $progres_terkini = $this->M_Dosen->get_progres_by_id($id_progres);
            if ($progres_terkini['bab'] == 3 && $progres_terkini['progres_dosen1'] == 100 && $progres_terkini['progres_dosen2'] == 100) {
                // Memberikan notifikasi bahwa mahasiswa sudah siap Sempro
                $this->session->set_flashdata('pesan_info', 'Mahasiswa siap Seminar Proposal. Segera arahkan Mahasiswa untuk mendaftar di SITA.');
                // Tambahkan kode untuk mengaktifkan tombol ACC Sempro di dashboard
            }
        } else {
            $this->session->set_flashdata('pesan_error', 'Gagal menyimpan koreksi.');
        }

        redirect('dosen/progres_detail/' . $id_skripsi);
    }

    // --- Monitoring (Khusus Kaprodi) ---

    public function monitoring_prodi()
    {
        // Cek hanya Kaprodi yang bisa mengakses
        if ($this->session->userdata('is_kaprodi') != 1) {
            $this->session->set_flashdata('pesan_error', 'Akses ditolak. Fitur ini hanya untuk Kaprodi.');
            redirect('dosen/bimbingan_list');
        }

        $prodi = $this->session->userdata('prodi');
        $data['title'] = 'Monitoring Seluruh Mahasiswa Prodi ' . $prodi;
        $data['mahasiswa_prodi'] = $this->M_Dosen->get_all_mahasiswa_prodi($prodi);

        $this->load->view('template/header', $data);
        $this->load->view('template/sidebar', $data);
        $this->load->view('dosen/v_monitoring_prodi', $data);
        $this->load->view('template/footer');
    }
}
