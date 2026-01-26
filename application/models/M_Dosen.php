<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class M_Dosen extends CI_Model {

    // --- Daftar Bimbingan ---

   // --- Daftar Bimbingan ---
    public function get_mahasiswa_bimbingan($id_dosen)
    {
        $this->db->select('S.id AS id_skripsi, S.judul, M.npm, A.nama AS nama_mhs, A.id AS id_mhs, 
                           P1.nama AS nama_p1, P2.nama AS nama_p2');
        $this->db->from('skripsi S');
        $this->db->join('data_mahasiswa M', 'S.id_mahasiswa = M.id', 'inner');
        $this->db->join('mstr_akun A', 'M.id = A.id', 'inner');
        $this->db->join('mstr_akun P1', 'S.pembimbing1 = P1.id', 'left');
        $this->db->join('mstr_akun P2', 'S.pembimbing2 = P2.id', 'left');
        
        // Filter di mana dosen ini adalah Pembimbing 1 atau Pembimbing 2
        $this->db->where("S.pembimbing1 = $id_dosen OR S.pembimbing2 = $id_dosen");
        
        $this->db->order_by('S.id', 'DESC'); 
        
        return $this->db->get()->result_array();
    }

    // --- Detail Skripsi dan Progres ---

    public function get_skripsi_details($id_skripsi)
    {
        // UPDATE: Menambahkan select M.telepon untuk notifikasi WA
        $this->db->select('S.*, M.npm, M.telepon, A.nama AS nama_mhs'); 
        $this->db->from('skripsi S');
        $this->db->join('data_mahasiswa M', 'S.id_mahasiswa = M.id', 'inner');
        $this->db->join('mstr_akun A', 'M.id = A.id', 'inner');
        $this->db->where('S.id', $id_skripsi);
        return $this->db->get()->row_array();
    }

    public function get_all_progres_skripsi($npm)
    {
        $this->db->order_by('bab', 'ASC');
        return $this->db->get_where('progres_skripsi', ['npm' => $npm])->result_array();
    }
    
    public function get_progres_by_id($id_progres)
    {
        return $this->db->get_where('progres_skripsi', ['id' => $id_progres])->row_array();
    }
    
    public function update_progres($id_progres, $data)
    {
        $this->db->where('id', $id_progres);
        return $this->db->update('progres_skripsi', $data);
    }

    public function count_total_bimbingan($id_dosen)
    {
        // Menghitung total mahasiswa yang dosen ini menjadi P1 atau P2
        $this->db->where("pembimbing1 = $id_dosen OR pembimbing2 = $id_dosen");
        return $this->db->get('skripsi')->num_rows();
    }

    public function get_plagiarisme_result($id_progres)
    {
        return $this->db->get_where('hasil_plagiarisme', ['id_progres' => $id_progres])->row_array();
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
            
            // Ambil data progres terbaru
            $progres_terkini = $this->M_Dosen->get_progres_by_id($id_progres);
            
            $dosen_label = $is_p1 ? 'P1' : 'P2';
            $status_text = ($status_progres == 100) ? 'ACC Penuh' : (($status_progres == 50) ? 'ACC Sebagian' : 'Revisi');
            
            // Catat Log
            $this->M_Log->record('Koreksi', 'Memberikan status **' . $status_text . '** Bab ' . $progres_terkini['bab'] . ' sebagai ' . $dosen_label, $id_progres);

            // ============================================================
            // INTEGRASI FONNTE: FORMAT PESAN BARU (SESUAI REQUEST)
            // ============================================================
            
            $this->load->helper('fonnte');

            $skripsi_info = $this->M_Dosen->get_skripsi_details($id_skripsi);
            $nomor_hp = isset($skripsi_info['telepon']) ? $skripsi_info['telepon'] : null;
            
            if (!empty($nomor_hp)) {
                $nama_mhs = $skripsi_info['nama_mhs'];
                $judul_skripsi = $skripsi_info['judul'];
                $nama_dosen = $this->session->userdata('nama');
                $role_pembimbing = ($is_p1) ? "Pembimbing 1" : "Pembimbing 2";
                $bab = $progres_terkini['bab'];

                // Format Pesan WA Cantik
                $pesan_wa = "🔔 Komentar Progres Skripsi\n";
                $pesan_wa .= "👨‍🎓 Nama: $nama_mhs\n";
                $pesan_wa .= "📘 Judul: $judul_skripsi\n";
                $pesan_wa .= "📄 BAB $bab\n";
                $pesan_wa .= "📝 $nama_dosen ($role_pembimbing) telah memberikan komentar.\n";
                $pesan_wa .= "Silakan cek sistem untuk melihat detailnya.\n\n";
                $pesan_wa .= "> Sent via Sistem Monitoring Skripsi";

                // Kirim
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

    // --- FITUR KAPRODI: Kinerja Dosen per Prodi ---

    private function _filter_dosen_prodi($prodi, $keyword) {
        $this->db->from('mstr_akun A');
        $this->db->join('data_dosen D', 'A.id = D.id');
        $this->db->where('A.role', 'dosen');
        $this->db->where('D.prodi', $prodi); // KUNCI: Filter Prodi

        if ($keyword && $keyword != '') {
            $this->db->group_start();
                $this->db->like('A.nama', $keyword);
                $this->db->or_like('D.nidk', $keyword);
            $this->db->group_end();
        }
    }

    public function count_dosen_by_prodi($prodi, $keyword = NULL)
    {
        $this->_filter_dosen_prodi($prodi, $keyword);
        return $this->db->count_all_results();
    }

    public function get_dosen_by_prodi($prodi, $keyword = NULL, $limit = NULL, $offset = NULL)
    {
        $this->db->select('A.id, A.nama, D.nidk, D.prodi');
        $this->_filter_dosen_prodi($prodi, $keyword);
        
        $this->db->order_by('A.nama', 'ASC');

        if ($limit) {
            $this->db->limit($limit, $offset);
        }
        
        return $this->db->get()->result_array();
    }

    // Tambahkan fungsi ini di dalam class M_Dosen

public function get_all_mahasiswa_prodi($prodi)
{
    // Tambahkan S.id_mahasiswa dan S.status_acc_kaprodi ke select
    $this->db->select('A.nama, A.id as id_user, M.npm, M.angkatan, S.judul, S.status_acc_kaprodi, S.id as id_skripsi, P1.nama AS p1, P2.nama AS p2');
    $this->db->from('data_mahasiswa M');
    $this->db->join('mstr_akun A', 'M.id = A.id', 'inner');
    $this->db->join('skripsi S', 'M.id = S.id_mahasiswa', 'left');
    $this->db->join('mstr_akun P1', 'S.pembimbing1 = P1.id', 'left');
    $this->db->join('mstr_akun P2', 'S.pembimbing2 = P2.id', 'left');
    $this->db->where('M.prodi', $prodi);
    $this->db->where('A.role', 'mahasiswa');
    $this->db->order_by('M.angkatan', 'ASC');
    return $this->db->get()->result_array();
}

public function update_status_judul($id_skripsi, $status)
{
    $this->db->where('id', $id_skripsi);
    return $this->db->update('skripsi', ['status_acc_kaprodi' => $status]);
}

public function get_list_angkatan($prodi)
    {
        $this->db->distinct();
        $this->db->select('angkatan');
        $this->db->from('data_mahasiswa');
        $this->db->where('prodi', $prodi);
        $this->db->order_by('angkatan', 'DESC');
        return $this->db->get()->result_array();
    }

    // Tambahkan fungsi ini di dalam class M_Dosen

public function get_stats_kaprodi($prodi)
{
    $stats = []; // Inisialisasi array biar aman

    // 1. Total Dosen di Prodi
    $stats['total_dosen'] = $this->db->where('prodi', $prodi)->get('data_dosen')->num_rows();

    // 2. Total Mahasiswa di Prodi
    $stats['total_mhs'] = $this->db->where('prodi', $prodi)->get('data_mahasiswa')->num_rows();

    // 3. Statistik Judul (Menunggu ACC)
    $this->db->from('skripsi S');
    $this->db->join('data_mahasiswa M', 'S.id_mahasiswa = M.id');
    $this->db->where('M.prodi', $prodi);
    $this->db->where('S.status_acc_kaprodi', 'menunggu');
    $stats['judul_pending'] = $this->db->count_all_results();

    // 4. Statistik Progress Per BAB
    $stats['bab_stats'] = [];
    for ($i = 1; $i <= 5; $i++) {
        // PERBAIKAN DI SINI: Tambahkan 'P.' sebelum npm
        $this->db->select('COUNT(DISTINCT(P.npm)) as total'); 
        $this->db->from('progres_skripsi P');
        $this->db->join('data_mahasiswa M', 'P.npm = M.npm');
        $this->db->where('M.prodi', $prodi);
        $this->db->where('P.bab', $i);
        $this->db->where('P.progres_dosen1', 100);
        $this->db->where('P.progres_dosen2', 100);
        $result = $this->db->get()->row();
        
        // Pastikan tidak error jika result kosong
        $stats['bab_stats'][$i] = isset($result->total) ? $result->total : 0;
    }

    return $stats;
}
}

// public function insert_plagiarisme_mockup($id_progres)
// {
//     // === MOCKUP LOGIC ===
//     // Ambang batas kelulusan kita tetapkan 25%.
//     $kemiripan = rand(10, 40); // Hasil acak antara 10% dan 40%
//     $status = ($kemiripan <= 25) ? 'Lulus' : 'Tolak';

//     $data = [
//         'id_progres' => $id_progres,
//         'tanggal_cek' => date('Y-m-d'),
//         'persentase_kemiripan' => $kemiripan,
//         'status' => $status,
//         'dokumen_laporan' => 'laporan_plagiarisme_' . $id_progres . '.pdf' // Dummy file
//     ];

//     $this->db->insert('hasil_plagiarisme', $data);
//     return $data; // Kembalikan data yang diinsert
// }
