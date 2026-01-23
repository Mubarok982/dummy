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
    
    if ($role == 'mahasiswa') {
        // Ambil hanya ID yang diizinkan (Kaprodi & Pembimbing jika sudah ACC)
        $allowed_ids = $this->M_Chat->get_valid_chat_recipients_mhs($npm);
        $data['kontak'] = $this->M_Chat->get_kontak_filtered($allowed_ids);
    } else {
        // Role dosen/operator tetap melihat kontak normal
        $data['kontak'] = $this->M_Chat->get_kontak_chat($id_user, $role);
    }

    $this->load->view('template/header', $data);
    $this->load->view('template/sidebar', $data);
    $this->load->view('v_chat', $data);
    $this->load->view('template/footer');
}

    public function load_pesan()
    {
        $id_saya = $this->session->userdata('id');
        $id_lawan = $this->input->post('id_lawan');

        $chat = $this->M_Chat->get_chat($id_saya, $id_lawan);
        
        $html = '';
        foreach ($chat as $c) {
            $is_me = ($c['id_pengirim'] == $id_saya);
            $posisi = $is_me ? 'right' : 'left';
            
            $bubbleColor = $is_me ? '#dcf8c6' : '#ffffff';
            $align = $is_me ? 'margin-left: auto;' : 'margin-right: auto;';
            
            $html .= '<div class="direct-chat-msg '.$posisi.'">';
            $html .= '  <div class="direct-chat-infos clearfix">';
            $html .= '    <span class="direct-chat-timestamp float-'.$posisi.'">'.date('H:i', strtotime($c['waktu'])).'</span>';
            $html .= '  </div>';
            
            $html .= '  <div class="direct-chat-text" style="background-color: '.$bubbleColor.'; border: 1px solid #ddd; color: #333; width: fit-content; max-width: 75%; '.$align.'">';
            
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