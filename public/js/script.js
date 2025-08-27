document.addEventListener("DOMContentLoaded", () => {
  const loggedInUser = JSON.parse(localStorage.getItem("loggedInUser"));
  const currentPage = window.location.pathname;

  // 🔐 Redirect if user is not logged in and tries to access protected pages
  const protectedPages = ["/shop.php", "/cart.php", "/checkout.php", "/orders.php"];
  const isProtected = protectedPages.some(page => currentPage.includes(page));

  if (!loggedInUser && isProtected) {
    window.location.href = "auth.html";
    return;
  }

  // 👋 Show welcome message if user is logged in
  const welcomeElement = document.getElementById("welcomeUser");
  if (loggedInUser && welcomeElement) {
    welcomeElement.textContent = `Hi, ${loggedInUser.username}`;
  }

  // 🚪 Logout
  window.logoutUser = () => {
    const userId = loggedInUser?.user_id || loggedInUser?.id;
    if (userId) {
      localStorage.removeItem(`cart_${userId}`);
    }
    localStorage.removeItem("loggedInUser");
    window.location.href = "auth.html";
  };

  // 🛒 Update Cart Badge in Navbar
  const updateCartBadge = () => {
    const userId = loggedInUser?.user_id || loggedInUser?.id;
    const cartKey = `cart_${userId}`;
    const cart = JSON.parse(localStorage.getItem(cartKey)) || [];

    const badge = document.getElementById("cartBadge");
    if (badge) {
      badge.textContent = cart.length;
      badge.style.display = cart.length > 0 ? "inline-block" : "none";
    }
  };

  // 📦 Add to Cart
  window.addToCart = (product) => {
    const userId = loggedInUser?.user_id || loggedInUser?.id;

    if (!userId) {
      alert("You must be logged in to add items to the cart.");
      return;
    }

    const cartKey = `cart_${userId}`;
    const existingCart = JSON.parse(localStorage.getItem(cartKey)) || [];

    const alreadyExists = existingCart.some(item => item.id === product.id);
    if (alreadyExists) {
      alert("Item already in cart.");
      return;
    }

    existingCart.push(product);
    localStorage.setItem(cartKey, JSON.stringify(existingCart));
    updateCartBadge();
    alert("✅ Added to cart!");
  };

  // ❌ Remove from Cart
  window.removeFromCart = (productId) => {
    const userId = loggedInUser?.user_id || loggedInUser?.id;
    const cartKey = `cart_${userId}`;
    const cart = JSON.parse(localStorage.getItem(cartKey)) || [];

    const updatedCart = cart.filter(item => item.id !== productId);
    localStorage.setItem(cartKey, JSON.stringify(updatedCart));
    updateCartBadge();
     // Optionally make this dynamic later
  };

  // 🚀 Initialize badge on load
  updateCartBadge();
});
window.removeFromCart = (productId) => {
  const userId = loggedInUser?.user_id || loggedInUser?.id;
  const cartKey = `cart_${userId}`;
  const cart = JSON.parse(localStorage.getItem(cartKey)) || [];

  const updatedCart = cart.filter(item => item.id !== productId);
  localStorage.setItem(cartKey, JSON.stringify(updatedCart));
  updateCartBadge();

  // **Re-render the cart UI if this function exists**
  if (typeof displayCartItems === 'function') {
    displayCartItems();
  } else {
    // fallback: reload page (less smooth)
    window.location.reload();
  }
};

//shop.php nav bar
document.querySelectorAll('.dropdown-submenu > a').forEach(el => {
  el.addEventListener('click', function(e) {
    if (window.innerWidth < 992) { // Mobile view
      e.preventDefault();
      let nextMenu = this.nextElementSibling;
      if (nextMenu && nextMenu.classList.contains('dropdown-menu')) {
        nextMenu.classList.toggle('show');
      }
    }
  });
});



//animations
document.querySelectorAll('a[href^="#"]').forEach(anchor => {
  anchor.addEventListener('click', function(e) {
    e.preventDefault();
    document.querySelector(this.getAttribute('href')).scrollIntoView({
      behavior: 'smooth'
    });
  });
});

const scrollElements = document.querySelectorAll('.scroll-animate');

const elementInView = (el, dividend = 1) => {
  const elementTop = el.getBoundingClientRect().top;
  return elementTop <= (window.innerHeight || document.documentElement.clientHeight) / dividend;
};

const displayScrollElement = (element) => element.classList.add('show');

const handleScrollAnimation = () => {
  scrollElements.forEach(el => {
    if (elementInView(el, 1.25)) displayScrollElement(el);
  });
};

window.addEventListener('scroll', () => { handleScrollAnimation(); });
window.addEventListener('load', () => { handleScrollAnimation(); });






