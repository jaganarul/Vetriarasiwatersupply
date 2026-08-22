<?php
require_once __DIR__ . '/../init.php';

if (!is_admin_logged_in()) {
    header('Location: ' . $base_url . '/admin/login.php');
    exit;
}

$invoice_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if (!$invoice_id) {
    die('Invalid invoice ID');
}

// Fetch invoice details
$stmt = $pdo->prepare("
    SELECT 
        i.id,
        i.invoice_number,
        i.total,
        i.subtotal,
        i.tax,
        i.status,
        i.invoice_date,
        i.due_date,
        o.id as order_id,
        o.tracking_code,
        u.name as customer_name,
        u.email as customer_email,
        u.phone,
        u.address
    FROM invoices i
    INNER JOIN orders o ON i.order_id = o.id
    INNER JOIN users u ON o.user_id = u.id
    WHERE i.id = ?
");
$stmt->execute([$invoice_id]);
$invoice = $stmt->fetch();

if (!$invoice) {
    die('Invoice not found');
}

// Fetch order items
$stmt = $pdo->prepare("
    SELECT 
        oi.qty,
        oi.price,
        p.name as product_name,
        p.category
    FROM order_items oi
    INNER JOIN products p ON oi.product_id = p.id
    WHERE oi.order_id = ?
");
$stmt->execute([$invoice['order_id']]);
$items = $stmt->fetchAll();

// HTML to PDF using TCPDF (simple HTML-based PDF generation)
ob_start();
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Invoice <?php echo $invoice['invoice_number']; ?></title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, sans-serif; color: #333; background: white; }
        .container { max-width: 800px; margin: 0 auto; padding: 40px; }
        .header { border-bottom: 3px solid #0b74ff; margin-bottom: 30px; padding-bottom: 20px; }
        .company-info { font-size: 28px; font-weight: bold; color: #0b74ff; margin-bottom: 5px; }
        .company-details { font-size: 12px; color: #666; margin-top: 10px; }
        .invoice-title { font-size: 24px; font-weight: bold; color: #333; margin-bottom: 10px; }
        .invoice-meta { display: table; width: 100%; margin-bottom: 30px; }
        .invoice-meta-col { display: table-cell; width: 50%; font-size: 13px; }
        .meta-label { font-weight: bold; color: #0b74ff; }
        .customer-info, .order-info { margin-bottom: 20px; }
        .section-title { font-weight: bold; color: #0b74ff; margin-top: 15px; margin-bottom: 8px; }
        table { width: 100%; border-collapse: collapse; margin: 20px 0; }
        thead { background: #f5f5f5; border-bottom: 2px solid #0b74ff; }
        th { padding: 12px; text-align: left; font-weight: bold; color: #333; }
        td { padding: 10px 12px; border-bottom: 1px solid #e0e0e0; }
        .total-section { margin-top: 30px; }
        .total-row { display: table; width: 100%; margin-bottom: 8px; }
        .total-row-label { display: table-cell; width: 70%; text-align: right; padding-right: 20px; }
        .total-row-value { display: table-cell; width: 30%; text-align: right; font-weight: bold; }
        .grand-total { border-top: 2px solid #0b74ff; border-bottom: 2px solid #0b74ff; font-size: 18px; padding-top: 10px; padding-bottom: 10px; }
        .footer { margin-top: 40px; text-align: center; font-size: 11px; color: #999; border-top: 1px solid #e0e0e0; padding-top: 20px; }
        .status-badge { padding: 4px 8px; border-radius: 4px; font-size: 11px; font-weight: bold; display: inline-block; }
        .status-paid { background: #e8f5e9; color: #388e3c; }
        .status-pending { background: #fff3e0; color: #e65100; }
        .status-draft { background: #f5f5f5; color: #666; }
        @media print {
            body { background: white; }
            .container { padding: 0; }
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <div class="company-info">💧 Vetriarasiwatersupply</div>
            <div class="company-details">
                Reliable Water Solutions<br>
                Email: vetriarasiwatersupply@gmail.com<br>
                Phone: 9360658623
            </div>
        </div>

        <!-- Invoice Title and Status -->
        <div style="display: table; width: 100%; margin-bottom: 20px;">
            <div style="display: table-cell; width: 50%;">
                <div class="invoice-title">INVOICE</div>
                <div style="font-size: 13px; margin-top: 5px;">
                    <span class="meta-label">Invoice #:</span> <?php echo esc($invoice['invoice_number']); ?><br>
                    <span class="meta-label">Order #:</span> <?php echo esc($invoice['order_id']); ?><br>
                    <span class="meta-label">Tracking:</span> <?php echo esc($invoice['tracking_code']); ?>
                </div>
            </div>
            <div style="display: table-cell; width: 50%; text-align: right;">
                <span class="status-badge status-<?php echo strtolower($invoice['status']); ?>">
                    Status: <?php echo ucfirst($invoice['status']); ?>
                </span>
            </div>
        </div>

        <!-- Invoice Meta Info -->
        <div class="invoice-meta">
            <div class="invoice-meta-col">
                <div class="meta-label">Invoice Date:</div>
                <div><?php echo date('F d, Y', strtotime($invoice['invoice_date'])); ?></div>
            </div>
            <div class="invoice-meta-col">
                <div class="meta-label">Due Date:</div>
                <div><?php echo $invoice['due_date'] ? date('F d, Y', strtotime($invoice['due_date'])) : 'Upon Receipt'; ?></div>
            </div>
        </div>

        <!-- Customer Info -->
        <div class="customer-info">
            <div class="section-title">BILL TO:</div>
            <div style="font-size: 13px; line-height: 1.6;">
                <strong><?php echo esc($invoice['customer_name']); ?></strong><br>
                Email: <?php echo esc($invoice['customer_email']); ?><br>
                Phone: <?php echo esc($invoice['phone']); ?><br>
                Address: <?php echo esc($invoice['address']); ?>
            </div>
        </div>

        <!-- Items Table -->
        <table>
            <thead>
                <tr>
                    <th style="width: 50%;">Description</th>
                    <th style="width: 15%; text-align: center;">Qty</th>
                    <th style="width: 17.5%; text-align: right;">Unit Price</th>
                    <th style="width: 17.5%; text-align: right;">Amount</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($items as $item): $amount = (float)$item['qty'] * (float)$item['price']; ?>
                    <tr>
                        <td>
                            <strong><?php echo esc($item['product_name']); ?></strong><br>
                            <span style="font-size: 11px; color: #999;"><?php echo esc($item['category']); ?></span>
                        </td>
                        <td style="text-align: center;"><?php echo (int)$item['qty']; ?></td>
                        <td style="text-align: right;">₹<?php echo number_format($item['price'], 2); ?></td>
                        <td style="text-align: right;">₹<?php echo number_format($amount, 2); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <!-- Totals Section -->
        <div class="total-section">
            <div class="total-row">
                <div class="total-row-label">Subtotal:</div>
                <div class="total-row-value">₹<?php echo number_format($invoice['subtotal'] ?: $invoice['total'], 2); ?></div>
            </div>
            <?php if ($invoice['tax'] > 0): ?>
                <div class="total-row">
                    <div class="total-row-label">Tax:</div>
                    <div class="total-row-value">₹<?php echo number_format($invoice['tax'], 2); ?></div>
                </div>
            <?php endif; ?>
            <div class="total-row grand-total">
                <div class="total-row-label">TOTAL:</div>
                <div class="total-row-value">₹<?php echo number_format($invoice['total'], 2); ?></div>
            </div>
        </div>

        <!-- Footer -->
        <div class="footer">
            <p><strong>Thank you for your business!</strong></p>
            <p>This is an electronically generated invoice. No signature is required.</p>
            <p style="margin-top: 15px; color: #999;">
                Generated on <?php echo date('F d, Y \a\t H:i A'); ?> from Vetriarasiwatersupply
            </p>
        </div>
    </div>

    <script>
        // Auto-print or allow user to print
        window.print();
    </script>
</body>
</html>
<?php
$html = ob_get_clean();

// Send as PDF download
header('Content-Type: application/pdf');
header('Content-Disposition: attachment; filename="Invoice_' . $invoice['invoice_number'] . '.pdf"');
header('Cache-Control: no-cache, no-store, must-revalidate');
header('Pragma: no-cache');
header('Expires: 0');

// For now, output HTML to browser (will print to PDF when user clicks print)
// To generate true PDF, you would need TCPDF or similar library
echo $html;
?>
