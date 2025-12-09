<?php
/**
 * Invoice Generation Utility
 * Generates PDF invoices for orders
 */

require_once 'init.php';

class InvoiceGenerator {
    private $pdo;
    private $base_url;

    public function __construct($pdo, $base_url) {
        $this->pdo = $pdo;
        $this->base_url = $base_url;
    }

    /**
     * Generate HTML invoice for order
     */
    public function generateHTML($order_id) {
        $stmt = $this->pdo->prepare('
            SELECT o.*, u.name, u.email, u.phone 
            FROM orders o 
            JOIN users u ON o.user_id = u.id 
            WHERE o.id = ?
        ');
        $stmt->execute([$order_id]);
        $order = $stmt->fetch();

        if (!$order) return null;

        $stmt = $this->pdo->prepare('
            SELECT oi.*, p.name as product_name 
            FROM order_items oi 
            JOIN products p ON oi.product_id = p.id 
            WHERE oi.order_id = ?
        ');
        $stmt->execute([$order_id]);
        $items = $stmt->fetchAll();

        $html = $this->getInvoiceHTML($order, $items);
        return $html;
    }

    /**
     * Get invoice HTML markup
     */
    private function getInvoiceHTML($order, $items) {
        $subtotal = $order['total'];
        $grand_total = $subtotal;

        $html = <<<HTML
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice #{$order['id']}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            color: #333;
            background: #f5f5f5;
            padding: 20px;
        }
        .invoice-logo {
            text-align: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 2px solid #0b74ff;
        }
        .invoice-logo h1 {
            font-size: 36px;
            color: #0b74ff;
            margin: 0 0 8px 0;
        }
        .invoice-logo p {
            color: #666;
            margin: 0;
            font-size: 14px;
        }
        .invoice-header {
            background: white;
            max-width: 900px;
            margin: 0 auto;
            padding: 40px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            border-radius: 8px;
        }
        .invoice-header {
            display: flex;
            justify-content: space-between;
            align-items: start;
            margin-bottom: 40px;
            border-bottom: 2px solid #0b74ff;
            padding-bottom: 20px;
        }
        .company-info h1 {
            color: #0b74ff;
            font-size: 28px;
            margin-bottom: 8px;
        }
        .company-info p {
            color: #666;
            font-size: 13px;
            line-height: 1.6;
        }
        .invoice-title {
            text-align: right;
        }
        .invoice-title h2 {
            font-size: 32px;
            color: #0b74ff;
            margin-bottom: 8px;
        }
        .invoice-meta {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 40px;
            margin-bottom: 40px;
        }
        .meta-section h3 {
            font-size: 12px;
            text-transform: uppercase;
            color: #999;
            letter-spacing: 0.5px;
            margin-bottom: 8px;
            font-weight: 600;
        }
        .meta-section p {
            font-size: 14px;
            color: #333;
            line-height: 1.8;
        }
        .meta-section strong {
            font-weight: 700;
        }
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 40px;
        }
        .items-table thead {
            background: #f9f9f9;
            border-bottom: 2px solid #0b74ff;
        }
        .items-table th {
            padding: 12px 10px;
            text-align: left;
            font-weight: 700;
            color: #0b74ff;
            font-size: 13px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .items-table td {
            padding: 16px 10px;
            border-bottom: 1px solid #e5e5e5;
            font-size: 14px;
        }
        .items-table tbody tr:last-child td {
            border-bottom: none;
        }
        .text-right {
            text-align: right;
        }
        .text-center {
            text-align: center;
        }
        .amount {
            font-weight: 700;
            color: #0b74ff;
        }
        .totals {
            display: flex;
            justify-content: flex-end;
            margin-bottom: 40px;
        }
        .totals-box {
            width: 100%;
            max-width: 400px;
        }
        .total-row {
            display: flex;
            justify-content: space-between;
            padding: 10px 0;
            border-bottom: 1px solid #e5e5e5;
            font-size: 14px;
        }
        .total-row.grand-total {
            border-bottom: 2px solid #0b74ff;
            border-top: 2px solid #0b74ff;
            padding: 15px 0;
            margin-top: 10px;
            font-weight: 700;
            font-size: 16px;
            color: #0b74ff;
        }
        .footer {
            border-top: 1px solid #e5e5e5;
            padding-top: 20px;
            margin-top: 40px;
            text-align: center;
            color: #999;
            font-size: 12px;
            line-height: 1.6;
        }
        .status-badge {
            display: inline-block;
            background: #34a853;
            color: white;
            padding: 6px 12px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
        }
        .status-badge.pending {
            background: #ff9800;
        }
        @media print {
            body {
                background: white;
                padding: 0;
            }
            .invoice-container {
                box-shadow: none;
                padding: 0;
            }
        }
    </style>
</head>
<body>

<div class="invoice-logo">
    <img src="assets/images/logo.png" alt="Vetriarasi Water Supply Logo" style="width:120px; height:auto;">
    <h1>Vetriarasiwatersupply</h1>
    <p>Quality Water Delivered Daily</p>
</div>

    <!-- Header -->
    <div class="invoice-header">
        <div class="company-info">
            <p><strong>Contact Information:</strong><br>
            Phone: +91-9360658623<br>
            Email: vetriarasiwatersupply@gamil.com<br>
            <br>
            Address: Babu Nagar, Jolarpet, Tirupattur district<br>
            pincode: 635851</p>
        </div>
        <div class="invoice-title">
            <h2>INVOICE</h2>
            <div class="status-badge {$this->getStatusClass($order['status'])}">{$order['status']}</div>
        </div>
    </div>

    <!-- Metadata -->
    <div class="invoice-meta">
        <div class="meta-section">
            <h3>Bill To</h3>
            <p>
                <strong>{$order['name']}</strong><br>
                {$order['email']}<br>
                {$order['phone']}<br>
                {$order['delivery_address']}
            </p>
        </div>
        <div class="meta-section">
            <h3>Invoice Details</h3>
            <p>
                <strong>Invoice #:</strong> {$order['id']}<br>
                <strong>Tracking Code:</strong> {$order['tracking_code']}<br>
                <strong>Date:</strong> {$this->formatDate($order['created_at'])}<br>
                <strong>Due Date:</strong> {$this->formatDate($order['created_at'], '+1 days')}
            </p>
        </div>
    </div>

    <!-- Items Table -->
    <table class="items-table">
        <thead>
            <tr>
                <th>Description</th>
                <th class="text-center">Qty</th>
                <th class="text-right">Unit Price</th>
                <th class="text-right">Amount</th>
            </tr>
        </thead>
        <tbody>
HTML;

        foreach ($items as $item) {
            $amount = $item['qty'] * $item['price'];
            $html .= <<<HTML
            <tr>
                <td>{$item['product_name']}</td>
                <td class="text-center">{$item['qty']}</td>
                <td class="text-right">₹{$this->formatPrice($item['price'])}</td>
                <td class="text-right amount">₹{$this->formatPrice($amount)}</td>
            </tr>
HTML;
        }

        $html .= <<<HTML
        </tbody>
    </table>

    <!-- Totals -->
    <div class="totals">
        <div class="totals-box">
            <div class="total-row">
                <span>Subtotal</span>
                <span class="amount">₹{$this->formatPrice($subtotal)}</span>
            </div>
            <div class="total-row">
                <span>Delivery</span>
                <span class="amount">₹ FREE</span>
            </div>
            <div class="total-row grand-total">
                <span>Grand Total</span>
                <span>₹{$this->formatPrice($grand_total)}</span>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <div class="footer">
        <p><strong>Thank you for your business!</strong></p>
        <p>This is a computer-generated invoice. No signature required.</p>
        <p>For any queries, please contact us at: vetriarasiwatersupply@gmail.com</p>
    </div>
</div>

</body>
</html>
HTML;

        return $html;
    }

    /**
     * Get status badge class
     */
    private function getStatusClass($status) {
        if (strtolower($status) === 'pending') return 'pending';
        if (strtolower($status) === 'delivered') return '';
        return '';
    }

    /**
     * Format date
     */
    private function formatDate($date, $modify = null) {
        $dt = new DateTime($date);
        if ($modify) $dt->modify($modify);
        return $dt->format('d M Y');
    }

    /**
     * Format price
     */
    private function formatPrice($price) {
        return number_format((float)$price, 2);
    }
}
