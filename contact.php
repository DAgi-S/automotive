<?php
define('INCLUDED', true);
$page_title = 'Contact Us';
require_once 'includes/header.php';

// Fetch company information (connection already available from header)
$company_info = [];
try {
    $stmt = $conn->prepare("SELECT address, phone, email, website, about FROM company_information ORDER BY id DESC LIMIT 1");
    $stmt->execute();
    $company_info = $stmt->fetch(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    // Use default values if database error
}

// Default values if no company info found
if (!$company_info) {
    $company_info = [
        'address' => '123 Automotive Street, Addis Ababa, Ethiopia, Near Bole Airport',
        'phone' => '+251 911-123456',
        'email' => 'info@natiautomotive.com',
        'website' => 'www.natiautomotive.com',
        'about' => 'Your trusted partner in automotive excellence since 2010.'
    ];
}

// Handle form submission
$success_message = '';
$error_message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $service = trim($_POST['service'] ?? '');
    $message = trim($_POST['message'] ?? '');
    
    // Basic validation
    if (empty($name) || empty($email) || empty($message)) {
        $error_message = 'Please fill in all required fields.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error_message = 'Please enter a valid email address.';
    } else {
        // Here you would typically save to database or send email
        // For now, we'll just show a success message
        $success_message = 'Thank you for your message! We will get back to you within 24 hours.';
        
        // Clear form data
        $name = $email = $phone = $service = $message = '';
    }
}
?>

<!-- Custom CSS for Enhanced Contact Page -->
<style>
/* Force Font Awesome Loading - Debug Mode */
@import url('https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css');

/* Icon Fallback System for Contact Page */
.contact-icon i {
    font-family: "Font Awesome 6 Free", "Font Awesome 6 Pro", "Font Awesome 5 Free", "Font Awesome 5 Pro", sans-serif !important;
    font-weight: 900 !important;
    font-style: normal !important;
    display: inline-block !important;
}

/* Ensure Font Awesome icons display properly */
.fas, .far, .fab {
    font-family: "Font Awesome 6 Free", "Font Awesome 6 Pro", "Font Awesome 5 Free", "Font Awesome 5 Pro" !important;
    font-weight: 900 !important;
    font-style: normal !important;
}

.fab {
    font-family: "Font Awesome 6 Brands", "Font Awesome 5 Brands" !important;
    font-weight: 400 !important;
}

/* Force icon display if Font Awesome fails */
.fa-map-marker-alt:before { content: "\f3c5"; }
.fa-phone:before { content: "\f095"; }
.fa-envelope:before { content: "\f0e0"; }
.fa-clock:before { content: "\f017"; }
.fa-route:before { content: "\f4d7"; }
.fa-paper-plane:before { content: "\f1d8"; }
.fa-check-circle:before { content: "\f058"; }
.fa-exclamation-circle:before { content: "\f06a"; }
.fa-info-circle:before { content: "\f05a"; }
.fa-whatsapp:before { content: "\f232"; }
.fa-telegram:before { content: "\f2c6"; }

/* Debug: Show icons even if Font Awesome fails completely */
.contact-icon .fa-map-marker-alt:not(:empty):before,
.contact-icon .fa-phone:not(:empty):before,
.contact-icon .fa-envelope:not(:empty):before,
.contact-icon .fa-clock:not(:empty):before {
    content: attr(data-fallback) !important;
    font-family: system-ui, -apple-system, sans-serif !important;
    font-size: 1.5rem !important;
}

/* Fallback for when all else fails */
.contact-icon .fa-map-marker-alt:empty:before { content: "📍" !important; font-family: system-ui !important; }
.contact-icon .fa-phone:empty:before { content: "📞" !important; font-family: system-ui !important; }
.contact-icon .fa-envelope:empty:before { content: "✉️" !important; font-family: system-ui !important; }
.contact-icon .fa-clock:empty:before { content: "🕐" !important; font-family: system-ui !important; }

/* Enhanced Contact Page Styles */
.contact-hero-section {
    background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
    position: relative;
    overflow: hidden;
}

.contact-hero-section::before {
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

.contact-hero-section .container {
    position: relative;
    z-index: 2;
}

.contact-card {
    transition: all 0.3s ease;
    border-radius: 15px !important;
    height: 100%;
}

.contact-card:hover {
    transform: translateY(-10px);
    box-shadow: 0 20px 40px rgba(0, 123, 255, 0.15) !important;
}

.contact-icon {
    transition: all 0.3s ease;
    width: 70px;
    height: 70px;
    background: linear-gradient(135deg, #007bff, #0056b3);
    border-radius: 50%;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    margin-bottom: 1rem;
    position: relative;
}

.contact-icon i {
    color: white !important;
    font-size: 1.5rem !important;
    z-index: 2;
    position: relative;
}

.contact-card:hover .contact-icon {
    transform: scale(1.1);
    box-shadow: 0 10px 25px rgba(0, 123, 255, 0.3);
}

.form-enhanced {
    border-radius: 15px !important;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1) !important;
}

.form-control, .form-select {
    border-radius: 10px;
    border: 2px solid #e9ecef;
    padding: 12px 15px;
    transition: all 0.3s ease;
}

.form-control:focus, .form-select:focus {
    border-color: #007bff;
    box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
    transform: translateY(-2px);
}

.contact-btn-enhanced {
    background: linear-gradient(135deg, #007bff, #0056b3);
    border: none;
    border-radius: 10px;
    padding: 15px 30px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 1px;
    transition: all 0.3s ease;
    position: relative;
    overflow: hidden;
}

.contact-btn-enhanced:hover {
    transform: translateY(-3px);
    box-shadow: 0 15px 35px rgba(0, 123, 255, 0.4);
}

.contact-btn-enhanced::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
    transition: left 0.5s;
}

.contact-btn-enhanced:hover::before {
    left: 100%;
}

.map-container {
    border-radius: 15px;
    overflow: hidden;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
    height: 400px;
}

.contact-section-title {
    font-weight: 700;
    color: #2c3e50;
    position: relative;
    margin-bottom: 2rem;
}

.contact-section-title::after {
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

.alert-enhanced {
    border-radius: 10px;
    border: none;
    padding: 15px 20px;
    margin-bottom: 25px;
}

.alert-success.alert-enhanced {
    background: linear-gradient(135deg, #28a745, #20c997);
    color: white;
}

.alert-danger.alert-enhanced {
    background: linear-gradient(135deg, #dc3545, #fd7e14);
    color: white;
}

/* Mobile Optimizations */
@media (max-width: 768px) {
    .contact-hero-section .display-4 {
        font-size: 2rem;
    }
    
    .contact-hero-section .lead {
        font-size: 1rem;
    }
    
    .contact-card {
        margin-bottom: 1rem;
    }
    
    .contact-icon {
        width: 60px;
        height: 60px;
    }
    
    .contact-icon i {
        font-size: 1.2rem !important;
    }
    
    .contact-btn-enhanced {
        padding: 12px 25px;
        font-size: 0.9rem;
    }
    
    .map-container {
        height: 300px;
        margin-top: 2rem;
    }
}

@media (max-width: 576px) {
    .contact-hero-section {
        padding: 3rem 0;
    }
    
    .contact-hero-section .display-4 {
        font-size: 1.8rem;
    }
    
    .contact-card .card-body {
        padding: 1.5rem;
    }
    
    .form-enhanced .card-body {
        padding: 2rem 1.5rem;
    }
}

/* Animation Classes */
.contact-fade-in-up {
    animation: contactFadeInUp 0.6s ease-out;
}

@keyframes contactFadeInUp {
    from {
        opacity: 0;
        transform: translateY(30px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.contact-bounce-in {
    animation: contactBounceIn 0.8s ease-out;
}

@keyframes contactBounceIn {
    0% {
        opacity: 0;
        transform: scale(0.3);
    }
    50% {
        opacity: 1;
        transform: scale(1.05);
    }
    70% {
        transform: scale(0.9);
    }
    100% {
        opacity: 1;
        transform: scale(1);
    }
}
</style>

<!-- Hero Section -->
<section class="contact-hero-section text-white py-5">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-8 contact-fade-in-up">
                <h1 class="display-4 fw-bold mb-3">Get In Touch</h1>
                <p class="lead mb-4">Ready to service your vehicle? Have questions about our services? We're here to help! Contact us today to schedule an appointment or get more information.</p>
                <a href="#contact-form" class="btn btn-light contact-btn-enhanced btn-lg">
                    <i class="fas fa-envelope me-2"></i>Send Message
                </a>
            </div>
            <div class="col-lg-4 text-center contact-bounce-in">
                <i class="fas fa-envelope display-1 text-light opacity-75"></i>
            </div>
        </div>
    </div>
</section>

<!-- Contact Information -->
<section class="py-5 bg-light">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="contact-section-title">Contact Information</h2>
            <p class="text-muted">Multiple ways to reach us for your convenience</p>
        </div>
        <div class="row g-4">
            <div class="col-lg-3 col-md-6 col-sm-6 col-6">
                <div class="card contact-card border-0 shadow-sm text-center contact-fade-in-up">
                    <div class="card-body p-4">
                        <div class="contact-icon text-white">
                            <i class="fas fa-map-marker-alt fa-lg" data-fallback="📍"></i>
                        </div>
                        <h5 class="card-title fw-bold">Visit Us</h5>
                        <p class="card-text text-muted small">
                            <?php echo nl2br(htmlspecialchars($company_info['address'])); ?>
                        </p>
                        <a href="https://maps.google.com" target="_blank" class="btn btn-outline-primary btn-sm">
                            <i class="fas fa-route me-1"></i>Get Directions
                        </a>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 col-sm-6 col-6">
                <div class="card contact-card border-0 shadow-sm text-center contact-fade-in-up">
                    <div class="card-body p-4">
                        <div class="contact-icon text-white">
                            <i class="fas fa-phone fa-lg" data-fallback="📞"></i>
                        </div>
                        <h5 class="card-title fw-bold">Call Us</h5>
                        <p class="card-text text-muted small">
                            <a href="tel:<?php echo str_replace(' ', '', $company_info['phone']); ?>" class="text-decoration-none text-primary fw-bold"><?php echo htmlspecialchars($company_info['phone']); ?></a><br>
                            <a href="tel:+251911654321" class="text-decoration-none text-primary">+251 911-654321</a><br>
                            <small class="text-success">24/7 Emergency</small>
                        </p>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 col-sm-6 col-6">
                <div class="card contact-card border-0 shadow-sm text-center contact-fade-in-up">
                    <div class="card-body p-4">
                        <div class="contact-icon text-white">
                            <i class="fas fa-envelope fa-lg" data-fallback="✉️"></i>
                        </div>
                        <h5 class="card-title fw-bold">Email Us</h5>
                        <p class="card-text text-muted small">
                            <a href="mailto:<?php echo htmlspecialchars($company_info['email']); ?>" class="text-decoration-none text-primary fw-bold"><?php echo htmlspecialchars($company_info['email']); ?></a><br>
                            <a href="mailto:service@natiautomotive.com" class="text-decoration-none text-primary">service@natiautomotive.com</a><br>
                            <small class="text-success">24h Reply</small>
                        </p>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 col-sm-6 col-6">
                <div class="card contact-card border-0 shadow-sm text-center contact-fade-in-up">
                    <div class="card-body p-4">
                        <div class="contact-icon text-white">
                            <i class="fas fa-clock fa-lg" data-fallback="🕐"></i>
                        </div>
                        <h5 class="card-title fw-bold">Working Hours</h5>
                        <p class="card-text text-muted small">
                            <strong>Mon - Fri:</strong> 8:00 AM - 6:00 PM<br>
                            <strong>Saturday:</strong> 8:00 AM - 4:00 PM<br>
                            <small class="text-warning">Sunday: Emergency Only</small>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Contact Form & Map -->
<section id="contact-form" class="py-5">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="contact-section-title">Send Us a Message</h2>
            <p class="text-muted">We'd love to hear from you. Send us a message and we'll respond as soon as possible.</p>
        </div>
        <div class="row g-5">
            <!-- Contact Form -->
            <div class="col-lg-6">
                <div class="card form-enhanced border-0">
                    <div class="card-body p-4">
                        <?php if ($success_message): ?>
                        <div class="alert alert-success alert-enhanced" role="alert">
                            <i class="fas fa-check-circle me-2"></i><?php echo $success_message; ?>
                        </div>
                        <?php endif; ?>
                        
                        <?php if ($error_message): ?>
                        <div class="alert alert-danger alert-enhanced" role="alert">
                            <i class="fas fa-exclamation-circle me-2"></i><?php echo $error_message; ?>
                        </div>
                        <?php endif; ?>
                        
                        <form method="POST" action="" class="needs-validation" novalidate>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label for="name" class="form-label fw-bold">Full Name *</label>
                                    <input type="text" class="form-control" id="name" name="name" value="<?php echo htmlspecialchars($name ?? ''); ?>" required>
                                    <div class="invalid-feedback">Please provide your full name.</div>
                                </div>
                                <div class="col-md-6">
                                    <label for="email" class="form-label fw-bold">Email Address *</label>
                                    <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($email ?? ''); ?>" required>
                                    <div class="invalid-feedback">Please provide a valid email address.</div>
                                </div>
                                <div class="col-md-6">
                                    <label for="phone" class="form-label fw-bold">Phone Number</label>
                                    <input type="tel" class="form-control" id="phone" name="phone" value="<?php echo htmlspecialchars($phone ?? ''); ?>" placeholder="+251 911-123456">
                                </div>
                                <div class="col-md-6">
                                    <label for="service" class="form-label fw-bold">Service Needed</label>
                                    <select class="form-select" id="service" name="service">
                                        <option value="">Select a service</option>
                                        <?php
                                        try {
                                            $stmt = $conn->prepare("SELECT service_id, service_name FROM tbl_services WHERE status = 'active' ORDER BY service_name");
                                            $stmt->execute();
                                            $services = $stmt->fetchAll(PDO::FETCH_ASSOC);
                                            foreach ($services as $service_item) {
                                                $selected = (isset($service) && $service == $service_item['service_name']) ? 'selected' : '';
                                                echo '<option value="' . htmlspecialchars($service_item['service_name']) . '" ' . $selected . '>' . htmlspecialchars($service_item['service_name']) . '</option>';
                                            }
                                        } catch (PDOException $e) {
                                            // Fallback options if database error
                                            echo '<option value="oil-change">Oil Change & Maintenance</option>';
                                            echo '<option value="brake-service">Brake Services</option>';
                                            echo '<option value="engine-diagnostics">Engine Diagnostics</option>';
                                            echo '<option value="tire-service">Tire Services</option>';
                                            echo '<option value="electrical">Electrical Systems</option>';
                                        }
                                        ?>
                                        <option value="emergency">Emergency Service</option>
                                        <option value="other">Other</option>
                                    </select>
                                </div>
                                <div class="col-12">
                                    <label for="message" class="form-label fw-bold">Message *</label>
                                    <textarea class="form-control" id="message" name="message" rows="5" placeholder="Tell us about your vehicle, the issue you're experiencing, or any questions you have..." required><?php echo htmlspecialchars($message ?? ''); ?></textarea>
                                    <div class="invalid-feedback">Please provide your message.</div>
                                </div>
                                <div class="col-12">
                                    <button type="submit" class="btn btn-primary btn-lg w-100 contact-btn-enhanced">
                                        <i class="fas fa-paper-plane me-2"></i>Send Message
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            
            <!-- Map & Additional Info -->
            <div class="col-lg-6">
                <!-- Map -->
                <div class="map-container mb-4">
                    <iframe 
                        src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3940.6307073509845!2d38.78773831478158!3d9.005401193562896!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x164b85cef5ab402d%3A0x8467b6b037a24d49!2sAddis%20Ababa%2C%20Ethiopia!5e0!3m2!1sen!2sus!4v1642687654321!5m2!1sen!2sus" 
                        width="100%" 
                        height="100%" 
                        style="border:0;" 
                        allowfullscreen="" 
                        loading="lazy" 
                        referrerpolicy="no-referrer-when-downgrade">
                    </iframe>
                </div>
                
                <!-- Quick Contact Info -->
                <div class="card border-0 shadow-sm">
                    <div class="card-body p-4">
                        <h5 class="card-title mb-3"><i class="fas fa-info-circle text-primary me-2"></i>Quick Contact</h5>
                        <div class="row g-3">
                            <div class="col-12">
                                <div class="d-flex align-items-center">
                                    <div class="bg-primary bg-opacity-10 rounded-circle p-2 me-3">
                                        <i class="fas fa-phone text-primary"></i>
                                    </div>
                                    <div>
                                        <strong>Emergency Hotline</strong><br>
                                        <a href="tel:+251911999999" class="text-decoration-none text-primary">+251 911-999999</a>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="d-flex align-items-center">
                                    <div class="bg-success bg-opacity-10 rounded-circle p-2 me-3">
                                        <i class="fab fa-whatsapp text-success"></i>
                                    </div>
                                    <div>
                                        <strong>WhatsApp Support</strong><br>
                                        <a href="https://wa.me/251911123456" target="_blank" class="text-decoration-none text-success">+251 911-123456</a>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="d-flex align-items-center">
                                    <div class="bg-info bg-opacity-10 rounded-circle p-2 me-3">
                                        <i class="fab fa-telegram text-info"></i>
                                    </div>
                                    <div>
                                        <strong>Telegram Bot</strong><br>
                                        <a href="https://t.me/natiautomotive_bot" target="_blank" class="text-decoration-none text-info">@natiautomotive_bot</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- JavaScript for Form Validation -->
<script>
// Ensure Font Awesome icons load properly
document.addEventListener('DOMContentLoaded', function() {
    // Check if Font Awesome is loaded
    function checkFontAwesome() {
        const testElement = document.createElement('i');
        testElement.className = 'fas fa-home';
        testElement.style.position = 'absolute';
        testElement.style.left = '-9999px';
        document.body.appendChild(testElement);
        
        const computedStyle = window.getComputedStyle(testElement, ':before');
        const content = computedStyle.getPropertyValue('content');
        
        document.body.removeChild(testElement);
        
        // If Font Awesome is not loaded, add fallback
        if (!content || content === 'none' || content === '""') {
            console.warn('Font Awesome not detected, applying fallbacks');
            
            // Add fallback icons as text
            const iconMappings = {
                'fa-map-marker-alt': '📍',
                'fa-phone': '📞',
                'fa-envelope': '✉️',
                'fa-clock': '🕐',
                'fa-route': '🗺️',
                'fa-paper-plane': '📤',
                'fa-check-circle': '✅',
                'fa-exclamation-circle': '❗',
                'fa-info-circle': 'ℹ️',
                'fa-whatsapp': '💬',
                'fa-telegram': '✈️'
            };
            
            // Apply fallback icons
            Object.keys(iconMappings).forEach(iconClass => {
                const elements = document.querySelectorAll('.' + iconClass);
                elements.forEach(el => {
                    el.textContent = iconMappings[iconClass];
                    el.style.fontFamily = 'system-ui, -apple-system, sans-serif';
                    el.style.fontSize = '1.2em';
                });
            });
        } else {
            console.log('Font Awesome loaded successfully');
        }
    }
    
    // Check Font Awesome loading with delay
    setTimeout(checkFontAwesome, 100);
    setTimeout(checkFontAwesome, 500);
    setTimeout(checkFontAwesome, 1000);
});

// Bootstrap form validation
(function() {
    'use strict';
    window.addEventListener('load', function() {
        var forms = document.getElementsByClassName('needs-validation');
        var validation = Array.prototype.filter.call(forms, function(form) {
            form.addEventListener('submit', function(event) {
                if (form.checkValidity() === false) {
                    event.preventDefault();
                    event.stopPropagation();
                }
                form.classList.add('was-validated');
            }, false);
        });
    }, false);
})();

// Smooth scrolling for anchor links
document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', function (e) {
        e.preventDefault();
        document.querySelector(this.getAttribute('href')).scrollIntoView({
            behavior: 'smooth'
        });
    });
});

// Form submission feedback
document.querySelector('form').addEventListener('submit', function(e) {
    const submitBtn = this.querySelector('button[type="submit"]');
    const originalText = submitBtn.innerHTML;
    
    if (this.checkValidity()) {
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Sending...';
        submitBtn.disabled = true;
        
        // Re-enable after form submission (in case of errors)
        setTimeout(() => {
            submitBtn.innerHTML = originalText;
            submitBtn.disabled = false;
        }, 3000);
    }
});
</script>

<?php require_once 'includes/footer.php'; ?> 