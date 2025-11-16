<?php
require_once __DIR__ . '/../init.php';
if(!is_admin_logged_in()) { header('Location: login.php'); exit; }
// simple analytics: daily sales last 7 days
$stmt = $pdo->query("SELECT DATE(created_at) as d, SUM(total) as s FROM orders GROUP BY DATE(created_at) ORDER BY d DESC LIMIT 30");
$rows = array_reverse($stmt->fetchAll());
$labels = array_column($rows,'d');
$values = array_column($rows,'s');
// top products
  $stmt = $pdo->query("SELECT p.id,p.name,SUM(oi.qty) as sold FROM order_items oi JOIN products p ON oi.product_id=p.id GROUP BY p.id ORDER BY sold DESC LIMIT 5");
  $top = $stmt->fetchAll();
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <title>Admin Dashboard</title>
</head>
<body>
<nav class="navbar navbar-dark bg-dark">
  <div class="container">
    <a class="navbar-brand" href="index.php">Admin</a>
    <div class="text-white">Welcome <?php echo esc($_SESSION['admin_name']); ?> <a class="btn btn-sm btn-light ms-2" href="logout.php">Logout</a></div>
  </div>
</nav>
<div class="container py-4">
  <div class="row">
    <div class="col-md-8">
      <div class="card mb-3">
        <div class="card-body">
          <h5>Sales (recent days)</h5>
          <canvas id="salesChart"></canvas>
        </div>
      </div>
    </div>
    <div class="col-md-4">
      <div class="card mb-3">
        <div class="card-body">
          <h5>Top Products</h5>
          <ul class="list-group">
            <?php foreach($top as $t): ?>
              <li class="list-group-item d-flex justify-content-between align-items-center"><?php echo esc($t['name']); ?> <span class="badge bg-primary"><?php echo (int)$t['sold']; ?></span></li>
            <?php endforeach; ?>
          </ul>
        </div>
      </div>
      <a class="btn btn-primary w-100 mb-2" href="products.php">Manage Products</a>
      <a class="btn btn-secondary w-100" href="orders.php">Manage Orders</a>
    </div>
  </div>
</div>
<script>
const labels = <?php echo json_encode($labels); ?>;
const data = <?php echo json_encode($values); ?>;
const ctx = document.getElementById('salesChart');
new Chart(ctx, { type: 'line', data: { labels, datasets: [{ label: 'Sales', data, borderColor: 'rgb(75, 192, 192)', tension:0.2 }]}});
</script>
</body>
</html>