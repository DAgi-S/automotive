// Product Slider JavaScript
let currentSlide = 0;

document.addEventListener('DOMContentLoaded', function() {
    const slides = document.querySelectorAll('.product-slide');
    const totalSlides = slides.length;
    
    function getSlidesToShow() {
        if (window.innerWidth <= 576) return 1;
        if (window.innerWidth <= 768) return 2;
        return 3;
    }
    
    const slidesToShow = getSlidesToShow();
    const maxSlide = Math.max(0, totalSlides - slidesToShow);
    
    function updateSlider() {
        const track = document.getElementById('productsTrack');
        if (track && slides.length > 0) {
            const slideWidth = slides[0].offsetWidth + 16; // 16px gap
            const translateX = -(currentSlide * slideWidth);
            track.style.transform = `translateX(${translateX}px)`;
        }
    }
    
    // Global function for button clicks
    window.slideProducts = function(direction) {
        if (direction === 1 && currentSlide < maxSlide) {
            currentSlide++;
        } else if (direction === -1 && currentSlide > 0) {
            currentSlide--;
        }
        updateSlider();
    };
    
    // Add to cart function
    window.addToCart = function(productId) {
        const button = event.target;
        const originalText = button.innerHTML;
        
        button.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Adding...';
        button.disabled = true;
        
        setTimeout(() => {
            button.innerHTML = '<i class="fas fa-check"></i> Added!';
            button.classList.add('btn-success');
            
            setTimeout(() => {
                button.innerHTML = originalText;
                button.disabled = false;
                button.classList.remove('btn-success');
            }, 2000);
        }, 1000);
    };
    
    // Initialize
    updateSlider();
}); 