<?php


session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit;
}

require_once '../config/db.php';

// Fetch all products
$result = $conn->query("SELECT * FROM products ORDER BY created_at DESC");

// Use fetch_all (MySQLi style)
$products = $result->fetch_all(MYSQLI_ASSOC);
?>
<!DOCTYPE html>
<html>
<head>
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Admin | Products</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="../public/css/style.css">
</head>
<body class="bg-light">
<div class="container py-5">
    <h2 class="mb-4">Products</h2>
    <a href="add-product.php" class="btn btn-success mb-3">➕ Add Product</a>
    
    <a href="orders.php" class="btn btn-secondary mb-3">⬅ Back to Dashboard</a>

    <table class="table table-bordered table-hover">
        <thead class="table-light">
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Price</th>
                <th>Category</th>
                <th>Image</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
        <?php if (!empty($products)): ?>
            <?php foreach($products as $product): ?>
            <tr>
                <td><?= $product['id'] ?></td>
                <td><?= htmlspecialchars($product['name']) ?></td>
                <td>₦<?= number_format($product['price'], 2) ?></td>
                <td><?= htmlspecialchars($product['category']) ?></td>
                <td>
                    <?php if($product['image']): ?>
                        <img src="../public/images/<?= $product['image'] ?>" width="80" class="img-thumbnail">
                    <?php else: ?>
                        N/A
                    <?php endif; ?>
                </td>
                <td>
                    <a href="edit-product.php?id=<?= $product['id'] ?>" class="btn btn-sm btn-info">✏ Edit</a>
                    <a href="delete-product.php?id=<?= $product['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Delete this product?');">🗑 Delete</a>
                </td>
            </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr>
                <td colspan="6" class="text-center">No products found</td>
            </tr>
        <?php endif; ?>
        </tbody>
    </table>
</div>
</body>
</html>
