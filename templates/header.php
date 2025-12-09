<?php
// Ensure session is started before using $_SESSION in real project
// session_start();

// Get cart count safely
$cart = $_SESSION['cart'] ?? [];
if (!is_array($cart)) {
    $cart = [];
}
$cart_count = array_sum($cart); // total quantity of items (simple quantity array)

// current request path for active link detection
$currentPath = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

/**
 * Check if a given path or list of paths should be considered active
 * $matchPaths can be string or array.
 * Always pass **paths**, NOT full URLs.
 *
 * - Exact match: "/about.php" === "/about.php"
 * - Prefix match: "/category" matches "/category/something"
 */
function is_active($matchPaths, $currentPath) {
    foreach ((array)$matchPaths as $m) {
        if (!$m) continue;

        // Normalize to path form: "/something"
        $path = parse_url($m, PHP_URL_PATH) ?: $m;
        $mNorm = '/' . ltrim($path, '/');

        // Exact match
        if ($mNorm === $currentPath) {
            return true;
        }

        // Prefix match, for things like "/category"
        $prefix = rtrim($mNorm, '/');
        if ($prefix !== '' && strpos($currentPath, $prefix) === 0) {
            return true;
        }
    }
    return false;
}
?>

<header class="glass-header sticky-top shadow-sm" role="banner" id="siteHeader">

  <!-- Wave Background (covers full header, non-interactive) -->
  <div class="header-wave-bg" aria-hidden="true"></div>

  <nav class="navbar navbar-expand-lg navbar-light py-2" role="navigation" aria-label="Main navigation">
    <div class="container d-flex align-items-center">

      <!-- Return (back) button - visible on mobile and desktop -->
      <button id="backButton" class="btn btn-water btn-sm me-2 d-flex align-items-center" aria-label="Go back" title="Go back" type="button">
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

      <!-- Mobile Menu Button -->
      <button class="navbar-toggler btn btn-water-outline ms-auto d-lg-none" type="button" id="openMobileMenu" aria-label="Open menu" aria-controls="mobileMenu" aria-expanded="false">
        <i class="bi bi-list fs-3" aria-hidden="true"></i>
      </button>

      <!-- Desktop Menu -->
      <div class="collapse navbar-collapse d-lg-flex justify-content-end" id="navMain" aria-label="Desktop navigation">
        <ul class="navbar-nav ms-lg-3 mb-2 mb-lg-0 align-items-lg-center">

          <!-- Home - special handling, no is_active('/') to avoid matching all -->
          <li class="nav-item">
            <a class="nav-link modern-link <?php echo in_array($currentPath, ['/', '/index.php']) ? 'active' : ''; ?>"
               href="<?php echo $base_url; ?>/">Home</a>
          </li>

          <!-- Products -->
          <li class="nav-item">
            <a class="nav-link modern-link <?php echo is_active('/product.php', $currentPath) ? 'active' : ''; ?>"
               href="<?php echo $base_url; ?>/product.php">Products</a>
          </li>

          <!-- About -->
          <li class="nav-item">
            <a class="nav-link modern-link <?php echo is_active('/about.php', $currentPath) ? 'active' : ''; ?>"
               href="<?php echo $base_url; ?>/about.php">About</a>
          </li>

          <!-- Contact -->
          <li class="nav-item">
            <a class="nav-link modern-link <?php echo is_active('/contact.php', $currentPath) ? 'active' : ''; ?>"
               href="<?php echo $base_url; ?>/contact.php">Contact</a>
          </li>

          <!-- Categories -->
          <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle <?php echo is_active('/category', $currentPath) ? 'active' : ''; ?>"
               href="#" id="categoriesDropdown" data-bs-toggle="dropdown" aria-expanded="false" role="button">
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

          <!-- Track Order -->
          <li class="nav-item">
            <a class="nav-link modern-link <?php echo is_active('/track.php', $currentPath) ? 'active' : ''; ?>"
               href="<?php echo $base_url; ?>/track.php">Track Order</a>
          </li>
        </ul>

        <!-- Right Buttons -->
        <div class="d-flex align-items-center gap-2 ms-3">

          <!-- Cart with Badge -->
          <a class="btn btn-outline-primary btn-sm px-3 position-relative d-flex align-items-center"
             href="<?php echo $base_url; ?>/cart.php"
             aria-label="View cart with <?php echo $cart_count; ?> items">
            <i class="bi bi-cart3 me-1" aria-hidden="true"></i>
            <span class="d-none d-sm-inline">Cart</span>
            <?php if($cart_count > 0): ?>
              <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger shadow-sm cart-badge" style="font-size:0.75rem;">
                <?php echo $cart_count; ?>
                <span class="visually-hidden">items in cart</span>
              </span>
            <?php endif; ?>
          </a>

          <?php if(is_logged_in()): ?>
            <a class="btn btn-link fw-semibold text-truncate"
               href="<?php echo $base_url; ?>/profile.php"
               title="<?php echo esc($_SESSION['user_name']); ?>">
              <?php echo esc($_SESSION['user_name']); ?>
            </a>
            <a class="btn btn-link text-danger"
               href="<?php echo $base_url; ?>/logout.php">Logout</a>
          <?php else: ?>
            <a class="btn btn-primary btn-sm px-3"
               href="<?php echo $base_url; ?>/login.php">Login</a>
            <a class="btn btn-outline-secondary btn-sm px-3"
               href="<?php echo $base_url; ?>/register.php">Register</a>
          <?php endif; ?>

          <a class="btn btn-dark btn-sm px-3 rounded-pill admin-btn"
             href="<?php echo $base_url; ?>/admin" title="Admin Login">
            Admin
          </a>

        </div>
      </div>

    </div>
  </nav>
</header>

<!-- MOBILE SLIDE MENU -->
<nav id="mobileMenu" class="mobile-menu" aria-hidden="true" tabindex="-1" aria-label="Mobile menu">

  <div class="mobile-top-strip" aria-hidden="true"></div>

  <div class="mobile-header">
    <span class="fw-bold fs-5">Menu</span>
    <button id="closeMobileMenu" class="btn-close" aria-label="Close menu" type="button"></button>
  </div>

  <div class="mobile-inner" role="menu" aria-label="Main mobile menu">

    <!-- Home (same logic as desktop) -->
    <a class="mm-item <?php echo in_array($currentPath, ['/', '/index.php']) ? 'active' : ''; ?>"
       href="<?php echo $base_url; ?>/" role="menuitem">
      <i class="bi bi-house-door me-2" aria-hidden="true"></i> Home
    </a>

    <!-- Products -->
    <a class="mm-item <?php echo is_active('/product.php', $currentPath) ? 'active' : ''; ?>"
       href="<?php echo $base_url; ?>/product.php" role="menuitem">
      <i class="bi bi-bucket me-2" aria-hidden="true"></i> Products
    </a>

    <!-- About -->
    <a class="mm-item <?php echo is_active('/about.php', $currentPath) ? 'active' : ''; ?>"
       href="<?php echo $base_url; ?>/about.php" role="menuitem">
      <i class="bi bi-info-circle me-2" aria-hidden="true"></i> About
    </a>

    <!-- Contact -->
    <a class="mm-item <?php echo is_active('/contact.php', $currentPath) ? 'active' : ''; ?>"
       href="<?php echo $base_url; ?>/contact.php" role="menuitem">
      <i class="bi bi-telephone me-2" aria-hidden="true"></i> Contact
    </a>

    <!-- Track Order -->
    <a class="mm-item <?php echo is_active('/track.php', $currentPath) ? 'active' : ''; ?>"
       href="<?php echo $base_url; ?>/track.php" role="menuitem">
      <i class="bi bi-truck me-2" aria-hidden="true"></i> Track Order
    </a>

    <!-- Mobile Cart with Badge -->
    <a class="mm-item position-relative <?php echo is_active('/cart.php', $currentPath) ? 'active' : ''; ?>"
       href="<?php echo $base_url; ?>/cart.php" role="menuitem"
       aria-label="View cart with <?php echo $cart_count; ?> items">
      <i class="bi bi-cart3 me-2" aria-hidden="true"></i> Cart
      <?php if($cart_count > 0): ?>
        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger shadow-sm mobile-cart-badge">
          <?php echo $cart_count; ?>
          <span class="visually-hidden">items in cart</span>
        </span>
      <?php endif; ?>
    </a>

    <?php if(is_logged_in()): ?>
      <a class="mm-item <?php echo is_active('/profile.php', $currentPath) ? 'active' : ''; ?>"
         href="<?php echo $base_url; ?>/profile.php" role="menuitem">
        <i class="bi bi-person-circle me-2" aria-hidden="true"></i> Profile
      </a>
      <a class="mm-item text-danger"
         href="<?php echo $base_url; ?>/logout.php" role="menuitem">
        <i class="bi bi-box-arrow-right me-2" aria-hidden="true"></i> Logout
      </a>
    <?php else: ?>
      <a class="mm-item <?php echo is_active('/login.php', $currentPath) ? 'active' : ''; ?>"
         href="<?php echo $base_url; ?>/login.php" role="menuitem">
        <i class="bi bi-box-arrow-in-right me-2" aria-hidden="true"></i> Login
      </a>
      <a class="mm-item <?php echo is_active('/register.php', $currentPath) ? 'active' : ''; ?>"
         href="<?php echo $base_url; ?>/register.php" role="menuitem">
        <i class="bi bi-person-plus me-2" aria-hidden="true"></i> Register
      </a>
    <?php endif; ?>

    <div class="mobile-section">Categories</div>

    <?php foreach($catRows as $cr): if(!$cr['category']) continue; ?>
      <?php
        // Active if URL is /category/<urlencoded-category>...
        $catPath = '/category/' . urlencode($cr['category']);
        $isCatActive = is_active($catPath, $currentPath);
      ?>
      <a class="mm-item <?php echo $isCatActive ? 'active' : ''; ?>"
         href="<?php echo $base_url; ?>/category/<?php echo urlencode($cr['category']); ?>" role="menuitem">
        <i class="bi bi-tag me-2" aria-hidden="true"></i> <?php echo esc($cr['category']); ?>
      </a>
    <?php endforeach; ?>

  </div>

</nav>

<style>
/* -------- Water / Glass header styling -------- */
:root{
  --water-1: #e8fbff; /* very light aqua */
  --water-2: #d8f7ff; /* soft aqua */
  --water-accent: #006fe6; /* accent */
  --water-deep: #0b74ff; /* used in wave */
}

/* Glass Header */
.glass-header {
  backdrop-filter: blur(10px);
  background: linear-gradient(180deg, rgba(232,251,255,0.92) 0%, rgba(216,247,255,0.88) 100%);
  border-bottom: 1px solid rgba(13,108,128,0.06);
  transition: background 0.25s ease, padding 0.18s ease;
  z-index: 1035;
  position: relative;
  overflow: visible;
  padding-bottom: 56px; /* room for wave visual */
}

/* On scroll slightly darker */
.glass-header.scrolled {
  background: linear-gradient(180deg, rgba(216,247,255,0.98) 0%, rgba(200,238,250,0.96) 100%);
  box-shadow: 0 6px 20px rgba(11,22,40,0.06);
  padding-bottom: 46px;
}

/* Brand text */
.brand-text {
  font-size: 1.25rem;
  font-weight: 700;
  color: var(--water-deep);
  font-family: 'Inter', sans-serif;
  user-select: none;
  white-space: nowrap;
  transition: color 0.25s ease, font-size 0.18s ease;
  max-width: 220px;
  overflow: hidden;
  text-overflow: ellipsis;
}

/* Responsive logo */
.site-logo { height: 36px; width: auto; }

/* Back button style */
.btn-water {
  background: linear-gradient(180deg, rgba(0,183,230,0.12), rgba(11,116,255,0.06));
  border: 1px solid rgba(11,116,255,0.12);
  color: var(--water-deep);
  box-shadow: 0 1px 6px rgba(11,22,40,0.04);
}
.btn-water:active,
.btn-water:focus {
  box-shadow: 0 2px 10px rgba(11,22,40,0.06);
  outline: none;
}

/* Toggler (menu) solid outline variant for mobile */
.btn-water-outline {
  background: #ffffff;
  border: 1px solid rgba(11,116,255,0.12);
  color: var(--water-deep);
  padding: .35rem .6rem;
  border-radius: .35rem;
  box-shadow: 0 1px 6px rgba(11,22,40,0.04);
}

/* Desktop: hide toggler, show nav */
@media (min-width: 992px) {
  .navbar-toggler { display: none; }
  .navbar-collapse { display: flex !important; }
}

/* Mobile friendly brand wrap */
@media (max-width: 991px) {
  .brand-text {
    font-size: 1rem;
    max-width: 140px;
    color: var(--water-deep);
  }
  .site-logo { height: 30px; }
  .navbar .container {
    gap: 6px;
    flex-wrap: wrap;
    align-items: center;
  }
}

/* Floating Wave background */
.header-wave-bg {
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background:
    url('data:image/svg+xml;utf8,<svg viewBox="0 0 1200 220" xmlns="http://www.w3.org/2000/svg" preserveAspectRatio="none"><path fill="%2300b7e6" fill-opacity="0.10" d="M0 110 C150 180 350 40 600 110 C850 180 1050 30 1200 110 V220 H0 Z" /></svg>') repeat-x;
  background-size: 60% 100%;
  animation: waveMove 10s linear infinite;
  pointer-events: none;
  z-index: 0;
  transform: translateZ(0);
}

/* Ensure nav content is above wave */
.glass-header .navbar,
.glass-header .container,
.glass-header .brand-link,
.glass-header .navbar-nav,
.glass-header .btn {
  position: relative;
  z-index: 1080;
}

/* Wave movement */
@keyframes waveMove {
  from { background-position-x: 0; }
  to   { background-position-x: 2000px; }
}

.cart-badge,
.mobile-cart-badge { z-index: 1090; }

.admin-btn { white-space: nowrap; }
.navbar-toggler { z-index: 1090; }

/* Small screens: show full brand text */
@media (max-width: 576px) {
  .brand-link {
    display: inline-flex;
    align-items: center;
    gap: 8px;
  }
  .brand-text {
    white-space: normal !important;
    overflow: visible !important;
    text-overflow: clip !important;
    max-width: none !important;
    display: inline-block;
    font-size: 1rem;
    line-height: 1.05;
  }
  .site-logo { height: 30px; }
}

/* Mobile menu */
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

.mobile-top-strip {
  height: 6px;
  background: linear-gradient(to right, #0b74ff, #4bb8ff, #00d4ff);
}
.mobile-header {
  display:flex;
  justify-content:space-between;
  align-items:center;
  padding: 14px 16px;
  border-bottom:1px solid rgba(0,0,0,0.05);
}
.mobile-header .btn-close {
  font-size: 1.2rem;
  color: #444;
  border: none;
  background: transparent;
}

.mm-item {
  display:flex;
  align-items:center;
  padding: 12px 18px;
  font-size: 1rem;
  font-weight: 500;
  text-decoration: none;
  color:#222;
  transition: background 0.18s ease, transform 0.18s ease;
  border-radius: 6px;
  margin: 6px 12px;
  user-select: none;
}
.mm-item:hover,
.mm-item:focus {
  background: rgba(11,116,255,0.08);
  transform: translateX(6px);
  outline: none;
}

/* Active states */
.nav-link.active,
.mm-item.active {
  color: var(--water-deep) !important;
  font-weight: 700;
  background: rgba(11,116,255,0.06);
}

/* Prevent horizontal scroll */
.mobile-menu,
.header-wave-bg,
.glass-header {
  max-width: 100%;
  box-sizing: border-box;
}

.mobile-section {
  margin: 12px 16px 4px;
  font-size: 0.92rem;
  font-weight: 600;
  text-transform: uppercase;
  letter-spacing: 0.04em;
  color: #666;
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

  // Mobile menu open/close
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
      if(window.innerWidth < 992){
        e.preventDefault();
        openMenu();
      } else {
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

  // Close on ESC
  document.addEventListener('keydown', function(e){
    if(e.key === 'Escape' || e.key === 'Esc'){
      closeMenu();
    }
  });

  // Header scroll class
  window.addEventListener('scroll', function(){
    var header = document.querySelector('.glass-header');
    if(!header) return;
    if(window.scrollY > 30) header.classList.add('scrolled');
    else header.classList.remove('scrolled');
  });

  // Close mobile menu when resizing to large screen
  window.addEventListener('resize', function(){
    if(window.innerWidth >= 992){
      if(mobileMenu && mobileMenu.classList.contains('open')) closeMenu();
    }
  });

})();
</script>
