<?php
session_start(); // Start the session
define('INCLUDED', true);

// Include database connection first
include("partial-front/db_con.php");
include("db_conn.php");

// Then include other files that might need the database connection
include 'header.php';
require_once 'includes/home_ad_functions.php';
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nati Automotive - Home</title>
    <meta name="description" content="Quality automotive services, GPS tracking, and professional car care at Nati Automotive. Your trusted partner for vehicle maintenance and repair.">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/css/home-page.css">
    <link rel="stylesheet" href="assets/css/bottom-nav-universal.css">
    <style>
        /* Inline styles moved to home-page.css for better organization */
        /* Only critical loading styles remain here */
        .site-content {
            opacity: 0;
            transition: opacity 0.3s ease;
        }
        .site-content.loaded {
            opacity: 1;
        }
        
        /* Ripple effect for click animations */
        .ripple {
            position: absolute;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.4);
            transform: scale(0);
            animation: ripple-animation 0.6s linear;
            pointer-events: none;
        }
        
        @keyframes ripple-animation {
            to {
                transform: scale(4);
                opacity: 0;
            }
        }
        
        /* Touch device optimizations */
        .touch-device .blog-card:hover,
        .touch-device .service-card:hover {
            transform: none;
        }
        
        .touch-device .touch-hover {
            transform: translateY(-3px) scale(1.02);
            box-shadow: 0 4px 8px rgba(0,0,0,0.15);
        }
    </style>
</head>

<body>
    <div class="site-content">
        <!-- Preloader end -->
        <!-- Header start -->
        <?php
        include 'top_nav.php'
        ?>
        <!-- Header end -->
        <!-- Homescreen content start -->
        <section id="homescreen" class="container-fluid">
            
            <!-- Hero Section -->
            <div class="hero-section">
                <div class="hero-content">
                    <div class="hero-badge">
                        <i class="fas fa-tools"></i>
                        <span>Quality Service Since 2020</span>
                    </div>
                    <h1 class="hero-title">
                        Quality Service, <span class="text-gradient">Every Mile</span>
                    </h1>
                    <p class="hero-subtitle">
                        Professional automotive services, GPS tracking solutions, and expert car care 
                        to keep your vehicle running at its best.
                    </p>
                    <div class="hero-cta">
                        <a href="services.php" class="cta-button primary">
                            <i class="fas fa-wrench"></i>
                            Our Services
                        </a>
                        <a href="contact.php" class="cta-button secondary">
                            <i class="fas fa-phone"></i>
                            Get Quote
                        </a>
                    </div>
                    <div class="hero-stats">
                        <div class="stat-item">
                            <div class="stat-number">500+</div>
                            <div class="stat-label">Happy Customers</div>
                        </div>
                        <div class="stat-item">
                            <div class="stat-number">5+</div>
                            <div class="stat-label">Years Experience</div>
                        </div>
                        <div class="stat-item">
                            <div class="stat-number">24/7</div>
                            <div class="stat-label">Support</div>
                        </div>
                    </div>
                </div>
                <div class="hero-image">
                    <img src="assets/images/gallery/car_heat.jpg" alt="Professional Car Service" class="hero-img">
                    <div class="hero-features">
                        <div class="feature-badge">
                            <i class="fas fa-map-marker-alt"></i>
                            <span>GPS Tracking</span>
                        </div>
                        <div class="feature-badge">
                            <i class="fas fa-shield-alt"></i>
                            <span>Secure Service</span>
                        </div>
                    </div>
                </div>
            </div>

            <?php echo displayHomeAd('home_top'); ?>

            <!-- Dashboard Warning Section -->
            <div class="home-section featured-content">
                <div class="featured-card">
                    <div class="featured-image">
                        <img src="assets/images/gallery/car_heat.jpg" alt="Car Dashboard" class="featured-img" />
                        <div class="featured-overlay">
                            <i class="fas fa-play-circle"></i>
                        </div>
                    </div>
                    <div class="featured-content-text">
                        <div class="featured-category">
                            <i class="fas fa-exclamation-triangle"></i>
                            <span>Warning Lights Guide</span>
                        </div>
                        <h3 class="featured-title">
                            በመኪና ዳሽቦርድ ላይ ያሉ ምልክቶች
                        </h3>
                        <h4 class="featured-subtitle">
                            The most common car warning lights guide
                        </h4>
                        <div class="warning-lights">
                            <img src="assets/images/gallery/dashboard_lights.jpg" alt="Dashboard Warning Lights" class="lights-img" />
                        </div>
                        <a href="dashboard.php" class="featured-link">
                            Learn More <i class="fas fa-arrow-right"></i>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Services Section -->
            <div class="home-section">
                <div class="section-header">
                    <h2 class="section-title">Our Expert Services</h2>
                    <p class="section-subtitle">Comprehensive automotive solutions for all your vehicle needs</p>
                </div>
                
                <div class="cards-grid">
                    <!-- Car Body Works -->
                    <div class="card service-card">
                        <div class="service-icon">
                            <i class="fas fa-car"></i>
                        </div>
                        <h3 class="service-title">Car Body Works</h3>
                        <ul class="service-features">
                            <li><i class="fas fa-check"></i> Collision repair & dent removal</li>
                            <li><i class="fas fa-check"></i> Paint & refinishing</li>
                            <li><i class="fas fa-check"></i> Frame straightening</li>
                            <li><i class="fas fa-check"></i> Bumper repair</li>
                        </ul>
                        <a href="services.php?category=body" class="service-link">
                            Learn More <i class="fas fa-arrow-right"></i>
                        </a>
                    </div>

                    <!-- Engine Repair -->
                    <div class="card service-card">
                        <div class="service-icon">
                            <i class="fas fa-cogs"></i>
                        </div>
                        <h3 class="service-title">Engine Repair</h3>
                        <ul class="service-features">
                            <li><i class="fas fa-check"></i> Engine diagnostics</li>
                            <li><i class="fas fa-check"></i> Complete overhauls</li>
                            <li><i class="fas fa-check"></i> Performance tuning</li>
                            <li><i class="fas fa-check"></i> Maintenance checks</li>
                        </ul>
                        <a href="services.php?category=engine" class="service-link">
                            Learn More <i class="fas fa-arrow-right"></i>
                        </a>
                    </div>

                    <!-- Electrical Works -->
                    <div class="card service-card">
                        <div class="service-icon">
                            <i class="fas fa-bolt"></i>
                        </div>
                        <h3 class="service-title">Electrical Systems</h3>
                        <ul class="service-features">
                            <li><i class="fas fa-check"></i> Wiring diagnostics</li>
                            <li><i class="fas fa-check"></i> Battery replacement</li>
                            <li><i class="fas fa-check"></i> Alternator repair</li>
                            <li><i class="fas fa-check"></i> Lighting systems</li>
                        </ul>
                        <a href="services.php?category=electrical" class="service-link">
                            Learn More <i class="fas fa-arrow-right"></i>
                        </a>
                    </div>

                    <!-- GPS Tracking -->
                    <div class="card service-card featured">
                        <div class="service-icon">
                            <i class="fas fa-satellite-dish"></i>
                        </div>
                        <h3 class="service-title">GPS Tracking</h3>
                        <ul class="service-features">
                            <li><i class="fas fa-check"></i> Real-time tracking</li>
                            <li><i class="fas fa-check"></i> Fleet management</li>
                            <li><i class="fas fa-check"></i> Security alerts</li>
                            <li><i class="fas fa-check"></i> Mobile app access</li>
                        </ul>
                        <a href="services.php?category=gps" class="service-link">
                            Learn More <i class="fas fa-arrow-right"></i>
                        </a>
                    </div>
                </div>
            </div>

            <?php echo displayHomeAd('home_middle'); ?>

            <!-- Products Section -->
            <div class="home-section">
                <div class="section-header">
                    <h2 class="section-title">Featured Products</h2>
                    <p class="section-subtitle">Quality automotive parts and accessories for your vehicle</p>
                </div>
                
                <div class="cards-grid">
                    <?php
                    $db = new DB_con();
                    $products_query = "SELECT * FROM tbl_products WHERE featured = 1 LIMIT 4";
                    $products_result = $db->select($products_query);
                    
                    if ($products_result && mysqli_num_rows($products_result) > 0) {
                        while ($product = mysqli_fetch_assoc($products_result)) {
                            ?>
                            <div class="card product-card">
                                <?php if (!empty($product['image']) && file_exists('uploads/products/' . $product['image'])): ?>
                                    <img src="uploads/products/<?php echo htmlspecialchars($product['image']); ?>" 
                                         alt="<?php echo htmlspecialchars($product['name']); ?>" 
                                         class="product-image"/>
                                <?php else: ?>
                                    <img src="assets/images/default-product.jpg" 
                                         alt="<?php echo htmlspecialchars($product['name']); ?>" 
                                         class="product-image"/>
                                <?php endif; ?>
                                
                                <div class="product-badge">
                                    <span class="badge-text">Featured</span>
                                </div>
                                
                                <h3 class="product-title">
                                    <?php echo htmlspecialchars($product['name']); ?>
                                </h3>
                                
                                <p class="product-description">
                                    <?php echo htmlspecialchars(substr($product['description'], 0, 100)) . '...'; ?>
                                </p>
                                
                                <div class="product-price">
                                    ETB <?php echo number_format($product['price'], 2); ?>
                                </div>
                                
                                <div class="product-actions">
                                    <a href="ecommerce/pages/product_details.php?id=<?php echo $product['id']; ?>" class="btn-primary">
                                        <i class="fas fa-eye"></i> View Details
                                    </a>
                                    <button onclick="addToCart(<?php echo $product['id']; ?>)" class="btn-outline">
                                        <i class="fas fa-cart-plus"></i> Add to Cart
                                    </button>
                                </div>
                            </div>
                            <?php
                        }
                    } else {
                        // Default products if none in database
                        ?>
                        <div class="card product-card">
                            <img src="assets/images/default-product.jpg" alt="Car Battery" class="product-image"/>
                            <div class="product-badge">
                                <span class="badge-text">Popular</span>
                            </div>
                            <h3 class="product-title">Car Battery</h3>
                            <p class="product-description">High-quality automotive batteries for reliable performance...</p>
                            <div class="product-price">ETB 2,500.00</div>
                            <div class="product-actions">
                                <a href="ecommerce/" class="btn-primary">
                                    <i class="fas fa-eye"></i> View Details
                                </a>
                                <a href="ecommerce/" class="btn-outline">
                                    <i class="fas fa-cart-plus"></i> Add to Cart
                                </a>
                            </div>
                        </div>
                        
                        <div class="card product-card">
                            <img src="assets/images/default-product.jpg" alt="Engine Oil" class="product-image"/>
                            <div class="product-badge">
                                <span class="badge-text">New</span>
                            </div>
                            <h3 class="product-title">Premium Engine Oil</h3>
                            <p class="product-description">Synthetic motor oil for enhanced engine protection...</p>
                            <div class="product-price">ETB 850.00</div>
                            <div class="product-actions">
                                <a href="ecommerce/" class="btn-primary">
                                    <i class="fas fa-eye"></i> View Details
                                </a>
                                <a href="ecommerce/" class="btn-outline">
                                    <i class="fas fa-cart-plus"></i> Add to Cart
                                </a>
                            </div>
                        </div>
                        <?php
                    }
                    ?>
                </div>
                
                <div style="text-align: center; margin-top: 30px;">
                    <a href="ecommerce/" class="cta-button">
                        <i class="fas fa-shopping-bag"></i>
                        View All Products
                    </a>
                </div>
            </div>

            <!-- Blog Posts Section -->
            <div class="home-section">
                <div class="section-header">
                    <h2 class="section-title">Latest Blog Posts</h2>
                    <p class="section-subtitle">Stay updated with automotive tips, maintenance guides, and industry news</p>
                </div>
                
                <div class="cards-grid">
                    <?php
                    $db = new DB_con();
                    $result = $db->fetcharticle();
                    
                    if ($result && mysqli_num_rows($result) > 0) {
                        $count = 0;
                        while ($row = mysqli_fetch_assoc($result) && $count < 4) {
                            ?>
                            <div class="card blog-card">
                                <?php if (!empty($row['image_url']) && file_exists('uploads/blogs/' . $row['image_url'])): ?>
                                    <img src="uploads/blogs/<?php echo htmlspecialchars($row['image_url']); ?>" 
                                         alt="<?php echo htmlspecialchars($row['title']); ?>" 
                                         class="blog-image"/>
                                <?php else: ?>
                                    <img src="assets/images/gallery/default-blog.jpg" 
                                         alt="<?php echo htmlspecialchars($row['title']); ?>" 
                                         class="blog-image"/>
                                <?php endif; ?>
                                
                                <div class="blog-content">
                                    <div class="blog-meta">
                                        <span class="blog-author">
                                            <i class="fas fa-user"></i> 
                                            <?php echo htmlspecialchars($row['writer']); ?>
                                        </span>
                                        <span class="blog-date">
                                            <i class="far fa-calendar"></i> 
                                            <?php echo date('M d, Y', strtotime($row['date'])); ?>
                                        </span>
                                    </div>
                                    
                                    <h3 class="blog-title">
                                        <?php echo htmlspecialchars($row['title']); ?>
                                    </h3>
                                    
                                    <p class="blog-excerpt">
                                        <?php echo htmlspecialchars(substr($row['s_article'], 0, 120)) . '...'; ?>
                                    </p>
                                    
                                    <a href="blog.php?blogid=<?php echo $row['id']; ?>" class="service-link">
                                        Read More <i class="fas fa-arrow-right"></i>
                                    </a>
                                </div>
                            </div>
                            <?php
                            $count++;
                        }
                    } else {
                        echo '<div class="card no-content">
                                <div class="no-content-icon">
                                    <i class="fas fa-newspaper"></i>
                                </div>
                                <h3>No Blog Posts Yet</h3>
                                <p>We\'re working on bringing you the latest automotive insights and tips.</p>
                                <a href="contact.php" class="cta-button">
                                    Subscribe for Updates
                                </a>
                              </div>';
                    }
                    ?>
                </div>
                
                <?php if ($result && mysqli_num_rows($result) > 0): ?>
                <div style="text-align: center; margin-top: 30px;">
                    <a href="blog.php" class="cta-button">
                        <i class="fas fa-newspaper"></i>
                        Read All Blogs
                    </a>
                </div>
                <?php endif; ?>
            </div>

            <?php echo displayHomeAd('small_banner'); ?>

            <!-- Testimonials Carousel Section -->
            <div class="home-section testimonials-section">
                <div class="section-header">
                    <h2 class="section-title">What Our Customers Say</h2>
                    <p class="section-subtitle">Real feedback from our satisfied customers</p>
                </div>
                
                <div class="testimonials-carousel">
                    <div class="carousel-container">
                        <div class="testimonial-slides">
                            <!-- Testimonial 1 -->
                            <div class="testimonial-item">
                                <div class="testimonial-content">
                                    <div class="testimonial-stars">
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                    </div>
                                    <div class="testimonial-image">
                                        <img src="assets/images/Nati-logo.png" alt="Abebe Kebede" class="testimonial-img">
                                    </div>
                                    <div class="testimonial-text">
                                        <p>"The GPS tracking service has been a game-changer for our fleet management. Real-time tracking helps us optimize routes and ensure driver safety. The peace of mind knowing exactly where our vehicles are is invaluable."</p>
                                        <h5>Abebe Kebede</h5>
                                        <span>Fleet Manager, TransEthiopia</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Testimonial 2 -->
                            <div class="testimonial-item">
                                <div class="testimonial-content">
                                    <div class="testimonial-stars">
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                    </div>
                                    <div class="testimonial-image">
                                        <img src="assets/images/Nati-logo.png" alt="Sara Mohammed" class="testimonial-img">
                                    </div>
                                    <div class="testimonial-text">
                                        <p>"After installing the GPS tracking system, we've seen a significant improvement in vehicle security. The instant alerts and geofencing features help us protect our assets 24/7."</p>
                                        <h5>Sara Mohammed</h5>
                                        <span>Business Owner, Addis Logistics</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Testimonial 3 -->
                            <div class="testimonial-item">
                                <div class="testimonial-content">
                                    <div class="testimonial-stars">
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                    </div>
                                    <div class="testimonial-image">
                                        <img src="assets/images/Nati-logo.png" alt="Daniel Tesfaye" class="testimonial-img">
                                    </div>
                                    <div class="testimonial-text">
                                        <p>"The customer service team is exceptional. They helped us set up the tracking system and provided comprehensive training. Now we can monitor fuel consumption and maintenance schedules efficiently."</p>
                                        <h5>Daniel Tesfaye</h5>
                                        <span>Transport Director, Blue Nile Express</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="carousel-controls">
                            <button class="prev-btn" onclick="moveTestimonial(-1)">❮</button>
                            <button class="next-btn" onclick="moveTestimonial(1)">❯</button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- GPS Features Comparison Section -->
            <div class="home-section comparison-section">
                <div class="section-header">
                    <h2 class="section-title">Why Choose Our GPS Tracking?</h2>
                    <p class="section-subtitle">Compare our features with other providers</p>
                </div>
                
                <div class="comparison-container">
                    <div class="comparison-table">
                        <div class="table-header">
                            <div class="header-cell feature-cell">
                                <i class="fas fa-list"></i>
                                <span>Features</span>
                            </div>
                            <div class="header-cell our-cell">
                                <i class="fas fa-crown"></i>
                                <span>Nati Automotive GPS</span>
                            </div>
                            <div class="header-cell competitor-cell">
                                <i class="fas fa-users"></i>
                                <span>Other Providers</span>
                            </div>
                        </div>
                        
                        <div class="table-row">
                            <div class="feature-cell">
                                <i class="fas fa-crosshairs"></i>
                                <span>Tracking Accuracy</span>
                            </div>
                            <div class="our-cell">
                                <span class="highlight">Up to 2m accuracy</span>
                                <i class="fas fa-check-circle"></i>
                            </div>
                            <div class="competitor-cell">
                                <span>5-10m average</span>
                                <i class="fas fa-minus-circle"></i>
                            </div>
                        </div>
                        
                        <div class="table-row">
                            <div class="feature-cell">
                                <i class="fas fa-shield-alt"></i>
                                <span>Security Features</span>
                            </div>
                            <div class="our-cell">
                                <span class="highlight">End-to-end encryption</span>
                                <i class="fas fa-check-circle"></i>
                            </div>
                            <div class="competitor-cell">
                                <span>Basic encryption</span>
                                <i class="fas fa-minus-circle"></i>
                            </div>
                        </div>
                        
                        <div class="table-row">
                            <div class="feature-cell">
                                <i class="fas fa-clock"></i>
                                <span>Real-time Updates</span>
                            </div>
                            <div class="our-cell">
                                <span class="highlight">Every 10 seconds</span>
                                <i class="fas fa-check-circle"></i>
                            </div>
                            <div class="competitor-cell">
                                <span>Every 2-5 minutes</span>
                                <i class="fas fa-minus-circle"></i>
                            </div>
                        </div>
                        
                        <div class="table-row">
                            <div class="feature-cell">
                                <i class="fas fa-mobile-alt"></i>
                                <span>Mobile App</span>
                            </div>
                            <div class="our-cell">
                                <span class="highlight">Free, full-featured</span>
                                <i class="fas fa-check-circle"></i>
                            </div>
                            <div class="competitor-cell">
                                <span>Limited features</span>
                                <i class="fas fa-minus-circle"></i>
                            </div>
                        </div>
                        
                        <div class="table-row">
                            <div class="feature-cell">
                                <i class="fas fa-headset"></i>
                                <span>Customer Support</span>
                            </div>
                            <div class="our-cell">
                                <span class="highlight">24/7 local support</span>
                                <i class="fas fa-check-circle"></i>
                            </div>
                            <div class="competitor-cell">
                                <span>Limited hours</span>
                                <i class="fas fa-minus-circle"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- GPS CTA Section -->
            <div class="home-section cta-section">
                <div class="cta-content">
                    <div class="cta-text">
                        <h2 class="cta-title">Ready to Track Your Fleet?</h2>
                        <p class="cta-description">
                            Get started with our advanced GPS tracking solution today. 
                            Protect your vehicles, optimize routes, and gain complete control over your fleet.
                        </p>
                        <ul class="cta-features">
                            <li><i class="fas fa-shield-alt"></i> Enhanced Vehicle Security</li>
                            <li><i class="fas fa-map-marked-alt"></i> Real-Time Location Tracking</li>
                            <li><i class="fas fa-gas-pump"></i> Fuel Consumption Monitoring</li>
                            <li><i class="fas fa-bell"></i> Instant Alert Notifications</li>
                        </ul>
                    </div>
                    <div class="cta-buttons">
                        <a href="contact.php?service=gps" class="cta-button primary large">
                            <i class="fas fa-rocket"></i>
                            Get Started Now
                        </a>
                        <a href="tel:+251911123456" class="cta-button secondary large">
                            <i class="fas fa-phone"></i>
                            Call: +251 911-123456
                        </a>
                        <div class="contact-info">
                            <p><i class="fas fa-clock"></i> Available 24/7 for support</p>
                        </div>
                    </div>
                </div>
            </div>

            <?php echo displayHomeAd('home_bottom'); ?>

            <!-- Automotive Services Section -->
            <div class="home-section">
                <div class="section-header">
                    <h2 class="section-title">Expert Automotive Services</h2>
                    <p class="section-subtitle">Your trusted partner for comprehensive vehicle care and maintenance</p>
                </div>
                
                <div class="cards-grid">
                    <!-- Car Body Works -->
                    <div class="card service-card">
                        <div class="service-icon">
                            <i class="fas fa-car"></i>
                        </div>
                        <h3 class="service-title">Car Body Works</h3>
                        <ul class="service-features">
                            <li><i class="fas fa-check"></i> Collision repair & dent removal</li>
                            <li><i class="fas fa-check"></i> Professional painting services</li>
                            <li><i class="fas fa-check"></i> Rust treatment & prevention</li>
                            <li><i class="fas fa-check"></i> Panel beating & restoration</li>
                        </ul>
                        <a href="services.php#body-works" class="service-link">Learn More <i class="fas fa-arrow-right"></i></a>
                    </div>

                    <!-- Engine Repair -->
                    <div class="card service-card">
                        <div class="service-icon">
                            <i class="fas fa-cogs"></i>
                        </div>
                        <h3 class="service-title">Engine Repair</h3>
                        <ul class="service-features">
                            <li><i class="fas fa-check"></i> Complete engine diagnostics</li>
                            <li><i class="fas fa-check"></i> Engine overhaul & rebuilding</li>
                            <li><i class="fas fa-check"></i> Performance optimization</li>
                            <li><i class="fas fa-check"></i> Preventive maintenance</li>
                        </ul>
                        <a href="services.php#engine-repair" class="service-link">Learn More <i class="fas fa-arrow-right"></i></a>
                    </div>

                    <!-- Car Electric Works -->
                    <div class="card service-card">
                        <div class="service-icon">
                            <i class="fas fa-bolt"></i>
                        </div>
                        <h3 class="service-title">Car Electric Works</h3>
                        <ul class="service-features">
                            <li><i class="fas fa-check"></i> Electrical system diagnosis</li>
                            <li><i class="fas fa-check"></i> Battery service & replacement</li>
                            <li><i class="fas fa-check"></i> Starter & alternator repair</li>
                            <li><i class="fas fa-check"></i> Lighting system maintenance</li>
                        </ul>
                        <a href="services.php#electric-works" class="service-link">Learn More <i class="fas fa-arrow-right"></i></a>
                    </div>

                    <!-- Wheels & Brakes -->
                    <div class="card service-card">
                        <div class="service-icon">
                            <i class="fas fa-dharmachakra"></i>
                        </div>
                        <h3 class="service-title">Wheels & Brakes</h3>
                        <ul class="service-features">
                            <li><i class="fas fa-check"></i> Wheel alignment & balancing</li>
                            <li><i class="fas fa-check"></i> Brake system inspection</li>
                            <li><i class="fas fa-check"></i> Brake pad replacement</li>
                            <li><i class="fas fa-check"></i> Tire rotation & fitting</li>
                        </ul>
                        <a href="services.php#wheels-brakes" class="service-link">Learn More <i class="fas fa-arrow-right"></i></a>
                    </div>
                </div>

                <div class="cta-section">
                    <div class="cta-content">
                        <h3 class="cta-title">Trust Nati Automotive for Excellence</h3>
                        <p class="cta-description">Experience the difference with our certified technicians and state-of-the-art facilities</p>
                        <div class="cta-buttons">
                            <a href="location.php" class="cta-button cta-primary">Schedule Service</a>
                            <a href="tel:+251911123456" class="cta-button cta-secondary">Call Us Now</a>
                        </div>
                    </div>
                </div>
            </div>

            <?php echo displayHomeAd('home_bottom'); ?>
        </section>
        <!-- Homescreen content end -->
        <!-- Tabbar start -->
        <?php include 'partial-front/bottom_nav.php'; ?>
        <!-- Tabbar end -->
        <!--SideBar setting menu start-->
        <?php
        include 'option.php'
        ?>
        <!--SideBar setting menu end-->
        <!-- pwa install app popup Start -->
        <!-- <div class="offcanvas offcanvas-bottom addtohome-popup theme-offcanvas" tabindex="-1" id="offcanvas" aria-modal="true" role="dialog">
			<button type="button" class="btn-close text-reset popup-close-home" data-bs-dismiss="offcanvas" aria-label="Close"></button>
			<div class="offcanvas-body small">
				<img src="assets/images/logo/logo.png" alt="logo" class="logo-popup">
				<p class="title font-w600">Guruji</p>
				<p class="install-txt">Install Guruji - Online Learning & Educational Courses PWA to your home screen for easy access, just like any other app</p>
				<a href="javascript:void(0)" class="theme-btn install-app btn-inline addhome-btn" id="installApp">Add to Home Screen</a>
			</div>
		</div> -->
        <!-- pwa install app popup End -->
    </div>
    <script src="assets/js/jquery.min.js"></script>
    <script src="assets/js/slick.min.js"></script>
    <script src="assets/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/modal.js"></script>
    <script src="assets/js/home-page.js" onerror="console.warn('Home page JS not found')"></script>
</body>

</html>