<?php
$host = 'localhost';
$user = 'root';
$pass = ''; // Leave empty if no password in XAMPP
$db   = 'urban';

$conn = new mysqli($host, $user, $pass, $db);

// Check connection
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}
?>
