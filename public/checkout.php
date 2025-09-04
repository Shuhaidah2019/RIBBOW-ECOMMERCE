<?php

// Enable full error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();

require_once '../config/db.php';
require_once '../backend/helpers/email-helper.php';
require_once '../backend/helpers/admin-notify.php'; 

if (!isset($_SESSION['user_id'])) {
    header("Location: auth.php");
    exit();
}


// (Your PHP checkout logic remains EXACTLY the same — no tampering)

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Grab and sanitize inputs
    $fullname = trim($_POST['fullname'] ?? '');
    $email    = trim($_POST['email'] ?? '');
    $address  = trim($_POST['address'] ?? '');
    $phone    = trim($_POST['phone'] ?? '');
    $cartItemsJson = $_POST['cart_items'] ?? '[]';
    $cartItems = json_decode($cartItemsJson, true);

    // Basic validation
    if (empty($fullname) || empty($email) || empty($address) || empty($phone) || empty($cartItems)) {
        $error = "All fields and cart items are required.";
    } else {
        // Validate and upload proof if present
        $proofPath = null;
        if (isset($_FILES['proof']) && $_FILES['proof']['error'] === UPLOAD_ERR_OK) {
            $allowedTypes = ['image/jpeg', 'image/png', 'application/pdf'];
            $fileType = $_FILES['proof']['type'];

            if (!in_array($fileType, $allowedTypes)) {
                $error = "Invalid file type. Only JPG, PNG, and PDF are allowed.";
            } else {
                $uploadDir = '../uploads/payment_proofs/';
                if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);

                $filename = time() . '_' . basename($_FILES['proof']['name']);
                $targetFile = $uploadDir . $filename;

                if (move_uploaded_file($_FILES['proof']['tmp_name'], $targetFile)) {
                    $proofPath = $targetFile;
                }
            }
        }

        // Only insert order if no errors so far
        if (empty($error)) {
            $total = 0;
            foreach ($cartItems as $item) {
                $total += ((float)$item['price']) * ((int)$item['quantity']);
            }

            $trackingId = strtoupper(substr(md5(uniqid(rand(), true)), 0, 10));
            $stmt = $conn->prepare("INSERT INTO orders (user_id, fullname, email, address, phone, items, total, proof, tracking_id) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $itemsJson = json_encode($cartItems);
            $stmt->bind_param(
                "isssssiss",
                $_SESSION['user_id'],
                $fullname,
                $email,
                $address,
                $phone,
                $itemsJson,
                $total,
                $proofPath,
                $trackingId
            );

            if ($stmt->execute()) {
                $newOrderId = $stmt->insert_id;

                // ----- EMAIL LOGIC ADDED HERE -----

                $orderData = [
                    'id'       => $newOrderId,
                    'fullname' => $fullname,
                    'email'    => $email,
                    'total'    => $total,
                    'items'    => $cartItems,
                    'proof'    => $proofPath ?? null,
                ];

                // Send email to customer
                $customerSubject = "Your RIBBOW Order #{$newOrderId} Confirmation";
                $customerBody = "
                    <p>Hi {$fullname},</p>
                    <p>Thank you for shopping with <strong>RIBBOW</strong>! Your order has been received and is being processed.</p>
                    <p><strong>Order ID:</strong> {$newOrderId}<br>
                       <strong>Total:</strong> ₦" . number_format($total, 2) . "</p>
                    <p><strong>Items Ordered:</strong><br>";

                foreach ($cartItems as $item) {
                    $qty = isset($item['quantity']) ? (int)$item['quantity'] : 1;
                    $customerBody .= "- {$item['name']} x {$qty} (₦" . number_format($item['price'], 2) . ")<br>";
                }

                if (!empty($proofPath)) {
                    $proofLink = htmlspecialchars($_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . '/' . ltrim($proofPath, '../'));
                    $customerBody .= "<br><strong>Payment Proof Uploaded:</strong> <a href='{$proofLink}' target='_blank'>View Proof</a>";
                } else {
                    $customerBody .= "<br><strong>Payment Proof:</strong> Not uploaded yet.";
                }

                $customerBody .= "</p><p>💜 RIBBOW</p>";

                sendEmail($email, $fullname, $customerSubject, $customerBody, true);

                // Send email to admin
                notifyAdminNewOrder($orderData);

                header("Location: order-success.php?order_id=" . $newOrderId);
                exit();
            } else {
                $error = "Failed to place order. Please try again.";
            }
        }
    }
}


// IMPORTANT: Do NOT have any insert/redirect logic outside this POST block

?>


<!DOCTYPE html>
<html lang="en">
<head>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta charset="UTF-8">
<title>Checkout - RIBBOW</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"/>
<style>
/* --- RIBBOW THEME STYLING --- */

/* Navbar */
.navbar {
  background-color: #6a1b9a !important;
}
.navbar .navbar-brand {
  font-weight: bold;
  color: #fff !important;
  letter-spacing: 1px;
}
.navbar .nav-link {
  color: #fff !important;
  transition: color 0.3s ease;
}
.navbar .nav-link:hover,
.navbar .nav-link.active {
  color: #dfc1ddff !important;
}
.navbar-toggler {
  border-color: #fff !important;
}
.navbar-toggler-icon {
  filter: invert(1);
}
/* Dropdown */
.dropdown-menu {
  background-color: #6a1b9a !important;
  border: none;
  border-radius: 10px;
}
.dropdown-menu .dropdown-item {
  color: #fff !important;
}
.dropdown-menu .dropdown-item:hover {
  background-color: #4b275f !important;
  color: #fff !important;
}

/* Buttons */
.btn-purple { background-color: #6a1b9a; color: white; }
.btn-purple:hover { background-color: #580a9c; color: white; }

/* Error message */
.error-message { 
  background-color: #ffe5e5; 
  border: 1px solid #ffb3b3; 
  color: #cc0000; 
  padding: 10px; 
  border-radius: 5px; 
  margin-bottom: 15px; 
}

/* Footer */
footer {
  background-color:#6a1b9a; 
  color:white;
  text-align:center; 
  padding: 1rem 0;
}
footer a {
  color:white;
  margin: 0 8px;
  font-size: 1.2rem;
}
footer a:hover {
  color:#dfc1ddff;
}
</style>
</head>
<body style="background-color: #fffafc; font-family: 'Poppins', sans-serif; min-height:100vh; display:flex; flex-direction: column;">

<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-light">
  <div class="container-fluid">
     <a class="navbar-brand fw-bold" href="index.php">
      <i class="fas fa-ribbon me-2"></i> RIBBOW
    </a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNavDropdown">
      <span class="navbar-toggler-icon"></span>
    </button>
    
    <div class="collapse navbar-collapse" id="navbarNavDropdown">
      <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
        <li class="nav-item"><a class="nav-link" href="index.php">Home</a></li>
        <li class="nav-item"><a class="nav-link active" href="cart.php">Cart</a></li>
        <li class="nav-item"><a class="nav-link" href="logout.php">Logout</a></li>
      </ul>
    </div>
  </div>
</nav>

<div class="container py-4 flex-grow-1">
    <h2>Complete Your Payment</h2>
    <?php if (!empty($error)): ?>
        <div class="error-message"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>
    <table class="table table-bordered mt-3">
        <thead>
            <tr><th>Product</th><th>Price (₦)</th><th>Quantity</th><th>Total (₦)</th></tr>
        </thead>
        <tbody id="cartItemsBody"></tbody>
    </table>
    <p class="fw-bold">Grand Total: ₦<span id="grandTotal">0</span></p>

    <div class="bank-details my-4">
        <h4>Bank Transfer Information</h4>
        <p><strong>Bank Name:</strong> GUARANTEE TRUST BANK (GTB)</p>
        <p><strong>Account Name:</strong> SHUHAIDAH RABIU SANUSI</p>
        <p><strong>Account Number:</strong> 0560866879</p>
        <p><em>After payment, upload your proof below.</em></p>
    </div>

    <form method="POST" enctype="multipart/form-data" id="checkoutForm" novalidate>
        <label for="fullname">Full Name:</label>
        <input type="text" name="fullname" id="fullname" class="form-control" value="<?= htmlspecialchars($fullname ?? '') ?>">

        <label for="email" class="mt-2">Email Address:</label>
        <input type="email" name="email" id="email" class="form-control" value="<?= htmlspecialchars($email ?? '') ?>">

        <label for="address" class="mt-2">Address:</label>
        <textarea name="address" id="address" rows="3" class="form-control"><?= htmlspecialchars($address ?? '') ?></textarea>

        <label for="phone" class="mt-2">Phone Number:</label>
        <input type="text" name="phone" id="phone" class="form-control" value="<?= htmlspecialchars($phone ?? '') ?>">

        <label for="proof" class="mt-2">Upload Payment Proof (jpg, png, pdf):</label>
        <input type="file" name="proof" id="proof" class="form-control" accept=".jpg,.jpeg,.png,.pdf">

        <input type="hidden" name="cart_items" id="cart_items">

        <button type="submit" class="btn btn-purple mt-3">Submit Payment</button>
    </form>
</div>

<!-- Footer -->
<footer>
  <p>&copy; <?= date('Y'); ?> RIBBOW. All rights reserved.</p>
  <div>
    <a href="https://www.instagram.com/_ribbow_" target="_blank"><i class="fab fa-instagram"></i></a>
    <a href="https://www.tiktok.com/@_ribbow_" target="_blank"><i class="fab fa-tiktok"></i></a>
    <a href="https://twitter.com/RabiuShuhaidah" target="_blank"><i class="fab fa-twitter"></i></a>
    <a href="https://wa.me/07017557260" target="_blank"><i class="fab fa-whatsapp"></i></a>
  </div>
</footer>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const userId = "<?= $_SESSION['user_id'] ?>";
    const cartKey = `cart_${userId}`;
    const cartJson = localStorage.getItem(cartKey) || '[]';
    const cart = JSON.parse(cartJson);

    const tbody = document.getElementById('cartItemsBody');
    let grandTotal = 0;

    if (cart.length === 0) {
        tbody.innerHTML = '<tr><td colspan="4" class="text-center">Your cart is empty.</td></tr>';
        return;
    }

    cart.forEach(item => {
        const qty = item.quantity || 1;
        const price = parseFloat(item.price);
        const total = price * qty;
        grandTotal += total;

        const tr = document.createElement('tr');
        tr.innerHTML = `
            <td>${item.name}</td>
            <td>₦${price.toLocaleString(undefined, {minimumFractionDigits: 2})}</td>
            <td>${qty}</td>
            <td>₦${total.toLocaleString(undefined, {minimumFractionDigits: 2})}</td>
        `;
        tbody.appendChild(tr);
    });

    document.getElementById('grandTotal').textContent = grandTotal.toLocaleString(undefined, {minimumFractionDigits: 2});
    
    document.getElementById('cart_items').value = cartJson;
});
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
