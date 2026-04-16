<?php
$mysqli = new mysqli('localhost', 'root', '', 'bimbingan');
if ($mysqli->connect_error) {
    die('DB error: ' . $mysqli->connect_error . "\n");
}
$queries = [
    "ALTER TABLE skripsi ADD COLUMN IF NOT EXISTS alasan_p1 TEXT DEFAULT NULL",
    "ALTER TABLE skripsi ADD COLUMN IF NOT EXISTS alasan_p2 TEXT DEFAULT NULL",
    "ALTER TABLE skripsi ADD COLUMN IF NOT EXISTS jadwal_sempro_pdf VARCHAR(255) DEFAULT NULL",
    "ALTER TABLE skripsi ADD COLUMN IF NOT EXISTS notif_p1 TINYINT(1) DEFAULT 1",
    "ALTER TABLE skripsi ADD COLUMN IF NOT EXISTS notif_p2 TINYINT(1) DEFAULT 1",
    "CREATE TABLE IF NOT EXISTS dashboard_settings (
        id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
        setting_key VARCHAR(100) NOT NULL UNIQUE,
        setting_value TEXT DEFAULT NULL,
        label VARCHAR(255) DEFAULT NULL,
        description TEXT DEFAULT NULL,
        updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        PRIMARY KEY(id)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci"
];
foreach ($queries as $q) {
    if (!$mysqli->query($q)) {
        echo 'FAILED: ' . $q . ' -- ' . $mysqli->error . "\n";
    } else {
        echo 'OK: ' . $q . "\n";
    }
}
$mysqli->close();
