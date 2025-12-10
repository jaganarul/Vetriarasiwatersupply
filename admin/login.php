<?php
require_once __DIR__ . '/../init.php';

// Redirect to main login page
header('Location: ' . $base_url . '/login.php');
exit;