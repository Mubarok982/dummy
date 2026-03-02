<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Chat extends CI_Controller {

    public function __construct()
    {
        parent::__construct();
        if (!$this->session->userdata('is_login')) redirect('auth/login');
        $this->load->model('M_Chat');
        $this->load->model('M_Mahasiswa');
    }

public function index()
    {
        $id_user = $this->session->userdata('id');
        $role = $this->session->userdata('role');
        $npm = $this->session->userdata('npm');

        $data['title'] = 'Ruang Diskusi';
        
        // LOGIKA ASLI ANDA (JANGAN DIHAPUS)
        if ($role == 'mahasiswa') {
            // Ambil hanya ID yang diizinkan (Kaprodi & Pembimbing jika sudah ACC)
            $allowed_ids = $this->M_Chat->get_valid_chat_recipients_mhs($npm);
            $data['kontak'] = $this->M_Chat->get_kontak_filtered($allowed_ids);
        } else {
            // Role dosen/operator tetap melihat kontak normal
            $data['kontak'] = $this->M_Chat->get_kontak_chat($id_user, $role);
        }

        // --- TAMBAHAN BARU UNTUK FITUR NOTIFIKASI ---
        // Ambil daftar pengirim pesan yang belum dibaca
        $data['unread_senders'] = $this->M_Chat->get_unread_senders($id_user);
        // --------------------------------------------

        $this->load->view('template/header', $data);
        $this->load->view('template/sidebar', $data);
        $this->load->view('v_chat', $data);
        $this->load->view('template/footer');
    }

  public function load_pesan()
    {
        $id_saya = $this->session->userdata('id');
        $id_lawan = $this->input->post('id_lawan');

        if (!$id_lawan) return;

        // 1. TANDAI PESAN TELAH DIBACA
        // Mengubah status is_read pesan dari id_lawan ke id_saya menjadi 1 (Terbaca)
        $this->db->where('id_pengirim', $id_lawan);
        $this->db->where('id_penerima', $id_saya);
        $this->db->where('is_read', 0);
        $this->db->update('tbl_pesan', ['is_read' => 1]); 

        // 2. AMBIL DATA PESAN DARI DATABASE
        $pesan = $this->M_Chat->get_chat($id_saya, $id_lawan);

        // 3. RENDER HTML BUBBLE CHAT
        $html = '';
        if(!empty($pesan)){
            $tanggal_sebelumnya = '';
            foreach($pesan as $p){
                // Header Tanggal (Jika beda hari)
                $tgl_pesan = date('Y-m-d', strtotime($p['waktu']));
                if($tgl_pesan != $tanggal_sebelumnya){
                    $html .= '<div class="text-center my-3"><span class="badge badge-light text-muted border shadow-sm px-3 py-1">'.date('d M Y', strtotime($tgl_pesan)).'</span></div>';
                    $tanggal_sebelumnya = $tgl_pesan;
                }

                // Tentukan Class Bubble (Saya atau Lawan Bicara)
                $class_bubble = ($p['id_pengirim'] == $id_saya) ? 'me' : 'you';
                
                // Mulai Bubble
                $html .= '<div class="bubble ' . $class_bubble . '">';

                // Jika ada Gambar
                if(!empty($p['gambar'])){
                    $html .= '<img src="'.base_url('uploads/chat/'.$p['gambar']).'" class="direct-chat-img w-100 mb-2" onclick="window.open(this.src, \'_blank\');">';
                }

                // Teks Pesan & Waktu
                $html .= htmlspecialchars($p['pesan']);
                $html .= '<div class="chat-time">' . date('H:i', strtotime($p['waktu'])) . '</div>';
                
                $html .= '</div>';
            }
        } else {
            $html .= '<div class="text-center mt-5 text-muted"><small><i class="fas fa-lock mr-1"></i> Pesan dilindungi dengan enkripsi end-to-end.</small><br><br>Belum ada percakapan. Mulai sapa sekarang!</div>';
        }

        // 4. KEMBALIKAN KE AJAX
        echo $html;
    }

    public function kirim_pesan()
    {
        $response = ['status' => false, 'msg' => 'Gagal tidak diketahui'];
        $path = './uploads/chat/';

        if (!is_dir($path)) {
            if (!mkdir($path, 0777, true)) {
                echo json_encode(['status' => false, 'msg' => 'Gagal membuat folder uploads/chat. Cek izin folder!']);
                return;
            }
        }

        $sender_id = $this->session->userdata('id');
        $recipient_id = $this->input->post('id_penerima');
        $pesan = $this->input->post('pesan');
        $attachment = NULL;

        // --- VALIDASI AKSES CHAT KHUSUS MAHASISWA ---
        if ($this->session->userdata('role') == 'mahasiswa') {
            $npm = $this->session->userdata('npm');
            $valid_recipients = $this->M_Chat->get_valid_chat_recipients_mhs($npm);

            if (!in_array($recipient_id, $valid_recipients)) {
                echo json_encode(['status' => false, 'msg' => 'Akses ditolak: Anda hanya diizinkan chat dengan Kaprodi saat pengajuan, atau Dospem setelah di-ACC Kaprodi.']);
                return;
            }
        }
        // --- END VALIDASI AKSES CHAT ---

        if (!empty($_FILES['gambar']['name'])) {
            $config['upload_path']   = $path;
            $config['allowed_types'] = 'gif|jpg|png|jpeg|pdf';
            $config['max_size']      = 5120;
            $config['encrypt_name']  = TRUE;

            $this->load->library('upload', $config);

            if ($this->upload->do_upload('gambar')) {
                $uploadData = $this->upload->data();
                $attachment = $uploadData['file_name'];
            } else {
                echo json_encode(['status' => false, 'msg' => $this->upload->display_errors('', '')]);
                return;
            }
        }

        if (empty(trim($pesan)) && empty($attachment)) {
            echo json_encode(['status' => false, 'msg' => 'Pesan atau gambar tidak boleh kosong']);
            return;
        }

        $data_insert = [
            'id_pengirim' => $sender_id,
            'id_penerima' => $recipient_id,
            'pesan'       => $pesan,
            'gambar'      => $attachment
        ];

        if ($this->M_Chat->send_message($data_insert)) {
            echo json_encode(['status' => true, 'msg' => 'Berhasil']);
        } else {
            echo json_encode(['status' => false, 'msg' => 'Gagal insert ke database']);
        }
    }

    public function get_kontak_filtered($allowed_ids) {
    if (empty($allowed_ids)) return [];
    
    $this->db->select('id, nama, foto, role');
    $this->db->from('mstr_akun');
    $this->db->where_in('id', $allowed_ids);
    return $this->db->get()->result_array();
}
}