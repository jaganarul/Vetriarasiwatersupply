<?php
require_once __DIR__ . '/init.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id <= 0) {
    http_response_code(404);
    echo "<h1>Product not found</h1>";
    exit;
}

$stmt = $pdo->prepare('SELECT * FROM products WHERE id = ?');
$stmt->execute([$id]);
$product = $stmt->fetch();

if (!$product || !is_array($product)) {
    http_response_code(404);
    require __DIR__ . '/templates/header.php';
    echo "<div class='container my-4'><h1>Product not found</h1></div>";
    require __DIR__ . '/templates/footer.php';
    exit;
}

$product = array_merge([
    'id' => 0,
    'name' => '',
    'category' => '',
    'price' => 0,
    'description' => '',
    'stock' => 0,
    'thumbnail' => null,
    'images' => null
], $product);

$images = [];
if (!empty($product['images'])) {
    $images = json_decode($product['images'], true);
    if (!is_array($images)) $images = [];
}
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?php echo esc($product['name']); ?> - Vetriarasiwatersupply</title>

  <!-- Bootstrap -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">

  <!-- Main stylesheet -->
  <link rel="stylesheet" href="<?php echo $base_url; ?>/assets/css/custom.css">

  <!-- Enhanced eCommerce Styles -->
  <style>
    body { background: #f5f7fa; }

    .product-wrapper { background: #fff; border-radius: 12px; padding: 20px; }
    .product-title { font-size: 28px; font-weight: 700; color: #222; }
    .product-category { font-size: 14px; color: #6c757d; }
    .product-price { font-size: 32px; font-weight: 700; color: #007bff; }
    .product-description { font-size: 16px; line-height: 1.6; }

    #mainImage {
      border-radius: 12px;
      border: 1px solid #ddd;
      width: 100%;
    }

    .thumb-gallery img {
      height: 70px;
      width: 70px;
      object-fit: cover;
      border-radius: 8px;
      border: 2px solid #e0e0e0;
      transition: 0.2s;
      cursor: pointer;
    }
    .thumb-gallery img:hover {
      border-color: #007bff;
    }

    .qty-select { width: 110px; }

    .btn-add-cart {
      background: #28a745;
      color: #fff;
      padding: 12px 20px;
      font-size: 18px;
      font-weight: 600;
      border-radius: 10px;
      width: 100%;
    }
    .btn-add-cart:hover {
      background: #218838;
    }
  </style>

</head>
<body>

<?php include __DIR__ . '/templates/header.php'; ?>

<div class="container my-4">
  <div class="row g-4">

    <!-- LEFT SECTION - IMAGES -->
    <div class="col-md-6">
      <div class="product-wrapper">

        <?php
        $mainImg = '';
        if (!empty($product['thumbnail'])) $mainImg = $product['thumbnail'];
        elseif (!empty($images[0])) $mainImg = $images[0];
        ?>

        <?php if ($mainImg): ?>
          <img id="mainImage"
               src="<?php echo $base_url; ?>/uploads/<?php echo esc($mainImg); ?>"
               class="img-fluid mb-3"
               alt="<?php echo esc($product['name']); ?>">
        <?php else: ?>
          <div class="bg-light text-center p-5">No Image</div>
        <?php endif; ?>

        <?php if (!empty($images)): ?>
          <div class="thumb-gallery d-flex gap-2 flex-wrap">
            <?php foreach ($images as $im): ?>
              <img 
                src="<?php echo $base_url; ?>/uploads/<?php echo esc($im); ?>"
                data-src="<?php echo $base_url; ?>/uploads/<?php echo esc($im); ?>"
              >
            <?php endforeach; ?>
          </div>
        <?php endif; ?>

      </div>
    </div>

    <!-- RIGHT SECTION - DETAILS -->
    <div class="col-md-6">
      <div class="product-wrapper">

        <h1 class="product-title"><?php echo esc($product['name']); ?></h1>

        <p class="product-category mb-1">
          Category: <?php echo esc($product['category']); ?>
        </p>

        <div class="product-price">
          ₹<?php echo number_format((float)$product['price'], 2); ?>
        </div>

        <p class="product-description mt-3">
          <?php echo nl2br(esc($product['description'])); ?>
        </p>

        <p class="mt-2">
          <strong>Stock:</strong> <?php echo (int)$product['stock']; ?>
        </p>

        <?php if ($product['stock'] > 0): ?>
          <form method="post" action="<?php echo $base_url; ?>/add_to_cart.php" class="mt-3">
            <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">

            <div class="d-flex gap-3 align-items-center">
              <select name="qty" class="form-select qty-select">
                <?php for($i=1;$i<=10;$i++): ?>
                  <option value="<?php echo $i; ?>"><?php echo $i; ?></option>
                <?php endfor; ?>
              </select>
            </div>

            <button class="btn btn-add-cart mt-3">
              <i class="bi bi-cart-plus"></i> Add to Cart
            </button>
          </form>

        <?php else: ?>
          <div class="alert alert-danger mt-3">Out of Stock</div>
        <?php endif; ?>

      </div>
    </div>

  </div>
</div>

<?php include __DIR__ . '/templates/footer.php'; ?>

<script>
document.querySelectorAll('.thumb-gallery img').forEach(img => {
  img.addEventListener('click', () => {
    document.getElementById('mainImage').src = img.dataset.src;
  });
});
</script>

</body>
</html>
