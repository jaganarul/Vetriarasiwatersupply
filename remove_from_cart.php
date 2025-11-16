<?php
require_once 'init.php';
if($_SERVER['REQUEST_METHOD'] !== 'POST') { header('Location: cart.php'); exit; }
$pid = (int)($_POST['product_id'] ?? 0);
if(isset($_SESSION['cart'][$pid])) unset($_SESSION['cart'][$pid]);
header('Location: cart.php');
?>