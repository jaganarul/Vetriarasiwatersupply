<?php
require_once 'init.php';
if(!is_logged_in()){ $_SESSION['return_to'] = 'payments.php'; header('Location: ' . $base_url . '/login'); exit; }
$cart = $_SESSION['checkout_cart'] ?? null;
$total = $_SESSION['checkout_total'] ?? 0;
if(!$cart){ header('Location: ' . $base_url . '/cart.php'); exit; }

// Simple payments flow: choose UPI or COD. For UPI we show instructions then confirm, for COD we create order immediately.
if($_SERVER['REQUEST_METHOD'] === 'POST'){
    $action = $_POST['action'] ?? '';
    $method = $_POST['method'] ?? '';
    // If user confirms payment (UPI) or chooses COD, create the order.
    if($action === 'confirm_payment' || $method === 'COD'){
        // create payments table if not exists (safe to run)
        $pdo->exec("CREATE TABLE IF NOT EXISTS payments (id INT AUTO_INCREMENT PRIMARY KEY, order_id INT NOT NULL, method VARCHAR(50) NOT NULL, status VARCHAR(50), created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP, FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE) ENGINE=InnoDB;");

        // Begin transaction: verify stock, create order, items and payment record
        $ids = array_keys($cart);
        $placeholders = implode(',', array_fill(0, count($ids), '?'));
        $stmt = $pdo->prepare("SELECT id,price,stock FROM products WHERE id IN ($placeholders) FOR UPDATE");
        $pdo->beginTransaction();
        try {
            $stmt->execute($ids);
            $rows = $stmt->fetchAll();
            $calcTotal = 0;
            foreach($rows as $r){
                $qty = $cart[$r['id']];
                if($qty > $r['stock']) throw new Exception('Not enough stock for product id '.$r['id']);
                $calcTotal += $qty * $r['price'];
            }
            $tracking = strtoupper(uniqid('TRK'));
            // fetch user's current phone/address to store on the order (so admin view remains accurate)
            $uStmt = $pdo->prepare('SELECT phone,address FROM users WHERE id = ? LIMIT 1');
            $uStmt->execute([$_SESSION['user_id']]);
            $u = $uStmt->fetch();
            $delivery_phone = $u['phone'] ?? null;
            $delivery_address = $u['address'] ?? null;
            // Ensure the `is_new` column exists on orders so admin can be notified of new orders
            // Ensure the `is_new` column exists on orders so admin can be notified of new orders
            $hasOrdersTbl = $pdo->query("SHOW TABLES LIKE 'orders'")->fetch();
            if($hasOrdersTbl){
              $col = $pdo->query("SHOW COLUMNS FROM orders LIKE 'is_new'")->fetch();
              if(!$col){
                $pdo->exec("ALTER TABLE orders ADD COLUMN is_new TINYINT(1) NOT NULL DEFAULT 1");
              }
            }

            $stmtIns = $pdo->prepare('INSERT INTO orders (user_id,total,delivery_phone,delivery_address,tracking_code,status,is_new) VALUES (?,?,?,?,?,?,?)');
            $stmtIns->execute([$_SESSION['user_id'], $calcTotal, $delivery_phone, $delivery_address, $tracking, 'Pending', 1]);
            $order_id = $pdo->lastInsertId();
            $stmtItem = $pdo->prepare('INSERT INTO order_items (order_id,product_id,qty,price) VALUES (?,?,?,?)');
            $stmtUpdate = $pdo->prepare('UPDATE products SET stock = stock - ? WHERE id = ?');
            foreach($rows as $r){
                $qty = $cart[$r['id']];
                $stmtItem->execute([$order_id, $r['id'], $qty, $r['price']]);
                $stmtUpdate->execute([$qty, $r['id']]);
            }
            // record payment info
            $payStatus = ($method === 'COD') ? 'Pending' : 'Paid';
            $stmtPay = $pdo->prepare('INSERT INTO payments (order_id, method, status) VALUES (?,?,?)');
            $stmtPay->execute([$order_id, $method ?: 'COD', $payStatus]);

            $pdo->commit();
            // clear checkout/cart
            unset($_SESSION['cart']);
            unset($_SESSION['checkout_cart']);
            unset($_SESSION['checkout_total']);
            header('Location: ' . $base_url . '/order_success.php?order=' . $order_id);
            exit;
        } catch(Exception $e){
            $pdo->rollBack();
            $error = 'Payment processing failed: '.$e->getMessage();
        }
    }
    // user selected UPI and we show instructions
    if($action === 'choose' && $method === 'UPI'){
        $show_upi = true;
    }
}
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="<?php echo $base_url; ?>/assets/css/custom.css">
  <title>Payments - Vetriarasiwatersupply</title>
  <style>
    /* Small, self-contained payment UI tweaks (won't override your custom.css unless identical selectors exist) */
    .upi-card { border-radius:12px; box-shadow: 0 8px 24px rgba(2,6,23,0.35); }
    .upi-btn { min-width:160px; }
    .qr-img { width:220px; height:220px; object-fit:contain; }
    .muted-small { color:#69748a; font-size:0.95rem; }
    .app-open-note { font-size:0.9rem; color:#3b3b3b; }
  </style>
</head>
<body>
<?php include __DIR__ . '/templates/header.php'; ?>
<div class="container py-4">
  <h3>Payment</h3>
  <?php if(!empty($error)): ?><div class="alert alert-danger"><?php echo esc($error); ?></div><?php endif; ?>
  <p>Total payable: <strong>₹<?php echo number_format((float)($total ?? 0),2); ?></strong></p>

  <?php
    // UPI configuration (change the UPI ID or business name here if needed)
    $upi_id = 'jaganarul179@okhdfcbank';
    $upi_name = 'Vetriarasi Water Supply';
    // format amount as plain number with two decimals (some apps prefer dot decimal)
    $amount_str = number_format((float)$total, 2, '.', '');
    // Build upi uri with amount pre-filled
    $upi_uri = 'upi://pay?pa=' . rawurlencode($upi_id) . '&pn=' . rawurlencode($upi_name) . '&am=' . rawurlencode($amount_str) . '&cu=INR';

    // === REPLACED: use a local static QR image instead of Google Chart API ===
    // Place your pre-generated QR image at: /assets/img/upi_qr.png
    // (You generated this once with the UPI URI and saved it to the assets folder.)
    $qr_src = $base_url . '/assets/images/upi_qr.png';
  ?>

  <?php if(!empty($show_upi)): ?>
    <div class="card p-3 mb-3 upi-card">
      <div class="d-flex align-items-center justify-content-between mb-2">
        <h5 class="mb-0">UPI Payment</h5>
        <span class="badge bg-success">Secure</span>
      </div>

      <div class="row g-3">
        <div class="col-md-5 text-center">
          <img src="<?php echo esc($qr_src); ?>" alt="UPI QR" class="qr-img img-fluid rounded mb-2">
          <div class="small muted-small">Scan QR with your UPI app</div>
        </div>

        <div class="col-md-7">
          <p class="muted-small mb-1">Pay using the UPI ID (tap to open your UPI app or copy):</p>

          <div class="mb-3">
            <!-- Clickable UPI link that auto-fills amount -->
            <a id="upiLink" href="<?php echo esc($upi_uri); ?>" class="fw-bold text-decoration-none" onclick="return tryOpenUPI(event)">
              <span class="h6 mb-0"><?php echo htmlspecialchars($upi_id); ?></span>
            </a>
            <div class="small text-muted">Amount: <strong>₹<?php echo htmlspecialchars($amount_str); ?></strong></div>
          </div>

          <div class="d-flex gap-2 flex-wrap">
            <button type="button" class="btn btn-success upi-btn" onclick="tryOpenUPI()">Open UPI App</button>
            <button type="button" class="btn btn-outline-primary upi-btn" data-bs-toggle="modal" data-bs-target="#upiModal">Show QR / Pay</button>
            <button type="button" class="btn btn-outline-secondary upi-btn" onclick="copyUPI()">Copy UPI ID</button>
          </div>

          <p class="mt-3 small">After completing the payment in your UPI app, click the button below to confirm.</p>

          <form method="post" class="mt-2">
            <input type="hidden" name="action" value="confirm_payment">
            <input type="hidden" name="method" value="UPI">
            <button class="btn btn-success">I have paid (Confirm)</button>
            <a href="<?php echo $base_url; ?>/payments" class="btn btn-secondary ms-2">Back</a>
          </form>

          <div class="mt-3 app-open-note">
            <small>Tip: If the app doesn't open automatically, try long-pressing the UPI ID to copy and paste it into your UPI app's "Pay to UPI ID" field, or scan the QR above.</small>
          </div>
        </div>
      </div>
    </div>

    <!-- Modal: Larger QR & direct UPI URI -->
    <div class="modal fade" id="upiModal" tabindex="-1" aria-labelledby="upiModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="upiModalLabel">Pay with UPI</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body text-center">
            <img src="<?php echo esc($qr_src); ?>" alt="UPI QR" class="img-fluid mb-3" style="max-width:260px;">
            <p class="mb-1">UPI ID: <strong><?php echo htmlspecialchars($upi_id); ?></strong></p>
            <p class="small text-muted mb-3">Amount: <strong>₹<?php echo htmlspecialchars($amount_str); ?></strong></p>

            <div class="d-grid gap-2">
              <a id="modalOpenBtn" href="<?php echo esc($upi_uri); ?>" class="btn btn-success" onclick="return tryOpenUPI(event)">Open UPI App</a>
              <button type="button" class="btn btn-outline-secondary" onclick="copyUPI()">Copy UPI ID</button>
            </div>

            <div class="mt-3 small text-muted">If your device doesn't support direct UPI links, scan the QR with your UPI app instead.</div>
          </div>
        </div>
      </div>
    </div>

  <?php else: ?>
    <div class="row">
      <div class="col-md-6">
        <div class="card p-3 mb-3 upi-card">
          <h5>Pay with UPI</h5>
          <p class="small">Fast and contactless. No extra fees.</p>
          <form method="post">
            <input type="hidden" name="action" value="choose">
            <input type="hidden" name="method" value="UPI">
            <button class="btn btn-primary">Choose UPI</button>
          </form>
        </div>
      </div>
      <div class="col-md-6">
        <div class="card p-3 mb-3">
          <h5>Cash on Delivery (COD)</h5>
          <p class="small">Pay when your order is delivered.</p>
          <form method="post">
            <input type="hidden" name="action" value="confirm_payment">
            <input type="hidden" name="method" value="COD">
            <button class="btn btn-outline-dark">Place Order - COD</button>
          </form>
        </div>
      </div>
    </div>
  <?php endif; ?>

</div>

<?php include __DIR__ . '/templates/footer.php'; ?>

<!-- Bootstrap JS (required for modal) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
  // Put the same UPI URI in JS (safe because it's built server-side above)
  const upiUri = <?php echo json_encode($upi_uri, JSON_HEX_TAG|JSON_HEX_QUOT|JSON_HEX_AMP); ?>;
  const upiId = <?php echo json_encode($upi_id, JSON_HEX_TAG|JSON_HEX_QUOT|JSON_HEX_AMP); ?>;
  const upiAmount = <?php echo json_encode($amount_str, JSON_HEX_TAG|JSON_HEX_QUOT|JSON_HEX_AMP); ?>;

  // Try to open UPI app using the upi:// URI. On desktop or unsupported browsers this will fail silently.
  function tryOpenUPI(e){
    if (e && e.preventDefault) e.preventDefault();

    // Best-effort: navigation to the upi:// URI
    // Some browsers block immediate navigation from JS; we attempt direct location change.
    // We also fallback to showing the modal QR if the redirect doesn't occur (user can scan).
    let opened = false;
    try {
      // Attempt to open via location — mobile UPI apps typically hijack this.
      window.location.href = upiUri;
      opened = true;
    } catch (err) {
      // ignore
    }

    // Also attempt to open via iframe (older fallback).
    let iframe = document.createElement('iframe');
    iframe.style.display = 'none';
    iframe.src = upiUri;
    document.body.appendChild(iframe);
    setTimeout(()=> {
      document.body.removeChild(iframe);
    }, 1500);

    // If user is on desktop or nothing happens, show the modal with QR (if exists)
    setTimeout(()=> {
      // If modal exists, show it; otherwise do nothing.
      const modalEl = document.getElementById('upiModal');
      if (modalEl && !opened) {
        const bsModal = new bootstrap.Modal(modalEl);
        bsModal.show();
      }
    }, 800);

    return false;
  }

  // Copy UPI ID to clipboard
  function copyUPI(){
    if (!navigator.clipboard) {
      // fallback
      const textarea = document.createElement('textarea');
      textarea.value = upiId;
      document.body.appendChild(textarea);
      textarea.select();
      try { document.execCommand('copy'); showToast('Copied UPI ID'); } catch(e){ alert('Copy failed, please select and copy manually.'); }
      document.body.removeChild(textarea);
      return;
    }
    navigator.clipboard.writeText(upiId).then(()=> {
      showToast('UPI ID copied to clipboard');
    }).catch(()=> {
      alert('Unable to copy. Please long-press the UPI ID to copy it manually.');
    });
  }

  // Minimal toast (small ephemeral message)
  function showToast(msg){
    // create ephemeral element
    const t = document.createElement('div');
    t.className = 'position-fixed bottom-0 end-0 p-3';
    t.style.zIndex = 2147483647;
    t.innerHTML = '<div class="toast align-items-center text-bg-dark border-0 show" role="alert" aria-live="assertive" aria-atomic="true"><div class="d-flex"><div class="toast-body">'+msg+'</div><button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button></div></div>';
    document.body.appendChild(t);
    setTimeout(()=> {
      try { bootstrap.Toast.getOrCreateInstance(t.querySelector('.toast')).hide(); } catch(e) {}
      setTimeout(()=> document.body.removeChild(t), 400);
    }, 2200);
  }

</script>
</body>
</html>
