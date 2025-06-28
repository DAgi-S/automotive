<?php 
session_start();
define('INCLUDED', true);
$page_title = "Home";
require_once 'includes/header.php'; 
require_once 'includes/website_content_functions.php';
require_once 'includes/ad_functions.php';
?>

<!-- Custom CSS for Enhanced Homepage Design -->
<style>
/* Enhanced Homepage Styles */
.main-content {
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    min-height: 100vh;
}

.section-title {
    font-weight: 700;
    color: #2c3e50;
    position: relative;
    margin-bottom: 1rem;
}

.section-title::after {
    content: '';
    position: absolute;
    bottom: -8px;
    left: 50%;
    transform: translateX(-50%);
    width: 60px;
    height: 3px;
    background: linear-gradient(135deg, #007bff, #0056b3);
    border-radius: 2px;
}

.section-subtitle {
    font-size: 1.1rem;
    margin-bottom: 2rem !important;
}

/* Enhanced Service Cards */
.service-card {
    transition: all 0.3s ease;
    border-radius: 15px !important;
    overflow: hidden;
    background: #fff;
    border: 1px solid rgba(0,0,0,0.05) !important;
}

.service-card:hover {
    transform: translateY(-8px);
    box-shadow: 0 15px 35px rgba(0,0,0,0.1) !important;
}

.service-icon {
    background: linear-gradient(135deg, #007bff10, #0056b320);
    border-radius: 50%;
    width: 80px;
    height: 80px;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 1rem auto;
}

/* Enhanced Product Cards */
.product-card {
    transition: all 0.3s ease;
    border-radius: 15px !important;
    overflow: hidden;
    background: #fff;
    border: 1px solid rgba(0,0,0,0.05) !important;
}

.product-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 12px 25px rgba(0,0,0,0.1) !important;
}

.product-card .card-img-top {
    transition: transform 0.3s ease;
}

.product-card:hover .card-img-top {
    transform: scale(1.05);
}

/* Enhanced Blog Cards */
.blog-card {
    transition: all 0.3s ease;
    border-radius: 15px !important;
    overflow: hidden;
    background: #fff;
    border: 1px solid rgba(0,0,0,0.05) !important;
}

.blog-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 12px 25px rgba(0,0,0,0.1) !important;
}

/* Enhanced Client Cards */
.client-card {
    transition: all 0.3s ease;
    border-radius: 15px !important;
    background: #fff;
    border: 1px solid rgba(0,0,0,0.05) !important;
}

.client-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 8px 20px rgba(0,0,0,0.08) !important;
}

/* Analytics Section Enhancement */
.analytics-section .card {
    border-radius: 20px !important;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important;
    border: none !important;
    box-shadow: 0 10px 30px rgba(102, 126, 234, 0.3) !important;
}

.analytics-card {
    transition: all 0.3s ease;
    border-radius: 15px;
    backdrop-filter: blur(10px);
    border: 1px solid rgba(255,255,255,0.2);
}

.analytics-card:hover {
    transform: translateY(-3px);
    background: rgba(255,255,255,0.15) !important;
}

/* Sidebar Enhancements */
.sidebar-section {
    background: #fff;
    border-radius: 15px;
    padding: 1.5rem;
    margin-bottom: 1.5rem;
    box-shadow: 0 5px 15px rgba(0,0,0,0.08);
    border: 1px solid rgba(0,0,0,0.05);
}

.sidebar-section h5 {
    color: #2c3e50;
    font-weight: 600;
    margin-bottom: 1rem;
    padding-bottom: 0.5rem;
    border-bottom: 2px solid #e9ecef;
}

/* Social Media Cards Enhancement */
.social-card {
    border-radius: 15px !important;
    transition: all 0.3s ease;
    border: none !important;
    overflow: hidden;
    position: relative;
}

.social-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: linear-gradient(135deg, rgba(255,255,255,0.1), rgba(255,255,255,0.05));
    z-index: 1;
}

.social-card .card-body {
    position: relative;
    z-index: 2;
}

.social-card:hover {
    transform: translateY(-5px) scale(1.02);
    box-shadow: 0 15px 35px rgba(0,0,0,0.2) !important;
}

/* Business Hours Enhancement */
.business-hours-list {
    list-style: none;
    padding: 0;
}

.business-hours-list li {
    padding: 0.5rem 0;
    border-bottom: 1px solid #e9ecef;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.business-hours-list li:last-child {
    border-bottom: none;
}

.business-hours-list .day {
    font-weight: 500;
    color: #2c3e50;
}

.business-hours-list .hours {
    color: #6c757d;
    font-size: 0.9rem;
}

/* Testimonials Enhancement */
.testimonial-section {
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    border-radius: 20px;
    padding: 2rem;
    margin: 2rem 0;
    border: 1px solid rgba(0,0,0,0.05);
}

/* FAQ Enhancement */
.faq-section {
    background: #fff;
    border-radius: 20px;
    padding: 2rem;
    margin: 2rem 0;
    box-shadow: 0 5px 15px rgba(0,0,0,0.08);
}

.accordion-item {
    border-radius: 10px !important;
    margin-bottom: 0.5rem;
    border: 1px solid rgba(0,0,0,0.05) !important;
}

.accordion-button {
    border-radius: 10px !important;
    font-weight: 500;
}

.accordion-button:not(.collapsed) {
    background: linear-gradient(135deg, #007bff10, #0056b320);
    color: #007bff;
}

/* Loading Animation */
.loading-spinner {
    display: inline-block;
    width: 20px;
    height: 20px;
    border: 3px solid rgba(0,0,0,.1);
    border-radius: 50%;
    border-top-color: #007bff;
    animation: spin 1s ease-in-out infinite;
}

@keyframes spin {
    to { transform: rotate(360deg); }
}

/* Responsive Enhancements */
@media (max-width: 768px) {
    .section-title {
        font-size: 1.5rem;
    }
    
    .analytics-card {
        margin-bottom: 1rem;
    }
    
    .social-card {
        margin-bottom: 1rem;
    }
    
    .sidebar-section {
        margin-bottom: 1rem;
    }
}

/* Smooth Scroll */
html {
    scroll-behavior: smooth;
}

/* Enhanced Buttons */
.btn {
    border-radius: 25px !important;
    padding: 0.5rem 1.5rem;
    font-weight: 500;
    transition: all 0.3s ease;
}

.btn-primary {
    background: linear-gradient(135deg, #007bff, #0056b3);
    border: none;
}

.btn-primary:hover {
    background: linear-gradient(135deg, #0056b3, #004085);
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(0,123,255,0.3);
}

.btn-outline-primary {
    border: 2px solid #007bff;
    color: #007bff;
    background: transparent;
}

.btn-outline-primary:hover {
    background: #007bff;
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(0,123,255,0.3);
}

/* Enhanced responsive design */
@media (max-width: 991px) {
    .sidebar-section {
        margin-bottom: 2rem;
    }
    
    .social-card {
        margin-bottom: 1rem;
    }
}

@media (max-width: 576px) {
    .contact-info .contact-item {
        flex-direction: column;
        align-items: flex-start;
    }
    
    .contact-info .contact-item i {
        margin-bottom: 0.25rem;
    }
}

/* Enhanced Quick Links */
.quick-links-list .quick-link-item a {
    color: #6c757d;
    transition: all 0.3s ease;
    border: 1px solid transparent;
}

.quick-links-list .quick-link-item a:hover {
    background: linear-gradient(135deg, #007bff10, #0056b320);
    color: #007bff;
    border-color: rgba(0,123,255,0.1);
    transform: translateX(5px);
}

.quick-links-list .quick-link-item a:hover i.fa-chevron-right {
    transform: translateX(3px);
    color: #007bff;
}

/* Enhanced Business Hours */
.business-hours-container .business-hours-item {
    transition: all 0.3s ease;
}

.business-hours-container .business-hours-item:hover {
    background: rgba(0,123,255,0.05);
    padding-left: 0.5rem !important;
    padding-right: 0.5rem !important;
}

.business-hours-container .business-hours-item:last-child {
    border-bottom: none !important;
}

.current-status {
    border: 1px solid #e9ecef;
    transition: all 0.3s ease;
}

.current-status:hover {
    background: #e3f2fd !important;
    border-color: #007bff;
}

/* Enhanced Testimonials */
.testimonial-card {
    background: rgba(255,255,255,0.95);
    border-radius: 20px;
    box-shadow: 0 10px 30px rgba(0,0,0,0.1);
    border: 1px solid rgba(0,0,0,0.05);
    backdrop-filter: blur(10px);
}

.testimonial-stars i {
    font-size: 1.2rem;
    transition: all 0.3s ease;
}

.testimonial-section .carousel-indicators {
    bottom: -50px;
}

.testimonial-section .carousel-indicators button {
    width: 12px;
    height: 12px;
    border-radius: 50%;
    border: 2px solid #007bff;
    background: transparent;
}

.testimonial-section .carousel-indicators button.active {
    background: #007bff;
}

.testimonial-section .carousel-control-prev,
.testimonial-section .carousel-control-next {
    width: 50px;
    height: 50px;
    top: 50%;
    transform: translateY(-50%);
    background: rgba(0,123,255,0.1);
    border-radius: 50%;
    border: 2px solid rgba(0,123,255,0.2);
    transition: all 0.3s ease;
}

.testimonial-section .carousel-control-prev:hover,
.testimonial-section .carousel-control-next:hover {
    background: rgba(0,123,255,0.2);
    border-color: #007bff;
}

.testimonial-section .carousel-control-prev {
    left: -60px;
}

.testimonial-section .carousel-control-next {
    right: -60px;
}

/* Enhanced FAQ Section */
.faq-section .accordion-item {
    transition: all 0.3s ease;
}

.faq-section .accordion-item:hover {
    box-shadow: 0 5px 15px rgba(0,0,0,0.1);
    transform: translateY(-2px);
}

.faq-section .accordion-button {
    font-size: 1.1rem;
    padding: 1.25rem 1.5rem;
}

.faq-section .accordion-button:focus {
    box-shadow: 0 0 0 0.25rem rgba(0,123,255,0.25);
}

.faq-section .accordion-body {
    padding: 1.5rem;
    background: #f8f9fa;
}

/* Loading States */
.btn.loading {
    pointer-events: none;
    opacity: 0.8;
}

/* Scroll Animation Classes */
.fade-in-up {
    opacity: 0;
    transform: translateY(30px);
    transition: all 0.6s ease;
}

.fade-in-up.visible {
    opacity: 1;
    transform: translateY(0);
}

/* Performance Optimizations */
.service-card,
.product-card,
.blog-card,
.client-card {
    will-change: transform;
}

/* Print Styles */
@media print {
    .sidebar-section,
    .testimonial-section .carousel-control-prev,
    .testimonial-section .carousel-control-next,
    .testimonial-section .carousel-indicators {
        display: none !important;
    }
}
</style>

<div class="container mt-4">
    <div class="row">
        <!-- Main Content -->
        <div class="col-lg-9">
            <!-- Top Advertisement Banner -->
            <?php echo displayAd($conn, 'home_top'); ?>

            <!-- Hero Section with Dynamic Carousel -->
            <?php echo renderHeroCarousel($conn); ?>

            <!-- Business Analytics Section -->
            <?php if (isSectionActive($conn, 'analytics')): ?>
            <section class="analytics-section mb-4">
                <div class="card bg-gradient-primary text-white">
                    <div class="card-body p-4">
                        <div class="text-center mb-4">
                            <h3 class="mb-0"><?php echo getWebsiteSetting($conn, 'analytics_title', 'Our Business Analytics'); ?></h3>
                            <p class="mb-0 opacity-75"><?php echo getWebsiteSetting($conn, 'analytics_subtitle', 'Real-time insights into our automotive services'); ?></p>
                        </div>
                        
                        <?php echo renderAnalyticsDisplay($conn); ?>
                    </div>
                </div>
            </section>
            <?php endif; ?>

            <!-- Middle Advertisement Banner -->
            <?php echo displayAd($conn, 'home_middle'); ?>

            <!-- Featured Services Section -->
            <?php if (isSectionActive($conn, 'services')): ?>
            <section class="services-section mb-5">
                <div class="text-center mb-4">
                    <h2 class="section-title"><?php echo getWebsiteSetting($conn, 'services_title', 'Our Featured Services'); ?></h2>
                    <p class="section-subtitle text-muted"><?php echo getWebsiteSetting($conn, 'services_subtitle', 'Professional automotive services you can trust'); ?></p>
                </div>
                
                <div class="row">
                    <?php
                    // Fetch featured services from database
                    try {
                        $limit = (int) getWebsiteSetting($conn, 'featured_services_limit', 3);
                        $stmt = $conn->prepare("SELECT service_name, icon_class, description FROM tbl_services WHERE status = 'active' ORDER BY service_id ASC LIMIT " . $limit);
                        $stmt->execute();
                        $featured_services = $stmt->fetchAll(PDO::FETCH_ASSOC);
                        
                        if (!empty($featured_services)) {
                            foreach ($featured_services as $service) {
                                echo '<div class="col-md-4 mb-4">';
                                echo '<div class="card service-card h-100 shadow-sm border-0">';
                                echo '<div class="card-body text-center p-4">';
                                echo '<div class="service-icon mb-3">';
                                echo '<i class="' . safeHtmlspecialchars($service['icon_class']) . ' fa-3x text-primary"></i>';
                                echo '</div>';
                                echo '<h5 class="card-title">' . safeHtmlspecialchars($service['service_name']) . '</h5>';
                                echo '<p class="card-text text-muted">' . safeHtmlspecialchars($service['description']) . '</p>';
                                echo '<a href="services.php" class="btn btn-primary">Learn More</a>';
                                echo '</div>';
                                echo '</div>';
                                echo '</div>';
                            }
                        } else {
                            echo '<div class="col-12 text-center">';
                            echo '<p class="text-muted">No services available at the moment.</p>';
                            echo '</div>';
                        }
                    } catch (PDOException $e) {
                        echo '<div class="col-12 text-center">';
                        echo '<p class="text-danger">Error loading services. Please try again later.</p>';
                        echo '</div>';
                    }
                    ?>
  </div>
</section>
            <?php endif; ?>

            <!-- Latest Products Section -->
            <?php if (isSectionActive($conn, 'products')): ?>
            <section class="products-section mb-5">
                <div class="text-center mb-4">
                    <h2 class="section-title"><?php echo getWebsiteSetting($conn, 'products_title', 'Latest Products'); ?></h2>
                    <p class="section-subtitle text-muted"><?php echo getWebsiteSetting($conn, 'products_subtitle', 'Quality automotive parts and accessories'); ?></p>
                </div>
                
                <div class="row">
      <?php
                    // Fetch latest products from database
                    try {
                        $limit = (int) getWebsiteSetting($conn, 'latest_products_limit', 3);
                        $stmt = $conn->prepare("SELECT id, name, price, image_url FROM tbl_products WHERE status = 'active' ORDER BY created_at DESC LIMIT " . $limit);
                        $stmt->execute();
                        $latest_products = $stmt->fetchAll(PDO::FETCH_ASSOC);
                        
                        if (!empty($latest_products)) {
                            foreach ($latest_products as $product) {
                                echo '<div class="col-md-4 mb-4">';
                                echo '<div class="card product-card h-100 shadow-sm border-0">';
                                if ($product['image_url']) {
                                    $imagePath = $product['image_url'];
                                    // Fix image path if it doesn't start with http or assets/
                                    if (!str_starts_with($imagePath, 'http') && !str_starts_with($imagePath, 'assets/')) {
                                        $imagePath = 'assets/img/' . $imagePath;
                                    }
                                    echo '<img src="' . safeHtmlspecialchars($imagePath) . '" class="card-img-top" alt="' . safeHtmlspecialchars($product['name']) . '" style="height: 200px; object-fit: cover;" onerror="this.src=\'assets/images/default-product.jpg\'">';
                                } else {
                                    echo '<img src="assets/images/default-product.jpg" class="card-img-top" alt="Default Product" style="height: 200px; object-fit: cover;">';
                                }
                                echo '<div class="card-body">';
                                echo '<h5 class="card-title">' . safeHtmlspecialchars($product['name']) . '</h5>';
                                echo '<p class="card-text text-primary font-weight-bold">Br ' . number_format($product['price'], 2) . '</p>';
                                echo '<a href="products.php?id=' . $product['id'] . '" class="btn btn-outline-primary">View Details</a>';
                                echo '</div>';
                                echo '</div>';
                                echo '</div>';
                            }
                        } else {
                            echo '<div class="col-12 text-center">';
                            echo '<p class="text-muted">No products available at the moment.</p>';
                            echo '</div>';
                        }
                    } catch (PDOException $e) {
                        echo '<div class="col-12 text-center">';
                        echo '<p class="text-danger">Error loading products. Please try again later.</p>';
                        echo '</div>';
                    }
                    ?>
          </div>
            </section>
            <?php endif; ?>

            <!-- Recent Blogs Section -->
            <?php if (isSectionActive($conn, 'blogs')): ?>
            <section class="blogs-section mb-5">
                <div class="text-center mb-4">
                    <h2 class="section-title"><?php echo getWebsiteSetting($conn, 'blogs_title', 'Recent Blog Posts'); ?></h2>
                    <p class="section-subtitle text-muted"><?php echo getWebsiteSetting($conn, 'blogs_subtitle', 'Stay updated with automotive tips and news'); ?></p>
        </div>
                
                <div class="row">
                    <?php
                    // Database connection already available from header
                    try {
                        $limit = (int) getWebsiteSetting($conn, 'recent_blogs_limit', 3);
                        $stmt = $conn->prepare("SELECT id, title, writer, date, s_article, content, image_url, status FROM tbl_blog ORDER BY date DESC LIMIT " . $limit);
                        $stmt->execute();
                        $blogs = $stmt->fetchAll(PDO::FETCH_ASSOC);
                        
                        if (!empty($blogs)) {
                            foreach ($blogs as $blog) {
                                echo '<div class="col-md-4 mb-4">';
                                echo '<div class="card blog-card h-100 shadow-sm border-0">';
                                if ($blog['image_url']) {
                                    $imagePath = $blog['image_url'];
                                    // Fix image path if it doesn't start with http or uploads/
                                    if (!str_starts_with($imagePath, 'http') && !str_starts_with($imagePath, 'uploads/')) {
                                        $imagePath = 'uploads/blogs/' . $imagePath;
                                    }
                                    echo '<img src="' . safeHtmlspecialchars($imagePath) . '" class="card-img-top" alt="' . safeHtmlspecialchars($blog['title']) . '" style="height: 200px; object-fit: cover;" onerror="this.src=\'assets/images/gallery/default-blog.jpg\'">';
                                } else {
                                    echo '<img src="assets/images/gallery/default-blog.jpg" class="card-img-top" alt="Default Blog" style="height: 200px; object-fit: cover;">';
                                }
                                echo '<div class="card-body">';
                                echo '<h5 class="card-title">' . safeHtmlspecialchars($blog['title']) . '</h5>';
                                echo '<p class="card-text text-muted small">By ' . safeHtmlspecialchars($blog['writer']) . ' • ' . date('M d, Y', strtotime($blog['date'])) . '</p>';
                                echo '<p class="card-text">' . safeHtmlspecialchars(substr($blog['s_article'], 0, 100)) . '...</p>';
                                echo '<a href="blog-details.php?id=' . $blog['id'] . '" class="btn btn-outline-primary">Read More</a>';
                                echo '</div>';
                                echo '</div>';
                                echo '</div>';
                            }
                        } else {
                            echo '<div class="col-12 text-center">';
                            echo '<p class="text-muted">No blog posts available at the moment.</p>';
                            echo '</div>';
                        }
                    } catch (PDOException $e) {
                        echo '<div class="col-12 text-center">';
                        echo '<p class="text-danger">Error loading blog posts. Please try again later.</p>';
                        echo '</div>';
                    }
                    ?>
    </div>
  </section>
            <?php endif; ?>

            <!-- Customer Testimonials Section -->
            <?php echo renderTestimonialsCarousel($conn); ?>

            <!-- Clients Section -->
            <?php if (isSectionActive($conn, 'clients')): ?>
            <section class="clients-section mb-5">
                <div class="text-center mb-4">
                    <h2 class="section-title"><?php echo getWebsiteSetting($conn, 'clients_title', 'Our Valued Clients'); ?></h2>
                    <p class="section-subtitle text-muted"><?php echo getWebsiteSetting($conn, 'clients_subtitle', 'Trusted by automotive enthusiasts'); ?></p>
                </div>
                
                <div class="row">
      <?php
                    // Fetch clients from database
                    try {
                        $limit = (int) getWebsiteSetting($conn, 'clients_limit', 4);
                        $stmt = $conn->prepare("SELECT name, new_img_name FROM tbl_user WHERE new_img_name IS NOT NULL ORDER BY created_at DESC LIMIT " . $limit);
                        $stmt->execute();
                        $clients = $stmt->fetchAll(PDO::FETCH_ASSOC);
                        
                        if (!empty($clients)) {
                            foreach ($clients as $client) {
                                echo '<div class="col-md-3 col-sm-6 mb-4">';
                                echo '<div class="card client-card h-100 shadow-sm border-0">';
                                echo '<div class="card-body text-center p-4">';
                                echo '<img src="uploads/' . safeHtmlspecialchars($client['new_img_name']) . '" class="rounded-circle mb-3" alt="' . safeHtmlspecialchars($client['name']) . '" style="width: 80px; height: 80px; object-fit: cover;">';
                                echo '<h6 class="card-title">' . safeHtmlspecialchars($client['name']) . '</h6>';
                                echo '<p class="card-text text-muted small">Satisfied Customer</p>';
                                echo '</div>';
                                echo '</div>';
                                echo '</div>';
                            }
                        } else {
                            echo '<div class="col-12 text-center">';
                            echo '<p class="text-muted">No client testimonials available at the moment.</p>';
                            echo '</div>';
                        }
                    } catch (PDOException $e) {
                        echo '<div class="col-12 text-center">';
                        echo '<p class="text-danger">Error loading clients. Please try again later.</p>';
                        echo '</div>';
                    }
                    ?>
                </div>
            </section>
            <?php endif; ?>

            <!-- FAQ Section -->
            <?php echo renderFAQsAccordion($conn); ?>

            <!-- Bottom Advertisement Banner -->
            <?php echo displayAd($conn, 'home_bottom'); ?>
        </div>

        <!-- Sidebar -->
        <div class="col-lg-3">
            <!-- Sidebar Advertisement -->
            <div class="sidebar-section">
                <?php echo displayAd($conn, 'sidebar'); ?>
            </div>

            <!-- Quick Links -->
            <div class="sidebar-section">
                <h5><i class="fas fa-link text-primary me-2"></i>Quick Links</h5>
                <?php echo renderQuickLinks($conn); ?>
            </div>

            <!-- Social Media Connect -->
            <div class="sidebar-section">
                <h5><i class="fas fa-share-alt text-primary me-2"></i>Connect With Us</h5>
                <?php echo renderSocialConnectSection($conn); ?>
          </div>

            <!-- Small Banner Ad -->
            <div class="sidebar-section">
                <?php echo displayAd($conn, 'small_banner'); ?>
        </div>

            <!-- Business Hours -->
            <div class="sidebar-section">
                <h5><i class="fas fa-clock text-primary me-2"></i>Business Hours</h5>
                <?php echo renderBusinessHours($conn); ?>
    </div>

            <!-- Mini Square Ad -->
            <div class="sidebar-section">
                <?php echo displayAd($conn, 'mini_square'); ?>
    </div>

            <!-- Contact Information -->
            <div class="sidebar-section">
                <h5><i class="fas fa-info-circle text-primary me-2"></i>Contact Info</h5>
                <div class="contact-info">
                    <div class="contact-item mb-3">
                        <i class="fas fa-map-marker-alt text-primary me-2"></i>
                        <span><?php echo getWebsiteSetting($conn, 'company_address', 'Addis Ababa, Ethiopia'); ?></span>
        </div>
                    <div class="contact-item mb-3">
                        <i class="fas fa-phone text-primary me-2"></i>
                        <a href="tel:<?php echo getWebsiteSetting($conn, 'contact_phone', '+251911123456'); ?>" class="text-decoration-none">
                            <?php echo getWebsiteSetting($conn, 'contact_phone', '+251 911 123 456'); ?>
                        </a>
      </div>
                    <div class="contact-item mb-3">
                        <i class="fas fa-envelope text-primary me-2"></i>
                        <a href="mailto:<?php echo getWebsiteSetting($conn, 'contact_email', 'info@natiautomotive.com'); ?>" class="text-decoration-none">
                            <?php echo getWebsiteSetting($conn, 'contact_email', 'info@natiautomotive.com'); ?>
                        </a>
        </div>
      </div>
    </div>

            <!-- Newsletter Signup -->
            <div class="sidebar-section">
                <h5><i class="fas fa-envelope-open text-primary me-2"></i>Newsletter</h5>
                <p class="text-muted small mb-3">Stay updated with our latest automotive tips and offers!</p>
                <form class="newsletter-form" action="#" method="POST">
                    <div class="input-group mb-2">
                        <input type="email" class="form-control" placeholder="Your email address" required>
                        <button class="btn btn-primary" type="submit">
                            <i class="fas fa-paper-plane"></i>
                        </button>
          </div>
                    <small class="text-muted">We respect your privacy. No spam!</small>
                </form>
          </div>
        </div>
    </div>
    </div>

<!-- Include ad tracking script -->
<?php echo getAdTrackingScript(); ?>

<!-- Enhanced JavaScript for better UX -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Smooth scrolling for anchor links
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                target.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        });
    });

    // Lazy loading for images
    if ('IntersectionObserver' in window) {
        const imageObserver = new IntersectionObserver((entries, observer) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const img = entry.target;
                    if (img.dataset.src) {
                        img.src = img.dataset.src;
                        img.classList.remove('lazy');
                        imageObserver.unobserve(img);
                    }
                }
            });
        });

        document.querySelectorAll('img[data-src]').forEach(img => {
            imageObserver.observe(img);
        });
    }

    // Enhanced card hover effects
    document.querySelectorAll('.service-card, .product-card, .blog-card, .client-card').forEach(card => {
        card.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-8px)';
        });
        
        card.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0)';
        });
    });

    // Newsletter form enhancement
    const newsletterForm = document.querySelector('.newsletter-form');
    if (newsletterForm) {
        newsletterForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const email = this.querySelector('input[type="email"]').value;
            
            // Show loading state
            const submitBtn = this.querySelector('button[type="submit"]');
            const originalContent = submitBtn.innerHTML;
            submitBtn.innerHTML = '<div class="loading-spinner"></div>';
            submitBtn.disabled = true;
            
            // Simulate API call (replace with actual implementation)
            setTimeout(() => {
                submitBtn.innerHTML = '<i class="fas fa-check"></i>';
                submitBtn.classList.remove('btn-primary');
                submitBtn.classList.add('btn-success');
                
                // Show success message
                const successMsg = document.createElement('div');
                successMsg.className = 'alert alert-success alert-dismissible fade show mt-2';
                successMsg.innerHTML = `
                    <small>Thank you! You've been subscribed to our newsletter.</small>
                    <button type="button" class="btn-close btn-close-sm" data-bs-dismiss="alert"></button>
                `;
                this.appendChild(successMsg);
                
                // Reset form after 3 seconds
                setTimeout(() => {
                    submitBtn.innerHTML = originalContent;
                    submitBtn.classList.remove('btn-success');
                    submitBtn.classList.add('btn-primary');
                    submitBtn.disabled = false;
                    this.reset();
                    if (successMsg.parentNode) {
                        successMsg.remove();
                    }
                }, 3000);
            }, 1500);
        });
    }

    // Add loading states to buttons
    document.querySelectorAll('.btn').forEach(btn => {
        if (btn.type !== 'submit' && btn.href && !btn.href.includes('#')) {
            btn.addEventListener('click', function(e) {
                if (!this.classList.contains('loading')) {
                    this.classList.add('loading');
                    const originalContent = this.innerHTML;
                    this.innerHTML = '<div class="loading-spinner me-2"></div>' + this.textContent;
                    
                    // Remove loading state after navigation
                    setTimeout(() => {
                        this.innerHTML = originalContent;
                        this.classList.remove('loading');
                    }, 1000);
                }
            });
        }
    });

    // Animate elements on scroll
    const animateOnScroll = () => {
        const elements = document.querySelectorAll('.service-card, .product-card, .blog-card, .analytics-card');
        elements.forEach(element => {
            const elementTop = element.getBoundingClientRect().top;
            const elementVisible = 150;
            
            if (elementTop < window.innerHeight - elementVisible) {
                element.style.opacity = '1';
                element.style.transform = 'translateY(0)';
            }
        });
    };

    // Initialize animation
    document.querySelectorAll('.service-card, .product-card, .blog-card, .analytics-card').forEach(element => {
        element.style.opacity = '0';
        element.style.transform = 'translateY(30px)';
        element.style.transition = 'all 0.6s ease';
    });

    window.addEventListener('scroll', animateOnScroll);
    animateOnScroll(); // Run once on load
});
</script>

<!-- Additional CSS for contact info and newsletter -->
<style>
.contact-info .contact-item {
    display: flex;
    align-items: center;
    padding: 0.5rem 0;
    border-bottom: 1px solid #e9ecef;
}

.contact-info .contact-item:last-child {
    border-bottom: none;
}

.contact-info .contact-item a {
    color: #6c757d;
    transition: color 0.3s ease;
}

.contact-info .contact-item a:hover {
    color: #007bff;
}

.newsletter-form .input-group {
    border-radius: 25px;
    overflow: hidden;
}

.newsletter-form .form-control {
    border: 2px solid #e9ecef;
    border-right: none;
    padding: 0.75rem 1rem;
}

.newsletter-form .form-control:focus {
    border-color: #007bff;
    box-shadow: none;
}

.newsletter-form .btn {
    border: 2px solid #007bff;
    border-left: none;
    padding: 0.75rem 1rem;
}

.sidebar-section:empty {
    display: none;
}

/* Enhanced responsive design */
@media (max-width: 991px) {
    .sidebar-section {
        margin-bottom: 2rem;
    }
    
    .social-card {
        margin-bottom: 1rem;
    }
}

@media (max-width: 576px) {
    .contact-info .contact-item {
        flex-direction: column;
        align-items: flex-start;
    }
    
    .contact-info .contact-item i {
        margin-bottom: 0.25rem;
    }
}

/* Enhanced Quick Links */
.quick-links-list .quick-link-item a {
    color: #6c757d;
    transition: all 0.3s ease;
    border: 1px solid transparent;
}

.quick-links-list .quick-link-item a:hover {
    background: linear-gradient(135deg, #007bff10, #0056b320);
    color: #007bff;
    border-color: rgba(0,123,255,0.1);
    transform: translateX(5px);
}

.quick-links-list .quick-link-item a:hover i.fa-chevron-right {
    transform: translateX(3px);
    color: #007bff;
}

/* Enhanced Business Hours */
.business-hours-container .business-hours-item {
    transition: all 0.3s ease;
}

.business-hours-container .business-hours-item:hover {
    background: rgba(0,123,255,0.05);
    padding-left: 0.5rem !important;
    padding-right: 0.5rem !important;
}

.business-hours-container .business-hours-item:last-child {
    border-bottom: none !important;
}

.current-status {
    border: 1px solid #e9ecef;
    transition: all 0.3s ease;
}

.current-status:hover {
    background: #e3f2fd !important;
    border-color: #007bff;
}

/* Enhanced Testimonials */
.testimonial-card {
    background: rgba(255,255,255,0.95);
    border-radius: 20px;
    box-shadow: 0 10px 30px rgba(0,0,0,0.1);
    border: 1px solid rgba(0,0,0,0.05);
    backdrop-filter: blur(10px);
}

.testimonial-stars i {
    font-size: 1.2rem;
    transition: all 0.3s ease;
}

.testimonial-section .carousel-indicators {
    bottom: -50px;
}

.testimonial-section .carousel-indicators button {
    width: 12px;
    height: 12px;
    border-radius: 50%;
    border: 2px solid #007bff;
    background: transparent;
}

.testimonial-section .carousel-indicators button.active {
    background: #007bff;
}

.testimonial-section .carousel-control-prev,
.testimonial-section .carousel-control-next {
    width: 50px;
    height: 50px;
    top: 50%;
    transform: translateY(-50%);
    background: rgba(0,123,255,0.1);
    border-radius: 50%;
    border: 2px solid rgba(0,123,255,0.2);
    transition: all 0.3s ease;
}

.testimonial-section .carousel-control-prev:hover,
.testimonial-section .carousel-control-next:hover {
    background: rgba(0,123,255,0.2);
    border-color: #007bff;
}

.testimonial-section .carousel-control-prev {
    left: -60px;
}

.testimonial-section .carousel-control-next {
    right: -60px;
}

/* Enhanced FAQ Section */
.faq-section .accordion-item {
    transition: all 0.3s ease;
}

.faq-section .accordion-item:hover {
    box-shadow: 0 5px 15px rgba(0,0,0,0.1);
    transform: translateY(-2px);
}

.faq-section .accordion-button {
    font-size: 1.1rem;
    padding: 1.25rem 1.5rem;
}

.faq-section .accordion-button:focus {
    box-shadow: 0 0 0 0.25rem rgba(0,123,255,0.25);
}

.faq-section .accordion-body {
    padding: 1.5rem;
    background: #f8f9fa;
}
</style>

<?php require_once 'includes/footer.php'; ?>