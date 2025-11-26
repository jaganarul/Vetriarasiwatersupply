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
if (php_sapi_name() !== 'cli') {
    // Prefer to map the filesystem path to DOCUMENT_ROOT: this gives us
    // an accurate application base path regardless of which script runs.
    $docRoot = realpath($_SERVER['DOCUMENT_ROOT'] ?? '') ?: '';
    $appDir = realpath(__DIR__) ?: '';
    if ($docRoot && $appDir && strpos($appDir, $docRoot) === 0) {
        $basePath = substr($appDir, strlen($docRoot));
        $basePath = str_replace('\\', '/', $basePath);
        $basePath = rtrim($basePath, '/');
        if ($basePath === '' || $basePath === '/') {
            $base_url = '';
        } else {
            $base_url = $basePath;
        }
    } else {
        // If this can't be calculated, fall back to using the script path and
        // strip common subdirs like '/admin'. This is a safe fallback.
        $scriptDir = rtrim(dirname($_SERVER['SCRIPT_NAME'] ?? ''), '/\\');
        $scriptDir = preg_replace('|/admin$|', '', $scriptDir);
        if ($scriptDir === '/' || $scriptDir === '\\') $scriptDir = '';
        $base_url = $scriptDir;
    }
}
// Uploads folder
$upload_dir = rtrim(__DIR__ . '/uploads', '/\\') . '/'; // ensure trailing slash
?>
