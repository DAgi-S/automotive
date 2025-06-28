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

<head>
    <title>Our Location - Nati Automotive</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta charset="UTF-8">
    <meta name="description" content="Visit Nati Automotive - conveniently located garage for all your automotive service needs">
    <link rel="icon" href="assets/images/favicon/Nati-logo.png" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/css/bottom-nav-universal.css">
    <link rel="stylesheet" href="assets/css/new-home-layout.css">
    <link rel="stylesheet" href="assets/css/mobile-header.css">
    
    <style>
        /* Mobile Header Styles for Location */
        .mobile-header-location {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            height: 60px;
            background: linear-gradient(135deg, #27ae60 0%, #229954 100%);
            color: white;
            z-index: 1000;
            box-shadow: 0 2px 10px rgba(39, 174, 96, 0.3);
        }
        
        .header-container {
            display: flex;
            align-items: center;
            justify-content: space-between;
            height: 100%;
            padding: 0 1rem;
            max-width: 1200px;
            margin: 0 auto;
        }
        
        .header-left {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            flex: 1;
        }
        
        .back-btn,
        .notifications-btn,
        .menu-btn {
            background: none;
            border: none;
            color: white;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s ease;
            position: relative;
        }
        
        .back-btn:hover,
        .notifications-btn:hover,
        .menu-btn:hover {
            background: rgba(255, 255, 255, 0.2);
            transform: scale(1.1);
        }
        
        .header-title h1 {
            font-size: 1.2rem;
            font-weight: 700;
            margin: 0;
            color: white;
        }
        
        .header-right {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .notification-badge {
            position: absolute;
            top: 8px;
            right: 8px;
            background: #ff4757;
            color: white;
            border-radius: 50%;
            width: 16px;
            height: 16px;
            font-size: 0.6rem;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
        }
        
        /* Enhanced Bottom Navigation Fixes */
        #bottom-navigation {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            background: white;
            border-top: 1px solid rgba(0, 0, 0, 0.1);
            box-shadow: 0 -2px 10px rgba(0, 0, 0, 0.1);
            z-index: 999;
            height: 70px;
        }
        
        .home-navigation-menu {
            height: 100%;
        }
        
        .bottom-panel {
            height: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .bootom-tabbar {
            display: flex;
            list-style: none;
            margin: 0;
            padding: 0;
            width: 100%;
            max-width: 400px;
            justify-content: space-around;
            align-items: center;
            height: 100%;
        }
        
        .bootom-tabbar li {
            position: relative;
            flex: 1;
            height: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .bootom-tabbar li a {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 50px;
            height: 50px;
            border-radius: 50%;
            transition: all 0.3s ease;
            text-decoration: none;
            position: relative;
        }
        
        .bootom-tabbar li a svg {
            width: 24px;
            height: 24px;
            transition: all 0.3s ease;
        }
        
        .bootom-tabbar li a svg path {
            stroke: #6c757d;
            transition: all 0.3s ease;
        }
        
        .bootom-tabbar li.active a {
            background: linear-gradient(135deg, #27ae60 0%, #229954 100%);
            box-shadow: 0 4px 12px rgba(39, 174, 96, 0.3);
            transform: translateY(-2px);
        }
        
        .bootom-tabbar li.active a svg path {
            stroke: white;
        }
        
        .bootom-tabbar li a:hover {
            background: rgba(39, 174, 96, 0.1);
            transform: translateY(-1px);
        }
        
        .orange-boder,
        .orange-border {
            display: none;
        }
        
        /* Location-specific enhancements using home CSS architecture */
        .location-content {
            padding-top: 60px;
            padding-bottom: 90px;
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            min-height: 100vh;
        }
        
        /* Compact Hero Section */
        .location-hero {
            background: linear-gradient(135deg, #27ae60 0%, #229954 100%);
            color: white;
            padding: 1.5rem 1rem;
            margin-bottom: 1.5rem;
            border-radius: 1rem;
            text-align: center;
            position: relative;
            overflow: hidden;
            box-shadow: 0 4px 15px rgba(39, 174, 96, 0.3);
        }
        
        .location-hero::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, #3498db 0%, #2980b9 50%, #3498db 100%);
        }
        
        .location-hero h3 {
            font-size: 1.4rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
        }
        
        .location-hero h3::before {
            content: '📍';
            font-size: 1.2rem;
        }
        
        .location-hero p {
            font-size: 0.9rem;
            opacity: 0.9;
            margin-bottom: 0.25rem;
            line-height: 1.4;
        }
        
        /* Compact Grid Layout System */
        .location-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 1rem;
            margin-bottom: 1.5rem;
        }
        
        .location-grid.two-column {
            grid-template-columns: repeat(2, 1fr);
        }
        
        .grid-item {
            background: white;
            border-radius: 0.75rem;
            overflow: hidden;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
            transition: all 0.3s ease;
            border: 1px solid #e9ecef;
            position: relative;
        }
        
        .grid-item:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
        }
        
        .grid-item.span-2 {
            grid-column: span 2;
        }
        
        .grid-item.span-3 {
            grid-column: span 3;
        }
        
        .grid-item.span-4 {
            grid-column: span 4;
        }
        
        /* Compact Card Styles */
        .compact-card {
            background: white;
            border-radius: 0.75rem;
            padding: 1rem;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
            transition: all 0.3s ease;
            border: 1px solid #e9ecef;
            position: relative;
            height: 100%;
            display: flex;
            flex-direction: column;
        }
        
        .compact-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 3px;
            background: linear-gradient(90deg, #27ae60 0%, #229954 100%);
            border-radius: 0.75rem 0.75rem 0 0;
        }
        
        .compact-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
        }
        
        .compact-card.contact-card::before {
            background: linear-gradient(90deg, #3498db 0%, #2980b9 100%);
        }
        
        .compact-card.social-card::before {
            background: linear-gradient(90deg, #e74c3c 0%, #c0392b 100%);
        }
        
        .compact-card.map-card::before {
            background: linear-gradient(90deg, #f39c12 0%, #e67e22 100%);
        }
        
        /* Compact Map Container */
        .compact-map {
            position: relative;
            border-radius: 0.75rem;
            overflow: hidden;
            background: white;
        }
        
        .compact-map iframe {
            width: 100%;
            height: 200px;
            border: none;
            transition: all 0.3s ease;
        }
        
        .compact-map:hover iframe {
            transform: scale(1.01);
        }
        
        /* Compact Contact Item */
        .compact-contact-item {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.75rem 0;
            border-bottom: 1px solid #f8f9fa;
            transition: all 0.3s ease;
            cursor: pointer;
        }
        
        .compact-contact-item:last-child {
            border-bottom: none;
        }
        
        .compact-contact-item:hover {
            background: #f8f9fa;
            padding-left: 0.5rem;
            border-radius: 0.5rem;
        }
        
        .compact-icon {
            width: 35px;
            height: 35px;
            border-radius: 50%;
            background: linear-gradient(135deg, #3498db, #2980b9);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 0.9rem;
            flex-shrink: 0;
            box-shadow: 0 3px 10px rgba(52, 152, 219, 0.3);
        }
        
        .compact-details {
            flex: 1;
            min-width: 0;
        }
        
        .compact-label {
            font-weight: 600;
            color: #2c3e50;
            font-size: 0.85rem;
            margin-bottom: 0.125rem;
        }
        
        .compact-value {
            color: #6c757d;
            font-size: 0.8rem;
            word-break: break-word;
        }
        
        /* Compact Social Media */
        .compact-social {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.75rem;
            background: #f8f9fa;
            border-radius: 0.5rem;
            text-decoration: none;
            transition: all 0.3s ease;
            border: 2px solid transparent;
        }
        
        .compact-social:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            text-decoration: none;
        }
        
        .compact-social.facebook:hover {
            background: #1877f2;
            color: white;
            border-color: #1877f2;
        }
        
        .compact-social.instagram:hover {
            background: linear-gradient(135deg, #e4405f, #833ab4);
            color: white;
            border-color: #e4405f;
        }
        
        .compact-social.tiktok:hover {
            background: #000000;
            color: white;
            border-color: #000000;
        }
        
        .compact-social-icon {
            width: 30px;
            height: 30px;
            border-radius: 50%;
            background: white;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }
        
        .compact-social-text {
            font-weight: 600;
            font-size: 0.9rem;
            color: inherit;
        }
        
        /* Contact Card Content */
        .contact-card-content {
            padding: 1rem 0;
            text-align: center;
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }
        
        .contact-value-large {
            font-size: 1rem;
            font-weight: 700;
            color: #2c3e50;
            margin-bottom: 0.25rem;
            line-height: 1.2;
        }
        
        .contact-value-small {
            font-size: 0.8rem;
            color: #6c757d;
            font-weight: 500;
        }
        
        /* Social Card Content */
        .social-card-content {
            padding: 1rem 0;
            text-align: center;
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }
        
        .social-link {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 0.5rem;
            text-decoration: none;
            color: inherit;
            transition: all 0.3s ease;
        }
        
        .social-link:hover {
            transform: translateY(-2px);
            text-decoration: none;
            color: inherit;
        }
        
        .social-icon-large {
            width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            background: rgba(0, 0, 0, 0.05);
            transition: all 0.3s ease;
        }
        
        .social-link:hover .social-icon-large {
            background: rgba(0, 0, 0, 0.1);
            transform: scale(1.1);
        }
        
        .social-text {
            font-size: 0.85rem;
            font-weight: 600;
            color: #2c3e50;
        }
        
        /* Card Headers */
        .card-header {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            margin-bottom: 1rem;
            padding-bottom: 0.5rem;
            border-bottom: 2px solid #f8f9fa;
        }
        
        .card-title {
            font-size: 1rem;
            font-weight: 700;
            color: #2c3e50;
            margin: 0;
        }
        
        .card-icon {
            font-size: 1.1rem;
            color: #27ae60;
        }
        

        
        /* Mobile Responsive */
        @media (max-width: 768px) {
            .mobile-header-location {
                height: 50px;
            }
            
            .location-content {
                padding-top: 50px;
            }
            
            .header-container {
                padding: 0 0.75rem;
            }
            
            .header-title h1 {
                font-size: 1.1rem;
            }
            
            .back-btn,
            .notifications-btn,
            .menu-btn {
                width: 35px;
                height: 35px;
            }
            
            #bottom-navigation {
                height: 60px;
            }
            
            .bootom-tabbar li a {
                width: 45px;
                height: 45px;
            }
            
            .bootom-tabbar li a svg {
                width: 20px;
                height: 20px;
            }
            
            /* Compact Mobile Layout */
            .location-grid {
                grid-template-columns: repeat(2, 1fr);
                gap: 0.75rem;
                margin-bottom: 1rem;
            }
            
            .location-grid.two-column {
                grid-template-columns: 1fr;
            }
            
            
            .location-hero {
                padding: 1rem;
                margin-bottom: 1rem;
            }
            
            .location-hero h3 {
                font-size: 1.2rem;
                flex-direction: column;
                gap: 0.25rem;
            }
            
            .location-hero p {
                font-size: 0.85rem;
            }
            
            .compact-card {
                padding: 0.75rem;
            }
            
            .compact-map iframe {
                height: 180px;
            }
            
            .compact-contact-item {
                flex-direction: column;
                text-align: center;
                gap: 0.5rem;
                padding: 0.75rem 0;
            }
            
            .compact-icon {
                width: 40px;
                height: 40px;
                font-size: 1rem;
            }
            
            .compact-social {
                justify-content: center;
                padding: 0.6rem;
            }
            
            .card-header {
                margin-bottom: 0.75rem;
            }
            
            .card-title {
                font-size: 0.9rem;
            }
        }
        
        @media (max-width: 576px) {
            .location-grid {
                grid-template-columns: 1fr;
                gap: 0.5rem;
            }
            
            .location-hero {
                padding: 0.75rem;
            }
            
            .location-hero h3 {
                font-size: 1.1rem;
            }
            
            .location-hero p {
                font-size: 0.8rem;
            }
            
            .compact-card {
                padding: 0.5rem;
            }
            
            .compact-map iframe {
                height: 160px;
            }
            
            .compact-icon {
                width: 35px;
                height: 35px;
                font-size: 0.9rem;
            }
            
            .compact-social {
                padding: 0.5rem;
            }
            
            .compact-social-icon {
                width: 25px;
                height: 25px;
            }
            
            .compact-social-text {
                font-size: 0.8rem;
            }
            
            .card-title {
                font-size: 0.85rem;
            }
        }
        
        /* Animation */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .location-card {
            animation: fadeInUp 0.6s ease-out;
        }
        
        .contact-info-card {
            animation: fadeInUp 0.8s ease-out;
        }
        
        .social-media-section {
            animation: fadeInUp 1s ease-out;
        }
        
        .cta-section {
            animation: fadeInUp 1.2s ease-out;
        }
    </style>
</head>

<body>
    <div class="site-content location-content">
        <!-- Mobile Header Navigation -->
        <header class="mobile-header-location">
          <div class="header-container">
            <div class="header-left">
              <button class="back-btn" onclick="history.back()">
                <i class="fas fa-arrow-left"></i>
              </button>
              <div class="header-title">
                <h1>Our Location</h1>
              </div>
            </div>
            <div class="header-right">
              <button class="notifications-btn" onclick="toggleNotifications()">
                <i class="fas fa-bell"></i>
                <span class="notification-badge">1</span>
              </button>
              <button class="menu-btn" onclick="toggleMenu()">
                <i class="fas fa-ellipsis-v"></i>
              </button>
            </div>
          </div>
        </header>
        
        <!-- Enhanced Compact Location Content -->
        <section id="location" class="container-fluid">
            
            <!-- Compact Hero Section -->
            <div class="location-hero">
                <h3>Our Location</h3>
                <p>Conveniently located garage for professional automotive service</p>
            </div>

            <!-- 4-Column Grid Layout -->
            <div class="location-grid">
                
                <!-- Map Section (Span 2 columns) -->
                <div class="grid-item span-2 compact-card map-card">
                    <div class="card-header">
                        <i class="card-icon fas fa-map-marked-alt"></i>
                        <h4 class="card-title">Find Us Here</h4>
                    </div>
                    <div class="compact-map">
                        <iframe 
                            src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3936.239912401058!2d38.75941167486615!3d9.034430191616842!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x164b857d6cb0f8a1%3A0xfbeec6a7bbf42148!2s2QFH%2B3FP%2C%20Addis%20Ababa%2C%20Ethiopia!5e0!3m2!1sen!2sus!4v1597072240941!5m2!1sen!2sus" 
                            allowfullscreen="" 
                            loading="lazy" 
                            referrerpolicy="no-referrer-when-downgrade"
                            title="Nati Automotive Location">
                        </iframe>
                    </div>
                </div>

                <!-- Contact Information (Individual columns) -->
                <div class="grid-item compact-card contact-card">
                    <div class="card-header">
                        <i class="card-icon fas fa-map-marker-alt"></i>
                        <h4 class="card-title">Address</h4>
                    </div>
                    <div class="contact-card-content">
                        <div class="contact-value-large">Aware, Addis Ababa</div>
                        <div class="contact-value-small">Ethiopia</div>
                    </div>
                </div>
                
                <div class="grid-item compact-card contact-card">
                    <div class="card-header">
                        <i class="card-icon fas fa-phone"></i>
                        <h4 class="card-title">Phone</h4>
                    </div>
                    <div class="contact-card-content">
                        <div class="contact-value-large">+251-912-2424</div>
                        <div class="contact-value-small">Call anytime</div>
                    </div>
                </div>
                
                
                <div class="grid-item compact-card contact-card">
                    <div class="card-header">
                        <i class="card-icon fas fa-envelope"></i>
                        <h4 class="card-title">Email</h4>
                    </div>
                    <div class="contact-card-content">
                        <div class="contact-value-large">info@natiauto.com</div>
                        <div class="contact-value-small">Get in touch</div>
                    </div>
                </div>
                
                <div class="grid-item compact-card contact-card">
                    <div class="card-header">
                        <i class="card-icon fas fa-building"></i>
                        <h4 class="card-title">Company</h4>
                    </div>
                    <div class="contact-card-content">
                        <div class="contact-value-large">Nati Automotive</div>
                        <div class="contact-value-small">Professional Service</div>
                    </div>
                </div>
                
            </div>

            <!-- Social Media Grid (3-Column) -->
            <div class="location-grid" style="grid-template-columns: repeat(3, 1fr);">
                
                <!-- Facebook -->
                <div class="grid-item compact-card social-card">
                    <div class="card-header">
                        <i class="card-icon fab fa-facebook-f" style="color: #1877f2;"></i>
                        <h4 class="card-title">Facebook</h4>
                    </div>
                    <div class="social-card-content">
                        <a href="https://www.facebook.com/" target="_blank" class="social-link facebook">
                            <div class="social-icon-large">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="#1877f2" viewBox="0 0 16 16">
                                    <path d="M16 8.049c0-4.446-3.582-8.05-8-8.05C3.58 0-.002 3.603-.002 8.05c0 4.017 2.926 7.347 6.75 7.951v-5.625h-2.03V8.05H6.75V6.275c0-2.017 1.195-3.131 3.022-3.131.876 0 1.791.157 1.791.157v1.98h-1.009c-.993 0-1.303.621-1.303 1.258v1.51h2.218l-.354 2.326H9.25V16c3.824-.604 6.75-3.934 6.75-7.951" />
                                </svg>
                            </div>
                            <span class="social-text">Follow Us</span>
                        </a>
                    </div>
                </div>
                
                <!-- Instagram -->
                <div class="grid-item compact-card social-card">
                    <div class="card-header">
                        <i class="card-icon fab fa-instagram" style="color: #e4405f;"></i>
                        <h4 class="card-title">Instagram</h4>
                    </div>
                    <div class="social-card-content">
                        <a href="https://www.instagram.com/" target="_blank" class="social-link instagram">
                            <div class="social-icon-large">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="#e4405f" viewBox="0 0 16 16">
                                    <path d="M8 0C5.829 0 5.556.01 4.703.048 3.85.088 3.269.222 2.76.42a3.9 3.9 0 0 0-1.417.923A3.9 3.9 0 0 0 .42 2.76C.222 3.268.087 3.85.048 4.7.01 5.555 0 5.827 0 8.001c0 2.172.01 2.444.048 3.297.04.852.174 1.433.372 1.942.205.526.478.972.923 1.417.444.445.89.719 1.416.923.51.198 1.09.333 1.942.372C5.555 15.99 5.827 16 8 16s2.444-.01 3.298-.048c.851-.04 1.434-.174 1.943-.372a3.9 3.9 0 0 0 1.416-.923c.445-.445.718-.891.923-1.417.197-.509.332-1.09.372-1.942C15.99 10.445 16 10.173 16 8s-.01-2.445-.048-3.299c-.04-.851-.175-1.433-.372-1.941a3.9 3.9 0 0 0-.923-1.417A3.9 3.9 0 0 0 13.24.42c-.51-.198-1.092-.333-1.943-.372C10.443.01 10.172 0 7.998 0zm-.717 1.442h.718c2.136 0 2.389.007 3.232.046.78.035 1.204.166 1.486.275.373.145.64.319.92.599s.453.546.598.92c.11.281.24.705.275 1.485.039.843.047 1.096.047 3.231s-.008 2.389-.047 3.232c-.035.78-.166 1.203-.275 1.485a2.5 2.5 0 0 1-.599.919c-.28.28-.546.453-.92.598-.28.11-.704.24-1.485.276-.843.038-1.096.047-3.232.047s-2.39-.009-3.233-.047c-.78-.036-1.203-.166-1.485-.276a2.5 2.5 0 0 1-.92-.598 2.5 2.5 0 0 1-.6-.92c-.109-.281-.24-.705-.275-1.485-.038-.843-.046-1.096-.046-3.233s.008-2.388.046-3.231c.036-.78.166-1.204.276-1.486.145-.373.319-.64.599-.92s.546-.453.92-.598c.282-.11.705-.24 1.485-.276.738-.034 1.024-.044 2.515-.045zm4.988 1.328a.96.96 0 1 0 0 1.92.96.96 0 0 0 0-1.92m-4.27 1.122a4.109 4.109 0 1 0 0 8.217 4.109 4.109 0 0 0 0-8.217m0 1.441a2.667 2.667 0 1 1 0 5.334 2.667 2.667 0 0 1 0-5.334" />
                                </svg>
                            </div>
                            <span class="social-text">Follow Us</span>
                        </a>
                    </div>
                </div>
                
                <!-- TikTok -->
                <div class="grid-item compact-card social-card">
                    <div class="card-header">
                        <i class="card-icon fab fa-tiktok" style="color: #000000;"></i>
                        <h4 class="card-title">TikTok</h4>
                    </div>
                    <div class="social-card-content">
                        <a href="https://www.tiktok.com/" target="_blank" class="social-link tiktok">
                            <div class="social-icon-large">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="#000000" viewBox="0 0 16 16">
                                    <path d="M9 0h1.98c.144.715.54 1.617 1.235 2.512C12.895 3.389 13.797 4 15 4v2c-1.753 0-3.07-.814-4-1.829V11a5 5 0 1 1-5-5v2a3 3 0 1 0 3 3z" />
                                </svg>
                            </div>
                            <span class="social-text">Follow Us</span>
                        </a>
                    </div>
                </div>
                
            </div>

            <!-- Call to Action Section (Full Width) -->
            <div class="location-grid" style="grid-template-columns: 1fr;">
                <div class="grid-item compact-card" style="background: linear-gradient(135deg, #27ae60 0%, #229954 100%); color: white;">
                    <div class="card-header" style="border-bottom: 2px solid rgba(255,255,255,0.2);">
                        <i class="card-icon fas fa-handshake" style="color: white;"></i>
                        <h4 class="card-title" style="color: white;">Visit Us Today</h4>
                    </div>
                    <p style="color: rgba(255,255,255,0.9); font-size: 0.9rem; margin-bottom: 1rem; line-height: 1.4;">
                        Experience professional automotive service at our conveniently located garage.
                    </p>
                    <div style="display: flex; gap: 1rem; flex-wrap: wrap;">
                        <a href="service.php" style="background: white; color: #27ae60; border: none; border-radius: 0.5rem; padding: 0.75rem 1.5rem; font-weight: 600; text-decoration: none; display: flex; align-items: center; gap: 0.5rem; transition: all 0.3s ease; box-shadow: 0 3px 10px rgba(0,0,0,0.2); flex: 1; justify-content: center;">
                            <i class="fas fa-tools"></i>
                            <span>Book Service</span>
                        </a>
                        <a href="tel:+251912424" style="background: rgba(255,255,255,0.2); color: white; border: 2px solid white; border-radius: 0.5rem; padding: 0.75rem 1.5rem; font-weight: 600; text-decoration: none; display: flex; align-items: center; gap: 0.5rem; transition: all 0.3s ease; flex: 1; justify-content: center;">
                            <i class="fas fa-phone"></i>
                            <span>Call Now</span>
                        </a>
                    </div>
                </div>
            </div>

        </section>
        
        <!-- Bottom Navigation -->
        <?php include 'partial-front/bottom_nav.php'; ?>
        
        <!-- Options Menu -->
        <?php include 'option.php' ?>
    </div>

    <!-- Scripts -->
    <script src="assets/js/jquery.min.js"></script>
    <script src="assets/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/custom.js"></script>
    
    <script>
        // Enhanced location page interactions
        document.addEventListener('DOMContentLoaded', function() {
            // Add smooth scroll animations
            const cards = document.querySelectorAll('.location-card, .contact-info-card, .social-media-section, .cta-section');
            
            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.style.opacity = '1';
                        entry.target.style.transform = 'translateY(0)';
                    }
                });
            }, { threshold: 0.1 });
            
            cards.forEach(card => {
                card.style.opacity = '0';
                card.style.transform = 'translateY(30px)';
                card.style.transition = 'all 0.6s ease';
                observer.observe(card);
            });
            
            // Enhanced contact item interactions
            const contactItems = document.querySelectorAll('.contact-item');
            contactItems.forEach(item => {
                item.addEventListener('click', function() {
                    const label = this.querySelector('.contact-label').textContent;
                    const value = this.querySelector('.contact-value').textContent;
                    
                    if (label === 'Phone') {
                        window.location.href = `tel:${value}`;
                    } else if (label === 'Email') {
                        window.location.href = `mailto:${value}`;
                    } else if (label === 'Address') {
                        // Open in maps app
                        const mapUrl = `https://www.google.com/maps/search/?api=1&query=${encodeURIComponent(value)}`;
                        window.open(mapUrl, '_blank');
                    }
                });
            });
            
            // Social media click tracking
            const socialLinks = document.querySelectorAll('.social-icon');
            socialLinks.forEach(link => {
                link.addEventListener('click', function(e) {
                    const platform = this.querySelector('.social-txt').textContent;
                    console.log(`Social media click: ${platform}`);
                    
                    // Add loading state
                    const originalContent = this.innerHTML;
                    this.style.opacity = '0.7';
                    
                    setTimeout(() => {
                        this.style.opacity = '1';
                    }, 500);
                });
            });
            
            // Map interaction enhancement
            const mapContainer = document.querySelector('.map-container');
            if (mapContainer) {
                mapContainer.addEventListener('click', function() {
                    const mapUrl = 'https://www.google.com/maps/search/?api=1&query=Aware,+Addis+Ababa,+Ethiopia';
                    window.open(mapUrl, '_blank');
                });
                
                // Add cursor pointer
                mapContainer.style.cursor = 'pointer';
                
                // Add tooltip
                mapContainer.title = 'Click to open in Google Maps';
            }
            
            // CTA button enhancement
            const ctaButton = document.querySelector('.cta-button');
            if (ctaButton) {
                ctaButton.addEventListener('click', function(e) {
                    // Add loading animation
                    const icon = this.querySelector('i');
                    icon.className = 'fas fa-spinner fa-spin';
                    
                    setTimeout(() => {
                        icon.className = 'fas fa-tools';
                    }, 1000);
                });
            }
            
            // Touch-friendly interactions for mobile
            if ('ontouchstart' in window) {
                contactItems.forEach(item => {
                    item.addEventListener('touchstart', function() {
                        this.style.transform = 'scale(0.98)';
                    });
                    
                    item.addEventListener('touchend', function() {
                        this.style.transform = 'scale(1)';
                    });
                });
                
                socialLinks.forEach(link => {
                    link.addEventListener('touchstart', function() {
                        this.style.transform = 'translateY(-2px) scale(0.98)';
                    });
                    
                    link.addEventListener('touchend', function() {
                        this.style.transform = 'translateY(-3px) scale(1)';
                    });
                });
            }
        });
        
        // Header functionality for Location page
        function toggleNotifications() {
            console.log('Notifications toggled');
            
            const notificationBadge = document.querySelector('.notification-badge');
            if (notificationBadge) {
                notificationBadge.style.display = 'none';
                setTimeout(() => {
                    notificationBadge.style.display = 'flex';
                }, 3000);
            }
        }
        
        function toggleMenu() {
            console.log('Menu toggled');
            
            const options = [
                { icon: 'fas fa-phone', text: 'Call Us', action: () => window.location.href = 'tel:+251912424' },
                { icon: 'fab fa-whatsapp', text: 'WhatsApp', action: () => window.open('https://wa.me/251912424', '_blank') },
                { icon: 'fas fa-calendar-check', text: 'Book Service', action: () => window.location.href = 'service.php' },
                { icon: 'fas fa-home', text: 'Home', action: () => window.location.href = 'home.php' },
                { icon: 'fas fa-user', text: 'Profile', action: () => window.location.href = 'profile.php' }
            ];
            
            let menuHtml = '<div style="position:fixed;top:60px;right:10px;background:white;border-radius:8px;box-shadow:0 4px 12px rgba(0,0,0,0.15);z-index:1001;min-width:150px;">';
            options.forEach(option => {
                menuHtml += `<div style="padding:12px 16px;border-bottom:1px solid #f0f0f0;cursor:pointer;display:flex;align-items:center;gap:8px;" onclick="${option.action.toString().replace('() => ', '')}">
                    <i class="${option.icon}" style="color:#27ae60;width:16px;"></i>
                    <span style="color:#333;font-size:14px;">${option.text}</span>
                </div>`;
            });
            menuHtml += '</div>';
            
            const existingMenu = document.querySelector('#temp-menu');
            if (existingMenu) {
                existingMenu.remove();
            } else {
                const menu = document.createElement('div');
                menu.id = 'temp-menu';
                menu.innerHTML = menuHtml;
                document.body.appendChild(menu);
                
                setTimeout(() => {
                    const menuToRemove = document.querySelector('#temp-menu');
                    if (menuToRemove) menuToRemove.remove();
                }, 5000);
            }
        }
    </script>
</body>
</html>