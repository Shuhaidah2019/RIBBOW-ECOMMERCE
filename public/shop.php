<?php
session_start();
require_once '../config/db.php';

// Protect page (user must be logged in)
if (!isset($_SESSION['user_id'])) {
    header("Location: auth.php");
    exit();
}

$categoryFilter = '';
$selectedCategory = 'all';

if (isset($_GET['category']) && $_GET['category'] !== 'all') {
    $selectedCategory = $_GET['category'];
    $categoryFilter = "WHERE category = ?";
}

$query = "SELECT * FROM products $categoryFilter ORDER BY id DESC";
$stmt = $conn->prepare($query);

if ($categoryFilter) {
    $stmt->bind_param("s", $selectedCategory);
}

$stmt->execute();
$result = $stmt->get_result();


?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shop - RIBBOW</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"/>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
    <style>
        body {
            background: linear-gradient(to bottom, #ae98d2ff, #e2d6e1ff);
            font-family: 'Poppins', sans-serif;
            min-height: 100vh;
            padding-top: 70px;
        }

        /* Navbar styling */
        .navbar {
            background-color: #6a1b9a !important;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            z-index: 1050;
        }

        .navbar .navbar-brand {
            font-weight: bold;
            color: #decee8ff !important;
            font-size: 1.5rem;
            letter-spacing: 1px;
            text-transform: uppercase;
        }

        .navbar .nav-link {
            color: #fff !important;
            font-weight: 600;
            transition: color 0.3s ease;
        }

        .navbar .nav-link:hover,
        .navbar .nav-link.active {
            color: #dfc1ddff !important;
        }

        .dropdown-menu {
            background-color: #6a1b9a !important;
            border: none;
            border-radius: 10px;
            overflow: hidden;
        }

        .dropdown-menu .dropdown-item {
            color: #fff !important;
            padding: 10px 20px;
            transition: background 0.3s ease;
        }

        .dropdown-menu .dropdown-item:hover {
            background-color: #770bdc !important;
            color: #fff !important;
        }

        .navbar-toggler {
            border-color: #fff !important;
        }
        .navbar-toggler-icon { filter: invert(1); }

        /* Product cards */
        .card {
            border: none;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }
        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 6px 15px rgba(0, 0, 0, 0.15);
        }
        .card-img-top {
            width: 100%;
            height: 200px;
            object-fit: cover;
        }
        .btn-primary {
            background-color: #6a1b9a;
            border: none;
            transition: background 0.3s ease;
        }
        .btn-primary:hover {
            background-color: #770bdc;
        }

        /* Footer */
        footer {
            background-color: #6a1b9a;
            color: white;
            margin-top: 40px;
            padding: 15px 0;
        }
        footer .social-links a {
            font-size: 1.3rem;
            transition: color 0.3s ease;
        }
        footer .social-links a:hover {
            color: #dfc1ddff;
        }
    </style>
</head>
<body>

<!-- NAVBAR -->
<nav class="navbar navbar-expand-lg navbar-light shadow-sm">
  <div class="container">
    <a class="navbar-brand fw-bold" href="index.php">
      <i class="fas fa-ribbon me-2"></i> RIBBOW
    </a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNavDropdown" aria-controls="navbarNavDropdown" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse justify-content-end" id="navbarNavDropdown">
      <ul class="navbar-nav">
        <li class="nav-item"><a class="nav-link" href="index.php">Home</a></li>
        <li class="nav-item"><a class="nav-link" href="cart.php">Cart</a></li>
        <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle <?php echo ($selectedCategory !== 'all') ? 'active' : ''; ?>" href="#" id="categoriesDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">Categories</a>
          <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="categoriesDropdown">
    <li><a class="dropdown-item" href="shop.php?category=all">All</a></li>
    <li><a class="dropdown-item" href="shop.php?category=lipbalm">Lip Balm</a></li>
    <li><a class="dropdown-item" href="shop.php?category=beadedbag">Beaded Bag</a></li>
    <li><a class="dropdown-item" href="shop.php?category=tote">Tote Bag</a></li>
    <li><a class="dropdown-item" href="shop.php?category=scrunchies">Scrunchies</a></li>
    <li><a class="dropdown-item" href="shop.php?category=handcream">Hand Cream</a></li>
    <li><a class="dropdown-item" href="shop.php?category=facetowel">Face Towel</a></li>
    <li><a class="dropdown-item" href="shop.php?category=facemassager">Face Massager</a></li>
    <li><a class="dropdown-item" href="shop.php?category=facesheet">Face Sheets</a></li>
    <li><a class="dropdown-item" href="shop.php?category=hairclip">Hair Clip</a></li>
    <li><a class="dropdown-item" href="shop.php?category=hairset">Hair Set</a></li>
    <li><a class="dropdown-item" href="shop.php?category=thankyouset">Thank You Set</a></li>
         </ul>
        </li>
        <li class="nav-item"><a class="nav-link text-danger" href="logout.php">Logout</a></li>
      </ul>
    </div>
  </div>
</nav>

<!-- PRODUCTS SECTION -->
<div class="container mt-4">
    <h2 class="mb-4 text-center" style="color:#6a1b9a;">Products <?php echo $selectedCategory !== 'all' ? " - " . ucfirst($selectedCategory) : ''; ?></h2>
    <div class="row">
        <?php if ($result->num_rows > 0): ?>
            <?php while ($product = $result->fetch_assoc()): ?>
                <div class="col-md-4 mb-4">
                    <div class="card h-100">
                        <img src="images/<?php echo htmlspecialchars($product['image']); ?>" class="card-img-top" alt="<?php echo htmlspecialchars($product['name']); ?>" onerror="this.src='images/placeholder.jpg'">
                        <div class="card-body d-flex flex-column">
                            <h5 class="card-title"><?php echo htmlspecialchars($product['name']); ?></h5>
                            <p class="fw-bold">#<?php echo number_format($product['price'], 2); ?></p>
                            <a href="add-to-cart.php?id=<?php echo $product['id']; ?>" class="btn btn-primary mt-auto">Buy Now</a>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p class="text-center">No products found in this category.</p>
        <?php endif; ?>
    </div>
</div>

<!-- FOOTER -->
<footer class="text-center">
  <p>&copy; <?php echo date('Y'); ?> RIBBOW. All rights reserved.</p>
  <div class="social-links">
    <a href="https://www.instagram.com/_ribbow_" target="_blank" class="text-white mx-2"><i class="fab fa-instagram"></i></a>
    <a href="https://www.tiktok.com/@_ribbow_" target="_blank" class="text-white mx-2"><i class="fab fa-tiktok"></i></a>
    <a href="https://twitter.com/RabiuShuhaidah" target="_blank" class="text-white mx-2"><i class="fab fa-twitter"></i></a>
    <a href="https://wa.me/07017557260" target="_blank" class="text-white mx-2"><i class="fab fa-whatsapp"></i></a>
  </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="js/script.js"></script>
</body>
</html>
