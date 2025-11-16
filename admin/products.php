<?php
require_once __DIR__ . '/../init.php';
if(!is_admin_logged_in()) { header('Location: login.php'); exit; }
// fetch products
$stmt = $pdo->query('SELECT * FROM products ORDER BY created_at DESC');
$products = $stmt->fetchAll();
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="../assets/css/custom.css">
  <title>Products</title>
</head>
<body>
<nav class="navbar navbar-dark bg-dark mb-3"><div class="container"><a class="navbar-brand" href="index.php">Admin</a><a class="btn btn-light btn-sm" href="index.php">Dashboard</a></div></nav>
<div class="container">
  <h3>Products <button class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#productModal">Add Product</button></h3>
  <table class="table table-modern">
    <thead><tr><th>ID</th><th>Thumb</th><th>Name</th><th>Price</th><th>Stock</th><th>Actions</th></tr></thead>
    <tbody>
      <?php foreach($products as $p): ?>
        <tr>
          <td><?php echo $p['id']; ?></td>
          <td><?php if($p['thumbnail']): ?><img src="../uploads/<?php echo esc($p['thumbnail']); ?>" style="height:50px;"><?php endif; ?></td>
          <td><?php echo esc($p['name']); ?></td>
          <td>$<?php echo number_format($p['price'],2); ?></td>
          <td><?php echo (int)$p['stock']; ?></td>
          <td>
            <button class="btn btn-sm btn-primary" onclick='openEdit(<?php echo json_encode($p); ?>)'>Edit</button>
            <form method="post" action="product_delete.php" class="d-inline-block" onsubmit="return confirm('Delete?');">
              <input type="hidden" name="id" value="<?php echo $p['id']; ?>">
              <button class="btn btn-sm btn-danger">Delete</button>
            </form>
          </td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>

<!-- Modal -->
<div class="modal fade" id="productModal" tabindex="-1">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <form method="post" action="product_save.php" enctype="multipart/form-data">
      <div class="modal-header"><h5 class="modal-title">Product</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
      <div class="modal-body">
        <input type="hidden" name="id" id="prod_id">
        <div class="mb-3"><label>Name</label><input class="form-control" name="name" id="prod_name"></div>
        <div class="mb-3"><label>Category</label><input class="form-control" name="category" id="prod_cat"></div>
        <div class="mb-3"><label>Price</label><input class="form-control" name="price" id="prod_price" type="number" step="0.01"></div>
        <div class="mb-3"><label>Stock</label><input class="form-control" name="stock" id="prod_stock" type="number"></div>
        <div class="mb-3"><label>Description</label><textarea class="form-control" name="description" id="prod_desc"></textarea></div>
        <div class="mb-3"><label>Thumbnail (single)</label><input type="file" name="thumbnail" class="form-control"></div>
        <div class="mb-3"><label>Secondary Images (multiple)</label><input type="file" name="images[]" class="form-control" multiple></div>
      </div>
      <div class="modal-footer"><button class="btn btn-secondary" data-bs-dismiss="modal">Close</button><button class="btn btn-primary">Save</button></div>
      </form>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
function openEdit(p){
  var modal = new bootstrap.Modal(document.getElementById('productModal'));
  document.getElementById('prod_id').value = p.id;
  document.getElementById('prod_name').value = p.name;
  document.getElementById('prod_cat').value = p.category;
  document.getElementById('prod_price').value = p.price;
  document.getElementById('prod_stock').value = p.stock;
  document.getElementById('prod_desc').value = p.description;
  modal.show();
}
</script>
</body>
</html>