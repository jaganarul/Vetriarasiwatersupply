<?php
require_once 'init.php';
if(!is_logged_in()) { header('Location: ' . $base_url . '/login'); exit; }
$stmt = $pdo->prepare('SELECT * FROM orders WHERE user_id = ? ORDER BY created_at DESC');
$stmt->execute([$_SESSION['user_id']]);
$orders = $stmt->fetchAll();
?>
<!doctype html>
<html>
<head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="<?php echo $base_url; ?>/assets/css/custom.css">
<title>Profile</title></head>
<body>
<?php include __DIR__ . '/templates/header.php'; ?>
<div class="container py-4">
  <h3>Your Profile</h3>
  <p>Name: <?php echo esc($_SESSION['user_name']); ?></p>
  <h5>Your Orders</h5>
  <?php if(!$orders): ?><div class="alert alert-info">No orders yet.</div><?php else: ?>
    <div class="table-responsive">
    <table class="table">
      <thead><tr><th>Order</th><th>Total</th><th>Status</th><th>Tracking</th><th>Date</th></tr></thead>
      <tbody>
        <?php foreach($orders as $o): ?>
          <tr>
            <td><?php echo $o['id']; ?></td>
            <td>$<?php echo number_format((float)($o['total'] ?? 0),2); ?></td>
            <td><?php echo esc($o['status']); ?></td>
            <td><a href="<?php echo $base_url; ?>/track?code=<?php echo esc($o['tracking_code']); ?>"><?php echo esc($o['tracking_code']); ?></a></td>
            <td><?php echo $o['created_at']; ?></td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
    </div>
  <?php endif; ?>
</div>
<?php include __DIR__ . '/templates/footer.php'; ?>
</body></html>