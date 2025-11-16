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
?>