<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Dosen extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        if ($this->session->userdata('role') != 'dosen' || !$this->session->userdata('is_login')) {
            redirect('auth/login');
        }

        $this->load->model(['M_Data', 'M_Dosen', 'M_Log']);
        $this->load->model('operator/M_akun_opt');

        $id_user = $this->session->userdata('id');
        $detail = $this->db->get_where('data_dosen', ['id' => $id_user])->row_array();

        $allowed_methods = ['profil', 'update_profil', 'logout'];
        $current_method = $this->router->method;

        if ((empty($detail['nidk']) || empty($detail['prodi'])) && !in_array($current_method, $allowed_methods)) {
            $this->session->set_flashdata('pesan_error', '⚠️ Mohon lengkapi <b>NIDK</b> dan <b>Program Studi</b> Anda di Profil terlebih dahulu.');
            redirect('dosen/profil');
        }
    }

    public function bimbingan_list()
    {
        $id_dosen = $this->session->userdata('id');
        $data['title'] = 'Daftar Mahasiswa Bimbingan';
        $this->load->library('pagination');
        $this->load->helper('pagination_custom');

        $keyword = $this->input->get('keyword');
        $prodi = $this->input->get('prodi');
        $angkatan = $this->input->get('angkatan');
        $sort_by = $this->input->get('sort_by') ? $this->input->get('sort_by') : 'nama_mhs';
        $sort_order = $this->input->get('sort_order') ? $this->input->get('sort_order') : 'asc';

        $data['keyword'] = $keyword;
        $data['prodi'] = $prodi;
        $data['angkatan'] = $angkatan;
        $data['sort_by'] = $sort_by;
        $data['sort_order'] = $sort_order;

        $all_data = $this->M_Dosen->get_bimbingan_list(
            $id_dosen, 
            $keyword, 
            $prodi, 
            $angkatan, 
            $sort_by, 
            $sort_order
        );

        $total_rows = count($all_data);

        $config['base_url'] = base_url('dosen/bimbingan_list');
        $config['total_rows'] = $total_rows;
        $config['per_page'] = 15;
        $config['reuse_query_string'] = TRUE;
        $config['page_query_string'] = TRUE;
        $config['query_string_segment'] = 'page';

        config_pagination($config);
        $this->pagination->initialize($config);

        $page = $this->input->get('page') ? $this->input->get('page') : 0;
        
        $data['bimbingan'] = array_slice($all_data, $page, $config['per_page']);

        $data['pagination'] = $this->pagination->create_links();
        $data['total_rows'] = $total_rows;
        $data['start_index'] = $page;

        $data['list_prodi'] = $this->M_Dosen->get_list_prodi();
        $data['list_angkatan'] = $this->M_Dosen->get_list_angkatan();

        $this->load->view('template/header', $data);
        $this->load->view('template/sidebar', $data);
        $this->load->view('dosen/v_bimbingan_list', $data);
        $this->load->view('template/footer');
    }

public function progres_detail($id_skripsi)
    {
        $id_dosen = $this->session->userdata('id');
        $data['title'] = 'Detail Progres Bimbingan';
        
        // 1. Ambil Data Skripsi & Validasi Dosen
        $data['skripsi'] = $this->M_Dosen->get_skripsi_details($id_skripsi);

        if (empty($data['skripsi'])) {
            $this->session->set_flashdata('pesan_error', 'Data skripsi tidak ditemukan.');
            redirect('dosen/bimbingan_list');
        }

        if ($data['skripsi']['pembimbing1'] != $id_dosen && $data['skripsi']['pembimbing2'] != $id_dosen) {
            $this->session->set_flashdata('pesan_error', 'Anda bukan dosen pembimbing untuk skripsi ini.');
            redirect('dosen/bimbingan_list');
        }

        $npm = $data['skripsi']['npm'];
        $data['is_p1'] = ($data['skripsi']['pembimbing1'] == $id_dosen);

        // --- TENTUKAN BATAS MAKSIMAL BAB BERDASARKAN PRODI ---
        $prodi = $data['skripsi']['prodi'] ?? '';
        $max_bab = (stripos($prodi, 'D3') !== false || stripos($prodi, 'Diploma 3') !== false) ? 5 : 6;
        $data['max_bab'] = $max_bab;

        // =================================================================
        // 2. QUERY SPESIFIK: AMBIL PROGRES, HILANGKAN DUPLIKAT, KUNCI JUDUL
        // =================================================================
        $this->db->select('p.*, s.judul as judul_skripsi_aktif, s.tema as tema_skripsi_aktif');
        $this->db->from('progres_skripsi p');
        $this->db->join('skripsi s', 'p.id_skripsi = s.id', 'left');
        $this->db->where('p.npm', $npm);
        $this->db->order_by('p.bab', 'ASC'); 
        $this->db->order_by('p.tgl_upload', 'DESC'); // Yang terbaru di atas untuk tiap bab
        $progres_raw = $this->db->get()->result_array();

        $progres_bersih = [];
        $seen_ids = []; 
        $highest_acc_bab = 0;
        $is_revisi = false;

        foreach ($progres_raw as $p) {
            if (!in_array($p['id'], $seen_ids)) {
                $seen_ids[] = $p['id'];

                // KUNCI JUDUL: Gunakan judul_saat_upload jika ada
                $p['judul'] = !empty($p['judul_saat_upload']) ? $p['judul_saat_upload'] : $p['judul_skripsi_aktif'];
                $p['tema']  = !empty($p['tema_saat_upload']) ? $p['tema_saat_upload'] : $p['tema_skripsi_aktif'];

                // LOGIKA ANALISA BAB (Sama persis seperti di controller mahasiswa)
                if ($p['progres_dosen1'] == 100 && $p['progres_dosen2'] == 100) {
                    if ($p['bab'] > $highest_acc_bab) {
                        $highest_acc_bab = $p['bab'];
                    }
                }

                $progres_bersih[] = $p;
            }
        }

        // =================================================================
        // 3. TENTUKAN TARGET BAB & LOGIKA STATUS UI (Untuk tampilan Info Box)
        // =================================================================
        $target_bab = 1;
        if (!empty($progres_bersih)) {
            // Ambil data progres paling akhir (terbaru)
            $last_progres = end($progres_bersih); 
            
            if ($last_progres['progres_dosen1'] < 100 || $last_progres['progres_dosen2'] < 100) {
                $target_bab = $last_progres['bab'];
                $is_revisi = true;
            } else {
                $target_bab = $highest_acc_bab + 1;
                $is_revisi = false;
            }
        }
        
        // Mencegah target melebihi batas prodi
        if ($target_bab > $max_bab) $target_bab = $max_bab;

        // UI Styling persis seperti Mahasiswa
        $status_card = 'card-primary';
        $text_header = 'Sedang Mengerjakan BAB ' . $target_bab;
        $pesan_info = 'Mahasiswa saat ini berada di tahap penulisan normal.';

        if ($is_revisi) {
            $status_card = 'card-warning';
            $text_header = 'Perbaikan / Revisi BAB ' . $target_bab;
            $pesan_info = 'Menunggu mahasiswa mengunggah file revisi terbaru.';
        }

        // Kirim semua variabel matang ke View
        $data['progres'] = $progres_bersih;
        $data['target_bab'] = $target_bab;
        $data['is_revisi'] = $is_revisi;
        $data['status_card'] = $status_card;
        $data['text_header'] = $text_header;
        $data['pesan_info'] = $pesan_info;

        $this->load->view('template/header', $data);
        $this->load->view('template/sidebar', $data);
        $this->load->view('dosen/v_progres_detail', $data);
        $this->load->view('template/footer');
    }

   public function submit_koreksi()
    {
        $id_progres = $this->input->post('id_progres');
        $is_p1      = $this->input->post('is_p1'); 
        $komentar   = $this->input->post('komentar');
        $status_progres = $this->input->post('status_progres'); 
        $id_skripsi = $this->input->post('id_skripsi');

        $cek_plagiat = $this->M_Dosen->get_plagiarisme_result($id_progres);

        if ($cek_plagiat) {
            
            if ($cek_plagiat['status_plagiasi'] == 'Menunggu') {
                $this->session->set_flashdata('pesan_error', 'Gagal: Admin belum memverifikasi hasil Cek Plagiarisme. Harap tunggu admin.');
                redirect('dosen/progres_detail/' . $id_skripsi);
                return; 
            }

            if ($cek_plagiat['status_plagiasi'] == 'Tolak') {
                $status_progres = 0; 
                $persen = $cek_plagiat['persentase_kemiripan'];
                
                $komentar .= "\n\n[SYSTEM]: Progres ini DITOLAK otomatis karena Tingkat Plagiarisme tinggi ($persen%). Silakan revisi dan upload ulang.";
            }
        }

        $data = [];
        $nilai_text = ($status_progres == 100) ? 'ACC' : (($status_progres == 50) ? 'ACC Sebagian' : 'Revisi');

        if ($is_p1) {
            $data['komentar_dosen1'] = $komentar;
            $data['progres_dosen1']  = $status_progres;
            $data['nilai_dosen1']    = $nilai_text;
            $data['tgl_koreksi_d1']  = date('Y-m-d H:i:s'); 
        } else {
            $data['komentar_dosen2'] = $komentar;
            $data['progres_dosen2']  = $status_progres;
            $data['nilai_dosen2']    = $nilai_text;
            $data['tgl_koreksi_d2']  = date('Y-m-d H:i:s');
        }

        if ($this->M_Dosen->update_progres($id_progres, $data)) {
            
            $label_dosen = $is_p1 ? 'Pembimbing 1' : 'Pembimbing 2';
            $this->M_Log->record('Koreksi', "Memberikan nilai $nilai_text ($status_progres) sebagai $label_dosen", $id_progres);

            $this->load->helper('fonnte');
            $skripsi_info = $this->M_Dosen->get_skripsi_details($id_skripsi);
            
            if (!empty($skripsi_info['telepon'])) {
                $pesan_wa  = "🔔 *Update Bimbingan Skripsi*\n\n";
                $pesan_wa .= "Assalamualaikum " . $skripsi_info['nama_mhs'] . ",\n\n";
                $pesan_wa .= "Dosen Pembimbing telah memberikan penilaian terhadap progres Anda.\n\n";
                $pesan_wa .= "📊 *Hasil Penilaian:* " . strtoupper($nilai_text) . "\n";
                if(isset($cek_plagiat['status_plagiasi']) && $cek_plagiat['status_plagiasi'] == 'Tolak'){
                    $pesan_wa .= "⚠️ *Status Plagiarisme:* DITOLAK OLEH ADMIN\n";
                }
                $pesan_wa .= "\nSilakan login ke sistem WBS untuk melihat detail koreksi dan komentar dari Dosen Pembimbing.\n\n";
                $pesan_wa .= "Terima kasih atas partisipasi Anda.\n";
                $pesan_wa .= "Wassalamualaikum Wr. Wb.\n\n";
                $pesan_wa .= "_Pesan otomatis dari Sistem Monitoring Skripsi_";
                
                kirim_wa_fonnte($skripsi_info['telepon'], $pesan_wa);
            }

            $this->session->set_flashdata('pesan_sukses', 'Koreksi berhasil disimpan.');
        } else {
            $this->session->set_flashdata('pesan_error', 'Terjadi kesalahan database saat menyimpan.');
        }

        redirect('dosen/progres_detail/' . $id_skripsi);
    }


public function monitoring_prodi()
    {
        if ($this->session->userdata('is_kaprodi') != 1) {
            $this->session->set_flashdata('pesan_error', 'Akses ditolak. Fitur ini hanya untuk Kaprodi.');
            redirect('dosen/bimbingan_list');
        }
        
        // load helper dan model operator untuk konsistensi data/status
        $this->load->helper('bimbingan');
        $this->load->model('operator/M_laporan_opt');

        $prodi = $this->session->userdata('prodi');
        $keyword = $this->input->get('keyword');
        $sort_by = $this->input->get('sort_by') ?: 'nama';
        $sort_order = $this->input->get('sort_order') ?: 'asc';

        // match operator title for consistency
        $data['title'] = 'Monitoring Progres Bimbingan';

        // 1. Ambil semua data mentah
        $all_data = $this->M_laporan_opt->get_laporan_progres($prodi, $keyword, NULL, NULL);

        // ==========================================================
        // 2. FILTERING & ANTI-DUPLIKAT (Hanya ID Skripsi Terbaru per NPM)
        // ==========================================================
        $temp_mhs = []; // Array penampung sementara (Key = NPM)

        foreach ($all_data as $item) {
            $match = true;

            // Filter Keyword (Nama, NPM, Judul) - Double check
            if ($keyword) {
                $search_text = strtolower(($item['nama'] ?? '') . ' ' . ($item['npm'] ?? '') . ' ' . ($item['judul'] ?? ''));
                if (strpos($search_text, strtolower($keyword)) === false) {
                    $match = false;
                }
            }

            // Filter Prodi (Harusnya sudah difilter model, tapi untuk keamanan ganda)
            if ($prodi && $prodi != 'all') {
                if (($item['prodi'] ?? '') != $prodi) {
                    $match = false;
                }
            }

            // Logika Anti Duplikat
            if ($match) {
                $npm = $item['npm'];
                
                // Jika NPM belum ada di list, masukkan
                if (!isset($temp_mhs[$npm])) {
                    $temp_mhs[$npm] = $item;
                } else {
                    // Jika NPM sudah ada, bandingkan ID Skripsi
                    // Ambil yang ID-nya lebih besar (data lebih baru/revisi judul)
                    $id_sekarang = isset($item['id_skripsi']) ? (int)$item['id_skripsi'] : 0;
                    $id_lama = isset($temp_mhs[$npm]['id_skripsi']) ? (int)$temp_mhs[$npm]['id_skripsi'] : 0;
                    
                    if ($id_sekarang > $id_lama) {
                        $temp_mhs[$npm] = $item;
                    }
                }
            }
        }

        // Kembalikan ke array index biasa (reset key NPM)
        $filtered_data = array_values($temp_mhs);

        // ==========================================================
        // 3. SORTING
        // ==========================================================
        usort($filtered_data, function($a, $b) use ($sort_by, $sort_order) {
            $val_a = strtolower($a[$sort_by] ?? '');
            $val_b = strtolower($b[$sort_by] ?? '');
            
            if ($sort_order == 'desc') {
                return strcmp($val_b, $val_a);
            } else {
                return strcmp($val_a, $val_b);
            }
        });

        // ==========================================================
        // 4. PAGINATION (Dari data yang sudah bersih)
        // ==========================================================
        $total_rows = count($filtered_data);
        
        $config['base_url'] = base_url('dosen/monitoring_prodi');
        $config['total_rows'] = $total_rows;
        $config['per_page'] = 10;
        $config['reuse_query_string'] = TRUE;
        $config['page_query_string'] = TRUE;
        $config['query_string_segment'] = 'page';
        
        // Load helper pagination custom jika ada, atau set manual config bootstrap
        $this->load->helper('pagination_custom');
        if (function_exists('config_pagination')) {
            config_pagination($config);
        } else {
            // Fallback config jika helper tidak terload
            $config['full_tag_open']    = '<ul class="pagination pagination-sm m-0 float-right">';
            $config['full_tag_close']   = '</ul>';
            $config['first_link']       = 'First';
            $config['first_tag_open']   = '<li class="page-item">';
            $config['first_tag_close']  = '</li>';
            $config['last_link']        = 'Last';
            $config['last_tag_open']    = '<li class="page-item">';
            $config['last_tag_close']   = '</li>';
            $config['next_link']        = '&raquo;';
            $config['next_tag_open']    = '<li class="page-item">';
            $config['next_tag_close']   = '</li>';
            $config['prev_link']        = '&laquo;';
            $config['prev_tag_open']    = '<li class="page-item">';
            $config['prev_tag_close']   = '</li>';
            $config['cur_tag_open']     = '<li class="page-item active"><a class="page-link" href="#">';
            $config['cur_tag_close']    = '</a></li>';
            $config['num_tag_open']     = '<li class="page-item">';
            $config['num_tag_close']    = '</li>';
            $config['attributes']       = array('class' => 'page-link');
        }

        $this->load->library('pagination');
        $this->pagination->initialize($config);

        $page = $this->input->get('page') ? $this->input->get('page') : 0;
        
        // Potong data sesuai halaman (Slice)
        $data['laporan'] = array_slice($filtered_data, $page, $config['per_page']);
        
        $data['pagination'] = $this->pagination->create_links();
        $data['total_rows'] = $total_rows;
        $data['start_index'] = $page;

        $data['keyword'] = $keyword;
        $data['prodi'] = $prodi;
        $data['sort_by'] = $sort_by;
        $data['sort_order'] = $sort_order;

        // Data pendukung untuk filter di View
        $data['list_prodi'] = $this->M_Data->get_all_prodi();
        $data['list_angkatan'] = $this->M_Data->get_unique_angkatan();

        // Reuse view Operator (karena strukturnya sama)
        $this->load->view('template/header', $data);
        $this->load->view('template/sidebar', $data);
        $this->load->view('operator/v_monitoring_progres', $data);
        $this->load->view('template/footer');
    }

    public function update_pembimbing()
    {
        if ($this->session->userdata('is_kaprodi') != 1) {
            redirect('dosen/monitoring_prodi');
        }

        $id_skripsi = $this->input->post('id_skripsi');
        $p1 = $this->input->post('pembimbing1');
        $p2 = $this->input->post('pembimbing2');

        if ($id_skripsi && $p1 && $p2) {
            
            if ($p1 == $p2) {
                $this->session->set_flashdata('pesan_error', 'Gagal: Pembimbing 1 dan 2 tidak boleh sama.');
                redirect('dosen/monitoring_prodi');
            }

            $data_update = [
                'pembimbing1' => $p1,
                'pembimbing2' => $p2
            ];

            $this->db->where('id', $id_skripsi);
            $this->db->update('skripsi', $data_update);

            $this->session->set_flashdata('pesan_sukses', 'Pembimbing berhasil diperbarui.');
        } else {
            $this->session->set_flashdata('pesan_error', 'Data tidak lengkap.');
        }

        redirect('dosen/monitoring_prodi');
    }


    public function profil()
    {
        $id_user = $this->session->userdata('id');
        $data['title'] = 'Profil Dosen';
        
        // Lakukan Query JOIN manual agar semua kolom spesifik dosen (telepon & ttd) pasti terbawa
        $this->db->select('
            a.id, 
            a.nama, 
            a.foto, 
            a.role, 
            d.nidk, 
            d.prodi as prodi_dsn, 
            d.telepon, 
            d.ttd, 
            d.is_kaprodi
        ');
        $this->db->from('mstr_akun a');
        $this->db->join('data_dosen d', 'a.id = d.id', 'left');
        $this->db->where('a.id', $id_user);
        $data['user'] = $this->db->get()->row_array();

        $this->load->view('template/header', $data);
        $this->load->view('template/sidebar', $data);
        $this->load->view('dosen/v_profil', $data); 
        $this->load->view('template/footer');
    }

  public function update_profil()
    {
        $id_user = $this->session->userdata('id');
        $this->load->model('M_Data');
        $this->load->library('upload');

        $this->form_validation->set_rules('nama', 'Nama Lengkap', 'required|trim');
        $this->form_validation->set_rules('telepon', 'Nomor Telepon', 'trim|numeric'); 

        if ($this->form_validation->run() == FALSE) {
            $this->session->set_flashdata('pesan_error', 'Gagal: ' . validation_errors());
            redirect('dosen/profil');
        }

        $telepon_input = $this->input->post('telepon', true);

        // =====================================================================
        // 1. ERROR HANDLING ANTI-DUPLIKASI (NO WA DOSEN)
        // =====================================================================
        if (!empty($telepon_input)) {
            // Cek apakah No WA sudah dipakai oleh Dosen LAIN
            $this->db->where('telepon', $telepon_input);
            $this->db->where('id !=', $id_user);
            $cek_wa = $this->db->get('data_dosen')->num_rows();

            if ($cek_wa > 0) {
                $this->session->set_flashdata('pesan_error', '<b>Gagal Update:</b> Nomor WhatsApp ('.$telepon_input.') sudah terdaftar pada akun dosen lain! Silakan gunakan nomor yang berbeda.');
                redirect('dosen/profil');
                return; // Hentikan eksekusi kode di bawahnya
            }
        }
        // =====================================================================

        $akun_data = [
            'nama' => $this->input->post('nama', true)
        ];

        $detail_data = [
            'telepon' => $telepon_input
        ];

        if (!empty($_FILES['foto']['name'])) {
            $this->upload->initialize(array(), TRUE);

            $config_foto['upload_path']   = './uploads/profile/';
            $config_foto['allowed_types'] = 'jpg|jpeg|png|webp';
            $config_foto['max_size']      = 5120; 
            $config_foto['file_name']     = 'dosen_profile_' . $id_user . '_' . time();
            $config_foto['overwrite']     = true;

            if (!is_dir($config_foto['upload_path'])) mkdir($config_foto['upload_path'], 0777, true);

            $this->upload->initialize($config_foto);

            if ($this->upload->do_upload('foto')) {
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

        $ttd_base64 = $this->input->post('ttd_base64');
        
        if (!empty($ttd_base64)) {
            $image_parts = explode(";base64,", $ttd_base64);
            
            if (count($image_parts) == 2) {
                $image_base64 = base64_decode($image_parts[1]);
                
                $path_ttd = './uploads/ttd/';
                if (!is_dir($path_ttd)) mkdir($path_ttd, 0777, true);

                $file_name = 'ttd_dosen_' . $id_user . '_' . time() . '.png';
                $file_path = FCPATH . 'uploads/ttd/' . $file_name;

                if (file_put_contents($file_path, $image_base64)) {
                    $old_detail = $this->db->get_where('data_dosen', ['id' => $id_user])->row_array();
                    if ($old_detail && !empty($old_detail['ttd']) && file_exists(FCPATH . 'uploads/ttd/' . $old_detail['ttd'])) {
                        unlink(FCPATH . 'uploads/ttd/' . $old_detail['ttd']);
                    }

                    $detail_data['ttd'] = $file_name;
                } else {
                    $this->session->set_flashdata('pesan_error', 'Gagal menyimpan file Tanda Tangan.');
                    redirect('dosen/profil');
                    return;
                }
            }
        }

        if ($this->M_Data->update_user($id_user, $akun_data, 'dosen', $detail_data)) {
            $this->session->set_flashdata('pesan_sukses', 'Profil berhasil diperbarui!');
            if(isset($akun_data['nama'])) {
                $this->session->set_userdata('nama', $akun_data['nama']);
            }
        } else {
            $this->session->set_flashdata('pesan_error', 'Gagal memperbarui profil di database.');
        }

        redirect('dosen/profil');
    }

    public function kinerja_dosen()
    {
        if ($this->session->userdata('is_kaprodi') != 1) {
            redirect('dosen/bimbingan_list');
        }

        $data['title'] = 'Laporan Kinerja Dosen';
        $this->load->library('pagination');
        $this->load->model('operator/M_laporan_opt');

        $prodi_kaprodi = $this->session->userdata('prodi');
        $keyword = $this->input->get('keyword');

        // Config Pagination
        $config['base_url'] = base_url('dosen/kinerja_dosen');
        $config['total_rows'] = $this->M_laporan_opt->count_dosen_pembimbing($keyword, $prodi_kaprodi);
        $config['per_page'] = 10;
        $config['reuse_query_string'] = TRUE;
        $config['page_query_string'] = TRUE;
        $config['query_string_segment'] = 'page';

        $this->load->helper('pagination_custom');
        config_pagination($config);
        $this->pagination->initialize($config);

        $page = $this->input->get('page') ? $this->input->get('page') : 0;
        $data['dosen_list'] = $this->M_laporan_opt->get_dosen_pembimbing_list($keyword, $config['per_page'], $page, $prodi_kaprodi);

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
        $data['list_semester'] = $this->M_laporan_opt->get_all_semesters();
        // Filter otomatis berdasarkan prodi kaprodi
        $data['prodi_kaprodi'] = $prodi_kaprodi;

        $data['pagination'] = $this->pagination->create_links();
        $data['total_rows'] = $config['total_rows'];
        $data['start_index'] = $page;
        $data['per_page'] = $config['per_page'];
        $data['keyword'] = $keyword;

        $this->load->view('template/header', $data);
        $this->load->view('template/sidebar', $data);
        $this->load->view('operator/v_kinerja_dosen', $data);
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
        if ($this->session->userdata('is_kaprodi') != 1) {
            $this->session->set_flashdata('pesan_error', 'Akses ditolak. Fitur ini hanya untuk Kaprodi.');
            redirect('dosen/monitoring_prodi');
        }

        $id_skripsi = $this->input->post('id_skripsi');
        $pembimbing1 = $this->input->post('pembimbing1');
        $pembimbing2 = $this->input->post('pembimbing2');

        if ($id_skripsi && $pembimbing1 && $pembimbing2) {
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
        if ($this->session->userdata('is_kaprodi') != 1) {
            echo '<div class="text-center py-4 text-danger"><i class="fas fa-exclamation-triangle fa-2x"></i><p class="mt-2">Akses ditolak.</p></div>';
            return;
        }

        $semester = $this->input->get('semester');
        $prodi = $this->input->get('prodi');

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

        $aktivitas = $this->M_Log->get_dosen_activity_by_semester($id_dosen, $semester, $prodi);

        $total_aktivitas = 0;
        foreach ($aktivitas as $act) {
            $total_aktivitas += $act['total_aksi'];
        }

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

    public function manajemen_akun()
    {
        if ($this->session->userdata('is_kaprodi') != 1) {
            $this->session->set_flashdata('pesan_error', 'Akses ditolak. Fitur ini hanya untuk Kaprodi.');
            redirect('dosen/bimbingan_list');
        }

        $data['title'] = 'Manajemen Akun Pengguna';
        $this->load->library('pagination');

        $role = $this->input->get('role');
        $prodi = $this->input->get('prodi');
        $keyword = $this->input->get('keyword');

        $config['base_url'] = base_url('dosen/manajemen_akun');
        $config['total_rows'] = $this->M_akun_opt->count_all_users($role, $prodi, $keyword);
        $config['per_page'] = 15;

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
        $data['users'] = $this->M_akun_opt->get_all_users_with_details($role, $prodi, $keyword, $config['per_page'], $page);

        $data['pagination'] = $this->pagination->create_links();
        $data['total_rows'] = $config['total_rows'];
        $data['start_index'] = $page;

        $this->load->view('template/header', $data);
        $this->load->view('template/sidebar', $data);
        $this->load->view('dosen/v_manajemen_akun', $data);
        $this->load->view('template/footer');
    }

    public function edit_akun($id = null)
    {
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
                // Update session if user is editing their own account
                if ($id == $this->session->userdata('id') && isset($akun_data['nama'])) {
                    $this->session->set_userdata('nama', $akun_data['nama']);
                }
            } else {
                $this->session->set_flashdata('pesan_error', 'Gagal memperbarui akun.');
            }

            redirect('dosen/manajemen_akun');
        }
    }

    public function delete_akun($id)
    {
        if ($this->session->userdata('is_kaprodi') != 1) {
            $this->session->set_flashdata('pesan_error', 'Akses ditolak. Fitur ini hanya untuk Kaprodi.');
            redirect('dosen/bimbingan_list');
        }

        $res = $this->M_akun_opt->delete_user($id);
        if ($res === 'blocked') {
            $this->session->set_flashdata('pesan_error', 'Penghapusan diblokir: mahasiswa ini memiliki riwayat skripsi/progres/ujian.');
        } elseif ($res) {
            $this->session->set_flashdata('pesan_sukses', 'Akun berhasil dihapus!');
        } else {
            $this->session->set_flashdata('pesan_error', 'Gagal menghapus akun.');
        }
        redirect('dosen/manajemen_akun');
    }

public function kinerja_dosen_kaprodi()
    {
        $prodi_kaprodi = $this->session->userdata('prodi'); 
        $keyword = $this->input->get('keyword');
        
        $this->load->library('pagination');
        $config['base_url'] = base_url('dosen/kinerja_dosen_kaprodi');
        
        $this->db->like('nama', $keyword);
        $this->db->where('role', 'dosen'); 
        
        $config['total_rows'] = $this->db->count_all_results('mstr_akun'); 
        $config['per_page'] = 10;
        
        // ... (Config Pagination Tetap Sama) ...
        $config['full_tag_open'] = '<ul class="pagination pagination-sm m-0 float-right">';
        $config['full_tag_close'] = '</ul>';
        $config['num_tag_open'] = '<li class="page-item">';
        $config['num_tag_close'] = '</li>';
        $config['cur_tag_open'] = '<li class="page-item active"><a class="page-link" href="#">';
        $config['cur_tag_close'] = '</a></li>';
        $config['next_tag_open'] = '<li class="page-item">';
        $config['next_tag_close'] = '</li>';
        $config['prev_tag_open'] = '<li class="page-item">';
        $config['prev_tag_close'] = '</li>';
        $config['attributes'] = array('class' => 'page-link');
        
        $this->pagination->initialize($config);
        
        $data['start_index'] = ($this->uri->segment(3)) ? $this->uri->segment(3) : 0;
        
        $this->db->select('mstr_akun.id, mstr_akun.nama, data_dosen.nidk');
        $this->db->from('mstr_akun');
        $this->db->join('data_dosen', 'mstr_akun.id = data_dosen.id', 'left');
        $this->db->where('mstr_akun.role', 'dosen');
        
        if($keyword){
            $this->db->group_start();
            $this->db->like('mstr_akun.nama', $keyword);
            $this->db->or_like('data_dosen.nidk', $keyword);
            $this->db->group_end();
        }
        
        $this->db->limit($config['per_page'], $data['start_index']);
        $dosen_raw = $this->db->get()->result_array();
        
        foreach($dosen_raw as $key => $val){
            $dosen_raw[$key]['total_aksi'] = 0; 
        }
        
        $data['dosen_list'] = $dosen_raw;
        $data['pagination'] = $this->pagination->create_links();
        $data['total_rows'] = $config['total_rows'];
        $data['per_page'] = $config['per_page'];

        // --- TAMBAHAN BARU: AMBIL LIST SEMESTER DARI DATABASE ---
        $data['list_semester'] = $this->M_Dosen->get_all_semesters();
        
        $this->load->view('template/header', $data);
        $this->load->view('template/sidebar', $data);
        $this->load->view('dosen/v_kinerja_dosen_kaprodi', $data); 
        $this->load->view('template/footer');
    }

    // --- FITUR BARU: ON/OFF NOTIFIKASI WA ---
    public function toggle_notif_wa($id_skripsi)
    {
        $id_dosen = $this->session->userdata('id');
        
        // Cek data skripsi
        $skripsi = $this->db->get_where('skripsi', ['id' => $id_skripsi])->row_array();
        
        if ($skripsi) {
            // Cek apakah dosen yang login ini adalah P1 atau P2
            if ($skripsi['pembimbing1'] == $id_dosen) {
                // Balikkan status (jika 1 jadi 0, jika 0 jadi 1)
                $status_baru = $skripsi['notif_p1'] == 1 ? 0 : 1;
                $this->db->where('id', $id_skripsi)->update('skripsi', ['notif_p1' => $status_baru]);
                
            } elseif ($skripsi['pembimbing2'] == $id_dosen) {
                $status_baru = $skripsi['notif_p2'] == 1 ? 0 : 1;
                $this->db->where('id', $id_skripsi)->update('skripsi', ['notif_p2' => $status_baru]);
            }
            
            $pesan = $status_baru == 1 ? 'Notifikasi WA Dihidupkan.' : 'Notifikasi WA Dimatikan (Mute).';
            $this->session->set_flashdata('pesan_sukses', $pesan);
        }
        
        redirect('dosen/bimbingan_list');
    }

}
