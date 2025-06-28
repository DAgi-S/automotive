<?php
define('INCLUDED', true);
session_start();
if (!isset($_SESSION['password'])) {
    header("location: login.php?message=login first");
    exit;
}

include 'header.php';
include("partial-front/db_con.php");

// Check if car ID is provided
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: my_cars.php?message=No car selected");
    exit;
}

// Get car details
$db = new db_con;
$car_id = (int)$_GET['id'];
$car = $db->get_car_by_id($car_id, $_SESSION['id']);

// If car not found or doesn't belong to user
if (!$car) {
    header("Location: my_cars.php?message=Car not found");
    exit;
}

// Calculate days until insurance expiry
$insurance_date = strtotime($car['insurance']);
$current_date = time();
$days_remaining = round(($insurance_date - $current_date) / (60 * 60 * 24));
$insurance_status = $days_remaining <= 0 ? "Expired" : ($days_remaining <= 30 ? "Expiring Soon" : "Valid");
$insurance_class = $days_remaining <= 0 ? "text-danger" : ($days_remaining <= 30 ? "text-warning" : "text-success");

// Calculate days until next service
$service_date = strtotime($car['service_date']);
$days_to_service = round(($service_date - $current_date) / (60 * 60 * 24));
$service_status = $days_to_service <= 0 ? "Overdue" : ($days_to_service <= 14 ? "Due Soon" : "Scheduled");
$service_class = $days_to_service <= 0 ? "text-danger" : ($days_to_service <= 14 ? "text-warning" : "text-success");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>View Car - Nati Automotive</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta charset="UTF-8">
    <meta name="description" content="Manage your cars at Nati Automotive">
    <link rel="icon" href="assets/images/favicon/Nati-logo.png" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/css/bottom-nav-universal.css">
    <link rel="stylesheet" href="assets/css/new-home-layout.css">
    <link rel="stylesheet" href="assets/css/mobile-header.css">
    <link rel="stylesheet" href="assets/css/profile-compact.css">
    
    
    <style>
        .car-detail-card {
            background: #fff;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 15px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .car-title {
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 15px;
            color: #333;
        }
        .car-detail-section {
            margin-bottom: 20px;
        }
        .section-title {
            font-size: 14px;
            font-weight: bold;
            margin-bottom: 10px;
            color: #555;
        }
        .detail-item {
            display: flex;
            justify-content: space-between;
            padding: 8px 0;
            border-bottom: 1px solid #eee;
        }
        .detail-label {
            color: #666;
            font-size: 13px;
        }
        .detail-value {
            font-weight: 500;
            font-size: 13px;
        }
        .status-badge {
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 11px;
            font-weight: bold;
        }
        .car-images-gallery {
            display: flex;
            gap: 10px;
            margin-top: 15px;
            overflow-x: auto;
        }
        .car-image {
            width: 100%;
            max-width: 250px;
            height: 180px;
            object-fit: cover;
            border-radius: 8px;
            cursor: pointer;
            transition: transform 0.3s;
        }
        .car-image:hover {
            transform: scale(1.05);
        }
        .action-buttons {
            display: flex;
            gap: 10px;
            margin-top: 20px;
        }
        .btn-action {
            flex: 1;
            padding: 10px;
            border-radius: 4px;
            text-align: center;
            text-decoration: none;
            color: white;
            font-weight: bold;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 5px;
        }
        .btn-back {
            background-color: #6c757d;
        }
        .btn-edit {
            background-color: #007bff;
        }
        .btn-delete {
            background-color: #dc3545;
        }
        .btn-notify {
            background-color: #17a2b8;
        }
    </style>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
</head>

<body>
    <div class="site-content">
        
        <div class="container mt-20" style="margin-bottom: 90px;">
            <div class="car-detail-card">
                <div class="car-title">
                    <?php echo htmlspecialchars(($car['brand_name'] ?? '') . ' ' . ($car['model_name'] ?? '') . ' (' . ($car['car_year'] ?? '') . ')'); ?>
                </div>
                
                <div class="car-images-gallery">
                    <img src="assets/img/<?php echo htmlspecialchars($car['img_name1'] ?? ''); ?>" alt="Car Image 1" class="car-image">
                    <img src="assets/img/<?php echo htmlspecialchars($car['img_name2'] ?? ''); ?>" alt="Car Image 2" class="car-image">
                    <img src="assets/img/<?php echo htmlspecialchars($car['img_name3'] ?? ''); ?>" alt="Car Image 3" class="car-image">
                </div>
                
                <div class="car-detail-section">
                    <div class="section-title">Car Information</div>
                    <div class="detail-item">
                        <span class="detail-label">Brand</span>
                        <span class="detail-value"><?php echo htmlspecialchars($car['brand_name'] ?? ''); ?></span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label">Model</span>
                        <span class="detail-value"><?php echo htmlspecialchars($car['model_name'] ?? ''); ?></span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label">Year</span>
                        <span class="detail-value"><?php echo htmlspecialchars($car['car_year'] ?? ''); ?></span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label">Mileage</span>
                        <span class="detail-value"><?php echo htmlspecialchars($car['mile_age'] ?? ''); ?> km</span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label">Plate Number</span>
                        <span class="detail-value"><?php echo htmlspecialchars($car['plate_number'] ?? ''); ?></span>
                    </div>
                    <?php if (!empty($car['trailer_number'])): ?>
                    <div class="detail-item">
                        <span class="detail-label">Trailer Number</span>
                        <span class="detail-value"><?php echo htmlspecialchars($car['trailer_number'] ?? ''); ?></span>
                    </div>
                    <?php endif; ?>
                </div>
                
                <div class="car-detail-section">
                    <div class="section-title">Maintenance Information</div>
                    <div class="detail-item">
                        <span class="detail-label">Service Date</span>
                        <span class="detail-value">
                            <?php echo htmlspecialchars($car['service_date'] ?? ''); ?>
                            <span class="status-badge <?php echo $service_class; ?>">
                                <?php echo $service_status; ?>
                                <?php if ($days_to_service > 0 && $days_to_service <= 30): ?>
                                (<?php echo $days_to_service; ?> days)
                                <?php endif; ?>
                            </span>
                        </span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label">Oil Change</span>
                        <span class="detail-value"><?php echo htmlspecialchars($car['oil_change'] ?? ''); ?></span>
                    </div>
                </div>
                
                <div class="car-detail-section">
                    <div class="section-title">Insurance Information</div>
                    <div class="detail-item">
                        <span class="detail-label">Insurance Expiry</span>
                        <span class="detail-value">
                            <?php echo htmlspecialchars($car['insurance'] ?? ''); ?>
                            <span class="status-badge <?php echo $insurance_class; ?>">
                                <?php echo $insurance_status; ?>
                                <?php if ($days_remaining > 0 && $days_remaining <= 30): ?>
                                (<?php echo $days_remaining; ?> days)
                                <?php endif; ?>
                            </span>
                        </span>
                    </div>
                </div>
                
                <div class="action-buttons" style="flex-wrap: wrap; gap: 10px; justify-content: space-between;">
                    <a href="my_cars.php" class="btn-action btn-back" style="min-width: 110px;">
                        <i class="fas fa-arrow-left"></i> Back
                    </a>
                    <a href="edit_car.php?id=<?php echo $car_id; ?>" class="btn-action btn-edit" style="min-width: 110px;">
                        <i class="fas fa-edit"></i> Edit
                    </a>
                    <a href="#" class="btn-action btn-notify" id="enableNotifications" style="min-width: 150px;">
                        <i class="fas fa-bell"></i> Enable Reminders
                    </a>
                </div>
            </div>
        </div>
        
        <?php include 'partial-front/bottom_nav.php'; ?>
    </div>
    
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Handle notification permission request
        const notifyButton = document.getElementById('enableNotifications');
        
        notifyButton.addEventListener('click', function(e) {
            e.preventDefault();
            
            // Check if browser supports notifications
            if (!('Notification' in window)) {
                alert('This browser does not support notifications');
                return;
            }
            
            // Request permission
            Notification.requestPermission().then(function(permission) {
                if (permission === 'granted') {
                    alert('You will now receive reminders for service and insurance dates');
                    // Here we would register the user for notifications in a real implementation
                    // saveNotificationPreference(<?php echo $car_id; ?>, true);
                }
            });
        });
        
        // Image gallery functionality
        const carImages = document.querySelectorAll('.car-image');
        carImages.forEach(img => {
            img.addEventListener('click', function() {
                // Simple lightbox effect could be added here
                this.classList.toggle('expanded');
            });
        });
    });
    </script>
</body>
</html> 