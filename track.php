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
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="assets/css/custom.css">
<title>Track Order</title>

<style>
/* Timeline styling */
.order-timeline {
    list-style: none;
    padding-left: 0;
    position: relative;
    margin-top: 20px;
}

.order-timeline li {
    margin-bottom: 18px;
    padding-left: 28px;
    position: relative;
    font-size: 16px;
    font-weight: 500;
}

.order-timeline li::before {
    content: '';
    width: 14px;
    height: 14px;
    border-radius: 50%;
    position: absolute;
    left: 0;
    top: 3px;
    background-color: #d92911ff;
}

.order-status::before {
    background-color: #08b605ff !important;
    box-shadow: 0 0 8px #17b012ff;
}

.order-progress-bar {
    height: 8px;
    background: #9bb7d4ff;
    border-radius: 10px;
    overflow: hidden;
    margin-bottom: 20px;
}

.order-progress-fill {
    height: 100%;
    background: linear-gradient(90deg, #b61515ff, #4cd964);
    transition: width .5s;
}

.card-custom {
    border-radius: 12px;
    padding: 20px;
    box-shadow: 0 4px 15px rgba(0,0,0,0.08);
}
</style>

</head>
<body>
<?php include __DIR__ . '/templates/header.php'; ?>

<div class="container py-5">

    <h3 class="mb-4">Track Your Order</h3>

    <!-- Search box -->
    <form method="get" class="mb-4">
        <div class="input-group input-group-lg">
            <input name="code" class="form-control" placeholder="Enter tracking code"
                   value="<?php echo esc($code); ?>">
            <button class="btn btn-primary">Track</button>
        </div>
    </form>

    <?php if($code && !$order): ?>
        <div class="alert alert-warning">❗ No order found with this tracking code.</div>
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

    <div class="card card-custom">

        <h5>Order #<?php echo $order['id']; ?></h5>
        <p class="mb-1">Tracking Code: <strong><?php echo esc($order['tracking_code']); ?></strong></p>
        <p>Status: <span class="badge bg-success"><?php echo esc($order['status']); ?></span></p>

        <!-- Progress Bar -->
        <div class="order-progress-bar">
            <div class="order-progress-fill" style="width: <?php echo $progress; ?>%;"></div>
        </div>

        <!-- Beautiful Timeline -->
        <h6 class="mt-3 mb-2">Delivery Timeline</h6>
        <ul class="order-timeline">
            <?php
            foreach($states as $s){
                $cls = $s === $order['status'] ? 'text-success order-status' : 'text-muted';
                $label = $emoji[$s] . " " . esc($s);
                echo "<li class='$cls'>$label</li>";
            }
            ?>
        </ul>

    </div>

    <?php endif; ?>

</div>

<?php include __DIR__ . '/templates/footer.php'; ?>
</body>
</html>
