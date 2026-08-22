<?php
// category.php - Displays categories list OR products from selected category
require_once __DIR__ . '/init.php';

// Check if a specific category is selected
$selected_category = isset($_GET['name']) ? trim($_GET['name']) : '';
$products = [];

// If category is selected, fetch products
if (!empty($selected_category)) {
    try {
        $stmt = $pdo->prepare('SELECT * FROM products WHERE category = ? ORDER BY name ASC');
        $stmt->execute([$selected_category]);
        $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        error_log('Products fetch error: ' . $e->getMessage());
        $products = [];
    }
} else {
    // Fetch distinct categories for the list view
    try {
        if (!empty($pdo)) {
            $stmt = $pdo->query("SELECT DISTINCT category FROM products WHERE category IS NOT NULL AND category <> '' ORDER BY category ASC");
            $cats = $stmt->fetchAll(PDO::FETCH_ASSOC);
        }
    } catch (Exception $e) {
        error_log('Categories fetch error: ' . $e->getMessage());
        $cats = [];
    }
}

// Fallback for base_url in case it's not set (prevents broken hrefs)
$base_url = isset($base_url) ? rtrim($base_url, '/') : '';
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
  <link rel="stylesheet" href="<?php echo htmlspecialchars($base_url, ENT_QUOTES, 'UTF-8'); ?>/assets/css/custom.css">
  <title><?php echo !empty($selected_category) ? htmlspecialchars($selected_category, ENT_QUOTES, 'UTF-8') . ' - Products' : 'Categories'; ?></title>
</head>
<body>

<?php include __DIR__ . '/templates/header.php'; ?>

<div class="container py-5">
  <?php if (!empty($selected_category)): ?>
      <!-- PRODUCTS VIEW -->
      <!-- Breadcrumb -->
      <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="<?php echo htmlspecialchars($base_url, ENT_QUOTES, 'UTF-8'); ?>/">Home</a></li>
          <li class="breadcrumb-item"><a href="<?php echo htmlspecialchars($base_url, ENT_QUOTES, 'UTF-8'); ?>/category.php">Categories</a></li>
          <li class="breadcrumb-item active" aria-current="page"><?php echo htmlspecialchars($selected_category, ENT_QUOTES, 'UTF-8'); ?></li>
        </ol>
      </nav>

      <h2 class="mb-4"><?php echo htmlspecialchars($selected_category, ENT_QUOTES, 'UTF-8'); ?> Products</h2>

      <?php if (empty($products)): ?>
          <div class="alert alert-info">No products found in this category.</div>
      <?php else: ?>
          <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-lg-4 g-4 mb-5">
              <?php foreach ($products as $p): ?>
                  <div class="col">
                      <div class="card h-100 product-card shadow-sm">
                          <img src="<?php echo htmlspecialchars($base_url . '/uploads/' . $p['thumbnail'], ENT_QUOTES, 'UTF-8'); ?>" 
                               class="card-img-top" alt="<?php echo htmlspecialchars($p['name'], ENT_QUOTES, 'UTF-8'); ?>" 
                               style="height: 200px; object-fit: cover;">
                          <div class="card-body d-flex flex-column">
                              <h5 class="card-title"><?php echo htmlspecialchars($p['name'], ENT_QUOTES, 'UTF-8'); ?></h5>
                              <p class="card-text text-muted small"><?php echo htmlspecialchars(substr($p['description'], 0, 60), ENT_QUOTES, 'UTF-8'); ?>...</p>
                              <div class="mt-auto">
                                  <p class="mb-3">
                                      <span class="h5 text-primary fw-bold">₹<?php echo htmlspecialchars(number_format($p['price'], 2), ENT_QUOTES, 'UTF-8'); ?></span>
                                  </p>
                                  <a href="<?php echo htmlspecialchars($base_url . '/product.php?id=' . $p['id'], ENT_QUOTES, 'UTF-8'); ?>" class="btn btn-primary w-100 btn-sm">
                                      <i class="bi bi-eye"></i> View Details
                                  </a>
                              </div>
                          </div>
                      </div>
                  </div>
              <?php endforeach; ?>
          </div>
      <?php endif; ?>
  <?php else: ?>
      <!-- CATEGORIES LIST VIEW -->
      <h3 class="mb-4">Categories</h3>

      <?php if (empty($cats)): ?>
          <div class="alert alert-warning">No categories found.</div>
      <?php else: ?>
          <div class="list-group mb-4">
          <?php foreach ($cats as $c):
              if (!isset($c['category'])) continue;
              $rawCat = trim($c['category']);
              if ($rawCat === '') continue;

              $href = $base_url . '/category.php?name=' . urlencode($rawCat);
          ?>
              <a class="list-group-item list-group-item-action d-flex justify-content-between align-items-center"
                 href="<?php echo htmlspecialchars($href, ENT_QUOTES, 'UTF-8'); ?>">
                 <span><?php echo htmlspecialchars($rawCat, ENT_QUOTES, 'UTF-8'); ?></span>
                 <i class="bi bi-chevron-right"></i>
              </a>
          <?php endforeach; ?>
          </div>
      <?php endif; ?>
  <?php endif; ?>
</div>

<?php include __DIR__ . '/templates/footer.php'; ?>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<style>
  .product-card {
    transition: all 0.3s ease;
    border: 1px solid #e0e0e0;
  }
  .product-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 24px rgba(0, 0, 0, 0.15) !important;
  }
</style>
</body>
</html>
