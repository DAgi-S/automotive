// New Home Layout JavaScript
document.addEventListener('DOMContentLoaded', function() {
    
    // Product Slider Functionality
    let currentSlide = 0;
    const slides = document.querySelectorAll('.product-slide');
    const totalSlides = slides.length;
    const slidesToShow = getSlidesToShow();
    const maxSlide = Math.max(0, totalSlides - slidesToShow);
    
    function getSlidesToShow() {
        if (window.innerWidth <= 576) return 1;
        if (window.innerWidth <= 768) return 2;
        if (window.innerWidth <= 1024) return 3;
        return 4;
    }
    
    function updateSlider() {
        const track = document.getElementById('productsTrack');
        if (track) {
            const slideWidth = slides[0].offsetWidth + 16; // 16px gap
            const translateX = -(currentSlide * slideWidth);
            track.style.transform = `translateX(${translateX}px)`;
        }
    }
    
    // Slide Products Function (called by buttons)
    window.slideProducts = function(direction) {
        if (direction === 1 && currentSlide < maxSlide) {
            currentSlide++;
        } else if (direction === -1 && currentSlide > 0) {
            currentSlide--;
        }
        updateSlider();
    };
    
    // Touch/Swipe Support for Products Slider
    let startX = 0;
    let isDragging = false;
    
    const sliderContainer = document.querySelector('.slider-container');
    if (sliderContainer) {
        // Mouse events
        sliderContainer.addEventListener('mousedown', handleStart);
        sliderContainer.addEventListener('mousemove', handleMove);
        sliderContainer.addEventListener('mouseup', handleEnd);
        sliderContainer.addEventListener('mouseleave', handleEnd);
        
        // Touch events
        sliderContainer.addEventListener('touchstart', handleStart);
        sliderContainer.addEventListener('touchmove', handleMove);
        sliderContainer.addEventListener('touchend', handleEnd);
    }
    
    function handleStart(e) {
        isDragging = true;
        startX = e.type.includes('mouse') ? e.clientX : e.touches[0].clientX;
        sliderContainer.style.cursor = 'grabbing';
    }
    
    function handleMove(e) {
        if (!isDragging) return;
        e.preventDefault();
        
        const currentX = e.type.includes('mouse') ? e.clientX : e.touches[0].clientX;
        const deltaX = startX - currentX;
        
        // Add visual feedback
        const track = document.getElementById('productsTrack');
        if (track) {
            const slideWidth = slides[0].offsetWidth + 16;
            const currentTranslate = -(currentSlide * slideWidth);
            track.style.transform = `translateX(${currentTranslate - deltaX}px)`;
        }
    }
    
    function handleEnd(e) {
        if (!isDragging) return;
        isDragging = false;
        sliderContainer.style.cursor = 'grab';
        
        const endX = e.type.includes('mouse') ? e.clientX : e.changedTouches[0].clientX;
        const deltaX = startX - endX;
        
        // Threshold for slide change
        if (Math.abs(deltaX) > 50) {
            if (deltaX > 0 && currentSlide < maxSlide) {
                currentSlide++;
            } else if (deltaX < 0 && currentSlide > 0) {
                currentSlide--;
            }
        }
        
        updateSlider();
    }
    
    // Auto-slide for products (optional)
    let autoSlideInterval;
    
    function startAutoSlide() {
        autoSlideInterval = setInterval(() => {
            if (currentSlide >= maxSlide) {
                currentSlide = 0;
            } else {
                currentSlide++;
            }
            updateSlider();
        }, 5000); // 5 seconds
    }
    
    function stopAutoSlide() {
        clearInterval(autoSlideInterval);
    }
    
    // Start auto-slide and pause on hover
    if (sliderContainer) {
        startAutoSlide();
        sliderContainer.addEventListener('mouseenter', stopAutoSlide);
        sliderContainer.addEventListener('mouseleave', startAutoSlide);
    }
    
    // Add to Cart Functionality
    window.addToCart = function(productId) {
        const button = event.target;
        const originalText = button.innerHTML;
        
        // Show loading state
        button.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Adding...';
        button.disabled = true;
        
        // Simulate API call (replace with actual implementation)
        setTimeout(() => {
            // Success feedback
            button.innerHTML = '<i class="fas fa-check"></i> Added!';
            button.classList.add('btn-success');
            
            // Show notification
            showNotification('Product added to cart successfully!', 'success');
            
            // Reset button after 2 seconds
            setTimeout(() => {
                button.innerHTML = originalText;
                button.disabled = false;
                button.classList.remove('btn-success');
            }, 2000);
            
            // Update cart count (if applicable)
            updateCartCount();
        }, 1000);
    };
    
    // Quick View Functionality
    document.addEventListener('click', function(e) {
        if (e.target.closest('.quick-view')) {
            const productId = e.target.closest('.quick-view').dataset.product;
            showQuickView(productId);
        }
    });
    
    function showQuickView(productId) {
        // Create modal for quick view (basic implementation)
        const modal = document.createElement('div');
        modal.className = 'modal fade';
        modal.innerHTML = `
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Product Quick View</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="d-flex justify-content-center">
                            <div class="spinner-border" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        `;
        
        document.body.appendChild(modal);
        const bsModal = new bootstrap.Modal(modal);
        bsModal.show();
        
        // Remove modal when closed
        modal.addEventListener('hidden.bs.modal', () => {
            modal.remove();
        });
        
        // Simulate loading product data
        setTimeout(() => {
            modal.querySelector('.modal-body').innerHTML = `
                <div class="row">
                    <div class="col-md-6">
                        <img src="assets/images/default-product.jpg" class="img-fluid rounded" alt="Product">
                    </div>
                    <div class="col-md-6">
                        <h4>Product Name</h4>
                        <p class="text-muted">Product description goes here...</p>
                        <div class="mb-3">
                            <span class="h5 text-primary">ETB 2,500</span>
                            <span class="text-muted text-decoration-line-through ms-2">ETB 3,000</span>
                        </div>
                        <button class="btn btn-primary">
                            <i class="fas fa-cart-plus"></i> Add to Cart
                        </button>
                    </div>
                </div>
            `;
        }, 1000);
    }
    
    // Wishlist Functionality
    document.addEventListener('click', function(e) {
        if (e.target.closest('.add-wishlist')) {
            const button = e.target.closest('.add-wishlist');
            const icon = button.querySelector('i');
            
            if (icon.classList.contains('far')) {
                icon.classList.remove('far');
                icon.classList.add('fas');
                button.classList.add('text-danger');
                showNotification('Added to wishlist!', 'success');
            } else {
                icon.classList.remove('fas');
                icon.classList.add('far');
                button.classList.remove('text-danger');
                showNotification('Removed from wishlist!', 'info');
            }
        }
    });
    
    // Notification System
    function showNotification(message, type = 'info') {
        const notification = document.createElement('div');
        notification.className = `alert alert-${type} alert-dismissible fade show position-fixed`;
        notification.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
        notification.innerHTML = `
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;
        
        document.body.appendChild(notification);
        
        // Auto-remove after 3 seconds
        setTimeout(() => {
            if (notification.parentNode) {
                notification.remove();
            }
        }, 3000);
    }
    
    // Update Cart Count (placeholder)
    function updateCartCount() {
        const cartBadge = document.querySelector('.cart-count');
        if (cartBadge) {
            const currentCount = parseInt(cartBadge.textContent) || 0;
            cartBadge.textContent = currentCount + 1;
        }
    }
    
    // Handle window resize for slider
    window.addEventListener('resize', function() {
        const newSlidesToShow = getSlidesToShow();
        if (newSlidesToShow !== slidesToShow) {
            currentSlide = 0; // Reset to first slide
            updateSlider();
        }
    });
    
    // Card Animation on Scroll
    const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    };
    
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.style.animationPlayState = 'running';
                observer.unobserve(entry.target);
            }
        });
    }, observerOptions);
    
    // Observe all cards for animation
    document.querySelectorAll('.ad-card, .article-card, .blog-card, .car-card, .product-card').forEach(card => {
        card.style.animationPlayState = 'paused';
        observer.observe(card);
    });
    
    // Initialize slider
    updateSlider();
    
    console.log('New Home Layout JavaScript initialized successfully!');
}); 