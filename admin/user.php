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
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">

<title>Manage Customers</title>

<style>
    :root {
      --bg: #f4f6f9;
      --accent: #0b74ff;
    }
    
    body {
        background: var(--bg);
        font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
    }

    .sidebar {
        width: 230px;
        height: 100vh;
        background: #222;
        color: white;
        position: fixed;
        padding-top: 20px;
        overflow-y: auto;
        z-index: 1000;
    }
    .sidebar a {
        color: #ddd;
        display: block;
        padding: 12px 20px;
        text-decoration: none;
        font-size: 15px;
        transition: 0.2s;
    }
    .sidebar a:hover {
        background: #444;
    }
    .logo-img {
        width: 150px;
        margin-bottom: 20px;
    }

    .main {
        margin-left: 240px;
        min-height: 100vh;
    }

    .logo {
        height: 40px;
    }
    
    .navbar {
        background: linear-gradient(90deg, #0b74ff, #00d4ff);
    }
    
    table {
        background: white;
        border-radius: 8px;
    }
    
    table thead th {
        background: #f8f9fa;
        color: var(--accent);
        font-weight: 700;
        border: none;
    }
    
    .btn-primary {
        background: linear-gradient(90deg, var(--accent), #00d4ff);
        border: none;
    }
    
    .btn-danger {
        background: #dc3545;
        border: none;
    }
    
    @media (max-width: 992px){
      .sidebar { position: fixed; left: -260px; top: 0; height: 100vh; width: 240px; z-index: 1050; transition: left 0.35s ease; }
      .sidebar.open { left: 0; }
      .main { margin-left: 0; }
    }
</style>
</head>

<body>

<!-- SIDEBAR -->
<div class="sidebar text-center">
  <img src="<?php echo $base_url; ?>/assets/images/logo.png" class="logo-img img-fluid" alt="Logo">
  <h5 class="text-white mb-4">Admin Panel</h5>
  <a href="index.php">📊 Dashboard</a>
  <a href="products.php">📦 Products</a>
  <a href="orders.php">🛒 Orders</a>
  <a href="invoices.php">📄 Invoices</a>
  <a href="user.php" style="background: #444; font-weight: bold;">👥 Customers</a>
  <a href="messages.php">💬 Messages</a>
  <a href="logout.php">🚪 Logout</a>
</div>

<!-- MAIN CONTENT -->
<div class="main">
<nav class="navbar navbar-dark px-3">
  <div class="container-fluid d-flex align-items-center">
    <button id="sidebarToggle" class="btn btn-light d-md-none me-2" aria-label="Toggle sidebar">
      <i class="bi bi-list"></i>
    </button>
    <div class="d-flex align-items-center">
      <img src="<?php echo $base_url; ?>/assets/images/logo.png" class="logo me-2 img-fluid">
      <span class="navbar-brand mb-0">Manage Customers</span>
    </div>
    <div class="ms-auto">
      <span class="text-white me-3">Admin</span>
      <a href="logout.php" class="btn btn-sm btn-outline-light">Logout</a>
    </div>
  </div>
</nav>

<div class="container-fluid px-4 py-4">

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
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function(){
  const sidebarToggle = document.getElementById('sidebarToggle');
  const sidebar = document.querySelector('.sidebar');
  
  if(sidebarToggle && sidebar) {
    sidebarToggle.addEventListener('click', function(){
      sidebar.classList.toggle('open');
    });
  }
  
  const sidebarLinks = document.querySelectorAll('.sidebar a');
  sidebarLinks.forEach(link => {
    link.addEventListener('click', function(){
      if(window.innerWidth <= 992) {
        sidebar.classList.remove('open');
      }
    });
  });
});
</script>

</body>
</html>
