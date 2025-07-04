console.log('Home page enhanced');

// Home Page JavaScript - Mobile-First Optimizations
(function() {
    'use strict';

    // Performance monitoring
    const performanceStart = performance.now();
    
    // Touch device detection
    const isTouchDevice = () => {
        return (('ontouchstart' in window) ||
                (navigator.maxTouchPoints > 0) ||
                (navigator.msMaxTouchPoints > 0));
    };

    // Add touch device class
    if (isTouchDevice()) {
        document.body.classList.add('touch-device');
    }

    // Site content loader
    const siteContent = document.querySelector('.site-content');
    if (siteContent) {
        // Show content after a brief delay for smooth loading
        setTimeout(() => {
            siteContent.classList.add('loaded');
        }, 100);
    }

    // Ripple effect for touch interactions
    function createRipple(event) {
        const button = event.currentTarget;
        const circle = document.createElement('span');
        const diameter = Math.max(button.clientWidth, button.clientHeight);
        const radius = diameter / 2;

        circle.style.width = circle.style.height = `${diameter}px`;
        circle.style.left = `${event.clientX - button.offsetLeft - radius}px`;
        circle.style.top = `${event.clientY - button.offsetTop - radius}px`;
        circle.classList.add('ripple');

        const ripple = button.getElementsByClassName('ripple')[0];
        if (ripple) {
            ripple.remove();
        }

        button.appendChild(circle);

        // Remove ripple after animation
        setTimeout(() => {
            if (circle.parentNode) {
                circle.remove();
            }
        }, 600);
    }

    // Add ripple effect to buttons and cards
    const addRippleEffect = () => {
        const rippleElements = document.querySelectorAll(
            '.cta-button, .btn-primary, .btn-outline, .card, .service-link'
        );
        
        rippleElements.forEach(element => {
            element.style.position = 'relative';
            element.style.overflow = 'hidden';
            element.addEventListener('click', createRipple);
        });
    };

    // Intersection Observer for scroll animations
    const observeElements = () => {
        if (!window.IntersectionObserver) return;

        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('animated');
                    observer.unobserve(entry.target);
                }
            });
        }, {
            threshold: 0.1,
            rootMargin: '0px 0px -50px 0px'
        });

        // Observe cards and sections
        const animateElements = document.querySelectorAll(
            '.card, .section-header, .cta-section, .blog-card'
        );
        
        animateElements.forEach((element, index) => {
            element.classList.add('animate-on-scroll');
            // Stagger animation delays
            element.style.animationDelay = `${index * 0.1}s`;
            observer.observe(element);
        });
    };

    // Touch-friendly hover effects for mobile
    const handleTouchHover = () => {
        if (!isTouchDevice()) return;

        const hoverElements = document.querySelectorAll('.card, .blog-card');
        
        hoverElements.forEach(element => {
            element.addEventListener('touchstart', function() {
                this.classList.add('touch-hover');
            });

            element.addEventListener('touchend', function() {
                setTimeout(() => {
                    this.classList.remove('touch-hover');
                }, 150);
            });

            element.addEventListener('touchcancel', function() {
                this.classList.remove('touch-hover');
            });
        });
    };

    // Testimonial Carousel Functionality
    let currentSlide = 0;
    let slides = null;
    let totalSlides = 0;

    function initializeTestimonialCarousel() {
        slides = document.querySelector('.testimonial-slides');
        const testimonialItems = document.querySelectorAll('.testimonial-item');
        totalSlides = testimonialItems.length;
        
        if (!slides || totalSlides === 0) return;
        
        // Auto-advance slides every 5 seconds
        if (totalSlides > 1) {
            setInterval(() => moveTestimonial(1), 5000);
        }
        
        // Add touch support for mobile
        if ('ontouchstart' in window) {
            let startX = 0;
            let endX = 0;
            
            slides.addEventListener('touchstart', (e) => {
                startX = e.touches[0].clientX;
            });
            
            slides.addEventListener('touchend', (e) => {
                endX = e.changedTouches[0].clientX;
                const diff = startX - endX;
                
                if (Math.abs(diff) > 50) {
                    if (diff > 0) {
                        moveTestimonial(1);
                    } else {
                        moveTestimonial(-1);
                    }
                }
            });
        }
    }

    function moveTestimonial(direction) {
        if (!slides || totalSlides === 0) return;
        
        currentSlide = (currentSlide + direction + totalSlides) % totalSlides;
        slides.style.transform = `translateX(-${currentSlide * 100}%)`;
    }

    // Make moveTestimonial globally available
    window.moveTestimonial = moveTestimonial;

    // Blog Card Hover Effects
    function initializeBlogEffects() {
        const blogCards = document.querySelectorAll('.blog-card');
        
        blogCards.forEach(card => {
            card.addEventListener('mouseenter', function() {
                this.style.transform = 'translateY(-5px)';
            });
            
            card.addEventListener('mouseleave', function() {
                this.style.transform = 'translateY(0)';
            });
            
            // Touch support for mobile
            card.addEventListener('touchstart', function() {
                this.classList.add('touch-hover');
            });
            
            card.addEventListener('touchend', function() {
                setTimeout(() => {
                    this.classList.remove('touch-hover');
                }, 200);
            });
        });
    }

    // Smooth scroll for anchor links
    const initSmoothScroll = () => {
        const links = document.querySelectorAll('a[href^="#"]');
        
        links.forEach(link => {
            link.addEventListener('click', function(e) {
                const href = this.getAttribute('href');
                if (href === '#') return;

                const target = document.querySelector(href);
                if (target) {
                    e.preventDefault();
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });
    };

    // Lazy loading for images
    const initLazyLoading = () => {
        if (!window.IntersectionObserver) return;

        const imageObserver = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const img = entry.target;
                    if (img.dataset.src) {
                        img.src = img.dataset.src;
                        img.removeAttribute('data-src');
                    }
                    imageObserver.unobserve(img);
                }
            });
        });

        const lazyImages = document.querySelectorAll('img[data-src]');
        lazyImages.forEach(img => imageObserver.observe(img));
    };

    // Error handling for missing elements
    const handleErrors = () => {
        window.addEventListener('error', function(e) {
            console.warn('Home page error:', e.message);
        });

        // Handle image load errors
        const images = document.querySelectorAll('img');
        images.forEach(img => {
            img.addEventListener('error', function() {
                // Replace with placeholder or default image
                if (!this.src.includes('Nati-logo.png')) {
                    this.src = 'assets/images/Nati-logo.png';
                }
            });
        });
    };

    // Performance monitoring
    const logPerformance = () => {
        const performanceEnd = performance.now();
        const loadTime = performanceEnd - performanceStart;
        
        console.log(`Home page JavaScript loaded in ${loadTime.toFixed(2)}ms`);
        
        // Log to analytics if available
        if (window.gtag) {
            gtag('event', 'timing_complete', {
                name: 'home_js_load',
                value: Math.round(loadTime)
            });
        }
    };

    // Initialize all functionality
    const init = () => {
        try {
            addRippleEffect();
            observeElements();
            handleTouchHover();
            initializeTestimonialCarousel();
            initializeBlogEffects();
            initSmoothScroll();
            initLazyLoading();
            handleErrors();
            logPerformance();

            // Dispatch custom event when home page is ready
            document.dispatchEvent(new CustomEvent('homePageReady', {
                detail: { loadTime: performance.now() - performanceStart }
            }));

        } catch (error) {
            console.warn('Home page initialization error:', error);
        }
    };

    // DOM ready check
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }

    // Cleanup on page unload
    window.addEventListener('beforeunload', () => {
        // Clear any intervals or timeouts
        document.querySelectorAll('.ripple').forEach(ripple => ripple.remove());
    });

})();
