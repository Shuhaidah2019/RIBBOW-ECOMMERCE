<?php
session_start();
require_once '../config/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: auth.php");
    exit();
}

$userId = $_SESSION['user_id'];
$productId = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($productId <= 0) {
    header("Location: shop.php");
    exit();
}

// Fetch product from DB
$stmt = $conn->prepare("SELECT id, name, price, image FROM products WHERE id = ?");
$stmt->bind_param("i", $productId);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    header("Location: shop.php");
    exit();
}

$product = $result->fetch_assoc();

function escapeForJs($str) {
    return htmlspecialchars($str, ENT_QUOTES, 'UTF-8');
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Adding to Cart...</title>
</head>
<body>
<script>
// Add to cart using localStorage (per user)
const userId = "<?= escapeForJs($userId) ?>";
const cartKey = `cart_${userId}`;
let cart = JSON.parse(localStorage.getItem(cartKey)) || [];

const productId = "<?= escapeForJs($product['id']) ?>";
const productName = "<?= escapeForJs($product['name']) ?>";
const productPrice = parseFloat("<?= $product['price'] ?>");
const productImage = "Images/<?= escapeForJs($product['image']) ?>";

// Check if already exists in cart
const existingIndex = cart.findIndex(item => item.id == productId);
if (existingIndex > -1) {
    cart[existingIndex].quantity += 1;
} else {
    cart.push({ id: productId, name: productName, price: productPrice, image: productImage, quantity: 1 });
}

localStorage.setItem(cartKey, JSON.stringify(cart));
window.location.href = "cart.php"; // Redirect to cart page
</script>
</body>
</html>
