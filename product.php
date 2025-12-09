<?php
require_once __DIR__ . '/init.php';

// Fetch all products
$stmt = $pdo->query("SELECT * FROM products ORDER BY id DESC");
$products = $stmt->fetchAll();
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>All Products - Vetriarasiwatersupply</title>

  <!-- Bootstrap + icons -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">

  <!-- ======= Enhanced Amazon-like UI (single light mode) ======= -->
  <style>
  :root{
    --accent: #62b0d4ff;           /* Amazon-like accent */
    --muted: #6b7280;
    --card-bg: #ffffff;
    --glass: rgba(2,6,23,0.02);
    --radius: 12px;
    --shadow: 0 8px 20px rgba(2,6,23,0.06);
    --max-w: 1200px;
    --gap: 1.25rem;
    --price-color: #111827;
    --page-bg: #f7fbff;
    --text: #0b1220;
  }

  /* Base */
  html,body{height:100%;margin:0;font-family:Inter,system-ui,-apple-system,Segoe UI,Roboto,Helvetica,Arial;color:var(--text);background:var(--page-bg);}
  .container.my-4{ max-width: var(--max-w); }

  /* Grid gap */
  .row.g-4 { gap: var(--gap); }

  /* Product Card */
  .product-card {
    border-radius: 10px;
    overflow: hidden;
    background: var(--card-bg);
    box-shadow: var(--shadow);
    transition: transform 180ms ease, box-shadow 180ms ease;
    display:flex;
    flex-direction:column;
    height:100%;
    border: 1px solid rgba(11,20,40,0.04);
  }
  .product-card:hover { transform: translateY(-6px); box-shadow: 0 14px 40px rgba(2,6,23,0.08); }

  /* Image area */
  .product-media { position: relative; background: #fff; display:block; }
  .product-media img {
    width:100%; height:220px; object-fit:cover; display:block; transition: transform 260ms ease;
  }
  .product-card:hover .product-media img { transform: scale(1.04); }

  /* badges */
  .card-badges { position:absolute; top:8px; left:8px; display:flex; gap:8px; z-index: 4; }
  .badge-prime { background: var(--accent); color:#fff; font-weight:700; font-size:12px; padding:6px 8px; border-radius:999px; display:inline-flex; align-items:center; gap:6px; box-shadow: 0 6px 18px rgba(2,6,23,0.06); }
  .badge-stock { background: rgba(0,0,0,0.06); color:#111; font-size:12px; padding:5px 7px; border-radius:6px; }

  /* Card body */
  .card-body { padding:14px; display:flex; flex-direction:column; gap:8px; flex:1; }
  .card-title { font-size:0.98rem; font-weight:700; color:var(--text); margin:0 0 4px; min-height:40px; line-height:1.25; overflow:hidden; text-overflow:ellipsis; }
  .card-meta { color:var(--muted); font-size:0.86rem; display:flex; gap:8px; align-items:center; flex-wrap:wrap; }
  .card-price { font-size:1.125rem; font-weight:800; color: var(--price-color); margin-top:4px; }

  /* rating */
  .rating { display:inline-flex; gap:4px; align-items:center; }
  .star { color: #fbbf24; font-size:0.95rem; line-height:1; }

  /* actions */
  .card-actions { margin-top:auto; display:flex; gap:8px; align-items:center; }
  .btn-view {
    flex:1; padding:10px 12px; border-radius:8px; background: linear-gradient(90deg,var(--accent), #126cf3ff); color:#111; font-weight:700; border: none; text-decoration:none; text-align:center;
    transition: transform 140ms ease;
  }
  .btn-view:hover{ transform: translateY(-2px); box-shadow: 0 10px 30px rgba(0,0,0,0.06); }
  .btn-wish {
    padding:8px 10px; border-radius:8px; background: transparent; border:1px solid rgba(11,20,40,0.04); color:var(--text); min-width:44px; display:inline-flex; justify-content:center; align-items:center;
  }
  .btn-wish.active { background: rgba(255,153,0,0.10); border-color: rgba(255,153,0,0.14); color: var(--accent); }

  .small-muted { font-size:0.82rem; color:var(--muted); }

  .no-image { height:220px; display:flex; align-items:center; justify-content:center; background: #fff; color:var(--muted); font-weight:600; }

  /* Quick view modal */
  #pvQuickView {
    position: fixed; inset: 0; display: none; align-items: center; justify-content:center; z-index: 2000;
    background: rgba(2,6,23,0.5); padding: 20px;
  }
  #pvQuickView .pv-card { width: min(980px, 96%); max-height: 92vh; overflow:auto; background: #fff; border-radius:12px; padding:18px; display:grid; grid-template-columns: 1fr 1fr; gap:18px; box-shadow: 0 12px 40px rgba(2,6,23,0.08); }
  #pvQuickView img { width:100%; height:420px; object-fit:cover; border-radius:8px; }
  #pvQuickView .pv-close { position:absolute; top:18px; right:18px; background:transparent; border:0; color:#111; font-size:1.6rem; }

  /* responsive adjustments */
  @media (max-width: 991px) {
    .product-media img, .no-image { height:180px; }
  }
  @media (max-width: 575px) {
    .product-media img, .no-image { height:160px; }
    .card-title { min-height:48px; font-size:0.95rem; }
    #pvQuickView .pv-card { grid-template-columns: 1fr; }
    #pvQuickView img { height: 260px; }
  }

  /* accessibility helper */
  .visually-hidden { position: absolute !important; height: 1px; width: 1px; overflow: hidden; clip: rect(1px, 1px, 1px, 1px); white-space: nowrap; }
  </style>
  <!-- =================================================================== -->
</head>

<body><!-- single-mode (light) UI; no dark/light switching -->

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

      <!-- START enhanced product card (drop-in; PHP logic unchanged) -->
      <div class="col-md-3 col-sm-6">
        <article class="product-card" aria-labelledby="prod-title-<?php echo (int)$p['id']; ?>">

          <div class="product-media" role="img" aria-label="<?php echo esc($p['name']); ?>">
            <?php if ($thumb): ?>
              <img 
                src="<?php echo $base_url; ?>/uploads/<?php echo esc($thumb); ?>" 
                alt="<?php echo esc($p['name']); ?>"
                loading="lazy"
                data-full="<?php echo $base_url; ?>/uploads/<?php echo esc($thumb); ?>"
                class="product-thumb"
              >
            <?php else: ?>
              <div class="no-image">No Image</div>
            <?php endif; ?>

            <div class="card-badges">
              <span class="badge-prime" aria-hidden="true">Prime</span>
              <?php if (!empty($p['stock']) && $p['stock'] <= 5): ?>
                <span class="badge-stock">Low stock</span>
              <?php endif; ?>
            </div>

          </div>

          <div class="card-body">
            <h3 id="prod-title-<?php echo (int)$p['id']; ?>" class="card-title"><?php echo esc($p['name']); ?></h3>

            <div class="card-meta">
              <div class="rating" aria-hidden="true">
                <span class="star">★</span><span class="star">★</span><span class="star">★</span><span class="star">★</span><span class="star" style="opacity:0.35">★</span>
                <span class="small-muted">&nbsp;4.2</span>
              </div>
              <div class="small-muted"> • <?php echo esc($p['category']); ?></div>
            </div>

            <div class="card-price">₹<?php echo number_format($p['price'], 2); ?></div>
            <div class="small-muted">Inclusive of taxes</div>

            <div class="card-actions">
              <a href="<?php echo $base_url; ?>/singleproduct.php?id=<?php echo (int)$p['id']; ?>" class="btn-view" aria-label="View <?php echo esc($p['name']); ?>">View Product</a>

              <button class="btn-wish" type="button" data-pid="<?php echo (int)$p['id']; ?>" title="Add to wishlist" aria-label="Add to wishlist">
                <i class="bi bi-heart"></i>
              </button>
            </div>
          </div>
        </article>
      </div>
      <!-- END enhanced product card -->

    <?php endforeach; ?>

  </div>
</div>

<!-- Quick view modal (UI only) -->
<div id="pvQuickView" aria-hidden="true">
  <div class="pv-card" role="dialog" aria-modal="true" aria-label="Product quick view">
    <button class="pv-close" aria-label="Close">✕</button>
    <div class="pv-left"><img src="" alt=""></div>
    <div class="pv-right">
      <h2 class="pv-title"></h2>
      <p class="pv-price"></p>
      <p class="pv-cat small-muted"></p>
      <div class="pv-actions" style="margin-top:12px;">
        <a href="#" class="btn-view pv-btn" style="display:inline-block">View product page</a>
      </div>
    </div>
  </div>
</div>

<!-- Inline JS: quick-view + wishlist UI (no theme code) -->
<script>
(function(){
  // Quick view modal helpers
  var pv = document.getElementById('pvQuickView');

  function openQuickView(imgSrc, title, price, cat, productUrl){
    if(!pv) return;
    pv.style.display = 'flex';
    pv.setAttribute('aria-hidden','false');
    pv.querySelector('img').src = imgSrc || '';
    pv.querySelector('img').alt = title || 'Product';
    pv.querySelector('.pv-title').textContent = title || '';
    pv.querySelector('.pv-price').textContent = price ? '₹' + price : '';
    pv.querySelector('.pv-cat').textContent = cat || '';
    var viewLink = pv.querySelector('.pv-btn');
    if(viewLink){
      viewLink.href = productUrl || '#';
    }
    var closeBtn = pv.querySelector('.pv-close');
    if(closeBtn) closeBtn.focus();
  }
  function closePV(){ if(pv){ pv.style.display = 'none'; pv.setAttribute('aria-hidden','true'); } }

  // click image to open quick-view
  document.addEventListener('click', function(e){
    var img = e.target.closest('.product-thumb');
    if(img){
      var src = img.getAttribute('data-full') || img.src;
      var card = img.closest('.product-card');
      var title = card ? (card.querySelector('.card-title')?.textContent || '') : '';
      var price = card ? (card.querySelector('.card-price')?.textContent || '') : '';
      var cat = card ? (card.querySelector('.card-meta .small-muted')?.textContent || '') : '';
      var productId = card ? card.querySelector('[data-pid]')?.getAttribute('data-pid') : null;
      var productUrl = null;
      if(productId) productUrl = '<?php echo $base_url; ?>/singleproduct.php?id=' + encodeURIComponent(productId);
      openQuickView(src, title, price.replace(/[^\d.,]/g,''), cat, productUrl);
    }
  });

  // wishlist UI toggle (client-side only)
  document.addEventListener('click', function(e){
    var btn = e.target.closest('.btn-wish');
    if(!btn) return;
    e.preventDefault();
    btn.classList.toggle('active');
    if(btn.classList.contains('active')){
      btn.innerHTML = '<i class="bi bi-heart-fill"></i>';
      btn.setAttribute('aria-pressed','true');
    } else {
      btn.innerHTML = '<i class="bi bi-heart"></i>';
      btn.setAttribute('aria-pressed','false');
    }
  });

  // pv close handlers
  if(pv){
    pv.addEventListener('click', function(ev){ if(ev.target === pv) closePV(); });
    var cb = pv.querySelector('.pv-close');
    if(cb) cb.addEventListener('click', closePV);
  }

  // ESC to close modal
  document.addEventListener('keydown', function(e){
    if(e.key === 'Escape') closePV();
  });
})();
</script>

<?php include __DIR__ . '/templates/footer.php'; ?>

</body>
</html>
