<?php
// login.php
session_start(); // ✅ Start session

$host = "localhost";
$user = "root";
$pass = "";
$dbname = "ribbowsite_db";

$conn = new mysqli($host, $user, $pass, $dbname);
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

$email = $_POST['email'];
$password = $_POST['password'];

$stmt = $conn->prepare("SELECT id, name, password FROM users WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 1) {
  $row = $result->fetch_assoc();

  if (password_verify($password, $row['password'])) {
    // ✅ Set session variables
    $_SESSION['user_id'] = $row['id'];
    $_SESSION['user_name'] = $row['name'];

    // ✅ Redirect to protected page
    header("Location: ../public/index.php");
    exit();
  } else {
    echo "<script>alert('Invalid password'); window.location.href = '../public/auth.html';</script>";
  }
} else {
  echo "<script>alert('Email not found'); window.location.href = '../public/auth.html';</script>";
}

$stmt->close();
$conn->close();
?>
