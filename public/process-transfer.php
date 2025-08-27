<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: auth.html");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $totalAmount = $_POST['total_amount'] ?? 0;
    $cartItems = $_POST['cart_items'] ?? '[]';

    // File upload handling
    $uploadDir = __DIR__ . "/uploads/";
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0775, true);
    }

    $proofFile = $_FILES['proof'];
    $fileName = time() . "_" . basename($proofFile['name']);
    $targetPath = $uploadDir . $fileName;

    if (move_uploaded_file($proofFile['tmp_name'], $targetPath)) {
        // Save order in DB
        require_once "../config/db.php";
        $stmt = $conn->prepare("INSERT INTO orders (user_id, total_amount, items, payment_method, payment_proof) VALUES (?, ?, ?, 'Bank Transfer', ?)");
        $stmt->bind_param("idss", $_SESSION['user_id'], $totalAmount, $cartItems, $fileName);
        
        if ($stmt->execute()) {
            header("Location: success.php");
            exit();
        } else {
            echo "Database error: " . $conn->error;
        }
    } else {
        echo "Error uploading payment proof.";
    }
}
?>
