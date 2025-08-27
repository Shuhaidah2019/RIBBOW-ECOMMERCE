<?php
$servername = "localhost";
$username = "admins";       // your MySQL username
$password = "$2y$10$ECvufZ19V9q9LiiFsGhr1uunTAd4W.877RwIdR.n3wYW0Fu1B1pMS";           // your MySQL password
$dbname = "ribbowsite_db"; // your database name

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
