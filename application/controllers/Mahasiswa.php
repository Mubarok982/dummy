<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Mahasiswa extends CI_Controller {

    public function __construct()
    {
        parent::__construct();
        if ($this->session->userdata('role') != 'mahasiswa' || !$this->session->userdata('is_login')) {
            redirect('auth/login');
        }
        
        $this->load->model(['M_Data', 'M_Mahasiswa', 'M_Log', 'M_Dosen', 'M_Chat']);

        $id_user = $this->session->userdata('id');
        
        $detail = $this->db->get_where('data_mahasiswa', ['id' => $id_user])->row_array();
        
        $allowed_methods = ['biodata', 'update_biodata', 'logout'];
        $current_method = $this->router->method;

        if ((empty($detail['npm']) || empty($detail['prodi'])) && !in_array($current_method, $allowed_methods)) {
            $this->session->set_flashdata('pesan_error', '⚠️ Halo! Silakan lengkapi <b>NPM</b> dan <b>Prodi</b> Anda terlebih dahulu sebelum melanjutkan.');
            redirect('mahasiswa/biodata');
        }
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

        // --- TAMBAHAN LOGIKA MENGULANG ---
        // Cek apakah mahasiswa sudah pernah ujian dan statusnya mengulang
        $status_ujian = null;
        if ($data['skripsi']) {
            $ujian = $this->M_Mahasiswa->get_status_ujian_terakhir($data['skripsi']['id']);
            $status_ujian = $ujian ? $ujian['status'] : null;
        }
        $data['status_ujian'] = $status_ujian;
        // ---------------------------------

        // --- TAMBAHAN FITUR HISTORI ---
        // Ambil histori perubahan judul jika skripsi sudah ada
        if ($data['skripsi']) {
            $data['histori_judul'] = $this->M_Dosen->get_histori_judul($data['skripsi']['id']);
        } else {
            $data['histori_judul'] = [];
        }
        // -------------------------------

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
            // Data mutlak untuk judul baru
            $data = [
                'id_mahasiswa' => $id_mahasiswa,
                'tema' => $this->input->post('tema'),
                'judul' => $this->input->post('judul'),
                'pembimbing1' => $this->input->post('pembimbing1'),
                'pembimbing2' => $this->input->post('pembimbing2'),
                'tgl_pengajuan_judul' => date('Y-m-d H:i:s'),
                'skema' => 'Reguler',
                'status_acc_kaprodi' => 'menunggu'
            ];


            $this->M_Mahasiswa->insert_skripsi($data);
            
            $this->session->set_flashdata('pesan_sukses', 'Pengajuan judul baru berhasil dikirim.');
            $this->M_Log->record('Judul', 'Mengajukan judul baru: ' . $data['judul']);
            redirect('mahasiswa/pengajuan_judul');
        }
    }

public function bimbingan()
    {
        $id_mahasiswa = $this->session->userdata('id');
        $data['title'] = 'Bimbingan Skripsi';
        
        // 1. Ambil Data Skripsi & Mahasiswa
        $data['skripsi'] = $this->M_Mahasiswa->get_skripsi_by_mhs($id_mahasiswa);

        if (!$data['skripsi']) {
            $this->session->set_flashdata('pesan_error', 'Anda belum mengajukan judul skripsi.');
            redirect('mahasiswa/pengajuan_judul');
        }

        $id_skripsi = $data['skripsi']['id'];
        $npm_mhs = $data['skripsi']['npm'] ?? $this->session->userdata('npm');
        
        // --- LOGIKA BATAS BAB & JENIS UJIAN BERDASARKAN PRODI ---
        $prodi = $this->session->userdata('prodi'); 
        if(empty($prodi) && isset($data['skripsi']['prodi'])) {
            $prodi = $data['skripsi']['prodi'];
        }

        // Default S1
        $max_bab = 6; 
        $id_ujian_sempro = [1, 5]; // Default 1, Teknik Informatika S1 = 5
        $id_ujian_pendadaran = [2, 6]; // Default 2, Teknik Informatika S1 = 6
        
        // Jika D3
        if (stripos($prodi, 'D3') !== false || stripos($prodi, 'Diploma 3') !== false) {
            $max_bab = 5;
            $id_ujian_sempro = [7]; // Teknologi Informasi D3 = 7
            $id_ujian_pendadaran = [8]; // Teknologi Informasi D3 = 8
        }
        $data['max_bab'] = $max_bab; 
        
        $data['status_acc'] = $data['skripsi']['status_acc_kaprodi'] ?? 'menunggu';
        
        $recipients = $this->M_Chat->get_valid_chat_recipients_mhs($npm_mhs);
        $data['valid_recipients'] = $recipients ? $recipients : [
            'kaprodi'     => null, 
            'pembimbing1' => null, 
            'pembimbing2' => null
        ];

        // 2. ANALISA PROGRESS MAHASISWA & HITUNG BAB TERTINGGI (ACC 100%)
        $progres = $this->M_Mahasiswa->get_progres_by_skripsi($id_skripsi);
        $highest_acc_bab = 0;
        $is_revisi = false;
        $target_bab = 1;
        $acc_bab_3_count = 0;
        $acc_bab_max_count = 0;

        if (empty($progres)) {
            $target_bab = 1;
        } else {
            foreach ($progres as $p) {
                if ($p['progres_dosen1'] == 100 && $p['progres_dosen2'] == 100) {
                    if ($p['bab'] > $highest_acc_bab) $highest_acc_bab = $p['bab'];
                    if ($p['bab'] == 3) $acc_bab_3_count++;
                    if ($p['bab'] >= $max_bab) $acc_bab_max_count++;
                }
            }

            $last = end($progres);
            if ($last['progres_dosen1'] < 100 || $last['progres_dosen2'] < 100) {
                $target_bab = $last['bab'];
                $is_revisi = true;
            } else {
                $target_bab = $highest_acc_bab + 1;
                $is_revisi = false;
            }
        }

        // 3. CEK STATUS UJIAN (PEMISAHAN MUTLAK SEMPRO & PENDADARAN BERDASARKAN ID UJIAN)
        $ujian_db = $this->db
            ->order_by('id', 'DESC')
            ->get_where('ujian_skripsi', ['id_skripsi' => $id_skripsi])
            ->result();

        $status_sempro = null;
        $status_pendadaran = null;

        // Simpan ID terbaru masing-masing kategori (ID terbaru = ujian terbaru)
        $latest_sempro_id = 0;
        $latest_pendadaran_id = 0;

        foreach ($ujian_db as $u) {
            // Kategori SEMPRO
            if (in_array($u->id_jenis_ujian_skripsi, $id_ujian_sempro)) {
                if ($u->id > $latest_sempro_id) {
                    $latest_sempro_id = $u->id;
                    $status_sempro = strtolower($u->status);
                }
            }

            // Kategori PENDADARAN
            if (in_array($u->id_jenis_ujian_skripsi, $id_ujian_pendadaran)) {
                if ($u->id > $latest_pendadaran_id) {
                    $latest_pendadaran_id = $u->id;
                    $status_pendadaran = strtolower($u->status);
                }
            }
        }

        // 4. GATEKEEPER LOGIC (Penentuan Notifikasi dan Kunci Upload)
        $is_locked = false;
        $notif_type = ""; 
        $status_card = 'card-primary';
        $text_header = 'Upload Progres Baru';
        $pesan_info = 'Silakan upload file untuk melanjutkan progres bimbingan Anda.';

        // GLOBAL: STATUS MENGULANG MUTLAK MEMATIKAN SEMUANYA
        if ($status_sempro == 'mengulang' || $status_pendadaran == 'mengulang') {
            $is_locked = true; 
            $notif_type = "mengulang";
        }
        else {
            // ============================================
            // FASE PENDADARAN (Jika Bab >= Batas Maksimal)
            // ============================================
            if ($highest_acc_bab >= $max_bab) {
                if (in_array($status_pendadaran, ['diterima', 'lulus', 'selesai'])) {
                    // Lulus Pendadaran
                    $target_bab = $max_bab;
                    $is_locked = true;
                    $notif_type = "lulus_akhir";
                } 
                elseif ($status_pendadaran == 'perbaikan') {
                    if ($acc_bab_max_count >= 2) {
                        // Revisi udah di ACC dosen -> Tahan & Suruh Daftar Ulang (Tunggu Admin klik Lulus)
                        $target_bab = $max_bab;
                        $is_locked = true;
                        $notif_type = "siap_pendadaran_ulang"; 
                    } else {
                        // Buka form untuk upload Revisi Pendadaran
                        $target_bab = $max_bab;
                        $is_revisi = true;
                        $is_locked = false;
                        $status_card = 'card-warning';
                        $text_header = 'Upload Revisi Pendadaran';
                        $pesan_info = '<b>STATUS: PERBAIKAN PENDADARAN.</b> Silakan upload <b>Bab '.$max_bab.'</b> yang sudah direvisi pasca sidang.';
                    }
                } 
                elseif ($status_pendadaran == 'berlangsung' || $status_pendadaran == 'menunggu') {
                    // Ujian Sedang Terjadi
                    $target_bab = $max_bab;
                    $is_locked = true;
                    $notif_type = "pendadaran_berlangsung"; 
                }
                else {
                    // Belum daftar atau status belum terdata
                    $target_bab = $max_bab;
                    $is_locked = true;
                    $notif_type = "siap_pendadaran"; 
                }
            }

            // ============================================
            // FASE SEMPRO (Jika target_bab menyentuh Bab 4)
            // ============================================
            elseif ($highest_acc_bab == 3) {
                if (in_array($status_sempro, ['diterima', 'lulus', 'selesai'])) {
                    // Lulus Sempro -> Form Terbuka Lanjut Bab 4
                    $target_bab = 4;
                    $is_locked = false;
                } 
                elseif ($status_sempro == 'perbaikan') {
                    if ($acc_bab_3_count >= 2) {
                        // Revisi Sempro udah di ACC -> Tahan & Suruh Konfirmasi (Tunggu Admin)
                        $target_bab = 4; 
                        $is_locked = true;
                        $notif_type = "siap_sempro_ulang"; 
                    } else {
                        // Buka form untuk upload Revisi Sempro
                        $target_bab = 3;
                        $is_revisi = true;
                        $is_locked = false;
                        $status_card = 'card-warning';
                        $text_header = 'Upload Revisi Sempro';
                        $pesan_info = '<b>STATUS: PERBAIKAN SEMPRO.</b> Silakan upload <b>Bab 3</b> yang sudah direvisi pasca sidang.';
                    }
                } 
                elseif ($status_sempro == 'berlangsung' || $status_sempro == 'menunggu') {
                    // Sempro Sedang Terjadi
                    $target_bab = 4;
                    $is_locked = true;
                    $notif_type = "sempro_berlangsung"; 
                }
                else {
                    // Belum Daftar Sempro
                    $target_bab = 4;
                    $is_locked = true;
                    $notif_type = "siap_sempro"; 
                }
            }
            
            // Jika statusnya cuma revisi biasa antar Bab (Di luar Sempro/Pendadaran)
            if ($is_revisi && !$is_locked && $notif_type == "") {
                $status_card = 'card-warning';
                $text_header = 'Upload Revisi';
                $pesan_info = 'Silakan upload revisi bab ini berdasarkan catatan dosen.';
            }
        }

        // Pengaman Batas maksimal mutlak
        if ($target_bab > $max_bab) $target_bab = $max_bab;

        // 5. KIRIM DATA MATANG KE VIEW
        $data['target_bab'] = $target_bab;
        $data['is_revisi']  = $is_revisi;
        $data['is_locked']  = $is_locked;
        $data['notif_type'] = $notif_type;
        $data['status_card']= $status_card;
        $data['text_header']= $text_header;
        $data['pesan_info'] = $pesan_info;
        
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

        // ========== HANDLE UPDATE JUDUL DARI FORM BIMBINGAN ==========
        $gunakan_judul_lama = $this->input->post('gunakan_judul_lama');
        
        if (!$gunakan_judul_lama) {
            // User ingin mengubah judul saat proses bimbingan.
            $judul_baru = $this->input->post('judul');
            
            if (!empty($judul_baru) && $judul_baru != $skripsi['judul']) {
                // Jika ganti judul saat bimbingan, KITA HARUS BUAT ID SKRIPSI BARU!
                // Jangan pakai update_skripsi_with_histori karena itu akan mengacaukan ID lama.
                $data_judul_baru = [
                    'id_mahasiswa' => $id_mahasiswa,
                    'tema' => $skripsi['tema'], // Copy tema lama
                    'judul' => $judul_baru,
                    'pembimbing1' => $skripsi['pembimbing1'], // Copy dospem lama
                    'pembimbing2' => $skripsi['pembimbing2'],
                    'tgl_pengajuan_judul' => date('Y-m-d H:i:s'),
                    'skema' => $skripsi['skema'],
                    'status_acc_kaprodi' => 'menunggu' // Judul baru butuh ACC ulang!
                ];
                
                // Masukkan judul baru sebagai entri baru di database
                $this->M_Mahasiswa->insert_skripsi($data_judul_baru);
                
                $this->session->set_flashdata('pesan_sukses', 'Judul berhasil diubah. Karena judul baru, Anda harus menunggu persetujuan Kaprodi kembali sebelum bisa melanjutkan upload.');
                $this->M_Log->record('Judul', 'Mengubah judul skripsi via Bimbingan: ' . $judul_baru);
                
                // Paksa mahasiswa kembali, karena mereka gak boleh upload progres untuk judul yang statusnya baru "Menunggu"
                redirect('mahasiswa/bimbingan');
                return;
            }
        }
        // =================================================

        // JIKA TIDAK GANTI JUDUL, LANJUTKAN UPLOAD SEPERTI BIASA
        $npm  = $this->session->userdata('npm');
        $nama = $this->session->userdata('nama'); 
        $bab  = $this->input->post('bab');
        $is_revisi = $this->input->post('is_revisi'); 

        $clean_nama = str_replace([' ', '.', ','], '_', $nama);
        $nama_file  = 'Progres_' . $clean_nama . '_' . $npm . '_BAB' . $bab;
        
        if ($is_revisi == '1') {
            $nama_file .= '_REVISI';
        }
        
        $nama_file .= '_' . time();

        $config['upload_path']   = './uploads/progres/';
        $config['allowed_types'] = 'pdf'; 
        $config['max_size']      = 5120; 
        $config['file_name']     = $nama_file;
        $config['overwrite']     = true;

        $this->load->library('upload', $config);

        if (!$this->upload->do_upload('file_progres')) {
            $this->session->set_flashdata('pesan_error', strip_tags($this->upload->display_errors()));
            redirect('mahasiswa/bimbingan');
        } else {
            $file_data = $this->upload->data();
            
            $status_plagiasi_awal = ($bab == 1) ? 'Menunggu' : '-';

            // Masukkan ke ID Skripsi yang aktif (lama)
            $progres_data = [
                'npm'            => $npm,
                'id_skripsi'     => $skripsi['id'], 
                'bab'            => $bab,
                'file'           => $file_data['file_name'], 
                'progres_dosen1' => 0,          
                'progres_dosen2' => 0,          
                'nilai_dosen1'   => 'Menunggu', 
                'nilai_dosen2'   => 'Menunggu', 
                'created_at'     => date('Y-m-d H:i:s'),
                'tgl_upload'     => date('Y-m-d H:i:s'),
                'status_plagiasi'      => $status_plagiasi_awal, 
                'persentase_kemiripan' => 0
            ];
            
            $this->M_Mahasiswa->insert_progres($progres_data);
            $id_baru = $this->db->insert_id();

            $jenis_upload = ($is_revisi == '1') ? "Revisi" : "Baru";
            $keterangan_log = "Unggah Progres $jenis_upload BAB $bab";
            $this->M_Log->record('Progres', $keterangan_log, $id_baru);
            
            $this->load->helper('fonnte');

            $kontak = $this->M_Mahasiswa->get_kontak_pembimbing_by_skripsi($skripsi['id']);

            if ($kontak) {
                $pesan_wa  = "*NOTIFIKASI BIMBINGAN SKRIPSI*\n\n";
                $pesan_wa .= "Halo Bapak/Ibu Dosen,\n";
                $pesan_wa .= "Mahasiswa bimbingan Anda telah mengunggah progres baru.\n\n";
                $pesan_wa .= "Nama: *$nama*\n";
                $pesan_wa .= "NPM: $npm\n";
                $pesan_wa .= "File: *BAB $bab ($jenis_upload)*\n";
                $pesan_wa .= "Waktu: " . date('d-m-Y H:i') . "\n\n";
                $pesan_wa .= "Silakan login ke sistem WBS untuk memeriksa dan memberikan koreksi.\n";
                $pesan_wa .= "Terima kasih.";

                if (!empty($kontak['hp_p1'])) {
                    kirim_wa_fonnte($kontak['hp_p1'], $pesan_wa);
                }

                if (!empty($kontak['hp_p2'])) {
                    kirim_wa_fonnte($kontak['hp_p2'], $pesan_wa);
                }
            }
            
            $this->session->set_flashdata('pesan_sukses', "File $keterangan_log Berhasil diunggah & Notifikasi dikirim ke Dosen.");
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
                'file' => $file_name, 
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

    public function update_biodata()
    {
        $this->load->library('upload');
        $id_mahasiswa = $this->session->userdata('id');

        $this->form_validation->set_rules('nama', 'Nama Lengkap', 'required|trim');
        $this->form_validation->set_rules('telepon', 'No. WhatsApp', 'required|numeric|trim');
        $this->form_validation->set_rules('email', 'Email', 'valid_email|trim');

        if ($this->form_validation->run() == FALSE) {
            $this->session->set_flashdata('pesan_error', 'Gagal update: Cek kembali isian form Anda.');
            $this->biodata();
        } else {
            
            $akun_data = [
                'nama' => $this->input->post('nama', true),
            ];

            $detail_data = [
                'jenis_kelamin'    => $this->input->post('jenis_kelamin', true),
                'tempat_tgl_lahir' => $this->input->post('tempat_tgl_lahir', true),
                'telepon'          => $this->input->post('telepon', true),
                'email'            => $this->input->post('email', true),
                'alamat'           => $this->input->post('alamat', true),
            ];

            if (!empty($_FILES['foto']['name'])) {
                $this->upload->initialize(array(), TRUE); 

                $config_foto['upload_path']   = './uploads/profile/';
                $config_foto['allowed_types'] = 'jpg|jpeg|png|webp'; 
                $config_foto['max_size']      = 5120; 
                $config_foto['file_name']     = 'profile_' . $id_mahasiswa . '_' . time();
                $config_foto['overwrite']     = true;

                $this->upload->initialize($config_foto);

                if ($this->upload->do_upload('foto')) {
                    $old_akun = $this->db->get_where('mstr_akun', ['id' => $id_mahasiswa])->row_array();
                    if ($old_akun && !empty($old_akun['foto']) && file_exists(FCPATH . 'uploads/profile/' . $old_akun['foto'])) {
                        unlink(FCPATH . 'uploads/profile/' . $old_akun['foto']);
                    }

                    $new_foto = $this->upload->data('file_name');
                    $akun_data['foto'] = $new_foto;
                    $this->session->set_userdata('foto', $new_foto);
                } else {
                    $this->session->set_flashdata('pesan_error', 'Upload Foto Gagal: ' . $this->upload->display_errors('', ''));
                    redirect('mahasiswa/biodata');
                    return;
                }
            }

            $ttd_base64 = $this->input->post('ttd_base64');
            
            if (!empty($ttd_base64)) {
                $image_parts = explode(";base64,", $ttd_base64);
                
                if (count($image_parts) == 2) {
                    $image_type_aux = explode("image/", $image_parts[0]);
                    $image_type = $image_type_aux[1]; 
                    $image_base64 = base64_decode($image_parts[1]);
                    
                    $file_name = 'ttd_' . $id_mahasiswa . '_' . time() . '.png';
                    $file_path = FCPATH . 'uploads/ttd/' . $file_name;

                    if (file_put_contents($file_path, $image_base64)) {
                        $old_mhs = $this->db->get_where('data_mahasiswa', ['id' => $id_mahasiswa])->row_array();
                        if ($old_mhs && !empty($old_mhs['ttd']) && file_exists(FCPATH . 'uploads/ttd/' . $old_mhs['ttd'])) {
                            unlink(FCPATH . 'uploads/ttd/' . $old_mhs['ttd']);
                        }

                        $detail_data['ttd'] = $file_name;
                    } else {
                        $this->session->set_flashdata('pesan_error', 'Gagal menyimpan file Tanda Tangan ke server.');
                        redirect('mahasiswa/biodata');
                        return;
                    }
                }
            }

            $this->db->trans_start(); 
            
            $this->db->where('id', $id_mahasiswa);
            $this->db->update('mstr_akun', $akun_data);

            $this->db->where('id', $id_mahasiswa);
            $this->db->update('data_mahasiswa', $detail_data);
            
            $this->db->trans_complete();

            if ($this->db->trans_status() === FALSE) {
                $this->session->set_flashdata('pesan_error', 'Gagal update database.');
            } else {
                if(isset($akun_data['nama'])) {
                    $this->session->set_userdata('nama', $akun_data['nama']);
                }
                $this->session->set_flashdata('pesan_sukses', 'Biodata berhasil diperbarui!');
            }
            
            redirect('mahasiswa/biodata');
        }
    }

   public function update_judul($id_skripsi)
    {
        $id_mahasiswa = $this->session->userdata('id');
        
        $this->form_validation->set_rules('judul', 'Judul Skripsi', 'required|trim');
        $this->form_validation->set_rules('tema', 'Tema', 'required');
        $this->form_validation->set_rules('pembimbing1', 'Pembimbing 1', 'required');
        $this->form_validation->set_rules('pembimbing2', 'Pembimbing 2', 'required');

        if ($this->form_validation->run() == FALSE) {
            $this->session->set_flashdata('pesan_error', validation_errors());
        } else {
            // Karena sistem Anda sekarang menggunakan riwayat, kita hentikan update_skripsi_with_histori
            // dan kita paksakan insert baru agar ID Skripsinya berubah!
            $data_judul_baru = [
                'id_mahasiswa' => $id_mahasiswa,
                'tema' => $this->input->post('tema'),
                'judul' => $this->input->post('judul'),
                'pembimbing1' => $this->input->post('pembimbing1'),
                'pembimbing2' => $this->input->post('pembimbing2'),
                'tgl_pengajuan_judul' => $this->input->post('tgl_pengajuan_judul') ? $this->input->post('tgl_pengajuan_judul') : date('Y-m-d H:i:s'),
                'skema' => 'Reguler',
                'status_acc_kaprodi' => 'menunggu'
            ];

            // Insert sebagai judul/skripsi baru
            $result = $this->M_Mahasiswa->insert_skripsi($data_judul_baru);

            if ($result) {
                $this->session->set_flashdata('pesan_sukses', 'Judul skripsi berhasil diajukan ulang dan menunggu persetujuan kaprodi.');
                $this->M_Log->record('Judul', 'Ganti judul skripsi baru menjadi: ' . $data_judul_baru['judul']);
            } else {
                $this->session->set_flashdata('pesan_error', 'Gagal mengajukan judul skripsi baru.');
            }
        }
        redirect('mahasiswa/pengajuan_judul');
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

    
}
