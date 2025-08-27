
<?php
session_start();

// 🔒 Protect: only admin can access
if (!isset($_SESSION['admin_id'])) {
    echo json_encode(['success' => false, 'message' => 'Not logged in']);
    exit;
}

require_once '../config/db.php';
require_once '../backend/send-email.php';

header('Content-Type: application/json'); // AJAX expects JSON

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $orderId = intval($_POST['order_id']);
    $status = $_POST['status'];

    // ✅ Validate status
    $validStatuses = ['Pending', 'Shipped', 'Delivered', 'Cancelled'];
    if (!in_array($status, $validStatuses)) {
        echo json_encode(['success' => false, 'message' => 'Invalid status']);
        exit();
    }

    // 🔄 Update the order status
    $stmt = $conn->prepare("UPDATE orders SET status = ? WHERE id = ?");
    $stmt->bind_param("si", $status, $orderId);
    $success = $stmt->execute();
    $stmt->close();

    if ($success) {
        // ✅ Only send email for Shipped / Delivered / Cancelled
        if (in_array($status, ['Shipped', 'Delivered', 'Cancelled'])) {
            $stmt2 = $conn->prepare("SELECT fullname, email, items FROM orders WHERE id = ?");
            $stmt2->bind_param("i", $orderId);
            $stmt2->execute();
            $result = $stmt2->get_result();
            $order = $result->fetch_assoc();
            $stmt2->close();

            if ($order) {
                $name = $order['fullname'];
                $email = $order['email'];
                $items = json_decode($order['items'], true);

                $itemList = "";
                if (is_array($items)) {
                    foreach ($items as $item) {
                        $itemList .= "- {$item['name']} (₦{$item['price']})<br>";
                    }
                }

                // Compose email based on status
                switch ($status) {
                    case 'Shipped':
                        $subject = "📦 Your RIBBOW order is on its way!";
                        $message = "<p>Hi {$name},</p>
                                    <p>Your order <strong>#{$orderId}</strong> status has been updated to <strong>{$status}</strong>.</p>
                                    <p><strong>Order Summary:</strong><br>{$itemList}</p>
                                    <p>Thanks for shopping with RIBBOW 💜</p>";
                        break;

                    case 'Delivered':
                        $subject = "🎉 Your RIBBOW order has been delivered!";
                        $message = "<p>Hi {$name},</p>
                                    <p>Your order <strong>#{$orderId}</strong> has been successfully delivered.</p>
                                    <p><strong>Order Summary:</strong><br>{$itemList}</p>
                                    <p>Thanks for shopping with RIBBOW 💜</p>";
                        break;

                    case 'Cancelled':
                        $subject = "❌ Your RIBBOW order has been cancelled";
                        $message = "<p>Hi {$name},</p>
                                    <p>We regret to inform you that your order <strong>#{$orderId}</strong> has been cancelled.</p>
                                    <p><strong>Order Summary:</strong><br>{$itemList}</p>
                                    <p>If you have any questions, contact us at <a href='mailto:support@ribbow.com'>support@ribbow.com</a>.</p>
                                    <p>Thanks,<br>RIBBOW Team 💜</p>";
                        break;
                }

                // 📩 Send email
if (sendOrderEmail($email, $name, $message, $subject)) {
    // Mark email as sent in DB
    $stmt3 = $conn->prepare("UPDATE orders SET email_sent = 1 WHERE id = ?");
    $stmt3->bind_param("i", $orderId);
    $stmt3->execute();
    $stmt3->close();
}

            }
        }

        echo json_encode(['success' => true, 'message' => 'Order status updated successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to update order status']);
    }

    $conn->close();
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request']);
}
