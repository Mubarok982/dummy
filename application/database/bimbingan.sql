-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Waktu pembuatan: 28 Jan 2026 pada 19.40
-- Versi server: 10.4.32-MariaDB
-- Versi PHP: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `bimbingan`
--

-- --------------------------------------------------------

--
-- Struktur dari tabel `apresiasi_ujian_skripsi`
--

CREATE TABLE `apresiasi_ujian_skripsi` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `id_ujian_skripsi` bigint(20) UNSIGNED NOT NULL,
  `id_penguji` bigint(20) UNSIGNED NOT NULL,
  `apresiasi` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `data_dosen`
--

CREATE TABLE `data_dosen` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `nidk` varchar(20) NOT NULL,
  `prodi` enum('Teknik Informatika S1','Teknologi Informasi D3','Teknik Industri S1','Teknik Mesin S1','Mesin Otomotif D3') NOT NULL,
  `ttd` text DEFAULT NULL,
  `is_kaprodi` tinyint(1) NOT NULL DEFAULT 0,
  `is_praktisi` tinyint(1) NOT NULL DEFAULT 0,
  `telepon` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `data_dosen`
--

INSERT INTO `data_dosen` (`id`, `nidk`, `prodi`, `ttd`, `is_kaprodi`, `is_praktisi`, `telepon`) VALUES
(3, '057508191', 'Teknologi Informasi D3', '76fd6ae79094c3525832e32681c68162.jpg', 0, 0, NULL),
(4, '027108182', 'Teknologi Informasi D3', '22ed2700bb17d49047fda1e850f4b0d2.png', 1, 0, NULL),
(5, '107906052', 'Teknologi Informasi D3', '85c81f05c1f3efe09071570d1a01a874.png', 0, 0, NULL),
(6, '107806051', 'Teknologi Informasi D3', '306cba0ccd7ffacf4448c71067006036.png', 0, 0, NULL),
(7, '107306024', 'Teknologi Informasi D3', '3b22ab010c9b6a6d3c042b121362f75e.png', 0, 0, NULL),
(8, '987008138', 'Teknik Informatika S1', '935af1f3659876512c64e8c7022f37a0.png', 0, 0, NULL),
(9, '057108188', 'Teknik Informatika S1', '764dad0ebdfc7efd218558c94b30bedf.png', 0, 0, NULL),
(10, '067206024', 'Teknik Informatika S1', '97defb35d2be7c7beb30bd6e27146997.png', 0, 0, NULL),
(11, '139006116', 'Teknik Informatika S1', '49878337d100e9f05cc62984a2d93c0d.png', 0, 0, NULL),
(12, '158808135', 'Teknik Informatika S1', '596e5d30d71ec9ff4f19af56558324d6.png', 0, 0, NULL),
(13, '158108139', 'Teknik Informatika S1', '9d545b4608a106b30a425095ba38cabd.png', 0, 0, NULL),
(14, '168508156', 'Teknik Informatika S1', '6d70c8fa2be7350042f30365685024a8.png', 0, 0, NULL),
(15, '168208163', 'Teknik Informatika S1', 'bc02ddcafb224d7780b201f345550209.png', 1, 0, NULL),
(16, '188508188', 'Teknik Informatika S1', '80fe614d55d086ff1a052fb55b57b5ee.png', 0, 0, NULL),
(17, '187708189', 'Teknik Informatika S1', '63241824e32d31b2b269cfc35b375369.png', 0, 0, NULL),
(18, '199208245', 'Teknik Informatika S1', '2637a992681eb93c8483b384833bf849.png', 0, 0, NULL),
(19, '219108337', 'Teknik Informatika S1', '6f267ec3a76eb80d10fec72de91c931d.png', 0, 0, NULL),
(9003, '12345678', 'Teknik Informatika S1', 'ttd_dosen_9003_1769625243.png', 0, 0, '082286965684'),
(9005, 'dosen2', 'Teknik Informatika S1', 'ttd_dosen_9005_1769444680.png', 0, 0, NULL),
(9999, '11223344', 'Teknik Informatika S1', 'ttd_dosen_9999_1769444029.png', 1, 0, NULL),
(10005, '1122334456', 'Teknik Informatika S1', NULL, 0, 0, NULL);

-- --------------------------------------------------------

--
-- Struktur dari tabel `data_mahasiswa`
--

CREATE TABLE `data_mahasiswa` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `npm` varchar(20) DEFAULT NULL,
  `jenis_kelamin` enum('Laki-laki','Perempuan') DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `telepon` varchar(15) DEFAULT NULL,
  `angkatan` year(4) NOT NULL,
  `prodi` enum('Teknik Informatika S1','Teknologi Informasi D3','Teknik Industri S1','Teknik Mesin S1','Mesin Otomotif D3') NOT NULL,
  `is_skripsi` tinyint(1) DEFAULT 0,
  `alamat` text DEFAULT NULL,
  `status_beasiswa` enum('Aktif','Tidak Aktif') NOT NULL,
  `status_mahasiswa` enum('Murni','Konversi','Transfer') NOT NULL,
  `ttd` text NOT NULL,
  `nik` varchar(20) DEFAULT NULL,
  `tempat_tgl_lahir` varchar(100) DEFAULT NULL,
  `nama_ortu_dengan_gelar` varchar(255) DEFAULT NULL,
  `kelas` varchar(50) DEFAULT NULL,
  `dokumen_identitas` varchar(255) NOT NULL,
  `sertifikat_toefl_niit` varchar(255) DEFAULT NULL,
  `sertifikat_office_puskom` varchar(255) NOT NULL,
  `sertifikat_btq_ibadah` varchar(255) NOT NULL,
  `sertifikat_bahasa` varchar(255) NOT NULL,
  `sertifikat_kompetensi_ujian_komprehensif` varchar(255) NOT NULL,
  `sertifikat_semaba_ppk_masta` varchar(255) NOT NULL,
  `sertifikat_kkn` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `data_mahasiswa`
--

INSERT INTO `data_mahasiswa` (`id`, `npm`, `jenis_kelamin`, `email`, `telepon`, `angkatan`, `prodi`, `is_skripsi`, `alamat`, `status_beasiswa`, `status_mahasiswa`, `ttd`, `nik`, `tempat_tgl_lahir`, `nama_ortu_dengan_gelar`, `kelas`, `dokumen_identitas`, `sertifikat_toefl_niit`, `sertifikat_office_puskom`, `sertifikat_btq_ibadah`, `sertifikat_bahasa`, `sertifikat_kompetensi_ujian_komprehensif`, `sertifikat_semaba_ppk_masta`, `sertifikat_kkn`) VALUES
(9004, '1234567896', 'Laki-laki', 'rizqymubarok99@gmail.com', '081391005220', '2020', 'Teknologi Informasi D3', 0, 'magelang', 'Tidak Aktif', 'Murni', 'ttd_9004_1769155406.png', NULL, 'jakarta 23 agustus 2003', NULL, NULL, 'dummy_doc.pdf', NULL, 'dummy_cert.pdf', 'dummy_cert.pdf', 'dummy_cert.pdf', 'dummy_cert.pdf', 'dummy_cert.pdf', 'dummy_cert.pdf'),
(9011, '2305040037', '', 'rizqy@gmail.com', '08139100522000', '2023', 'Teknik Informatika S1', 0, 'magelang', 'Tidak Aktif', 'Murni', 'ttd_9011_1769394164.png', NULL, 'magelang 19198', NULL, NULL, 'dummy_doc.pdf', NULL, 'dummy_cert.pdf', 'dummy_cert.pdf', 'dummy_cert.pdf', 'dummy_cert.pdf', 'dummy_cert.pdf', 'dummy_cert.pdf'),
(10001, '098765', 'Laki-laki', 'rizqy4@gmail.com', '091', '2026', 'Teknologi Informasi D3', 0, 'magelang', 'Tidak Aktif', 'Murni', 'ttd_10001_1769395507.png', NULL, 'magelang 19198', NULL, NULL, 'dummy_doc.pdf', NULL, 'dummy_cert.pdf', 'dummy_cert.pdf', 'dummy_cert.pdf', 'dummy_cert.pdf', 'dummy_cert.pdf', 'dummy_cert.pdf'),
(10002, '123456790A', 'Laki-laki', 'rizqymubarok9@gmail.com', '081229099996', '2025', 'Teknik Informatika S1', 0, 'Magelang', 'Tidak Aktif', 'Murni', 'ttd_10002_1769497080.png', NULL, 'jakarta 23 agustus 2010', NULL, NULL, 'dummy_doc.pdf', NULL, 'dummy_cert.pdf', 'dummy_cert.pdf', 'dummy_cert.pdf', 'dummy_cert.pdf', 'dummy_cert.pdf', 'dummy_cert.pdf'),
(10003, '123456', NULL, NULL, NULL, '2026', 'Teknik Informatika S1', 0, NULL, 'Tidak Aktif', 'Murni', 'dummy_ttd.png', NULL, NULL, NULL, NULL, 'dummy_doc.pdf', NULL, 'dummy_cert.pdf', 'dummy_cert.pdf', 'dummy_cert.pdf', 'dummy_cert.pdf', 'dummy_cert.pdf', 'dummy_cert.pdf');

-- --------------------------------------------------------

--
-- Struktur dari tabel `hasil_plagiarisme`
--

CREATE TABLE `hasil_plagiarisme` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `id_progres` int(11) NOT NULL,
  `tanggal_cek` date NOT NULL,
  `persentase_kemiripan` decimal(5,2) NOT NULL,
  `status` enum('Menunggu','Lulus','Tolak') NOT NULL DEFAULT 'Menunggu',
  `dokumen_laporan` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `hasil_plagiarisme`
--

INSERT INTO `hasil_plagiarisme` (`id`, `id_progres`, `tanggal_cek`, `persentase_kemiripan`, `status`, `dokumen_laporan`) VALUES
(2, 23, '2026-01-01', 0.00, 'Lulus', NULL),
(3, 24, '2026-01-26', 0.00, 'Lulus', NULL),
(4, 25, '2026-01-26', 0.00, 'Lulus', NULL),
(5, 26, '2026-01-26', 0.00, 'Lulus', NULL),
(6, 27, '2026-01-27', 0.00, 'Lulus', NULL),
(10, 32, '2026-01-27', 0.00, 'Menunggu', NULL),
(11, 33, '2026-01-27', 0.00, 'Menunggu', NULL);

-- --------------------------------------------------------

--
-- Struktur dari tabel `jenis_ujian_skripsi`
--

CREATE TABLE `jenis_ujian_skripsi` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `nama` varchar(100) NOT NULL,
  `rumus` enum('informatika sempro','informatika sidang','non informatika','informatika 2025') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `jenis_ujian_skripsi`
--

INSERT INTO `jenis_ujian_skripsi` (`id`, `nama`, `rumus`) VALUES
(1, 'Seminar Proposal Teknik Lama', 'informatika sempro'),
(2, 'Seminar Pendadaran Teknik Lama', 'informatika sidang'),
(3, 'Seminar Proposal Teknik Industri', 'non informatika'),
(4, 'Seminar Pendadaran Teknik Industri', 'non informatika'),
(5, 'Seminar Proposal Teknik Informatika 2025', 'informatika 2025'),
(6, 'Seminar Pendadaran Teknik Informatika 2025', 'informatika 2025'),
(7, 'Seminar Proposal Teknologi Informasi D3', 'informatika 2025'),
(8, 'Seminar Pendadaran Teknologi Informasi D3', 'informatika 2025');

-- --------------------------------------------------------

--
-- Struktur dari tabel `log_aktivitas`
--

CREATE TABLE `log_aktivitas` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `id_user` bigint(20) UNSIGNED NOT NULL,
  `user_role` enum('dosen','mahasiswa','operator') NOT NULL,
  `kategori` varchar(50) NOT NULL COMMENT 'Contoh: Progres, Judul, Akun, Koreksi',
  `deskripsi` text NOT NULL COMMENT 'Deskripsi detail aksi yang dilakukan',
  `id_data_terkait` bigint(20) UNSIGNED DEFAULT NULL COMMENT 'ID data yang terpengaruh (e.g., id_skripsi, id_progres)',
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `log_aktivitas`
--

INSERT INTO `log_aktivitas` (`id`, `id_user`, `user_role`, `kategori`, `deskripsi`, `id_data_terkait`, `timestamp`) VALUES
(1, 9003, 'dosen', 'Koreksi', 'Memberikan status **ACC Penuh** Bab  sebagai P1', 18, '2025-11-22 16:08:02'),
(2, 9011, 'mahasiswa', 'Progres', 'Mengunggah file Bab 1 dan menunggu verifikasi plagiat oleh Operator.', 22, '2025-11-22 16:53:24'),
(3, 1, 'operator', 'Plagiarisme', 'Memverifikasi cek plagiat ID: 1 dengan status: Lulus', 1, '2025-11-22 16:53:46'),
(4, 9003, 'dosen', 'Koreksi', 'Memberikan status **ACC Penuh** Bab  sebagai P1', 22, '2025-11-22 16:54:37'),
(5, 1, 'operator', 'Akun', 'Menambahkan akun baru: dosen (Wibran)', NULL, '2025-11-22 16:56:30'),
(6, 9011, 'mahasiswa', 'Judul', 'Memperbarui judul skripsi: Mengadili Jokowi secara Sistematis', NULL, '2025-11-22 16:57:31'),
(7, 1, 'operator', 'Penugasan', 'Mengatur Pembimbing Skripsi ID: 1', 1, '2025-12-31 18:24:26'),
(8, 1, 'operator', 'Penugasan', 'Mengatur Pembimbing Skripsi ID: 1', 1, '2025-12-31 18:24:33'),
(9, 9004, 'mahasiswa', 'Judul', 'Input/Update judul: NGGAK TAHU NJIR, KOK TANYA SAYAjjj', NULL, '2026-01-01 07:01:49'),
(10, 9004, 'mahasiswa', 'Progres', 'Unggah BAB 1', 23, '2026-01-01 07:20:30'),
(11, 1, 'operator', 'Plagiarisme', 'Memverifikasi cek plagiat ID: 2 dengan status: Lulus', 2, '2026-01-01 07:55:43'),
(12, 1, 'operator', 'Akun', 'Menambahkan akun baru: mahasiswa (mhs1)', NULL, '2026-01-23 09:57:26'),
(13, 1, 'operator', 'Akun', 'Menambahkan akun baru: mahasiswa (wahyu)', NULL, '2026-01-26 02:41:19'),
(14, 1, 'operator', 'Akun', 'Menambahkan akun baru: mahasiswa (mhs2)', NULL, '2026-01-26 15:32:24'),
(15, 10002, 'mahasiswa', 'Judul', 'Input/Update judul: ini judul', NULL, '2026-01-26 16:12:59'),
(16, 10002, 'mahasiswa', 'Progres', 'Unggah BAB 1', 24, '2026-01-26 16:21:38'),
(17, 1, 'operator', 'Plagiarisme', 'Memverifikasi cek plagiat ID: 3 dengan status: Lulus', 3, '2026-01-26 16:22:14'),
(18, 9003, 'dosen', 'Koreksi', 'Memberikan status **ACC Penuh** Bab 1 sebagai P2', 24, '2026-01-26 16:22:45'),
(19, 9005, 'dosen', 'Koreksi', 'Memberikan status **ACC Penuh** Bab 1 sebagai P1', 24, '2026-01-26 16:25:03'),
(20, 10002, 'mahasiswa', 'Progres', 'Unggah BAB 2', 25, '2026-01-26 16:25:26'),
(21, 1, 'operator', 'Plagiarisme', 'Memverifikasi cek plagiat ID: 4 dengan status: Lulus', 4, '2026-01-26 16:26:23'),
(22, 9003, 'dosen', 'Koreksi', 'Memberikan status **ACC Penuh** Bab 2 sebagai P2', 25, '2026-01-26 16:26:46'),
(23, 9005, 'dosen', 'Koreksi', 'Memberikan status **ACC Penuh** Bab 2 sebagai P1', 25, '2026-01-26 16:27:03'),
(24, 9003, 'dosen', 'Koreksi', 'Memberikan status **ACC Penuh** Bab 2 sebagai P2', 25, '2026-01-26 16:27:32'),
(25, 10002, 'mahasiswa', 'Progres', 'Unggah BAB 3', 26, '2026-01-26 16:27:49'),
(26, 1, 'operator', 'Plagiarisme', 'Memverifikasi cek plagiat ID: 5 dengan status: Lulus', 5, '2026-01-26 16:28:09'),
(27, 9003, 'dosen', 'Koreksi', 'Memberikan status **ACC Penuh** Bab 3 sebagai P2', 26, '2026-01-26 16:28:23'),
(28, 9005, 'dosen', 'Koreksi', 'Memberikan status **ACC Penuh** Bab 3 sebagai P1', 26, '2026-01-26 16:28:35'),
(29, 1, 'operator', 'Akun', 'Menambahkan akun baru: mahasiswa (mhs3)', NULL, '2026-01-27 06:45:20'),
(30, 1, 'operator', 'Akun', 'Menambahkan akun baru: dosen (pak wahyu)', NULL, '2026-01-27 06:46:35'),
(31, 10002, 'mahasiswa', 'Progres', 'Unggah BAB 1', 27, '2026-01-27 07:00:27'),
(32, 1, 'operator', 'Plagiarisme', 'Memverifikasi cek plagiat ID: 6 dengan status: Lulus', 6, '2026-01-27 07:00:53'),
(33, 9003, 'dosen', 'Koreksi', 'Memberikan status **Revisi** Bab 1 sebagai P2', 27, '2026-01-27 07:05:28'),
(34, 9003, 'dosen', 'Koreksi', 'Memberikan status **ACC Sebagian** Bab 1 sebagai P2', 27, '2026-01-27 07:06:25'),
(35, 9003, 'dosen', 'Koreksi', 'Memberikan status **ACC Penuh** Bab 1 sebagai P2', 27, '2026-01-27 07:08:31'),
(36, 9003, 'dosen', 'Koreksi', 'Memberikan status **ACC Penuh** Bab 1 sebagai P2', 27, '2026-01-27 07:09:04'),
(37, 9003, 'dosen', 'Koreksi', 'Memberikan status **ACC Penuh** Bab 1 sebagai P2', 27, '2026-01-27 07:09:40'),
(38, 9005, 'dosen', 'Koreksi', 'Memberikan status **ACC Penuh** Bab 1 sebagai P1', 27, '2026-01-27 07:26:10'),
(39, 10002, 'mahasiswa', 'Progres', 'Unggah BAB 2', 29, '2026-01-27 07:29:15'),
(40, 1, 'operator', 'Plagiarisme', 'Memverifikasi cek plagiat ID: 7 dengan status: Lulus', 7, '2026-01-27 07:29:58'),
(41, 9003, 'dosen', 'Koreksi', 'Memberikan status **Revisi** Bab 2 sebagai P2', 29, '2026-01-27 07:40:56'),
(42, 9003, 'dosen', 'Koreksi', 'Memberikan status **ACC Sebagian** Bab 2 sebagai P2', 29, '2026-01-27 07:46:26'),
(43, 9004, 'mahasiswa', 'Progres', 'Unggah BAB 1', 30, '2026-01-27 15:00:40'),
(44, 1, 'operator', 'Plagiarisme', 'Memverifikasi cek plagiat ID: 8 dengan status: Lulus', 8, '2026-01-27 15:06:18'),
(45, 9004, 'mahasiswa', 'Progres', 'Unggah BAB 1', 31, '2026-01-27 15:07:17'),
(46, 9004, 'mahasiswa', 'Progres', 'Unggah BAB 1', 32, '2026-01-27 16:25:27'),
(47, 1, 'operator', 'Plagiarisme', 'Verifikasi ID: 32 Status: Tolak', 32, '2026-01-27 16:53:28'),
(48, 1, 'operator', 'Plagiarisme', 'Verifikasi ID: 32 Status: Lulus', 32, '2026-01-27 16:54:00'),
(49, 9004, 'mahasiswa', 'Progres', 'Unggah BAB 1', 33, '2026-01-27 16:55:20'),
(50, 9003, 'dosen', 'Koreksi', 'Memberikan nilai ACC (100) sebagai Pembimbing 1', 32, '2026-01-27 17:10:45'),
(51, 9004, 'mahasiswa', 'Progres', 'Unggah Revisi BAB 1', 34, '2026-01-27 17:27:23'),
(52, 9004, 'mahasiswa', 'Progres', 'Unggah Progres Revisi BAB 1', 35, '2026-01-28 18:35:26'),
(53, 9004, 'mahasiswa', 'Progres', 'Unggah Progres Revisi BAB 1', 36, '2026-01-28 18:35:40'),
(54, 9004, 'mahasiswa', 'Progres', 'Unggah Progres Revisi BAB 1', 37, '2026-01-28 18:38:32');

-- --------------------------------------------------------

--
-- Struktur dari tabel `mahasiswa_skripsi`
--

CREATE TABLE `mahasiswa_skripsi` (
  `npm` varchar(20) NOT NULL,
  `nama` varchar(100) NOT NULL,
  `prodi` varchar(50) NOT NULL,
  `semester` int(11) NOT NULL,
  `periode` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `mahasiswa_skripsi`
--

INSERT INTO `mahasiswa_skripsi` (`npm`, `nama`, `prodi`, `semester`, `periode`) VALUES
('123', 'Mahasiswa Dummy', 'Teknik Informatika', 7, '2025/2026 (Genap)');

-- --------------------------------------------------------

--
-- Struktur dari tabel `mstr_akun`
--

CREATE TABLE `mstr_akun` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` text NOT NULL,
  `nama` varchar(100) NOT NULL,
  `foto` text DEFAULT NULL,
  `role` enum('dosen','mahasiswa','operator','tata_usaha') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `mstr_akun`
--

INSERT INTO `mstr_akun` (`id`, `username`, `password`, `nama`, `foto`, `role`) VALUES
(1, 'admin', 'admin', 'Operator FT', '9d1a523eeda16bd038923cab38fc0c70.png', 'operator'),
(2, 'tatausahaft', 'tatausahaft', 'Tata Usaha FT', NULL, 'tata_usaha'),
(3, '0602047502', '$2y$10$/zQ7QgWuDxEuJP9yXNPz7eH58otjA3BW3I2Shd5TL9H0bgdKhicPu', 'Mukhtar Hanafi, ST., MCs.', '0ac2ec8183d84c2880f3cc0896ad1abc.JPG', 'dosen'),
(4, '0616127102', '$2y$10$6V6jrZa26PuYXS2jWa19EuradXrZc5DnalE6evJCA.bX0BxfBnNUq', 'R. Arri Widyanto, S.Kom.,MT', 'bd10699e06a0baf6d58bb681be27248d.JPG', 'dosen'),
(5, '0623087901', '$2y$10$6udEasoJ4Cmv0qbBwi5skueggdGS.rrSIvx4ALslFWMw41pcqrMbO', 'Andi Widiyanto, M.Kom', 'f7c9a5c3a7678e1ac07a8e94405d7606.JPG', 'dosen'),
(6, '0623107802', '$2y$10$Jz9Bt9XeFz0eR54eC2OC7uhNanUF9epzRUcEZJAOGkfEpr3ih/3dq', 'Bambang Pujiarto, M.Kom', 'e83601a799be5dbb5b55af9605e8d551.JPG', 'dosen'),
(7, '0624077302', '$2y$10$nka/8mzzBv45hDq14bhq1uZl3WLG3gYAOaB9FtTT.itJpY4pOggE.', 'Nugroho Agung Prabowo, M.Kom., Ph.D', '4db64be85fa33ff0511a7389d595daf8.JPG', 'dosen'),
(8, '0605037002', '$2y$10$C3W08YomI/9pgHBvfEKizuz2az.xtPgtItVbozL9LuytXFOZabFq2', 'Nuryanto, ST., M. Kom', 'f308d09c239581637fe3b861c2c91d9d.JPG', 'dosen'),
(9, '0624077101', '$2y$10$9q47XRi/Fw6CppwKLhOq6OWFlWRZl8sLDzv32WvqhnbAwQevjw3UG', 'Purwono Hendradi, M.Kom., Ph.D', '7345bdef042d66404e8cc96c4f957581.JPG', 'dosen'),
(10, '0605107201', '$2y$10$LwEQfOD6XqOyY9ru5xPWZeTxzXDYeG8NklMII9JiGsPA14HJhheU.', 'Dr. Uky Yudatama, S.Si.,M.Kom., M.M.', '52a163938d7eab4d1e7100f9da5f324a.JPG', 'dosen'),
(11, '0601129001', '$2y$10$54UghxtXFYdNGBRUXmz/Qu9LJ.fpf.kgNEVeqGuSoZPEjhSHDLbOi', 'Endah Ratna Arumi, M. Cs.', '03cc0d6a365688b541d3c3a48d916fc1.JPG', 'dosen'),
(12, '0617088801', '$2y$10$dotVs8atHlu/0tYrpNvav.mALStuF9M3Ss5lz38wk8YbPE9EPf032', 'Agus Setiawan, M.Eng.', '952f061697f17ae5ad90ccd13c378c7d.JPG', 'dosen'),
(13, '0512128101', '$2y$10$e26kRA97YMg69/SJYrdgOeqE550UjVv7mJYp21bznhdnmrg/WCK7q', 'Emilya Ully Artha, M.Kom.', '7b5e103e1e60fd28a1d5a352e96b393e.JPG', 'dosen'),
(14, '0619048501', '$2y$10$paQuGQRtnv1h2wjTA.UEm.7OfIpHbEXlzJk22VZEWFPdGA9V1z6wK', 'Ardhin Primadewi, S.Si, M.TI.', '02b2b610202babfe1a7c1deb3f483ad0.JPG', 'dosen'),
(15, '0631088203', '$2y$10$9jIcrIboHUhnm.eP/H490e2puHKqLwW3cqy0zfAnBdZX7z3CSWN9C', 'Setiya Nugroho, ST., M.Eng.', '637a5e5aafcfd08bd5c8a68819da332c.JPG', 'dosen'),
(16, '0602058502', '$2y$10$/EHCjpyqmT7QeY/1bvaFXe5B69je2tHihyqn4Xp3w7BmbCMCl07Fm', 'Dimas Sasongko, S. Kom., M. Eng', 'e54a12ea5349fbc20c176f1515b44e4f.JPG', 'dosen'),
(17, '0612117702', '$2y$10$XsS5v74UlxjHyby7toOWqOjEK/Xzc1EdGS.2ZBulayP7lyR8dVRRa', 'Maimunah, S. Si., M. Kom', '43972ba885d631f3bd6c9bcd31565846.JPG', 'dosen'),
(18, '0618129201', '$2y$10$6MiM9I4uYacfsmGLAj1VNeONmAG4cVjvJ3/jbFZE3oGDzGydZZU.G', 'Pristi Sukmasetya, S.Komp., M.Kom', 'a01222aca189c5939dbdc5ddb406e2be.JPG', 'dosen'),
(19, '0631079101', '$2y$10$tguDAdnaxppdo.JN7UajROAy9qNuz/G.MK1gWZCnnisIMCxaYqL1q', 'Rofi Abul Hasani, S.Kom., M.Eng', 'cbd4483a91f02f901d9a8051ea01f1bf.JPG', 'dosen'),
(9001, 'admin_test', '$2y$10$Z9VCUi1tSsZtMAL0.P0Iju1m89gQasvoVR3byKIZ63cWTIn98tA72', 'Operator Dummy', 'default.png', 'operator'),
(9002, 'tu_test', '$2y$10$Z9VCUi1tSsZtMAL0.P0Iju1m89gQasvoVR3byKIZ63cWTIn98tA72', 'Staff TU Dummy', 'default.png', 'tata_usaha'),
(9003, 'dosen_test', 'dosen_test', 'Dosen Dummy', 'dosen_profile_9003_1769169310.jpg', 'dosen'),
(9004, 'rizqy', 'rizqy', 'Mahasiswa Dummy ', 'profile_9004_1769155419.jpg', 'mahasiswa'),
(9005, 'dosen2', 'dosen2', 'Dosen Baru, M.Kom', 'dosen_profile_9005_1769444672.jpg', 'dosen'),
(9011, 'indra', 'qwerty', 'Ahmad Abdillah Indragiri 2', 'profile_9011_1769394164.jpg', 'mahasiswa'),
(9012, 'wibran', 'qwerty', 'Wibran', NULL, 'dosen'),
(9999, 'kaprodi', '123456', 'Bpk. Kaprodi TI, M.Kom', 'dosen_profile_9999_1769444029.jpg', 'dosen'),
(10000, 'mhs1', 'mhs1', 'mhs1', 'profile_10000_1769170417.jpg', 'mahasiswa'),
(10001, 'wahyu', '$2y$10$xUKxm.c6sLY7edxdYFOFiOyr4ALTkAxpkhtjl8sl2s836Y24JWARW', 'wahyu', 'profile_10001_1769395507.jpg', 'mahasiswa'),
(10002, 'mhs2', '$2y$10$rk58VTfi4kQw18K38DVKeu4KIinoKflxJGnWe7k/c8.N1u9kQu7Wa', 'mhs2', 'profile_10002_1769496805.jpg', 'mahasiswa'),
(10003, 'mhs3', '$2y$10$ZmhkzpCgj4VuHGZIfuZVnukwLmznS2WY1w0pTjbbqXYKFuOKoDSLa', 'mhs3', NULL, 'mahasiswa'),
(10005, 'wahyu2', '$2y$10$cC.hiJkQz945qgPLyLh5E.89.kqRJVzp3bSlvHmz.dNlPU8oFWFKe', 'pak wahyu', NULL, 'dosen');

-- --------------------------------------------------------

--
-- Struktur dari tabel `mstr_komponen_nilai_ujian_skripsi`
--

CREATE TABLE `mstr_komponen_nilai_ujian_skripsi` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `keterangan` text NOT NULL,
  `keterangan_berita_acara` text DEFAULT NULL,
  `gambar` text DEFAULT NULL,
  `bobot` float NOT NULL,
  `bobot_berita_acara` float DEFAULT NULL,
  `jenis_nilai` enum('naskah','presentasi') NOT NULL,
  `status` enum('aktif','tidak aktif') NOT NULL,
  `id_jenis_ujian_skripsi` bigint(20) UNSIGNED NOT NULL,
  `jenis_indikator` enum('1-5','10-100') NOT NULL,
  `urutan` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `mstr_komponen_nilai_ujian_skripsi`
--

INSERT INTO `mstr_komponen_nilai_ujian_skripsi` (`id`, `keterangan`, `keterangan_berita_acara`, `gambar`, `bobot`, `bobot_berita_acara`, `jenis_nilai`, `status`, `id_jenis_ujian_skripsi`, `jenis_indikator`, `urutan`) VALUES
(1, 'Relevansi topik TA dengan (1) body of knowledge program studi, (2) profil lulusan dan CPL, serta (3) roadmap penelitian Fakultas/Prodi.', 'Relevansi topik TA dengan (1) body of knowledge program studi, (2) profil lulusan dan CPL, serta (3) roadmap penelitian Fakultas/Prodi.', '1558595cd9426d9306a5a1d9b60d5936.png', 1, NULL, 'naskah', 'aktif', 5, '10-100', 1),
(2, 'Kualitas proses deduktif: (1) Inovasi dan kebaruan ide/gagasan, (2) Perumusan masalah, (3) Tinjauan literatur, (4) Kerangka konsep, dan (5) Hipotesis.', 'Kualitas proses deduktif: (1) Inovasi dan kebaruan ide/gagasan, (2) Perumusan masalah, (3) Tinjauan literatur, (4) Kerangka konsep, dan (5) Hipotesis.', '68b6727c22d6dde4829432a699731b1f.png', 1, NULL, 'naskah', 'aktif', 5, '10-100', 2),
(3, 'Presentasi laporan: (1) Typografi, (2) Kerapian dan kejelasan instrumen pendukung berupa gambar dan tabel, dan (3) Struktur dan keterkaitan antar bab', 'Presentasi laporan: (1) Typografi, (2) Kerapian dan kejelasan instrumen pendukung berupa gambar dan tabel, dan (3) Struktur dan keterkaitan antar bab', '00a88dc864b36ccbc411ae2856ed1fd3.png', 1, NULL, 'naskah', 'aktif', 5, '10-100', 3),
(4, 'Penguasaan materi: (1) Kemampuan mengkomunikasikan ide, dan (2) Kemampuan merespons pertanyaan.', 'Penguasaan materi: (1) Kemampuan mengkomunikasikan ide, dan (2) Kemampuan merespons pertanyaan.', '194099c489861b6d7475bcbedddbb23f.png', 1, NULL, 'naskah', 'aktif', 5, '10-100', 4),
(5, 'Kemampuan bekerja sama dalam tim (termasuk dengan pembimbing), berbagi tanggung jawab, dan memberikan kontribusi yang konstruktif.', 'Kemampuan bekerja sama dalam tim (termasuk dengan pembimbing), berbagi tanggung jawab, dan memberikan kontribusi yang konstruktif.', '8e20ca953f23af12d5e87169806f3d55.png', 1, NULL, 'naskah', 'aktif', 5, '10-100', 5),
(6, 'Relevansi topik TA dengan (1) body of knowledge program studi, (2) profil lulusan dan CPL, serta (3) roadmap penelitian Fakultas/Prodi.', 'Relevansi topik TA dengan (1) body of knowledge program studi, (2) profil lulusan dan CPL, serta (3) roadmap penelitian Fakultas/Prodi.', '30f16d13a3d35cf38e943c8cc92f2595.png', 1, NULL, 'naskah', 'aktif', 6, '10-100', 1),
(7, 'Kualitas proses deduktif: (1) Inovasi dan kebaruan ide/gagasan, (2) Perumusan masalah, (3) Tinjauan literatur, (4) Kerangka konsep, dan (5) Hipotesis.', 'Kualitas proses deduktif: (1) Inovasi dan kebaruan ide/gagasan, (2) Perumusan masalah, (3) Tinjauan literatur, (4) Kerangka konsep, dan (5) Hipotesis.', 'd7dc329a37980af2ed1207c3302645f0.png', 1, NULL, 'naskah', 'aktif', 6, '10-100', 2),
(8, 'Kualitas proses induktif: (1) Keakuratan metode, (2) Penyajian hasil, (3) Pembahasan, dan (4) Kualitas penyimpulan.', 'Kualitas proses induktif: (1) Keakuratan metode, (2) Penyajian hasil, (3) Pembahasan, dan (4) Kualitas penyimpulan.', '1270d00813e21738235d40b5c921e783.png', 1, NULL, 'naskah', 'aktif', 6, '10-100', 3),
(9, 'Presentasi laporan: (1) Typografi, (2) Kerapian dan kejelasan instrumen pendukung berupa gambar dan tabel, dan (3) Struktur dan keterkaitan antar bab.', 'Presentasi laporan: (1) Typografi, (2) Kerapian dan kejelasan instrumen pendukung berupa gambar dan tabel, dan (3) Struktur dan keterkaitan antar bab.', '793c47e831d84547c0590191ea35f685.png', 1, NULL, 'naskah', 'aktif', 6, '10-100', 4),
(10, 'Penguasaan materi: (1) Kemampuan mengkomunikasikan ide, dan (2) Kemampuan merespons pertanyaan.', 'Penguasaan materi: (1) Kemampuan mengkomunikasikan ide, dan (2) Kemampuan merespons pertanyaan.', '153243b967e196ba9df04714014a5c5b.png', 1, NULL, 'naskah', 'aktif', 6, '10-100', 5),
(11, 'Kemampuan bekerja sama dalam tim (termasuk dengan pembimbing), berbagi tanggung jawab, dan memberikan kontribusi yang konstruktif.', 'Kemampuan bekerja sama dalam tim (termasuk dengan pembimbing), berbagi tanggung jawab, dan memberikan kontribusi yang konstruktif.', '03f5ab0bba9fdb658b5092f2126e99c5.png', 1, NULL, 'naskah', 'aktif', 6, '10-100', 6),
(12, 'Relevansi topik TA dengan (1) body of knowledge program studi, (2) profil lulusan dan CPL, serta (3) roadmap penelitian Fakultas/Prodi.', 'Relevansi topik TA dengan (1) body of knowledge program studi, (2) profil lulusan dan CPL, serta (3) roadmap penelitian Fakultas/Prodi.', '10af34cd2db76fcc12ae3c810e481cbd.png', 1, NULL, 'naskah', 'aktif', 7, '10-100', 1),
(13, 'Kualitas proses deduktif: (1) Inovasi dan kebaruan ide/gagasan, (2) Perumusan masalah, (3) Tinjauan literatur, (4) Kerangka konsep, dan (5) Hipotesis.', ' Kualitas proses deduktif: (1) Inovasi dan kebaruan ide/gagasan, (2) Perumusan masalah, (3) Tinjauan literatur, (4) Kerangka konsep, dan (5) Hipotesis.', 'ca3737a1bbdb161a7a1405a302642225.png', 1, NULL, 'naskah', 'aktif', 7, '10-100', 2),
(14, 'Presentasi laporan: (1) Typografi, (2) Kerapian dan kejelasan instrumen pendukung berupa gambar dan tabel, dan (3) Struktur dan keterkaitan antar bab.', 'Presentasi laporan: (1) Typografi, (2) Kerapian dan kejelasan instrumen pendukung berupa gambar dan tabel, dan (3) Struktur dan keterkaitan antar bab.', 'a2e2dc8243801a290cb7e28cc2b598a6.png', 1, NULL, 'naskah', 'aktif', 7, '10-100', 3),
(15, 'Penguasaan materi: (1) Kemampuan mengkomunikasikan ide, dan (2) Kemampuan merespons pertanyaan.', 'Penguasaan materi: (1) Kemampuan mengkomunikasikan ide, dan (2) Kemampuan merespons pertanyaan.', '87b328f22df8b4ea14f291b55b5bf7a3.png', 1, NULL, 'naskah', 'aktif', 7, '10-100', 4),
(16, 'Kemampuan bekerja sama dalam tim (termasuk dengan pembimbing), berbagi tanggung jawab, dan memberikan kontribusi yang konstruktif.', 'Kemampuan bekerja sama dalam tim (termasuk dengan pembimbing), berbagi tanggung jawab, dan memberikan kontribusi yang konstruktif.', '6cc15996a8550126bd391c3fa288bab6.png', 1, NULL, 'naskah', 'aktif', 7, '10-100', 5),
(17, 'Relevansi topik TA dengan (1) body of knowledge program studi, (2) profil lulusan dan CPL, serta (3) roadmap penelitian Fakultas/Prodi.', 'Relevansi topik TA dengan (1) body of knowledge program studi, (2) profil lulusan dan CPL, serta (3) roadmap penelitian Fakultas/Prodi.', 'e41979ed2d729f40e5af59ce36044d87.png', 1, NULL, 'naskah', 'aktif', 8, '10-100', 1),
(18, 'Kualitas proses deduktif: (1) Inovasi dan kebaruan ide/gagasan, (2) Perumusan masalah, (3) Tinjauan literatur, (4) Kerangka konsep, dan (5) Hipotesis.', 'Kualitas proses deduktif: (1) Inovasi dan kebaruan ide/gagasan, (2) Perumusan masalah, (3) Tinjauan literatur, (4) Kerangka konsep, dan (5) Hipotesis.', 'bb6506bb63611cde3f52eeabf90d621b.png', 1, NULL, 'naskah', 'aktif', 8, '10-100', 2),
(19, 'Kualitas proses induktif: (1) Keakuratan metode, (2) Penyajian hasil, (3) Pembahasan, dan (4) Kualitas penyimpulan.*', 'Kualitas proses induktif: (1) Keakuratan metode, (2) Penyajian hasil, (3) Pembahasan, dan (4) Kualitas penyimpulan.*', '4dd439a3cfc2c3a8858517b627adc3e4.png', 1, NULL, 'naskah', 'aktif', 8, '10-100', 3),
(20, 'Presentasi laporan: (1) Typografi, (2) Kerapian dan kejelasan instrumen pendukung berupa gambar dan tabel, dan (3) Struktur dan keterkaitan antar bab.', 'Presentasi laporan: (1) Typografi, (2) Kerapian dan kejelasan instrumen pendukung berupa gambar dan tabel, dan (3) Struktur dan keterkaitan antar bab.', '0be98a43290f2d82271609f2d0f5d4ed.png', 1, NULL, 'naskah', 'aktif', 8, '10-100', 4),
(21, 'Penguasaan materi: (1) Kemampuan mengkomunikasikan ide, dan (2) Kemampuan merespons pertanyaan.', 'Penguasaan materi: (1) Kemampuan mengkomunikasikan ide, dan (2) Kemampuan merespons pertanyaan.', '9ed6417321c69d4b1565c885969fc495.png', 1, NULL, 'naskah', 'aktif', 8, '10-100', 5),
(22, 'Kemampuan bekerja sama dalam tim (termasuk dengan pembimbing), berbagi tanggung jawab, dan memberikan kontribusi yang konstruktif.', 'Kemampuan bekerja sama dalam tim (termasuk dengan pembimbing), berbagi tanggung jawab, dan memberikan kontribusi yang konstruktif.', 'eb38cf4091b5023e10cb47f0ad219154.png', 1, NULL, 'naskah', 'aktif', 8, '10-100', 6);

-- --------------------------------------------------------

--
-- Struktur dari tabel `progres_skripsi`
--

CREATE TABLE `progres_skripsi` (
  `id` int(11) NOT NULL,
  `npm` varchar(20) DEFAULT NULL,
  `bab` int(11) DEFAULT NULL,
  `file` varchar(255) DEFAULT NULL,
  `komentar_dosen1` text DEFAULT NULL,
  `komentar_dosen2` text DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `nilai_dosen1` varchar(20) DEFAULT NULL,
  `nilai_dosen2` varchar(20) DEFAULT NULL,
  `progres_dosen1` int(11) DEFAULT 0,
  `progres_dosen2` int(11) DEFAULT 0,
  `is_published_to_sita` tinyint(1) DEFAULT 0,
  `published_at` datetime DEFAULT NULL,
  `tgl_upload` datetime DEFAULT current_timestamp(),
  `status_plagiasi` varchar(20) DEFAULT 'Menunggu',
  `persentase_kemiripan` int(11) DEFAULT 0,
  `tgl_verifikasi` datetime DEFAULT NULL,
  `tgl_koreksi_d1` datetime DEFAULT NULL,
  `tgl_koreksi_d2` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `progres_skripsi`
--

INSERT INTO `progres_skripsi` (`id`, `npm`, `bab`, `file`, `komentar_dosen1`, `komentar_dosen2`, `created_at`, `nilai_dosen1`, `nilai_dosen2`, `progres_dosen1`, `progres_dosen2`, `is_published_to_sita`, `published_at`, `tgl_upload`, `status_plagiasi`, `persentase_kemiripan`, `tgl_verifikasi`, `tgl_koreksi_d1`, `tgl_koreksi_d2`) VALUES
(23, '123', 1, 'Progres_Mahasiswa_Dummy_123_BAB1_1767252030.pdf', NULL, NULL, '2026-01-01 08:20:30', 'Menunggu', 'Menunggu', 0, 0, 0, NULL, '2026-01-01 08:20:30', 'Menunggu', 0, NULL, NULL, NULL),
(24, '123456790', 1, 'Progres_mhs2_123456790_BAB1_1769444498.pdf', 'lanjut bosssBab ini sudah baik, segera lanjutkan ke Bab berikutnya (ACC).\r\n\r\n', 'gass bossBab ini sudah baik, segera lanjutkan ke Bab berikutnya (ACC).\r\n\r\n', '2026-01-26 17:21:38', 'ACC', 'ACC', 100, 100, 0, NULL, '2026-01-26 17:21:38', 'Menunggu', 0, NULL, NULL, NULL),
(25, '123456790', 2, 'Progres_mhs2_123456790_BAB2_1769444726.pdf', 'lanjut Bab ini sudah baik, segera lanjutkan ke Bab berikutnya (ACC).\r\n\r\n', 'lanjut Bab ini sudah baik, segera lanjutkan ke Bab berikutnya (ACC).\r\n\r\n', '2026-01-26 17:25:26', 'ACC', 'ACC', 100, 100, 0, NULL, '2026-01-26 17:25:26', 'Menunggu', 0, NULL, NULL, NULL),
(26, '123456790', 3, 'Progres_mhs2_123456790_BAB3_1769444869.pdf', 'Bab ini sudah baik, segera lanjutkan ke Bab berikutnya (ACC).\r\n\r\n', 'Bab ini sudah baik, segera lanjutkan ke Bab berikutnya (ACC).\r\n\r\n', '2026-01-26 17:27:49', 'ACC', 'ACC', 100, 100, 0, NULL, '2026-01-26 17:27:49', 'Menunggu', 0, NULL, NULL, NULL),
(27, '123456790A', 1, 'Progres_mhs2_123456790A_BAB1_1769497227.pdf', 'Revisi Bab Pendahuluan: Fokus pada gap penelitian.\r\n\r\n', 'lanjut Revisi Bab Pendahuluan: Fokus pada gap penelitian.\r\n\r\n', '2026-01-27 08:00:27', 'ACC', 'ACC', 100, 100, 0, NULL, '2026-01-27 08:00:27', 'Tolak', 45, '2026-01-27 17:40:57', NULL, NULL),
(32, '1234567896', 1, 'Progres_Mahasiswa_Dummy__1234567896_BAB1_1769531127.pdf', 'Bab ini sudah baik, segera lanjutkan ke Bab berikutnya (ACC).\r\n\r\n', NULL, '2026-01-27 17:25:27', 'ACC', 'Menunggu', 100, 0, 0, NULL, '2026-01-27 17:25:27', 'Lulus', 7, '2026-01-27 17:54:00', '2026-01-27 18:10:45', NULL),
(33, '1234567896', 1, 'Progres_Mahasiswa_Dummy__1234567896_BAB1_1769532920.pdf', NULL, NULL, '2026-01-27 17:55:20', 'Menunggu', 'Menunggu', 0, 0, 0, NULL, '2026-01-27 17:55:20', 'Menunggu', 0, NULL, NULL, NULL),
(34, '1234567896', 1, 'Progres_Mahasiswa_Dummy__1234567896_BAB1_REVISI_1769534843.pdf', NULL, NULL, '2026-01-27 18:27:23', 'Menunggu', 'Menunggu', 0, 0, 0, NULL, '2026-01-27 18:27:23', 'Menunggu', 0, NULL, NULL, NULL),
(35, '1234567896', 1, 'Progres_Mahasiswa_Dummy__1234567896_BAB1_REVISI_1769625326.pdf', NULL, NULL, '2026-01-28 19:35:26', 'Menunggu', 'Menunggu', 0, 0, 0, NULL, '2026-01-28 19:35:26', 'Menunggu', 0, NULL, NULL, NULL),
(36, '1234567896', 1, 'Progres_Mahasiswa_Dummy__1234567896_BAB1_REVISI_1769625340.pdf', NULL, NULL, '2026-01-28 19:35:40', 'Menunggu', 'Menunggu', 0, 0, 0, NULL, '2026-01-28 19:35:40', 'Menunggu', 0, NULL, NULL, NULL),
(37, '1234567896', 1, 'Progres_Mahasiswa_Dummy__1234567896_BAB1_REVISI_1769625512.pdf', NULL, NULL, '2026-01-28 19:38:32', 'Menunggu', 'Menunggu', 0, 0, 0, NULL, '2026-01-28 19:38:32', 'Menunggu', 0, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Struktur dari tabel `saran_ujian_skripsi`
--

CREATE TABLE `saran_ujian_skripsi` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `id_ujian_skripsi` bigint(20) UNSIGNED NOT NULL,
  `id_penguji` bigint(20) UNSIGNED NOT NULL,
  `saran` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `skripsi`
--

CREATE TABLE `skripsi` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `id_mahasiswa` bigint(20) UNSIGNED NOT NULL,
  `tema` enum('Software Engineering','Networking','Artificial Intelligence') NOT NULL,
  `judul` text NOT NULL,
  `pembimbing1` bigint(20) UNSIGNED DEFAULT NULL,
  `pembimbing2` bigint(20) UNSIGNED DEFAULT NULL,
  `status_acc_kaprodi` enum('menunggu','diterima','ditolak') DEFAULT 'menunggu',
  `tgl_pengajuan_judul` date NOT NULL,
  `skema` enum('Reguler','Penyetaraan') NOT NULL,
  `naskah` text DEFAULT NULL,
  `nilai_akhir` decimal(5,2) DEFAULT NULL,
  `status_sempro` enum('Menunggu Syarat','Siap Sempro','Disetujui Sempro') DEFAULT 'Menunggu Syarat'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `skripsi`
--

INSERT INTO `skripsi` (`id`, `id_mahasiswa`, `tema`, `judul`, `pembimbing1`, `pembimbing2`, `status_acc_kaprodi`, `tgl_pengajuan_judul`, `skema`, `naskah`, `nilai_akhir`, `status_sempro`) VALUES
(1, 9004, 'Software Engineering', 'NGGAK TAHU NJIR, KOK TANYA SAYAjjj', 9003, 9005, 'diterima', '2025-11-19', 'Reguler', NULL, NULL, 'Menunggu Syarat'),
(2, 9011, 'Software Engineering', 'Mengadili Jokowi secara Sistematis', 9003, 9005, 'diterima', '2025-11-22', 'Reguler', NULL, NULL, 'Menunggu Syarat'),
(3, 10002, 'Software Engineering', 'ini judul', 9005, 9003, 'diterima', '2026-01-26', 'Reguler', NULL, NULL, 'Menunggu Syarat');

-- --------------------------------------------------------

--
-- Struktur dari tabel `histori_judul_skripsi`
--

CREATE TABLE `histori_judul_skripsi` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `id_skripsi` bigint(20) UNSIGNED NOT NULL,
  `judul` text NOT NULL,
  `tema` enum('Software Engineering','Networking','Artificial Intelligence') NOT NULL,
  `tgl_pengajuan_judul` date NOT NULL,
  `dibuat_pada` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `syarat_pendadaran`
--

CREATE TABLE `syarat_pendadaran` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `naskah` varchar(255) NOT NULL,
  `id_ujian_skripsi` bigint(20) UNSIGNED NOT NULL,
  `berita_acara_seminar` varchar(255) DEFAULT NULL,
  `daftar_nilai_sementara` varchar(255) NOT NULL,
  `krs_terbaru` varchar(255) NOT NULL,
  `dokumen_identitas` varchar(255) NOT NULL,
  `sertifikat_toefl_niit` varchar(255) DEFAULT NULL,
  `sertifikat_office_puskom` varchar(255) NOT NULL,
  `sertifikat_btq_ibadah` varchar(255) NOT NULL,
  `sertifikat_bahasa` varchar(255) NOT NULL,
  `sertifikat_kompetensi_ujian_komprehensif` varchar(255) NOT NULL,
  `sertifikat_semaba_ppk_masta` varchar(255) NOT NULL,
  `sertifikat_kkn` varchar(255) NOT NULL,
  `buku_kendali_bimbingan` varchar(255) NOT NULL,
  `bukti_pembayaran_sidang` varchar(255) NOT NULL,
  `ipk` decimal(3,2) DEFAULT NULL,
  `status` tinyint(4) DEFAULT NULL,
  `catatan` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `syarat_sempro`
--

CREATE TABLE `syarat_sempro` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `naskah` varchar(255) NOT NULL,
  `id_ujian_skripsi` bigint(20) UNSIGNED NOT NULL,
  `fotokopi_daftar_nilai` varchar(255) DEFAULT NULL,
  `fotokopi_krs` varchar(255) DEFAULT NULL,
  `buku_kendali_bimbingan` varchar(255) DEFAULT NULL,
  `lembar_revisi_ba_dan_tanda_terima_laporan_kp` varchar(255) DEFAULT NULL,
  `bukti_seminar_teman` varchar(255) DEFAULT NULL,
  `status` tinyint(4) DEFAULT NULL,
  `catatan` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `syarat_sempro`
--

INSERT INTO `syarat_sempro` (`id`, `naskah`, `id_ujian_skripsi`, `fotokopi_daftar_nilai`, `fotokopi_krs`, `buku_kendali_bimbingan`, `lembar_revisi_ba_dan_tanda_terima_laporan_kp`, `bukti_seminar_teman`, `status`, `catatan`) VALUES
(1, '', 1, NULL, NULL, NULL, NULL, NULL, 0, NULL);

-- --------------------------------------------------------

--
-- Struktur dari tabel `tbl_nilai_ujian_skripsi`
--

CREATE TABLE `tbl_nilai_ujian_skripsi` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `id_komponen_nilai` bigint(20) UNSIGNED NOT NULL,
  `id_ujian_skripsi` bigint(20) UNSIGNED NOT NULL,
  `id_penguji` bigint(20) UNSIGNED NOT NULL,
  `nilai` float NOT NULL CHECK (`nilai` >= 0)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `tbl_pesan`
--

CREATE TABLE `tbl_pesan` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `id_pengirim` bigint(20) UNSIGNED NOT NULL,
  `id_penerima` bigint(20) UNSIGNED NOT NULL,
  `pesan` text DEFAULT NULL COMMENT 'Isi pesan teks',
  `gambar` varchar(255) DEFAULT NULL COMMENT 'Nama file gambar jika ada',
  `waktu` timestamp NOT NULL DEFAULT current_timestamp(),
  `is_read` tinyint(1) NOT NULL DEFAULT 0 COMMENT '0=Belum Dibaca, 1=Sudah Dibaca'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `tbl_pesan`
--

INSERT INTO `tbl_pesan` (`id`, `id_pengirim`, `id_penerima`, `pesan`, `gambar`, `waktu`, `is_read`) VALUES
(1, 9004, 9003, 'woyy', NULL, '2025-11-27 14:35:12', 0),
(2, 9003, 9004, '', '7d096a9bcb4af2bef3df378ecaa2e47d.jpg', '2025-11-27 14:38:00', 0),
(3, 9003, 9004, 'uyy', NULL, '2025-12-31 06:57:23', 0),
(4, 9003, 9011, 'uyy', NULL, '2025-12-31 06:57:27', 0),
(5, 9999, 9004, 'hadeh', NULL, '2025-12-31 18:22:55', 0),
(6, 9999, 9004, 'judul kok kongono le', NULL, '2025-12-31 18:23:01', 0),
(7, 9999, 9004, 'halo le', NULL, '2026-01-01 07:08:13', 0),
(8, 9999, 9011, 'halooo kang', NULL, '2026-01-05 16:21:08', 0),
(9, 9004, 9003, 'haloo', NULL, '2026-01-11 18:17:36', 0),
(10, 9999, 10002, 'ndang bimbingan le', NULL, '2026-01-26 16:14:21', 0),
(11, 9999, 16, '', '4f326f6af02fd59b0bc2a193f3d8ff52.jpg', '2026-01-26 16:18:32', 0),
(12, 10002, 9003, '', 'ba2bfb3d3dc41db71eb9ab5b8b75e7b4.jpg', '2026-01-26 16:20:10', 0),
(13, 10002, 9003, 'halo dos', NULL, '2026-01-26 16:20:14', 0),
(14, 10002, 9003, 'halooo', NULL, '2026-01-27 07:37:25', 0),
(15, 10002, 9005, 'oyyy', NULL, '2026-01-27 07:37:29', 0);

-- --------------------------------------------------------

--
-- Struktur dari tabel `ujian_skripsi`
--

CREATE TABLE `ujian_skripsi` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `id_skripsi` bigint(20) UNSIGNED NOT NULL,
  `tanggal` date DEFAULT NULL,
  `tanggal_daftar` date DEFAULT NULL,
  `ruang` varchar(50) DEFAULT NULL,
  `penguji1` bigint(20) UNSIGNED DEFAULT NULL,
  `penguji2` bigint(20) UNSIGNED DEFAULT NULL,
  `penguji3` bigint(20) UNSIGNED DEFAULT NULL,
  `id_jenis_ujian_skripsi` bigint(20) UNSIGNED NOT NULL,
  `persetujuan_pembimbing1` tinyint(1) DEFAULT 0,
  `persetujuan_pembimbing2` tinyint(1) DEFAULT 0,
  `status` enum('Berlangsung','Diterima','Perbaikan','Mengulang') DEFAULT 'Berlangsung'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `ujian_skripsi`
--

INSERT INTO `ujian_skripsi` (`id`, `id_skripsi`, `tanggal`, `tanggal_daftar`, `ruang`, `penguji1`, `penguji2`, `penguji3`, `id_jenis_ujian_skripsi`, `persetujuan_pembimbing1`, `persetujuan_pembimbing2`, `status`) VALUES
(1, 1, NULL, '2025-11-20', NULL, NULL, NULL, NULL, 5, 0, 0, 'Berlangsung');

-- --------------------------------------------------------

--
-- Struktur dari tabel `validasi_syarat_pendadaran`
--

CREATE TABLE `validasi_syarat_pendadaran` (
  `id` int(11) NOT NULL,
  `id_syarat_pendadaran` bigint(20) UNSIGNED NOT NULL,
  `nama_field_syarat` varchar(100) NOT NULL,
  `status` enum('Diterima','Revisi','Menunggu') NOT NULL DEFAULT 'Menunggu',
  `catatan` text DEFAULT NULL,
  `id_validator` int(11) DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `validasi_syarat_sempro`
--

CREATE TABLE `validasi_syarat_sempro` (
  `id` int(11) NOT NULL,
  `id_syarat_sempro` bigint(20) UNSIGNED NOT NULL,
  `nama_field_syarat` varchar(100) NOT NULL,
  `status` enum('Diterima','Revisi','Menunggu') NOT NULL DEFAULT 'Menunggu',
  `catatan` text DEFAULT NULL,
  `id_validator` int(11) DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `apresiasi_ujian_skripsi`
--
ALTER TABLE `apresiasi_ujian_skripsi`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_apresisasi_penguji` (`id_penguji`),
  ADD KEY `fk_apresisasi_ujian_skripsi` (`id_ujian_skripsi`);

--
-- Indeks untuk tabel `data_dosen`
--
ALTER TABLE `data_dosen`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `nidk` (`nidk`);

--
-- Indeks untuk tabel `data_mahasiswa`
--
ALTER TABLE `data_mahasiswa`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `telepon` (`telepon`),
  ADD KEY `npm` (`npm`);

--
-- Indeks untuk tabel `hasil_plagiarisme`
--
ALTER TABLE `hasil_plagiarisme`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_plagiarisme_progres` (`id_progres`);

--
-- Indeks untuk tabel `jenis_ujian_skripsi`
--
ALTER TABLE `jenis_ujian_skripsi`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `log_aktivitas`
--
ALTER TABLE `log_aktivitas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_log_user` (`id_user`);

--
-- Indeks untuk tabel `mahasiswa_skripsi`
--
ALTER TABLE `mahasiswa_skripsi`
  ADD PRIMARY KEY (`npm`);

--
-- Indeks untuk tabel `mstr_akun`
--
ALTER TABLE `mstr_akun`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indeks untuk tabel `mstr_komponen_nilai_ujian_skripsi`
--
ALTER TABLE `mstr_komponen_nilai_ujian_skripsi`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_mst_komponen_jenis_skripsi` (`id_jenis_ujian_skripsi`);

--
-- Indeks untuk tabel `progres_skripsi`
--
ALTER TABLE `progres_skripsi`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `saran_ujian_skripsi`
--
ALTER TABLE `saran_ujian_skripsi`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_saran_penguji` (`id_penguji`),
  ADD KEY `fk_saran_ujian_skripsi` (`id_ujian_skripsi`);

--
-- Indeks untuk tabel `skripsi`
--
ALTER TABLE `skripsi`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_mahasiswa` (`id_mahasiswa`),
  ADD KEY `pembimbing1` (`pembimbing1`),
  ADD KEY `pembimbing2` (`pembimbing2`);

--
-- Indeks untuk tabel `syarat_pendadaran`
--
ALTER TABLE `syarat_pendadaran`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_ujian_skripsi` (`id_ujian_skripsi`);

--
-- Indeks untuk tabel `syarat_sempro`
--
ALTER TABLE `syarat_sempro`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_ujian_skripsi` (`id_ujian_skripsi`);

--
-- Indeks untuk tabel `tbl_nilai_ujian_skripsi`
--
ALTER TABLE `tbl_nilai_ujian_skripsi`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `id_sempro` (`id_ujian_skripsi`,`id_komponen_nilai`,`id_penguji`),
  ADD KEY `tbl_nilai_sempro_ibfk_3` (`id_penguji`),
  ADD KEY `tbl_nilai_sempro_ibfk_4` (`id_komponen_nilai`);

--
-- Indeks untuk tabel `tbl_pesan`
--
ALTER TABLE `tbl_pesan`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `ujian_skripsi`
--
ALTER TABLE `ujian_skripsi`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_skripsi` (`id_skripsi`),
  ADD KEY `penguji1` (`penguji1`),
  ADD KEY `penguji2` (`penguji2`),
  ADD KEY `fk_ujian_jenis` (`id_jenis_ujian_skripsi`),
  ADD KEY `penguji3` (`penguji3`);

--
-- Indeks untuk tabel `validasi_syarat_pendadaran`
--
ALTER TABLE `validasi_syarat_pendadaran`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_syarat_field` (`id_syarat_pendadaran`,`nama_field_syarat`);

--
-- Indeks untuk tabel `validasi_syarat_sempro`
--
ALTER TABLE `validasi_syarat_sempro`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_syarat_field` (`id_syarat_sempro`,`nama_field_syarat`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `apresiasi_ujian_skripsi`
--
ALTER TABLE `apresiasi_ujian_skripsi`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `hasil_plagiarisme`
--
ALTER TABLE `hasil_plagiarisme`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT untuk tabel `jenis_ujian_skripsi`
--
ALTER TABLE `jenis_ujian_skripsi`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT untuk tabel `log_aktivitas`
--
ALTER TABLE `log_aktivitas`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=55;

--
-- AUTO_INCREMENT untuk tabel `mstr_akun`
--
ALTER TABLE `mstr_akun`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10006;

--
-- AUTO_INCREMENT untuk tabel `mstr_komponen_nilai_ujian_skripsi`
--
ALTER TABLE `mstr_komponen_nilai_ujian_skripsi`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT untuk tabel `progres_skripsi`
--
ALTER TABLE `progres_skripsi`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=38;

--
-- AUTO_INCREMENT untuk tabel `saran_ujian_skripsi`
--
ALTER TABLE `saran_ujian_skripsi`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `skripsi`
--
ALTER TABLE `skripsi`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT untuk tabel `histori_judul_skripsi`
--
ALTER TABLE `histori_judul_skripsi`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `syarat_pendadaran`
--
ALTER TABLE `syarat_pendadaran`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `syarat_sempro`
--
ALTER TABLE `syarat_sempro`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT untuk tabel `tbl_nilai_ujian_skripsi`
--
ALTER TABLE `tbl_nilai_ujian_skripsi`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `tbl_pesan`
--
ALTER TABLE `tbl_pesan`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT untuk tabel `ujian_skripsi`
--
ALTER TABLE `ujian_skripsi`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT untuk tabel `validasi_syarat_pendadaran`
--
ALTER TABLE `validasi_syarat_pendadaran`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `validasi_syarat_sempro`
--
ALTER TABLE `validasi_syarat_sempro`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Ketidakleluasaan untuk tabel pelimpahan (Dumped Tables)
--

--
-- Ketidakleluasaan untuk tabel `apresiasi_ujian_skripsi`
--
ALTER TABLE `apresiasi_ujian_skripsi`
  ADD PRIMARY KEY (`id`),
  ADD CONSTRAINT `fk_apresisasi_penguji` FOREIGN KEY (`id_penguji`) REFERENCES `data_dosen` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_apresisasi_ujian_skripsi` FOREIGN KEY (`id_ujian_skripsi`) REFERENCES `ujian_skripsi` (`id`) ON UPDATE CASCADE;

--
-- Ketidakleluasaan untuk tabel `histori_judul_skripsi`
--
ALTER TABLE `histori_judul_skripsi`
  ADD PRIMARY KEY (`id`),
  ADD CONSTRAINT `histori_judul_skripsi_ibfk_1` FOREIGN KEY (`id_skripsi`) REFERENCES `skripsi` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `data_dosen`
--
ALTER TABLE `data_dosen`
  ADD CONSTRAINT `data_dosen_ibfk_1` FOREIGN KEY (`id`) REFERENCES `mstr_akun` (`id`);

--
-- Ketidakleluasaan untuk tabel `data_mahasiswa`
--
ALTER TABLE `data_mahasiswa`
  ADD CONSTRAINT `data_mahasiswa_ibfk_1` FOREIGN KEY (`id`) REFERENCES `mstr_akun` (`id`);

--
-- Ketidakleluasaan untuk tabel `hasil_plagiarisme`
--
ALTER TABLE `hasil_plagiarisme`
  ADD CONSTRAINT `fk_plagiarisme_progres` FOREIGN KEY (`id_progres`) REFERENCES `progres_skripsi` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `log_aktivitas`
--
ALTER TABLE `log_aktivitas`
  ADD CONSTRAINT `fk_log_user` FOREIGN KEY (`id_user`) REFERENCES `mstr_akun` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `mstr_komponen_nilai_ujian_skripsi`
--
ALTER TABLE `mstr_komponen_nilai_ujian_skripsi`
  ADD CONSTRAINT `fk_mst_komponen_jenis_skripsi` FOREIGN KEY (`id_jenis_ujian_skripsi`) REFERENCES `jenis_ujian_skripsi` (`id`) ON UPDATE CASCADE;

--
-- Ketidakleluasaan untuk tabel `saran_ujian_skripsi`
--
ALTER TABLE `saran_ujian_skripsi`
  ADD CONSTRAINT `fk_saran_penguji` FOREIGN KEY (`id_penguji`) REFERENCES `data_dosen` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_saran_ujian_skripsi` FOREIGN KEY (`id_ujian_skripsi`) REFERENCES `ujian_skripsi` (`id`) ON UPDATE CASCADE;

--
-- Ketidakleluasaan untuk tabel `skripsi`
--
ALTER TABLE `skripsi`
  ADD CONSTRAINT `skripsi_ibfk_1` FOREIGN KEY (`id_mahasiswa`) REFERENCES `data_mahasiswa` (`id`),
  ADD CONSTRAINT `skripsi_ibfk_2` FOREIGN KEY (`pembimbing1`) REFERENCES `data_dosen` (`id`),
  ADD CONSTRAINT `skripsi_ibfk_3` FOREIGN KEY (`pembimbing2`) REFERENCES `data_dosen` (`id`);

--
-- Ketidakleluasaan untuk tabel `syarat_pendadaran`
--
ALTER TABLE `syarat_pendadaran`
  ADD CONSTRAINT `syarat_pendadaran_ibfk_1` FOREIGN KEY (`id_ujian_skripsi`) REFERENCES `ujian_skripsi` (`id`);

--
-- Ketidakleluasaan untuk tabel `syarat_sempro`
--
ALTER TABLE `syarat_sempro`
  ADD CONSTRAINT `syarat_sempro_ibfk_1` FOREIGN KEY (`id_ujian_skripsi`) REFERENCES `ujian_skripsi` (`id`);

--
-- Ketidakleluasaan untuk tabel `tbl_nilai_ujian_skripsi`
--
ALTER TABLE `tbl_nilai_ujian_skripsi`
  ADD CONSTRAINT `tbl_nilai_ujian_skripsi_ibfk_2` FOREIGN KEY (`id_ujian_skripsi`) REFERENCES `ujian_skripsi` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `tbl_nilai_ujian_skripsi_ibfk_3` FOREIGN KEY (`id_penguji`) REFERENCES `data_dosen` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `tbl_nilai_ujian_skripsi_ibfk_4` FOREIGN KEY (`id_komponen_nilai`) REFERENCES `mstr_komponen_nilai_ujian_skripsi` (`id`) ON UPDATE CASCADE;

--
-- Ketidakleluasaan untuk tabel `ujian_skripsi`
--
ALTER TABLE `ujian_skripsi`
  ADD CONSTRAINT `fk_ujian_jenis` FOREIGN KEY (`id_jenis_ujian_skripsi`) REFERENCES `jenis_ujian_skripsi` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_ujian_skripsi_penguji3` FOREIGN KEY (`penguji3`) REFERENCES `data_dosen` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `ujian_skripsi_ibfk_1` FOREIGN KEY (`id_skripsi`) REFERENCES `skripsi` (`id`),
  ADD CONSTRAINT `ujian_skripsi_ibfk_2` FOREIGN KEY (`penguji1`) REFERENCES `data_dosen` (`id`),
  ADD CONSTRAINT `ujian_skripsi_ibfk_3` FOREIGN KEY (`penguji2`) REFERENCES `data_dosen` (`id`);

--
-- Ketidakleluasaan untuk tabel `validasi_syarat_pendadaran`
--
ALTER TABLE `validasi_syarat_pendadaran`
  ADD CONSTRAINT `validasi_syarat_pendadaran_ibfk_1` FOREIGN KEY (`id_syarat_pendadaran`) REFERENCES `syarat_pendadaran` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `validasi_syarat_sempro`
--
ALTER TABLE `validasi_syarat_sempro`
  ADD CONSTRAINT `validasi_syarat_sempro_ibfk_1` FOREIGN KEY (`id_syarat_sempro`) REFERENCES `syarat_sempro` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
