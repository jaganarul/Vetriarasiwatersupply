<?php
require_once __DIR__ . '/../init.php';
if(!is_admin_logged_in()) { header('Location: login.php'); exit; }

$stmt = $pdo->query('SELECT * FROM products ORDER BY created_at DESC');
$products = $stmt->fetchAll();
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

<title>Products</title>

<style>
body {
  background: #f4f6f9;
  font-family: "Segoe UI", sans-serif;
}

/* NAVBAR */
.navbar-custom {
  background: #fff;
  border-bottom: 1px solid #ddd;
  box-shadow: 0 2px 8px rgba(0,0,0,0.05);
}

@media (max-width: 992px){
  .sidebar { position: fixed; left: -260px; top: 0; height: 100vh; width: 240px; z-index: 1050; transition: left 0.35s ease; }
  .sidebar.open { left: 0; }
  .main { margin-left: 0; padding-top: 12px; }
}
.navbar-brand img {
  height: 45px;
  margin-right: 10px;
}

/* CARD + TABLE */
.table-modern {
  background: #fff;
  border-radius: 16px;
  box-shadow: 0 4px 20px rgba(0,0,0,0.05);
  overflow: hidden;
}

.table-modern thead {
  background: #0d6efd;
  color: white;
}

.table-modern tbody tr:hover {
  background: #f0f8ff;
  transition: 0.2s;
}

/* BUTTONS */
.btn-primary, .btn-success {
  border-radius: 8px;
}
.btn-danger {
  border-radius: 8px;
}

/* MODAL */
.modal-content {
  border-radius: 16px !important;
  box-shadow: 0 4px 25px rgba(0,0,0,0.2);
}
</style>

</head>
<body>

<!-- TOP NAV WITH LOGO -->
<nav class="navbar navbar-custom mb-4">
  <div class="container d-flex align-items-center">
      <button id="sidebarToggle" class="btn btn-light d-md-none me-2" aria-label="Toggle sidebar"><i class="bi bi-list"></i></button>
      <a class="navbar-brand d-flex align-items-center" href="index.php">
          <img src="<?php echo $base_url; ?>/assets/images/logo.png" alt="Logo">
          <span class="fw-bold">Admin Panel</span>
      </a>
      <a class="btn btn-outline-primary ms-auto" href="index.php">Dashboard</a>
  </div>
</nav>

<div class="container">

  <div class="d-flex justify-content-between align-items-center mb-3">
    <h3 class="fw-bold">Products</h3>
    <button class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#productModal">+ Add Product</button>
  </div>

  <?php if(!empty($_SESSION['admin_msg'])): ?>
    <div class="alert alert-info">
      <?php echo esc($_SESSION['admin_msg']); unset($_SESSION['admin_msg']); ?>
    </div>
  <?php endif; ?>

  <div class="table-modern p-3">
    <table class="table align-middle">
      <thead>
        <tr>
          <th>ID</th>
          <th>Thumb</th>
          <th>Name</th>
          <th>Price</th>
          <th>Stock</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
      <?php foreach($products as $p): ?>
        <tr>
          <td><?php echo $p['id']; ?></td>
          <td>
            <?php if($p['thumbnail']): ?>
              <img src="<?php echo $base_url; ?>/uploads/<?php echo esc($p['thumbnail']); ?>" style="height:60px;border-radius:8px;">
            <?php else: ?>
              <span class="text-muted small">No Image</span>
            <?php endif; ?>
          </td>
          <td><?php echo esc($p['name']); ?></td>
          <td>₹<?php echo number_format((float)($p['price'] ?? 0), 2); ?></td>
          <td><?php echo (int)$p['stock']; ?></td>
          <td>
            <button class="btn btn-sm btn-primary"
              onclick="openEditFromButton(this)"
              data-product='<?php echo htmlspecialchars((string)json_encode($p), ENT_QUOTES, "UTF-8"); ?>'>
              Edit
            </button>

            <form method="post" action="product_delete.php" class="d-inline-block" onsubmit="return confirm('Delete this product?');">
              <input type="hidden" name="id" value="<?php echo $p['id']; ?>">
              <button class="btn btn-sm btn-danger">Delete</button>
            </form>
          </td>
        </tr>
      <?php endforeach; ?>
      </tbody>
    </table>
  </div>
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

<!-- PRODUCT MODAL -->
<div class="modal fade" id="productModal" tabindex="-1">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">

      <form method="post" action="product_save.php" enctype="multipart/form-data">

        <div class="modal-header">
          <h5 class="modal-title">Product Editor</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>

        <div class="modal-body">

          <input type="hidden" name="id" id="prod_id">

          <div class="row">
            <div class="col-md-6 mb-3">
              <label>Name</label>
              <input class="form-control" name="name" id="prod_name">
            </div>

            <div class="col-md-6 mb-3">
              <label>Category</label>
              <input class="form-control" name="category" id="prod_cat">
            </div>

            <div class="col-md-6 mb-3">
              <label>Price</label>
              <input class="form-control" name="price" id="prod_price" type="number" step="0.01">
            </div>

            <div class="col-md-6 mb-3">
              <label>Stock</label>
              <input class="form-control" name="stock" id="prod_stock" type="number">
            </div>

            <div class="col-12 mb-3">
              <label>Description</label>
              <textarea class="form-control" name="description" id="prod_desc"></textarea>
            </div>

            <div class="col-md-6 mb-3">
              <label>Thumbnail</label>
              <input type="file" name="thumbnail" class="form-control">
            </div>

            <div class="col-md-6 mb-3">
              <label>Secondary Images</label>
              <input type="file" name="images[]" class="form-control" multiple>
            </div>
          </div>

          <div id="existingImages" class="mt-3"></div>

        </div>

        <div class="modal-footer">
          <button class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
          <button class="btn btn-primary">Save</button>
        </div>

      </form>

    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<script>
const BASE_URL = '<?php echo $base_url; ?>';

function openEditFromButton(el) {
  try {
    var p = JSON.parse(el.getAttribute('data-product'));
    openEdit(p);
  } catch (e) { console.warn(e); }
}

function openEdit(p) {
  var modal = new bootstrap.Modal(document.getElementById('productModal'));

  document.getElementById('prod_id').value = p.id;
  document.getElementById('prod_name').value = p.name;
  document.getElementById('prod_cat').value = p.category;
  document.getElementById('prod_price').value = p.price;
  document.getElementById('prod_stock').value = p.stock;
  document.getElementById('prod_desc').value = p.description;

  var container = document.getElementById('existingImages');
  container.innerHTML = '';

  try {
    var imgs = [];
    if (p.images) imgs = (typeof p.images === 'string') ? JSON.parse(p.images) : p.images;

    if (p.thumbnail) {
      var div = document.createElement('div');
      div.innerHTML = `
        <label class="form-label">Current Thumbnail</label>
        <div class="d-flex align-items-center gap-3">
          <img src="${BASE_URL + '/uploads/' + p.thumbnail}" style="height:60px;border-radius:8px;">
          <label><input type="checkbox" name="remove_thumbnail" value="1"> Remove</label>
        </div>`;
      container.appendChild(div);
    }

    if (imgs.length) {
      var g = document.createElement('div');
      g.innerHTML = `<label class="form-label mt-3">Secondary Images</label>`;

      imgs.forEach(im => {
        var item = document.createElement('div');
        item.className = 'd-flex align-items-center gap-3 mb-2';

        item.innerHTML = `
          <img src="${BASE_URL + '/uploads/' + im}" style="height:60px;border-radius:8px;">
          <label><input type="checkbox" name="remove_images[]" value="${im}"> Remove</label>`;

        g.appendChild(item);
      });

      container.appendChild(g);
    }
  } catch (e) {}

  modal.show();
}
</script>

</body>
</html>
