<?php
require_once __DIR__ . '/../init.php';
if(!is_admin_logged_in()) { header('Location: login.php'); exit; }
$id = isset($_GET['id'])? (int)$_GET['id'] : 0;
$stmt = $pdo->prepare('SELECT o.*, u.name as customer,u.email FROM orders o JOIN users u ON o.user_id=u.id WHERE o.id = ?');
$stmt->execute([$id]);
$order = $stmt->fetch();
if(!$order) { echo 'Not found'; exit; }
$stmt = $pdo->prepare('SELECT oi.*, p.name FROM order_items oi JOIN products p ON oi.product_id = p.id WHERE oi.order_id = ?');
$stmt->execute([$id]);
$items = $stmt->fetchAll();
?>
<!doctype html>
<html><head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<title>Order #<?php echo $order['id']; ?></title></head>
<body>
<div class="container py-4">
  <h3>Order #<?php echo $order['id']; ?></h3>
  <p>Customer: <?php echo esc($order['customer']); ?> (<?php echo esc($order['email']); ?> )</p>
    <p>Delivery Address: <?php echo esc($order['Delivery Address']); ?> (<?php echo esc($order['Delivery Address']); ?> )</p>
  <p>Tracking: <?php echo esc($order['tracking_code']); ?> - Status: <?php echo esc($order['status']); ?></p>
  <h5>Items</h5>
  <ul>
    <?php foreach($items as $it): ?>
      <li><?php echo esc($it['name']); ?> x <?php echo (int)$it['qty']; ?> ($<?php echo number_format($it['price'],2); ?>)</li>
    <?php endforeach; ?>
  </ul>
  <a class="btn btn-secondary" href="orders.php">Back</a>
</div>
</body></html>