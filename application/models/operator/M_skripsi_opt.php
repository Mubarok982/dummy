<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class M_skripsi_opt extends CI_Model {

    public function get_all_mahasiswa_skripsi()
    {
        $this->db->select('A.id AS id_mhs, A.nama, M.npm, M.prodi, S.id AS id_skripsi, S.judul, S.pembimbing1, S.pembimbing2, P1.nama AS nama_p1, P2.nama AS nama_p2');
        $this->db->from('mstr_akun A');
        $this->db->join('data_mahasiswa M', 'A.id = M.id', 'inner');
        $this->db->join('skripsi S', 'A.id = S.id_mahasiswa', 'left');
        $this->db->join('mstr_akun P1', 'S.pembimbing1 = P1.id', 'left');
        $this->db->join('mstr_akun P2', 'S.pembimbing2 = P2.id', 'left');
        $this->db->where('A.role', 'mahasiswa');
        $this->db->order_by('M.npm', 'ASC');
        return $this->db->get()->result_array();
    }
    
    public function get_dosen_pembimbing_list()
    {
        $this->db->select('A.id, A.nama, D.nidk');
        $this->db->from('mstr_akun A');
        $this->db->join('data_dosen D', 'A.id = D.id');
        $this->db->where('A.role', 'dosen');
        $this->db->order_by('A.nama', 'ASC');
        return $this->db->get()->result_array();
    }

    public function assign_pembimbing($id_skripsi, $p1, $p2)
    {
        $data = ['pembimbing1' => $p1, 'pembimbing2' => $p2];
        $this->db->where('id', $id_skripsi);
        return $this->db->update('skripsi', $data);
    }

    public function get_plagiarisme_tasks()
    {
        $this->db->select('HP.*, PS.npm, PS.bab, A.nama, S.judul, PS.file AS progres_file');
        $this->db->from('hasil_plagiarisme HP');
        $this->db->join('progres_skripsi PS', 'HP.id_progres = PS.id');
        $this->db->join('data_mahasiswa DM', 'PS.npm = DM.npm');
        $this->db->join('mstr_akun A', 'DM.id = A.id');
        $this->db->join('skripsi S', 'DM.id = S.id_mahasiswa', 'left');
        $this->db->where('HP.status', 'Menunggu');
        $this->db->order_by('HP.tanggal_cek', 'ASC');
        return $this->db->get()->result_array();
    }

    public function update_plagiarisme_status($id, $status)
    {
        $data = ['status' => $status, 'tanggal_cek' => date('Y-m-d')];
        $this->db->where('id', $id);
        return $this->db->update('hasil_plagiarisme', $data);
    }

    public function get_pengajuan_dospem_menunggu()
    {
        $this->db->select('S.*, A_MHS.nama AS nama_mahasiswa, DM.npm, A1.nama AS nama_p1, A2.nama AS nama_p2');
        $this->db->from('skripsi S');
        $this->db->join('mstr_akun A_MHS', 'S.id_mahasiswa = A_MHS.id');
        $this->db->join('data_mahasiswa DM', 'S.id_mahasiswa = DM.id');
        $this->db->join('mstr_akun A1', 'S.pembimbing1 = A1.id', 'left');
        $this->db->join('mstr_akun A2', 'S.pembimbing2 = A2.id', 'left');
        $this->db->where('S.status_acc_kaprodi', 'menunggu');
        $this->db->order_by('S.tgl_pengajuan_judul', 'ASC');
        return $this->db->get()->result_array();
    }
public function update_skripsi($id_skripsi, $data)
    {
        $this->db->where('id', $id_skripsi);
        $update = $this->db->update('skripsi', $data);

        // Jika Judul / Dospem di-ACC Kaprodi
        if ($update && isset($data['status_acc_kaprodi']) && $data['status_acc_kaprodi'] == 'diterima') {
            
            $this->db->select('M.npm, M.prodi');
            $this->db->from('skripsi S');
            $this->db->join('data_mahasiswa M', 'S.id_mahasiswa = M.id');
            $this->db->where('S.id', $id_skripsi);
            $mhs = $this->db->get()->row();

            if ($mhs) {
                // ==============================================================
                // LOGIKA MENGULANG: HAPUS SEMUA FILE FISIK DAN DATABASE
                // ==============================================================
                $this->db->where('id_skripsi', $id_skripsi);
                $this->db->where('status', 'Mengulang');
                $cek_mengulang = $this->db->get('ujian_skripsi')->num_rows();

                if ($cek_mengulang > 0) {
                    // 1. Hapus File Fisik PDF dari Folder Server
                    $files = $this->db->get_where('progres_skripsi', ['npm' => $mhs->npm])->result();
                    foreach ($files as $f) {
                        $path = FCPATH . 'uploads/progres/' . $f->file; 
                        if (!empty($f->file) && file_exists($path) && !is_dir($path)) {
                            unlink($path); // Hapus permanen
                        }
                    }
                    // 2. Hapus seluruh progress lama dari database agar kembali murni ke Bab 1
                    $this->db->where('npm', $mhs->npm);
                    $this->db->delete('progres_skripsi');
                }

                // ==============================================================
                // OTOMATISASI INSERT KE UJIAN_SKRIPSI (SEMPRO)
                // ==============================================================
                $id_jenis_sempro = 1; 
                if ($mhs->prodi == 'Teknik Informatika S1') $id_jenis_sempro = 5; 
                elseif ($mhs->prodi == 'Teknologi Informasi D3') $id_jenis_sempro = 7; 

                // Cari Sempro yang sedang aktif (Abaikan yang statusnya Mengulang)
                $this->db->where('id_skripsi', $id_skripsi);
                $this->db->where('id_jenis_ujian_skripsi', $id_jenis_sempro);
                $this->db->where_in('status', ['Berlangsung', 'Perbaikan', 'Diterima', 'Lulus']);
                $cek_ujian = $this->db->get('ujian_skripsi')->num_rows();
                
                if ($cek_ujian == 0) {
                    $this->db->insert('ujian_skripsi', [
                        'id_skripsi' => $id_skripsi,
                        'id_jenis_ujian_skripsi' => $id_jenis_sempro,
                        'status' => 'Berlangsung',
                        'tanggal_daftar' => date('Y-m-d')
                    ]);
                }
            }
        }
        
        return $update;
    }

    // Pagination methods for ACC DOSPEM
    public function count_pengajuan_dospem($keyword = null, $prodi = null)
    {
        $this->db->from('skripsi S');
        $this->db->join('mstr_akun A_MHS', 'S.id_mahasiswa = A_MHS.id');
        $this->db->join('data_mahasiswa DM', 'S.id_mahasiswa = DM.id');
        $this->db->where('S.status_acc_kaprodi', 'menunggu');
        
        if ($keyword) {
            $this->db->like('A_MHS.nama', $keyword);
            $this->db->or_like('DM.npm', $keyword);
            $this->db->or_like('S.judul', $keyword);
        }
        
        if ($prodi) {
            $this->db->where('DM.prodi', $prodi);
        }
        
        return $this->db->count_all_results();
    }

    public function get_pengajuan_dospem_paginated($keyword = null, $prodi = null, $limit = 15, $offset = 0)
    {
        $this->db->select('S.*, A_MHS.nama AS nama_mahasiswa, DM.npm, A1.nama AS nama_p1, A2.nama AS nama_p2, DM.prodi');
        $this->db->from('skripsi S');
        $this->db->join('mstr_akun A_MHS', 'S.id_mahasiswa = A_MHS.id');
        $this->db->join('data_mahasiswa DM', 'S.id_mahasiswa = DM.id');
        $this->db->join('mstr_akun A1', 'S.pembimbing1 = A1.id', 'left');
        $this->db->join('mstr_akun A2', 'S.pembimbing2 = A2.id', 'left');
        $this->db->where('S.status_acc_kaprodi', 'menunggu');
        
        if ($keyword) {
            $this->db->group_start();
            $this->db->like('A_MHS.nama', $keyword);
            $this->db->or_like('DM.npm', $keyword);
            $this->db->or_like('S.judul', $keyword);
            $this->db->group_end();
        }
        
        if ($prodi) {
            $this->db->where('DM.prodi', $prodi);
        }
        
        $this->db->order_by('S.tgl_pengajuan_judul', 'ASC');
        $this->db->limit($limit, $offset);
        return $this->db->get()->result_array();
    }

    // ====================================================================
    // FUNGSI UPDATE STATUS UJIAN (EKSEKUSI HAPUS FILE FISIK & DATABASE)
    // ====================================================================
    // ====================================================================
    // FUNGSI UPDATE STATUS UJIAN (HAPUS FISIK + DATABASE BERDASARKAN STATUS)
    // ====================================================================
    public function update_status_ujian($id_ujian, $data)
    {
        // 1. Update status ujian ke tabel ujian_skripsi (Perbaikan/Mengulang/Lulus)
        $this->db->where('id', $id_ujian);
        $update = $this->db->update('ujian_skripsi', $data);

        // 2. LOGIKA HAPUS FILE FISIK DARI SERVER & DATABASE
        if ($update && isset($data['status'])) {
            $status_ujian = strtolower(trim($data['status']));

            if (in_array($status_ujian, ['perbaikan', 'mengulang'])) {
                
                // Ambil data ujian & NPM
                $this->db->select('M.npm, U.id_jenis_ujian_skripsi');
                $this->db->from('ujian_skripsi U');
                $this->db->join('skripsi S', 'U.id_skripsi = S.id');
                $this->db->join('data_mahasiswa M', 'S.id_mahasiswa = M.id');
                $this->db->where('U.id', $id_ujian);
                $ujian = $this->db->get()->row();

                if ($ujian && !empty($ujian->npm)) {
                    
                    $is_sempro = in_array($ujian->id_jenis_ujian_skripsi, [1, 5, 7]); 
                    $is_pendadaran = in_array($ujian->id_jenis_ujian_skripsi, [2, 6, 8]);

                    // --- PERSIAPKAN QUERY UNTUK MENCARI FILE YANG MAU DIHAPUS ---
                    $this->db->select('file');
                    $this->db->where('npm', $ujian->npm);
                    
                    if ($status_ujian == 'perbaikan') {
                        if ($is_sempro) {
                            $this->db->where('bab', 3); // Cari Bab 3
                        } elseif ($is_pendadaran) {
                            $this->db->where('bab >=', 6); // Cari Bab 6 (dan atasnya)
                        }
                    }
                    
                    // LANGKAH 1: TARIK NAMA FILE DARI DATABASE SEBELUM DIHAPUS
                    $files_to_delete = $this->db->get('progres_skripsi')->result();

                    // LANGKAH 2: EKSEKUSI HANCURKAN FILE FISIK PDF-NYA!
                    // LANGKAH 2: EKSEKUSI HANCURKAN FILE FISIK PDF-NYA!
                   foreach ($files_to_delete as $item) {

                        if (!empty($item->file)) {

                            // path absolut yang benar
                            $file_path = FCPATH . 'uploads/progres/' . $item->file;

                            // debug
                            echo "<h3>Trying delete:</h3>";
                            echo $file_path;
                            exit;

                            if (file_exists($file_path) && is_file($file_path)) {
                                unlink($file_path);
                            }
                        }
                    }

                    // LANGKAH 3: SETELAH FILE FISIK HANCUR, BARU HAPUS DARI DATABASE
                    $this->db->where('npm', $ujian->npm);
                    
                    if ($status_ujian == 'perbaikan') {
                        if ($is_sempro) {
                            $this->db->where('bab', 3);
                        } elseif ($is_pendadaran) {
                            $this->db->where('bab >=', 6);
                        }
                    }
                    
                    $this->db->delete('progres_skripsi');
                }
            }
        }

        return $update;
    }

public function delete($id)
{
    // ambil data
    $row = $this->Model_mhs->get_by_id($id);

    if ($row) {
        // hapus file fisik
        $path = './uploads/' . $row->file;
        if (file_exists($path)) {
            unlink($path);
        }

        // hapus record database
        $this->Model_mhs->delete($id);

        redirect('mhs/progress');
    } else {
        echo "Data tidak ditemukan";
    }
}
}