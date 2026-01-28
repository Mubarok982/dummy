<?php
// Script to hash plain text passwords in the database
$hostname = 'localhost';
$username = 'root';
$password = '';
$database = 'bimbingan';

$conn = new mysqli($hostname, $username, $password, $database);
if ($conn->connect_error) die('Connection failed: ' . $conn->connect_error);

$result = $conn->query('SELECT id, username, password FROM mstr_akun');
while ($row = $result->fetch_assoc()) {
    if (!password_get_info($row['password'])['algo']) {
        // Plain text, hash it
        $hashed = password_hash($row['password'], PASSWORD_DEFAULT);
        $conn->query("UPDATE mstr_akun SET password = '$hashed' WHERE id = " . $row['id']);
        echo 'Hashed password for user: ' . $row['username'] . PHP_EOL;
    }
}
echo 'All plain text passwords have been hashed.' . PHP_EOL;
$conn->close();
?>
