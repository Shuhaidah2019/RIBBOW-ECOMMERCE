<?php
session_start(); // always at the top
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
  http_response_code(405);
  echo "Method Not Allowed";
  exit;
}



// DB config
$host = "localhost";
$user = "root";
$pass = "";
$dbname = "ribbowsite_db";

// Connect
$conn = new mysqli($host, $user, $pass, $dbname);
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

// Get values
$name = trim($_POST['name']);
$email = trim($_POST['email']);
$password = $_POST['password'];

// Hash password
$hashedPassword = password_hash($password, PASSWORD_DEFAULT);

// Check if user already exists
$check = $conn->prepare("SELECT id FROM users WHERE email = ?");
$check->bind_param("s", $email);
$check->execute();
$check->store_result();

if ($check->num_rows > 0) {
  echo "<script>
    alert('Email already registered');
    window.location.href = '../public/auth.html';
  </script>";
} else {
  $stmt = $conn->prepare("INSERT INTO users (name, email, password) VALUES (?, ?, ?)");
  $stmt->bind_param("sss", $name, $email, $hashedPassword);

  if ($stmt->execute()) {
    $user_id = $stmt->insert_id;

    // Set session for auto-login
    $_SESSION['user_id'] = $user_id;
    $_SESSION['user_name'] = $name;
    $_SESSION['user_email'] = $email;

    // Redirect to home
    header("Location: ../public/index.php");
    exit;
  } else {
    echo "Error: " . $stmt->error;
  }

  $stmt->close();
}

$check->close();
$conn->close();
?>