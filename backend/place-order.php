<?php
session_start();
require_once '../config/db.php';

if (!isset($_SESSION['user_id'])) {
    die("Not logged in");
}

$user_id = $_SESSION['user_id'];

// Check required fields
$required = ['fullname', 'email', 'address', 'phone', 'cart_items'];
foreach ($required as $field) {
    if (empty($_POST[$field])) {
        die("Please fill all required fields.");
    }
}

// Handle file upload
if (!isset($_FILES['proof']) || $_FILES['proof']['error'] !== UPLOAD_ERR_OK) {
    die("Please upload a valid proof of payment file.");
}

$proofName = time() . '_' . basename($_FILES['proof']['name']);
$uploadDir = '../uploads/';
if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0777, true);
}
$proofPath = $uploadDir . $proofName;
move_uploaded_file($_FILES['proof']['tmp_name'], $proofPath);

// Decode cart items
$cartItems = json_decode($_POST['cart_items'], true);
if (empty($cartItems)) {
    die("Cart is empty.");
}

// Insert order into DB
$stmt = $conn->prepare("INSERT INTO orders (user_id, fullname, email, address, phone, proof, status) VALUES (?, ?, ?, ?, ?, ?, 'pending')");
$stmt->bind_param("isssss", $user_id, $_POST['fullname'], $_POST['email'], $_POST['address'], $_POST['phone'], $proofName);
$stmt->execute();
$order_id = $stmt->insert_id;

// Insert order items
$itemStmt = $conn->prepare("INSERT INTO order_items (order_id, product_id, quantity) VALUES (?, ?, ?)");
foreach ($cartItems as $item) {
    $itemStmt->bind_param("iii", $order_id, $item['id'], $item['quantity']);
    $itemStmt->execute();
}

echo "Order placed successfully!";
