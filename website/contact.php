<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Us - Nati Automotive</title>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.css">
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        .page-header {
            padding-top: 160px; /* Account for fixed header (top-bar + main-nav height) */
            padding-bottom: 40px;
            background: linear-gradient(135deg, #000F1D, #001F3D);
            text-align: center;
            margin-bottom: 0;
        }

        .page-header h1 {
            color: #fff;
            font-size: 2.5rem;
            margin-bottom: 15px;
        }

        .page-header p {
            color: rgba(255,255,255,0.8);
            font-size: 1.1rem;
        }

        .contact-info {
            padding: 0;
            background: linear-gradient(135deg, #000F1D, #001F3D);
            position: relative;
            z-index: 1;
        }
        
        .contact-grid {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr 1fr;
            gap: 0;
        }
        
        .contact-card {
            background: transparent;
            border: none;
            padding: 30px 15px;
            text-align: center;
            transition: transform 0.3s ease;
            border-radius: 0;
            position: relative;
            z-index: 2;
        }
        
        .contact-card:hover {
            transform: translateY(-5px);
            box-shadow: none;
            background: rgba(255,255,255,0.03);
        }
        
        .contact-card i {
            font-size: 24px;
            color: #ffbe00;
            margin-bottom: 15px;
        }
        
        .contact-card h3 {
            color: #fff;
            margin-bottom: 10px;
            font-size: 16px;
            font-weight: 500;
        }
        
        .contact-card p {
            color: rgba(255,255,255,0.7);
            line-height: 1.4;
            font-size: 14px;
        }
        
        .contact-form-section {
            padding: 80px 0;
            background: #111;
        }
        
        .contact-wrapper {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 50px;
        }
        
        .form-container {
            background: rgba(255,255,255,0.05);
            border: 1px solid rgba(255,255,255,0.1);
            padding: 40px;
            border-radius: 15px;
        }
        
        .form-container h2 {
            color: #fff;
            margin-bottom: 30px;
            font-size: 2rem;
        }
        
        .form-group {
            margin-bottom: 25px;
        }
        
        .form-group label {
            display: block;
            color: rgba(255,255,255,0.9);
            margin-bottom: 10px;
        }
        
        .form-group input,
        .form-group select,
        .form-group textarea {
            width: 100%;
            padding: 12px 20px;
            background: rgba(255,255,255,0.05);
            border: 1px solid rgba(255,255,255,0.1);
            border-radius: 8px;
            color: #fff;
            transition: all 0.3s ease;
        }
        
        .form-group input:focus,
        .form-group select:focus,
        .form-group textarea:focus {
            border-color: #ffbe00;
            box-shadow: 0 0 0 2px rgba(0,168,255,0.2);
            outline: none;
        }
        
        .cta-button {
            background: linear-gradient(135deg, #ffbe00, #0077ff);
            color: #fff;
            border: none;
            padding: 15px 30px;
            border-radius: 8px;
            cursor: pointer;
            font-size: 1rem;
            font-weight: 600;
            transition: all 0.3s ease;
            width: 100%;
        }
        
        .cta-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(0,168,255,0.2);
        }
        
        .map-container {
            background: rgba(255,255,255,0.05);
            border: 1px solid rgba(255,255,255,0.1);
            padding: 40px;
            border-radius: 15px;
        }
        
        .map-container h2 {
            color: #fff;
            margin-bottom: 30px;
            font-size: 2rem;
        }
        
        .map iframe {
            border-radius: 8px;
            width: 100%;
            height: 450px;
        }
        
        .whatsapp-section {
            background: linear-gradient(135deg, #25D366, #128C7E);
            padding: 60px 0;
            text-align: center;
            color: #fff;
        }
        
        .whatsapp-content i {
            font-size: 4rem;
            margin-bottom: 20px;
        }
        
        .whatsapp-button {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            background: #fff;
            color: #128C7E;
            padding: 15px 30px;
            border-radius: 50px;
            text-decoration: none;
            font-weight: 600;
            margin-top: 20px;
            transition: all 0.3s ease;
        }
        
        .whatsapp-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.1);
        }
        
        @media (max-width: 992px) {
            .contact-wrapper {
                grid-template-columns: 1fr;
            }
        }
        
        @media (max-width: 576px) {
            .contact-grid {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 768px) {
            .page-header {
                padding-top: 100px; /* Adjusted for mobile where top-bar is hidden */
            }
        }
    </style>
</head>
<body class="dark-theme">
    <?php include 'includes/header.php'; ?>
    <?php include 'includes/navbar.php'; ?>

    <section class="page-header" data-aos="fade-up">
        <div class="container">
            <h1>Contact Us</h1>
            <p>We're here to help with all your automotive needs</p>
        </div>
    </section>

    <section class="contact-info" data-aos="fade-up">
        <div class="container">
            <div class="contact-grid">
                <div class="contact-card">
                    <i class="fas fa-map-marker-alt"></i>
                    <h3>Visit Us</h3>
                    <p>Aware, Adwa Bridge<br>Addis Ababa, Ethiopia</p>
                </div>
                <div class="contact-card">
                    <i class="fas fa-phone"></i>
                    <h3>Call Us</h3>
                    <p>+251 91 214 3538</p>
                </div>
                <div class="contact-card">
                    <i class="fas fa-envelope"></i>
                    <h3>Email Us</h3>
                    <p>info@NatiAutomotive.com</p>
                </div>
                <div class="contact-card">
                    <i class="fas fa-clock"></i>
                    <h3>Working Hours</h3>
                    <p>Mon-Sat: 8:00 AM - 6:00 PM<br>Sunday: Closed</p>
                </div>
            </div>
        </div>
    </section>

    <section class="contact-form-section" data-aos="fade-up">
        <div class="container">
            <div class="contact-wrapper">
                <div class="form-container">
                    <h2>Send Us a Message</h2>
                    <form id="contactForm" action="process_contact.php" method="POST">
                        <div class="form-group">
                            <label for="name">Full Name *</label>
                            <input type="text" id="name" name="name" required>
                        </div>
                        <div class="form-group">
                            <label for="email">Email Address *</label>
                            <input type="email" id="email" name="email" required>
                        </div>
                        <div class="form-group">
                            <label for="phone">Phone Number *</label>
                            <input type="tel" id="phone" name="phone" required>
                        </div>
                        <div class="form-group">
                            <label for="service">Service Required *</label>
                            <select id="service" name="service" required>
                                <option value="">Select a Service</option>
                                <option value="bodyworks">Body Works</option>
                                <option value="electrical">Electrical Service</option>
                                <option value="engine">Engine Service</option>
                                <option value="maintenance">General Maintenance</option>
                                <option value="diagnostics">Vehicle Diagnostics</option>
                                <option value="other">Other Services</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="message">Your Message *</label>
                            <textarea id="message" name="message" rows="5" required></textarea>
                        </div>
                        <button type="submit" class="cta-button">
                            <i class="fas fa-paper-plane"></i> Send Message
                        </button>
                    </form>
                </div>
                <div class="map-container">
                    <h2>Our Location</h2>
                    <div class="map">
                        <iframe
                            src="https://www.google.com/maps/embed?pb=!1m17!1m12!1m3!1d3940.5721666792584!2d38.74661937497555!3d9.015775091744561!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m2!1m1!2zOcKwMDAnNTYuOCJOIDM4wrA0NCc1NS4yIkU!5e0!3m2!1sen!2sus!4v1710644547044!5m2!1sen!2sus"
                            width="100%"
                            height="450"
                            style="border:0;"
                            allowfullscreen=""
                            loading="lazy"
                            referrerpolicy="no-referrer-when-downgrade">
                        </iframe>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="whatsapp-section" data-aos="fade-up">
        <div class="container">
            <div class="whatsapp-content">
                <i class="fab fa-whatsapp"></i>
                <h2>Quick Support on WhatsApp</h2>
                <p>Get instant responses to your queries through WhatsApp</p>
                <a href="https://wa.me/251912143538" class="whatsapp-button" target="_blank">
                    <i class="fab fa-whatsapp"></i> Chat with Us
                </a>
            </div>
        </div>
    </section>

    <?php include 'includes/footer.php'; ?>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/js/all.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.js"></script>
    <script>
        // Initialize AOS
        AOS.init({
            duration: 1000,
            once: true
        });

        // Form validation
        document.getElementById('contactForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            // Basic validation
            const name = document.getElementById('name').value;
            const email = document.getElementById('email').value;
            const phone = document.getElementById('phone').value;
            const message = document.getElementById('message').value;
            
            if (name && email && phone && message) {
                // Here you would typically send the form data to your server
                alert('Thank you for your message. We will get back to you soon!');
                this.reset();
            } else {
                alert('Please fill in all required fields.');
            }
        });
    </script>
</body>
</html> 