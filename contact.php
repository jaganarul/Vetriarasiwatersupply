<?php
require_once 'init.php';
$saved = false; $errors = [];
if($_SERVER['REQUEST_METHOD'] === 'POST'){
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $message = trim($_POST['message'] ?? '');
    if(!$name || !filter_var($email, FILTER_VALIDATE_EMAIL) || !$message){
        $errors[] = 'Please provide name, valid email and message.';
    } else {
        // create messages table if necessary
        $pdo->exec("CREATE TABLE IF NOT EXISTS messages (id INT AUTO_INCREMENT PRIMARY KEY, name VARCHAR(200), email VARCHAR(255), message TEXT, created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP) ENGINE=InnoDB;");
        $stmt = $pdo->prepare('INSERT INTO messages (name,email,message) VALUES (?,?,?)');
        $stmt->execute([$name,$email,$message]);
        $saved = true;
    }
}
?>
<!doctype html>
<html><head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="assets/css/custom.css">
<title>Contact - Vetriarasi Water Supply</title></head>
<body>
<?php include __DIR__ . '/templates/header.php'; ?>
<div class="container py-4">
  <h3>Contact Us</h3>
  <div class="row">
    <div class="col-md-6">
      <?php if($saved): ?><div class="alert alert-success">Thank you, your message has been received.</div><?php endif; ?>
      <?php if($errors): ?><div class="alert alert-danger"><?php echo esc(implode('<br>', $errors)); ?></div><?php endif; ?>
      <form method="post">
        <div class="mb-3"><label>Name</label><input class="form-control" name="name" required></div>
        <div class="mb-3"><label>Email</label><input class="form-control" name="email" type="email" required></div>
        <div class="mb-3"><label>Message</label><textarea class="form-control" name="message" rows="5" required></textarea></div>
        <button class="btn btn-primary">Send Message</button>
      </form>
    </div>
    <div class="col-md-6">
      <h6>Our Location</h6>
      <!-- Google Maps embed. Replace the src with your place if desired -->
      <div class="ratio ratio-16x9 mb-3">
        <iframe src="https://www.google.com/maps?q=Jolarpettai+Tirupattur+Tamil+Nadu&output=embed" allowfullscreen loading="lazy"></iframe>
      </div>
      <p class="small text-muted">Phone: +91 98400 00000<br>Email: info@vetriarasiwatersupply.com</p>
    </div>
  </div>
</div>
<?php include __DIR__ . '/templates/footer.php'; ?>
</body></html>
