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

// If not found, show 404
if (!$product) {
    http_response_code(404);
    echo "<h1>Product not found</h1>";
    exit;
}

// Ensure product is an array and populate default keys to avoid undefined indices
if (!is_array($product)) {
  http_response_code(404);
  echo "<h1>Product not found</h1>";
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

// ensure $product is a usable array; if not found, show 404
if (!$product || !is_array($product)) {
  http_response_code(404);
  require __DIR__ . '/templates/header.php';
  echo "<div class='container my-4'><h1>Product not found</h1><p>The product you requested was not found.</p></div>";
  require __DIR__ . '/templates/footer.php';
  exit;
}

$images = [];
if (!empty($product['images'])) {
  $images = json_decode($product['images'], true);
  if (!is_array($images)) $images = [];
} else {
  $images = [];
}

?><!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?php echo esc($product['name'] ?? ''); ?> - Vetriarasiwatersupply</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
  <link rel="stylesheet" href="<?php echo $base_url; ?>/assets/css/custom.css">
</head>
<body>
<?php include __DIR__ . '/templates/header.php'; ?>

<div class="container my-4">
  <div class="row g-4">
    <div class="col-md-6">
      <div class="card p-3">
        <?php
        $mainImg = '';
        if (!empty($product['thumbnail'])) $mainImg = $product['thumbnail'];
        elseif (!empty($images[0])) $mainImg = $images[0];
        ?>
        <?php if ($mainImg): ?>
          <img id="mainImage" src="<?php echo $base_url; ?>/uploads/<?php echo esc($mainImg); ?>" class="img-fluid mb-3" alt="<?php echo esc($product['name'] ?? ''); ?>">
        <?php else: ?>
          <div class="bg-light p-5 text-center">No image</div>
        <?php endif; ?>

        <?php if (is_array($images) && count($images) > 0): ?>
          <div class="d-flex gap-2 flex-wrap">
            <?php foreach ($images as $im): ?>
              <img class="preview-thumb rounded" loading="lazy" src="<?php echo $base_url; ?>/uploads/<?php echo esc($im); ?>" style="height:72px; cursor:pointer;" data-src="<?php echo $base_url; ?>/uploads/<?php echo esc($im); ?>">
            <?php endforeach; ?>
          </div>
        <?php endif; ?>
      </div>
    </div>

    <div class="col-md-6">
      <div class="card p-4">
        <h3><?php echo esc($product['name'] ?? ''); ?></h3>
        <p class="text-muted mb-2">Category: <?php echo esc($product['category'] ?? ''); ?></p>
        <p class="fs-4 text-primary fw-bold">₹<?php echo number_format((float)($product['price'] ?? 0), 2); ?></p>
        <p><?php echo nl2br(esc($product['description'] ?? '')); ?></p>
        <p>Stock: <?php echo (int)($product['stock'] ?? 0); ?></p>

        <?php if ((int)($product['stock'] ?? 0) > 0): ?>
          <form method="post" action="<?php echo $base_url; ?>/add_to_cart.php" class="d-flex align-items-center gap-2">
            <input type="hidden" name="product_id" value="<?php echo (int)($product['id'] ?? 0); ?>">
            <label class="mb-0">Qty</label>
            <select name="qty" class="form-select" style="width:110px;">
              <?php for($i=1;$i<=10;$i++): ?>
                <option value="<?php echo $i; ?>"><?php echo $i; ?></option>
              <?php endfor; ?>
            </select>
            <button class="btn btn-success">Add to Cart</button>
          </form>
        <?php else: ?>
          <div class="alert alert-danger mt-2">Out of Stock</div>
        <?php endif; ?>

      </div>
    </div>
  </div>
</div>

<?php include __DIR__ . '/templates/footer.php'; ?>

<script>
// Small thumbnail switching
document.querySelectorAll('.preview-thumb').forEach(function(t){
  t.addEventListener('click', function(){
    var src = this.getAttribute('data-src');
    var main = document.getElementById('mainImage');
    if (main && src) main.src = src;
  });
});
</script>
</body>
</html>

