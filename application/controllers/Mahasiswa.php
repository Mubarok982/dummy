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
        
        // 1. Ambil Data Skripsi & Mahasiswa (Join)
        // Pastikan model get_skripsi_by_mhs melakukan JOIN ke tabel data_mahasiswa untuk ambil 'prodi'
        $data['skripsi'] = $this->M_Mahasiswa->get_skripsi_by_mhs($id_mahasiswa);

        if (!$data['skripsi']) {
            $this->session->set_flashdata('pesan_error', 'Anda belum mengajukan judul skripsi.');
            redirect('mahasiswa/pengajuan_judul');
        }

        $npm_mhs = $data['skripsi']['npm'] ?? $this->session->userdata('npm');
        
        // --- LOGIKA BARU: TENTUKAN MAX BAB BERDASARKAN PRODI ---
        $prodi = $this->session->userdata('prodi'); // Ambil dari session
        // Atau ambil dari data skripsi jika session kosong
        if(empty($prodi) && isset($data['skripsi']['prodi'])) {
            $prodi = $data['skripsi']['prodi'];
        }

        // Default S1 (Bab 6)
        $max_bab = 6; 
        
        // Cek jika D3 (Bab 5)
        if (stripos($prodi, 'D3') !== false || stripos($prodi, 'Diploma 3') !== false) {
            $max_bab = 5;
        }
        
        $data['max_bab'] = $max_bab; // Kirim ke View
        // -------------------------------------------------------

        $data['status_acc'] = $data['skripsi']['status_acc_kaprodi'] ?? 'menunggu';
        
        $recipients = $this->M_Chat->get_valid_chat_recipients_mhs($npm_mhs);
        $data['valid_recipients'] = $recipients ? $recipients : [
            'kaprodi'     => null, 
            'pembimbing1' => null, 
            'pembimbing2' => null
        ];

        $progres = $this->M_Mahasiswa->get_progres_by_skripsi($data['skripsi']['id']);
        
        if (empty($progres)) {
            $data['next_bab'] = 1;
            $data['last_progres'] = NULL;
        } else {
            $last = end($progres); 
            $data['last_progres'] = $last;
            
            $p1 = $last['progres_dosen1'] ?? 0;
            $p2 = $last['progres_dosen2'] ?? 0;

            if ($p1 == 100 && $p2 == 100) {
                $data['next_bab'] = $last['bab'] + 1;
            } else {
                $data['next_bab'] = $last['bab'];
            }
        }

        // Cegah next_bab melebihi max_bab prodi
        if ($data['next_bab'] > $max_bab) {
            $data['next_bab'] = $max_bab; 
            $data['is_finished'] = true; // Tandai sudah selesai
        } else {
            $data['is_finished'] = false;
        }

        $data['progres_riwayat'] = $this->M_Mahasiswa->get_riwayat_progres($npm_mhs); 
        
     // Ambil Status Ujian (Sempro/Pendadaran)
        $ujian = $this->M_Mahasiswa->get_status_ujian_terakhir($data['skripsi']['id']);
        
        // Langsung ambil dari kolom status
        $data['status_ujian'] = $ujian ? $ujian['status'] : null;
        
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

        // ========== FITUR BARU: HANDLE UPDATE JUDUL ==========
        $gunakan_judul_lama = $this->input->post('gunakan_judul_lama');
        
        if (!$gunakan_judul_lama) {
            // User ingin mengubah judul
            $judul_baru = $this->input->post('judul');
            
            if ($judul_baru) {
                $data_update_judul = [
                    'judul' => $judul_baru,
                    'status_acc_kaprodi' => 'menunggu' // Reset ke menunggu approval kaprodi
                ];
                
                // Update dengan sistem histori
                $this->M_Dosen->update_skripsi_with_histori($skripsi['id'], $data_update_judul);
                
                // Update data skripsi lokal
                $skripsi['judul'] = $judul_baru;
            }
        }
        // =================================================
        
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

            $progres_data = [
                'npm'            => $npm,
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
        
        // Validasi form
        $this->form_validation->set_rules('judul', 'Judul Skripsi', 'required|trim');
        $this->form_validation->set_rules('tema', 'Tema', 'required');
        $this->form_validation->set_rules('pembimbing1', 'Pembimbing 1', 'required');
        $this->form_validation->set_rules('pembimbing2', 'Pembimbing 2', 'required');

        if ($this->form_validation->run() == FALSE) {
            $this->session->set_flashdata('pesan_error', validation_errors());
        } else {
            $data_update = [
                'judul' => $this->input->post('judul'),
                'tema' => $this->input->post('tema'),
                'pembimbing1' => $this->input->post('pembimbing1'),
                'pembimbing2' => $this->input->post('pembimbing2'),
                'tgl_pengajuan_judul' => $this->input->post('tgl_pengajuan_judul') ? $this->input->post('tgl_pengajuan_judul') : date('Y-m-d'),
                'status_acc_kaprodi' => 'menunggu'
            ];

            // Update dengan sistem histori
            $result = $this->M_Dosen->update_skripsi_with_histori($id_skripsi, $data_update);

            if ($result) {
                $this->session->set_flashdata('pesan_sukses', 'Judul skripsi berhasil diperbarui dan menunggu persetujuan kaprodi.');
                $this->M_Log->record('Judul', 'Mengubah judul skripsi menjadi: ' . $data_update['judul']);
            } else {
                $this->session->set_flashdata('pesan_error', 'Gagal memperbarui judul skripsi.');
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
