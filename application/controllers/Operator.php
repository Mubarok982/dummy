<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Operator extends CI_Controller {

    public function __construct()
    {
        parent::__construct();
        // Cek Login - Izinkan operator atau kaprodi (dosen dengan is_kaprodi=1)
        $role = $this->session->userdata('role');
        $is_kaprodi = $this->session->userdata('is_kaprodi');
        $is_login = $this->session->userdata('is_login');

        if (!$is_login || ($role != 'operator' && !($role == 'dosen' && $is_kaprodi == 1))) {
            redirect('auth/login');
        }

        // Load Semua Model yang Dibutuhkan di Sini
        $this->load->model('M_Data');
        $this->load->model('M_Log');
        $this->load->model('operator/M_skripsi_opt');
        $this->load->model('operator/M_akun_opt');
        $this->load->model('operator/M_laporan_opt'); // Penting untuk fitur Kinerja
    }

    // --- MANAJEMEN AKUN ---
    public function manajemen_akun()
    {
        $data['title'] = 'Manajemen Akun';
        $this->load->library('pagination');

        $role = $this->input->get('role');
        $prodi = $this->input->get('prodi');
        $keyword = $this->input->get('keyword');

        $config['base_url'] = base_url('operator/manajemen_akun');
        $config['total_rows'] = $this->M_akun_opt->count_all_users($role, $prodi, $keyword);
        $config['per_page'] = 15;
        $config['reuse_query_string'] = TRUE;
        $config['page_query_string'] = TRUE;
        $config['query_string_segment'] = 'page';

        // Styling Pagination
        $this->_config_pagination($config); // Panggil fungsi private config
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
    
    public function tambah_akun()
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
            $role = $this->input->post('role');
            
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
                $this->session->set_flashdata('pesan_error', 'Gagal menambahkan akun. Cek log database.');
            }
            redirect('operator/manajemen_akun');
        }
    }

    public function edit_akun($id = null)
    {
        if ($id == null) {
            $this->session->set_flashdata('pesan_error', 'ID Akun tidak ditemukan!');
            redirect('operator/manajemen_akun');
        }

        $data['user'] = $this->M_akun_opt->get_user_by_id($id);
        if (!$data['user']) {
            redirect('operator/manajemen_akun');
        }

        $data['source'] = $this->input->get('source'); 
        $data['title'] = 'Edit Akun: ' . $data['user']['nama'];
        
        $this->form_validation->set_rules('nama', 'Nama', 'required');
        if ($this->input->post('password')) {
            $this->form_validation->set_rules('password', 'Password', 'min_length[3]');
        }

        if ($this->form_validation->run() == FALSE) {
            $this->load->view('template/header', $data);
            $this->load->view('template/sidebar', $data);
            $this->load->view('operator/v_tambah_edit_akun', $data);
            $this->load->view('template/footer');
        } else {
            $role = $data['user']['role']; 
            
            $akun_data = ['nama' => $this->input->post('nama')];
            if ($this->input->post('password')) {
                $akun_data['password'] = password_hash($this->input->post('password'), PASSWORD_DEFAULT);
            }

            $detail_data = [];
            if ($role == 'dosen') {
                $detail_data = [
                    'nidk' => $this->input->post('nidk'),
                    'prodi' => $this->input->post('prodi_dosen'),
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

            $redirect_source = $this->input->post('redirect_source'); 
            if ($redirect_source == 'data_dosen') redirect('operator/data_dosen');
            elseif ($redirect_source == 'data_mahasiswa') redirect('operator/data_mahasiswa');
            else redirect('operator/manajemen_akun');
        }
    }

    public function delete_akun($id)
    {
        if ($this->M_akun_opt->delete_user($id)) {
            $this->session->set_flashdata('pesan_sukses', 'Akun berhasil dihapus!');
        } else {
            $this->session->set_flashdata('pesan_error', 'Gagal menghapus akun.');
        }
        redirect('operator/manajemen_akun');
    }

    // --- DATA DOSEN & MAHASISWA ---
    
    public function data_dosen()
    {
        $prodi   = $this->input->get('prodi');
        $jabatan = $this->input->get('jabatan');
        $keyword = $this->input->get('keyword');

        $this->load->library('pagination');
        $config['base_url'] = base_url('operator/data_dosen');
        $config['total_rows'] = $this->M_akun_opt->count_dosen_filtered($prodi, $jabatan, $keyword);
        $config['per_page'] = 10; 
        $config['reuse_query_string'] = TRUE;
        $config['page_query_string'] = TRUE;
        $config['query_string_segment'] = 'page';

        $this->_config_pagination($config);
        $this->pagination->initialize($config);

        $page = $this->input->get('page') ? $this->input->get('page') : 0;
        
        $data['dosen'] = $this->M_akun_opt->get_dosen_paginated($config['per_page'], $page, $prodi, $jabatan, $keyword);
        $data['pagination'] = $this->pagination->create_links();
        $data['title'] = 'Manajemen Data Dosen';
        $data['total_rows'] = $config['total_rows'];
        $data['start'] = $page;
        $data['f_prodi'] = $prodi;
        $data['f_jabatan'] = $jabatan;
        $data['f_keyword'] = $keyword;

        $this->load->view('template/header', $data);
        $this->load->view('template/sidebar', $data);
        $this->load->view('operator/v_data_dosen', $data);
        $this->load->view('template/footer');
    }

    public function data_mahasiswa()
    {
        $data['title'] = 'Data Mahasiswa & Status Skripsi';
        $data['mahasiswa'] = $this->M_Data->get_all_mahasiswa_lengkap();

        $this->load->view('template/header', $data);
        $this->load->view('template/sidebar', $data);
        $this->load->view('operator/v_data_mahasiswa_lengkap', $data);
        $this->load->view('template/footer');
    }

    public function mahasiswa_siap_sempro()
    {
        $data['title'] = 'Mahasiswa Siap Sempro';
        $data['mahasiswa'] = $this->M_Data->get_mahasiswa_siap_sempro();

        $this->load->view('template/header', $data);
        $this->load->view('template/sidebar', $data);
        $this->load->view('operator/v_mahasiswa_siap_sempro', $data);
        $this->load->view('template/footer');
    }

    public function mahasiswa_siap_pendadaran()
    {
        $data['title'] = 'Mahasiswa Siap Pendadaran';
        $data['mahasiswa'] = $this->M_Data->get_mahasiswa_siap_pendadaran();

        $this->load->view('template/header', $data);
        $this->load->view('template/sidebar', $data);
        $this->load->view('operator/v_mahasiswa_siap_pendadaran', $data);
        $this->load->view('template/footer');
    }

    public function list_revisi()
    {
        $data['title'] = 'Riwayat Progress Mahasiswa';
        $keyword = $this->input->get('keyword');

        if (!$keyword) {
            // Jika belum ada filter, kosongkan tabel
            $data['list_revisi'] = [];
        } else {
            $data['list_revisi'] = $this->M_Data->get_riwayat_progress($keyword);
        }

        $this->load->view('template/header', $data);
        $this->load->view('template/sidebar', $data);
        $this->load->view('operator/v_progres_mahasiswa', $data);
        $this->load->view('template/footer');
    }

    // --- SKRIPSI & BIMBINGAN ---

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
        $p1 = $this->input->post('pembimbing1');
        $p2 = $this->input->post('pembimbing2');
        
        if ($id_skripsi && $p1 && $p2) {
            $this->M_Data->assign_pembimbing($id_skripsi, $p1, $p2);
            $this->session->set_flashdata('pesan_sukses', 'Penugasan berhasil.');
            $this->M_Log->record('Penugasan', 'Set Pembimbing Skripsi ID: ' . $id_skripsi, $id_skripsi);
        } else {
             $this->session->set_flashdata('pesan_error', 'Gagal: Data tidak lengkap.');
        }
        redirect('operator/penugasan_pembimbing');
    }

   public function acc_judul()
    {
        $data['title'] = 'Persetujuan Judul Skripsi';
        $data['mahasiswa'] = $this->M_Data->get_all_mahasiswa_lengkap(); 
        
        // TAMBAHAN: Ambil list dosen untuk dropdown edit
        $data['dosen_list'] = $this->M_Data->get_dosen_pembimbing_list(); 

        $this->load->view('template/header', $data);
        $this->load->view('template/sidebar', $data);
        $this->load->view('operator/v_acc_judul', $data);
        $this->load->view('template/footer');
    }

    public function setuju_judul($id_skripsi)
    {
        $data_update = ['status_acc_kaprodi' => 'diterima'];
        if ($this->M_skripsi_opt->update_skripsi($id_skripsi, $data_update)) {
            $this->session->set_flashdata('pesan_sukses', 'Judul & Pembimbing berhasil di-ACC!');
            $this->M_Log->record('ACC Judul', 'Operator menyetujui judul skripsi ID: ' . $id_skripsi);
        } else {
            $this->session->set_flashdata('pesan_error', 'Gagal menyetujui judul.');
        }
        redirect('operator/acc_judul');
    }

    public function tolak_judul($id_skripsi)
    {
        $data_update = ['status_acc_kaprodi' => 'ditolak'];
        if ($this->M_skripsi_opt->update_skripsi($id_skripsi, $data_update)) {
            $this->session->set_flashdata('pesan_sukses', 'Judul ditolak.');
            $this->M_Log->record('Tolak Judul', 'Operator menolak judul skripsi ID: ' . $id_skripsi);
        } else {
            $this->session->set_flashdata('pesan_error', 'Gagal menolak judul.');
        }
        redirect('operator/acc_judul');
    }



    public function edit_dospem($id_skripsi)
    {
        $data['title'] = 'Edit Dosen Pembimbing';
        $data['skripsi'] = $this->M_Data->get_skripsi_by_id($id_skripsi);
        $data['dosen_list'] = $this->M_Data->get_dosen_pembimbing_list();

        if (!$data['skripsi']) {
            $this->session->set_flashdata('pesan_error', 'Data skripsi tidak ditemukan!');
            redirect('operator/acc_judul');
        }

        $this->load->view('template/header', $data);
        $this->load->view('template/sidebar', $data);
        $this->load->view('operator/v_edit_dospem', $data);
        $this->load->view('template/footer');
    }

    public function update_dospem()
    {
        $id_skripsi = $this->input->post('id_skripsi');
        $pembimbing1 = $this->input->post('pembimbing1');
        $pembimbing2 = $this->input->post('pembimbing2');

        if ($id_skripsi && $pembimbing1 && $pembimbing2) {
            $this->M_Data->assign_pembimbing($id_skripsi, $pembimbing1, $pembimbing2);
            $this->session->set_flashdata('pesan_sukses', 'Dosen Pembimbing berhasil diperbarui!');
            $this->M_Log->record('Edit Dospem', 'Operator mengubah dosen pembimbing skripsi ID: ' . $id_skripsi);
        } else {
            $this->session->set_flashdata('pesan_error', 'Gagal memperbarui dosen pembimbing. Data tidak lengkap.');
        }
        redirect('operator/acc_judul');
    }

    // --- MONITORING & LAPORAN ---

    public function monitoring_progres()
    {
        $data['title'] = 'Monitoring Progres Bimbingan';
        $this->load->library('pagination');

        $prodi = $this->input->get('prodi');
        $keyword = $this->input->get('keyword');

        // Gunakan M_laporan_opt agar konsisten
        $config['base_url'] = base_url('operator/monitoring_progres');
        $config['total_rows'] = $this->M_laporan_opt->count_laporan_progres($prodi, $keyword);
        $config['per_page'] = 10;
        $config['reuse_query_string'] = TRUE;
        $config['page_query_string'] = TRUE;
        $config['query_string_segment'] = 'page';

        $this->_config_pagination($config);
        $this->pagination->initialize($config);

        $page = $this->input->get('page') ? $this->input->get('page') : 0;
        $data['laporan'] = $this->M_laporan_opt->get_laporan_progres($prodi, $keyword, $config['per_page'], $page);

        $data['pagination'] = $this->pagination->create_links();
        $data['total_rows'] = $config['total_rows'];
        $data['start_index'] = $page;

        $this->load->view('template/header', $data);
        $this->load->view('template/sidebar', $data);
        $this->load->view('operator/v_monitoring_progres', $data);
        $this->load->view('template/footer');
    }

    public function kinerja_dosen()
    {
        $data['title'] = 'Laporan Kinerja Dosen';
        $this->load->library('pagination');

        $keyword = $this->input->get('keyword');

        // Config Pagination
        $config['base_url'] = base_url('operator/kinerja_dosen');
        $config['total_rows'] = $this->M_laporan_opt->count_dosen_pembimbing($keyword);
        $config['per_page'] = 10;
        $config['reuse_query_string'] = TRUE;
        $config['page_query_string'] = TRUE;
        $config['query_string_segment'] = 'page';

        $this->_config_pagination($config);
        $this->pagination->initialize($config);

        $page = $this->input->get('page') ? $this->input->get('page') : 0;
        $data['dosen_list'] = $this->M_laporan_opt->get_dosen_pembimbing_list($keyword, $config['per_page'], $page);

        // Hitung total aktivitas
        foreach ($data['dosen_list'] as $key => $dosen) {
            $aktivitas = $this->M_Log->get_dosen_activity_summary($dosen['id']);
            $data['dosen_list'][$key]['aktivitas'] = $aktivitas;
            $total = 0;
            foreach($aktivitas as $act) { $total += $act['total_aksi']; }
            $data['dosen_list'][$key]['total_aksi'] = $total;
        }

        // --- KIRIM DATA UNTUK FILTER ---
        $data['list_prodi'] = $this->M_laporan_opt->get_all_prodi();
        $data['list_semester'] = $this->M_laporan_opt->get_all_semesters(); // <--- BARU
        // -------------------------------

        $data['pagination'] = $this->pagination->create_links();
        $data['total_rows'] = $config['total_rows'];
        $data['start_index'] = $page;
        $data['per_page'] = $config['per_page'];

        $this->load->view('template/header', $data);
        $this->load->view('template/sidebar', $data);
        $this->load->view('operator/v_kinerja_dosen', $data);
        $this->load->view('template/footer');
    }

    public function kinerja_dosen_csv()
    {
        $keyword = $this->input->get('keyword');
        $dosen_list = $this->M_laporan_opt->get_dosen_pembimbing_list($keyword, NULL, NULL);

        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="Laporan_Kinerja_Dosen_'.date('Y-m-d').'.csv"');

        $output = fopen('php://output', 'w');
        fputcsv($output, array('No', 'Nama Dosen', 'NIDK', 'Total Aktivitas Koreksi'));

        $no = 1;
        foreach ($dosen_list as $dosen) {
            $aktivitas = $this->M_Log->get_dosen_activity_summary($dosen['id']);
            $total = 0;
            foreach($aktivitas as $act) { $total += $act['total_aksi']; }

            fputcsv($output, array($no++, $dosen['nama'], "'".$dosen['nidk'], $total));
        }
        fclose($output);
    }

    // --- AJAX HANDLER (Untuk Modal Detail Kinerja) ---
    public function get_detail_kinerja_ajax()
    {
        // 1. Ambil data dari POST
        $id_dosen = $this->input->post('id_dosen');
        $semester_str = $this->input->post('semester'); 
        $prodi = $this->input->post('prodi');

        // 2. Default Semester jika kosong
        if (empty($semester_str)) {
            $semester_str = "2025/2026 Genap"; 
        }

        // 3. Konversi Semester ke Tanggal
        $parts = explode(' ', $semester_str);
        $years = explode('/', $parts[0]); 
        $type = isset($parts[1]) ? strtolower($parts[1]) : 'genap'; 

        $start_year = $years[0];
        $end_year = isset($years[1]) ? $years[1] : $start_year;

        if ($type == 'ganjil') {
            // Ganjil: 1 Sept - 28 Feb
            $start_date = $start_year . '-09-01';
            $end_date = $end_year . '-02-28';
        } else {
            // Genap: 1 Maret - 31 Agust
            $start_date = $end_year . '-03-01';
            $end_date = $end_year . '-08-31';
        }

        // 4. Ambil Data dari Model
        $data = $this->M_laporan_opt->get_detail_kinerja($id_dosen, $start_date, $end_date, $prodi);
        
        // 5. Render HTML untuk Respon AJAX
        if (empty($data['riwayat_aktivitas'])) {
            echo '<div class="alert alert-warning text-center"><i class="fas fa-info-circle"></i> Tidak ada aktivitas bimbingan pada semester/prodi ini.</div>';
        } else {
            // Info Box
            echo '<div class="row mb-3">
                    <div class="col-6">
                        <div class="info-box bg-light shadow-none border">
                            <span class="info-box-icon bg-info"><i class="fas fa-users"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">Mahasiswa Dibimbing</span>
                                <span class="info-box-number">' . $data['total_mhs_bimbingan'] . ' Orang</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="info-box bg-light shadow-none border">
                            <span class="info-box-icon bg-success"><i class="fas fa-check-circle"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">Total Aktivitas (Revisi/ACC)</span>
                                <span class="info-box-number">' . count($data['riwayat_aktivitas']) . ' Kali</span>
                            </div>
                        </div>
                    </div>
                  </div>';

            // Tabel
            echo '<div class="table-responsive">
                    <table class="table table-sm table-bordered table-striped">
                        <thead class="bg-secondary">
                            <tr>
                                <th>Tanggal</th>
                                <th>Mahasiswa</th>
                                <th>Prodi</th>
                                <th>Aktivitas</th>
                            </tr>
                        </thead>
                        <tbody>';
            
            foreach ($data['riwayat_aktivitas'] as $row) {
                $tgl = date('d/m/Y H:i', strtotime($row['created_at']));
                echo '<tr>
                        <td>' . $tgl . '</td>
                        <td>
                            <b>' . $row['nama_mahasiswa'] . '</b><br>
                            <small class="text-muted">' . $row['npm'] . '</small>
                        </td>
                        <td>' . $row['prodi'] . '</td>
                        <td>
                            Memeriksa <b>BAB ' . $row['bab'] . '</b>
                        </td>
                      </tr>';
            }

            echo '</tbody></table></div>';
        }
    }

    // --- CEK PLAGIARISME & LAINNYA ---

    public function cek_plagiarisme_list()
    {
        $data['title'] = 'Cek Plagiarisme';
        $data['list_plagiasi'] = $this->M_Data->get_all_plagiarisme_bab_1();

        $this->load->view('template/header', $data);
        $this->load->view('template/sidebar', $data);
        $this->load->view('operator/v_cek_plagiat_list', $data);
        $this->load->view('template/footer');
    }

    public function proses_verifikasi_plagiarisme()
    {
        $id_plagiat = $this->input->post('id_progres');
        $persentase = $this->input->post('persentase');
        $status     = $this->input->post('status_plagiasi'); 

        if (empty($id_plagiat) || $persentase === '') {
            $this->session->set_flashdata('pesan_error', 'Gagal: Data tidak lengkap.');
            redirect('operator/cek_plagiarisme_list');
        }

        $update_data = [
            'status_plagiasi'      => $status,
            'persentase_kemiripan' => $persentase,
            'tgl_verifikasi'       => date('Y-m-d H:i:s')
        ];

        if ($this->M_Data->update_plagiarisme($id_plagiat, $update_data)) {
            $this->M_Log->record('Plagiarisme', 'Verifikasi ID: '.$id_plagiat.' Status: '.$status, $id_plagiat);
            $this->session->set_flashdata('pesan_sukses', 'Verifikasi berhasil disimpan.');
        } else {
            $this->session->set_flashdata('pesan_error', 'Gagal update database.');
        }
        redirect('operator/cek_plagiarisme_list');
    }

    public function pengaturan_kaprodi()
    {
        $data['title'] = 'Pengaturan Kaprodi';
        // Ambil data prodi
        $this->db->select('DISTINCT(prodi) as prodi');
        $this->db->from('data_dosen');
        $this->db->where('prodi !=', '');
        $data['prodi_list'] = $this->db->get()->result_array();

        // Ambil data dosen per prodi
        $data['dosen_per_prodi'] = [];
        foreach ($data['prodi_list'] as $prodi) {
            $this->db->select('d.id, a.nama, d.nidk, d.is_kaprodi');
            $this->db->from('data_dosen d');
            $this->db->join('mstr_akun a', 'd.id = a.id');
            $this->db->where('d.prodi', $prodi['prodi']);
            $data['dosen_per_prodi'][$prodi['prodi']] = $this->db->get()->result_array();
        }

        $this->load->view('template/header', $data);
        $this->load->view('template/sidebar', $data);
        $this->load->view('operator/v_pengaturan_kaprodi', $data);
        $this->load->view('template/footer');
        
    }

    public function simpan_kaprodi()
    {
        $prodi = $this->input->post('prodi');
        $kaprodi_id = $this->input->post('kaprodi');

        if ($prodi && $kaprodi_id) {
            $this->db->where('prodi', $prodi)->update('data_dosen', ['is_kaprodi' => 0]);
            $this->db->where('id', $kaprodi_id)->update('data_dosen', ['is_kaprodi' => 1]);
            $this->session->set_flashdata('pesan_sukses', 'Kaprodi berhasil diperbarui!');
        } else {
            $this->session->set_flashdata('pesan_error', 'Gagal memperbarui kaprodi.');
        }
        redirect('operator/pengaturan_kaprodi');
    }

    // Helper Private untuk Pagination
    private function _config_pagination(&$config) {
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
    }

    // --- FITUR BARU: UPDATE PEMBIMBING ---
    public function update_pembimbing()
    {
        $id_skripsi = $this->input->post('id_skripsi');
        $pembimbing1 = $this->input->post('pembimbing1');
        $pembimbing2 = $this->input->post('pembimbing2');

        if ($id_skripsi && $pembimbing1 && $pembimbing2) {
            
            // Cek apakah pembimbing sama
            if ($pembimbing1 == $pembimbing2) {
                $this->session->set_flashdata('pesan_error', 'Gagal: Pembimbing 1 dan 2 tidak boleh sama.');
                redirect('operator/acc_judul');
            }

            $data_update = [
                'pembimbing1' => $pembimbing1,
                'pembimbing2' => $pembimbing2
            ];

            // Panggil model untuk update (Gunakan model yang relevan, misal M_skripsi_opt atau M_Data)
            // Asumsi pakai M_Data->update_skripsi_by_id (Buat fungsi ini jika belum ada di Model)
            $this->db->where('id', $id_skripsi);
            $update = $this->db->update('skripsi', $data_update);

            if ($update) {
                $this->session->set_flashdata('pesan_sukses', 'Dosen Pembimbing berhasil diperbarui.');
                $this->M_Log->record('Update Pembimbing', 'Mengubah pembimbing skripsi ID: ' . $id_skripsi);
            } else {
                $this->session->set_flashdata('pesan_error', 'Gagal memperbarui database.');
            }

        } else {
            $this->session->set_flashdata('pesan_error', 'Data tidak lengkap.');
        }

        redirect('operator/acc_judul');
    }
}