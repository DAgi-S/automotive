<?php
$current_page = basename($_SERVER['PHP_SELF']);
?>
<header class="main-header">
    <!-- Top Bar -->
    <div class="top-bar">
        <div class="container">
            <div class="contact-info">
                <span class="info-item">
                    <i class="fas fa-phone pulse"></i>
                    <a href="tel:+251912143538">+251912143538</a>
                </span>
                <span class="info-item">
                    <i class="fas fa-envelope pulse"></i>
                    <a href="mailto:info@natiautomotive.com">info@natiautomotive.com</a>
                </span>
                <span class="info-item">
                    <i class="fas fa-clock pulse"></i>
                    <span>Mon-Sat: 8:00 AM - 6:00 PM</span>
                </span>
            </div>
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
    </div>

    <!-- Main Navigation -->
    <nav class="main-nav">
        <div class="container nav-container">
            <a href="index.php" class="logo">
                <i class="fas fa-car-mechanic"></i>
                <span>Nati</span>Automotive
            </a>
            <div class="nav-toggle" id="navToggle" aria-label="Open navigation" tabindex="0">
                <span></span>
                <span></span>
                <span></span>
            </div>
            <ul class="nav-links" id="navLinks">
                <li><a href="index.php" class="<?php echo $current_page == 'index.php' ? 'active' : ''; ?>">Home</a></li>
                <li><a href="about.php" class="<?php echo $current_page == 'about.php' ? 'active' : ''; ?>">About</a></li>
                <li><a href="services.php" class="<?php echo $current_page == 'services.php' ? 'active' : ''; ?>">Services</a></li>
                <li><a href="blogs.php" class="<?php echo $current_page == 'blogs.php' ? 'active' : ''; ?>">Blog</a></li>
                <li><a href="contact.php" class="<?php echo $current_page == 'contact.php' ? 'active' : ''; ?>">Contact</a></li>
                <li class="cta-button">
                    <a href="contact.php" class="book-now">Book Now</a>
                </li>
            </ul>
        </div>
    </nav>
</header>

<style>
.main-header {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    z-index: 1000;
    background: transparent;
    transition: all 0.3s ease;
}

.main-header.scrolled {
    background: rgba(0, 0, 0, 0.95);
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
}

/* Top Bar Styles */
.top-bar {
    background: rgba(0, 0, 0, 0.9);
    padding: 10px 0;
    font-size: 0.9rem;
}

.container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 0 20px;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.contact-info {
    display: flex;
    gap: 20px;
}

.info-item {
    display: flex;
    align-items: center;
    gap: 8px;
    color: #fff;
}

.info-item i {
    color: #ffbe00;
    font-size: 1rem;
}

.info-item a {
    color: #fff;
    text-decoration: none;
    transition: color 0.3s ease;
}

.info-item a:hover {
    color: #ffbe00;
}

.social-links {
    display: flex;
    gap: 15px;
}

.social-link {
    color: #fff;
    font-size: 1rem;
    transition: all 0.3s ease;
    width: 32px;
    height: 32px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 50%;
    background: rgba(255, 255, 255, 0.1);
}

.social-link:hover {
    color: #ffbe00;
    background: rgba(255, 255, 255, 0.2);
    transform: translateY(-2px);
}

/* Main Navigation Styles */
.main-nav {
    background: rgba(0, 0, 0, 0.8);
    padding: 15px 0;
    backdrop-filter: blur(10px);
}

.nav-container {
    position: relative;
}

.logo {
    display: flex;
    align-items: center;
    gap: 10px;
    color: #fff;
    text-decoration: none;
    font-size: 1.5rem;
    font-weight: 700;
}

.logo i {
    color: #ffbe00;
}

.logo span {
    color: #ffbe00;
}

.nav-toggle {
    display: none;
    flex-direction: column;
    gap: 6px;
    cursor: pointer;
}

.nav-toggle span {
    width: 30px;
    height: 2px;
    background: #fff;
    transition: all 0.3s ease;
}

.nav-links {
    display: flex;
    align-items: center;
    gap: 30px;
    margin: 0;
    padding: 0;
    list-style: none;
}

.nav-links a {
    color: #fff;
    text-decoration: none;
    font-weight: 500;
    transition: all 0.3s ease;
    position: relative;
}

.nav-links a:not(.book-now)::after {
    content: '';
    position: absolute;
    bottom: -5px;
    left: 0;
    width: 0;
    height: 2px;
    background: #ffbe00;
    transition: width 0.3s ease;
}

.nav-links a:hover::after,
.nav-links a.active::after {
    width: 100%;
}

.book-now {
    background: #ffbe00;
    padding: 10px 20px;
    border-radius: 5px;
    transition: all 0.3s ease;
}

.book-now:hover {
    background: #0088cc;
    transform: translateY(-2px);
}

/* Animations */
@keyframes pulse {
    0% { transform: scale(1); }
    50% { transform: scale(1.1); }
    100% { transform: scale(1); }
}

.pulse {
    animation: pulse 2s infinite;
}

/* Responsive Design */
@media (max-width: 992px) {
    .contact-info span:last-child {
        display: none;
    }
}

@media (max-width: 768px) {
    .top-bar {
        display: none;
    }

    .nav-toggle {
        display: flex;
    }

    .nav-links {
        display: none;
        position: absolute;
        top: 100%;
        left: 0;
        width: 100%;
        background: rgba(0, 0, 0, 0.95);
        padding: 20px;
        flex-direction: column;
        align-items: center;
        gap: 20px;
    }

    .nav-links.active {
        display: flex;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Sticky Header
    const header = document.querySelector('.main-header');
    const navToggle = document.querySelector('.nav-toggle');
    const navLinks = document.querySelector('.nav-links');

    window.addEventListener('scroll', () => {
        if (window.scrollY > 100) {
            header.classList.add('scrolled');
        } else {
            header.classList.remove('scrolled');
        }
    });

    // Mobile Menu Toggle
    navToggle.addEventListener('click', () => {
        navLinks.classList.toggle('active');
        navToggle.classList.toggle('active');
    });
});

// Hamburger menu toggle
const navToggle = document.getElementById('navToggle');
const navLinks = document.getElementById('navLinks');
navToggle.addEventListener('click', function() {
    navLinks.classList.toggle('open');
});
navToggle.addEventListener('keypress', function(e) {
    if (e.key === 'Enter' || e.key === ' ') {
        navLinks.classList.toggle('open');
    }
});
// Close menu on link click (mobile UX)
Array.from(navLinks.getElementsByTagName('a')).forEach(link => {
    link.addEventListener('click', () => {
        if (window.innerWidth < 1024) navLinks.classList.remove('open');
    });
});
</script> 