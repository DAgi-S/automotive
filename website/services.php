<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Our Services - Nati Automotive</title>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.css">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body class="dark-theme">
    <?php include 'includes/header.php'; ?>

    <!-- Hero Section -->
    <section class="services-hero">
        <div class="hero-overlay"></div>
        <div class="container">
            <h1 data-aos="fade-up">Our Services</h1>
        </div>
    </section>

    <!-- Service Filters -->
    <section class="service-filters">
        <div class="container">
            <div class="filter-container" data-aos="fade-up">
                <button class="filter-btn active" data-filter="all">
                    <i class="fas fa-th-large"></i>
                    <span>All Services</span>
                </button>
                <button class="filter-btn" data-filter="bodyworks">
                    <i class="fas fa-car"></i>
                    <span>Body Works</span>
                </button>
                <button class="filter-btn" data-filter="electrical">
                    <i class="fas fa-bolt"></i>
                    <span>Electrical</span>
                </button>
                <button class="filter-btn" data-filter="engine">
                    <i class="fas fa-cogs"></i>
                    <span>Engine</span>
                </button>
                <button class="filter-btn" data-filter="maintenance">
                    <i class="fas fa-tools"></i>
                    <span>Maintenance</span>
                </button>
            </div>
        </div>
    </section>

    <!-- Services Grid -->
    <section class="services-grid-section">
        <div class="container">
            <div class="services-grid">
                <!-- Body Works -->
                <div class="service-card" data-category="bodyworks" data-aos="fade-up">
                    <div class="service-icon">
                        <i class="fas fa-car-crash"></i>
                    </div>
                    <div class="service-content">
                        <h3>Collision Repair</h3>
                        <p>Expert repair services for vehicles involved in accidents</p>
                        <ul class="service-features">
                            <li><i class="fas fa-check"></i> Frame straightening</li>
                            <li><i class="fas fa-check"></i> Panel replacement</li>
                            <li><i class="fas fa-check"></i> Paint matching</li>
                        </ul>
                        <a href="contact.php" class="service-btn">Book Service</a>
                    </div>
                </div>

                <div class="service-card" data-category="bodyworks" data-aos="fade-up" data-aos-delay="100">
                    <div class="service-icon">
                        <i class="fas fa-spray-can"></i>
                    </div>
                    <div class="service-content">
                        <h3>Paint Services</h3>
                        <p>Professional painting and finishing services</p>
                        <ul class="service-features">
                            <li><i class="fas fa-check"></i> Custom paint jobs</li>
                            <li><i class="fas fa-check"></i> Color matching</li>
                            <li><i class="fas fa-check"></i> Clear coat protection</li>
                        </ul>
                        <a href="contact.php" class="service-btn">Book Service</a>
                    </div>
                </div>

                <!-- Electrical -->
                <div class="service-card" data-category="electrical" data-aos="fade-up">
                    <div class="service-icon">
                        <i class="fas fa-bolt"></i>
                    </div>
                    <div class="service-content">
                        <h3>Electrical Diagnostics</h3>
                        <p>Advanced diagnostic services for electrical systems</p>
                        <ul class="service-features">
                            <li><i class="fas fa-check"></i> Computer diagnostics</li>
                            <li><i class="fas fa-check"></i> Wiring repair</li>
                            <li><i class="fas fa-check"></i> Battery testing</li>
                        </ul>
                        <a href="contact.php" class="service-btn">Book Service</a>
                    </div>
                </div>

                <div class="service-card" data-category="electrical" data-aos="fade-up" data-aos-delay="100">
                    <div class="service-icon">
                        <i class="fas fa-car-battery"></i>
                    </div>
                    <div class="service-content">
                        <h3>Battery Services</h3>
                        <p>Battery testing, replacement, and maintenance</p>
                        <ul class="service-features">
                            <li><i class="fas fa-check"></i> Battery testing</li>
                            <li><i class="fas fa-check"></i> Replacement service</li>
                            <li><i class="fas fa-check"></i> Charging system check</li>
                        </ul>
                        <a href="contact.php" class="service-btn">Book Service</a>
                    </div>
                </div>

                <!-- Engine -->
                <div class="service-card" data-category="engine" data-aos="fade-up">
                    <div class="service-icon">
                        <i class="fas fa-cogs"></i>
                    </div>
                    <div class="service-content">
                        <h3>Engine Repair</h3>
                        <p>Comprehensive engine repair and rebuilding services</p>
                        <ul class="service-features">
                            <li><i class="fas fa-check"></i> Engine diagnostics</li>
                            <li><i class="fas fa-check"></i> Rebuilding service</li>
                            <li><i class="fas fa-check"></i> Performance tuning</li>
                        </ul>
                        <a href="contact.php" class="service-btn">Book Service</a>
                    </div>
                </div>

                <div class="service-card" data-category="engine" data-aos="fade-up" data-aos-delay="100">
                    <div class="service-icon">
                        <i class="fas fa-oil-can"></i>
                    </div>
                    <div class="service-content">
                        <h3>Oil Services</h3>
                        <p>Regular maintenance and oil change services</p>
                        <ul class="service-features">
                            <li><i class="fas fa-check"></i> Oil change</li>
                            <li><i class="fas fa-check"></i> Filter replacement</li>
                            <li><i class="fas fa-check"></i> Fluid check</li>
                        </ul>
                        <a href="contact.php" class="service-btn">Book Service</a>
                    </div>
                </div>

                <!-- Maintenance -->
                <div class="service-card" data-category="maintenance" data-aos="fade-up">
                    <div class="service-icon">
                        <i class="fas fa-brake-disk"></i>
                    </div>
                    <div class="service-content">
                        <h3>Brake Service</h3>
                        <p>Complete brake system maintenance and repair</p>
                        <ul class="service-features">
                            <li><i class="fas fa-check"></i> Brake pad replacement</li>
                            <li><i class="fas fa-check"></i> Rotor resurfacing</li>
                            <li><i class="fas fa-check"></i> System inspection</li>
                        </ul>
                        <a href="contact.php" class="service-btn">Book Service</a>
                    </div>
                </div>

                <div class="service-card" data-category="maintenance" data-aos="fade-up" data-aos-delay="100">
                    <div class="service-icon">
                        <i class="fas fa-temperature-high"></i>
                    </div>
                    <div class="service-content">
                        <h3>AC Service</h3>
                        <p>Air conditioning system repair and maintenance</p>
                        <ul class="service-features">
                            <li><i class="fas fa-check"></i> System diagnosis</li>
                            <li><i class="fas fa-check"></i> Refrigerant recharge</li>
                            <li><i class="fas fa-check"></i> Component repair</li>
                        </ul>
                        <a href="contact.php" class="service-btn">Book Service</a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="cta-section">
        <div class="container">
            <div class="cta-content" data-aos="fade-up">
                <h2>Ready to Get Started?</h2>
                <p>Book your service appointment today and experience our professional care</p>
                <a href="contact.php" class="cta-button">Schedule Service</a>
            </div>
        </div>
    </section>

    <?php include 'includes/footer.php'; ?>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/js/all.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.js"></script>
    <script src="assets/js/scripts.js"></script>

<style>
/* Services Hero Section */
.services-hero {
    position: relative;
    height: 400px;
    background: url('assets/images/services-bg.jpg') no-repeat center center;
    background-size: cover;
    display: flex;
    align-items: center;
    text-align: center;
    margin-top: 80px;
}

.hero-overlay {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0,0,0,0.7);
}

.services-hero .container {
    position: relative;
    z-index: 2;
    color: #fff;
}

.services-hero h1 {
    font-size: 3.5rem;
    margin-bottom: 20px;
}

.services-hero p {
    font-size: 1.2rem;
    opacity: 0.9;
}

/* Service Filters */
.service-filters {
    background: #111;
    padding: 40px 0;
    position: relative;
    z-index: 2;
}

.filter-container {
    display: flex;
    justify-content: center;
    gap: 20px;
    flex-wrap: wrap;
}

.filter-btn {
    background: rgba(255,255,255,0.05);
    border: 1px solid rgba(255,255,255,0.1);
    color: #fff;
    padding: 15px 30px;
    border-radius: 50px;
    cursor: pointer;
    transition: all 0.3s ease;
    display: flex;
    align-items: center;
    gap: 10px;
}

.filter-btn i {
    font-size: 1.2rem;
    color: #ffbe00;
}

.filter-btn:hover, .filter-btn.active {
    background: #ffbe00;
    transform: translateY(-2px);
}

.filter-btn:hover i, .filter-btn.active i {
    color: #fff;
}

/* Services Grid */
.services-grid-section {
    background: linear-gradient(135deg, #000F1D, #001F3D);
    padding: 80px 0;
}

.services-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 30px;
    max-width: 1200px;
    margin: 0 auto;
    padding: 0 20px;
}

.service-card {
    background: rgba(255,255,255,0.05);
    border-radius: 15px;
    overflow: hidden;
    border: 1px solid rgba(255,255,255,0.1);
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.service-card:hover {
    transform: translateY(-10px);
    box-shadow: 0 10px 30px rgba(0,168,255,0.1);
}

.service-icon {
    background: linear-gradient(135deg, #ffbe00, #0077ff);
    height: 100px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.service-icon i {
    font-size: 40px;
    color: #fff;
}

.service-content {
    padding: 30px;
    color: #fff;
}

.service-content h3 {
    margin: 0 0 15px;
    font-size: 1.5rem;
    color: #ffbe00;
}

.service-content p {
    margin: 0 0 20px;
    opacity: 0.8;
    line-height: 1.6;
}

.service-features {
    list-style: none;
    padding: 0;
    margin: 0 0 25px;
}

.service-features li {
    display: flex;
    align-items: center;
    gap: 10px;
    margin-bottom: 10px;
    opacity: 0.9;
}

.service-features li i {
    color: #ffbe00;
    font-size: 0.8rem;
}

.service-btn {
    display: inline-block;
    background: #ffbe00;
    color: #fff;
    padding: 12px 25px;
    border-radius: 5px;
    text-decoration: none;
    transition: all 0.3s ease;
}

.service-btn:hover {
    background: #0077ff;
    transform: translateY(-2px);
}

/* CTA Section */
.cta-section {
    background: url('assets/images/cta-bg.jpg') no-repeat center center;
    background-size: cover;
    position: relative;
    padding: 100px 0;
}

.cta-section::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0,0,0,0.8);
}

.cta-content {
    position: relative;
    text-align: center;
    color: #fff;
}

.cta-content h2 {
    font-size: 2.5rem;
    margin-bottom: 20px;
}

.cta-content p {
    font-size: 1.2rem;
    margin-bottom: 30px;
    opacity: 0.9;
}

.cta-button {
    display: inline-block;
    background: #ffbe00;
    color: #fff;
    padding: 15px 40px;
    border-radius: 50px;
    text-decoration: none;
    font-weight: 600;
    transition: all 0.3s ease;
}

.cta-button:hover {
    background: #0077ff;
    transform: translateY(-2px);
}

/* Responsive Design */
@media (max-width: 992px) {
    .services-hero h1 {
        font-size: 2.5rem;
    }
    
    .filter-btn {
        padding: 12px 20px;
    }
    
    .services-grid {
        grid-template-columns: repeat(2, 1fr);
    }
}

@media (max-width: 768px) {
    .services-grid {
        grid-template-columns: 1fr;
    }
    
    .filter-container {
        flex-direction: column;
        align-items: stretch;
    }
    
    .filter-btn {
        justify-content: center;
    }
    
    .cta-content h2 {
        font-size: 2rem;
    }
}
</style>

<script>
// Initialize AOS
AOS.init({
    duration: 1000,
    once: true
});

// Service filtering
document.addEventListener('DOMContentLoaded', function() {
    const filterBtns = document.querySelectorAll('.filter-btn');
    const serviceCards = document.querySelectorAll('.service-card');

    filterBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            // Remove active class from all buttons
            filterBtns.forEach(b => b.classList.remove('active'));
            // Add active class to clicked button
            this.classList.add('active');

            const filter = this.dataset.filter;

            // Add fade effect
            serviceCards.forEach(card => {
                card.style.opacity = '0';
                card.style.transform = 'translateY(20px)';
                
                setTimeout(() => {
                    if (filter === 'all' || card.dataset.category === filter) {
                        card.style.display = 'block';
                        setTimeout(() => {
                            card.style.opacity = '1';
                            card.style.transform = 'translateY(0)';
                        }, 50);
                    } else {
                        card.style.display = 'none';
                    }
                }, 300);
            });
        });
    });
});
</script>
</body>
</html> 