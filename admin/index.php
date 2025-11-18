<?php
require_once __DIR__ . '/../init.php';
if (!is_admin_logged_in()) { header('Location: login.php'); exit; }

// ------- FILTER HANDLING -------
$filter = $_GET['filter'] ?? 'daily';

switch ($filter) {

    case 'yearly':
        $sql = "SELECT YEAR(created_at) AS d, SUM(total) AS s 
                FROM orders 
                GROUP BY YEAR(created_at) 
                ORDER BY d DESC LIMIT 5";
        break;

    case 'monthly':
        $sql = "SELECT DATE_FORMAT(created_at,'%Y-%m') AS d, SUM(total) AS s 
                FROM orders 
                GROUP BY YEAR(created_at), MONTH(created_at) 
                ORDER BY d DESC LIMIT 12";
        break;

    case 'weekly':
        $sql = "SELECT DATE_FORMAT(created_at,'%x-W%v') AS d, SUM(total) AS s 
                FROM orders 
                GROUP BY YEARWEEK(created_at) 
                ORDER BY d DESC LIMIT 12";
        break;

    default:
    case 'daily':
        $sql = "SELECT DATE(created_at) AS d, SUM(total) AS s 
                FROM orders 
                GROUP BY DATE(created_at) 
                ORDER BY d DESC LIMIT 30";
        break;
}

$stmt = $pdo->query($sql);
$rows = array_reverse($stmt->fetchAll());
$labels = array_column($rows, 'd');
$values = array_column($rows, 's');

// Top Selling Products
$stmt = $pdo->query("SELECT p.name, SUM(oi.qty) AS sold 
                     FROM order_items oi 
                     JOIN products p ON oi.product_id = p.id 
                     GROUP BY p.id 
                     ORDER BY sold DESC LIMIT 5");
$top = $stmt->fetchAll();
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <title>Admin Dashboard</title>

<style>
body {
    background: #f4f6f9;
}
.sidebar {
    width: 230px;
    height: 100vh;
    background: #222;
    color: white;
    position: fixed;
    padding-top: 20px;
}
.sidebar a {
    color: #ddd;
    display: block;
    padding: 12px 20px;
    text-decoration: none;
    font-size: 15px;
}
.sidebar a:hover {
    background: #444;
}
.main {
    margin-left: 240px;
}
.logo-img {
    width: 150px;
    margin-bottom: 20px;
}
.filter-btn {
    margin-right: 8px;
}
.card {
    border-radius: 12px;
}
</style>

</head>
<body>

<!-- SIDEBAR -->
<div class="sidebar text-center">
  <img src="../assets/images/logo.png" class="logo-img">
  <h5 class="text-white mb-4">Admin Panel</h5>
  <a href="index.php">Dashboard</a>
  <a href="products.php">Products</a>
  <a href="orders.php">Orders</a>
  <a href="users.php">Customers</a>
  <a href="messages.php">Messages</a>
  <a href="logout.php">Logout</a>
</div>

<!-- MAIN CONTENT -->
<div class="main">
<nav class="navbar navbar-light bg-white shadow-sm px-4">
  <span class="navbar-brand mb-0 h4">Welcome, <?php echo esc($_SESSION['admin_name']); ?></span>
</nav>

<div class="container-fluid py-4">

  <!-- FILTER BUTTONS -->
  <div class="mb-3">
    <a href="?filter=daily" class="btn btn-outline-primary filter-btn">Daily</a>
    <a href="?filter=weekly" class="btn btn-outline-primary filter-btn">Weekly</a>
    <a href="?filter=monthly" class="btn btn-outline-primary filter-btn">Monthly</a>
    <a href="?filter=yearly" class="btn btn-outline-primary filter-btn">Yearly</a>
  </div>

  <div class="row">
    <!-- SALES CHART -->
    <div class="col-md-8">
      <div class="card p-3 mb-4 shadow-sm">
        <h5 class="mb-3">Sales Analytics (<?php echo ucfirst($filter); ?>)</h5>
        <canvas id="salesChart"></canvas>
      </div>
    </div>

    <!-- TOP PRODUCTS -->
    <div class="col-md-4">
      <div class="card p-3 mb-4 shadow-sm">
        <h5 class="mb-3">Top Products</h5>
        <ul class="list-group">
        <?php foreach ($top as $t): ?>
          <li class="list-group-item d-flex justify-content-between">
            <?php echo esc($t['name']); ?>
            <span class="badge bg-primary"><?php echo $t['sold']; ?></span>
          </li>
        <?php endforeach; ?>
        </ul>
      </div>

      <a class="btn btn-primary w-100 mb-2" href="products.php">Manage Products</a>
      <a class="btn btn-secondary w-100" href="orders.php">Manage Orders</a>
    </div>
  </div>
</div>

<script>
const labels = <?php echo json_encode($labels); ?>;
const data = <?php echo json_encode($values); ?>;

new Chart(document.getElementById('salesChart'), {
  type: 'line',
  data: {
    labels: labels,
    datasets: [{
      label: 'Sales',
      data: data,
      borderColor: '#007bff',
      tension: 0.3,
      borderWidth: 2,
      pointRadius: 3
    }]
  }
});
</script>

</body>
</html>
