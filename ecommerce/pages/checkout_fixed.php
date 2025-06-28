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

// Get user information
$user_query = "SELECT * FROM tbl_user WHERE id = ?";
$stmt = $conn->prepare($user_query);
$stmt->execute([$user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    header('Location: ../../login.php');
    exit();
}

// Get user's saved addresses
$addresses_query = "SELECT * FROM tbl_customer_addresses WHERE user_id = ? ORDER BY is_default DESC, created_at DESC";
$stmt = $conn->prepare($addresses_query);
$stmt->execute([$user_id]);
$addresses = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get cart items
$cart_query = "SELECT c.*, p.name, p.image_url as product_image, p.price 
               FROM tbl_cart c 
               LEFT JOIN tbl_products p ON c.product_id = p.id 
               WHERE c.user_id = ?";
$stmt = $conn->prepare($cart_query);
$stmt->execute([$user_id]);
$cartItems = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (empty($cartItems)) {
    header('Location: cart.php');
    exit();
}

// Calculate total
$total = 0;
foreach ($cartItems as $item) {
    $total += $item['price'] * $item['quantity'];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Checkout - Nati Automotive</title>
    <?php require_once('../includes/head.php'); ?>
    <style>
        /* Fix for validation errors */
        .checkout-container { padding: 20px; }
        .address-form { display: none !important; }
        .address-form.show { display: block !important; }
    </style>
</head>
<body>
    <?php require_once('../includes/header.php'); ?>
    
    <div class="site-content">
        <?php 
        $conn = getDBConnection();
        echo displayAd($conn, 'checkout_top'); 
        ?>
        
        <div class="checkout-container">
            <h1 class="h5 mb-4">Checkout</h1>
            
            <!-- FIXED FORM WITH NOVALIDATE -->
            <form id="checkoutForm" method="POST" action="../ajax/process_order.php" novalidate>
                <!-- Shipping Address Section -->
                <div class="checkout-section">
                    <h2 class="section-title">Shipping Address</h2>
                    
                    <?php if (!empty($addresses)): ?>
                        <?php foreach ($addresses as $index => $address): ?>
                        <div class="address-card <?php echo $index === 0 ? 'selected' : ''; ?>" 
                             onclick="selectAddress(this, <?php echo $address['id']; ?>)">
                            <input type="radio" name="shipping_address_id" value="<?php echo $address['id']; ?>" 
                                   <?php echo $index === 0 ? 'checked' : ''; ?>>
                            <strong><?php echo htmlspecialchars($address['full_name']); ?></strong><br>
                            <?php echo nl2br(htmlspecialchars($address['full_address'])); ?><br>
                            <?php if ($address['phone']): ?>
                                Phone: <?php echo htmlspecialchars($address['phone']); ?>
                            <?php endif; ?>
                        </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                    
                    <button type="button" class="add-address-btn" onclick="toggleAddressForm()">
                        <i class="fas fa-plus"></i> Add New Address
                    </button>
                    
                    <!-- FIXED ADDRESS FORM - NO REQUIRED ATTRIBUTES -->
                    <div id="addressForm" class="address-form">
                        <h3>Add New Address</h3>
                        <div class="form-group">
                            <label class="form-label">Full Name *</label>
                            <input type="text" name="full_name" id="full_name" class="form-control">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Phone Number *</label>
                            <input type="tel" name="phone" id="phone" class="form-control">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Full Address *</label>
                            <textarea name="full_address" id="full_address" class="form-control" rows="3" 
                                      placeholder="Enter your complete address including street, city, state, and postal code..."></textarea>
                        </div>
                        <div class="form-group">
                            <label>
                                <input type="checkbox" name="save_address" value="1"> Save this address for future orders
                            </label>
                        </div>
                    </div>
                </div>
                
                <!-- Payment Method Section -->
                <div class="checkout-section">
                    <h2 class="section-title">Payment Method</h2>
                    
                    <div class="payment-method selected" onclick="selectPayment(this, 'cash_on_delivery')">
                        <input type="radio" name="payment_method" value="cash_on_delivery" checked>
                        <i class="fas fa-money-bill-wave payment-icon"></i>
                        <div>
                            <strong>Cash on Delivery</strong><br>
                            <small>Pay when you receive your order</small>
                        </div>
                    </div>
                    
                    <div class="payment-method" onclick="selectPayment(this, 'bank_transfer')">
                        <input type="radio" name="payment_method" value="bank_transfer">
                        <i class="fas fa-university payment-icon"></i>
                        <div>
                            <strong>Bank Transfer</strong><br>
                            <small>Transfer to our bank account</small>
                        </div>
                    </div>
                    
                    <div class="payment-method" onclick="selectPayment(this, 'mobile_money')">
                        <input type="radio" name="payment_method" value="mobile_money">
                        <i class="fas fa-mobile-alt payment-icon"></i>
                        <div>
                            <strong>Mobile Money</strong><br>
                            <small>Pay via mobile money transfer</small>
                        </div>
                    </div>
                </div>
                
                <!-- Order Summary Section -->
                <div class="checkout-section">
                    <h2 class="section-title">Order Summary</h2>
                    
                    <div class="order-summary">
                        <?php foreach ($cartItems as $item): ?>
                        <div class="order-item">
                            <div class="order-item-image">
                                <img src="<?php echo htmlspecialchars($item['product_image']); ?>" 
                                     alt="<?php echo htmlspecialchars($item['name']); ?>"
                                     onerror="this.src='../assets/img/placeholder.jpg';">
                            </div>
                            <div class="order-item-details">
                                <div class="order-item-name"><?php echo htmlspecialchars($item['name']); ?></div>
                                <div class="order-item-price">Br <?php echo number_format($item['price'], 2); ?></div>
                            </div>
                            <div class="order-item-quantity">Qty: <?php echo $item['quantity']; ?></div>
                        </div>
                        <?php endforeach; ?>
                        
                        <div class="total-row">
                            <span>Total Amount:</span>
                            <span>Br <?php echo number_format($total, 2); ?></span>
                        </div>
                    </div>
                </div>
                
                <!-- Order Notes Section -->
                <div class="checkout-section">
                    <h2 class="section-title">Order Notes (Optional)</h2>
                    <div class="form-group">
                        <textarea name="order_notes" class="form-control" rows="3" 
                                  placeholder="Any special instructions for your order..."></textarea>
                    </div>
                </div>
                
                <!-- Place Order Button -->
                <div class="checkout-section">
                    <button type="submit" class="place-order-btn" id="placeOrderBtn">
                        <i class="fas fa-check-circle"></i> Place Order - Br <?php echo number_format($total, 2); ?>
                    </button>
                </div>
            </form>
        </div>
        
        <?php echo displayAd($conn, 'checkout_bottom'); ?>
    </div>
    
    <?php require_once('../includes/bottom_nav.php'); ?>
    
    <!-- FIXED JAVASCRIPT - NO CONSOLE ERRORS -->
    <script>
    // Prevent all validation errors
    document.addEventListener('DOMContentLoaded', function() {
        // Fix image errors
        document.querySelectorAll('img').forEach(img => {
            img.addEventListener('error', function() {
                if (this.src.indexOf('placeholder.jpg') === -1) {
                    this.src = '../assets/img/placeholder.jpg';
                }
            });
        });
    });
    
    function selectAddress(element, addressId) {
        document.querySelectorAll('.address-card').forEach(card => {
            card.classList.remove('selected');
        });
        element.classList.add('selected');
        element.querySelector('input[type="radio"]').checked = true;
    }
    
    function selectPayment(element, method) {
        document.querySelectorAll('.payment-method').forEach(method => {
            method.classList.remove('selected');
        });
        element.classList.add('selected');
        element.querySelector('input[type="radio"]').checked = true;
    }
    
    function toggleAddressForm() {
        const form = document.getElementById('addressForm');
        if (form.classList.contains('show')) {
            form.classList.remove('show');
        } else {
            form.classList.add('show');
        }
    }
    
    // Form submission with proper validation
    document.getElementById('checkoutForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        // Custom validation
        const hasExistingAddress = document.querySelector('input[name="shipping_address_id"]:checked');
        const addressForm = document.getElementById('addressForm');
        const isNewAddressVisible = addressForm.classList.contains('show');
        
        if (!hasExistingAddress && isNewAddressVisible) {
            const fullName = document.getElementById('full_name').value.trim();
            const phone = document.getElementById('phone').value.trim();
            const fullAddress = document.getElementById('full_address').value.trim();
            
            if (!fullName || !phone || !fullAddress) {
                alert('Please fill in all required address fields');
                return;
            }
        } else if (!hasExistingAddress && !isNewAddressVisible) {
            alert('Please select an address or add a new one');
            return;
        }
        
        const btn = document.getElementById('placeOrderBtn');
        btn.disabled = true;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Processing...';
        
        const formData = new FormData(this);
        
        fetch('../ajax/process_order.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Order placed successfully! Order Number: ' + data.order_number);
                window.location.href = 'order_success.php?order=' + data.order_number;
            } else {
                alert(data.message || 'Error placing order');
                btn.disabled = false;
                btn.innerHTML = '<i class="fas fa-check-circle"></i> Place Order - Br <?php echo number_format($total, 2); ?>';
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error placing order');
            btn.disabled = false;
            btn.innerHTML = '<i class="fas fa-check-circle"></i> Place Order - Br <?php echo number_format($total, 2); ?>';
        });
    });
    </script>
</body>
</html> 