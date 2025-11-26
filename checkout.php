<?php
require_once 'init.php';
if(empty($_SESSION['cart'])) { header('Location: ' . $base_url . '/cart.php'); exit; }
if(!is_logged_in()){ $_SESSION['return_to'] = 'checkout.php'; header('Location: ' . $base_url . '/login.php'); exit; }

// Prepare checkout summary and redirect to payments page where user chooses UPI or COD
$cart = $_SESSION['cart'];
$ids = array_keys($cart);
$placeholders = implode(',', array_fill(0, count($ids), '?'));
$stmt = $pdo->prepare("SELECT id,price,stock FROM products WHERE id IN ($placeholders)");
$stmt->execute($ids);
$rows = $stmt->fetchAll();
$total = 0;
foreach($rows as $r){
    $qty = $cart[$r['id']];
    if($qty > $r['stock']){
        // trim qty to available stock
        $_SESSION['cart'][$r['id']] = min($qty, $r['stock']);
    }
    $total += min($qty, $r['stock']) * $r['price'];
}

// Save checkout data into session and redirect to payments selection
$_SESSION['checkout_cart'] = $_SESSION['cart'];
$_SESSION['checkout_total'] = $total;
header('Location: ' . $base_url . '/payments.php');
exit;
?>