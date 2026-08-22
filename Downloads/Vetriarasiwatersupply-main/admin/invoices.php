<?php
require_once __DIR__ . '/../init.php';
if(!is_admin_logged_in()) { header('Location: ' . $base_url . '/login.php'); exit; }

// Get all orders with invoice data
$sql = '
    SELECT o.id, o.total, u.name as customer_name, u.email,
      o.tracking_code, o.created_at, o.status,
      (SELECT COUNT(*) FROM order_items WHERE order_id = o.id) as item_count
    FROM orders o
    JOIN users u ON o.user_id = u.id
    ORDER BY o.created_at DESC
';
$stmt = $pdo->query($sql);
$orders = $stmt->fetchAll();
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
<title>Invoice Management</title>
<style>
:root {
  --bg: #f4f6f9;
  --accent: #0b74ff;
}

body {
    background: var(--bg);
    font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
}

/* Sidebar styles */
.sidebar {
    width: 230px;
    height: 100vh;
    background: #222;
    color: white;
    position: fixed;
    padding-top: 20px;
    overflow-y: auto;
    z-index: 1000;
}
.sidebar a {
    color: #ddd;
    display: block;
    padding: 12px 20px;
    text-decoration: none;
    font-size: 15px;
    transition: 0.2s;
}
.sidebar a:hover {
    background: #444;
}
.logo-img {
    width: 150px;
    margin-bottom: 20px;
}

.main {
    margin-left: 240px;
    min-height: 100vh;
}

.navbar {
    background: linear-gradient(90deg, #0b74ff, #00d4ff);
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
}
.navbar-brand {
    font-weight: 700;
    font-size: 20px;
}
.page-title {
    font-size: 28px;
    font-weight: 800;
    color: #0b74ff;
    margin-bottom: 24px;
}
.card-header {
    background: linear-gradient(90deg, #0b74ff, #00d4ff);
    color: white;
    border: none;
    font-weight: 700;
}
.table {
    background: white;
    border-radius: 8px;
    overflow: hidden;
}
.table thead th {
    background: #f8f9fa;
    color: #0b74ff;
    font-weight: 700;
    border: none;
    padding: 16px 12px;
    font-size: 13px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}
.table tbody td {
    padding: 14px 12px;
    border-color: #e5e7eb;
    vertical-align: middle;
}
.table tbody tr:hover {
    background: #f9f9f9;
}
.badge {
    padding: 6px 12px;
    border-radius: 6px;
    font-weight: 600;
    font-size: 12px;
}
.btn-sm {
    padding: 6px 12px;
    font-size: 12px;
    font-weight: 600;
    border-radius: 6px;
}
.btn-primary {
    background: linear-gradient(90deg, #0b74ff, #00d4ff);
    border: none;
}
.btn-primary:hover {
    background: linear-gradient(90deg, #0a63db, #00bfd4);
    transform: translateY(-2px);
}
.action-buttons {
    display: flex;
    gap: 6px;
    flex-wrap: wrap;
}
.stats-box {
    background: white;
    padding: 20px;
    border-radius: 8px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.05);
    margin-bottom: 20px;
}
.stat-value {
    font-size: 24px;
    font-weight: 800;
    color: #0b74ff;
}
.stat-label {
    font-size: 13px;
    color: #666;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    margin-top: 6px;
}
.container-fluid {
    background: white;
    margin-top: 20px;
    border-radius: 8px;
    padding: 24px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.05);
}

/* Mobile responsiveness */
@media (max-width: 992px) {
  .sidebar { 
    position: fixed; 
    left: -260px; 
    top: 0; 
    height: 100vh; 
    width: 240px; 
    z-index: 1050; 
    transition: left 0.35s ease; 
  }
  .sidebar.open { left: 0; }
  .main { margin-left: 0; }
}
</style>
</head>
<body>

<!-- SIDEBAR -->
<div class="sidebar text-center">
  <img src="<?php echo $base_url; ?>/assets/images/logo.png" class="logo-img img-fluid" alt="Logo">
  <h5 class="text-white mb-4">Admin Panel</h5>
  <a href="index.php">Dashboard</a>
  <a href="products.php">Products</a>
  <a href="orders.php">Orders</a>
  <a href="invoices.php" style="background: #444; font-weight: bold;">📄 Invoices</a>
  <a href="user.php">Customers</a>
  <a href="messages.php">Messages</a>
  <a href="logout.php">Logout</a>
</div>

<!-- MAIN CONTENT -->
<div class="main">
<nav class="navbar navbar-dark px-3">
  <div class="container-fluid d-flex align-items-center">
    <button id="sidebarToggle" class="btn btn-light d-md-none me-2" aria-label="Toggle sidebar"><i class="bi bi-list"></i></button>
    <div class="d-flex align-items-center">
      <img src="<?php echo $base_url; ?>/assets/images/logo.png" alt="Logo" style="height: 40px; margin-right: 12px;">
      <span class="navbar-brand mb-0">
        <i class="bi bi-file-earmark-pdf"></i> Invoice Management
      </span>
    </div>
    <div class="ms-auto d-flex align-items-center gap-2">
      <span class="text-white me-3">Admin</span>
      <a href="logout.php" class="btn btn-sm btn-outline-light">Logout</a>
    </div>
  </div>
</nav>

<div class="container-fluid px-4 py-4">
  
  <h1 class="page-title">📄 All Invoices</h1>

  <!-- Stats -->
  <div class="row mb-4">
    <div class="col-md-3">
      <div class="stats-box">
        <div class="stat-value"><?php echo count($orders); ?></div>
        <div class="stat-label">Total Invoices</div>
      </div>
    </div>
    <div class="col-md-3">
      <div class="stats-box">
        <div class="stat-value">₹<?php echo number_format((float)array_sum(array_column($orders, 'total')), 0); ?></div>
        <div class="stat-label">Total Revenue</div>
      </div>
    </div>
    <div class="col-md-3">
      <div class="stats-box">
        <div class="stat-value"><?php echo count(array_filter($orders, function($o) { return $o['status'] === 'Delivered'; })); ?></div>
        <div class="stat-label">Delivered Orders</div>
      </div>
    </div>
    <div class="col-md-3">
      <div class="stats-box">
        <div class="stat-value"><?php echo count(array_filter($orders, function($o) { return $o['status'] !== 'Delivered'; })); ?></div>
        <div class="stat-label">Pending Orders</div>
      </div>
    </div>
  </div>

  <!-- Invoices Table -->
  <div class="table-responsive">
    <table class="table">
      <thead>
        <tr>
          <th>Invoice #</th>
          <th>Customer</th>
          <th>Email</th>
          <th>Amount</th>
          <th>Items</th>
          <th>Status</th>
          <th>Date</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach($orders as $order): ?>
          <tr>
            <td>
              <strong>#<?php echo $order['id']; ?></strong><br>
              <small class="text-muted"><?php echo esc($order['tracking_code']); ?></small>
            </td>
            <td><?php echo esc($order['customer_name']); ?></td>
            <td><?php echo esc($order['email']); ?></td>
            <td>
              <strong style="color: #0b74ff;">₹<?php echo number_format((float)$order['total'], 2); ?></strong>
            </td>
            <td>
              <span class="badge bg-light text-dark"><?php echo $order['item_count']; ?> items</span>
            </td>
            <td>
              <?php
              $status_class = 'bg-warning';
              if ($order['status'] === 'Delivered') $status_class = 'bg-success';
              elseif ($order['status'] === 'Shipped') $status_class = 'bg-info';
              elseif ($order['status'] === 'Cancelled') $status_class = 'bg-danger';
              ?>
              <span class="badge <?php echo $status_class; ?>"><?php echo esc($order['status']); ?></span>
            </td>
            <td>
              <small><?php echo date('M d, Y', strtotime($order['created_at'])); ?></small>
            </td>
            <td>
              <div class="action-buttons">
                <a href="<?php echo $base_url; ?>/invoice.php?order=<?php echo $order['id']; ?>" 
                   class="btn btn-sm btn-primary" target="_blank" title="View Invoice">
                  <i class="bi bi-file-earmark-pdf"></i> View
                </a>
                <a href="<?php echo $base_url; ?>/invoice.php?order=<?php echo $order['id']; ?>&download=true" 
                   class="btn btn-sm btn-outline-primary" title="Download Invoice">
                  <i class="bi bi-download"></i>
                </a>
              </div>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>

  <?php if(empty($orders)): ?>
    <div class="alert alert-info text-center" role="alert">
      <i class="bi bi-info-circle"></i> No invoices found yet.
    </div>
  <?php endif; ?>
</div>
<!-- END MAIN CONTENT -->
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function(){
  const sidebarToggle = document.getElementById('sidebarToggle');
  const sidebar = document.querySelector('.sidebar');
  
  if(sidebarToggle && sidebar) {
    sidebarToggle.addEventListener('click', function(){
      sidebar.classList.toggle('open');
    });
  }
  
  // Close sidebar when a link is clicked on mobile
  const sidebarLinks = document.querySelectorAll('.sidebar a');
  sidebarLinks.forEach(link => {
    link.addEventListener('click', function(){
      if(window.innerWidth <= 992) {
        sidebar.classList.remove('open');
      }
    });
  });
});
</script>
</body>
</html>
