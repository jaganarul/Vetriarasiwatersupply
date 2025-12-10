<?php
require_once __DIR__ . '/../init.php';
if (!is_admin_logged_in()) { 
    header('Location: ' . $base_url . '/login.php'); 
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
    @media (max-width: 992px){
      .sidebar { position: fixed; left: -260px; top: 0; height: 100vh; width: 240px; z-index: 1050; transition: left 0.35s ease; }
      .sidebar.open { left: 0; }
      .main { margin-left: 0; }
    }
</style>
</head>

<body>
<nav class="navbar navbar-dark bg-dark mb-3">
    <div class="container d-flex align-items-center">
        <button id="sidebarToggle" class="btn btn-light d-md-none me-2" aria-label="Toggle sidebar"><i class="bi bi-list"></i></button>
        <a class="navbar-brand d-flex align-items-center" href="index.php">
            <img src="<?php echo $base_url; ?>/assets/images/logo.png" class="logo me-2 img-fluid">
            Admin Panel
        </a>
    </div>
</nav>

<div class="container">

  <div class="d-flex justify-content-between align-items-center mb-3">
      <h3>Users</h3>
  </div>

    <div class="table-responsive">
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
                         class="btn btn-sm btn-primary w-100 w-md-auto">View</a>

                     <a href="user_delete.php?id=<?php echo $u['id']; ?>" 
                   onclick="return confirm('Delete this user?');"
                         class="btn btn-sm btn-danger w-100 w-md-auto">Delete</a>

              </td>
          </tr>
      <?php endforeach; ?>
      </tbody>
    </table>
    </div>

</div>

<script>
document.addEventListener('DOMContentLoaded', function(){
    const btn = document.getElementById('sidebarToggle');
    if(!btn) return;
    btn.addEventListener('click', function(){
        const s = document.querySelector('.sidebar');
        if(!s) return;
        s.classList.toggle('open');
    });
});
</script>

</body>
</html>
