<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class M_Mahasiswa extends CI_Model {

    // GANTI fungsi get_skripsi_by_mhs yang lama dengan ini:
    public function get_skripsi_by_mhs($id_mahasiswa)
    {
        $this->db->select('S.*, M.npm, A_mhs.nama AS nama_mahasiswa, A_mhs.foto AS foto_mahasiswa, A1.nama AS nama_p1, A2.nama AS nama_p2');
        $this->db->from('skripsi S');
        $this->db->join('data_mahasiswa M', 'S.id_mahasiswa = M.id'); 
        $this->db->join('mstr_akun A_mhs', 'S.id_mahasiswa = A_mhs.id', 'left');
        $this->db->join('mstr_akun A1', 'S.pembimbing1 = A1.id', 'left');
        $this->db->join('mstr_akun A2', 'S.pembimbing2 = A2.id', 'left');
        $this->db->where('S.id_mahasiswa', $id_mahasiswa);
        
        // PENTING: Ambil yang paling baru berdasarkan ID atau Tanggal
        $this->db->order_by('S.id', 'DESC'); 
        
        return $this->db->get()->row_array(); // Hanya ambil 1 data terbaru
    }

    // TAMBAHKAN fungsi baru ini untuk Riwayat Dashboard:
    public function get_all_skripsi_by_mhs($id_mahasiswa)
    {
        $this->db->select('S.*, A1.nama AS nama_p1, A2.nama AS nama_p2');
        $this->db->from('skripsi S');
        $this->db->join('mstr_akun A1', 'S.pembimbing1 = A1.id', 'left');
        $this->db->join('mstr_akun A2', 'S.pembimbing2 = A2.id', 'left');
        $this->db->where('S.id_mahasiswa', $id_mahasiswa);
        
        // Urutkan dari yang terbaru
        $this->db->order_by('S.tgl_pengajuan_judul', 'DESC');
        $this->db->order_by('S.id', 'DESC');
        
        return $this->db->get()->result_array(); // Ambil BANYAK data (result_array)
    }
    
    public function insert_skripsi($data)
    {
        return $this->db->insert('skripsi', $data);
    }
    
    public function update_skripsi_judul($id_mahasiswa, $data)
    {
        // ==============================================================
        // 1. Ambil data mahasiswa + id skripsi
        // ==============================================================
        $this->db->select('M.npm, S.id as id_skripsi');
        $this->db->from('data_mahasiswa M');
        $this->db->join('skripsi S', 'M.id = S.id_mahasiswa', 'left');
        $this->db->where('M.id', $id_mahasiswa);
        $mhs = $this->db->get()->row();

        // KOREKSI UTAMA: Fitur Penghancur Data (unlink & delete) DIBASMI DARI SINI
        // Membiarkan data lama tetap utuh di database agar menjadi arsip "Riwayat Lama"

        // ==============================================================
        // 2. Simpan judul baru (Siklus Baru dimulai)
        // ==============================================================
        $update_data = [
            'tema'               => $data['tema'],
            'judul'              => $data['judul'],
            'pembimbing1'        => $data['pembimbing1'],
            'pembimbing2'        => $data['pembimbing2'],
            'status_acc_kaprodi' => 'menunggu',
            'tgl_pengajuan_judul'=> date('Y-m-d H:i:s')
        ];

        $this->db->where('id_mahasiswa', $id_mahasiswa);
        return $this->db->update('skripsi', $update_data);
    }
    
    public function get_progres_by_skripsi($id_skripsi)
    {
        // Langkah 1: Cari tahu NPM dan Judul pemilik skripsi
        $this->db->select('M.npm, S.judul');
        $this->db->from('skripsi S');
        $this->db->join('data_mahasiswa M', 'S.id_mahasiswa = M.id');
        $this->db->where('S.id', $id_skripsi);

        $data_mhs = $this->db->get()->row_array();

        if (!$data_mhs) {
            return [];
        }

        $npm_mahasiswa = $data_mhs['npm'];
        $judul_skripsi = $data_mhs['judul'];

        // Langkah 2: Ambil progres berdasarkan NPM (Perbaikan: dan ID Skripsi)
        $this->db->where('npm', $npm_mahasiswa);
        
        // PENTING: Hanya ambil file dari id_skripsi spesifik ini agar tidak bercampur
        if ($this->db->field_exists('id_skripsi', 'progres_skripsi')) {
            $this->db->where('id_skripsi', $id_skripsi);
        }
        
        $this->db->order_by('bab', 'ASC');
        $progres = $this->db->get('progres_skripsi')->result_array();

        // Tambahkan judul ke setiap row progres
        foreach ($progres as &$p) {
            $p['judul'] = $judul_skripsi;
        }

        return $progres;
    }
    
    public function insert_progres($data)
    {
        return $this->db->insert('progres_skripsi', $data);
    }
    
    public function update_progres($id_progres, $data)
    {
        // 1. Update progres ke database seperti biasa
        $this->db->where('id', $id_progres);
        $update = $this->db->update('progres_skripsi', $data);

        // 2. --- OTOMATISASI INSERT KE UJIAN PENDADARAN ---
        if ($update) {
            // Ambil data progres yang baru saja diupdate
            $progres = $this->db->get_where('progres_skripsi', ['id' => $id_progres])->row();
            
            if ($progres) {
                // Pastikan dosen 1 & 2 sudah ACC 100%
                if ($progres->progres_dosen1 == 100 && $progres->progres_dosen2 == 100) {
                    
                    // Ambil id_skripsi dan prodi dari mahasiswa
                    $this->db->select('S.id as id_skripsi, M.prodi');
                    $this->db->from('skripsi S');
                    $this->db->join('data_mahasiswa M', 'S.id_mahasiswa = M.id');
                    $this->db->where('M.npm', $progres->npm);
                    // KUNCI UTAMA FIX BUG: Wajib order by DESC agar mengambil ID Judul TERBARU!
                    $this->db->order_by('S.id', 'DESC'); 
                    $mhs = $this->db->get()->row();

                    if ($mhs) {
                        // Tentukan ID Jenis Ujian Pendadaran & Max Bab sesuai prodi
                        $id_jenis_pendadaran = 2; // Default
                        $max_bab = 6;

                        if (stripos($mhs->prodi, 'Teknik Informatika S1') !== false) {
                            $id_jenis_pendadaran = 6;
                            $max_bab = 6;
                        } elseif (stripos($mhs->prodi, 'D3') !== false || stripos($mhs->prodi, 'Diploma') !== false) {
                            $id_jenis_pendadaran = 8;
                            $max_bab = 5;
                        }

                        // Pastikan otomatisasi hanya jalan kalau Bab yang di-ACC adalah Bab Maksimal (5/6)
                        if ($progres->bab >= $max_bab) {
                            
                            // Cek apakah pendadaran yang aktif untuk id_skripsi TERBARU ini sudah ada
                            $this->db->where('id_skripsi', $mhs->id_skripsi);
                            $this->db->where('id_jenis_ujian_skripsi', $id_jenis_pendadaran);
                            $this->db->where_in('status', ['Berlangsung', 'Perbaikan', 'Diterima', 'Lulus']);
                            $cek_pendadaran = $this->db->get('ujian_skripsi')->num_rows();

                            if ($cek_pendadaran == 0) {
                                // Insert otomatis ke ujian_skripsi untuk PENDADARAN
                                // Sekarang 100% masuk ke ID Skripsi yang BARU
                                $this->db->insert('ujian_skripsi', [
                                    'id_skripsi' => $mhs->id_skripsi,
                                    'id_jenis_ujian_skripsi' => $id_jenis_pendadaran,
                                    'status' => 'Berlangsung',
                                    'tanggal_daftar' => date('Y-m-d')
                                ]);
                            }
                        }
                    }
                }
            }
        }
        
        return $update;
    }

    public function simpan_draft_skripsi($data)
    {
        // Menggunakan kolom 'file' (disesuaikan dengan upload_progres_bab agar konsisten)
        return $this->db->insert('progres_skripsi', $data);
    }

    public function get_riwayat_progres($npm)
    {
        $this->db->where('npm', $npm);
        $this->db->order_by('bab', 'ASC');
        // Pastikan kolom tgl_upload atau created_at ada di database
        $this->db->order_by('tgl_upload', 'DESC'); 
        $query = $this->db->get('progres_skripsi');
        return $query->result();
    }

    // Ambil Nomor HP & Nama Pembimbing 1 & 2
    public function get_kontak_pembimbing_by_skripsi($id_skripsi)
    {
        $this->db->select('
            dd1.telepon as hp_p1, 
            ma1.nama as nama_p1, 
            dd2.telepon as hp_p2, 
            ma2.nama as nama_p2
        ');
        $this->db->from('skripsi s');
        
        // JOIN UNTUK PEMBIMBING 1
        $this->db->join('data_dosen dd1', 's.pembimbing1 = dd1.id', 'left');
        $this->db->join('mstr_akun ma1', 's.pembimbing1 = ma1.id', 'left');
        
        // JOIN UNTUK PEMBIMBING 2
        $this->db->join('data_dosen dd2', 's.pembimbing2 = dd2.id', 'left');
        $this->db->join('mstr_akun ma2', 's.pembimbing2 = ma2.id', 'left');
        
        $this->db->where('s.id', $id_skripsi);
        return $this->db->get()->row_array();
    }

    // --- AMBIL STATUS DARI TABEL UJIAN_SKRIPSI ---
    public function get_status_ujian_terakhir($id_skripsi)
    {
        // Cukup select status saja
        $this->db->select('status'); 
        $this->db->from('ujian_skripsi'); 
        $this->db->where('id_skripsi', $id_skripsi);
        
        // Ambil yang paling terakhir
        $this->db->order_by('id', 'DESC'); 
        $this->db->limit(1);
        
        return $this->db->get()->row_array();
    }
}