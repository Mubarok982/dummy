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
        
        // Ambil data filter dari URL (GET request)
        $angkatan_filter = $this->input->get('angkatan');

        $data['title'] = 'Monitoring Mahasiswa Prodi ' . $prodi;
        
        // Ambil list angkatan untuk dropdown
        $data['list_angkatan'] = $this->M_Dosen->get_list_angkatan($prodi);
        $data['selected_angkatan'] = $angkatan_filter; // Untuk menandai dropdown yang dipilih

        // Ambil data mahasiswa dengan filter
        $data['mahasiswa_prodi'] = $this->M_Dosen->get_all_mahasiswa_prodi($prodi, $angkatan_filter);

        $this->load->view('template/header', $data);
        $this->load->view('template/sidebar', $data);
        $this->load->view('dosen/v_monitoring_prodi', $data);
        $this->load->view('template/footer'); // Pastikan footer view sudah dikosongkan isinya seperti request sebelumnya
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
        $this->load->library('upload');

        // Validasi Input Dasar
        $this->form_validation->set_rules('nama', 'Nama Lengkap', 'required|trim');

        if ($this->form_validation->run() == FALSE) {
            $this->session->set_flashdata('pesan_error', 'Nama tidak boleh kosong.');
            redirect('dosen/profil');
        }
        $akun_data = [
            'nama' => $this->input->post('nama', true)
        ];
        $detail_data = [];
        if (!empty($_FILES['foto']['name'])) {
            // Reset config upload
            $this->upload->initialize(array(), TRUE);

            $config_foto['upload_path']   = './uploads/profile/';
            $config_foto['allowed_types'] = 'jpg|jpeg|png|webp';
            $config_foto['max_size']      = 5120; // 5MB
            $config_foto['file_name']     = 'dosen_profile_' . $id_user . '_' . time();
            $config_foto['overwrite']     = true;

            // Buat folder jika belum ada
            if (!is_dir($config_foto['upload_path'])) mkdir($config_foto['upload_path'], 0777, true);

            $this->upload->initialize($config_foto);

            if ($this->upload->do_upload('foto')) {
                // Hapus foto lama
                $old_data = $this->db->get_where('mstr_akun', ['id' => $id_user])->row_array();
                if ($old_data && !empty($old_data['foto']) && file_exists(FCPATH . 'uploads/profile/' . $old_data['foto'])) {
                    unlink(FCPATH . 'uploads/profile/' . $old_data['foto']);
                }

                $akun_data['foto'] = $this->upload->data('file_name');
                $this->session->set_userdata('foto', $akun_data['foto']);
            } else {
                $this->session->set_flashdata('pesan_error', 'Upload Foto Gagal: ' . $this->upload->display_errors('', ''));
                redirect('dosen/profil');
                return;
            }
        }

        // 2. PROSES TTD DIGITAL (Dari Canvas Base64)
        $ttd_base64 = $this->input->post('ttd_base64');
        
        if (!empty($ttd_base64)) {
            // Format string: "data:image/png;base64,iVBORw0KGgoAAA..."
            $image_parts = explode(";base64,", $ttd_base64);
            
            if (count($image_parts) == 2) {
                $image_base64 = base64_decode($image_parts[1]);
                
                // Siapkan folder
                $path_ttd = './uploads/ttd/';
                if (!is_dir($path_ttd)) mkdir($path_ttd, 0777, true);

                // Buat nama file unik
                $file_name = 'ttd_dosen_' . $id_user . '_' . time() . '.png';
                $file_path = FCPATH . 'uploads/ttd/' . $file_name;

                // Simpan file
                if (file_put_contents($file_path, $image_base64)) {
                    // Hapus TTD lama jika ada
                    $old_detail = $this->db->get_where('data_dosen', ['id' => $id_user])->row_array();
                    if ($old_detail && !empty($old_detail['ttd']) && file_exists(FCPATH . 'uploads/ttd/' . $old_detail['ttd'])) {
                        unlink(FCPATH . 'uploads/ttd/' . $old_detail['ttd']);
                    }

                    // Masukkan ke array update detail
                    $detail_data['ttd'] = $file_name;
                } else {
                    $this->session->set_flashdata('pesan_error', 'Gagal menyimpan file Tanda Tangan.');
                    redirect('dosen/profil');
                    return;
                }
            }
        }

        // 3. EKSEKUSI UPDATE KE DATABASE
        // Parameter ke-3 'dosen' memberitahu model untuk update tabel data_dosen
        if ($this->M_Data->update_user($id_user, $akun_data, 'dosen', $detail_data)) {
            $this->session->set_flashdata('pesan_sukses', 'Profil berhasil diperbarui!');
            // Update nama di session jika berubah
            if(isset($akun_data['nama'])) {
                $this->session->set_userdata('nama', $akun_data['nama']);
            }
        } else {
            $this->session->set_flashdata('pesan_error', 'Gagal memperbarui profil di database.');
        }

        redirect('dosen/profil');
    }
    // --- MENU KAPRODI: Kinerja Dosen ---
    public function kinerja_dosen()
    {
        // 1. Security Check: Hanya Kaprodi
        if ($this->session->userdata('is_kaprodi') != 1) {
            redirect('dosen/bimbingan_list');
        }

        $data['title'] = 'Kinerja Dosen Prodi';
        $this->load->library('pagination');
        $this->load->model('M_Data'); // Load M_Data jika belum ada di construct

        // 2. Ambil Data Session & Filter
        $prodi_kaprodi = $this->session->userdata('prodi');
        $keyword = $this->input->get('keyword');

        // 3. Konfigurasi Pagination
        $config['base_url'] = base_url('dosen/kinerja_dosen');
        $config['total_rows'] = $this->M_Dosen->count_dosen_by_prodi($prodi_kaprodi, $keyword);
        $config['per_page'] = 10;
        
        $config['reuse_query_string'] = TRUE;
        $config['page_query_string'] = TRUE;
        $config['query_string_segment'] = 'page';

        // Styling Pagination
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

        $this->pagination->initialize($config);

        // 4. Ambil Data Dosen (Filtered by Prodi)
        $page = $this->input->get('page') ? $this->input->get('page') : 0;
        $data['dosen_list'] = $this->M_Dosen->get_dosen_by_prodi($prodi_kaprodi, $keyword, $config['per_page'], $page);
        
        // 5. Hitung Aktivitas (Sama seperti Operator)
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
        $this->load->view('dosen/v_kinerja_dosen_prodi', $data); // View Baru
        $this->load->view('template/footer');
    }

public function setuju_judul($id_skripsi)
{
    if ($this->session->userdata('is_kaprodi') != 1) redirect('auth/login');
    
    if ($this->M_Dosen->update_status_judul($id_skripsi, 'diterima')) {
        $this->session->set_flashdata('pesan_sukses', 'Judul dan Pembimbing berhasil disetujui.');
    }
    redirect('dosen/monitoring_prodi');
}

public function tolak_judul($id_skripsi)
{
    if ($this->session->userdata('is_kaprodi') != 1) redirect('auth/login');

    if ($this->M_Dosen->update_status_judul($id_skripsi, 'ditolak')) {
        $this->session->set_flashdata('pesan_error', 'Judul dan Pembimbing ditolak.');
    }
    redirect('dosen/monitoring_prodi');
}


}