<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: admin-login.php");
    exit();
}

require_once '../config/db.php';
require_once '../backend/send-email.php'; // Make sure sendOrderEmail() exists

if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $orderId = (int)$_GET['id'];

    // Fetch customer info BEFORE deletion
    $orderStmt = $conn->prepare("SELECT fullname, email FROM orders WHERE id = ?");
    $orderStmt->bind_param("i", $orderId);
    $orderStmt->execute();
    $result = $orderStmt->get_result();
    $order = $result->fetch_assoc();
    $orderStmt->close();

    // Prepare delete statement
    $stmt = $conn->prepare("DELETE FROM orders WHERE id = ?");
    $stmt->bind_param("i", $orderId);

    if ($stmt->execute()) {
        $msg = "Order deleted successfully.";

        // Send email to customer
        $customerName = $order['fullname'] ?? '';
        $customerEmail = $order['email'] ?? '';

        if ($customerEmail) {
            $emailHtml = "
            <div style='font-family:Poppins,sans-serif;color:#333;'>
                <h2 style='color:#6a1b9a;'>RIBBOW Notification</h2>
                <p>Hi {$customerName},</p>
                <p>Your order with ID <strong>#{$orderId}</strong> has been deleted from our system.</p>
                <p>If you think this was a mistake, contact us at <a href='mailto:support@ribbow.com'>support@ribbow.com</a>.</p>
                <p>Thanks,<br>RIBBOW Team</p>
            </div>
            ";
            sendOrderEmail($customerEmail, $customerName, $emailHtml, "RIBBOW Order #$orderId Deleted");
        }

        // Send email to admin
        $adminEmail = "shuhaidahrabiu@gmail.com";
        $adminHtml = "
        <div style='font-family:Poppins,sans-serif;color:#333;'>
            <h2 style='color:#6a1b9a;'>RIBBOW Admin Notification</h2>
            <p>Order ID <strong>#{$orderId}</strong> placed by {$customerName} ({$customerEmail}) has been deleted.</p>
        </div>
        ";
        sendOrderEmail($adminEmail, "RIBBOW Admin", $adminHtml, "Order #$orderId Deleted by Admin");

    } else {
        $msg = "Error deleting order.";
    }

    $stmt->close();
} else {
    $msg = "Invalid order ID.";
}

$conn->close();
header("Location: orders.php?message=" . urlencode($msg));
exit();
