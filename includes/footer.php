<?php
if (!defined('INCLUDED')) {
    exit;
}

// Include website content functions for dynamic footer content
require_once __DIR__ . '/website_content_functions.php';

// Get footer settings and social links
$company_info = [
    'name' => getWebsiteSetting($conn, 'site_title', 'Nati Automotive'),
    'tagline' => getWebsiteSetting($conn, 'site_tagline', 'Your trusted automotive partner'),
    'address' => getWebsiteSetting($conn, 'company_address', 'Addis Ababa, Ethiopia'),
    'phone' => getWebsiteSetting($conn, 'contact_phone', '+251 911-123456'),
    'email' => getWebsiteSetting($conn, 'contact_email', 'info@natiautomotive.com'),
    'description' => getWebsiteSetting($conn, 'company_description', 'Your trusted partner for quality auto parts and professional automotive services.')
];

$social_links = getSocialLinks($conn);
$quick_links = getQuickLinks($conn);
?>

<!-- Fallback Font Awesome CDN for Footer Icons -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA==" crossorigin="anonymous" referrerpolicy="no-referrer" />

<!-- Enhanced Footer -->
<footer class="footer mt-5 bg-dark text-white position-relative overflow-hidden">
    <!-- Background Pattern -->
    <div class="footer-pattern position-absolute w-100 h-100" style="opacity: 0.05; background-image: url('data:image/svg+xml,<svg xmlns=\"http://www.w3.org/2000/svg\" viewBox=\"0 0 100 100\"><path d=\"M10 10h80v80H10z\" fill=\"none\" stroke=\"%23ffffff\" stroke-width=\"0.5\"/><circle cx=\"50\" cy=\"50\" r=\"30\" fill=\"none\" stroke=\"%23ffffff\" stroke-width=\"0.5\"/></svg>'); background-size: 50px 50px;"></div>
    
    <!-- Main Footer Content -->
    <div class="container position-relative py-5">
        <div class="row g-4">
            <!-- Company Information -->
            <div class="col-lg-4 col-md-6 mb-4">
                <div class="footer-section">
                    <!-- Logo and Brand -->
                    <div class="footer-brand mb-4">
                        <div class="d-flex align-items-center mb-3">
                            <div class="footer-logo me-3">
                                <i class="fas fa-car text-primary" style="font-size: 2rem;"></i>
                            </div>
                            <div>
                                <h5 class="mb-1 text-white fw-bold"><?php echo htmlspecialchars($company_info['name']); ?></h5>
                                <small class="text-muted"><?php echo htmlspecialchars($company_info['tagline']); ?></small>
                            </div>
                        </div>
                        <p class="text-muted mb-4"><?php echo htmlspecialchars($company_info['description']); ?></p>
                    </div>
                    
                    <!-- Social Media Links -->
                    <?php if (!empty($social_links)): ?>
                    <div class="social-links">
                        <h6 class="text-white mb-3 fw-semibold">
                            <i class="fas fa-share-alt text-primary me-2"></i>Follow Us
                        </h6>
                        <div class="d-flex flex-wrap gap-3">
                            <?php foreach ($social_links as $social): ?>
                            <a href="<?php echo htmlspecialchars($social['platform_url']); ?>" 
                               class="social-link d-flex align-items-center justify-content-center"
                               style="background: <?php echo htmlspecialchars($social['platform_color']); ?>;"
                               target="_blank" rel="noopener noreferrer"
                               aria-label="<?php echo htmlspecialchars($social['platform_name']); ?>"
                               data-bs-toggle="tooltip" 
                               data-bs-placement="top" 
                               title="<?php echo htmlspecialchars($social['description']); ?>">
                                <i class="<?php echo htmlspecialchars($social['platform_icon']); ?> fa-lg"></i>
                            </a>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <?php else: ?>
                    <!-- Default Social Links if none in database -->
                    <div class="social-links">
                        <h6 class="text-white mb-3 fw-semibold">
                            <i class="fas fa-share-alt text-primary me-2"></i>Follow Us
                        </h6>
                        <div class="d-flex flex-wrap gap-3">
                            <a href="#" class="social-link d-flex align-items-center justify-content-center" style="background: #1877f2;" target="_blank" rel="noopener noreferrer" aria-label="Facebook">
                                <i class="fab fa-facebook-f fa-lg"></i>
                            </a>
                            <a href="#" class="social-link d-flex align-items-center justify-content-center" style="background: #1da1f2;" target="_blank" rel="noopener noreferrer" aria-label="Twitter">
                                <i class="fab fa-twitter fa-lg"></i>
                            </a>
                            <a href="#" class="social-link d-flex align-items-center justify-content-center" style="background: #25d366;" target="_blank" rel="noopener noreferrer" aria-label="WhatsApp">
                                <i class="fab fa-whatsapp fa-lg"></i>
                            </a>
                            <a href="#" class="social-link d-flex align-items-center justify-content-center" style="background: #0088cc;" target="_blank" rel="noopener noreferrer" aria-label="Telegram">
                                <i class="fab fa-telegram fa-lg"></i>
                            </a>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
            
            <!-- Quick Links -->
            <div class="col-lg-2 col-md-6 mb-4">
                <div class="footer-section">
                    <h6 class="text-white mb-4 fw-semibold">
                        <i class="fas fa-link text-primary me-2"></i>Quick Links
                    </h6>
                    <ul class="list-unstyled footer-links">
                        <?php if (!empty($quick_links)): ?>
                            <?php foreach ($quick_links as $link): ?>
                            <li class="mb-2">
                                <a href="<?php echo htmlspecialchars($link['link_url']); ?>" 
                                   class="footer-link d-flex align-items-center">
                                    <?php if ($link['link_icon']): ?>
                                    <i class="<?php echo htmlspecialchars($link['link_icon']); ?> me-2 text-primary"></i>
                                    <?php endif; ?>
                                    <?php echo htmlspecialchars($link['link_title']); ?>
                                    <i class="fas fa-chevron-right ms-auto small opacity-50"></i>
                                </a>
                            </li>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <li class="mb-2">
                                <a href="index.php" class="footer-link d-flex align-items-center">
                                    <i class="fas fa-home me-2 text-primary"></i>Home
                                    <i class="fas fa-chevron-right ms-auto small opacity-50"></i>
                                </a>
                            </li>
                            <li class="mb-2">
                                <a href="services.php" class="footer-link d-flex align-items-center">
                                    <i class="fas fa-wrench me-2 text-primary"></i>Services
                                    <i class="fas fa-chevron-right ms-auto small opacity-50"></i>
                                </a>
                            </li>
                            <li class="mb-2">
                                <a href="about.php" class="footer-link d-flex align-items-center">
                                    <i class="fas fa-info-circle me-2 text-primary"></i>About Us
                                    <i class="fas fa-chevron-right ms-auto small opacity-50"></i>
                                </a>
                            </li>
                            <li class="mb-2">
                                <a href="contact.php" class="footer-link d-flex align-items-center">
                                    <i class="fas fa-envelope me-2 text-primary"></i>Contact
                                    <i class="fas fa-chevron-right ms-auto small opacity-50"></i>
                                </a>
                            </li>
                        <?php endif; ?>
                    </ul>
        </div>
      </div>
            
            <!-- Services -->
            <div class="col-lg-3 col-md-6 mb-4">
                <div class="footer-section">
                    <h6 class="text-white mb-4 fw-semibold">
                        <i class="fas fa-tools text-primary me-2"></i>Our Services
                    </h6>
                    <ul class="list-unstyled footer-links">
                        <?php
                        // Fetch featured services for footer
                        try {
                            $stmt = $conn->prepare("SELECT service_name, service_id FROM tbl_services WHERE status = 'active' ORDER BY service_id ASC LIMIT 6");
                            $stmt->execute();
                            $footer_services = $stmt->fetchAll(PDO::FETCH_ASSOC);
                            
                            if (!empty($footer_services)) {
                                foreach ($footer_services as $service) {
                                    echo '<li class="mb-2">';
                                    echo '<a href="services.php#service-' . $service['service_id'] . '" class="footer-link d-flex align-items-center">';
                                    echo '<i class="fas fa-cog me-2 text-primary"></i>';
                                    echo htmlspecialchars($service['service_name']);
                                    echo '<i class="fas fa-chevron-right ms-auto small opacity-50"></i>';
                                    echo '</a>';
                                    echo '</li>';
                                }
                            } else {
                                // Default services if none in database
                                $default_services = [
                                    'Auto Diagnostics',
                                    'Oil Change',
                                    'Brake Service',
                                    'Engine Repair',
                                    'Parts & Accessories',
                                    'Maintenance'
                                ];
                                foreach ($default_services as $service) {
                                    echo '<li class="mb-2">';
                                    echo '<a href="services.php" class="footer-link d-flex align-items-center">';
                                    echo '<i class="fas fa-cog me-2 text-primary"></i>';
                                    echo htmlspecialchars($service);
                                    echo '<i class="fas fa-chevron-right ms-auto small opacity-50"></i>';
                                    echo '</a>';
                                    echo '</li>';
                                }
                            }
                        } catch (PDOException $e) {
                            // Default services on error
                            $default_services = ['Auto Diagnostics', 'Oil Change', 'Brake Service', 'Engine Repair'];
                            foreach ($default_services as $service) {
                                echo '<li class="mb-2">';
                                echo '<a href="services.php" class="footer-link d-flex align-items-center">';
                                echo '<i class="fas fa-cog me-2 text-primary"></i>';
                                echo htmlspecialchars($service);
                                echo '<i class="fas fa-chevron-right ms-auto small opacity-50"></i>';
                                echo '</a>';
                                echo '</li>';
                            }
                        }
                        ?>
        </ul>
      </div>
            </div>
            
            <!-- Contact Information -->
            <div class="col-lg-3 col-md-6 mb-4">
                <div class="footer-section">
                    <h6 class="text-white mb-4 fw-semibold">
                        <i class="fas fa-map-marker-alt text-primary me-2"></i>Contact Info
                    </h6>
                    <ul class="list-unstyled contact-info">
                        <li class="mb-3 d-flex align-items-start">
                            <div class="contact-icon me-3">
                                <i class="fas fa-map-marker-alt text-primary"></i>
                            </div>
                            <div class="contact-details">
                                <small class="text-muted d-block">Address</small>
                                <span class="text-white"><?php echo htmlspecialchars($company_info['address']); ?></span>
                            </div>
                        </li>
                        <li class="mb-3 d-flex align-items-start">
                            <div class="contact-icon me-3">
                                <i class="fas fa-phone text-primary"></i>
                            </div>
                            <div class="contact-details">
                                <small class="text-muted d-block">Phone</small>
                                <a href="tel:<?php echo htmlspecialchars(str_replace([' ', '-'], '', $company_info['phone'])); ?>" 
                                   class="text-white text-decoration-none hover-primary">
                                    <?php echo htmlspecialchars($company_info['phone']); ?>
                                </a>
                            </div>
                        </li>
                        <li class="mb-3 d-flex align-items-start">
                            <div class="contact-icon me-3">
                                <i class="fas fa-envelope text-primary"></i>
                            </div>
                            <div class="contact-details">
                                <small class="text-muted d-block">Email</small>
                                <a href="mailto:<?php echo htmlspecialchars($company_info['email']); ?>" 
                                   class="text-white text-decoration-none hover-primary">
                                    <?php echo htmlspecialchars($company_info['email']); ?>
                                </a>
                            </div>
                        </li>
                        <li class="mb-3 d-flex align-items-start">
                            <div class="contact-icon me-3">
                                <i class="fas fa-clock text-primary"></i>
                            </div>
                            <div class="contact-details">
                                <small class="text-muted d-block">Business Hours</small>
                                <span class="text-white">Mon - Fri: 8:00 AM - 6:00 PM</span>
                                <br>
                                <span class="text-white">Sat: 9:00 AM - 4:00 PM</span>
                            </div>
                        </li>
        </ul>
      </div>
            </div>
        </div>
    </div>
    
    <!-- Newsletter Subscription Section -->
    <div class="newsletter-section bg-primary py-4">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6 mb-3 mb-lg-0">
                    <div class="d-flex align-items-center">
                        <div class="newsletter-icon me-3">
                            <i class="fas fa-envelope-open fa-2x text-white"></i>
                        </div>
                        <div>
                            <h5 class="text-white mb-1 fw-bold">Stay Updated!</h5>
                            <p class="text-white-50 mb-0">Subscribe to our newsletter for automotive tips and exclusive offers.</p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <form class="newsletter-form d-flex gap-2" action="#" method="POST">
                        <div class="input-group">
                            <input type="email" class="form-control newsletter-input" 
                                   placeholder="Enter your email address" required 
                                   aria-label="Email address for newsletter">
                            <button class="btn btn-light newsletter-btn" type="submit" 
                                    aria-label="Subscribe to newsletter">
                                <i class="fas fa-paper-plane me-1"></i>
                                <span class="d-none d-sm-inline">Subscribe</span>
                            </button>
                        </div>
                    </form>
                    <small class="text-white-50 d-block mt-2">
                        <i class="fas fa-shield-alt me-1"></i>We respect your privacy. No spam!
                    </small>
                </div>
        </div>
      </div>
    </div>
    
    <!-- Footer Bottom -->
    <div class="footer-bottom bg-dark border-top border-secondary py-3">
        <div class="container">
    <div class="row align-items-center">
                <div class="col-md-6 text-center text-md-start mb-2 mb-md-0">
                    <p class="mb-0 text-muted">
                        <i class="fas fa-copyright me-1"></i>
                        <?php echo date('Y'); ?> <?php echo htmlspecialchars($company_info['name']); ?>. All rights reserved.
                    </p>
                </div>
                <div class="col-md-6 text-center text-md-end">
                    <div class="footer-legal-links">
                        <a href="privacy-policy.php" class="text-muted text-decoration-none me-3 hover-primary">
                            <i class="fas fa-shield-alt me-1"></i>Privacy Policy
                        </a>
                        <a href="terms-of-service.php" class="text-muted text-decoration-none me-3 hover-primary">
                            <i class="fas fa-file-contract me-1"></i>Terms of Service
                        </a>
                        <a href="contact.php" class="text-muted text-decoration-none hover-primary">
                            <i class="fas fa-headset me-1"></i>Support
                        </a>
                    </div>
                </div>
      </div>
      </div>
    </div>
    
    <!-- Back to Top Button -->
    <button class="back-to-top position-fixed d-none" id="backToTop" 
            aria-label="Back to top">
        <i class="fas fa-chevron-up"></i>
    </button>
</footer>

<!-- Enhanced Footer Styles -->
<style>
/* Enhanced Footer Styles */
.footer {
    background: linear-gradient(135deg, #2c3e50 0%, #34495e 100%);
    position: relative;
}

/* Icon Fallbacks - Unicode symbols if Font Awesome fails */
.fa-car::before { content: "🚗"; }
.fa-share-alt::before { content: "📤"; }
.fa-facebook-f::before { content: "📘"; }
.fa-twitter::before { content: "🐦"; }
.fa-whatsapp::before { content: "💬"; }
.fa-telegram::before { content: "✈️"; }
.fa-link::before { content: "🔗"; }
.fa-home::before { content: "🏠"; }
.fa-wrench::before { content: "🔧"; }
.fa-info-circle::before { content: "ℹ️"; }
.fa-envelope::before { content: "✉️"; }
.fa-tools::before { content: "🛠️"; }
.fa-cog::before { content: "⚙️"; }
.fa-chevron-right::before { content: "▶"; }
.fa-map-marker-alt::before { content: "📍"; }
.fa-phone::before { content: "📞"; }
.fa-clock::before { content: "🕐"; }
.fa-envelope-open::before { content: "📧"; }
.fa-paper-plane::before { content: "📤"; }
.fa-shield-alt::before { content: "🛡️"; }
.fa-copyright::before { content: "©"; }
.fa-file-contract::before { content: "📄"; }
.fa-headset::before { content: "🎧"; }
.fa-chevron-up::before { content: "⬆"; }

/* Ensure icons fallback gracefully */
i[class*="fa-"]:not(.fa):before {
    font-family: "Font Awesome 6 Free", "Font Awesome 6 Brands", sans-serif;
    font-style: normal;
    font-variant: normal;
    text-rendering: auto;
    -webkit-font-smoothing: antialiased;
}

/* If Font Awesome fails completely, use system emojis */
@supports not (font-family: "Font Awesome 6 Free") {
    .fa-car::before { content: "🚗"; font-family: system-ui, -apple-system, sans-serif; }
    .fa-share-alt::before { content: "📤"; font-family: system-ui, -apple-system, sans-serif; }
    .fa-facebook-f::before { content: "📘"; font-family: system-ui, -apple-system, sans-serif; }
    .fa-twitter::before { content: "🐦"; font-family: system-ui, -apple-system, sans-serif; }
    .fa-whatsapp::before { content: "💬"; font-family: system-ui, -apple-system, sans-serif; }
    .fa-telegram::before { content: "✈️"; font-family: system-ui, -apple-system, sans-serif; }
}

.footer-section h6 {
    position: relative;
    padding-bottom: 0.5rem;
    margin-bottom: 1.5rem;
}

.footer-section h6::after {
    content: '';
    position: absolute;
    bottom: 0;
    left: 0;
    width: 30px;
    height: 2px;
    background: linear-gradient(135deg, #007bff, #0056b3);
    border-radius: 1px;
}

.footer-logo img {
    filter: brightness(1.1);
    transition: all 0.3s ease;
}

.footer-logo:hover img {
    filter: brightness(1.3) drop-shadow(0 0 10px rgba(0, 123, 255, 0.5));
    transform: scale(1.05);
}

.social-link {
    width: 45px;
    height: 45px;
    border-radius: 50%;
    color: white;
    transition: all 0.3s ease;
    text-decoration: none;
    position: relative;
    overflow: hidden;
}

.social-link::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(255, 255, 255, 0.2);
    transform: scale(0);
    transition: transform 0.3s ease;
    border-radius: 50%;
}

.social-link:hover::before {
    transform: scale(1);
}

.social-link:hover {
    transform: translateY(-3px) scale(1.1);
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.3);
    color: white;
}

.footer-link {
    color: #adb5bd;
    text-decoration: none;
    transition: all 0.3s ease;
    padding: 0.5rem 0;
    border-radius: 5px;
    position: relative;
    overflow: hidden;
}

.footer-link::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(0, 123, 255, 0.1), transparent);
    transition: left 0.5s ease;
}

.footer-link:hover::before {
    left: 100%;
}

.footer-link:hover {
    color: #007bff;
    transform: translateX(5px);
    background: rgba(0, 123, 255, 0.05);
}

.footer-link:hover i {
    transform: scale(1.1);
    color: #007bff;
}

.contact-info li {
    transition: all 0.3s ease;
    padding: 0.75rem;
    border-radius: 8px;
    border: 1px solid transparent;
}

.contact-info li:hover {
    background: rgba(0, 123, 255, 0.05);
    border-color: rgba(0, 123, 255, 0.2);
    transform: translateX(5px);
}


.contact-icon i {
    font-size: 1.1rem;
    transition: all 0.3s ease;
}

.contact-info li:hover .contact-icon i {
    transform: scale(1.2);
    color: #007bff;
}

.hover-primary:hover {
    color: #007bff !important;
    transition: color 0.3s ease;
}

/* Newsletter Section */
.newsletter-section {
    background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
    position: relative;
}

.newsletter-section::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 20"><path d="M0 10c20 0 20-10 40-10s20 10 40 10 20-10 40-10 20 10 40 10v10H0z" fill="rgba(255,255,255,0.1)"/></svg>') repeat-x;
    background-size: 200px 20px;
    animation: wave 10s linear infinite;
}

@keyframes wave {
    0% { background-position-x: 0; }
    100% { background-position-x: 200px; }
}

.newsletter-input {
    border: 2px solid rgba(255, 255, 255, 0.2);
    background: rgba(255, 255, 255, 0.1);
    color: white;
    border-radius: 25px 0 0 25px;
    padding: 0.75rem 1.25rem;
    backdrop-filter: blur(10px);
}

.newsletter-input::placeholder {
    color: rgba(255, 255, 255, 0.7);
}

.newsletter-input:focus {
    border-color: rgba(255, 255, 255, 0.5);
    background: rgba(255, 255, 255, 0.15);
    color: white;
    box-shadow: 0 0 0 0.25rem rgba(255, 255, 255, 0.1);
}

.newsletter-btn {
    border-radius: 0 25px 25px 0;
    padding: 0.75rem 1.5rem;
    font-weight: 600;
    border: 2px solid white;
    transition: all 0.3s ease;
}

.newsletter-btn:hover {
    background: white;
    color: #007bff;
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.2);
}

/* Footer Bottom */
.footer-bottom {
    background: linear-gradient(135deg, #1a252f 0%, #2c3e50 100%);
}

.footer-legal-links a {
    font-size: 0.9rem;
    transition: all 0.3s ease;
    padding: 0.25rem 0.5rem;
    border-radius: 5px;
}

.footer-legal-links a:hover {
    background: rgba(0, 123, 255, 0.1);
    transform: translateY(-1px);
}

/* Back to Top Button */
.back-to-top {
    bottom: 30px;
    right: 30px;
    width: 50px;
    height: 50px;
    background: linear-gradient(135deg, #007bff, #0056b3);
    color: white;
    border: none;
    border-radius: 50%;
    box-shadow: 0 4px 15px rgba(0, 123, 255, 0.3);
    transition: all 0.3s ease;
    z-index: 1000;
}

.back-to-top:hover {
    transform: translateY(-3px) scale(1.1);
    box-shadow: 0 8px 25px rgba(0, 123, 255, 0.4);
    color: white;
}

.back-to-top.show {
    opacity: 1;
    visibility: visible;
}

/* Mobile Optimizations */
@media (max-width: 768px) {
    .footer .container {
        padding-left: 1rem;
        padding-right: 1rem;
    }
    
    .footer-section {
        text-align: center;
        margin-bottom: 2rem;
    }
    
    .footer-section h6::after {
        left: 50%;
        transform: translateX(-50%);
    }
    
    .social-links .d-flex {
        justify-content: center;
    }
    
    .contact-info li {
        text-align: left;
    }
    
    .newsletter-form {
        flex-direction: column;
    }
    
    .newsletter-input {
        border-radius: 25px;
        margin-bottom: 0.75rem;
    }
    
    .newsletter-btn {
        border-radius: 25px;
        width: 100%;
    }
    
    .footer-legal-links {
        display: flex;
        flex-direction: column;
        gap: 0.5rem;
    }
    
    .back-to-top {
        bottom: 20px;
        right: 20px;
        width: 45px;
        height: 45px;
    }
}

@media (max-width: 576px) {
    .social-link {
        width: 40px;
        height: 40px;
    }
    
    .newsletter-section .row {
        text-align: center;
    }
    
    .newsletter-section .col-lg-6:first-child .d-flex {
        justify-content: center;
        text-align: center;
    }
}

/* Performance Optimizations */
.footer-link,
.social-link,
.contact-info li,
.newsletter-btn,
.back-to-top {
    will-change: transform;
}

/* Accessibility Improvements */
.footer-link:focus,
.social-link:focus,
.newsletter-input:focus,
.newsletter-btn:focus,
.back-to-top:focus {
    outline: 2px solid #007bff;
    outline-offset: 2px;
}

/* Print Styles */
@media print {
    .footer {
        background: white !important;
        color: black !important;
    }
    
    .footer * {
        color: black !important;
    }
    
    .newsletter-section,
    .back-to-top {
        display: none !important;
    }
}
</style>

<!-- Bootstrap JS Bundle with Popper -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>

<!-- Enhanced Footer JavaScript -->
<script>
document.addEventListener('DOMContentLoaded', function() {
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

    // Enhanced newsletter form
    const newsletterForm = document.querySelector('.newsletter-form');
    if (newsletterForm) {
        newsletterForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const emailInput = this.querySelector('.newsletter-input');
            const submitBtn = this.querySelector('.newsletter-btn');
            const email = emailInput.value.trim();
            
            if (!email) return;
            
            // Show loading state
            const originalContent = submitBtn.innerHTML;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> <span class="d-none d-sm-inline">Subscribing...</span>';
            submitBtn.disabled = true;
            
            // Simulate API call (replace with actual implementation)
            setTimeout(() => {
                submitBtn.innerHTML = '<i class="fas fa-check"></i> <span class="d-none d-sm-inline">Subscribed!</span>';
                submitBtn.classList.remove('btn-light');
                submitBtn.classList.add('btn-success');
                
                // Show success message
                const successMsg = document.createElement('div');
                successMsg.className = 'alert alert-success alert-dismissible fade show mt-3';
                successMsg.innerHTML = `
                    <i class="fas fa-check-circle me-2"></i>
                    <strong>Thank you!</strong> You've been successfully subscribed to our newsletter.
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                `;
                this.appendChild(successMsg);
                
                // Reset form after 3 seconds
                setTimeout(() => {
                    submitBtn.innerHTML = originalContent;
                    submitBtn.classList.remove('btn-success');
                    submitBtn.classList.add('btn-light');
                    submitBtn.disabled = false;
                    emailInput.value = '';
                    if (successMsg.parentNode) {
                        successMsg.remove();
                    }
                }, 3000);
            }, 1500);
        });
    }

    // Enhanced back to top functionality
    const backToTopBtn = document.getElementById('backToTop');
    if (backToTopBtn) {
        // Show/hide button based on scroll position
        function toggleBackToTop() {
            if (window.pageYOffset > 300) {
                backToTopBtn.classList.remove('d-none');
                backToTopBtn.classList.add('show');
            } else {
                backToTopBtn.classList.remove('show');
                setTimeout(() => {
                    if (!backToTopBtn.classList.contains('show')) {
                        backToTopBtn.classList.add('d-none');
                    }
                }, 300);
            }
        }
        
        window.addEventListener('scroll', toggleBackToTop);
        toggleBackToTop(); // Check initial state
        
        // Smooth scroll to top
        backToTopBtn.addEventListener('click', function() {
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        });
    }

    // Enhanced tooltips
    if (typeof bootstrap !== 'undefined' && bootstrap.Tooltip) {
        const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl, {
                delay: { show: 500, hide: 100 }
            });
        });
    }

    // Enhanced link tracking
    document.querySelectorAll('.footer-link, .social-link').forEach(link => {
        link.addEventListener('click', function(e) {
            // Add analytics tracking here if needed
            console.log('Footer link clicked:', this.href || this.getAttribute('href'));
        });
    });

    // Lazy load footer images
    if ('IntersectionObserver' in window) {
        const imageObserver = new IntersectionObserver((entries, observer) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const img = entry.target;
                    if (img.dataset.src) {
                        img.src = img.dataset.src;
                        imageObserver.unobserve(img);
                    }
                }
            });
        });

        document.querySelectorAll('.footer img[data-src]').forEach(img => {
            imageObserver.observe(img);
        });
    }

    // Initialize cart count
    updateCartCount();
    
    // Auto-refresh cart count every 30 seconds
    setInterval(updateCartCount, 30000);
    
    console.log('Enhanced footer initialized successfully');
});
</script>
</body>
</html> 