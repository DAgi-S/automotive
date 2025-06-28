<?php
define('INCLUDED', true);

session_start(); // Start the session

if (!isset($_SESSION['password'])) {
    header("location: login.php?message=login first");
    exit;
}

include("partial-front/db_con.php");
include("db_conn.php");
require_once 'includes/ad_functions.php';
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <title>Nati Automotive - Home</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta charset="UTF-8">
    <meta name="description" content="Quality automotive services, GPS tracking, and professional car care at Nati Automotive">
    <meta name="keywords" content="automotive, car service, GPS tracking, vehicle maintenance">
    <link rel="icon" href="assets/images/favicon/Nati-logo.png" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/css/bottom-nav-universal.css">
    <link rel="stylesheet" href="assets/css/new-home-layout.css">
    <link rel="stylesheet" href="assets/css/mobile-header.css">
    
    <style>
        /* Mobile Header Styles for Home */
        .mobile-header-home {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            height: 60px;
            background: linear-gradient(135deg, #3498db 0%, #2980b9 100%);
            color: white;
            z-index: 1000;
            box-shadow: 0 2px 10px rgba(52, 152, 219, 0.3);
        }
        
        .header-container {
            display: flex;
            align-items: center;
            justify-content: space-between;
            height: 100%;
            padding: 0 1rem;
            max-width: 1200px;
            margin: 0 auto;
        }
        
        .header-left {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            flex: 1;
        }
        
        .logo-btn,
        .notifications-btn,
        .menu-btn {
            background: none;
            border: none;
            color: white;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s ease;
            position: relative;
        }
        
        .logo-btn:hover,
        .notifications-btn:hover,
        .menu-btn:hover {
            background: rgba(255, 255, 255, 0.2);
            transform: scale(1.1);
        }
        
        .header-title h1 {
            font-size: 1.2rem;
            font-weight: 700;
            margin: 0;
            color: white;
        }
        
        .header-right {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .notification-badge {
            position: absolute;
            top: 8px;
            right: 8px;
            background: #ff4757;
            color: white;
            border-radius: 50%;
            width: 16px;
            height: 16px;
            font-size: 0.6rem;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
        }
        
        /* Adjust body padding for fixed header */
        .site-content {
            padding-top: 60px;
            min-height: 100vh;
            position: relative;
            padding-bottom: 90px !important;
        }
        
        /* Enhanced Bottom Navigation Fixes */
        #bottom-navigation {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            background: white;
            border-top: 1px solid rgba(0, 0, 0, 0.1);
            box-shadow: 0 -2px 10px rgba(0, 0, 0, 0.1);
            z-index: 999;
            height: 70px;
        }
        
        .home-navigation-menu {
            height: 100%;
        }
        
        .bottom-panel {
            height: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .bootom-tabbar {
            display: flex;
            list-style: none;
            margin: 0;
            padding: 0;
            width: 100%;
            max-width: 400px;
            justify-content: space-around;
            align-items: center;
            height: 100%;
        }
        
        .bootom-tabbar li {
            position: relative;
            flex: 1;
            height: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .bootom-tabbar li a {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 50px;
            height: 50px;
            border-radius: 50%;
            transition: all 0.3s ease;
            text-decoration: none;
            position: relative;
        }
        
        .bootom-tabbar li a svg {
            width: 24px;
            height: 24px;
            transition: all 0.3s ease;
        }
        
        .bootom-tabbar li a svg path {
            stroke: #6c757d;
            transition: all 0.3s ease;
        }
        
        .bootom-tabbar li.active a {
            background: linear-gradient(135deg, #3498db 0%, #2980b9 100%);
            box-shadow: 0 4px 12px rgba(52, 152, 219, 0.3);
            transform: translateY(-2px);
        }
        
        .bootom-tabbar li.active a svg path {
            stroke: white;
        }
        
        .bootom-tabbar li a:hover {
            background: rgba(52, 152, 219, 0.1);
            transform: translateY(-1px);
        }
        
        .orange-boder,
        .orange-border {
            display: none;
        }
        
        /* Mobile Responsive */
        @media (max-width: 768px) {
            .mobile-header-home {
                height: 50px;
            }
            
            .site-content {
                padding-top: 10px;
            }
            
            .header-container {
                padding: 0 0.75rem;
            }
            
            .header-title h1 {
                font-size: 1.1rem;
            }
            
            .logo-btn,
            .notifications-btn,
            .menu-btn {
                width: 35px;
                height: 35px;
            }
            
            #bottom-navigation {
                height: 60px;
            }
            
            .bootom-tabbar li a {
                width: 45px;
                height: 45px;
            }
            
            .bootom-tabbar li a svg {
                width: 20px;
                height: 20px;
            }
        }
    </style>
</head>

<body>
    <div class="site-content">
        <!-- Mobile Header Navigation -->
        <header class="mobile-header-home">
          <div class="header-container">
            <div class="header-left">
              <button class="logo-btn" onclick="window.location.reload()">
                <i class="fas fa-car"></i>
              </button>
              <div class="header-title">
                <h1>Nati Automotive</h1>
              </div>
            </div>
            <div class="header-right">
              <button class="notifications-btn" onclick="toggleNotifications()">
                <i class="fas fa-bell"></i>
                <span class="notification-badge">4</span>
              </button>
              <button class="menu-btn" onclick="toggleMenu()">
                <i class="fas fa-ellipsis-v"></i>
              </button>
            </div>
          </div>
        </header>
        
        <!-- Main Content -->
        <section id="homescreen" class="container-fluid">
            
            <!-- Dynamic Ads Section -->
            <div class="home-section ads-section">
                <div class="section-header">
                    <h2 class="section-title">
                        <i class="fas fa-bullhorn me-2"></i>Latest Offers
                    </h2>
                </div>
                <div class="row g-3" id="ads-container">
                    <!-- Dynamic ads will be loaded here -->
                    <div class="ads-loading">
                        <div class="col-12 text-center">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">Loading offers...</span>
                                </div>
                            <p class="mt-2 text-muted">Loading latest offers...</p>
                            </div>
                    </div>
                </div>
            </div>

            <!-- Articles Section -->
            <div class="home-section articles-section">
                <div class="section-header">
                    <h2 class="section-title">
                        <i class="fas fa-newspaper me-2"></i>Technical Articles
                    </h2>
                    <a href="articles.php" class="view-all-link">
                        View All <i class="fas fa-arrow-right"></i>
                    </a>
                </div>
                <div class="row g-3">
                    <div class="col-md-6">
                        <div class="article-card">
                            <div class="article-image">
                                <img src="assets/images/gallery/car_heat.jpg" alt="Engine Maintenance" class="img-fluid">
                                <div class="article-category">
                                    <span class="badge bg-info">Maintenance</span>
                                </div>
                            </div>
                            <div class="article-content">
                                <div class="article-meta">
                                    <span class="article-date">
                                        <i class="far fa-calendar"></i> Dec 15, 2024
                                    </span>
                                    <span class="article-author">
                                        <i class="fas fa-user"></i> Expert Team
                                    </span>
                                </div>
                                <h5 class="article-title">Essential Engine Maintenance Tips</h5>
                                <p class="article-excerpt">Learn the key maintenance practices that keep your engine running smoothly and extend its lifespan...</p>
                                <a href="article.php?id=1" class="read-more-link">
                                    Read More <i class="fas fa-arrow-right"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="article-card">
                            <div class="article-image">
                                <img src="assets/images/gallery/dashboard_lights.jpg" alt="Dashboard Lights" class="img-fluid">
                                <div class="article-category">
                                    <span class="badge bg-warning">Guide</span>
                                </div>
                            </div>
                            <div class="article-content">
                                <div class="article-meta">
                                    <span class="article-date">
                                        <i class="far fa-calendar"></i> Dec 12, 2024
                                    </span>
                                    <span class="article-author">
                                        <i class="fas fa-user"></i> Tech Support
                                    </span>
                                </div>
                                <h5 class="article-title">Understanding Dashboard Warning Lights</h5>
                                <p class="article-excerpt">A comprehensive guide to understanding what your car's dashboard warning lights mean and when to take action...</p>
                                <a href="article.php?id=2" class="read-more-link">
                                    Read More <i class="fas fa-arrow-right"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Dynamic Blogs Section -->
            <div class="home-section blogs-section">
                <div class="section-header">
                    <h2 class="section-title">
                        <i class="fas fa-blog me-2"></i>Latest Blog Posts
                    </h2>
                    <a href="blog.php" class="view-all-link">
                        View All <i class="fas fa-arrow-right"></i>
                    </a>
                </div>
                <div class="row g-3" id="blogs-container">
                    <!-- Dynamic blogs will be loaded here -->
                    <div class="blogs-loading">
                        <div class="col-12 text-center">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">Loading blog posts...</span>
                                </div>
                            <p class="mt-2 text-muted">Loading latest blog posts...</p>
                            </div>
                                </div>
                                </div>
                            </div>

            <!-- Featured Services Section -->
            <div class="home-section services-section">
                <div class="section-header">
                    <h2 class="section-title">
                        <i class="fas fa-tools me-2"></i>Our Services
                    </h2>
                    <a href="service.php" class="view-all-link">
                        View All <i class="fas fa-arrow-right"></i>
                                </a>
                            </div>
                <div class="row" id="services-container">
                    <!-- Dynamic services will be loaded here -->
                    <div class="services-loading">
                        <div class="col-12 text-center">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">Loading services...</span>
                            </div>
                            <p class="mt-2 text-muted">Loading our services...</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- User Cars with Deadline Notifications -->
            <div class="home-section user-cars-section">
                <div class="section-header">
                    <h2 class="section-title">
                        <i class="fas fa-car me-2"></i>Your Vehicles
                    </h2>
                    <a href="my-cars.php" class="view-all-link">
                        Manage All <i class="fas fa-cog"></i>
                    </a>
                </div>
                <div class="cars-container">
                    <!-- Car 1 -->
                    <div class="car-card urgent">
                        <div class="car-header">
                            <div class="car-info">
                                <h5 class="car-name">Toyota Corolla</h5>
                                <p class="car-plate">አ.አ 12345</p>
                            </div>
                            <div class="car-status">
                                <span class="status-badge urgent">
                                    <i class="fas fa-exclamation-triangle"></i> Urgent
                                </span>
                            </div>
                        </div>
                        <div class="car-notifications">
                            <div class="notification-item urgent">
                                <div class="notification-icon">
                                    <i class="fas fa-calendar-times"></i>
                                </div>
                                <div class="notification-content">
                                    <h6>Insurance Expiring</h6>
                                    <p>Expires in 3 days (Dec 18, 2024)</p>
                                </div>
                            </div>
                            <div class="notification-item warning">
                                <div class="notification-icon">
                                    <i class="fas fa-wrench"></i>
                                </div>
                                <div class="notification-content">
                                    <h6>Service Due</h6>
                                    <p>Oil change due in 500 km</p>
                                </div>
                            </div>
                        </div>
                        <div class="car-actions">
                            <a href="renew-insurance.php?car=1" class="btn btn-danger btn-sm">
                                <i class="fas fa-shield-alt"></i> Renew Insurance
                            </a>
                            <a href="book-service.php?car=1" class="btn btn-warning btn-sm">
                                <i class="fas fa-calendar-plus"></i> Book Service
                            </a>
                        </div>
                    </div>

                    <!-- Car 2 -->
                    <div class="car-card good">
                        <div class="car-header">
                            <div class="car-info">
                                <h5 class="car-name">Hyundai Elantra</h5>
                                <p class="car-plate">አ.አ 67890</p>
                            </div>
                            <div class="car-status">
                                <span class="status-badge good">
                                    <i class="fas fa-check-circle"></i> Good
                                </span>
                            </div>
                        </div>
                        <div class="car-notifications">
                            <div class="notification-item info">
                                <div class="notification-icon">
                                    <i class="fas fa-calendar-check"></i>
                                </div>
                                <div class="notification-content">
                                    <h6>Next Service</h6>
                                    <p>Due in 45 days (Jan 30, 2025)</p>
                                </div>
                            </div>
                            <div class="notification-item success">
                                <div class="notification-icon">
                                    <i class="fas fa-shield-alt"></i>
                                </div>
                                <div class="notification-content">
                                    <h6>Insurance Valid</h6>
                                    <p>Until Aug 15, 2025</p>
                                </div>
                            </div>
                        </div>
                        <div class="car-actions">
                            <a href="car-details.php?car=2" class="btn btn-primary btn-sm">
                                <i class="fas fa-eye"></i> View Details
                            </a>
                            <a href="service-history.php?car=2" class="btn btn-outline-primary btn-sm">
                                <i class="fas fa-history"></i> History
                            </a>
                        </div>
                    </div>

                    <!-- Car 3 -->
                    <div class="car-card warning">
                        <div class="car-header">
                            <div class="car-info">
                                <h5 class="car-name">Suzuki Swift</h5>
                                <p class="car-plate">አ.አ 11223</p>
                            </div>
                            <div class="car-status">
                                <span class="status-badge warning">
                                    <i class="fas fa-exclamation-circle"></i> Attention
                                </span>
                            </div>
                        </div>
                        <div class="car-notifications">
                            <div class="notification-item warning">
                                <div class="notification-icon">
                                    <i class="fas fa-calendar-alt"></i>
                                </div>
                                <div class="notification-content">
                                    <h6>Registration Renewal</h6>
                                    <p>Due in 15 days (Dec 30, 2024)</p>
                                </div>
                            </div>
                            <div class="notification-item info">
                                <div class="notification-icon">
                                    <i class="fas fa-tachometer-alt"></i>
                                </div>
                                <div class="notification-content">
                                    <h6>Mileage Check</h6>
                                    <p>45,000 km - Service recommended</p>
                                </div>
                            </div>
                        </div>
                        <div class="car-actions">
                            <a href="renew-registration.php?car=3" class="btn btn-warning btn-sm">
                                <i class="fas fa-file-alt"></i> Renew Registration
                            </a>
                            <a href="book-service.php?car=3" class="btn btn-outline-warning btn-sm">
                                <i class="fas fa-tools"></i> Schedule Service
                            </a>
                        </div>
                    </div>
                </div>
            </div>


            <!-- Business Analytics Dashboard -->
            <div class="home-section analytics-section">
                <div class="section-header">
                    <h2 class="section-title">
                        <i class="fas fa-chart-bar me-2"></i>Business Overview
                    </h2>
                    <a href="dashboard.php" class="view-all-link">
                        Full Dashboard <i class="fas fa-arrow-right"></i>
                    </a>
                </div>
                <div class="analytics-container" id="analytics-container">
                    <!-- Dynamic analytics will be loaded here -->
                    <div class="analytics-loading">
                        <div class="col-12 text-center">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">Loading analytics...</span>
                            </div>
                            <p class="mt-2 text-muted">Loading business metrics...</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Dynamic Latest Products Section -->
            <div class="home-section products-section">
                <div class="section-header">
                    <h2 class="section-title">
                        <i class="fas fa-shopping-bag me-2"></i>Latest Products
                    </h2>
                    <a href="ecommerce/pages/products.php" class="view-all-link">
                        Shop All <i class="fas fa-external-link-alt"></i>
                    </a>
                </div>
                <div class="products-slider">
                    <div class="slider-container">
                        <div class="slider-track" id="productsTrack">
                            <!-- Dynamic products will be loaded here -->
                            <div class="products-loading text-center">
                                <div class="spinner-border text-primary" role="status">
                                    <span class="visually-hidden">Loading products...</span>
                                        </div>
                                <p class="mt-2 text-muted">Loading latest products...</p>
                            </div>
                        </div>
                        <div class="slider-controls">
                            <button class="slider-btn prev-btn" onclick="slideProducts(-1)">
                                <i class="fas fa-chevron-left"></i>
                            </button>
                            <button class="slider-btn next-btn" onclick="slideProducts(1)">
                                <i class="fas fa-chevron-right"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

        </section>

        <!-- Bottom Navigation -->
        <?php include 'partial-front/bottom_nav.php'; ?>
        
        <!-- Options Menu -->
        <?php include 'option.php'; ?>
    </div>

    <!-- Scripts -->
    <script src="assets/js/jquery.min.js"></script>
    <script src="assets/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/homepage-dynamic.js"></script>
    <script>
        // Product Slider JavaScript
        let currentSlide = 0;
        
        document.addEventListener('DOMContentLoaded', function() {
            const slides = document.querySelectorAll('.product-slide');
            const totalSlides = slides.length;
            
            function getSlidesToShow() {
                if (window.innerWidth <= 576) return 1;
                if (window.innerWidth <= 768) return 2;
                return 3;
            }
            
            const slidesToShow = getSlidesToShow();
            const maxSlide = Math.max(0, totalSlides - slidesToShow);
            
            function updateSlider() {
                const track = document.getElementById('productsTrack');
                if (track && slides.length > 0) {
                    const slideWidth = slides[0].offsetWidth + 16; // 16px gap
                    const translateX = -(currentSlide * slideWidth);
                    track.style.transform = `translateX(${translateX}px)`;
                }
            }
            
            // Global function for button clicks
            window.slideProducts = function(direction) {
                if (direction === 1 && currentSlide < maxSlide) {
                    currentSlide++;
                } else if (direction === -1 && currentSlide > 0) {
                    currentSlide--;
                }
                updateSlider();
            };
            
            // Add to cart function
            window.addToCart = function(productId) {
                const button = event.target;
                const originalText = button.innerHTML;
                
                button.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Adding...';
                button.disabled = true;
                
                setTimeout(() => {
                    button.innerHTML = '<i class="fas fa-check"></i> Added!';
                    button.classList.add('btn-success');
                    
                    setTimeout(() => {
                        button.innerHTML = originalText;
                        button.disabled = false;
                        button.classList.remove('btn-success');
                    }, 2000);
                }, 1000);
            };
            
            // Initialize
            updateSlider();
            
            // Auto-slide every 5 seconds
            setInterval(() => {
                if (currentSlide >= maxSlide) {
                    currentSlide = 0;
                } else {
                    currentSlide++;
                }
                updateSlider();
            }, 5000);
        });
        
        // Header functionality for Home page
        async function toggleNotifications() {
            const existingDropdown = document.querySelector('#notifications-dropdown');
            if (existingDropdown) {
                existingDropdown.remove();
                return;
            }
            
            try {
                const response = await fetch('api/get_notifications_summary.php');
                const data = await response.json();
                
                if (data.success) {
                    showNotificationsDropdown(data.recent_notifications);
                    // Update badge count
                    const badge = document.querySelector('.notification-badge');
                    if (badge) {
                        badge.textContent = data.unread_count;
                        if (data.unread_count === 0) {
                            badge.style.display = 'none';
                        }
                    }
                }
            } catch (error) {
                console.error('Error loading notifications:', error);
                showNotificationsDropdown([]);
            }
        }
        
        function showNotificationsDropdown(notifications) {
            const dropdown = document.createElement('div');
            dropdown.id = 'notifications-dropdown';
            dropdown.style.cssText = `
                position: fixed;
                top: 60px;
                right: 10px;
                width: 320px;
                max-height: 400px;
                background: white;
                border-radius: 8px;
                box-shadow: 0 4px 20px rgba(0,0,0,0.15);
                z-index: 1001;
                overflow: hidden;
            `;
            
            const header = `
                <div style="padding: 12px 16px; background: linear-gradient(135deg, #3498db 0%, #2980b9 100%); color: white;">
                    <h6 style="margin: 0; font-weight: 600;">Notifications</h6>
                </div>
            `;
            
            const notificationsList = notifications.length > 0 ? notifications.map(notification => `
                <div style="padding: 12px 16px; border-bottom: 1px solid #f0f0f0; cursor: pointer; transition: background 0.2s;" 
                     onmouseover="this.style.background='#f8f9fa'" 
                     onmouseout="this.style.background='white'"
                     onclick="window.location.href='${notification.action_url}'">
                    <div style="display: flex; align-items: flex-start; gap: 10px;">
                        <div style="width: 32px; height: 32px; border-radius: 50%; background: ${notification.priority === 'urgent' ? '#e74c3c' : notification.priority === 'high' ? '#f39c12' : '#3498db'}; display: flex; align-items: center; justify-content: center; color: white; font-size: 12px;">
                            <i class="${notification.icon}"></i>
                        </div>
                        <div style="flex: 1;">
                            <h6 style="margin: 0 0 4px 0; font-size: 14px; font-weight: 600; color: #2c3e50;">${notification.title}</h6>
                            <p style="margin: 0 0 4px 0; font-size: 12px; color: #7f8c8d; line-height: 1.4;">${notification.message}</p>
                            <small style="color: #95a5a6; font-size: 11px;">${notification.time}</small>
                        </div>
                        ${!notification.is_read ? '<div style="width: 8px; height: 8px; background: #e74c3c; border-radius: 50%; margin-top: 4px;"></div>' : ''}
                    </div>
                </div>
            `).join('') : `
                <div style="padding: 20px; text-align: center; color: #7f8c8d;">
                    <i class="fas fa-bell-slash fa-2x" style="margin-bottom: 10px; opacity: 0.5;"></i>
                    <p style="margin: 0; font-size: 14px;">No notifications yet</p>
                </div>
            `;
            
            const footer = `
                <div style="padding: 8px 16px; background: #f8f9fa; text-align: center;">
                    <a href="notifications.php" style="color: #3498db; text-decoration: none; font-size: 12px; font-weight: 500;">
                        View All Notifications
                    </a>
                </div>
            `;
            
            dropdown.innerHTML = header + notificationsList + footer;
            document.body.appendChild(dropdown);
            
            // Auto-hide after 10 seconds
            setTimeout(() => {
                if (dropdown.parentNode) {
                    dropdown.remove();
                }
            }, 10000);
        }
        
        // Load notification count on page load
        document.addEventListener('DOMContentLoaded', async function() {
            try {
                const response = await fetch('api/get_notifications_summary.php');
                const data = await response.json();
                
                if (data.success) {
                    const badge = document.querySelector('.notification-badge');
                    if (badge) {
                        badge.textContent = data.unread_count;
                        if (data.unread_count === 0) {
                            badge.style.display = 'none';
                        }
                    }
                }
            } catch (error) {
                console.error('Error loading notification count:', error);
            }
        });
        
        function toggleMenu() {
            console.log('Menu toggled');
            
            const options = [
                { icon: 'fas fa-calendar-check', text: 'Book Service', action: () => window.location.href = 'service.php' },
                { icon: 'fas fa-user', text: 'My Profile', action: () => window.location.href = 'profile.php' },
                { icon: 'fas fa-shopping-cart', text: 'Shop', action: () => window.location.href = 'ecommerce/pages/products.php' },
                { icon: 'fas fa-phone', text: 'Call Us', action: () => window.location.href = 'tel:+251912424' },
                { icon: 'fas fa-map-marker-alt', text: 'Location', action: () => window.location.href = 'location.php' }
            ];
            
            let menuHtml = '<div style="position:fixed;top:60px;right:10px;background:white;border-radius:8px;box-shadow:0 4px 12px rgba(0,0,0,0.15);z-index:1001;min-width:150px;">';
            options.forEach(option => {
                menuHtml += `<div style="padding:12px 16px;border-bottom:1px solid #f0f0f0;cursor:pointer;display:flex;align-items:center;gap:8px;" onclick="${option.action.toString().replace('() => ', '')}">
                    <i class="${option.icon}" style="color:#3498db;width:16px;"></i>
                    <span style="color:#333;font-size:14px;">${option.text}</span>
                </div>`;
            });
            menuHtml += '</div>';
            
            const existingMenu = document.querySelector('#temp-menu');
            if (existingMenu) {
                existingMenu.remove();
            } else {
                const menu = document.createElement('div');
                menu.id = 'temp-menu';
                menu.innerHTML = menuHtml;
                document.body.appendChild(menu);
                
                setTimeout(() => {
                    const menuToRemove = document.querySelector('#temp-menu');
                    if (menuToRemove) menuToRemove.remove();
                }, 5000);
            }
        }
    </script>
</body>

</html>
