<?php
define('INCLUDED', true);
session_start();

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

if (!isset($_SESSION['password'])) {
    header("location: login.php?message=login first");
    exit;
}

try {
include("partial-front/db_con.php");
include("db_conn.php");
} catch (Exception $e) {
    echo "Error loading database connection: " . $e->getMessage();
    exit;
}

// Handle Delete Request
if (isset($_POST['delete_car'])) {
    $car_id = (int)$_POST['car_id'];
    $db = new DB_con;
    if ($db->delete_car($car_id, $_SESSION['id'])) {
        header("Location: my_cars.php?message=Car deleted successfully");
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>My Cars - Nati Automotive</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta charset="UTF-8">
    <meta name="description" content="Manage your cars at Nati Automotive">
    <link rel="icon" href="assets/images/favicon/Nati-logo.png" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/css/bottom-nav-universal.css">
    <link rel="stylesheet" href="assets/css/my-cars.css?v=<?php echo time(); ?>">
</head>

<body>
    <div class="site-content cars-content">
        <!-- Mobile Header Navigation -->
        <header class="mobile-header-cars">
            <div class="header-container">
                <div class="header-left">
                    <button class="back-btn" onclick="history.back()">
                        <i class="fas fa-arrow-left"></i>
                    </button>
                    <div class="header-title">
                        <h1>My Cars</h1>
                    </div>
                </div>
                <div class="header-right">
                    <button class="notifications-btn" onclick="toggleNotifications()">
                        <i class="fas fa-bell"></i>
                        <span class="notification-badge">2</span>
                    </button>
                    <button class="menu-btn" onclick="toggleMenu()">
                        <i class="fas fa-ellipsis-v"></i>
                    </button>
                </div>
            </div>
        </header>
        
        <div class="container">
            <?php
            $db = new DB_con;
            $cars = $db->get_user_cars($_SESSION['id']);
            ?>
            
            <div class="cars-grid-container">
                <?php
            if ($cars && count($cars) > 0) {
                foreach ($cars as $car) {
                    // Calculate days until insurance expiry
                    $insurance_date = strtotime($car['insurance']);
                    $current_date = time();
                    $days_remaining = round(($insurance_date - $current_date) / (60 * 60 * 24));
                    $needs_attention = $days_remaining <= 30;
                    ?>
                        <div class="car-card <?php echo $needs_attention ? 'urgent' : ''; ?>" 
                             data-bs-toggle="modal" 
                             data-bs-target="#carModal<?php echo $car['id']; ?>">
                            
                        <?php if ($needs_attention): ?>
                            <div class="urgent-badge" title="Insurance expiring soon">
                                <i class="fas fa-exclamation"></i>
                        </div>
                        <?php endif; ?>
                        
                            <div class="car-content-wrapper">
                                <div class="car-image-wrapper">
                                    <img src="assets/img/<?php echo htmlspecialchars($car['img_name1']); ?>" 
                                         alt="Car Image" class="car-image-main">
                                </div>
                                
                        <div class="car-info">
                                    <h4 class="car-title"><?php echo htmlspecialchars($car['brand_name']); ?> <?php echo htmlspecialchars($car['model_name']); ?></h4>
                                    <div class="car-year"><?php echo htmlspecialchars($car['car_year']); ?></div>
                                    
                                    <div class="car-quick-info">
                                        <div class="info-item">
                                            <i class="fas fa-calendar"></i>
                                            <span><?php echo date('M d', strtotime($car['service_date'])); ?></span>
                                        </div>
                                        <div class="info-item">
                                            <i class="fas fa-tachometer-alt"></i>
                                            <span><?php echo number_format($car['mile_age']); ?>km</span>
                                        </div>
                                        <div class="info-item <?php echo $needs_attention ? 'urgent' : ''; ?>">
                                            <i class="fas fa-shield-alt"></i>
                                            <span>
                                    <?php if ($needs_attention): ?>
                                                    <?php echo $days_remaining; ?>d left
                                                <?php else: ?>
                                                    OK
                                    <?php endif; ?>
                                </span>
                                        </div>
                                    </div>
                        </div>
                        </div>
                            
                        <div class="car-actions">
                                <button type="button" class="btn-action btn-view" 
                                        data-bs-toggle="modal" 
                                        data-bs-target="#carModal<?php echo $car['id']; ?>" 
                                        onclick="event.stopPropagation();">
                                <i class="fas fa-eye"></i>
                                    <span>View</span>
                                </button>
                                <button type="button" class="btn-action btn-edit" 
                                        onclick="openEditModal(<?php echo $car['id']; ?>); event.stopPropagation();">
                                <i class="fas fa-edit"></i>
                                    <span>Edit</span>
                                </button>
                                <button type="button" class="btn-action btn-delete" 
                                        onclick="openDeleteModal(<?php echo $car['id']; ?>, '<?php echo htmlspecialchars($car['brand_name'] . ' ' . $car['model_name']); ?>'); event.stopPropagation();" 
                                        style="width: 100%;">
                                    <i class="fas fa-trash"></i>
                                    <span>Delete</span>
                                </button>
                            </div>
                        </div>

                        <!-- Car Details Modal -->
                        <div class="modal fade" id="carModal<?php echo $car['id']; ?>" tabindex="-1" aria-labelledby="carModalLabel<?php echo $car['id']; ?>" aria-hidden="true">
                            <div class="modal-dialog modal-lg">
                                <div class="modal-content">
                                    <div class="modal-header" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white;">
                                        <h5 class="modal-title" id="carModalLabel<?php echo $car['id']; ?>">
                                            <i class="fas fa-car me-2"></i>
                                            <?php echo htmlspecialchars($car['brand_name']); ?> <?php echo htmlspecialchars($car['model_name']); ?>
                                        </h5>
                                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="car-images-gallery">
                                                    <div class="main-image mb-3">
                                                        <img src="assets/img/<?php echo htmlspecialchars($car['img_name1']); ?>" 
                                                             alt="Car Main Image" 
                                                             class="img-fluid rounded" 
                                                             style="width: 100%; height: 200px; object-fit: cover;">
                                                    </div>
                                                    <div class="thumbnail-images d-flex gap-2">
                                                        <img src="assets/img/<?php echo htmlspecialchars($car['img_name1']); ?>" 
                                                             alt="Car Image 1" 
                                                             class="img-thumbnail" 
                                                             style="width: 60px; height: 45px; object-fit: cover; cursor: pointer;">
                                                        <img src="assets/img/<?php echo htmlspecialchars($car['img_name2']); ?>" 
                                                             alt="Car Image 2" 
                                                             class="img-thumbnail" 
                                                             style="width: 60px; height: 45px; object-fit: cover; cursor: pointer;">
                                                        <img src="assets/img/<?php echo htmlspecialchars($car['img_name3']); ?>" 
                                                             alt="Car Image 3" 
                                                             class="img-thumbnail" 
                                                             style="width: 60px; height: 45px; object-fit: cover; cursor: pointer;">
                                                    </div>
                                                </div>
                                                
                                                <?php if ($needs_attention): ?>
                                                <div class="alert alert-warning mt-3" style="padding: 0.75rem; font-size: 0.85rem;">
                                                    <i class="fas fa-exclamation-triangle me-2"></i>
                                                    <strong>Attention Required!</strong><br>
                                                    Insurance expires in <?php echo $days_remaining; ?> days.
                                                </div>
                                                <?php endif; ?>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="car-details-grid">
                                                    <div class="detail-item mb-3">
                                                        <label class="detail-label fw-bold text-muted" style="font-size: 0.8rem;">YEAR</label>
                                                        <div class="detail-value" style="font-size: 1rem; color: #2c3e50; font-weight: 600;"><?php echo htmlspecialchars($car['car_year']); ?></div>
                                                    </div>
                                                    <div class="detail-item mb-3">
                                                        <label class="detail-label fw-bold text-muted" style="font-size: 0.8rem;">LAST SERVICE</label>
                                                        <div class="detail-value" style="font-size: 1rem; color: #2c3e50; font-weight: 600;"><?php echo date('M d, Y', strtotime($car['service_date'])); ?></div>
                                                    </div>
                                                    <div class="detail-item mb-3">
                                                        <label class="detail-label fw-bold text-muted" style="font-size: 0.8rem;">MILEAGE</label>
                                                        <div class="detail-value" style="font-size: 1rem; color: #2c3e50; font-weight: 600;"><?php echo number_format($car['mile_age']); ?> km</div>
                                                    </div>
                                                    <div class="detail-item mb-3">
                                                        <label class="detail-label fw-bold text-muted" style="font-size: 0.8rem;">OIL CHANGE</label>
                                                        <div class="detail-value" style="font-size: 1rem; color: #2c3e50; font-weight: 600;"><?php echo date('M d, Y', strtotime($car['oil_change'])); ?></div>
                                                    </div>
                                                    <div class="detail-item mb-3">
                                                        <label class="detail-label fw-bold text-muted" style="font-size: 0.8rem;">INSURANCE EXPIRY</label>
                                                        <div class="detail-value <?php echo $needs_attention ? 'text-danger' : ''; ?>" style="font-size: 1rem; font-weight: 600;">
                                                            <?php echo date('M d, Y', strtotime($car['insurance'])); ?>
                                                            <?php if ($needs_attention): ?>
                                                            <br><small class="text-danger">(<?php echo $days_remaining; ?> days left)</small>
                                                            <?php endif; ?>
                                                        </div>
                                                    </div>
                                                    <div class="detail-item mb-3">
                                                        <label class="detail-label fw-bold text-muted" style="font-size: 0.8rem;">PLATE NUMBER</label>
                                                        <div class="detail-value" style="font-size: 1rem; color: #2c3e50; font-weight: 600;"><?php echo htmlspecialchars($car['plate_number'] ?? 'Not specified'); ?></div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="modal-footer d-flex justify-content-between">
                                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                                            <i class="fas fa-times me-1"></i>Close
                                        </button>
                                        <div class="d-flex gap-2">
                                            <a href="edit_car.php?id=<?php echo $car['id']; ?>" class="btn btn-outline-primary">
                                                <i class="fas fa-edit me-1"></i>Edit Car
                                            </a>
                                            <a href="order_service.php?car_id=<?php echo $car['id']; ?>" class="btn btn-primary">
                                                <i class="fas fa-calendar-plus me-1"></i>Book Service
                                            </a>
                                        </div>
                                    </div>
                                </div>
                        </div>
                    </div>
                    <?php
                }
            } else {
                    echo '<div class="no-cars-message">
                            <div class="no-cars-content">
                                <div class="no-cars-icon">
                                    <i class="fas fa-car-side"></i>
                                </div>
                                <h5 class="no-cars-title">No Cars Added</h5>
                                <p class="no-cars-text">Add your first car to get started with managing your automotive services and maintenance schedules.</p>
                                <a href="add_car.php" class="btn-add-first-car">
                                    <i class="fas fa-plus me-2"></i>Add Your First Car
                                </a>
                            </div>
                          </div>';
                }
                ?>
            </div>
           
            <!-- Enhanced Floating Add New Car Button -->
            <button class="floating-add-btn" onclick="openAddModal()" title="Add New Car" aria-label="Add New Car">
                <i class="fas fa-plus"></i>
            </button>
        </div>
        
        <!-- CRUD Modals -->
        <?php include 'partial-front/bottom_nav.php'; ?>
    </div>

    <script src="assets/js/jquery.min.js"></script>
    <script src="assets/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/my-cars-enhanced.js?v=<?php echo time(); ?>"></script>

    <script>
        // Header functionality functions
        function toggleNotifications() {
            console.log('Notifications toggled');
            // You can implement notification dropdown here
            alert('Notifications feature - coming soon!');
        }
        
        function toggleMenu() {
            console.log('Menu toggled');
            // Simple menu options
            const choice = confirm('Menu Options:\n\nOK = Settings\nCancel = Logout');
            
            if (choice) {
                // Settings
                window.location.href = 'settings.php';
            } else {
                // Logout
                if (confirm('Are you sure you want to logout?')) {
                    window.location.href = 'logout.php';
                }
            }
        }
        
        // Add car card click enhancement
        $(document).ready(function() {
            // Enhanced car card interactions
            $('.car-card').on('click', function(e) {
                // Prevent modal opening when clicking on buttons
                if ($(e.target).closest('.car-actions, .btn-action').length) {
                    return;
                }
                
                // Get the modal target and open it
                const modalTarget = $(this).data('bs-target');
                if (modalTarget) {
                    $(modalTarget).modal('show');
                }
            });
            
            // Image gallery functionality for car detail modals
            $('.thumbnail-images img').on('click', function() {
                const newSrc = $(this).attr('src');
                $(this).closest('.car-images-gallery').find('.main-image img').attr('src', newSrc);
            });
            
            // Add loading animation to action buttons
            $('.btn-action').on('click', function() {
                const $btn = $(this);
                const originalContent = $btn.html();
                
                $btn.html('<i class="fas fa-spinner fa-spin"></i> Loading...');
                
                setTimeout(() => {
                    $btn.html(originalContent);
                }, 1000);
            });
    });
    </script>
</body>
</html> 