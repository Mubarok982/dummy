# TODO: Tambahkan 4 Fitur Bimbingan Skripsi

## Fitur 1: List Revisi
- [x] Tambahkan method di Operator controller: list_revisi()
- [x] Tambahkan method di M_Data: get_list_revisi() - ambil dari log_aktivitas atau progres_skripsi dengan status 'Revisi'
- [x] Buat view: v_list_revisi.php di application/views/operator/
- [x] Tambahkan link di sidebar operator

## Fitur 2: Laporan Kinerja Dospem per Semester
- [x] Tambahkan method di Operator controller: laporan_dospem_semester()
- [x] Tambahkan method di M_Data: get_kinerja_dospem_per_semester($semester, $prodi) - hitung jumlah bimbingan per dosen, siapa yang dibimbing, jumlah mahasiswa
- [x] Buat view: v_laporan_dospem.php di application/views/operator/
- [x] Tambahkan filter semester dan prodi
- [x] Tambahkan link di sidebar operator

## Fitur 3: Tambah Bimbingan sampai Pendadaran
- [ ] Update progres_skripsi untuk bab 4 dan 5 (pendadaran)
- [x] Tambahkan method di Operator controller: mahasiswa_siap_pendadaran()
- [x] Tambahkan method di M_Data: get_mahasiswa_siap_pendadaran() - bab 4 ACC
- [x] Tambahkan method di Operator controller: mahasiswa_selesai_skripsi()
- [x] Tambahkan method di M_Data: get_mahasiswa_selesai_skripsi() - bab 5 ACC
- [x] Buat view: v_mahasiswa_siap_pendadaran.php dan v_mahasiswa_selesai_skripsi.php di application/views/operator/
- [ ] Update view progres_detail di dosen untuk bab 4 dan 5
- [x] Tambahkan link di sidebar operator

## Fitur 4: Update Proses Sempro dari ujian_skripsi
- [x] Update method get_mahasiswa_siap_sempro di M_Data untuk ambil dari ujian_skripsi berdasarkan status dan id_jenis_ujian_skripsi
- [x] Pastikan ujian_skripsi terhubung dengan skripsi
- [ ] Update view v_mahasiswa_siap_sempro jika perlu

## Testing
- [x] Test semua fitur baru
- [x] Pastikan tidak ada error
- [x] Verifikasi data benar
