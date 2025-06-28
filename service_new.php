<?php
define('INCLUDED', true);

session_start(); // Start the session

if (!isset($_SESSION['password'])) {
    header("location: login.php?message=login first");
    exit;
}

include("partial-front/db_con.php");
include("db_conn.php");
include 'header.php';
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <title>Nati Automotive - Services</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta charset="UTF-8">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="assets/css/service-page.css">
</head>

<body>
  <div class="site-content">
        <?php include 'top_nav.php' ?>

        <section id="services" class="container mt-4">
            <!-- Service Banner -->
            <div class="service-banner">
                <h1>Our Professional Services</h1>
                <p>Expert automotive care for your vehicle's every need</p>
            </div>

            <?php echo displayAd($conn, 'service_page'); ?>

            <div class="row">
                <!-- Main Content -->
                <div class="col-lg-8">
                    <h2 class="section-title">Available Services</h2>
                    <div class="row-cols-2">
                        <?php
                        try {
                            // Fetch active services from the database using PDO
                            $query = "SELECT * FROM tbl_services WHERE status = 'active' ORDER BY service_id ASC";
                            $stmt = $conn->prepare($query);
                            $stmt->execute();
                            $services = $stmt->fetchAll(PDO::FETCH_ASSOC);

                            if (count($services) > 0) {
                                foreach ($services as $service) {
                                    // Use the icon_class from database or fallback to default
                                    $iconClass = !empty($service['icon_class']) ? $service['icon_class'] : 'fas fa-wrench';
                                    ?>
                                    <div class="service-card">
                                        <div class="card-body">
                                            <div class="service-header">
                                                <div class="service-icon">
                                                    <i class="<?php echo htmlspecialchars($iconClass); ?>"></i>
                                                </div>
                                                <h3 class="service-title"><?php echo htmlspecialchars($service['service_name']); ?></h3>
                                            </div>
                                            
                                            <p class="service-description"><?php echo htmlspecialchars($service['description']); ?></p>
                                            
                                            <div class="service-footer">
                                                <div class="service-price">Br <?php echo number_format($service['price'], 2); ?></div>
                                                <div class="service-duration">
                                                    <i class="far fa-clock"></i> 
                                                    <?php echo htmlspecialchars($service['duration']); ?>
                                                </div>
                                            </div>
                                            
                                            <a href="order_service.php?service_id=<?php echo $service['service_id']; ?>" 
                                               class="btn-book">
                                                <i class="fas fa-calendar-plus"></i>
                                                Book Now
                                            </a>
                                        </div>
                                    </div>
                                    <?php
                                }
                            } else {
                                echo '<div class="col-12 text-center">';
                                echo '<div class="alert alert-info">';
                                echo '<i class="fas fa-info-circle me-2"></i>';
                                echo 'No services available at the moment. Please check back later.';
                                echo '</div>';
                                echo '</div>';
                            }
                        } catch (PDOException $e) {
                            echo '<div class="col-12 text-center">';
                            echo '<div class="alert alert-danger">';
                            echo '<i class="fas fa-exclamation-triangle me-2"></i>';
                            echo 'Error loading services. Please try again later.';
                            echo '</div>';
                            echo '</div>';
                        }
                        ?>
                    </div>
                </div>

                <!-- Sidebar -->
                <div class="col-lg-4">
                    <?php echo displayAd($conn, 'sidebar'); ?>

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
                                <span>Closed</span>
                            </li>
                        </ul>
                    </div>

                    <!-- Emergency Contact -->
                    <div class="emergency-contact">
                        <h3><i class="fas fa-phone-alt me-2"></i>Emergency Service</h3>
                        <p>24/7 Emergency Support Available for urgent automotive needs</p>
                        <a href="tel:+251911123456" class="btn w-100">
                            <i class="fas fa-phone-alt me-2"></i>Call Emergency Line
                        </a>
                    </div>
                </div>
            </div>

            <?php echo displayAd($conn, 'service_bottom'); ?>
        </section>

        <?php include 'partial-front/bottom_nav.php'; ?>
        <?php include 'option.php' ?>
  </div>
  
  <script src="assets/js/jquery.min.js"></script>
  <script src="assets/js/bootstrap.bundle.min.js"></script>
  <script src="assets/js/custom.js"></script>
  
  <script>
    // Enhanced service page interactions
    $(document).ready(function() {
        // Add loading state to book buttons
        $('.btn-book').on('click', function(e) {
            const button = $(this);
            const originalText = button.html();
            
            button.addClass('loading');
            button.html('<i class="fas fa-spinner fa-spin me-2"></i>Booking...');
            
            // Reset after navigation (if user comes back)
            setTimeout(() => {
                button.removeClass('loading');
                button.html(originalText);
            }, 2000);
        });
        
        // Add smooth hover animations
        $('.service-card').hover(
            function() {
                $(this).find('.service-icon').addClass('animated');
            },
            function() {
                $(this).find('.service-icon').removeClass('animated');
            }
        );
        
        // Add click tracking for analytics
        $('.service-card').on('click', function() {
            const serviceName = $(this).find('.service-title').text();
            console.log('Service card clicked:', serviceName);
            // Add analytics tracking here if needed
        });
        
        // Emergency call button enhancement
        $('.emergency-contact .btn').on('click', function(e) {
            // Add confirmation for emergency calls
            if (!confirm('You are about to call our emergency service line. Continue?')) {
                e.preventDefault();
            }
        });
        
        // Lazy load animations
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
        
        // Observe service cards for animations
        $('.service-card').each(function() {
            observer.observe(this);
        });
    });
  </script>
</body>

</html> 