<?php
/**
 * Website Content Management Helper Functions
 * 
 * This file contains helper functions to fetch dynamic content
 * from the website content management system.
 */

/**
 * Safely escape HTML special characters, handling null values
 * 
 * @param mixed $string The string to escape
 * @param string $default Default value if string is null
 * @return string Escaped string or default
 */
function safeHtmlspecialchars($string, $default = '') {
    return htmlspecialchars($string ?? $default, ENT_QUOTES, 'UTF-8');
}

/**
 * Get website setting value by key
 * 
 * @param PDO $conn Database connection
 * @param string $key Setting key
 * @param mixed $default Default value if setting not found
 * @return mixed Setting value or default
 */
function getWebsiteSetting($conn, $key, $default = null) {
    try {
        $stmt = $conn->prepare("SELECT setting_value FROM website_settings WHERE setting_key = ?");
        $stmt->execute([$key]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ? $result['setting_value'] : $default;
    } catch (PDOException $e) {
        error_log("Error fetching website setting: " . $e->getMessage());
        return $default;
    }
}

/**
 * Get active hero carousel slides
 * 
 * @param PDO $conn Database connection
 * @return array Array of hero slides
 */
function getHeroSlides($conn) {
    try {
        $stmt = $conn->query("SELECT * FROM active_hero_slides");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Error fetching hero slides: " . $e->getMessage());
        return [];
    }
}

/**
 * Get active testimonials
 * 
 * @param PDO $conn Database connection
 * @param int $limit Number of testimonials to fetch
 * @return array Array of testimonials
 */
function getTestimonials($conn, $limit = null) {
    try {
        $sql = "SELECT * FROM active_testimonials";
        if ($limit) {
            $sql .= " LIMIT " . intval($limit);
        }
        $stmt = $conn->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Error fetching testimonials: " . $e->getMessage());
        return [];
    }
}

/**
 * Get active FAQs
 * 
 * @param PDO $conn Database connection
 * @param string $category Filter by category (optional)
 * @param int $limit Number of FAQs to fetch
 * @return array Array of FAQs
 */
function getFAQs($conn, $category = null, $limit = null) {
    try {
        $sql = "SELECT * FROM active_faqs";
        $params = [];
        
        if ($category) {
            $sql .= " WHERE category = ?";
            $params[] = $category;
        }
        
        if ($limit) {
            $sql .= " LIMIT " . intval($limit);
        }
        
        $stmt = $conn->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Error fetching FAQs: " . $e->getMessage());
        return [];
    }
}

/**
 * Get active social media links
 * 
 * @param PDO $conn Database connection
 * @return array Array of social media links
 */
function getSocialLinks($conn) {
    try {
        $stmt = $conn->query("SELECT * FROM active_social_links");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Error fetching social links: " . $e->getMessage());
        return [];
    }
}

/**
 * Get active quick links
 * 
 * @param PDO $conn Database connection
 * @return array Array of quick links
 */
function getQuickLinks($conn) {
    try {
        $stmt = $conn->query("SELECT * FROM active_quick_links");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Error fetching quick links: " . $e->getMessage());
        return [];
    }
}

/**
 * Get business hours
 * 
 * @param PDO $conn Database connection
 * @return array Array of business hours
 */
function getBusinessHours($conn) {
    try {
        $stmt = $conn->query("SELECT * FROM website_business_hours");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Error fetching business hours: " . $e->getMessage());
        return [];
    }
}

/**
 * Get active analytics metrics configuration
 * 
 * @param PDO $conn Database connection
 * @return array Array of analytics metrics
 */
function getAnalyticsMetrics($conn) {
    try {
        $stmt = $conn->query("SELECT * FROM active_analytics_metrics");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Error fetching analytics metrics: " . $e->getMessage());
        return [];
    }
}

/**
 * Check if a website section is active
 * 
 * @param PDO $conn Database connection
 * @param string $sectionKey Section key to check
 * @return bool True if section is active
 */
function isSectionActive($conn, $sectionKey) {
    try {
        $stmt = $conn->prepare("SELECT is_active FROM website_sections WHERE section_key = ?");
        $stmt->execute([$sectionKey]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ? (bool)$result['is_active'] : true; // Default to active if not found
    } catch (PDOException $e) {
        error_log("Error checking section status: " . $e->getMessage());
        return true; // Default to active on error
    }
}

/**
 * Get formatted business hours for display
 * 
 * @param PDO $conn Database connection
 * @return array Formatted business hours
 */
function getFormattedBusinessHours($conn) {
    $hours = getBusinessHours($conn);
    $formatted = [];
    
    foreach ($hours as $hour) {
        $day = $hour['day_name'];
        
        if ($hour['is_closed']) {
            $formatted[] = [
                'day' => $day,
                'hours' => 'Closed',
                'display' => $day . ' - Closed'
            ];
        } else {
            $opening = date('H:i', strtotime($hour['opening_time']));
            $closing = date('H:i', strtotime($hour['closing_time']));
            $formatted[] = [
                'day' => $day,
                'hours' => $opening . ' - ' . $closing,
                'display' => $day . ' - ' . $opening . ' - ' . $closing
            ];
        }
    }
    
    return $formatted;
}

/**
 * Get website analytics data with configuration
 * 
 * @param PDO $conn Database connection
 * @return array Analytics data with display configuration
 */
function getWebsiteAnalytics($conn) {
    $metrics = getAnalyticsMetrics($conn);
    $analytics = [];
    
    foreach ($metrics as $metric) {
        $value = 0;
        
        // Fetch actual values based on metric key
        try {
            switch ($metric['metric_key']) {
                case 'completed_services':
                    $stmt = $conn->query("SELECT COUNT(*) as count FROM tbl_appointments WHERE status = 'completed'");
                    $value = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
                    break;
                    
                case 'total_clients':
                    $stmt = $conn->query("SELECT COUNT(*) as count FROM tbl_user");
                    $value = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
                    break;
                    
                case 'total_vehicles':
                    $stmt = $conn->query("SELECT COUNT(*) as count FROM tbl_vehicles");
                    $value = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
                    break;
                    
                case 'total_workers':
                    $stmt = $conn->query("SELECT COUNT(*) as count FROM tbl_worker");
                    $value = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
                    break;
                    
                case 'active_users':
                    $stmt = $conn->query("SELECT COUNT(DISTINCT user_id) as count FROM tbl_appointments");
                    $value = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
                    break;
                    
                case 'total_appointments':
                    $stmt = $conn->query("SELECT COUNT(*) as count FROM tbl_appointments");
                    $value = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
                    break;
            }
        } catch (PDOException $e) {
            error_log("Error fetching analytics data for {$metric['metric_key']}: " . $e->getMessage());
        }
        
        $analytics[] = [
            'key' => $metric['metric_key'],
            'title' => $metric['metric_title'],
            'icon' => $metric['metric_icon'],
            'description' => $metric['metric_description'],
            'color' => $metric['metric_color'],
            'value' => $value,
            'formatted_value' => number_format($value)
        ];
    }
    
    return $analytics;
}

/**
 * Render analytics display section HTML
 * 
 * @param PDO $conn Database connection
 * @return string HTML for analytics display
 */
function renderAnalyticsDisplay($conn) {
    $analytics = getWebsiteAnalytics($conn);
    
    if (empty($analytics)) {
        return '<div class="alert alert-info">No analytics metrics configured.</div>';
    }
    
    $html = '<div class="row g-4">';
    
    foreach ($analytics as $metric) {
        $html .= '<div class="col-lg-2 col-md-4 col-6">';
        $html .= '<div class="analytics-card text-center p-3 rounded bg-white bg-opacity-10">';
        $html .= '<div class="analytics-icon mb-2">';
        $html .= '<i class="fas ' . htmlspecialchars($metric['icon']) . ' fa-2x" style="color: ' . htmlspecialchars($metric['color']) . ';"></i>';
        $html .= '</div>';
        $html .= '<h4 class="analytics-value mb-1">' . htmlspecialchars($metric['formatted_value']) . '</h4>';
        $html .= '<p class="analytics-label mb-0 small opacity-75">' . htmlspecialchars($metric['title']) . '</p>';
        $html .= '</div>';
        $html .= '</div>';
    }
    
    $html .= '</div>';
    
    return $html;
}

/**
 * Render hero carousel HTML
 * 
 * @param PDO $conn Database connection
 * @return string HTML for hero carousel
 */
function renderHeroCarousel($conn) {
    $slides = getHeroSlides($conn);
    
    if (empty($slides)) {
        return '<div class="alert alert-info">No hero slides configured.</div>';
    }
    
    $autoplay = getWebsiteSetting($conn, 'hero_carousel_autoplay', '1') === '1';
    $interval = getWebsiteSetting($conn, 'hero_carousel_interval', '5000');
    
    $html = '<div class="hero-section mb-4">';
    $html .= '<div id="heroCarousel" class="carousel slide"' . ($autoplay ? ' data-bs-ride="carousel"' : '') . ' data-bs-interval="' . $interval . '">';
    $html .= '<div class="carousel-inner">';
    
    foreach ($slides as $index => $slide) {
        $active = $index === 0 ? 'active' : '';
        $html .= '<div class="carousel-item ' . $active . '">';
        $html .= '<img src="' . htmlspecialchars($slide['background_image']) . '" class="d-block w-100 rounded" alt="' . htmlspecialchars($slide['title']) . '" style="height:260px;object-fit:cover;">';
        $html .= '<div class="carousel-caption d-none d-md-block">';
        $html .= '<h1 class="display-4">' . htmlspecialchars($slide['title']) . '</h1>';
        if ($slide['subtitle']) {
            $html .= '<p class="lead">' . htmlspecialchars($slide['subtitle']) . '</p>';
        }
        if ($slide['button_text'] && $slide['button_link']) {
            $html .= '<a class="btn btn-primary btn-lg" href="' . htmlspecialchars($slide['button_link']) . '" role="button">' . htmlspecialchars($slide['button_text']) . '</a>';
        }
        $html .= '</div>';
        $html .= '</div>';
    }
    
    $html .= '</div>';
    
    // Add controls if more than one slide
    if (count($slides) > 1) {
        $html .= '<button class="carousel-control-prev" type="button" data-bs-target="#heroCarousel" data-bs-slide="prev">';
        $html .= '<span class="carousel-control-prev-icon" aria-hidden="true"></span>';
        $html .= '<span class="visually-hidden">Previous</span>';
        $html .= '</button>';
        $html .= '<button class="carousel-control-next" type="button" data-bs-target="#heroCarousel" data-bs-slide="next">';
        $html .= '<span class="carousel-control-next-icon" aria-hidden="true"></span>';
        $html .= '<span class="visually-hidden">Next</span>';
        $html .= '</button>';
    }
    
    $html .= '</div>';
    $html .= '</div>';
    
    return $html;
}

/**
 * Render testimonials carousel HTML
 * 
 * @param PDO $conn Database connection
 * @return string HTML for testimonials carousel
 */
function renderTestimonialsCarousel($conn) {
    if (!isSectionActive($conn, 'testimonials')) {
        return '';
    }
    
    $testimonials = getTestimonials($conn);
    
    if (empty($testimonials)) {
        return '';
    }
    
    $html = '<section class="testimonial-section mb-5">';
    $html .= '<div class="text-center mb-4">';
    $html .= '<h2 class="section-title">' . getWebsiteSetting($conn, 'testimonials_title', 'What Our Customers Say') . '</h2>';
    $html .= '<p class="section-subtitle text-muted">' . getWebsiteSetting($conn, 'testimonials_subtitle', 'Real feedback from satisfied customers') . '</p>';
    $html .= '</div>';
    
    $html .= '<div id="testimonialCarousel" class="carousel slide" data-bs-ride="carousel" data-bs-interval="5000">';
    $html .= '<div class="carousel-inner">';
    
    foreach ($testimonials as $index => $testimonial) {
        $active = $index === 0 ? 'active' : '';
        $html .= '<div class="carousel-item ' . $active . '">';
        $html .= '<div class="row justify-content-center">';
        $html .= '<div class="col-md-8">';
        $html .= '<div class="testimonial-card text-center p-4">';
        $html .= '<div class="testimonial-stars mb-3">';
        for ($i = 1; $i <= 5; $i++) {
            $starClass = $i <= $testimonial['rating'] ? 'fas fa-star text-warning' : 'far fa-star text-muted';
            $html .= '<i class="' . $starClass . ' me-1"></i>';
        }
        $html .= '</div>';
        $html .= '<blockquote class="blockquote">';
        $html .= '<p class="mb-4 fst-italic">"' . htmlspecialchars($testimonial['testimonial_text']) . '"</p>';
        $html .= '<footer class="blockquote-footer mt-3">';
        $html .= '<strong>' . htmlspecialchars($testimonial['customer_name']) . '</strong>';
        if ($testimonial['customer_title']) {
            $html .= ' <cite class="text-muted">• ' . htmlspecialchars($testimonial['customer_title']) . '</cite>';
        }
        $html .= '</footer>';
        $html .= '</blockquote>';
        $html .= '</div>';
        $html .= '</div>';
        $html .= '</div>';
        $html .= '</div>';
    }
    
    $html .= '</div>';
    
    // Add controls if more than one testimonial
    if (count($testimonials) > 1) {
        $html .= '<button class="carousel-control-prev" type="button" data-bs-target="#testimonialCarousel" data-bs-slide="prev">';
        $html .= '<span class="carousel-control-prev-icon" aria-hidden="true"></span>';
        $html .= '<span class="visually-hidden">Previous</span>';
        $html .= '</button>';
        $html .= '<button class="carousel-control-next" type="button" data-bs-target="#testimonialCarousel" data-bs-slide="next">';
        $html .= '<span class="carousel-control-next-icon" aria-hidden="true"></span>';
        $html .= '<span class="visually-hidden">Next</span>';
        $html .= '</button>';
        
        // Add indicators
        $html .= '<div class="carousel-indicators">';
        for ($i = 0; $i < count($testimonials); $i++) {
            $active = $i === 0 ? 'active' : '';
            $html .= '<button type="button" data-bs-target="#testimonialCarousel" data-bs-slide-to="' . $i . '" class="' . $active . '" aria-label="Slide ' . ($i + 1) . '"></button>';
        }
        $html .= '</div>';
    }
    
    $html .= '</div>';
    $html .= '</section>';
    
    return $html;
}

/**
 * Render FAQs accordion HTML
 * 
 * @param PDO $conn Database connection
 * @return string HTML for FAQs accordion
 */
function renderFAQsAccordion($conn) {
    if (!isSectionActive($conn, 'faqs')) {
        return '';
    }
    
    $faqs = getFAQs($conn);
    
    if (empty($faqs)) {
        return '';
    }
    
    $html = '<section class="faq-section mb-5">';
    $html .= '<div class="text-center mb-4">';
    $html .= '<h2 class="section-title">' . getWebsiteSetting($conn, 'faqs_title', 'Frequently Asked Questions') . '</h2>';
    $html .= '<p class="section-subtitle text-muted">' . getWebsiteSetting($conn, 'faqs_subtitle', 'Find answers to common questions') . '</p>';
    $html .= '</div>';
    
    $html .= '<div class="accordion" id="faqAccordion">';
    
    foreach ($faqs as $index => $faq) {
        $id = 'faq' . ($index + 1);
        $expanded = $index === 0 ? 'true' : 'false';
        $show = $index === 0 ? 'show' : '';
        $collapsed = $index === 0 ? '' : 'collapsed';
        
        $html .= '<div class="accordion-item">';
        $html .= '<h2 class="accordion-header" id="' . $id . 'Heading">';
        $html .= '<button class="accordion-button ' . $collapsed . '" type="button" data-bs-toggle="collapse" data-bs-target="#' . $id . 'Collapse" aria-expanded="' . $expanded . '" aria-controls="' . $id . 'Collapse">';
        $html .= '<i class="fas fa-question-circle text-primary me-2"></i>';
        $html .= htmlspecialchars($faq['question']);
        $html .= '</button>';
        $html .= '</h2>';
        $html .= '<div id="' . $id . 'Collapse" class="accordion-collapse collapse ' . $show . '" aria-labelledby="' . $id . 'Heading" data-bs-parent="#faqAccordion">';
        $html .= '<div class="accordion-body">';
        $html .= '<p class="mb-0">' . htmlspecialchars($faq['answer']) . '</p>';
        if ($faq['category'] !== 'general') {
            $html .= '<small class="text-muted mt-2 d-block"><i class="fas fa-tag me-1"></i>Category: ' . ucfirst($faq['category']) . '</small>';
        }
        $html .= '</div>';
        $html .= '</div>';
        $html .= '</div>';
    }
    
    $html .= '</div>';
    $html .= '</section>';
    
    return $html;
}

/**
 * Render social media connect section HTML
 * 
 * @param PDO $conn Database connection
 * @return string HTML for social media connect section
 */
function renderSocialConnectSection($conn) {
    $socialLinks = getSocialLinks($conn);
    
    if (empty($socialLinks)) {
        return '<div class="alert alert-info">No social media links configured.</div>';
    }
    
    $html = '<div class="row g-3">';
    
    foreach ($socialLinks as $social) {
        $html .= '<div class="col-12">';
        $html .= '<div class="card border-0 text-white h-100 social-card" style="background: ' . htmlspecialchars($social['platform_color']) . ' !important;">';
        $html .= '<div class="card-body text-center p-3">';
        $html .= '<div class="d-flex align-items-center">';
        $html .= '<i class="' . htmlspecialchars($social['platform_icon']) . ' fa-2x me-3"></i>';
        $html .= '<div class="text-start flex-grow-1">';
        $html .= '<h6 class="card-title mb-1">' . htmlspecialchars($social['platform_name']) . '</h6>';
        $html .= '<p class="card-text small mb-0">' . htmlspecialchars($social['description']) . '</p>';
        $html .= '</div>';
        $html .= '<a href="' . htmlspecialchars($social['platform_url']) . '" class="btn btn-light btn-sm ms-2" target="_blank">';
        $html .= '<i class="fas fa-external-link-alt"></i>';
        $html .= '</a>';
        $html .= '</div>';
        $html .= '</div>';
        $html .= '</div>';
        $html .= '</div>';
    }
    
    $html .= '</div>';
    
    return $html;
}

/**
 * Render quick links sidebar HTML
 * 
 * @param PDO $conn Database connection
 * @return string HTML for quick links
 */
function renderQuickLinks($conn) {
    $quickLinks = getQuickLinks($conn);
    
    if (empty($quickLinks)) {
        return '<p class="text-muted small">No quick links available.</p>';
    }
    
    $html = '<div class="quick-links-list">';
    
    foreach ($quickLinks as $link) {
        $html .= '<div class="quick-link-item mb-2">';
        $html .= '<a href="' . htmlspecialchars($link['link_url']) . '" class="text-decoration-none d-flex align-items-center p-2 rounded transition-all">';
        if ($link['link_icon']) {
            $html .= '<i class="' . htmlspecialchars($link['link_icon']) . ' text-primary me-3 fa-fw"></i>';
        }
        $html .= '<span>' . htmlspecialchars($link['link_title']) . '</span>';
        $html .= '<i class="fas fa-chevron-right ms-auto text-muted small"></i>';
        $html .= '</a>';
        $html .= '</div>';
    }
    
    $html .= '</div>';
    
    return $html;
}

/**
 * Render business hours sidebar HTML
 * 
 * @param PDO $conn Database connection
 * @return string HTML for business hours
 */
function renderBusinessHours($conn) {
    $hours = getFormattedBusinessHours($conn);
    
    if (empty($hours)) {
        return '<p class="text-muted small">Business hours not available.</p>';
    }
    
    $html = '<div class="business-hours-container">';
    
    // Get current day
    $currentDay = date('l'); // Full day name (e.g., Monday)
    
    foreach ($hours as $hour) {
        $isToday = strtolower($hour['day']) === strtolower($currentDay);
        $dayClass = $isToday ? 'fw-bold text-primary' : '';
        $hoursClass = $isToday ? 'fw-bold' : 'text-muted';
        
        $html .= '<div class="business-hours-item d-flex justify-content-between align-items-center py-2 border-bottom">';
        $html .= '<span class="day ' . $dayClass . '">' . htmlspecialchars($hour['day']) . '</span>';
        
        if ($hour['hours'] === 'Closed') {
            $html .= '<span class="hours text-danger small">Closed</span>';
        } else {
            $html .= '<span class="hours ' . $hoursClass . ' small">' . htmlspecialchars($hour['hours']) . '</span>';
        }
        
        $html .= '</div>';
    }
    
    // Add current status
    $html .= '<div class="current-status mt-3 p-2 rounded" style="background: #f8f9fa;">';
    $html .= '<div class="d-flex align-items-center">';
    $html .= '<i class="fas fa-clock text-primary me-2"></i>';
    $html .= '<small class="text-muted">Current Status: </small>';
    
    // Simple open/closed logic (you can enhance this)
    $currentHour = (int)date('H');
    if ($currentHour >= 8 && $currentHour < 18) {
        $html .= '<span class="badge bg-success ms-1">Open</span>';
    } else {
        $html .= '<span class="badge bg-danger ms-1">Closed</span>';
    }
    
    $html .= '</div>';
    $html .= '</div>';
    
    $html .= '</div>';
    
    return $html;
}
?> 