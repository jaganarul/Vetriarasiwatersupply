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
        $this->base_url = rtrim($base_url, '/');
    }

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

        return $this->getInvoiceHTML($order, $items);
    }

    private function getInvoiceHTML($order, $items) {

        $subtotal = $order['total'];
        $grand_total = $subtotal;

        /* PDF-safe logo */
        $logoPath = 'file://' . realpath(__DIR__ . '/assets/images/logo.png');

        /* WhatsApp link */
        $whatsappLink = "https://wa.me/91{$order['phone']}?text=" . urlencode(
            "Hello {$order['name']},\nInvoice #{$order['id']} from Vetriarasi Water Supply.\nTotal: ₹{$this->formatPrice($grand_total)}"
        );

$html = <<<HTML
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Invoice #{$order['id']}</title>

<style>
@page{
    size:A4;
    margin:15mm;
}

body{
    font-family:DejaVu Sans,'Segoe UI',sans-serif;
    background:#eef6ff;
    color:#1f2937;
    margin:0;
}

.invoice-container{
    max-width:900px;
    margin:auto;
    background:#fff;
    border-radius:14px;
    overflow:hidden;
}

/* LOGO */
.invoice-logo{
    text-align:center;
    padding:25px 10px;
    border-bottom:4px solid #0b74ff;
    background:linear-gradient(#eaf3ff,#ffffff);
}
.invoice-logo img{width:120px;margin-bottom:6px}
.invoice-logo h1{margin:0;color:#0b74ff;font-size:26px}
.invoice-logo p{font-size:13px;color:#555}

/* HEADER */
.invoice-header{
    display:flex;
    justify-content:space-between;
    padding:25px 30px;
    border-bottom:2px solid #0b74ff;
}
.invoice-title h2{margin:0;color:#0b74ff}
.status-badge{
    margin-top:6px;
    padding:6px 14px;
    border-radius:20px;
    font-size:12px;
    color:#fff;
    background:#16a34a;
    text-transform:uppercase;
}
.status-badge.pending{background:#f59e0b}

/* META */
.meta-grid{
    display:grid;
    grid-template-columns:1fr 1fr;
    gap:30px;
    padding:30px;
}
.meta h4{
    font-size:12px;
    color:#6b7280;
    margin-bottom:6px;
    text-transform:uppercase;
}
.meta p{font-size:14px;line-height:1.7}

/* TABLE */
table{
    width:calc(100% - 60px);
    margin:0 30px 30px;
    border-collapse:collapse;
    table-layout:fixed;
}
th,td{
    padding:12px;
    border-bottom:1px solid #e5e7eb;
    font-size:14px;
}
th{color:#0b74ff;text-align:left}

th:nth-child(1),td:nth-child(1){width:40%}
th:nth-child(2),td:nth-child(2){width:15%}
th:nth-child(3),td:nth-child(3){width:20%}
th:nth-child(4),td:nth-child(4){
    width:25%;
    white-space:nowrap;
}

.text-right{text-align:right}
.text-center{text-align:center}
.amount{
    font-weight:700;
    color:#0b74ff;
    white-space:nowrap;
}

/* TOTAL */
.totals{
    display:flex;
    justify-content:flex-end;
    padding:0 30px 30px;
}
.totals-box{
    width:calc(100% - 60px);
    max-width:320px;
}
.total-row{
    display:flex;
    justify-content:space-between;
    padding:8px 0;
}
.total-row.grand-total{
    border-top:2px solid #0b74ff;
    font-weight:700;
    color:#0b74ff;
    padding-top:12px;
}

/* FOOTER */
.footer{
    background:#f8fbff;
    padding:25px;
    text-align:center;
    font-size:12px;
    color:#555;
}
.footer a{
    display:inline-block;
    margin-top:12px;
    background:#25D366;
    color:#fff;
    padding:10px 24px;
    border-radius:30px;
    text-decoration:none;
    font-weight:600;
}
</style>
</head>

<body>

<div class="invoice-container">

    <div class="invoice-logo">
        <img src="assets/images/logo.png" alt="Vetriarasi Water Supply Logo" style="width:120px; height:auto;">
        <h1>Vetriarasi Watersupply</h1>
        <p>Pure Water • Fast Delivery • Trusted Service</p>
    </div>

    <div class="invoice-header">
        <div>
            📞 +91-9360658623<br>
            ✉️ vetriarasiwatersupply@gmail.com<br>
            Babu Nagar, Jolarpet – 635851
        </div>
        <div class="invoice-title">
            <h2>INVOICE</h2>
            <div class="status-badge {$this->getStatusClass($order['status'])}">
                {$order['status']}
            </div>
        </div>
    </div>

    <div class="meta-grid">
        <div class="meta">
            <h4>Bill To</h4>
            <p>
                <strong>{$order['name']}</strong><br>
                {$order['phone']}<br>
                {$order['email']}<br>
                {$order['delivery_address']}
            </p>
        </div>
        <div class="meta">
            <h4>Invoice Details</h4>
            <p>
                <strong>Invoice #:</strong> {$order['id']}<br>
                <strong>Tracking:</strong> {$order['tracking_code']}<br>
                <strong>Date:</strong> {$this->formatDate($order['created_at'])}<br>
                <strong>Due:</strong> {$this->formatDate($order['created_at'], '+1 days')}
            </p>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th>Description</th>
                <th class="text-center">Qty</th>
                <th class="text-right">Unit</th>
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

    <div class="totals">
        <div class="totals-box">
            <div class="total-row">
                <span>Subtotal</span>
                <span>₹{$this->formatPrice($subtotal)}</span>
            </div>
            <div class="total-row">
                <span>Delivery</span>
                <span>FREE</span>
            </div>
            <div class="total-row grand-total">
                <span>Grand Total</span>
                <span>₹{$this->formatPrice($grand_total)}</span>
            </div>
        </div>
    </div>

    <div class="footer">
        Thank you for choosing Vetriarasi Watersupply 💧<br>
        <a href="$whatsappLink">Send Invoice via WhatsApp</a>
    </div>

</div>
</body>
</html>
HTML;

        return $html;
    }

    private function getStatusClass($status) {
        return strtolower($status) === 'pending' ? 'pending' : '';
    }

    private function formatDate($date, $modify = null) {
        $dt = new DateTime($date);
        if ($modify) $dt->modify($modify);
        return $dt->format('d M Y');
    }

    private function formatPrice($price) {
        return number_format((float)$price, 2);
    }
}
