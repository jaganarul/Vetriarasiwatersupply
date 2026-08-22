<?php
/**
 * Database Setup & Verification
 * 
 * This file creates missing tables and verifies database structure.
 * Access: /setup.php or run this from admin panel
 */

require_once __DIR__ . '/init.php';

// Check if user is admin or setup is being run for first time
$show_form = true;
$setup_status = [];

// If form is submitted, create tables
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['create_tables'])) {
    try {
        // Create invoices table if it doesn't exist
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
        $setup_status['invoices'] = ['status' => 'success', 'message' => '✅ Invoices table created successfully'];
        
    } catch (PDOException $e) {
        if (strpos($e->getMessage(), 'already exists') !== false) {
            $setup_status['invoices'] = ['status' => 'info', 'message' => 'ℹ️ Invoices table already exists'];
        } else {
            $setup_status['invoices'] = ['status' => 'error', 'message' => '❌ Error: ' . $e->getMessage()];
        }
    }
    
    $show_form = false;
}

// Check which tables exist
$tables = [];
try {
    $stmt = $pdo->query("SHOW TABLES");
    $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
} catch (Exception $e) {
    // Can't check tables
}

?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Database Setup - Vetriarasiwatersupply</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            font-family: 'Inter', system-ui, -apple-system, 'Segoe UI', Roboto;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        .container-card {
            background: white;
            border-radius: 12px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            max-width: 600px;
            width: 100%;
            padding: 40px;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #f0f0f0;
            padding-bottom: 20px;
        }
        .header h1 {
            color: #0b74ff;
            font-weight: 700;
            margin-bottom: 10px;
        }
        .header p {
            color: #666;
            font-size: 14px;
        }
        .table-status {
            background: #f9f9f9;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 20px;
        }
        .table-item {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 12px 0;
            border-bottom: 1px solid #e0e0e0;
        }
        .table-item:last-child {
            border-bottom: none;
        }
        .table-item .name {
            font-weight: 600;
            color: #333;
        }
        .table-item .badge {
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 600;
        }
        .badge-exists {
            background: #e8f5e9;
            color: #388e3c;
        }
        .badge-missing {
            background: #ffebee;
            color: #c62828;
        }
        .btn-setup {
            background: linear-gradient(90deg, #0b74ff, #00d4ff);
            color: white;
            border: none;
            padding: 12px 24px;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            width: 100%;
        }
        .btn-setup:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(11, 116, 255, 0.3);
            color: white;
        }
        .alert-custom {
            border-radius: 8px;
            border-left: 4px solid;
            margin-bottom: 20px;
        }
        .alert-success-custom {
            border-left-color: #10b981;
            background: #e8f5e9;
            color: #388e3c;
        }
        .alert-info-custom {
            border-left-color: #3b82f6;
            background: #e3f2fd;
            color: #1976d2;
        }
        .alert-error-custom {
            border-left-color: #ef4444;
            background: #ffebee;
            color: #c62828;
        }
        .instructions {
            background: #f0f9ff;
            border-radius: 8px;
            padding: 15px;
            margin-top: 20px;
            font-size: 13px;
            color: #0366d6;
            border-left: 4px solid #0366d6;
        }
    </style>
</head>
<body>
    <div class="container-card">
        <div class="header">
            <h1><i class="bi bi-database"></i> Database Setup</h1>
            <p>Vetriarasiwatersupply - Invoice System</p>
        </div>

        <!-- Status Messages -->
        <?php foreach ($setup_status as $table => $status): ?>
            <div class="alert-custom alert-<?php echo $status['status']; ?>-custom">
                <?php echo $status['message']; ?>
            </div>
        <?php endforeach; ?>

        <!-- Table Status -->
        <div class="table-status">
            <h5 style="margin-bottom: 16px; color: #333; font-weight: 700;">Database Tables</h5>
            
            <div class="table-item">
                <span class="name"><i class="bi bi-table"></i> users</span>
                <span class="badge <?php echo in_array('users', $tables) ? 'badge-exists' : 'badge-missing'; ?>">
                    <?php echo in_array('users', $tables) ? '✓ Exists' : '✗ Missing'; ?>
                </span>
            </div>
            
            <div class="table-item">
                <span class="name"><i class="bi bi-table"></i> admins</span>
                <span class="badge <?php echo in_array('admins', $tables) ? 'badge-exists' : 'badge-missing'; ?>">
                    <?php echo in_array('admins', $tables) ? '✓ Exists' : '✗ Missing'; ?>
                </span>
            </div>
            
            <div class="table-item">
                <span class="name"><i class="bi bi-table"></i> products</span>
                <span class="badge <?php echo in_array('products', $tables) ? 'badge-exists' : 'badge-missing'; ?>">
                    <?php echo in_array('products', $tables) ? '✓ Exists' : '✗ Missing'; ?>
                </span>
            </div>
            
            <div class="table-item">
                <span class="name"><i class="bi bi-table"></i> orders</span>
                <span class="badge <?php echo in_array('orders', $tables) ? 'badge-exists' : 'badge-missing'; ?>">
                    <?php echo in_array('orders', $tables) ? '✓ Exists' : '✗ Missing'; ?>
                </span>
            </div>
            
            <div class="table-item">
                <span class="name"><i class="bi bi-table"></i> order_items</span>
                <span class="badge <?php echo in_array('order_items', $tables) ? 'badge-exists' : 'badge-missing'; ?>">
                    <?php echo in_array('order_items', $tables) ? '✓ Exists' : '✗ Missing'; ?>
                </span>
            </div>
            
            <div class="table-item">
                <span class="name"><i class="bi bi-file-earmark-pdf"></i> invoices</span>
                <span class="badge <?php echo in_array('invoices', $tables) ? 'badge-exists' : 'badge-missing'; ?>">
                    <?php echo in_array('invoices', $tables) ? '✓ Exists' : '✗ Missing'; ?>
                </span>
            </div>
        </div>

        <!-- Setup Form -->
        <?php if ($show_form && !in_array('invoices', $tables)): ?>
            <form method="POST">
                <button type="submit" name="create_tables" value="1" class="btn-setup">
                    <i class="bi bi-lightning-charge"></i> Create Missing Tables
                </button>
            </form>
            
            <div class="instructions">
                <i class="bi bi-info-circle"></i> Click the button above to create the invoices table needed for the invoice management system.
            </div>
        <?php elseif (in_array('invoices', $tables)): ?>
            <div class="alert-custom alert-success-custom">
                <i class="bi bi-check-circle"></i> All tables are set up correctly! You can now use the invoice system.
            </div>
            <a href="admin/invoices.php" class="btn-setup" style="text-decoration: none; display: inline-block; width: 100%;">
                <i class="bi bi-file-earmark-pdf"></i> Go to Invoice Management
            </a>
        <?php endif; ?>
    </div>
</body>
</html>
