<?php
session_start();
require_once '../config/db.php';

// Check admin session
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

$id = $_GET['id'] ?? null;

if ($id) {
    $stmt = $conn->prepare("DELETE FROM products WHERE id = ?");
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        header("Location: products.php?message=Product deleted successfully");
        exit();
    } else {
        header("Location: products.php?error=Error deleting product");
        exit();
    }
} else {
    header("Location: products.php?error=Invalid product");
    exit();
}
