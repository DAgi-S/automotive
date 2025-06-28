<?php
define('INCLUDED', true);

session_start(); // Start the session

if (!isset($_SESSION['password'])) {
    header("location: login.php?message=login first");
    exit;
}

include("partial-front/db_con.php");
include("db_conn.php");
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <title>Nati Automotive - Services</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta charset="UTF-8">
    <meta name="description" content="Professional automotive services including oil change, tire rotation, brake service, and more at Nati Automotive">
    <meta name="keywords" content="automotive, car service, oil change, brake service, tire rotation, engine tune-up">
    <link rel="icon" href="assets/images/favicon/Nati-logo.png" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/css/bottom-nav-universal.css">
    <link rel="stylesheet" href="assets/css/new-home-layout.css">
    <link rel="stylesheet" href="assets/css/service-page.css">
    <link rel="stylesheet" href="assets/css/mobile-header.css">
    
    <style>
        /* Mobile Header Styles for Services */
        .mobile-header-services {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            height: 60px;
            background: linear-gradient(135deg, #e74c3c 0%, #c0392b 100%);
            color: white;
            z-index: 1000;
            box-shadow: 0 2px 10px rgba(231, 76, 60, 0.3);
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
        
        .back-btn,
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
        
        .back-btn:hover,
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
            background: linear-gradient(135deg, #e74c3c 0%, #c0392b 100%);
            box-shadow: 0 4px 12px rgba(231, 76, 60, 0.3);
            transform: translateY(-2px);
        }
        
        .bootom-tabbar li.active a svg path {
            stroke: white;
        }
        
        .bootom-tabbar li a:hover {
            background: rgba(231, 76, 60, 0.1);
            transform: translateY(-1px);
        }
        
        .orange-boder,
        .orange-border {
            display: none;
        }
        
        /* Mobile Responsive */
        @media (max-width: 768px) {
            .mobile-header-services {
                height: 50px;
            }
            
            .site-content {
                padding-top: 50px;
            }
            
            .header-container {
                padding: 0 0.75rem;
            }
            
            .header-title h1 {
                font-size: 1.1rem;
            }
            
            .back-btn,
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
    <header class="mobile-header-services">
      <div class="header-container">
        <div class="header-left">
          <button class="back-btn" onclick="history.back()">
            <i class="fas fa-arrow-left"></i>
          </button>
          <div class="header-title">
            <h1>Our Services</h1>
          </div>
        </div>
        <div class="header-right">
          <a class="chat-btn" href="chat-screen.php" style="background: none; border: none; color: white; width: 40px; height: 40px; border-radius: 50%; display: flex; align-items: center; justify-content: center; position: relative; margin-right: 2px;">
            <i class="fas fa-message"></i>
          </a>
          <button class="notifications-btn" onclick="toggleNotifications()">
            <i class="fas fa-bell"></i>
            <span class="notification-badge">2</span>
          </button>
          <button class="menu-btn" onclick="toggleMenu()">
            <i class="fas fa-ellipsis-v"></i>
          </button>
        </div>
      </div>
    </header>
      

        <!-- Main Content -->
        <section id="homescreen" class="container-fluid">
            
            <!-- Service Hero Section -->
            <div class="home-section service-hero-section">
                <div class="section-header text-center">
                    <h1 class="section-title">
                        <i class="fas fa-tools me-2"></i>Our Professional Services
                    </h1>
                    <p class="section-subtitle">Expert automotive care for your vehicle's every need</p>
                </div>
            </div>

            <?php 
            // Safe ad display with error handling
            if (function_exists('displayAd')) {
                echo displayAd($conn, 'service_page');
            }
            ?>

            <!-- Service Search and Filter -->
            <div class="service-controls mb-4">
                <div class="row align-items-center">
                    <div class="col-md-6">
                        <div class="search-box">
                            <i class="fas fa-search"></i>
                            <input type="text" id="serviceSearch" placeholder="Search services..." class="form-control">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="filter-buttons">
                            <button class="filter-btn active" data-category="all">
                                <i class="fas fa-th-large"></i> All Services
                            </button>
                            <button class="filter-btn" data-category="maintenance">
                                <i class="fas fa-tools"></i> Maintenance
                            </button>
                            <button class="filter-btn" data-category="repair">
                                <i class="fas fa-wrench"></i> Repair
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <!-- Main Content -->
                <div class="col-lg-8">
                    <div class="services-header">
                        <h2 class="section-title">Available Services</h2>
                        <div class="services-count">
                            <span id="servicesCount">Loading...</span>
                        </div>
                    </div>
                    
                    <div class="services-grid" id="servicesGrid">
                        <?php
                        try {
                            // Fetch active services from the database using mysqli
                            $query = "SELECT * FROM tbl_services WHERE status = 'active' ORDER BY service_id ASC";
                            $result = mysqli_query($conn, $query);
                            
                            if ($result && mysqli_num_rows($result) > 0) {
                                $services = mysqli_fetch_all($result, MYSQLI_ASSOC);
                                foreach ($services as $service) {
                                    // Use the icon_class from database or fallback to default
                                    $iconClass = !empty($service['icon_class']) ? $service['icon_class'] : 'fas fa-wrench';
                                    
                                    // Determine service category for filtering
                                    $category = 'maintenance'; // default
                                    if (stripos($service['service_name'], 'repair') !== false || 
                                        stripos($service['service_name'], 'fix') !== false) {
                                        $category = 'repair';
                                    }
                                    
                                    // Truncate description for better layout
                                    $description = $service['description'];
                                    if (strlen($description) > 120) {
                                        $description = substr($description, 0, 120) . '...';
                                    }
                                    ?>
                                    <div class="service-card animate-in" 
                                         data-category="<?php echo $category; ?>" 
                                         data-name="<?php echo strtolower($service['service_name']); ?>"
                                         data-aos="fade-up"
                                         data-aos-delay="<?php echo ($service['service_id'] % 4) * 100; ?>">
                                        <div class="card-body">
                                            <div class="service-header">
                                                <div class="service-icon">
                                                    <i class="<?php echo htmlspecialchars($iconClass); ?>"></i>
                                                </div>
                                                <h3 class="service-title"><?php echo htmlspecialchars($service['service_name']); ?></h3>
                                            </div>
                                            
                                            <p class="service-description"><?php echo htmlspecialchars($description); ?></p>
                                            
                                            <div class="service-footer">
                                                <div class="service-price">
                                                    <span class="currency">Br</span>
                                                    <span class="amount"><?php echo number_format($service['price'], 2); ?></span>
                                                </div>
                                                <div class="service-duration">
                                                    <i class="far fa-clock"></i> 
                                                    <?php echo htmlspecialchars($service['duration']); ?>
                                                </div>
                                            </div>
                                            
                                            <a href="order_service.php?service_id=<?php echo $service['service_id']; ?>" 
                                               class="btn-book"
                                               data-service-id="<?php echo $service['service_id']; ?>"
                                               data-service-name="<?php echo htmlspecialchars($service['service_name']); ?>"
                                               onclick="showBookingLoader(this)">
                                                <i class="fas fa-calendar-plus"></i>
                                                Book Now
                                            </a>
                                        </div>
                                    </div>
                                    <?php
                                }
                            } else {
                                echo '<div class="col-12 text-center no-services">';
                                echo '<div class="alert alert-info">';
                                echo '<i class="fas fa-info-circle me-2"></i>';
                                echo 'No services available at the moment. Please check back later.';
                                echo '</div>';
                                echo '</div>';
                            }
                        } catch (Exception $e) {
                            echo '<div class="col-12 text-center">';
                            echo '<div class="alert alert-danger">';
                            echo '<i class="fas fa-exclamation-triangle me-2"></i>';
                            echo 'Error loading services. Please try again later.';
                            echo '</div>';
                            echo '</div>';
                        }
                        
                        // Also handle mysqli errors
                        if (!$result) {
                            echo '<div class="col-12 text-center">';
                            echo '<div class="alert alert-danger">';
                            echo '<i class="fas fa-exclamation-triangle me-2"></i>';
                            echo 'Database error: ' . mysqli_error($conn);
                            echo '</div>';
                            echo '</div>';
                        }
                        ?>
                    </div>

                    <!-- No Results Message -->
                    <div id="noResults" class="no-results" style="display: none;">
                        <div class="text-center py-5">
                            <i class="fas fa-search fa-3x text-muted mb-3"></i>
                            <h4>No services found</h4>
                            <p class="text-muted">Try adjusting your search or filter criteria</p>
                            <button class="btn btn-primary" onclick="clearFilters()">
                                <i class="fas fa-refresh"></i> Clear Filters
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Sidebar -->
                <div class="col-lg-4">
                    <?php 
                    // Safe ad display with error handling
                    if (function_exists('displayAd')) {
                        echo displayAd($conn, 'sidebar');
                    }
                    ?>

                    <!-- Service Hours -->
                    <div class="service-hours">
                        <h3><i class="fas fa-clock me-2"></i>Service Hours</h3>
                        <ul>
                            <li>
                                <span>Monday - Friday</span>
                                <span>8:00 AM - 6:00 PM</span>
                            </li>
                            <li>
                                <span>Saturday</span>
                                <span>9:00 AM - 4:00 PM</span>
                            </li>
                            <li>
                                <span>Sunday</span>
                                <span class="closed">Closed</span>
                            </li>
                        </ul>
                        <div class="current-status">
                            <span class="status-indicator"></span>
                            <span class="status-text">We are currently open</span>
                        </div>
                    </div>

                    <!-- Quick Actions -->
                    <div class="quick-actions">
                        <h3><i class="fas fa-bolt me-2"></i>Quick Actions</h3>
                        <div class="action-buttons">
                            <a href="tel:+251911123456" class="action-btn call-btn">
                                <i class="fas fa-phone"></i>
                                <span>Call Now</span>
                            </a>
                            <a href="https://wa.me/251911123456" class="action-btn whatsapp-btn" target="_blank">
                                <i class="fab fa-whatsapp"></i>
                                <span>WhatsApp</span>
                            </a>
                            <a href="#" class="action-btn location-btn" onclick="openDirections()">
                                <i class="fas fa-map-marker-alt"></i>
                                <span>Directions</span>
                            </a>
                        </div>
                    </div>

                    <!-- Emergency Contact -->
                    <div class="emergency-contact">
                        <h3><i class="fas fa-phone-alt me-2"></i>Emergency Service</h3>
                        <p>24/7 Emergency Support Available for urgent automotive needs</p>
                        <a href="tel:+251911123456" class="btn emergency-btn w-100">
                            <i class="fas fa-phone-alt me-2"></i>Call Emergency Line
                        </a>
                    </div>
                </div>
            </div>

            <?php 
            // Safe ad display with error handling
            if (function_exists('displayAd')) {
                echo displayAd($conn, 'service_bottom');
            }
            ?>
        </section>

        <?php include 'partial-front/bottom_nav.php'; ?>
        <?php include 'option.php' ?>
  </div>

  <!-- Scripts with error handling -->
  <script>
    // Error handling for missing jQuery
    if (typeof jQuery === 'undefined') {
        console.warn('jQuery not loaded, loading from CDN');
        document.write('<script src="https://code.jquery.com/jquery-3.6.0.min.js"><\/script>');
    }
  </script>
  <script src="assets/js/jquery.min.js" onerror="console.warn('Local jQuery failed, using CDN')"></script>
  <script src="assets/js/bootstrap.bundle.min.js" onerror="console.warn('Local Bootstrap failed')"></script>
  <script src="assets/js/service-page.js" onerror="console.warn('Service page JS not found')"></script>
  
  <script>
    // Header functionality for Services page
    function toggleNotifications() {
      console.log('Notifications toggled');
      
      const notificationBadge = document.querySelector('.notification-badge');
      if (notificationBadge) {
        notificationBadge.style.display = 'none';
        setTimeout(() => {
          notificationBadge.style.display = 'flex';
        }, 3000);
      }
    }
    
    function toggleMenu() {
      console.log('Menu toggled');
      
      const options = [
        { icon: 'fas fa-phone', text: 'Call Us', action: () => window.location.href = 'tel:+251912424' },
        { icon: 'fab fa-whatsapp', text: 'WhatsApp', action: () => window.open('https://wa.me/251912424', '_blank') },
        { icon: 'fas fa-map-marker-alt', text: 'Location', action: () => window.location.href = 'location.php' },
        { icon: 'fas fa-home', text: 'Home', action: () => window.location.href = 'home.php' }
      ];
      
      let menuHtml = '<div style="position:fixed;top:60px;right:10px;bg:white;border-radius:8px;box-shadow:0 4px 12px rgba(0,0,0,0.15);z-index:1001;min-width:150px;">';
      options.forEach(option => {
        menuHtml += `<div style="padding:12px 16px;border-bottom:1px solid #f0f0f0;cursor:pointer;display:flex;align-items:center;gap:8px;" onclick="${option.action.toString().replace('() => ', '')}">
          <i class="${option.icon}" style="color:#e74c3c;width:16px;"></i>
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