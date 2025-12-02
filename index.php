<?php
require_once 'init.php';

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// fetch products with optional category or search filter
$category = isset($_GET['category'])? trim($_GET['category']) : '';
$q = isset($_GET['q'])? trim($_GET['q']) : '';
if($q && $category){
  $stmt = $pdo->prepare('SELECT id,name,price,thumbnail,stock FROM products WHERE category = ? AND (name LIKE ? OR description LIKE ?) ORDER BY created_at DESC LIMIT 50');
  $like = "%$q%";
  $stmt->execute([$category, $like, $like]);
} elseif($q){
  $stmt = $pdo->prepare('SELECT id,name,price,thumbnail,stock FROM products WHERE (name LIKE ? OR description LIKE ?) ORDER BY created_at DESC LIMIT 50');
  $like = "%$q%";
  $stmt->execute([$like, $like]);
} elseif($category){
  $stmt = $pdo->prepare('SELECT id, name, price, thumbnail, stock FROM products WHERE category = ? ORDER BY created_at DESC LIMIT 50');
  $stmt->execute([$category]);
} else {
  $stmt = $pdo->query('SELECT id, name, price, thumbnail, stock FROM products ORDER BY created_at DESC LIMIT 50');
}
$products = $stmt->fetchAll();
// page header
?><!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Vetriarasiwatersupply - Reliable Water Solutions</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
  <link rel="stylesheet" href="<?php echo $base_url; ?>/assets/css/custom.css">

  <!-- YOUR EXISTING STYLES -->
  <style>
    .hero-banner {
      background: linear-gradient(135deg, #138ec7ff 0%, #071215ff 100%);
      position: relative;
      overflow: hidden;
    }

    .hero-banner::before {
      content: '';
      position: absolute;
      width: 400px;
      height: 400px;
      background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 70%);
      border-radius: 50%;
      top: -100px;
      right: -100px;
      animation: float 6s ease-in-out infinite;
    }

    .hero-banner::after {
      content: '';
      position: absolute;
      width: 300px;
      height: 300px;
      background: radial-gradient(circle, rgba(255,255,255,0.05) 0%, transparent 70%);
      border-radius: 50%;
      bottom: -50px;
      left: -50px;
      animation: float 8s ease-in-out infinite reverse;
    }

    .hero-content {
      position: relative;
      z-index: 1;
    }

    @keyframes float {
      0%, 100% { transform: translateY(0px); }
      50% { transform: translateY(20px); }
    }

    .hero-icon {
      font-size: 3rem;
      animation: slideInUp 0.8s ease-out 0.2s both;
    }

    .categories-card {
      border-left: 4px solid #138ec7ff;
      transition: all 0.3s ease;
      animation: slideInUp 0.6s ease-out;
    }

    .categories-card:hover {
      box-shadow: 0 8px 20px rgba(34, 197, 94, 0.15);
      transform: translateX(5px);
    }

    .categories-card h6 {
      color: #138ec7ff;
      font-weight: 700;
    }

    .categories-card ul li {
      padding: 8px 0;
      border-bottom: 1px solid #f0fdf4;
      transition: all 0.3s ease;
    }

    .categories-card ul li:last-child {
      border-bottom: none;
    }

    .categories-card a {
      color: #374151;
      text-decoration: none;
      transition: all 0.3s ease;
      display: inline-block;
    }

    .categories-card a:hover {
      color: #138ec7ff;
      margin-left: 5px;
      font-weight: 600;
    }

    .products-section {
      animation: slideInUp 0.7s ease-out 0.1s both;
    }

    .products-section h5 {
      color: #1f2937;
      font-weight: 700;
      padding-bottom: 15px;
      border-bottom: 3px solid #138ec7ff;
      margin-bottom: 25px;
      display: inline-block;
    }

    .badge-low-stock {
      background: #fef3c7 !important;
      color: #92400e !important;
      border: 1px solid #f59e0b;
      animation: pulse-glow 2s infinite;
    }

    .product-card-wrapper {
      transition: all 0.3s ease;
    }

    .product-card-wrapper:hover {
      transform: translateY(-3px);
    }

    @keyframes pulse-glow {
      0% { box-shadow: 0 0 0 0 rgba(34, 197, 94, 0.4); }
      70% { box-shadow: 0 0 0 8px rgba(34, 197, 94, 0); }
      100% { box-shadow: 0 0 0 0 rgba(34, 197, 94, 0); }
    }

    @keyframes slideInUp {
      from {
        opacity: 0;
        transform: translateY(20px);
      }
      to {
        opacity: 1;
        transform: translateY(0);
      }
    }
  </style>

  <!-- UI ENHANCEMENTS (non-invasive; paste here to enhance appearance only) -->
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700;800&display=swap" rel="stylesheet">
  <style>
  :root{
    --brand: #0b74ff;
    --accent: #138ec7;
    --muted: #6b7280;
    --success: #10b981;
    --danger: #ef4444;
    --glass: rgba(255,255,255,0.7);
    --card-radius: 0.6rem;
    --transition-fast: 180ms;
  }

  body { font-family: "Inter", system-ui, -apple-system, "Segoe UI", Roboto, "Helvetica Neue", Arial; color: #0f1724; }

  .container.py-4 { max-width: 1180px; }

  .hero-banner, .hero {
    border-radius: calc(var(--card-radius) + 0.15rem);
    padding: 2.2rem;
    background-image: linear-gradient(135deg, rgba(11,116,255,0.95), rgba(6,35,67,0.95));
    box-shadow: 0 20px 50px rgba(6,35,67,0.12);
    color: #fff;
  }
  .hero-banner h2, .hero h1 { letter-spacing: -0.5px; font-weight:800; font-size: clamp(1.4rem, 2.6vw, 2.5rem); }
  .hero-banner p, .hero p { opacity: .95; font-size: 1rem; }

  .btn-hero {
    background: linear-gradient(90deg,var(--brand),#00d4ff);
    color: #fff;
    border: none;
    padding: .6rem .9rem;
    box-shadow: 0 8px 24px rgba(11,116,255,0.12);
    transition: transform var(--transition-fast), box-shadow var(--transition-fast);
  }
  .btn-hero:hover { transform: translateY(-4px); box-shadow: 0 14px 34px rgba(11,116,255,0.18); }

  .card-product {
    border-radius: var(--card-radius);
    overflow: hidden;
    transition: transform .28s cubic-bezier(.2,.9,.2,1), box-shadow .28s;
    border: 0;
    background: linear-gradient(180deg,#fff,#fbfdff 70%);
  }
  .card-product:hover { transform: translateY(-8px) scale(1.01); box-shadow: 0 26px 50px rgba(8,20,40,0.08); }

  .card-product .card-body { padding: 1rem; }
  .card-product .card-title { font-weight: 700; font-size: .98rem; color:#0f1724; margin-bottom: .35rem; }
  .card-product .thumbnail { height: 220px; width:100%; object-fit: cover; display:block; transform-origin:center; transition: transform .45s ease, filter .45s ease, opacity .45s ease; filter: contrast(1.02) saturate(1.06); }

  .img-placeholder {
    background: linear-gradient(90deg,#e9f6ff 0%, #f8fcff 50%, #e9f6ff 100%);
    background-size: 200% 100%;
    animation: shimmer 1.4s linear infinite;
  }
  @keyframes shimmer { 0% { background-position: -200% 0; } 100% { background-position: 200% 0; } }

  .thumbnail.loaded { opacity: 1; transform: scale(1); box-shadow: none; }

  .price { color: var(--brand); font-weight:800; font-size:1.05rem; letter-spacing:-0.2px; }
  .stock-muted { color: var(--muted); font-size: .9rem; }

  .btn-outline-primary { transition: transform var(--transition-fast), box-shadow var(--transition-fast); }
  .btn-outline-primary:hover { transform: translateY(-3px); box-shadow: 0 10px 24px rgba(11,116,255,0.06); }

  .btn-add {
    background: linear-gradient(90deg,#10b981,#0ea5a1); color: #fff; border: 0;
    transition: transform .18s ease, box-shadow .18s ease;
  }
  .btn-add:hover { transform: translateY(-3px); box-shadow: 0 10px 26px rgba(16,185,129,0.12); }

  .bg-low, .bg-out, .badge-low-stock, .badge.bg-danger {
    display:inline-flex; align-items:center; gap:6px; padding:.32rem .5rem; border-radius:.45rem; font-weight:600; font-size:.78rem;
  }
  @keyframes badgePulse { 0%{ transform: scale(1); opacity:1; } 60%{ transform: scale(1.06); opacity:.95; } 100%{ transform: scale(1); opacity:1; } }
  .badge-low-stock, .bg-low { animation: badgePulse 2.2s ease-in-out infinite; }

  .categories-card { background: #fff; border-radius: .6rem; padding: 1.1rem; border-left: 4px solid var(--accent); box-shadow: 0 6px 20px rgba(9,30,63,0.04); }
  .category-link { color:#0f1724; display:inline-flex; gap:.6rem; align-items:center; padding:.45rem .25rem; border-radius:.45rem; }
  .category-link:hover { color:var(--brand); transform: translateX(6px); }

  @media (max-width: 767px){
    .card-product { border-radius: .55rem; }
    .thumbnail { height: 180px; }
    .container.py-4 { padding-left: 12px; padding-right: 12px; }
  }
  </style>
</head>
<body>
<?php include __DIR__ . '/templates/header.php'; ?>

 <!-- HERO BANNER WITH FALLING WATER ANIMATION -->
<div class="hero-banner mb-5 p-5 rounded d-flex align-items-center justify-content-between" style="min-height:320px; position:relative; overflow:hidden;">
  <div class="hero-content position-relative" style="z-index:2;">
    <div class="hero-icon mb-3">
      <i class="bi bi-droplet-half" style="color:white;"></i>
    </div>
    <h2 class="mb-2" style="color:white; font-weight:700; font-size:2.5rem;">Welcome to Vetriarasiwatersupply</h2>
    <p style="color:rgba(255,255,255,0.95); font-size:1.1rem; margin-bottom:20px;">
      Reliable water solutions for home and business — bottled water, tanker delivery and service plans.
    </p>
    <a href="#products" class="btn btn-hero btn-hero btn-lg" style="transition:all 0.3s ease;">
      <i class="bi bi-shop"></i> View Products & Plans
    </a>
  </div>
  <div class="d-none d-md-block hero-right">
    <div style="font-size:120px; opacity:0.9; animation: float 4s ease-in-out infinite;">
      💧
    </div>
  </div>
  <!-- Animated Falling Water SVG Overlay -->
  <div class="falling-water" aria-hidden="true" style="position:absolute;z-index:1;top:0;left:0;width:100%;height:100%;pointer-events:none;">
    <svg width="100%" height="100%" viewBox="0 0 800 320" preserveAspectRatio="none" style="display:block;">
      <g>
        <!-- 10 animated falling water streaks -->
        <?php for($i=0;$i<10;$i++): $x=60+$i*70;?>
        <rect x="<?= $x ?>" width="2" height="100" y="-120" fill="#b5e7fa" style="opacity:0.33;">
          <animate attributeName="y" from="-120" to="320" dur="<?=1.6 + $i*0.18?>s" repeatCount="indefinite" />
        </rect>
        <?php endfor;?>
      </g>
    </svg>
  </div>
</div>

<style>
.hero-banner {
  background: linear-gradient(120deg,#1093d8 0%,#062343 65%);
  box-shadow: 0 8px 80px 0 rgba(16,147,216,0.13), 0 1.5rem 2rem 0 rgba(6,35,67,0.15);
  position: relative;
  overflow: hidden;
}
@keyframes float {
  0%,100% { transform: translateY(0);}
  50% { transform: translateY(18px);}
}
</style>


<!-- Guidance Video Section: Modern, Premium UI -->
<div class="row mb-5">
  <div class="col-md-8 mx-auto">
    <div class="card shadow-lg border-0 overflow-hidden position-relative" style="background:linear-gradient(120deg, #e0f7fa 0%, #ffffff 100%);">
      <div class="position-absolute w-100 h-100" style="pointer-events:none;z-index:1;">
        <!-- Animated water waves SVG -->
        <svg height="120" width="100%" style="position:absolute;bottom:0;left:0;">
          <path d="M0 20 Q60 80 180 20 T320 20 T480 20 T640 20 T800 20 V120 H0Z" fill="#bdefff" opacity="0.38">
            <animate attributeName="d"
              values="
                M0 20 Q60 80 180 20 T320 20 T480 20 T640 20 T800 20 V120 H0Z;
                M0 40 Q60 60 180 40 T320 30 T480 60 T640 20 T800 40 V120 H0Z;
                M0 20 Q60 80 180 20 T320 20 T480 20 T640 20 T800 20 V120 H0Z"
              dur="6s"
              repeatCount="indefinite"
            />
          </path>
        </svg>
      </div>
      <div class="p-4 pb-0 position-relative" style="z-index:2;">
        <div class="d-flex align-items-center gap-3 mb-2">
          <div class="bg-primary p-3 rounded-circle shadow-sm" style="animation: pulseGlow 2s infinite;">
            <i class="bi bi-play-circle" style="font-size:2.2rem;color:#fff;"></i>
          </div>
          <h5 class="fw-bold mb-0 text-primary" style="letter-spacing: -1px;">User Guidance Video</h5>
        </div>
        <p class="text-muted mb-2">
          Watch a short walkthrough on <span class="text-primary fw-semibold">how to place an order</span>, choose a plan, and track deliveries.
        </p>
      </div>
      <div class="ratio ratio-16x9 rounded-bottom overflow-hidden" style="background:#eef8fb;">
        <video
          autoplay
          muted
          loop
          playsinline
          controls
          poster="<?php echo $base_url; ?>/uploads/Cars-poster.jpg"
          style="width:100%; height:100%; object-fit:cover; border-radius:0 0 1rem 1rem;"
          onended="this.play()"
        >
          <source src="<?php echo $base_url; ?>/uploads/Cars.mp4" type="video/mp4">
          Your browser does not support the video tag.
        </video>
      </div>
    </div>
  </div>
</div>

<style>
@keyframes pulseGlow {
  0% {box-shadow:0 0 0 0 #6ed4ff;}
  70% {box-shadow:0 0 0 10px #6ed4ff77;}
  100% {box-shadow:0 0 0 0 #6ed4ff;}
}
</style>



  <!-- Features Section -->
<div class="row g-3 mb-5" style="--bs-gutter-x: 1.5rem; --bs-gutter-y: 1.5rem;">
  <?php 
    $features = [
      ['icon' => 'check-circle', 'title' => '100% Authentic', 'desc' => 'Certified By FSSAI', 'delay' => '0s'],
      ['icon' => 'truck', 'title' => 'Fast Delivery', 'desc' => 'Quick shipping Local', 'delay' => '0.1s'],
      ['icon' => 'arrow-counterclockwise', 'title' => '200 Plus', 'desc' => 'Customer Satisfaction', 'delay' => '0.2s'],
      ['icon' => 'chat-dots', 'title' => '24/7 Support', 'desc' => 'Dedicated customer service', 'delay' => '0.3s'],
    ];
    foreach ($features as $feature): ?>
      <div class="col-md-3 col-sm-6">
        <div class="card p-4 text-center border-0 shadow-sm feature-card" style="animation-delay: <?= $feature['delay']; ?>;">
          <div class="feature-icon mb-3">
            <i class="bi bi-<?= $feature['icon']; ?>" aria-hidden="true"></i>
          </div>
          <h6 class="feature-title"><?= htmlspecialchars($feature['title']); ?></h6>
          <p class="text-muted small"><?= htmlspecialchars($feature['desc']); ?></p>
        </div>
      </div>
  <?php endforeach; ?>
</div>

<!-- Products Section -->
<div id="products" class="row g-3">
  <div class="col-md-3 d-none d-md-block">
    <div class="card categories-card p-4 mb-3 shadow-sm rounded">
      <h6><i class="bi bi-list"></i> Categories</h6>
      <ul class="list-unstyled mt-3">
        <?php
          $cats = $pdo->query("SELECT DISTINCT category FROM products WHERE category IS NOT NULL AND category <> ''")->fetchAll();
          foreach($cats as $c){ if(!$c['category']) continue;
        ?>
          <li><a href="<?php echo $base_url; ?>/category.php/<?php echo urlencode($c['category']); ?>" class="category-link">
            <i class="bi bi-chevron-right"></i> <?php echo esc($c['category']); ?>
          </a></li>
        <?php } ?>
      </ul>
    </div>
  </div>
  <div class="col-md-9">
    <div class="products-section mb-3">
      <h5><i class="bi bi-star"></i> <?php echo $q? 'Search results for: '.esc($q) : ($category? 'Category: '.esc($category) : 'Latest Products'); ?></h5>
    </div>
    <div class="row g-4">
      <?php foreach($products as $p): ?>
        <div class="col-sm-6 col-md-4 col-lg-4 col-xl-3 product-card-wrapper">
          <div class="card card-product shadow-sm position-relative h-100 product-card">
            <?php if((int)$p['stock'] <= 5 && (int)$p['stock'] > 0): ?>
              <span class="badge badge-low-stock position-absolute" style="right:10px; top:10px;">
                <i class="bi bi-exclamation-lg"></i> Low stock
              </span>
            <?php elseif((int)$p['stock'] <= 0): ?>
              <span class="badge bg-danger position-absolute" style="right:10px; top:10px;">
                <i class="bi bi-x-lg"></i> Out of Stock
              </span>
            <?php endif; ?>
            <?php if($p['thumbnail']): ?>
                  <img src="<?php echo $base_url; ?>/uploads/<?php echo esc($p['thumbnail']); ?>" 
                    class="card-img-top thumbnail rounded-top img-fluid" 
                   alt="<?php echo esc($p['name']); ?>" 
                   loading="lazy"
                   decoding="async">
            <?php else: ?>
              <div class="bg-light p-5 text-center d-flex align-items-center justify-content-center" style="height:220px;">
                <span class="text-muted"><i class="bi bi-image"></i> No image</span>
              </div>
            <?php endif; ?>
            <div class="card-body d-flex flex-column">
              <h6 class="card-title h6 mb-1 text-truncate" title="<?php echo esc($p['name']); ?>">
                <?php echo esc($p['name']); ?>
              </h6>
              <p class="mb-1 fw-bold text-primary fs-5">₹<?php echo number_format((float)($p['price'] ?? 0), 2); ?></p>
              <p class="text-muted small mb-3"><i class="bi bi-boxes"></i> Stock: <?php echo (int)$p['stock']; ?></p>
              <div class="mt-auto d-flex gap-2">
                <a href="<?php echo $base_url; ?>/singleproduct.php?id=<?php echo $p['id']; ?>" class="btn btn-sm btn-outline-primary flex-grow-1" aria-label="View product <?php echo esc($p['name']); ?>">
                  <i class="bi bi-eye"></i> View
                </a>
                <?php if($p['stock'] > 0): ?>
                  <form method="post" action="<?php echo $base_url; ?>/add_to_cart.php" class="d-inline-block flex-grow-1" aria-label="Add product <?php echo esc($p['name']); ?> to cart">
                    <input type="hidden" name="product_id" value="<?php echo $p['id']; ?>">
                    <input type="hidden" name="qty" value="1">
                    <button class="btn btn-sm btn-success w-100">
                      <i class="bi bi-cart-plus"></i> Add
                    </button>
                  </form>
                <?php endif; ?>
              </div>
            </div>
          </div>
        </div>
      <?php endforeach; ?>
      <?php if(empty($products)): ?>
        <div class="text-center py-5">
          <p class="text-muted lead">No products found</p>
          <p class="text-muted">Try adjusting your search or filter criteria</p>
        </div>
      <?php endif; ?>
    </div>
  </div>
</div>

<style>
  /* Animate features sliding in smoothly */
  @keyframes slideInUp {
    0% {
      transform: translateY(20px);
      opacity: 0;
    }
    100% {
      transform: translateY(0);
      opacity: 1;
    }
  }
  .feature-card {
    animation-name: slideInUp;
    animation-duration: 0.6s;
    animation-fill-mode: both;
  }
  .feature-icon {
    font-size: 2.5rem;
    color: #138ec7ff;
    margin-bottom: 10px;
    transition: color 0.3s ease;
  }
  .feature-card:hover .feature-icon {
    color: #0b74ff;
  }
  .feature-title {
    color: #138ec7ff;
    font-weight: 700;
    margin-bottom: 0.3rem;
  }
  /* Categories card improved */
  .categories-card {
    border-left: 4px solid #138ec7ff;
    box-shadow: 0 4px 15px rgb(19 142 207 / 0.15);
    border-radius: 0.35rem;
    transition: box-shadow 0.3s ease, transform 0.3s ease;
  }
  .categories-card:hover {
    box-shadow: 0 8px 30px rgb(11 116 255 / 0.3);
    transform: translateX(4px);
  }
  .categories-card h6 {
    color: #138ec7ff;
    font-weight: 700;
    font-size: 1.1rem;
  }
  .categories-card a.category-link {
    text-decoration: none;
    color: #374151;
    display: inline-flex;
    align-items: center;
    transition: color 0.3s ease;
  }
  .categories-card a.category-link:hover {
    color: #0b74ff;
    font-weight: 600;
    margin-left: 6px;
  }
  /* Product cards refinement */
  .product-card-wrapper {
    transition: box-shadow 0.3s ease, transform 0.3s ease;
  }
  .product-card-wrapper:hover {
    transform: translateY(-6px);
    box-shadow: 0 15px 30px rgb(0 0 0 / 0.12);
    z-index: 2;
  }
  .card-product img.thumbnail {
    object-fit: cover;
    height: 220px;
    border-top-left-radius: 0.375rem;
    border-top-right-radius: 0.375rem;
    transition: transform 0.3s ease;
  }
  .product-card-wrapper:hover .card-product img.thumbnail {
    transform: scale(1.05);
  }
  .badge-low-stock, .badge.bg-danger {
    font-size: 0.75rem;
    padding: 0.3em 0.5em;
    letter-spacing: 0.03em;
  }
</style>



<?php include __DIR__ . '/templates/footer.php'; ?>

<!-- ENHANCEMENT JS: image fade-in, micro interactions, floating CTA -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
(function(){
  // Image lazy-load & fade-in
  document.querySelectorAll('img.thumbnail').forEach(function(img){
    if(!img.complete){
      img.classList.add('img-placeholder');
      img.style.opacity = '0.01';
    } else {
      img.classList.add('loaded');
      img.style.opacity = '1';
    }
    img.addEventListener('load', function(){
      img.classList.remove('img-placeholder');
      img.classList.add('loaded');
      img.style.opacity = '1';
    });
    img.addEventListener('error', function(){
      img.classList.remove('img-placeholder');
      img.style.opacity = '1';
    });
  });

  // micro-toast on Add to cart buttons (UI only)
  document.querySelectorAll('form[action*="add_to_cart.php"] button[type="submit"]').forEach(function(btn){
    btn.addEventListener('click', function(e){
      var t = document.createElement('div');
      t.textContent = 'Added to cart';
      t.style.position = 'fixed';
      t.style.right = '20px';
      t.style.bottom = '90px';
      t.style.background = 'linear-gradient(90deg,#10b981,#0ea5a1)';
      t.style.color = '#fff';
      t.style.padding = '8px 12px';
      t.style.borderRadius = '8px';
      t.style.boxShadow = '0 8px 20px rgba(16,185,129,0.12)';
      t.style.zIndex = 99998;
      t.style.opacity = '0';
      t.style.transform = 'translateY(10px)';
      document.body.appendChild(t);
      requestAnimationFrame(function(){
        t.style.transition = 'all 260ms ease';
        t.style.opacity = '1';
        t.style.transform = 'translateY(0)';
      });
      setTimeout(function(){
        t.style.opacity = '0';
        t.style.transform = 'translateY(-10px)';
        setTimeout(function(){ t.remove(); }, 300);
      }, 1200);
    });
  });

})();
</script>



<!-- ======= MOBILE/HEADER HELPER (small, non-invasive) ======= -->
<script>
/*
  Small non-invasive helper:
  - Ensures the desktop collapse can be toggled programmatically for consistent UI.
  - Ensures categories column is shown above product list on mobile.
  Nothing changes server-side or PHP; this only adjusts classes for presentation.
*/
(function(){
  try{
    var navMain = document.getElementById('navMain');
    var toggler = document.querySelector('.navbar-toggler');

    // If toggler exists — ensure it toggles 'show' class consistently
    if(toggler && navMain){
      toggler.addEventListener('click', function(e){
        e.preventDefault();
        navMain.classList.toggle('show');
      }, {passive:true});
    }

    // If categories column is present and we're on small screen, move it above products for better UX
    function reorderForMobile(){
      if(window.innerWidth <= 991){
        var leftCol = document.querySelector('.col-md-3.d-none.d-md-block');
        var rightCol = document.querySelector('.col-md-9');
        var parent = document.querySelector('#products');
        if(leftCol && rightCol && parent && leftCol.parentNode === parent){
          parent.insertBefore(leftCol, rightCol); // move categories above products
        }
      } else {
        // On larger screens, default Bootstrap layout will handle columns
      }
    }
    window.addEventListener('resize', reorderForMobile, {passive:true});
    document.addEventListener('DOMContentLoaded', reorderForMobile, {passive:true});
  }catch(e){
    console.warn('Responsive helper error', e);
  }
})();
</script>
<!-- ======= end mobile/header helper ======= -->

<!-- Social Floating Buttons (bottom-left) -->
<style>
/* Social floating (bottom-left) */
.social-floating {
  position: fixed;
  left: 16px;
  bottom: 16px;
  z-index: 11000;
  display: flex;
  flex-direction: column;
  gap: 10px;
  align-items: center;
  pointer-events: auto;
  transition: transform .18s ease, opacity .18s ease;
}

/* Individual button style */
.social-floating .sf-btn {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  width: 44px;
  height: 44px;
  border-radius: 50%;
  color: #fff;
  text-decoration: none;
  box-shadow: 0 8px 20px rgba(7,20,40,0.12);
  transition: transform .16s ease, box-shadow .16s ease, opacity .16s;
  font-size: 20px;
  background: #0b74ff;
}

/* Brand colors */
.social-floating .sf-wa { background: #25D366; }        /* WhatsApp */
.social-floating .sf-fb { background: #1877F2; }        /* Facebook */
.social-floating .sf-ig { background: radial-gradient(circle at 30% 107%, #fdf497 0%, #fdf497 5%, #fd5949 45%, #d6249f 60%, #285AEB 90%); } /* Instagram gradient */
.social-floating .sf-yt { background: #FF0000; }        /* YouTube */

/* Hover states */
.social-floating .sf-btn:hover { transform: translateY(-4px) scale(1.03); box-shadow: 0 14px 36px rgba(7,20,40,0.16); }

/* Toggle (collapsed state) */
.social-floating .sf-toggle {
  display: inline-flex;
  align-items:center;
  justify-content:center;
  width:54px;
  height:54px;
  border-radius:999px;
  background: linear-gradient(90deg,#0b74ff,#00d4ff);
  color:#fff;
  font-size:20px;
  border: none;
  box-shadow: 0 12px 34px rgba(11,116,255,0.18);
  cursor: pointer;
}

/* Hide individual icons when collapsed on small screens */
.social-floating.collapsed .sf-list { display: none; }

/* Small labels (optional on hover) */
.sf-label {
  display: none;
  margin-left: 10px;
  background: rgba(0,0,0,0.7);
  color: #fff;
  padding: 6px 8px;
  border-radius: 6px;
  font-size: 0.82rem;
  white-space: nowrap;
}

/* Show label on large screens when hovering button */
@media (min-width: 576px) {
  .social-floating .sf-item { position: relative; }
  .social-floating .sf-item:hover .sf-label { display: block; position: absolute; left: 56px; top: 50%; transform: translateY(-50%); }
}

/* Responsive: on very small screens (<=420px) start collapsed */
@media (max-width: 420px) {
  .social-floating { left: 10px; bottom: 10px; gap: 8px; }
  .social-floating:not(.open) .sf-list { display: none; }
  .social-floating .sf-toggle { width:48px; height:48px; font-size:18px; }
}

/* When open (expanded) show full list */
.social-floating.open { gap: 12px; transform: none; opacity: 1; }
</style>

<div class="social-floating collapsed" id="socialFloating" aria-hidden="false" aria-label="Social links">
  <!-- Toggle button (always visible) -->
  <button id="sfToggle" class="sf-toggle" aria-expanded="false" aria-controls="sfList" title="Open social links">
    <i class="bi bi-share-fill" aria-hidden="true"></i>
  </button>

  <!-- Social list -->
  <div id="sfList" class="sf-list" role="list" aria-hidden="true">
    <!-- WhatsApp (replace PHONE with your number, e.g. 9199xxxxxxx) -->
    <div class="sf-item" role="listitem">
      <a class="sf-btn sf-wa" href="https://wa.me/9360658623" target="_blank" rel="noopener noreferrer" aria-label="Chat on WhatsApp">
        <i class="bi bi-whatsapp" aria-hidden="true"></i>
      </a>
      <span class="sf-label">WhatsApp</span>
    </div>

    <!-- Facebook -->
    <div class="sf-item" role="listitem">
      <a class="sf-btn sf-fb" href="https://www.facebook.com/YOUR_PAGE" target="_blank" rel="noopener noreferrer" aria-label="Visit Facebook page">
        <i class="bi bi-facebook" aria-hidden="true"></i>
      </a>
      <span class="sf-label">Facebook</span>
    </div>

    <!-- Instagram -->
    <div class="sf-item" role="listitem">
      <a class="sf-btn sf-ig" href="https://www.instagram.com/vetriarasiwatersupply?igsh=cDRuYjgyZWxlcHhz" target="_blank" rel="noopener noreferrer" aria-label="Visit Instagram">
        <i class="bi bi-instagram" aria-hidden="true"></i>
      </a>
      <span class="sf-label">Instagram</span>
    </div>

  <!-- Gmail (email) -->
  <div class="sf-item" role="listitem">
    <a class="sf-btn sf-yt" href="mailto:vetriarasiwatersupply@gmail.com" target="_blank" rel="noopener noreferrer" aria-label="Send email">
      <i class="bi bi-envelope-fill" aria-hidden="true"></i>
    </a>
    <span class="sf-label">Email</span>
  </div>
  </div>
</div>

<script>
(function(){
  var container = document.getElementById('socialFloating');
  var toggle = document.getElementById('sfToggle');
  var list = document.getElementById('sfList');

  // helper: set collapsed/expanded UI
  function setOpen(open){
    if(open){
      container.classList.add('open');
      container.classList.remove('collapsed');
      list.setAttribute('aria-hidden','false');
      toggle.setAttribute('aria-expanded','true');
      container.classList.remove('collapsed');
    } else {
      container.classList.remove('open');
      container.classList.add('collapsed');
      list.setAttribute('aria-hidden','true');
      toggle.setAttribute('aria-expanded','false');
    }
  }

  // initial: collapse on very small screens
  var mq = window.matchMedia('(max-width:420px)');
  setOpen(!mq.matches);

  // Toggle click
  toggle.addEventListener('click', function(e){
    e.preventDefault();
    var isOpen = container.classList.contains('open');
    setOpen(!isOpen);
  });

  // close when clicking outside
  document.addEventListener('click', function(e){
    if(!container.contains(e.target) && window.matchMedia('(max-width:420px)').matches){
      setOpen(false);
    }
  });

  // keyboard accessibility: Esc closes
  document.addEventListener('keydown', function(e){
    if(e.key === 'Escape') setOpen(false);
  });

  // ensure accessible focus flow for social links
  var links = container.querySelectorAll('a.sf-btn');
  links.forEach(function(a){
    a.addEventListener('focus', function(){ setOpen(true); });
  });

  // update collapse when screen size changes
  mq.addEventListener('change', function(e){
    setOpen(!e.matches);
  });
})();
</script>

</body>
</html>
