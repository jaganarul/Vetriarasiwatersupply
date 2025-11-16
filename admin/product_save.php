<?php
require_once __DIR__ . '/../init.php';
if(!is_admin_logged_in()) { header('Location: login.php'); exit; }
// handle create or update
$id = isset($_POST['id'])? (int)$_POST['id'] : 0;
$name = $_POST['name'] ?? '';
$category = $_POST['category'] ?? '';
$price = (float)($_POST['price'] ?? 0);
$stock = (int)($_POST['stock'] ?? 0);
$desc = $_POST['description'] ?? '';

// file handling
$thumbnail_name = null;
if(!empty($_FILES['thumbnail']['tmp_name'])){
    $t = $_FILES['thumbnail'];
    $ext = pathinfo($t['name'], PATHINFO_EXTENSION);
    $allowed = ['jpg','jpeg','png','webp','gif'];
    if(in_array(strtolower($ext), $allowed)){
        $thumbnail_name = time() . '_thumb.' . $ext;
        move_uploaded_file($t['tmp_name'], __DIR__ . '/../uploads/' . $thumbnail_name);
    }
}
$secondary_names = [];
if(!empty($_FILES['images'])){
    foreach($_FILES['images']['tmp_name'] as $k => $tmp){
        if(!$tmp) continue;
        $nm = $_FILES['images']['name'][$k];
        $ext = pathinfo($nm, PATHINFO_EXTENSION);
        $allowed = ['jpg','jpeg','png','webp','gif'];
        if(in_array(strtolower($ext), $allowed)){
            $fname = time() . '_' . $k . '.' . $ext;
            move_uploaded_file($tmp, __DIR__ . '/../uploads/' . $fname);
            $secondary_names[] = $fname;
        }
    }
}

if($id){
    // fetch existing
    $stmt = $pdo->prepare('SELECT thumbnail, images FROM products WHERE id = ?');
    $stmt->execute([$id]);
    $old = $stmt->fetch();
    $images = $old['images'] ? json_decode($old['images'], true) : [];
    // merge
    $images = array_merge($images, $secondary_names);
    $thumb = $thumbnail_name ?? $old['thumbnail'];
    $stmt = $pdo->prepare('UPDATE products SET name=?,category=?,price=?,description=?,stock=?,thumbnail=?,images=? WHERE id=?');
    $stmt->execute([$name,$category,$price,$desc,$stock,$thumb,json_encode($images),$id]);
} else {
    $stmt = $pdo->prepare('INSERT INTO products (name,category,price,description,stock,thumbnail,images) VALUES (?,?,?,?,?,?,?)');
    $stmt->execute([$name,$category,$price,$desc,$stock,$thumbnail_name,json_encode($secondary_names)]);
}
header('Location: products.php'); exit;
?>