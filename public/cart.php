<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: auth.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta charset="UTF-8" />
  <title>Your Cart | RIBBOW</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css"/>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <link rel="stylesheet" href="css/style.css">
  <style>
    @media (max-width: 768px) {
    .btn-purple {
      background-color: #6a1b9a;
      color: white;
    }
    .btn-purple:hover {
      background-color: #770bdc;
      color: white;
    }
  }
  .cart-item img {
    width: 120px; /* or any fixed width */
    height: auto; /* keeps the ratio */
    object-fit: cover; /* trims weird sizes */
    border-radius: 10px;
}

  </style>
</head>
<body style="background: linear-gradient(to bottom, #ae98d2ff, #e2d6e1ff); font-family: 'Poppins', sans-serif; min-height:100vh; display:flex; flex-direction: column; padding-top:70px;">

<nav class="navbar navbar-expand-lg navbar-light shadow-sm" style="background-color: #6a1b9a; position: fixed; top:0; left:0; width:100%; z-index:1050;">
  <div class="container">
    <a class="navbar-brand fw-bold" href="index.php" style="color: #decee8ff; font-size: 1.5rem; letter-spacing: 1px; text-transform: uppercase;">
      <i class="fas fa-ribbon me-2"></i> RIBBOW
    </a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNavDropdown" 
            aria-controls="navbarNavDropdown" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse justify-content-end" id="navbarNavDropdown">
      <ul class="navbar-nav">
        <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle text-white fw-bold" href="#" id="navbarDropdownMenuLink" role="button" 
             data-bs-toggle="dropdown" aria-expanded="false">
            Menu
          </a>
          <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdownMenuLink">
            <li><a class="dropdown-item" href="shop.php" style="color: #dfc1ddff;"
                   onmouseover="this.style.backgroundColor='#770bdcff'; this.style.color='#fff';" 
                   onmouseout="this.style.backgroundColor=''; this.style.color='#dfc1ddff';">
                   <i class="fas fa-shopping-bag me-2"></i> Shop
               </a></li>
            <li><a class="dropdown-item" href="cart.php" style="color: #dfc1ddff;"
                   onmouseover="this.style.backgroundColor='#770bdcff'; this.style.color='#fff';" 
                   onmouseout="this.style.backgroundColor=''; this.style.color='#dfc1ddff';">
                   <i class="fas fa-shopping-cart me-2"></i> Cart
               </a></li>
           <li><a class="dropdown-item text-danger" href="#" onclick="logoutUser()">Logout</a></li>
          </ul>
        </li>
      </ul>
    </div>
  </div>
</nav>

<div class="container my-5 flex-grow-1">
  <h2 class="text-center mb-4" style="color:#6a1b9a;">Your Cart</h2>
  <div id="cartItems" class="row g-4"></div>
  <div id="cartSummary" class="text-end mt-4"></div>
  
  <form id="checkoutForm" method="POST" action="checkout.php" style="display:none;" class="text-end">
    <input type="hidden" name="cart_items" id="cart_items" />
    <button type="submit" class="btn btn-purple">Proceed to Checkout</button>
  </form>
</div>

<script>
const userId = "<?php echo $_SESSION['user_id']; ?>";
const cartKey = `cart_${userId}`;
let cart = JSON.parse(localStorage.getItem(cartKey)) || [];

// Get product info from URL (if coming from shop.php)
const urlParams = new URLSearchParams(window.location.search);
const id    = urlParams.get("id");      // unique product id
const name  = urlParams.get("name");
const price = urlParams.get("price");
const image = urlParams.get("image");

if (id && name && price && image) {
    // Check if product already in cart by id
    const idx = cart.findIndex(item => item.id === id);
    if(idx !== -1){
        // Same product, increase quantity
        cart[idx].quantity += 1;
    } else {
        // New product, add to cart
        cart.push({ id, name, price: parseFloat(price), image, quantity: 1 });
    }
    localStorage.setItem(cartKey, JSON.stringify(cart));

    // Remove URL params to prevent re-adding on refresh
    window.history.replaceState({}, document.title, "cart.php");
}

// Function to render cart items
function renderCart(){
    const container = document.getElementById("cartItems");
    const summary = document.getElementById("cartSummary");
    const checkoutForm = document.getElementById("checkoutForm");
    container.innerHTML = '';
    summary.innerHTML = '';
    checkoutForm.style.display = 'none';

    if(cart.length === 0){
        container.innerHTML = '<p class="text-center">Your cart is empty.</p>';
        return;
    }

    let total = 0;
    cart.forEach((item, idx)=>{
        total += item.price * item.quantity;
        const col = document.createElement("div");
        col.className = "col-md-4";
        col.innerHTML = `
        <div class="card h-100 shadow-sm">
            <img src="${item.image}" alt="${item.name}" class="card-img-top" style="height:200px; object-fit:cover;" onerror="this.src='Images/placeholder.jpg'">
            <div class="card-body d-flex flex-column">
                <h5 class="card-title">${item.name}</h5>
                <p class="card-text">₦${item.price.toLocaleString()} x ${item.quantity}</p>
                <button class="btn btn-danger mt-auto removeBtn" data-idx="${idx}">Remove</button>
            </div>
        </div>
        `;
        container.appendChild(col);
    });

    summary.innerHTML = `<h4>Total: ₦${total.toLocaleString()}</h4>`;
    checkoutForm.style.display = 'block';

    document.querySelectorAll(".removeBtn").forEach(btn=>{
        btn.addEventListener("click", e=>{
            const i = e.target.dataset.idx;
            cart.splice(i,1);
            localStorage.setItem(cartKey, JSON.stringify(cart));
            renderCart();
        });
    });
}

// Submit cart items for checkout
document.getElementById('checkoutForm').addEventListener('submit', function(e){
    document.getElementById('cart_items').value = JSON.stringify(cart);
});

// Logout
function logoutUser(){
    localStorage.removeItem(`cart_${userId}`);
    window.location.href = "auth.php";
}

window.onload = renderCart;
</script>

<footer class="text-center py-4" style="background-color:#6a1b9a; color:white;">
  <p>&copy; <?= date('Y') ?> RIBBOW. All rights reserved.</p>
  <div class="social-links">
    <a href="https://www.instagram.com/_ribbow_" target="_blank" class="text-white mx-2"><i class="fab fa-instagram"></i></a>
    <a href="https://www.tiktok.com/@_ribbow_" target="_blank" class="text-white mx-2"><i class="fab fa-tiktok"></i></a>
    <a href="https://twitter.com/RabiuShuhaidah" target="_blank" class="text-white mx-2"><i class="fab fa-twitter"></i></a>
    <a href="https://wa.me/07017557260" target="_blank" class="text-white mx-2"><i class="fab fa-whatsapp"></i></a>
  </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
</body>
</html>
