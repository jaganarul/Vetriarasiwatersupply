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

// --- New: Sales for last 30 days (used by the modern chart)
// This query returns one row per date (for last 30 days), date ascending
$salesStmt = $pdo->query("
    SELECT
      DATE(created_at) AS d,
      COALESCE(SUM(total),0) AS s
    FROM orders
    WHERE created_at >= DATE_SUB(CURDATE(), INTERVAL 29 DAY)
    GROUP BY DATE(created_at)
    ORDER BY DATE(created_at) ASC
");
$salesRows = $salesStmt->fetchAll(PDO::FETCH_ASSOC);

// Build labels for full 30-day window even if some days have no rows
$labels30 = [];
$values30 = [];
$start = new DateTime('-29 days');
$end = new DateTime('today');
$period = new DatePeriod($start, new DateInterval('P1D'), (clone $end)->modify('+1 day'));

$salesMap = [];
foreach($salesRows as $r){
  $salesMap[$r['d']] = (float)$r['s'];
}
foreach($period as $dt){
  $key = $dt->format('Y-m-d');
  $labels30[] = $key;
  $values30[] = isset($salesMap[$key]) ? $salesMap[$key] : 0;
}
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<title>Admin Analytics</title>

<style>
    body {
        background: #f5f7fa;
        font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Arial;
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

    /* Modern chart card */
    .chart-modern {
      background: linear-gradient(180deg, #ffffff 0%, #fbfdff 100%);
      border-radius: 14px;
      padding: 18px;
      box-shadow: 0 8px 30px rgba(11,22,40,0.04);
    }

    /* small responsive tweaks */
    @media (max-width: 576px) {
      .chart-modern { padding: 12px; }
    }
    @media (max-width: 992px){
      .sidebar { position: fixed; left: -260px; top: 0; height: 100vh; width: 240px; z-index: 1050; transition: left 0.35s ease; }
      .sidebar.open { left: 0; }
      .main { margin-left: 0; }
    }
</style>

</head>
<body>

<nav class="navbar navbar-dark">
  <div class="container d-flex align-items-center">
    <button id="sidebarToggle" class="btn btn-light d-md-none me-2" aria-label="Toggle sidebar"><i class="bi bi-list"></i></button>
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

  <!-- NEW: Modern Sales Chart (last 30 days) -->
  <div class="row mb-4">
    <div class="col-12">
      <div class="chart-modern">
        <div class="d-flex justify-content-between align-items-center mb-2">
          <div>
            <h5 class="mb-0">Sales — Last 30 days</h5>
            <small class="text-muted">Y axis fixed from 0 to 5000</small>
          </div>
          <div class="text-end">
            <small class="text-muted">Range: <?php echo htmlspecialchars($labels30[0] ?? ''); ?> — <?php echo htmlspecialchars(end($labels30) ?? ''); ?></small>
          </div>
        </div>
        <canvas id="modernSalesChart" height="120" aria-label="Sales chart last 30 days" role="img"></canvas>
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
<script>
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

<!-- Chart script -->
<script>
document.addEventListener('DOMContentLoaded', function(){

  const ctx = document.getElementById('modernSalesChart');
  if(!ctx) return;

  // Data from PHP
  const labels = <?php echo json_encode($labels30, JSON_HEX_TAG); ?>;
  const values = <?php echo json_encode($values30, JSON_NUMERIC_CHECK); ?>;

  // Create gradient for the area under the line
  const c = ctx.getContext('2d');
  const gradient = c.createLinearGradient(0,0,0,200);
  gradient.addColorStop(0, 'rgba(54,162,235,0.28)');
  gradient.addColorStop(1, 'rgba(54,162,235,0.02)');

  // Modern chart options
  const config = {
    type: 'line',
    data: {
      labels: labels,
      datasets: [{
        label: 'Daily revenue (₹)',
        data: values,
        fill: true,
        backgroundColor: gradient,
        borderColor: 'rgba(54,162,235,1)',
        pointBackgroundColor: 'rgba(255,255,255,1)',
        pointBorderColor: 'rgba(54,162,235,1)',
        pointRadius: 3,
        pointHoverRadius: 6,
        tension: 0.35,
        borderWidth: 2
      }]
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      interaction: { mode: 'index', intersect: false },
      plugins: {
        legend: { display: false },
        tooltip: {
          callbacks: {
            label: function(context){
              let v = context.parsed.y;
              return '₹' + (v===null ? '0' : v.toLocaleString(undefined, {minimumFractionDigits:2, maximumFractionDigits:2}));
            }
          }
        }
      },
      scales: {
        x: {
          grid: { display: false },
          ticks: { maxRotation: 0, autoSkip: true, maxTicksLimit: 10 }
        },
        y: {
          suggestedMin: 0,
          suggestedMax: 5000,
          min: 0,
          max: 5000,
          ticks: {
            callback: function(val){ return '₹' + val; }
          },
          grid: { color: 'rgba(0,0,0,0.04)' }
        }
      }
    }
  };

  try {
    new Chart(c, config);
  } catch (err) {
    console.error('Chart init error', err);
    ctx.insertAdjacentHTML('afterend', '<div class="text-danger small mt-2">Chart failed to load.</div>');
  }
});
</script>

</body>
</html>
