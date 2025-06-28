<?php
define('INCLUDED', true);
$page_title = 'Our Services';
require_once 'includes/header.php';
require_once 'includes/website_content_functions.php';
?>

<!-- Custom CSS for Enhanced Services Page -->
<style>
/* Enhanced Services Page Styles */
.services-hero-section {
    background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
    position: relative;
    overflow: hidden;
}

.services-hero-section::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: url('assets/images/homescreen/auto1.jpg') center/cover;
    opacity: 0.1;
    z-index: 1;
}

.services-hero-section .container {
    position: relative;
    z-index: 2;
}

.services-service-card {
    transition: all 0.3s ease;
    border-radius: 15px !important;
    overflow: hidden;
    background: #fff;
    border: 1px solid rgba(0,0,0,0.05) !important;
    height: 100%;
}

.services-service-card:hover {
    transform: translateY(-8px);
    box-shadow: 0 15px 35px rgba(0,0,0,0.15) !important;
}

.services-service-icon-wrapper {
    background: linear-gradient(135deg, #007bff10, #0056b320);
    border-radius: 50%;
    width: 80px;
    height: 80px;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 1rem auto;
    transition: all 0.3s ease;
}

.services-service-card:hover .services-service-icon-wrapper {
    background: linear-gradient(135deg, #007bff20, #0056b340);
    transform: scale(1.1);
}

.services-feature-card {
    transition: all 0.3s ease;
    border-radius: 15px;
    padding: 2rem 1rem;
    background: #fff;
    border: 1px solid rgba(0,0,0,0.05);
    height: 100%;
}

.services-feature-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 25px rgba(0,0,0,0.1);
}

.services-process-step {
    transition: all 0.3s ease;
    position: relative;
}

.services-process-step:hover {
    transform: translateY(-3px);
}

.services-process-number {
    background: linear-gradient(135deg, #007bff, #0056b3);
    width: 60px;
    height: 60px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-weight: bold;
    font-size: 1.2rem;
    margin: 0 auto 1rem auto;
    box-shadow: 0 5px 15px rgba(0,123,255,0.3);
}

.services-emergency-section {
    background: linear-gradient(135deg, #dc3545, #c82333);
    position: relative;
    overflow: hidden;
}

.services-emergency-section::before {
    content: '';
    position: absolute;
    top: 0;
    right: -50px;
    width: 200px;
    height: 200px;
    background: rgba(255,255,255,0.1);
    border-radius: 50%;
    z-index: 1;
}

.services-cta-section {
    background: linear-gradient(135deg, #007bff, #0056b3);
    position: relative;
}

.services-btn-enhanced {
    border-radius: 25px !important;
    padding: 0.75rem 2rem;
    font-weight: 500;
    transition: all 0.3s ease;
    border: 2px solid transparent;
}

.services-btn-enhanced:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 20px rgba(0,0,0,0.2);
}

.services-section-title {
    font-weight: 700;
    color: #2c3e50;
    position: relative;
    margin-bottom: 2rem;
}

.services-section-title::after {
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

/* Mobile Optimizations */
@media (max-width: 768px) {
    .services-hero-section {
        padding: 3rem 0 !important;
    }
    
    .services-hero-section h1 {
        font-size: 2rem !important;
    }
    
    .services-service-card {
        margin-bottom: 1.5rem;
    }
    
    .services-feature-card {
        margin-bottom: 1.5rem;
        padding: 1.5rem 1rem;
    }
    
    .services-process-step {
        margin-bottom: 2rem;
    }
    
    .services-emergency-section {
        text-align: center;
        padding: 2rem 0 !important;
    }
    
    .services-emergency-section .btn {
        margin-top: 1rem;
        width: 100%;
    }
}

@media (max-width: 576px) {
    .services-service-card .card-body {
        padding: 1.5rem !important;
    }
    
    .services-service-icon-wrapper {
        width: 60px;
        height: 60px;
    }
    
    .services-process-number {
        width: 50px;
        height: 50px;
        font-size: 1rem;
    }
}

/* Loading Animation */
.services-loading-spinner {
    display: inline-block;
    width: 20px;
    height: 20px;
    border: 3px solid rgba(255,255,255,.3);
    border-radius: 50%;
    border-top-color: #fff;
    animation: servicesSpin 1s ease-in-out infinite;
}

@keyframes servicesSpin {
    to { transform: rotate(360deg); }
}

/* Smooth Scroll */
html {
    scroll-behavior: smooth;
}
</style>

<!-- Hero Section -->
<section class="services-hero-section text-white py-5">
    <div class="container">
        <div class="row align-items-center min-vh-50">
            <div class="col-lg-8">
                <h1 class="display-4 fw-bold mb-3">Professional Automotive Services</h1>
                <p class="lead mb-4">Expert care for your vehicle with state-of-the-art equipment and certified technicians. We provide comprehensive automotive solutions to keep your car running smoothly.</p>
                <a href="#services" class="btn btn-light services-btn-enhanced btn-lg">
                    <i class="fas fa-tools me-2"></i>View Our Services
                </a>
            </div>
            <div class="col-lg-4 text-center">
                <i class="fas fa-tools display-1 text-light opacity-75"></i>
            </div>
        </div>
    </div>
</section>

<!-- Services Overview -->
<section id="services" class="py-5">
    <div class="container">
        <div class="row mb-5">
            <div class="col-lg-8 mx-auto text-center">
                <h2 class="services-section-title display-5 fw-bold mb-3">What We Offer</h2>
                <p class="lead text-muted">From routine maintenance to complex repairs, we provide comprehensive automotive services with quality parts and professional expertise.</p>
            </div>
        </div>

        <div class="row g-4">
            <?php
            // Fetch services from database with enhanced error handling
            try {
                $stmt = $conn->prepare("SELECT service_id, service_name, icon_class, description, price, duration FROM tbl_services WHERE status = 'active' ORDER BY service_name");
                $stmt->execute();
                $services = $stmt->fetchAll(PDO::FETCH_ASSOC);
                
                // Enhanced features for each service
                $service_features = [
                    'Professional service',
                    'Quality parts used', 
                    'Warranty included',
                    'Expert technicians'
                ];
                
                if (!empty($services)) {
                    foreach ($services as $service): 
                        $icon = $service['icon_class'] ?: 'fa-wrench';
                        $price = $service['price'] > 0 ? 'Starting at Br ' . number_format($service['price'], 0) : 'Contact for Quote';
                        $duration = $service['duration'] ? ' • ' . $service['duration'] . ' mins' : '';
                        ?>
            
            <div class="col-lg-4 col-md-6 mb-4">
                <div class="card services-service-card h-100 shadow-sm border-0">
                    <div class="card-body p-4 text-center">
                        <div class="services-service-icon-wrapper mb-3">
                            <i class="fas <?php echo htmlspecialchars($icon); ?> fa-2x text-primary"></i>
                        </div>
                        <h4 class="card-title mb-3"><?php echo htmlspecialchars($service['service_name']); ?></h4>
                        <p class="card-text text-muted mb-3"><?php echo htmlspecialchars($service['description']); ?></p>
                        
                        <ul class="list-unstyled mb-4 text-start">
                            <?php foreach ($service_features as $feature): ?>
                            <li class="mb-2">
                                <i class="fas fa-check text-success me-2"></i>
                                <small><?php echo $feature; ?></small>
                            </li>
                            <?php endforeach; ?>
                        </ul>
                        
                        <div class="mt-auto">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <span class="fw-bold text-primary"><?php echo $price; ?></span>
                                <?php if ($duration): ?>
                                <small class="text-muted"><?php echo $duration; ?></small>
                                <?php endif; ?>
                            </div>
                            <a href="contact.php?service=<?php echo urlencode($service['service_name']); ?>" class="btn btn-primary services-btn-enhanced w-100">
                                <i class="fas fa-calendar-alt me-2"></i>Book Now
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            <?php 
                    endforeach;
                } else {
                    echo '<div class="col-12 text-center">';
                    echo '<div class="alert alert-info">';
                    echo '<i class="fas fa-info-circle me-2"></i>';
                    echo 'No services are currently available. Please check back later or contact us directly.';
                    echo '</div>';
                    echo '</div>';
                }
            } catch (PDOException $e) {
                echo '<div class="col-12">';
                echo '<div class="alert alert-danger">';
                echo '<i class="fas fa-exclamation-triangle me-2"></i>';
                echo 'Error loading services. Please try again later or contact us directly.';
                echo '</div>';
                echo '</div>';
                error_log("Services page error: " . $e->getMessage());
            }
            ?>
        </div>
    </div>
</section>

<!-- Why Choose Us -->
<section class="py-5 bg-light">
    <div class="container">
        <div class="row mb-5">
            <div class="col-lg-8 mx-auto text-center">
                <h2 class="services-section-title display-5 fw-bold mb-3">Why Choose Nati Automotive?</h2>
                <p class="lead text-muted">We're committed to providing exceptional service and building lasting relationships with our customers.</p>
            </div>
        </div>

        <div class="row g-4">
            <div class="col-lg-3 col-md-6 mb-4">
                <div class="services-feature-card text-center">
                    <i class="fas fa-award fa-3x text-primary mb-3"></i>
                    <h4>Certified Technicians</h4>
                    <p class="text-muted">Our team consists of certified technicians with years of experience in automotive service.</p>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 mb-4">
                <div class="services-feature-card text-center">
                    <i class="fas fa-tools fa-3x text-primary mb-3"></i>
                    <h4>Modern Equipment</h4>
                    <p class="text-muted">State-of-the-art diagnostic tools and equipment for accurate and efficient service.</p>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 mb-4">
                <div class="services-feature-card text-center">
                    <i class="fas fa-shield-alt fa-3x text-primary mb-3"></i>
                    <h4>Quality Guarantee</h4>
                    <p class="text-muted">We stand behind our work with comprehensive warranties on both parts and labor.</p>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 mb-4">
                <div class="services-feature-card text-center">
                    <i class="fas fa-clock fa-3x text-primary mb-3"></i>
                    <h4>Fast Service</h4>
                    <p class="text-muted">Quick turnaround times without ever compromising on quality or safety standards.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Emergency Services -->
<section class="services-emergency-section py-5 text-white">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-8">
                <h2 class="display-6 fw-bold mb-3">
                    <i class="fas fa-exclamation-triangle me-3"></i>24/7 Emergency Services
                </h2>
                <p class="lead mb-0">Breakdown on the road? Don't worry! Our emergency service team is available 24/7 to get you back on the road safely and quickly.</p>
            </div>
            <div class="col-lg-4 text-center">
                <a href="tel:<?php echo getWebsiteSetting($conn, 'emergency_phone', '+251911123456'); ?>" class="btn btn-light services-btn-enhanced btn-lg">
                    <i class="fas fa-phone me-2"></i>
                    Call Now: <?php echo getWebsiteSetting($conn, 'emergency_phone', '+251 911-123456'); ?>
                </a>
            </div>
        </div>
    </div>
</section>

<!-- Service Process -->
<section class="py-5">
    <div class="container">
        <div class="row mb-5">
            <div class="col-lg-8 mx-auto text-center">
                <h2 class="services-section-title display-5 fw-bold mb-3">Our Service Process</h2>
                <p class="lead text-muted">Simple, transparent, and professional service from start to finish.</p>
            </div>
        </div>

        <div class="row g-4">
            <div class="col-lg-3 col-md-6 mb-4">
                <div class="services-process-step text-center">
                    <div class="services-process-number">1</div>
                    <h4>Schedule</h4>
                    <p class="text-muted">Book your appointment online or call us. We offer flexible scheduling to fit your busy lifestyle.</p>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 mb-4">
                <div class="services-process-step text-center">
                    <div class="services-process-number">2</div>
                    <h4>Diagnose</h4>
                    <p class="text-muted">Our certified technicians perform a thorough inspection and provide you with a detailed, transparent estimate.</p>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 mb-4">
                <div class="services-process-step text-center">
                    <div class="services-process-number">3</div>
                    <h4>Repair</h4>
                    <p class="text-muted">We use only quality parts and proven techniques to get your vehicle running like new again.</p>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 mb-4">
                <div class="services-process-step text-center">
                    <div class="services-process-number">4</div>
                    <h4>Follow-up</h4>
                    <p class="text-muted">We follow up to ensure you're completely satisfied and provide helpful maintenance reminders.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Call to Action -->
<section class="services-cta-section py-5 text-white">
    <div class="container text-center">
        <div class="row">
            <div class="col-lg-8 mx-auto">
                <h2 class="display-6 fw-bold mb-3">Ready to Schedule Your Service?</h2>
                <p class="lead mb-4">Experience the difference of professional automotive care. Book your appointment today and join thousands of satisfied customers!</p>
                <div class="d-flex flex-column flex-sm-row gap-3 justify-content-center">
                    <a href="contact.php" class="btn btn-light services-btn-enhanced btn-lg">
                        <i class="fas fa-calendar-alt me-2"></i>
                        Schedule Appointment
                    </a>
                    <a href="tel:<?php echo getWebsiteSetting($conn, 'contact_phone', '+251911123456'); ?>" class="btn btn-outline-light services-btn-enhanced btn-lg">
                        <i class="fas fa-phone me-2"></i>
                        Call Us Now
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Enhanced JavaScript -->
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

    // Enhanced button interactions
    document.querySelectorAll('.services-btn-enhanced').forEach(btn => {
        btn.addEventListener('click', function(e) {
            // Add loading state for external links
            if (this.href && !this.href.includes('#') && !this.href.includes('tel:')) {
                const originalContent = this.innerHTML;
                this.innerHTML = '<div class="services-loading-spinner me-2"></div>' + this.textContent;
                
                // Remove loading state after a short delay
                setTimeout(() => {
                    this.innerHTML = originalContent;
                }, 1000);
            }
        });
    });

    // Animate elements on scroll
    const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    };

    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.style.opacity = '1';
                entry.target.style.transform = 'translateY(0)';
            }
        });
    }, observerOptions);

    // Observe service cards and feature cards
    document.querySelectorAll('.services-service-card, .services-feature-card, .services-process-step').forEach(card => {
        card.style.opacity = '0';
        card.style.transform = 'translateY(30px)';
        card.style.transition = 'all 0.6s ease';
        observer.observe(card);
    });

    // Add stagger effect to service cards
    document.querySelectorAll('.services-service-card').forEach((card, index) => {
        card.style.transitionDelay = `${index * 0.1}s`;
    });
});
</script>

<?php require_once 'includes/footer.php'; ?> 