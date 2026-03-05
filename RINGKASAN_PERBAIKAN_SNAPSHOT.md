# RINGKASAN PERUBAHAN - 5 MARET 2026

## Masalah yang Dilaporkan

**USER**: "Belum berjalan semestinya. Setelah mahasiswa mengganti judul/tema baru, lanjut ke bab selanjutnya, bukannya menimpa data bab 1 nya. Judul/tema baru itu berlaku untuk naskah yang akan diupload, bukan naskah yang sebelumnya diupload"

---

## Solusi yang Diimplementasikan

### 1️⃣ Tambah Kolom Snapshot ke Database

**File**: `MIGRATION_20260305_AddJudulTemaSnapshot.sql`

Tambahkan 2 kolom ke tabel `progres_skripsi`:
- `judul_saat_upload` (longtext) - Snapshot judul saat BAB diupload
- `tema_saat_upload` (enum) - Snapshot tema saat BAB diupload

**Tujuan**: Menyimpan versi judul/tema PER BAB, bukan JOIN ke tabel skripsi yang berubah-ubah.

### 2️⃣ Update Controller: Simpan Snapshot

**File**: [application/controllers/Mahasiswa.php](application/controllers/Mahasiswa.php#L408-L428)  
**Fungsi**: `upload_progres_bab()`

**Perubahan**:
```php
// Sebelum: Tidak ada snapshot
$progres_data = ['bab' => $bab, 'file' => $file, ...];

// Sesudah: Simpan snapshot judul & tema dari skripsi saat ini
$progres_data = [
    'bab' => $bab,
    'judul_saat_upload' => $skripsi['judul'],  // ← NEW
    'tema_saat_upload' => $skripsi['tema'],    // ← NEW
    'file' => $file,
    ...
];
```

### 3️⃣ Update View: Tampilkan Snapshot

**File**: [application/views/mahasiswa/v_bimbingan.php](application/views/mahasiswa/v_bimbingan.php#L295-L356)  
**Section**: Tabel "Semua Riwayat Upload"

**Perubahan**:
- Ubah kolom header: "Tanggal & Judul" → "Tanggal Upload" + "Judul & Tema Saat Itu"
- Restructure kolom tabel untuk 6 kolom (sebelum 5)
- Tampilkan `judul_saat_upload` & `tema_saat_upload` dari object `$pr`
- Tambahkan tooltip/badge untuk membedakan "Judul Aktif" (hijau) vs "Judul Lama" (merah)

---

## Alur Kerja Setelah Implementasi

```
Timeline Mahasiswa:

T1 - Upload BAB 1 dengan Judul A
    └─ Sistem: Simpan progres (bab=1, judul_saat_upload='Judul A', tema='SE')
    └─ Status: ✅ Aktif

T2 - Ubah Judul menjadi Judul B
    └─ Sistem: Update skripsi (judul='Judul B'), create histori
    └─ Status: ACC reset ke 'menunggu'

T3 - Upload BAB 2 dengan Judul B
    └─ Sistem: Simpan progres (bab=2, judul_saat_upload='Judul B', tema='SE')
    └─ Status: ✅ Aktif

RIWAYAT PROGRES (v_bimbingan.php):
├─ BAB 1: Judul A (SE) | Status: Judul Lama [Merah]
└─ BAB 2: Judul B (SE) | Status: Judul Aktif [Hijau]
```

---

## Checklist Implementasi

### Database
- [ ] Jalankan migration SQL untuk menambah kolom
- [ ] Verify kolom `judul_saat_upload` & `tema_saat_upload` ada
- [ ] Verify data lama sudah populated (migration UPDATE query)

### Code
- [x] Update controller `upload_progres_bab()` di Mahasiswa.php
- [x] Update view `v_bimbingan.php` untuk tampilkan snapshot
- [x] Pastikan view punya fallback untuk data lama (isset checks)

### Testing
- [ ] Upload BAB dengan judul A
- [ ] Ubah judul menjadi B
- [ ] Upload BAB lagi dengan judul B
- [ ] Verifikasi: BAB 1 tampil Judul A, BAB 2 tampil Judul B
- [ ] Cek badge status benar (Judul Aktif vs Judul Lama)

---

## Files yang Berubah

| File | Tipe | Perubahan |
|------|------|-----------|
| [MIGRATION_20260305_AddJudulTemaSnapshot.sql](MIGRATION_20260305_AddJudulTemaSnapshot.sql) | 🆕 New | Migration script untuk menambah kolom |
| [application/controllers/Mahasiswa.php](application/controllers/Mahasiswa.php) | ✏️ Updated | Upload progres: tambah snapshot judul/tema |
| [application/views/mahasiswa/v_bimbingan.php](application/views/mahasiswa/v_bimbingan.php) | ✏️ Updated | Tabel riwayat: tampilkan snapshot judul/tema |
| [IMPLEMENTASI_SNAPSHOT_JUDUL.md](IMPLEMENTASI_SNAPSHOT_JUDUL.md) | 📄 Doc | Dokumentasi lengkap implementasi |

---

## Perbedaan Utama Sebelum vs Sesudah

### SEBELUM ❌
```
Upload BAB 1: Judul A → Simpan (tanpa snapshot)
Ubah Judul: A → B
Upload BAB 2: Judul B → Simpan
Riwayat: BAB1 tampil "Judul B", BAB2 tampil "Judul B" ❌
```

### SESUDAH ✅
```
Upload BAB 1: Judul A → Simpan (judul_saat_upload='Judul A')
Ubah Judul: A → B
Upload BAB 2: Judul B → Simpan (judul_saat_upload='Judul B')
Riwayat: BAB1 tampil "Judul A", BAB2 tampil "Judul B" ✅
```

---

## Langkah Selanjutnya (Manual)

Anda perlu:
1. **Buka PHPMyAdmin atau MySQL command line**
2. **Jalankan SQL dari file**: `MIGRATION_20260305_AddJudulTemaSnapshot.sql`
3. **Test dengan cara**: Buat akun test → upload BAB → ubah judul → upload BAB lain
4. **Verifikasi**: Riwayat tampil dengan judul yang benar per BAB

**Catatan**: Code sudah siap, hanya tinggal jalankan migration SQL.

---

**Status**: ✅ SELESAI  
**Tanggal**: 5 Maret 2026  
**Tested**: Menunggu user verification
