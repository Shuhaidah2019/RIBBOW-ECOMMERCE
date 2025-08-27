<?php
session_start();
require_once '../config/db.php';

// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}
require_once '../backend/helpers/csrf-helper.php';

$id = $_GET['id'] ?? null;

// Fetch product details
if ($id) {
    $stmt = $conn->prepare("SELECT * FROM products WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $product = $stmt->get_result()->fetch_assoc();
}

// Handle update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $price = $_POST['price'];
    $category = $_POST['category'];

    $stmt = $conn->prepare("UPDATE products SET name=?, price=?, category=? WHERE id=?");
    $stmt->bind_param("sdsi", $name, $price, $category, $id);

    if ($stmt->execute()) {
        header("Location: products.php?message=Product updated");
        exit();
    } else {
        echo "Error updating product: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta charset="UTF-8">
  <title>Edit Product</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="container mt-5">
  <h2>Edit Product</h2>
  <form method="post">
    <input type="hidden" name="csrf_token" value="<?php echo generateCsrfToken(); ?>">

    <div class="mb-3">
      <label class="form-label">Product Name</label>
      <input type="text" name="name" class="form-control" 
             value="<?= htmlspecialchars($product['name']) ?>" required>
    </div>
    <div class="mb-3">
      <label class="form-label">Price</label>
      <input type="number" name="price" step="0.01" class="form-control" 
             value="<?= htmlspecialchars($product['price']) ?>" required>
    </div>
    <div class="mb-3">
      <label class="form-label">Category</label>
      <input type="text" name="category" class="form-control" 
             value="<?= htmlspecialchars($product['category']) ?>" required>
    </div>
    <button type="submit" class="btn btn-primary">Update</button>
    <a href="products.php" class="btn btn-secondary">Cancel</a>
  </form>
</body>
</html>
