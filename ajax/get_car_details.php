<?php
/**
 * Enhanced Get Car Details AJAX Handler
 * Robust data retrieval with comprehensive validation and error handling
 */

// Start output buffering to prevent any unwanted output
ob_start();

// Set proper headers for JSON response
header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-cache, must-revalidate');
header('Expires: Thu, 01 Jan 1970 00:00:00 GMT');

// Initialize response array
$response = [
    'success' => false,
    'message' => '',
    'data' => null
];

try {
    // Check if request is GET
    if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
        throw new Exception('Invalid request method. Only GET is allowed.');
    }
    
    // Include database connection
    require_once '../partial-front/db_con.php';
    
    // Start session for user verification
    session_start();
    
    // Verify user is logged in
    if (!isset($_SESSION['id']) || empty($_SESSION['id'])) {
        throw new Exception('User not authenticated. Please log in.');
    }
    
    $user_id = intval($_SESSION['id']);
    
    // Initialize database connection
    $db = new DB_con();
    if (!$db) {
        throw new Exception('Database connection failed.');
    }
    
    // Validate car ID
    if (empty($_GET['car_id']) || !is_numeric($_GET['car_id'])) {
        throw new Exception('Invalid car ID provided.');
    }
    
    $car_id = intval($_GET['car_id']);
    
    // Prepare query to get car details with brand information
    $query = "SELECT 
                c.id,
                c.car_brand,
                c.car_model,
                c.car_year,
                c.plate_number,
                c.mile_age,
                c.service_date,
                c.oil_change,
                c.insurance,
                c.bolo,
                c.rd_wegen,
                c.yemenged_fend,
                c.img_name1,
                c.img_name2,
                c.img_name3,
                c.created_at
              FROM tbl_info c
              WHERE c.id = ? AND c.userid = ?";
    
    $stmt = $db->getConn()->prepare($query);
    if (!$stmt) {
        throw new Exception('Database prepare failed: ' . $db->getConn()->error);
    }
    
    $stmt->bind_param('ii', $car_id, $user_id);
    
    if (!$stmt->execute()) {
        throw new Exception('Database query failed: ' . $stmt->error);
    }
    
    $result = $stmt->get_result();
    
    if (!$result || $result->num_rows === 0) {
        throw new Exception('Car not found or access denied.');
    }
    
    $car_data = $result->fetch_assoc();
    $stmt->close();
    
    // Process and format the data
    $formatted_data = [
        'id' => intval($car_data['id']),
        'car_brand' => htmlspecialchars($car_data['car_brand'] ?? '', ENT_QUOTES, 'UTF-8'),
        'car_model' => htmlspecialchars($car_data['car_model'] ?? '', ENT_QUOTES, 'UTF-8'),
        'car_year' => $car_data['car_year'] ?? '',
        'plate_number' => htmlspecialchars($car_data['plate_number'] ?? '', ENT_QUOTES, 'UTF-8'),
        'mile_age' => $car_data['mile_age'] ?? '0',
        'service_date' => $car_data['service_date'] ?? '',
        'oil_change' => $car_data['oil_change'] ?? '',
        'insurance' => $car_data['insurance'] ?? '',
        'bolo' => $car_data['bolo'] ?? '',
        'rd_wegen' => $car_data['rd_wegen'] ?? '',
        'yemenged_fend' => $car_data['yemenged_fend'] ?? '',
        'img_name1' => $car_data['img_name1'] ?? '',
        'img_name2' => $car_data['img_name2'] ?? '',
        'img_name3' => $car_data['img_name3'] ?? '',
        'created_at' => $car_data['created_at'] ?? ''
    ];
    
    // Add computed fields for better UX
    $current_date = new DateTime();
    
    // Calculate insurance expiry status
    if (!empty($formatted_data['insurance'])) {
        try {
            $insurance_date = new DateTime($formatted_data['insurance']);
            $days_until_expiry = $current_date->diff($insurance_date)->days;
            $is_expired = $insurance_date < $current_date;
            
            $formatted_data['insurance_status'] = [
                'days_remaining' => $is_expired ? -$days_until_expiry : $days_until_expiry,
                'is_expired' => $is_expired,
                'needs_attention' => $days_until_expiry <= 30,
                'formatted_date' => $insurance_date->format('M d, Y')
            ];
        } catch (Exception $e) {
            $formatted_data['insurance_status'] = [
                'days_remaining' => null,
                'is_expired' => false,
                'needs_attention' => false,
                'formatted_date' => 'Invalid date'
            ];
        }
    }
    
    // Calculate service status
    if (!empty($formatted_data['service_date'])) {
        try {
            $service_date = new DateTime($formatted_data['service_date']);
            $days_since_service = $current_date->diff($service_date)->days;
            $service_overdue = $service_date < $current_date && $days_since_service > 180; // 6 months
            
            $formatted_data['service_status'] = [
                'days_since_service' => $days_since_service,
                'is_overdue' => $service_overdue,
                'formatted_date' => $service_date->format('M d, Y')
            ];
        } catch (Exception $e) {
            $formatted_data['service_status'] = [
                'days_since_service' => null,
                'is_overdue' => false,
                'formatted_date' => 'Invalid date'
            ];
        }
    }
    
    // Validate image files existence
    $image_base_path = '../assets/img/';
    for ($i = 1; $i <= 3; $i++) {
        $img_field = "img_name{$i}";
        if (!empty($formatted_data[$img_field])) {
            $image_path = $image_base_path . $formatted_data[$img_field];
            $formatted_data["{$img_field}_exists"] = file_exists($image_path);
            $formatted_data["{$img_field}_url"] = "assets/img/" . $formatted_data[$img_field];
        } else {
            $formatted_data["{$img_field}_exists"] = false;
            $formatted_data["{$img_field}_url"] = '';
        }
    }
    
    // Success response
    $response['success'] = true;
    $response['message'] = 'Car details retrieved successfully.';
    $response['data'] = $formatted_data;
    
    // Log successful retrieval (optional, for debugging)
    error_log("Car details retrieved successfully - User ID: {$user_id}, Car ID: {$car_id}");
    
} catch (Exception $e) {
    // Log error
    error_log("Get car details error: " . $e->getMessage());
    
    // Set error response
    $response['message'] = $e->getMessage();
    
    // Don't expose sensitive error details in production
    if (strpos($e->getMessage(), 'Database') !== false) {
        $response['message'] = 'A database error occurred. Please try again later.';
    }
}

// Clean output buffer
ob_clean();

// Send JSON response
echo json_encode($response, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
exit;
?> 