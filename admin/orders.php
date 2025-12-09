<?php
require_once __DIR__ . '/../init.php';
if(!is_admin_logged_in()) { header('Location: login.php'); exit; }

// update status
if($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['order_id']) && isset($_POST['status'])){
    // When admin updates status, mark the order as handled (is_new = 0)
    $stmt = $pdo->prepare('UPDATE orders SET status = ?, updated_at = NOW(), is_new = 0 WHERE id = ?');
    $stmt->execute([$_POST['status'], (int)$_POST['order_id']]);
}

// delete order
if($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_order'])){
    $stmt = $pdo->prepare('DELETE FROM orders WHERE id = ?');
    $stmt->execute([(int)$_POST['delete_order']]);
}

// mark new/read
if($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['mark_new'])){
  $stmt = $pdo->prepare('UPDATE orders SET is_new = 1 WHERE id = ?');
  $stmt->execute([(int)$_POST['mark_new']]);
}
if($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['mark_read'])){
  $stmt = $pdo->prepare('UPDATE orders SET is_new = 0 WHERE id = ?');
  $stmt->execute([(int)$_POST['mark_read']]);
}

$sql = '
    SELECT o.*, u.name as customer,
      (SELECT method FROM payments p WHERE p.order_id = o.id ORDER BY p.created_at DESC LIMIT 1) AS pay_method,
      (SELECT status FROM payments p WHERE p.order_id = o.id ORDER BY p.created_at DESC LIMIT 1) AS pay_status
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

<title>Manage Orders</title>

<style>
body {
    background: #f4f6f9;
}
.navbar {
    background: #222 !important;
}
.logo {
    height: 45px;
    margin-right: 10px;
}
.table thead {
    background: #343a40;
    color: #fff;
}
.table tbody tr:hover {
    background: #f1f1f1;
@media (max-width: 992px){
.btn-danger {
    background: #dc3545;

@media (max-width: 992px){
  .sidebar { position: fixed; left: -260px; top: 0; height: 100vh; width: 240px; z-index: 1050; transition: left 0.35s ease; }
  .sidebar.open { left: 0; }
  .main { margin-left: 0; }
}
    border: none;
}
.card-box {
    background: white;
    padding: 20px;
    border-radius: 12px;
    box-shadow: 0 4px 10px rgba(0,0,0,0.1);
}
</style>
</head>

<body>

<!-- NAVBAR -->
<nav class="navbar navbar-dark px-3 mb-4">
  <div class="container-fluid d-flex align-items-center">
    <button id="sidebarToggle" class="btn btn-light d-md-none me-2" aria-label="Toggle sidebar"><i class="bi bi-list"></i></button>
    <img src="<?php echo $base_url; ?>/assets/images/logo.png" class="logo img-fluid">
    <span class="navbar-brand mb-0 h4">Admin – Manage Orders</span>
  </div>
</nav>

<div class="container">

<div class="card-box">

  <h3 class="mb-3">Orders List</h3>

  <div class="table-responsive">
  <table class="table table-hover">
    <thead>
      <tr>
        <th>ID</th>
        <th>Customer</th>
        <th>Total</th>
        <th>Status</th>
        <th>Payment</th>
        <th>Tracking</th>
        <th>Date</th>
        <th>Actions</th>
      </tr>
    </thead>

    <tbody>
    <?php foreach($orders as $o): ?>
        <tr>
          <td>
            <?php echo $o['id']; ?>
            <?php if(isset($o['is_new']) && $o['is_new'] == 1): ?>
              <span class="badge bg-danger ms-2">New</span>
            <?php endif; ?>
          </td>
          <td><?php echo esc($o['customer']); ?></td>
          <td>₹<?php echo number_format((float)($o['total'] ?? 0),2); ?></td>
          <td><span class="badge bg-primary"><?php echo esc($o['status']); ?></span></td>
          <td>
            <?php if(!empty($o['pay_method'])): ?>
              <?php if(strtoupper($o['pay_method']) === 'UPI'): ?>
                <span class="badge bg-success">Paid (UPI)</span>
              <?php else: ?>
                <span class="badge bg-info text-dark"><?php echo esc($o['pay_method']); ?> <?php if(!empty($o['pay_status'])): ?>(<?php echo esc($o['pay_status']); ?>)<?php endif; ?></span>
              <?php endif; ?>
            <?php else: ?>
              <span class="text-muted small">Not recorded</span>
            <?php endif; ?>
          </td>
          <td><?php echo esc($o['tracking_code']); ?></td>
          <td><?php echo esc($o['created_at']); ?></td>

          <td>
            <div class="d-flex flex-column flex-md-row align-items-start gap-1">

              <!-- STATUS UPDATE FORM -->
              <form method="post" class="d-flex flex-column flex-md-row gap-2" style="min-width:160px;">
                <input type="hidden" name="order_id" value="<?php echo $o['id']; ?>">

                <select name="status" class="form-select form-select-sm flex-grow-1">
                  <?php foreach(['Pending','Processing','Shipped','Delivered','Cancelled'] as $s): ?>
                    <option <?php echo $o['status']==$s? 'selected':''; ?>><?php echo $s; ?></option>
                  <?php endforeach; ?>
                </select>

                <button class="btn btn-sm btn-primary w-100 w-md-auto">Update</button>
              </form>

                <div class="d-flex gap-2 flex-column flex-md-row">

                <!-- VIEW INVOICE BUTTON -->
                <a class="btn btn-sm btn-outline-info w-100 w-md-auto" href="<?php echo $base_url; ?>/invoice.php?order=<?php echo $o['id']; ?>" target="_blank">
                  📄 Invoice
                </a>

                <!-- VIEW BUTTON -->
                <a class="btn btn-sm btn-outline-secondary w-100 w-md-auto" href="order_view.php?id=<?php echo $o['id']; ?>">
                  View
                </a>

                <?php if(isset($o['is_new']) && $o['is_new'] == 1): ?>
                  <form method="post" style="display:inline-block;">
                    <input type="hidden" name="mark_read" value="<?php echo $o['id']; ?>">
                    <button class="btn btn-sm btn-outline-success w-100 w-md-auto">Mark Read</button>
                  </form>
                <?php else: ?>
                  <form method="post" style="display:inline-block;">
                    <input type="hidden" name="mark_new" value="<?php echo $o['id']; ?>">
                    <button class="btn btn-sm btn-outline-warning w-100 w-md-auto">Mark New</button>
                  </form>
                <?php endif; ?>

                <!-- DELETE BUTTON -->
                <form method="post" onsubmit="return confirm('Are you sure you want to delete this order?');">
                  <input type="hidden" name="delete_order" value="<?php echo $o['id']; ?>">
                  <button class="btn btn-sm btn-danger w-100 w-md-auto">Delete</button>
                </form>

              </div>
            </div>
          </td>

        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>

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

</div>

</body>
</html>
