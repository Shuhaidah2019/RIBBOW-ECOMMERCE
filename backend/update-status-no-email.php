<?php
session_start();

require_once '../config/db.php';

// Handle only POST requests (avoid direct GET access)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

  // Get order ID from the form and sanitize
  $orderId = intval($_POST['order_id']);

  // Get the new status from the form
  $newStatus = $_POST['status'];

  // -------------------------------
  // STEP 1: Update the order status in the database
  // -------------------------------
  $stmt = $conn->prepare("UPDATE orders SET status = ? WHERE id = ?");
  $stmt->bind_param("si", $newStatus, $orderId);
  $stmt->execute();
  $stmt->close();

  // -------------------------------
  // STEP 2: Redirect back with a message
  // -------------------------------
  header("Location: orders-dashboard.php?message=Order #{$orderId} updated to {$newStatus}");
  exit(); // Stop script execution
}
?>
