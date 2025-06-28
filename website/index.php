<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nati Automotive - Professional Auto Services</title>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.css">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body class="dark-theme">
    <?php include 'includes/header.php'; ?>
    <?php include 'includes/navbar.php'; ?>

    <!-- Hero Section -->
    <?php include 'includes/hero-section.php'; ?>

    <!-- Info Cards Section -->
    <section class="info-cards">
        <div class="container">
            <div class="info-card">
                <div class="card-icon">
                    <i class="fas fa-clock"></i>
                </div>
                <div class="card-content">
                    <h3>Working Hours</h3>
                    <p>Mon-Sat: 8:00 AM - 6:00 PM</p>
                </div>
            </div>
            
            <div class="info-card">
                <div class="card-icon">
                    <i class="fas fa-map-marker-alt"></i>
                </div>
                <div class="card-content">
                    <h3>Location</h3>
                    <p>Aware, Adwa Bridge, Addis Ababa</p>
                </div>
            </div>
            
            <div class="info-card">
                <div class="card-icon">
                    <i class="fas fa-phone-alt"></i>
                </div>
                <div class="card-content">
                    <h3>Contact</h3>
                    <p>+251912143538</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Services Overview Section -->
    <section class="services-overview">
        <div class="container">
            <h2 class="section-title">Our Services</h2><br>
            
            <div class="services-grid">
                <div class="service-card">
                    <div class="service-icon">
                        <i class="fas fa-car-crash"></i>
                    </div>
                    <h3>Body Works</h3>
                    <p>Complete auto body repair and painting services</p>
                </div>
                
                <div class="service-card">
                    <div class="service-icon">
                        <i class="fas fa-bolt"></i>
                    </div>
                    <h3>Electrical Systems</h3>
                    <p>Diagnostic and repair of electrical components</p>
                </div>
                
                <div class="service-card">
                    <div class="service-icon">
                        <i class="fas fa-cogs"></i>
                    </div>
                    <h3>Engine Service</h3>
                    <p>Comprehensive engine maintenance and repair</p>
                </div>
                
                <div class="service-card">
                    <div class="service-icon">
                        <i class="fas fa-brake-disk"></i>
                    </div>
                    <h3>Brake Service</h3>
                    <p>Professional brake system maintenance</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Blog Preview -->
    <section class="blog-preview" data-aos="fade-up">
        <div class="container">
            <h2>Latest News</h2>
            <div class="blog-grid">
                <?php
                require_once 'includes/blog_functions.php';
                
                // Get latest blogs and articles
                $blogs = getLatestBlogs(3);
                
                foreach($blogs as $blog) {
                    ?>
                    <div class="blog-card">
                        <div class="blog-image">
                            <?php if($blog['image_url']): ?>
                                <img src="<?php echo htmlspecialchars($blog['image_url']); ?>" alt="<?php echo htmlspecialchars($blog['title']); ?>">
                            <?php else: ?>
                                <img src="assets/images/default-blog.jpg" alt="Default Blog Image">
                            <?php endif; ?>
                        </div>
                        <div class="blog-content">
                            <div class="blog-meta">
                                <span class="date"><i class="far fa-calendar"></i> <?php echo $blog['formatted_date']; ?></span>
                                <span class="comments"><i class="far fa-comments"></i> <?php echo $blog['comment_count']; ?></span>
                                <span class="likes"><i class="far fa-heart"></i> <?php echo $blog['like_count']; ?></span>
                            </div>
                            <h3><?php echo htmlspecialchars($blog['title']); ?></h3>
                            <p><?php echo htmlspecialchars($blog['preview']); ?></p>
                            <a href="blog-single.php?id=<?php echo $blog['id']; ?>" class="read-more">Read More <i class="fas fa-arrow-right"></i></a>
                        </div>
                    </div>
                    <?php
                }
                ?>
            </div>
            <div class="view-all-wrapper">
                <a href="blogs.php" class="view-all-btn">View All News</a>
            </div>
        </div>
    </section>

    <!-- Testimonials -->
    <section class="testimonials" data-aos="fade-up">
        <div class="container">
            <h2>What Our Clients Say</h2>
            <div class="testimonial-slider">
                <!-- Testimonials will be dynamically loaded here -->
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="cta-section" data-aos="fade-up">
        <div class="container">
            <h2>Ready to Get Started?</h2>
            <p>Book your appointment today and experience our professional service</p>
            <a href="contact.php" class="cta-button glowing">Book Appointment</a>
        </div>
    </section>

    <?php include 'includes/footer.php'; ?>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/js/all.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.js"></script>
    <script src="assets/js/scripts.js"></script>
</body>
</html> 