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
    <link rel="stylesheet" href="<?php echo $base_url; ?>/assets/css/custom.css">
  <title>Cart</title>

<style>
.cart-container {
    padding: 40px 15px;
}

.cart-card {
    background: #ffffff;
    border-radius: 18px;
    padding: 25px;
    box-shadow: 0 8px 28px rgba(0,0,0,0.12);
    animation: fadeIn .6s ease;
}

@keyframes fadeIn {
    from {opacity:0; transform:translateY(20px);}
    to {opacity:1; transform:translateY(0);}
}

.cart-table img {
    border-radius: 8px;
}

.cart-table th {
    background: #f1f5ff;
    border-bottom: 2px solid #dbe3ff;
    font-weight: bold;
    color: #003d8f;
}

.cart-table td {
    vertical-align: middle;
    padding: 14px 12px;
}

.total-box {
    background: #003d8f;
    color: #fff;
    padding: 18px 22px;
    border-radius: 15px;
    font-size: 20px;
    font-weight: 600;
}

.checkout-btn {
    height: 50px;
    border-radius: 12px;
    font-size: 18px;
}
</style>
</head>

<body>

<?php include __DIR__ . '/templates/header.php'; ?>

<div class="container cart-container">

    <h2 class="fw-bold mb-3 text-primary">🛒 Your Shopping Cart</h2>

    <?php if(!$products): ?>
        <div class="alert alert-info text-center py-4 fs-5">
            Your cart is empty.  
            <br><a href="<?php echo $base_url; ?>/product.php" class="btn btn-primary btn-sm mt-3">Browse Products</a>
        </div>

    <?php else: ?>

    <div class="cart-card">

        <table class="table cart-table align-middle">
            <thead>
                <tr>
                    <th>Item</th>
                    <th>Product</th>
                    <th>Price</th>
                    <th width="150">Qty</th>
                    <th>Subtotal</th>
                    <th></th>
                </tr>
            </thead>

            <tbody>
            <?php foreach($products as $p): ?>
                <tr>
                    <td>
                        <?php if($p['thumbnail']): ?>
                            <img src="<?php echo $base_url; ?>/uploads/<?php echo esc($p['thumbnail']); ?>" height="55">
                        <?php endif; ?>
                    </td>

                    <td class="fw-semibold">
                        <?php echo esc($p['name']); ?>
                    </td>

                    <td class="fw-bold text-success">
                        ₹<?php echo number_format((float)($p['price'] ?? 0),2); ?>
                    </td>

                    <td>
                        <form method="post" action="<?php echo $base_url; ?>/update_cart.php" class="d-flex gap-2 align-items-center">
                            <input type="hidden" name="product_id" value="<?php echo $p['id']; ?>">
                            <input type="number" name="qty" value="<?php echo $p['qty']; ?>" min="1" max="<?php echo $p['stock']; ?>" class="form-control" style="width:80px;">
                            <button class="btn btn-sm btn-outline-primary">Update</button>
                        </form>
                    </td>

                    <td class="fw-bold text-dark">
                        ₹<?php echo number_format((float)($p['subtotal'] ?? 0),2); ?>
                    </td>

                    <td>
                        <form method="post" action="<?php echo $base_url; ?>/remove_from_cart.php">
                            <input type="hidden" name="product_id" value="<?php echo $p['id']; ?>">
                            <button class="btn btn-sm btn-danger px-3">Remove</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>

        <div class="d-flex justify-content-between align-items-center mt-4">
            <div class="total-box">
                Total: ₹<?php echo number_format((float)($total ?? 0),2); ?>
            </div>

            <a href="<?php echo $base_url; ?>/checkout.php" class="btn btn-success checkout-btn px-4">
                Proceed to Checkout →
            </a>
        </div>

    </div>

    <?php endif; ?>

</div>

<?php include __DIR__ . '/templates/footer.php'; ?>

</body>
</html>
