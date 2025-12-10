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

  <nav class="navbar navbar-expand-lg navbar-light py-3 curved-nav" role="navigation" aria-label="Main navigation">
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
          <a class="btn btn-outline-primary btn-sm px-3 position-relative d-flex align-items-center floating-btn"
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
            <a class="btn btn-link fw-semibold text-truncate user-link" 
               href="<?php echo $base_url; ?>/profile.php"
               title="<?php echo esc($_SESSION['user_name']); ?>">
              <?php echo esc($_SESSION['user_name']); ?>
            </a>
            <a class="btn btn-link text-danger"
               href="<?php echo $base_url; ?>/logout.php">Logout</a>
          <?php elseif(is_admin_logged_in()): ?>
            <span class="btn btn-link fw-semibold" title="Admin">
              <?php echo $_SESSION['admin_name'] ?? 'Admin'; ?>
            </span>
            <a class="btn btn-link text-danger"
               href="<?php echo $base_url; ?>/admin/logout.php">Logout</a>
          <?php else: ?>
            <a class="btn btn-primary btn-sm px-3"
               href="<?php echo $base_url; ?>/login.php">Login</a>
            <a class="btn btn-outline-secondary btn-sm px-3"
               href="<?php echo $base_url; ?>/register.php">Register</a>
          <?php endif; ?>

   <!--       <a class="btn btn-admin btn-sm px-3 rounded-pill admin-btn floating-btn"
             href="<?php echo $base_url; ?>/admin" title="Admin Login">
            Admin
          </a> -->

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
    <?php elseif(is_admin_logged_in()): ?>
      <a class="mm-item" role="menuitem">
        <i class="bi bi-shield-lock me-2" aria-hidden="true"></i> Admin: <?php echo $_SESSION['admin_name'] ?? 'Admin'; ?>
      </a>
      <a class="mm-item text-danger"
         href="<?php echo $base_url; ?>/admin/logout.php" role="menuitem">
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
/* -----------------------------
   APPLE-STYLE: PREMIUM GLASS NAV
   UI ONLY — logic unchanged
   ----------------------------- */

/* Inter font for crisp look */
@import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&display=swap');

:root{
  --bg-start: #f8fbff;
  --bg-end: #eef7ff;
  --glass-accent: #00d4ff;
  --neon: #0b74ff;
  --muted: #6c7684;
  --glass-border: rgba(11,116,255,0.08);
  --glass-strong: rgba(11,116,255,0.12);
  --shadow-strong: rgba(11,116,255,0.12);
}

/* Header base */
.glass-header {
  font-family: 'Inter', system-ui, -apple-system, "Segoe UI", Roboto, "Helvetica Neue", Arial;
  background: linear-gradient(180deg, var(--bg-start), var(--bg-end));
  backdrop-filter: blur(12px) saturate(130%);
  -webkit-backdrop-filter: blur(12px) saturate(130%);
  border: none;
  padding: 18px 0 14px;
  transition: padding 220ms ease, box-shadow 220ms ease, transform 220ms ease;
  z-index: 1040;
  position: relative;
}

/* Curved navbar wrapper */
.curved-nav {
  background: rgba(255,255,255,0.6);
  border-radius: 14px;
  padding: 10px 18px;
  width: 100%;
  box-shadow: 0 8px 30px rgba(9,30,66,0.06), 0 1px 0 rgba(255,255,255,0.6) inset;
  border: 1px solid var(--glass-border);
  align-items: center;
}

/* shrink header on scroll */
.glass-header.scrolled { padding: 8px 0 8px; transform: translateY(-2px); box-shadow: 0 10px 40px rgba(8,28,60,0.06); }

/* Logo + brand */
.brand-link { display:inline-flex; align-items:center; gap:12px; z-index:1060; }
.site-logo { height:44px; width:auto; transition: height 160ms ease; }
.brand-text { font-weight:800; color: #08284f; font-size:1.15rem; letter-spacing:-0.02em; }

/* Nav links (desktop) */
.navbar-nav .nav-link {
  color: #0e2b56;
  font-weight:600;
  padding: 8px 12px;
  border-radius:10px;
  transition: transform 160ms ease, box-shadow 160ms ease, color 120ms ease;
}
.navbar-nav .nav-link:hover {
  transform: translateY(-3px);
  box-shadow: 0 8px 26px rgba(11,116,255,0.08);
  background: linear-gradient(90deg, rgba(11,116,255,0.06), rgba(0,212,255,0.02));
}
.navbar-nav .nav-link.active {
  color: var(--neon);
  background: linear-gradient(90deg, rgba(11,116,255,0.08), rgba(0,212,255,0.02));
  box-shadow: inset 0 -2px 0 rgba(11,116,255,0.02);
}

/* Dropdown menu */
.dropdown-menu {
  border-radius: 12px;
  padding: 6px;
  border: 1px solid rgba(11,116,255,0.06);
  box-shadow: 0 14px 40px rgba(6,22,48,0.06);
  min-width: 200px;
}

/* Buttons: floating style */
.floating-btn {
  transform: translateY(-2px);
  border-radius: 12px;
  padding: 8px 14px;
  transition: transform 160ms ease, box-shadow 160ms ease;
}
.floating-btn:hover {
  transform: translateY(-6px);
  box-shadow: 0 14px 40px var(--shadow-strong);
}

/* Outline primary (cart) */
.btn-outline-primary {
  background: linear-gradient(180deg, rgba(255,255,255,0.6), rgba(255,255,255,0.5));
  border: 1px solid rgba(11,116,255,0.08);
  color: var(--neon);
}
.btn-outline-primary .bi { opacity: 0.95; }

/* Primary buttons */
.btn-primary {
  background: linear-gradient(90deg, var(--neon), var(--glass-accent));
  border: none;
  color: #fff;
  padding: 8px 14px;
  border-radius: 10px;
  box-shadow: 0 10px 30px rgba(11,116,255,0.12);
}

/* Admin variant */
.btn-admin {
  background: linear-gradient(90deg, #02203b, var(--neon));
  color: #fff;
  box-shadow: 0 10px 30px rgba(11,116,255,0.10);
}

/* user link */
.user-link { color: #0b3a66; margin-right: 6px; }

/* badges */
.cart-badge, .mobile-cart-badge {
  transform: translate(30%, -30%);
  font-weight:700;
  border: 2px solid white;
}

/* Mobile: compact */
@media (max-width: 991px) {
  .site-logo { height:36px; }
  .brand-text { font-size:1rem; max-width:140px; overflow:hidden; text-overflow:ellipsis; white-space:nowrap; }
  .curved-nav { padding: 8px 12px; border-radius: 12px; }
}

/* Mobile menu panel */
.mobile-menu {
  position: fixed;
  top: 0;
  left: -320px;
  width: 320px;
  max-width: 92%;
  height: 100vh;
  background: linear-gradient(180deg, rgba(255,255,255,0.98), rgba(246,252,255,0.98));
  backdrop-filter: blur(14px);
  box-shadow: 6px 0 40px rgba(0,0,0,0.16);
  transition: left 0.32s cubic-bezier(.2,.9,.3,1);
  z-index: 1060;
  border-right: 1px solid rgba(11,116,255,0.04);
  overflow-y: auto;
}
.mobile-menu.open { left: 0; }

/* mobile strip */
.mobile-top-strip { height:6px; background: linear-gradient(90deg, var(--neon), var(--glass-accent)); }

/* mobile items */
.mm-item { display:block; padding:12px 18px; font-size:1rem; font-weight:600; color:#143a65; margin:8px 12px; border-radius:10px; }
.mm-item:hover { background: rgba(11,116,255,0.06); transform: translateX(8px); }

/* active */
.nav-link.active, .mm-item.active { color: var(--neon) !important; background: rgba(11,116,255,0.06); font-weight:700; }

/* focus */
a:focus, button:focus { outline: 3px solid rgba(11,116,255,0.12); outline-offset: 3px; border-radius: 8px; }

/* small screens tweak */
@media (max-width: 575px) {
  .brand-text { font-size: 0.95rem; }
  .site-logo { height: 32px; }
  .btn-primary, .btn-outline-primary { padding: 6px 10px; font-size: 0.95rem; }
}
</style>

<script>
(function(){
  function $id(id){ return document.getElementById(id); }

  // Back button behavior (unchanged behavior)
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

  // Mobile menu open/close (preserved behavior)
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
      ov.style.background = 'rgba(0,0,0,0.22)';
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

  // Header shrink on scroll with subtle neon line
  window.addEventListener('scroll', function(){
    var header = document.querySelector('.glass-header');
    if(!header) return;
    if(window.scrollY > 28) header.classList.add('scrolled');
    else header.classList.remove('scrolled');
  });

  // Close mobile menu on resize to large screen
  window.addEventListener('resize', function(){
    if(window.innerWidth >= 992){
      if(mobileMenu && mobileMenu.classList.contains('open')) closeMenu();
    }
  });

  // small ARIA improvement: focus trap fallback
  document.addEventListener('focusin', function(e){
    if(mobileMenu && mobileMenu.classList.contains('open')){
      if(!mobileMenu.contains(e.target) && e.target.id !== 'openMobileMenu'){
        mobileMenu.querySelector('[role="menuitem"]')?.focus();
      }
    }
  });

})();
</script>
