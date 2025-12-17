<?php
require_once __DIR__ . '/../init.php';
if (!is_admin_logged_in()) { 
    header('Location: ' . $base_url . '/login.php'); 
    exit; 
}

// Get user ID from query parameter
$user_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if (!$user_id) {
    header('Location: user.php');
    exit;
}

// Fetch user details
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();

if (!$user) {
    header('Location: user.php');
    exit;
}

// Fetch user's orders
$stmt = $pdo->prepare("
    SELECT id, total, status, created_at, tracking_code 
    FROM orders 
    WHERE user_id = ? 
    ORDER BY created_at DESC
");
$stmt->execute([$user_id]);
$orders = $stmt->fetchAll();

// Fetch user's invoices
$stmt = $pdo->prepare("
    SELECT i.id, i.order_id, i.invoice_number, i.total, i.status, i.created_at, i.due_date
    FROM invoices i
    INNER JOIN orders o ON i.order_id = o.id
    WHERE o.user_id = ?
    ORDER BY i.created_at DESC
");
$stmt->execute([$user_id]);
$invoices = $stmt->fetchAll();
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
<title>Customer Details - Admin</title>
<style>
    :root {
        --bg: #f4f6f9;
        --accent: #0b74ff;
        --accent2: #00d4ff;
    }
    
    body { 
        background: var(--bg);
        font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial;
    }
    
    .navbar { 
        background: linear-gradient(90deg, var(--accent) 0%, var(--accent2) 100%);
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    }
    
    .logo { height: 40px; }
    
    /* Sidebar - Dark Theme Consistent */
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
        text-decoration: none;
        padding: 12px 20px;
        display: block;
        font-size: 15px;
        transition: 0.2s;
    }
    
    .sidebar a:hover { 
        background: #444;
    }
    
    .sidebar a.active { 
        background: #444;
        font-weight: bold;
    }
    
    .main {
        margin-left: 240px;
        min-height: 100vh;
    }
    
    .logo-img {
        width: 150px;
        margin-bottom: 20px;
    }
    
    .customer-card { 
        background: white;
        border-radius: 12px;
        padding: 20px;
        margin-bottom: 20px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    }
    
    .customer-header { 
        display: flex;
        align-items: center;
        gap: 15px;
        padding-bottom: 15px;
        border-bottom: 1px solid #e0e0e0;
    }
    
    .customer-icon { 
        font-size: 40px;
        color: var(--accent);
    }
    
    .customer-info h3 { 
        margin: 0;
        color: #333;
    }
    
    .customer-info p { 
        margin: 5px 0;
        color: #666;
        font-size: 14px;
    }
    
    .info-section { 
        margin-top: 15px;
    }
    
    .info-row { 
        display: flex;
        gap: 20px;
        margin: 10px 0;
    }
    
    .info-label { 
        font-weight: 600;
        color: #666;
        min-width: 120px;
    }
    
    .info-value { 
        color: #333;
    }
    
    .data-section {
        background: white;
        border-radius: 12px;
        padding: 20px;
        margin-bottom: 20px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    }
    
    .section-title {
        font-size: 18px;
        font-weight: 600;
        color: #333;
        margin-bottom: 15px;
        padding-bottom: 10px;
        border-bottom: 2px solid var(--accent);
    }
    
    .table {
        background: white;
        border-radius: 8px;
    }
    
    .table thead th {
        background: #f8f9fa;
        color: var(--accent);
        font-weight: 700;
        border: none;
    }
    
    .badge-pending { background-color: #ffc107; color: #000; }
    .badge-delivered { background-color: #28a745; color: #fff; }
    .badge-shipped { background-color: #17a2b8; color: #fff; }
    .badge-cancelled { background-color: #dc3545; color: #fff; }
    .badge-paid { background-color: #28a745; color: #fff; }
    .badge-unpaid { background-color: #ffc107; color: #000; }
    .badge-overdue { background-color: #dc3545; color: #fff; }
    
    .btn-primary {
        background: linear-gradient(90deg, var(--accent), var(--accent2));
        border: none;
    }
    
    .btn-primary:hover {
        background: linear-gradient(90deg, #0066ff, #00c4ff);
        border: none;
    }
    
    @media (max-width: 992px) {
        .sidebar { 
            position: fixed;
            left: -260px;
            top: 0;
            height: 100vh;
            width: 240px;
            z-index: 1050;
            transition: left 0.35s ease;
            overflow-y: auto;
        }
        .sidebar.open { left: 0; }
        .main { margin-left: 0; }
    }
    
    @media (max-width: 575px) {
        .customer-header { 
            flex-direction: column;
            align-items: flex-start;
        }
        .info-row { 
            flex-direction: column;
            gap: 5px;
        }
        .customer-card { 
            padding: 15px;
        }
    }
</style>
</head>
<body>

<!-- SIDEBAR -->
<div class="sidebar text-center" id="sidebar">
  <img src="<?php echo $base_url; ?>/assets/images/logo.png" class="logo-img img-fluid" alt="Logo">
  <h5 class="text-white mb-4">Admin Panel</h5>
  <a href="index.php" class="<?php echo strpos($_SERVER['REQUEST_URI'], 'index.php') !== false ? 'active' : ''; ?>">
    📊 Dashboard
  </a>
  <a href="products.php" class="<?php echo strpos($_SERVER['REQUEST_URI'], 'products.php') !== false ? 'active' : ''; ?>">
    📦 Products
  </a>
  <a href="orders.php" class="<?php echo strpos($_SERVER['REQUEST_URI'], 'orders.php') !== false ? 'active' : ''; ?>">
    🛒 Orders
  </a>
  <a href="invoices.php" class="<?php echo strpos($_SERVER['REQUEST_URI'], 'invoices.php') !== false ? 'active' : ''; ?>">
    📄 Invoices
  </a>
  <a href="user.php" class="<?php echo strpos($_SERVER['REQUEST_URI'], 'user.php') !== false ? 'active' : ''; ?>">
    👥 Customers
  </a>
  <a href="messages.php" class="<?php echo strpos($_SERVER['REQUEST_URI'], 'messages.php') !== false ? 'active' : ''; ?>">
    💬 Messages
  </a>
  <hr style="background: #444; margin: 10px 0;">
  <a href="logout.php" style="color: #ff6b6b;">
    🚪 Logout
  </a>
</div>

<!-- MAIN CONTENT -->
<div class="main">
<nav class="navbar navbar-dark px-3">
  <div class="container-fluid d-flex align-items-center">
    <button id="sidebarToggle" class="btn btn-light d-md-none me-2" aria-label="Toggle sidebar">
      <i class="bi bi-list"></i>
    </button>
    <div class="d-flex align-items-center">
      <img src="<?php echo $base_url; ?>/assets/images/logo.png" class="logo me-2 img-fluid">
      <span class="navbar-brand mb-0">Customer Details</span>
    </div>
    <div class="ms-auto">
      <span class="text-white me-3">Admin</span>
      <a href="logout.php" class="btn btn-sm btn-outline-light">Logout</a>
    </div>
  </div>
</nav>

<div class="container-fluid px-4 py-4">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2>Customer Details</h2>
                <a href="user.php" class="btn btn-secondary">
                    <i class="bi bi-arrow-left me-2"></i>Back to Customers
                </a>
            </div>

            <!-- Customer Information -->
            <div class="customer-card">
                <div class="customer-header">
                    <div class="customer-icon">👤</div>
                    <div class="customer-info">
                        <h3><?php echo esc($user['name']); ?></h3>
                        <p>Customer ID: <?php echo $user['id']; ?></p>
                        <p class="mb-0">Member since <?php echo date('M d, Y', strtotime($user['created_at'])); ?></p>
                    </div>
                </div>

                <div class="info-section">
                    <h5 class="mb-3">Contact Information</h5>
                    <div class="info-row">
                        <span class="info-label">Email:</span>
                        <span class="info-value"><?php echo esc($user['email']); ?></span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Phone:</span>
                        <span class="info-value"><?php echo esc($user['phone'] ?? 'N/A'); ?></span>
                    </div>
                </div>

                <div class="info-section">
                    <h5 class="mb-3">Delivery Address</h5>
                    <div class="info-row">
                        <span class="info-label">Address:</span>
                        <span class="info-value"><?php echo nl2br(esc($user['address'] ?? 'N/A')); ?></span>
                    </div>
                </div>

                <div class="info-section">
                    <h5 class="mb-3">Account Statistics</h5>
                    <div class="row">
                        <div class="col-sm-6 col-md-4">
                            <div style="background: #f0f7ff; padding: 12px; border-radius: 8px; text-align: center; border-left: 4px solid var(--accent);">
                                <div style="font-size: 24px; font-weight: bold; color: var(--accent);"><?php echo count($orders); ?></div>
                                <div style="font-size: 12px; color: #666;">Total Orders</div>
                            </div>
                        </div>
                        <div class="col-sm-6 col-md-4">
                            <div style="background: #f0f7ff; padding: 12px; border-radius: 8px; text-align: center; border-left: 4px solid var(--accent2);">
                                <div style="font-size: 24px; font-weight: bold; color: var(--accent2);">
                                    ₹<?php 
                                        $total = 0;
                                        foreach($orders as $order) $total += $order['total'];
                                        echo number_format($total, 2);
                                    ?>
                                </div>
                                <div style="font-size: 12px; color: #666;">Total Spent</div>
                            </div>
                        </div>
                        <div class="col-sm-6 col-md-4">
                            <div style="background: #f0f7ff; padding: 12px; border-radius: 8px; text-align: center; border-left: 4px solid var(--accent);">
                                <div style="font-size: 24px; font-weight: bold; color: var(--accent);"><?php echo count($invoices); ?></div>
                                <div style="font-size: 12px; color: #666;">Total Invoices</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Customer Orders -->
            <div class="data-section">
                <h5 class="section-title">Order History</h5>
                <?php if(count($orders) > 0): ?>
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead class="table-light">
                                <tr>
                                    <th>Order ID</th>
                                    <th>Date</th>
                                    <th>Amount</th>
                                    <th>Status</th>
                                    <th>Tracking</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach($orders as $order): ?>
                                    <tr>
                                        <td><strong>#<?php echo $order['id']; ?></strong></td>
                                        <td><?php echo date('M d, Y', strtotime($order['created_at'])); ?></td>
                                        <td><strong>₹<?php echo number_format($order['total'], 2); ?></strong></td>
                                        <td>
                                            <span class="badge badge-<?php echo strtolower($order['status']); ?>">
                                                <?php echo ucfirst($order['status']); ?>
                                            </span>
                                        </td>
                                        <td><?php echo esc($order['tracking_code'] ?? 'N/A'); ?></td>
                                        <td>
                                            <a href="order_view.php?id=<?php echo $order['id']; ?>" class="btn btn-sm btn-primary">
                                                <i class="bi bi-eye me-1"></i>View
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="alert alert-info">No orders found for this customer.</div>
                <?php endif; ?>
            </div>

            <!-- Customer Invoices -->
            <div class="data-section">
                <h5 class="section-title">Invoice History</h5>
                <?php if(count($invoices) > 0): ?>
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead class="table-light">
                                <tr>
                                    <th>Invoice #</th>
                                    <th>Order ID</th>
                                    <th>Date</th>
                                    <th>Amount</th>
                                    <th>Status</th>
                                    <th>Due Date</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach($invoices as $invoice): ?>
                                    <tr>
                                        <td><strong><?php echo esc($invoice['invoice_number']); ?></strong></td>
                                        <td>#<?php echo $invoice['order_id']; ?></td>
                                        <td><?php echo date('M d, Y', strtotime($invoice['created_at'])); ?></td>
                                        <td><strong>₹<?php echo number_format($invoice['total'], 2); ?></strong></td>
                                        <td>
                                            <span class="badge badge-<?php echo strtolower($invoice['status']); ?>">
                                                <?php echo ucfirst($invoice['status']); ?>
                                            </span>
                                        </td>
                                        <td>
                                            <?php 
                                                $due_date = strtotime($invoice['due_date']);
                                                $now = time();
                                                $status_class = 'text-success';
                                                if($due_date < $now) $status_class = 'text-danger';
                                                echo '<span class="' . $status_class . '">' . date('M d, Y', $due_date) . '</span>';
                                            ?>
                                        </td>
                                        <td>
                                            <a href="order_view.php?id=<?php echo $invoice['order_id']; ?>" class="btn btn-sm btn-primary">
                                                <i class="bi bi-file-pdf me-1"></i>View
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="alert alert-info">No invoices found for this customer.</div>
                <?php endif; ?>
            </div>

        </div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function(){
  const sidebarToggle = document.getElementById('sidebarToggle');
  const sidebar = document.getElementById('sidebar');
  
  if(sidebarToggle && sidebar) {
    sidebarToggle.addEventListener('click', function(){
      sidebar.classList.toggle('open');
    });
  }
  
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
