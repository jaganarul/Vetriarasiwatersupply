<?php
require_once 'init.php';
$code = trim($_GET['code'] ?? $_POST['code'] ?? '');
$order = null;
if($code){
    $stmt = $pdo->prepare('SELECT * FROM orders WHERE tracking_code = ?');
    $stmt->execute([$code]);
    $order = $stmt->fetch();
}
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" />
<link rel="stylesheet" href="<?php echo $base_url; ?>/assets/css/responsive.css">
  <link rel="stylesheet" href="<?php echo $base_url; ?>/assets/css/custom-clean.css" />
<title>Track Order</title>

<style>
/* Card styling */
.card-custom {
  border-radius: 12px;
  padding: 25px 30px;
  box-shadow: 0 5px 20px rgba(0,0,0,0.08);
  background: #f9fbfd;
  transition: box-shadow 0.3s ease;
}
.card-custom:hover {
  box-shadow: 0 12px 35px rgba(0,0,0,0.12);
}

/* Progress bar container */
.order-progress-bar {
  height: 12px;
  background: #e1e9fb;
  border-radius: 8px;
  overflow: hidden;
  margin-bottom: 30px;
  box-shadow: inset 0 1px 3px rgba(0,0,0,0.1);
}

/* Animated progress fill */
.order-progress-fill {
  height: 100%;
  background: linear-gradient(90deg, #b61515, #4cd964);
  width: 0;
  border-radius: 8px 0 0 8px;
  transition: width 0.7s ease-in-out;
  box-shadow: 0 0 12px #4cd964;
}

/* Timeline styling */
.order-timeline {
  list-style: none;
  padding-left: 0;
  position: relative;
  margin-top: 10px;
  border-left: 3px solid #4cd964;
}

.order-timeline li {
  margin-bottom: 22px;
  padding-left: 30px;
  position: relative;
  font-size: 17px;
  font-weight: 600;
  color: #555;
  cursor: default;
  user-select: none;
  transition: color 0.3s ease;
}
.order-timeline li.order-status {
  color: #218838;
  font-weight: 700;
}
.order-timeline li.order-status::before {
  background-color: #4cd964 !important;
  box-shadow: 0 0 12px #4cd964;
}

.order-timeline li::before {
  content: '';
  width: 20px;
  height: 20px;
  border-radius: 50%;
  position: absolute;
  left: -13px;
  top: 50%;
  transform: translateY(-50%);
  background-color: #d92911;
  box-shadow: inset 0 2px 6px rgba(255,255,255,0.7);
  transition: background-color 0.3s ease, box-shadow 0.3s ease;
}

/* Responsive text and spacing */
@media (max-width: 576px) {
  .card-custom {
    padding: 20px;
  }
  .order-progress-bar {
    margin-bottom: 20px;
  }
}

/* Smooth fade in for timeline */
.fade-in {
  opacity: 0;
  animation: fadeInUp 0.7s forwards;
  animation-delay: 0.3s;
}
@keyframes fadeInUp {
  to {
    opacity: 1;
    transform: translateY(0);
  }
  from {
    opacity: 0;
    transform: translateY(20px);
  }
}
</style>

</head>
<body>
<?php include __DIR__ . '/templates/header.php'; ?>

<div class="container py-5">
  <h3 class="mb-4">Track Your Order</h3>

  <!-- Search box -->
  <form method="get" class="mb-4" role="search" aria-label="Search order tracking">
    <div class="input-group input-group-lg shadow-sm">
      <input name="code" class="form-control" placeholder="Enter tracking code"
             value="<?php echo esc($code); ?>" aria-required="true" aria-label="Enter tracking code" />
      <button class="btn btn-primary" type="submit" aria-label="Track order">Track</button>
    </div>
  </form>

  <?php if($code && !$order): ?>
    <div class="alert alert-warning" role="alert">❗ No order found with this tracking code.</div>
  <?php endif; ?>

  <?php if($order): ?>
  <?php
    // Calculate progress %
    $states = ['Pending','Processing','Shipped','Delivered','Cancelled'];
    $index = array_search($order['status'], $states);
    $progress = ($index !== false && $order['status'] != 'Cancelled')
        ? (($index) / (count($states)-1)) * 100
        : 0;

    // Emojis for each status
    $emoji = [
        'Pending'    => '⏳',
        'Processing' => '🔄',
        'Shipped'    => '🚚',
        'Delivered'  => '✅',
        'Cancelled'  => '❌'
    ];
  ?>

  <div class="card card-custom fade-in" role="region" aria-live="polite" aria-label="Order tracking details">

    <h5>Order #<?php echo $order['id']; ?></h5>
    <p><strong>Tracking Code:</strong> <?php echo esc($order['tracking_code']); ?></p>
    <p>Status: <span class="badge bg-success"><?php echo esc($order['status']); ?></span></p>

    <!-- Progress Bar -->
    <div class="order-progress-bar" role="progressbar" aria-valuemin="0" aria-valuemax="100" aria-valuenow="<?php echo round($progress); ?>">
      <div class="order-progress-fill" style="width: <?php echo $progress; ?>%;"></div>
    </div>

    <!-- Vertical Timeline -->
    <h6 class="mt-3 mb-2">Delivery Timeline</h6>
    <ul class="order-timeline" role="list">
      <?php foreach($states as $s):
        $cls = $s === $order['status'] ? 'order-status' : '';
        $label = $emoji[$s] . " " . esc($s);
      ?>
        <li class="<?php echo $cls; ?>" aria-current="<?php echo ($s === $order['status']) ? 'step' : ''; ?>"><?php echo $label; ?></li>
      <?php endforeach; ?>
    </ul>

  </div>
  <?php endif; ?>
</div>

<?php include __DIR__ . '/templates/footer.php'; ?>
</body>
</html>
