<?php
require_once 'config.php';
echo "PDO DRIVER: " . $pdo->getAttribute(PDO::ATTR_DRIVER_NAME) . "<br>";
echo "PHP VERSION: " . PHP_VERSION . "<br>";
echo "PDO DRIVERS: " . implode(', ', PDO::getAvailableDrivers());
?>
