<?php
require_once __DIR__ . '/../init.php';
if (!is_admin_logged_in()) { 
    header('Location: login.php'); 
    exit; 
}

// Fetch all users/customers
$stmt = $pdo->query("SELECT * FROM users ORDER BY created_at DESC");
$users = $stmt->fetchAll();
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

<title>Manage Users</title>

<style>
    .logo {
        height: 40px;
    }
</style>
</head>

<body>
<nav class="navbar navbar-dark bg-dark mb-3">
    <div class="container">
        <a class="navbar-brand d-flex align-items-center" href="index.php">
            <img src="<?php echo $base_url; ?>/assets/images/logo.png" class="logo me-2">
            Admin Panel
        </a>
    </div>
</nav>

<div class="container">

  <div class="d-flex justify-content-between align-items-center mb-3">
      <h3>Users</h3>
  </div>

  <table class="table table-striped table-bordered shadow-sm">
      <thead class="table-dark">
           <tr>
              <th>ID</th>
              <th>Name</th>
              <th>Email</th>
              <th>Phone</th>
              <th>Address</th>
              <th>Date Joined</th>
              <th>Actions</th>
           </tr>
      </thead>
      <tbody>
      <?php foreach ($users as $u): ?>
          <tr>
              <td><?php echo $u['id']; ?></td>
              <td><?php echo esc($u['name']); ?></td>
              <td><?php echo esc($u['email']); ?></td>
              <td><?php echo esc($u['phone']); ?></td>
              <td><?php echo nl2br(esc($u['address'])); ?></td>
              <td><?php echo $u['created_at']; ?></td>
              <td>

                <a href="user_view.php?id=<?php echo $u['id']; ?>" 
                   class="btn btn-sm btn-primary">View</a>

                <a href="user_delete.php?id=<?php echo $u['id']; ?>" 
                   onclick="return confirm('Delete this user?');"
                   class="btn btn-sm btn-danger">Delete</a>

              </td>
          </tr>
      <?php endforeach; ?>
      </tbody>
  </table>

</div>

</body>
</html>
