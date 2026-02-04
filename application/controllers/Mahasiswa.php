<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Mahasiswa extends CI_Controller {

    public function __construct()
    {
        parent::__construct();
        // 1. Cek Login & Role
        if ($this->session->userdata('role') != 'mahasiswa' || !$this->session->userdata('is_login')) {
            redirect('auth/login');
        }
        
        $this->load->model(['M_Data', 'M_Mahasiswa', 'M_Log', 'M_Dosen', 'M_Chat']);

        // 2. LOGIKA FORCE REDIRECT (CEK DATA LENGKAP)
        $id_user = $this->session->userdata('id');
        
        // Ambil data detail mahasiswa
        $detail = $this->db->get_where('data_mahasiswa', ['id' => $id_user])->row_array();
        
        // Tentukan halaman yang 'boleh' diakses saat data belum lengkap
        // Kita izinkan 'biodata', 'update_biodata', dan 'logout'
        $allowed_methods = ['biodata', 'update_biodata', 'logout'];
        $current_method = $this->router->method;

        // Cek jika NPM atau Prodi kosong (Indikator data belum lengkap)
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
        // Ambil data skripsi yang sudah di-join dengan NPM di model
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
                'status_acc_kaprodi' => 'menunggu'
            ];

            // --- PERBAIKAN: HAPUS LOGIKA UPDATE, PAKSA INSERT ---
            // Kita tidak perlu cek $is_update, langsung insert saja agar jadi history
            
            $this->M_Mahasiswa->insert_skripsi($data);
            $this->session->set_flashdata('pesan_sukses', 'Pengajuan judul baru berhasil dikirim.');
            
            // Log
            $this->M_Log->record('Judul', 'Mengajukan judul baru: ' . $data['judul']);
            redirect('mahasiswa/pengajuan_judul');
        }
    }

    public function bimbingan()
    {
        $id_mahasiswa = $this->session->userdata('id');
        $data['title'] = 'Bimbingan Skripsi';
        
        $data['skripsi'] = $this->M_Mahasiswa->get_skripsi_by_mhs($id_mahasiswa);

        if (!$data['skripsi']) {
            $this->session->set_flashdata('pesan_error', 'Anda belum mengajukan judul skripsi.');
            redirect('mahasiswa/pengajuan_judul');
        }

        // Ambil NPM & Status ACC Kaprodi
        $npm_mhs = $data['skripsi']['npm'] ?? $this->session->userdata('npm');
        $data['status_acc'] = $data['skripsi']['status_acc_kaprodi'] ?? 'menunggu';
        
        // Logika Chat: Default NULL agar view tidak error 'Undefined Index'
        $recipients = $this->M_Chat->get_valid_chat_recipients_mhs($npm_mhs);
        $data['valid_recipients'] = $recipients ? $recipients : [
            'kaprodi'     => null, 
            'pembimbing1' => null, 
            'pembimbing2' => null
        ];

        // Logika Penguncian BAB Selanjutnya
        $progres = $this->M_Mahasiswa->get_progres_by_skripsi($data['skripsi']['id']);
        
        if (empty($progres)) {
            $data['next_bab'] = 1;
            $data['last_progres'] = NULL;
        } else {
            $last = end($progres); 
            $data['last_progres'] = $last;
            
            // Cek progres dosen (Wajib 100 untuk lanjut bab)
            $p1 = $last['progres_dosen1'] ?? 0;
            $p2 = $last['progres_dosen2'] ?? 0;

            if ($p1 == 100 && $p2 == 100) {
                $data['next_bab'] = $last['bab'] + 1;
            } else {
                $data['next_bab'] = $last['bab'];
            }
        }

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

        // 1. Validasi Judul
        if (!$skripsi || $skripsi['status_acc_kaprodi'] != 'diterima') {
            $this->session->set_flashdata('pesan_error', 'Gagal: Judul belum disetujui Kaprodi.');
            redirect('mahasiswa/bimbingan');
        }
        
        // 2. Ambil Data Input
        $npm  = $this->session->userdata('npm');
        $nama = $this->session->userdata('nama'); // Nama Mahasiswa
        $bab  = $this->input->post('bab');
        $is_revisi = $this->input->post('is_revisi'); 

        // 3. Konfigurasi Nama File
        $clean_nama = str_replace([' ', '.', ','], '_', $nama);
        $nama_file  = 'Progres_' . $clean_nama . '_' . $npm . '_BAB' . $bab;
        
        if ($is_revisi == '1') {
            $nama_file .= '_REVISI';
        }
        
        $nama_file .= '_' . time();

        // 4. Config Upload
        $config['upload_path']   = './uploads/progres/';
        $config['allowed_types'] = 'pdf'; 
        $config['max_size']      = 5120; // 5MB
        $config['file_name']     = $nama_file;
        $config['overwrite']     = true;

        $this->load->library('upload', $config);

        // 5. Eksekusi Upload
        if (!$this->upload->do_upload('file_progres')) {
            $this->session->set_flashdata('pesan_error', strip_tags($this->upload->display_errors()));
            redirect('mahasiswa/bimbingan');
        } else {
            // --- JIKA BERHASIL UPLOAD ---
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

            // Catat Log
            $jenis_upload = ($is_revisi == '1') ? "Revisi" : "Baru";
            $keterangan_log = "Unggah Progres $jenis_upload BAB $bab";
            $this->M_Log->record('Progres', $keterangan_log, $id_baru);
            
            // ============================================================
            // INTEGRASI FONNTE: KIRIM NOTIFIKASI KE DOSEN
            // ============================================================
            
            // 1. Load Helper
            $this->load->helper('fonnte');

            // 2. Ambil Nomor HP Dosen
            $kontak = $this->M_Mahasiswa->get_kontak_pembimbing_by_skripsi($skripsi['id']);

            if ($kontak) {
                // 3. Susun Pesan
                $pesan_wa  = "*NOTIFIKASI BIMBINGAN SKRIPSI*\n\n";
                $pesan_wa .= "Halo Bapak/Ibu Dosen,\n";
                $pesan_wa .= "Mahasiswa bimbingan Anda telah mengunggah progres baru.\n\n";
                $pesan_wa .= "Nama: *$nama*\n";
                $pesan_wa .= "NPM: $npm\n";
                $pesan_wa .= "File: *BAB $bab ($jenis_upload)*\n";
                $pesan_wa .= "Waktu: " . date('d-m-Y H:i') . "\n\n";
                $pesan_wa .= "Silakan login ke sistem WBS untuk memeriksa dan memberikan koreksi.\n";
                $pesan_wa .= "Terima kasih.";

                // 4. Kirim ke Pembimbing 1 (Jika ada nomornya)
                if (!empty($kontak['hp_p1'])) {
                    kirim_wa_fonnte($kontak['hp_p1'], $pesan_wa);
                }

                // 5. Kirim ke Pembimbing 2 (Jika ada nomornya)
                if (!empty($kontak['hp_p2'])) {
                    kirim_wa_fonnte($kontak['hp_p2'], $pesan_wa);
                }
            }
            // ============================================================
            
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
                'file' => $file_name, // Ganti file_draft ke file agar sinkron dengan tabel riwayat
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
        // Load library upload
        $this->load->library('upload');
        
        $id_mahasiswa = $this->session->userdata('id');

        // 1. Validasi Input
        $this->form_validation->set_rules('nama', 'Nama Lengkap', 'required|trim');
        $this->form_validation->set_rules('telepon', 'No. WhatsApp', 'required|numeric|trim');
        $this->form_validation->set_rules('email', 'Email', 'valid_email|trim');

        if ($this->form_validation->run() == FALSE) {
            $this->session->set_flashdata('pesan_error', 'Gagal update: Cek kembali isian form Anda.');
            $this->biodata();
        } else {
            
            // --- DATA UNTUK TABEL mstr_akun ---
            $akun_data = [
                'nama' => $this->input->post('nama', true),
            ];

            // --- DATA UNTUK TABEL data_mahasiswa ---
            $detail_data = [
                'jenis_kelamin'    => $this->input->post('jenis_kelamin', true),
                'tempat_tgl_lahir' => $this->input->post('tempat_tgl_lahir', true),
                'telepon'          => $this->input->post('telepon', true),
                'email'            => $this->input->post('email', true),
                'alamat'           => $this->input->post('alamat', true),
            ];

            // 2. PROSES UPLOAD FOTO PROFIL (IMPROVED: 5MB + Support WEBP
            if (!empty($_FILES['foto']['name'])) {
                $this->upload->initialize(array(), TRUE); // Reset config

                $config_foto['upload_path']   = './uploads/profile/';
                // Tambahkan format webp dan jpeg
                $config_foto['allowed_types'] = 'jpg|jpeg|png|webp'; 
                // Naikkan limit ke 5MB (5120 KB)
                $config_foto['max_size']      = 5120; 
                $config_foto['file_name']     = 'profile_' . $id_mahasiswa . '_' . time();
                $config_foto['overwrite']     = true;

                $this->upload->initialize($config_foto);

                if ($this->upload->do_upload('foto')) {
                    // Hapus foto lama
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

            // 3. PROSES TTD DIGITAL (Dari Canvas / Base64)

            $ttd_base64 = $this->input->post('ttd_base64');
            
            if (!empty($ttd_base64)) {
                // Decode base64 image
                // Format data biasanya: "data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAA..."
                $image_parts = explode(";base64,", $ttd_base64);
                
                if (count($image_parts) == 2) {
                    $image_type_aux = explode("image/", $image_parts[0]);
                    $image_type = $image_type_aux[1]; // png
                    $image_base64 = base64_decode($image_parts[1]);
                    
                    // Buat nama file unik
                    $file_name = 'ttd_' . $id_mahasiswa . '_' . time() . '.png';
                    $file_path = FCPATH . 'uploads/ttd/' . $file_name;

                    // Simpan file ke folder
                    if (file_put_contents($file_path, $image_base64)) {
                        // Hapus TTD lama jika ada
                        $old_mhs = $this->db->get_where('data_mahasiswa', ['id' => $id_mahasiswa])->row_array();
                        if ($old_mhs && !empty($old_mhs['ttd']) && file_exists(FCPATH . 'uploads/ttd/' . $old_mhs['ttd'])) {
                            unlink(FCPATH . 'uploads/ttd/' . $old_mhs['ttd']);
                        }

                        // Masukkan nama file ke array update database
                        $detail_data['ttd'] = $file_name;
                    } else {
                        $this->session->set_flashdata('pesan_error', 'Gagal menyimpan file Tanda Tangan ke server.');
                        redirect('mahasiswa/biodata');
                        return;
                    }
                }
            }

            // 4. EKSEKUSI UPDATE DATABASE
            $this->db->trans_start(); // Mulai Transaksi biar aman
            
            $this->db->where('id', $id_mahasiswa);
            $this->db->update('mstr_akun', $akun_data);

            $this->db->where('id', $id_mahasiswa);
            $this->db->update('data_mahasiswa', $detail_data);
            
            $this->db->trans_complete(); // Selesai Transaksi

            if ($this->db->trans_status() === FALSE) {
                $this->session->set_flashdata('pesan_error', 'Gagal update database.');
            } else {
                // Update session nama jika berubah
                if(isset($akun_data['nama'])) {
                    $this->session->set_userdata('nama', $akun_data['nama']);
                }
                $this->session->set_flashdata('pesan_sukses', 'Biodata berhasil diperbarui!');
            }
            
            redirect('mahasiswa/biodata');
        }
    }

    public function riwayat_progres()
{
    $id_mahasiswa = $this->session->userdata('id');
    $data['title'] = 'Riwayat Progres';
    
    // Ambil data skripsi untuk kebutuhan title/header
    $data['skripsi'] = $this->M_Mahasiswa->get_skripsi_by_mhs($id_mahasiswa);
    
    if (!$data['skripsi']) {
        redirect('mahasiswa/pengajuan_judul');
    }

    // Ambil semua data progres berdasarkan ID Skripsi
    $data['progres'] = $this->M_Mahasiswa->get_progres_by_skripsi($data['skripsi']['id']);

    $this->load->view('template/header', $data);
    $this->load->view('template/sidebar', $data);
    $this->load->view('mahasiswa/v_riwayat', $data); // Pastikan file v_riwayat.php ada di folder views/mahasiswa
    $this->load->view('template/footer');
}
}