<?php
session_start(); // Start the session
define('INCLUDED', true);

// Check if the session 'password' is set
if (!isset($_SESSION['password'])) {
    header("location: login.php?message=login first"); // Redirect to the login page if the session is not set
    exit;
}

include 'header.php';
include("partial-front/db_con.php");
include("db_conn.php");

$success_message = '';
$error_message = '';
$user_id = $_SESSION['id'];

// Get service ID from URL if available
$service_id = isset($_GET['service_id']) ? (int)$_GET['service_id'] : 0;

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $car_id = isset($_POST['car_id']) ? (int)$_POST['car_id'] : 0;
    $service_ids = isset($_POST['service_ids']) ? $_POST['service_ids'] : [];
    $appointment_date = isset($_POST['appointment_date']) ? $_POST['appointment_date'] : '';
    $appointment_time = isset($_POST['appointment_time']) ? $_POST['appointment_time'] : '';
    $notes = isset($_POST['notes']) ? trim($_POST['notes']) : '';
    
    if ($car_id > 0 && !empty($service_ids) && !empty($appointment_date) && !empty($appointment_time)) {
        $success = true;
        $conn->begin_transaction();
        
        try {
        foreach ($service_ids as $service_id) {
                $stmt = $conn->prepare("INSERT INTO tbl_appointments (user_id, service_id, appointment_date, appointment_time, status, notes, created_at) VALUES (?, ?, ?, ?, 'pending', ?, NOW())");
                $stmt->bind_param("iisss", $user_id, $service_id, $appointment_date, $appointment_time, $notes);
            
            if (!$stmt->execute()) {
                    throw new Exception("Failed to create appointment: " . $stmt->error);
            }
                
                $appointment_id = $conn->insert_id;
            $stmt->close();
        }
        
            $conn->commit();
            $success_message = "Service appointments created successfully!";
            $_SESSION['booking_success'] = true;
            $_SESSION['booking_details'] = [
                'appointment_date' => $appointment_date,
                'appointment_time' => $appointment_time,
                'services' => $service_ids
            ];
            
        } catch (Exception $e) {
            $conn->rollback();
            $error_message = $e->getMessage();
            $success = false;
        }
    } else {
        $error_message = "Please fill in all required fields";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Nati Automotive - Order Service</title>
    
    <!-- CSS -->
    <link rel="stylesheet" href="assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/css/bottom-nav-universal.css">
    <link rel="stylesheet" href="assets/css/service_order.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <!-- Add Flatpickr for better date/time picking -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    
    <style>
        /* Mobile Header for Service Order */
        .mobile-header {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            height: 60px;
            background: linear-gradient(135deg, #3498db 0%, #2980b9 100%);
            color: white;
            z-index: 1000;
            box-shadow: 0 2px 10px rgba(52, 152, 219, 0.3);
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 1rem;
        }
        
        .header-left {
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }
        
        .back-btn {
            background: none;
            border: none;
            color: white;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s ease;
        }
        
        .back-btn:hover {
            background: rgba(255, 255, 255, 0.2);
            transform: scale(1.1);
        }
        
        .header-title h1 {
            font-size: 1.1rem;
            font-weight: 700;
            margin: 0;
            color: white;
        }
        
        .header-right {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .help-btn {
            background: none;
            border: none;
            color: white;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s ease;
        }
        
        .help-btn:hover {
            background: rgba(255, 255, 255, 0.2);
            transform: scale(1.1);
        }
        
        /* Adjust body padding for fixed header */
        body {
            padding-top: 60px;
        }
        
        .site-content {
            padding-top: 1rem;
        }
        
        /* Enhanced step indicators */
        .progress-steps .step-item i {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            background: #e9ecef;
            color: #6c757d;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 0.5rem;
            transition: all 0.3s ease;
        }
        
        .progress-steps .step-item.active i {
            background: #3498db;
            color: white;
            transform: scale(1.1);
        }
        
        .progress-steps .step-item.completed i {
            background: #27ae60;
            color: white;
        }
        
        /* Enhanced service cards */
        .service-card {
            position: relative;
            overflow: hidden;
        }
        
        .service-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(52, 152, 219, 0.1), transparent);
            transition: left 0.5s ease;
        }
        
        .service-card:hover::before {
            left: 100%;
        }
        
        .service-card .form-check-input:checked + .form-check-label {
            color: #3498db;
        }
        
        .service-card .form-check-input:checked + .form-check-label .service-title {
            color: #3498db;
        }
        
        /* Enhanced time slots */
        .time-slot {
            position: relative;
            overflow: hidden;
        }
        
        .time-slot::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, #3498db 0%, #2980b9 100%);
            transform: scaleX(0);
            transform-origin: left;
            transition: transform 0.3s ease;
            z-index: -1;
        }
        
        .time-slot.selected::before {
            transform: scaleX(1);
        }
        
        /* Enhanced navigation buttons */
        .navigation-buttons {
            background: linear-gradient(to top, rgba(255,255,255,0.95), rgba(255,255,255,0.9));
            backdrop-filter: blur(10px);
        }
        
        .btn-next {
            background: linear-gradient(135deg, #3498db 0%, #2980b9 100%);
            border: none;
            position: relative;
            overflow: hidden;
        }
        
        .btn-next::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
            transition: left 0.5s ease;
        }
        
        .btn-next:hover::before {
            left: 100%;
        }
        
        /* Mobile responsive adjustments */
        @media (max-width: 768px) {
            .mobile-header {
                height: 50px;
            }
            
            body {
                padding-top: 50px;
        }
        
            .header-title h1 {
                font-size: 1rem;
        }
        
            .back-btn, .help-btn {
                width: 35px;
                height: 35px;
        }

            .progress-steps .step-item i {
                width: 28px;
                height: 28px;
                font-size: 0.8rem;
            }
            
            .progress-steps .step-item span {
                font-size: 0.7rem;
            }
        }
    </style>
</head>
<body>
    
    <!-- Success Modal -->
    <div class="modal fade" id="bookingSuccessModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-body text-center p-4">
                    <div class="success-icon mb-3">
                        <i class="fas fa-check-circle" style="font-size: 48px; color: #28a745;"></i>
                    </div>
                    <h5 style="font-size: 14px; font-weight: 600;">Booking Confirmed!</h5>
                    <p style="font-size: 11px; color: #666; margin: 10px 0;">Your service appointment has been successfully scheduled.</p>
                    <div class="booking-details mt-3" style="font-size: 11px; text-align: left; background: #f8f9fa; padding: 15px; border-radius: 8px;">
                        <div id="modalBookingSummary">
                            <!-- Booking details will be populated here -->
                        </div>
                    </div>
                    <div class="mt-3">
                        <button type="button" class="btn btn-primary me-2" style="font-size: 11px;" onclick="printBookingOrder()">
                            <i class="fas fa-print"></i> Print Order
                        </button>
                        <a href="profile.php?tab=appointments" class="btn btn-success" style="font-size: 11px;">
                            <i class="fas fa-user"></i> View in Profile
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Printable Booking Order (hidden by default) -->
    <div id="printableBookingOrder" style="display: none;">
        <div class="print-container" style="max-width: 800px; margin: 0 auto; padding: 20px;">
            <div class="print-header" style="text-align: center; margin-bottom: 30px;">
                <img src="assets/images/logo.png" alt="Nati Automotive Logo" style="max-width: 200px; margin-bottom: 15px;">
                <h2 style="margin: 10px 0; color: #333; font-size: 24px;">Service Booking Order</h2>
        </div>
            
            <div class="print-booking-details" style="margin-bottom: 30px;">
                <div class="print-section">
                    <h3 style="font-size: 18px; color: #444; margin-bottom: 10px; border-bottom: 2px solid #eee; padding-bottom: 5px;">
                        Booking Information
                    </h3>
                    <table style="width: 100%; border-collapse: collapse;">
                        <tr>
                            <td style="padding: 8px 0; width: 150px;"><strong>Order Number:</strong></td>
                            <td id="print-order-number"></td>
                        </tr>
                        <tr>
                            <td style="padding: 8px 0;"><strong>Booking Date:</strong></td>
                            <td id="print-booking-date"></td>
                        </tr>
                        <tr>
                            <td style="padding: 8px 0;"><strong>Customer Name:</strong></td>
                            <td id="print-customer-name"></td>
                        </tr>
                    </table>
    </div>

                <div class="print-section" style="margin-top: 20px;">
                    <h3 style="font-size: 18px; color: #444; margin-bottom: 10px; border-bottom: 2px solid #eee; padding-bottom: 5px;">
                        Vehicle Details
                    </h3>
                    <table style="width: 100%; border-collapse: collapse;">
                        <tr>
                            <td style="padding: 8px 0; width: 150px;"><strong>Vehicle:</strong></td>
                            <td id="print-vehicle-details"></td>
                        </tr>
                    </table>
                    </div>

                <div class="print-section" style="margin-top: 20px;">
                    <h3 style="font-size: 18px; color: #444; margin-bottom: 10px; border-bottom: 2px solid #eee; padding-bottom: 5px;">
                        Service Details
                    </h3>
                    <table style="width: 100%; border-collapse: collapse; margin-top: 10px;">
                        <thead>
                            <tr style="background-color: #f8f9fa;">
                                <th style="padding: 10px; text-align: left; border-bottom: 2px solid #ddd;">Service</th>
                                <th style="padding: 10px; text-align: right; border-bottom: 2px solid #ddd;">Price</th>
                            </tr>
                        </thead>
                        <tbody id="print-services-list">
                            <!-- Services will be populated here -->
                        </tbody>
                        <tfoot>
                            <tr>
                                <td style="padding: 10px; text-align: right; border-top: 2px solid #ddd;"><strong>Total Amount:</strong></td>
                                <td id="print-total-amount" style="padding: 10px; text-align: right; border-top: 2px solid #ddd;"></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                <div class="print-section" style="margin-top: 20px;">
                    <h3 style="font-size: 18px; color: #444; margin-bottom: 10px; border-bottom: 2px solid #eee; padding-bottom: 5px;">
                        Appointment Schedule
                    </h3>
                    <table style="width: 100%; border-collapse: collapse;">
                        <tr>
                            <td style="padding: 8px 0; width: 150px;"><strong>Date:</strong></td>
                            <td id="print-appointment-date"></td>
                        </tr>
                        <tr>
                            <td style="padding: 8px 0;"><strong>Time:</strong></td>
                            <td id="print-appointment-time"></td>
                        </tr>
                    </table>
                </div>
            </div>

            <div class="print-footer" style="margin-top: 40px; text-align: center; font-size: 12px; color: #666;">
                <p>Thank you for choosing Nati Automotive!</p>
                <p>For any inquiries, please contact us at: +251 911 123456</p>
                <p>Address: Addis Ababa, Ethiopia</p>
                    </div>
        </div>
    </div>

    <!-- Mobile Header -->
    <header class="mobile-header">
        <div class="header-left">
            <button class="back-btn" onclick="window.history.back()">
                <i class="fas fa-arrow-left"></i>
            </button>
            <div class="header-title">
                <h1>Book Service</h1>
            </div>
        </div>
        <div class="header-right">
            <button class="help-btn" onclick="showHelp()">
                <i class="fas fa-question-circle"></i>
            </button>
        </div>
    </header>

    <div class="site-content">
        <div class="container">
            <!-- Progress Steps -->
            <div class="progress-steps">
                <div class="step-item active" data-step="1">
                        <i class="fas fa-car"></i>
                    <span>Vehicle</span>
                    </div>
                <div class="step-item" data-step="2">
                        <i class="fas fa-tools"></i>
                    <span>Services</span>
                    </div>
                <div class="step-item" data-step="3">
                        <i class="fas fa-calendar-alt"></i>
                    <span>Schedule</span>
                    </div>
                <div class="step-item" data-step="4">
                        <i class="fas fa-check-circle"></i>
                    <span>Confirm</span>
                </div>
            </div>

            <form id="service-booking-form" method="POST">
                <!-- Step 1: Vehicle Selection -->
                <div class="booking-step active" id="step-1">
                        <div class="card">
                        <div class="card-header">
                            <h5 class="card-title">
                                <i class="fas fa-car"></i>
                                Select Your Vehicle
                            </h5>
                        </div>
                            <div class="card-body">
                                    <?php
                                    $cars_query = "SELECT id, car_brand, car_model, car_year FROM tbl_info WHERE userid = ?";
                                    $stmt = $conn->prepare($cars_query);
                                    $stmt->bind_param("i", $user_id);
                                    $stmt->execute();
                                    $cars_result = $stmt->get_result();
                                    
                                    if ($cars_result->num_rows > 0):
                                    ?>
                            <div class="vehicle-selector">
                                <input type="hidden" name="car_id" id="car_id" required>
                                            <?php while ($car = $cars_result->fetch_assoc()): ?>
                                <div class="vehicle-option" onclick="selectVehicle(<?php echo $car['id']; ?>)" data-vehicle-id="<?php echo $car['id']; ?>">
                                    <div class="vehicle-info">
                                        <div class="vehicle-icon">
                                            <i class="fas fa-car"></i>
                                        </div>
                                        <div class="vehicle-details">
                                            <h6><?php echo htmlspecialchars($car['car_brand'] . ' ' . $car['car_model']); ?></h6>
                                            <small class="text-muted">Year: <?php echo htmlspecialchars($car['car_year']); ?></small>
                                        </div>
                                    </div>
                                    <div class="vehicle-select-indicator">
                                        <i class="fas fa-check-circle"></i>
                                    </div>
                                </div>
                                            <?php endwhile; ?>
                            </div>
                                    <?php else: ?>
                            <div class="add-vehicle-card">
                                <div class="text-center">
                                    <i class="fas fa-plus-circle fa-3x text-muted mb-3"></i>
                                    <h6>No Vehicles Registered</h6>
                                    <p class="text-muted mb-3">You need to add a vehicle before booking a service.</p>
                                    <a href="add_car.php" class="btn btn-primary">
                                        <i class="fas fa-plus"></i> Add New Vehicle
                                    </a>
                                </div>
                            </div>
                                    <?php endif; ?>
                        </div>
                    </div>
                </div>
                
                <!-- Step 2: Service Selection -->
                <div class="booking-step" id="step-2">
                        <div class="card">
                        <div class="card-header">
                            <h5 class="card-title">
                                <i class="fas fa-tools"></i>
                                Select Services
                            </h5>
                        </div>
                            <div class="card-body">
                            <div class="services-grid">
                                    <?php
                                    $services_query = "SELECT service_id, service_name, description, price FROM tbl_services WHERE status = 'active' ORDER BY service_name";
                                    $services_result = $conn->query($services_query);
                                
                                // Service icons mapping
                                $service_icons = [
                                    'Oil Change' => 'fas fa-oil-can',
                                    'Brake Service' => 'fas fa-circle-notch',
                                    'Engine Diagnostic' => 'fas fa-search',
                                    'Tire Service' => 'fas fa-circle',
                                    'Battery Service' => 'fas fa-battery-full',
                                    'AC Service' => 'fas fa-snowflake',
                                    'Transmission' => 'fas fa-cogs',
                                    'Suspension' => 'fas fa-car-side',
                                    'default' => 'fas fa-wrench'
                                ];
                                    
                                    if ($services_result && $services_result->num_rows > 0):
                                        while ($service = $services_result->fetch_assoc()):
                                        $icon = $service_icons[$service['service_name']] ?? $service_icons['default'];
                                    ?>
                                <div class="service-card" onclick="toggleService(<?php echo $service['service_id']; ?>)">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="service_ids[]" 
                                               value="<?php echo $service['service_id']; ?>" 
                                               id="service_<?php echo $service['service_id']; ?>"
                                               data-price="<?php echo $service['price']; ?>">
                                        <label class="form-check-label w-100" for="service_<?php echo $service['service_id']; ?>">
                                            <div class="service-header">
                                                <div class="service-icon">
                                                    <i class="<?php echo $icon; ?>"></i>
                                                </div>
                                                <div class="service-info">
                                            <div class="service-title"><?php echo htmlspecialchars($service['service_name']); ?></div>
                                            <div class="service-price">Br <?php echo number_format($service['price'], 2); ?></div>
                                                </div>
                                            </div>
                                            <div class="service-description"><?php echo htmlspecialchars($service['description']); ?></div>
                                            </label>
                                    </div>
                                        </div>
                                    <?php 
                                        endwhile;
                                else:
                                    ?>
                                <div class="text-center p-4">
                                    <i class="fas fa-tools fa-3x text-muted mb-3"></i>
                                    <p class="text-muted">No services available at the moment.</p>
                                    <a href="contact.php" class="btn btn-primary">Contact Us</a>
                        </div>
                                <?php endif; ?>
                            </div>
                            
                            <!-- Service Summary -->
                            <div class="service-summary mt-4 p-3 bg-light rounded" id="service-summary" style="display: none;">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="mb-1">Selected Services</h6>
                                        <small class="text-muted" id="selected-count">0 services selected</small>
                                    </div>
                                    <div class="text-end">
                                        <div class="h5 mb-0 text-primary" id="total-price">Br 0.00</div>
                                        <small class="text-muted">Total</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Step 3: Schedule -->
                <div class="booking-step" id="step-3">
                        <div class="card">
                        <div class="card-header">
                            <h5 class="card-title">
                                <i class="fas fa-calendar-alt"></i>
                                Schedule Appointment
                            </h5>
                        </div>
                            <div class="card-body">
                            <div class="schedule-section">
                                <div class="form-group">
                                    <label class="form-label">
                                        <i class="fas fa-calendar me-2"></i>Select Date
                                    </label>
                                    <input type="text" id="appointment_date" name="appointment_date" class="form-control" required placeholder="Choose your preferred date">
                                    <small class="form-text text-muted">Service is available Monday to Saturday</small>
                                </div>
                                
                                <div class="form-group">
                                    <label class="form-label">
                                        <i class="fas fa-clock me-2"></i>Select Time
                                    </label>
                                <input type="hidden" id="appointment_time" name="appointment_time" required>
                                    <div class="time-slots-container">
                                        <div id="time-slots-loading" class="text-center p-4" style="display: none;">
                                            <div class="spinner-border text-primary" role="status">
                                                <span class="visually-hidden">Loading available times...</span>
                            </div>
                                            <p class="mt-2 text-muted">Loading available time slots...</p>
                        </div>
                                        <div class="time-slots" id="time-slots-container">
                                            <!-- Time slots will be loaded here -->
                                        </div>
                                    </div>
                                    <small class="form-text text-muted">Available times: 8:00 AM - 5:00 PM</small>
                                </div>
                                
                                <!-- Additional Notes -->
                                <div class="form-group">
                                    <label class="form-label">
                                        <i class="fas fa-sticky-note me-2"></i>Additional Notes (Optional)
                                    </label>
                                    <textarea name="notes" id="notes" class="form-control" rows="3" placeholder="Any specific requirements or notes for your service..."></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Step 4: Confirm -->
                <div class="booking-step" id="step-4">
                        <div class="card">
                        <div class="card-header">
                            <h5 class="card-title">
                                <i class="fas fa-check-circle"></i>
                                Review & Confirm
                            </h5>
                        </div>
                            <div class="card-body">
                            <div class="booking-summary" id="booking-summary">
                                <!-- Summary will be populated by JavaScript -->
                                <div class="text-center p-4">
                                    <i class="fas fa-clipboard-list fa-3x text-muted mb-3"></i>
                                    <p class="text-muted">Please complete the previous steps to see your booking summary.</p>
                                        </div>
                                        </div>
                            
                            <!-- Terms and Conditions -->
                            <div class="terms-section mt-4 p-3 bg-light rounded">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="terms-agreement" required>
                                    <label class="form-check-label" for="terms-agreement">
                                        I agree to the <a href="terms.php" target="_blank">Terms and Conditions</a> and understand the service policies.
                                    </label>
                                </div>
                                <small class="text-muted d-block mt-2">
                                    <i class="fas fa-info-circle me-1"></i>
                                    By confirming this booking, you agree to our cancellation policy and service terms.
                                </small>
                            </div>
                        </div>
                                        </div>
                                    </div>
            </form>
                                </div>
                                
        <!-- Navigation Buttons -->
        <div class="navigation-buttons">
            <div class="btn-container">
                <button type="button" class="btn btn-back" onclick="previousStep()" style="display: none;">
                    <i class="fas fa-arrow-left"></i> Back
                </button>
                <button type="button" class="btn btn-next" onclick="nextStep()" disabled>
                    Next <i class="fas fa-arrow-right"></i>
                </button>
                                </div>
                                </div>
    </div>
    
    <?php include 'partial-front/bottom_nav.php'; ?>
    <?php include 'option.php'; ?>

    <!-- Scripts -->
    <script src="assets/js/jquery.min.js"></script>
    <script src="assets/js/bootstrap.bundle.min.js"></script>
    <script src="https://kit.fontawesome.com/your-font-awesome-kit.js"></script>
    
    <?php if ($success_message): ?>
    <script>
        $(document).ready(function() {
            // Copy booking summary to modal
            const summaryContent = document.getElementById('booking-summary').innerHTML;
            document.getElementById('modalBookingSummary').innerHTML = summaryContent;
            
            // Show success modal using Bootstrap 5 modal
            const successModal = new bootstrap.Modal(document.getElementById('bookingSuccessModal'), {
                backdrop: 'static',
                keyboard: false
            });
            successModal.show();
        });
    </script>
    <?php endif; ?>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize Flatpickr
        flatpickr("#appointment_date", {
            minDate: "today",
            disable: [
                function(date) {
                    // Disable Sundays
                    return date.getDay() === 0;
                }
            ],
            onChange: function(selectedDates, dateStr) {
                loadTimeSlots(dateStr);
            },
            dateFormat: "Y-m-d",
            allowInput: false
        });
        
        // Initialize service selection handlers
        initializeServiceSelection();
        
        // Initialize navigation buttons
        initializeNavigation();
        
        // Auto-select service if passed in URL
        const urlParams = new URLSearchParams(window.location.search);
        const serviceId = urlParams.get('service_id');
        if (serviceId) {
            const serviceCheckbox = document.getElementById(`service_${serviceId}`);
            if (serviceCheckbox) {
                serviceCheckbox.checked = true;
                updateServiceSummary();
            }
        }
    });
    
    function initializeNavigation() {
        // Ensure navigation buttons are visible
        const navButtons = document.querySelector('.navigation-buttons');
        const nextBtn = document.querySelector('.btn-next');
        const backBtn = document.querySelector('.btn-back');
        
        if (navButtons) {
            navButtons.style.display = 'block';
            navButtons.style.visibility = 'visible';
        }
        
        if (nextBtn) {
            nextBtn.style.display = 'inline-flex';
            nextBtn.style.visibility = 'visible';
            nextBtn.disabled = true; // Start disabled until vehicle is selected
        }
        
        if (backBtn) {
            backBtn.style.display = 'none'; // Hide on first step
        }
        
        // Initialize step display
        showStep(1);
    }

    // Vehicle selection function
    function selectVehicle(vehicleId) {
        // Remove selection from all vehicles
        document.querySelectorAll('.vehicle-option').forEach(option => {
            option.classList.remove('selected');
        });
        
        // Select the clicked vehicle
        const selectedOption = document.querySelector(`[data-vehicle-id="${vehicleId}"]`);
        if (selectedOption) {
            selectedOption.classList.add('selected');
            
            // Get vehicle name for feedback
            const vehicleName = selectedOption.querySelector('.vehicle-details h6').textContent;
            
            // Update hidden input
            document.getElementById('car_id').value = vehicleId;
            
            // Enable next button
            const nextBtn = document.querySelector('.btn-next');
            if (nextBtn) {
                nextBtn.disabled = false;
                nextBtn.classList.remove('btn-disabled');
                nextBtn.style.display = 'inline-flex';
                nextBtn.style.visibility = 'visible';
            }
            
            // Show success feedback
            showAlert('Vehicle selected: ' + vehicleName, 'success');
            
            // Auto-advance to next step after 1.5 seconds
            setTimeout(() => {
                if (currentStep === 1) {
                    nextStep();
                }
            }, 1500);
        }
    }

    // Service toggle function
    function toggleService(serviceId) {
        const checkbox = document.getElementById(`service_${serviceId}`);
        checkbox.checked = !checkbox.checked;
        updateServiceSummary();
    }

    // Help function
    function showHelp() {
        const helpModal = `
            <div class="modal fade" id="helpModal" tabindex="-1">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">
                                <i class="fas fa-question-circle me-2"></i>Booking Help
                            </h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <div class="help-section">
                                <h6><i class="fas fa-car me-2"></i>Step 1: Vehicle Selection</h6>
                                <p>Choose the vehicle you want to service from your registered vehicles.</p>
                            </div>
                            <div class="help-section">
                                <h6><i class="fas fa-tools me-2"></i>Step 2: Service Selection</h6>
                                <p>Select one or more services you need. You can see the total cost as you select.</p>
                            </div>
                            <div class="help-section">
                                <h6><i class="fas fa-calendar-alt me-2"></i>Step 3: Schedule</h6>
                                <p>Pick your preferred date and time. Available slots are shown in blue.</p>
                            </div>
                            <div class="help-section">
                                <h6><i class="fas fa-check-circle me-2"></i>Step 4: Confirm</h6>
                                <p>Review your booking details and confirm your appointment.</p>
                            </div>
                            <hr>
                            <p class="text-muted mb-0">
                                <i class="fas fa-phone me-2"></i>Need more help? Call us at: +251 911 123456
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        `;
        
        // Remove existing modal if any
        const existingModal = document.getElementById('helpModal');
        if (existingModal) {
            existingModal.remove();
        }
        
        // Add modal to body
        document.body.insertAdjacentHTML('beforeend', helpModal);
        
        // Show modal
        const modal = new bootstrap.Modal(document.getElementById('helpModal'));
        modal.show();
    }

    function initializeServiceSelection() {
        const serviceCheckboxes = document.querySelectorAll('input[name="service_ids[]"]');
        
        serviceCheckboxes.forEach(checkbox => {
            checkbox.addEventListener('change', updateServiceSummary);
        });
    }
        
    function updateServiceSummary() {
        const serviceCheckboxes = document.querySelectorAll('input[name="service_ids[]"]:checked');
        const summary = document.getElementById('service-summary');
        const selectedCount = document.getElementById('selected-count');
        const totalPrice = document.getElementById('total-price');
        
            let total = 0;
        let count = serviceCheckboxes.length;
            
            serviceCheckboxes.forEach(checkbox => {
            const price = parseFloat(checkbox.dataset.price);
                    total += price;
            
            // Add visual feedback to selected service cards
            const serviceCard = checkbox.closest('.service-card');
            serviceCard.classList.add('selected');
        });
        
        // Remove selection from unchecked services
        document.querySelectorAll('input[name="service_ids[]"]:not(:checked)').forEach(checkbox => {
            const serviceCard = checkbox.closest('.service-card');
            serviceCard.classList.remove('selected');
        });
        
        if (count > 0) {
            summary.style.display = 'block';
            selectedCount.textContent = `${count} service${count > 1 ? 's' : ''} selected`;
            totalPrice.textContent = `Br ${total.toFixed(2)}`;
        } else {
            summary.style.display = 'none';
        }
    }

    function loadTimeSlots(date) {
        const container = document.getElementById('time-slots-container');
        container.innerHTML = ''; // Clear existing slots
        
        // Fetch available time slots from the server
        fetch(`api/available_slots.php?date=${date}`)
            .then(response => response.json())
            .then(slots => {
                slots.forEach(slot => {
                    const slotElement = document.createElement('div');
                    slotElement.className = `time-slot ${slot.available ? '' : 'unavailable'}`;
                    slotElement.textContent = slot.time;
                    
                    if (slot.available) {
                        slotElement.addEventListener('click', () => selectTimeSlot(slotElement, slot.time));
                    }
                    
                    container.appendChild(slotElement);
                });
            });
    }

    function selectTimeSlot(element, time) {
        // Remove selection from other slots
        document.querySelectorAll('.time-slot.selected').forEach(slot => {
            slot.classList.remove('selected');
        });
        
        // Select this slot
        element.classList.add('selected');
        
        // Update hidden input
        document.getElementById('appointment_time').value = time;
    }

    function initializePrioritySelection() {
        const priorityOptions = document.querySelectorAll('.priority-option');
        const priorityInput = document.getElementById('selected-priority');
        
        priorityOptions.forEach(option => {
            option.addEventListener('click', () => {
                // Remove selection from other options
                priorityOptions.forEach(opt => opt.classList.remove('selected'));
                
                // Select this option
                option.classList.add('selected');
                
                // Update hidden input
                priorityInput.value = option.dataset.priority;
            });
        });
    }

    function initializeFileUpload() {
        const fileInput = document.getElementById('file-upload');
        const preview = document.getElementById('file-upload-preview');
        
        fileInput.addEventListener('change', function() {
            preview.innerHTML = '';
            preview.style.display = 'block';
            
            [...this.files].forEach(file => {
                if (file.type.startsWith('image/')) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        const img = document.createElement('img');
                        img.src = e.target.result;
                        img.className = 'preview-image';
                        preview.appendChild(img);
                    }
                    reader.readAsDataURL(file);
                }
            });
        });
    }

    let currentStep = 1;
    const totalSteps = 4;
    
    function showStep(step) {
        // Hide all steps
        document.querySelectorAll('.booking-step').forEach(s => s.classList.remove('active'));
        
        // Show current step
        document.getElementById(`step-${step}`).classList.add('active');
        
        // Update progress steps
        document.querySelectorAll('.step-item').forEach((item, index) => {
            item.classList.remove('active', 'completed');
            if (index + 1 === step) {
                item.classList.add('active');
            } else if (index + 1 < step) {
                item.classList.add('completed');
            }
        });
        
        // Update navigation buttons
        const backBtn = document.querySelector('.btn-back');
        const nextBtn = document.querySelector('.btn-next');
        const navButtons = document.querySelector('.navigation-buttons');
        
        // Ensure navigation is visible
        if (navButtons) {
            navButtons.style.display = 'block';
            navButtons.style.visibility = 'visible';
        }
        
        // Handle back button
        if (backBtn) {
            backBtn.style.display = step === 1 ? 'none' : 'inline-flex';
            backBtn.style.visibility = 'visible';
        }
        
        // Handle next button
        if (nextBtn) {
            nextBtn.style.display = 'inline-flex';
            nextBtn.style.visibility = 'visible';
        
        if (step === totalSteps) {
                nextBtn.innerHTML = '<i class="fas fa-check-circle"></i> Confirm Booking';
            nextBtn.onclick = submitBooking;
                nextBtn.disabled = false;
            updateBookingSummary();
            } else {
            nextBtn.innerHTML = 'Next <i class="fas fa-arrow-right"></i>';
            nextBtn.onclick = nextStep;
                // Keep disabled state based on step validation
                if (step === 1) {
                    nextBtn.disabled = !document.getElementById('car_id').value;
                } else {
                    nextBtn.disabled = false;
                }
            }
        }
    }
    
    function nextStep() {
        if (validateStep(currentStep)) {
            currentStep = Math.min(currentStep + 1, totalSteps);
            showStep(currentStep);
        }
    }
    
    function previousStep() {
        currentStep = Math.max(currentStep - 1, 1);
        showStep(currentStep);
    }
    
    function validateStep(step) {
        switch (step) {
            case 1:
                const carId = document.getElementById('car_id').value;
                if (!carId) {
                    showAlert('Please select a vehicle to continue', 'warning');
                    return false;
                }
                break;
                
            case 2:
                const selectedServices = document.querySelectorAll('input[name="service_ids[]"]:checked');
                if (selectedServices.length === 0) {
                    showAlert('Please select at least one service', 'warning');
                    return false;
                }
                break;
                
            case 3:
                const date = document.getElementById('appointment_date').value;
                const time = document.getElementById('appointment_time').value;
                if (!date) {
                    showAlert('Please select an appointment date', 'warning');
                    return false;
                }
                if (!time) {
                    showAlert('Please select an appointment time', 'warning');
                    return false;
                }
                break;
                
            case 4:
                const termsAgreed = document.getElementById('terms-agreement').checked;
                if (!termsAgreed) {
                    showAlert('Please agree to the terms and conditions', 'warning');
                    return false;
                }
                break;
        }
        return true;
    }

    function showAlert(message, type = 'info') {
        const alertDiv = document.createElement('div');
        alertDiv.className = `alert alert-${type} alert-dismissible fade show position-fixed`;
        alertDiv.style.cssText = 'top: 70px; right: 20px; z-index: 1050; max-width: 300px;';
        alertDiv.innerHTML = `
            <i class="fas fa-${type === 'warning' ? 'exclamation-triangle' : type === 'success' ? 'check-circle' : 'info-circle'} me-2"></i>
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;
        
        document.body.appendChild(alertDiv);
        
        // Auto remove after 5 seconds
        setTimeout(() => {
            if (alertDiv.parentNode) {
                alertDiv.remove();
            }
        }, 5000);
    }
    
    function submitBooking() {
        if (validateStep(currentStep)) {
            // Update booking summary before form submission
            updateBookingSummary();
            
            const form = document.getElementById('service-booking-form');
            const formData = new FormData(form);

            // Show loading state
            const submitBtn = document.querySelector('.btn-next');
            const originalText = submitBtn.innerHTML;
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Processing...';

            fetch(form.action, {
                method: 'POST',
                body: formData
            })
            .then(response => response.text())
            .then(html => {
                // Copy booking summary to modal
                const summaryContent = document.getElementById('booking-summary').innerHTML;
                document.getElementById('modalBookingSummary').innerHTML = summaryContent;
                
                // Show success modal using Bootstrap 5 modal
                const successModal = new bootstrap.Modal(document.getElementById('bookingSuccessModal'), {
                    backdrop: 'static',
                    keyboard: false
                });
                successModal.show();
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred while processing your booking. Please try again.');
            })
            .finally(() => {
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalText;
            });
        }
    }

    function updateBookingSummary() {
        const summary = document.getElementById('booking-summary');
        const vehicleId = document.getElementById('car_id').value;
        const selectedVehicle = document.querySelector(`[data-vehicle-id="${vehicleId}"]`);
        const selectedServices = document.querySelectorAll('input[name="service_ids[]"]:checked');
        const date = document.getElementById('appointment_date').value;
        const time = document.getElementById('appointment_time').value;
        const notes = document.getElementById('notes').value;
        
        let totalAmount = 0;
        const servicesList = Array.from(selectedServices).map(service => {
            const card = service.closest('.service-card');
            const title = card.querySelector('.service-title').textContent;
            const priceValue = parseFloat(service.dataset.price);
            totalAmount += priceValue;
            return {
                name: title,
                price: priceValue
            };
        });
        
        const vehicleName = selectedVehicle ? 
            selectedVehicle.querySelector('.vehicle-details h6').textContent : 
            'No vehicle selected';
        
        const formattedDate = date ? new Date(date).toLocaleDateString('en-US', {
            weekday: 'long',
            year: 'numeric',
            month: 'long',
            day: 'numeric'
        }) : 'No date selected';
        
        let summaryHTML = `
            <div class="summary-section">
                <h6 class="summary-title">
                    <i class="fas fa-car me-2"></i>Vehicle
                </h6>
                <p class="summary-content">${vehicleName}</p>
            </div>
            
            <div class="summary-section">
                <h6 class="summary-title">
                    <i class="fas fa-tools me-2"></i>Selected Services
                </h6>
                <div class="summary-content">
                    ${servicesList.length > 0 ? 
                        servicesList.map(service => `
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span>${service.name}</span>
                                <span class="text-primary fw-bold">Br ${service.price.toFixed(2)}</span>
                            </div>
                        `).join('') : 
                        '<p class="text-muted">No services selected</p>'
                    }
                </div>
            </div>
            
            <div class="summary-section">
                <h6 class="summary-title">
                    <i class="fas fa-calendar-alt me-2"></i>Appointment
                </h6>
                <p class="summary-content">
                    <strong>Date:</strong> ${formattedDate}<br>
                    <strong>Time:</strong> ${time || 'No time selected'}
                </p>
            </div>
            
            ${notes ? `
            <div class="summary-section">
                <h6 class="summary-title">
                    <i class="fas fa-sticky-note me-2"></i>Notes
                </h6>
                <p class="summary-content">${notes}</p>
            </div>
            ` : ''}
            
            <div class="summary-highlight">
                <div class="total-amount">Br ${totalAmount.toFixed(2)}</div>
                <div class="total-label">Total Amount</div>
            </div>
        `;
        
        summary.innerHTML = summaryHTML;
    }

    function printBookingOrder() {
        // Get booking details
        const vehicle = document.getElementById('car_id');
        const selectedServices = document.querySelectorAll('input[name="service_ids[]"]:checked');
        const date = document.getElementById('appointment_date').value;
        const time = document.getElementById('appointment_time').value;
        
        // Format date for better readability
        const formattedDate = new Date(date).toLocaleDateString('en-US', {
            year: 'numeric',
            month: 'long',
            day: 'numeric'
        });
        
        // Generate order number with current date
        const today = new Date();
        const orderNumber = 'ORD-' + 
            today.getFullYear().toString().substr(-2) +
            (today.getMonth() + 1).toString().padStart(2, '0') +
            today.getDate().toString().padStart(2, '0') +
            today.getHours().toString().padStart(2, '0') +
            today.getMinutes().toString().padStart(2, '0');
        
        // Update printable content
        document.getElementById('print-order-number').textContent = orderNumber;
        document.getElementById('print-booking-date').textContent = today.toLocaleDateString('en-US', {
            year: 'numeric',
            month: 'long',
            day: 'numeric'
        });
        document.getElementById('print-customer-name').textContent = '<?php echo isset($_SESSION["name"]) ? htmlspecialchars($_SESSION["name"]) : "Customer"; ?>';
        
        // Get and set vehicle details
        const selectedVehicle = vehicle.options[vehicle.selectedIndex];
        document.getElementById('print-vehicle-details').textContent = selectedVehicle ? selectedVehicle.text : '-- Select a vehicle --';
        
        // Update appointment details
        document.getElementById('print-appointment-date').textContent = formattedDate;
        document.getElementById('print-appointment-time').textContent = time;

        // Update services list
        const servicesList = document.getElementById('print-services-list');
        servicesList.innerHTML = '';
        let totalAmount = 0;

        if (selectedServices.length > 0) {
            selectedServices.forEach(service => {
                const card = service.closest('.service-card');
                const title = card.querySelector('.service-title').textContent;
                const priceText = card.querySelector('.service-price').textContent;
                const priceValue = parseFloat(priceText.replace('Br ', '').replace(',', ''));
                totalAmount += priceValue;

                const row = document.createElement('tr');
                row.innerHTML = `
                    <td style="padding: 10px; border-bottom: 1px solid #ddd;">${title}</td>
                    <td style="padding: 10px; text-align: right; border-bottom: 1px solid #ddd;">Br ${priceValue.toFixed(2)}</td>
                `;
                servicesList.appendChild(row);
            });
        } else {
            // If no services selected, show a message
            const row = document.createElement('tr');
            row.innerHTML = `
                <td colspan="2" style="padding: 10px; text-align: center; border-bottom: 1px solid #ddd;">No services selected</td>
            `;
            servicesList.appendChild(row);
        }

        // Update total amount
        document.getElementById('print-total-amount').textContent = `Br ${totalAmount.toFixed(2)}`;

        // Make sure the printable div is visible before printing
        const printableDiv = document.getElementById('printableBookingOrder');
        printableDiv.style.display = 'block';

        // Add a small delay to ensure content is rendered before printing
        setTimeout(() => {
            window.print();
            // Hide the printable div after printing
            printableDiv.style.display = 'none';
        }, 100);
    }
    </script>
</body>
</html> 