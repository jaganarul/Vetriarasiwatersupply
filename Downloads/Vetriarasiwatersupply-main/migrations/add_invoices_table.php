<?php
/**
 * Migration: Add invoices table
 * 
 * This migration creates the invoices table to support invoice management
 * for admins to view and download invoices for customer orders.
 */

require_once __DIR__ . '/../init.php';

try {
    // Create invoices table
    $sql = "
    CREATE TABLE IF NOT EXISTS `invoices` (
      `id` INT AUTO_INCREMENT PRIMARY KEY,
      `order_id` INT NOT NULL UNIQUE,
      `invoice_number` VARCHAR(100) NOT NULL UNIQUE,
      `invoice_date` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
      `due_date` DATE,
      `subtotal` DECIMAL(10,2) DEFAULT 0,
      `tax` DECIMAL(10,2) DEFAULT 0,
      `total` DECIMAL(10,2) NOT NULL,
      `status` ENUM('Draft','Sent','Viewed','Paid','Overdue','Cancelled') DEFAULT 'Draft',
      `notes` TEXT,
      `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
      `updated_at` TIMESTAMP NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
      INDEX (`order_id`),
      INDEX (`invoice_number`),
      INDEX (`status`),
      CONSTRAINT `fk_invoices_order` FOREIGN KEY (`order_id`) REFERENCES `orders`(`id`) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
    ";
    
    $pdo->exec($sql);
    echo "✅ Migration successful! Invoices table created.\n";
    
} catch (PDOException $e) {
    if ($e->getCode() == '42S01') {
        echo "ℹ️ Table already exists. Skipping creation.\n";
    } else {
        echo "❌ Migration failed: " . $e->getMessage() . "\n";
        exit(1);
    }
}
?>
