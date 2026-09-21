<?php

// ============================================================
// SUPABASE PostgreSQL DATABASE CONNECTION - VERCEL
// ============================================================

$host = getenv('SUPABASE_HOST') ?: 'aws-0-ap-south-1.pooler.supabase.com';
$port = getenv('SUPABASE_PORT') ?: '5432';
$dbname = getenv('SUPABASE_DB') ?: 'postgres';
$user = getenv('SUPABASE_USER') ?: 'postgres.hchrjjnemksuyozftmwy';
$password = getenv('SUPABASE_PASSWORD');

$dsn = "pgsql:host=$host;port=$port;dbname=$dbname";

$options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
];

try {
    $pdo = new PDO($dsn, $user, $password, $options);
} catch (PDOException $e) {
    die('Database connection failed: ' . $e->getMessage());
}

// ============================================================
// WEBSITE BASE URL
// ============================================================

$base_url = '';

// ============================================================
// UPLOAD DIRECTORY
// ============================================================

$upload_dir = rtrim(__DIR__ . '/uploads', '/\\') . '/';

?>
