<?php
require_once 'init.php';
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
  <link rel="stylesheet" href="assets/css/custom.css">
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
    <a href="#products" class="btn btn-light btn-lg" style="transition:all 0.3s ease;">
      <i class="bi bi-shop"></i> View Products & Plans
    </a>
  </div>
  <div class="d-none d-md-block" style="max-width:350px; position: relative; z-index:2;">
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
          <li><a href="index.php?category=<?php echo urlencode($c['category']); ?>" class="category-link">
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
                   class="card-img-top thumbnail rounded-top" 
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
              <p class="mb-1 fw-bold text-primary fs-5">₹<?php echo number_format($p['price'], 2); ?></p>
              <p class="text-muted small mb-3"><i class="bi bi-boxes"></i> Stock: <?php echo (int)$p['stock']; ?></p>
              <div class="mt-auto d-flex gap-2">
                <a href="product.php?id=<?php echo $p['id']; ?>" class="btn btn-sm btn-outline-primary flex-grow-1" aria-label="View product <?php echo esc($p['name']); ?>">
                  <i class="bi bi-eye"></i> View
                </a>
                <?php if($p['stock'] > 0): ?>
                  <form method="post" action="add_to_cart.php" class="d-inline-block flex-grow-1" aria-label="Add product <?php echo esc($p['name']); ?> to cart">
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