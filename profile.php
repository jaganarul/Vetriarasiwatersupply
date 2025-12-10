<?php
require_once 'init.php';
if(!is_logged_in()) { header('Location: ' . $base_url . '/login.php'); exit; }
$stmt = $pdo->prepare('SELECT * FROM orders WHERE user_id = ? ORDER BY created_at DESC');
$stmt->execute([$_SESSION['user_id']]);
$orders = $stmt->fetchAll();
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
  <link rel="stylesheet" href="<?php echo $base_url; ?>/assets/css/custom.css">
  <title>Profile</title>
  <style>
    .profile-container { max-width: 1000px; margin: 0 auto; }
    .profile-header { background: linear-gradient(135deg, #0b74ff 0%, #00d4ff 100%); color: white; padding: 24px; border-radius: 12px; margin-bottom: 24px; }
    .profile-header h3 { margin-bottom: 8px; font-weight: 700; }
    .profile-header p { margin-bottom: 0; opacity: 0.95; }
    .order-card { border-radius: 10px; border: 1px solid #e0e0e0; overflow: hidden; margin-bottom: 16px; transition: all 0.3s ease; }
    .order-card:hover { box-shadow: 0 8px 24px rgba(0,0,0,0.1); }
    .order-card-header { background: #f9f9f9; padding: 16px; border-bottom: 1px solid #e0e0e0; }
    .order-card-body { padding: 16px; }
    .order-status { display: inline-block; padding: 6px 12px; border-radius: 6px; font-size: 12px; font-weight: 600; }
    .order-status.pending { background: #fff3cd; color: #856404; }
    .order-status.delivered { background: #d4edda; color: #155724; }
    .order-status.cancelled { background: #f8d7da; color: #721c24; }
    .order-detail-row { display: flex; justify-content: space-between; padding: 8px 0; border-bottom: 1px solid #f0f0f0; }
    .order-detail-row:last-child { border-bottom: none; }
    .order-detail-label { font-weight: 600; color: #666; }
    .order-detail-value { color: #333; }
    .action-button { display: inline-block; padding: 8px 16px; background: linear-gradient(90deg, #0b74ff, #00d4ff); color: white; text-decoration: none; border-radius: 6px; font-size: 13px; font-weight: 600; transition: all 0.3s; }
    .action-button:hover { transform: translateY(-2px); box-shadow: 0 6px 16px rgba(11, 116, 255, 0.3); color: white; }
    
    @media (max-width: 575.98px) {
      .profile-container { padding: 12px; }
      .profile-header { padding: 16px; margin-bottom: 16px; }
      .profile-header h3 { font-size: 20px; }
      .order-card { margin-bottom: 12px; }
      .order-card-header { padding: 12px; }
      .order-card-body { padding: 12px; }
      .order-detail-row { font-size: 13px; padding: 6px 0; }
      .action-button { display: block; width: 100%; text-align: center; margin-top: 8px; }
    }
    
    @media (min-width: 576px) and (max-width: 767.98px) {
      .profile-header { padding: 20px; }
      .order-card { margin-bottom: 14px; }
    }
  </style>
</head>
<body>

<?php include __DIR__ . '/templates/header.php'; ?>

<div class="profile-container py-4">
  <!-- Profile Header -->
  <div class="profile-header">
    <div class="d-flex align-items-center gap-3 mb-2">
      <i class="bi bi-person-circle" style="font-size: 2.5rem;"></i>
      <div>
        <h3 class="mb-0"><?php echo esc($_SESSION['user_name']); ?></h3>
        <p class="mb-0" style="font-size: 14px;">Account Overview</p>
      </div>
    </div>
  </div>

  <!-- Orders Section -->
  <div>
    <div class="mb-3">
      <h5 style="font-weight: 700; color: #1a1a1a;">
        <i class="bi bi-box-seam"></i> Your Orders
      </h5>
    </div>

    <?php if(!$orders): ?>
      <div class="alert alert-info" role="alert">
        <i class="bi bi-info-circle"></i> You haven't placed any orders yet.
        <a href="<?php echo $base_url; ?>/" class="alert-link">Start shopping</a>
      </div>
    <?php else: ?>
      <?php foreach($orders as $o): ?>
        <div class="order-card">
          <div class="order-card-header d-flex justify-content-between align-items-center">
            <div>
              <div style="font-weight: 600; color: #333;">Order #<?php echo htmlspecialchars($o['id']); ?></div>
              <div style="font-size: 13px; color: #999; margin-top: 4px;"><?php echo date('d M Y', strtotime($o['created_at'])); ?></div>
            </div>
            <span class="order-status <?php echo strtolower($o['status']); ?>">
              <?php echo esc($o['status']); ?>
            </span>
          </div>
          
          <div class="order-card-body">
            <div class="order-detail-row">
              <span class="order-detail-label">Amount:</span>
              <span class="order-detail-value fw-bold" style="color: #0b74ff;">₹<?php echo number_format((float)($o['total'] ?? 0), 2); ?></span>
            </div>
            <div class="order-detail-row">
              <span class="order-detail-label">Tracking Code:</span>
              <span class="order-detail-value"><?php echo esc($o['tracking_code']); ?></span>
            </div>
            <div class="order-detail-row">
              <span class="order-detail-label">Delivery Address:</span>
              <span class="order-detail-value" style="text-align: right; max-width: 60%;"><?php echo esc(substr($o['delivery_address'], 0, 50)); ?></span>
            </div>
            
            <div style="margin-top: 12px; display: flex; gap: 8px; flex-wrap: wrap;">
              <a href="<?php echo $base_url; ?>/track.php?code=<?php echo esc($o['tracking_code']); ?>" class="action-button" style="background: #f0f0f0; color: #0b74ff;">
                <i class="bi bi-pin-map"></i> Track
              </a>
              <a href="<?php echo $base_url; ?>/invoice.php?order=<?php echo $o['id']; ?>" class="action-button">
                <i class="bi bi-file-pdf"></i> Invoice
              </a>
            </div>
          </div>
        </div>
      <?php endforeach; ?>
    <?php endif; ?>
  </div>
</div>

<?php include __DIR__ . '/templates/footer.php'; ?>
</body></html>