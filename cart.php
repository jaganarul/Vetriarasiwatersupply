<?php
require_once 'init.php';
$cart = $_SESSION['cart'] ?? [];
$products = [];
$total = 0;
if($cart){
    $ids = array_keys($cart);
    $placeholders = implode(',', array_fill(0, count($ids), '?'));
    $stmt = $pdo->prepare("SELECT id,name,price,stock,thumbnail FROM products WHERE id IN ($placeholders)");
    $stmt->execute($ids);
    $rows = $stmt->fetchAll();
    foreach($rows as $r){
        $r['qty'] = $cart[$r['id']];
        $r['subtotal'] = $r['qty'] * $r['price'];
        $total += $r['subtotal'];
        $products[] = $r;
    }
}
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
  <link rel="stylesheet" href="<?php echo $base_url; ?>/assets/css/custom.css">
  <title>Cart</title>

<style>
/* Base layout */
.cart-container {
    padding: 28px 12px;
    max-width: 1180px;
    margin: 0 auto;
    min-height: 70vh;
}

/* Card wrapper for desktop */
.cart-card {
    background: #ffffff;
    border-radius: 14px;
    padding: 22px;
    box-shadow: 0 8px 28px rgba(0,0,0,0.08);
    animation: fadeIn .42s ease;
}

/* Table (desktop) */
.cart-table img {
    border-radius: 12px;
    object-fit: cover;
    transition: transform 0.3s ease;
}
.cart-table img:hover { transform: scale(1.05); }
.cart-table th {
    background: #f1f5ff;
    border-bottom: 2px solid #dbe3ff;
    font-weight: 700;
    color: #003d8f;
}
.cart-table td {
    vertical-align: middle;
    padding: 12px;
}

/* Totals + checkout */
.total-box {
    background: linear-gradient(90deg,#0b74ff,#00d4ff);
    color: #fff;
    padding: 16px 20px;
    border-radius: 12px;
    font-size: 18px;
    font-weight: 700;
    min-width: 220px;
    text-align: center;
}
.checkout-btn {
    height: 48px;
    border-radius: 12px;
    font-size: 16px;
}

/* Mobile stacked cards */
.cart-mobile-list { display: none; gap: 12px; }
.cart-mobile-item {
    display: flex;
    gap: 12px;
    padding: 16px;
    border-radius: 14px;
    background: #fff;
    box-shadow: 0 12px 24px rgba(0,0,0,0.06);
    align-items: center;
    transition: transform 0.2s ease;
}
.cart-mobile-item:hover { transform: translateY(-2px); }
.cart-mobile-thumb {
    width: 84px;
    height: 84px;
    flex-shrink: 0;
    border-radius: 12px;
    overflow: hidden;
    display: inline-block;
    background: linear-gradient(90deg,#eef8ff,#f7fdff);
}
.cart-mobile-thumb img { width:100%; height:100%; object-fit:cover; display:block; }

/* Buttons on mobile */
.mobile-actions { display:flex; gap:8px; width:100%; margin-top:6px; }
.mobile-actions .btn { flex:1; }

/* Quantity selector styling */
.qty-input, .form-select { border-radius: 10px; }

/* Responsive behaviors */
@media (max-width: 991.98px) {
  .cart-table { display: none; }
  .cart-mobile-list { display: flex; flex-direction: column; }
  .d-desktop-only { display: none !important; }
  .total-box { min-width: 160px; font-size: 16px; }
}

/* Sticky checkout on small screens */
@media (max-width: 575.98px) {
  .sticky-checkout {
    position: fixed;
    left: 12px;
    right: 12px;
    bottom: 12px;
    z-index: 11000;
    display: flex;
    gap: 12px;
    align-items: center;
  }
  .sticky-checkout .total-box { flex: 1; text-align: left; padding-left: 14px; padding-right: 14px; }
  .sticky-checkout .checkout-btn { min-width: 140px; }
  body { padding-bottom: 92px; }
}

/* fade animation */
@keyframes fadeIn { from {opacity:0; transform:translateY(8px);} to {opacity:1; transform:none;} }

/* Suggested products (Amazon style) */
.suggested-products {
    margin-top: 32px;
}
.suggested-products h5 {
    margin-bottom: 16px;
    font-weight: 700;
}
.suggested-products .card {
    border-radius: 12px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.05);
    transition: transform 0.2s ease;
}
.suggested-products .card:hover { transform: translateY(-4px); }
.suggested-products img { border-radius: 12px; object-fit: cover; height:160px; width:100%; }
</style>
</head>

<body>

<?php include __DIR__ . '/templates/header.php'; ?>

<div class="container cart-container">

    <h2 class="fw-bold mb-3 text-primary">🛒 Your Shopping Cart</h2>

    <?php if(!$products): ?>
        <div class="alert alert-info text-center py-4 fs-5">
            Your cart is empty.
            <br><a href="<?php echo $base_url; ?>/product.php" class="btn btn-primary btn-sm mt-3">Browse Products</a>
        </div>

    <?php else: ?>

    <div class="cart-card">

        <!-- Desktop table -->
        <div class="table-responsive d-desktop-only">
          <table class="table cart-table align-middle mb-0">
              <thead>
                  <tr>
                      <th>Item</th>
                      <th>Product</th>
                      <th>Price</th>
                      <th class="col-qty">Qty</th>
                      <th>Subtotal</th>
                      <th></th>
                  </tr>
              </thead>

              <tbody>
              <?php foreach($products as $p): ?>
                  <tr>
                      <td>
                        <?php if($p['thumbnail']): ?>
                          <img src="<?php echo $base_url; ?>/uploads/<?php echo esc($p['thumbnail']); ?>" alt="<?php echo esc($p['name']); ?>" class="cart-thumb img-fluid rounded" loading="lazy">
                        <?php else: ?>
                          <div class="cart-placeholder">
                            <i class="bi bi-image"></i>
                          </div>
                        <?php endif; ?>
                      </td>

                      <td class="fw-semibold"><?php echo esc($p['name']); ?></td>
                      <td class="fw-bold text-success">₹<?php echo number_format((float)($p['price'] ?? 0),2); ?></td>

                      <td>
                          <form method="post" action="<?php echo $base_url; ?>/update_cart.php" class="d-flex gap-2 align-items-center">
                              <input type="hidden" name="product_id" value="<?php echo $p['id']; ?>">
                              <input type="number" name="qty" value="<?php echo $p['qty']; ?>" min="1" max="<?php echo $p['stock']; ?>" class="form-control qty-input">
                              <button class="btn btn-outline-primary btn-sm">Update</button>
                          </form>
                      </td>

                      <td class="fw-bold text-dark">₹<?php echo number_format((float)($p['subtotal'] ?? 0),2); ?></td>

                      <td>
                        <form method="post" action="<?php echo $base_url; ?>/remove_from_cart.php">
                          <input type="hidden" name="product_id" value="<?php echo $p['id']; ?>">
                          <button class="btn btn-sm btn-danger remove-btn">Remove</button>
                        </form>
                      </td>
                  </tr>
              <?php endforeach; ?>
              </tbody>
          </table>
        </div>

        <!-- Mobile stacked list -->
        <div class="cart-mobile-list">
          <?php foreach($products as $p): ?>
            <article class="cart-mobile-item">
              <div class="cart-mobile-thumb">
                <?php if($p['thumbnail']): ?>
                  <img src="<?php echo $base_url; ?>/uploads/<?php echo esc($p['thumbnail']); ?>" alt="<?php echo esc($p['name']); ?>" class="img-fluid rounded">
                <?php else: ?>
                  <div class="text-center text-muted"><i class="bi bi-image"></i></div>
                <?php endif; ?>
              </div>

              <div style="flex:1; min-width:0;">
                <h6 class="mb-1 fw-bold"><?php echo esc($p['name']); ?></h6>
                <div class="d-flex justify-content-between align-items-center">
                  <div>
                    <div class="price text-success fw-bold">₹<?php echo number_format((float)($p['price'] ?? 0),2); ?></div>
                    <div class="small text-muted">Stock: <?php echo (int)$p['stock']; ?></div>
                  </div>
                  <div class="text-end">
                    <div class="small text-muted">Subtotal</div>
                    <div class="fw-bold">₹<?php echo number_format((float)($p['subtotal'] ?? 0),2); ?></div>
                  </div>
                </div>

                <div class="mobile-actions">
                  <form method="post" action="<?php echo $base_url; ?>/update_cart.php" class="d-flex gap-2">
                    <input type="hidden" name="product_id" value="<?php echo $p['id']; ?>">
                    <input type="number" name="qty" value="<?php echo $p['qty']; ?>" min="1" max="<?php echo $p['stock']; ?>" class="form-control form-control-sm">
                    <button class="btn btn-outline-primary btn-sm">Update</button>
                  </form>

                  <form method="post" action="<?php echo $base_url; ?>/remove_from_cart.php">
                    <input type="hidden" name="product_id" value="<?php echo $p['id']; ?>">
                    <button class="btn btn-danger btn-sm">Remove</button>
                  </form>
                </div>
              </div>
            </article>
          <?php endforeach; ?>
        </div>

        <!-- Desktop totals row -->
        <div class="d-none d-md-flex justify-content-between align-items-center mt-4">
            <div></div>
            <div class="d-flex gap-3 align-items-center">
                <div class="total-box">Total: ₹<?php echo number_format((float)($total ?? 0),2); ?></div>
                <a href="<?php echo $base_url; ?>/checkout.php" class="btn btn-success checkout-btn px-4">Proceed to Checkout →</a>
            </div>
        </div>

    </div> <!-- /cart-card -->

    <!-- Sticky checkout for small screens -->
    <div class="sticky-checkout d-md-none">
      <div class="total-box">Total: ₹<?php echo number_format((float)($total ?? 0),2); ?></div>
      <a href="<?php echo $base_url; ?>/checkout.php" class="btn btn-success checkout-btn px-3">Checkout</a>
    </div>

    <!-- Suggested Products -->
    <div class="suggested-products">
      <h5>You might also like</h5>
      <div class="row g-3">
        <?php
        // demo suggested products: pick 4 random products (replace with real query in production)
        $suggested = $pdo->query('SELECT * FROM products ORDER BY RAND() LIMIT 4')->fetchAll();
        foreach($suggested as $sp): ?>
          <div class="col-6 col-md-3">
            <div class="card p-2">
              <img src="<?php echo $base_url; ?>/uploads/<?php echo esc($sp['thumbnail']); ?>" alt="<?php echo esc($sp['name']); ?>">
              <div class="mt-2">
                <h6 class="mb-1"><?php echo esc($sp['name']); ?></h6>
                <div class="fw-bold text-success">₹<?php echo number_format((float)$sp['price'],2); ?></div>
              </div>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    </div>

    <?php endif; ?>

</div>

<?php include __DIR__ . '/templates/footer.php'; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
document.querySelectorAll('input[type="number"][name="qty"]').forEach(inp=>{
  inp.addEventListener('input',function(){
    let max=parseInt(this.getAttribute('max')||'0',10);
    let val=parseInt(this.value||'0',10);
    if(max>0&&val>max) this.value=max;
    if(val<1) this.value=1;
  });
});
</script>

</body>
</html>
