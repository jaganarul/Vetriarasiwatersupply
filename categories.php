<?php
require_once 'init.php';
// fetch distinct categories
$stmt = $pdo->query('SELECT DISTINCT category FROM products WHERE category IS NOT NULL AND category <> ""');
$cats = $stmt->fetchAll();
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="assets/css/custom.css">
  <title>Categories</title>
</head>
<body>
<?php include __DIR__ . '/templates/header.php'; ?>
  <h3>Categories</h3>
  <div class="list-group mb-3">
    <?php foreach($cats as $c): $cat = $c['category']; ?>
      <a class="list-group-item list-group-item-action" href="index.php?category=<?php echo urlencode($cat); ?>"><?php echo esc($cat); ?></a>
    <?php endforeach; ?>
  </div>
<?php include __DIR__ . '/templates/footer.php'; ?>
</body>
</html>
