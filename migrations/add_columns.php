<?php
// migrations/add_columns.php
// Run: /Applications/XAMPP/xamppfiles/bin/php migrations/add_columns.php
require_once __DIR__ . '/../init.php';

function columnExists($pdo, $table, $column){
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM information_schema.columns WHERE table_schema = DATABASE() AND table_name = ? AND column_name = ?");
    $stmt->execute([$table, $column]);
    return (int)$stmt->fetchColumn() > 0;
}

$alterations = [];
if(!columnExists($pdo,'users','phone')){
    $alterations[] = "ALTER TABLE users ADD COLUMN phone VARCHAR(30) NOT NULL DEFAULT ''";
}
if(!columnExists($pdo,'users','address')){
    $alterations[] = "ALTER TABLE users ADD COLUMN address TEXT NULL";
}
if(!columnExists($pdo,'orders','delivery_address')){
    $alterations[] = "ALTER TABLE orders ADD COLUMN delivery_address TEXT NULL";
}
if(!columnExists($pdo,'orders','delivery_phone')){
    $alterations[] = "ALTER TABLE orders ADD COLUMN delivery_phone VARCHAR(30) NULL";
}

if(empty($alterations)){
    echo "No changes needed. Columns already exist.\n";
    exit(0);
}

foreach($alterations as $sql){
    try{
        echo "Running: $sql\n";
        $pdo->exec($sql);
        echo "OK\n";
    } catch (PDOException $e){
        echo "FAILED: " . $e->getMessage() . "\n";
    }
}

echo "Migration complete. Please verify your application.\n";

?>
