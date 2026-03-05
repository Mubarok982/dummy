<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Dashboard extends CI_Controller {

    public function __construct()
    {
        parent::__construct();
        if (!$this->session->userdata('id')) {
            redirect('auth/login'); 
        }
        $this->load->model('M_Akun'); 
        $this->load->model('M_Data'); 
        $this->load->model('M_Mahasiswa'); 
        $this->load->model('M_Dosen');
        $this->load->model('M_Chat'); 
    }

    public function index()
    {   
        $data['title'] = 'Dashboard Utama';
        $role = $this->session->userdata('role');
        $id_user = $this->session->userdata('id');
        $is_kaprodi = $this->session->userdata('is_kaprodi');
        
        $data['role'] = $role;
        $data['is_kaprodi'] = $is_kaprodi;
        $data['unread_chat'] = 0; 
        
        // 1. DATA DEFAULT AGAR VIEW TIDAK ERROR
        $data['statistik'] = [
            'total_mhs' => 0, 'mhs_skripsi' => 0, 'total_dosen' => 0,
            'mhs_ready_sempro' => 0, 'total_bimbingan' => 0,
            'last_bab' => 0, 'judul_status' => '-'
        ];
        
        $data['stats_kaprodi'] = [
            'total_dosen' => 0, 'judul_pending' => 0, 
            'bab_stats' => [1=>0, 2=>0, 3=>0, 4=>0, 5=>0, 6=>0], 
            'total_mhs' => 0
        ];

        // 2. LOGIKA KAPRODI
        if ($is_kaprodi == 1) {
            $prodi = $this->session->userdata('prodi');
            $data['stats_kaprodi'] = $this->M_Dosen->get_stats_kaprodi($prodi);
            $data['dosen_col_class'] = 'col-lg-4 col-sm-12';
        } else {
            $data['dosen_col_class'] = 'col-lg-4 col-sm-6';
        }

        // 3. LOGIKA BERDASARKAN ROLE
        if ($role == 'operator' || $role == 'tata_usaha') {
            $data['statistik']['total_mhs'] = $this->M_Data->count_mahasiswa();
            $data['statistik']['total_dosen'] = $this->M_Data->count_dosen();
            $data['statistik']['mhs_skripsi'] = $this->M_Data->count_mahasiswa_with_skripsi();
            $data['statistik']['mhs_ready_sempro'] = $this->M_Data->count_mahasiswa_ready_sempro();

        } elseif ($role == 'dosen') {
            $data['statistik']['total_bimbingan'] = $this->M_Dosen->count_total_bimbingan($id_user);
            $data['unread_chat'] = $this->M_Chat->count_unread_messages($id_user);

        } elseif ($role == 'mahasiswa') {
            $data['unread_chat'] = $this->M_Chat->count_unread_messages($id_user);

            // A. Ambil Data Skripsi Utama
            $skripsi = $this->M_Mahasiswa->get_skripsi_by_mhs($id_user);
            $data['skripsi'] = $skripsi;
            $data['statistik']['judul_status'] = $skripsi ? 'Sudah Diajukan' : 'Belum Diajukan';
            
            // B. Hitung Bab Terakhir
            $current_bab = 0;
            if ($skripsi) {
                $progres = $this->M_Mahasiswa->get_progres_by_skripsi($skripsi['id']);
                $current_bab = count($progres) > 0 ? end($progres)['bab'] : 0;
            }
            $data['current_bab'] = $current_bab;

            // C. Hitung Persentase & Warna Progress Bar Dinamis Berdasarkan Prodi
            $prodi_mhs = $this->session->userdata('prodi');
            $total_bab = (stripos($prodi_mhs, 'D3') !== false || stripos($prodi_mhs, 'Diploma 3') !== false) ? 5 : 6; 
            $progress_percent = ($total_bab > 0) ? min(100, round(($current_bab / $total_bab) * 100)) : 0;
            
            $bg_class = 'bg-danger';
            if ($progress_percent >= 50) $bg_class = 'bg-warning';
            if ($progress_percent >= 80) $bg_class = 'bg-success';
            
            $data['total_bab'] = $total_bab;
            $data['progress_percent'] = $progress_percent;
            $data['bg_class'] = $bg_class;

            // D. Tentukan Label Status Bimbingan
            $label = "BELUM PENGAJUAN";
            if ($skripsi) {
                // Cek model dulu (Jika punya method khusus)
                if(method_exists($this->M_Mahasiswa, 'get_status_bimbingan_terbaru')) {
                    $label_db = $this->M_Mahasiswa->get_status_bimbingan_terbaru($skripsi['id']);
                    if(!empty($label_db)) $label = $label_db;
                }
                
                // Fallback Logika jika string kosong
                if ($label == "BELUM PENGAJUAN" || empty($label)) {
                    $label = "DALAM BIMBINGAN";
                    if (strtolower($skripsi['status_acc_kaprodi']) == 'ditolak') {
                        $label = "DITOLAK";
                    } else {
                        $ujian = $this->M_Mahasiswa->get_status_ujian_terakhir($skripsi['id']);
                        if ($ujian && strtolower($ujian['status']) == 'mengulang') {
                            $label = "MENGULANG";
                        }
                    }
                }
            }
            $data['status_bimbingan'] = $label;

            // =========================================================
            // E. GABUNGKAN RIWAYAT JUDUL (Tabel Skripsi + Tabel Histori)
            // =========================================================
          // =========================================================
            // E. GABUNGKAN RIWAYAT JUDUL (Tabel Skripsi + Tabel Histori)
            // =========================================================
            $all_history = [];

            // E.1. Ambil SEMUA Data dari tabel Skripsi (Bisa jadi mahasiswa punya beberapa baris skripsi karena pengajuan ditolak di masa lalu)
            $this->db->select('s.id, s.judul, s.tema, s.tgl_pengajuan_judul, s.skema, s.status_acc_kaprodi, d1.nama as nama_p1, d2.nama as nama_p2');
            $this->db->from('skripsi s');
            $this->db->join('mstr_akun d1', 's.pembimbing1 = d1.id', 'left');
            $this->db->join('mstr_akun d2', 's.pembimbing2 = d2.id', 'left');
            $this->db->where('s.id_mahasiswa', $id_user);
            $this->db->order_by('s.id', 'DESC'); // Wajib Descending agar ID terbesar ada di index ke-0
            $skripsi_list = $this->db->get()->result_array();

            // Kita harus mencari tahu ID Skripsi TERBESAR (Aktif sesungguhnya)
            $id_skripsi_aktif = !empty($skripsi_list) ? $skripsi_list[0]['id'] : 0;

            foreach ($skripsi_list as $s) {
                // Cek apakah baris ini adalah Skripsi yang benar-benar aktif (ID terbesar)
                $is_active_row = ($s['id'] == $id_skripsi_aktif);

                $all_history[] = [
                    'judul' => $s['judul'],
                    'tema' => $s['tema'],
                    'tgl_pengajuan_judul' => $s['tgl_pengajuan_judul'],
                    'nama_p1' => $s['nama_p1'],
                    'nama_p2' => $s['nama_p2'],
                    'skema' => $s['skema'],
                    'status_asli' => strtolower($s['status_acc_kaprodi']),
                    'is_active' => $is_active_row // True hanya untuk 1 baris
                ];

              // E.2. Ambil Histori Judul Lama HANYA dari ID Skripsi yang bersangkutan
                // TAMBAHKAN dibuat_pada di select query
                $this->db->select('judul, tema, tgl_pengajuan_judul, dibuat_pada');
                $this->db->from('histori_judul_skripsi');
                $this->db->where('id_skripsi', $s['id']);
                $histories = $this->db->get()->result_array();

                foreach ($histories as $h) {
                    $all_history[] = [
                        'judul' => $h['judul'],
                        'tema' => $h['tema'],
                        // Gunakan dibuat_pada agar kelihatan kapan judul ini diganti jadi riwayat
                        'tgl_pengajuan_judul' => !empty($h['dibuat_pada']) ? $h['dibuat_pada'] : $h['tgl_pengajuan_judul'],
                        'nama_p1' => $s['nama_p1'], 
                        'nama_p2' => $s['nama_p2'], 
                        'skema' => $s['skema'],
                        'status_asli' => 'riwayat',
                        'is_active' => false 
                    ];
                }
            }

            // E.3. Urutkan: Aktif SELALU di atas, sisanya urut berdasarkan tanggal terbaru
            usort($all_history, function($a, $b) {
                // Aturan 1: Prioritas mutlak untuk status Aktif
                if ($a['is_active'] && !$b['is_active']) return -1; // A naik ke atas
                if (!$a['is_active'] && $b['is_active']) return 1;  // B naik ke atas
                
                // Aturan 2: Jika sama-sama riwayat (lama), urutkan dari tanggal terbaru
                $timeA = strtotime($a['tgl_pengajuan_judul']);
                $timeB = strtotime($b['tgl_pengajuan_judul']);
                
                return $timeB <=> $timeA; // Descending (terbaru ke terlama)
            });

            // E.4. Rapikan Format Status dan Warna Badge Untuk View
            foreach ($all_history as &$row) {
                if (!$row['is_active']) {
                    // Jika status aslinya ditolak (dari tabel skripsi lama), tampilkan Ditolak. 
                    // Jika dari histori, tampilkan Diganti.
                    if ($row['status_asli'] == 'ditolak') {
                        $row['badge_color'] = 'danger';
                        $row['status_label'] = 'DITOLAK (LAMA)';
                    } else {
                        $row['badge_color'] = 'secondary';
                        $row['status_label'] = 'DIGANTI (RIWAYAT)';
                    }
                    $row['row_class'] = 'bg-light';
                } else {
                    $st = $row['status_asli'];
                    if ($st == 'diterima') $row['badge_color'] = 'success';
                    elseif ($st == 'ditolak') $row['badge_color'] = 'danger';
                    elseif ($st == 'menunggu') $row['badge_color'] = 'warning';
                    else $row['badge_color'] = 'secondary';
                    
                    $row['status_label'] = strtoupper($st);
                    $row['row_class'] = '';
                }
            }

            $data['riwayat_judul'] = $all_history;
        }
        
        $this->load->view('template/header', $data);
        $this->load->view('template/sidebar', $data);
        $this->load->view('dashboard_view', $data); 
        $this->load->view('template/footer');
    }
}