<?php
session_start();
define('INCLUDED', true);
require_once('../../includes/config.php');
require_once('../../includes/ad_functions.php');

// Check if user is logged in
$cartItems = [];
$total = 0;

if (isset($_SESSION['user_id'])) {
    $conn = getDBConnection();
    $user_id = $_SESSION['user_id'];
    
    // Get cart items from database
    $cart_query = "SELECT c.*, p.name, p.image_url as image, p.price 
                   FROM tbl_cart c 
                   LEFT JOIN tbl_products p ON c.product_id = p.id 
                   WHERE c.user_id = ?";
    $stmt = $conn->prepare($cart_query);
    $stmt->execute([$user_id]);
    $cartItems = $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Shopping Cart - Nati Automotive</title>
    <?php require_once('../includes/head.php'); ?>
    <style>
        .cart-container {
            padding: 15px;
            background: #fff;
            margin-bottom: 100px; /* Space for cart summary */
        }
        .cart-item {
            display: flex;
            padding: 15px;
            border-bottom: 1px solid #eee;
            position: relative;
        }
        .cart-item:last-child {
            border-bottom: none;
        }
        .cart-item-image {
            width: 80px;
            height: 80px;
            margin-right: 15px;
        }
        .cart-item-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            border-radius: 4px;
        }
        .cart-item-details {
            flex: 1;
        }
        .cart-item-title {
            font-size: 0.95rem;
            margin: 0 0 5px 0;
            color: #333;
        }
        .cart-item-price {
            font-size: 1rem;
            color: #ff4757;
            font-weight: 600;
            margin: 5px 0;
        }
        .cart-item-actions {
            display: flex;
            align-items: center;
            margin-top: 10px;
        }
        .quantity-control {
            display: flex;
            align-items: center;
            border: 1px solid #ddd;
            border-radius: 4px;
            overflow: hidden;
        }
        .quantity-btn {
            background: #f8f9fa;
            border: none;
            padding: 5px 12px;
            font-size: 1rem;
            color: #666;
        }
        .quantity-input {
            width: 40px;
            text-align: center;
            border: none;
            border-left: 1px solid #ddd;
            border-right: 1px solid #ddd;
            padding: 5px 0;
        }
        .remove-btn {
            margin-left: 15px;
            color: #ff4757;
            background: none;
            border: none;
            padding: 5px;
            font-size: 0.9rem;
        }
        .cart-summary {
            background: #fff;
            padding: 15px;
            position: fixed;
            bottom: 60px; /* Position above bottom navigation */
            left: 0;
            right: 0;
            box-shadow: 0 -2px 10px rgba(0,0,0,0.1);
            z-index: 1000;
            border-top: 1px solid #eee;
        }
        .cart-total {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
        }
        .cart-total-label {
            font-size: 1rem;
            color: #666;
        }
        .cart-total-amount {
            font-size: 1.2rem;
            font-weight: 600;
            color: #333;
        }
        .checkout-btn {
            display: block;
            width: 100%;
            padding: 12px;
            background: #ff4757;
            color: white;
            border: none;
            border-radius: 4px;
            font-size: 1rem;
            font-weight: 500;
            text-align: center;
            text-decoration: none;
        }
        .empty-cart {
            text-align: center;
            padding: 30px 15px;
        }
        .empty-cart i {
            font-size: 3rem;
            color: #ddd;
            margin-bottom: 15px;
        }
        .empty-cart p {
            color: #666;
            margin-bottom: 15px;
        }
        .continue-shopping {
            color: #ff4757;
            text-decoration: none;
            font-weight: 500;
        }
    </style>
</head>
<body class="ecommerce-wrapper">
    <?php require_once('../includes/header.php'); ?>

    <div class="site-content">
        <?php 
        // Get database connection for ads
        $conn = getDBConnection();
        echo displayAd($conn, 'cart_top'); 
        ?>

        <div class="cart-container">
            <h1 class="h5 mb-4">Shopping Cart</h1>

            <?php if (empty($cartItems)): ?>
            <div class="empty-cart">
                <i class="fas fa-shopping-cart"></i>
                <p>Your cart is empty</p>
                <a href="products.php" class="continue-shopping">Continue Shopping</a>
            </div>
            <?php else: ?>
                <?php foreach ($cartItems as $item): 
                    $itemTotal = $item['price'] * $item['quantity'];
                    $total += $itemTotal;
                ?>
                <div class="cart-item" data-id="<?php echo $item['id']; ?>">
                    <div class="cart-item-image">
                        <img src="<?php echo htmlspecialchars($item['image']); ?>" 
                             alt="<?php echo htmlspecialchars($item['name']); ?>"
                             onerror="this.src='../assets/img/placeholder.jpg';">
                    </div>
                    <div class="cart-item-details">
                        <h3 class="cart-item-title"><?php echo htmlspecialchars($item['name']); ?></h3>
                        <div class="cart-item-price">Br <?php echo number_format($item['price'], 2); ?></div>
                        <div class="cart-item-actions">
                            <div class="quantity-control">
                                <button class="quantity-btn minus" onclick="updateQuantity(<?php echo $item['id']; ?>, 'decrease')">-</button>
                                <input type="number" class="quantity-input" value="<?php echo $item['quantity']; ?>" 
                                       min="1" max="99" onchange="updateQuantity(<?php echo $item['id']; ?>, 'set', this.value)">
                                <button class="quantity-btn plus" onclick="updateQuantity(<?php echo $item['id']; ?>, 'increase')">+</button>
                            </div>
                            <button class="remove-btn" onclick="removeItem(<?php echo $item['id']; ?>)">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

        <?php if (!empty($cartItems)): ?>
        <div class="cart-summary">
            <div class="cart-total">
                <span class="cart-total-label">Total</span>
                <span class="cart-total-amount">Br <?php echo number_format($total, 2); ?></span>
            </div>
            <a href="checkout.php" class="checkout-btn">Proceed to Checkout</a>
        </div>
        <?php endif; ?>

        <?php echo displayAd($conn, 'cart_bottom'); ?>
    </div>

    <?php require_once('../includes/bottom_nav.php'); ?>

    <script>
    function updateQuantity(productId, action, value = null) {
        let url = '../ajax/update_cart.php';
        let data = {
            product_id: productId,
            action: action
        };
        
        if (value !== null) {
            data.value = parseInt(value);
        }

        fetch(url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(data)
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert(data.message || 'Error updating cart');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error updating cart');
        });
    }

    function removeItem(productId) {
        if (confirm('Are you sure you want to remove this item?')) {
            fetch('../ajax/update_cart.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    product_id: productId,
                    action: 'remove'
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                } else {
                    alert(data.message || 'Error removing item');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error removing item');
            });
        }
    }

    // Update cart badge count
    function updateCartBadge() {
        const cartCount = document.querySelector('.cart-count');
        if (cartCount) {
            fetch('../ajax/get_cart_count.php')
            .then(response => response.json())
            .then(data => {
                cartCount.textContent = data.count;
            })
            .catch(error => console.error('Error:', error));
        }
    }

    // Call updateCartBadge when page loads
    document.addEventListener('DOMContentLoaded', updateCartBadge);
    </script>
</body>
</html> 