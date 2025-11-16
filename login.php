<?php
require_once 'init.php';
$errors = [];
if($_SERVER['REQUEST_METHOD'] === 'POST'){
    $email = trim($_POST['email']);
    $pass = $_POST['password'];
    $stmt = $pdo->prepare('SELECT id,name,password FROM users WHERE email = ?');
    $stmt->execute([$email]);
    $u = $stmt->fetch();
    if($u && password_verify($pass, $u['password'])){
        $_SESSION['user_id'] = $u['id'];
        $_SESSION['user_name'] = $u['name'];
        header('Location: index.php'); exit;
    } else {
        $errors[] = 'Invalid credentials.';
    }
}
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="assets/css/custom.css">
  <title>Login</title>
</head>
<body>
<?php include __DIR__ . '/templates/header.php'; ?>
<div class="container py-4">
  <h3>Login</h3>
  <?php if($errors): ?><div class="alert alert-danger"><?php echo esc(implode('<br>', $errors)); ?></div><?php endif; ?>
  <form method="post">
    <div class="mb-3"><label>Email</label><input class="form-control" name="email" type="email"></div>
    <div class="mb-3"><label>Password</label><input class="form-control" name="password" type="password"></div>
    <button class="btn btn-primary">Login</button>
  </form>
</div>
<?php include __DIR__ . '/templates/footer.php'; ?>
</body>
</html>