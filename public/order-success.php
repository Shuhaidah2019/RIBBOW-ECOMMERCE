<?php
session_start();

if (!isset($_GET['order_id'])) {
    header("Location: index.php");
    exit();
}

require_once '../config/db.php';

$orderId = (int)$_GET['order_id'];

// Fetch order from DB
$stmt = $conn->prepare("SELECT * FROM orders WHERE id = ?");
$stmt->bind_param("i", $orderId);
$stmt->execute();
$result = $stmt->get_result();
$order = $result->fetch_assoc();
$stmt->close();
$conn->close();

if (!$order) {
    die("Order not found.");
}

// Decode items
$items = json_decode($order['items'], true);
?>

<script>
  document.addEventListener('DOMContentLoaded', () => {
      localStorage.removeItem(`cart_<?= $_SESSION['user_id'] ?>`);
  });
</script>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Order Success | RIBBOW</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"/>
<style>
body {
    font-family: 'Poppins', sans-serif;
    background: linear-gradient(to bottom, #e2d6e1ff, #fffafc);
    min-height: 100vh;
    padding-top: 70px;
}

.container {
    max-width: 800px;
}

/* Success banner */
.success-banner {
    background-color: #f5e6ff;
    border: 1px solid #d8b4fe;
    border-radius: 15px;
    padding: 30px;
    text-align: center;
    box-shadow: 0 6px 12px rgba(0,0,0,0.08);
}
.success-banner h2 { color: #6a1b9a; margin-bottom: 15px; }
.success-banner p { font-size: 1.1rem; margin-bottom: 10px; }

/* Buttons */
.btn-purple {
    background-color: #6a1b9a;
    color: #fff;
}
.btn-purple:hover { background-color: #580a9c; color: #fff; }
.btn-outline-secondary {
    border-radius: 50px;
}

/* Order Details card */
.order-details {
    background-color: #fff;
    padding: 20px;
    border-radius: 12px;
    box-shadow: 0 4px 10px rgba(0,0,0,0.05);
    margin-top: 20px;
}
.order-details h4, .order-details h5 { color: #6a1b9a; margin-bottom: 10px; }
.order-details p, .order-details ul { margin-bottom: 10px; }
.order-details ul li { padding: 8px 0; border-bottom: 1px solid #eee; }

/* Footer */
footer {
    background-color: #6a1b9a;
    color: white;
    text-align: center;
    padding: 1rem 0;
}
footer a { color: white; margin: 0 8px; font-size: 1.2rem; }
footer a:hover { color: #dfc1ddff; }

@media (max-width: 768px) {
    .success-banner, .order-details { padding: 20px; }
    .btn-purple { width: 100%; margin-bottom: 10px; }
}
</style>
</head>
<body>

<div class="container py-4">
    <div class="success-banner mb-4 shadow-sm">
        <h2 class="fw-bold">🎉 Thank you for your order!</h2>
        <p>Your order #<?= htmlspecialchars($order['id']) ?> has been received and is being processed.</p>
        <p>Total: ₦<?= number_format($order['total'], 2) ?></p>
        <a href="shop.php" class="btn btn-purple btn-sm rounded-pill me-2"><i class="fas fa-store"></i> Continue Shopping</a>
        <a href="../backend/generate-invoice.php?id=<?= htmlspecialchars($order['id']) ?>" class="btn btn-outline-secondary btn-sm rounded-pill" target="_blank">
            <i class="fas fa-file-download"></i> Download Invoice
        </a>
    </div>

    <div class="order-details">
        <h4>Order Details</h4>
        <p><strong>Name:</strong> <?= htmlspecialchars($order['fullname']) ?><br>
           <strong>Email:</strong> <?= htmlspecialchars($order['email']) ?><br>
           <strong>Address:</strong> <?= nl2br(htmlspecialchars($order['address'])) ?><br>
           <strong>Phone:</strong> <?= htmlspecialchars($order['phone']) ?><br>
           <strong>Status:</strong> <?= ucfirst($order['status']) ?><br>
           <strong>Placed On:</strong> <?= date("F j, Y g:i A", strtotime($order['created_at'])) ?>
        </p>

        <h5>Items:</h5>
        <ul>
            <?php foreach ($items as $item): ?>
                <li><?= htmlspecialchars($item['name']) ?> x <?= $item['quantity'] ?? 1 ?> - ₦<?= htmlspecialchars($item['price']) ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
</div>

<footer>
  <p>&copy; <?= date('Y'); ?> RIBBOW. All rights reserved.</p>
  <div>
    <a href="https://www.instagram.com/_ribbow_" target="_blank"><i class="fab fa-instagram"></i></a>
    <a href="https://www.tiktok.com/@_ribbow_" target="_blank"><i class="fab fa-tiktok"></i></a>
    <a href="https://twitter.com/RabiuShuhaidah" target="_blank"><i class="fab fa-twitter"></i></a>
    <a href="https://wa.me/07017557260" target="_blank"><i class="fab fa-whatsapp"></i></a>
  </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
</body>
</html>
