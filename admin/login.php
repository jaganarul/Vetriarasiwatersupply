<?php
session_start();

// Default credentials
$default_email = 'admin@kavis.com';
$default_password = 'Admin@123';

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $pass = trim($_POST['password']);

    // Check against default credentials
    if ($email === $default_email && $pass === $default_password) {
        $_SESSION['admin_id'] = 1;
        $_SESSION['admin_name'] = 'Site Admin';
        header('Location: index.php');
        exit;
    } else {
        $errors[] = 'Invalid credentials.';
    }
}
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <title>Admin Login</title>
</head>
<body>
<div class="container py-4">
  <h3 class="mb-4">Admin Login</h3>

  <?php if ($errors): ?>
    <div class="alert alert-danger"><?php echo implode('<br>', $errors); ?></div>
  <?php endif; ?>

  <form method="post">
    <div class="mb-3">
      <label class="form-label">Email</label>
      <input class="form-control" name="email" value="admin@kavis.com">
    </div>
    <div class="mb-3">
      <label class="form-label">Password</label>
      <input class="form-control" name="password" type="password" value="Admin@123">
    </div>
    <button class="btn btn-primary w-100">Login</button>
  </form>
</div>
</body>
</html>
