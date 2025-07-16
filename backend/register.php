<?php
// Connect to your database
$conn = new mysqli("localhost", "root", "", "ribbowsite_db"); // Ensure this matches your DB name

if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

// Get and sanitize form input
$name = htmlspecialchars($_POST['name']);
$email = htmlspecialchars($_POST['email']);
$password = password_hash($_POST['password'], PASSWORD_DEFAULT);

// Check if email already exists
$check = $conn->prepare("SELECT * FROM users WHERE email = ?");
$check->bind_param("s", $email);
$check->execute();
$result = $check->get_result();

if ($result->num_rows > 0) {
  echo "<script>
    alert('Email already registered. Please log in.');
    window.location.href = 'auth.html';
  </script>";
} else {
  // Insert new user
  $stmt = $conn->prepare("INSERT INTO users (name, email, password) VALUES (?, ?, ?)");
  $stmt->bind_param("sss", $name, $email, $password);

  if ($stmt->execute()) {
    echo "<script>
      alert('Registration successful!');
      localStorage.setItem('isLoggedIn', 'true');
      window.location.href = 'shop.html';
    </script>";
  } else {
    echo "Error: " . $stmt->error;
  }
}

$conn->close();
?>
