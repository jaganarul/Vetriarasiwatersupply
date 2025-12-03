<?php
// categories.php
require_once __DIR__ . '/init.php';

// Toggle: set to true if you have server rewrite to handle /category/slug
// Default false => links will use category.php?name=...
define('USE_PRETTY_URLS', false);

// Fallback for base_url in case it's not set (prevents broken hrefs)
$base_url = isset($base_url) ? rtrim($base_url, 'category.php') : '';

// fetch distinct categories safely
$cats = [];
try {
    if (!empty($pdo)) {
        $stmt = $pdo->query("SELECT DISTINCT category FROM products WHERE category IS NOT NULL AND category <> ''");
        $cats = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
} catch (Exception $e) {
    // Log the error in your real app; keep user-facing behavior graceful
    error_log('Categories fetch error: ' . $e->getMessage());
    $cats = [];
}
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
  <link rel="stylesheet" href="<?php echo htmlspecialchars($base_url, ENT_QUOTES, 'UTF-8'); ?>/assets/css/custom.css">
  <title>Categories</title>
</head>
<body>

<?php include __DIR__ . '/templates/header.php'; ?>

<div class="container py-4">
  <h3 class="mb-3">Categories</h3>

  <?php if (empty($cats)): ?>
      <div class="alert alert-warning">No categories found.</div>
  <?php else: ?>
      <div class="list-group mb-4">
      <?php foreach ($cats as $c):
          if (!isset($c['category'])) continue;
          $rawCat = trim($c['category']);
          if ($rawCat === '') continue;

          // Build both possible URLs; choose depending on USE_PRETTY_URLS
          $prettyUrl = $base_url . '/category.php/' . rawurlencode($rawCat);
          $queryUrl  = $base_url . '/category.php/?name=' . urlencode($rawCat);

          $href = USE_PRETTY_URLS ? $prettyUrl : $queryUrl;
      ?>
          <a class="list-group-item list-group-item-action d-flex justify-content-between align-items-center"
             href="<?php echo htmlspecialchars($href, ENT_QUOTES, 'UTF-8'); ?>">
             <span><?php echo htmlspecialchars($rawCat, ENT_QUOTES, 'UTF-8'); ?></span>
             <i class="bi bi-chevron-right"></i>
          </a>
      <?php endforeach; ?>
      </div>
  <?php endif; ?>
</div>

<?php include __DIR__ . '/templates/footer.php'; ?>

<!-- optional: bootstrap JS once -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
