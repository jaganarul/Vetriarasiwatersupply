<?php
require_once 'init.php';
$errors = [];

if($_SERVER['REQUEST_METHOD'] === 'POST'){
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $pass = $_POST['password'];
    $phone = trim($_POST['phone']); // new mobile field
    $delivery = trim($_POST['delivery']); // delivery address

    // Validation
    if(!$name || !filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($pass) < 6 || !$phone || !$delivery){
        $errors[] = 'Please provide valid name, email, password (min 6 chars), mobile number and delivery address.';
    } else {
        // check if email exists
        $stmt = $pdo->prepare('SELECT id FROM users WHERE email = ?');
        $stmt->execute([$email]);
        if($stmt->fetch()){
            $errors[] = 'Email already registered.';
        } else {
            $hash = password_hash($pass, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare('INSERT INTO users (name,email,phone,password,address) VALUES (?,?,?,?,?)');
            $stmt->execute([$name, $email, $phone, $hash, $delivery]);

            $_SESSION['user_id'] = $pdo->lastInsertId();
            $_SESSION['user_name'] = $name;
            header('Location: ' . $base_url . '/'); exit;
        }
    }
}
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="<?php echo $base_url; ?>/assets/css/custom.css">
<title>Register</title>

<style>
.register-bg {
    min-height: 100vh;
    padding: 40px 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: linear-gradient(135deg,#009ffd,#2a2a72);
}
.register-card {
    background: #ffffff;
    width: 100%;
    max-width: 500px;
    border-radius: 18px;
    padding: 35px;
    box-shadow: 0 8px 35px rgba(0,0,0,0.18);
    animation: fadeIn .7s ease;
}
@keyframes fadeIn {
    from { opacity: 0; transform: translateY(20px); }
    to { opacity: 1; transform: translateY(0); }
}
.register-card input {
    height: 48px;
    border-radius: 10px;
}
.register-card button {
    height: 50px;
    border-radius: 12px;
    font-size: 17px;
    font-weight: 600;
}

/* Mobile Responsive */
@media (max-width: 575.98px) {
    .register-bg { padding: 20px 12px; }
    .register-card { padding: 24px; max-width: 100%; }
    .register-card h2 { font-size: 22px; }
    .register-card input { height: 44px; font-size: 16px; padding: 10px 12px; }
    .register-card button { height: 48px; font-size: 16px; }
    .form-group { margin-bottom: 14px; }
    .form-text { font-size: 12px; }
}

@media (min-width: 576px) and (max-width: 767.98px) {
    .register-card { padding: 28px; max-width: 420px; }
    .register-card h2 { font-size: 24px; }
}
</style>
</head>
<body>

<?php include __DIR__ . '/templates/header.php'; ?>

<div class="register-bg">
  <div class="register-card">

    <div class="text-center mb-4">
      <img src="<?php echo $base_url; ?>/assets/images/logo.png" alt="Logo" style="height: 60px; margin-bottom: 15px;">
      <h3 class="text-center fw-bold mb-2 text-primary">Create Account</h3>
      <p class="text-center text-muted">Register to continue</p>
    </div>

    <?php if($errors): ?>
      <div class="alert alert-danger text-center">
        <?php echo esc(implode('<br>', $errors)); ?>
      </div>
    <?php endif; ?>

    <form method="post">

      <div class="mb-3">
        <label class="form-label fw-semibold">Name</label>
        <input class="form-control" name="name" placeholder="Enter your name" required>
      </div>

      <div class="mb-3">
        <label class="form-label fw-semibold">Email</label>
        <input class="form-control" name="email" type="email" placeholder="example@gmail.com" required>
      </div>

      <div class="mb-3">
        <label class="form-label fw-semibold">Password</label>
        <input class="form-control" name="password" type="password" placeholder="Minimum 6 characters" required>
      </div>

      <div class="mb-3">
        <label class="form-label fw-semibold">Mobile Number</label>
        <input class="form-control" name="phone" type="tel" placeholder="Enter mobile number" required>
      </div>

      <div class="mb-4">
        <label class="form-label fw-semibold">Delivery Address</label>
        <input class="form-control" name="delivery" placeholder="Enter delivery location" required>
      </div>

      <button class="btn btn-primary w-100">Register</button>
    </form>

    <p class="text-center mt-3 small text-muted">
      Already have an account? <a href="<?php echo $base_url; ?>/login.php" class="fw-semibold">Login</a>
    </p>

  </div>
</div>

<?php include __DIR__ . '/templates/footer.php'; ?>
</body>
</html>
