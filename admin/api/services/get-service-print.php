<?php
require_once __DIR__ . '/../../../includes/config.php';
require_once __DIR__ . '/../../../includes/auth.php';

header('Content-Type: application/json');

// Check if user is logged in and has admin privileges
if (!isLoggedIn() || !hasAdminPrivileges()) {
    http_response_code(401);
    echo json_encode(['success' => false, 'error' => 'Unauthorized']);
    exit;
}

if (!isset($_GET['id'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Service ID is required']);
    exit;
}

try {
    $stmt = $conn->prepare("
        SELECT 
            a.appointment_id as history_id,
            a.appointment_date as service_date,
            a.notes,
            s.price as cost,
            a.status,
            i.car_brand,
            i.car_model,
            i.car_year,
            i.plate_number,
            s.service_name,
            s.description as service_description,
            u.name,
            u.phonenum as phone,
            u.email
        FROM tbl_appointments a
        JOIN tbl_services s ON a.service_id = s.service_id
        JOIN tbl_info i ON a.info_id = i.id
        JOIN tbl_user u ON i.userid = u.id
        WHERE a.appointment_id = :id
    ");
    
    $stmt->execute(['id' => $_GET['id']]);
    $service = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$service) {
        throw new Exception('Service not found');
    }
    
    // Generate printable HTML
    $html = '
    <div class="print-wrapper">
        <div class="text-center mb-4">
            <h4 class="mb-1">Service Receipt</h4>
            <p class="mb-0">Auto Service Management System</p>
            <small class="text-muted">Receipt generated on ' . date('F j, Y') . '</small>
        </div>
        
        <div class="row mb-4">
            <div class="col-6">
                <h6 class="font-weight-bold">Customer Information</h6>
                <p class="mb-1"><strong>Name:</strong> ' . htmlspecialchars($service['name']) . '</p>
                <p class="mb-1"><strong>Phone:</strong> ' . htmlspecialchars($service['phone']) . '</p>
                <p class="mb-1"><strong>Email:</strong> ' . htmlspecialchars($service['email']) . '</p>
            </div>
            <div class="col-6">
                <h6 class="font-weight-bold">Vehicle Information</h6>
                <p class="mb-1"><strong>Vehicle:</strong> ' . htmlspecialchars($service['car_year'] . ' ' . $service['car_brand'] . ' ' . $service['car_model']) . '</p>
                <p class="mb-1"><strong>Plate Number:</strong> ' . htmlspecialchars($service['plate_number']) . '</p>
            </div>
        </div>
        
        <div class="row mb-4">
            <div class="col-12">
                <h6 class="font-weight-bold">Service Details</h6>
                <table class="table table-bordered">
                    <tr>
                        <th>Service ID</th>
                        <td>#' . str_pad($service['history_id'], 6, '0', STR_PAD_LEFT) . '</td>
                    </tr>
                    <tr>
                        <th>Service Type</th>
                        <td>' . htmlspecialchars($service['service_name']) . '</td>
                    </tr>
                    <tr>
                        <th>Description</th>
                        <td>' . htmlspecialchars($service['service_description']) . '</td>
                    </tr>
                    <tr>
                        <th>Date</th>
                        <td>' . date('F j, Y', strtotime($service['service_date'])) . '</td>
                    </tr>
                    <tr>
                        <th>Status</th>
                        <td><span class="badge bg-' . ($service['status'] === 'completed' ? 'success' : 'warning') . '">' 
                            . ucfirst($service['status']) . '</span></td>
                    </tr>
                    <tr>
                        <th>Notes</th>
                        <td>' . nl2br(htmlspecialchars($service['notes'])) . '</td>
                    </tr>
                    <tr>
                        <th>Cost</th>
                        <td>ETB ' . number_format($service['cost'], 2) . '</td>
                    </tr>
                </table>
            </div>
        </div>
        
        <div class="row mt-5">
            <div class="col-6">
                <p class="mb-4">Customer Signature: _____________________</p>
            </div>
            <div class="col-6 text-end">
                <p class="mb-4">Service Provider Signature: _____________________</p>
            </div>
        </div>
        
        <div class="text-center mt-4">
            <small class="text-muted">This is a computer-generated document. No signature is required.</small>
        </div>
    </div>';
    
    echo json_encode([
        'success' => true,
        'html' => $html
    ]);
    
} catch (Exception $e) {
    error_log("Error in get-service-print.php: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
} 