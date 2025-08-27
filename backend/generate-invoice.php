<?php
header('Content-Type: text/html; charset=UTF-8');
session_start();

require_once '../config/db.php';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("Invalid order ID.");
}

$order_id = intval($_GET['id']);

$stmt = $conn->prepare("SELECT * FROM orders WHERE id = ?");
$stmt->bind_param("i", $order_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die("Order not found.");
}

$order = $result->fetch_assoc();

$conn->close();

function naira($amount) {
    return "₦" . number_format($amount, 2);
}

$items = json_decode($order['items'], true);
if (!is_array($items)) {
    $items = [];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta charset="UTF-8" />
  <title>Invoice #<?= htmlspecialchars($order['id']) ?> | RIBBOW</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
  body {
    font-family: 'Poppins', sans-serif;
    background: #fffafc;
    color: #3a3a3a;
    padding: 20px;
    min-height: 100vh;
}

/* Invoice container */
.invoice-container {
    max-width: 800px;
    margin: auto;
    background: #fff;
    padding: 30px;
    box-shadow: 0 8px 25px rgba(0,0,0,0.1);
    border-radius: 15px;
    border: 1px solid #eee;
    transition: transform 0.2s ease;
}
.invoice-container:hover {
    transform: translateY(-2px);
}

/* Header */
.header-section {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 30px;
}
.logo { width: 130px; }

/* Main title */
h1 {
    color: #6a1b9a;
    font-weight: 700;
    margin-bottom: 30px;
    text-align: center;
}

/* Section titles */
h2.section-title {
    color: #6a1b9a;
    font-weight: 600;
    margin-bottom: 15px;
    border-bottom: 2px solid #6a1b9a;
    padding-bottom: 5px;
}

/* Info sections */
.customer-info p,
.order-info p {
    margin-bottom: 8px;
    font-size: 15px;
}

/* Items list */
ul.items-list {
    padding-left: 0;
    list-style: none;
    margin-bottom: 25px;
}
ul.items-list li {
    margin-bottom: 10px;
    font-size: 16px;
    padding: 10px;
    border-radius: 10px;
    background-color: #f8f0fa;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

/* Payment proof image */
img {
    max-width: 250px;
    border: 1px solid #ccc;
    border-radius: 8px;
    margin-top: 10px;
}

/* Total */
.total-amount {
    font-size: 24px;
    font-weight: 700;
    color: #6a1b9a;
    text-align: right;
    margin-top: 30px;
    margin-bottom: 15px;
}

/* Print button */
.print-btn {
    background-color: #6a1b9a;
    color: white;
    border: none;
    padding: 12px 25px;
    border-radius: 50px;
    font-weight: 600;
    cursor: pointer;
    transition: background-color 0.3s ease, transform 0.2s ease;
}
.print-btn:hover {
    background-color: #580a9c;
    transform: translateY(-2px);
}

/* Footer note */
.footer-note {
    text-align: center;
    font-style: italic;
    color: #888;
    margin-top: 40px;
    font-size: 14px;
}

/* Responsive tweaks */
@media (max-width: 768px) {
    body { padding: 15px; }
    .invoice-container { padding: 20px; }
    .print-btn { width: 100%; margin-bottom: 15px; }
}

@media print {
    .print-btn { display: none; }
}


  </style>
</head>
<body>

  <div class="invoice-container">
    <div class="header-section">
      <div>
        <img src="/RIBBOW/public/Images/ribbow.png" alt="RIBBOW Logo" class="logo" />
      </div>
      <button class="print-btn" onclick="window.print()">🖨️ Print Invoice</button>
    </div>

    <h1>Invoice #<?= htmlspecialchars($order['id']) ?></h1>

    <section class="order-info mb-4">
      <h2 class="section-title">Order Details</h2>
      <p><strong>Date:</strong> <?= date("F j, Y, g:i A", strtotime($order['created_at'])) ?></p>
      <p><strong>Status:</strong> <?= ucfirst(htmlspecialchars($order['status'])) ?></p>
    </section>

    <section class="customer-info mb-4">
      <h2 class="section-title">Customer Information</h2>
      <p><strong>Name:</strong> <?= htmlspecialchars($order['fullname']) ?></p>
      <p><strong>Phone:</strong> <?= htmlspecialchars($order['phone']) ?></p>
      <p><strong>Address:</strong><br><?= nl2br(htmlspecialchars($order['address'])) ?></p>
    </section>

    <section class="items-ordered mb-4">
      <h2 class="section-title">Items Ordered</h2>
      <ul class="items-list">
        <?php foreach ($items as $item): ?>
          <li><?= htmlspecialchars($item['name']) ?> — <strong><?= naira($item['price']) ?></strong></li>
        <?php endforeach; ?>
      </ul>
    </section>

   <?php if(!empty($order['proof'])): ?>
  <h2 class="section-title">Payment Proof</h2>
  <?php 
      $proofPath = htmlspecialchars($order['proof']);
      $proofExt = pathinfo($proofPath, PATHINFO_EXTENSION);
  ?>
  <?php if(strtolower($proofExt) === 'pdf'): ?>
    <a href="<?= $proofPath ?>" target="_blank" class="btn btn-purple btn-sm">View Payment Proof (PDF)</a>
  <?php else: ?>
    <img src="<?= $proofPath ?>" alt="Payment Proof" style="max-width: 300px; border:1px solid #ccc; border-radius:8px;">
  <?php endif; ?>
<?php endif; ?>

    <div class="total-amount">Total: <?= naira($order['total']) ?></div>

    <div class="footer-note">
      Thank you for shopping with <strong>RIBBOW</strong> 💜<br />
      <small>Invoice generated on <?= date("F j, Y") ?></small>
    </div>
  </div>

</body>
</html>
