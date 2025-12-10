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
    body { background: #f5f7fa; }
    .navbar { background: linear-gradient(135deg, #0b74ff 0%, #00d4ff 100%); }
    .logo { height: 40px; }
    .sidebar { background: #fff; border-right: 1px solid #e0e0e0; }
    .sidebar a { color: #666; text-decoration: none; padding: 12px 15px; display: block; }
    .sidebar a:hover { background: #f5f7fa; color: #0b74ff; }
    .sidebar a.active { background: #e7f3ff; color: #0b74ff; border-right: 3px solid #0b74ff; }
    .customer-card { background: white; border-radius: 12px; padding: 20px; margin-bottom: 20px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); }
    .customer-header { display: flex; align-items: center; gap: 15px; padding-bottom: 15px; border-bottom: 1px solid #e0e0e0; }
    .customer-icon { font-size: 40px; color: #0b74ff; }
    .customer-info h3 { margin: 0; color: #333; }
    .customer-info p { margin: 5px 0; color: #666; font-size: 14px; }
    .info-section { margin-top: 15px; }
    .info-row { display: flex; gap: 20px; margin: 10px 0; }
    .info-label { font-weight: 600; color: #666; min-width: 120px; }
    .info-value { color: #333; }
    .orders-table { background: white; border-radius: 12px; padding: 20px; }
    .badge-pending { background-color: #ffc107; color: #000; }
    .badge-delivered { background-color: #28a745; color: #fff; }
    .badge-shipped { background-color: #17a2b8; color: #fff; }
    .badge-cancelled { background-color: #dc3545; color: #fff; }
    @media (max-width: 992px) {
        .sidebar { position: fixed; left: -260px; top: 0; height: 100vh; width: 240px; z-index: 1050; transition: left 0.35s ease; overflow-y: auto; }
        .sidebar.open { left: 0; }
        .main { margin-left: 0; }
    }
    @media (max-width: 575px) {
        .customer-header { flex-direction: column; align-items: flex-start; }
        .info-row { flex-direction: column; gap: 5px; }
        .customer-card { padding: 15px; }
    }
</style>
</head>
<body>

<nav class="navbar navbar-dark mb-3">
    <div class="container d-flex align-items-center">
        <button id="sidebarToggle" class="btn btn-light d-md-none me-2" aria-label="Toggle sidebar">
            <i class="bi bi-list"></i>
        </button>
        <a class="navbar-brand d-flex align-items-center" href="index.php">
            <img src="<?php echo $base_url; ?>/assets/images/logo.png" class="logo me-2 img-fluid">
            Admin Panel
        </a>
        <div class="ms-auto d-flex align-items-center gap-3">
            <span class="text-white"><?php echo $_SESSION['admin_name'] ?? 'Admin'; ?></span>
            <a href="logout.php" class="btn btn-light btn-sm">Logout</a>
        </div>
    </div>
</nav>

<div class="container-fluid">
    <div class="row">
        <!-- Sidebar -->
        <aside class="col-md-2 sidebar" id="sidebar">
            <a href="index.php" class="<?php echo strpos($_SERVER['REQUEST_URI'], 'index.php') !== false ? 'active' : ''; ?>">
                <i class="bi bi-house-door me-2"></i> Dashboard
            </a>
            <a href="orders.php" class="<?php echo strpos($_SERVER['REQUEST_URI'], 'orders.php') !== false ? 'active' : ''; ?>">
                <i class="bi bi-bag-check me-2"></i> Orders
            </a>
            <a href="products.php" class="<?php echo strpos($_SERVER['REQUEST_URI'], 'products.php') !== false ? 'active' : ''; ?>">
                <i class="bi bi-box me-2"></i> Products
            </a>
            <a href="user.php" class="<?php echo strpos($_SERVER['REQUEST_URI'], 'user.php') !== false ? 'active' : ''; ?>">
                <i class="bi bi-people me-2"></i> Customers
            </a>
            <a href="invoices.php" class="<?php echo strpos($_SERVER['REQUEST_URI'], 'invoices.php') !== false ? 'active' : ''; ?>">
                <i class="bi bi-file-text me-2"></i> Invoices
            </a>
            <a href="analytics.php" class="<?php echo strpos($_SERVER['REQUEST_URI'], 'analytics.php') !== false ? 'active' : ''; ?>">
                <i class="bi bi-bar-chart me-2"></i> Analytics
            </a>
            <hr>
            <a href="logout.php" class="text-danger">
                <i class="bi bi-box-arrow-right me-2"></i> Logout
            </a>
        </aside>

        <!-- Main Content -->
        <main class="col-md-10 main">
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
                            <div style="background: #f0f7ff; padding: 12px; border-radius: 8px; text-align: center;">
                                <div style="font-size: 24px; font-weight: bold; color: #0b74ff;"><?php echo count($orders); ?></div>
                                <div style="font-size: 12px; color: #666;">Total Orders</div>
                            </div>
                        </div>
                        <div class="col-sm-6 col-md-4">
                            <div style="background: #f0f7ff; padding: 12px; border-radius: 8px; text-align: center;">
                                <div style="font-size: 24px; font-weight: bold; color: #00d4ff;">
                                    ₹<?php 
                                        $total = 0;
                                        foreach($orders as $order) $total += $order['total'];
                                        echo number_format($total, 2);
                                    ?>
                                </div>
                                <div style="font-size: 12px; color: #666;">Total Spent</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Customer Orders -->
            <div class="orders-table">
                <h5 class="mb-3">Order History</h5>
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

        </main>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function(){
    const btn = document.getElementById('sidebarToggle');
    if(!btn) return;
    btn.addEventListener('click', function(){
        const s = document.getElementById('sidebar');
        if(!s) return;
        s.classList.toggle('open');
    });
});
</script>

</body>
</html>
