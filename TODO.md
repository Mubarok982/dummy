# TODO: Sistem Pengelolaan Jabatan Kaprodi di Manajemen Akun

## Tugas Utama
Membuat sistem pengelolaan jabatan kaprodi berjalan dengan baik di manajemen akun, termasuk tombol khusus untuk pengaturan kaprodi.

## Masalah yang Ditemukan
- Kolom `is_kaprodi` tidak disertakan dalam query utama di model `M_akun_opt`
- Query terpisah di controller untuk mendapatkan `is_kaprodi` menyebabkan inefisiensi
- Urutan data tidak memprioritaskan kaprodi
- Tidak ada tombol khusus untuk mengatur kaprodi secara langsung

## Perbaikan yang Dilakukan
### Fase 1: Integrasi Kaprodi di Manajemen Akun
- [x] Tambahkan `COALESCE(D.is_kaprodi, 0) as is_kaprodi` ke dalam select query di `get_all_users_with_details()`
- [x] Ubah urutan order by: `ORDER BY is_kaprodi DESC, A.role ASC, A.nama ASC` agar kaprodi muncul di atas
- [x] Hapus query terpisah di controller `manajemen_akun()` yang mengambil `is_kaprodi` secara manual

### Fase 2: Tombol Pengaturan Kaprodi
- [x] Tambahkan tombol "Pengaturan Kaprodi" di halaman manajemen akun
- [x] Buat method `pengaturan_kaprodi()` di controller Operator untuk menampilkan halaman pengaturan
- [x] Buat method `simpan_kaprodi()` untuk menyimpan perubahan kaprodi
- [x] Buat view `v_pengaturan_kaprodi.php` dengan dropdown untuk setiap prodi
- [x] Implementasikan logika untuk unset kaprodi lama dan set kaprodi baru

## Hasil
- Sistem kaprodi sekarang terintegrasi penuh dalam manajemen akun
- Badge "Kaprodi" ditampilkan dengan benar untuk dosen yang memiliki jabatan tersebut
- Kaprodi muncul di urutan teratas dalam daftar akun
- Tombol "Pengaturan Kaprodi" tersedia di halaman manajemen akun
- Halaman pengaturan kaprodi memungkinkan operator memilih kaprodi untuk setiap prodi melalui dropdown
- Pengaturan kaprodi melalui form edit akun dosen tetap berfungsi sebagai fallback

## Status
✅ **SELESAI** - Sistem pengelolaan jabatan kaprodi sudah berjalan di manajemen akun dengan fitur lengkap.
