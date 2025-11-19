<?php
require_once 'init.php';
$id = isset($_GET['id'])? (int)$_GET['id'] : 0;
$stmt = $pdo->prepare('SELECT * FROM products WHERE id = ?');
$stmt->execute([$id]);
$p = $stmt->fetch();
if(!$p){
   header("Location: $base_url/products.php");
    exit;
}
$images = [];
if(!empty($p['images'])) {
    $images = json_decode($p['images'], true);
    if(!is_array($images)) { $images = []; } // fallback if JSON invalid
}

?><!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?php echo esc($p['name']); ?> - Vetriarasiwatersupply</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
  <link rel="stylesheet" href="assets/css/custom.css">
  <style>
    .product-hero img {
      max-height: 420px;
      object-fit: cover;
      width: 100%;
      border-radius: 12px;
      transition: all 0.5s cubic-bezier(0.34, 1.56, 0.64, 1);
      cursor: zoom-in;
    }

    .product-hero {
      position: relative;
      overflow: hidden;
      border-radius: 12px;
    }

    .product-hero img:hover {
      transform: scale(1.05);
    }

    /* Main image container with animation */
    .product-image-container {
      position: relative;
      background: linear-gradient(135deg, #f0fdf4 0%, #dcfce7 100%);
      border-radius: 12px;
      overflow: hidden;
      animation: slideInUp 0.6s ease-out;
    }

    .main-product-image {
      width: 100%;
      height: 400px;
      object-fit: cover;
      display: block;
      transition: all 0.4s ease;
      cursor: zoom-in;
    }

    .main-product-image:hover {
      transform: scale(1.05) rotate(0.5deg);
    }

    /* Thumbnail carousel */
    .thumbnail-carousel {
      display: flex;
      gap: 8px;
      margin-top: 15px;
      padding: 10px;
      background: white;
      border-radius: 8px;
      overflow-x: auto;
      border: 2px solid #dcfce7;
    }

    .thumbnail-item {
      width: 100px;
      height: 100px;
      border-radius: 8px;
      overflow: hidden;
      cursor: pointer;
      transition: all 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
      border: 3px solid transparent;
      flex-shrink: 0;
      position: relative;
    }

    .thumbnail-item img {
      width: 100%;
      height: 100%;
      object-fit: cover;
      transition: all 0.4s ease;
    }

    .thumbnail-item:hover {
      transform: scale(1.15) translateY(-5px);
      box-shadow: 0 10px 25px rgba(34, 197, 94, 0.3);
      border-color: #2258c5ff;
    }

    .thumbnail-item.active {
      border-color: #22c55e;
      box-shadow: 0 8px 20px rgba(34, 197, 94, 0.4);
    }

    .thumbnail-item.active img {
      transform: scale(1.05);
    }

    /* Product details card */
    .product-details-card {
      border: 2px solid #dcfce7;
      border-radius: 12px;
      animation: slideInUp 0.7s ease-out 0.1s both;
      transition: all 0.3s ease;
    }

    .product-details-card:hover {
      box-shadow: 0 8px 25px rgba(34, 197, 94, 0.15);
      border-color: #22c55e;
    }

    .product-price {
      font-size: 2rem;
      color: #22c55e;
      font-weight: 700;
      animation: slideInUp 0.8s ease-out 0.2s both;
    }

    .product-title {
      font-size: 1.8rem;
      font-weight: 700;
      color: #1f2937;
      animation: slideInUp 0.8s ease-out 0.1s both;
    }

    .product-category {
      color: #6b7280;
      font-size: 0.95rem;
      animation: slideInUp 0.8s ease-out 0.15s both;
    }

    .product-description {
      font-size: 1rem;
      line-height: 1.7;
      color: #4b5563;
      animation: slideInUp 0.8s ease-out 0.25s both;
    }

    .stock-badge {
      display: inline-block;
      padding: 8px 16px;
      border-radius: 20px;
      font-weight: 600;
      animation: pulse-glow 2s infinite;
    }

    .stock-badge.in-stock {
      background: #dcfce7;
      color: #15803d;
      border: 2px solid #22c55e;
    }

    .stock-badge.low-stock {
      background: #fef3c7;
      color: #92400e;
      border: 2px solid #f59e0b;
    }

    .stock-badge.out-of-stock {
      background: #fee2e2;
      color: #991b1b;
      border: 2px solid #ef4444;
    }

    .add-to-cart-section {
      animation: slideInUp 0.8s ease-out 0.3s both;
    }

    .quantity-selector {
      display: flex;
      gap: 10px;
      align-items: center;
      margin: 15px 0;
    }

    .quantity-input {
      width: 80px;
      border: 2px solid #dcfce7;
      border-radius: 8px;
      transition: all 0.3s ease;
    }

    .quantity-input:focus {
      border-color: #22c55e;
      box-shadow: 0 0 0 3px rgba(34, 197, 94, 0.1);
    }

    .btn-add-to-cart {
      transition: all 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
      font-weight: 600;
      padding: 12px 24px;
      font-size: 1.05rem;
    }

    .btn-add-to-cart:not(:disabled):hover {
      transform: translateY(-3px);
      box-shadow: 0 10px 25px rgba(34, 197, 94, 0.3);
    }

    .btn-add-to-cart:not(:disabled):active {
      transform: translateY(-1px);
    }

    /* Zoom effect for images on click */
    .image-zoom-modal {
      display: none;
      position: fixed;
      z-index: 1000;
      left: 0;
      top: 0;
      width: 100%;
      height: 100%;
      background-color: rgba(0, 0, 0, 0.7);
      animation: fadeIn 0.3s ease;
    }

    .image-zoom-modal.show {
      display: flex;
      align-items: center;
      justify-content: center;
    }

    .image-zoom-content {
      max-width: 90%;
      max-height: 90vh;
      border-radius: 12px;
      overflow: hidden;
      animation: zoomIn 0.3s ease;
    }

    .image-zoom-content img {
      width: 100%;
      height: auto;
      display: block;
    }

    .image-zoom-close {
      position: absolute;
      top: 20px;
      right: 30px;
      color: white;
      font-size: 28px;
      cursor: pointer;
      transition: all 0.3s ease;
    }

    .image-zoom-close:hover {
      transform: scale(1.2);
    }

    @keyframes fadeIn {
      from { opacity: 0; }
      to { opacity: 1; }
    }

    @keyframes zoomIn {
      from { transform: scale(0.9); opacity: 0; }
      to { transform: scale(1); opacity: 1; }
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

    @keyframes pulse-glow {
      0% { box-shadow: 0 0 0 0 rgba(34, 197, 94, 0.4); }
      70% { box-shadow: 0 0 0 6px rgba(34, 197, 94, 0); }
      100% { box-shadow: 0 0 0 0 rgba(34, 197, 94, 0); }
    }

    .rating-section {
      margin: 20px 0;
      padding: 15px;
      background: linear-gradient(135deg, #f0fdf4 0%, #dcfce7 100%);
      border-radius: 8px;
      animation: slideInUp 0.8s ease-out 0.35s both;
    }

    .rating-stars {
      color: #f59e0b;
      font-size: 1.2rem;
    }
  </style>
</head>
<body>
<?php include __DIR__ . '/templates/header.php'; ?>

<div class="row">
  <div class="col-md-6">
    <!-- Main Product Image -->
    <div class="product-image-container">
      <img id="mainProductImage" src="<?php echo $base_url; ?>/uploads/<?php echo esc($p['thumbnail']); ?>" class="main-product-image" alt="<?php echo esc($p['name']); ?>" onclick="openZoom(this.src)">
    </div>

    <!-- Thumbnail Carousel -->
    <?php if(!empty($images)): ?>
      <div class="thumbnail-carousel">
        <!-- Thumbnail of main image -->
        <div class="thumbnail-item active" onclick="switchImage(this, '<?php echo $base_url; ?>/uploads/<?php echo esc($p['thumbnail']); ?>')">
          <img src="<?php echo $base_url; ?>/uploads/<?php echo esc($p['thumbnail']); ?>" alt="Main">
        </div>
        <!-- Other images -->
        <?php foreach($images as $img): ?>
          <div class="thumbnail-item" onclick="switchImage(this, '<?php echo $base_url; ?>/uploads/<?php echo esc($img); ?>')">
            <img src="<?php echo $base_url; ?>/uploads/<?php echo esc($img); ?>" alt="<?php echo esc($p['name']); ?>">
          </div>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>
  </div>

  <div class="col-md-6">
    <div class="product-details-card p-4">
      <h3 class="product-title"><?php echo esc($p['name']); ?></h3>
      <p class="product-category"><i class="bi bi-tag"></i> Category: <?php echo esc($p['category']); ?></p>
      
      <h4 class="product-price mb-3">
        ₹<?php echo number_format($p['price'], 2); ?>
      </h4>

      <!-- Stock Status -->
      <div class="mb-3">
        <?php 
          $stock = (int)$p['stock'];
          if($stock > 5):
        ?>
          <span class="stock-badge in-stock"><i class="bi bi-check-circle"></i> In Stock (<?php echo $stock; ?> available)</span>
        <?php elseif($stock > 0): ?>
          <span class="stock-badge low-stock"><i class="bi bi-exclamation-circle"></i> Low Stock (<?php echo $stock; ?> left)</span>
        <?php else: ?>
          <span class="stock-badge out-of-stock"><i class="bi bi-x-circle"></i> Out of Stock</span>
        <?php endif; ?>
      </div>

      <!-- Product Description -->
      <div class="product-description mb-4">
        <?php echo nl2br(esc($p['description'])); ?>
      </div>

      <!-- Rating Section -->
      <div class="rating-section">
        <div class="rating-stars mb-2">
          <i class="bi bi-star-fill"></i>
          <i class="bi bi-star-fill"></i>
          <i class="bi bi-star-fill"></i>
          <i class="bi bi-star-fill"></i>
          <i class="bi bi-star-half"></i>
          <span class="text-muted ms-2">(Based on natural quality)</span>
        </div>
      </div>

      <!-- Add to Cart Section -->
      <div class="add-to-cart-section">
        <?php if($stock > 0): ?>
          <form method="post" action="add_to_cart.php">
            <input type="hidden" name="product_id" value="<?php echo $p['id']; ?>">
            
            <div class="quantity-selector">
              <label for="qty" class="form-label fw-600 mb-0">Quantity:</label>
              <input type="number" id="qty" name="qty" value="1" min="1" max="<?php echo $stock; ?>" class="form-control quantity-input">
            </div>

            <button type="submit" class="btn btn-success btn-lg btn-add-to-cart w-100">
              <i class="bi bi-cart-plus"></i> Add to Cart
            </button>
          </form>
        <?php else: ?>
          <button class="btn btn-secondary btn-lg w-100" disabled>
            <i class="bi bi-x-circle"></i> Out of Stock
          </button>
        <?php endif; ?>
      </div>

      <!-- Additional Info -->
      <div class="mt-4 p-3" style="background: #f0fdf4; border-radius: 8px; border-left: 4px solid #22c55e;">
        <p class="mb-2"><i class="bi bi-truck"></i> <strong>Free Delivery</strong> on orders above ₹500</p>
        <p class="mb-2"><i class="bi bi-shield-check"></i> <strong>100% Authentic</strong> Purified Water</p>
        <p class="mb-0"><i class="bi bi-arrow-counterclockwise"></i> <strong>Easy Returns</strong> within 7 days</p>
      </div>
    </div>
  </div>
</div>

<!-- Image Zoom Modal -->
<div class="image-zoom-modal" id="imageZoomModal" onclick="closeZoom(event)">
  <span class="image-zoom-close" onclick="closeZoom()">&times;</span>
  <div class="image-zoom-content" onclick="event.stopPropagation()">
    <img id="zoomedImage" src="" alt="">
  </div>
</div>

<?php include __DIR__ . '/templates/footer.php'; ?>

<script>
  // Switch between images
  function switchImage(element, imageSrc) {
    // Remove active class from all thumbnails
    document.querySelectorAll('.thumbnail-item').forEach(item => {
      item.classList.remove('active');
    });
    
    // Add active class to clicked thumbnail
    element.classList.add('active');
    
    // Update main image with animation
    const mainImg = document.getElementById('mainProductImage');
    mainImg.style.opacity = '0.7';
    mainImg.src = imageSrc;
    
    setTimeout(() => {
      mainImg.style.opacity = '1';
    }, 150);
  }

  // Open zoom modal
  function openZoom(imageSrc) {
    const modal = document.getElementById('imageZoomModal');
    const zoomedImg = document.getElementById('zoomedImage');
    zoomedImg.src = imageSrc;
    modal.classList.add('show');
    document.body.style.overflow = 'hidden';
  }

  // Close zoom modal
  function closeZoom(event) {
    const modal = document.getElementById('imageZoomModal');
    if (event && event.target !== modal) return;
    modal.classList.remove('show');
    document.body.style.overflow = 'auto';
  }

  // Close zoom modal on ESC key
  document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape') {
      closeZoom();
    }
  });

  // Add smooth transition to main image
  const mainImage = document.getElementById('mainProductImage');
  if (mainImage) {
    mainImage.style.transition = 'opacity 0.3s ease';
  }
</script>
</body>
</html>