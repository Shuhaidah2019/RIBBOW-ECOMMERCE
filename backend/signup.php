<?php
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
  http_response_code(405);
  echo "Method Not Allowed";
  exit;
}

// signup.php
$host = "localhost";
$user = "root";
$pass = "";
$dbname = "ribbowsite_db";

$conn = new mysqli($host, $user, $pass, $dbname);
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

// Get values from form
$name = $_POST['name'];
$email = $_POST['email'];
$password = $_POST['password'];

// Hash password
$hashedPassword = password_hash($password, PASSWORD_DEFAULT);

// Check if user exists
$check = $conn->prepare("SELECT id FROM users WHERE email = ?");
$check->bind_param("s", $email);
$check->execute();
$check->store_result();

if ($check->num_rows > 0) {
  echo "<script>alert('Email already registered'); window.location.href = '../public/auth.html';</script>";
} else {
  $stmt = $conn->prepare("INSERT INTO users (name, email, password) VALUES (?, ?, ?)");
  $stmt->bind_param("sss", $name, $email, $hashedPassword);

  if ($stmt->execute()) {
    session_start();
    $_SESSION['user_email'] = $email;
    $_SESSION['user_name'] = $name;

    echo "<script>
      alert('Registration successful! Redirecting...');
      window.location.href = '../public/index.php';
    </script>";
  } else {
    echo "Error: " . $stmt->error;
  }

  $stmt->close();
}

$check->close();
$conn->close();
?>
