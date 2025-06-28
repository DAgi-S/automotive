<?php
session_start();
define('INCLUDED', true);
require_once('../../includes/config.php');
require_once('../../includes/ad_functions.php');

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: ../../login.php?redirect=ecommerce/pages/checkout.php');
    exit();
}

$conn = getDBConnection();
$user_id = $_SESSION['user_id'];

// Get cart items
$cart_query = "SELECT c.*, p.name, p.image_url as product_image, p.price 
               FROM tbl_cart c 
               LEFT JOIN tbl_products p ON c.product_id = p.id 
               WHERE c.user_id = ?";
$stmt = $conn->prepare($cart_query);
$stmt->execute([$user_id]);
$cartItems = $stmt->fetchAll(PDO::FETCH_ASSOC);

// If cart is empty, redirect to products
if (empty($cartItems)) {
    header('Location: products.php');
    exit();
}

// Calculate total
$total = 0;
foreach ($cartItems as $item) {
    $total += $item['price'] * $item['quantity'];
}

// Get user information
$user_query = "SELECT * FROM tbl_user WHERE id = ?";
$stmt = $conn->prepare($user_query);
$stmt->execute([$user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

// Get user addresses
$address_query = "SELECT * FROM tbl_customer_addresses WHERE user_id = ? ORDER BY is_default DESC, id DESC";
$stmt = $conn->prepare($address_query);
$stmt->execute([$user_id]);
$addresses = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Checkout - Nati Automotive</title>
    <?php require_once('../includes/head.php'); ?>
    <style>
        .checkout-container {
            padding: 15px;
            background: #f8f9fa;
            min-height: calc(100vh - 120px);
        }
        
        .checkout-section {
            background: #fff;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 15px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        
        .section-title {
            font-size: 1.1rem;
            font-weight: 600;
            margin-bottom: 15px;
            color: #333;
            border-bottom: 2px solid #007bff;
            padding-bottom: 8px;
        }
        
        .form-group {
            margin-bottom: 15px;
        }
        
        .form-label {
            display: block;
            margin-bottom: 5px;
            font-weight: 500;
            color: #555;
        }
        
        .form-control {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 0.9rem;
        }
        
        .form-control:focus {
            outline: none;
            border-color: #007bff;
            box-shadow: 0 0 0 2px rgba(0,123,255,0.25);
        }
        
        .address-card {
            border: 1px solid #ddd;
            border-radius: 4px;
            padding: 15px;
            margin-bottom: 10px;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        
        .address-card:hover {
            border-color: #007bff;
            background-color: #f8f9ff;
        }
        
        .address-card.selected {
            border-color: #007bff;
            background-color: #e7f3ff;
        }
        
        .address-card input[type="radio"] {
            margin-right: 10px;
        }
        
        .order-summary {
            background: #f8f9fa;
            border-radius: 4px;
            padding: 15px;
        }
        
        .order-item {
            display: flex;
            align-items: center;
            padding: 10px 0;
            border-bottom: 1px solid #eee;
        }
        
        .order-item:last-child {
            border-bottom: none;
        }
        
        .order-item-image {
            width: 50px;
            height: 50px;
            margin-right: 15px;
            border-radius: 4px;
            overflow: hidden;
        }
        
        .order-item-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        
        .order-item-details {
            flex: 1;
        }
        
        .order-item-name {
            font-size: 0.9rem;
            margin-bottom: 5px;
            color: #333;
        }
        
        .order-item-price {
            font-size: 0.8rem;
            color: #666;
        }
        
        .order-item-quantity {
            font-size: 0.8rem;
            color: #007bff;
            font-weight: 500;
        }
        
        .total-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 0;
            font-weight: 600;
            border-top: 2px solid #007bff;
            margin-top: 10px;
        }
        
        .payment-method {
            display: flex;
            align-items: center;
            padding: 15px;
            border: 1px solid #ddd;
            border-radius: 4px;
            margin-bottom: 10px;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        
        .payment-method:hover {
            border-color: #007bff;
            background-color: #f8f9ff;
        }
        
        .payment-method.selected {
            border-color: #007bff;
            background-color: #e7f3ff;
        }
        
        .payment-method input[type="radio"] {
            margin-right: 15px;
        }
        
        .payment-icon {
            font-size: 1.5rem;
            margin-right: 15px;
            color: #007bff;
        }
        
        .place-order-btn {
            width: 100%;
            padding: 15px;
            background: #28a745;
            color: white;
            border: none;
            border-radius: 4px;
            font-size: 1.1rem;
            font-weight: 600;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }
        
        .place-order-btn:hover {
            background: #218838;
        }
        
        .place-order-btn:disabled {
            background: #6c757d;
            cursor: not-allowed;
        }
        
        .add-address-btn {
            background: #007bff;
            color: white;
            border: none;
            padding: 8px 15px;
            border-radius: 4px;
            font-size: 0.9rem;
            cursor: pointer;
            margin-top: 10px;
        }
        
        .add-address-btn:hover {
            background: #0056b3;
        }
        
        .address-form {
            display: none;
            margin-top: 15px;
            padding: 15px;
            background: #f8f9fa;
            border-radius: 4px;
        }
        
        .form-row {
            display: flex;
            gap: 15px;
        }
        
        .form-row .form-group {
            flex: 1;
        }
        
        @media (max-width: 767px) {
            .form-row {
                flex-direction: column;
                gap: 0;
            }
        }
    </style>
</head>
<body class="ecommerce-wrapper">
    <?php require_once('../includes/header.php'); ?>
    
    <div class="site-content">
        <?php echo displayAd($conn, 'checkout_top'); ?>
        
        <div class="checkout-container">
            <h1 class="h5 mb-4">Checkout</h1>
            
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
                    
                    <!-- New Address Form -->
                    <div id="addressForm" class="address-form" style="display: none;">
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
    
    <script>
    // Prevent default form validation errors
    document.addEventListener('DOMContentLoaded', function() {
        // Disable HTML5 validation to prevent console errors
        document.getElementById('checkoutForm').setAttribute('novalidate', 'novalidate');
        
        // Fix image loading errors
        document.querySelectorAll('img').forEach(img => {
            img.addEventListener('error', function() {
                if (this.src !== '../assets/img/placeholder.jpg') {
                    this.src = '../assets/img/placeholder.jpg';
                }
            });
        });
    });
    
    function selectAddress(element, addressId) {
        // Remove selected class from all address cards
        document.querySelectorAll('.address-card').forEach(card => {
            card.classList.remove('selected');
        });
        
        // Add selected class to clicked card
        element.classList.add('selected');
        
        // Check the radio button
        element.querySelector('input[type="radio"]').checked = true;
    }
    
    function selectPayment(element, method) {
        // Remove selected class from all payment methods
        document.querySelectorAll('.payment-method').forEach(method => {
            method.classList.remove('selected');
        });
        
        // Add selected class to clicked method
        element.classList.add('selected');
        
        // Check the radio button
        element.querySelector('input[type="radio"]').checked = true;
    }
    
    function toggleAddressForm() {
        const form = document.getElementById('addressForm');
        const isVisible = form.style.display === 'block';
        
        if (isVisible) {
            form.style.display = 'none';
        } else {
            form.style.display = 'block';
        }
    }
    
    // Handle form submission with custom validation
    document.getElementById('checkoutForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        // Custom validation
        const addressForm = document.getElementById('addressForm');
        const isNewAddressVisible = addressForm.style.display === 'block';
        const hasExistingAddress = document.querySelector('input[name="shipping_address_id"]:checked');
        
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
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
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
            alert('Error placing order: ' + error.message);
            btn.disabled = false;
            btn.innerHTML = '<i class="fas fa-check-circle"></i> Place Order - Br <?php echo number_format($total, 2); ?>';
        });
    });
    </script>
</body>
</html> 