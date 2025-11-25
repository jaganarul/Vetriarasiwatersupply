<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '/../init.php';

if (!is_admin_logged_in()) {
    header('Location: login.php');
    exit;
}

// -------------------------------------------------
// Read Form Data
// -------------------------------------------------
$id        = isset($_POST['id']) ? (int)$_POST['id'] : 0;
$name      = trim($_POST['name'] ?? '');
$category  = trim($_POST['category'] ?? '');
$price     = (float)($_POST['price'] ?? 0);
$stock     = (int)($_POST['stock'] ?? 0);
$desc      = trim($_POST['description'] ?? '');

// -------------------------------------------------
// Upload Directory Setup
// -------------------------------------------------
// Use configured upload directory (ensure it has trailing slash)
$uploadDir = $upload_dir ?? (__DIR__ . '/../uploads/');
$uploadDir = rtrim($uploadDir, '/\\') . '/';

if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0777, true);
}

if (!is_writable($uploadDir)) {
    chmod($uploadDir, 0777);
}

$allowedExt = ['jpg','jpeg','png','webp','gif'];

// -------------------------------------------------
// Thumbnail Upload Handling
// -------------------------------------------------
$newThumbnail = null;

if (!empty($_FILES['thumbnail']['tmp_name'])) {
    if ($_FILES['thumbnail']['error'] === UPLOAD_ERR_OK) {

        $ext = strtolower(pathinfo($_FILES['thumbnail']['name'], PATHINFO_EXTENSION));

        if (in_array($ext, $allowedExt)) {

            $newThumbnail = time() . "_thumb." . $ext;
            $dest = $uploadDir . $newThumbnail;

            if (!move_uploaded_file($_FILES['thumbnail']['tmp_name'], $dest)) {
                if (!copy($_FILES['thumbnail']['tmp_name'], $dest)) {
                    $_SESSION['admin_msg'] = "Thumbnail upload failed.";
                    $newThumbnail = null;
                }
            } else {
                chmod($dest, 0644);
            }

        } else {
            $_SESSION['admin_msg'] = "Invalid thumbnail format.";
        }
    }
}

// -------------------------------------------------
// Secondary Images Upload Handling
// -------------------------------------------------
$newImages = [];

if (!empty($_FILES['images']['tmp_name'][0])) {

    foreach ($_FILES['images']['tmp_name'] as $i => $tmp) {

        $error = $_FILES['images']['error'][$i];

        if ($error !== UPLOAD_ERR_OK || !$tmp) continue;

        $ext = strtolower(pathinfo($_FILES['images']['name'][$i], PATHINFO_EXTENSION));

        if (!in_array($ext, $allowedExt)) continue;

        $file = time() . "_" . rand(1000,9999) . "." . $ext;
        $dest = $uploadDir . $file;

        if (!move_uploaded_file($tmp, $dest)) {
            if (!copy($tmp, $dest)) continue;
        }

        chmod($dest, 0644);
        $newImages[] = $file;
    }
}

// -------------------------------------------------
// Update Product
// -------------------------------------------------
if ($id > 0) {

    // Fetch old data
    $stmt = $pdo->prepare("SELECT thumbnail, images FROM products WHERE id = ?");
    $stmt->execute([$id]);
    $old = $stmt->fetch();

    $existingImages = $old['images'] ? json_decode($old['images'], true) : [];
    if (!is_array($existingImages)) $existingImages = [];

    // Remove selected images
    $removeImages = $_POST['remove_images'] ?? [];

    foreach ($removeImages as $img) {
        $img = basename($img);

        if (($key = array_search($img, $existingImages)) !== false) {
            unset($existingImages[$key]);
        }

        $file = $uploadDir . $img;
        if (file_exists($file)) unlink($file);
    }

    $existingImages = array_values($existingImages);

    // Remove thumbnail if selected
    if (!empty($_POST['remove_thumbnail']) && !empty($old['thumbnail'])) {
        $oldFile = $uploadDir . $old['thumbnail'];
        if (file_exists($oldFile)) unlink($oldFile);
        $old['thumbnail'] = null;
    }

    // Merge old + new images
    $finalImages = array_merge($existingImages, $newImages);
    $finalImages = array_values($finalImages);

    // Decide final thumbnail
    $finalThumbnail = $newThumbnail ?? $old['thumbnail'];

    // Update query
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

}
// -------------------------------------------------
// Create New Product
// -------------------------------------------------
else {

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
