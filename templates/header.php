<?php
// Get cart count
$cart = $_SESSION['cart'] ?? [];
$cart_count = array_sum($cart); // total quantity of items

// current request path for active link detection
$currentPath = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
function is_active($matchPaths, $currentPath) {
  // $matchPaths may be string or array
  foreach ((array)$matchPaths as $m) {
    // normalize slashes
    $mNorm = '/' . ltrim($m, '/');
    if ($m === $currentPath || $mNorm === $currentPath || strpos($currentPath, $m) === 0) return true;
  }
  return false;
}
?>

<header class="glass-header sticky-top shadow-sm" role="banner" id="siteHeader">

  <!-- Wave Background (always visible) -->
  <div class="header-wave-bg" aria-hidden="true"></div>

  <!-- Single Navbar (desktop shown on >=992, mobile uses off-canvas overlay) -->
  <nav class="navbar navbar-expand-lg navbar-light py-2" role="navigation" aria-label="Main navigation">
    <div class="container d-flex align-items-center">

      <!-- Return (back) button -->
      <button id="backButton" class="btn btn-sm btn-light me-2 d-flex align-items-center" aria-label="Go back" title="Go back" type="button">
        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-arrow-left" viewBox="0 0 16 16" aria-hidden="true">
          <path fill-rule="evenodd" d="M15 8a.5.5 0 0 1-.5.5H2.707l4.147 4.146a.5.5 0 0 1-.708.708l-5-5a.5.5 0 0 1 0-.708l5-5a.5.5 0 1 1 .708.708L2.707 7.5H14.5A.5.5 0 0 1 15 8z"/>
        </svg>
      </button>

      <!-- Logo -->
      <a href="<?php echo $base_url; ?>/" class="d-flex align-items-center text-decoration-none brand-link" title="Home">
           <img src="<?php echo $base_url; ?>/assets/images/logo.png"
             alt="Vetriarasiwatersupply"
             class="me-2 site-logo img-fluid"
             loading="lazy">
        <span class="brand-text">Vetriarasiwatersupply</span>
      </a>

      <!-- Mobile Menu Button (visible on small screens) -->
      <button class="navbar-toggler border-0 ms-auto" type="button" id="openMobileMenu" aria-label="Open menu" aria-controls="mobileMenu" aria-expanded="false">
        <i class="bi bi-list fs-3" aria-hidden="true"></i>
      </button>

      <!-- Desktop Menu (visible on >=992). We use d-lg-flex so it displays as flex on large screens -->
      <div class="collapse navbar-collapse d-lg-flex justify-content-end" id="navMain" role="navigation" aria-label="Desktop navigation">
        <ul class="navbar-nav ms-lg-3 mb-2 mb-lg-0 align-items-lg-center">

          <li class="nav-item">
            <a class="nav-link modern-link <?php echo is_active('/', $currentPath) || is_active($base_url.'/', $currentPath) ? 'active' : ''; ?>"
               href="<?php echo $base_url; ?>/">Home</a>
          </li>

          <!-- products -> product.php -->
          <li class="nav-item">
            <a class="nav-link modern-link <?php echo is_active('/product.php', $currentPath) ? 'active' : ''; ?>"
               href="<?php echo $base_url; ?>/product.php">Products</a>
          </li>

          <li class="nav-item">
            <a class="nav-link modern-link <?php echo is_active('/about.php', $currentPath) ? 'active' : ''; ?>"
               href="<?php echo $base_url; ?>/about.php">About</a>
          </li>

          <li class="nav-item">
            <a class="nav-link modern-link <?php echo is_active('/contact.php', $currentPath) ? 'active' : ''; ?>"
               href="<?php echo $base_url; ?>/contact.php">Contact</a>
          </li>

          <!-- Categories -->
          <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle <?php echo (strpos($currentPath, '/category') === 0) ? 'active' : ''; ?>" href="#" id="categoriesDropdown" data-bs-toggle="dropdown" aria-expanded="false" role="button">
              Categories
            </a>
            <ul class="dropdown-menu shadow-sm border-0 rounded-3" aria-labelledby="categoriesDropdown">
              <?php foreach($catRows as $cr): if(!$cr['category']) continue; ?>
                <li>
                  <a class="dropdown-item py-2"
                    href="<?php echo $base_url; ?>/category.php/<?php echo urlencode($cr['category']); ?>">
                    <?php echo esc($cr['category']); ?>
                  </a>
                </li>
              <?php endforeach; ?>
            </ul>
          </li>

          <li class="nav-item">
            <a class="nav-link <?php echo is_active('/track.php', $currentPath) ? 'active' : ''; ?>"
               href="<?php echo $base_url; ?>/track.php">Track Order</a>
          </li>
        </ul>

        <!-- Right Buttons -->
        <div class="d-flex align-items-center gap-2 ms-3">

          <!-- Cart with Badge -->
          <a class="btn btn-outline-primary btn-sm px-3 position-relative d-flex align-items-center" href="<?php echo $base_url; ?>/cart.php" aria-label="View cart with <?php echo $cart_count; ?> items">
            <i class="bi bi-cart3 me-1" aria-hidden="true"></i> <span class="d-none d-sm-inline">Cart</span>
            <?php if($cart_count > 0): ?>
              <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger shadow-sm cart-badge" style="font-size:0.75rem;">
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

          <a class="btn btn-dark btn-sm px-3 rounded-pill admin-btn" href="<?php echo $base_url; ?>/admin" title="Admin Login">
            Admin
          </a>

        </div>
      </div>

    </div>
  </nav>
</header>

<!-- MOBILE SLIDE MENU (single mobile menu, used on small screens) -->
<nav id="mobileMenu" class="mobile-menu" aria-hidden="true" tabindex="-1" role="navigation" aria-label="Mobile menu">

  <div class="mobile-top-strip" aria-hidden="true"></div>

  <div class="mobile-header">
    <span class="fw-bold fs-5">Menu</span>
    <button id="closeMobileMenu" class="btn-close" aria-label="Close menu" type="button"></button>
  </div>

  <div class="mobile-inner" role="menu" aria-label="Main mobile menu">
    <a class="mm-item <?php echo is_active('/', $currentPath) ? 'active' : ''; ?>" href="<?php echo $base_url; ?>" role="menuitem">
      <i class="bi bi-house-door me-2" aria-hidden="true"></i> Home
    </a>

    <a class="mm-item <?php echo is_active('/product.php', $currentPath) ? 'active' : ''; ?>" href="<?php echo $base_url; ?>/product.php" role="menuitem">
      <i class="bi bi-bucket me-2" aria-hidden="true"></i> Products
    </a>

    <a class="mm-item <?php echo is_active('/about.php', $currentPath) ? 'active' : ''; ?>" href="<?php echo $base_url; ?>/about.php" role="menuitem">
      <i class="bi bi-info-circle me-2" aria-hidden="true"></i> About
    </a>

    <a class="mm-item <?php echo is_active('/contact.php', $currentPath) ? 'active' : ''; ?>" href="<?php echo $base_url; ?>/contact.php" role="menuitem">
      <i class="bi bi-telephone me-2" aria-hidden="true"></i> Contact
    </a>

    <a class="mm-item <?php echo is_active('/track.php', $currentPath) ? 'active' : ''; ?>" href="<?php echo $base_url; ?>/track.php" role="menuitem">
      <i class="bi bi-truck me-2" aria-hidden="true"></i> Track Order
    </a>

    <!-- Mobile Cart with Badge -->
    <a class="mm-item position-relative <?php echo is_active('/cart.php', $currentPath) ? 'active' : ''; ?>" href="<?php echo $base_url; ?>/cart.php" role="menuitem" aria-label="View cart with <?php echo $cart_count; ?> items">
      <i class="bi bi-cart3 me-2" aria-hidden="true"></i> Cart
      <?php if($cart_count > 0): ?>
        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger shadow-sm mobile-cart-badge">
          <?php echo $cart_count; ?>
          <span class="visually-hidden">items in cart</span>
        </span>
      <?php endif; ?>
    </a>

    <?php if(is_logged_in()): ?>
      <a class="mm-item <?php echo is_active('/profile.php', $currentPath) ? 'active' : ''; ?>" href="<?php echo $base_url; ?>/profile.php" role="menuitem">
        <i class="bi bi-person-circle me-2" aria-hidden="true"></i> Profile
      </a>
      <a class="mm-item text-danger" href="<?php echo $base_url; ?>/logout.php" role="menuitem">
        <i class="bi bi-box-arrow-right me-2" aria-hidden="true"></i> Logout
      </a>
    <?php else: ?>
      <a class="mm-item <?php echo is_active('/login.php', $currentPath) ? 'active' : ''; ?>" href="<?php echo $base_url; ?>/login.php" role="menuitem">
        <i class="bi bi-box-arrow-in-right me-2" aria-hidden="true"></i> Login
      </a>
      <a class="mm-item <?php echo is_active('/register.php', $currentPath) ? 'active' : ''; ?>" href="<?php echo $base_url; ?>/register.php" role="menuitem">
        <i class="bi bi-person-plus me-2" aria-hidden="true"></i> Register
      </a>
    <?php endif; ?>

    <div class="mobile-section">Categories</div>

    <?php foreach($catRows as $cr): if(!$cr['category']) continue; ?>
      <a class="mm-item <?php echo (strpos($currentPath, '/category') === 0 && strpos($currentPath, urlencode($cr['category'])) !== false) ? 'active' : ''; ?>"
         href="<?php echo $base_url; ?>/category.php?name=<?php echo urlencode($cr['category']); ?>" role="menuitem">
        <i class="bi bi-tag me-2" aria-hidden="true"></i> <?php echo esc($cr['category']); ?>
      </a>
    <?php endforeach; ?>
  </div>

</nav>

<style>
/* Glass Header */
.glass-header {
  backdrop-filter: blur(12px);
  background: rgba(255,255,255,0.75);
  border-bottom: 1px solid rgba(255,255,255,0.5);
  transition: background 0.3s ease, padding 0.2s ease;
  z-index: 1035;
  position: relative;
  overflow: visible; /* allow wave visible */
  padding-bottom: 46px; /* make room for wave so it doesn't overlap content */
}

/* Change header background on scroll */
.glass-header.scrolled {
  background: rgba(255,255,255,0.90);
  box-shadow: 0 6px 20px rgba(11,22,40,0.06);
  padding-bottom: 36px;
}

/* Brand text */
.brand-text {
  font-size: 1.25rem;
  font-weight: 700;
  color: #0b74ff;
  font-family: 'Inter', sans-serif;
  user-select: none;
  white-space: nowrap;
  transition: color 0.3s ease, font-size 0.2s ease;
  max-width: 220px;
  overflow: hidden;
  text-overflow: ellipsis;
}

/* Responsive logo */
.site-logo { height: 36px; width: auto; }

/* On small screens make brand smaller */
@media (max-width: 991px) {
  .brand-text { font-size: 1rem; max-width: 140px; }
  .site-logo { height: 30px; }
}

/* Desktop: ensure toggler hidden on large screens */
@media (min-width: 992px) {
  .navbar-toggler { display: none; } /* hide toggler on large screens */
  .navbar-collapse { display: flex !important; } /* ensure desktop menu visible */
}

/* Return/back button */
#backButton { border: none; background: rgba(255,255,255,0.9); box-shadow: 0 1px 6px rgba(11,22,40,0.06); }

/* Mobile Menu (off-canvas overlay) */
.mobile-menu {
  position: fixed;
  top: 0;
  left: -320px;
  width: 300px;
  max-width: 92%;
  height: 100vh;
  background: rgba(255,255,255,0.98);
  backdrop-filter: blur(14px);
  box-shadow: 6px 0 30px rgba(0,0,0,0.16);
  transition: left 0.32s ease;
  z-index: 1060;
  border-right: 1px solid rgba(0,0,0,0.04);
  overflow-y: auto;
  overscroll-behavior: contain;
}
.mobile-menu.open { left: 0; }

/* top strip & header */
.mobile-top-strip { height: 6px; background: linear-gradient(to right, #0b74ff, #4bb8ff, #00d4ff); }
.mobile-header { display:flex; justify-content:space-between; align-items:center; padding: 14px 16px; border-bottom:1px solid rgba(0,0,0,0.05); }
.mobile-header .btn-close { font-size: 1.2rem; color: #444; border: none; background: transparent; }

/* Menu items */
.mobile-inner { padding: 10px 0; display: flex; flex-direction: column; }
.mm-item { display:flex; align-items:center; padding: 12px 18px; font-size: 1rem; font-weight: 500; text-decoration: none; color:#222; transition: background 0.18s ease, transform 0.18s ease; border-radius: 6px; margin: 6px 12px; user-select: none; }
.mm-item:hover, .mm-item:focus { background: rgba(11,116,255,0.08); transform: translateX(6px); outline: none; }

/* active states */
.nav-link.active, .mm-item.active { color: #0b74ff !important; font-weight: 700; background: rgba(11,116,255,0.06); }

/* Floating Wave always visible and not interfering with clicks */
.header-wave-bg {
  position: absolute;
  bottom: 0;
  left: -60%;
  width: 220%;
  height: 56px;
  background:
    url('data:image/svg+xml;utf8,<svg viewBox="0 0 1200 80" xmlns="http://www.w3.org/2000/svg" preserveAspectRatio="none"><path fill="%230b74ff" fill-opacity="0.28" d="M0 30 C150 80 350 0 600 30 C850 60 1050 10 1200 30 V80 H0 Z" /></svg>') repeat-x;
  background-size: 60% 100%;
  animation: waveMove 8s linear infinite;
  pointer-events: none;
  z-index: 0;
  transform: translateZ(0);
}
@keyframes waveMove { from { background-position-x: 0; } to { background-position-x: 1600px; } }

.cart-badge, .mobile-cart-badge { z-index: 5; }
.admin-btn { white-space: nowrap; }
.navbar-toggler { z-index: 1070; }

/* --- Make full brand name visible on very small screens --- */
@media (max-width: 576px) {
  /* Allow the brand text to wrap and take full space on mobile */
  .brand-link { display: inline-flex; align-items: center; gap: 8px; }
  .brand-text {
    white-space: normal !important;    /* allow wrapping */
    overflow: visible !important;     /* no clipping */
    text-overflow: clip !important;
    max-width: none !important;       /* remove previously set max-width */
    display: inline-block;
    font-size: 1rem;                  /* slightly smaller to fit but still readable */
    line-height: 1.05;
  }

  /* Slightly reduce logo height so the combined width fits better */
  .site-logo { height: 30px; }

  /* If the header becomes too crowded, allow brand to move to a new line */
  .navbar .container { gap: 6px; flex-wrap: wrap; }
}

/* Ensure mobile overlay does not create page horizontal scroll */
.mobile-menu,
.header-wave-bg,
.glass-header {
  max-width: 100%;
  box-sizing: border-box;
}
</style>

<script>
(function(){
  function $id(id){ return document.getElementById(id); }

  // Back button behavior
  var backBtn = $id('backButton');
  if(backBtn){
    backBtn.addEventListener('click', function(e){
      e.preventDefault();
      if(window.history && window.history.length > 1){
        window.history.back();
      } else {
        window.location.href = '<?php echo $base_url; ?>/';
      }
    });
  }

  // Mobile menu open/close (overlay)
  var mobileMenu = $id('mobileMenu');
  var openBtn = $id('openMobileMenu');
  var closeBtn = $id('closeMobileMenu');
  var body = document.body;
  var lastFocused = null;

  function openMenu(){
    if(!mobileMenu) return;
    mobileMenu.classList.add('open');
    mobileMenu.setAttribute('aria-hidden','false');
    body.style.overflow = 'hidden';
    if(!document.getElementById('mobileMenuOverlay')){
      var ov = document.createElement('div');
      ov.id = 'mobileMenuOverlay';
      ov.style.position = 'fixed';
      ov.style.top = 0;
      ov.style.left = 0;
      ov.style.right = 0;
      ov.style.bottom = 0;
      ov.style.background = 'rgba(0,0,0,0.24)';
      ov.style.zIndex = 1055;
      ov.addEventListener('click', closeMenu);
      document.body.appendChild(ov);
    }
    lastFocused = document.activeElement;
    var first = mobileMenu.querySelector('[role="menuitem"]');
    if(first) first.focus();
    if(openBtn) openBtn.setAttribute('aria-expanded','true');
  }

  function closeMenu(){
    if(!mobileMenu) return;
    mobileMenu.classList.remove('open');
    mobileMenu.setAttribute('aria-hidden','true');
    body.style.overflow = '';
    var ov = document.getElementById('mobileMenuOverlay');
    if(ov) ov.remove();
    if(lastFocused) lastFocused.focus();
    if(openBtn) openBtn.setAttribute('aria-expanded','false');
  }

  if(openBtn){
    openBtn.addEventListener('click', function(e){
      // Use overlay only on narrow viewports (<992). On large screens, Bootstrap will handle collapse.
      if(window.innerWidth < 992){
        e.preventDefault();
        openMenu();
      } else {
        // allow default bootstrap collapse toggle to work on >=992 if needed
        var navMain = document.getElementById('navMain');
        if(navMain) navMain.classList.toggle('show');
      }
    });
  }
  if(closeBtn){
    closeBtn.addEventListener('click', function(e){
      e.preventDefault();
      closeMenu();
    });
  }

  // close on ESC
  document.addEventListener('keydown', function(e){
    if(e.key === 'Escape' || e.key === 'Esc'){
      closeMenu();
    }
  });

  // header scroll class
  window.addEventListener('scroll', function(){
    var header = document.querySelector('.glass-header');
    if(!header) return;
    if(window.scrollY > 30) header.classList.add('scrolled');
    else header.classList.remove('scrolled');
  });

  // make sure overlay closes when resizing to large screen
  window.addEventListener('resize', function(){
    if(window.innerWidth >= 992){
      if(mobileMenu && mobileMenu.classList.contains('open')) closeMenu();
    }
  });

})();
</script>
