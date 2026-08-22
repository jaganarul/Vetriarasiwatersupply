<?php
require_once __DIR__ . '/../init.php';
if(!is_admin_logged_in()) { header('Location: ' . $base_url . '/login.php'); exit; }

$id = isset($_GET['id'])? (int)$_GET['id'] : 0;

// fetch order with customer info
$stmt = $pdo->prepare('SELECT o.*, u.name as customer, u.email, u.phone, u.address FROM orders o JOIN users u ON o.user_id=u.id WHERE o.id = ?');
$stmt->execute([$id]);
$order = $stmt->fetch();
if(!$order) { echo 'Not found'; exit; }

// Mark the order as read/seen for admin notifications (if is_new column exists)
try{
  $col = $pdo->query("SHOW COLUMNS FROM orders LIKE 'is_new'")->fetch();
  if($col && !empty($order['is_new'])){
    $pdo->prepare('UPDATE orders SET is_new = 0 WHERE id = ?')->execute([$id]);
  }
} catch(Exception $e) { /* no-op on error */ }

// fetch order items
$stmt = $pdo->prepare('SELECT oi.*, p.name FROM order_items oi JOIN products p ON oi.product_id = p.id WHERE oi.order_id = ?');
$stmt->execute([$id]);
$items = $stmt->fetchAll();

// fetch payment info (last payment, if any) - safe check if payments table exists
$payment = null;
try{
  $hasPayments = (bool)$pdo->query("SHOW TABLES LIKE 'payments'")->fetch();
  if($hasPayments){
    $stmt = $pdo->prepare('SELECT method, status, created_at FROM payments WHERE order_id = ? ORDER BY created_at DESC LIMIT 1');
    $stmt->execute([$id]);
    $payment = $stmt->fetch();
  }
} catch(Exception $e){
  $payment = null; // ignore and continue
}

// safely get delivery address and phone (prefer order-stored values)
$delivery_address = $order['delivery_address'] ?? $order['address'] ?? 'Not provided';
$delivery_phone = $order['delivery_phone'] ?? $order['phone'] ?? 'Not provided';
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
<title>Order #<?php echo $order['id']; ?></title>
<style>
  body {
    background: #f8fafc;
  }
  .card-order {
    border-radius: 12px;
    box-shadow: 0 8px 20px rgba(0,0,0,0.05);
  }
  .badge-status {
    font-size: 0.9rem;
    padding: 0.45em 0.8em;
  }
  .order-header {
    border-bottom: 1px solid #e5e7eb;
    padding-bottom: 10px;
    margin-bottom: 15px;
  }
  .list-group-item {
    border: none;
    padding-left: 0;
    padding-right: 0;
  }
  .back-btn {
    margin-top: 20px;
  }
</style>
</head>
<body>
<div class="container py-5">
  <div class="card card-order p-4">
    <div class="order-header d-flex justify-content-between align-items-center">
      <h3>Order #<?php echo $order['id']; ?></h3>
      <span class="badge bg-<?php 
          switch(strtolower($order['status'])){
              case 'delivered': echo 'success'; break;
              case 'shipped': echo 'info'; break;
              case 'processing': echo 'warning'; break;
              case 'cancelled': echo 'danger'; break;
              default: echo 'secondary';
          } 
      ?> badge-status">
        <?php echo esc($order['status']); ?>
      </span>
    </div>

    <div class="row mb-3">
      <div class="col-md-6">
        <h5>Customer Info</h5>
        <p class="mb-1"><i class="bi bi-person-fill"></i> <?php echo esc($order['customer']); ?></p>
        <p class="mb-1"><i class="bi bi-envelope-fill"></i> <?php echo esc($order['email']); ?></p>
      </div>
      <div class="col-md-6">
        <h5>Delivery</h5>
        <p class="mb-1"><i class="bi bi-telephone-fill"></i> <?php echo esc($delivery_phone); ?></p>
        <p class="mb-1"><i class="bi bi-geo-alt-fill"></i> <?php echo esc($delivery_address); ?></p>
        <p class="mb-0"><i class="bi bi-truck"></i> Tracking: <?php echo esc($order['tracking_code']); ?></p>
        <?php if($payment): ?>
        <p class="mb-0 mt-2"><strong>Payment:</strong>
          <?php if(strtoupper($payment['method']) === 'UPI'): ?>
            <span class="badge bg-success">UPI</span>
          <?php elseif(strtoupper($payment['method']) === 'COD'): ?>
            <span class="badge bg-info text-dark">COD</span>
          <?php else: ?>
            <span class="badge bg-secondary"><?php echo esc($payment['method']); ?></span>
          <?php endif; ?>
          <small class="text-muted ms-2"><?php echo esc($payment['status']); ?><?php echo $payment['status'] ? ' • ' . esc($payment['created_at']) : ''; ?></small>
        </p>
        <?php else: ?>
        <p class="mb-0 mt-2"><strong>Payment:</strong> <span class="text-muted">Not recorded</span></p>
        <?php endif; ?>
      </div>
    </div>

    <h5 class="mt-4">Items</h5>
    <ul class="list-group list-group-flush mb-3">
      <?php foreach($items as $it): ?>
        <li class="list-group-item d-flex justify-content-between align-items-center">
          <span><?php echo esc($it['name']); ?> x <?php echo (int)$it['qty']; ?></span>
          <span>₹<?php echo number_format((float)($it['price'] ?? 0),2); ?></span>
        </li>
      <?php endforeach; ?>
    </ul>

    <a class="btn btn-secondary back-btn" href="orders.php"><i class="bi bi-arrow-left"></i> Back to Orders</a>
  </div>
</div>

</body>
</html>
