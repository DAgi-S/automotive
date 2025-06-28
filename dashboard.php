<?php
define('INCLUDED', true);
session_start(); // Start the session

// Check if the session 'password' is set
if (!isset($_SESSION['password'])) {
    header("location: login.php?message=login first"); // Redirect to the login page if the session is not set
    exit;
}

?>
<!DOCTYPE html>
<html lang="en">
<?php
  include 'header.php'
?>

<head>
    <title>Dashboard Warning Lights - Nati Automotive</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta charset="UTF-8">
    <meta name="description" content="Understanding dashboard warning lights and their meanings at Nati Automotive">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/css/bottom-nav-universal.css">
    <link rel="stylesheet" href="assets/css/new-home-layout.css">
    <link rel="stylesheet" href="assets/css/mobile-header.css">
    
    <style>
        /* Dashboard-specific enhancements using home CSS architecture */
        .dashboard-content {
            padding-bottom: 90px;
            background-color: #f8f9fa;
        }
        
        .dashboard-hero {
            background: linear-gradient(135deg, #2c3e50 0%, #3498db 100%);
            color: white;
            padding: 2rem 1rem;
            margin-bottom: 1.5rem;
            border-radius: var(--border-radius-lg);
            text-align: center;
            position: relative;
            overflow: hidden;
        }
        
        .dashboard-hero::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, #f39c12 0%, #e74c3c 50%, #f39c12 100%);
        }
        
        .dashboard-hero h2 {
            font-size: 1.4rem;
            font-weight: 700;
            margin: 0;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.3);
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
        }
        
        .dashboard-hero h2::before {
            content: '⚠️';
            font-size: 1.2rem;
        }
        
        .warning-light-card {
            background: white;
            border-radius: var(--border-radius-lg);
            overflow: hidden;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            transition: all 0.3s ease;
            margin-bottom: 1.5rem;
            border: 1px solid #e9ecef;
            position: relative;
        }
        
        .warning-light-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 3px;
            background: linear-gradient(90deg, #e74c3c 0%, #f39c12 50%, #e74c3c 100%);
        }
        
        .warning-light-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.15);
        }
        
        .warning-light-content {
            display: flex;
            align-items: flex-start;
            padding: 1.5rem;
            gap: 1.5rem;
        }
        
        .warning-light-icon {
            flex-shrink: 0;
            width: 100px;
            height: 100px;
            border-radius: var(--border-radius-lg);
            overflow: hidden;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
        }
        
        .warning-light-icon::after {
            content: '';
            position: absolute;
            inset: 3px;
            border-radius: var(--border-radius);
            background: white;
        }
        
        .warning-light-icon img {
            width: 80px;
            height: 80px;
            object-fit: contain;
            position: relative;
            z-index: 2;
            transition: transform 0.3s ease;
        }
        
        .warning-light-card:hover .warning-light-icon img {
            transform: scale(1.1);
        }
        
        .warning-light-info {
            flex: 1;
        }
        
        .warning-light-title {
            font-size: 1.1rem;
            font-weight: 700;
            color: var(--dark-color);
            margin-bottom: 0.75rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .warning-light-title::before {
            content: '🚨';
            font-size: 1rem;
        }
        
        .warning-light-description {
            color: var(--secondary-color);
            line-height: 1.6;
            font-size: 0.9rem;
        }
        
        .warning-light-description p {
            margin-bottom: 0.75rem;
        }
        
        .warning-light-description p:last-child {
            margin-bottom: 0;
        }
        
        .warning-light-description b {
            color: var(--dark-color);
            font-weight: 600;
        }
        
        /* Severity indicators */
        .warning-light-card.critical::before {
            background: linear-gradient(90deg, #e74c3c 0%, #c0392b 100%);
        }
        
        .warning-light-card.warning::before {
            background: linear-gradient(90deg, #f39c12 0%, #e67e22 100%);
        }
        
        .warning-light-card.info::before {
            background: linear-gradient(90deg, #3498db 0%, #2980b9 100%);
        }
        
        .warning-light-card.success::before {
            background: linear-gradient(90deg, #27ae60 0%, #229954 100%);
        }
        
        /* Mobile Responsiveness */
        @media (max-width: 768px) {
            .dashboard-hero {
                padding: 1.5rem 1rem;
                margin-bottom: 1.2rem;
            }
            
            .dashboard-hero h2 {
                font-size: 1.2rem;
                flex-direction: column;
                gap: 0.3rem;
            }
            
            .warning-light-content {
                flex-direction: column;
                padding: 1.2rem;
                gap: 1rem;
                text-align: center;
            }
            
            .warning-light-icon {
                width: 80px;
                height: 80px;
                align-self: center;
            }
            
            .warning-light-icon img {
                width: 60px;
                height: 60px;
            }
            
            .warning-light-title {
                font-size: 1rem;
                justify-content: center;
            }
            
            .warning-light-description {
                font-size: 0.85rem;
                text-align: left;
            }
        }
        
        @media (max-width: 576px) {
            .dashboard-hero h2 {
                font-size: 1.1rem;
            }
            
            .warning-light-content {
                padding: 1rem;
            }
            
            .warning-light-icon {
                width: 70px;
                height: 70px;
            }
            
            .warning-light-icon img {
                width: 50px;
                height: 50px;
            }
        }
        
        /* Animation */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .warning-light-card {
            animation: fadeInUp 0.6s ease-out;
        }
        
        .warning-light-card:nth-child(even) {
            animation-delay: 0.1s;
        }
        
        .warning-light-card:nth-child(odd) {
            animation-delay: 0.2s;
        }
    </style>
</head>

<body>
    <div class="site-content dashboard-content">
        <!-- Header start -->
        <!-- Header end -->
        
        <!-- Dashboard content start -->
        <section id="dashboard" class="container-fluid">
            
            <!-- Hero Section -->
            <div class="dashboard-hero">
                <h2>What do the more common dashboard lights actually mean?</h2>
            </div>

            <!-- Warning Lights Grid -->
            <div class="warning-lights-container">
                
                <!-- Check Engine Light -->
                <div class="warning-light-card critical">
                    <div class="warning-light-content">
                        <div class="warning-light-icon">
                            <img src="assets/images/dashboard/check-engine.png" alt="Check Engine Light" />
                        </div>
                        <div class="warning-light-info">
                            <div class="warning-light-title">Check Engine Light</div>
                            <div class="warning-light-description">
                                <p>One of the most common lights, but one of the most ambiguous.</p>
                                <p>Sometimes it can be very minor, but if it's flashing, be careful, because that could mean something serious.</p>
                                <p>Either way, you should take your car to your local repair shop to get it checked out.</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Oil Pressure Indicator Light -->
                <div class="warning-light-card critical">
                    <div class="warning-light-content">
                        <div class="warning-light-icon">
                            <img src="assets/images/dashboard/oil-pressure.png" alt="Oil Pressure Indicator Light" />
                        </div>
                        <div class="warning-light-info">
                            <div class="warning-light-title">Oil Pressure Indicator Light</div>
                            <div class="warning-light-description">
                                <p>This dashboard light means you could be running low on oil, or something is wrong with your oil pump.</p>
                                <p>Either way, you want to check your oil levels and get your car to a shop ASAP, as waiting could cause further issues with your engine leading to costly repairs.</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Seat Belt Reminder -->
                <div class="warning-light-card info">
                    <div class="warning-light-content">
                        <div class="warning-light-icon">
                            <img src="assets/images/dashboard/seatbelt.png" alt="Seat Belt Reminder" />
                        </div>
                        <div class="warning-light-info">
                            <div class="warning-light-title">Seat Belt Reminder</div>
                            <div class="warning-light-description">
                                <p>This light indicates that you need to buckle your seat belt. This is a simple reminder that is easy to fix, just buckle your seat belt.</p>
                                <p>Just 36% of people killed in car crashes were wearing a seat belt. It's easy to do and helps ensure you're getting to your destination safely.</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Engine Temperature Light -->
                <div class="warning-light-card warning">
                    <div class="warning-light-content">
                        <div class="warning-light-icon">
                            <img src="assets/images/dashboard/temperature.png" alt="Engine Temperature Light" />
                        </div>
                        <div class="warning-light-info">
                            <div class="warning-light-title">Engine Temperature Light</div>
                            <div class="warning-light-description">
                                <p>One of the most common in the summer, this light lets you know your engine's temperature is too high.</p>
                                <p>This is typically caused by a lack of coolant, but could be caused by other things.</p>
                                <p>Check your coolant level, and keep a close eye on your cars engine temp to make sure you don't do permanent damage.</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Tire Pressure Warning Light -->
                <div class="warning-light-card warning">
                    <div class="warning-light-content">
                        <div class="warning-light-icon">
                            <img src="assets/images/dashboard/tire_pressure.png" alt="Tire Pressure Warning Light" />
                        </div>
                        <div class="warning-light-info">
                            <div class="warning-light-title">Tire Pressure Warning Light</div>
                            <div class="warning-light-description">
                                <p>A more common light to see, the tire pressure warning light indicates that the pressure in one of your tires is too low or too high.</p>
                                <p>Sometimes this is an immediate thing and you have a puncture, but more commonly just means a tire may be slowly leaking air and needs a check.</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Battery Warning Light -->
                <div class="warning-light-card critical">
                    <div class="warning-light-content">
                        <div class="warning-light-icon">
                            <img src="assets/images/dashboard/car-battery.png" alt="Battery Warning Light" />
                        </div>
                        <div class="warning-light-info">
                            <div class="warning-light-title">Battery Warning Light</div>
                            <div class="warning-light-description">
                                <p>This light means that somethings wrong with your car's electrical system. It could be a bad battery or alternator, but could also be a loose wire.</p>
                                <p>The good news is that Nati Automotive's experts can help get you a new one and get you back on the road.</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Low Fuel Warning Light -->
                <div class="warning-light-card info">
                    <div class="warning-light-content">
                        <div class="warning-light-icon">
                            <img src="assets/images/dashboard/low_fuel.png" alt="Low Fuel Warning Light" />
                        </div>
                        <div class="warning-light-info">
                            <div class="warning-light-title">Low Fuel Warning Light</div>
                            <div class="warning-light-description">
                                <p>You've probably seen this light, and you're never too happy to see it pop up, but the good news is that it's one of the easiest to fix on this list!</p>
                                <p>If you run out of gas, Nati Automotive members can get emergency fuel delivery to get you to your destination.</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Brake Warning Light -->
                <div class="warning-light-card critical">
                    <div class="warning-light-content">
                        <div class="warning-light-icon">
                            <img src="assets/images/dashboard/brake-system.png" alt="Brake Warning Light" />
                        </div>
                        <div class="warning-light-info">
                            <div class="warning-light-title">Brake Warning Light</div>
                            <div class="warning-light-description">
                                <p>The brake warning light usually means that the emergency brake is engaged. This is easily rectified by releasing it.</p>
                                <p>If this does not solve the problem, then it's likely a bigger issue with your primary brake system that you want to get fixed ASAP.</p>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

        </section>
        <!-- Dashboard content end -->
        
        <!-- Bottom Navigation -->
        <?php include 'partial-front/bottom_nav.php'; ?>
        
        <!-- Options Menu -->
        <?php include 'option.php' ?>
    </div>

    <!-- Scripts -->
    <script src="assets/js/jquery.min.js"></script>
    <script src="assets/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/custom.js"></script>
</body>
</html>
