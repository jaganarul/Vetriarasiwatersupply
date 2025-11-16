<?php
// setup.php - create initial admin account. Run: php setup.php
require_once __DIR__ . '/config.php';
$admin_email = 'admin@example.com';
$admin_name = 'Site Admin';
$admin_password_plain = 'Admin@123';

// check if admin exists
$stmt = $pdo->prepare("SELECT id FROM admins WHERE email = ?");
$stmt->execute([$admin_email]);
if ($stmt->fetch()) {
    echo "Admin already exists.\n";
    exit;
}
$hash = password_hash($admin_password_plain, PASSWORD_DEFAULT);
$stmt = $pdo->prepare("INSERT INTO admins (name, email, password) VALUES (?, ?, ?)");
$stmt->execute([$admin_name, $admin_email, $hash]);

echo "Created admin: $admin_email with password: $admin_password_plain\n";
echo "Please change the password after first login.\n";
?>