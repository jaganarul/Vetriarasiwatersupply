<?php
require_once 'init.php';
$id = isset($_GET['order'])? (int)$_GET['order'] : 0;
$stmt = $pdo->prepare('SELECT * FROM orders WHERE id = ? AND user_id = ?');
$stmt->execute([$id, $_SESSION['user_id'] ?? 0]);
$order = $stmt->fetch();
if(!$order) { echo 'Order not found'; exit; }
?>
<!doctype html>
<html><head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<title>Order Placed</title></head>
<body>
<div class="container py-4">
  <h3>Order Placed</h3>
  <p>Thank you! Your order #<?php echo $order['id']; ?> has been created.</p>
  <p>Tracking Code: <strong><?php echo esc($order['tracking_code']); ?></strong></p>
  <a href="profile.php" class="btn btn-primary">Go to Profile / Orders</a>
</div>
</body></html>