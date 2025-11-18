<?php
// init.php - include on each page
ini_set('display_errors', 1);
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
require_once __DIR__ . '/config.php';

function is_logged_in() {
    return !empty($_SESSION['user_id']);
}
function is_admin_logged_in() {
    return !empty($_SESSION['admin_id']);
}

function esc($s) { return htmlspecialchars($s, ENT_QUOTES, 'UTF-8'); }
// Populate categories for header navigation if DB connection is available
$catRows = [];
if(isset($pdo)){
    try{
        $stmt = $pdo->query("SELECT DISTINCT category FROM products WHERE category IS NOT NULL AND category <> '' ORDER BY category ASC");
        $catRows = $stmt->fetchAll();
    } catch(Exception $e){
        $catRows = [];
    }
}
?>