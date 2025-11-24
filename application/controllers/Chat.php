<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Chat extends CI_Controller {

    public function __construct()
    {
        parent::__construct();
        if (!$this->session->userdata('is_login')) redirect('auth/login');
        $this->load->model('M_Chat');
    }

    // Halaman Utama Chat
    public function index()
    {
        $id_user = $this->session->userdata('id');
        $role = $this->session->userdata('role');

        $data['title'] = 'Ruang Diskusi';
        $data['kontak'] = $this->M_Chat->get_kontak_chat($id_user, $role); // Ambil list teman chat

        $this->load->view('template/header', $data);
        $this->load->view('template/sidebar', $data);
        $this->load->view('v_chat', $data); // Kita buat view ini nanti
        $this->load->view('template/footer');
    }

    // --- API UNTUK AJAX ---

    // Ambil isi percakapan (Load Realtime)
   // --- UPDATE: Load Pesan dengan Gambar ---
    public function load_pesan()
    {
        $id_saya = $this->session->userdata('id');
        $id_lawan = $this->input->post('id_lawan');

        $chat = $this->M_Chat->get_chat($id_saya, $id_lawan);
        
        $html = '';
        foreach ($chat as $c) {
            $is_me = ($c['id_pengirim'] == $id_saya);
            $posisi = $is_me ? 'right' : 'left';
            
            // Styling Bubble Chat Modern
            $bubbleColor = $is_me ? '#dcf8c6' : '#ffffff'; // Warna ala WA
            $align = $is_me ? 'margin-left: auto;' : 'margin-right: auto;';
            
            $html .= '<div class="direct-chat-msg '.$posisi.'">';
            $html .= '  <div class="direct-chat-infos clearfix">';
            $html .= '    <span class="direct-chat-timestamp float-'.$posisi.'">'.date('H:i', strtotime($c['waktu'])).'</span>';
            $html .= '  </div>';
            
            // Bubble Container
            $html .= '  <div class="direct-chat-text" style="background-color: '.$bubbleColor.'; border: 1px solid #ddd; color: #333; width: fit-content; max-width: 75%; '.$align.'">';
            
            // Cek ada gambar atau tidak
            if (!empty($c['gambar'])) {
                $img_url = base_url('uploads/chat/' . $c['gambar']);
                $html .= '<a href="'.$img_url.'" target="_blank"><img src="'.$img_url.'" style="width: 100%; max-width: 200px; border-radius: 5px; margin-bottom: 5px;"></a><br>';
            }

            $html .=      $c['pesan'];
            $html .= '  </div>';
            $html .= '</div>';
        }
        echo $html;
    }

    // --- UPDATE: Kirim Pesan dengan Upload ---
   public function kirim_pesan()
    {
        // 1. Setup Respon Default
        $response = ['status' => false, 'msg' => 'Gagal tidak diketahui'];

        // 2. Pastikan Folder Ada (Cek Permission)
        $path = './uploads/chat/';
        if (!is_dir($path)) {
            if (!mkdir($path, 0777, true)) {
                echo json_encode(['status' => false, 'msg' => 'Gagal membuat folder uploads/chat. Cek izin folder!']);
                return;
            }
        }

        $data = [
            'id_pengirim' => $this->session->userdata('id'),
            'id_penerima' => $this->input->post('id_penerima'),
            'pesan'       => $this->input->post('pesan')
        ];

        // 3. Logika Upload Gambar
        if (!empty($_FILES['gambar']['name'])) {
            $config['upload_path']   = $path;
            $config['allowed_types'] = 'gif|jpg|png|jpeg|pdf'; 
            $config['max_size']      = 5120; // 5MB
            $config['encrypt_name']  = TRUE; // Enkripsi nama file agar aman

            $this->load->library('upload', $config);

            if ($this->upload->do_upload('gambar')) {
                $uploadData = $this->upload->data();
                $data['gambar'] = $uploadData['file_name'];
            } else {
                // JIKA GAGAL UPLOAD, KIRIM ERROR KE JS
                echo json_encode(['status' => false, 'msg' => $this->upload->display_errors('', '')]);
                return;
            }
        }

        // 4. Simpan Database
        // Validasi: Jangan simpan jika pesan & gambar kosong dua-duanya
        if (empty(trim($data['pesan'])) && empty($data['gambar'])) {
            echo json_encode(['status' => false, 'msg' => 'Pesan atau gambar tidak boleh kosong']);
            return;
        }

        if ($this->M_Chat->send_message($data)) {
            echo json_encode(['status' => true, 'msg' => 'Berhasil']);
        } else {
            echo json_encode(['status' => false, 'msg' => 'Gagal insert ke database']);
        }
    }
}