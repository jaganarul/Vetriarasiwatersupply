<?php
require_once 'init.php';
$cart = $_SESSION['cart'] ?? [];
$products = [];
$total = 0;
if($cart){
    $ids = array_keys($cart);
    $placeholders = implode(',', array_fill(0, count($ids), '?'));
    $stmt = $pdo->prepare("SELECT id,name,price,stock,thumbnail FROM products WHERE id IN ($placeholders)");
    $stmt->execute($ids);
    $rows = $stmt->fetchAll();
    foreach($rows as $r){
        $r['qty'] = $cart[$r['id']];
        $r['subtotal'] = $r['qty'] * $r['price'];
        $total += $r['subtotal'];
        $products[] = $r;
    }
}
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="assets/css/custom.css">
  <title>Cart</title>
</head>
<body>
<?php include __DIR__ . '/templates/header.php'; ?>

  <h3>Your Cart</h3>
  <?php if(!$products): ?><div class="alert alert-info">Cart is empty.</div><?php else: ?>
    <table class="table">
      <thead><tr><th></th><th>Product</th><th>Price</th><th>Qty</th><th>Subtotal</th><th></th></tr></thead>
      <tbody>
        <?php foreach($products as $p): ?>
          <tr>
            <td><?php if($p['thumbnail']): ?><img src="uploads/<?php echo esc($p['thumbnail']); ?>" style="height:40px;"><?php endif; ?></td>
            <td><?php echo esc($p['name']); ?></td>
            <td>$<?php echo number_format($p['price'],2); ?></td>
            <td>
              <form method="post" action="update_cart.php" class="d-inline">
                <input type="hidden" name="product_id" value="<?php echo $p['id']; ?>">
                <input type="number" name="qty" value="<?php echo $p['qty']; ?>" min="1" max="<?php echo $p['stock']; ?>" style="width:70px;" class="form-control d-inline-block">
                <button class="btn btn-sm btn-primary">Update</button>
              </form>
            </td>
            <td>$<?php echo number_format($p['subtotal'],2); ?></td>
            <td>
              <form method="post" action="remove_from_cart.php">
                <input type="hidden" name="product_id" value="<?php echo $p['id']; ?>">
                <button class="btn btn-sm btn-danger">Remove</button>
              </form>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
    <div class="d-flex justify-content-between align-items-center">
      <h5>Total: $<?php echo number_format($total,2); ?></h5>
      <a href="checkout.php" class="btn btn-success">Checkout</a>
    </div>
  <?php endif; ?>

<?php include __DIR__ . '/templates/footer.php'; ?>
</body>
</html>