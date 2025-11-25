<?php
require_once __DIR__ . '/init.php';

// Fetch all products
$stmt = $pdo->query("SELECT * FROM products ORDER BY id DESC");
$products = $stmt->fetchAll();
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>All Products - Vetriarasiwatersupply</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">

  <style>
    .product-card img {
      height: 200px;
      object-fit: cover;
    }
  </style>
</head>
<body>

<?php include __DIR__ . '/templates/header.php'; ?>

<div class="container my-4">
  <h2 class="mb-4">All Products</h2>

  <div class="row g-4">

    <?php foreach ($products as $p): ?>

      <?php
      // Determine thumbnail
      $thumb = '';
      if (!empty($p['thumbnail'])) {
        $thumb = $p['thumbnail'];
      } elseif (!empty($p['images'])) {
        $imgs = json_decode($p['images'], true);
        if (is_array($imgs) && !empty($imgs[0])) {
          $thumb = $imgs[0];
        }
      }
      ?>

      <div class="col-md-3 col-sm-6">
        <div class="card product-card shadow-sm">

          <?php if ($thumb): ?>
            <img 
              src="<?php echo $base_url; ?>/uploads/<?php echo esc($thumb); ?>" 
              class="card-img-top" 
              alt="<?php echo esc($p['name']); ?>"
            >
          <?php else: ?>
            <div class="bg-light p-5 text-center">No Image</div>
          <?php endif; ?>

          <div class="card-body">
            <h5 class="card-title"><?php echo esc($p['name']); ?></h5>
            <p class="text-muted mb-1">Category: <?php echo esc($p['category']); ?></p>
            <p class="fw-bold fs-5 text-primary">₹<?php echo number_format($p['price'], 2); ?></p>

            <a href="<?php echo $base_url; ?>/singleproduct.php?id=<?php echo (int)$p['id']; ?>" 
               class="btn btn-primary w-100">
              View Product
            </a>
          </div>
        </div>
      </div>

    <?php endforeach; ?>

  </div>
</div>

<?php include __DIR__ . '/templates/footer.php'; ?>

</body>
</html>
