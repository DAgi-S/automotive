<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About Us - Nati Automotive</title>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.css">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body class="dark-theme">
    <?php include 'includes/header.php'; ?>

    <!-- Hero Section -->
    <section class="about-hero">
        <div class="hero-overlay"></div>
        <div class="container">
            <h1 data-aos="fade-up">About Us</h1>
            <p data-aos="fade-up" data-aos-delay="100">Learn more about our commitment to excellence in automotive care</p>
        </div>
    </section>

    <!-- Story Section -->
    <section class="story-section">
        <div class="container">
            <div class="story-grid">
                <div class="story-content" data-aos="fade-right">
                    <h2>Our Story</h2>
                    <p>Founded in 2010, Nati Automotive has been at the forefront of automotive excellence. We combine traditional expertise with cutting-edge technology to deliver the best service to our clients.</p>
                    
                    <div class="mission-vision">
                        <div class="mission" data-aos="fade-up">
                            <h3><i class="fas fa-bullseye"></i> Our Mission</h3>
                            <p>To provide exceptional automotive care through technical excellence, honest communication, and outstanding customer service.</p>
                        </div>
                        
                        <div class="vision" data-aos="fade-up" data-aos-delay="100">
                            <h3><i class="fas fa-eye"></i> Our Vision</h3>
                            <p>To be the most trusted name in automotive care, setting industry standards for quality and customer satisfaction.</p>
                        </div>
                    </div>
                </div>
                <div class="story-image" data-aos="fade-left">
                    <div class="image-container">
                        <img src="assets/images/workshop.jpg" alt="Our Workshop">
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Stats Section -->
    <section class="stats-section">
        <div class="container">
            <div class="stats-grid">
                <div class="stat-card" data-aos="fade-up">
                    <div class="stat-icon">
                        <i class="fas fa-car"></i>
                    </div>
                    <div class="stat-number" data-count="10000">10,000+</div>
                    <div class="stat-label">Cars Serviced</div>
                </div>

                <div class="stat-card" data-aos="fade-up" data-aos-delay="100">
                    <div class="stat-icon">
                        <i class="fas fa-users"></i>
                    </div>
                    <div class="stat-number" data-count="5000">5,000+</div>
                    <div class="stat-label">Happy Clients</div>
                </div>

                <div class="stat-card" data-aos="fade-up" data-aos-delay="200">
                    <div class="stat-icon">
                        <i class="fas fa-tools"></i>
                    </div>
                    <div class="stat-number" data-count="15">15+</div>
                    <div class="stat-label">Years Experience</div>
                </div>

                <div class="stat-card" data-aos="fade-up" data-aos-delay="300">
                    <div class="stat-icon">
                        <i class="fas fa-award"></i>
                    </div>
                    <div class="stat-number" data-count="50">50+</div>
                    <div class="stat-label">Awards Won</div>
                </div>
            </div>
        </div>
    </section>

    <!-- Team Section -->
    <section class="team-section">
        <div class="container">
            <h2 class="section-title" data-aos="fade-up">Our Expert Team</h2>
            <div class="team-grid">
                <div class="team-card" data-aos="fade-up">
                    <div class="member-image">
                        <img src="assets/images/team1.jpg" alt="Nati">
                        <div class="member-overlay">
                            <div class="member-social">
                                <a href="#"><i class="fab fa-linkedin"></i></a>
                                <a href="#"><i class="fab fa-twitter"></i></a>
                            </div>
                        </div>
                    </div>
                    <div class="member-info">
                        <h3>Nati</h3>
                        <p>CEO & Master Technician</p>
                    </div>
                </div>

                <div class="team-card" data-aos="fade-up" data-aos-delay="100">
                    <div class="member-image">
                        <img src="assets/images/team2.jpg" alt="Shaka">
                        <div class="member-overlay">
                            <div class="member-social">
                                <a href="#"><i class="fab fa-linkedin"></i></a>
                                <a href="#"><i class="fab fa-twitter"></i></a>
                            </div>
                        </div>
                    </div>
                    <div class="member-info">
                        <h3>Shaka</h3>
                        <p>Diagnostic Specialist</p>
                    </div>
                </div>

                <div class="team-card" data-aos="fade-up" data-aos-delay="200">
                    <div class="member-image">
                        <img src="assets/images/team3.jpg" alt="Ali">
                        <div class="member-overlay">
                            <div class="member-social">
                                <a href="#"><i class="fab fa-linkedin"></i></a>
                                <a href="#"><i class="fab fa-twitter"></i></a>
                            </div>
                        </div>
                    </div>
                    <div class="member-info">
                        <h3>Ali</h3>
                        <p>Body Work Expert</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <?php include 'includes/footer.php'; ?>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/js/all.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.js"></script>
    <script src="assets/js/scripts.js"></script>

<style>
/* About Hero Section */
.about-hero {
    position: relative;
    height: 400px;
    background: url('assets/images/hero-bg.jpg') no-repeat center center;
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

.about-hero .container {
    position: relative;
    z-index: 2;
    color: #fff;
}

.about-hero h1 {
    font-size: 3.5rem;
    margin-bottom: 20px;
}

.about-hero p {
    font-size: 1.2rem;
    opacity: 0.9;
}

/* Story Section */
.story-section {
    padding: 80px 0;
    background: #111;
}

.story-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 50px;
    align-items: center;
}

.story-content h2 {
    color: #fff;
    font-size: 2.5rem;
    margin-bottom: 30px;
}

.story-content p {
    color: #fff;
    opacity: 0.8;
    line-height: 1.8;
    margin-bottom: 40px;
}

.mission-vision {
    display: grid;
    gap: 30px;
}

.mission, .vision {
    background: rgba(255,255,255,0.05);
    padding: 30px;
    border-radius: 15px;
    border: 1px solid rgba(255,255,255,0.1);
}

.mission h3, .vision h3 {
    color: #ffbe00;
    display: flex;
    align-items: center;
    gap: 10px;
    margin-bottom: 15px;
}

.image-container {
    border-radius: 20px;
    overflow: hidden;
    box-shadow: 0 20px 40px rgba(0,0,0,0.3);
}

.image-container img {
    width: 100%;
    height: auto;
    transition: transform 0.3s ease;
}

.image-container:hover img {
    transform: scale(1.05);
}

/* Stats Section */
.stats-section {
    padding: 80px 0;
    background: linear-gradient(135deg, #000F1D, #001F3D);
}

.stats-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 30px;
}

.stat-card {
    background: rgba(255,255,255,0.05);
    border-radius: 15px;
    padding: 40px 20px;
    text-align: center;
    color: #fff;
    border: 1px solid rgba(255,255,255,0.1);
    transition: transform 0.3s ease;
}

.stat-card:hover {
    transform: translateY(-10px);
}

.stat-icon {
    width: 70px;
    height: 70px;
    background: linear-gradient(135deg, #ffbe00, #0077ff);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 20px;
}

.stat-icon i {
    font-size: 30px;
    color: #fff;
}

.stat-number {
    font-size: 2.5rem;
    font-weight: 700;
    margin-bottom: 10px;
    background: linear-gradient(135deg, #ffbe00, #0077ff);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
}

.stat-label {
    font-size: 1.1rem;
    opacity: 0.8;
}

/* Team Section */
.team-section {
    padding: 80px 0;
    background: #111;
}

.section-title {
    text-align: center;
    color: #fff;
    font-size: 2.5rem;
    margin-bottom: 60px;
    position: relative;
}

.section-title::after {
    content: '';
    position: absolute;
    bottom: -15px;
    left: 50%;
    transform: translateX(-50%);
    width: 80px;
    height: 3px;
    background: #ffbe00;
}

.team-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 40px;
}

.team-card {
    background: rgba(255,255,255,0.05);
    border-radius: 15px;
    overflow: hidden;
    transition: transform 0.3s ease;
}

.team-card:hover {
    transform: translateY(-10px);
}

.member-image {
    position: relative;
    overflow: hidden;
}

.member-image img {
    width: 100%;
    height: auto;
    transition: transform 0.3s ease;
}

.member-overlay {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0,168,255,0.9);
    display: flex;
    align-items: center;
    justify-content: center;
    opacity: 0;
    transition: opacity 0.3s ease;
}

.team-card:hover .member-overlay {
    opacity: 1;
}

.member-social {
    display: flex;
    gap: 20px;
}

.member-social a {
    color: #fff;
    font-size: 1.5rem;
    transition: transform 0.3s ease;
}

.member-social a:hover {
    transform: scale(1.2);
}

.member-info {
    padding: 20px;
    text-align: center;
    color: #fff;
}

.member-info h3 {
    margin: 0 0 5px;
    font-size: 1.3rem;
}

.member-info p {
    margin: 0;
    opacity: 0.8;
    color: #ffbe00;
}

/* Responsive Design */
@media (max-width: 992px) {
    .story-grid {
        grid-template-columns: 1fr;
    }
    
    .stats-grid {
        grid-template-columns: repeat(2, 1fr);
    }
    
    .team-grid {
        grid-template-columns: repeat(2, 1fr);
    }
}

@media (max-width: 768px) {
    .about-hero h1 {
        font-size: 2.5rem;
    }
    
    .stats-grid {
        grid-template-columns: 1fr;
    }
    
    .team-grid {
        grid-template-columns: 1fr;
    }
    
    .story-content h2 {
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

// Stats Counter Animation
const stats = document.querySelectorAll('.stat-number');
stats.forEach(stat => {
    const target = parseInt(stat.getAttribute('data-count'));
    let count = 0;
    const duration = 2000; // 2 seconds
    const increment = target / (duration / 16); // 60fps

    const updateCount = () => {
        if (count < target) {
            count += increment;
            stat.textContent = Math.ceil(count) + '+';
            requestAnimationFrame(updateCount);
        } else {
            stat.textContent = target + '+';
        }
    };

    const observer = new IntersectionObserver((entries) => {
        if (entries[0].isIntersecting) {
            updateCount();
            observer.disconnect();
        }
    });

    observer.observe(stat);
});
</script>
</body>
</html> 