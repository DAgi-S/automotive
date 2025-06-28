<?php
session_start();

// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

// Include database connection
require_once('../config/database.php');

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'update_hero_slide':
                try {
                    $stmt = $conn->prepare("UPDATE hero_carousel SET title = ?, subtitle = ?, button_text = ?, button_link = ?, is_active = ? WHERE id = ?");
                    $stmt->execute([
                        $_POST['title'],
                        $_POST['subtitle'],
                        $_POST['button_text'],
                        $_POST['button_link'],
                        isset($_POST['is_active']) ? 1 : 0,
                        $_POST['slide_id']
                    ]);
                    $_SESSION['success'] = "Hero slide updated successfully!";
                } catch (PDOException $e) {
                    $_SESSION['error'] = "Failed to update hero slide: " . $e->getMessage();
                }
                break;

            case 'add_hero_slide':
                try {
                    $stmt = $conn->prepare("INSERT INTO hero_carousel (title, subtitle, button_text, button_link, background_image, display_order, is_active) VALUES (?, ?, ?, ?, ?, ?, ?)");
                    $stmt->execute([
                        $_POST['title'],
                        $_POST['subtitle'],
                        $_POST['button_text'],
                        $_POST['button_link'],
                        $_POST['background_image'],
                        $_POST['display_order'],
                        isset($_POST['is_active']) ? 1 : 0
                    ]);
                    $_SESSION['success'] = "Hero slide added successfully!";
                } catch (PDOException $e) {
                    $_SESSION['error'] = "Failed to add hero slide: " . $e->getMessage();
                }
                break;

            case 'update_testimonial':
                try {
                    $stmt = $conn->prepare("UPDATE testimonials SET customer_name = ?, customer_title = ?, testimonial_text = ?, rating = ?, is_active = ? WHERE id = ?");
                    $stmt->execute([
                        $_POST['customer_name'],
                        $_POST['customer_title'],
                        $_POST['testimonial_text'],
                        $_POST['rating'],
                        isset($_POST['is_active']) ? 1 : 0,
                        $_POST['testimonial_id']
                    ]);
                    $_SESSION['success'] = "Testimonial updated successfully!";
                } catch (PDOException $e) {
                    $_SESSION['error'] = "Failed to update testimonial: " . $e->getMessage();
                }
                break;

            case 'add_testimonial':
                try {
                    $stmt = $conn->prepare("INSERT INTO testimonials (customer_name, customer_title, testimonial_text, rating, display_order, is_active) VALUES (?, ?, ?, ?, ?, ?)");
                    $stmt->execute([
                        $_POST['customer_name'],
                        $_POST['customer_title'],
                        $_POST['testimonial_text'],
                        $_POST['rating'],
                        $_POST['display_order'],
                        isset($_POST['is_active']) ? 1 : 0
                    ]);
                    $_SESSION['success'] = "Testimonial added successfully!";
                } catch (PDOException $e) {
                    $_SESSION['error'] = "Failed to add testimonial: " . $e->getMessage();
                }
                break;

            case 'update_faq':
                try {
                    $stmt = $conn->prepare("UPDATE faqs SET question = ?, answer = ?, category = ?, is_active = ? WHERE id = ?");
                    $stmt->execute([
                        $_POST['question'],
                        $_POST['answer'],
                        $_POST['category'],
                        isset($_POST['is_active']) ? 1 : 0,
                        $_POST['faq_id']
                    ]);
                    $_SESSION['success'] = "FAQ updated successfully!";
                } catch (PDOException $e) {
                    $_SESSION['error'] = "Failed to update FAQ: " . $e->getMessage();
                }
                break;

            case 'add_faq':
                try {
                    $stmt = $conn->prepare("INSERT INTO faqs (question, answer, category, display_order, is_active) VALUES (?, ?, ?, ?, ?)");
                    $stmt->execute([
                        $_POST['question'],
                        $_POST['answer'],
                        $_POST['category'],
                        $_POST['display_order'],
                        isset($_POST['is_active']) ? 1 : 0
                    ]);
                    $_SESSION['success'] = "FAQ added successfully!";
                } catch (PDOException $e) {
                    $_SESSION['error'] = "Failed to add FAQ: " . $e->getMessage();
                }
                break;

            case 'update_social_link':
                try {
                    $stmt = $conn->prepare("UPDATE social_media_links SET platform_name = ?, platform_icon = ?, platform_url = ?, platform_color = ?, description = ?, is_active = ? WHERE id = ?");
                    $stmt->execute([
                        $_POST['platform_name'],
                        $_POST['platform_icon'],
                        $_POST['platform_url'],
                        $_POST['platform_color'],
                        $_POST['description'],
                        isset($_POST['is_active']) ? 1 : 0,
                        $_POST['social_id']
                    ]);
                    $_SESSION['success'] = "Social media link updated successfully!";
                } catch (PDOException $e) {
                    $_SESSION['error'] = "Failed to update social media link: " . $e->getMessage();
                }
                break;

            case 'add_social_link':
                try {
                    $stmt = $conn->prepare("INSERT INTO social_media_links (platform_name, platform_icon, platform_url, platform_color, description, display_order, is_active) VALUES (?, ?, ?, ?, ?, ?, ?)");
                    $stmt->execute([
                        $_POST['platform_name'],
                        $_POST['platform_icon'],
                        $_POST['platform_url'],
                        $_POST['platform_color'],
                        $_POST['description'],
                        $_POST['display_order'],
                        isset($_POST['is_active']) ? 1 : 0
                    ]);
                    $_SESSION['success'] = "Social media link added successfully!";
                } catch (PDOException $e) {
                    $_SESSION['error'] = "Failed to add social media link: " . $e->getMessage();
                }
                break;

            case 'update_website_settings':
                try {
                    foreach ($_POST['settings'] as $key => $value) {
                        $stmt = $conn->prepare("UPDATE website_settings SET setting_value = ? WHERE setting_key = ?");
                        $stmt->execute([$value, $key]);
                    }
                    $_SESSION['success'] = "Website settings updated successfully!";
                } catch (PDOException $e) {
                    $_SESSION['error'] = "Failed to update website settings: " . $e->getMessage();
                }
                break;

            case 'delete_item':
                try {
                    $table = $_POST['table'];
                    $id = $_POST['item_id'];
                    
                    // Validate table name for security
                    $allowed_tables = ['hero_carousel', 'testimonials', 'faqs', 'social_media_links', 'quick_links'];
                    if (in_array($table, $allowed_tables)) {
                        $stmt = $conn->prepare("DELETE FROM $table WHERE id = ?");
                        $stmt->execute([$id]);
                        $_SESSION['success'] = "Item deleted successfully!";
                    } else {
                        $_SESSION['error'] = "Invalid table specified.";
                    }
                } catch (PDOException $e) {
                    $_SESSION['error'] = "Failed to delete item: " . $e->getMessage();
                }
                break;
        }
        header("Location: website-content.php");
        exit();
    }
}

// Include header
require_once('includes/header.php');

// Fetch data for display
try {
    // Hero carousel slides
    $stmt = $conn->query("SELECT * FROM hero_carousel ORDER BY display_order ASC");
    $hero_slides = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Testimonials
    $stmt = $conn->query("SELECT * FROM testimonials ORDER BY display_order ASC");
    $testimonials = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // FAQs
    $stmt = $conn->query("SELECT * FROM faqs ORDER BY display_order ASC");
    $faqs = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Social media links
    $stmt = $conn->query("SELECT * FROM social_media_links ORDER BY display_order ASC");
    $social_links = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Website settings grouped
    $stmt = $conn->query("SELECT * FROM website_settings ORDER BY setting_group, setting_key");
    $all_settings = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    $website_settings = [];
    foreach ($all_settings as $setting) {
        $website_settings[$setting['setting_group']][] = $setting;
    }
    
} catch (PDOException $e) {
    $_SESSION['error'] = "Error fetching data: " . $e->getMessage();
    $hero_slides = $testimonials = $faqs = $social_links = $website_settings = [];
}
?>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

<style>
.content-section {
    background: #fff;
    border-radius: 0.35rem;
    box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
    margin-bottom: 2rem;
}

.section-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 1rem 1.5rem;
    border-radius: 0.35rem 0.35rem 0 0;
    margin-bottom: 0;
}

.content-item {
    border: 1px solid #e3e6f0;
    border-radius: 0.35rem;
    padding: 1rem;
    margin-bottom: 1rem;
    transition: all 0.3s ease;
}

.content-item:hover {
    box-shadow: 0 0.25rem 0.75rem rgba(0, 0, 0, 0.1);
    transform: translateY(-2px);
}

.status-badge {
    font-size: 0.75rem;
    padding: 0.25rem 0.5rem;
}

.btn-group-sm .btn {
    font-size: 0.75rem;
    padding: 0.25rem 0.5rem;
}

.settings-group {
    background: #f8f9fc;
    border-radius: 0.35rem;
    padding: 1rem;
    margin-bottom: 1rem;
}

.nav-pills {
    background: #f8f9fc;
    padding: 0.5rem;
    border-radius: 0.5rem;
    margin-bottom: 1.5rem;
}

.nav-pills .nav-link {
    color: #5a5c69;
    border-radius: 0.35rem;
    padding: 0.75rem 1.25rem;
    margin: 0 0.25rem;
    font-weight: 500;
    border: 1px solid transparent;
    transition: all 0.3s ease;
}

.nav-pills .nav-link:hover {
    background-color: rgba(102, 126, 234, 0.1);
    color: #667eea;
    border-color: rgba(102, 126, 234, 0.2);
}

.nav-pills .nav-link.active {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border-color: transparent;
    box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);
}

.nav-pills .nav-link i {
    margin-right: 0.5rem;
}

.form-control:focus {
    border-color: #667eea;
    box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
}

.btn-primary {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border: none;
}

.btn-primary:hover {
    background: linear-gradient(135deg, #5a6fd8 0%, #6a4190 100%);
    transform: translateY(-1px);
}

/* Tab Content Styling */
.tab-content {
    min-height: 400px;
}

.tab-pane {
    opacity: 0;
    transition: opacity 0.3s ease;
}

.tab-pane.active {
    opacity: 1;
}

/* Ensure content is visible */
.content-section {
    display: block !important;
    visibility: visible !important;
}
</style>

<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Website Content Management</h1>
        <div class="btn-group" role="group">
            <button class="btn btn-primary" onclick="window.open('https://www.natiautomotive.com', '_blank')">
                <i class="fas fa-eye"></i> Preview Website
            </button>
        </div>
    </div>

    <?php if (isset($_SESSION['success'])): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?php 
            echo $_SESSION['success'];
            unset($_SESSION['success']);
            ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?php 
            echo $_SESSION['error'];
            unset($_SESSION['error']);
            ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <!-- Debug Info (remove in production) -->
    <div class="alert alert-info alert-dismissible fade show" role="alert">
        <strong>Debug Info:</strong> 
        Hero Slides: <?php echo count($hero_slides); ?> | 
        Testimonials: <?php echo count($testimonials); ?> | 
        FAQs: <?php echo count($faqs); ?> | 
        Social Links: <?php echo count($social_links); ?> | 
        Settings Groups: <?php echo count($website_settings); ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>

    <!-- Navigation Tabs -->
    <ul class="nav nav-pills mb-4" id="contentTabs" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="hero-tab" data-bs-toggle="pill" data-bs-target="#hero" type="button" role="tab" aria-controls="hero" aria-selected="true">
                <i class="fas fa-image"></i> Hero Carousel
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="testimonials-tab" data-bs-toggle="pill" data-bs-target="#testimonials-content" type="button" role="tab" aria-controls="testimonials-content" aria-selected="false">
                <i class="fas fa-quote-left"></i> Testimonials
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="faqs-tab" data-bs-toggle="pill" data-bs-target="#faqs-content" type="button" role="tab" aria-controls="faqs-content" aria-selected="false">
                <i class="fas fa-question-circle"></i> FAQs
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="social-tab" data-bs-toggle="pill" data-bs-target="#social-content" type="button" role="tab" aria-controls="social-content" aria-selected="false">
                <i class="fas fa-share-alt"></i> Social Media
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="settings-tab" data-bs-toggle="pill" data-bs-target="#settings-content" type="button" role="tab" aria-controls="settings-content" aria-selected="false">
                <i class="fas fa-cog"></i> Settings
            </button>
        </li>
    </ul>

    <!-- Tab Content -->
    <div class="tab-content" id="contentTabsContent">
        
        <!-- Hero Carousel Tab -->
        <div class="tab-pane fade show active" id="hero" role="tabpanel">
            <div class="content-section">
                <div class="section-header d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">Hero Carousel Management</h4>
                    <button class="btn btn-light btn-sm" data-bs-toggle="modal" data-bs-target="#addHeroModal">
                        <i class="fas fa-plus"></i> Add Slide
                    </button>
                </div>
                <div class="p-4">
                    <?php foreach ($hero_slides as $slide): ?>
                    <div class="content-item">
                        <div class="row align-items-center">
                            <div class="col-md-8">
                                <h5 class="mb-2"><?php echo htmlspecialchars($slide['title']); ?></h5>
                                <p class="text-muted mb-2"><?php echo htmlspecialchars($slide['subtitle']); ?></p>
                                <div class="d-flex align-items-center">
                                    <span class="badge <?php echo $slide['is_active'] ? 'bg-success' : 'bg-secondary'; ?> status-badge me-2">
                                        <?php echo $slide['is_active'] ? 'Active' : 'Inactive'; ?>
                                    </span>
                                    <small class="text-muted">Order: <?php echo $slide['display_order']; ?></small>
                                </div>
                            </div>
                            <div class="col-md-4 text-end">
                                <div class="btn-group btn-group-sm">
                                    <button class="btn btn-outline-primary edit-hero" 
                                            data-bs-toggle="modal" 
                                            data-bs-target="#editHeroModal"
                                            data-id="<?php echo $slide['id']; ?>"
                                            data-title="<?php echo htmlspecialchars($slide['title']); ?>"
                                            data-subtitle="<?php echo htmlspecialchars($slide['subtitle']); ?>"
                                            data-button-text="<?php echo htmlspecialchars($slide['button_text']); ?>"
                                            data-button-link="<?php echo htmlspecialchars($slide['button_link']); ?>"
                                            data-active="<?php echo $slide['is_active']; ?>">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button class="btn btn-outline-danger delete-item" 
                                            data-table="hero_carousel" 
                                            data-id="<?php echo $slide['id']; ?>"
                                            data-name="<?php echo htmlspecialchars($slide['title']); ?>">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        <!-- Testimonials Tab -->
        <div class="tab-pane fade" id="testimonials-content" role="tabpanel">
            <div class="content-section">
                <div class="section-header d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">Customer Testimonials</h4>
                    <button class="btn btn-light btn-sm" data-bs-toggle="modal" data-bs-target="#addTestimonialModal">
                        <i class="fas fa-plus"></i> Add Testimonial
                    </button>
                </div>
                <div class="p-4">
                    <?php foreach ($testimonials as $testimonial): ?>
                    <div class="content-item">
                        <div class="row align-items-center">
                            <div class="col-md-8">
                                <h5 class="mb-2"><?php echo htmlspecialchars($testimonial['customer_name']); ?></h5>
                                <p class="text-muted mb-2"><?php echo htmlspecialchars($testimonial['testimonial_text']); ?></p>
                                <div class="d-flex align-items-center">
                                    <div class="me-3">
                                        <?php for ($i = 1; $i <= 5; $i++): ?>
                                            <i class="fas fa-star <?php echo $i <= $testimonial['rating'] ? 'text-warning' : 'text-muted'; ?>"></i>
                                        <?php endfor; ?>
                                    </div>
                                    <span class="badge <?php echo $testimonial['is_active'] ? 'bg-success' : 'bg-secondary'; ?> status-badge">
                                        <?php echo $testimonial['is_active'] ? 'Active' : 'Inactive'; ?>
                                    </span>
                                </div>
                            </div>
                            <div class="col-md-4 text-end">
                                <div class="btn-group btn-group-sm">
                                    <button class="btn btn-outline-primary edit-testimonial" 
                                            data-bs-toggle="modal" 
                                            data-bs-target="#editTestimonialModal"
                                            data-id="<?php echo $testimonial['id']; ?>"
                                            data-name="<?php echo htmlspecialchars($testimonial['customer_name']); ?>"
                                            data-title="<?php echo htmlspecialchars($testimonial['customer_title']); ?>"
                                            data-text="<?php echo htmlspecialchars($testimonial['testimonial_text']); ?>"
                                            data-rating="<?php echo $testimonial['rating']; ?>"
                                            data-active="<?php echo $testimonial['is_active']; ?>">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button class="btn btn-outline-danger delete-item" 
                                            data-table="testimonials" 
                                            data-id="<?php echo $testimonial['id']; ?>"
                                            data-name="<?php echo htmlspecialchars($testimonial['customer_name']); ?>">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        <!-- FAQs Tab -->
        <div class="tab-pane fade" id="faqs-content" role="tabpanel">
            <div class="content-section">
                <div class="section-header d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">Frequently Asked Questions</h4>
                    <button class="btn btn-light btn-sm" data-bs-toggle="modal" data-bs-target="#addFaqModal">
                        <i class="fas fa-plus"></i> Add FAQ
                    </button>
                </div>
                <div class="p-4">
                    <?php foreach ($faqs as $faq): ?>
                    <div class="content-item">
                        <div class="row align-items-start">
                            <div class="col-md-8">
                                <h5 class="mb-2"><?php echo htmlspecialchars($faq['question']); ?></h5>
                                <p class="text-muted mb-2"><?php echo htmlspecialchars($faq['answer']); ?></p>
                                <div class="d-flex align-items-center">
                                    <span class="badge bg-info status-badge me-2"><?php echo ucfirst($faq['category']); ?></span>
                                    <span class="badge <?php echo $faq['is_active'] ? 'bg-success' : 'bg-secondary'; ?> status-badge">
                                        <?php echo $faq['is_active'] ? 'Active' : 'Inactive'; ?>
                                    </span>
                                </div>
                            </div>
                            <div class="col-md-4 text-end">
                                <div class="btn-group btn-group-sm">
                                    <button class="btn btn-outline-primary edit-faq" 
                                            data-bs-toggle="modal" 
                                            data-bs-target="#editFaqModal"
                                            data-id="<?php echo $faq['id']; ?>"
                                            data-question="<?php echo htmlspecialchars($faq['question']); ?>"
                                            data-answer="<?php echo htmlspecialchars($faq['answer']); ?>"
                                            data-category="<?php echo htmlspecialchars($faq['category']); ?>"
                                            data-active="<?php echo $faq['is_active']; ?>">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button class="btn btn-outline-danger delete-item" 
                                            data-table="faqs" 
                                            data-id="<?php echo $faq['id']; ?>"
                                            data-name="<?php echo htmlspecialchars($faq['question']); ?>">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        <!-- Social Media Tab -->
        <div class="tab-pane fade" id="social-content" role="tabpanel">
            <div class="content-section">
                <div class="section-header d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">Social Media Links</h4>
                    <button class="btn btn-light btn-sm" data-bs-toggle="modal" data-bs-target="#addSocialModal">
                        <i class="fas fa-plus"></i> Add Platform
                    </button>
                </div>
                <div class="p-4">
                    <?php foreach ($social_links as $social): ?>
                    <div class="content-item">
                        <div class="row align-items-center">
                            <div class="col-md-8">
                                <div class="d-flex align-items-center mb-2">
                                    <i class="<?php echo htmlspecialchars($social['platform_icon']); ?> fa-2x me-3" style="color: <?php echo htmlspecialchars($social['platform_color']); ?>"></i>
                                    <div>
                                        <h5 class="mb-1"><?php echo htmlspecialchars($social['platform_name']); ?></h5>
                                        <p class="text-muted mb-0"><?php echo htmlspecialchars($social['description']); ?></p>
                                    </div>
                                </div>
                                <div class="d-flex align-items-center">
                                    <span class="badge <?php echo $social['is_active'] ? 'bg-success' : 'bg-secondary'; ?> status-badge me-2">
                                        <?php echo $social['is_active'] ? 'Active' : 'Inactive'; ?>
                                    </span>
                                    <small class="text-muted">Order: <?php echo $social['display_order']; ?></small>
                                </div>
                            </div>
                            <div class="col-md-4 text-end">
                                <div class="btn-group btn-group-sm">
                                    <a href="<?php echo htmlspecialchars($social['platform_url']); ?>" 
                                       class="btn btn-outline-info" target="_blank">
                                        <i class="fas fa-external-link-alt"></i>
                                    </a>
                                    <button class="btn btn-outline-primary edit-social" 
                                            data-bs-toggle="modal" 
                                            data-bs-target="#editSocialModal"
                                            data-id="<?php echo $social['id']; ?>"
                                            data-name="<?php echo htmlspecialchars($social['platform_name']); ?>"
                                            data-icon="<?php echo htmlspecialchars($social['platform_icon']); ?>"
                                            data-url="<?php echo htmlspecialchars($social['platform_url']); ?>"
                                            data-color="<?php echo htmlspecialchars($social['platform_color']); ?>"
                                            data-description="<?php echo htmlspecialchars($social['description']); ?>"
                                            data-active="<?php echo $social['is_active']; ?>">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button class="btn btn-outline-danger delete-item" 
                                            data-table="social_media_links" 
                                            data-id="<?php echo $social['id']; ?>"
                                            data-name="<?php echo htmlspecialchars($social['platform_name']); ?>">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        <!-- Settings Tab -->
        <div class="tab-pane fade" id="settings-content" role="tabpanel">
            <div class="content-section">
                <div class="section-header">
                    <h4 class="mb-0">Website Settings</h4>
                </div>
                <div class="p-4">
                    <form method="POST">
                        <input type="hidden" name="action" value="update_website_settings">
                        
                        <?php foreach ($website_settings as $group => $settings): ?>
                        <div class="settings-group">
                            <h5 class="mb-3 text-capitalize"><?php echo str_replace('_', ' ', $group); ?> Settings</h5>
                            <div class="row">
                                <?php foreach ($settings as $setting): ?>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label"><?php echo ucwords(str_replace('_', ' ', $setting['setting_key'])); ?></label>
                                    <?php if ($setting['setting_type'] === 'textarea'): ?>
                                        <textarea class="form-control" 
                                                  name="settings[<?php echo $setting['setting_key']; ?>]" 
                                                  rows="3"><?php echo htmlspecialchars($setting['setting_value']); ?></textarea>
                                    <?php elseif ($setting['setting_type'] === 'boolean'): ?>
                                        <div class="form-check">
                                            <input type="checkbox" class="form-check-input" 
                                                   name="settings[<?php echo $setting['setting_key']; ?>]" 
                                                   value="1" <?php echo $setting['setting_value'] ? 'checked' : ''; ?>>
                                            <label class="form-check-label">Enable</label>
                                        </div>
                                    <?php else: ?>
                                        <input type="<?php echo $setting['setting_type'] === 'number' ? 'number' : 'text'; ?>" 
                                               class="form-control" 
                                               name="settings[<?php echo $setting['setting_key']; ?>]" 
                                               value="<?php echo htmlspecialchars($setting['setting_value']); ?>">
                                    <?php endif; ?>
                                    <?php if ($setting['setting_description']): ?>
                                        <small class="form-text text-muted"><?php echo htmlspecialchars($setting['setting_description']); ?></small>
                                    <?php endif; ?>
                                </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                        <?php endforeach; ?>
                        
                        <div class="text-center">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="fas fa-save"></i> Save All Settings
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Include Modals -->
<?php include 'components/website-content-modals.php'; ?>

<script>
document.addEventListener('DOMContentLoaded', function() {
    console.log('Website Content Management: Initializing...');
    
    // Debug: Check if Bootstrap is loaded
    if (typeof bootstrap === 'undefined') {
        console.error('Bootstrap is not loaded!');
        return;
    }
    
    // Initialize Bootstrap tabs explicitly with better error handling
    const triggerTabList = [].slice.call(document.querySelectorAll('#contentTabs button[data-bs-toggle="pill"]'));
    console.log('Found tabs:', triggerTabList.length);
    
    triggerTabList.forEach(function (triggerEl, index) {
        console.log(`Initializing tab ${index + 1}:`, triggerEl.id);
        
        try {
            const tabTrigger = new bootstrap.Tab(triggerEl);
            
            triggerEl.addEventListener('click', function (event) {
                event.preventDefault();
                console.log('Tab clicked:', this.id, 'Target:', this.getAttribute('data-bs-target'));
                
                // Remove active class from all tabs and content
                document.querySelectorAll('#contentTabs .nav-link').forEach(tab => {
                    tab.classList.remove('active');
                    tab.setAttribute('aria-selected', 'false');
                });
                document.querySelectorAll('.tab-pane').forEach(pane => {
                    pane.classList.remove('show', 'active');
                });
                
                // Add active class to clicked tab
                this.classList.add('active');
                this.setAttribute('aria-selected', 'true');
                
                // Show target content
                const targetId = this.getAttribute('data-bs-target');
                const targetPane = document.querySelector(targetId);
                if (targetPane) {
                    targetPane.classList.add('show', 'active');
                    console.log('Activated pane:', targetId);
                } else {
                    console.error('Target pane not found:', targetId);
                }
            });
        } catch (error) {
            console.error('Error initializing tab:', triggerEl.id, error);
        }
    });
    
    // Ensure first tab is active
    const firstTab = document.querySelector('#contentTabs .nav-link');
    const firstPane = document.querySelector('.tab-pane');
    if (firstTab && firstPane) {
        firstTab.classList.add('active');
        firstTab.setAttribute('aria-selected', 'true');
        firstPane.classList.add('show', 'active');
        console.log('First tab activated');
    }
    
    // Handle edit buttons
    document.querySelectorAll('.edit-hero').forEach(button => {
        button.addEventListener('click', function() {
            console.log('Edit hero button clicked');
            const modal = document.getElementById('editHeroModal');
            if (modal) {
                modal.querySelector('#edit_slide_id').value = this.dataset.id;
                modal.querySelector('#edit_hero_title').value = this.dataset.title;
                modal.querySelector('#edit_hero_subtitle').value = this.dataset.subtitle;
                modal.querySelector('#edit_hero_button_text').value = this.dataset.buttonText;
                modal.querySelector('#edit_hero_button_link').value = this.dataset.buttonLink;
                modal.querySelector('#edit_hero_is_active').checked = this.dataset.active == '1';
            }
        });
    });

    document.querySelectorAll('.edit-testimonial').forEach(button => {
        button.addEventListener('click', function() {
            console.log('Edit testimonial button clicked');
            const modal = document.getElementById('editTestimonialModal');
            if (modal) {
                modal.querySelector('#edit_testimonial_id').value = this.dataset.id;
                modal.querySelector('#edit_testimonial_name').value = this.dataset.name;
                modal.querySelector('#edit_testimonial_title').value = this.dataset.title;
                modal.querySelector('#edit_testimonial_text').value = this.dataset.text;
                modal.querySelector('#edit_testimonial_rating').value = this.dataset.rating;
                modal.querySelector('#edit_testimonial_is_active').checked = this.dataset.active == '1';
            }
        });
    });

    document.querySelectorAll('.edit-faq').forEach(button => {
        button.addEventListener('click', function() {
            console.log('Edit FAQ button clicked');
            const modal = document.getElementById('editFaqModal');
            if (modal) {
                modal.querySelector('#edit_faq_id').value = this.dataset.id;
                modal.querySelector('#edit_faq_question').value = this.dataset.question;
                modal.querySelector('#edit_faq_answer').value = this.dataset.answer;
                modal.querySelector('#edit_faq_category').value = this.dataset.category;
                modal.querySelector('#edit_faq_is_active').checked = this.dataset.active == '1';
            }
        });
    });

    document.querySelectorAll('.edit-social').forEach(button => {
        button.addEventListener('click', function() {
            console.log('Edit social button clicked');
            const modal = document.getElementById('editSocialModal');
            if (modal) {
                modal.querySelector('#edit_social_id').value = this.dataset.id;
                modal.querySelector('#edit_social_name').value = this.dataset.name;
                modal.querySelector('#edit_social_icon').value = this.dataset.icon;
                modal.querySelector('#edit_social_url').value = this.dataset.url;
                modal.querySelector('#edit_social_color').value = this.dataset.color;
                modal.querySelector('#edit_social_description').value = this.dataset.description;
                modal.querySelector('#edit_social_is_active').checked = this.dataset.active == '1';
            }
        });
    });

    // Handle delete buttons
    document.querySelectorAll('.delete-item').forEach(button => {
        button.addEventListener('click', function() {
            const table = this.dataset.table;
            const id = this.dataset.id;
            const name = this.dataset.name;
            
            if (confirm(`Are you sure you want to delete "${name}"? This action cannot be undone.`)) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.innerHTML = `
                    <input type="hidden" name="action" value="delete_item">
                    <input type="hidden" name="table" value="${table}">
                    <input type="hidden" name="item_id" value="${id}">
                `;
                document.body.appendChild(form);
                form.submit();
            }
        });
    });

    // Auto-dismiss alerts
    document.querySelectorAll('.alert').forEach(alert => {
        setTimeout(() => {
            const closeButton = alert.querySelector('.btn-close');
            if (closeButton && !alert.classList.contains('alert-info')) { // Keep debug info visible
                closeButton.click();
            }
        }, 5000);
    });
    
    console.log('Website Content Management: Initialization complete');
});
</script>

<?php require_once('includes/footer.php'); ?> 