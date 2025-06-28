<?php
if (!defined('INCLUDED')) {
    exit;
}

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Include database and ad functions
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/ad_functions.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($page_title) ? $page_title . ' - ' : ''; ?>Nati Automotive</title>
    
    <!-- SEO Meta Tags -->
    <meta name="description" content="Nati Automotive - Your trusted partner for quality auto parts and professional automotive services in Ethiopia.">
    <meta name="keywords" content="automotive, car parts, auto repair, Ethiopia, Addis Ababa, car service">
    <meta name="author" content="Nati Automotive">
    <meta name="robots" content="index, follow">
    
    <!-- Open Graph Meta Tags -->
    <meta property="og:title" content="<?php echo isset($page_title) ? $page_title . ' - ' : ''; ?>Nati Automotive">
    <meta property="og:description" content="Your trusted partner for quality auto parts and professional automotive services.">
    <meta property="og:type" content="website">
    <meta property="og:url" content="<?php echo 'https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']; ?>">
    <meta property="og:image" content="assets/images/logo/logo.png">
    
    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="assets/images/logo/favicon.ico">
    <link rel="apple-touch-icon" href="assets/images/logo/apple-touch-icon.png">
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome - Latest Version -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Custom CSS for Enhanced Header -->
    <style>
    /* Enhanced Header Styles */
    :root {
        --primary-color: #007bff;
        --primary-dark: #0056b3;
        --secondary-color: #6c757d;
        --success-color: #28a745;
        --warning-color: #ffc107;
        --danger-color: #dc3545;
        --dark-color: #2c3e50;
        --light-color: #f8f9fa;
        --white: #ffffff;
        --border-radius: 10px;
        --box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        --transition: all 0.3s ease;
    }
    
    * {
        font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
    }
    
    /* Enhanced Navigation */
    .navbar {
        background: linear-gradient(135deg, var(--dark-color) 0%, #34495e 100%) !important;
        box-shadow: 0 2px 20px rgba(0, 0, 0, 0.15);
        backdrop-filter: blur(10px);
        border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        transition: var(--transition);
        padding: 0.75rem 0;
    }
    
    .navbar.scrolled {
        background: rgba(44, 62, 80, 0.95) !important;
        padding: 0.5rem 0;
    }
    
    .navbar-brand {
        font-weight: 700;
        font-size: 1.5rem;
        color: var(--white) !important;
        transition: var(--transition);
        display: flex;
        align-items: center;
    }
    
    .navbar-brand:hover {
        transform: scale(1.05);
        color: var(--primary-color) !important;
    }
    
    .logo-container {
        position: relative;
        display: flex;
        align-items: center;
        transition: var(--transition);
    }
    
    .logo-container img {
        filter: brightness(1.1);
        transition: var(--transition);
    }
    
    .logo-container:hover img {
        filter: brightness(1.3) drop-shadow(0 0 10px rgba(0, 123, 255, 0.5));
    }
    
    .logo-container.img-placeholder::before {
        content: "🚗";
        font-size: 24px;
        margin-right: 8px;
    }
    
    .logo-container.img-placeholder::after {
        content: "Nati Automotive";
        font-weight: 600;
        color: var(--white);
    }
    
    /* Enhanced Navigation Links */
    .navbar-nav .nav-link {
        color: rgba(255, 255, 255, 0.9) !important;
        font-weight: 500;
        padding: 0.75rem 1rem !important;
        margin: 0 0.25rem;
        border-radius: var(--border-radius);
        transition: var(--transition);
        position: relative;
        overflow: hidden;
    }
    
    .navbar-nav .nav-link::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.1), transparent);
        transition: left 0.5s ease;
    }
    
    .navbar-nav .nav-link:hover::before {
        left: 100%;
    }
    
    .navbar-nav .nav-link:hover {
        color: var(--white) !important;
        background: rgba(0, 123, 255, 0.2);
        transform: translateY(-2px);
        box-shadow: 0 4px 15px rgba(0, 123, 255, 0.3);
    }
    
    .navbar-nav .nav-link.active {
        color: var(--white) !important;
        background: var(--primary-color);
        box-shadow: 0 4px 15px rgba(0, 123, 255, 0.4);
    }
    
    .navbar-nav .nav-link i {
        margin-right: 0.5rem;
        transition: var(--transition);
    }
    
    .navbar-nav .nav-link:hover i {
        transform: scale(1.1);
    }
    
    /* Enhanced Navbar Toggler */
    .navbar-toggler {
        border: 2px solid rgba(255, 255, 255, 0.3);
        border-radius: var(--border-radius);
        padding: 0.5rem;
        transition: var(--transition);
    }
    
    .navbar-toggler:hover {
        border-color: var(--primary-color);
        background: rgba(0, 123, 255, 0.1);
    }
    
    .navbar-toggler-icon {
        background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 30 30'%3e%3cpath stroke='rgba%28255, 255, 255, 0.9%29' stroke-linecap='round' stroke-miterlimit='10' stroke-width='2' d='m4 7h22M4 15h22M4 23h22'/%3e%3c/svg%3e");
    }
    
    /* Enhanced Action Buttons */
    .header-actions {
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }
    
    .cart-btn {
        position: relative;
        background: rgba(255, 255, 255, 0.1);
        border: 2px solid rgba(255, 255, 255, 0.2);
        color: var(--white);
        border-radius: var(--border-radius);
        padding: 0.5rem 1rem;
        transition: var(--transition);
        backdrop-filter: blur(10px);
    }
    
    .cart-btn:hover {
        background: var(--primary-color);
        border-color: var(--primary-color);
        color: var(--white);
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(0, 123, 255, 0.3);
    }
    
    .cart-count {
        position: absolute;
        top: -8px;
        right: -8px;
        background: var(--danger-color);
        color: var(--white);
        border-radius: 50%;
        width: 22px;
        height: 22px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.75rem;
        font-weight: 600;
        border: 2px solid var(--white);
        animation: pulse 2s infinite;
    }
    
    @keyframes pulse {
        0% { transform: scale(1); }
        50% { transform: scale(1.1); }
        100% { transform: scale(1); }
    }
    
    /* Enhanced Notification Bell */
    .notification-bell {
        position: relative;
        background: rgba(255, 255, 255, 0.1);
        border: 2px solid rgba(255, 255, 255, 0.2);
        color: var(--white);
        border-radius: 50%;
        width: 45px;
        height: 45px;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: var(--transition);
        cursor: pointer;
    }
    
    .notification-bell:hover {
        background: var(--warning-color);
        border-color: var(--warning-color);
        color: var(--dark-color);
        transform: scale(1.1);
        box-shadow: 0 8px 25px rgba(255, 193, 7, 0.3);
    }
    
    .notification-bell.has-notifications {
        animation: shake 1s ease-in-out infinite;
    }
    
    @keyframes shake {
        0%, 100% { transform: translateX(0); }
        25% { transform: translateX(-2px); }
        75% { transform: translateX(2px); }
    }
    
    .notification-badge {
        position: absolute;
        top: -5px;
        right: -5px;
        background: var(--danger-color);
        color: var(--white);
        border-radius: 50%;
        width: 18px;
        height: 18px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.7rem;
        font-weight: 600;
        border: 2px solid var(--white);
    }
    
    /* Enhanced User Dropdown */
    .user-dropdown .dropdown-toggle {
        background: rgba(255, 255, 255, 0.1);
        border: 2px solid rgba(255, 255, 255, 0.2);
        color: var(--white);
        border-radius: var(--border-radius);
        padding: 0.5rem 1rem;
        transition: var(--transition);
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }
    
    .user-dropdown .dropdown-toggle:hover {
        background: var(--success-color);
        border-color: var(--success-color);
        color: var(--white);
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(40, 167, 69, 0.3);
    }
    
    .user-dropdown .dropdown-toggle::after {
        margin-left: 0.5rem;
    }
    
    .user-dropdown .dropdown-menu {
        background: var(--white);
        border: none;
        border-radius: var(--border-radius);
        box-shadow: var(--box-shadow);
        padding: 0.5rem 0;
        margin-top: 0.5rem;
        min-width: 200px;
    }
    
    .user-dropdown .dropdown-item {
        padding: 0.75rem 1.5rem;
        color: var(--dark-color);
        transition: var(--transition);
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }
    
    .user-dropdown .dropdown-item:hover {
        background: linear-gradient(135deg, rgba(0, 123, 255, 0.1), rgba(0, 123, 255, 0.05));
        color: var(--primary-color);
        transform: translateX(5px);
    }
    
    .user-dropdown .dropdown-item i {
        width: 16px;
        text-align: center;
    }
    
    .user-dropdown .dropdown-divider {
        margin: 0.5rem 1rem;
        border-color: rgba(0, 0, 0, 0.1);
    }
    
    /* Auth Buttons */
    .auth-buttons {
        display: flex;
        gap: 0.5rem;
    }
    
    .auth-buttons .btn {
        border-radius: var(--border-radius);
        padding: 0.5rem 1.25rem;
        font-weight: 500;
        transition: var(--transition);
        border: 2px solid;
    }
    
    .auth-buttons .btn-outline-light {
        border-color: rgba(255, 255, 255, 0.3);
        color: var(--white);
        background: rgba(255, 255, 255, 0.1);
    }
    
    .auth-buttons .btn-outline-light:hover {
        background: var(--white);
        color: var(--dark-color);
        border-color: var(--white);
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(255, 255, 255, 0.3);
    }
    
    .auth-buttons .btn-primary {
        background: var(--primary-color);
        border-color: var(--primary-color);
    }
    
    .auth-buttons .btn-primary:hover {
        background: var(--primary-dark);
        border-color: var(--primary-dark);
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(0, 123, 255, 0.4);
    }
    
    /* Enhanced Flash Messages */
    .flash-message {
        border-radius: var(--border-radius);
        border: none;
        box-shadow: var(--box-shadow);
        margin: 1rem;
        backdrop-filter: blur(10px);
    }
    
    .flash-message.alert-success {
        background: linear-gradient(135deg, rgba(40, 167, 69, 0.1), rgba(40, 167, 69, 0.05));
        border-left: 4px solid var(--success-color);
        color: var(--success-color);
    }
    
    .flash-message.alert-danger {
        background: linear-gradient(135deg, rgba(220, 53, 69, 0.1), rgba(220, 53, 69, 0.05));
        border-left: 4px solid var(--danger-color);
        color: var(--danger-color);
    }
    
    .flash-message.alert-warning {
        background: linear-gradient(135deg, rgba(255, 193, 7, 0.1), rgba(255, 193, 7, 0.05));
        border-left: 4px solid var(--warning-color);
        color: #856404;
    }
    
    .flash-message.alert-info {
        background: linear-gradient(135deg, rgba(0, 123, 255, 0.1), rgba(0, 123, 255, 0.05));
        border-left: 4px solid var(--primary-color);
        color: var(--primary-color);
    }
    
    /* Mobile Optimizations */
    @media (max-width: 991px) {
        .navbar-collapse {
            background: rgba(44, 62, 80, 0.98);
            border-radius: var(--border-radius);
            margin-top: 1rem;
            padding: 1rem;
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }
        
        .navbar-nav {
            margin-bottom: 1rem;
        }
        
        .navbar-nav .nav-link {
            margin: 0.25rem 0;
            text-align: center;
        }
        
        .header-actions {
            justify-content: center;
            flex-wrap: wrap;
            gap: 0.5rem;
        }
        
        .auth-buttons {
            width: 100%;
            justify-content: center;
        }
        
        .auth-buttons .btn {
            flex: 1;
            max-width: 120px;
        }
    }
    
    @media (max-width: 576px) {
        .navbar-brand {
            font-size: 1.25rem;
        }
        
        .logo-container {
            height: 25px;
        }
        
        .cart-btn,
        .user-dropdown .dropdown-toggle {
            padding: 0.4rem 0.8rem;
            font-size: 0.9rem;
        }
        
        .notification-bell {
            width: 40px;
            height: 40px;
        }
        
        .cart-count,
        .notification-badge {
            width: 18px;
            height: 18px;
            font-size: 0.7rem;
        }
    }
    
    /* Loading Animation */
    .loading-overlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(44, 62, 80, 0.8);
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 9999;
        opacity: 0;
        visibility: hidden;
        transition: var(--transition);
    }
    
    .loading-overlay.show {
        opacity: 1;
        visibility: visible;
    }
    
    .loading-spinner {
        width: 50px;
        height: 50px;
        border: 4px solid rgba(255, 255, 255, 0.3);
        border-radius: 50%;
        border-top-color: var(--primary-color);
        animation: spin 1s ease-in-out infinite;
    }
    
    @keyframes spin {
        to { transform: rotate(360deg); }
    }
    
    /* Accessibility Improvements */
    .navbar-nav .nav-link:focus,
    .cart-btn:focus,
    .notification-bell:focus,
    .user-dropdown .dropdown-toggle:focus,
    .auth-buttons .btn:focus {
        outline: 2px solid var(--primary-color);
        outline-offset: 2px;
    }
    
    /* Scroll Behavior */
    html {
        scroll-behavior: smooth;
    }
    
    /* Main Content Spacing */
    .main-content {
        padding-top: 76px; /* Account for fixed navbar */
        min-height: calc(100vh - 76px);
    }
    
    /* Enhanced Typography */
    .navbar-brand,
    .navbar-nav .nav-link,
    .btn {
        letter-spacing: 0.5px;
    }
    
    /* Performance Optimizations */
    .navbar,
    .navbar-nav .nav-link,
    .cart-btn,
    .notification-bell,
    .user-dropdown .dropdown-toggle,
    .auth-buttons .btn {
        will-change: transform;
    }
    </style>
</head>
<body>
    <!-- Loading Overlay -->
    <div class="loading-overlay" id="loadingOverlay">
        <div class="loading-spinner"></div>
    </div>

    <!-- Enhanced Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark fixed-top" id="mainNavbar">
        <div class="container">
            <!-- Brand Logo -->
            <a class="navbar-brand" href="index.php">
                <div class="logo-container" style="height: 35px;">
                    <img src="assets/images/logo/logo.png" alt="Nati Automotive" height="35" 
                         onerror="this.parentElement.classList.add('img-placeholder'); this.style.display='none';">
                </div>
            </a>
            
            <!-- Mobile Toggle Button -->
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" 
                    aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <!-- Navigation Menu -->
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'index.php' ? 'active' : ''; ?>" 
                           href="index.php" aria-label="Home">
                            <i class="fas fa-home"></i> Home
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'services.php' ? 'active' : ''; ?>" 
                           href="services.php" aria-label="Services">
                            <i class="fas fa-wrench"></i> Services
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo strpos($_SERVER['PHP_SELF'], 'ecommerce') !== false ? 'active' : ''; ?>" 
                           href="ecommerce/pages/products.php" aria-label="Shop">
                            <i class="fas fa-shopping-cart"></i> Shop
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'about.php' ? 'active' : ''; ?>" 
                           href="about.php" aria-label="About">
                            <i class="fas fa-info-circle"></i> About
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'contact.php' ? 'active' : ''; ?>" 
                           href="contact.php" aria-label="Contact">
                            <i class="fas fa-envelope"></i> Contact
                        </a>
                    </li>
                </ul>
                
                <!-- Header Actions -->
                <div class="header-actions">
                    <!-- Shopping Cart -->
                    <a href="ecommerce/pages/cart.php" class="btn cart-btn" aria-label="Shopping Cart">
                        <i class="fas fa-shopping-cart"></i>
                        <span class="cart-count">0</span>
                    </a>
                    
                    <!-- Notification Bell (if logged in) -->

                        <?php include __DIR__ . '/notification_bell.php'; ?>
                   
                    <!-- User Authentication -->
                    <?php if (isset($_SESSION['user_id']) || isset($_SESSION['admin_id'])): ?>
                        <div class="dropdown user-dropdown">
                            <button class="btn dropdown-toggle" type="button" id="userDropdown" 
                                    data-bs-toggle="dropdown" aria-expanded="false" aria-label="User Menu">
                                <i class="fas fa-user"></i>
                                <span class="d-none d-md-inline">
                                    <?php echo isset($_SESSION['admin_id']) ? 'Admin' : 'Account'; ?>
                                </span>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                                <li>
                                    <a class="dropdown-item" href="profile.php">
                                        <i class="fas fa-user-circle"></i> Profile
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="appointments.php">
                                        <i class="fas fa-calendar-check"></i> Appointments
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="orders.php">
                                        <i class="fas fa-shopping-bag"></i> Orders
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="cars.php">
                                        <i class="fas fa-car"></i> My Cars
                                    </a>
                                </li>
                                <?php if (isset($_SESSION['admin_id'])): ?>
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <a class="dropdown-item" href="admin/dashboard.php">
                                        <i class="fas fa-tachometer-alt"></i> Admin Dashboard
                                    </a>
                                </li>
                                <?php endif; ?>
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <a class="dropdown-item" href="logout.php">
                                        <i class="fas fa-sign-out-alt"></i> Logout
                                    </a>
                                </li>
                            </ul>
                        </div>
                    <?php else: ?>
                        <div class="auth-buttons">
                            <a href="login.php" class="btn btn-outline-light">
                                <i class="fas fa-sign-in-alt me-1"></i>Login
                            </a>
                            <a href="register.php" class="btn btn-primary">
                                <i class="fas fa-user-plus me-1"></i>Register
                            </a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content Container -->
    <main class="main-content">
        <!-- Flash Messages -->
        <?php if (isset($_SESSION['flash_message'])): ?>
            <div class="alert alert-<?php echo $_SESSION['flash_type'] ?? 'info'; ?> alert-dismissible fade show flash-message" role="alert">
                <i class="fas fa-<?php echo $_SESSION['flash_type'] === 'success' ? 'check-circle' : ($_SESSION['flash_type'] === 'danger' ? 'exclamation-circle' : ($_SESSION['flash_type'] === 'warning' ? 'exclamation-triangle' : 'info-circle')); ?> me-2"></i>
                <?php echo $_SESSION['flash_message']; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            <?php unset($_SESSION['flash_message'], $_SESSION['flash_type']); ?>
        <?php endif; ?>

    <!-- Enhanced JavaScript -->
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Enhanced navbar scroll effect
        const navbar = document.getElementById('mainNavbar');
        const loadingOverlay = document.getElementById('loadingOverlay');
        
        // Navbar scroll behavior
        let lastScrollTop = 0;
        window.addEventListener('scroll', function() {
            const scrollTop = window.pageYOffset || document.documentElement.scrollTop;
            
            if (scrollTop > 100) {
                navbar.classList.add('scrolled');
            } else {
                navbar.classList.remove('scrolled');
            }
            
            lastScrollTop = scrollTop;
        });
        
        // Enhanced cart count update
        function updateCartCount() {
            const cartCount = document.querySelector('.cart-count');
            if (cartCount) {
                fetch('api/get_cart_count.php')
                .then(response => {
                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }
                    return response.text(); // Get as text first
                })
                .then(text => {
                    try {
                        const data = JSON.parse(text);
                        const count = data.count || 0;
                        cartCount.textContent = count;
                        if (count > 0) {
                            cartCount.style.display = 'flex';
                        } else {
                            cartCount.style.display = 'none';
                        }
                    } catch (jsonError) {
                        console.warn('Invalid JSON response for cart count:', text);
                        cartCount.textContent = '0';
                        cartCount.style.display = 'none';
                    }
                })
                .catch(error => {
                    console.error('Error updating cart count:', error);
                    cartCount.textContent = '0';
                    cartCount.style.display = 'none';
                });
            }
        }
        
        // Enhanced notification handling
        function updateNotifications() {
            const notificationBell = document.getElementById('notificationBell');
            const notificationBadge = document.getElementById('notificationBadge');
            
            if (notificationBell && notificationBadge) {
                fetch('api/get_notifications_count.php')
                .then(response => {
                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }
                    return response.text(); // Get as text first
                })
                .then(text => {
                    try {
                        const data = JSON.parse(text);
                        const count = data.count || 0;
                        if (count > 0) {
                            notificationBadge.textContent = count;
                            notificationBadge.classList.remove('d-none');
                            notificationBell.classList.add('has-notifications');
                        } else {
                            notificationBadge.classList.add('d-none');
                            notificationBell.classList.remove('has-notifications');
                        }
                    } catch (jsonError) {
                        console.warn('Invalid JSON response for notifications:', text);
                        notificationBadge.classList.add('d-none');
                        notificationBell.classList.remove('has-notifications');
                    }
                })
                .catch(error => {
                    console.error('Error updating notifications:', error);
                    notificationBadge.classList.add('d-none');
                    notificationBell.classList.remove('has-notifications');
                });
            }
        }
        
        // Loading overlay for navigation
        function showLoading() {
            loadingOverlay.classList.add('show');
        }
        
        function hideLoading() {
            loadingOverlay.classList.remove('show');
        }
        
        // Add loading effect to navigation links
        document.querySelectorAll('.navbar-nav .nav-link, .auth-buttons .btn').forEach(link => {
            if (link.href && !link.href.includes('#') && !link.href.includes('javascript:')) {
                link.addEventListener('click', function(e) {
                    showLoading();
                    
                    // Hide loading after a short delay if navigation doesn't happen
                    setTimeout(() => {
                        hideLoading();
                    }, 3000);
                });
            }
        });
        
        // Hide loading when page loads
        window.addEventListener('load', hideLoading);
        
        // Enhanced mobile menu behavior
        const navbarToggler = document.querySelector('.navbar-toggler');
        const navbarCollapse = document.querySelector('.navbar-collapse');
        
        if (navbarToggler && navbarCollapse) {
            // Close mobile menu when clicking on nav links
            document.querySelectorAll('.navbar-nav .nav-link').forEach(link => {
                link.addEventListener('click', function() {
                    if (window.innerWidth < 992) {
                        const bsCollapse = new bootstrap.Collapse(navbarCollapse, {
                            toggle: false
                        });
                        bsCollapse.hide();
                    }
                });
            });
            
            // Close mobile menu when clicking outside
            document.addEventListener('click', function(e) {
                if (window.innerWidth < 992 && 
                    !navbarCollapse.contains(e.target) && 
                    !navbarToggler.contains(e.target) &&
                    navbarCollapse.classList.contains('show')) {
                    const bsCollapse = new bootstrap.Collapse(navbarCollapse, {
                        toggle: false
                    });
                    bsCollapse.hide();
                }
            });
        }
        
        // Enhanced keyboard navigation
        document.addEventListener('keydown', function(e) {
            // ESC key closes mobile menu
            if (e.key === 'Escape' && navbarCollapse.classList.contains('show')) {
                const bsCollapse = new bootstrap.Collapse(navbarCollapse, {
                    toggle: false
                });
                bsCollapse.hide();
            }
        });
        
        // Initialize functions
        updateCartCount();
        updateNotifications();
        
        // Auto-refresh cart and notifications every 30 seconds
        setInterval(() => {
            updateCartCount();
            updateNotifications();
        }, 30000);
        
        // Enhanced tooltip initialization
        if (typeof bootstrap !== 'undefined' && bootstrap.Tooltip) {
            const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            tooltipTriggerList.map(function (tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });
        }
        
        // Performance optimization: Preload critical pages
        const criticalPages = ['services.php', 'about.php', 'contact.php'];
        criticalPages.forEach(page => {
            const link = document.createElement('link');
            link.rel = 'prefetch';
            link.href = page;
            document.head.appendChild(link);
        });
        
        console.log('Enhanced header initialized successfully');
    });
    </script>

        <?php 
        // Display top ad if we're on the home page
        if (basename($_SERVER['PHP_SELF']) == 'index.php') {
        echo displayAd($conn, 'home_top');
        }
        ?> 