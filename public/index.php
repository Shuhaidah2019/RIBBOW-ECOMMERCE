<?php
session_start();
if (!isset($_SESSION['user_id'])) {
  header("Location: auth.html");
  exit();
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>RIBBOW | Home</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet"/>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet"/>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"/>
  <link rel="stylesheet" href="css/style.css" />
</head>
<body>
<h2>Welcome, <?php echo htmlspecialchars($_SESSION['user_name']); ?>!</h2>
  <!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-light bg-light sticky-top shadow-sm">
  <div class="container-fluid px-lg-5">
    <a class="navbar-brand fw-bold text-purple" href="index.html">
      <i class="fas fa-ribbon me-2"></i> RIBBOW
    </a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarDropdownMenu">
      <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse justify-content-end" id="navbarDropdownMenu">
      <ul class="navbar-nav">
        <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle text-purple fw-semibold" href="#" id="mainMenuDropdown" role="button" data-bs-toggle="dropdown">
            <i class="fas fa-bars me-1"></i> Menu
          </a>
          <ul class="dropdown-menu dropdown-menu-end">

            <!-- Home -->
            <li>
              <a class="dropdown-item" href="index.php">
                <i class="fas fa-home me-2"></i> Home
              </a>
            </li>
            <!-- Categories with Submenu -->
            <li class="dropdown-submenu">
              <a class="dropdown-item dropdown-toggle" href="#" id="categoryDropdown" role="button" data-bs-toggle="dropdown">
                <i class="fas fa-th-large me-2"></i> Categories
              </a>
              <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="categoryDropdown">
                <li><a class="dropdown-item" href="shop.html?category=beaded"><i class="fas fa-gem me-2"></i> Beaded Bags</a></li>
                <li><a class="dropdown-item" href="shop.html?category=hairclips"><i class="fas fa-star me-2"></i> Hair Clips</a></li>
                <li><a class="dropdown-item" href="shop.html?category=totes"><i class="fas fa-shopping-bag me-2"></i> Tote Bags</a></li>
                <li><a class="dropdown-item" href="shop.html?category=all"><i class="fas fa-list me-2"></i> All Products</a></li>
              </ul>
            </li>

            <!-- Auth Section -->
            <?php if (!isset($_SESSION['user_id'])): ?>
              <li><a class="dropdown-item" href="auth.html"><i class="fas fa-sign-in-alt me-2"></i> Login</a></li>
              <li><a class="dropdown-item" href="auth.html#signin"><i class="fas fa-user-plus me-2"></i> Signup</a></li>
            <?php else: ?>
              <li><a class="dropdown-item text-danger" href="../backend/logout.php"><i class="fas fa-sign-out-alt me-2"></i> Logout</a></li>
            <?php endif; ?>
          </ul>
        </li>
      </ul>
    </div>
  </div>
</nav>

<!-- Floating Theme Toggle -->
<button id="themeToggle" class="theme-toggle floating-theme-toggle" aria-label="Toggle dark mode">
  <i class="fas fa-moon"></i>
</button>


 <section class="hero">
  <!-- Background Video -->
  <video autoplay muted loop playsinline id="heroVideo" class="hero-video">
    <source src="media/2bg-video.mp4" type="video/mp4">
    Your browser does not support the video tag.
  </video>
 </section>

 <!-- Cards Preview Section -->
<section class="preview-panels-section">
  <div class="preview-grid">
    <div class="preview-card card-1">
      <img src="images/image12.jpg" alt="Panel 1">
      <div class="preview-text">Hair Clips</div>
    </div>
    <div class="preview-card card-2">
      <img src="Images/beadedwhite.jpg" alt="Panel 2">
      <div class="preview-text">Beaded Bag</div>
    </div>
    <div class="preview-card card-3">
      <img src="Images/hero-bg.jpg" alt="Panel 3">
      <div class="preview-text"> Beaded Accessories</div>
    </div>
    <div class="preview-card card-4">
      <img src="Images/image1.jpg" alt="Panel 4">
      <div class="preview-text">Crochet</div>
    </div>
    <div class="preview-card card-5">
      <img src="Images/image2.jpg" alt="Panel 5">
      <div class="preview-text">Embroidey</div>
    </div>
  </div>
</section>

  <!-- Foreground Content -->
  <div class="hero-overlay-content">
    <h1>Elegant accessories<br>Made With Love<br>Just For You!</h1>
    <p class="short-description">Explore handmade elegance that speaks to your style.</p>
    <button onclick="goToShop()" class="btn btn-primary">Shop Now</button>
  </div>
</section>

  <!-- Features Section -->
<section class="py-5 text-center bg-white">
  <div class="container">
    <h1 class="section-heading mb-4" data-aos="fade-up">Why Shop with Us</h1>
    <div class="row g-4">
      <div class="col-md-4" data-aos="fade-up" data-aos-delay="100">
        <i class="fas fa-gem feature-icon mb-3"></i>
        <h4>Unique Products</h4>
        <p>Handcrafted pieces made with passion and precision.</p>
      </div>
      <div class="col-md-4" data-aos="fade-up" data-aos-delay="200">
        <i class="fas fa-shipping-fast feature-icon mb-3"></i>
        <h4>Fast Delivery</h4>
        <p>Reliable and swift delivery, right to your doorstep.</p>
      </div>
      <div class="col-md-4" data-aos="fade-up" data-aos-delay="300">
        <i class="fas fa-smile-beam feature-icon mb-3"></i>
        <h4>Customer Love</h4>
        <p>Our clients adore the quality, design, and service.</p>
      </div>
    </div>
  </div>
</section>

<!-- About Section -->
<section id="about" class="py-5 bg-light">
  <div class="container text-center">
    <h1 class="section-heading mb-3" data-aos="fade-up">About RIBBOW</h1>
    <p class="mx-auto" style="max-width: 700px;" data-aos="fade-up" data-aos-delay="100">
      RIBBOW is a fashion-forward brand delivering handmade accessories for bold, beautiful, and brilliant women. From beaded bags to trendy clips, each piece is crafted with care to spark your uniqueness.
    </p>
  </div>
</section>

<!-- Contact Section -->
<section id="contact" class="py-5">
  <div class="container">
    <h1 class="text-center section-heading mb-4" data-aos="fade-up">Contact Us</h1>
    <form class="row g-3 mt-4" action="../backend/contact.php" method="POST" style="max-width: 700px; margin: auto;" data-aos="fade-up" data-aos-delay="100">
      <div class="col-md-6">
        <input type="text" name="name" class="form-control" placeholder="Your Name" required />
      </div>
      <div class="col-md-6">
        <input type="email" name="email" class="form-control" placeholder="Email Address" required />
      </div>
      <div class="col-12">
        <textarea name="message" class="form-control" rows="5" placeholder="Your Message" required></textarea>
      </div>
      <div class="col-12 text-center">
        <button type="submit" class="btn btn-purple mt-3">Send Message</button>
      </div>
    </form>
  </div>
</section>

<!-- Floating Social Icons -->
<div class="floating-icons">
  <a href="https://wa.me/2347017557260" class="whatsapp-icon" target="_blank"><i class="fab fa-whatsapp"></i></a>
  <a href="https://instagram.com/_RIBBOW_" class="instagram-icon" target="_blank"><i class="fab fa-instagram"></i></a>
  <a href="https://www.tiktok.com/@shuir_dar" class="tiktok-icon" target="_blank"><i class="fab fa-tiktok"></i></a>
</div>


<!-- Footer -->
<footer class="text-center text-white py-4" style="background-color: #6a1b9a;">
  <p class="mb-0">&copy; 2025 RIBBOW. All rights reserved.</p>
</footer>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <script src="js/script.js"></script>
<!-- AOS Animation Library -->
<link href="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.js"></script>
<script>
  AOS.init();
</script>


</body>
</html>
