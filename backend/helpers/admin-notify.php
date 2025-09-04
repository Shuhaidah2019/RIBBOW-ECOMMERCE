<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once __DIR__ . '/email-helper.php'; // make sure sendEmail() exists

/**
 * Notify admin of a new order
 * @param array $order Array with order details (id, fullname, email, total, items)
 */
function notifyAdminNewOrder($order) {
    $adminEmail = 'shuhaidahrabiu@gmail.com'; // your admin email
    $adminName  = 'RIBBOW Admin';

    $orderId  = $order['id'];
    $customer = htmlspecialchars($order['fullname']);
    $email    = htmlspecialchars($order['email']);
    $total    = number_format($order['total'], 2);
    $items    = $order['items'];

    // Build item list HTML
    $itemList = "";
    foreach ($items as $item) {
        $itemName  = htmlspecialchars($item['name']);
        $itemPrice = number_format($item['price'], 2);
        $qty       = isset($item['quantity']) ? (int)$item['quantity'] : 1;
        $itemList .= "- {$itemName} x {$qty} (₦{$itemPrice})<br>";
    }

    $subject = " New Order #{$orderId} Received!";
    $body = "
        <p>Hello Admin,</p>
        <p>A new order has been placed on <strong>RIBBOW</strong>:</p>
        <p><strong>Order ID:</strong> {$orderId}<br>
           <strong>Customer:</strong> {$customer} ({$email})<br>
           <strong>Total:</strong> ₦{$total}</p>
        <p><strong>Items Ordered:</strong><br>{$itemList}</p>
        <p>Check your dashboard for more details.</p>
        <p>💜 RIBBOW</p>
    ";

    try {
        $result = sendEmail($adminEmail, $adminName, $subject, $body, true);
        if (!$result['success']) {
            error_log("Admin email failed: " . $result['error']);
        }
    } catch (\Exception $e) {
        error_log("Admin email exception: " . $e->getMessage());
    }
}
