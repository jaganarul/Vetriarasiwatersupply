<?php
// config.php - set your DB credentials here
$db_host = 'localhost';
$db_name = 'if0_40473744_vetriarasiwatersupply';
$db_user = 'root';
$db_pass = 'root';


// server
// $db_host = 'sql113.infinityfree.com';
// $db_name = 'if0_40511202_web';
// $db_user = 'if0_40511202';
// $db_pass = 'yuyjHHKTSMcSnz8';

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

//yuyjHHKTSMcSnz8

// Base URL for links
// IMPORTANT: Your project is now directly in /htdocs/
// So the base URL must be root "/"
// $base_url = 'https://vetriarasiwatersupply.infinityfreeapp.com';
$base_url = '';
if (php_sapi_name() !== 'cli' && isset($_SERVER['SCRIPT_NAME'])) {
    $base_url = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/\\');
    if ($base_url === '/' || $base_url === '\\') {
        $base_url = ''; // root
    }
}
// Uploads folder
$upload_dir = rtrim(__DIR__ . '/uploads', '/\\') . '/'; // ensure trailing slash
?>
