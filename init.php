<?php
// init.php - include on each page
session_start();
require_once __DIR__ . '/config.php';

function is_logged_in() {
    return !empty($_SESSION['user_id']);
}
function is_admin_logged_in() {
    return !empty($_SESSION['admin_id']);
}

function esc($s) { return htmlspecialchars($s, ENT_QUOTES, 'UTF-8'); }

// populate categories for header dropdown (safe: ensure variable exists)
$catRows = [];
try {
    if (isset($pdo)) {
        $stmt = $pdo->query('SELECT DISTINCT category FROM products ORDER BY category ASC');
        $catRows = $stmt->fetchAll();
    }
} catch (Exception $e) {
    // on error, leave $catRows empty to avoid warnings in templates
    $catRows = [];
}
?>