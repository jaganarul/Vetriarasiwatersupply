<?php
require_once __DIR__ . '/../init.php';
if(!is_admin_logged_in()) { header('Location: login.php'); exit; }

// update status
if($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['order_id']) && isset($_POST['status'])){
    $stmt = $pdo->prepare('UPDATE orders SET status = ?, updated_at = NOW() WHERE id = ?');
    $stmt->execute([$_POST['status'], (int)$_POST['order_id']]);
}

// delete order
if($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_order'])){
    $stmt = $pdo->prepare('DELETE FROM orders WHERE id = ?');
    $stmt->execute([(int)$_POST['delete_order']]);
}

// fetch orders sorted by date (newest first)
$stmt = $pdo->query('
    SELECT o.*, u.name as customer 
    FROM orders o 
    JOIN users u ON o.user_id=u.id 
    ORDER BY o.created_at DESC
');
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
}
.btn-danger {
    background: #dc3545;
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
    <img src="../assets/images/logo.png" class="logo">
    <span class="navbar-brand mb-0 h4">Admin – Manage Orders</span>
  </div>
</nav>

<div class="container">

<div class="card-box">

  <h3 class="mb-3">Orders List</h3>

  <table class="table table-hover">
    <thead>
      <tr>
        <th>ID</th>
        <th>Customer</th>
        <th>Total</th>
        <th>Status</th>
        <th>Tracking</th>
        <th>Date</th>
        <th>Actions</th>
      </tr>
    </thead>

    <tbody>
      <?php foreach($orders as $o): ?>
        <tr>
          <td><?php echo $o['id']; ?></td>
          <td><?php echo esc($o['customer']); ?></td>
          <td>₹<?php echo number_format($o['total'],2); ?></td>
          <td><span class="badge bg-primary"><?php echo esc($o['status']); ?></span></td>
          <td><?php echo esc($o['tracking_code']); ?></td>
          <td><?php echo esc($o['created_at']); ?></td>

          <td>
            <div class="d-flex flex-column gap-1">

              <!-- STATUS UPDATE FORM -->
              <form method="post" class="d-flex gap-2">
                <input type="hidden" name="order_id" value="<?php echo $o['id']; ?>">

                <select name="status" class="form-select form-select-sm">
                  <?php foreach(['Pending','Processing','Shipped','Delivered','Cancelled'] as $s): ?>
                    <option <?php echo $o['status']==$s? 'selected':''; ?>><?php echo $s; ?></option>
                  <?php endforeach; ?>
                </select>

                <button class="btn btn-sm btn-primary">Update</button>
              </form>

              <div class="d-flex gap-2">

                <!-- VIEW BUTTON -->
                <a class="btn btn-sm btn-outline-secondary" href="order_view.php?id=<?php echo $o['id']; ?>">
                  View
                </a>

                <!-- DELETE BUTTON -->
                <form method="post" onsubmit="return confirm('Are you sure you want to delete this order?');">
                  <input type="hidden" name="delete_order" value="<?php echo $o['id']; ?>">
                  <button class="btn btn-sm btn-danger">Delete</button>
                </form>

              </div>
            </div>
          </td>

        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>

</div>

</div>

</body>
</html>
