/*home page*/
function goToShop() {
  const isLoggedIn = localStorage.getItem("isLoggedIn");
  if (isLoggedIn === "true") {
    window.location.href = "shop.html";
  } else {
    alert("Please login or register to access the shop.");
    window.location.href = "auth.html";
  }
}
/*add to cart*/
function addToCart() {
  const params = new URLSearchParams(window.location.search);
  const title = decodeURIComponent(params.get('title') || '');
  const price = decodeURIComponent(params.get('price') || '');
  const image = decodeURIComponent(params.get('image') || '');

  if (!title || !price || !image) {
    alert("Missing product information.");
    return;
  }

  let cart = JSON.parse(localStorage.getItem('cart')) || [];
  cart.push({ title, price, image });
  localStorage.setItem('cart', JSON.stringify(cart));
  alert(`${title} has been added to your cart.`);
}

/*auth page*/
// Redirect logged-in users to the shop page
document.addEventListener("DOMContentLoaded", function () {
  const isLoggedIn = localStorage.getItem("isLoggedIn");
  if (isLoggedIn === "true") {
    // Optional: Only redirect if on auth.html
    if (window.location.pathname.includes("auth.html")) {
      window.location.href = "shop.html";
    }
  }

  // Check for a message in the URL and show it
  const urlParams = new URLSearchParams(window.location.search);
  const message = urlParams.get("msg");
  if (message) {
    alert(decodeURIComponent(message));
  }
});

/* cart.html logic */
if (window.location.pathname.includes("cart.html")) {
  const cart = JSON.parse(localStorage.getItem('cart')) || [];
  const cartContainer = document.getElementById('cartContainer');
  const totalContainer = document.getElementById('totalContainer');

  function renderCart() {
    cartContainer.innerHTML = '';
    let total = 0;

    if (cart.length === 0) {
      cartContainer.innerHTML = '<p class="text-center">Your cart is empty.</p>';
      totalContainer.textContent = '';
      return;
    }

    cart.forEach((item, index) => {
      total += parseInt(item.price);
      const imagePath = item.image ? decodeURIComponent(item.image) : 'placeholder.jpg';

      const div = document.createElement('div');
      div.className = 'cart-item row align-items-center';
      div.innerHTML = `
        <div class="col-md-2 col-sm-4 mb-2 mb-md-0">
          <img src="${imagePath}" class="img-fluid rounded" alt="${item.title}">
        </div>
        <div class="col-md-6 col-sm-8">
          <h5>${item.title}</h5>
          <p>₦${parseInt(item.price).toLocaleString()}</p>
        </div>
        <div class="col-md-4 text-end">
          <button class="btn btn-sm btn-danger" onclick="removeItem(${index})">Remove</button>
        </div>
      `;
      cartContainer.appendChild(div);
    });

    totalContainer.textContent = `Total: ₦${total.toLocaleString()}`;
  }

  window.removeItem = function(index) {
    cart.splice(index, 1);
    localStorage.setItem('cart', JSON.stringify(cart));
    renderCart();
  }

  window.clearCart = function() {
    if (confirm("Are you sure you want to clear your cart?")) {
      localStorage.removeItem('cart');
      cart.length = 0;
      renderCart();
    }
  }

  renderCart();
}

/*checkout.html logic */
if (window.location.pathname.includes("checkout.html")) {
  const cart = JSON.parse(localStorage.getItem('cart')) || [];
  const checkoutContainer = document.getElementById('checkoutContainer');
  const totalContainer = document.getElementById('totalContainer');

  function renderCheckout() {
    checkoutContainer.innerHTML = '';
    let total = 0;

    if (cart.length === 0) {
      checkoutContainer.innerHTML = '<p class="text-center">Your cart is empty.</p>';
      totalContainer.textContent = '';
      return;
    }

    cart.forEach(item => {
      total += parseInt(item.price);
      const itemDiv = document.createElement('div');
      itemDiv.className = 'checkout-item row align-items-center';

      itemDiv.innerHTML = `
        <div class="col-md-2 col-sm-4 mb-2 mb-md-0">
          <img src="${decodeURIComponent(item.image)}" class="img-fluid rounded" alt="${item.title}">
        </div>
        <div class="col-md-6 col-sm-8">
          <h5>${item.title}</h5>
          <p>₦${parseInt(item.price).toLocaleString()}</p>
        </div>
      `;
      checkoutContainer.appendChild(itemDiv);
    });

    totalContainer.textContent = `Total: ₦${total.toLocaleString()}`;
  }

  renderCheckout();
}

/*contact.html logic */
function sendMessage() {
  const name = document.getElementById("name")?.value.trim();
  const email = document.getElementById("email")?.value.trim();
  const message = document.getElementById("message")?.value.trim();
  const status = document.getElementById("messageStatus");

  if (name && email && message) {
    status.innerHTML = `<p class="text-success">Thank you, ${name}! Your message has been sent successfully.</p>`;
    document.getElementById("contactForm").reset();
  } else {
    status.innerHTML = `<p class="text-danger">Please fill in all fields.</p>`;
  }
}
 
/*login page*/
// Toggle Show/Hide Password
const togglePasswordBtn = document.getElementById("togglePassword");
const passwordInput = document.getElementById("password");

if (togglePasswordBtn && passwordInput) {
  togglePasswordBtn.addEventListener("click", () => {
    const type = passwordInput.getAttribute("type");
    if (type === "password") {
      passwordInput.setAttribute("type", "text");
      togglePasswordBtn.textContent = "Hide";
    } else {
      passwordInput.setAttribute("type", "password");
      togglePasswordBtn.textContent = "Show";
    }
  });
}

/*login page*/
// Enable Login Button Only When Fields Are Filled
const loginForm = document.getElementById("loginForm");
const loginBtn = document.getElementById("loginBtn");
const emailInput = document.getElementById("email");

if (loginForm && loginBtn && emailInput && passwordInput) {
  loginForm.addEventListener("input", () => {
    const email = emailInput.value.trim();
    const password = passwordInput.value.trim();
    loginBtn.disabled = !(email && password);
  });
}

// === Register Form Behavior*/
const regForm = document.getElementById("registerForm");
const regName = document.getElementById("regName");
const regEmail = document.getElementById("regEmail");
const regPassword = document.getElementById("regPassword");
const regBtn = document.getElementById("registerBtn");
const toggleRegPassword = document.getElementById("toggleRegPassword");

if (regForm && regName && regEmail && regPassword && regBtn) {
  regForm.addEventListener("input", () => {
    const name = regName.value.trim();
    const email = regEmail.value.trim();
    const password = regPassword.value.trim();
    regBtn.disabled = !(name && email && password);
  });
}

if (toggleRegPassword && regPassword) {
  toggleRegPassword.addEventListener("click", () => {
    const type = regPassword.getAttribute("type");
    regPassword.setAttribute("type", type === "password" ? "text" : "password");
    toggleRegPassword.textContent = type === "password" ? "Hide" : "Show";
  });
}

/* Product Details Page*/
function addToCart() {
  const product = {
    title: "Beaded Handbags",
    price: "30000",
    image: "Images/beadedhandbag.jpg" // Make sure this file exists
  };

  let cart = JSON.parse(localStorage.getItem("cart")) || [];
  cart.push(product);
  localStorage.setItem("cart", JSON.stringify(cart));
  alert("Product added to your cart!");
}
// Add product to cart (used on product-details.html)
function addToCart() {
  const product = {
    title: "Beaded Handbags",
    price: "30000",
    image: "Images/beadedblack.jpg" // Update to match your actual local image path
  };

  let cart = JSON.parse(localStorage.getItem("cart")) || [];
  cart.push(product);
  localStorage.setItem("cart", JSON.stringify(cart));

  alert("Product added to your cart!");
}
  
/*Add to Cart from products.html*/
function addToCartFromProducts(title, price, image) {
  const product = {
    title: title,
    price: price,
    image: image
  };

  let cart = JSON.parse(localStorage.getItem("cart")) || [];
  cart.push(product);
  localStorage.setItem("cart", JSON.stringify(cart));

  alert(`${title} has been added to your cart!`);
}

/* Auto-redirect logged-in users away from register page */
document.addEventListener("DOMContentLoaded", function () {
  const isLoggedIn = localStorage.getItem("isLoggedIn");
  if (isLoggedIn === "true" && window.location.pathname.includes("register.html")) {
    window.location.href = "shop.html";
  }
});

