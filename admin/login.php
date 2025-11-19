<?php
session_start();

// Default credentials (hidden from UI)
$default_email = 'admin@Vetriarasiwatersupply.com';
$default_password = 'Admin@104';

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $pass = trim($_POST['password']);

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
  <style>
    .login-box {
      max-width: 420px;
      margin: auto;
      margin-top: 80px;
      padding: 30px;
      border-radius: 10px;
      box-shadow: 0 0 15px rgba(0,0,0,0.1);
      background: #fff;
      background : #77c0edff;
    }
    .logo {
      width: 120px;
      display: block;
      margin: auto;
      margin-bottom: 15px;
    }
  </style>
</head>
<body class="bg-light">

<div class="login-box">

  <!-- LOGO HERE -->
  <img src="/Vetriarasiwatersupply/assets/images/logo.png" class="logo" alt="Logo">

  <h4 class="text-center mb-3">Admin Login</h4>

  <?php if ($errors): ?>
    <div class="alert alert-danger"><?php echo implode('<br>', $errors); ?></div>
  <?php endif; ?>

  <form method="post">
    <div class="mb-3">
      <label class="form-label">Email</label>
      <input class="form-control" name="email" placeholder="Enter email" required>
    </div>

    <div class="mb-3">
      <label class="form-label">Password</label>

      <div class="input-group">
        <input class="form-control" name="password" type="password" id="passwordField" placeholder="Enter password" required>
        <button class="btn btn-outline-secondary" type="button" onclick="togglePassword()">Show</button>
      </div>

    </div>

    <button class="btn btn-primary w-100">Login</button>
  </form>

</div>

<script>
function togglePassword() {
  const field = document.getElementById('passwordField');
  if (field.type === "password") {
      field.type = "text";
  } else {
      field.type = "password";
  }
}
</script>

</body>
</html>
