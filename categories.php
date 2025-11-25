<?php
require_once __DIR__ . '/init.php';

// fetch distinct categories
$stmt = $pdo->query("SELECT DISTINCT category FROM products WHERE category IS NOT NULL AND category <> ''");
$cats = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="<?php echo $base_url; ?>/assets/css/custom.css">
  <title>Categories</title>
</head>
<body>

<?php include __DIR__ . '/templates/header.php'; ?>

<div class="container py-4">
  <h3 class="mb-3">Categories</h3>

  <?php if (empty($cats)): ?>
      <div class="alert alert-warning">No categories found.</div>
  <?php else: ?>
      <div class="list-group mb-4">
      <?php foreach ($cats as $c): ?>
          <?php $cat = $c['category']; ?>
          <a class="list-group-item list-group-item-action"
             href="<?php echo $base_url; ?>/category/<?php echo urlencode($cat); ?>">
             <?php echo htmlspecialchars($cat); ?>
          </a>
      <?php endforeach; ?>
      </div>
  <?php endif; ?>
</div>

<?php include __DIR__ . '/templates/footer.php'; ?>

</body>
</html>
