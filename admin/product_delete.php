<?php
require_once __DIR__ . '/../init.php';
if(!is_admin_logged_in()) { header('Location: login.php'); exit; }
$id = isset($_POST['id'])? (int)$_POST['id'] : 0;
if($id){
    // optionally remove images
    $stmt = $pdo->prepare('SELECT thumbnail,images FROM products WHERE id = ?');
    $stmt->execute([$id]);
    $r = $stmt->fetch();
    if($r){
        if($r['thumbnail'] && file_exists(__DIR__.'/../uploads/'.$r['thumbnail'])) @unlink(__DIR__.'/../uploads/'.$r['thumbnail']);
        if($r['images']){
            $imgs = json_decode($r['images'], true) ?: [];
            foreach($imgs as $im) if($im && file_exists(__DIR__.'/../uploads/'.$im)) @unlink(__DIR__.'/../uploads/'.$im);
        }
    }
    $stmt = $pdo->prepare('DELETE FROM products WHERE id = ?');
    $stmt->execute([$id]);
}
header('Location: products.php'); exit;
?>