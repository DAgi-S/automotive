<?php
define('INCLUDED', true);
$page_title = 'About Us';
require_once 'includes/header.php';
?>

<!-- Custom CSS for Enhanced About Page -->
<style>
/* Enhanced About Page Styles */
.about-hero-section {
    background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
    position: relative;
    overflow: hidden;
}

.about-hero-section::before {
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

.about-hero-section .container {
    position: relative;
    z-index: 2;
}

.about-section-enhanced {
    padding: 4rem 0;
}

.about-section-title {
    font-weight: 700;
    color: #2c3e50;
    position: relative;
    margin-bottom: 2rem;
}

.about-section-title::after {
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

.about-section-title.text-start::after {
    left: 0;
    transform: none;
}

.about-card-enhanced {
    transition: all 0.3s ease;
    border-radius: 20px !important;
    border: 1px solid rgba(0,0,0,0.05) !important;
    overflow: hidden;
}

.about-card-enhanced:hover {
    transform: translateY(-8px);
    box-shadow: 0 20px 40px rgba(0,0,0,0.1) !important;
}

.about-team-card {
    transition: all 0.3s ease;
    border-radius: 20px !important;
    border: 1px solid rgba(0,0,0,0.05) !important;
    overflow: hidden;
    background: #fff;
}

.about-team-card:hover {
    transform: translateY(-10px);
    box-shadow: 0 25px 50px rgba(0,0,0,0.15) !important;
}

.about-team-card .card-body {
    padding: 2rem 1.5rem;
}

.about-team-image {
    width: 120px;
    height: 120px;
    border-radius: 50%;
    object-fit: cover;
    border: 4px solid #fff;
    box-shadow: 0 8px 25px rgba(0,0,0,0.15);
    transition: all 0.3s ease;
}

.about-team-card:hover .about-team-image {
    transform: scale(1.1);
    box-shadow: 0 12px 35px rgba(0,0,0,0.2);
}

.about-value-icon {
    width: 80px;
    height: 80px;
    background: linear-gradient(135deg, #007bff10, #0056b320);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 1.5rem auto;
    transition: all 0.3s ease;
}

.about-value-icon:hover {
    background: linear-gradient(135deg, #007bff20, #0056b340);
    transform: scale(1.1);
}

.about-stat-card {
    background: rgba(255,255,255,0.1);
    border-radius: 20px;
    padding: 2rem;
    text-align: center;
    backdrop-filter: blur(10px);
    border: 1px solid rgba(255,255,255,0.2);
    transition: all 0.3s ease;
}

.about-stat-card:hover {
    background: rgba(255,255,255,0.2);
    transform: translateY(-5px);
}

.about-cert-card {
    transition: all 0.3s ease;
    border-radius: 20px !important;
    border: 1px solid rgba(0,0,0,0.05) !important;
    overflow: hidden;
    background: #fff;
}

.about-cert-card:hover {
    transform: translateY(-8px);
    box-shadow: 0 20px 40px rgba(0,0,0,0.1) !important;
}

.about-cert-icon {
    transition: all 0.3s ease;
}

.about-cert-card:hover .about-cert-icon {
    transform: scale(1.1) rotate(5deg);
}

.about-btn-enhanced {
    border-radius: 25px !important;
    padding: 0.75rem 2rem;
    font-weight: 600;
    transition: all 0.3s ease;
    border: 2px solid transparent;
}

.btn-primary.about-btn-enhanced {
    background: linear-gradient(135deg, #007bff, #0056b3);
    border: none;
}

.btn-primary.about-btn-enhanced:hover {
    background: linear-gradient(135deg, #0056b3, #004085);
    transform: translateY(-3px);
    box-shadow: 0 10px 25px rgba(0,123,255,0.4);
}

.btn-outline-primary.about-btn-enhanced {
    border: 2px solid #007bff;
    color: #007bff;
    background: transparent;
}

.btn-outline-primary.about-btn-enhanced:hover {
    background: #007bff;
    transform: translateY(-3px);
    box-shadow: 0 10px 25px rgba(0,123,255,0.4);
}

.about-story-image {
    border-radius: 20px;
    box-shadow: 0 15px 35px rgba(0,0,0,0.1);
    transition: all 0.3s ease;
}

.about-story-image:hover {
    transform: scale(1.02);
    box-shadow: 0 20px 45px rgba(0,0,0,0.15);
}

.about-stats-counter {
    font-size: 3.5rem;
    font-weight: 700;
    background: linear-gradient(135deg, #fff, #f8f9fa);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}

/* Mobile Optimizations */
@media (max-width: 768px) {
    .about-section-enhanced {
        padding: 2.5rem 0;
    }
    
    .about-hero-section {
        padding: 3rem 0;
    }
    
    .about-hero-section h1 {
        font-size: 2.5rem;
    }
    
    .about-team-image {
        width: 100px;
        height: 100px;
    }
    
    .about-value-icon {
        width: 60px;
        height: 60px;
        margin-bottom: 1rem;
    }
    
    .about-stats-counter {
        font-size: 2.5rem;
    }
    
    .about-stat-card {
        padding: 1.5rem;
        margin-bottom: 1rem;
    }
}

@media (max-width: 576px) {
    .about-hero-section h1 {
        font-size: 2rem;
    }
    
    .about-hero-section .lead {
        font-size: 1rem;
    }
    
    .about-team-card .card-body {
        padding: 1.5rem 1rem;
    }
    
    .about-value-icon {
        width: 50px;
        height: 50px;
    }
    
    .about-cert-card .card-body {
        padding: 1.5rem;
    }
}

/* Animation Classes */
.about-fade-in-up {
    opacity: 0;
    transform: translateY(30px);
    transition: all 0.8s ease;
}

.about-fade-in-up.visible {
    opacity: 1;
    transform: translateY(0);
}

.about-fade-in-left {
    opacity: 0;
    transform: translateX(-30px);
    transition: all 0.8s ease;
}

.about-fade-in-left.visible {
    opacity: 1;
    transform: translateX(0);
}

.about-fade-in-right {
    opacity: 0;
    transform: translateX(30px);
    transition: all 0.8s ease;
}

.about-fade-in-right.visible {
    opacity: 1;
    transform: translateX(0);
}

/* Loading Animation */
.about-loading-spinner {
    display: inline-block;
    width: 20px;
    height: 20px;
    border: 3px solid rgba(255,255,255,.3);
    border-radius: 50%;
    border-top-color: #fff;
    animation: aboutSpin 1s ease-in-out infinite;
}

@keyframes aboutSpin {
    to { transform: rotate(360deg); }
}

/* Smooth Scroll */
html {
    scroll-behavior: smooth;
}
</style>

<!-- Hero Section -->
<section class="about-hero-section text-white py-5">
    <div class="container">
        <div class="row align-items-center min-vh-50">
            <div class="col-lg-8 about-fade-in-left">
                <h1 class="display-4 fw-bold mb-3">About Nati Automotive</h1>
                <p class="lead mb-4">Your trusted partner in automotive excellence since 2010. We combine traditional craftsmanship with modern technology to deliver exceptional service and quality parts.</p>
                <a href="#story" class="btn btn-light about-btn-enhanced btn-lg">
                    <i class="fas fa-book-open me-2"></i>Learn Our Story
                </a>
            </div>
            <div class="col-lg-4 text-center about-fade-in-right">
                <div class="hero-icon">
                    <i class="fas fa-car display-1 text-light opacity-75"></i>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Our Story -->
<section id="story" class="about-section-enhanced">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6 about-fade-in-left">
                <h2 class="about-section-title text-start">Our Story</h2>
                <p class="lead mb-4">Founded in 2010 by a team of passionate automotive professionals, Nati Automotive began as a small workshop with a big vision: to provide honest, reliable, and high-quality automotive services to our community.</p>
                
                <p class="mb-4">Over the years, we've grown from a modest garage to a full-service automotive center, but our core values remain unchanged. We believe in treating every customer like family and every vehicle like it's our own.</p>
                
                <p class="mb-4">Today, we're proud to serve thousands of satisfied customers with our comprehensive range of services, from routine maintenance to complex repairs, all backed by our commitment to excellence and customer satisfaction.</p>
                
                <div class="row g-4 mt-4">
                    <div class="col-6">
                        <div class="text-center">
                            <h3 class="fw-bold text-primary display-6">15+</h3>
                            <p class="mb-0 text-muted fw-semibold">Years of Experience</p>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="text-center">
                            <h3 class="fw-bold text-primary display-6">10,000+</h3>
                            <p class="mb-0 text-muted fw-semibold">Vehicles Serviced</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-6 about-fade-in-right">
                <div class="text-center">
                    <img src="assets/images/gallery/nati1.jpg" alt="Our Workshop" class="img-fluid about-story-image" style="max-height: 500px; object-fit: cover; width: 100%;" onerror="this.src='assets/images/homescreen/auto1.jpg'">
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Mission & Vision -->
<section class="about-section-enhanced bg-light">
    <div class="container">
        <div class="text-center mb-5 about-fade-in-up">
            <h2 class="about-section-title">Mission & Vision</h2>
            <p class="lead text-muted">Driving our commitment to excellence and customer satisfaction</p>
        </div>
        
        <div class="row g-4">
            <div class="col-lg-6 about-fade-in-left">
                <div class="card about-card-enhanced h-100 border-0 shadow-sm">
                    <div class="card-body p-5 text-center">
                        <div class="about-value-icon mb-4">
                            <i class="fas fa-bullseye fa-2x text-primary"></i>
                        </div>
                        <h3 class="card-title mb-4">Our Mission</h3>
                        <p class="card-text">To provide exceptional automotive care through technical excellence, honest communication, and outstanding customer service. We strive to build lasting relationships with our customers by exceeding their expectations and keeping their vehicles running safely and efficiently.</p>
                    </div>
                </div>
            </div>
            <div class="col-lg-6 about-fade-in-right">
                <div class="card about-card-enhanced h-100 border-0 shadow-sm">
                    <div class="card-body p-5 text-center">
                        <div class="about-value-icon mb-4">
                            <i class="fas fa-eye fa-2x text-primary"></i>
                        </div>
                        <h3 class="card-title mb-4">Our Vision</h3>
                        <p class="card-text">To be the most trusted name in automotive care in Ethiopia, setting industry standards for quality, innovation, and customer satisfaction. We envision a future where every vehicle owner has access to reliable, professional automotive services.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Our Team -->
<section class="about-section-enhanced">
    <div class="container">
        <div class="row mb-5">
            <div class="col-lg-8 mx-auto text-center about-fade-in-up">
                <h2 class="about-section-title">Meet Our Expert Team</h2>
                <p class="lead text-muted">Our certified technicians and staff are dedicated to providing you with the best automotive service experience.</p>
            </div>
        </div>

        <div class="row g-4 justify-content-center">
            <?php
            // Fetch team members from database (connection already available from header)
            try {
                $stmt = $conn->prepare("SELECT id, full_name, position, image_url FROM tbl_worker ORDER BY id ASC");
                $stmt->execute();
                $team_members = $stmt->fetchAll(PDO::FETCH_ASSOC);
                
                // If no team members found, show default ones
                if (empty($team_members)) {
                    $team_members = [
                        [
                            'full_name' => 'Nati Bekele',
                            'position' => 'Founder & Master Technician',
                            'image_url' => null
                        ],
                        [
                            'full_name' => 'Shaka Meron', 
                            'position' => 'Lead Diagnostic Specialist',
                            'image_url' => null
                        ],
                        [
                            'full_name' => 'Ali Hassan',
                            'position' => 'Body Work Expert', 
                            'image_url' => null
                        ]
                    ];
                }

                foreach ($team_members as $index => $member): 
                    $image_path = $member['image_url'] ? 'admin/uploads/workers/' . $member['image_url'] : 'assets/images/single-courses/client1.png';
                    $position_title = ucfirst($member['position']);
                    if ($position_title === 'Worker') {
                        $position_title = 'Automotive Technician';
                    }
                    
                    $animation_class = ($index % 2 == 0) ? 'about-fade-in-left' : 'about-fade-in-right';
                    ?>
            
             <div class="col-lg-4 col-md-6 <?php echo $animation_class; ?>">
                 <div class="card about-team-card border-0 shadow-sm">
                    <div class="card-body text-center">
                        <div class="mb-4">
                            <img src="<?php echo $image_path; ?>" alt="<?php echo htmlspecialchars($member['full_name']); ?>" class="about-team-image" onerror="this.src='assets/images/single-courses/client1.png'">
                        </div>
                        <h4 class="card-title mb-2 fw-bold"><?php echo htmlspecialchars($member['full_name']); ?></h4>
                        <p class="text-primary fw-semibold mb-3"><?php echo htmlspecialchars($position_title); ?></p>
                        <div class="d-flex justify-content-center align-items-center mb-2">
                            <div class="badge bg-primary bg-opacity-10 text-primary px-3 py-2 rounded-pill">
                                <i class="fas fa-star me-1"></i>5+ Years Experience
                            </div>
                        </div>
                        <p class="text-muted small mb-0">Certified Automotive Service Specialist</p>
                    </div>
                </div>
            </div>
            <?php 
                endforeach;
            } catch (PDOException $e) {
                echo '<div class="col-12"><div class="alert alert-danger">Error loading team members: ' . $e->getMessage() . '</div></div>';
            }
            ?>
        </div>
    </div>
</section>

<!-- Our Values -->
<section class="about-section-enhanced bg-light">
    <div class="container">
        <div class="row mb-5">
            <div class="col-lg-8 mx-auto text-center about-fade-in-up">
                <h2 class="about-section-title">Our Core Values</h2>
                <p class="lead text-muted">These principles guide everything we do and shape our commitment to excellence.</p>
            </div>
        </div>

        <div class="row g-4">
            <div class="col-lg-3 col-md-6 about-fade-in-up" style="animation-delay: 0.1s;">
                <div class="text-center">
                    <div class="about-value-icon">
                        <i class="fas fa-handshake fa-2x text-primary"></i>
                    </div>
                    <h4 class="fw-bold mb-3">Integrity</h4>
                    <p class="text-muted">We believe in honest communication and transparent pricing. No hidden fees, no unnecessary repairs.</p>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 about-fade-in-up" style="animation-delay: 0.2s;">
                <div class="text-center">
                    <div class="about-value-icon">
                        <i class="fas fa-star fa-2x text-primary"></i>
                    </div>
                    <h4 class="fw-bold mb-3">Excellence</h4>
                    <p class="text-muted">We strive for perfection in every service we provide, using the best parts and latest techniques.</p>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 about-fade-in-up" style="animation-delay: 0.3s;">
                <div class="text-center">
                    <div class="about-value-icon">
                        <i class="fas fa-users fa-2x text-primary"></i>
                    </div>
                    <h4 class="fw-bold mb-3">Customer Focus</h4>
                    <p class="text-muted">Your satisfaction is our priority. We listen to your needs and exceed your expectations.</p>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 about-fade-in-up" style="animation-delay: 0.4s;">
                <div class="text-center">
                    <div class="about-value-icon">
                        <i class="fas fa-graduation-cap fa-2x text-primary"></i>
                    </div>
                    <h4 class="fw-bold mb-3">Continuous Learning</h4>
                    <p class="text-muted">We stay updated with the latest automotive technologies and industry best practices.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Statistics -->
<section class="about-section-enhanced bg-primary text-white">
    <div class="container">
        <div class="row g-4">
            <div class="col-lg-3 col-md-6 about-fade-in-up" style="animation-delay: 0.1s;">
                <div class="about-stat-card">
                    <h2 class="about-stats-counter mb-2">15+</h2>
                    <p class="lead mb-0 fw-semibold">Years of Experience</p>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 about-fade-in-up" style="animation-delay: 0.2s;">
                <div class="about-stat-card">
                    <h2 class="about-stats-counter mb-2">10K+</h2>
                    <p class="lead mb-0 fw-semibold">Vehicles Serviced</p>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 about-fade-in-up" style="animation-delay: 0.3s;">
                <div class="about-stat-card">
                    <h2 class="about-stats-counter mb-2">5K+</h2>
                    <p class="lead mb-0 fw-semibold">Happy Customers</p>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 about-fade-in-up" style="animation-delay: 0.4s;">
                <div class="about-stat-card">
                    <h2 class="about-stats-counter mb-2">50+</h2>
                    <p class="lead mb-0 fw-semibold">Awards & Certifications</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Certifications & Awards -->
<section class="about-section-enhanced">
    <div class="container">
        <div class="row mb-5">
            <div class="col-lg-8 mx-auto text-center about-fade-in-up">
                <h2 class="about-section-title">Certifications & Recognition</h2>
                <p class="lead text-muted">We're proud of our industry certifications and the recognition we've received from our community.</p>
            </div>
        </div>

        <div class="row g-4">
            <div class="col-lg-4 col-md-6 about-fade-in-left">
                <div class="card about-cert-card border-0 shadow-sm h-100">
                    <div class="card-body p-4 text-center">
                        <div class="about-cert-icon mb-4">
                            <i class="fas fa-certificate fa-4x text-warning"></i>
                        </div>
                        <h4 class="fw-bold mb-3">ASE Certified</h4>
                        <p class="text-muted">Our technicians are certified by the National Institute for Automotive Service Excellence.</p>
                        <div class="badge bg-warning bg-opacity-10 text-warning px-3 py-2 rounded-pill">
                            <i class="fas fa-check-circle me-1"></i>Verified
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 col-md-6 about-fade-in-up">
                <div class="card about-cert-card border-0 shadow-sm h-100">
                    <div class="card-body p-4 text-center">
                        <div class="about-cert-icon mb-4">
                            <i class="fas fa-award fa-4x text-warning"></i>
                        </div>
                        <h4 class="fw-bold mb-3">Best Service Award</h4>
                        <p class="text-muted">Recognized as "Best Automotive Service" by the Addis Ababa Chamber of Commerce 2023.</p>
                        <div class="badge bg-warning bg-opacity-10 text-warning px-3 py-2 rounded-pill">
                            <i class="fas fa-trophy me-1"></i>2023 Winner
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 col-md-6 about-fade-in-right">
                <div class="card about-cert-card border-0 shadow-sm h-100">
                    <div class="card-body p-4 text-center">
                        <div class="about-cert-icon mb-4">
                            <i class="fas fa-shield-alt fa-4x text-warning"></i>
                        </div>
                        <h4 class="fw-bold mb-3">Quality Assurance</h4>
                        <p class="text-muted">ISO 9001:2015 certified for quality management systems and customer satisfaction.</p>
                        <div class="badge bg-warning bg-opacity-10 text-warning px-3 py-2 rounded-pill">
                            <i class="fas fa-shield-check me-1"></i>ISO Certified
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Call to Action -->
<section class="about-section-enhanced bg-light">
    <div class="container text-center">
        <div class="row">
            <div class="col-lg-8 mx-auto about-fade-in-up">
                <h2 class="about-section-title">Experience the Nati Automotive Difference</h2>
                <p class="lead mb-5">Join thousands of satisfied customers who trust us with their automotive needs. Schedule your service today!</p>
                <div class="d-flex flex-column flex-sm-row gap-3 justify-content-center">
                    <a href="services.php" class="btn btn-primary about-btn-enhanced btn-lg">
                        <i class="fas fa-wrench me-2"></i>
                        View Our Services
                    </a>
                    <a href="contact.php" class="btn btn-outline-primary about-btn-enhanced btn-lg">
                        <i class="fas fa-envelope me-2"></i>
                        Contact Us
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Enhanced JavaScript for animations and interactions -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Intersection Observer for animations
    const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    };

    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('visible');
            }
        });
    }, observerOptions);

    // Observe all animation elements
    document.querySelectorAll('.about-fade-in-up, .about-fade-in-left, .about-fade-in-right').forEach(el => {
        observer.observe(el);
    });

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
    document.querySelectorAll('.about-btn-enhanced').forEach(btn => {
        btn.addEventListener('click', function(e) {
            if (this.href && !this.href.includes('#')) {
                const originalContent = this.innerHTML;
                this.innerHTML = '<div class="about-loading-spinner me-2"></div>' + this.textContent;
                
                setTimeout(() => {
                    this.innerHTML = originalContent;
                }, 1000);
            }
        });
    });

    // Counter animation for statistics
    function animateCounters() {
        const counters = document.querySelectorAll('.about-stats-counter');
        counters.forEach(counter => {
            const target = parseInt(counter.textContent.replace(/\D/g, ''));
            const suffix = counter.textContent.replace(/\d/g, '');
            let current = 0;
            const increment = target / 100;
            const timer = setInterval(() => {
                current += increment;
                if (current >= target) {
                    current = target;
                    clearInterval(timer);
                }
                counter.textContent = Math.floor(current) + suffix;
            }, 20);
        });
    }

    // Trigger counter animation when statistics section is visible
    const statsSection = document.querySelector('.bg-primary');
    if (statsSection) {
        const statsObserver = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    animateCounters();
                    statsObserver.unobserve(entry.target);
                }
            });
        }, { threshold: 0.5 });
        
        statsObserver.observe(statsSection);
    }

    // Parallax effect for hero section
    window.addEventListener('scroll', () => {
        const scrolled = window.pageYOffset;
        const heroSection = document.querySelector('.about-hero-section');
        if (heroSection) {
            heroSection.style.transform = `translateY(${scrolled * 0.5}px)`;
        }
    });

    // Add loading states to team images
    document.querySelectorAll('.about-team-image').forEach(img => {
        img.addEventListener('load', function() {
            this.style.opacity = '1';
        });
        
        img.addEventListener('error', function() {
            this.style.opacity = '1';
        });
        
        img.style.opacity = '0';
        img.style.transition = 'opacity 0.3s ease';
    });
});
</script>

<?php require_once 'includes/footer.php'; ?> 