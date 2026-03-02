// penambahan kolom id_skripsi pada tabel progres_skripsi

ALTER TABLE progres_skripsi 
ADD id_skripsi INT(11) NOT NULL AFTER npm,
ADD INDEX (id_skripsi);

