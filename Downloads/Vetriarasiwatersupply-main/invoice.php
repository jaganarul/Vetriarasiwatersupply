<?php
require_once 'init.php';

if(!is_logged_in()) {
    header('Location: ' . $base_url . '/login.php');
    exit;
}

$order_id = isset($_GET['order']) ? (int)$_GET['order'] : 0;
$download = isset($_GET['download']) ? true : false;

// Verify order belongs to user
$stmt = $pdo->prepare('SELECT id FROM orders WHERE id = ? AND user_id = ?');
$stmt->execute([$order_id, $_SESSION['user_id']]);
if (!$stmt->fetch()) {
    header('HTTP/1.0 403 Forbidden');
    exit('Unauthorized access to invoice');
}

// Load invoice generator
require_once __DIR__ . '/classes/InvoiceGenerator.php';
$invoiceGen = new InvoiceGenerator($pdo, $base_url);
$invoiceHTML = $invoiceGen->generateHTML($order_id);

if (!$invoiceHTML) {
    header('HTTP/1.0 404 Not Found');
    exit('Invoice not found');
}

if ($download) {
    // Force download as HTML file
    header('Content-Type: text/html; charset=UTF-8');
    header('Content-Disposition: attachment; filename="invoice_' . $order_id . '.html"');
    echo $invoiceHTML;
} else {
    // Display HTML with print/download button
    $download_link = $base_url . '/invoice.php?order=' . $order_id . '&download=true';
    header('Content-Type: text/html; charset=UTF-8');
    echo <<<HTML
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice Download</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            background: #f5f5f5;
        }
        .actions {
            max-width: 900px;
            margin: 0 auto 20px;
            display: flex;
            gap: 10px;
            justify-content: center;
            flex-wrap: wrap;
        }
        .btn {
            padding: 10px 20px;
            border: none;
            border-radius: 6px;
            font-weight: 600;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
            transition: all 0.3s;
        }
        .btn-primary {
            background: linear-gradient(90deg, #0b74ff, #00d4ff);
            color: white;
        }
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 30px rgba(11, 116, 255, 0.3);
        }
        .btn-secondary {
            background: #666;
            color: white;
        }
        .btn-secondary:hover {
            background: #555;
        }
        @media print {
            .actions {
                display: none;
            }
        }
    </style>
</head>
<body>

<div class="actions">
    <button class="btn btn-primary" onclick="window.print()">🖨️ Print as PDF</button>
    <a href="{$download_link}" class="btn btn-primary">⬇️ Download HTML</a>
    <a href="{$base_url}/profile.php" class="btn btn-secondary">← Back to Orders</a>
</div>

{$invoiceHTML}

</body>
</html>
HTML;
}
?>

