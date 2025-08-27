<?php

// Protect page
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit;
}

require_once '../backend/helpers/csrf-helper.php';

require_once '../config/db.php'; // DB connection

$message = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $price = floatval($_POST['price']);
    $category = trim($_POST['category']);

    // Handle image upload
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $fileTmpPath = $_FILES['image']['tmp_name'];
        $fileName = $_FILES['image']['name'];
        $fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
        $newFileName = uniqid('product_') . '.' . $fileExt;
        $uploadDir = '../public/images/';
        $destPath = $uploadDir . $newFileName;

        $allowedExts = ['jpg','jpeg','png','webp'];
        if (in_array($fileExt, $allowedExts)) {
            if (move_uploaded_file($fileTmpPath, $destPath)) {
                // Insert into DB
                $stmt = $conn->prepare("INSERT INTO products (name, price, category, image) VALUES (?, ?, ?, ?)");
                $stmt->bind_param("sdss", $name, $price, $category, $newFileName);
                if ($stmt->execute()) {
                    header("Location: products.php?message=Product added successfully");
                    exit();
                } else {
                    $message = "❌ Failed to add product.";
                }
            } else {
                $message = "❌ Failed to upload image.";
            }
        } else {
            $message = "❌ Invalid image format. Allowed: jpg, jpeg, png, webp.";
        }
    } else {
        $message = "❌ Please select an image.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta charset="UTF-8">
    <title>Add Product | RIBBOW Admin</title>
    <link rel="stylesheet" href="../public/css/style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body style="background-color: #fffafc; font-family: 'Poppins', sans-serif;">

<nav class="navbar navbar-light bg-light p-3">
    <div class="container-fluid">
        <a class="navbar-brand fw-bold" href="products.php">RIBBOW Admin Panel</a>
        <a href="products.php" class="btn btn-outline-secondary">Back to products</a>
    </div>
</nav>

<div class="container py-5">
    <div class="card shadow-sm">
        <div class="card-body">
            <h3 class="card-title mb-4">➕ Add New Product</h3>

            <?php if($message): ?>
                <div class="alert alert-info"><?= htmlspecialchars($message) ?></div>
            <?php endif; ?>

            <form method="POST" enctype="multipart/form-data">
                <input type="hidden" name="csrf_token" value="<?php echo generateCsrfToken(); ?>">

                <div class="mb-3">
                    <label class="form-label">Product Name</label>
                    <input type="text" name="name" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Price (₦)</label>
                    <input type="number" name="price" class="form-control" step="0.01" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Category</label>
                    <select name="category" class="form-control" required>
                        <option value="facesheet">Face Sheet</option>
                        <option value="handcream">Hand Cream</option>
                        <option value="lipbalm">Lip Balm</option>
                        <option value="scrunchies">Scrunchies</option>
                        <option value="hairclips">Hair Clips</option>
                        <option value="facemassager">Face Massager</option>
                        <option value="beadedbag">Beaded Bag</option>
                        <option value="totebags">Tote Bag</option>
                        <option value="facetowel">Face Towel</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label">Product Image</label>
                    <input type="file" name="image" class="form-control" accept=".jpg,.jpeg,.png,.webp" required>
                </div>

                <button type="submit" class="btn btn-success">Add Product</button>
                <a href="products.php" class="btn btn-secondary">Cancel</a>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
