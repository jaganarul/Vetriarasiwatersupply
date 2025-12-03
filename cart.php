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

.cart-thumb { width:92px; height:92px; object-fit:cover; }

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
                  <tr data-product-id="<?php echo (int)$p['id']; ?>">
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
                          <!-- added class and explicit type submit -->
                          <form method="post" action="<?php echo $base_url; ?>/update_cart.php" class="d-flex gap-2 align-items-center update-cart-form">
                              <input type="hidden" name="product_id" value="<?php echo $p['id']; ?>">
                              <input type="number" name="qty" value="<?php echo $p['qty']; ?>" min="1" max="<?php echo $p['stock']; ?>" class="form-control qty-input" style="width:90px;">
                              <button type="submit" class="btn btn-outline-primary btn-sm">Update</button>
                          </form>
                      </td>

                      <td class="fw-bold text-dark subtotal-cell">₹<?php echo number_format((float)($p['subtotal'] ?? 0),2); ?></td>

                      <td>
                        <form method="post" action="<?php echo $base_url; ?>/remove_from_cart.php" class="remove-cart-form">
                          <input type="hidden" name="product_id" value="<?php echo $p['id']; ?>">
                          <button type="submit" class="btn btn-sm btn-danger remove-btn">Remove</button>
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
            <article class="cart-mobile-item" data-product-id="<?php echo (int)$p['id']; ?>">
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
                    <div class="fw-bold subtotal-cell">₹<?php echo number_format((float)($p['subtotal'] ?? 0),2); ?></div>
                  </div>
                </div>

                <div class="mobile-actions">
                  <form method="post" action="<?php echo $base_url; ?>/update_cart.php" class="d-flex gap-2 update-cart-form">
                    <input type="hidden" name="product_id" value="<?php echo $p['id']; ?>">
                    <input type="number" name="qty" value="<?php echo $p['qty']; ?>" min="1" max="<?php echo $p['stock']; ?>" class="form-control form-control-sm qty-input" style="width:80px;">
                    <button type="submit" class="btn btn-outline-primary btn-sm">Update</button>
                  </form>

                  <form method="post" action="<?php echo $base_url; ?>/remove_from_cart.php" class="remove-cart-form">
                    <input type="hidden" name="product_id" value="<?php echo $p['id']; ?>">
                    <button type="submit" class="btn btn-danger btn-sm">Remove</button>
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
                <div id="cartTotal" class="total-box">Total: ₹<?php echo number_format((float)($total ?? 0),2); ?></div>
                <a href="<?php echo $base_url; ?>/checkout.php" class="btn btn-success checkout-btn px-4">Proceed to Checkout →</a>
            </div>
        </div>

    </div> <!-- /cart-card -->

    <!-- Sticky checkout for small screens -->
    <div class="sticky-checkout d-md-none">
      <div id="cartTotalMobile" class="total-box">Total: ₹<?php echo number_format((float)($total ?? 0),2); ?></div>
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
/*
  Lightweight cart update enhancement:
  - Ensures qty inputs stay within min/max
  - Intercepts update forms and submits via fetch() to update_cart.php
  - Expects JSON response like: { success: true, product_id: 12, qty: 2, subtotal: 199.00, total: 799.00 }
  - If server does not return JSON or fetch fails, it falls back to normal submit (so server-side still works).
  - Does not change your server endpoints or parameters.
*/

document.addEventListener('DOMContentLoaded', function(){

  // enforce min/max on quantity inputs
  document.querySelectorAll('input[type="number"][name="qty"]').forEach(inp=>{
    inp.addEventListener('input',function(){
      let max=parseInt(this.getAttribute('max')||'0',10);
      let val=parseInt(this.value||'0',10);
      if(max>0 && val>max) this.value = max;
      if(isNaN(val) || val < 1) this.value = 1;
    });
  });

  // helper to format currency (matches your server formatting, adjust decimals if needed)
  function fmt(num){
    return '₹' + Number(num).toLocaleString('en-IN', {minimumFractionDigits:2, maximumFractionDigits:2});
  }

  // intercept update forms
  document.querySelectorAll('form.update-cart-form').forEach(form => {
    form.addEventListener('submit', function(e){
      // allow ctrl/cmd or shift submit to perform normal submit if user wants (still, we handle ordinary clicks)
      if(e.isTrusted === false) return; // ignore programmatic submits
      // only intercept if fetch available
      if(!window.fetch) return;
      e.preventDefault();

      let data = new FormData(form);
      // Use fetch to post to the same action
      fetch(form.action, {
        method: form.method || 'POST',
        body: data,
        credentials: 'same-origin',
        headers: {
          'X-Requested-With': 'XMLHttpRequest' // hint for server
        }
      }).then(resp => {
        // Attempt to parse JSON. If server responds with redirect/html, fallback to full reload
        return resp.json().catch(()=> { throw new Error('non-json'); });
      }).then(json => {
        if(!json || !json.success){
          // fallback: if server indicates failure, reload to show server-side message
          return window.location.reload();
        }

        // update the row subtotal and totals on the page
        let pid = String(json.product_id || data.get('product_id'));
        let qty = Number(json.qty);
        let subtotal = Number(json.subtotal || 0);
        let total = Number(json.total || 0);

        // find row elements by product id (desktop row)
        let rowSel = '[data-product-id="'+pid+'"]';
        let row = document.querySelector(rowSel);
        if(row){
          let subCell = row.querySelector('.subtotal-cell');
          if(subCell) subCell.textContent = fmt(subtotal);
        }
        // mobile subtotal cells (article)
        document.querySelectorAll('.cart-mobile-item[data-product-id="'+pid+'"]').forEach(item=>{
          let s = item.querySelector('.subtotal-cell');
          if(s) s.textContent = fmt(subtotal);
        });

        // update totals
        let totalEl = document.getElementById('cartTotal');
        if(totalEl) totalEl.textContent = 'Total: ' + fmt(total);
        let totalMobile = document.getElementById('cartTotalMobile');
        if(totalMobile) totalMobile.textContent = 'Total: ' + fmt(total);

        // Optional: show a small success flash on the update button
        let btn = form.querySelector('button[type="submit"]');
        if(btn){
          let old = btn.innerHTML;
          btn.innerHTML = 'Updated ✓';
          btn.disabled = true;
          setTimeout(()=>{ btn.innerHTML = old; btn.disabled = false; }, 1200);
        }

      }).catch(err=>{
        // if fetch fails or server didn't return json, fallback to normal submit to let server handle it
        console.warn('AJAX update failed, falling back to normal submit.', err);
        form.submit();
      });
    });
  });

  // keep remove forms as normal submits (unless you prefer AJAX here too)
  // Optionally add a confirm prompt for remove
  document.querySelectorAll('form.remove-cart-form').forEach(f=>{
    f.addEventListener('submit', function(e){
      // small confirm (feel free to remove)
      if(!confirm('Remove this item from cart?')) e.preventDefault();
    });
  });

});
</script>

</body>
</html>
