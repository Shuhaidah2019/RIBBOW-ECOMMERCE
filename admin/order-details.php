<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit;
}

require_once '../config/db.php';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("Invalid order ID");
}

$orderId = intval($_GET['id']);

$stmt = $conn->prepare("SELECT * FROM orders WHERE id = ?");
$stmt->bind_param("i", $orderId);
$stmt->execute();
$result = $stmt->get_result();
$order = $result->fetch_assoc();
$stmt->close();

if (!$order) {
    die("Order not found");
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta charset="UTF-8">
<title>Order #<?= $order['id'] ?> Details | Admin</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="../public/css/style.css">
<style>
  .status-Pending { border-left: 5px solid #ffc107; }       /* Yellow */
  .status-Shipped { border-left: 5px solid #17a2b8; }      /* Blue */
  .status-Delivered { border-left: 5px solid #28a745; }    /* Green */
  .status-Cancelled { border-left: 5px solid #dc3545; }    /* Red */
</style>
</head>
<body class="bg-light">

<div class="container py-5">
  <h2>Order #<?= $order['id'] ?> Details</h2>
  <a href="orders.php" class="btn btn-secondary mb-3">Back to Orders</a>

  <div id="orderCard" class="card mb-3 status-<?= $order['status'] ?>">
    <div class="card-body">
      <h5 class="card-title">Customer Info</h5>
      <p><strong>Name:</strong> <?= htmlspecialchars($order['fullname']) ?></p>
      <p><strong>Email:</strong> <?= htmlspecialchars($order['email']) ?></p>
      <p><strong>Phone:</strong> <?= htmlspecialchars($order['phone']) ?></p>
      <p><strong>Address:</strong> <?= nl2br(htmlspecialchars($order['address'])) ?></p>
    </div>
  </div>

  <div class="card mb-3 status-<?= $order['status'] ?>">
    <div class="card-body">
      <h5 class="card-title">Order Items</h5>
      <ul>
        <?php
        $items = json_decode($order['items'], true);
        if (is_array($items)) {
            foreach ($items as $item) {
                echo "<li>" . htmlspecialchars($item['name']) . " - ₦" . number_format($item['price'], 2) . " x " . $item['quantity'] . "</li>";
            }
        } else {
            echo "<li>No items found</li>";
        }
        ?>
      </ul>
      <p><strong>Total:</strong> ₦<?= number_format($order['total'], 2) ?></p>
      <label for="status">Order Status:</label>
      <select id="status" class="form-select w-25 mb-3" data-order-id="<?= $order['id'] ?>">
          <option value="Pending" <?= $order['status']=='Pending' ? 'selected' : '' ?>>Pending</option>
          <option value="Shipped" <?= $order['status']=='Shipped' ? 'selected' : '' ?>>Shipped</option>
          <option value="Delivered" <?= $order['status']=='Delivered' ? 'selected' : '' ?>>Delivered</option>
          <option value="Cancelled" <?= $order['status']=='Cancelled' ? 'selected' : '' ?>>Cancelled</option>
      </select>
       <div class="mb-3">
    <button id="sendEmailBtn" class="btn btn-primary" data-order-id="<?= $order['id'] ?>">
        📧 Send Email Now
    </button>
    <span id="emailStatusBadge" class="badge bg-secondary ms-2">Not Sent</span>
</div>


      <p><strong>Placed On:</strong> <?= date("M d, Y h:i A", strtotime($order['created_at'])) ?></p>
    </div>
  </div>
</div>

<script>
const statusSelect = document.getElementById('status');
const orderCardElements = document.querySelectorAll('#orderCard, .card.status-<?= $order['status'] ?>');

statusSelect.addEventListener('change', function() {
    const orderId = this.dataset.orderId;
    const status = this.value;

    fetch('update-status.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: `order_id=${orderId}&status=${status}`
    })
    .then(response => response.json())
    .then(data => {
        if(data.success){
            alert('✅ Order status updated!');

            // Update border color
            orderCardElements.forEach(el => {
                el.classList.remove('status-Pending','status-Shipped','status-Delivered','status-Cancelled');
                el.classList.add(`status-${status}`);
            });

        } else {
            alert('⚠️ ' + data.message);
        }
    })
    .catch(err => {
        alert('❌ Failed to update status.');
        console.error(err);
    });
});


//send email
const sendEmailBtn = document.getElementById('sendEmailBtn');
const emailBadge = document.getElementById('emailStatusBadge');

sendEmailBtn.addEventListener('click', function() {
    const orderId = this.dataset.orderId;

    fetch('send-status-email.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: `order_id=${orderId}`
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('✅ Email sent successfully!');
            emailBadge.textContent = 'Sent';
            emailBadge.classList.remove('bg-secondary', 'bg-danger');
            emailBadge.classList.add('bg-success');
        } else {
            alert('⚠️ ' + data.message);
            emailBadge.textContent = 'Failed';
            emailBadge.classList.remove('bg-secondary', 'bg-success');
            emailBadge.classList.add('bg-danger');
        }
    })
    .catch(err => {
        alert('❌ Failed to send email.');
        emailBadge.textContent = 'Failed';
        emailBadge.classList.remove('bg-secondary', 'bg-success');
        emailBadge.classList.add('bg-danger');
        console.error(err);
    });
});
</script>

</body>
</html>
