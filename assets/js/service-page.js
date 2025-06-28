/*
 * Service Page JavaScript
 * Automotive Website - Service Page Functionality
 * No external dependencies except jQuery
 */

$(document).ready(function() {
    // Initialize service page
    initServicePage();
});

function initServicePage() {
    updateServicesCount();
    initSearch();
    initFilters();
    initBookingButtons();
    initQuickActions();
    checkBusinessHours();
    
    // Initialize animations
    if ('IntersectionObserver' in window) {
        initScrollAnimations();
    }
    
    // Initialize service card interactions
    initServiceCardEffects();
}

function updateServicesCount() {
    const totalServices = $('.service-card').length;
    const visibleServices = $('.service-card:visible').length;
    $('#servicesCount').text(`Showing ${visibleServices} of ${totalServices} services`);
}

function initSearch() {
    $('#serviceSearch').on('input', function() {
        const searchTerm = $(this).val().toLowerCase();
        filterServices();
    });
}

function initFilters() {
    $('.filter-btn').on('click', function() {
        $('.filter-btn').removeClass('active');
        $(this).addClass('active');
        filterServices();
    });
}

function filterServices() {
    const searchTerm = $('#serviceSearch').val().toLowerCase();
    const activeCategory = $('.filter-btn.active').data('category');
    let visibleCount = 0;

    $('.service-card').each(function() {
        const serviceName = $(this).data('name');
        const serviceCategory = $(this).data('category');
        
        const matchesSearch = searchTerm === '' || serviceName.includes(searchTerm);
        const matchesCategory = activeCategory === 'all' || serviceCategory === activeCategory;
        
        if (matchesSearch && matchesCategory) {
            $(this).show().addClass('animate-in');
            visibleCount++;
        } else {
            $(this).hide().removeClass('animate-in');
        }
    });

    // Show/hide no results message
    if (visibleCount === 0) {
        $('#noResults').show();
    } else {
        $('#noResults').hide();
    }

    updateServicesCount();
}

function clearFilters() {
    $('#serviceSearch').val('');
    $('.filter-btn[data-category="all"]').click();
}

function showBookingLoader(button) {
    const $button = $(button);
    const originalText = $button.html();
    $button.addClass('loading').prop('disabled', true);
    $button.html('<i class="fas fa-spinner fa-spin me-2"></i>Booking...');
    
    // Store original text for potential restoration
    $button.data('original-text', originalText);
    
    // Vibrate if supported (mobile feedback)
    if ('vibrate' in navigator) {
        navigator.vibrate(50);
    }
}

function hideBookingLoader(button) {
    const $button = $(button);
    const originalText = $button.data('original-text') || '<i class="fas fa-calendar-plus me-2"></i>Book Now';
    $button.removeClass('loading').prop('disabled', false);
    $button.html(originalText);
}

function initBookingButtons() {
    $('.btn-book').on('click', function(e) {
        e.preventDefault();
        
        const button = $(this);
        const serviceId = button.data('service-id');
        const serviceName = button.data('service-name');
        
        // Show loading state
        showBookingLoader(button);
        
        // Simulate booking process
        setTimeout(() => {
            // Navigate to booking page
            window.location.href = button.attr('href');
        }, 1000);
        
        // Track booking attempt
        if (typeof gtag !== 'undefined') {
            gtag('event', 'service_booking_attempt', {
                'service_id': serviceId,
                'service_name': serviceName
            });
        }
    });
}

function initQuickActions() {
    $('.emergency-btn').on('click', function(e) {
        // Add confirmation for emergency calls
        if (!confirm('You are about to call our emergency service line. Continue?')) {
            e.preventDefault();
        }
    });
    
    // Quick action button effects
    $('.action-btn').on('click', function() {
        const $btn = $(this);
        $btn.addClass('clicked');
        setTimeout(() => {
            $btn.removeClass('clicked');
        }, 200);
    });
}

function checkBusinessHours() {
    const now = new Date();
    const day = now.getDay(); // 0 = Sunday, 1 = Monday, etc.
    const hour = now.getHours();
    
    let isOpen = false;
    
    if (day >= 1 && day <= 5) { // Monday to Friday
        isOpen = hour >= 8 && hour < 18;
    } else if (day === 6) { // Saturday
        isOpen = hour >= 9 && hour < 16;
    }
    
    const statusIndicator = $('.status-indicator');
    const statusText = $('.status-text');
    
    if (isOpen) {
        statusIndicator.addClass('open').removeClass('closed');
        statusText.text('We are currently open');
    } else {
        statusIndicator.addClass('closed').removeClass('open');
        statusText.text('We are currently closed');
    }
}

function openDirections() {
    // Replace with actual coordinates
    const lat = 9.0307;
    const lng = 38.7616;
    const url = `https://www.google.com/maps/dir/?api=1&destination=${lat},${lng}`;
    window.open(url, '_blank');
}

function initScrollAnimations() {
    const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    };
    
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('animate-in');
            }
        });
    }, observerOptions);
    
    // Observe service cards
    $('.service-card').each(function() {
        observer.observe(this);
    });
}

function initServiceCardEffects() {
    // Service card hover effects
    $('.service-card').hover(
        function() {
            $(this).find('.service-icon').addClass('hover-effect');
            $(this).addClass('card-hover');
        },
        function() {
            $(this).find('.service-icon').removeClass('hover-effect');
            $(this).removeClass('card-hover');
        }
    );
    
    // Add click effect for mobile
    $('.service-card').on('touchstart', function() {
        $(this).addClass('card-touch');
    }).on('touchend', function() {
        const $card = $(this);
        setTimeout(() => {
            $card.removeClass('card-touch');
        }, 150);
    });
}

// Enhanced bookmark functionality (if needed)
function toggleBookmark(element) {
    const $bookmark = $(element);
    $bookmark.toggleClass('active');
    
    // Add visual feedback
    if ($bookmark.hasClass('active')) {
        $bookmark.html('<i class="fas fa-bookmark"></i>');
        showToast('Service bookmarked!', 'success');
    } else {
        $bookmark.html('<i class="far fa-bookmark"></i>');
        showToast('Bookmark removed', 'info');
    }
}

// Toast notification function
function showToast(message, type = 'info') {
    const toastHtml = `
        <div class="toast-notification toast-${type}">
            <i class="fas fa-${type === 'success' ? 'check-circle' : type === 'error' ? 'exclamation-circle' : 'info-circle'}"></i>
            <span>${message}</span>
        </div>
    `;
    
    const $toast = $(toastHtml);
    $('body').append($toast);
    
    // Show toast
    setTimeout(() => {
        $toast.addClass('show');
    }, 100);
    
    // Hide toast
    setTimeout(() => {
        $toast.removeClass('show');
        setTimeout(() => {
            $toast.remove();
        }, 300);
    }, 3000);
}

// Handle errors gracefully
window.addEventListener('error', function(e) {
    console.warn('JavaScript error handled:', e.message);
    // Don't show error to users in production
    if (window.location.hostname === 'localhost') {
        showToast('A JavaScript error occurred. Check console for details.', 'error');
    }
});

// Performance monitoring
if ('performance' in window) {
    window.addEventListener('load', function() {
        setTimeout(() => {
            const loadTime = performance.timing.loadEventEnd - performance.timing.navigationStart;
            console.log(`Service page loaded in ${loadTime}ms`);
        }, 0);
    });
}

// Prevent CORS errors by handling external content gracefully
$(document).ajaxError(function(event, xhr, settings, error) {
    if (xhr.status === 0 && error === '') {
        console.warn('CORS or network error prevented request to:', settings.url);
    }
});

// Add CSS for toast notifications
const toastCSS = `
<style>
.toast-notification {
    position: fixed;
    top: 20px;
    right: 20px;
    background: #fff;
    padding: 15px 20px;
    border-radius: 8px;
    box-shadow: 0 4px 20px rgba(0,0,0,0.15);
    z-index: 10000;
    display: flex;
    align-items: center;
    gap: 10px;
    transform: translateX(100%);
    transition: transform 0.3s ease;
    max-width: 300px;
}

.toast-notification.show {
    transform: translateX(0);
}

.toast-notification.toast-success {
    border-left: 4px solid #28a745;
    color: #28a745;
}

.toast-notification.toast-error {
    border-left: 4px solid #dc3545;
    color: #dc3545;
}

.toast-notification.toast-info {
    border-left: 4px solid #007bff;
    color: #007bff;
}

@media (max-width: 768px) {
    .toast-notification {
        top: 80px;
        right: 10px;
        left: 10px;
        max-width: none;
    }
}
</style>
`;

$('head').append(toastCSS);
