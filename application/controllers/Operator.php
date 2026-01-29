<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Operator extends CI_Controller {

    public function __construct()
    {
        parent::__construct();
        if ($this->session->userdata('role') != 'operator' || !$this->session->userdata('is_login')) {
            redirect('auth/login');
        }
        $this->load->model('M_Data'); // Model Umum
        $this->load->model('M_Log');
        $this->load->model('operator/M_Skripsi_Opt', 'MSkripsi');
        
        // LOAD MODEL KHUSUS AKUN (Yang sudah diperbaiki logic-nya)
        $this->load->model('operator/M_akun_opt'); 
    }

    public function manajemen_akun()
    {
        $data['title'] = 'Manajemen Akun';
        $this->load->library('pagination');

        $role = $this->input->get('role');
        $prodi = $this->input->get('prodi');
        $keyword = $this->input->get('keyword');

        // Gunakan M_akun_opt agar konsisten
        $config['base_url'] = base_url('operator/manajemen_akun');
        $config['total_rows'] = $this->M_akun_opt->count_all_users($role, $prodi, $keyword);
        $config['per_page'] = 15;
        
        $config['reuse_query_string'] = TRUE;
        $config['page_query_string'] = TRUE;
        $config['query_string_segment'] = 'page';

        // Styling Pagination
        $config['full_tag_open']    = '<ul class="pagination pagination-sm m-0 float-right">';
        $config['full_tag_close']   = '</ul>';
        $config['first_link']       = '<i class="fas fa-angle-double-left"></i>';
        $config['last_link']        = '<i class="fas fa-angle-double-right"></i>';
        $config['first_tag_open']   = '<li class="page-item">';
        $config['first_tag_close']  = '</li>';
        $config['prev_link']        = '<i class="fas fa-angle-left"></i>';
        $config['prev_tag_open']    = '<li class="page-item">';
        $config['prev_tag_close']   = '</li>';
        $config['next_link']        = '<i class="fas fa-angle-right"></i>';
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
        // Gunakan M_akun_opt
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
                    // Nilai Default
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

            // Gunakan M_akun_opt
            if ($this->M_akun_opt->insert_user($akun_data, $detail_data, $role)) {
                $this->session->set_flashdata('pesan_sukses', 'Akun ' . $role . ' berhasil ditambahkan!');
                $this->M_Log->record('Akun', 'Menambahkan akun baru: ' . $role . ' (' . $akun_data['nama'] . ')');
            } else {
                $this->session->set_flashdata('pesan_error', 'Gagal menambahkan akun. Cek log database.');
            }
            redirect('operator/manajemen_akun');
        }
    }
    
    // Perbaikan: Tambahkan nilai default null agar tidak error ArgumentCountError
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

        // --- LOGIKA BARU: Tangkap Source ---
        // Ambil dari URL (saat load halaman)
        $source = $this->input->get('source'); 
        
        // Kirim ke View agar bisa dijadikan input hidden
        $data['source'] = $source; 
        // -----------------------------------

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
            // Ambil role ASLI dari database
            $role = $data['user']['role']; 
            
            $akun_data = [
                'nama' => $this->input->post('nama'),
            ];
            
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
                $this->session->set_flashdata('pesan_sukses', 'Akun ' . $role . ' berhasil diperbarui!');
            } else {
                $this->session->set_flashdata('pesan_error', 'Gagal memperbarui akun.');
            }

            // --- LOGIKA BARU: Redirect Sesuai Asal ---
            $redirect_source = $this->input->post('redirect_source'); // Ambil dari hidden input form
            
            if ($redirect_source == 'data_mahasiswa') {
                redirect('operator/data_mahasiswa');
            } else {
                redirect('operator/manajemen_akun');
            }
            // -----------------------------------------
        }
    }

    public function delete_akun($id)
    {
        // Gunakan M_akun_opt
        if ($this->M_akun_opt->delete_user($id)) {
            $this->session->set_flashdata('pesan_sukses', 'Akun berhasil dihapus!');
        } else {
            $this->session->set_flashdata('pesan_error', 'Gagal menghapus akun.');
        }
        redirect('operator/manajemen_akun');
    }

    // Fungsi lain tetap sama, tapi pastikan menggunakan model yang konsisten jika mengakses data user
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

    public function monitoring_progres()
    {
        $data['title'] = 'Laporan Monitoring Progres Bimbingan';
        $this->load->library('pagination');

        $prodi = $this->input->get('prodi');
        $keyword = $this->input->get('keyword');

        $config['base_url'] = base_url('operator/monitoring_progres');
        $config['total_rows'] = $this->M_Data->count_laporan_progres($prodi, $keyword);
        $config['per_page'] = 10; 
        
        $config['reuse_query_string'] = TRUE;
        $config['page_query_string'] = TRUE;
        $config['query_string_segment'] = 'page';

        $config['full_tag_open']    = '<ul class="pagination pagination-sm m-0 float-right">';
        $config['full_tag_close']   = '</ul>';
        $config['first_link']       = '<i class="fas fa-angle-double-left"></i>';
        $config['last_link']        = '<i class="fas fa-angle-double-right"></i>';
        $config['first_tag_open']   = '<li class="page-item">';
        $config['first_tag_close']  = '</li>';
        $config['prev_link']        = '<i class="fas fa-angle-left"></i>';
        $config['prev_tag_open']    = '<li class="page-item">';
        $config['prev_tag_close']   = '</li>';
        $config['next_link']        = '<i class="fas fa-angle-right"></i>';
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
        $data['laporan'] = $this->M_Data->get_laporan_progres($prodi, $keyword, $config['per_page'], $page);
        
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
        $data['title'] = 'Laporan Kinerja Dosen Pembimbing';
        $this->load->library('pagination');

        $keyword = $this->input->get('keyword');

        $config['base_url'] = base_url('operator/kinerja_dosen');
        $config['total_rows'] = $this->M_Data->count_dosen_pembimbing($keyword);
        $config['per_page'] = 10;
        
        $config['reuse_query_string'] = TRUE;
        $config['page_query_string'] = TRUE;
        $config['query_string_segment'] = 'page';

        $config['full_tag_open']    = '<ul class="pagination pagination-sm m-0 float-right">';
        $config['full_tag_close']   = '</ul>';
        $config['first_link']       = '<i class="fas fa-angle-double-left"></i>';
        $config['last_link']        = '<i class="fas fa-angle-double-right"></i>';
        $config['first_tag_open']   = '<li class="page-item">';
        $config['first_tag_close']  = '</li>';
        $config['prev_link']        = '<i class="fas fa-angle-left"></i>';
        $config['prev_tag_open']    = '<li class="page-item">';
        $config['prev_tag_close']   = '</li>';
        $config['next_link']        = '<i class="fas fa-angle-right"></i>';
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
        $data['dosen_list'] = $this->M_Data->get_dosen_pembimbing_list($keyword, $config['per_page'], $page);
        
        foreach ($data['dosen_list'] as $key => $dosen) {
            $aktivitas = $this->M_Log->get_dosen_activity_summary($dosen['id']);
            $data['dosen_list'][$key]['aktivitas'] = $aktivitas;
            $total = 0;
            foreach($aktivitas as $act) { $total += $act['total_aksi']; }
            $data['dosen_list'][$key]['total_aksi'] = $total;
        }

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
        $dosen_list = $this->M_Data->get_dosen_pembimbing_list($keyword, NULL, NULL);

        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="Laporan_Kinerja_Dosen_'.date('Y-m-d').'.csv"');

        $output = fopen('php://output', 'w');
        fputcsv($output, array('No', 'Nama Dosen', 'NIDK', 'Total Aktivitas Koreksi'));

        $no = 1;
        foreach ($dosen_list as $dosen) {
            $aktivitas = $this->M_Log->get_dosen_activity_summary($dosen['id']);
            $total = 0;
            foreach($aktivitas as $act) { $total += $act['total_aksi']; }

            fputcsv($output, array(
                $no++, 
                $dosen['nama'], 
                "'".$dosen['nidk'], 
                $total
            ));
        }
        fclose($output);
    }

    public function cek_plagiarisme_list()
    {
        $data['title'] = 'Cek Plagiarisme';
        
        // Panggil fungsi model yang baru (Satu sumber data)
        $data['list_plagiasi'] = $this->M_Data->get_all_plagiarisme_bab_1();

        $this->load->view('template/header', $data);
        $this->load->view('template/sidebar', $data);
        $this->load->view('operator/v_cek_plagiat_list', $data);
        $this->load->view('template/footer');
    }

    public function proses_verifikasi_plagiarisme()
    {
        // ... (Kode proses ini TETAP SAMA seperti sebelumnya, tidak perlu diubah) ...
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
    // --- FITUR BARU: PERSETUJUAN DOSEN PEMBIMBING (KAPRODI FLOW) ---


    public function acc_dospem()
    {
        $data['title'] = 'Persetujuan Dosen Pembimbing';
        
        $data['pengajuan'] = $this->MSkripsi->get_pengajuan_dospem_menunggu();
        
        $this->load->view('template/header', $data);
        $this->load->view('template/sidebar', $data);
        $this->load->view('operator/v_acc_dospem', $data);
        $this->load->view('template/footer');
    }

    public function proses_acc_dospem($id_skripsi, $action)
    {
        if ($action == 'setujui') {
            $status = 'diterima';
            $message = 'Pengajuan Dosen Pembimbing berhasil disetujui. Mahasiswa dapat memulai bimbingan.';
        } elseif ($action == 'tolak') {
            $status = 'ditolak';
            $message = 'Pengajuan Dosen Pembimbing berhasil ditolak. Mahasiswa harus mengajukan ulang.';
        } else {
            $this->session->set_flashdata('pesan_error', 'Aksi tidak valid.');
            redirect('operator/acc_dospem');
        }

        $data_update = [
            'status_acc_kaprodi' => $status,
            'tgl_acc_kaprodi' => date('Y-m-d H:i:s')
        ];

        $this->MSkripsi->update_skripsi($id_skripsi, $data_update);
        
        $this->session->set_flashdata('pesan_sukses', $message);
        redirect('operator/acc_dospem');
    }

    public function data_mahasiswa()
    {
        $data['title'] = 'Data Mahasiswa & Status Skripsi';

        // Panggil fungsi model yang baru dibuat
        $data['mahasiswa'] = $this->M_Data->get_all_mahasiswa_lengkap();

        $this->load->view('template/header', $data);
        $this->load->view('template/sidebar', $data);
        $this->load->view('operator/v_data_mahasiswa_lengkap', $data); // View baru
        $this->load->view('template/footer');
    }

    public function mahasiswa_siap_sempro()
    {
        $data['title'] = 'Mahasiswa Siap Sempro';

        // Panggil model yang baru dibuat
        $data['mahasiswa'] = $this->M_Data->get_mahasiswa_siap_sempro();

        $this->load->view('template/header', $data);
        $this->load->view('template/sidebar', $data);
        $this->load->view('operator/v_mahasiswa_siap_sempro', $data); // View baru
        $this->load->view('template/footer');
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
            // Unset kaprodi lama
            $this->db->where('prodi', $prodi);
            $this->db->update('data_dosen', ['is_kaprodi' => 0]);

            // Set kaprodi baru
            $this->db->where('id', $kaprodi_id);
            $this->db->update('data_dosen', ['is_kaprodi' => 1]);

            $this->session->set_flashdata('pesan_sukses', 'Kaprodi berhasil diperbarui!');
        } else {
            $this->session->set_flashdata('pesan_error', 'Gagal memperbarui kaprodi.');
        }

        redirect('operator/pengaturan_kaprodi');
    }
}
