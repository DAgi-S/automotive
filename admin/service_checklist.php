<?php
session_start();

// Debug information
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Include database connection
require_once('../config/database.php');

// Check admin session
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

// Get all active services
try {
    // First check if table exists
    $stmt = $conn->prepare("SHOW TABLES LIKE 'tbl_services'");
    $stmt->execute();
    $tableExists = $stmt->fetch(PDO::FETCH_NUM);
    
    if (!$tableExists) {
        throw new Exception("Services table does not exist");
    }
    
    // Get services
    $stmt = $conn->prepare("SELECT service_id, service_name, description, price, duration FROM tbl_services WHERE status = 'active' OR status IS NULL");
    $stmt->execute();
    $services = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (empty($services)) {
        $_SESSION['warning'] = "No active services found. Please add services first.";
    }
} catch (Exception $e) {
    $_SESSION['error'] = "Error loading services: " . $e->getMessage();
    $services = [];
}

// Get all workers/mechanics
try {
    $stmt = $conn->prepare("SELECT id, full_name, position FROM tbl_worker WHERE position = 'worker'");
    $stmt->execute();
    $mechanics = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (empty($mechanics)) {
        $_SESSION['warning'] = ($_SESSION['warning'] ?? '') . " No active mechanics found. Please add mechanics first.";
    }
} catch (Exception $e) {
    $_SESSION['error'] = ($_SESSION['error'] ?? '') . " Error loading mechanics: " . $e->getMessage();
    $mechanics = [];
}

// Include header
require_once('includes/header.php');
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Service Checklist Form - Automotive Admin</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="style1.css">
    <style>
        .checklist-section {
            background: #fff;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 20px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .form-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 20px;
        }
        .service-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 15px;
        }
        .checklist-item {
            display: flex;
            align-items: center;
            padding: 10px;
            background: #f8f9fa;
            border-radius: 4px;
            border: 1px solid #e9ecef;
        }
        .checklist-item label {
            margin: 0 10px;
            flex-grow: 1;
            font-size: 0.9rem;
        }
        .checklist-item select {
            width: 130px;
            padding: 4px;
            font-size: 0.9rem;
        }
        .signature-pad {
            border: 2px solid #ddd;
            border-radius: 4px;
            margin-top: 10px;
            width: 100%;
            height: 150px;
        }
        .section-title {
            color: #2c3e50;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid #eee;
        }
        .status-select {
            font-size: 0.9rem;
            padding: 4px 8px;
            border-radius: 4px;
            border: 1px solid #ced4da;
        }
        .checkbox-custom {
            width: 18px;
            height: 18px;
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <h2 class="section-title">Service Checklist Form</h2>
        
        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <?php 
                echo $_SESSION['error'];
                unset($_SESSION['error']);
                ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <?php if (isset($_SESSION['warning'])): ?>
            <div class="alert alert-warning alert-dismissible fade show" role="alert">
                <?php 
                echo $_SESSION['warning'];
                unset($_SESSION['warning']);
                ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <?php 
                echo $_SESSION['success'];
                unset($_SESSION['success']);
                ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>
        
        <form id="serviceChecklistForm" method="POST" action="process_service_checklist.php">
            <!-- Vehicle and Customer Information Section -->
            <div class="checklist-section">
                <h3>Vehicle & Customer Information</h3>
                <div class="form-grid">
                    <div class="form-group">
                        <label>Select Vehicle</label>
                        <select name="vehicle_id" id="vehicleSelect" class="form-control" required onchange="getCustomerInfo(this.value)">
                            <option value="">Select Vehicle</option>
                            <?php
                            try {
                                $stmt = $conn->query("
                                    SELECT 
                                        i.id,
                                        i.plate_number,
                                        i.car_brand,
                                        i.car_model,
                                        i.car_year,
                                        u.name as owner_name,
                                        u.id as user_id
                                    FROM tbl_info i
                                    LEFT JOIN tbl_user u ON i.userid = u.id
                                    ORDER BY i.plate_number
                                ");
                                while ($vehicle = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                    printf('<option value="%d" data-user-id="%d">%s - %s %s (%s) - %s</option>',
                                        $vehicle['id'],
                                        $vehicle['user_id'],
                                        htmlspecialchars($vehicle['plate_number']),
                                        htmlspecialchars($vehicle['car_brand']),
                                        htmlspecialchars($vehicle['car_model']),
                                        htmlspecialchars($vehicle['car_year']),
                                        htmlspecialchars($vehicle['owner_name'])
                                    );
                                }
                            } catch (Exception $e) {
                                echo '<option value=\"\">Error loading vehicles: ' . htmlspecialchars($e->getMessage()) . '</option>';
                            }
                            ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Customer Name</label>
                        <input type="text" id="customerName" class="form-control" readonly>
                        <input type="hidden" name="customer_id" id="customerId">
                    </div>
                    <div class="form-group">
                        <label>Contact Number</label>
                        <input type="text" id="customerPhone" class="form-control" readonly>
                    </div>
                    <div class="form-group">
                        <label>Email</label>
                        <input type="email" id="customerEmail" class="form-control" readonly>
                    </div>
                </div>
            </div>

            <!-- Service Checklist Section -->
            <div class="checklist-section">
                <h3>Service Checklist</h3>
                <div class="service-grid">
                    <?php foreach ($services as $service): ?>
                    <div class="checklist-item">
                        <input type="checkbox" name="services[]" value="<?php echo $service['service_id']; ?>" 
                               id="service_<?php echo $service['service_id']; ?>" class="checkbox-custom">
                        <label for="service_<?php echo $service['service_id']; ?>">
                            <?php echo htmlspecialchars($service['service_name']); ?>
                        </label>
                        <select name="service_status[<?php echo $service['service_id']; ?>]" class="status-select">
                            <option value="good">Good</option>
                            <option value="needs_attention">Needs Attention</option>
                            <option value="critical">Critical</option>
                            <option value="not_applicable">N/A</option>
                        </select>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Additional Notes -->
            <div class="checklist-section">
                <h3>Additional Notes</h3>
                <textarea name="notes" class="form-control" rows="4" 
                          placeholder="Enter any additional notes or observations"></textarea>
            </div>

            <!-- Confirmation Section -->
            <div class="checklist-section">
                <h3>Confirmation</h3>
                <div class="form-grid">
                    <div class="form-group">
                        <label>Assigned Mechanic</label>
                        <select name="mechanic_id" class="form-control" required>
                            <option value="">Select Mechanic</option>
                            <?php foreach ($mechanics as $mechanic): ?>
                            <option value="<?php echo $mechanic['id']; ?>">
                                <?php echo htmlspecialchars($mechanic['full_name']); ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Service Date</label>
                        <input type="date" name="service_date" class="form-control" required min="<?php echo date('Y-m-d'); ?>">
                    </div>
                </div>

                <!-- Signature Pads -->
                <div class="form-grid">
                    <div class="form-group">
                        <label>Mechanic's Signature</label>
                        <div class="signature-pad" id="mechanicSignature"></div>
                        <input type="hidden" name="mechanic_signature" id="mechanicSignatureData">
                        <button type="button" class="btn btn-secondary btn-sm mt-2" onclick="clearSignature('mechanicSignature')">Clear</button>
                    </div>
                    <div class="form-group">
                        <label>Customer's Signature</label>
                        <div class="signature-pad" id="customerSignature"></div>
                        <input type="hidden" name="customer_signature" id="customerSignatureData">
                        <button type="button" class="btn btn-secondary btn-sm mt-2" onclick="clearSignature('customerSignature')">Clear</button>
                    </div>
                </div>
            </div>

            <div class="form-group text-center">
                <button type="submit" class="btn btn-primary">Submit Service Checklist</button>
                <button type="button" class="btn btn-secondary" onclick="window.history.back()">Cancel</button>
            </div>
        </form>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/signature_pad@4.0.0/dist/signature_pad.umd.min.js"></script>
    <script>
        // Function to get customer info
        function getCustomerInfo(vehicleId) {
            if (!vehicleId) return;

            $.ajax({
                url: 'ajax/get_customer_info.php',
                data: { vehicle_id: vehicleId },
                method: 'GET',
                dataType: 'json',
                success: function(data) {
                    if (data.error) {
                        console.error('Error:', data.error);
                        return;
                    }
                    $('#customerName').val(data.name || '');
                    $('#customerId').val(data.id || '');
                    $('#customerPhone').val(data.phonenum || '');
                    $('#customerEmail').val(data.email || '');
                },
                error: function(xhr, status, error) {
                    console.error('AJAX Error:', status, error);
                    // Clear the fields on error
                    $('#customerName, #customerId, #customerPhone, #customerEmail').val('');
                }
            });
        }

        // Wait for DOM to be fully loaded
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize signature pads
            const mechanicCanvas = document.getElementById('mechanicSignature');
            const customerCanvas = document.getElementById('customerSignature');

            if (!mechanicCanvas || !customerCanvas) {
                console.error('Signature canvas elements not found');
                return;
            }

            // Convert divs to canvas elements
            mechanicCanvas.innerHTML = '<canvas width="400" height="150"></canvas>';
            customerCanvas.innerHTML = '<canvas width="400" height="150"></canvas>';

            const mechanicPad = new SignaturePad(mechanicCanvas.querySelector('canvas'), {
                backgroundColor: 'rgb(255, 255, 255)'
            });
            const customerPad = new SignaturePad(customerCanvas.querySelector('canvas'), {
                backgroundColor: 'rgb(255, 255, 255)'
            });

            // Form submission
            const form = document.getElementById('serviceChecklistForm');
            if (form) {
                form.onsubmit = function(e) {
                    e.preventDefault();
                    
                    // Get signature data
                    document.getElementById('mechanicSignatureData').value = mechanicPad.toDataURL();
                    document.getElementById('customerSignatureData').value = customerPad.toDataURL();

                    // Submit form
                    this.submit();
                };
            }

            // Clear signature pad
            window.clearSignature = function(padId) {
                const padElement = document.getElementById(padId);
                if (!padElement) return;
                
                const canvas = padElement.querySelector('canvas');
                if (!canvas) return;
                
                const signaturePad = new SignaturePad(canvas);
                signaturePad.clear();
            };
        });
    </script>
</body>
</html> 