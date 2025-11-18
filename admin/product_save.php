<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
require_once __DIR__ . '/../init.php';
if (!is_admin_logged_in()) { header('Location: login.php'); exit; }

// -------------------------
// GET FORM FIELDS
// -------------------------
$id        = isset($_POST['id']) ? (int)$_POST['id'] : 0;
$name      = trim($_POST['name'] ?? '');
$category  = trim($_POST['category'] ?? '');
$price     = (float)($_POST['price'] ?? 0);
$stock     = (int)($_POST['stock'] ?? 0);
$desc      = trim($_POST['description'] ?? '');

// Ensure uploads folder exists
$uploadDir = __DIR__ . '/../uploads/';
if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0755, true);
}

// Allowed image extensions
$allowedExt = ['jpg','jpeg','png','webp','gif'];

// -------------------------
// HANDLE THUMBNAIL
// -------------------------
$newThumbnail = null;

if (!empty($_FILES['thumbnail']['tmp_name'])) {
    $ext = strtolower(pathinfo($_FILES['thumbnail']['name'], PATHINFO_EXTENSION));
    if (in_array($ext, $allowedExt)) {
        $newThumbnail = time() . "_thumb." . $ext;
        move_uploaded_file($_FILES['thumbnail']['tmp_name'], $uploadDir . $newThumbnail);
    }
}

// -------------------------
// HANDLE SECONDARY IMAGES
// -------------------------
$newImages = [];

if (!empty($_FILES['images']['tmp_name'])) {
    foreach ($_FILES['images']['tmp_name'] as $i => $tmp) {
        if (!$tmp) continue;

        $ext = strtolower(pathinfo($_FILES['images']['name'][$i], PATHINFO_EXTENSION));

        if (in_array($ext, $allowedExt)) {
            $fileName = time() . "_" . rand(1000,9999) . "." . $ext;
            move_uploaded_file($tmp, $uploadDir . $fileName);
            $newImages[] = $fileName;
        }
    }
}

//
// -------------------------
// UPDATE PRODUCT
// -------------------------
if ($id > 0) {

    // Fetch old data
    $stmt = $pdo->prepare("SELECT thumbnail, images FROM products WHERE id = ?");
    $stmt->execute([$id]);
    $old = $stmt->fetch();

    // Decode old JSON images
    $existingImages = $old['images'] ? json_decode($old['images'], true) : [];
    if (!is_array($existingImages)) $existingImages = [];

    // -------------------------
    // DELETE SELECTED IMAGES
    // -------------------------
    $removeImages = $_POST['remove_images'] ?? [];

    if (!empty($removeImages)) {
        foreach ($removeImages as $img) {

            $img = basename($img); // prevent path injection

            // Remove from array
            $index = array_search($img, $existingImages);
            if ($index !== false) {
                unset($existingImages[$index]);
            }

            // Delete from folder
            $f = $uploadDir . $img;
            if (file_exists($f)) unlink($f);
        }

        // Reindex
        $existingImages = array_values($existingImages);
    }

    // -------------------------
    // DELETE THUMBNAIL IF REQUESTED
    // -------------------------
    if (!empty($_POST['remove_thumbnail'])) {
        if (!empty($old['thumbnail'])) {
            $thumbFile = $uploadDir . $old['thumbnail'];
            if (file_exists($thumbFile)) unlink($thumbFile);
        }
        $old['thumbnail'] = null;
    }

    // -------------------------
    // MERGE OLD + NEW IMAGES
    // -------------------------
    $finalImages = array_merge($existingImages, $newImages);
    $finalImages = array_values($finalImages); // clean indexing

    // -------------------------
    // FINAL THUMBNAIL DECISION
    // -------------------------
    $finalThumbnail = $newThumbnail ?? $old['thumbnail'];

    // -------------------------
    // UPDATE QUERY
    // -------------------------
    $stmt = $pdo->prepare("
        UPDATE products SET 
            name=?, category=?, price=?, description=?, stock=?, 
            thumbnail=?, images=? 
        WHERE id=?
    ");

    $stmt->execute([
        $name, $category, $price, $desc, $stock,
        $finalThumbnail,
        json_encode($finalImages),
        $id
    ]);

} else {
    // -------------------------
    // CREATE PRODUCT
    // -------------------------
    $stmt = $pdo->prepare("
        INSERT INTO products (name, category, price, description, stock, thumbnail, images)
        VALUES (?, ?, ?, ?, ?, ?, ?)
    ");

    $stmt->execute([
        $name, $category, $price, $desc, $stock,
        $newThumbnail,
        json_encode($newImages)
    ]);
}

header("Location: products.php");
exit;
?>
