<?php
require_once __DIR__ . '/../init.php';
if(!is_admin_logged_in()) { header('Location: login.php'); exit; }
// update status
if($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['order_id']) && isset($_POST['status'])){
    $stmt = $pdo->prepare('UPDATE orders SET status = ?, updated_at = NOW() WHERE id = ?');
    $stmt->execute([$_POST['status'], (int)$_POST['order_id']]);
}
// fetch orders
$stmt = $pdo->query('SELECT o.*, u.name as customer FROM orders o JOIN users u ON o.user_id=u.id ORDER BY o.created_at DESC');
$orders = $stmt->fetchAll();
?>
<!doctype html>
<html><head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<title>Manage Orders</title></head>
<body>
<nav class="navbar navbar-dark bg-dark mb-3"><div class="container"><a class="navbar-brand" href="index.php">Admin</a></div></nav>
<div class="container">
  <h3>Orders</h3>
  <table class="table">
    <thead><tr><th>ID</th><th>Customer</th><th>Total</th><th>Status</th><th>Tracking</th><th>Date</th><th></th></tr></thead>
    <tbody>
      <?php foreach($orders as $o): ?>
        <tr>
          <td><?php echo $o['id']; ?></td>
          <td><?php echo esc($o['customer']); ?></td>
          <td>$<?php echo number_format($o['total'],2); ?></td>
          <td><?php echo esc($o['status']); ?></td>
          <td><?php echo esc($o['tracking_code']); ?></td>
          <td><?php echo esc($o['created_at']); ?></td>
          <td>
            <form method="post" class="d-flex gap-2">
              <input type="hidden" name="order_id" value="<?php echo $o['id']; ?>">
              <select name="status" class="form-select form-select-sm">
                <?php foreach(['Pending','Processing','Shipped','Delivered','Cancelled'] as $s): ?>
                  <option <?php echo $o['status']==$s? 'selected':''; ?>><?php echo $s; ?></option>
                <?php endforeach; ?>
              </select>
              <button class="btn btn-sm btn-primary">Update</button>
            </form>
            <a class="btn btn-sm btn-outline-secondary mt-1" href="order_view.php?id=<?php echo $o['id']; ?>">View</a>
          </td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>
</body></html>