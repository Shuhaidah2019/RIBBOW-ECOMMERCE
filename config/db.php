<?php
$host = 'localhost';
$user = 'root';
$pass = ''; // empty if you haven't set one
$db = 'ribbowsite_db';

$conn = new mysqli($host, $user, $pass, $db);

// Check connection
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}
?>
