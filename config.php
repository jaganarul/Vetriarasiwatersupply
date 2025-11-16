<?php
// config.php - set your DB credentials here
$db_host = '127.0.0.1';
$db_name = 'ecommerce_dbbb';
$db_user = 'root';
$db_pass = '';
$dsn = "mysql:host=$db_host;dbname=$db_name;charset=utf8mb4";
$options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
];
try {
    $pdo = new PDO($dsn, $db_user, $db_pass, $options);
} catch (Exception $e) {
    die('Database connection failed: ' . $e->getMessage());
}

// base URL for links -- change if placed in subfolder or different host
$base_url = '/Vetriarasiwatersupply';

// uploads folder
$upload_dir = __DIR__ . '/uploads';
?>