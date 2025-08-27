<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
  echo json_encode(["success" => false, "message" => "Not logged in"]);
  exit();
}

require_once '../config/db.php';

$orderId = intval($_GET['id'] ?? 0);
$userId = $_SESSION['user_id'];

$stmt = $conn->prepare("SELECT items FROM orders WHERE id = ? AND user_id = ?");
$stmt->bind_param("ii", $orderId, $userId);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
  echo json_encode(["success" => false, "message" => "Order not found"]);
  exit();
}

$order = $result->fetch_assoc();
$items = json_decode($order['items'], true);

echo json_encode([
  "success" => true,
  "items" => $items
]);

$stmt->close();
$conn->close();
