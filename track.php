<?php
require_once 'init.php';
$code = trim($_GET['code'] ?? $_POST['code'] ?? '');
$order = null;
if($code){
    $stmt = $pdo->prepare('SELECT * FROM orders WHERE tracking_code = ?');
    $stmt->execute([$code]);
    $order = $stmt->fetch();
}
?>
<!doctype html>
<html><head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="assets/css/custom.css">
<title>Track Order</title></head>
<body>
<?php include __DIR__ . '/templates/header.php'; ?>
<h3>Track Order</h3>
<form method="get" class="mb-3">
  <div class="input-group">
    <input name="code" class="form-control" placeholder="Enter tracking code" value="<?php echo esc($code); ?>">
    <button class="btn btn-primary">Track</button>
  </div>
</form>
<?php if($code && !$order): ?><div class="alert alert-warning">No order found.</div><?php endif; ?>
<?php if($order): ?>
  <h5>Order #<?php echo $order['id']; ?></h5>
  <p>Tracking: <?php echo esc($order['tracking_code']); ?></p>
  <p>Status: <strong><?php echo esc($order['status']); ?></strong></p>
  <ul class="order-timeline">
    <?php
      $states = ['Pending','Processing','Shipped','Delivered','Cancelled'];
      foreach($states as $s){
          $cls = $s === $order['status'] ? 'text-success order-status' : 'text-muted';
          echo "<li class='$cls'>".esc($s)."</li>";
      }
    ?>
  </ul>
<?php endif; ?>
<?php include __DIR__ . '/templates/footer.php'; ?>
</body></html>