<?php
require_once __DIR__ . '/../init.php';
if(!is_admin_logged_in()) { header('Location: login.php'); exit; }

// Basic metrics
$totalOrders = $pdo->query('SELECT COUNT(*) FROM orders')->fetchColumn();
$pending = $pdo->query("SELECT COUNT(*) FROM orders WHERE status='Pending'")->fetchColumn();
$delivered = $pdo->query("SELECT COUNT(*) FROM orders WHERE status='Delivered'")->fetchColumn();
$revenue = $pdo->query('SELECT COALESCE(SUM(total),0) FROM orders')->fetchColumn();

// Recent orders
$orders = $pdo->query('SELECT id,user_id,total,status,tracking_code,created_at FROM orders ORDER BY created_at DESC LIMIT 20')->fetchAll();

// Recent contact messages
$pdo->exec("CREATE TABLE IF NOT EXISTS messages (id INT AUTO_INCREMENT PRIMARY KEY, name VARCHAR(200), email VARCHAR(255), message TEXT, created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP) ENGINE=InnoDB;");
$messages = $pdo->query('SELECT * FROM messages ORDER BY created_at DESC LIMIT 50')->fetchAll();
?>
<!doctype html>
<html><head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="../assets/css/custom.css">
<title>Admin Analytics</title></head>
<body>
<nav class="navbar navbar-dark bg-dark mb-3"><div class="container"><a class="navbar-brand" href="index.php">Admin</a><a class="btn btn-light btn-sm" href="index.php">Dashboard</a></div></nav>
<div class="container">
  <h3>Analytics Dashboard</h3>
  <div class="row g-3 mb-4">
    <div class="col-md-3"><div class="card p-3"><h6>Total Orders</h6><h4><?php echo $totalOrders; ?></h4></div></div>
    <div class="col-md-3"><div class="card p-3"><h6>Pending</h6><h4><?php echo $pending; ?></h4></div></div>
    <div class="col-md-3"><div class="card p-3"><h6>Delivered</h6><h4><?php echo $delivered; ?></h4></div></div>
    <div class="col-md-3"><div class="card p-3"><h6>Revenue</h6><h4>₹<?php echo number_format($revenue,2); ?></h4></div></div>
  </div>

  <h5>Recent Orders</h5>
  <table class="table"><thead><tr><th>ID</th><th>User</th><th>Total</th><th>Status</th><th>Tracking</th><th>Created</th></tr></thead>
  <tbody>
    <?php foreach($orders as $o): ?>
      <tr>
        <td><?php echo $o['id']; ?></td>
        <td><?php echo $o['user_id']; ?></td>
        <td>₹<?php echo number_format($o['total'],2); ?></td>
        <td><?php echo esc($o['status']); ?></td>
        <td><?php echo esc($o['tracking_code']); ?></td>
        <td><?php echo $o['created_at']; ?></td>
      </tr>
    <?php endforeach; ?>
  </tbody></table>

  <h5>Contact Messages</h5>
  <?php if(!$messages): ?><div class="alert alert-info">No messages</div><?php else: ?>
    <ul class="list-group mb-5">
      <?php foreach($messages as $m): ?>
        <li class="list-group-item">
          <strong><?php echo esc($m['name']); ?></strong> &lt;<?php echo esc($m['email']); ?>&gt; <span class="text-muted small">(<?php echo $m['created_at']; ?>)</span>
          <div class="mt-2 small"><?php echo nl2br(esc($m['message'])); ?></div>
        </li>
      <?php endforeach; ?>
    </ul>
  <?php endif; ?>

</div>
</body></html>
