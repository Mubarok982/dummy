# Perubahan Sistem - Tanggal 5 Maret 2026

## 1. Hilangkan Status "REVISI ..." pada Role Mahasiswa ✅

### Yang Diubah:
- **File**: [v_bimbingan.php](application/views/mahasiswa/v_bimbingan.php#L97-L100)
- **Bagian**: Status Bimbingan di kartu profil mahasiswa

### Sebelumnya:
Status menampilkan detail revisi yang spesifik:
- "REVISI BAB 1"
- "REVISI BAB 2" 
- "REVISI SEMPRO"
- "REVISI PENDADARAN"

### Sesudahnya:
- Status "REVISI ..." dihilangkan sepenuhnya
- Hanya menampilkan status umum yang relevan:
  - DALAM BIMBINGAN
  - SIAP SEMPRO
  - SIAP PENDADARAN
  - PROSES SEMPRO
  - PROSES PENDADARAN
  - LULUS SKRIPSI
  - DITOLAK
  - MENGULANG

**Alasan**: Keselarasan dengan tampilan di role operator, lebih sederhana dan tidak overwhelming untuk mahasiswa.

### Dampak:
- Mahasiswa hanya lihat status umum, bukan detail revisi spesifik
- Riwayat progres tetap tersedia di tombol "Lihat Riwayat Progres"
- Tombol "Lihat Riwayat Revisi" diganti menjadi "Lihat Riwayat Progres" (lebih akurat)

---

## 2. Perbaikan UI/UX Fitur "Gunakan Judul/Tema Sebelumnya" ✅

### Yang Diubah:
- **File**: [v_bimbingan.php](application/views/mahasiswa/v_bimbingan.php#L243-L273)
- **Bagian**: Form upload progres, bagian pemilihan judul/tema

### Sebelumnya:
```
Checkbox: "Gunakan Judul/Tema Sebelumnya" (CHECKED by default)
- Jika UNCHECKED: tampilkan form untuk edit
- Text: "Anda dapat mengubah judul/tema skripsi. Judul/tema lama akan disimpan di riwayat perubahan."
```

### Sesudahnya:
```
Checkbox: "Gunakan Judul/Tema yang Sama" (CHECKED by default)
- Jika UNCHECKED: tampilkan form untuk memasukkan judul/tema BARU
- Text lebih jelas: "Jika ingin menggunakan judul/tema berbeda, unchecklist untuk memasukkan data baru."
- Form untuk edit: "Anda akan mengubah judul/tema skripsi. Data lama akan disimpan otomatis di riwayat."
- Field menjadi REQUIRED (marked dengan *)
- Alert berubah dari "info" menjadi "warning" untuk emphasis
```

**Alasan**: 
- Checkbox label lebih akurat (sebut "sama" bukan "sebelumnya")
- Instruksi lebih jelas tentang kapan dan kenapa unchecklist
- Alert yang tepat sesuai tipe aksi (warning untuk perubahan)
- Field required memastikan data valid

### Dampak:
- UX lebih intuitif, mahasiswa paham kapan perlu ubah judul
- Validasi lebih kuat
- Konsistensi dengan cara berpikir mahasiswa

---

## 3. Perbaikan Logika di Controller `upload_progres_bab()` ✅

### Yang Diubah:
- **File**: [Mahasiswa.php](application/controllers/Mahasiswa.php#L344-L369)
- **Fungsi**: `upload_progres_bab()`

### Sebelumnya:
```php
if (!empty($judul_baru) && $judul_baru != $skripsi['judul'] || 
    !empty($tema_baru) && $tema_baru != $skripsi['tema']) {
    // update
}
```
- Operator precedence tidak jelas (kurang parenthesis)
- Case-sensitive comparison

### Sesudahnya:
```php
// Cek apakah ada perbedaan (case-insensitive)
$judul_berubah = strtolower($judul_baru) !== strtolower($skripsi['judul']);
$tema_berubah = strtolower($tema_baru) !== strtolower($skripsi['tema']);

// HANYA update jika ada perbedaan
if ((!empty($judul_baru) && $judul_berubah) || 
    (!empty($tema_baru) && $tema_berubah)) {
    // update
}
```

**Perbaikan**:
- Logika lebih jelas dan robust
- Case-insensitive comparison (sama seperti di model)
- Parenthesis eksplisit untuk operator precedence
- Comment yang lebih jelas
- Trim() pada input judul/tema
- Pesan success yang lebih deskriptif

### Dampak:
- Konsistensi dengan logika di `M_Mahasiswa::update_skripsi_with_histori()`
- Histori hanya dibuat jika ada perubahan NYATA (tidak case-sensitive)
- Validasi lebih solid

---

## 4. Kejelasan Sistem Histori Judul/Tema 

### Alur Kerja yang SESUAI Permintaan:

#### Skenario A: Mahasiswa Pakai Judul/Tema yang Sama
```
1. Buka form bimbingan
2. Checkbox "Gunakan Judul/Tema yang Sama" → CHECKED (default)
3. Upload file progres
4. Submit
5. TIDAK ADA UPDATE judul/tema
6. TIDAK ADA HISTORI dibuat
7. Progres masuk dengan judul/tema yang aktif saat ini
```

#### Skenario B: Mahasiswa Ganti Judul/Tema BARU
```
1. Buka form bimbingan
2. Checkbox "Gunakan Judul/Tema yang Sama" → UNCHECKED
3. Masukkan judul baru: "Platform E-Commerce Terpadu"
4. Masukkan tema baru: "Software Engineering" (bisa sama atau berbeda)
5. Upload file progres
6. Submit

JIKA BERBEDA dari yang aktif saat ini:
   → Sistem OTOMATIS:
     a) Ambil judul/tema LAMA dari tabel skripsi
     b) Simpan ke tabel histori_judul_skripsi
     c) Update tabel skripsi dengan data BARU
     d) ID skripsi TIDAK BERUBAH
     
   → HASIL:
     - Riwayat judul tersimpan
     - Progres lama tetap terbaca (terhubung dengan ID skripsi yang sama)
     - Riwayat tampil di view pengajuan_judul & bimbingan

JIKA SAMA dengan yang aktif saat ini:
   → Tidak ada histori
   → Hanya upload progres normal
```

#### Skenario C: Mahasiswa Ganti Judul 2x Kali
```
Hari 1:
- Upload BAB 1 dengan Judul A, Tema X
- Tidak ada histori (pertama kali)

Hari 2:
- Ganti menjadi Judul B, Tema Y (berbeda dari Judul A)
- Upload BAB 2
- Sistem simpan: Judul A ke histori
- Status ACC reset ke "menunggu" (untuk review ulang)

Hari 3:
- Ganti lagi menjadi Judul C, Tema Z (berbeda dari Judul B)
- Upload BAB 3
- Sistem simpan: Judul B ke histori (Judul A tetap di histori)
- Status ACC reset ke "menunggu"

HASIL DI DATABASE:
- Tabel skripsi: Judul C, Tema Z (SAAT INI)
- Tabel histori_judul_skripsi:
  - Row 1: Judul B, Tema Y (dibuat 5 Maret 10:00)
  - Row 2: Judul A, Tema X (dibuat 5 Maret 09:00)

HASIL DI VIEW PENGAJUAN_JUDUL:
- Tampil riwayat 2 perubahan judul
- Judul C (saat ini) tidak tampil di riwayat, cuma di form
```

---

## Summary Perubahan

| Aspek | Sebelum | Sesudah | Status |
|-------|---------|---------|--------|
| Status "REVISI..." | Tampil di mahasiswa | Dihilangkan | ✅ |
| Checkbox label | "Gunakan Judul/Tema Sebelumnya" | "Gunakan Judul/Tema yang Sama" | ✅ |
| Alert tipe | Info (blue) | Warning (orange) | ✅ |
| Form field | Optional | Required (*) | ✅ |
| Logika kondisional | Case-sensitive, kurang jelas | Case-insensitive, clear | ✅ |
| Histori dibuat | Tiap update | Hanya jika ada perbedaan | ✅ |
| Trim input | Tidak | Ya | ✅ |

---

## Testing Checklist

Untuk memverifikasi semua berfungsi:

- [ ] Buka form bimbingan mahasiswa
- [ ] Verifikasi: Tidak ada status "REVISI..." di kartu profil
- [ ] Verifikasi: Checkbox text berubah menjadi "Gunakan Judul/Tema yang Sama"
- [ ] Verifikasi: Info text berubah (instruksi lebih jelas)
- [ ] Verifikasi: Form edit adalah REQUIRED field (bintang merah *)
- [ ] Upload tanpa ubah judul → tidak ada histori baru
- [ ] Upload dengan ubah judul → histori dibuat otomatis
- [ ] Lihat riwayat pengajuan_judul → tampil riwayat perubahan
- [ ] Lihat riwayat bimbingan → tampil dengan badge "Judul Aktif" & "Riwayat Lama"
- [ ] Ganti judul 2x kali → verifikasi histori berisi keduanya

---

**Dibuat:** 5 Maret 2026  
**Status:** Perubahan Selesai ✅  
**Next**: Testing & Verifikasi Produksi
