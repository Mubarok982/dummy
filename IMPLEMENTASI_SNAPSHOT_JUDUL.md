# Implementasi Snapshot Judul/Tema pada Progres Upload

**Tanggal**: 5 Maret 2026  
**Status**: SOLUSI UNTUK MASALAH BAB LAMA BERUBAH TAMPILAN

---

## Masalah yang Diselesaikan

**Sebelumnya**:
- Mahasiswa upload BAB 1 dengan Judul A
- Mahasiswa ganti judul menjadi Judul B
- Mahasiswa upload BAB 2 dengan Judul B
- **HASIL MASALAH**: BAB 1 tampil dengan Judul B (tidak sesuai)

**Sekarang**:
- Mahasiswa upload BAB 1 dengan Judul A → disimpan snapshot judul A
- Mahasiswa ganti judul menjadi Judul B
- Mahasiswa upload BAB 2 dengan Judul B → disimpan snapshot judul B
- **HASIL BENAR**: BAB 1 tetap tampil dengan Judul A (sesuai upload time)

---

## Langkah Implementasi

### STEP 1: Jalankan Migration SQL

Buka MySQL (phpMyAdmin atau command line) dan jalankan SQL dari file:  
📄 `MIGRATION_20260305_AddJudulTemaSnapshot.sql`

```sql
-- Query 1: Tambah kolom
ALTER TABLE `progres_skripsi` 
ADD COLUMN `judul_saat_upload` longtext DEFAULT NULL AFTER `bab`,
ADD COLUMN `tema_saat_upload` enum('Software Engineering','Networking','Artificial Intelligence') DEFAULT NULL AFTER `judul_saat_upload`;

-- Query 2: Update data existing
UPDATE `progres_skripsi` ps
JOIN `skripsi` s ON ps.id_skripsi = s.id
SET ps.judul_saat_upload = s.judul,
    ps.tema_saat_upload = s.tema
WHERE ps.judul_saat_upload IS NULL;
```

**Hasil**: Tabel `progres_skripsi` akan memiliki 2 kolom baru:
- `judul_saat_upload` (longtext)
- `tema_saat_upload` (enum)

### STEP 2: Update Controller

**File**: [application/controllers/Mahasiswa.php](application/controllers/Mahasiswa.php#L408-L428)  
**Fungsi**: `upload_progres_bab()`

**Perubahan**: Tambah 2 baris pada `$progres_data` array:

```php
// SEBELUM
$progres_data = [
    'npm'            => $npm,
    'id_skripsi'     => $skripsi['id'], 
    'bab'            => $bab,
    'file'           => $file_data['file_name'], 
    ...
];

// SESUDAH
$progres_data = [
    'npm'                => $npm,
    'id_skripsi'         => $skripsi['id'], 
    'bab'                => $bab,
    'judul_saat_upload'  => $skripsi['judul'],  // ← BARU
    'tema_saat_upload'   => $skripsi['tema'],   // ← BARU
    'file'               => $file_data['file_name'], 
    ...
];
```

**Alasan**: Menyimpan snapshot judul & tema pada saat upload, bukan ambil dari DB nanti.

✅ **STATUS**: SUDAH DIUPDATE

### STEP 3: Update View

**File**: [application/views/mahasiswa/v_bimbingan.php](application/views/mahasiswa/v_bimbingan.php#L295-L356)

**Perubahan Tabel**:

| Bagian | Sebelum | Sesudah |
|--------|---------|---------|
| Header Kolom 1 | "Tanggal & Judul" | "Tanggal Upload" |
| Header Kolom 2 | "Bab" | "Judul & Tema Saat Itu" |
| Header Kolom 3 | "Status ACC" | "Bab" |
| Header Kolom 4 | "Catatan Dosen" | "Status ACC" |
| Header Kolom 5 | "File" | "Catatan Dosen" |
| Header Kolom 6 | (tidak ada) | "File" |

**Penampilan Kolom 2 (Judul & Tema)**:
```php
<td>
    <small class="d-block text-dark font-weight-bold">
        <?= isset($pr->judul_saat_upload) ? substr($pr->judul_saat_upload, 0, 50) . (strlen($pr->judul_saat_upload) > 50 ? '...' : '') : 'N/A'; ?>
    </small>
    <small class="text-muted d-block">
        <i class="fas fa-tag mr-1"></i><?= isset($pr->tema_saat_upload) ? $pr->tema_saat_upload : 'N/A'; ?>
    </small>
</td>
```

**Badge Status**:
- "Judul Aktif" → Badge hijau (progres terbaru)
- "Judul Lama" → Badge merah (progres sebelum ada perubahan judul)

✅ **STATUS**: SUDAH DIUPDATE

---

## Flow Lengkap Setelah Implementasi

```
Waktu T1:
├─ Mahasiswa upload BAB 1
├─ Sistem simpan: id_skripsi=2, judul_saat_upload="Judul A", tema_saat_upload="SE"
└─ Status: Aktif

Waktu T2:
├─ Mahasiswa ubah judul menjadi "Judul B"
├─ Sistem: update tabel skripsi (id=2, judul="Judul B")
├─ Sistem: create histori (judul lama="Judul A" tersimpan)
└─ Status ACC reset ke "menunggu"

Waktu T3:
├─ Mahasiswa upload BAB 2
├─ Sistem simpan: id_skripsi=2, judul_saat_upload="Judul B", tema_saat_upload="SE"
└─ Status: Aktif

HASIL RIWAYAT PROGRES:
├─ BAB 1 (T1): Judul A, Tema SE, Status "Judul Lama" (merah)
└─ BAB 2 (T3): Judul B, Tema SE, Status "Judul Aktif" (hijau)
```

---

## Testing Checklist

- [ ] Buka phpMyAdmin, verifikasi kolom `judul_saat_upload` & `tema_saat_upload` ada
- [ ] Verifikasi data lama sudah ter-update (query migration)
- [ ] Mahasiswa upload BAB 1 dengan Judul A
- [ ] Verifikasi di database: `progres_skripsi` row baru punya `judul_saat_upload="Judul A"`
- [ ] Mahasiswa ubah judul menjadi "Judul B" → verifikasi histori dibuat
- [ ] Mahasiswa upload BAB 2
- [ ] Verifikasi di database: row BAB 2 punya `judul_saat_upload="Judul B"`
- [ ] Lihat riwayat progres di view:
  - [ ] BAB 1 tampil dengan Judul A, Status "Judul Lama"
  - [ ] BAB 2 tampil dengan Judul B, Status "Judul Aktif"
- [ ] Ubah judul lagi menjadi "Judul C" → upload BAB 3
- [ ] Verifikasi: BAB 1, BAB 2, BAB 3 masing-masing tampil dengan judul mereka sendiri saat upload

---

## Database Verification SQL

```sql
-- Cek kolom sudah ditambah
DESC `progres_skripsi`;

-- Cek data sudah populated
SELECT id, npm, id_skripsi, bab, judul_saat_upload, tema_saat_upload 
FROM `progres_skripsi`
ORDER BY id DESC
LIMIT 10;

-- Cek data lama vs baru ada perbedaan
SELECT 
    ps.id,
    ps.bab,
    ps.judul_saat_upload,
    s.judul as judul_saat_ini
FROM `progres_skripsi` ps
JOIN `skripsi` s ON ps.id_skripsi = s.id
WHERE ps.judul_saat_upload != s.judul;
-- Expected: Ada rows jika ada perubahan judul
```

---

## Keuntungan Solusi Ini

✅ **Data Akurat**: Setiap BAB simpan snapshot judul/tema saat upload  
✅ **Riwayat Terlihat**: Mahasiswa bisa lihat semua versi judul per BAB  
✅ **Join Simplified**: View tidak perlu join kompleks ke tabel skripsi  
✅ **Performance**: Query lebih cepat (langsung baca dari progres_skripsi)  
✅ **Backward Compatible**: Kolom baru DEFAULT NULL, data lama tetap valid  
✅ **Audit Trail**: Rekam persis judul apa saat upload (forensik)

---

## Catatan Penting

1. **Migration Harus Dijalankan Dulu**: Tanpa kolom baru, view akan error (undefined index)
2. **Backward Compatible**: Code sudah cek `isset($pr->judul_saat_upload)` jadi safe
3. **Data Lama**: Sudah di-populate via UPDATE query di migration
4. **Future Data**: Otomatis ter-save dari controller

---

**Dibuat**: 5 Maret 2026  
**Implementasi**: Selesai ✅  
**Testing**: [Lihat checklist di atas]
