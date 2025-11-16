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
  <title>Vetriarasi Water Supply - Reliable Water Solutions</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
  <link rel="stylesheet" href="assets/css/custom.css">
  <style>
    .hero-banner {
      background: linear-gradient(135deg, #22c55e 0%, #16a34a 100%);
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
      border-left: 4px solid #22c55e;
      transition: all 0.3s ease;
      animation: slideInUp 0.6s ease-out;
    }

    .categories-card:hover {
      box-shadow: 0 8px 20px rgba(34, 197, 94, 0.15);
      transform: translateX(5px);
    }

    .categories-card h6 {
      color: #22c55e;
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
      color: #22c55e;
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
      border-bottom: 3px solid #22c55e;
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

  <!-- Enhanced Hero Banner -->
  <div class="hero-banner mb-5 p-5 rounded d-flex align-items-center justify-content-between" style="min-height: 320px;">
    <div class="hero-content">
      <div class="hero-icon mb-3">
        <i class="bi bi-droplet-half" style="color: white;"></i>
      </div>
      <h2 class="mb-2" style="color: white; font-weight: 700; font-size: 2.5rem;">Welcome to Vetriarasi Water Supply</h2>
      <p style="color: rgba(255,255,255,0.95); font-size: 1.1rem; margin-bottom: 20px;">Reliable water solutions for home and business — bottled water, tanker delivery and service plans.</p>
      <a href="#products" class="btn btn-light btn-lg" style="transition: all 0.3s ease;">
        <i class="bi bi-shop"></i> View Products & Plans
      </a>
    </div>
    <div class="d-none d-md-block" style="max-width:350px; position: relative; z-index: 1;">
      <div style="font-size: 120px; opacity: 0.9; animation: float 4s ease-in-out infinite;">
        💧
      </div>
    </div>
  </div>

  <!-- Guidance Video Sample -->
  <div class="row mb-5">
    <div class="col-md-8">
      <h5>User Guidance Video</h5>
      <p class="text-muted">Watch a short walkthrough on how to place an order, choose a plan, and track deliveries.</p>
      <div class="ratio ratio-16x9">
        <!-- Replace the src below with a real guidance video URL when available -->
        <iframe src="https://www.youtube.com/embed/dQw4w9WgXcQ" title="Guidance video" allowfullscreen></iframe>
      </div>
    </div>
    <div class="col-md-4">
      <div class="card p-3 shadow-sm">
        <h6 class="mb-2">Why choose Vetriarasi?</h6>
        <ul class="small">
          <li>Professional delivery & support</li>
          <li>Pure, tested water products</li>
          <li>Flexible payment (UPI / COD)</li>
          <li>Track orders in real time</li>
        </ul>
        <a href="product.php" class="btn btn-primary mt-3">Browse Products</a>
      </div>
    </div>
  </div>

  <!-- Features Section -->
  <div class="row g-3 mb-5">
    <div class="col-md-3 col-sm-6">
      <div class="card p-4 text-center border-0 shadow-sm" style="transition: all 0.3s ease; animation: slideInUp 0.6s ease-out;">
        <div style="font-size: 2.5rem; color: #22c55e; margin-bottom: 10px;">
          <i class="bi bi-check-circle"></i>
        </div>
        <h6 style="color: #22c55e; font-weight: 700;">100% Authentic</h6>
        <p class="text-muted small">Certified natural products</p>
      </div>
    </div>
    <div class="col-md-3 col-sm-6">
      <div class="card p-4 text-center border-0 shadow-sm" style="transition: all 0.3s ease; animation: slideInUp 0.6s ease-out 0.1s both;">
        <div style="font-size: 2.5rem; color: #22c55e; margin-bottom: 10px;">
          <i class="bi bi-truck"></i>
        </div>
        <h6 style="color: #22c55e; font-weight: 700;">Fast Delivery</h6>
        <p class="text-muted small">Quick shipping nationwide</p>
      </div>
    </div>
    <div class="col-md-3 col-sm-6">
      <div class="card p-4 text-center border-0 shadow-sm" style="transition: all 0.3s ease; animation: slideInUp 0.6s ease-out 0.2s both;">
        <div style="font-size: 2.5rem; color: #22c55e; margin-bottom: 10px;">
          <i class="bi bi-arrow-counterclockwise"></i>
        </div>
        <h6 style="color: #22c55e; font-weight: 700;">Easy Returns</h6>
        <p class="text-muted small">30-day money back guarantee</p>
      </div>
    </div>
    <div class="col-md-3 col-sm-6">
      <div class="card p-4 text-center border-0 shadow-sm" style="transition: all 0.3s ease; animation: slideInUp 0.6s ease-out 0.3s both;">
        <div style="font-size: 2.5rem; color: #22c55e; margin-bottom: 10px;">
          <i class="bi bi-chat-dots"></i>
        </div>
        <h6 style="color: #22c55e; font-weight: 700;">24/7 Support</h6>
        <p class="text-muted small">Dedicated customer service</p>
      </div>
    </div>
  </div>

  <!-- Products Section -->
  <div id="products" class="row g-3">
    <div class="col-md-3 d-none d-md-block">
      <div class="card categories-card p-4 mb-3">
        <h6><i class="bi bi-list"></i> Categories</h6>
        <ul class="list-unstyled mt-3">
          <?php
            $cats = $pdo->query("SELECT DISTINCT category FROM products WHERE category IS NOT NULL AND category <> ''")->fetchAll();
            foreach($cats as $c){ if(!$c['category']) continue;
          ?>
            <li><a href="index.php?category=<?php echo urlencode($c['category']); ?>"><i class="bi bi-chevron-right"></i> <?php echo esc($c['category']); ?></a></li>
          <?php } ?>
        </ul>
      </div>
    </div>
    <div class="col-md-9">
      <div class="products-section">
        <h5><i class="bi bi-star"></i> <?php echo $q? 'Search results for: '.esc($q) : ($category? 'Category: '.esc($category) : 'Latest Products'); ?></h5>
      </div>
      <div class="row g-4">
        <?php foreach($products as $p): ?>
          <div class="col-sm-6 col-md-4 col-lg-4 col-xl-3 product-card-wrapper">
            <div class="card card-product shadow-sm position-relative h-100">
              <?php if((int)$p['stock'] <= 5 && (int)$p['stock'] > 0): ?>
                <span class="badge badge-low-stock position-absolute" style="right:10px;top:10px;"><i class="bi bi-exclamation-lg"></i> Low stock</span>
              <?php elseif((int)$p['stock'] <= 0): ?>
                <span class="badge bg-danger position-absolute" style="right:10px;top:10px;"><i class="bi bi-x-lg"></i> Out of Stock</span>
              <?php endif; ?>
              <?php if($p['thumbnail']): ?>
                <img src="uploads/<?php echo esc($p['thumbnail']); ?>" class="card-img-top thumbnail" alt="<?php echo esc($p['name']); ?>">
              <?php else: ?>
                <div class="bg-light p-5 text-center" style="height: 220px; display: flex; align-items: center; justify-content: center;">
                  <span class="text-muted"><i class="bi bi-image"></i> No image</span>
                </div>
              <?php endif; ?>
              <div class="card-body">
                <h6 class="card-title h6 mb-1 text-truncate"><?php echo esc($p['name']); ?></h6>
                <p class="mb-1 fw-600" style="color: #22c55e; font-size: 1.2rem;">₹<?php echo number_format($p['price'], 2); ?></p>
                <p class="text-muted small mb-3"><i class="bi bi-boxes"></i> Stock: <?php echo (int)$p['stock']; ?></p>
                <div class="d-flex gap-2">
                  <a href="product.php?id=<?php echo $p['id']; ?>" class="btn btn-sm btn-outline-primary flex-grow-1">
                    <i class="bi bi-eye"></i> View
                  </a>
                  <?php if($p['stock'] > 0): ?>
                    <form method="post" action="add_to_cart.php" class="d-inline-block flex-grow-1">
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
      </div>
    </div>
  </div>

<?php include __DIR__ . '/templates/footer.php'; ?>