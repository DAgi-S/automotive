<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: /automotive/login.php");
    exit();
}

// Define INCLUDED constant for security
define('INCLUDED', true);

// Include necessary files
require_once '../includes/db_con.php';
require_once '../includes/head.php';
require_once '../includes/header.php';

// Initialize DB_Ecom class
$db_ecom = new DB_Ecom();

// Get user profile data
$user_id = $_SESSION['user_id'];
$profile = $db_ecom->get_user_profile($user_id);
$orders = $db_ecom->get_user_orders($user_id);
$cart_items = $db_ecom->get_cart_items($user_id);
$wishlist_items = $db_ecom->get_wishlist_items($user_id);
?>

<div class="container mt-4">
    <div class="row">
        <!-- Profile Information -->
        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Profile Information</h5>
                    <p class="card-text">
                        <strong>Name:</strong> <?php echo htmlspecialchars($profile['name']); ?><br>
                        <strong>Email:</strong> <?php echo htmlspecialchars($profile['email']); ?><br>
                        <strong>Phone:</strong> <?php echo htmlspecialchars($profile['phone']); ?><br>
                    </p>
                </div>
            </div>
        </div>

        <!-- Orders and Items -->
        <div class="col-md-8">
            <ul class="nav nav-tabs" id="profileTabs" role="tablist">
                <li class="nav-item">
                    <a class="nav-link active" id="orders-tab" data-bs-toggle="tab" href="#orders" role="tab">Orders</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="cart-tab" data-bs-toggle="tab" href="#cart" role="tab">Cart</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="wishlist-tab" data-bs-toggle="tab" href="#wishlist" role="tab">Wishlist</a>
                </li>
            </ul>

            <div class="tab-content mt-3" id="profileTabsContent">
                <!-- Orders Tab -->
                <div class="tab-pane fade show active" id="orders" role="tabpanel">
                    <?php if (empty($orders)): ?>
                        <p>No orders found.</p>
                    <?php else: ?>
                        <?php foreach ($orders as $order): ?>
                            <div class="card mb-3">
                                <div class="card-body">
                                    <h6 class="card-subtitle mb-2 text-muted">Order #<?php echo htmlspecialchars($order['order_id']); ?></h6>
                                    <p class="card-text">
                                        <strong>Date:</strong> <?php echo htmlspecialchars($order['order_date']); ?><br>
                                        <strong>Status:</strong> <?php echo htmlspecialchars($order['status']); ?><br>
                                        <strong>Total:</strong> $<?php echo number_format($order['total'], 2); ?>
                                    </p>
                                    <?php if ($order['status'] == 'Pending'): ?>
                                        <a href="cancel_order.php?order_id=<?php echo $order['order_id']; ?>" 
                                           class="btn btn-danger btn-sm"
                                           onclick="return confirm('Are you sure you want to cancel this order?')">
                                            Cancel Order
                                        </a>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>

                <!-- Cart Tab -->
                <div class="tab-pane fade" id="cart" role="tabpanel">
                    <?php if (empty($cart_items)): ?>
                        <p>Your cart is empty.</p>
                    <?php else: ?>
                        <?php foreach ($cart_items as $item): ?>
                            <div class="card mb-3">
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-8">
                                            <h6 class="card-title"><?php echo htmlspecialchars($item['product_name']); ?></h6>
                                            <p class="card-text">
                                                <strong>Price:</strong> $<?php echo number_format($item['price'], 2); ?><br>
                                                <strong>Quantity:</strong> <?php echo $item['quantity']; ?>
                                            </p>
                                        </div>
                                        <div class="col-md-4 text-end">
                                            <a href="remove_from_cart.php?cart_id=<?php echo $item['cart_id']; ?>" 
                                               class="btn btn-danger btn-sm"
                                               onclick="return confirm('Remove this item from cart?')">
                                                Remove
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>

                <!-- Wishlist Tab -->
                <div class="tab-pane fade" id="wishlist" role="tabpanel">
                    <?php if (empty($wishlist_items)): ?>
                        <p>Your wishlist is empty.</p>
                    <?php else: ?>
                        <?php foreach ($wishlist_items as $item): ?>
                            <div class="card mb-3">
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-8">
                                            <h6 class="card-title"><?php echo htmlspecialchars($item['product_name']); ?></h6>
                                            <p class="card-text">
                                                <strong>Price:</strong> $<?php echo number_format($item['price'], 2); ?>
                                            </p>
                                        </div>
                                        <div class="col-md-4 text-end">
                                            <a href="add_to_cart.php?product_id=<?php echo $item['product_id']; ?>" 
                                               class="btn btn-primary btn-sm">
                                                Add to Cart
                                            </a>
                                            <a href="remove_from_wishlist.php?wishlist_id=<?php echo $item['wishlist_id']; ?>" 
                                               class="btn btn-danger btn-sm"
                                               onclick="return confirm('Remove this item from wishlist?')">
                                                Remove
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once '../includes/bottom_nav.php'; ?> 