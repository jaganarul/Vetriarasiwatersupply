<?php
require_once __DIR__ . '/../init.php';
if (!is_admin_logged_in()) { header('Location: ' . $base_url . '/login.php'); exit; }

// ------- FILTER HANDLING (from first snippet) -------
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

// Count new orders and unread messages for notifications
$newOrdersCount = 0;
$unreadMessagesCount = 0;
try{
  $hasOrders = $pdo->query("SHOW TABLES LIKE 'orders'")->fetch();
  if($hasOrders){
    $col = $pdo->query("SHOW COLUMNS FROM orders LIKE 'is_new'")->fetch();
    if($col){
      $newOrdersCount = (int)$pdo->query('SELECT COUNT(*) FROM orders WHERE is_new = 1')->fetchColumn();
    }
  }
  $hasMessages = $pdo->query("SHOW TABLES LIKE 'messages'")->fetch();
  if($hasMessages){
    $colm = $pdo->query("SHOW COLUMNS FROM messages LIKE 'is_read'")->fetch();
    if($colm){
      $unreadMessagesCount = (int)$pdo->query('SELECT COUNT(*) FROM messages WHERE is_read = 0')->fetchColumn();
    }
  }
} catch(Exception $e) { /* ignore */ }

// ------- BASIC METRICS (from your second snippet) -------
$totalOrders = $pdo->query('SELECT COUNT(*) FROM orders')->fetchColumn();
$pending = $pdo->query("SELECT COUNT(*) FROM orders WHERE status='Pending'")->fetchColumn();
$delivered = $pdo->query("SELECT COUNT(*) FROM orders WHERE status='Delivered'")->fetchColumn();
$revenue = $pdo->query('SELECT COALESCE(SUM(total),0) FROM orders')->fetchColumn();

// Revenue Today
$revenueToday = $pdo->query("SELECT COALESCE(SUM(total),0) 
                             FROM orders 
                             WHERE DATE(created_at) = CURDATE()")->fetchColumn();

// Revenue This Month
$revenueMonth = $pdo->query("SELECT COALESCE(SUM(total),0) 
                             FROM orders 
                             WHERE MONTH(created_at) = MONTH(CURDATE()) 
                             AND YEAR(created_at) = YEAR(CURDATE())")->fetchColumn();

// Monthly Orders + Revenue (Last 6 Months) (kept from your second snippet)
$monthlyData = $pdo->query(" 
    SELECT 
        DATE_FORMAT(created_at, '%Y-%m') AS month,
        COUNT(*) AS total_orders,
        COALESCE(SUM(total),0) AS total_revenue
    FROM orders
    GROUP BY DATE_FORMAT(created_at, '%Y-%m')
    ORDER BY month ASC
")->fetchAll();

// Recent orders
$orders = $pdo->query('SELECT id,user_id,total,status,tracking_code,created_at FROM orders ORDER BY created_at DESC LIMIT 20')->fetchAll();

// Contact messages (ensure table exists)
$pdo->exec("CREATE TABLE IF NOT EXISTS messages (id INT AUTO_INCREMENT PRIMARY KEY, name VARCHAR(200), email VARCHAR(255), message TEXT, created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP) ENGINE=InnoDB;");
$messages = $pdo->query('SELECT * FROM messages ORDER BY created_at DESC LIMIT 50')->fetchAll();
?>
<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

<title>Admin Analytics</title>

<style>
    :root{
      --bg:#f4f6f9;
      --accent:#007bff;
    }
    body {
        background: var(--bg);
        font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial;
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
        min-height: 100vh;
    }
    .logo-img {
        width: 150px;
        margin-bottom: 20px;
    }
    .filter-btn { margin-right: 8px; }

    h3, h5 { font-weight: 600; }
    .card { border-radius: 12px; }
    .card-custom { border-radius: 15px; box-shadow: 0 4px 20px rgba(0,0,0,0.06); transition: 0.3s; }
    .card-custom:hover { transform: translateY(-4px); box-shadow: 0 6px 30px rgba(0,0,0,0.08); }
    .chart-card { border-radius: 12px; padding: 16px; background: white; box-shadow: 0 6px 20px rgba(0,0,0,0.06); }
    .metric-number { font-size: 22px; font-weight: 700; }

    .tools-panel { position: sticky; top: 16px; }
    .calc-screen { height: 48px; font-size: 1.25rem; }
    .calc-btn { min-width: 55px; }

    @media (max-width: 992px){
      .sidebar { position: fixed; left: -260px; top: 0; height: 100vh; width: 240px; z-index: 1050; transition: left 0.35s ease; }
      .sidebar.open { left: 0; }
      .main { margin-left: 0; padding-top: 12px; }
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
  <a href="orders.php">Orders <?php if($newOrdersCount>0): ?><span class="badge bg-danger" style="margin-left:8px;"><?php echo $newOrdersCount; ?></span><?php endif; ?></a>
  <a href="invoices.php">📄 Invoices</a>
  <a href="users.php">Customers</a>
  <a href="messages.php">Messages <?php if($unreadMessagesCount>0): ?><span class="badge bg-warning text-dark" style="margin-left:8px;"><?php echo $unreadMessagesCount; ?></span><?php endif; ?></a>
  <a href="logout.php">Logout</a>
</div>

<!-- MAIN CONTENT -->
<div class="main">
<nav class="navbar navbar-light bg-white shadow-sm px-4">
  <div class="container-fluid d-flex align-items-center">
    <button id="sidebarToggle" class="btn btn-light d-md-none me-2" aria-label="Toggle sidebar"><i class="bi bi-list"></i></button>
    <span class="navbar-brand mb-0 h5">Welcome, <?php echo esc($_SESSION['admin_name']); ?></span>
  </div>
</nav>

<div class="container-fluid py-4">

  <!-- FILTER BUTTONS (Sales filter) -->
  <div class="mb-3">
    <a href="?filter=daily" class="btn <?php echo $filter==='daily'?'btn-primary':'btn-outline-primary'; ?> filter-btn">Daily</a>
    <a href="?filter=weekly" class="btn <?php echo $filter==='weekly'?'btn-primary':'btn-outline-primary'; ?> filter-btn">Weekly</a>
    <a href="?filter=monthly" class="btn <?php echo $filter==='monthly'?'btn-primary':'btn-outline-primary'; ?> filter-btn">Monthly</a>
    <a href="?filter=yearly" class="btn <?php echo $filter==='yearly'?'btn-primary':'btn-outline-primary'; ?> filter-btn">Yearly</a>
  </div>

  <div class="row">
    <!-- SALES CHART (uses $labels / $values from filter) -->
    <div class="col-md-8">
      <div class="card p-3 mb-4 shadow-sm">
        <h5 class="mb-3">Sales Analytics (<?php echo ucfirst($filter); ?>)</h5>
        <?php if ($newOrdersCount > 0): ?>
          <div class="alert alert-warning">You have <strong><?php echo $newOrdersCount; ?></strong> new order(s). <a href="orders.php">View Orders</a></div>
        <?php endif; ?>
        <canvas id="salesChart" aria-label="Sales chart" role="img"></canvas>
      </div>
    </div>

    <!-- TOP PRODUCTS + Quick actions -->
    <div class="col-md-4">
      <div class="card p-3 mb-4 shadow-sm">
        <h5 class="mb-3">Top Products</h5>
        <ul class="list-group">
        <?php foreach ($top as $t): ?>
          <li class="list-group-item d-flex justify-content-between align-items-center">
            <?php echo esc($t['name']); ?>
            <span class="badge bg-primary"><?php echo $t['sold']; ?></span>
          </li>
        <?php endforeach; ?>
        </ul>
      </div>

      <a class="btn btn-primary w-100 mb-2" href="products.php">Manage Products</a>
      <a class="btn btn-secondary w-100 mb-2" href="orders.php">Manage Orders</a>
      <a class="btn btn-info w-100" href="invoices.php">View All Invoices</a>
    </div>
  </div>

  <!-- ---- The rest of the dashboard (metrics, monthly chart, recent orders, messages) ---- -->
  <div class="row g-3 mb-4 mt-3">

    <div class="col-md-3">
      <div class="card card-custom p-3 text-center">
        <h6 class="text-muted">Total Orders</h6>
        <div class="metric-number text-primary"><?php echo $totalOrders; ?></div>
      </div>
    </div>

    <div class="col-md-3">
      <div class="card card-custom p-3 text-center">
        <h6 class="text-muted">Pending</h6>
        <div class="metric-number"><?php echo $pending; ?></div>
      </div>
    </div>

    <div class="col-md-3">
      <div class="card card-custom p-3 text-center">
        <h6 class="text-muted">Delivered</h6>
        <div class="metric-number"><?php echo $delivered; ?></div>
      </div>
    </div>

    <div class="col-md-3">
      <div class="card card-custom p-3 text-center">
        <h6 class="text-muted">Revenue</h6>
        <div class="metric-number">₹<?php echo number_format((float)($revenue ?? 0),2); ?></div>
      </div>
    </div>

    <div class="col-md-4">
      <div class="card card-custom p-3 text-center">
        <h6 class="text-muted">Revenue Today</h6>
        <div class="metric-number text-success">₹<?php echo number_format($revenueToday, 2); ?></div>
      </div>
    </div>

    <div class="col-md-4">
      <div class="card card-custom p-3 text-center">
        <h6 class="text-muted">Revenue This Month</h6>
        <div class="metric-number text-danger">₹<?php echo number_format($revenueMonth, 2); ?></div>
      </div>
    </div>

    <!-- Tools (Calendar + Calculator) placed in the grid -->
    <div class="col-md-4">
      <div class="card p-3">
        <h6 class="text-muted">Quick Tools</h6>
        <div class="mt-2 tools-panel">
          <label class="small text-muted">Select Date</label>
          <input id="miniCalendar" class="form-control mb-3" placeholder="Pick a date..">

          <!-- Calculator -->
          <div class="card mt-2 p-2">
            <input id="calcScreen" class="form-control calc-screen text-end mb-2" readonly>
            <div class="d-flex flex-wrap gap-2">
              <?php $buttons = ['7','8','9','/','4','5','6','*','1','2','3','-','0','.','=','+','C'];
              foreach($buttons as $b){
                $cls = $b==='=' ? 'btn-primary' : ($b==='C' ? 'btn-danger' : 'btn-light');
                echo "<button class='btn $cls calc-btn' data-val='".htmlspecialchars($b, ENT_QUOTES)."'>".htmlspecialchars($b)."</button>";
              }
              ?>
            </div>
          </div>

        </div>
      </div>
    </div>

  </div>

  <div class="row">
    <div class="col-lg-8">
      <div class="chart-card mb-4">
        <h5 class="fw-bold mb-3">📅 Monthly Orders & Revenue</h5>
        <canvas id="ordersRevenueChart" height="110"></canvas>
      </div>

      <h5 class="mt-3 mb-2">Recent Orders</h5>
      <div class="table-responsive">
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
      </div>

    </div>

    <div class="col-lg-4">
      <h5 class="mb-3">Customer Messages</h5>

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

      <!-- Small stats card -->
      <div class="card p-3 mb-3">
        <h6 class="text-muted">Summary</h6>
        <p class="mb-0 small">Total Orders: <?php echo $totalOrders; ?><br>Pending: <?php echo $pending; ?><br>Delivered: <?php echo $delivered; ?></p>
      </div>

    </div>
  </div>

</div>

<!-- Scripts -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<script>
/**
 * Robust chart initialization:
 * - Uses json_encoded PHP arrays (safe)
 * - Checks element presence before creating Chart
 * - Handles empty data gracefully
 * - Wrapped in DOMContentLoaded to ensure canvases present
 */

// Sales filter data (daily/weekly/monthly/yearly)
const salesLabels = <?php echo json_encode(array_values($labels ?: [])); ?>;
const salesValues = <?php echo json_encode(array_values($values ?: []), JSON_NUMERIC_CHECK); ?>;

// Monthly orders & revenue arrays (from $monthlyData)
const monthLabels = <?php
    $ml = array_map(function($r){ return $r['month']; }, $monthlyData ?: []);
    echo json_encode($ml);
?>;
const monthlyOrders = <?php
    $mo = array_map(function($r){ return (int)$r['total_orders']; }, $monthlyData ?: []);
    echo json_encode($mo, JSON_NUMERIC_CHECK);
?>;
const monthlyRevenue = <?php
    $mr = array_map(function($r){ return (float)$r['total_revenue']; }, $monthlyData ?: []);
    echo json_encode($mr, JSON_NUMERIC_CHECK);
?>;

document.addEventListener('DOMContentLoaded', function(){
  // ---------------- Sales Chart ----------------
  const salesEl = document.getElementById('salesChart');
  if(salesEl){
    const sl = (Array.isArray(salesLabels) && salesLabels.length) ? salesLabels : ['No Data'];
    const sv = (Array.isArray(salesValues) && salesValues.length) ? salesValues : [0];

    try {
      new Chart(salesEl.getContext('2d'), {
        type: 'line',
        data: {
          labels: sl,
          datasets: [{
            label: 'Sales',
            data: sv,
            borderColor: '#007bff',
            tension: 0.3,
            borderWidth: 2,
            pointRadius: 3,
            fill: false
          }]
        },
        options: {
          responsive: true,
          scales: { y: { beginAtZero: true } },
          plugins: { legend: { display: false } }
        }
      });
    } catch(err){
      console.error('Failed to init salesChart', err);
      salesEl.insertAdjacentHTML('afterend','<div class="text-danger small mt-2">Chart failed to load.</div>');
    }
  }

  // -------------- Monthly Orders & Revenue Chart --------------
  const mrEl = document.getElementById('ordersRevenueChart');
  if(mrEl){
    const ml = (Array.isArray(monthLabels) && monthLabels.length) ? monthLabels : ['No Data'];
    const od = (Array.isArray(monthlyOrders) && monthlyOrders.length) ? monthlyOrders : [0];
    const rd = (Array.isArray(monthlyRevenue) && monthlyRevenue.length) ? monthlyRevenue : [0];

    try {
      new Chart(mrEl.getContext('2d'), {
        type: 'bar',
        data: {
          labels: ml,
          datasets: [
            {
              label: 'Orders',
              data: od,
              borderWidth: 2,
              backgroundColor: 'rgba(54, 162, 235, 0.6)'
            },
            {
              label: 'Revenue (₹)',
              type: 'line',
              data: rd,
              borderWidth: 2,
              fill: false,
              tension: 0.3,
              borderColor: 'rgba(255, 99, 132, 1)'
            }
          ]
        },
        options: {
          responsive: true,
          plugins: { legend: { position: 'top' } },
          scales: { y: { beginAtZero: true } }
        }
      });
    } catch(err){
      console.error('Failed to init ordersRevenueChart', err);
      mrEl.insertAdjacentHTML('afterend','<div class="text-danger small mt-2">Chart failed to load.</div>');
    }
  }

  // ---------------- Flatpickr calendar ----------------
  try {
    flatpickr("#miniCalendar", {
      inline: false,
      enableTime: false,
      dateFormat: "Y-m-d",
      defaultDate: new Date()
    });
  } catch(e){ console.warn('Flatpickr init failed', e); }

  // ---------------- Calculator logic ----------------
  const screen = document.getElementById('calcScreen');
  if(screen){
    document.querySelectorAll('.calc-btn').forEach(btn => {
      btn.addEventListener('click', () => {
        const v = btn.getAttribute('data-val');
        if(v === 'C') { screen.value = ''; return; }
        if(v === '=') {
          try {
            if(/^[0-9\.\+\-\*\/\s()%]+$/.test(screen.value)){
              // safer evaluation via Function
              const res = Function('"use strict";return (' + screen.value + ')')();
              screen.value = (typeof res === 'undefined') ? '' : res;
            } else {
              screen.value = 'Err';
            }
          } catch(e) { screen.value = 'Err'; }
          return;
        }
        screen.value += v;
      });
    });

    // keyboard input for calculator (basic)
    document.addEventListener('keydown', (e) => {
      const allowed = '0123456789+-*/().';
      if(allowed.includes(e.key)) { screen.value += e.key; return; }
      if(e.key === 'Enter') {
        try { if(/^[0-9\.\+\-\*\/\s()%]+$/.test(screen.value)){ screen.value = Function('"use strict";return (' + screen.value + ')')(); } else screen.value='Err'; } catch(e){ screen.value='Err'; }
      }
      if(e.key === 'Backspace') { screen.value = screen.value.slice(0,-1); }
      if(e.key.toLowerCase() === 'c') { screen.value = ''; }
    });
  }
});
</script>

<script>
// sidebar toggle for small screens
document.addEventListener('DOMContentLoaded', function(){
  const btn = document.getElementById('sidebarToggle');
  if(!btn) return;
  btn.addEventListener('click', function(){
    const s = document.querySelector('.sidebar');
    if(!s) return;
    const overlayId = 'adminSidebarOverlay';
    if(s.classList.contains('open')){
      s.classList.remove('open');
      const ov = document.getElementById(overlayId);
      if(ov) ov.remove();
      document.body.style.overflow = '';
    } else {
      s.classList.add('open');
      const ov = document.createElement('div');
      ov.id = overlayId;
      ov.style.position = 'fixed';
      ov.style.top = '0';
      ov.style.left = '0';
      ov.style.right = '0';
      ov.style.bottom = '0';
      ov.style.background = 'rgba(0,0,0,0.2)';
      ov.style.zIndex = '1040';
      ov.addEventListener('click', function(){ s.classList.remove('open'); ov.remove(); document.body.style.overflow = ''; });
      document.body.appendChild(ov);
      document.body.style.overflow = 'hidden';
    }
  });
});
</script>

</body>
</html>
