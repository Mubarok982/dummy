<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Dosen extends CI_Controller
{
public function __construct()
    {
        parent::__construct();
        // 1. Cek Login & Role
        if ($this->session->userdata('role') != 'dosen' || !$this->session->userdata('is_login')) {
            redirect('auth/login');
        }

        $this->load->model(['M_Data', 'M_Dosen', 'M_Log']);
        $this->load->model('operator/M_akun_opt');

        // 2. LOGIKA FORCE REDIRECT DOSEN
        $id_user = $this->session->userdata('id');
        
        // Ambil data detail dosen
        $detail = $this->db->get_where('data_dosen', ['id' => $id_user])->row_array();

        // Halaman yang diizinkan: profil, update_profil, dan logout
        $allowed_methods = ['profil', 'update_profil', 'logout'];
        $current_method = $this->router->method;

        // Cek jika NIDK atau Prodi kosong
        if ((empty($detail['nidk']) || empty($detail['prodi'])) && !in_array($current_method, $allowed_methods)) {
            $this->session->set_flashdata('pesan_error', '⚠️ Mohon lengkapi <b>NIDK</b> dan <b>Program Studi</b> Anda di Profil terlebih dahulu.');
            redirect('dosen/profil');
        }
    }
    // --- Menu Utama Dosen: Daftar Mahasiswa Bimbingan ---

    public function bimbingan_list()
    {
        $id_dosen = $this->session->userdata('id');
        $data['title'] = 'Daftar Mahasiswa Bimbingan';

        // Get filter parameters
        $keyword = $this->input->get('keyword');
        $prodi = $this->input->get('prodi');
        $angkatan = $this->input->get('angkatan');
        $sort_by = $this->input->get('sort_by') ?: 'nama_mhs';
        $sort_order = $this->input->get('sort_order') ?: 'asc';

        // Get all data first
        $all_data = $this->M_Dosen->get_mahasiswa_bimbingan($id_dosen);

        // Apply filters
        $filtered_data = [];
        foreach ($all_data as $item) {
            $match = true;

            // Keyword search (nama_mhs, npm, judul)
            if ($keyword) {
                $search_text = strtolower($item['nama_mhs'] . ' ' . $item['npm'] . ' ' . ($item['judul'] ?? ''));
                if (strpos($search_text, strtolower($keyword)) === false) {
                    $match = false;
                }
            }

            // Prodi filter (assuming prodi is in the data)
            if ($prodi && $prodi != 'all') {
                if (($item['prodi'] ?? '') != $prodi) {
                    $match = false;
                }
            }

            // Angkatan filter
            if ($angkatan && $angkatan != 'all') {
                if (($item['angkatan'] ?? '') != $angkatan) {
                    $match = false;
                }
            }

            if ($match) {
                $filtered_data[] = $item;
            }
        }

        // Apply sorting
        usort($filtered_data, function($a, $b) use ($sort_by, $sort_order) {
            $val_a = strtolower($a[$sort_by] ?? '');
            $val_b = strtolower($b[$sort_by] ?? '');
            if ($sort_order == 'desc') {
                return $val_b <=> $val_a;
            } else {
                return $val_a <=> $val_b;
            }
        });

        $data['bimbingan'] = $filtered_data;
        $data['keyword'] = $keyword;
        $data['prodi'] = $prodi;
        $data['angkatan'] = $angkatan;
        $data['sort_by'] = $sort_by;
        $data['sort_order'] = $sort_order;

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
        $is_p1      = $this->input->post('is_p1'); // 1 atau 0
        $komentar   = $this->input->post('komentar');
        $status_progres = $this->input->post('status_progres'); // 0, 50, 100
        $id_skripsi = $this->input->post('id_skripsi');

        // 1. AMBIL DATA PLAGIASI DARI DATABASE
        // Pastikan Model mengambil kolom 'status_plagiasi' dan 'persentase_kemiripan'
        $cek_plagiat = $this->M_Dosen->get_plagiarisme_result($id_progres);

        // 2. CEK LOGIKA PLAGIASI (Khusus Bab 1 atau yang ada datanya)
        // Jika data plagiasi ditemukan (artinya ini Bab 1)
        if ($cek_plagiat) {
            
            // A. Jika Admin Belum Verifikasi (Masih 'Menunggu')
            if ($cek_plagiat['status_plagiasi'] == 'Menunggu') {
                $this->session->set_flashdata('pesan_error', 'Gagal: Admin belum memverifikasi hasil Cek Plagiarisme. Harap tunggu admin.');
                redirect('dosen/progres_detail/' . $id_skripsi);
                return; // Stop eksekusi
            }

            // B. Jika Admin Menolak (Status 'Tolak')
            // Maka Dosen dipaksa memberikan Revisi (Nilai 0), apapun inputannya
            if ($cek_plagiat['status_plagiasi'] == 'Tolak') {
                $status_progres = 0; // Paksa Revisi
                $persen = $cek_plagiat['persentase_kemiripan'];
                
                // Tambahkan pesan sistem ke komentar
                $komentar .= "\n\n[SYSTEM]: Progres ini DITOLAK otomatis karena Tingkat Plagiarisme tinggi ($persen%). Silakan revisi dan upload ulang.";
            }
        }

        // 3. SIAPKAN DATA UPDATE
        $data = [];
        // Tentukan Nilai Text (Revisi/ACC Sebagian/ACC)
        $nilai_text = ($status_progres == 100) ? 'ACC' : (($status_progres == 50) ? 'ACC Sebagian' : 'Revisi');

        if ($is_p1) {
            $data['komentar_dosen1'] = $komentar;
            $data['progres_dosen1']  = $status_progres;
            $data['nilai_dosen1']    = $nilai_text;
            $data['tgl_koreksi_d1']  = date('Y-m-d H:i:s'); // Catat waktu koreksi
        } else {
            $data['komentar_dosen2'] = $komentar;
            $data['progres_dosen2']  = $status_progres;
            $data['nilai_dosen2']    = $nilai_text;
            $data['tgl_koreksi_d2']  = date('Y-m-d H:i:s');
        }

        // 4. EKSEKUSI UPDATE
        if ($this->M_Dosen->update_progres($id_progres, $data)) {
            
            // Log Aktivitas
            $label_dosen = $is_p1 ? 'Pembimbing 1' : 'Pembimbing 2';
            $this->M_Log->record('Koreksi', "Memberikan nilai $nilai_text ($status_progres) sebagai $label_dosen", $id_progres);

            // --- INTEGRASI FONNTE (Optional / Jika dipakai) ---
            $this->load->helper('fonnte');
            $skripsi_info = $this->M_Dosen->get_skripsi_details($id_skripsi);
            
            if (!empty($skripsi_info['telepon'])) {
                $pesan_wa  = "*UPDATE BIMBINGAN SKRIPSI*\n\n";
                $pesan_wa .= "Halo " . $skripsi_info['nama_mhs'] . ",\n";
                $pesan_wa .= "Dosen pembimbing Anda baru saja mengoreksi progres Anda.\n\n";
                $pesan_wa .= "Hasil: *" . strtoupper($nilai_text) . "*\n";
                if(isset($cek_plagiat['status_plagiasi']) && $cek_plagiat['status_plagiasi'] == 'Tolak'){
                    $pesan_wa .= "Status Plagiasi: *DITOLAK ADMIN*\n";
                }
                $pesan_wa .= "Silakan cek website untuk detailnya.";
                
                kirim_wa_fonnte($skripsi_info['telepon'], $pesan_wa);
            }
            // --------------------------------------------------

            $this->session->set_flashdata('pesan_sukses', 'Koreksi berhasil disimpan.');
        } else {
            $this->session->set_flashdata('pesan_error', 'Terjadi kesalahan database saat menyimpan.');
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

        $data['list_dosen'] = $this->M_Data->get_dosen_pembimbing_list(); 
    }

    public function update_pembimbing()
    {
        // Hanya Kaprodi yang boleh akses
        if ($this->session->userdata('is_kaprodi') != 1) {
            redirect('dosen/monitoring_prodi');
        }

        $id_skripsi = $this->input->post('id_skripsi');
        $p1 = $this->input->post('pembimbing1');
        $p2 = $this->input->post('pembimbing2');

        if ($id_skripsi && $p1 && $p2) {
            
            // Validasi: P1 dan P2 tidak boleh sama
            if ($p1 == $p2) {
                $this->session->set_flashdata('pesan_error', 'Gagal: Pembimbing 1 dan 2 tidak boleh sama.');
                redirect('dosen/monitoring_prodi');
            }

            $data_update = [
                'pembimbing1' => $p1,
                'pembimbing2' => $p2
            ];

            // Update ke database
            $this->db->where('id', $id_skripsi);
            $this->db->update('skripsi', $data_update);

            $this->session->set_flashdata('pesan_sukses', 'Pembimbing berhasil diperbarui.');
        } else {
            $this->session->set_flashdata('pesan_error', 'Data tidak lengkap.');
        }

        redirect('dosen/monitoring_prodi');
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
        $this->form_validation->set_rules('telepon', 'Nomor Telepon', 'trim|numeric'); // Tambah validasi telepon

        if ($this->form_validation->run() == FALSE) {
            $this->session->set_flashdata('pesan_error', 'Gagal: ' . validation_errors());
            redirect('dosen/profil');
        }

        // 1. Data Akun (Tabel mstr_akun)
        $akun_data = [
            'nama' => $this->input->post('nama', true)
        ];

        // 2. Data Detail Dosen (Tabel data_dosen)
        // Masukkan 'telepon' ke sini karena kolom 'telepon' ada di tabel data_dosen
        $detail_data = [
            'telepon' => $this->input->post('telepon', true)
        ];

        // 3. PROSES UPLOAD FOTO PROFIL
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

        // 4. PROSES TTD DIGITAL (Dari Canvas Base64)
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

        // 5. EKSEKUSI UPDATE KE DATABASE
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

        $data['title'] = 'Kinerja Dosen';
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

public function kinerja_dosen_csv()
{
    // 1. Security Check: Hanya Kaprodi
    if ($this->session->userdata('is_kaprodi') != 1) {
        redirect('dosen/bimbingan_list');
    }

    $keyword = $this->input->get('keyword');
    $prodi_kaprodi = $this->session->userdata('prodi');

    $dosen_list = $this->M_Dosen->get_dosen_by_prodi($prodi_kaprodi, $keyword, NULL, NULL);

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

    public function edit_dospem($id_skripsi)
    {
        // Hanya Kaprodi yang boleh akses
        if ($this->session->userdata('is_kaprodi') != 1) {
            $this->session->set_flashdata('pesan_error', 'Akses ditolak. Fitur ini hanya untuk Kaprodi.');
            redirect('dosen/monitoring_prodi');
        }

        $data['title'] = 'Edit Dosen Pembimbing';
        $data['skripsi'] = $this->M_Data->get_skripsi_by_id($id_skripsi);
        $data['dosen_list'] = $this->M_Data->get_dosen_pembimbing_list();

        if (!$data['skripsi']) {
            $this->session->set_flashdata('pesan_error', 'Data skripsi tidak ditemukan!');
            redirect('dosen/monitoring_prodi');
        }

        $this->load->view('template/header', $data);
        $this->load->view('template/sidebar', $data);
        $this->load->view('dosen/v_edit_dospem', $data);
        $this->load->view('template/footer');
    }

    public function update_dospem()
    {
        // Hanya Kaprodi yang boleh akses
        if ($this->session->userdata('is_kaprodi') != 1) {
            $this->session->set_flashdata('pesan_error', 'Akses ditolak. Fitur ini hanya untuk Kaprodi.');
            redirect('dosen/monitoring_prodi');
        }

        $id_skripsi = $this->input->post('id_skripsi');
        $pembimbing1 = $this->input->post('pembimbing1');
        $pembimbing2 = $this->input->post('pembimbing2');

        if ($id_skripsi && $pembimbing1 && $pembimbing2) {
            // Validasi: P1 dan P2 tidak boleh sama
            if ($pembimbing1 == $pembimbing2) {
                $this->session->set_flashdata('pesan_error', 'Gagal: Pembimbing 1 dan 2 tidak boleh sama.');
                redirect('dosen/edit_dospem/' . $id_skripsi);
            }

            $this->M_Data->assign_pembimbing($id_skripsi, $pembimbing1, $pembimbing2);
            $this->session->set_flashdata('pesan_sukses', 'Dosen Pembimbing berhasil diperbarui!');
            $this->M_Log->record('Edit Dospem', 'Kaprodi mengubah dosen pembimbing skripsi ID: ' . $id_skripsi);
        } else {
            $this->session->set_flashdata('pesan_error', 'Gagal memperbarui dosen pembimbing. Data tidak lengkap.');
        }
        redirect('dosen/monitoring_prodi');
    }

    public function get_semester_report($id_dosen)
    {
        // 1. Security Check: Hanya Kaprodi
        if ($this->session->userdata('is_kaprodi') != 1) {
            echo '<div class="text-center py-4 text-danger"><i class="fas fa-exclamation-triangle fa-2x"></i><p class="mt-2">Akses ditolak.</p></div>';
            return;
        }

        $semester = $this->input->get('semester');
        $prodi = $this->input->get('prodi');

        // Ambil data dosen
        $dosen = $this->db->select('a.nama, d.nidk')
                         ->from('mstr_akun a')
                         ->join('data_dosen d', 'a.id = d.id')
                         ->where('a.id', $id_dosen)
                         ->get()
                         ->row_array();

        if (!$dosen) {
            echo '<div class="text-center py-4 text-danger"><i class="fas fa-exclamation-triangle fa-2x"></i><p class="mt-2">Data dosen tidak ditemukan.</p></div>';
            return;
        }

        // Ambil aktivitas berdasarkan semester dan prodi
        $aktivitas = $this->M_Log->get_dosen_activity_by_semester($id_dosen, $semester, $prodi);

        // Hitung total
        $total_aktivitas = 0;
        foreach ($aktivitas as $act) {
            $total_aktivitas += $act['total_aksi'];
        }

        // Tampilkan laporan
        echo '<div class="row">';
        echo '<div class="col-md-6">';
        echo '<h6 class="font-weight-bold text-info">Informasi Dosen</h6>';
        echo '<p class="mb-1"><strong>Nama:</strong> ' . $dosen['nama'] . '</p>';
        echo '<p class="mb-1"><strong>NIDK:</strong> ' . $dosen['nidk'] . '</p>';
        echo '<p class="mb-1"><strong>Semester:</strong> ' . ($semester ?: 'Semua') . '</p>';
        echo '<p class="mb-1"><strong>Prodi:</strong> ' . ($prodi ?: 'Semua') . '</p>';
        echo '</div>';
        echo '<div class="col-md-6">';
        echo '<h6 class="font-weight-bold text-success">Ringkasan Aktivitas</h6>';
        echo '<p class="mb-1"><strong>Total Koreksi:</strong> <span class="badge badge-success">' . $total_aktivitas . ' kali</span></p>';
        echo '<p class="mb-1"><strong>Jumlah Hari Aktif:</strong> <span class="badge badge-info">' . count($aktivitas) . ' hari</span></p>';
        echo '</div>';
        echo '</div>';

        if (!empty($aktivitas)) {
            echo '<div class="mt-4">';
            echo '<h6 class="font-weight-bold text-primary">Detail Aktivitas per Hari</h6>';
            echo '<div class="table-responsive">';
            echo '<table class="table table-sm table-striped">';
            echo '<thead class="bg-light"><tr><th>Tanggal</th><th>Jumlah Koreksi</th><th>Detail</th></tr></thead>';
            echo '<tbody>';
            foreach ($aktivitas as $act) {
                echo '<tr>';
                echo '<td>' . date('d M Y', strtotime($act['tanggal'])) . '</td>';
                echo '<td><span class="badge badge-primary">' . $act['total_aksi'] . '</span></td>';
                echo '<td>' . $act['detail'] . '</td>';
                echo '</tr>';
            }
            echo '</tbody></table></div></div>';
        } else {
            echo '<div class="text-center py-4 text-muted"><i class="fas fa-info-circle fa-2x mb-2"></i><br>Tidak ada aktivitas pada periode ini.</div>';
        }
    }

    // --- MENU KAPRODI: Manajemen Akun ---
    public function manajemen_akun()
    {
        // 1. Security Check: Hanya Kaprodi
        if ($this->session->userdata('is_kaprodi') != 1) {
            $this->session->set_flashdata('pesan_error', 'Akses ditolak. Fitur ini hanya untuk Kaprodi.');
            redirect('dosen/bimbingan_list');
        }

        $data['title'] = 'Manajemen Akun Pengguna';
        $this->load->library('pagination');

        $role = $this->input->get('role');
        $prodi = $this->input->get('prodi');
        $keyword = $this->input->get('keyword');

        // Gunakan M_akun_opt agar konsisten
        $config['base_url'] = base_url('dosen/manajemen_akun');
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
        $this->load->view('dosen/v_manajemen_akun', $data);
        $this->load->view('template/footer');
    }

    // --- EDIT AKUN (SESUAI REQUEST) ---
    public function edit_akun($id = null)
    {
        // Security Check: Hanya Kaprodi
        if ($this->session->userdata('is_kaprodi') != 1) {
            $this->session->set_flashdata('pesan_error', 'Akses ditolak. Fitur ini hanya untuk Kaprodi.');
            redirect('dosen/bimbingan_list');
        }

        if ($id == null) {
            $this->session->set_flashdata('pesan_error', 'ID Akun tidak ditemukan!');
            redirect('dosen/manajemen_akun');
        }

        $data['user'] = $this->M_akun_opt->get_user_by_id($id);
        if (!$data['user']) {
            redirect('dosen/manajemen_akun');
        }

        $data['title'] = 'Edit Akun: ' . $data['user']['nama'];

        // Validasi
        $this->form_validation->set_rules('nama', 'Nama', 'required');

        if ($this->input->post('password')) {
            $this->form_validation->set_rules('password', 'Password', 'min_length[3]');
        }

        if ($this->form_validation->run() == FALSE) {
            $this->load->view('template/header', $data);
            $this->load->view('template/sidebar', $data);
            $this->load->view('operator/v_tambah_edit_akun', $data); // Reuse operator's view
            $this->load->view('template/footer');
        } else {
            // Ambil Role
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

            if ($this->M_akun_opt->update_user($id, $akun_data, $role, $detail_data)) {
                $this->session->set_flashdata('pesan_sukses', 'Akun ' . $role . ' berhasil diperbarui!');
            } else {
                $this->session->set_flashdata('pesan_error', 'Gagal memperbarui akun.');
            }

            redirect('dosen/manajemen_akun');
        }
    }

    public function delete_akun($id)
    {
        // Security Check: Hanya Kaprodi
        if ($this->session->userdata('is_kaprodi') != 1) {
            $this->session->set_flashdata('pesan_error', 'Akses ditolak. Fitur ini hanya untuk Kaprodi.');
            redirect('dosen/bimbingan_list');
        }

        if ($this->M_akun_opt->delete_user($id)) {
            $this->session->set_flashdata('pesan_sukses', 'Akun berhasil dihapus!');
        } else {
            $this->session->set_flashdata('pesan_error', 'Gagal menghapus akun.');
        }
        redirect('dosen/manajemen_akun');
    }

}
