
<?php
require_once '../backend/helpers/csrf-helper.php';

session_start();
require_once "../config/db.php"; // adjust path if needed

$error = '';

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    // Check if admin exists
    $stmt = $conn->prepare("SELECT id, username, email, password FROM admins WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $row = $result->fetch_assoc();

        // Verify hashed password
        if (password_verify($password, $row['password'])) {
            // Store session
            $_SESSION['admin_id'] = $row['id'];
            $_SESSION['admin_name'] = $row['username'];

            // Redirect to dashboard
            header("Location: orders.php"); // redirect to your dashboard page
            exit();
        } else {
            $error = "Invalid password.";
        }
    } else {
        $error = "No account found with that email.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Admin Login</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body {
      background: #f0f2f5; 
      height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
    }
    .login-card {
      width: 100%;
      max-width: 400px;
      padding: 2rem;
      background: #fff;
      border-radius: 10px;
      box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
    }
    .error-message {
      color: red;
      font-size: 0.9rem;
      margin-bottom: 10px;
    }
  </style>
</head>

<body>
  <div class="login-card">
    <h3 class="text-center mb-3 fw-bold">Admin Login</h3>

    <?php if (!empty($error)): ?>
      <p class="error-message text-center"><?= htmlspecialchars($error) ?></p>
    <?php endif; ?>

    <form method="POST">
      <input type="hidden" name="csrf_token" value="<?php echo generateCsrfToken(); ?>">

      <div class="mb-3">
        <input type="email" name="email" class="form-control" placeholder="Email" required>
      </div>
      <div class="mb-3">
        <input type="password" name="password" class="form-control" placeholder="Password" required>
      </div>
      <button type="submit" class="btn btn-primary w-100">Login</button>
    </form>
  </div>
</body>

</html>
