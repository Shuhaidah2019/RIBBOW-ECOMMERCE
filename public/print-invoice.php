<?php
session_start();
header('Content-Type: text/html; charset=UTF-8');

if (!isset($_SESSION['user_id'])) {
    header("Location: auth.html");
    exit();
}

require_once '../backend/generate-invoice.php';
require_once '../config/db.php';

// Accept either ?id= or ?order_id=
$orderId = null;
if (isset($_GET['id'])) {
    $orderId = intval($_GET['id']);
} elseif (isset($_GET['order_id'])) {
    $orderId = intval($_GET['order_id']);
} else {
    die("❌ Missing order ID in URL.");
}

$userId = $_SESSION['user_id'];

$stmt = $conn->prepare("SELECT * FROM orders WHERE id = ? AND user_id = ?");
$stmt->bind_param("ii", $orderId, $userId);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die("❌ Order not found or you don’t have access to it.");
}

$order = $result->fetch_assoc();
$stmt->close();
$conn->close();

$items = json_decode($order['items'], true);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta charset="UTF-8" />
  <title>Invoice #<?= $orderId ?> | RIBBOW</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    @media (max-width: 768px) {
    body { font-family: 'Poppins', sans-serif; background: #fff; padding: 30px; }
    .invoice-box { max-width: 800px; margin: auto; border: 1px solid #eee; padding: 30px; box-shadow: 0 0 10px rgba(0,0,0,0.05); }
    .logo { width: 120px; }
    .text-purple { color: #800cec; }
    .print-btn { float: right; }
    ul { padding-left: 1.2rem; }
    @media print {
      .print-btn { display: none; }
    }
  }
  </style>
</head>
<body>
  <div class="invoice-box">
    <div class="d-flex justify-content-between align-items-center mb-4">
      <div>
        <img src="Images/logo.png" alt="RIBBOW Logo" class="logo">
        <h4 class="mt-2 mb-0 text-purple">RIBBOW</h4>
      </div>
      <button class="btn btn-sm btn-outline-dark print-btn" onclick="window.print()">🖨️ Print</button>
    </div>

    <hr>

    <div class="mb-4">
      <h5 class="text-purple">Order Summary</h5>
      <p>
        <strong>Order ID:</strong> <?= $orderId ?><br>
        <strong>Date:</strong> <?= date("F j, Y", strtotime($order['created_at'])) ?><br>
        <strong>Status:</strong> <?= ucfirst($order['status']) ?>
      </p>
    </div>

    <div class="mb-4">
      <h5 class="text-purple">Customer Information</h5>
      <p>
        <strong><?= htmlspecialchars($order['fullname']) ?></strong><br>
        <?= htmlspecialchars($order['phone']) ?><br>
        <?= nl2br(htmlspecialchars($order['address'])) ?>
      </p>
    </div>

    <div class="mb-4">
      <h5 class="text-purple">Items Ordered</h5>
      <ul>
        <?php foreach ($items as $item): ?>
          <li><?= htmlspecialchars($item['name']) ?> - ₦<?= number_format(floatval($item['price']), 2) ?></li>
        <?php endforeach; ?>
      </ul>
    </div>

    <h4 class="text-end text-purple">Total: ₦<?= number_format(floatval($order['total']), 2) ?></h4>

    <hr>

    <p class="text-muted text-center mt-4">
      Thank you for shopping with <span class="text-purple fw-bold">RIBBOW</span> 💜<br>
      <small>This invoice was generated on <?= date("F j, Y") ?></small>
    </p>
  </div>

<script>
  window.onload = () => {
    const btn = document.createElement('button');
    btn.textContent = 'Back to Orders';
    btn.className = 'btn btn-purple mt-3';
    btn.onclick = () => window.location.href = 'orders.php';
    document.body.appendChild(btn);
  };
</script>

<!-- Footer -->
<footer class="text-center py-4" style="background-color:#6a1b9a; color:white;">
  <p>&copy; <?php echo date('Y'); ?> RIBBOW. All rights reserved.</p>
  <div class="social-links">
    <a href="https://www.instagram.com/_ribbow_" target="_blank" class="text-white mx-2" aria-label="Instagram">
      <i class="fab fa-instagram"></i>
    </a>
    <a href="https://www.tiktok.com/@_ribbow_" target="_blank" class="text-white mx-2" aria-label="TikTok">
      <i class="fab fa-tiktok"></i>
    </a>
    <a href="https://twitter.com/RabiuShuhaidah" target="_blank" class="text-white mx-2" aria-label="Twitter">
      <i class="fab fa-twitter"></i>
    </a>
    <a href="https://wa.me/07017557260" target="_blank" class="text-white mx-2" aria-label="WhatsApp">
      <i class="fab fa-whatsapp"></i>
    </a>
  </div>
</footer>

</body>
</html>
