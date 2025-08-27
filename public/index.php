<?php
session_start();
$userName = $_SESSION['user_name'] ?? null;
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta charset="UTF-8" />
  <title>RIBBOW | Home</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet"/>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet"/>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"/>
  <link rel="stylesheet" href="css/style.css" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<?php if ($userName): ?>
  <div class="welcome-banner text-center mt-4">
    <h2>Welcome, <?= htmlspecialchars($userName) ?>! 🎉</h2>
  </div>
<?php endif; ?>
  <!-- Navbar -->
   <main>
<nav class="navbar navbar-expand-lg shadow-sm" 
     style="background-color: #6a1b9a; position: sticky; top: 0; z-index: 1050;">
  <div class="container-fluid px-lg-5">
    <a class="navbar-brand fw-bold" href="index.php" style="color: #a57ccbff;">
      <i class="fas fa-ribbon me-2"></i> RIBBOW
    </a>
    <button class="navbar-toggler text-white" type="button" data-bs-toggle="collapse" data-bs-target="#navbarMenu" 
            aria-controls="navbarMenu" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse justify-content-end" id="navbarMenu">
      <ul class="navbar-nav">
        <!-- Dropdown Menu -->
        <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle fw-semibold text-white" href="#" id="menuDropdown" role="button" 
             data-bs-toggle="dropdown" aria-expanded="false">
            <i class="fas fa-bars me-1"></i> Menu
          </a>
          <ul class="dropdown-menu dropdown-menu-end" 
              aria-labelledby="menuDropdown" 
              style="background-color: #6a1b9a; border: none;">
            <li><a class="dropdown-item" href="shop.php?category=all" style="color: #dfc1ddff;">Shop</a></li>
            <li><a class="dropdown-item" href="index.php" style="color: #dfc1ddff;">Home</a></li>
            <?php if (!isset($_SESSION['user_id'])): ?>
              <li><a class="dropdown-item" href="auth.html" style="color: #dfc1ddff;">Login</a></li>
              <li><a class="dropdown-item" href="auth.html#signin" style="color: #dfc1ddff;">Signup</a></li>
            <?php else: ?>
              <li><a class="dropdown-item text-danger" href="#" onclick="logoutUser()">Logout</a></li>
            <?php endif; ?>
          </ul>
        </li>
      </ul>
    </div>
  </div>
</nav>

 <section class="hero">
  <!-- Background Video -->
  <video autoplay muted loop playsinline id="heroVideo" class="hero-video">
    <source src="media/2bg-video.mp4" type="video/mp4">
    Your browser does not support the video tag.
  </video>
 </section>
   </main>


 <!-- Cards Preview Section -->
<section class="preview-panels-section">
  <div class="preview-grid">
    <div class="preview-card card-1">
      <img src="images/pin1.jpg" alt="Panel 1">
      <div class="preview-text">Hair Clips</div>
    </div>
    <div class="preview-card card-2">
      <img src="Images/BeadedBagCombo2.jpg" alt="Panel 2">
      <div class="preview-text">Beaded Bag</div>
    </div>
    <div class="preview-card card-3">
      <img src="Images/ThankYouSet.jpg" alt="Panel 3">
      <div class="preview-text"> Gift Bags</div>
    </div>
    <div class="preview-card card-4">
      <img src="Images/ToteBag.jpg" alt="Panel 4">
      <div class="preview-text">Tote Bags</div>
    </div>
    <div class="preview-card card-5">
      <img src="Images/FaceMassager.jpg" alt="Panel 5">
      <div class="preview-text">Face Massager</div>
    </div>
  </div>
</section>

  <!-- Foreground Content -->
  <div class="hero-overlay-content">
    <h1>Elegant accessories<br>Made With Love<br>Just For You!</h1>
    <p class="short-description">Explore handmade elegance that speaks to your style.</p>
   <button onclick="goToShop()" class="btn btn-primary">Shop Now</button>
<script>
  function goToShop() {
    window.location.href = "shop.php?category=all"; 
  }
</script>

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
        <p>Handcrafted and imported pieces made with passion and precision.</p>
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
      RIBBOW is a fashion-forward brand delivering handmade accessories and imported pieces for bold, beautiful, and brilliant women. From beaded bags to trendy clips, each piece is crafted with care to spark your uniqueness.
    </p>
  </div>
</section>

<!-- Contact Section -->
<section id="contact" class="py-5">
  <div class="container" style="max-width: 700px; margin: auto;">
    <h1 class="text-center section-heading mb-4">Contact Us</h1>
    <form action="../backend/contact.php" method="POST" ...>
      <input type="hidden" name="csrf_token" value="<?php echo generateCsrfToken(); ?>">

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

    <?php
    
    if (isset($_SESSION['contact_success'])) {
      echo '<div class="alert alert-success text-center mt-3">' . htmlspecialchars($_SESSION['contact_success']) . '</div>';
      unset($_SESSION['contact_success']);
    } elseif (isset($_SESSION['contact_error'])) {
      echo '<div class="alert alert-danger text-center mt-3">' . htmlspecialchars($_SESSION['contact_error']) . '</div>';
      unset($_SESSION['contact_error']);
    }
    ?>
  </div>
</section>


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
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <script src="js/script.js"></script>
<script src="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
  <!-- AOS Animation Library -->
  AOS.init();
</script>

<?php if (isset($_SESSION['user_id'])): ?>
<script>
  // Set the user info into localStorage
  localStorage.setItem('loggedInUser', JSON.stringify({
    id: "<?php echo $_SESSION['user_id']; ?>",
    name: "<?php echo $_SESSION['user_name']; ?>"
  }));
</script>
<?php endif; ?>

<script>
  function logoutUser() {
    const userId = "<?php echo $_SESSION['user_id']; ?>";
    localStorage.removeItem(`cart_${userId}`);
    localStorage.removeItem("loggedInUser");
    window.location.href = "auth.html";
  }
</script>
</body>
</html>
