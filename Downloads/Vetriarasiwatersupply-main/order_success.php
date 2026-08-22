<?php
require_once 'init.php';
$id = isset($_GET['order'])? (int)$_GET['order'] : 0;
$stmt = $pdo->prepare('SELECT * FROM orders WHERE id = ? AND user_id = ?');
$stmt->execute([$id, $_SESSION['user_id'] ?? 0]);
$order = $stmt->fetch();
if(!$order) { echo 'Order not found'; exit; }
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
<link rel="stylesheet" href="<?php echo $base_url; ?>/assets/css/responsive.css">
  <link rel="stylesheet" href="<?php echo $base_url; ?>/assets/css/custom-clean.css">
<link rel="stylesheet" href="<?php echo $base_url; ?>/assets/css/amazon-style.css">
<title>Order Confirmed</title>

<style>

/* --------------------------------------------------
   🎨 EXACT COLOR PALETTE (INSERTED HERE)
-------------------------------------------------- */
:root {
    --primary-blue: #0B74FF;
    --accent-cyan: #00D4FF;
    --success-green: #22C55E;

    --bg-primary: #FFFFFF;
    --bg-secondary: #F7F9FC;
    --bg-tertiary: #E9EEF7;

    --text-primary: #0A1F44;
    --text-secondary: #6B7C93;

    --border-light: #DCE3EC;
}
/* -------------------------------------------------- */


.success-container {
    min-height: 80vh;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 40px 20px;
    background: linear-gradient(135deg, rgba(11, 116, 255, 0.05), rgba(0, 212, 255, 0.05));
}

.success-card {
    background: var(--bg-primary);
    border-radius: 16px;
    padding: 48px 32px;
    max-width: 600px;
    width: 100%;
    box-shadow: 0 20px 60px rgba(0, 0, 0, 0.1);
    text-align: center;
    animation: slideUp 0.6s ease;
}

@keyframes slideUp {
    from { opacity: 0; transform: translateY(30px); }
    to { opacity: 1; transform: translateY(0); }
}

.success-icon {
    width: 80px;
    height: 80px;
    background: var(--success-green);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 24px;
    font-size: 48px;
    color: white;
}

.success-title {
    font-size: 32px;
    font-weight: 800;
    color: var(--text-primary);
    margin: 0 0 12px 0;
}

.success-subtitle {
    font-size: 16px;
    color: var(--text-secondary);
    margin-bottom: 32px;
}

.order-details {
    background: var(--bg-secondary);
    border-radius: 12px;
    padding: 24px;
    margin: 32px 0;
    text-align: left;
}

.detail-row {
    display: flex;
    justify-content: space-between;
    padding: 12px 0;
    border-bottom: 1px solid var(--border-light);
    font-size: 14px;
}

.detail-row:last-child {
    border-bottom: none;
}

.detail-label {
    color: var(--text-secondary);
    font-weight: 600;
}

.detail-value {
    color: var(--text-primary);
    font-weight: 700;
    font-family: monospace;
}

.action-buttons {
    display: flex;
    gap: 12px;
    flex-wrap: wrap;
    justify-content: center;
    margin-top: 32px;
}

.btn-action {
    padding: 12px 28px;
    border-radius: 8px;
    font-weight: 700;
    font-size: 15px;
    border: none;
    cursor: pointer;
    transition: all 0.3s ease;
    text-decoration: none;
    display: inline-block;
}

.btn-action.primary {
    background: linear-gradient(90deg, var(--primary-blue), var(--accent-cyan));
    color: white;
}

.btn-action.primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 10px 30px rgba(11, 116, 255, 0.3);
}

.btn-action.secondary {
    background: var(--bg-tertiary);
    color: var(--text-primary);
}

.btn-action.secondary:hover {
    background: #d7e3fa;
}

.info-box {
    background: rgba(11, 116, 255, 0.05);
    border: 1px solid rgba(11, 116, 255, 0.2);
    border-radius: 8px;
    padding: 16px;
    margin: 24px 0;
    font-size: 14px;
    line-height: 1.6;
    color: var(--text-secondary);
}

.info-box strong {
    color: var(--text-primary);
}

@media (max-width: 576px) {
    .success-card { padding: 32px 20px; }
    .success-title { font-size: 24px; }
    .action-buttons { flex-direction: column; }
    .btn-action { width: 100%; }
}

</style>
</head>
<body>

<?php include __DIR__ . '/templates/header.php'; ?>

<div class="success-container">
    <div class="success-card">

        <!-- Success Icon -->
        <div class="success-icon">✓</div>

        <!-- Title -->
        <h1 class="success-title">Order Confirmed!</h1>
        <p class="success-subtitle">Thank you for your order. We're preparing your items for shipment.</p>

        <!-- Order Details -->
        <div class="order-details">
            <div class="detail-row">
                <span class="detail-label">Order ID</span>
                <span class="detail-value">#<?php echo $order['id']; ?></span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Tracking Code</span>
                <span class="detail-value"><?php echo esc($order['tracking_code']); ?></span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Status</span>
                <span class="detail-value" style="color: var(--primary-blue);">📦 <?php echo esc($order['status']); ?></span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Total Amount</span>
                <span class="detail-value" style="color: var(--success-green);">₹<?php echo number_format((float)$order['total'], 2); ?></span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Delivery To</span>
                <span class="detail-value" style="font-family: inherit; max-width: 200px; text-align: right; word-break: break-word;"><?php echo esc($order['delivery_address'] ?? 'N/A'); ?></span>
            </div>
        </div>

        <!-- Info -->
        <div class="info-box">
            <strong>📧 What Next?</strong><br>
            You'll receive an email confirmation shortly with your tracking details.  
            Track using your code: <strong><?php echo esc($order['tracking_code']); ?></strong>
        </div>

        <!-- Buttons -->
        <div class="action-buttons">
            <a href="<?php echo $base_url; ?>/invoice.php?order=<?php echo $order['id']; ?>" class="btn-action primary">
                <i class="bi bi-file-earmark-pdf"></i> Download Invoice
            </a>
            <a href="<?php echo $base_url; ?>/track.php" class="btn-action primary">
                <i class="bi bi-box-seam"></i> Track Order
            </a>
            <a href="<?php echo $base_url; ?>/product.php" class="btn-action secondary">
                <i class="bi bi-shop"></i> Continue Shopping
            </a>
        </div>

    </div>
</div>

<?php include __DIR__ . '/templates/footer.php'; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
