<?php
// Hardcoded database config for CLI debugging
$hostname = 'localhost';
$username = 'root';
$password = '';
$database = 'bimbingan';

$conn = new mysqli($hostname, $username, $password, $database);
if ($conn->connect_error) die('Connection failed: ' . $conn->connect_error);

$result = $conn->query('SELECT id, username, password FROM mstr_akun LIMIT 5');
echo "Sample passwords from database:\n";
while ($row = $result->fetch_assoc()) {
    echo 'ID: ' . $row['id'] . ', Username: ' . $row['username'] . ', Password: ' . substr($row['password'], 0, 20) . '...' . PHP_EOL;
}
$conn->close();
?>
