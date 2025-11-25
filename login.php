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
        // If login was triggered due to needing authentication for a specific page
        // use the `return_to` session key. Otherwise redirect to the home page.
        $redirect = $base_url . '/';
        if (!empty($_SESSION['return_to'])) {
            $redirect = $base_url . '/' . ltrim($_SESSION['return_to'], '/');
            unset($_SESSION['return_to']);
        }
        header('Location: ' . $redirect);
        exit;
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
<link rel="stylesheet" href="<?php echo $base_url; ?>/assets/css/custom.css">
<title>Login</title>

<style>
/* Page background */
.login-bg {
    background: linear-gradient(135deg,#007bff 0%, #00b4d8 100%);
    min-height: 100vh;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 30px 10px;
}

/* Login card */
.login-card {
    background: #ffffff;
    width: 100%;
    max-width: 420px;
    border-radius: 18px;
    box-shadow: 0 8px 30px rgba(0,0,0,0.15);
    padding: 35px;
    animation: fadein .7s ease;
}

/* Fade in animation */
@keyframes fadein {
    from { opacity: 0; transform: translateY(20px); }
    to { opacity: 1; transform: translateY(0); }
}

/* Input styling */
.login-card input {
    height: 48px;
    border-radius: 10px;
}

/* Button styling */
.login-card button {
    height: 50px;
    border-radius: 12px;
    font-size: 17px;
    font-weight: 600;
}

/* Title */
.login-title {
    font-size: 28px;
    font-weight: 700;
}

/* Icon */
.login-icon {
    font-size: 42px;
    color: #007bff;
    margin-bottom: 10px;
}
</style>

</head>
<body>

<?php include __DIR__ . '/templates/header.php'; ?>

<div class="login-bg">

    <div class="login-card">

        <div class="text-center mb-3">
            <div class="login-icon">🔐</div>
            <div class="login-title">Welcome Back</div>
            <p class="text-muted">Login to continue</p>
        </div>

        <?php if($errors): ?>
            <div class="alert alert-danger text-center">
                <?php echo esc(implode('<br>', $errors)); ?>
            </div>
        <?php endif; ?>

        <form method="post">
            <div class="mb-3">
                <label class="form-label">Email</label>
                <input class="form-control" name="email" type="email" placeholder="Enter your email">
            </div>

            <div class="mb-3">
                <label class="form-label">Password</label>
                <input class="form-control" name="password" type="password" placeholder="Enter password">
            </div>

            <button class="btn btn-primary w-100 mt-2">Login</button>
        </form>

        <p class="text-center mt-3 small text-muted">
            Don’t have an account? <a href="<?php echo $base_url; ?>/register">Register</a>
        </p>

    </div>

</div>

<?php include __DIR__ . '/templates/footer.php'; ?>
</body>
</html>
