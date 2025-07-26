

// ===============================
// DOM Loaded Handler
// ===============================
document.addEventListener("DOMContentLoaded", function () {
  const path = window.location.pathname;
  const currentPage = path.substring(path.lastIndexOf("/") + 1);

  // Redirect if not logged in
  const user = localStorage.getItem("loggedInUser");

  if (
    currentPage === "index.html" ||
    ["shop.html", "cart.html", "checkout.html"].includes(currentPage)
  ) {
    if (!user) {
      alert("Please log in to access this page.");
      window.location.href = "auth.html";
    }
  }

//dark and light mode
  document.addEventListener("DOMContentLoaded", function () {
  const themeToggle = document.getElementById("themeToggle");
  const icon = themeToggle.querySelector("i");

  const savedTheme = localStorage.getItem("theme");
  if (savedTheme === "dark") {
    document.body.classList.add("dark-mode");
    icon.classList.remove("fa-moon");
    icon.classList.add("fa-sun");
  }

  themeToggle.addEventListener("click", () => {
    document.body.classList.toggle("dark-mode");

    if (document.body.classList.contains("dark-mode")) {
      icon.classList.remove("fa-moon");
      icon.classList.add("fa-sun");
      localStorage.setItem("theme", "dark");
    } else {
      icon.classList.remove("fa-sun");
      icon.classList.add("fa-moon");
      localStorage.setItem("theme", "light");
    }
  });
});


  // ===============================
  // Background Video Playlist Logic
  // ===============================
  const video = document.getElementById("heroVideo");

  if (video) {
    const videoSources = [
      "media/bg-video.mp4",
      "media/2bg-video.mp4",
      "media/3bg-video.mp4"
    ];

    let currentVideo = 0;

    function playNextVideo() {
      video.src = videoSources[currentVideo];
      video.load();
      video.play();
      currentVideo = (currentVideo + 1) % videoSources.length;
    }

    video.addEventListener("ended", playNextVideo);
    playNextVideo();
  }
});

// ===============================
// Redirect to shop.html if user is logged in
// ===============================
function goToShop() {
  const user = JSON.parse(localStorage.getItem("loggedInUser"));
  if (user) {
    window.location.href = "shop.html";
  } else {
    alert("Please login to continue shopping.");
    window.location.href = "auth.html";
  }
}

// ===============================
// Logout Handler
// ===============================
function logoutUser() {
  localStorage.removeItem("loggedInUser");
  alert("You have been logged out.");
  window.location.href = "auth.html";
}

//form options//
document.addEventListener("DOMContentLoaded", () => {
  const loginTab = document.getElementById("login-tab");
  const signupTab = document.getElementById("signup-tab");
  const welcomeText = document.getElementById("welcomeMessage");

  if (!loginTab || !signupTab || !welcomeText) return;

  // Click event for Login
  loginTab.addEventListener("click", () => {
    welcomeText.innerHTML = "<h2>Hey<br>Welcome<br>Back!</h2>";
  });

  // Click event for Signup
  signupTab.addEventListener("click", () => {
    welcomeText.innerHTML = "<h2>Hey<br>Nice to<br>Meet You!</h2>";
  });

  // Detect if opened via auth.html#signin
  if (window.location.hash === "#signin") {
    welcomeText.innerHTML = "<h2>Hey<br>Nice to<br>Meet You</h2>";
  }
});

//toggle password
document.addEventListener("DOMContentLoaded", () => {
  const toggleBtn = document.getElementById("toggleRegPassword");
  const passwordInput = document.getElementById("regPassword");

  toggleBtn.addEventListener("click", () => {
    const type = passwordInput.getAttribute("type");
    if (type === "password") {
      passwordInput.setAttribute("type", "text");
      toggleBtn.innerHTML = '<i class="fas fa-eye-slash"></i>';
    } else {
      passwordInput.setAttribute("type", "password");
      toggleBtn.innerHTML = '<i class="fas fa-eye"></i>';
    }
  });
});


//categorie section
document.addEventListener("DOMContentLoaded", () => {
  const urlParams = new URLSearchParams(window.location.search);
  const category = urlParams.get('category'); // "beaded", "totes", etc.

  const allProducts = document.querySelectorAll('.product-card');

  allProducts.forEach(product => {
    const productCategory = product.dataset.category;

    if (!category || category === 'all') {
      product.style.display = 'block'; // Show all
    } else if (productCategory === category) {
      product.style.display = 'block'; // Show only the selected category
    } else {
      product.style.display = 'none'; // Hide other categories
    }
  });
});

// Enable submenu toggle inside dropdown
document.addEventListener("DOMContentLoaded", function () {
  document.querySelectorAll('.dropdown-submenu .dropdown-toggle').forEach(function (element) {
    element.addEventListener('click', function (e) {
      e.preventDefault();
      e.stopPropagation();
      let submenu = this.nextElementSibling;
      if (submenu && submenu.classList.contains('dropdown-menu')) {
        submenu.classList.toggle('show');
      }
    });
  });
});

//sub menu or categories
document.addEventListener("DOMContentLoaded", () => {
  const urlParams = new URLSearchParams(window.location.search);
  const category = urlParams.get("category"); // e.g. "beaded", "hairclips", etc.

  const allProducts = document.querySelectorAll(".product-card");

  allProducts.forEach(product => {
    const productCategory = product.dataset.category;

    if (!category || category === "all") {
      product.style.display = "block"; // Show all products
    } else if (productCategory === category) {
      product.style.display = "block"; // Show matching
    } else {
      product.style.display = "none"; // Hide others
    }
  });
});
