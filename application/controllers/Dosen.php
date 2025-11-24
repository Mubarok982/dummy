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
            $this->session->set_flashdata('pesan_error', 'Gagal: Hasil cek plagiarisme masih Menunggu.');
            redirect('dosen/progres_detail/' . $id_skripsi);
        }

        if ($plagiat_result['status'] == 'Tolak') {
            $status_progres = 0;
            $komentar .= "\n[Sistem] : Hasil Plagiarisme Ditolak (" . $plagiat_result['persentase_kemiripan'] . "%). Wajib Revisi!";
            $this->M_Log->record('Plagiarisme', 'Otomatis menetapkan status revisi karena persentase plagiat tinggi.', $id_progres);
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
            
            // Ambil data progres terbaru untuk keperluan Log & WA
            $progres_terkini = $this->M_Dosen->get_progres_by_id($id_progres);
            
            $dosen_label = $is_p1 ? 'P1' : 'P2';
            $status_text = ($status_progres == 100) ? 'ACC Penuh' : (($status_progres == 50) ? 'ACC Sebagian' : 'Revisi');
            
            // Catat Log
            $this->M_Log->record('Koreksi', 'Memberikan status **' . $status_text . '** Bab ' . $progres_terkini['bab'] . ' sebagai ' . $dosen_label, $id_progres);

            // ============================================================
            // INTEGRASI FONNTE: KIRIM NOTIFIKASI WA KE MAHASISWA
            // ============================================================
            
            // Load Helper
            $this->load->helper('fonnte');

            // Ambil data detail skripsi untuk dapat No HP & Nama Mahasiswa
            $skripsi_info = $this->M_Dosen->get_skripsi_details($id_skripsi);
            
            $nomor_hp = isset($skripsi_info['telepon']) ? $skripsi_info['telepon'] : null;
            
            if (!empty($nomor_hp)) {
                $nama_mhs = $skripsi_info['nama_mhs'];
                $nama_dosen = $this->session->userdata('nama');
                $bab = $progres_terkini['bab'];
                $status_pesan = strtoupper($status_text);
                
                // Batasi panjang komentar di WA agar tidak terlalu panjang
                $preview_komentar = (strlen($komentar) > 100) ? substr($komentar, 0, 100) . "..." : $komentar;

                // Format Pesan WA
                $pesan_wa = "*NOTIFIKASI BIMBINGAN SKRIPSI*\n\n";
                $pesan_wa .= "Halo $nama_mhs,\n";
                $pesan_wa .= "Dosen pembimbing Anda ($nama_dosen) baru saja memberikan tanggapan untuk progres *BAB $bab*.\n\n";
                $pesan_wa .= "Status: *$status_pesan*\n";
                $pesan_wa .= "Komentar: _" . $preview_komentar . "_\n\n";
                $pesan_wa .= "Silakan login ke sistem WBS untuk melihat detail revisi lengkap.\n";
                $pesan_wa .= "Terima kasih.";

                // Kirim via Helper
                kirim_wa_fonnte($nomor_hp, $pesan_wa);
            }
            // ============================================================

            // Cek Sempro
            if ($progres_terkini['bab'] == 3 && $progres_terkini['progres_dosen1'] == 100 && $progres_terkini['progres_dosen2'] == 100) {
                $this->session->set_flashdata('pesan_info', 'Mahasiswa siap Seminar Proposal. Segera arahkan Mahasiswa untuk mendaftar di SITA.');
            }

        } else {
            $this->session->set_flashdata('pesan_error', 'Gagal menyimpan koreksi.');
        }

        redirect('dosen/progres_detail/' . $id_skripsi);
    }

    // --- Monitoring (Khusus Kaprodi) ---

    public function monitoring_prodi()
    {
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

    // --- FITUR PROFIL DOSEN ---

    public function profil()
    {
        $id_user = $this->session->userdata('id');
        $data['title'] = 'Profil Dosen';
        
        $this->load->model('M_Data');
        $data['user'] = $this->M_Data->get_user_by_id($id_user);

        $this->load->view('template/header', $data);
        $this->load->view('template/sidebar', $data);
        $this->load->view('dosen/v_profil', $data); // View khusus dosen
        $this->load->view('template/footer');
    }

    public function update_profil()
    {
        $id_user = $this->session->userdata('id');
        $this->load->model('M_Data');

        // Konfigurasi Upload
        $config['upload_path'] = './uploads/profile/';
        $config['allowed_types'] = 'jpg|jpeg|png';
        $config['max_size'] = 2048;
        $config['encrypt_name'] = TRUE;

        if (!is_dir($config['upload_path'])) mkdir($config['upload_path'], 0777, true);
        $this->load->library('upload', $config);

        $akun_data = ['nama' => $this->input->post('nama')];
        
        // Upload Foto
        if (!empty($_FILES['foto']['name'])) {
            if ($this->upload->do_upload('foto')) {
                $uploadData = $this->upload->data();
                $akun_data['foto'] = $uploadData['file_name'];
            }
        }

        // Data Detail Dosen (Tidak banyak yang bisa diubah selain TTD)
        $detail_data = [];

        // Upload TTD
        if (!empty($_FILES['ttd']['name'])) {
            $config['upload_path'] = './uploads/ttd/';
            if (!is_dir($config['upload_path'])) mkdir($config['upload_path'], 0777, true);
            $this->upload->initialize($config);

            if ($this->upload->do_upload('ttd')) {
                $uploadData = $this->upload->data();
                $detail_data['ttd'] = $uploadData['file_name'];
            }
        }

        if ($this->M_Data->update_user($id_user, $akun_data, 'dosen', $detail_data)) {
            $this->session->set_flashdata('pesan_sukses', 'Profil berhasil diperbarui!');
            $this->session->set_userdata('nama', $akun_data['nama']);
        } else {
            $this->session->set_flashdata('pesan_error', 'Gagal memperbarui profil.');
        }

        redirect('dosen/profil');
    }
}