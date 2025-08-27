<?php
session_start();
if (!isset($_SESSION['user_id'])) {
  header("Location: auth.html");
  exit();
}

require_once '../config/db.php';

$userId = $_SESSION['user_id'];
$orders = [];

// Fetch orders for this user
$sql = "SELECT * FROM orders WHERE user_id = ? ORDER BY created_at DESC";
if ($stmt = $conn->prepare($sql)) {
  $stmt->bind_param("i", $userId);
  $stmt->execute();
  $result = $stmt->get_result();
  $orders = $result->fetch_all(MYSQLI_ASSOC);
  $stmt->close();
}
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta charset="UTF-8" />
  <title>My Orders | RIBBOW</title>
  <link rel="stylesheet" href="css/style.css">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <style>
    @media (max-width: 768px) {
    .alert-lilac {
      background-color: #f5e6ff;
      border: 1px solid #d8b4fe;
      color: #800cec;
      border-radius: 15px;
    }
    .btn-purple {
      background-color: #800cec;
      color: white;
    }
    .btn-purple:hover {
      background-color: #6e0ab8;
      color: white;
    }
  }
  </style>
</head>
<body style="background-color: #fffafc; font-family: 'Poppins', sans-serif;">

<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-light sticky-top shadow-sm" style="background-color: #fffafc;">
  <div class="container">
    <a class="navbar-brand fw-bold" href="index.php" style="color: #800cec;">RIBBOW</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
      <ul class="navbar-nav align-items-center">
        <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle text-purple" href="#" role="button" data-bs-toggle="dropdown">Menu</a>
          <ul class="dropdown-menu">
            <li><a class="dropdown-item" href="index.php">Home</a></li>
            <li><a class="dropdown-item" href="shop.php">Shop</a></li>
            <li><a class="dropdown-item" href="cart.php">Cart</a></li>
            <li><a class="dropdown-item" href="checkout.php">Checkout</a></li>
            <li><a class="dropdown-item fw-bold active text-purple" href="orders.php">My Orders</a></li>
            <li><a class="dropdown-item text-danger" href="#" onclick="logoutUser()">Logout</a></li>
          </ul>
        </li>
      </ul>
    </div>
  </div>
</nav>

<div class="container py-5">

  <?php if (!empty($orders)): ?>
    <!-- Thank you banner for the latest order -->
    <div class="alert alert-lilac text-center shadow-sm">
      <h4 class="fw-bold">🎉 Thank you for your order!</h4>
      <p class="mb-3">We’ve received your order and will start processing it shortly.</p>
      <a href="shop.php" class="btn btn-purple btn-sm rounded-pill">
        <i class="fas fa-store"></i> Continue Shopping
      </a>
      <a href="../backend/generate-invoice.php?id=<?= htmlspecialchars($orders[0]['id']) ?>" 
         class="btn btn-outline-secondary btn-sm rounded-pill" target="_blank">
         <i class="fas fa-file-download"></i> Download Invoice
      </a>
    </div>
  <?php endif; ?>

  <h2 class="text-center mb-4">My Orders</h2>

  <?php if (empty($orders)): ?>
    <p class="text-center">🛍️ You haven’t placed any orders yet.</p>
  <?php else: ?>
    <?php foreach ($orders as $index => $order): ?>
      <?php
        $status = ucfirst($order['status']);
        $badgeClass = match (strtolower($status)) {
          'pending' => 'bg-warning text-dark',
          'shipped' => 'bg-info',
          'delivered' => 'bg-success',
          'cancelled' => 'bg-danger',
          default => 'bg-secondary',
        };

        // Highlight the latest order
        $highlightStyle = ($index === 0) ? "border: 2px solid #800cec;" : "";
      ?>
      <div class="card mb-4 shadow-sm border-0" style="<?= $highlightStyle ?>">
        <div class="card-body">
          <div class="d-flex justify-content-between align-items-center">
            <h5 class="card-title mb-0">Order #<?= htmlspecialchars($order['id']) ?> | ₦<?= htmlspecialchars($order['total']) ?></h5>
            <span class="badge <?= $badgeClass ?>"><?= $status ?></span>
          </div>
          <p class="mt-2 mb-1">
            <strong>Date:</strong> <?= date("F j, Y g:i A", strtotime($order['created_at'])) ?><br>
            <strong>Name:</strong> <?= htmlspecialchars($order['fullname']) ?><br>
            <strong>Address:</strong> <?= nl2br(htmlspecialchars($order['address'])) ?><br>
            <strong>Phone:</strong> <?= htmlspecialchars($order['phone']) ?>
          </p>
          <h6 class="mt-3">Items:</h6>
          <ul class="mb-3">
            <?php foreach (json_decode($order['items'], true) as $item): ?>
              <li><?= htmlspecialchars($item['name']) ?> - ₦<?= htmlspecialchars($item['price']) ?></li>
            <?php endforeach; ?>
          </ul>
          <?php if ($index !== 0): // For past orders ?>
            <a href="#" onclick="reorderCart(<?= $order['id'] ?>)" class="btn btn-purple btn-sm rounded-pill">
              <i class="fas fa-redo-alt"></i> Reorder
            </a>
            <a href="../backend/generate-invoice.php?id=<?= htmlspecialchars($order['id']) ?>" class="btn btn-purple btn-sm rounded-pill" target="_blank">
              <i class="fas fa-file-download"></i> Invoice
            </a>
          <?php endif; ?>
        </div>
      </div>
    <?php endforeach; ?>
  <?php endif; ?>
</div>

<!-- Scripts -->
<script>
  function logoutUser() {
    const userId = "<?= $userId ?>";
    localStorage.removeItem(`cart_${userId}`);
    localStorage.removeItem("loggedInUser");
    window.location.href = "auth.html";
  }

  const userId = "<?php echo $_SESSION['user_id']; ?>";
  function reorderCart(orderId) {
    fetch(`../backend/get-order-items.php?id=${orderId}`)
      .then(res => res.json())
      .then(data => {
        if (data.success && Array.isArray(data.items)) {
          localStorage.setItem(`cart_${userId}`, JSON.stringify(data.items));
          alert("🛒 Items added back to cart!");
          window.location.href = "cart.php";
        } else {
          alert("Could not reorder: " + (data.message || "Unknown error"));
        }
      })
      .catch(err => {
        console.error("Reorder error:", err);
        alert("Something went wrong while reordering.");
      });
  }
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

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="js/script.js"></script>
</body>
</html>