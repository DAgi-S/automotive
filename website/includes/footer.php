<footer class="main-footer">
    <div class="footer-top">
        <div class="container">
            <div class="footer-content">
                <div class="footer-section about-section">
                    <div class="footer-logo">
                        <span class="logo-text"><span class="highlight">Nati</span>Automotive</span>
                    </div>
                    <p>Nati Automotive is your trusted partner for all automotive needs. We provide professional services with state-of-the-art equipment and experienced technicians.</p>
                    <div class="social-links">
                        <a href="#" class="social-link" aria-label="Facebook">
                            <i class="fab fa-facebook-f"></i>
                        </a>
                        <a href="#" class="social-link" aria-label="Instagram">
                            <i class="fab fa-instagram"></i>
                        </a>
                        <a href="#" class="social-link" aria-label="Twitter">
                            <i class="fab fa-twitter"></i>
                        </a>
                    </div>
                </div>
                <div class="footer-section">
                    <h3>Quick Links</h3>
                    <ul class="footer-links">
                        <li><a href="index.php">Home</a></li>
                        <li><a href="services.php">Services</a></li>
                        <li><a href="about.php">About Us</a></li>
                        <li><a href="blogs.php">Blog</a></li>
                        <li><a href="contact.php">Contact</a></li>
                    </ul>
                </div>
                <div class="footer-section">
                    <h3>Contact Info</h3>
                    <ul class="footer-contact-info">
                        <li>
                            <i class="fas fa-map-marker-alt"></i>
                            <span>Aware, Adwa Bridge<br>Addis Ababa, Ethiopia</span>
                        </li>
                        <li>
                            <i class="fas fa-phone"></i>
                            <span>+251 91 214 3538</span>
                        </li>
                        <li>
                            <i class="fas fa-envelope"></i>
                            <span>info@NatiAutomotive.com</span>
                        </li>
                    </ul>
                </div>
               
            </div>
        </div>
    </div>
    <div class="footer-bottom">
        <div class="container">
            <p>&copy; <?php echo date('Y'); ?> Nati Automotive. All rights reserved. Developed by Lebawi Net Trading plc </p>
        </div>
    </div>
</footer>

<style>
.main-footer {
    background: linear-gradient(135deg, #000F1D, #001F3D);
    color: #fff;
    position: relative;
    margin-top: 80px;
}

.footer-top {
    padding: 80px 0 40px;
}

.footer-content {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 40px;
}

.footer-section {
    position: relative;
}

.footer-logo {
    margin-bottom: 25px;
}

.logo-text {
    font-size: 24px;
    font-weight: 700;
}

.logo-text .highlight {
    color: #ffbe00;
}

.about-section p {
    color: rgba(255, 255, 255, 0.7);
    line-height: 1.6;
    margin-bottom: 25px;
}

.footer-section h3 {
    color: #fff;
    font-size: 18px;
    margin-bottom: 25px;
    font-weight: 600;
    position: relative;
    padding-bottom: 15px;
}

.footer-section h3::after {
    content: '';
    position: absolute;
    left: 0;
    bottom: 0;
    width: 30px;
    height: 2px;
    background: #ffbe00;
}

.footer-links {
    list-style: none;
    padding: 0;
    margin: 0;
}

.footer-links li {
    margin-bottom: 12px;
}

.footer-links a {
    color: rgba(255, 255, 255, 0.7);
    text-decoration: none;
    transition: all 0.3s ease;
    display: inline-block;
}

.footer-links a:hover {
    color: #ffbe00;
    transform: translateX(5px);
}

.footer-contact-info {
    list-style: none;
    padding: 0;
    margin: 0;
}

.footer-contact-info li {
    display: block;
    align-items: flex-start;
    margin-bottom: 20px;
    color: rgba(255, 255, 255, 0.7);
}

.footer-contact-info i {
    color: #ffbe00;
    margin-right: 15px;
    margin-top: 5px;
}

.working-hours {
    list-style: none;
    padding: 0;
    margin: 0;
}

.working-hours li {
    margin-bottom: 15px;
    color: rgba(255, 255, 255, 0.7);
}

.working-hours .day {
    display: block;
    margin-bottom: 5px;
    color: #fff;
}

.social-links {
    display: flex;
    gap: 15px;
}

.social-link {
    width: 35px;
    height: 35px;
    background: rgba(255, 255, 255, 0.1);
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 50%;
    color: #fff;
    text-decoration: none;
    transition: all 0.3s ease;
}

.social-link:hover {
    background: #ffbe00;
    color: #000;
    transform: translateY(-3px);
}

.footer-bottom {
    background: rgba(0, 0, 0, 0.2);
    padding: 20px 0;
    text-align: center;
}

.footer-bottom p {
    color: rgba(255, 255, 255, 0.7);
    margin: 0;
}

@media (max-width: 992px) {
    .footer-content {
        grid-template-columns: repeat(2, 1fr);
    }
}

@media (max-width: 576px) {
    .footer-content {
        grid-template-columns: 1fr;
    }
    
    .footer-section {
        text-align: center;
    }
    
    .footer-section h3::after {
        left: 50%;
        transform: translateX(-50%);
    }
    
    .contact-info li {
        justify-content: center;
    }
    
    .social-links {
        justify-content: center;
    }
}
</style> 