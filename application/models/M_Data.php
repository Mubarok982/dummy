    <?php
defined('BASEPATH') OR exit('No direct script access allowed');

class M_Data extends CI_Model
{

   
// --- MODIFIKASI BAGIAN INI DI M_Data.php ---

    // 1. Helper Private untuk Filter (Agar tidak koding ulang)
    private function _filter_users_query($role, $prodi, $keyword)
    {
        $this->db->from('mstr_akun A');
        $this->db->join('data_dosen D', 'A.id = D.id', 'left');
        $this->db->join('data_mahasiswa M', 'A.id = M.id', 'left');

        if ($role && $role != '') {
            $this->db->where('A.role', $role);
        }

        if ($prodi && $prodi != '') {
            $this->db->group_start();
                $this->db->where('D.prodi', $prodi);
                $this->db->or_where('M.prodi', $prodi);
            $this->db->group_end();
        }

        if ($keyword && $keyword != '') {
            $this->db->group_start();
                $this->db->like('A.nama', $keyword);
                $this->db->or_like('A.username', $keyword);
                $this->db->or_like('D.nidk', $keyword);
                $this->db->or_like('M.npm', $keyword);
            $this->db->group_end();
        }
    }

    // 2. Method Hitung Total Data (Untuk Pagination)
    public function count_all_users($role = NULL, $prodi = NULL, $keyword = NULL)
    {
        $this->_filter_users_query($role, $prodi, $keyword);
        return $this->db->count_all_results();
    }

    // 3. Method Ambil Data dengan Limit & Offset
    public function get_all_users_with_details($role = NULL, $prodi = NULL, $keyword = NULL, $limit = NULL, $offset = NULL)
    {
        $this->db->select('A.id, A.username, A.nama, A.role, D.nidk, M.npm, M.prodi AS prodi_mhs, D.prodi AS prodi_dsn');
        
        $this->_filter_users_query($role, $prodi, $keyword);
        
        $this->db->order_by('A.role', 'ASC');
        $this->db->order_by('A.nama', 'ASC');

        // Tambahkan Limit untuk Paginasi
        if ($limit) {
            $this->db->limit($limit, $offset);
        }
        
        return $this->db->get()->result_array();
    }

    public function get_user_by_id($id)
    {
        $this->db->select('A.*, D.*, M.*, D.prodi AS prodi_dsn, M.prodi AS prodi_mhs');
        
        $this->db->from('mstr_akun A');
        $this->db->join('data_dosen D', 'A.id = D.id', 'left');
        $this->db->join('data_mahasiswa M', 'A.id = M.id', 'left');
        $this->db->where('A.id', $id);
        return $this->db->get()->row_array();
    }

    // Fungsi untuk menambah data akun dan detailnya
    public function insert_user($akun_data, $role, $detail_data = NULL)
    {
        $this->db->trans_start();

        // 1. Masukkan ke mstr_akun
        $this->db->insert('mstr_akun', $akun_data);
        $id = $this->db->insert_id();

        // 2. Masukkan ke tabel detail (data_dosen atau data_mahasiswa)
        if ($detail_data && ($role == 'dosen' || $role == 'mahasiswa')) {
            $detail_data['id'] = $id;

            if ($role == 'dosen') {
                $this->db->insert('data_dosen', $detail_data);
            } else if ($role == 'mahasiswa') {
                $this->db->insert('data_mahasiswa', $detail_data);
            }
        }

        $this->db->trans_complete();
        return $this->db->trans_status();
    }

    public function update_user($id, $akun_data, $role, $detail_data = NULL)
    {
        $this->db->trans_start();

        // 1. Update mstr_akun
        $this->db->where('id', $id);
        $this->db->update('mstr_akun', $akun_data);

        // 2. Update atau Insert ke tabel detail
        if ($detail_data && ($role == 'dosen' || $role == 'mahasiswa')) {
            $this->db->where('id', $id);
            // Cek apakah data detail sudah ada, jika belum, lakukan insert.
            if ($role == 'dosen' && $this->db->get('data_dosen')->num_rows() > 0) {
                $this->db->where('id', $id)->update('data_dosen', $detail_data);
            } elseif ($role == 'mahasiswa' && $this->db->get('data_mahasiswa')->num_rows() > 0) {
                $this->db->where('id', $id)->update('data_mahasiswa', $detail_data);
            } else {
                $detail_data['id'] = $id;
                if ($role == 'dosen')
                    $this->db->insert('data_dosen', $detail_data);
                if ($role == 'mahasiswa')
                    $this->db->insert('data_mahasiswa', $detail_data);
            }
        }

        $this->db->trans_complete();
        return $this->db->trans_status();
    }

    // Fungsi delete akun (menghapus dari semua tabel terkait)
    public function delete_user($id)
    {
        $this->db->where('id', $id);
        return $this->db->delete('mstr_akun');
    }


    // --- Penugasan Pembimbing ---

    public function get_all_mahasiswa_skripsi()
    {
        // PERBAIKAN DI SINI: Menambahkan S.pembimbing1 dan S.pembimbing2
        $this->db->select('A.id AS id_mhs, A.nama, M.npm, M.prodi, S.id AS id_skripsi, S.judul, 
                           S.pembimbing1, S.pembimbing2, 
                           P1.nama AS nama_p1, P2.nama AS nama_p2');

        $this->db->from('mstr_akun A');
        $this->db->join('data_mahasiswa M', 'A.id = M.id', 'inner');
        $this->db->join('skripsi S', 'A.id = S.id_mahasiswa', 'left');
        $this->db->join('mstr_akun P1', 'S.pembimbing1 = P1.id', 'left');
        $this->db->join('mstr_akun P2', 'S.pembimbing2 = P2.id', 'left');
        $this->db->where('A.role', 'mahasiswa');
        $this->db->order_by('M.npm', 'ASC');
        return $this->db->get()->result_array();
    }

    // --- UPDATE: Kinerja Dosen dengan Filter & Pagination ---

    // Helper private untuk query dosen
    private function _filter_dosen_query($keyword) {
        $this->db->from('mstr_akun A');
        $this->db->join('data_dosen D', 'A.id = D.id');
        $this->db->where('A.role', 'dosen');

        if ($keyword && $keyword != '') {
            $this->db->group_start();
                $this->db->like('A.nama', $keyword);
                $this->db->or_like('D.nidk', $keyword);
            $this->db->group_end();
        }
    }

    // 1. Hitung Total Dosen (Untuk Pagination)
    public function count_dosen_pembimbing($keyword = NULL)
    {
        $this->_filter_dosen_query($keyword);
        return $this->db->count_all_results();
    }

    // 2. Ambil List Dosen (Support Limit & Offset)
    public function get_dosen_pembimbing_list($keyword = NULL, $limit = NULL, $offset = NULL)
    {
        $this->db->select('A.id, A.nama, D.nidk');
        $this->_filter_dosen_query($keyword);
        
        $this->db->order_by('A.nama', 'ASC');

        if ($limit) {
            $this->db->limit($limit, $offset);
        }
        
        return $this->db->get()->result_array();
    }

    public function assign_pembimbing($id_skripsi, $pembimbing1, $pembimbing2)
    {
        $data = [
            'pembimbing1' => $pembimbing1,
            'pembimbing2' => $pembimbing2
        ];
        $this->db->where('id', $id_skripsi);
        return $this->db->update('skripsi', $data);
    }

    public function get_laporan_progres_semua_mhs()
    {
        $this->db->select('A.nama, M.npm, M.prodi, S.judul, S.id AS id_skripsi, S.status_acc_kaprodi, S.status_sempro, P1.nama AS p1, P2.nama AS p2');
        $this->db->from('mstr_akun A');
        $this->db->join('data_mahasiswa M', 'A.id = M.id', 'inner');
        $this->db->join('skripsi S', 'A.id = S.id_mahasiswa', 'left');
        $this->db->join('mstr_akun P1', 'S.pembimbing1 = P1.id', 'left');
        $this->db->join('mstr_akun P2', 'S.pembimbing2 = P2.id', 'left');
        $this->db->where('A.role', 'mahasiswa');
        $this->db->order_by('M.prodi, A.nama', 'ASC');

        $result = $this->db->get()->result_array();

        foreach ($result as $key => $mhs) {
            $progres = $this->db->order_by('bab', 'DESC')->limit(1)->get_where('progres_skripsi', ['npm' => $mhs['npm']])->row_array();

            // Determine unified status_bimbingan (and badge class)
            $status_label = 'BIMBINGAN';
            $status_class = 'badge-primary';

            $status_acc = isset($mhs['status_acc_kaprodi']) ? $mhs['status_acc_kaprodi'] : '';
            $status_sempro = isset($mhs['status_sempro']) ? $mhs['status_sempro'] : '';

            // Check last ujian status for this skripsi (if any)
            $last_ujian = [];
            if (!empty($mhs['id_skripsi'])) {
                $last_ujian = $this->db->select('status')->from('ujian_skripsi')->where('id_skripsi', $mhs['id_skripsi'])->order_by('id', 'DESC')->limit(1)->get()->row_array();
            }

            // If judul ditolak or last ujian says Mengulang => MENGULANG
            if (strtolower($status_acc) == 'ditolak' || (!empty($last_ujian) && strtolower($last_ujian['status']) == 'mengulang')) {
                $status_label = 'MENGULANG';
                $status_class = 'badge-danger';
            }
            // Menunggu cek plagiarisme
            elseif ($status_sempro == 'Menunggu Plagiarisme') {
                $status_label = 'MENUNGGU CEK PLAGIARISME';
                $status_class = 'badge-secondary';
            }
            // If we have progres info, use it to refine status
            elseif ($progres) {
                $p1 = isset($progres['progres_dosen1']) ? intval($progres['progres_dosen1']) : 0;
                $p2 = isset($progres['progres_dosen2']) ? intval($progres['progres_dosen2']) : 0;
                $bab = isset($progres['bab']) ? intval($progres['bab']) : 0;

                $max_bab = 6;
                if (stripos($mhs['prodi'] ?? '', 'D3') !== false) $max_bab = 5;

                if ($p1 === 100 && $p2 === 100) {
                    if ($bab >= $max_bab) {
                        $status_label = 'SIAP PENDADARAN';
                        $status_class = 'badge-success';
                    } elseif ($bab == 3) {
                        $status_label = 'SIAP SEMPRO';
                        $status_class = 'badge-info';
                    } elseif ($bab >= 4) {
                        $status_label = 'BIMBINGAN';
                        $status_class = 'badge-primary';
                    } else {
                        $status_label = 'BIMBINGAN';
                        $status_class = 'badge-primary';
                    }
                } else {
                    $status_label = 'BIMBINGAN';
                    $status_class = 'badge-primary';
                }
            }
            // Fallbacks based on skripsi flags
            elseif ($status_sempro == 'Siap Pendadaran') {
                $status_label = 'SIAP PENDADARAN';
                $status_class = 'badge-success';
            } elseif ($status_sempro == 'Siap Sempro') {
                $status_label = 'SIAP SEMPRO';
                $status_class = 'badge-info';
            }

            $result[$key]['status_bimbingan'] = $status_label;
            $result[$key]['status_class'] = $status_class;

            if ($progres) {
                $result[$key]['last_bab'] = 'BAB ' . $progres['bab'];
            } else {
                $result[$key]['last_bab'] = 'Belum Mulai';
            }
        }
        return $result;
    }

    public function count_users_by_role($role)
    {
        $this->db->where('role', $role);
        return $this->db->get('mstr_akun')->num_rows();
    }

    public function count_mahasiswa()
    {
        return $this->db->get_where('mstr_akun', ['role' => 'mahasiswa'])->num_rows();
    }

    public function count_dosen()
    {
        return $this->db->get_where('mstr_akun', ['role' => 'dosen'])->num_rows();
    }

    public function count_mahasiswa_with_skripsi()
    {
        $this->db->select('COUNT(DISTINCT id_mahasiswa) as total');
        return $this->db->get('skripsi')->row()->total;
    }

    public function count_mahasiswa_ready_sempro()
    {
        $sql = "SELECT COUNT(DISTINCT ps.npm) AS total
                FROM progres_skripsi ps
                JOIN skripsi s ON ps.npm = (SELECT npm FROM data_mahasiswa WHERE id = s.id_mahasiswa)
                WHERE ps.bab = 3 AND ps.progres_dosen1 = 100 AND ps.progres_dosen2 = 100";
        return $this->db->query($sql)->row()->total;
    }

    public function get_all_plagiarisme_bab_1()
    {
        $this->db->select('
            p.id, 
            p.bab, 
            p.file as progres_file, 
            p.tgl_upload, 
            p.tgl_verifikasi,
            p.status_plagiasi, 
            p.persentase_kemiripan,
            a.nama, 
            m.npm, 
            s.judul
        ');
        
        $this->db->from('progres_skripsi p');
        $this->db->join('data_mahasiswa m', 'p.npm = m.npm');
        $this->db->join('mstr_akun a', 'm.id = a.id');
        $this->db->join('skripsi s', 's.id_mahasiswa = m.id', 'left');
        
        // HANYA BAB 1 
        $this->db->where('p.bab', 1);
        $this->db->order_by("CASE WHEN p.status_plagiasi = 'Menunggu' THEN 0 ELSE 1 END", "ASC");
        $this->db->order_by('p.tgl_upload', 'DESC');

        return $this->db->get()->result_array();
    }

    // 3. UPDATE DATA
    public function update_plagiarisme($id, $data)
    {
        $this->db->where('id', $id);
        return $this->db->update('progres_skripsi', $data);
    }
    // --- FUNGSI BARU: Monitoring Progres dengan Filter & Pagination ---

    // 1. Helper Private untuk Query Filter
    private function _filter_laporan_query($prodi, $keyword)
    {
        $this->db->from('mstr_akun A');
        $this->db->join('data_mahasiswa M', 'A.id = M.id', 'inner');
        $this->db->join('skripsi S', 'A.id = S.id_mahasiswa', 'left');
        $this->db->join('mstr_akun P1', 'S.pembimbing1 = P1.id', 'left');
        $this->db->join('mstr_akun P2', 'S.pembimbing2 = P2.id', 'left');
        $this->db->where('A.role', 'mahasiswa');

        // Filter Prodi
        if ($prodi && $prodi != '') {
            $this->db->where('M.prodi', $prodi);
        }

        // Filter Keyword (Nama, NPM, atau Judul Skripsi)
        if ($keyword && $keyword != '') {
            $this->db->group_start();
                $this->db->like('A.nama', $keyword);
                $this->db->or_like('M.npm', $keyword);
                $this->db->or_like('S.judul', $keyword);
            $this->db->group_end();
        }
    }

    // 2. Hitung Total Data (Untuk Paginasi)
    public function count_laporan_progres($prodi = NULL, $keyword = NULL)
    {
        $this->_filter_laporan_query($prodi, $keyword);
        return $this->db->count_all_results();
    }

    // 3. Ambil Data dengan Limit
    public function get_laporan_progres($prodi = NULL, $keyword = NULL, $limit = NULL, $offset = NULL)
    {
        $this->db->select('A.nama, M.npm, M.prodi, S.judul, P1.nama AS p1, P2.nama AS p2');
        
        $this->_filter_laporan_query($prodi, $keyword);
        
        $this->db->order_by('M.prodi', 'ASC');
        $this->db->order_by('A.nama', 'ASC');

        if ($limit) {
            $this->db->limit($limit, $offset);
        }
        
        $result = $this->db->get()->result_array();
        
        // Logic Bab Terakhir (Looping hanya pada 15 data yang diambil)
        foreach ($result as $key => $mhs) {
            $progres = $this->db->order_by('bab', 'DESC')->limit(1)->get_where('progres_skripsi', ['npm' => $mhs['npm']])->row_array();
            
            if ($progres) {
                $result[$key]['last_bab'] = 'BAB ' . $progres['bab'];
                $result[$key]['status_p1'] = $progres['nilai_dosen1'];
                $result[$key]['status_p2'] = $progres['nilai_dosen2'];
            } else {
                $result[$key]['last_bab'] = 'Belum Mulai';
                $result[$key]['status_p1'] = '-';
                $result[$key]['status_p2'] = '-';
            }
        }
        return $result;
    }

   // Ambil data lengkap semua mahasiswa + Data Skripsi (Judul, Pembimbing, Status)
    public function get_all_mahasiswa_lengkap()
    {
        $this->db->select('
            a.id AS id_user, 
            a.nama, 
            a.foto, 
            a.username, 
            m.npm, 
            m.prodi, 
            m.angkatan, 
            m.is_skripsi, 
            m.telepon,
            s.id AS id_skripsi,
            s.judul,
            s.status_acc_kaprodi,
            p1.nama AS p1,
            p2.nama AS p2
        ');
        
        $this->db->from('mstr_akun a');
        $this->db->join('data_mahasiswa m', 'a.id = m.id');
        
        // JOIN ke tabel Skripsi (Left Join agar mahasiswa yang belum ajukan judul tetap tampil)
        $this->db->join('skripsi s', 'm.id = s.id_mahasiswa', 'left');
        
        // JOIN untuk ambil nama Pembimbing 1 & 2
        $this->db->join('mstr_akun p1', 's.pembimbing1 = p1.id', 'left');
        $this->db->join('mstr_akun p2', 's.pembimbing2 = p2.id', 'left');

        $this->db->where('a.role', 'mahasiswa');
        $this->db->order_by('m.angkatan', 'DESC');
        $this->db->order_by('a.nama', 'ASC');
        
        return $this->db->get()->result_array();
    }

    // Helper: Tentukan id_jenis_ujian_skripsi untuk SEMPRO berdasarkan prodi
    private function get_sempro_jenis_by_prodi($prodi) {
        if (stripos($prodi, 'Teknik Informatika S1') !== false) {
            return 5; // Seminar Proposal Teknik Informatika 2025
        } elseif (stripos($prodi, 'Teknologi Informasi D3') !== false) {
            return 7; // Seminar Proposal Teknologi Informasi D3
        } elseif (stripos($prodi, 'Teknik Industri') !== false) {
            return 3; // Seminar Proposal Teknik Industri
        } elseif (stripos($prodi, 'Teknik Mesin') !== false) {
            return 3; // Seminar Proposal (sama dengan Teknik Industri)
        } elseif (stripos($prodi, 'Mesin Otomotif') !== false) {
            return 3; // Seminar Proposal (sama dengan Teknik Industri)
        }
        return 5; // Default ke Teknik Informatika
    }

    // Helper: Tentukan id_jenis_ujian_skripsi untuk PENDADARAN berdasarkan prodi
    private function get_pendadaran_jenis_by_prodi($prodi) {
        if (stripos($prodi, 'Teknik Informatika S1') !== false) {
            return 6; // Seminar Pendadaran Teknik Informatika 2025
        } elseif (stripos($prodi, 'Teknologi Informasi D3') !== false) {
            return 8; // Seminar Pendadaran Teknologi Informasi D3
        } elseif (stripos($prodi, 'Teknik Industri') !== false) {
            return 4; // Seminar Pendadaran Teknik Industri
        } elseif (stripos($prodi, 'Teknik Mesin') !== false) {
            return 4; // Seminar Pendadaran (sama dengan Teknik Industri)
        } elseif (stripos($prodi, 'Mesin Otomotif') !== false) {
            return 4; // Seminar Pendadaran (sama dengan Teknik Industri)
        }
        return 6; // Default ke Teknik Informatika
    }

    // Ambil data mahasiswa yang Bab 3-nya sudah ACC Penuh oleh kedua dosen
    public function get_mahasiswa_siap_sempro()
    {
        // Pertama: temukan mahasiswa yang memenuhi syarat (BAB 3 ACC penuh)
        $sql = "SELECT DISTINCT m.id AS id_mahasiswa, s.id AS id_skripsi, m.prodi
                FROM progres_skripsi p
                JOIN data_mahasiswa m ON p.npm = m.npm
                JOIN skripsi s ON s.id_mahasiswa = m.id
                WHERE p.bab = 3 AND p.progres_dosen1 = 100 AND p.progres_dosen2 = 100";

        $rows = $this->db->query($sql)->result_array();

        // Pastikan ada entri ujian_skripsi untuk sempro dengan jenis yang sesuai prodi
        foreach ($rows as $r) {
            $id_skripsi = $r['id_skripsi'];
            $prodi = $r['prodi'];
            $id_jenis_sempro = $this->get_sempro_jenis_by_prodi($prodi);

            $this->db->from('ujian_skripsi');
            $this->db->where('id_skripsi', $id_skripsi);
            $this->db->where_in('id_jenis_ujian_skripsi', [3, 5, 7]); // Check all possible sempro jenis
            $exists = $this->db->get()->num_rows();

            if (!$exists) {
                $insert = [
                    'id_skripsi' => $id_skripsi,
                    'tanggal_daftar' => date('Y-m-d'),
                    'id_jenis_ujian_skripsi' => $id_jenis_sempro,
                    'status' => 'Berlangsung'
                ];
                $this->db->insert('ujian_skripsi', $insert);
            }
        }

        // Sekarang ambil data dari tabel ujian_skripsi untuk ditampilkan
        $this->db->select('
            a.nama, a.foto,
            m.npm, m.prodi, m.angkatan,
            s.judul,
            d1.nama as nama_p1,
            d2.nama as nama_p2,
            u.tanggal_daftar as tgl_daftar_sempro,
            u.status as status_sempro
        ');

        $this->db->from('ujian_skripsi u');
        $this->db->join('skripsi s', 'u.id_skripsi = s.id');
        $this->db->join('data_mahasiswa m', 's.id_mahasiswa = m.id');
        $this->db->join('mstr_akun a', 'm.id = a.id');
        $this->db->join('mstr_akun d1', 's.pembimbing1 = d1.id', 'left');
        $this->db->join('mstr_akun d2', 's.pembimbing2 = d2.id', 'left');

        $this->db->where_in('u.id_jenis_ujian_skripsi', [1, 5]);
        $this->db->where_in('u.status', ['Berlangsung', 'Diterima']);
        $this->db->order_by('u.tanggal_daftar', 'DESC');

        return $this->db->get()->result_array();
    }

    // --- FITUR BARU: Riwayat Progress Mahasiswa ---
    public function get_riwayat_progress($keyword = NULL)
    {
        $this->db->select('
            p.id, p.npm, p.bab, p.file, p.komentar_dosen1, p.komentar_dosen2, p.nilai_dosen1, p.nilai_dosen2, p.progres_dosen1, p.progres_dosen2, p.tgl_upload, p.tgl_verifikasi,
            a.nama as nama_mhs, m.prodi,
            s.id as id_skripsi, s.judul,
            d1.nama as nama_p1,
            d2.nama as nama_p2
        ');

        $this->db->from('progres_skripsi p');
        $this->db->join('data_mahasiswa m', 'p.npm = m.npm');
        $this->db->join('mstr_akun a', 'm.id = a.id');
        $this->db->join('skripsi s', 's.id_mahasiswa = m.id', 'left');
        $this->db->join('mstr_akun d1', 's.pembimbing1 = d1.id', 'left');
        $this->db->join('mstr_akun d2', 's.pembimbing2 = d2.id', 'left');

        if ($keyword) {
            $this->db->group_start();
                $this->db->like('a.nama', $keyword);
                $this->db->or_like('p.npm', $keyword);
                $this->db->or_like('s.judul', $keyword);
            $this->db->group_end();
        }

        $this->db->order_by('p.tgl_upload', 'DESC');

        return $this->db->get()->result_array();
    }

    // --- FITUR BARU: Kinerja Dospem per Semester ---
    public function get_kinerja_dospem_per_semester($semester = NULL, $prodi = NULL)
    {
        // Jika semester tidak diberikan, gunakan semester aktif (misal 2025/2026 Genap)
        if (!$semester) {
            $semester = '2025/2026 Genap'; // Default, bisa diubah
        }

        $this->db->select('
            d.id, d.nama, d.nidk, dd.prodi,
            COUNT(DISTINCT s.id) as jumlah_mahasiswa,
            COUNT(p.id) as jumlah_bimbingan
        ');

        $this->db->from('mstr_akun d');
        $this->db->join('data_dosen dd', 'd.id = dd.id');
        $this->db->join('skripsi s', '(s.pembimbing1 = d.id OR s.pembimbing2 = d.id)', 'left');
        $this->db->join('progres_skripsi p', 'p.npm = (SELECT npm FROM data_mahasiswa WHERE id = s.id_mahasiswa)', 'left');

        $this->db->where('d.role', 'dosen');

        if ($prodi) {
            $this->db->where('dd.prodi', $prodi);
        }

        // Filter semester: asumsikan berdasarkan tgl_upload progres
        // Misal, semester Genap: bulan 8-12, Ganjil: 1-7
        if (strpos($semester, 'Genap') !== false) {
            $this->db->where('MONTH(p.tgl_upload) >=', 8);
            $this->db->where('MONTH(p.tgl_upload) <=', 12);
        } else {
            $this->db->where('MONTH(p.tgl_upload) >=', 1);
            $this->db->where('MONTH(p.tgl_upload) <=', 7);
        }

        $this->db->group_by('d.id');
        $this->db->order_by('jumlah_bimbingan', 'DESC');

        $result = $this->db->get()->result_array();

        // Tambahkan detail mahasiswa yang dibimbing
        foreach ($result as $key => $dosen) {
            $this->db->select('a.nama, m.npm, s.judul');
            $this->db->from('skripsi s');
            $this->db->join('data_mahasiswa m', 's.id_mahasiswa = m.id');
            $this->db->join('mstr_akun a', 'm.id = a.id');
            $this->db->where("(s.pembimbing1 = {$dosen['id']} OR s.pembimbing2 = {$dosen['id']})");
            $result[$key]['mahasiswa_dibimbing'] = $this->db->get()->result_array();
        }

        return $result;
    }

    // --- FITUR BARU: Mahasiswa Siap Pendadaran (Bab 4 ACC) ---
    public function get_mahasiswa_siap_pendadaran()
    {
        // Cari mahasiswa yang sudah ACC di bab terakhirnya tergantung prodi (max_bab)
        $sql_last = "SELECT m.id AS id_mahasiswa, s.id AS id_skripsi, m.prodi, p.bab, p.tgl_upload
                     FROM (
                        SELECT npm, MAX(CONCAT(LPAD(bab,3,'0'), '::', tgl_upload)) as last_key
                        FROM progres_skripsi
                        GROUP BY npm
                     ) as last
                     JOIN progres_skripsi p ON CONCAT(LPAD(p.bab,3,'0'), '::', p.tgl_upload) = last.last_key
                     JOIN data_mahasiswa m ON p.npm = m.npm
                     JOIN skripsi s ON s.id_mahasiswa = m.id";

        $rows = $this->db->query($sql_last)->result_array();

        foreach ($rows as $r) {
            $prodi = $r['prodi'] ?? '';
            $max_bab = 6;
            if (stripos($prodi, 'D3') !== false || stripos($prodi, 'Diploma 3') !== false) {
                $max_bab = 5;
            }

            // Ambil data progres terakhir untuk npm tersebut
            $npm_row = $this->db->select('npm')->from('data_mahasiswa')->where('id', $r['id_mahasiswa'])->get()->row_array();
            if (!$npm_row) continue;
            $npm = $npm_row['npm'];

            $this->db->order_by('bab', 'DESC');
            $this->db->order_by('tgl_upload', 'DESC');
            $last_progres = $this->db->get_where('progres_skripsi', ['npm' => $npm])->row_array();

            if (!$last_progres) continue;

            if ((int)$last_progres['progres_dosen1'] === 100 && (int)$last_progres['progres_dosen2'] === 100 && (int)$last_progres['bab'] >= $max_bab) {
                // pastikan ada ujian_skripsi untuk pendadaran dengan jenis yang sesuai prodi
                $id_skripsi = $r['id_skripsi'];
                $prodi = $r['prodi'];
                $id_jenis_pendadaran = $this->get_pendadaran_jenis_by_prodi($prodi);
                
                $this->db->from('ujian_skripsi');
                $this->db->where('id_skripsi', $id_skripsi);
                $this->db->where_in('id_jenis_ujian_skripsi', [2, 4, 6, 8]); // Check all possible pendadaran jenis
                $exists = $this->db->get()->num_rows();

                if (!$exists) {
                    $insert = [
                        'id_skripsi' => $id_skripsi,
                        'tanggal_daftar' => date('Y-m-d'),
                        'id_jenis_ujian_skripsi' => $id_jenis_pendadaran,
                        'status' => 'Berlangsung'
                    ];
                    $this->db->insert('ujian_skripsi', $insert);
                }
            }
        }

        // Ambil dari ujian_skripsi untuk pendadaran
        $this->db->select('
            a.nama, a.foto,
            m.npm, m.prodi, m.angkatan,
            s.judul,
            d1.nama as nama_p1,
            d2.nama as nama_p2,
            u.tanggal_daftar as tgl_daftar,
            u.status as status_ujian
        ');

        $this->db->from('ujian_skripsi u');
        $this->db->join('skripsi s', 'u.id_skripsi = s.id');
        $this->db->join('data_mahasiswa m', 's.id_mahasiswa = m.id');
        $this->db->join('mstr_akun a', 'm.id = a.id');
        $this->db->join('mstr_akun d1', 's.pembimbing1 = d1.id', 'left');
        $this->db->join('mstr_akun d2', 's.pembimbing2 = d2.id', 'left');

        $this->db->where_in('u.id_jenis_ujian_skripsi', [2,4,6,8]);
        $this->db->where_in('u.status', ['Berlangsung', 'Diterima']);
        $this->db->order_by('u.tanggal_daftar', 'DESC');

        return $this->db->get()->result_array();
    }

    // --- GET SKRIPSI BY ID ---
    public function get_skripsi_by_id($id_skripsi)
    {
        $this->db->select('
            s.id, s.judul, s.pembimbing1, s.pembimbing2,
            a.nama as nama_mahasiswa, m.npm
        ');

        $this->db->from('skripsi s');
        $this->db->join('data_mahasiswa m', 's.id_mahasiswa = m.id');
        $this->db->join('mstr_akun a', 'm.id = a.id');
        $this->db->where('s.id', $id_skripsi);

        return $this->db->get()->row_array();
    }

    // --- GET ALL UNIQUE PRODI ---
    public function get_all_prodi()
    {
        $this->db->select('DISTINCT(prodi) as prodi');
        $this->db->from('data_mahasiswa');
        $this->db->where('prodi !=', '');
        $this->db->order_by('prodi', 'ASC');
        $mahasiswa_prodi = $this->db->get()->result_array();

        $this->db->select('DISTINCT(prodi) as prodi');
        $this->db->from('data_dosen');
        $this->db->where('prodi !=', '');
        $this->db->order_by('prodi', 'ASC');
        $dosen_prodi = $this->db->get()->result_array();

        // Merge and unique
        $all_prodi = array_merge($mahasiswa_prodi, $dosen_prodi);
        $unique_prodi = array_unique(array_column($all_prodi, 'prodi'));
        sort($unique_prodi);

        return array_map(function($prodi) {
            return ['prodi' => $prodi];
        }, $unique_prodi);
    }

    // --- GET UNIQUE ANGKATAN ---
    public function get_unique_angkatan()
    {
        $this->db->select('DISTINCT(angkatan) as angkatan');
        $this->db->from('data_mahasiswa');
        $this->db->where('angkatan IS NOT NULL');
        $this->db->order_by('angkatan', 'DESC');
        return $this->db->get()->result_array();
    }

    // --- GET UNIQUE STATUS ACC KAPRODI ---
    public function get_unique_status_acc_kaprodi()
    {
        $this->db->select('DISTINCT(status_acc_kaprodi) as status');
        $this->db->from('skripsi');
        $this->db->where('status_acc_kaprodi IS NOT NULL');
        $this->db->order_by('status_acc_kaprodi', 'ASC');
        return $this->db->get()->result_array();
    }

    // --- GET UNIQUE STATUS PLAGIARISME ---
    public function get_unique_status_plagiarisme()
    {
        $this->db->select('DISTINCT(status_plagiasi) as status');
        $this->db->from('progres_skripsi');
        $this->db->where('status_plagiasi IS NOT NULL');
        $this->db->order_by('status_plagiasi', 'ASC');
        return $this->db->get()->result_array();
    }

    // --- COUNT FUNCTIONS FOR PAGINATION ---

    // Count untuk Data Mahasiswa Lengkap
    public function count_mahasiswa_lengkap($f_prodi = null, $f_kelengkapan = null, $f_keyword = null)
    {
        $this->db->select('
            a.id AS id_user, 
            a.nama, 
            a.foto, 
            a.username, 
            m.npm, 
            m.prodi, 
            m.angkatan, 
            m.is_skripsi, 
            m.telepon,
            s.id AS id_skripsi,
            s.judul,
            s.status_acc_kaprodi,
            p1.nama AS p1,
            p2.nama AS p2
        ');
        
        $this->db->from('mstr_akun a');
        $this->db->join('data_mahasiswa m', 'a.id = m.id');
        $this->db->join('skripsi s', 'm.id = s.id_mahasiswa', 'left');
        $this->db->join('mstr_akun p1', 's.pembimbing1 = p1.id', 'left');
        $this->db->join('mstr_akun p2', 's.pembimbing2 = p2.id', 'left');
        $this->db->where('a.role', 'mahasiswa');

        // Apply filters if provided
        if ($f_prodi && $f_prodi != '') {
            $this->db->where('m.prodi', $f_prodi);
        }

        if ($f_keyword && $f_keyword != '') {
            $this->db->group_start();
            $this->db->like('a.nama', $f_keyword);
            $this->db->or_like('m.npm', $f_keyword);
            $this->db->group_end();
        }

        return $this->db->count_all_results();
    }

    // Get Data Mahasiswa Lengkap dengan Limit & Offset
    public function get_mahasiswa_lengkap_paginated($f_prodi = null, $f_kelengkapan = null, $f_keyword = null, $limit = null, $offset = null)
    {
        $this->db->select('
            a.id AS id_user, 
            a.nama, 
            a.foto, 
            a.username, 
            m.npm, 
            m.prodi, 
            m.angkatan, 
            m.is_skripsi, 
            m.telepon,
            s.id AS id_skripsi,
            s.judul,
            s.status_acc_kaprodi,
            p1.nama AS p1,
            p2.nama AS p2
        ');
        
        $this->db->from('mstr_akun a');
        $this->db->join('data_mahasiswa m', 'a.id = m.id');
        $this->db->join('skripsi s', 'm.id = s.id_mahasiswa', 'left');
        $this->db->join('mstr_akun p1', 's.pembimbing1 = p1.id', 'left');
        $this->db->join('mstr_akun p2', 's.pembimbing2 = p2.id', 'left');
        $this->db->where('a.role', 'mahasiswa');

        // Apply filters if provided
        if ($f_prodi && $f_prodi != '') {
            $this->db->where('m.prodi', $f_prodi);
        }

        if ($f_keyword && $f_keyword != '') {
            $this->db->group_start();
            $this->db->like('a.nama', $f_keyword);
            $this->db->or_like('m.npm', $f_keyword);
            $this->db->group_end();
        }

        $this->db->order_by('m.angkatan', 'DESC');
        $this->db->order_by('a.nama', 'ASC');

        if ($limit) {
            $this->db->limit($limit, $offset);
        }
        
        return $this->db->get()->result_array();
    }

    // Count untuk ACC Judul
    public function count_acc_judul($keyword = null, $status = null, $prodi = null)
    {
        $this->db->select('
            a.id AS id_user, 
            a.nama, 
            m.npm, 
            m.prodi, 
            m.angkatan,
            s.id AS id_skripsi,
            s.judul,
            s.status_acc_kaprodi,
            p1.nama AS p1,
            p2.nama AS p2
        ');
        
        $this->db->from('mstr_akun a');
        $this->db->join('data_mahasiswa m', 'a.id = m.id');
        $this->db->join('skripsi s', 'm.id = s.id_mahasiswa', 'left');
        $this->db->join('mstr_akun p1', 's.pembimbing1 = p1.id', 'left');
        $this->db->join('mstr_akun p2', 's.pembimbing2 = p2.id', 'left');
        $this->db->where('a.role', 'mahasiswa');

        if ($keyword && $keyword != '') {
            $this->db->group_start();
            $this->db->like('a.nama', $keyword);
            $this->db->or_like('m.npm', $keyword);
            $this->db->or_like('s.judul', $keyword);
            $this->db->group_end();
        }

        if ($status && $status != 'all') {
            $this->db->where('s.status_acc_kaprodi', $status);
        }

        if ($prodi && $prodi != 'all') {
            $this->db->where('m.prodi', $prodi);
        }

        return $this->db->count_all_results();
    }

    // Get ACC Judul dengan Limit & Offset
    public function get_acc_judul_paginated($keyword = null, $status = null, $prodi = null, $limit = null, $offset = null)
    {
        $this->db->select('
            a.id AS id_user, 
            a.nama, 
            m.npm, 
            m.prodi, 
            m.angkatan,
            s.id AS id_skripsi,
            s.judul,
            s.status_acc_kaprodi,
            p1.nama AS p1,
            p2.nama AS p2
        ');
        
        $this->db->from('mstr_akun a');
        $this->db->join('data_mahasiswa m', 'a.id = m.id');
        $this->db->join('skripsi s', 'm.id = s.id_mahasiswa', 'left');
        $this->db->join('mstr_akun p1', 's.pembimbing1 = p1.id', 'left');
        $this->db->join('mstr_akun p2', 's.pembimbing2 = p2.id', 'left');
        $this->db->where('a.role', 'mahasiswa');

        if ($keyword && $keyword != '') {
            $this->db->group_start();
            $this->db->like('a.nama', $keyword);
            $this->db->or_like('m.npm', $keyword);
            $this->db->or_like('s.judul', $keyword);
            $this->db->group_end();
        }

        if ($status && $status != 'all') {
            $this->db->where('s.status_acc_kaprodi', $status);
        }

        if ($prodi && $prodi != 'all') {
            $this->db->where('m.prodi', $prodi);
        }

        $this->db->order_by('a.nama', 'ASC');

        if ($limit) {
            $this->db->limit($limit, $offset);
        }
        
        return $this->db->get()->result_array();
    }

    // Count untuk Plagiarisme
    public function count_plagiarisme($keyword = null, $status = null)
    {
        $this->db->select('
            p.id, 
            p.bab, 
            p.file as progres_file, 
            p.tgl_upload, 
            p.tgl_verifikasi,
            p.status_plagiasi, 
            p.persentase_kemiripan,
            a.nama, 
            m.npm, 
            s.judul
        ');
        
        $this->db->from('progres_skripsi p');
        $this->db->join('data_mahasiswa m', 'p.npm = m.npm');
        $this->db->join('mstr_akun a', 'm.id = a.id');
        $this->db->join('skripsi s', 's.id_mahasiswa = m.id', 'left');
        
        // HANYA BAB 1 
        $this->db->where('p.bab', 1);

        if ($keyword && $keyword != '') {
            $this->db->group_start();
            $this->db->like('a.nama', $keyword);
            $this->db->or_like('m.npm', $keyword);
            $this->db->group_end();
        }

        if ($status && $status != 'all') {
            $this->db->where('p.status_plagiasi', $status);
        }

        return $this->db->count_all_results();
    }

    // Get Plagiarisme dengan Limit & Offset
    public function get_plagiarisme_paginated($keyword = null, $status = null, $limit = null, $offset = null)
    {
        $this->db->select('
            p.id, 
            p.bab, 
            p.file as progres_file, 
            p.tgl_upload, 
            p.tgl_verifikasi,
            p.status_plagiasi, 
            p.persentase_kemiripan,
            a.nama, 
            m.npm, 
            s.judul
        ');
        
        $this->db->from('progres_skripsi p');
        $this->db->join('data_mahasiswa m', 'p.npm = m.npm');
        $this->db->join('mstr_akun a', 'm.id = a.id');
        $this->db->join('skripsi s', 's.id_mahasiswa = m.id', 'left');
        
        // HANYA BAB 1 
        $this->db->where('p.bab', 1);

        if ($keyword && $keyword != '') {
            $this->db->group_start();
            $this->db->like('a.nama', $keyword);
            $this->db->or_like('m.npm', $keyword);
            $this->db->group_end();
        }

        if ($status && $status != 'all') {
            $this->db->where('p.status_plagiasi', $status);
        }

        $this->db->order_by("CASE WHEN p.status_plagiasi = 'Menunggu' THEN 0 ELSE 1 END", "ASC");
        $this->db->order_by('p.tgl_upload', 'DESC');

        if ($limit) {
            $this->db->limit($limit, $offset);
        }
        
        return $this->db->get()->result_array();
    }

    // Count untuk Mahasiswa Siap Sempro
    public function count_siap_sempro($keyword = null, $prodi = null, $angkatan = null)
    {
        // Pertama dapatkan ID yang memenuhi syarat
        $sql = "SELECT DISTINCT m.id AS id_mahasiswa, s.id AS id_skripsi, m.prodi
                FROM progres_skripsi p
                JOIN data_mahasiswa m ON p.npm = m.npm
                JOIN skripsi s ON s.id_mahasiswa = m.id
                WHERE p.bab = 3 AND p.progres_dosen1 = 100 AND p.progres_dosen2 = 100";
        
        $rows = $this->db->query($sql)->result_array();

        if (empty($rows)) {
            return 0;
        }

        // Build IDs
        $ids = array_column($rows, 'id_skripsi');
        
        // Sekarang ambil dari tabel ujian_skripsi
        $this->db->select('
            a.nama, a.foto,
            m.npm, m.prodi, m.angkatan,
            s.judul,
            d1.nama as nama_p1,
            d2.nama as nama_p2,
            u.tanggal_daftar as tgl_daftar_sempro,
            u.status as status_sempro
        ');

        $this->db->from('ujian_skripsi u');
        $this->db->join('skripsi s', 'u.id_skripsi = s.id');
        $this->db->join('data_mahasiswa m', 's.id_mahasiswa = m.id');
        $this->db->join('mstr_akun a', 'm.id = a.id');
        $this->db->join('mstr_akun d1', 's.pembimbing1 = d1.id', 'left');
        $this->db->join('mstr_akun d2', 's.pembimbing2 = d2.id', 'left');

        $this->db->where_in('u.id_jenis_ujian_skripsi', [1, 5]);
        $this->db->where_in('u.status', ['Berlangsung', 'Diterima']);
        
        // Filter by IDs from first query
        if (!empty($ids)) {
            $this->db->where_in('s.id', $ids);
        }

        if ($keyword && $keyword != '') {
            $this->db->group_start();
            $this->db->like('a.nama', $keyword);
            $this->db->or_like('m.npm', $keyword);
            $this->db->or_like('s.judul', $keyword);
            $this->db->group_end();
        }

        if ($prodi && $prodi != 'all') {
            $this->db->where('m.prodi', $prodi);
        }

        if ($angkatan && $angkatan != 'all') {
            $this->db->where('m.angkatan', $angkatan);
        }

        return $this->db->count_all_results();
    }

    // Get Mahasiswa Siap Sempro dengan Limit & Offset
    public function get_siap_sempro_paginated($keyword = null, $prodi = null, $angkatan = null, $limit = null, $offset = null)
    {
        // Pertama dapatkan ID yang memenuhi syarat
        $sql = "SELECT DISTINCT m.id AS id_mahasiswa, s.id AS id_skripsi, m.prodi
                FROM progres_skripsi p
                JOIN data_mahasiswa m ON p.npm = m.npm
                JOIN skripsi s ON s.id_mahasiswa = m.id
                WHERE p.bab = 3 AND p.progres_dosen1 = 100 AND p.progres_dosen2 = 100";
        
        $rows = $this->db->query($sql)->result_array();

        if (empty($rows)) {
            return array();
        }

        $ids = array_column($rows, 'id_skripsi');

        $this->db->select('
            a.nama, a.foto,
            m.npm, m.prodi, m.angkatan,
            s.judul,
            d1.nama as nama_p1,
            d2.nama as nama_p2,
            u.tanggal_daftar as tgl_daftar_sempro,
            u.status as status_sempro
        ');

        $this->db->from('ujian_skripsi u');
        $this->db->join('skripsi s', 'u.id_skripsi = s.id');
        $this->db->join('data_mahasiswa m', 's.id_mahasiswa = m.id');
        $this->db->join('mstr_akun a', 'm.id = a.id');
        $this->db->join('mstr_akun d1', 's.pembimbing1 = d1.id', 'left');
        $this->db->join('mstr_akun d2', 's.pembimbing2 = d2.id', 'left');

        $this->db->where_in('u.id_jenis_ujian_skripsi', [1, 5]);
        $this->db->where_in('u.status', ['Berlangsung', 'Diterima']);
        
        if (!empty($ids)) {
            $this->db->where_in('s.id', $ids);
        }

        if ($keyword && $keyword != '') {
            $this->db->group_start();
            $this->db->like('a.nama', $keyword);
            $this->db->or_like('m.npm', $keyword);
            $this->db->or_like('s.judul', $keyword);
            $this->db->group_end();
        }

        if ($prodi && $prodi != 'all') {
            $this->db->where('m.prodi', $prodi);
        }

        if ($angkatan && $angkatan != 'all') {
            $this->db->where('m.angkatan', $angkatan);
        }

        $this->db->order_by('u.tanggal_daftar', 'DESC');

        if ($limit) {
            $this->db->limit($limit, $offset);
        }
        
        return $this->db->get()->result_array();
    }

    // Count untuk Mahasiswa Siap Pendadaran
    public function count_siap_pendadaran($keyword = null, $prodi = null, $angkatan = null)
    {
        // Get max bab per prodi
        $sql_last = "SELECT m.id AS id_mahasiswa, s.id AS id_skripsi, m.prodi, p.bab, p.tgl_upload
                     FROM (
                        SELECT npm, MAX(CONCAT(LPAD(bab,3,'0'), '::', tgl_upload)) as last_key
                        FROM progres_skripsi
                        GROUP BY npm
                     ) as last
                     JOIN progres_skripsi p ON CONCAT(LPAD(p.bab,3,'0'), '::', p.tgl_upload) = last.last_key
                     JOIN data_mahasiswa m ON p.npm = m.npm
                     JOIN skripsi s ON s.id_mahasiswa = m.id";

        $rows = $this->db->query($sql_last)->result_array();

        if (empty($rows)) {
            return 0;
        }

        // Filter rows
        $valid_ids = array();
        foreach ($rows as $r) {
            $prodi_row = $r['prodi'] ?? '';
            $max_bab = 6;
            if (stripos($prodi_row, 'D3') !== false || stripos($prodi_row, 'Diploma 3') !== false) {
                $max_bab = 5;
            }

            $npm_row = $this->db->select('npm')->from('data_mahasiswa')->where('id', $r['id_mahasiswa'])->get()->row_array();
            if (!$npm_row) continue;
            $npm = $npm_row['npm'];

            $this->db->order_by('bab', 'DESC');
            $this->db->order_by('tgl_upload', 'DESC');
            $last_progres = $this->db->get_where('progres_skripsi', ['npm' => $npm])->row_array();

            if (!$last_progres) continue;

            if ((int)$last_progres['progres_dosen1'] === 100 && (int)$last_progres['progres_dosen2'] === 100 && (int)$last_progres['bab'] >= $max_bab) {
                $valid_ids[] = $r['id_skripsi'];
            }
        }

        if (empty($valid_ids)) {
            return 0;
        }

        $this->db->select('
            a.nama, a.foto,
            m.npm, m.prodi, m.angkatan,
            s.judul,
            d1.nama as nama_p1,
            d2.nama as nama_p2,
            u.tanggal_daftar as tgl_daftar,
            u.status as status_ujian
        ');

        $this->db->from('ujian_skripsi u');
        $this->db->join('skripsi s', 'u.id_skripsi = s.id');
        $this->db->join('data_mahasiswa m', 's.id_mahasiswa = m.id');
        $this->db->join('mstr_akun a', 'm.id = a.id');
        $this->db->join('mstr_akun d1', 's.pembimbing1 = d1.id', 'left');
        $this->db->join('mstr_akun d2', 's.pembimbing2 = d2.id', 'left');

        $this->db->where_in('u.id_jenis_ujian_skripsi', [2,4,6,8]);
        $this->db->where_in('u.status', ['Berlangsung', 'Diterima']);
        $this->db->where_in('s.id', $valid_ids);

        if ($keyword && $keyword != '') {
            $this->db->group_start();
            $this->db->like('a.nama', $keyword);
            $this->db->or_like('m.npm', $keyword);
            $this->db->or_like('s.judul', $keyword);
            $this->db->group_end();
        }

        if ($prodi && $prodi != 'all') {
            $this->db->where('m.prodi', $prodi);
        }

        if ($angkatan && $angkatan != 'all') {
            $this->db->where('m.angkatan', $angkatan);
        }

        return $this->db->count_all_results();
    }

    // Get Mahasiswa Siap Pendadaran dengan Limit & Offset
    public function get_siap_pendadaran_paginated($keyword = null, $prodi = null, $angkatan = null, $limit = null, $offset = null)
    {
        $sql_last = "SELECT m.id AS id_mahasiswa, s.id AS id_skripsi, m.prodi, p.bab, p.tgl_upload
                     FROM (
                        SELECT npm, MAX(CONCAT(LPAD(bab,3,'0'), '::', tgl_upload)) as last_key
                        FROM progres_skripsi
                        GROUP BY npm
                     ) as last
                     JOIN progres_skripsi p ON CONCAT(LPAD(p.bab,3,'0'), '::', p.tgl_upload) = last.last_key
                     JOIN data_mahasiswa m ON p.npm = m.npm
                     JOIN skripsi s ON s.id_mahasiswa = m.id";

        $rows = $this->db->query($sql_last)->result_array();

        if (empty($rows)) {
            return array();
        }

        $valid_ids = array();
        foreach ($rows as $r) {
            $prodi_row = $r['prodi'] ?? '';
            $max_bab = 6;
            if (stripos($prodi_row, 'D3') !== false || stripos($prodi_row, 'Diploma 3') !== false) {
                $max_bab = 5;
            }

            $npm_row = $this->db->select('npm')->from('data_mahasiswa')->where('id', $r['id_mahasiswa'])->get()->row_array();
            if (!$npm_row) continue;
            $npm = $npm_row['npm'];

            $this->db->order_by('bab', 'DESC');
            $this->db->order_by('tgl_upload', 'DESC');
            $last_progres = $this->db->get_where('progres_skripsi', ['npm' => $npm])->row_array();

            if (!$last_progres) continue;

            if ((int)$last_progres['progres_dosen1'] === 100 && (int)$last_progres['progres_dosen2'] === 100 && (int)$last_progres['bab'] >= $max_bab) {
                $valid_ids[] = $r['id_skripsi'];
            }
        }

        if (empty($valid_ids)) {
            return array();
        }

        $this->db->select('
            a.nama, a.foto,
            m.npm, m.prodi, m.angkatan,
            s.judul,
            d1.nama as nama_p1,
            d2.nama as nama_p2,
            u.tanggal_daftar as tgl_daftar,
            u.status as status_ujian
        ');

        $this->db->from('ujian_skripsi u');
        $this->db->join('skripsi s', 'u.id_skripsi = s.id');
        $this->db->join('data_mahasiswa m', 's.id_mahasiswa = m.id');
        $this->db->join('mstr_akun a', 'm.id = a.id');
        $this->db->join('mstr_akun d1', 's.pembimbing1 = d1.id', 'left');
        $this->db->join('mstr_akun d2', 's.pembimbing2 = d2.id', 'left');

        $this->db->where_in('u.id_jenis_ujian_skripsi', [2,4,6,8]);
        $this->db->where_in('u.status', ['Berlangsung', 'Diterima']);
        $this->db->where_in('s.id', $valid_ids);

        if ($keyword && $keyword != '') {
            $this->db->group_start();
            $this->db->like('a.nama', $keyword);
            $this->db->or_like('m.npm', $keyword);
            $this->db->or_like('s.judul', $keyword);
            $this->db->group_end();
        }

        if ($prodi && $prodi != 'all') {
            $this->db->where('m.prodi', $prodi);
        }

        if ($angkatan && $angkatan != 'all') {
            $this->db->where('m.angkatan', $angkatan);
        }

        $this->db->order_by('u.tanggal_daftar', 'DESC');

        if ($limit) {
            $this->db->limit($limit, $offset);
        }
        
        return $this->db->get()->result_array();
    }

    // Count untuk Riwayat Progress
    public function count_riwayat_progress($keyword = null)
    {
        $this->db->select('
            p.id, p.npm, p.bab, p.file, p.komentar_dosen1, p.komentar_dosen2, p.nilai_dosen1, p.nilai_dosen2, p.progres_dosen1, p.progres_dosen2, p.tgl_upload, p.tgl_verifikasi,
            a.nama as nama_mhs, m.prodi,
            s.id as id_skripsi, s.judul,
            d1.nama as nama_p1,
            d2.nama as nama_p2
        ');

        $this->db->from('progres_skripsi p');
        $this->db->join('data_mahasiswa m', 'p.npm = m.npm');
        $this->db->join('mstr_akun a', 'm.id = a.id');
        $this->db->join('skripsi s', 's.id_mahasiswa = m.id', 'left');
        $this->db->join('mstr_akun d1', 's.pembimbing1 = d1.id', 'left');
        $this->db->join('mstr_akun d2', 's.pembimbing2 = d2.id', 'left');

        if ($keyword && $keyword != '') {
            $this->db->group_start();
            $this->db->like('a.nama', $keyword);
            $this->db->or_like('p.npm', $keyword);
            $this->db->or_like('s.judul', $keyword);
            $this->db->group_end();
        }

        return $this->db->count_all_results();
    }

    // Get Riwayat Progress dengan Limit & Offset
    public function get_riwayat_progress_paginated($keyword = null, $limit = null, $offset = null)
    {
        $this->db->select('
            p.id, p.npm, p.bab, p.file, p.komentar_dosen1, p.komentar_dosen2, p.nilai_dosen1, p.nilai_dosen2, p.progres_dosen1, p.progres_dosen2, p.tgl_upload, p.tgl_verifikasi,
            a.nama as nama_mhs, m.prodi,
            s.id as id_skripsi, s.judul,
            d1.nama as nama_p1,
            d2.nama as nama_p2
        ');

        $this->db->from('progres_skripsi p');
        $this->db->join('data_mahasiswa m', 'p.npm = m.npm');
        $this->db->join('mstr_akun a', 'm.id = a.id');
        $this->db->join('skripsi s', 's.id_mahasiswa = m.id', 'left');
        $this->db->join('mstr_akun d1', 's.pembimbing1 = d1.id', 'left');
        $this->db->join('mstr_akun d2', 's.pembimbing2 = d2.id', 'left');

        if ($keyword && $keyword != '') {
            $this->db->group_start();
            $this->db->like('a.nama', $keyword);
            $this->db->or_like('p.npm', $keyword);
            $this->db->or_like('s.judul', $keyword);
            $this->db->group_end();
        }

        $this->db->order_by('p.tgl_upload', 'DESC');

        if ($limit) {
            $this->db->limit($limit, $offset);
        }
        
        return $this->db->get()->result_array();
    }

}
