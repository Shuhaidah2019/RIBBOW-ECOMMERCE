<?php
require_once '../backend/helpers/csrf-helper.php';

session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: admin-login.php"); // redirect if not logged in
    exit();
}



require_once '../config/db.php'; // DB connection
require_once '../backend/send-email.php'; // if needed for order emails

$orders = []; // Initialize

// Base query
$sql = "SELECT * FROM orders WHERE 1";
$params = [];
$types = "";

// Filters
if (!empty($_GET['status'])) {
    $sql .= " AND status = ?";
    $types .= "s";
    $params[] = $_GET['status'];
}
if (!empty($_GET['start_date'])) {
    $sql .= " AND DATE(created_at) >= ?";
    $types .= "s";
    $params[] = $_GET['start_date'];
}
if (!empty($_GET['end_date'])) {
    $sql .= " AND DATE(created_at) <= ?";
    $types .= "s";
    $params[] = $_GET['end_date'];
}
if (!empty($_GET['min_price'])) {
    $sql .= " AND total >= ?";
    $types .= "d";
    $params[] = $_GET['min_price'];
}
if (!empty($_GET['max_price'])) {
    $sql .= " AND total <= ?";
    $types .= "d";
    $params[] = $_GET['max_price'];
}
if (!empty($_GET['search'])) {
    $sql .= " AND (fullname LIKE ? OR phone LIKE ?)";
    $types .= "ss";
    $searchTerm = "%" . $_GET['search'] . "%";
    $params[] = $searchTerm;
    $params[] = $searchTerm;
}

$sql .= " ORDER BY created_at DESC";

// Execute
$stmt = $conn->prepare($sql);
if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$result = $stmt->get_result();
$orders = $result->fetch_all(MYSQLI_ASSOC);
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta charset="UTF-8" />
  <title>Admin Dashboard | Orders</title>
  <link rel="stylesheet" href="../public/css/style.css">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
</head>
<body style="background-color: #fffafc; font-family: 'Poppins', sans-serif;">
<nav class="navbar navbar-light bg-light p-3">
  <div class="container-fluid d-flex justify-content-between align-items-center">
    <a class="navbar-brand fw-bold" href="#">RIBBOW Admin Panel</a>
    <div>
      <a href="../public/index.php" class="btn btn-outline-secondary me-2">Back to Site</a>
      <a href="logout.php" class="btn btn-danger">Logout</a>
    </div>
  </div>
</nav>


<div class="container py-5">
 <div class="d-flex justify-content-between align-items-center mb-4">
  <h2 class="mb-0">All Orders</h2>
  <div class="btn-group" role="group" aria-label="Product Management">
  <a href="products.php" class="btn btn-primary">📦 Manage Products</a>
  </div>
</div>

  <?php if (isset($_GET['message'])): ?>
  <div class="alert alert-info"><?= htmlspecialchars($_GET['message']) ?></div>
<?php endif; ?>

  <!-- Search + Filter Form -->
  <form method="GET" class="row g-3 mb-4">
    <input type="hidden" name="csrf_token" value="<?php echo generateCsrfToken(); ?>">

    <div class="col-md-3">
      <label>Search (name or phone)</label>
      <input type="text" name="search" class="form-control" value="<?= $_GET['search'] ?? '' ?>" placeholder="e.g. Khadijah or 0701..." />
    </div>
    <div class="col-md-2">
      <label>Start Date</label>
      <input type="date" name="start_date" class="form-control" value="<?= $_GET['start_date'] ?? '' ?>">
    </div>
    <div class="col-md-2">
      <label>End Date</label>
      <input type="date" name="end_date" class="form-control" value="<?= $_GET['end_date'] ?? '' ?>">
    </div>
    <div class="col-md-1">
      <label>Min ₦</label>
      <input type="number" name="min_price" class="form-control" value="<?= $_GET['min_price'] ?? '' ?>">
    </div>
    <div class="col-md-1">
      <label>Max ₦</label>
      <input type="number" name="max_price" class="form-control" value="<?= $_GET['max_price'] ?? '' ?>">
    </div>
    <div class="col-md-1 d-flex align-items-end">
      <button type="submit" class="btn btn-purple w-100">Filter</button>
    </div>
  <div class="col-md-2 d-flex align-items-end">
  <a href="orders.php?status=Cancelled" class="btn btn-danger w-100 me-2">Show Cancelled</a>
</div>
<div class="col-md-2 d-flex align-items-end">
  <a href="orders.php" class="btn btn-secondary w-100">Show All</a>
</div>
</form>
  <!-- Orders Table -->
  <?php if (count($orders) === 0): ?>
    <p>No orders found.</p>
  <?php else: ?>
  <div class="table-responsive">
  <table class="table table-bordered table-hover">
  <thead class="table-light">        
  <tr>
    <th>#</th>
    <th>Name</th>
    <th>Phone</th>
    <th>Address</th>
    <th><strong>Items</strong></th> 
    <th>Total</th>
    <th>Status</th>
    <th>Placed On</th>
    <th>Actions</th>
  </tr>
</thead>

<tbody>
  <?php foreach ($orders as $order): ?>
    <?php
      // Row color based on status
      $rowClass = '';
      switch($order['status']) {
          case 'Delivered': $rowClass = 'table-success'; break;
          case 'Cancelled': $rowClass = 'table-danger'; break;
          default: $rowClass = ''; break;
      }

      // Badge class
      $statusClass = '';
      switch($order['status']) {
          case 'Pending': $statusClass = 'badge bg-warning'; break;
          case 'Shipped': $statusClass = 'badge bg-info'; break;
          case 'Delivered': $statusClass = 'badge bg-success'; break;
          case 'Cancelled': $statusClass = 'badge bg-danger'; break;
      }
    ?>
 <tr class="<?= $rowClass ?>">
  <td><?= $order['id'] ?></td>
  <td><?= htmlspecialchars($order['fullname']) ?></td>
  <td><?= htmlspecialchars($order['phone']) ?></td>
  <td><?= nl2br(htmlspecialchars($order['address'])) ?></td>
  <td>
    <?php
      $items = json_decode($order['items'], true);
      if (is_array($items)) {
        $names = array_map(fn($item) => htmlspecialchars($item['name']), $items);
        echo implode(', ', $names);
      } else {
        echo 'N/A';
      }
    ?>
  </td>
  <td>₦<?= number_format($order['total'], 2) ?></td>
    
  <td>
  <select class="order-status form-select" data-order-id="<?= $order['id'] ?>">
    <option value="Pending"   <?= $order['status']=='Pending' ? 'selected' : '' ?>>Pending</option>
    <option value="Shipped"   <?= $order['status']=='Shipped' ? 'selected' : '' ?>>Shipped</option>
    <option value="Delivered" <?= $order['status']=='Delivered' ? 'selected' : '' ?>>Delivered</option>
    <option value="Cancelled" <?= $order['status']=='Cancelled' ? 'selected' : '' ?>>Cancelled</option>
  </select>
</td>


<!-- ✅ Placed On -->
<td><?= date("M d, Y h:i A", strtotime($order['created_at'])); ?></td>

<!-- ✅ Actions -->
<td>
  <!-- View Button -->
  <a href="order-details.php?id=<?= $order['id'] ?>" 
     class="btn btn-sm btn-info">
     View
  </a>

  <!-- Delete Button -->
  <a href="delete-order.php?id=<?= $order['id'] ?>" 
     class="btn btn-sm btn-danger"
     onclick="return confirm('Are you sure you want to delete this order?');">
     Delete
  </a>
</td>
</tr>
  <?php endforeach; ?>
</tbody>


      </table>
    </div>
  <?php endif; ?>
</div>

<script>
  function confirmDelete(orderId) {
    if (confirm("Are you sure you want to delete this order?")) {
      window.location.href = 'delete-order.php?id=' + orderId;
    }
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
<script>
document.querySelectorAll('.order-status').forEach(select => {
    select.addEventListener('change', function() {
        const orderId = this.dataset.orderId;
        const status = this.value;

        fetch('update-status.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: `order_id=${orderId}&status=${status}`
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // ✅ Update row color
                const row = select.closest('tr');
                row.classList.remove('table-success', 'table-danger', 'table-info', 'table-warning');

                switch(status) {
                    case 'Delivered':
                        row.classList.add('table-success');
                        break;
                    case 'Cancelled':
                        row.classList.add('table-danger');
                        break;
                    case 'Shipped':
                        row.classList.add('table-info');
                        break;
                    case 'Pending':
                        row.classList.add('table-warning');
                        break;
                }

                // ✅ Optional: change badge inside the select for quick visual
                select.classList.remove('bg-success','bg-danger','bg-info','bg-warning');
                switch(status) {
                    case 'Delivered': select.classList.add('bg-success','text-white'); break;
                    case 'Cancelled': select.classList.add('bg-danger','text-white'); break;
                    case 'Shipped': select.classList.add('bg-info','text-white'); break;
                    case 'Pending': select.classList.add('bg-warning','text-dark'); break;
                }

                alert('✅ Order status updated successfully!');
            } else {
                alert('⚠️ ' + data.message);
            }
        })
        .catch(err => {
            alert('❌ Failed to update order status.');
            console.error(err);
        });
    });
});
</script>


</body>
</html>
