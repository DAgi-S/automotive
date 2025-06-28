<?php
// Prevent direct access to this file
if (!defined('INCLUDED')) {
    header("HTTP/1.0 403 Forbidden");
    exit('Direct access forbidden.');
}

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Initialize cart count
$cart_count = 0;
if (isset($_SESSION['id'])) {
    include_once("db_con.php");
    $db = new DB_Ecom();
    $cart_items = $db->get_user_cart($_SESSION['id']);
    $cart_count = count($cart_items);
}
?>

<!-- Top Header -->
<div class="top-header fixed-top">
    <div class="top-header-content">
        <div class="brand-section">
            <button class="mobile-menu-btn">
                <i class="fas fa-bars"></i>
            </button>
            <div class="brand-logo">
                <img src="/automotive/ecommerce/assets/img/logo.png" alt="Nati Automotive">
            </div>
            <a href="../../index.php" class="brand-name">Nati Automotive</a>
        </div>
        <div class="header-actions">
            <?php if (isset($_SESSION['id'])): ?>
                <a href="cart.php" class="header-icon position-relative">
                    <i class="fas fa-shopping-cart"></i>
                    <?php if ($cart_count > 0): ?>
                    <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger cart-badge">
                        <?php echo $cart_count; ?>
                    </span>
                    <?php endif; ?>
                </a>
                <a href="notifications.php" class="header-icon">
                    <i class="fas fa-bell"></i>
                </a>
            <?php else: ?>
                <a href="../../login.php" class="header-icon">
                    <i class="fas fa-user"></i>
                </a>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Desktop Navigation (hidden on mobile) -->
<nav class="navbar navbar-expand-md navbar-light bg-white shadow-sm desktop-nav" style="margin-top: 56px;">
    <div class="container">
        <a class="navbar-brand" href="../../index.php">
            <img src="/automotive/assets/img/logo.png" alt="Logo" height="40">
        </a>
        
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto">
                <li class="nav-item">
                    <a class="nav-link" href="../../index.php">Home</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="products.php">Shop</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="categories.php">Categories</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="../../about.php">About</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="../../contact.php">Contact</a>
                </li>
            </ul>
            
            <ul class="navbar-nav ms-auto">
                <li class="nav-item">
                    <form class="d-flex" action="search.php" method="GET">
                        <input class="form-control me-2" type="search" name="q" placeholder="Search products...">
                        <button class="btn btn-outline-primary" type="submit">
                            <i class="fas fa-search"></i>
                        </button>
                    </form>
                </li>
                <li class="nav-item ms-2">
                    <a class="nav-link position-relative" href="cart.php">
                        <i class="fas fa-shopping-cart"></i>
                        <?php if ($cart_count > 0): ?>
                        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger cart-badge">
                            <?php echo $cart_count; ?>
                        </span>
                        <?php endif; ?>
                    </a>
                </li>
                <li class="nav-item ms-2">
                    <a class="nav-link" href="wishlist.php">
                        <i class="fas fa-heart"></i>
                    </a>
                </li>
                <?php if (isset($_SESSION['id'])): ?>
                <li class="nav-item ms-2 dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown">
                        <i class="fas fa-user"></i>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><a class="dropdown-item" href="profile.php">My Profile</a></li>
                        <li><a class="dropdown-item" href="orders.php">My Orders</a></li>
                        <li><a class="dropdown-item" href="wishlist.php">My Wishlist</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item" href="../../logout.php">Logout</a></li>
                    </ul>
                </li>
                <?php else: ?>
                <li class="nav-item ms-2">
                    <a class="btn btn-primary" href="../../login.php">Login</a>
                </li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>

 