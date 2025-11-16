<?php
require_once 'init.php';
$errors = [];
if($_SERVER['REQUEST_METHOD'] === 'POST'){
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $pass = $_POST['password'];
    if(!$name || !filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($pass) < 6){
        $errors[] = 'Please provide valid name, email and password (min 6 chars).';
    } else {
        // check exists
        $stmt = $pdo->prepare('SELECT id FROM users WHERE email = ?');
        $stmt->execute([$email]);
        if($stmt->fetch()){
            $errors[] = 'Email already registered.';
        } else {
            $hash = password_hash($pass, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare('INSERT INTO users (name,email,password) VALUES (?,?,?)');
            $stmt->execute([$name,$email,$hash]);
            $_SESSION['user_id'] = $pdo->lastInsertId();
            $_SESSION['user_name'] = $name;
            header('Location: index.php'); exit;
        }
    }
}
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="assets/css/custom.css">
  <title>Register</title>
</head>
<body>
<?php include __DIR__ . '/templates/header.php'; ?>
<div class="container py-4">
  <h3>Register</h3>
  <?php if($errors): ?>
    <div class="alert alert-danger"><?php echo esc(implode('<br>', $errors)); ?></div>
  <?php endif; ?>
  <form method="post">
    <div class="mb-3"><label>Name</label><input class="form-control" name="name"></div>
    <div class="mb-3"><label>Email</label><input class="form-control" name="email" type="email"></div>
    <div class="mb-3"><label>Password</label><input class="form-control" name="password" type="password"></div>
    <button class="btn btn-primary">Register</button>
  </form>
</div>
<?php include __DIR__ . '/templates/footer.php'; ?>
</body>
</html>