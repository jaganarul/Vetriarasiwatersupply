<?php
require_once __DIR__ . '/../init.php';
if (!is_admin_logged_in()) { header('Location: login.php'); exit; }

// Ensure messages table exists and has an is_read column
$hasMessages = false;
try{
  $hasMessages = (bool)$pdo->query("SHOW TABLES LIKE 'messages'")->fetch();
} catch(Exception $e){ $hasMessages = false; }

if(!$hasMessages){
  // create messages table if it doesn't exist
  $pdo->exec("CREATE TABLE IF NOT EXISTS messages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(200),
    email VARCHAR(255),
    message TEXT,
    is_read TINYINT(1) NOT NULL DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");
  $hasMessages = true;
}

// ensure is_read column exists when table is present
if($hasMessages){
  $col = $pdo->query("SHOW COLUMNS FROM messages LIKE 'is_read'")->fetch();
  if(!$col){
    $pdo->exec("ALTER TABLE messages ADD COLUMN is_read TINYINT(1) NOT NULL DEFAULT 0");
  }
}
// POST actions: mark read/unread or delete
if($_SERVER['REQUEST_METHOD'] === 'POST'){
    if(isset($_POST['mark_read'])){
        $stmt = $pdo->prepare('UPDATE messages SET is_read = 1 WHERE id = ?');
        $stmt->execute([(int)$_POST['mark_read']]);
    }
    if(isset($_POST['mark_unread'])){
        $stmt = $pdo->prepare('UPDATE messages SET is_read = 0 WHERE id = ?');
        $stmt->execute([(int)$_POST['mark_unread']]);
    }
    if(isset($_POST['delete_msg'])){
        $stmt = $pdo->prepare('DELETE FROM messages WHERE id = ?');
        $stmt->execute([(int)$_POST['delete_msg']]);
    }
    header('Location: messages.php'); exit;
}

// fetch messages safely
$messages = [];
if($hasMessages){
  $stmt = $pdo->query('SELECT * FROM messages ORDER BY created_at DESC');
  $messages = $stmt->fetchAll();
}
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<title>Messages</title>
<style>
  .logo { height: 40px; }
  .message-card { background: #fff; border-radius: 12px; box-shadow:0 6px 18px rgba(0,0,0,0.08); padding:18px; }

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
      <img src="<?php echo $base_url; ?>/assets/images/logo.png" class="logo me-2"> Admin Panel
    </a>
</div>
<script>
document.addEventListener('DOMContentLoaded', function(){
  const btn = document.getElementById('sidebarToggle');
  if(!btn) return;
  btn.addEventListener('click', function(){
    const s = document.querySelector('.sidebar');
    if(!s) return;
    const overlayId = 'adminSidebarOverlay';
    if(s.classList.contains('open')){
      s.classList.remove('open');
      const ov = document.getElementById(overlayId);
      if(ov) ov.remove();
      document.body.style.overflow = '';
    } else {
      s.classList.add('open');
      const ov = document.createElement('div');
      ov.id = overlayId;
      ov.style.position = 'fixed';
      ov.style.top = '0';
      ov.style.left = '0';
      ov.style.right = '0';
      ov.style.bottom = '0';
      ov.style.background = 'rgba(0,0,0,0.2)';
      ov.style.zIndex = '1040';
      ov.addEventListener('click', function(){ s.classList.remove('open'); ov.remove(); document.body.style.overflow = ''; });
      document.body.appendChild(ov);
      document.body.style.overflow = 'hidden';
    }
  });
});
</script>
</nav>

<div class="container">
  <h3>Messages</h3>
  <div class="row g-3">
    <?php foreach($messages as $m): ?>
      <div class="col-md-6">
        <div class="message-card">
          <div class="d-flex justify-content-between align-items-center mb-2">
            <div>
              <strong><?php echo esc($m['name']); ?></strong> <span class="text-muted">&lt;<?php echo esc($m['email']); ?>&gt;</span>
            </div>
            <div>
              <?php if($m['is_read']): ?>
                <span class="badge bg-success">Read</span>
              <?php else: ?>
                <span class="badge bg-warning">New</span>
              <?php endif; ?>
            </div>
          </div>
          <p class="small mb-2"><?php echo nl2br(esc($m['message'])); ?></p>
          <p class="small text-muted mb-2"><?php echo $m['created_at']; ?></p>
          <div class="d-flex gap-2">
            <?php if(!$m['is_read']): ?>
            <form method="post"><input type="hidden" name="mark_read" value="<?php echo $m['id']; ?>"><button class="btn btn-sm btn-outline-primary">Mark Read</button></form>
            <?php else: ?>
            <form method="post"><input type="hidden" name="mark_unread" value="<?php echo $m['id']; ?>"><button class="btn btn-sm btn-outline-secondary">Mark Unread</button></form>
            <?php endif; ?>

            <form method="post" onsubmit="return confirm('Delete message?');"><input type="hidden" name="delete_msg" value="<?php echo $m['id']; ?>"><button class="btn btn-sm btn-danger">Delete</button></form>
          </div>
        </div>
      </div>
    <?php endforeach; ?>
  </div>
</div>
</body>
</html>
