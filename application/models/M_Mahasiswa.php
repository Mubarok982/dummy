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
        $this->db->where('id_mahasiswa', $id_mahasiswa);
        $update_data = [
            'tema'               => $data['tema'],
            'judul'              => $data['judul'],
            'pembimbing1'        => $data['pembimbing1'],
            'pembimbing2'        => $data['pembimbing2'],
            'status_acc_kaprodi' => 'menunggu' // Status di-reset agar Kaprodi bisa cek ulang
        ];
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

        // Langkah 2: Ambil progres berdasarkan NPM dan tambahkan judul
        $this->db->order_by('bab', 'ASC');
        $progres = $this->db->get_where('progres_skripsi', ['npm' => $npm_mahasiswa])->result_array();

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
        $this->db->where('id', $id_progres);
        return $this->db->update('progres_skripsi', $data);
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

    // Ambil Nomor HP Pembimbing 1 & 2
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
        // dd1 = data_dosen (ambil telepon), ma1 = mstr_akun (ambil nama)
        $this->db->join('data_dosen dd1', 's.pembimbing1 = dd1.id', 'left');
        $this->db->join('mstr_akun ma1', 's.pembimbing1 = ma1.id', 'left');
        
        // JOIN UNTUK PEMBIMBING 2
        // dd2 = data_dosen (ambil telepon), ma2 = mstr_akun (ambil nama)
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