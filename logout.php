<?php
require_once 'init.php';
session_unset(); session_destroy();
header('Location: ' . $base_url . '/'); exit;
?>