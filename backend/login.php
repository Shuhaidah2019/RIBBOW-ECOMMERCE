<?php

session_start();
include '../config/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $email = $_POST['email'];
  $password = $_POST['password'];

  $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
  $stmt->bind_param("s", $email);
  $stmt->execute();
  $result = $stmt->get_result();

  if ($result->num_rows === 1) {
    $user = $result->fetch_assoc();

    if (password_verify($password, $user['password'])) {
      // ✅ Save necessary user info into the session
      $_SESSION['user_id'] = $user['id'];
      $_SESSION['user_name'] = $user['name'];
      $_SESSION['user_email'] = $user['email'];

      // ✅ Redirect to homepage after successful login
      header("Location: /RIBBOW/public/index.php");
      exit();
    }
  }

  // ❌ If login fails (wrong email or password), redirect back to login page
  header("Location: /RIBBOW/public/auth.html?error=invalid");
  exit();
}
?>   