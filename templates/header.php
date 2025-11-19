<?php
// Get cart count
$cart = $_SESSION['cart'] ?? [];
$cart_count = array_sum($cart); // total quantity of items
?>

<header class="glass-header sticky-top">
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
      <button class="navbar-toggler border-0" type="button" id="openMobileMenu">
        <i class="bi bi-list" style="font-size: 1.7rem;"></i>
      </button>

      <!-- Desktop Menu -->
      <div class="collapse navbar-collapse" id="navMain">
        <ul class="navbar-nav ms-lg-3 mb-2 mb-lg-0 align-items-lg-center">

          <li class="nav-item"><a class="nav-link modern-link" href="<?php echo $base_url; ?>/index.php">Home</a></li>
          <li class="nav-item"><a class="nav-link modern-link" href="<?php echo $base_url; ?>/product.php">Products</a></li>
          <li class="nav-item"><a class="nav-link modern-link" href="<?php echo $base_url; ?>/about.php">About</a></li>
          <li class="nav-item"><a class="nav-link modern-link" href="<?php echo $base_url; ?>/contact.php">Contact</a></li>

          <!-- Categories -->
          <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">Categories</a>
            <ul class="dropdown-menu shadow-sm border-0 rounded-3">
              <?php foreach($catRows as $cr): if(!$cr['category']) continue; ?>
                <li>
                  <a class="dropdown-item py-2"
                     href="<?php echo $base_url; ?>/index.php?category=<?php echo urlencode($cr['category']); ?>">
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
          <a class="btn btn-outline-primary btn-sm px-3 position-relative" href="<?php echo $base_url; ?>/cart.php">
            <i class="bi bi-cart3"></i> Cart
            <?php if($cart_count > 0): ?>
              <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                <?php echo $cart_count; ?>
                <span class="visually-hidden">items in cart</span>
              </span>
            <?php endif; ?>
          </a>

          <?php if(is_logged_in()): ?>
            <a class="btn btn-link fw-semibold" href="<?php echo $base_url; ?>/profile.php">
              <?php echo esc($_SESSION['user_name']); ?>
            </a>
            <a class="btn btn-link text-danger" href="<?php echo $base_url; ?>/logout.php">Logout</a>
          <?php else: ?>
            <a class="btn btn-primary btn-sm px-3" href="<?php echo $base_url; ?>/login.php">Login</a>
            <a class="btn btn-outline-secondary btn-sm px-3" href="<?php echo $base_url; ?>/register.php">Register</a>
          <?php endif; ?>

          <a class="btn btn-dark btn-sm px-3 rounded-pill" href="<?php echo $base_url; ?>/admin/login.php">
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
<div id="mobileMenu" class="mobile-menu">
  
  <div class="mobile-top-strip"></div>

  <div class="mobile-header">
    <span class="fw-bold fs-5">Menu</span>
    <i class="bi bi-x-lg" id="closeMobileMenu"></i>
  </div>

  <div class="mobile-inner">

    <a class="mm-item" href="<?php echo $base_url; ?>/index.php">
      <i class="bi bi-house-door me-2"></i> Home
    </a>

    <a class="mm-item" href="<?php echo $base_url; ?>/product.php">
      <i class="bi bi-bucket me-2"></i> Products
    </a>

    <a class="mm-item" href="<?php echo $base_url; ?>/about.php">
      <i class="bi bi-info-circle me-2"></i> About
    </a>

    <a class="mm-item" href="<?php echo $base_url; ?>/contact.php">
      <i class="bi bi-telephone me-2"></i> Contact
    </a>

    <a class="mm-item" href="<?php echo $base_url; ?>/track.php">
      <i class="bi bi-truck me-2"></i> Track Order
    </a>

    <!-- Mobile Cart with Badge -->
    <a class="mm-item position-relative" href="<?php echo $base_url; ?>/cart.php">
      <i class="bi bi-cart3 me-2"></i> Cart
      <?php if($cart_count > 0): ?>
        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
          <?php echo $cart_count; ?>
          <span class="visually-hidden">items in cart</span>
        </span>
      <?php endif; ?>
    </a>

    <?php if(is_logged_in()): ?>
      <a class="mm-item" href="<?php echo $base_url; ?>/profile.php">
        <i class="bi bi-person-circle me-2"></i> Profile
      </a>
      <a class="mm-item text-danger" href="<?php echo $base_url; ?>/logout.php">
        <i class="bi bi-box-arrow-right me-2"></i> Logout
      </a>
    <?php else: ?>
      <a class="mm-item" href="<?php echo $base_url; ?>/login.php">
        <i class="bi bi-box-arrow-in-right me-2"></i> Login
      </a>
      <a class="mm-item" href="<?php echo $base_url; ?>/register.php">
        <i class="bi bi-person-plus me-2"></i> Register
      </a>
    <?php endif; ?>

    <div class="mobile-section">Categories</div>

    <?php foreach($catRows as $cr): if(!$cr['category']) continue; ?>
      <a class="mm-item" href="<?php echo $base_url; ?>/index.php?category=<?php echo urlencode($cr['category']); ?>">
        <i class="bi bi-tag me-2"></i> <?php echo esc($cr['category']); ?>
      </a>
    <?php endforeach; ?>

  </div>

</div>

<style>
/* Glass Header */
.glass-header {
  backdrop-filter: blur(12px);
  background: rgba(255,255,255,0.55);
  border-bottom: 1px solid rgba(255,255,255,0.3);
}

.brand-text {
  font-size: 1.4rem;
  font-weight: 700;
  color:#0b74ff;
  font-family:'Inter', sans-serif;
}

/* Mobile Menu */
.mobile-menu {
  position: fixed;
  top: 0;
  left: -290px;
  width: 270px;
  height: 100vh;
  background: rgba(255,255,255,0.83);
  backdrop-filter: blur(12px);
  box-shadow: 4px 0 25px rgba(0,0,0,0.20);
  transition: 0.35s ease;
  z-index: 9999;
  border-right: 1px solid rgba(255,255,255,0.6);
}

.mobile-top-strip {
  height: 6px;
  background: linear-gradient(to right, #0b74ff, #4bb8ff, #00d4ff);
}

.mobile-header {
  display:flex;
  justify-content:space-between;
  align-items:center;
  padding: 15px 18px;
  border-bottom:1px solid rgba(0,0,0,0.1);
}

.mobile-inner { padding: 10px 0; }

.mm-item {
  display:flex;
  align-items:center;
  padding: 12px 18px;
  font-size: 1rem;
  font-weight: 500;
  text-decoration: none;
  color:#222;
  transition: 0.25s;
  border-radius: 6px;
  margin: 3px 10px;
}

.mm-item:hover {
  background: rgba(11,116,255,0.08);
  transform: translateX(4px);
}

.mobile-section {
  margin: 12px 18px 6px;
  font-size: 0.78rem;
  text-transform: uppercase;
  font-weight: 700;
  color:#777;
  border-left: 3px solid #0b74ff;
  padding-left: 8px;
}
</style>

<script>
document.getElementById("openMobileMenu").onclick = function() {
  document.getElementById("mobileMenu").style.left = "0px";
};

document.getElementById("closeMobileMenu").onclick = function() {
  document.getElementById("mobileMenu").style.left = "-290px";
};
</script>
