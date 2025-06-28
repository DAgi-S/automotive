<?php
session_start();
define('INCLUDED', true);
require_once('../../includes/config.php');
require_once('../../includes/ad_functions.php');

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: ../../login.php');
    exit();
}

$conn = getDBConnection();
$user_id = $_SESSION['user_id'];
$order_number = $_GET['order'] ?? '';

if (empty($order_number)) {
    header('Location: products.php');
    exit();
}

// Get order details
$order_query = "SELECT o.*, p.status as payment_status 
                FROM tbl_orders o 
                LEFT JOIN tbl_payments p ON o.id = p.order_id 
                WHERE o.order_number = ? AND o.user_id = ?";
$stmt = $conn->prepare($order_query);
$stmt->execute([$order_number, $user_id]);
$order = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$order) {
    header('Location: products.php');
    exit();
}

// Get order items
$items_query = "SELECT * FROM tbl_order_items WHERE order_id = ?";
$stmt = $conn->prepare($items_query);
$stmt->execute([$order['id']]);
$orderItems = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Order Confirmation - Nati Automotive</title>
    <?php require_once('../includes/head.php'); ?>
    <style>
        .success-container {
            padding: 20px;
            background: #f8f9fa;
            min-height: calc(100vh - 120px);
        }
        
        .success-card {
            background: #fff;
            border-radius: 8px;
            padding: 30px;
            margin-bottom: 20px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            text-align: center;
        }
        
        .success-icon {
            font-size: 4rem;
            color: #28a745;
            margin-bottom: 20px;
        }
        
        .success-title {
            font-size: 1.5rem;
            font-weight: 600;
            color: #333;
            margin-bottom: 10px;
        }
        
        .success-message {
            color: #666;
            margin-bottom: 20px;
        }
        
        .order-number {
            font-size: 1.2rem;
            font-weight: 600;
            color: #007bff;
            background: #e7f3ff;
            padding: 10px 20px;
            border-radius: 4px;
            display: inline-block;
            margin-bottom: 20px;
        }
        
        .order-details {
            background: #fff;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        
        .detail-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 0;
            border-bottom: 1px solid #eee;
        }
        
        .detail-row:last-child {
            border-bottom: none;
        }
        
        .detail-label {
            font-weight: 500;
            color: #555;
        }
        
        .detail-value {
            color: #333;
        }
        
        .status-badge {
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 500;
            text-transform: uppercase;
        }
        
        .status-pending {
            background: #fff3cd;
            color: #856404;
        }
        
        .status-confirmed {
            background: #d4edda;
            color: #155724;
        }
        
        .order-items {
            background: #fff;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        
        .section-title {
            font-size: 1.1rem;
            font-weight: 600;
            margin-bottom: 15px;
            color: #333;
        }
        
        .item-row {
            display: flex;
            align-items: center;
            padding: 15px 0;
            border-bottom: 1px solid #eee;
        }
        
        .item-row:last-child {
            border-bottom: none;
        }
        
        .item-image {
            width: 60px;
            height: 60px;
            margin-right: 15px;
            border-radius: 4px;
            overflow: hidden;
        }
        
        .item-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        
        .item-details {
            flex: 1;
        }
        
        .item-name {
            font-weight: 500;
            margin-bottom: 5px;
            color: #333;
        }
        
        .item-price {
            color: #666;
            font-size: 0.9rem;
        }
        
        .item-quantity {
            color: #007bff;
            font-weight: 500;
            margin-left: 15px;
        }
        
        .total-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px 0;
            font-size: 1.2rem;
            font-weight: 600;
            border-top: 2px solid #007bff;
            margin-top: 15px;
        }
        
        .action-buttons {
            display: flex;
            gap: 15px;
            justify-content: center;
            margin-top: 20px;
        }
        
        .btn {
            padding: 12px 24px;
            border: none;
            border-radius: 4px;
            font-size: 1rem;
            font-weight: 500;
            text-decoration: none;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        
        .btn-primary {
            background: #007bff;
            color: white;
        }
        
        .btn-primary:hover {
            background: #0056b3;
            color: white;
            text-decoration: none;
        }
        
        .btn-secondary {
            background: #6c757d;
            color: white;
        }
        
        .btn-secondary:hover {
            background: #545b62;
            color: white;
            text-decoration: none;
        }
        
        .shipping-address {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 4px;
            border-left: 4px solid #007bff;
            white-space: pre-line;
            font-size: 0.9rem;
            color: #555;
        }
        
        @media (max-width: 767px) {
            .action-buttons {
                flex-direction: column;
            }
            
            .detail-row {
                flex-direction: column;
                align-items: flex-start;
                gap: 5px;
            }
        }
    </style>
</head>
<body>
    <?php require_once('../includes/header.php'); ?>
    
    <div class="site-content">
        <?php echo displayAd($conn, 'order_success_top'); ?>
        
        <div class="success-container">
            <!-- Success Message -->
            <div class="success-card">
                <div class="success-icon">
                    <i class="fas fa-check-circle"></i>
                </div>
                <h1 class="success-title">Order Placed Successfully!</h1>
                <p class="success-message">Thank you for your order. We'll process it shortly and keep you updated.</p>
                <div class="order-number">Order #<?php echo htmlspecialchars($order['order_number']); ?></div>
            </div>
            
            <!-- Order Details -->
            <div class="order-details">
                <h2 class="section-title">Order Information</h2>
                <div class="detail-row">
                    <span class="detail-label">Order Date:</span>
                    <span class="detail-value">
                        <?php 
                        if (!empty($order['order_date'])) {
                            echo date('M d, Y g:i A', strtotime($order['order_date'])); 
                        } else {
                            echo 'Not available';
                        }
                        ?>
                    </span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Order Status:</span>
                    <span class="detail-value">
                        <span class="status-badge status-<?php echo $order['status']; ?>">
                            <?php echo ucfirst($order['status']); ?>
                        </span>
                    </span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Payment Method:</span>
                    <span class="detail-value"><?php echo ucwords(str_replace('_', ' ', $order['payment_method'])); ?></span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Payment Status:</span>
                    <span class="detail-value">
                        <span class="status-badge status-<?php echo $order['payment_status'] ?? 'pending'; ?>">
                            <?php echo ucfirst($order['payment_status'] ?? 'pending'); ?>
                        </span>
                    </span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Total Amount:</span>
                    <span class="detail-value"><strong>Br <?php echo number_format($order['total_amount'], 2); ?></strong></span>
                </div>
            </div>
            
            <!-- Shipping Address -->
            <?php if ($order['shipping_address']): ?>
            <div class="order-details">
                <h2 class="section-title">Shipping Address</h2>
                <div class="shipping-address">
                    <?php echo htmlspecialchars($order['shipping_address']); ?>
                </div>
            </div>
            <?php endif; ?>
            
            <!-- Order Items -->
            <div class="order-items">
                <h2 class="section-title">Order Items</h2>
                <?php foreach ($orderItems as $item): ?>
                <div class="item-row">
                    <div class="item-image">
                        <img src="<?php echo htmlspecialchars($item['product_image']); ?>" 
                             alt="<?php echo htmlspecialchars($item['product_name']); ?>"
                             onerror="this.src='../assets/img/placeholder.jpg';">
                    </div>
                    <div class="item-details">
                        <div class="item-name"><?php echo htmlspecialchars($item['product_name']); ?></div>
                        <div class="item-price">Br <?php echo number_format($item['unit_price'], 2); ?> each</div>
                    </div>
                    <div class="item-quantity">Qty: <?php echo $item['quantity']; ?></div>
                </div>
                <?php endforeach; ?>
                
                <div class="total-row">
                    <span>Total Amount:</span>
                    <span>Br <?php echo number_format($order['total_amount'], 2); ?></span>
                </div>
            </div>
            
            <!-- Order Notes -->
            <?php if ($order['notes']): ?>
            <div class="order-details">
                <h2 class="section-title">Order Notes</h2>
                <p><?php echo nl2br(htmlspecialchars($order['notes'])); ?></p>
            </div>
            <?php endif; ?>
            
            <!-- Action Buttons -->
            <div class="action-buttons">
                <a href="products.php" class="btn btn-primary">
                    <i class="fas fa-shopping-cart"></i> Continue Shopping
                </a>
                <a href="../../profile.php" class="btn btn-secondary">
                    <i class="fas fa-user"></i> View My Orders
                </a>
            </div>
        </div>
        
        <?php echo displayAd($conn, 'order_success_bottom'); ?>
    </div>
    
    <?php require_once('../includes/bottom_nav.php'); ?>
    
    <script>
    // Auto-redirect after 30 seconds (optional)
    setTimeout(function() {
        if (confirm('Would you like to continue shopping?')) {
            window.location.href = 'products.php';
        }
    }, 30000);
    </script>
</body>
</html> 