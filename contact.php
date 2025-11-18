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
<html>
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Contact - Vetriarasiwatersupply</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">

<style>
  body { background: #f4f7fb; font-family: "Poppins", sans-serif; }

  .contact-banner {
    background: linear-gradient(135deg, #0A84FF 0%, #074a74 100%);
    padding: 45px;
    color: white;
    border-radius: 18px;
    box-shadow: 0 10px 25px rgba(0,0,0,0.15);
    margin-bottom: 35px;
  }

  .contact-box {
    background: white;
    padding: 25px;
    border-radius: 16px;
    box-shadow: 0 6px 18px rgba(0,0,0,0.08);
  }

  .info-box {
    background: #ffffff;
    padding: 20px;
    border-radius: 14px;
    margin-bottom: 20px;
    box-shadow: 0 5px 15px rgba(0,0,0,0.08);
  }

  .info-box i {
    font-size: 1.8rem;
    color: #0A84FF;
    margin-right: 12px;
  }

  label { font-weight: 600; }
</style>
</head>

<body>
<?php include __DIR__ . '/templates/header.php'; ?>

<div class="container py-4">

  <!-- BEAUTIFUL HEADER -->
  <div class="contact-banner">
    <h2 class="fw-bold">Get in Touch</h2>
    <p class="mb-0">Have a question? Need quick delivery? We're always ready to assist you.</p>
  </div>

  <div class="row g-4">

    <!-- FORM -->
    <div class="col-md-6">
      <div class="contact-box">

        <?php if($saved): ?>
          <div class="alert alert-success">
            <i class="bi bi-check-circle-fill"></i> Thank you, your message has been received.
          </div>
        <?php endif; ?>

        <?php if($errors): ?>
          <div class="alert alert-danger">
            <i class="bi bi-exclamation-triangle-fill"></i> 
            <?php echo esc(implode('<br>', $errors)); ?>
          </div>
        <?php endif; ?>

        <form method="post">
          <div class="mb-3">
            <label><i class="bi bi-person-fill"></i> Name</label>
            <input class="form-control" name="name" required>
          </div>

          <div class="mb-3">
            <label><i class="bi bi-envelope-fill"></i> Email</label>
            <input class="form-control" name="email" type="email" required>
          </div>

          <div class="mb-3">
            <label><i class="bi bi-chat-left-dots-fill"></i> Message</label>
            <textarea class="form-control" name="message" rows="5" required></textarea>
          </div>

          <button class="btn btn-primary w-100 py-2 fw-bold">
            <i class="bi bi-send-fill"></i> Send Message
          </button>
        </form>

      </div>
    </div>

    <!-- CONTACT INFO + MAP -->
    <div class="col-md-6">

      <!-- INFO BOXES -->
      <div class="info-box d-flex align-items-center">
        <i class="bi bi-telephone-fill"></i>
        <div>
          <strong>Phone</strong><br>
          +91 9360658623
        </div>
      </div>

      <div class="info-box d-flex align-items-center">
        <i class="bi bi-envelope-open-fill"></i>
        <div>
          <strong>Email</strong><br>
          vetriarasiwatersupply@gmail.com
        </div>
      </div>

      <div class="info-box d-flex align-items-center">
        <i class="bi bi-geo-alt-fill"></i>
        <div>
          <strong>Location</strong><br>
          Jolarpettai, Tirupattur, Tamil Nadu
        </div>
      </div>

      <h6 class="mt-4 fw-bold">Find Us on Map</h6>
      <div class="ratio ratio-16x9 mb-3">
        <iframe src="https://www.google.com/maps?q=Jolarpettai+Tirupattai+Tamil+Nadu&output=embed"
          allowfullscreen loading="lazy"></iframe>
      </div>

    </div>
  </div>
</div>

<?php include __DIR__ . '/templates/footer.php'; ?>
</body>
</html>
