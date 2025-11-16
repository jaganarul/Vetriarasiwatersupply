<?php
require_once 'init.php';
if(!is_logged_in()){ $_SESSION['return_to'] = 'payments.php'; header('Location: login.php'); exit; }
$cart = $_SESSION['checkout_cart'] ?? null;
$total = $_SESSION['checkout_total'] ?? 0;
if(!$cart){ header('Location: cart.php'); exit; }

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
            $stmtIns = $pdo->prepare('INSERT INTO orders (user_id,total,tracking_code,status) VALUES (?,?,?,?)');
            $stmtIns->execute([$_SESSION['user_id'], $calcTotal, $tracking, 'Pending']);
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
            header('Location: order_success.php?order='.$order_id);
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
  <link rel="stylesheet" href="assets/css/custom.css">
  <title>Payments - Vetriarasi Water Supply</title>
</head>
<body>
<?php include __DIR__ . '/templates/header.php'; ?>
<div class="container py-4">
  <h3>Payment</h3>
  <?php if(!empty($error)): ?><div class="alert alert-danger"><?php echo esc($error); ?></div><?php endif; ?>
  <p>Total payable: <strong>₹<?php echo number_format($total,2); ?></strong></p>

  <?php if(!empty($show_upi)): ?>
    <div class="card p-3 mb-3">
      <h5>UPI Payment</h5>
      <p class="small">Scan the QR or pay using the UPI ID: <strong>vetriarasi@upi</strong></p>
      <p class="small">After completing the payment in your UPI app click the button below to confirm.</p>
      <form method="post">
        <input type="hidden" name="action" value="confirm_payment">
        <input type="hidden" name="method" value="UPI">
        <button class="btn btn-success">I have paid (Confirm)</button>
      </form>
    </div>
    <a href="payments.php" class="btn btn-secondary">Back</a>
  <?php else: ?>
    <div class="row">
      <div class="col-md-6">
        <div class="card p-3 mb-3">
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
</body>
</html>
