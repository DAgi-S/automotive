/**
 * Homepage Dynamic Content Integration
 * Author: agent_website
 * Date: 2025-01-20
 */

class HomepageIntegration {
    constructor() {
        this.init();
    }

    init() {
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', () => this.loadAllContent());
        } else {
            this.loadAllContent();
        }
    }

    async loadAllContent() {
        console.log('🚀 Loading dynamic homepage content...');
        
        try {
            await Promise.allSettled([
                this.loadAds(),
                this.loadBlogs(),
                this.loadProducts(),
                this.loadUserVehicles(),
                this.loadServices(),
                this.loadAnalytics()
            ]);
            console.log('✅ All content loaded');
        } catch (error) {
            console.error('❌ Error loading content:', error);
        }
    }

    async loadAds() {
        const container = document.getElementById('ads-container');
        if (!container) return;

        try {
            const response = await fetch('api/get_home_ads.php');
            const data = await response.json();

            if (data.success && data.data.length > 0) {
                this.renderAds(data.data, container);
            } else {
                this.renderFallbackAds(container);
            }
        } catch (error) {
            console.error('Error loading ads:', error);
            this.renderFallbackAds(container);
        }
    }

    async loadBlogs() {
        const container = document.getElementById('blogs-container');
        if (!container) return;

        try {
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
        }
    }

    async loadProducts() {
        const container = document.getElementById('productsTrack');
        if (!container) return;

        try {
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
        }
    }

    renderAds(ads, container) {
        const adsHTML = ads.map(ad => `
            <div class="col-md-6">
                <div class="ad-card">
                    <div class="ad-image">
                        <img src="${ad.image}" alt="${ad.title}" class="img-fluid">
                        <div class="ad-badge">
                            <span class="badge bg-${ad.badge.color}">${ad.badge.text}</span>
                        </div>
                    </div>
                    <div class="ad-content">
                        <h5 class="ad-title">${ad.title}</h5>
                        <p class="ad-description">${ad.description}</p>
                        ${ad.pricing && ad.pricing.original ? `
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
                        <img src="${blog.image}" alt="${blog.title}" class="img-fluid">
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
                        <img src="${product.image}" alt="${product.name}" class="img-fluid">
                        <div class="product-badge">
                            <span class="badge bg-success">New</span>
                        </div>
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
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="far fa-star"></i>
                            </div>
                            <span class="rating-count">(${product.reviews || 0})</span>
                        </div>
                        <div class="product-price">
                            <span class="current-price">ETB ${product.price}</span>
                        </div>
                        <button class="btn btn-primary btn-sm add-to-cart" data-product="${product.id}">
                            <i class="fas fa-cart-plus"></i> Add to Cart
                        </button>
                    </div>
                </div>
            </div>
        `).join('');

        container.innerHTML = productsHTML;
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
                excerpt: 'Prepare your vehicle for the winter season with these essential maintenance tips...',
                image: 'assets/images/gallery/car_heat.jpg',
                date: 'Dec 10, 2024',
                comments: 12,
                read_time: '5 min read'
            },
            {
                id: 2,
                title: 'The Future of Electric Vehicles in Ethiopia',
                excerpt: 'Exploring the growing electric vehicle market in Ethiopia...',
                image: 'assets/images/gallery/dashboard_lights.jpg',
                date: 'Dec 8, 2024',
                comments: 8,
                read_time: '8 min read'
            }
        ];
        
        this.renderBlogs(fallbackBlogs, container);
    }

    async loadUserVehicles() {
        const container = document.querySelector('.cars-container');
        if (!container) return;

        try {
            const response = await fetch('api/get_user_vehicles.php');
            const data = await response.json();

            if (data.success && data.data.length > 0) {
                this.renderUserVehicles(data.data, container);
            } else if (data.suggestion) {
                this.renderNoVehicles(data.suggestion, container);
            } else {
                this.renderFallbackVehicles(container);
            }
        } catch (error) {
            console.error('Error loading user vehicles:', error);
            this.renderFallbackVehicles(container);
        }
    }

    async loadServices() {
        const container = document.getElementById('services-container');
        if (!container) return;

        try {
            const response = await fetch('api/get_home_services.php?limit=6');
            const data = await response.json();

            if (data.success && data.data.length > 0) {
                this.renderServices(data.data, container);
            } else {
                this.renderFallbackServices(container);
            }
        } catch (error) {
            console.error('Error loading services:', error);
            this.renderFallbackServices(container);
        }
    }

    renderUserVehicles(vehicles, container) {
        const vehiclesHTML = vehicles.map(vehicle => {
            const statusClass = vehicle.status === 'urgent' ? 'urgent' : 
                               vehicle.status === 'warning' ? 'warning' : 'good';
            
            const statusIcon = vehicle.status === 'urgent' ? 'fas fa-exclamation-triangle' : 
                              vehicle.status === 'warning' ? 'fas fa-exclamation-circle' : 'fas fa-check-circle';
            
            const statusText = vehicle.status === 'urgent' ? 'Urgent' : 
                              vehicle.status === 'warning' ? 'Attention' : 'Good';

            const notificationsHTML = vehicle.notifications.map(notification => `
                <div class="notification-item ${notification.priority}">
                    <div class="notification-icon">
                        <i class="${notification.icon}"></i>
                    </div>
                    <div class="notification-content">
                        <h6>${notification.title}</h6>
                        <p>${notification.message}</p>
                    </div>
                </div>
            `).join('');

            const actionsHTML = vehicle.actions.map(action => `
                <a href="${action.url}" class="btn ${action.class} btn-sm">
                    <i class="${action.icon}"></i> ${action.text}
                </a>
            `).join('');

            return `
                <div class="car-card ${statusClass}">
                    <div class="car-header">
                        <div class="car-info">
                            <h5 class="car-name">${vehicle.make} ${vehicle.model}</h5>
                            <p class="car-plate">${vehicle.license_plate}</p>
                        </div>
                        <div class="car-status">
                            <span class="status-badge ${statusClass}">
                                <i class="${statusIcon}"></i> ${statusText}
                            </span>
                        </div>
                    </div>
                    <div class="car-notifications">
                        ${notificationsHTML}
                    </div>
                    <div class="car-actions">
                        ${actionsHTML}
                    </div>
                </div>
            `;
        }).join('');

        container.innerHTML = vehiclesHTML;
    }

    renderNoVehicles(suggestion, container) {
        container.innerHTML = `
            <div class="no-vehicles-card">
                <div class="no-vehicles-content">
                    <div class="no-vehicles-icon">
                        <i class="fas fa-car fa-3x text-muted"></i>
                    </div>
                    <h5>${suggestion.title}</h5>
                    <p class="text-muted">${suggestion.description}</p>
                    <a href="${suggestion.action.url}" class="btn ${suggestion.action.class}">
                        <i class="${suggestion.action.icon}"></i> ${suggestion.action.text}
                    </a>
                </div>
            </div>
        `;
    }

    renderServices(services, container) {
        const servicesHTML = services.map(service => `
            <div class="col-md-4 col-sm-6 mb-4">
                <div class="service-card h-100">
                    <div class="service-icon">
                        <i class="${service.icon} fa-2x text-primary"></i>
                    </div>
                    <div class="service-content">
                        <h5 class="service-title">${service.name}</h5>
                        <p class="service-description">${service.description}</p>
                        <div class="service-details">
                            <div class="service-price">
                                <strong>${service.price}</strong>
                            </div>
                            <div class="service-duration">
                                <i class="far fa-clock"></i> ${service.duration}
                            </div>
                        </div>
                        <div class="service-features">
                            ${service.features.map(feature => `
                                <small class="feature-item">
                                    <i class="fas fa-check text-success"></i> ${feature}
                                </small>
                            `).join('')}
                        </div>
                    </div>
                    <div class="service-actions">
                        <a href="${service.booking_url}" class="btn btn-primary btn-sm">
                            <i class="fas fa-calendar-check"></i> Book Now
                        </a>
                        <a href="${service.details_url}" class="btn btn-outline-primary btn-sm">
                            <i class="fas fa-info-circle"></i> Details
                        </a>
                    </div>
                </div>
            </div>
        `).join('');

        container.innerHTML = servicesHTML;
    }

    renderFallbackVehicles(container) {
        // Keep the existing static vehicles as fallback
        console.log('Using fallback vehicle data');
    }

    renderFallbackServices(container) {
        const fallbackServices = [
            {
                id: 'fallback-service-1',
                name: 'Engine Diagnostics',
                description: 'Complete engine health check and diagnostic service',
                icon: 'fas fa-search',
                price: 'ETB 500',
                duration: '1 hour',
                features: ['Advanced diagnostic tools', 'Detailed report', 'Expert analysis'],
                booking_url: 'service.php?book=diagnostics',
                details_url: 'service.php'
            },
            {
                id: 'fallback-service-2',
                name: 'Oil Change Service',
                description: 'Premium oil change with filter replacement',
                icon: 'fas fa-oil-can',
                price: 'ETB 800',
                duration: '30 minutes',
                features: ['High-quality oil', 'Filter replacement', 'Quick service'],
                booking_url: 'service.php?book=oil-change',
                details_url: 'service.php'
            },
            {
                id: 'fallback-service-3',
                name: 'GPS Installation',
                description: 'Professional GPS tracking system installation',
                icon: 'fas fa-map-marker-alt',
                price: 'ETB 2,500',
                duration: '2 hours',
                features: ['Real-time tracking', 'Mobile app access', '1-year warranty'],
                booking_url: 'service.php?book=gps-installation',
                details_url: 'service.php'
            }
        ];

        this.renderServices(fallbackServices, container);
    }

    async loadAnalytics() {
        const container = document.getElementById('analytics-container');
        if (!container) return;

        try {
            const response = await fetch('api/get_homepage_analytics.php');
            const data = await response.json();

            if (data.success && data.data) {
                this.renderAnalytics(data.data, container);
            } else {
                this.renderFallbackAnalytics(container);
            }
        } catch (error) {
            console.error('Error loading analytics:', error);
            this.renderFallbackAnalytics(container);
        }
    }

    renderAnalytics(analytics, container) {
        const analyticsHTML = `
            <div class="row g-3">
                <!-- Business Overview: 2-column grid -->
                <div class="col-12 mb-3">
                    <div class="row g-3">
                        <div class="col-6">
                            <div class="analytics-card overview-card">
                                <div class="analytics-icon">
                                    <i class="fas fa-users fa-2x text-primary"></i>
                                </div>
                                <div class="analytics-content">
                                    <h3 class="analytics-number">${analytics.overview.total_users}</h3>
                                    <p class="analytics-label">Total Users</p>
                                    <small class="analytics-trend success">
                                        <i class="fas fa-arrow-up"></i> ${analytics.trends.user_growth}
                                    </small>
                                </div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="analytics-card overview-card">
                                <div class="analytics-icon">
                                    <i class="fas fa-car fa-2x text-success"></i>
                                </div>
                                <div class="analytics-content">
                                    <h3 class="analytics-number">${analytics.overview.total_vehicles}</h3>
                                    <p class="analytics-label">Vehicles Managed</p>
                                    <small class="analytics-trend success">
                                        <i class="fas fa-arrow-up"></i> Active
                                    </small>
                                </div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="analytics-card overview-card">
                                <div class="analytics-icon">
                                    <i class="fas fa-calendar-check fa-2x text-warning"></i>
                                </div>
                                <div class="analytics-content">
                                    <h3 class="analytics-number">${analytics.overview.total_appointments}</h3>
                                    <p class="analytics-label">Appointments</p>
                                    <small class="analytics-trend success">
                                        <i class="fas fa-arrow-up"></i> ${analytics.trends.appointment_growth}
                                    </small>
                                </div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="analytics-card overview-card">
                                <div class="analytics-icon">
                                    <i class="fas fa-tools fa-2x text-info"></i>
                                </div>
                                <div class="analytics-content">
                                    <h3 class="analytics-number">${analytics.overview.active_services}</h3>
                                    <p class="analytics-label">Active Services</p>
                                    <small class="analytics-trend success">
                                        <i class="fas fa-arrow-up"></i> ${analytics.trends.services_growth}
                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Business Metrics -->
                <div class="col-12 col-md-6 d-flex flex-column gap-3">
                    <div class="analytics-card metrics-card mb-3">
                        <h5 class="card-title">
                            <i class="fas fa-chart-line me-2"></i>Business Metrics
                        </h5>
                        <div class="metrics-grid">
                            <div class="metric-item">
                                <span class="metric-label">Customer Satisfaction</span>
                                <span class="metric-value">${analytics.business_metrics.customer_satisfaction}/5.0</span>
                            </div>
                            <div class="metric-item">
                                <span class="metric-label">Service Completion</span>
                                <span class="metric-value">${analytics.business_metrics.service_completion_rate}%</span>
                            </div>
                            <div class="metric-item">
                                <span class="metric-label">On-Time Delivery</span>
                                <span class="metric-value">${analytics.business_metrics.on_time_delivery}%</span>
                            </div>
                            <div class="metric-item">
                                <span class="metric-label">Repeat Customers</span>
                                <span class="metric-value">${analytics.business_metrics.repeat_customers}%</span>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Recent Activity -->
                <div class="col-12 col-md-6 d-flex flex-column gap-3">
                    <div class="analytics-card activity-card">
                        <h5 class="card-title">
                            <i class="fas fa-clock me-2"></i>Recent Activity (30 days)
                        </h5>
                        <div class="activity-list">
                            <div class="activity-item">
                                <i class="fas fa-user-plus text-primary"></i>
                                <span>New Users: <strong>${analytics.recent_activity.new_users_this_month}</strong></span>
                            </div>
                            <div class="activity-item">
                                <i class="fas fa-wrench text-success"></i>
                                <span>Services Completed: <strong>${analytics.recent_activity.services_completed}</strong></span>
                            </div>
                            <div class="activity-item">
                                <i class="fas fa-calendar text-warning"></i>
                                <span>Appointments Booked: <strong>${analytics.recent_activity.appointments_booked}</strong></span>
                            </div>
                            <div class="activity-item">
                                <i class="fas fa-shopping-cart text-info"></i>
                                <span>Products Sold: <strong>${analytics.recent_activity.products_sold}</strong></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        `;

        container.innerHTML = analyticsHTML;
    }

    renderFallbackAnalytics(container) {
        const fallbackHTML = `
            <div class="row g-3">
                <div class="col-12 mb-3">
                    <div class="row g-3">
                        <div class="col-6">
                            <div class="analytics-card overview-card">
                                <div class="analytics-icon">
                                    <i class="fas fa-users fa-2x text-primary"></i>
                                </div>
                                <div class="analytics-content">
                                    <h3 class="analytics-number">150</h3>
                                    <p class="analytics-label">Total Users</p>
                                    <small class="analytics-trend success">
                                        <i class="fas fa-arrow-up"></i> +8%
                                    </small>
                                </div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="analytics-card overview-card">
                                <div class="analytics-icon">
                                    <i class="fas fa-car fa-2x text-success"></i>
                                </div>
                                <div class="analytics-content">
                                    <h3 class="analytics-number">89</h3>
                                    <p class="analytics-label">Vehicles Managed</p>
                                    <small class="analytics-trend success">
                                        <i class="fas fa-arrow-up"></i> Active
                                    </small>
                                </div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="analytics-card overview-card">
                                <div class="analytics-icon">
                                    <i class="fas fa-calendar-check fa-2x text-warning"></i>
                                </div>
                                <div class="analytics-content">
                                    <h3 class="analytics-number">245</h3>
                                    <p class="analytics-label">Appointments</p>
                                    <small class="analytics-trend success">
                                        <i class="fas fa-arrow-up"></i> +15%
                                    </small>
                                </div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="analytics-card overview-card">
                                <div class="analytics-icon">
                                    <i class="fas fa-tools fa-2x text-info"></i>
                                </div>
                                <div class="analytics-content">
                                    <h3 class="analytics-number">16</h3>
                                    <p class="analytics-label">Active Services</p>
                                    <small class="analytics-trend success">
                                        <i class="fas fa-arrow-up"></i> +12%
                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-md-6 d-flex flex-column gap-3">
                    <div class="analytics-card metrics-card mb-3">
                        <h5 class="card-title">
                            <i class="fas fa-chart-line me-2"></i>Business Metrics
                        </h5>
                        <div class="metrics-grid">
                            <div class="metric-item">
                                <span class="metric-label">Customer Satisfaction</span>
                                <span class="metric-value">4.7/5.0</span>
                            </div>
                            <div class="metric-item">
                                <span class="metric-label">Service Completion</span>
                                <span class="metric-value">96%</span>
                            </div>
                            <div class="metric-item">
                                <span class="metric-label">On-Time Delivery</span>
                                <span class="metric-value">94%</span>
                            </div>
                            <div class="metric-item">
                                <span class="metric-label">Repeat Customers</span>
                                <span class="metric-value">78%</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-md-6 d-flex flex-column gap-3">
                    <div class="analytics-card activity-card">
                        <h5 class="card-title">
                            <i class="fas fa-clock me-2"></i>Recent Activity (30 days)
                        </h5>
                        <div class="activity-list">
                            <div class="activity-item">
                                <i class="fas fa-user-plus text-primary"></i>
                                <span>New Users: <strong>5</strong></span>
                            </div>
                            <div class="activity-item">
                                <i class="fas fa-wrench text-success"></i>
                                <span>Services Completed: <strong>90</strong></span>
                            </div>
                            <div class="activity-item">
                                <i class="fas fa-calendar text-warning"></i>
                                <span>Appointments Booked: <strong>69</strong></span>
                            </div>
                            <div class="activity-item">
                                <i class="fas fa-shopping-cart text-info"></i>
                                <span>Products Sold: <strong>13</strong></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        `;
        container.innerHTML = fallbackHTML;
    }

    renderFallbackProducts(container) {
        const fallbackProducts = [
            {
                id: 1,
                name: 'Premium Car Battery',
                description: 'Long-lasting automotive battery with 3-year warranty',
                image: 'assets/images/default-product.jpg',
                price: '2,500',
                reviews: 24
            },
            {
                id: 2,
                name: 'Synthetic Engine Oil',
                description: 'High-performance 5W-30 synthetic motor oil',
                image: 'assets/images/default-product.jpg',
                price: '850',
                reviews: 18
            }
        ];
        
        this.renderProducts(fallbackProducts, container);
    }
}

// Initialize when DOM is ready
new HomepageIntegration(); 