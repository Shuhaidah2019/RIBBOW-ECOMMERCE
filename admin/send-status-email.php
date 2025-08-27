<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['admin_id'])) {
    echo json_encode(['success' => false, 'message' => 'Not logged in']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['order_id'])) {
    echo json_encode(['success' => false, 'message' => 'Invalid request']);
    exit;
}

require_once '../config/db.php';
require_once '../backend/send-email.php';

$orderId = intval($_POST['order_id']);

$stmt = $conn->prepare("SELECT fullname, email, status, items FROM orders WHERE id = ?");
$stmt->bind_param("i", $orderId);
$stmt->execute();
$result = $stmt->get_result();
$order = $result->fetch_assoc();
$stmt->close();

if (!$order) {
    echo json_encode(['success' => false, 'message' => 'Order not found']);
    exit;
}

// Compose email like in update-status.php
$name = $order['fullname'];
$email = $order['email'];
$status = $order['status'];
$items = json_decode($order['items'], true);

$itemList = "";
if (is_array($items)) {
    foreach ($items as $item) {
        $itemList .= "- {$item['name']} (₦{$item['price']})<br>";
    }
}

switch ($status) {
    case 'Pending':
        $subject = "⏳ Your RIBBOW order is pending";
        $message = "<p>Hi {$name},</p><p>Your order <strong>#{$orderId}</strong> is still pending.</p><p>{$itemList}</p>";
        break;
    case 'Shipped':
        $subject = "📦 Your RIBBOW order is on its way!";
        $message = "<p>Hi {$name},</p><p>Your order <strong>#{$orderId}</strong> has been shipped.</p><p>{$itemList}</p>";
        break;
    case 'Delivered':
        $subject = "🎉 Your RIBBOW order has been delivered!";
        $message = "<p>Hi {$name},</p><p>Your order <strong>#{$orderId}</strong> has been delivered.</p><p>{$itemList}</p>";
        break;
    case 'Cancelled':
        $subject = "❌ Your RIBBOW order has been cancelled";
        $message = "<p>Hi {$name},</p><p>Your order <strong>#{$orderId}</strong> has been cancelled.</p><p>{$itemList}</p>";
        break;
}

// Send email
if (sendOrderEmail($email, $name, $message, $subject)) {
 // ✅ Mark email as sent
    $stmt3 = $conn->prepare("UPDATE orders SET email_sent = 1 WHERE id = ?");
    $stmt3->bind_param("i", $orderId);
    $stmt3->execute();
    $stmt3->close();

    echo json_encode(['success' => true, 'message' => 'Email sent successfully']);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to send email']);
}

$conn->close();
?>
