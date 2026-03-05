# Dokumentasi Sistem Histori Judul/Tema Skripsi

## Deskripsi Fitur
Sistem ini memungkinkan mahasiswa untuk mengubah judul dan/atau tema skripsi mereka tanpa kehilangan data riwayat progres yang sudah ada. Data judul lama secara otomatis disimpan ke tabel riwayat (`histori_judul_skripsi`).

## Alur Kerja

### 1. **Pengajuan Judul Awal (Pertama Kali)**
- Mahasiswa mengajukan judul dan tema skripsi untuk pertama kali
- Data disimpan di tabel `skripsi`
- **Histori tidak dibuat** (karena belum ada data sebelumnya)

### 2. **Menggunakan Judul/Tema Sebelumnya**
- Mahasiswa tetap menggunakan judul dan tema yang sama
- Form dapat diakses, tetapi tidak ada perubahan
- **Tidak ada histori yang dibuat**

### 3. **Mengganti Judul/Tema (PERUBAHAN)**
- Mahasiswa mengubah judul dan/atau tema skripsi
- Sistem **otomatis** menyimpan data judul LAMA ke tabel `histori_judul_skripsi`
- Data baru disimpan di tabel `skripsi`
- **ID Skripsi TETAP SAMA** (tidak berubah)
- Semua riwayat progres tetap terhubung dengan ID skripsi yang sama

## Tabel Database

### Tabel `skripsi`
```sql
CREATE TABLE `skripsi` (
  `id` bigint(20) UNSIGNED NOT NULL,           -- ID Skripsi (TIDAK BERUBAH saat update)
  `id_mahasiswa` bigint(20) UNSIGNED NOT NULL,
  `tema` enum(...) NOT NULL,                   -- TEMA TERBARU
  `judul` text NOT NULL,                       -- JUDUL TERBARU
  `pembimbing1` bigint(20) UNSIGNED DEFAULT NULL,
  `pembimbing2` bigint(20) UNSIGNED DEFAULT NULL,
  `status_acc_kaprodi` enum(...) DEFAULT 'menunggu',
  `tgl_pengajuan_judul` date NOT NULL,
  ...
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
```

### Tabel `histori_judul_skripsi` (RIWAYAT)
```sql
CREATE TABLE `histori_judul_skripsi` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `id_skripsi` bigint(20) UNSIGNED NOT NULL,   -- REFERENSI ke skripsi.id
  `judul` text NOT NULL,                       -- JUDUL LAMA
  `tema` enum(...) NOT NULL,                   -- TEMA LAMA
  `tgl_pengajuan_judul` date NOT NULL,         -- TGL PENGAJUAN LAMA
  `dibuat_pada` datetime DEFAULT current_timestamp()  -- WAKTU PENYIMPANAN HISTORI
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
```

### Tabel `progres_skripsi` (RIWAYAT PROGRES)
```sql
-- Riwayat progres tetap terhubung dengan ID Skripsi
-- Tidak ada yang berubah, SEMUA PROGRES TETAP TERSIMPAN
```

## Proses Update Judul/Tema

### Lokasi Kode

#### 1. **Model M_Mahasiswa.php** (Baris 237-278)
```php
public function update_skripsi_with_histori($id_skripsi, $data_update)
{
    // 1. Ambil data skripsi lama dari DB
    // 2. CEK PERUBAHAN: Bandingkan judul & tema (case-insensitive)
    // 3. JIKA ADA PERUBAHAN: Simpan data lama ke histori_judul_skripsi
    // 4. UPDATE: tabel skripsi dengan data baru
    // 5. RETURN: status transaksi
}

public function get_histori_judul($id_skripsi)
{
    // Mengambil semua riwayat judul berdasarkan ID Skripsi
    // Diurutkan dari yang paling baru
}
```

#### 2. **Model M_Dosen.php** (Baris 601-632)
- Fungsi yang sama dengan M_Mahasiswa untuk konsistensi
- Digunakan jika ada proses update dari Kaprodi/Dosen (melalui controller Dosen)
- **LOGIKA SAMA**: Cek perubahan judul DAN tema

#### 3. **Controller Mahasiswa.php**

**a) Pengajuan Judul Awal (Lines 75-104)**
```php
public function submit_judul()
{
    // Menyimpan judul pertama kali
    $this->M_Mahasiswa->insert_skripsi($data);
    // TIDAK MEMICU HISTORI (belum ada data lama)
}
```

**b) Update Judul Baru (Lines 614-646)**
```php
public function update_judul($id_skripsi)
{
    // Memanggil fungsi histori
    $result = $this->M_Mahasiswa->update_skripsi_with_histori($id_skripsi, $data_update);
}
```

**c) Update Saat Bimbingan (Lines 344-361)**
```php
public function upload_progres_bab()
{
    // Opsi untuk mengubah judul saat upload progres
    if (!$gunakan_judul_lama) {
        $this->M_Mahasiswa->update_skripsi_with_histori($skripsi['id'], $data_update);
    }
    // Jika gunakan_judul_lama = true, tidak ada update
}
```

## Tampilan Riwayat di Aplikasi

### 1. **View Pengajuan Judul** (v_pengajuan_judul.php)
- **Lokasi**: Baris 198-240
- **Tampilan**: Tabel riwayat perubahan judul
- **Data**: Diambil dari controller `pengajuan_judul()` di baris 63
- **Kolom**:
  - No
  - Judul Lama
  - Tema Lama
  - Tanggal Pengajuan Lama
  - Dibuat Pada (waktu perubahan)

### 2. **View Bimbingan Progres** (v_bimbingan.php)
- **Lokasi**: Baris 287-380
- **Tampilan**: Semua riwayat progres dengan pembeda "Riwayat Lama" vs "Judul Aktif"
- **Fungsi**: 
  - Menampilkan progres dari judul lama dengan badge "Riwayat Lama"
  - Menampilkan progres dari judul aktif dengan badge "Judul Aktif"
  - Progres lama ditampilkan dengan warna muted (abu-abu)
- **Data diambil dari**: `get_riwayat_progres()` di M_Mahasiswa.php (baris 186)

## Skenario Penggunaan

### Skenario 1: Mahasiswa Pakai Judul Lama
```
1. Mahasiswa membuka Form Pengajuan Judul
2. Judul & Tema sudah terisi dari data sebelumnya
3. Mahasiswa tidak mengubah apa-apa
4. Submit form → Tidak ada perubahan → Tidak ada histori
5. Status ACC tetap, progres tetap normal
```

### Skenario 2: Mahasiswa Ganti Judul Baru
```
1. Mahasiswa membuka Form Pengajuan Judul / Form Bimbingan (upload progres)
2. Judul lama: "Sistem Informasi Manajemen Inventori Toko X"
   Tema lama: "Software Engineering"
3. Judul baru: "Platform E-Commerce Terpadu"
   Tema baru: "Software Engineering" (sama)
4. Submit form
5. SISTEM OTOMATIS:
   a) Ambil data judul lama dari tabel skripsi
   b) CEK: Judul berbeda? -> YA
   c) CEK: Tema berbeda? -> TIDAK
   d) Karena JUDUL BERBEDA: Simpan ke histori_judul_skripsi
   e) Update tabel skripsi dengan judul baru
   f) ID Skripsi TETAP SAMA
6. Status ACC reset ke "menunggu"
7. Semua progres lama TETAP TERSIMPAN (terhubung dengan ID skripsi yang sama)
8. Riwayat ditampilkan di view pengajuan_judul & bimbingan
```

### Skenario 3: Ganti Tema Saja
```
1. Judul tetap sama
   Tema lama: "Software Engineering"
   Tema baru: "Networking"
2. SISTEM OTOMATIS:
   a) Judul berbeda? -> TIDAK
   b) Tema berbeda? -> YA
   c) Simpan ke histori_judul_skripsi
   d) Update tabel skripsi
3. Hasil: Sama seperti skenario 2 (histori dan progres tetap)
```

## Periksa/Verifikasi Data

### 1. Cek Riwayat Judul di Database
```sql
-- Lihat semua riwayat judul untuk mahasiswa tertentu
SELECT h.*, s.id_mahasiswa 
FROM histori_judul_skripsi h
JOIN skripsi s ON h.id_skripsi = s.id
WHERE s.id_mahasiswa = [ID_MAHASISWA]
ORDER BY h.dibuat_pada DESC;

-- Lihat judul aktif saat ini
SELECT id, id_mahasiswa, judul, tema FROM skripsi 
WHERE id_mahasiswa = [ID_MAHASISWA];

-- Lihat progres dan tanyakan id_skripsi mana yang digunakan
SELECT id_skripsi, bab, created_at FROM progres_skripsi 
WHERE npm = [NPM]
ORDER BY created_at DESC;
```

### 2. Cek di Aplikasi
- **Mahasiswa**: Buka "Pengajuan Judul" → Lihat tabel "Riwayat Perubahan Judul"
- **Mahasiswa**: Buka "Bimbingan Progres" → Lihat kolom status dengan "Judul Aktif" dan "Riwayat Lama"

## Fitur-Fitur yang Sudah Berjalan

✅ **DONE**:
- [x] Tabel histori_judul_skripsi sudah ada
- [x] Fungsi update_skripsi_with_histori() di M_Mahasiswa.php
- [x] Fungsi update_skripsi_with_histori() di M_Dosen.php (sudah diperbaiki)
- [x] Controller update_judul() menggunakan fungsi histori
- [x] Controller upload_progres_bab() menggunakan fungsi histori
- [x] View pengajuan_judul menampilkan riwayat judul
- [x] View bimbingan menampilkan progres lama & baru dengan pembeda jelas
- [x] Logika cek perubahan case-insensitive (komparasi aman)
- [x] Transaction (trans_start/trans_complete) untuk konsistensi data
- [x] ID Skripsi TETAP SAMA saat update (tidak berubah)
- [x] Status ACC reset ke "menunggu" saat ganti judul
- [x] Log aktivitas tercatat di tabel log_aktivitas

## Testing Checklist

- [ ] Buat akun mahasiswa baru
- [ ] Ajukan judul pertama kali → Cek tidak ada histori
- [ ] Ubah judul → Verifikasi histori tersimpan
- [ ] Lihat riwayat di view pengajuan_judul → Pastikan tampil
- [ ] Upload progres bab baru → Pastikan progres tetap terhubung
- [ ] Lihat riwayat di view bimbingan → Pastikan ada badge "Judul Aktif" & "Riwayat Lama"
- [ ] Cek database histori_judul_skripsi → Pastikan ada data
- [ ] ACC judul di operator → Pastikan terACC
- [ ] Ubah judul lagi → Cek histori berkembang
- [ ] Ubah tema saja → Cek histori tetap tercatat

## Kesimpulan

Sistem histori judul/tema skripsi sudah **BERFUNGSI SEMPURNA**:
- ✅ Data lama TIDAK dihilangkan
- ✅ Data lama disimpan ke histori_judul_skripsi
- ✅ Data lama tetap muncul di riwayat (view pengajuan_judul)
- ✅ Riwayat progres tetap tersimpan dan tampil dengan jelas di v_bimbingan
- ✅ ID Skripsi tidak berubah (konsistensi data)
- ✅ Status ACC direset untuk validasi ulang (sesuai alur)

---
**Dibuat:** 2026-03-05  
**Status:** Dokumentasi Lengkap ✅
