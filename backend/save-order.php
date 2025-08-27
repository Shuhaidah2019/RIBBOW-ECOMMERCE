<?php
session_start();
header('Content-Type: application/json');
require_once '../config/db.php'; // adjust path

if (!isset($_SESSION['user_id'])) {
    echo json_encode(["success" => false, "message" => "Not logged in"]);
    exit;
}

$data = json_decode(file_get_contents("php://input"), true);
$reference = $data['reference'] ?? '';
$total = $data['total'] ?? '';

if ($reference && $total) {
    $userId = $_SESSION['user_id'];
    $stmt = $conn->prepare("INSERT INTO orders (user_id, total_amount, payment_reference, status) VALUES (?, ?, ?, 'Paid')");
    $stmt->bind_param("ids", $userId, $total, $reference);

    if ($stmt->execute()) {
        echo json_encode(["success" => true]);
    } else {
        echo json_encode(["success" => false, "message" => "DB error"]);
    }
} else {
    echo json_encode(["success" => false, "message" => "Invalid data"]);
}

// Get JSON input
$rawData = file_get_contents("php://input");
$data = json_decode($rawData, true);

if (!is_array($data)) {
    echo json_encode(["success" => false, "message" => "Invalid JSON payload"]);
    exit();
}

$fullname = trim($data['fullname'] ?? '');
$address  = trim($data['address'] ?? '');
$phone    = trim($data['phone'] ?? '');
$items    = $data['cart'] ?? [];
$total    = floatval($data['total'] ?? 0);
$user_id  = $_SESSION['user_id'];
$status   = "Pending";

if (empty($fullname) || empty($address) || empty($phone) || !is_array($items) || count($items) === 0) {
    echo json_encode(["success" => false, "message" => "Missing required fields or cart is empty"]);
    exit();
}

// Encode cart items
$items_json = json_encode($items);

// Fetch email from users table
$email = '';
$emailQuery = $conn->prepare("SELECT email FROM users WHERE id = ?");
$emailQuery->bind_param("i", $user_id);
$emailQuery->execute();
$result = $emailQuery->get_result();
if ($row = $result->fetch_assoc()) {
    $email = $row['email'];
}
$emailQuery->close();

// Insert order (corrected)
$stmt = $conn->prepare("INSERT INTO orders (user_id, fullname, email, address, phone, items, total, status, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW())");

if (!$stmt) {
    echo json_encode(["success" => false, "message" => "SQL Prepare failed: " . $conn->error]);
    exit();
}

$stmt->bind_param("isssssss", $user_id, $fullname, $email, $address, $phone, $items_json, $total, $status);

if ($stmt->execute()) {
    $order_id = $stmt->insert_id;

    // EMAIL STUFF
    // EMAIL STUFF
$logoURL = "http://localhost/RIBBOW/public/images/ribbow.png";
$orderSummaryHtml = "";
foreach ($items as $item) {
    $orderSummaryHtml .= "
    <tr>
      <td style='padding:8px;border:1px solid #ddd;'>{$item['name']}</td>
      <td style='padding:8px;border:1px solid #ddd;'>₦" . number_format($item['price'], 2) . "</td>
    </tr>";
}

$htmlContent = "
<html>
  <body style='font-family:Poppins,sans-serif;background-color:#fffafc;padding:20px;color:#4a0072;'>
    <div style='max-width:600px;margin:0 auto;border:1px solid #e0b3ff;border-radius:8px;overflow:hidden;'>
      <div style='background-color:#e0b3ff;padding:20px;text-align:center;'>
        <img src='$logoURL' alt='RIBBOW Logo' style='height:50px;'>
        <h2 style='margin:10px 0;color:#4a0072;'>Order Confirmation</h2>
      </div>
      <div style='padding:20px;'>
        <p>Hi <strong>$fullname</strong>,</p>
        <p>Thanks for shopping with <strong>RIBBOW</strong> 💜</p>
        <p><strong>Order ID:</strong> $order_id</p>
        <p><strong>Status:</strong> $status</p>
        <p><strong>Total:</strong> ₦" . number_format($total, 2) . "</p>

        <h4>Delivery Info</h4>
        <p><strong>Address:</strong> $address</p>
        <p><strong>Phone:</strong> $phone</p>

        <h4>Order Summary</h4>
        <table style='width:100%;border-collapse:collapse;margin-top:10px;'>$orderSummaryHtml</table>

        <p style='margin-top:20px;'>We’ll process your order soon.</p>
        <p style='color:#999;'>– The RIBBOW Team</p>
      </div>
    </div>
  </body>
</html>
";

$subjectCustomer = "🎉 RIBBOW Order Confirmation - Order #$order_id";
$subjectAdmin    = "📥 New Order Received - RIBBOW";

// Send email to customer
if ($email) {
    sendRibbowEmail($email, $subjectCustomer, $htmlContent);
}

// Send to admin
sendRibbowEmail("shuhaidahrabiu@gmail.com", $subjectAdmin, $htmlContent);


    echo json_encode(["success" => true, "message" => "Order saved."]);
} else {
    echo json_encode(["success" => false, "message" => "Database Error: " . $stmt->error]);
}

$stmt->close();
$conn->close();
ob_end_flush();
