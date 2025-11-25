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

// Contact messages
$pdo->exec("CREATE TABLE IF NOT EXISTS messages (id INT AUTO_INCREMENT PRIMARY KEY, name VARCHAR(200), email VARCHAR(255), message TEXT, created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP) ENGINE=InnoDB;");
$messages = $pdo->query('SELECT * FROM messages ORDER BY created_at DESC LIMIT 50')->fetchAll();
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

<title>Admin Analytics</title>

<style>
    body {
        background: #f5f7fa;
    }
    .navbar {
        background: #1f2937 !important;
        padding: 12px;
    }
    .navbar-brand img {
        height: 38px;
        margin-right: 8px;
    }
    h3, h5 {
        font-weight: 600;
    }
    .card {
        border-radius: 12px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        transition: 0.2s;
    }
    .card:hover {
        transform: translateY(-3px);
        box-shadow: 0 4px 14px rgba(0,0,0,0.12);
    }
    table {
        background: white;
        border-radius: 10px;
        overflow: hidden;
        box-shadow: 0 2px 8px rgba(0,0,0,0.08);
    }
    .list-group-item {
        border-radius: 10px !important;
        margin-bottom: 7px;
        box-shadow: 0 1px 4px rgba(0,0,0,0.05);
    }
</style>

</head>
<body>

<nav class="navbar navbar-dark">
  <div class="container d-flex align-items-center">
    <a class="navbar-brand d-flex align-items-center" href="index.php">
        <img src="<?php echo $base_url; ?>/assets/images/logo.png" alt="Logo">
        <span class="text-white">Admin Dashboard</span>
    </a>
    <a class="btn btn-outline-light btn-sm" href="index.php">Dashboard</a>
  </div>
</nav>

<div class="container py-4">

  <h3 class="mb-4">Analytics Overview</h3>

  <div class="row g-3 mb-4">

    <div class="col-md-3">
      <div class="card p-3">
        <h6 class="text-muted">Total Orders</h6>
        <h3><?php echo $totalOrders; ?></h3>
      </div>
    </div>

    <div class="col-md-3">
      <div class="card p-3">
        <h6 class="text-muted">Pending</h6>
        <h3><?php echo $pending; ?></h3>
      </div>
    </div>

    <div class="col-md-3">
      <div class="card p-3">
        <h6 class="text-muted">Delivered</h6>
        <h3><?php echo $delivered; ?></h3>
      </div>
    </div>

    <div class="col-md-3">
      <div class="card p-3">
        <h6 class="text-muted">Revenue</h6>
        <h3>₹<?php echo number_format((float)($revenue ?? 0),2); ?></h3>
      </div>
    </div>

  </div>


  <h5 class="mt-4 mb-3">Recent Orders</h5>
  
  <table class="table table-striped">
    <thead class="table-dark">
      <tr>
        <th>ID</th>
        <th>User</th>
        <th>Total</th>
        <th>Status</th>
        <th>Tracking</th>
        <th>Created</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach($orders as $o): ?>
      <tr>
        <td><?php echo $o['id']; ?></td>
        <td><?php echo $o['user_id']; ?></td>
        <td>₹<?php echo number_format((float)($o['total'] ?? 0),2); ?></td>
        <td><?php echo esc($o['status']); ?></td>
        <td><?php echo esc($o['tracking_code']); ?></td>
        <td><?php echo $o['created_at']; ?></td>
      </tr>
      <?php endforeach; ?>
    </tbody>
  </table>


  <h5 class="mt-5 mb-3">Customer Messages</h5>

  <?php if(!$messages): ?>
    <div class="alert alert-info">No messages found</div>
  <?php else: ?>
    <ul class="list-group mb-5">
      <?php foreach($messages as $m): ?>
      <li class="list-group-item">
        <strong><?php echo esc($m['name']); ?></strong> 
        &lt;<?php echo esc($m['email']); ?>&gt;
        <span class="text-muted small">(<?php echo $m['created_at']; ?>)</span>
        <div class="mt-2 small"><?php echo nl2br(esc($m['message'])); ?></div>
      </li>
      <?php endforeach; ?>
    </ul>
  <?php endif; ?>

</div>
</body>
</html>
