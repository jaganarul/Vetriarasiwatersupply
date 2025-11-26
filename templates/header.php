<?php
// Get cart count
$cart = $_SESSION['cart'] ?? [];
$cart_count = array_sum($cart); // total quantity of items
?>

<header class="glass-header sticky-top shadow-sm">

  <!-- Wave Background -->
  <div class="header-wave-bg"></div>

  <nav class="navbar navbar-expand-lg navbar-light py-2">
    <div class="container">

      <!-- Logo -->
      <a href="<?php echo $base_url; ?>/" class="d-flex align-items-center text-decoration-none">
        <img src="<?php echo $base_url; ?>/assets/images/logo.png" 
             alt="Vetriarasiwatersupply"
             class="me-2"
             style="height:34px;width:auto;">
        <span class="brand-text">Vetriarasiwatersupply</span>
      </a>

      <!-- Mobile Menu Button -->
      <button class="navbar-toggler border-0" type="button" id="openMobileMenu" aria-label="Open menu">
        <i class="bi bi-list fs-3"></i>
      </button>

      <!-- Desktop Menu -->
      <div class="collapse navbar-collapse" id="navMain" role="navigation">
        <ul class="navbar-nav ms-lg-3 mb-2 mb-lg-0 align-items-lg-center">

          <li class="nav-item"><a class="nav-link modern-link" href="<?php echo $base_url; ?>">Home</a></li>
          <li class="nav-item"><a class="nav-link modern-link" href="<?php echo $base_url; ?>/product.php">Products</a></li>
          <li class="nav-item"><a class="nav-link modern-link" href="<?php echo $base_url; ?>/about.php">About</a></li>
          <li class="nav-item"><a class="nav-link modern-link" href="<?php echo $base_url; ?>/contact.php">Contact</a></li>

          <!-- Categories -->
          <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle" href="#" id="categoriesDropdown" data-bs-toggle="dropdown" aria-expanded="false" role="button">
              Categories
            </a>
            <ul class="dropdown-menu shadow-sm border-0 rounded-3" aria-labelledby="categoriesDropdown">
              <?php foreach($catRows as $cr): if(!$cr['category']) continue; ?>
                <li>
                  <a class="dropdown-item py-2"
                    href="<?php echo $base_url; ?>/category/<?php echo urlencode($cr['category']); ?>">
                    <?php echo esc($cr['category']); ?>
                  </a>
                </li>
              <?php endforeach; ?>
            </ul>
          </li>

          <li class="nav-item"><a class="nav-link" href="<?php echo $base_url; ?>/track.php">Track Order</a></li>
        </ul>

        <!-- Right Buttons -->
        <div class="ms-auto d-flex align-items-center gap-2">

          <!-- Cart with Badge -->
          <a class="btn btn-outline-primary btn-sm px-3 position-relative" href="<?php echo $base_url; ?>/cart.php" aria-label="View cart with <?php echo $cart_count; ?> items">
            <i class="bi bi-cart3"></i> Cart
            <?php if($cart_count > 0): ?>
              <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger shadow-sm" style="font-size:0.75rem;">
                <?php echo $cart_count; ?>
                <span class="visually-hidden">items in cart</span>
              </span>
            <?php endif; ?>
          </a>

          <?php if(is_logged_in()): ?>
            <a class="btn btn-link fw-semibold text-truncate" href="<?php echo $base_url; ?>/profile" title="<?php echo esc($_SESSION['user_name']); ?>">
              <?php echo esc($_SESSION['user_name']); ?>
            </a>
            <a class="btn btn-link text-danger" href="<?php echo $base_url; ?>/logout.php">Logout</a>
          <?php else: ?>
            <a class="btn btn-primary btn-sm px-3" href="<?php echo $base_url; ?>/login.php">Login</a>
            <a class="btn btn-outline-secondary btn-sm px-3" href="<?php echo $base_url; ?>/register.php">Register</a>
          <?php endif; ?>

          <a class="btn btn-dark btn-sm px-3 rounded-pill" href="<?php echo $base_url; ?>/admin" title="Admin Login">
            Admin
          </a>

        </div>
      </div>
    </div>
  </nav>
</header>

<!-- ============================
      MOBILE SLIDE MENU
=============================== -->
<div id="mobileMenu" class="mobile-menu" aria-hidden="true" tabindex="-1">
  
  <div class="mobile-top-strip"></div>

  <div class="mobile-header">
    <span class="fw-bold fs-5">Menu</span>
    <button id="closeMobileMenu" class="btn-close" aria-label="Close menu"></button>
  </div>

  <nav class="mobile-inner" role="menu">
    <a class="mm-item" href="<?php echo $base_url; ?>" role="menuitem">
      <i class="bi bi-house-door me-2"></i> Home
    </a>

    <a class="mm-item" href="<?php echo $base_url; ?>/products.php" role="menuitem">
      <i class="bi bi-bucket me-2"></i> Products
    </a>

    <a class="mm-item" href="<?php echo $base_url; ?>/about.php" role="menuitem">
      <i class="bi bi-info-circle me-2"></i> About
    </a>

    <a class="mm-item" href="<?php echo $base_url; ?>/contact.php" role="menuitem">
      <i class="bi bi-telephone me-2"></i> Contact
    </a>

    <a class="mm-item" href="<?php echo $base_url; ?>/track.php" role="menuitem">
      <i class="bi bi-truck me-2"></i> Track Order
    </a>

    <!-- Mobile Cart with Badge -->
    <a class="mm-item position-relative" href="<?php echo $base_url; ?>/cart.php" role="menuitem" aria-label="View cart with <?php echo $cart_count; ?> items">
      <i class="bi bi-cart3 me-2"></i> Cart
      <?php if($cart_count > 0): ?>
        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger shadow-sm">
          <?php echo $cart_count; ?>
          <span class="visually-hidden">items in cart</span>
        </span>
      <?php endif; ?>
    </a>

    <?php if(is_logged_in()): ?>
      <a class="mm-item" href="<?php echo $base_url; ?>/profile.php" role="menuitem">
        <i class="bi bi-person-circle me-2"></i> Profile
      </a>
      <a class="mm-item text-danger" href="<?php echo $base_url; ?>/logout" role="menuitem">
        <i class="bi bi-box-arrow-right me-2"></i> Logout
      </a>
    <?php else: ?>
      <a class="mm-item" href="<?php echo $base_url; ?>/login.php" role="menuitem">
        <i class="bi bi-box-arrow-in-right me-2"></i> Login
      </a>
      <a class="mm-item" href="<?php echo $base_url; ?>/register.php" role="menuitem">
        <i class="bi bi-person-plus me-2"></i> Register
      </a>
    <?php endif; ?>

    <div class="mobile-section">Categories</div>

    <?php foreach($catRows as $cr): if(!$cr['category']) continue; ?>
      <a class="mm-item" href="<?php echo $base_url; ?>/category.php?name=<?php echo urlencode($cr['category']); ?>" role="menuitem">
        <i class="bi bi-tag me-2"></i> <?php echo esc($cr['category']); ?>
      </a>
    <?php endforeach; ?>
  </nav>

</div>

<style>
/* Glass Header */
.glass-header {
  backdrop-filter: blur(12px);
  background: rgba(255,255,255,0.65);
  border-bottom: 1px solid rgba(255,255,255,0.4);
  transition: background 0.3s ease;
  z-index: 1030;
  position: relative;
  overflow: hidden; /* important for wave */
}

/* Change header background on scroll */
.glass-header.scrolled {
  background: rgba(255,255,255,0.85);
  box-shadow: 0 4px 12px rgb(0 0 0 / 0.08);
}

/* Brand text */
.brand-text {
  font-size: 1.5rem;
  font-weight: 700;
  color: #0b74ff;
  font-family: 'Inter', sans-serif;
  user-select: none;
  white-space: nowrap;
  transition: color 0.3s ease;
}
.brand-text:hover {
  color: #0851bf;
}

/* Mobile Menu */
.mobile-menu {
  position: fixed;
  top: 0;
  left: -290px;
  width: 270px;
  height: 100vh;
  background: rgba(255,255,255,0.90);
  backdrop-filter: blur(14px);
  box-shadow: 4px 0 25px rgba(0,0,0,0.2);
  transition: left 0.35s ease;
  z-index: 1050;
  border-right: 1px solid rgba(255,255,255,0.7);
  overflow-y: auto;
  overscroll-behavior: contain;
}

.mobile-top-strip {
  height: 6px;
  background: linear-gradient(to right, #0b74ff, #4bb8ff, #00d4ff);
}

.mobile-header {
  display:flex;
  justify-content:space-between;
  align-items:center;
  padding: 15px 20px;
  border-bottom:1px solid rgba(0,0,0,0.1);
}

.mobile-header .btn-close {
  font-size: 1.3rem;
  color: #444;
  font-weight: 700;
  border: none;
  background: transparent;
  cursor: pointer;
  transition: color 0.2s ease;
}
.mobile-header .btn-close:hover {
  color: #0b74ff;
}

.mobile-inner {
  padding: 10px 0;
  display: flex;
  flex-direction: column;
}

.mm-item {
  display:flex;
  align-items:center;
  padding: 12px 22px;
  font-size: 1rem;
  font-weight: 500;
  text-decoration: none;
  color:#222;
  transition: background 0.25s ease, transform 0.3s ease;
  border-radius: 6px;
  margin: 3px 12px;
  user-select: none;
}
.mm-item:hover, .mm-item:focus {
  background: rgba(11,116,255,0.12);
  transform: translateX(6px);
  outline: none;
}
.mobile-section {
  margin: 18px 15px 8px;
  font-size: 0.75rem;
  text-transform: uppercase;
  font-weight: 700;
  color:#555;
  border-left: 3px solid #0b74ff;
  padding-left: 9px;
  user-select: none;
}

/* Floating Wave */
.header-wave-bg {
  position: absolute;
  bottom: 0;
  left: 0;
  width: 200%;
  height: 80px;
  background:
    url('data:image/svg+xml;utf8,<svg viewBox="0 0 1200 80" xmlns="http://www.w3.org/2000/svg" preserveAspectRatio="none"><path fill="%230b74ff" fill-opacity="0.3" d="M0 30 C150 80 350 0 600 30 C850 60 1050 10 1200 30 V80 H0 Z"></path></svg>') repeat-x;
  background-size: 50% 100%;
  animation: waveMove 8s linear infinite;
  pointer-events: none;
  z-index: 0;
}

/* Wave Animation */
@keyframes waveMove {
  from {
    background-position-x: 0;
  }
  to {
    background-position-x: 1600px;
  }
}
</style>

<script>
document.getElementById("openMobileMenu").onclick = function() {
  document.getElementById("mobileMenu").style.left = "0px";
  document.getElementById("mobileMenu").setAttribute("aria-hidden", "false");
  document.getElementById("mobileMenu").focus();
};

document.getElementById("closeMobileMenu").onclick = function() {
  document.getElementById("mobileMenu").style.left = "-290px";
  document.getElementById("mobileMenu").setAttribute("aria-hidden", "true");
};

// Optional: Change header background on scroll
window.addEventListener('scroll', function() {
  const header = document.querySelector('.glass-header');
  if(window.scrollY > 30) {
    header.classList.add('scrolled');
  } else {
    header.classList.remove('scrolled');
  }
});
</script>
