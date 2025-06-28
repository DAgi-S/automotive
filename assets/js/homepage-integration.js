/**
 * Homepage Dynamic Content Integration
 * Author: agent_website
 * Date: 2025-01-20
 * Purpose: Handle dynamic loading of ads, blogs, services, and products
 */

class HomepageIntegration {
    constructor() {
        this.loadingStates = {};
        this.init();
    }

    init() {
        // Wait for DOM to be ready
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', () => this.loadAllContent());
        } else {
            this.loadAllContent();
        }
    }

    async loadAllContent() {
        console.log('🚀 Loading dynamic homepage content...');
        
        // Load all sections in parallel for better performance
        const promises = [
            this.loadAds(),
            this.loadBlogs(),
            this.loadProducts(),
            this.loadServices()
        ];

        try {
            await Promise.allSettled(promises);
            console.log('✅ All homepage content loaded successfully');
        } catch (error) {
            console.error('❌ Error loading homepage content:', error);
        }
    }

    async loadAds() {
        const container = document.getElementById('ads-container');
        if (!container) return;

        try {
            this.showLoading('ads');
            const response = await fetch('api/get_home_ads.php');
            const data = await response.json();

            if (data.success && data.data.length > 0) {
                this.renderAds(data.data, container);
            } else {
                // Use fallback ads if API fails
                this.renderFallbackAds(container);
            }
        } catch (error) {
            console.error('Error loading ads:', error);
            this.renderFallbackAds(container);
        } finally {
            this.hideLoading('ads');
        }
    }

    async loadBlogs() {
        const container = document.getElementById('blogs-container');
        if (!container) return;

        try {
            this.showLoading('blogs');
            const response = await fetch('api/get_blogs.php?limit=2');
            const data = await response.json();

            if (data.success && data.data.length > 0) {
                this.renderBlogs(data.data, container);
            } else {
                this.renderFallbackBlogs(container);
            }
        } catch (error) {
            console.error('Error loading blogs:', error);
            this.renderFallbackBlogs(container);
        } finally {
            this.hideLoading('blogs');
        }
    }

    async loadProducts() {
        const container = document.getElementById('productsTrack');
        if (!container) return;

        try {
            this.showLoading('products');
            const response = await fetch('api/get_home_products.php');
            const data = await response.json();

            if (data.success && data.data.length > 0) {
                this.renderProducts(data.data, container);
            } else {
                this.renderFallbackProducts(container);
            }
        } catch (error) {
            console.error('Error loading products:', error);
            this.renderFallbackProducts(container);
        } finally {
            this.hideLoading('products');
        }
    }

    async loadServices() {
        // Services section to be added separately
        console.log('Services section will be added in next phase');
    }

    renderAds(ads, container) {
        const adsHTML = ads.map(ad => `
            <div class="col-md-6">
                <div class="ad-card">
                    <div class="ad-image">
                        <img src="${ad.image}" alt="${ad.title}" class="img-fluid" loading="lazy">
                        <div class="ad-badge">
                            <span class="badge bg-${ad.badge.color}">${ad.badge.text}</span>
                        </div>
                    </div>
                    <div class="ad-content">
                        <h5 class="ad-title">${ad.title}</h5>
                        <p class="ad-description">${ad.description}</p>
                        ${ad.pricing.original && ad.pricing.sale ? `
                            <div class="ad-price">
                                <span class="original-price">${ad.pricing.original}</span>
                                <span class="sale-price">${ad.pricing.sale}</span>
                            </div>
                        ` : ''}
                        <a href="${ad.cta.url}" class="btn btn-primary btn-sm">
                            <i class="${ad.cta.icon} me-1"></i>${ad.cta.text}
                        </a>
                    </div>
                </div>
            </div>
        `).join('');

        container.innerHTML = adsHTML;
    }

    renderBlogs(blogs, container) {
        const blogsHTML = blogs.map(blog => `
            <div class="col-md-6">
                <div class="blog-card">
                    <div class="blog-image">
                        <img src="${blog.image}" alt="${blog.title}" class="img-fluid" loading="lazy">
                        <div class="blog-read-time">
                            <i class="far fa-clock"></i> ${blog.read_time || '5 min read'}
                        </div>
                    </div>
                    <div class="blog-content">
                        <div class="blog-meta">
                            <span class="blog-date">
                                <i class="far fa-calendar"></i> ${blog.date}
                            </span>
                            <span class="blog-comments">
                                <i class="far fa-comments"></i> ${blog.comments || 0} Comments
                            </span>
                        </div>
                        <h5 class="blog-title">${blog.title}</h5>
                        <p class="blog-excerpt">${blog.excerpt}</p>
                        ${blog.tags && blog.tags.length > 0 ? `
                            <div class="blog-tags">
                                ${blog.tags.map(tag => `<span class="tag">${tag}</span>`).join('')}
                            </div>
                        ` : ''}
                        <a href="blog.php?id=${blog.id}" class="read-more-link">
                            Read Full Post <i class="fas fa-arrow-right"></i>
                        </a>
                    </div>
                </div>
            </div>
        `).join('');

        container.innerHTML = blogsHTML;
    }

    renderProducts(products, container) {
        const productsHTML = products.map(product => `
            <div class="product-slide">
                <div class="product-card">
                    <div class="product-image">
                        <img src="${product.image}" alt="${product.name}" class="img-fluid" loading="lazy">
                        ${product.badge ? `
                            <div class="product-badge">
                                <span class="badge bg-${product.badge.color}">${product.badge.text}</span>
                            </div>
                        ` : ''}
                        <div class="product-overlay">
                            <button class="btn btn-light btn-sm quick-view" data-product="${product.id}">
                                <i class="fas fa-eye"></i>
                            </button>
                            <button class="btn btn-light btn-sm add-wishlist" data-product="${product.id}">
                                <i class="far fa-heart"></i>
                            </button>
                        </div>
                    </div>
                    <div class="product-content">
                        <h6 class="product-title">${product.name}</h6>
                        <p class="product-description">${product.description}</p>
                        <div class="product-rating">
                            <div class="stars">
                                ${this.generateStars(product.rating || 4)}
                            </div>
                            <span class="rating-count">(${product.reviews || 0})</span>
                        </div>
                        <div class="product-price">
                            <span class="current-price">ETB ${product.price}</span>
                            ${product.original_price ? `<span class="original-price">ETB ${product.original_price}</span>` : ''}
                        </div>
                        <button class="btn btn-primary btn-sm add-to-cart" data-product="${product.id}" onclick="addToCart(${product.id})">
                            <i class="fas fa-cart-plus"></i> Add to Cart
                        </button>
                    </div>
                </div>
            </div>
        `).join('');

        container.innerHTML = productsHTML;
        
        // Reinitialize product slider
        this.initializeProductSlider();
    }

    generateStars(rating) {
        const fullStars = Math.floor(rating);
        const hasHalfStar = rating % 1 !== 0;
        const emptyStars = 5 - fullStars - (hasHalfStar ? 1 : 0);
        
        let starsHTML = '';
        for (let i = 0; i < fullStars; i++) {
            starsHTML += '<i class="fas fa-star"></i>';
        }
        if (hasHalfStar) {
            starsHTML += '<i class="fas fa-star-half-alt"></i>';
        }
        for (let i = 0; i < emptyStars; i++) {
            starsHTML += '<i class="far fa-star"></i>';
        }
        
        return starsHTML;
    }

    renderFallbackAds(container) {
        const fallbackAds = [
            {
                title: 'Engine Service Special',
                description: 'Get 20% off on complete engine diagnostics and tune-up services this month.',
                image: 'assets/images/gallery/car_heat.jpg',
                badge: { text: 'Hot Deal', color: 'danger' },
                pricing: { original: 'ETB 1,500', sale: 'ETB 1,200' },
                cta: { text: 'Book Now', icon: 'fas fa-calendar-check', url: 'service.php' }
            },
            {
                title: 'GPS Tracking Package',
                description: 'Premium GPS tracking with mobile app access and real-time monitoring.',
                image: 'assets/images/gallery/dashboard_lights.jpg',
                badge: { text: 'New', color: 'success' },
                pricing: { original: 'ETB 3,000', sale: 'ETB 2,500' },
                cta: { text: 'Learn More', icon: 'fas fa-map-marker-alt', url: 'service.php?category=gps' }
            }
        ];
        
        this.renderAds(fallbackAds, container);
    }

    renderFallbackBlogs(container) {
        const fallbackBlogs = [
            {
                id: 1,
                title: 'Winter Car Care: Essential Tips for Cold Weather',
                excerpt: 'Prepare your vehicle for the winter season with these essential maintenance tips to ensure safe and reliable transportation...',
                image: 'assets/images/gallery/car_heat.jpg',
                date: 'Dec 10, 2024',
                comments: 12,
                read_time: '5 min read',
                tags: ['Winter', 'Maintenance', 'Safety']
            },
            {
                id: 2,
                title: 'The Future of Electric Vehicles in Ethiopia',
                excerpt: 'Exploring the growing electric vehicle market in Ethiopia and what it means for automotive service providers...',
                image: 'assets/images/gallery/dashboard_lights.jpg',
                date: 'Dec 8, 2024',
                comments: 8,
                read_time: '8 min read',
                tags: ['Electric', 'Future', 'Technology']
            }
        ];
        
        this.renderBlogs(fallbackBlogs, container);
    }

    renderFallbackProducts(container) {
        const fallbackProducts = [
            {
                id: 1,
                name: 'Premium Car Battery',
                description: 'Long-lasting automotive battery with 3-year warranty',
                image: 'assets/images/default-product.jpg',
                badge: { text: 'New', color: 'success' },
                price: '2,500',
                original_price: '3,000',
                rating: 4,
                reviews: 24
            },
            {
                id: 2,
                name: 'Synthetic Engine Oil',
                description: 'High-performance 5W-30 synthetic motor oil',
                image: 'assets/images/default-product.jpg',
                badge: { text: 'Popular', color: 'primary' },
                price: '850',
                rating: 5,
                reviews: 18
            }
        ];
        
        this.renderProducts(fallbackProducts, container);
    }

    showLoading(section) {
        this.loadingStates[section] = true;
        console.log(`Loading ${section}...`);
    }

    hideLoading(section) {
        this.loadingStates[section] = false;
        // Remove loading elements
        const loadingElements = document.querySelectorAll(`.${section}-loading`);
        loadingElements.forEach(el => el.remove());
    }

    initializeProductSlider() {
        // Reinitialize the product slider functionality
        window.currentSlide = 0;
        if (typeof window.slideProducts === 'function') {
            // Slider is already initialized in home.php
        }
    }
}

// Initialize when DOM is ready
document.addEventListener('DOMContentLoaded', function() {
    new HomepageIntegration();
});

// Global add to cart function
window.addToCart = function(productId) {
    const button = event.target;
    const originalText = button.innerHTML;
    
    button.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Adding...';
    button.disabled = true;
    
    // Simulate API call to add to cart
    fetch('ecommerce/ajax/add_to_cart.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({ product_id: productId, quantity: 1 })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            button.innerHTML = '<i class="fas fa-check"></i> Added!';
            button.classList.add('btn-success');
            
            // Update cart count in header/navigation
            updateCartCount();
        } else {
            button.innerHTML = '<i class="fas fa-times"></i> Error';
            button.classList.add('btn-danger');
        }
        
        setTimeout(() => {
            button.innerHTML = originalText;
            button.disabled = false;
            button.classList.remove('btn-success', 'btn-danger');
        }, 2000);
    })
    .catch(error => {
        console.error('Error adding to cart:', error);
        button.innerHTML = originalText;
        button.disabled = false;
    });
};

// Update cart count function
function updateCartCount() {
    fetch('api/get_cart_count.php')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const cartBadges = document.querySelectorAll('.cart-count');
                cartBadges.forEach(badge => {
                    badge.textContent = data.count;
                    badge.style.display = data.count > 0 ? 'block' : 'none';
                });
            }
        })
        .catch(error => console.error('Error updating cart count:', error));
} 