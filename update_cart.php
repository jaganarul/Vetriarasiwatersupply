<?php
require_once 'init.php';
if($_SERVER['REQUEST_METHOD'] !== 'POST') { header('Location: ' . $base_url . '/cart'); exit; }
$pid = (int)($_POST['product_id'] ?? 0);
$qty = max(1, (int)($_POST['qty'] ?? 1));
// check stock
$stmt = $pdo->prepare('SELECT stock FROM products WHERE id = ?');
$stmt->execute([$pid]);
$p = $stmt->fetch();
if(!$p) { header('Location: ' . $base_url . '/cart'); exit; }
if($qty > $p['stock']) $qty = $p['stock'];
if(isset($_SESSION['cart'][$pid])){
    $_SESSION['cart'][$pid] = $qty;
}
header('Location: ' . $base_url . '/cart');
?>
<?php
require_once 'init.php';
if($_SERVER['REQUEST_METHOD'] !== 'POST') { header('Location: ' . $base_url . '/cart'); exit; }
$pid = isset($_POST['product_id']) ? (int)$_POST['product_id'] : 0;
$qty = isset($_POST['qty']) ? max(1, (int)$_POST['qty']) : 1;
// verify stock
$stmt = $pdo->prepare('SELECT stock FROM products WHERE id = ?');
$stmt->execute([$pid]);
$p = $stmt->fetch();
if(!$p){ header('Location: ' . $base_url . '/cart'); exit; }
if($qty > $p['stock']) $qty = $p['stock'];
if(!isset($_SESSION['cart'])) $_SESSION['cart'] = [];
$_SESSION['cart'][$pid] = $qty;
header('Location: ' . $base_url . '/cart'); exit;
?>
<?php
require_once 'init.php';
if($_SERVER['REQUEST_METHOD'] !== 'POST') { header('Location: ' . $base_url . '/cart'); exit; }
$pid = isset($_POST['product_id']) ? (int)$_POST['product_id'] : 0;
$qty = isset($_POST['qty']) ? max(1, (int)$_POST['qty']) : 1;
// verify stock
$stmt = $pdo->prepare('SELECT stock FROM products WHERE id = ?');
$stmt->execute([$pid]);
$p = $stmt->fetch();
if(!$p){ header('Location: ' . $base_url . '/cart'); exit; }
if($qty > $p['stock']) $qty = $p['stock'];
if(!isset($_SESSION['cart'])) $_SESSION['cart'] = [];
$_SESSION['cart'][$pid] = $qty;
header('Location: ' . $base_url . '/cart'); exit;
?>
<?php
require_once 'init.php';
if($_SERVER['REQUEST_METHOD'] !== 'POST') { header('Location: ' . $base_url . '/cart'); exit; }
$pid = (int)($_POST['product_id'] ?? 0);
$qty = max(1, (int)($_POST['qty'] ?? 1));
// check stock
$stmt = $pdo->prepare('SELECT stock FROM products WHERE id = ?');
$stmt->execute([$pid]);
$p = $stmt->fetch();
if(!$p) { header('Location: ' . $base_url . '/cart'); exit; }
if($qty > $p['stock']) $qty = $p['stock'];
if(isset($_SESSION['cart'][$pid])){
    $_SESSION['cart'][$pid] = $qty;
}
header('Location: ' . $base_url . '/cart');
?>
<?php
require_once 'init.php';
if($_SERVER['REQUEST_METHOD'] !== 'POST') { header('Location: ' . $base_url . '/cart'); exit; }
$pid = isset($_POST['product_id'])? (int)$_POST['product_id'] : 0;
$qty = isset($_POST['qty'])? max(1,(int)$_POST['qty']) : 1;
// verify stock
$stmt = $pdo->prepare('SELECT stock FROM products WHERE id = ?');
$stmt->execute([$pid]);
$p = $stmt->fetch();
if(!$p){ header('Location: ' . $base_url . '/cart'); exit; }
if($qty > $p['stock']) $qty = $p['stock'];
if(!isset($_SESSION['cart'])) $_SESSION['cart'] = [];
$_SESSION['cart'][$pid] = $qty;
header('Location: ' . $base_url . '/cart'); exit;
?>
<?php
require_once 'init.php';
if($_SERVER['REQUEST_METHOD'] !== 'POST') { header('Location: cart.php'); exit; }
$pid = (int)($_POST['product_id'] ?? 0);
$qty = max(1, (int)($_POST['qty'] ?? 1));
// check stock
$stmt = $pdo->prepare('SELECT stock FROM products WHERE id = ?');
$stmt->execute([$pid]);
$p = $stmt->fetch();
if(!$p) { header('Location: cart.php'); exit; }
if($qty > $p['stock']) $qty = $p['stock'];
if(isset($_SESSION['cart'][$pid])){
    $_SESSION['cart'][$pid] = $qty;
}
header('Location: cart.php');
?>