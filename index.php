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

// ----------------------
// HERO IMAGES (configure below)
// ----------------------
// Place hero images in /uploads named hero1.jpg, hero2.jpg, ... or change filenames below.
$hero_candidates = [
  'NaturalBottle.png',
  'NaturalTruck.png',
  'christmasOffer1.png',
  'TruckUR.png',
  'deliverytruck.png',
  'ClearBottle.png'
];
$hero_images = [];
foreach($hero_candidates as $f){
  $path = __DIR__ . '/assets/images/' . $f;
  if(file_exists($path)){
    $hero_images[] = $base_url . '/assets/images/' . $f;
  }
}
// If none found, try to pick first few product thumbnails as fallback
if(empty($hero_images)){
  foreach($products as $p){
    if(!empty($p['thumbnail'])){
      $hero_images[] = $base_url . '/assets/images/' . $p['thumbnail'];
      if(count($hero_images) >= 3) break;
    }
  }
}
// final fallback: a gradient background will be used by CSS if still empty

// page header
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Vetriarasiwatersupply - Reliable Water Solutions</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
  <link rel="stylesheet" href="<?php echo $base_url; ?>/assets/css/custom.css">
  <link rel="stylesheet" href="<?php echo $base_url; ?>/assets/css/amazon-style.css">

  <!-- YOUR EXISTING STYLES (kept) -->
  <style>
    /* small addition: slideshow styles for hero */
    .hero-banner { position: relative; overflow: hidden; }
    .hero-slideshow { position:absolute; inset:0; z-index:0; display:block; }
    .hero-slide { position:absolute; top:0; left:0; width:100%; height:100%; background-size:cover; background-position:center; opacity:0; transition:opacity 900ms ease-in-out; will-change:opacity; }
    .hero-slide.active { opacity:1; }
    .hero-slide img{ display:none; }
    /* slight dark overlay to keep text readable */
    .hero-overlay { position:absolute; inset:0; background: linear-gradient(180deg, rgba(3,10,20,0.25), rgba(3,10,20,0.35)); z-index:1; pointer-events:none; }
    .hero-content { position: relative; z-index:2; }

    /* decorative falling droplets */
    .falling-droplets { position:absolute; inset:0; z-index:1; pointer-events:none; overflow:hidden; }
    .falling-droplets .drop { position:absolute; top:-20%; display:block; background: radial-gradient(circle at 40% 30%, rgba(51, 126, 187, 0.9) 0%, rgba(255,255,255,0.2) 30%, rgba(22, 174, 233, 0.6) 100%); border-radius:50%; opacity:0.9; transform: translateY(-30vh); animation: fall 5s linear infinite; }
    @keyframes fall { 0% { transform: translateY(-30vh) scale(0.8); opacity:0.0 } 30% { opacity:0.6 } 100% { transform: translateY(120vh) scale(1.05); opacity:0 } }

    /* category thumbnail styles */
    .category-thumb { width:54px; height:54px; object-fit:cover; border-radius:0.45rem; border:1px solid rgba(255,255,255,0.06); box-shadow:0 6px 18px rgba(9,30,63,0.06); }
    .category-thumb-placeholder { width:74px; height:54px; background:linear-gradient(90deg,#eef9ff,#fff); color:#0b74ff; }

    /* small responsive tweak for hero-right emoji when images are large */
    .hero-right { z-index:2; }

    /* keep previous floating water animation keyframes available (no server changes required) */
    @keyframes float {
      10%, 80% { transform: translateY(0px); }
      70% { transform: translateY(20px); }
    }
  </style>

  <!-- UI ENHANCEMENTS (non-invasive; paste here to enhance appearance only) -->
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700;800&display=swap" rel="stylesheet">
  <style>
  :root{ --brand: #0b74ff; --accent: #138ec7; --muted: #6b7280; --success: #10b981; --danger: #ef4444; --glass: rgba(255,255,255,0.7); --card-radius: 0.6rem; --transition-fast: 180ms; }
  body { font-family: "Inter", system-ui, -apple-system, "Segoe UI", Roboto, "Helvetica Neue", Arial; color: #0f1724; }
  .container.py-4 { max-width: 1180px; }
  /* rest kept as before... (omitted here in the editor for brevity) */
  </style>
  
</head>
<body>
<?php include __DIR__ . '/templates/header.php'; ?>

 <!-- HERO BANNER WITH IMAGE SLIDESHOW BACKGROUND -->
<div class="hero-banner mb-5 p-5 rounded d-flex align-items-center justify-content-between" style="min-height:420px; position:relative;">

  <!-- Slideshow container (background images) -->
  <div class="hero-slideshow" aria-hidden="true">
    <?php if(!empty($hero_images)): foreach($hero_images as $i => $img): ?>
      <div class="hero-slide<?php echo $i===0 ? ' active' : ''; ?>" style="background-image: url('<?php echo esc($img); ?>');"></div>
    <?php endforeach; else: ?>
      <!-- fallback decorative gradient if no hero images are available -->
      <div class="hero-slide active" style="background: linear-gradient(120deg,#1093d8 0%,#062343 65%);"></div>
    <?php endif; ?>
  </div>

  <!-- subtle overlay for readability -->
  <div class="hero-overlay" aria-hidden="true"></div>

  <!-- Decorative falling droplets (CSS-only) -->
  <div class="falling-droplets" aria-hidden="true">
    <?php for($i=0;$i<12;$i++): ?>
      <span class="drop" style="left:<?= 3 + ($i*7) ?>%; animation-delay: <?= ($i*0.35) ?>s; width:<?= 6 + ($i%4) ?>px; height:<?= 10 + ($i%4)*3 ?>px;"></span>
    <?php endfor; ?>
  </div>

  <div class="hero-content position-relative" style="z-index:2;">
    <div class="hero-icon mb-3">
      <i class="bi bi-droplet-half" style="color:white;font-size:3rem;"></i>
    </div>
    <h2 class="mb-2" style="color:white; font-weight:700; font-size:2.5rem;">Welcome to Vetriarasiwatersupply</h2>
    <p style="color:rgba(255,255,255,0.95); font-size:1.1rem; margin-bottom:20px;">
      Reliable water solutions for home and business — bottled water, tanker delivery and service plans.
    </p>
    <a href="#products" class="btn btn-hero btn-hero btn-lg" style="transition:all 0.3s ease; z-index:3;">
      <i class="bi bi-shop"></i> View Products & Plans
    </a>
  </div>

   <!-- <div class="d-none d-md-block hero-right">
    <div style="font-size:120px; opacity:0.9; animation: float 4s ease-in-out infinite;">
      💧
    </div>
  </div>  -->      

</div>

<style>
/* preserve and slightly refine the existing hero look when JS is unavailable */
@media (prefers-reduced-motion: reduce){
  .hero-slide { transition: none !important; }
}
</style>

<!-- Small JS to cycle hero images (non-invasive; unobtrusive) -->
<script>
(function(){
  try{
    var slides = document.querySelectorAll('.hero-slide');
    if(!slides || slides.length <= 1) return; // nothing to rotate
    var idx = 0;
    var delay = 4500; // ms between slides

    function show(i){
      slides.forEach(function(s, j){
        if(j === i) s.classList.add('active'); else s.classList.remove('active');
      });
    }

    // autoplay loop
    setInterval(function(){
      idx = (idx + 1) % slides.length;
      show(idx);
    }, delay);

    // progressive image preloading for smoother transition
    slides.forEach(function(s){
      var bg = s.style.backgroundImage.replace(/url\(|\)|\"|\'/g,'');
      if(bg){
        var img = new Image(); img.src = bg;
      }
    });

    // optional: make drops vary timing slightly for natural look
    var drops = document.querySelectorAll('.falling-droplets .drop');
    drops.forEach(function(d,i){ d.style.animationDuration = (4 + (i%3)) + 's'; });

  }catch(e){ console.warn('Hero slideshow error', e); }
})();
</script>

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
<div id="products" class="row g-4">
  <!-- Categories Sidebar -->
  <div class="col-lg-3 d-none d-lg-block">
    <div class="sticky-top" style="top: 20px;">
      <!-- Categories Header -->
      <div class="mb-4">
        <div class="d-flex align-items-center mb-3">
          <h5 class="mb-0" style="color: #0b74ff; font-weight: 700;">
            <i class="bi bi-grid-3x3-gap"></i> Categories
          </h5>
        </div>
        
        <!-- Categories Grid -->
        <div class="row row-cols-2 g-3">
          <?php
            $cats = $pdo->query("SELECT category, (SELECT thumbnail FROM products p2 WHERE p2.category = products.category AND p2.thumbnail IS NOT NULL LIMIT 1) AS thumb FROM products WHERE category IS NOT NULL AND category <> '' GROUP BY category ORDER BY category")->fetchAll();
            foreach($cats as $c){ if(empty($c['category'])) continue; $catSlug = urlencode($c['category']); $thumb = $c['thumb'] ? ($base_url . '/uploads/' . $c['thumb']) : null;
          ?>
            <div class="col">
              <a href="<?php echo $base_url; ?>/category.php?name=<?php echo urlencode($c['category']); ?>" class="category-box text-decoration-none" title="View products in <?php echo esc($c['category']); ?>" style="display: block; border-radius: 10px; overflow: hidden; transition: all 0.3s ease; border: 1px solid #e0e0e0; background: white;">
                <div style="position: relative; height: 120px; overflow: hidden; background: #f5f5f5;">
                  <?php if($thumb): ?>
                    <img src="<?php echo esc($thumb); ?>" alt="<?php echo esc($c['category']); ?>" style="width: 100%; height: 100%; object-fit: cover;" loading="lazy">
                  <?php else: ?>
                    <div style="width: 100%; height: 100%; display: flex; align-items: center; justify-content: center; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                      <i class="bi bi-droplet" style="font-size: 2.5rem; color: white; opacity: 0.8;"></i>
                    </div>
                  <?php endif; ?>
                  <div style="position: absolute; inset: 0; background: linear-gradient(180deg, rgba(0,0,0,0) 0%, rgba(0,0,0,0.3) 100%); opacity: 0; transition: opacity 0.3s ease;"></div>
                </div>
                <div style="padding: 12px; text-align: center;">
                  <div style="font-weight: 600; font-size: 13px; color: #333; text-truncate;"><?php echo esc($c['category']); ?></div>
                  <div style="font-size: 11px; color: #999; margin-top: 4px;">View</div>
                </div>
              </a>
            </div>
          <?php } ?>
        </div>

        <!-- View All Categories Button -->
        <div style="margin-top: 16px; text-align: center;">
          <a href="<?php echo $base_url; ?>/category.php" class="btn btn-sm" style="background: linear-gradient(90deg, #0b74ff, #00d4ff); color: white; border: none; font-weight: 600;">
            <i class="bi bi-arrow-right"></i> View All Categories
          </a>
        </div>
      </div>
    </div>
  </div>
  <!-- Products Main Content -->
  <div class="col-lg-9">
    <!-- Section Header -->
    <div class="mb-4">
      <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 24px;">
        <div style="width: 4px; height: 28px; background: linear-gradient(90deg, #0b74ff, #00d4ff); border-radius: 2px;"></div>
        <h4 style="margin: 0; color: #1a1a1a; font-weight: 700;">
          <?php echo $q? 'Search Results: '.esc($q) : ($category? 'Category: '.esc($category) : 'Latest Products'); ?>
        </h4>
      </div>
    </div>

    <!-- Products Grid -->
    <div class="row g-4">
      <?php foreach($products as $p): ?>
        <div class="col-sm-6 col-md-4 col-xl-3 product-card-wrapper">
          <div class="card card-product shadow-sm position-relative h-100 product-card" style="border: 1px solid #e8e8e8; border-radius: 12px; overflow: hidden; transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);">
            
            <!-- Stock Badge -->
            <?php if((int)$p['stock'] <= 5 && (int)$p['stock'] > 0): ?>
              <span class="badge badge-low-stock position-absolute" style="right: 12px; top: 12px; background: linear-gradient(135deg, #ff9800, #ff6f00); color: white; font-size: 11px; font-weight: 600; padding: 6px 10px; border-radius: 6px; z-index: 10;">
                <i class="bi bi-exclamation-circle"></i> Low Stock
              </span>
            <?php elseif((int)$p['stock'] <= 0): ?>
              <span class="badge position-absolute" style="right: 12px; top: 12px; background: #ff4444; color: white; font-size: 11px; font-weight: 600; padding: 6px 10px; border-radius: 6px; z-index: 10;">
                <i class="bi bi-x-circle"></i> Out of Stock
              </span>
            <?php endif; ?>
            
            <!-- Product Image -->
            <div style="position: relative; height: 220px; overflow: hidden; background: #f9f9f9;">
              <?php if($p['thumbnail']): ?>
                <img src="<?php echo $base_url; ?>/uploads/<?php echo esc($p['thumbnail']); ?>" 
                  class="card-img-top thumbnail img-fluid" 
                  alt="<?php echo esc($p['name']); ?>" 
                  loading="lazy"
                  decoding="async"
                  style="width: 100%; height: 100%; object-fit: cover;">
              <?php else: ?>
                <div style="height: 100%; display: flex; align-items: center; justify-content: center; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white;">
                  <i class="bi bi-image" style="font-size: 2.5rem; opacity: 0.6;"></i>
                </div>
              <?php endif; ?>
            </div>

            <!-- Product Details -->
            <div class="card-body d-flex flex-column" style="padding: 16px;">
              <h6 class="card-title h6 mb-2 text-truncate" title="<?php echo esc($p['name']); ?>" style="color: #1a1a1a; font-weight: 600; font-size: 14px;">
                <?php echo esc($p['name']); ?>
              </h6>
              
              <div style="margin-bottom: 12px;">
                <p class="mb-1 fw-bold text-primary fs-5" style="background: linear-gradient(90deg, #0b74ff, #00d4ff); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text;">
                  ₹<?php echo number_format((float)($p['price'] ?? 0), 2); ?>
                </p>
                <p class="text-muted small" style="font-size: 12px;">
                  <i class="bi bi-boxes"></i> 
                  <span style="color: <?php echo (int)$p['stock'] > 0 ? '#4caf50' : '#f44444'; ?>; font-weight: 500;">
                    <?php echo (int)$p['stock'] > 0 ? (int)$p['stock'] . ' in stock' : 'Out of stock'; ?>
                  </span>
                </p>
              </div>

              <!-- Action Buttons -->
              <div class="mt-auto d-flex gap-2">
                <a href="<?php echo $base_url; ?>/singleproduct.php?id=<?php echo $p['id']; ?>" class="btn btn-sm flex-grow-1" style="background: white; color: #0b74ff; border: 2px solid #0b74ff; font-weight: 600; border-radius: 8px; transition: all 0.3s;" aria-label="View product <?php echo esc($p['name']); ?>">
                  <i class="bi bi-eye"></i> View
                </a>
                <?php if($p['stock'] > 0): ?>
                  <form method="post" action="<?php echo $base_url; ?>/add_to_cart.php" class="d-inline-block flex-grow-1" aria-label="Add product <?php echo esc($p['name']); ?> to cart">
                    <input type="hidden" name="product_id" value="<?php echo $p['id']; ?>">
                    <input type="hidden" name="qty" value="1">
                    <button class="btn btn-sm w-100" style="background: linear-gradient(90deg, #0b74ff, #00d4ff); color: white; font-weight: 600; border: none; border-radius: 8px; transition: all 0.3s;">
                      <i class="bi bi-cart-plus"></i> Add
                    </button>
                  </form>
                <?php endif; ?>
              </div>
            </div>
          </div>
        </div>
      <?php endforeach; ?>
    </div>

    <!-- No Products Message -->
    <?php if(empty($products)): ?>
      <div style="text-align: center; padding: 60px 20px;">
        <div style="margin-bottom: 20px;">
          <i class="bi bi-inbox" style="font-size: 4rem; color: #ddd;"></i>
        </div>
        <p style="font-size: 18px; color: #666; font-weight: 500; margin-bottom: 8px;">No products found</p>
        <p style="color: #999; margin-bottom: 24px;">Try adjusting your search or filter criteria</p>
        <a href="<?php echo $base_url; ?>/" class="btn btn-sm" style="background: linear-gradient(90deg, #0b74ff, #00d4ff); color: white; border: none; font-weight: 600;">
          <i class="bi bi-arrow-left"></i> Browse All Products
        </a>
      </div>
    <?php endif; ?>
  </div>
</div>

<style>
  /* Enhanced Categories & Products Styles */
  .category-box {
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    position: relative;
  }
  .category-box:hover {
    box-shadow: 0 12px 32px rgba(11, 116, 255, 0.15);
    border-color: #0b74ff;
    transform: translateY(-4px);
  }
  .category-box:hover img {
    transform: scale(1.08);
  }
  .category-box img {
    transition: transform 0.3s ease;
  }

  /* Product Card Hover Effects */
  .product-card {
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
  }
  .product-card:hover {
    box-shadow: 0 16px 48px rgba(11, 116, 255, 0.18);
    transform: translateY(-6px);
    border-color: #0b74ff !important;
  }
  .product-card:hover .card-img-top {
    transform: scale(1.05);
  }
  .product-card .card-img-top {
    transition: transform 0.3s ease;
  }

  /* Button Hover Effects */
  .product-card .btn-sm[style*="white"] {
    transition: all 0.3s ease;
  }
  .product-card .btn-sm[style*="white"]:hover {
    background: linear-gradient(90deg, #0b74ff, #00d4ff) !important;
    color: white !important;
    border-color: #0b74ff !important;
  }
  .product-card .btn-sm[style*="linear-gradient"]:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 20px rgba(11, 116, 255, 0.3);
  }

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

  // make category thumbnail clicks accessible (keyboard)
  document.addEventListener('DOMContentLoaded', function(){
    document.querySelectorAll('.category-link').forEach(function(a){
      a.setAttribute('role','link');
      a.setAttribute('tabindex','0');
      a.addEventListener('keypress', function(e){ if(e.key === 'Enter' || e.key === ' ') { window.location = a.href; } });
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
