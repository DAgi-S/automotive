<?php
/**
 * Enhanced Edit Car AJAX Handler
 * Robust CRUD operation with comprehensive validation and error handling
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
    'data' => null,
    'errors' => []
];

try {
    // Check if request is POST
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Invalid request method. Only POST is allowed.');
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
    if (empty($_POST['car_id']) || !is_numeric($_POST['car_id'])) {
        throw new Exception('Invalid car ID provided.');
    }
    
    $car_id = intval($_POST['car_id']);
    
    // Verify car ownership
    $ownership_query = "SELECT id, img_name1, img_name2, img_name3 FROM tbl_info WHERE id = ? AND userid = ?";
    $ownership_stmt = $db->getConn()->prepare($ownership_query);
    if (!$ownership_stmt) {
        throw new Exception('Database prepare failed: ' . $db->getConn()->error);
    }
    
    $ownership_stmt->bind_param('ii', $car_id, $user_id);
    $ownership_stmt->execute();
    $ownership_result = $ownership_stmt->get_result();
    
    if (!$ownership_result || $ownership_result->num_rows === 0) {
        throw new Exception('Car not found or access denied.');
    }
    
    $existing_car = $ownership_result->fetch_assoc();
    $ownership_stmt->close();
    
    // Validate and sanitize input data
    $car_data = [
        'car_brand' => trim($_POST['car_brand'] ?? ''),
        'car_model' => trim($_POST['car_model'] ?? ''),
        'car_year' => trim($_POST['car_year'] ?? ''),
        'plate_number' => trim($_POST['plate_number'] ?? ''),
        'mile_age' => trim($_POST['mile_age'] ?? ''),
        'service_date' => trim($_POST['service_date'] ?? ''),
        'oil_change' => trim($_POST['oil_change'] ?? ''),
        'insurance' => trim($_POST['insurance'] ?? ''),
        'bolo' => trim($_POST['bolo'] ?? ''),
        'rd_wegen' => trim($_POST['rd_wegen'] ?? ''),
        'yemenged_fend' => trim($_POST['yemenged_fend'] ?? '')
    ];
    
    // Validation rules
    $validation_errors = [];
    
    // Required field validation
    $required_fields = ['car_brand', 'car_model', 'car_year', 'plate_number'];
    foreach ($required_fields as $field) {
        if (empty($car_data[$field])) {
            $validation_errors[] = ucfirst(str_replace('_', ' ', $field)) . ' is required.';
        }
    }
    
    // Year validation
    if (!empty($car_data['car_year'])) {
        $year = intval($car_data['car_year']);
        $current_year = date('Y');
        if ($year < 1900 || $year > ($current_year + 1)) {
            $validation_errors[] = 'Car year must be between 1900 and ' . ($current_year + 1) . '.';
        }
    }
    
    // Mileage validation
    if (!empty($car_data['mile_age']) && (!is_numeric($car_data['mile_age']) || $car_data['mile_age'] < 0)) {
        $validation_errors[] = 'Mileage must be a positive number.';
    }
    
    // Date validation
    $date_fields = ['service_date', 'oil_change', 'insurance', 'bolo', 'rd_wegen', 'yemenged_fend'];
    foreach ($date_fields as $field) {
        if (!empty($car_data[$field])) {
            $date = DateTime::createFromFormat('Y-m-d', $car_data[$field]);
            if (!$date || $date->format('Y-m-d') !== $car_data[$field]) {
                $validation_errors[] = ucfirst(str_replace('_', ' ', $field)) . ' must be a valid date.';
            }
        }
    }
    
    // Plate number uniqueness check (excluding current car)
    if (!empty($car_data['plate_number'])) {
        $plate_check_query = "SELECT id FROM tbl_info WHERE plate_number = ? AND userid = ? AND id != ?";
        $plate_check_stmt = $db->getConn()->prepare($plate_check_query);
        if ($plate_check_stmt) {
            $plate_check_stmt->bind_param('sii', $car_data['plate_number'], $user_id, $car_id);
            $plate_check_stmt->execute();
            $plate_result = $plate_check_stmt->get_result();
            if ($plate_result && $plate_result->num_rows > 0) {
                $validation_errors[] = 'A car with this plate number already exists in your account.';
            }
            $plate_check_stmt->close();
        }
    }
    
    // If validation errors exist, return them
    if (!empty($validation_errors)) {
        $response['errors'] = $validation_errors;
        $response['message'] = 'Validation failed. Please correct the errors and try again.';
        throw new Exception('Validation failed');
    }
    
    // Handle file uploads (optional for edit)
    $updated_images = [];
    $upload_dir = '../assets/img/';
    $files_to_delete = [];
    
    // Create upload directory if it doesn't exist
    if (!is_dir($upload_dir)) {
        if (!mkdir($upload_dir, 0755, true)) {
            throw new Exception('Failed to create upload directory.');
        }
    }
    
    // Process up to 3 images (only if new files are uploaded)
    for ($i = 1; $i <= 3; $i++) {
        $file_key = "img{$i}";
        $img_field = "img_name{$i}";
        
        if (isset($_FILES[$file_key]) && $_FILES[$file_key]['error'] === UPLOAD_ERR_OK) {
            $file = $_FILES[$file_key];
            
            // Validate file type
            $allowed_types = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
            $file_type = $file['type'];
            $file_extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
            
            if (!in_array($file_type, $allowed_types) || !in_array($file_extension, ['jpg', 'jpeg', 'png', 'gif'])) {
                throw new Exception("Image {$i}: Invalid file type. Only JPG, PNG, and GIF are allowed.");
            }
            
            // Validate file size (5MB max)
            if ($file['size'] > 5 * 1024 * 1024) {
                throw new Exception("Image {$i}: File size too large. Maximum size is 5MB.");
            }
            
            // Generate unique filename
            $file_extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
            $new_filename = 'car_' . $user_id . '_' . time() . '_' . $i . '.' . $file_extension;
            $upload_path = $upload_dir . $new_filename;
            
            // Move uploaded file
            if (move_uploaded_file($file['tmp_name'], $upload_path)) {
                $updated_images[$img_field] = $new_filename;
                
                // Mark old file for deletion (if it exists and is not default)
                $old_file = $existing_car[$img_field];
                if ($old_file && $old_file !== 'default-car.jpg' && file_exists($upload_dir . $old_file)) {
                    $files_to_delete[] = $upload_dir . $old_file;
                }
            } else {
                throw new Exception("Image {$i}: Failed to upload file.");
            }
        }
    }
    
    // Prepare update query
    $update_fields = [];
    $params = [];
    $param_types = '';
    
    // Add car data fields
    foreach ($car_data as $field => $value) {
        $update_fields[] = "{$field} = ?";
        $params[] = $value;
        $param_types .= 's';
    }
    
    // Add image fields if updated
    foreach ($updated_images as $field => $filename) {
        $update_fields[] = "{$field} = ?";
        $params[] = $filename;
        $param_types .= 's';
    }
    
    // Add updated_at timestamp
    $update_fields[] = "updated_at = NOW()";
    
    // Add car_id and user_id for WHERE clause
    $params[] = $car_id;
    $params[] = $user_id;
    $param_types .= 'ii';
    
    $update_query = "UPDATE tbl_info SET " . implode(', ', $update_fields) . " WHERE id = ? AND userid = ?";
    
    $stmt = $db->getConn()->prepare($update_query);
    if (!$stmt) {
        throw new Exception('Database prepare failed: ' . $db->getConn()->error);
    }
    
    // Bind parameters
    $stmt->bind_param($param_types, ...$params);
    
    // Execute query
    if ($stmt->execute()) {
        $affected_rows = $stmt->affected_rows;
        $stmt->close();
        
        if ($affected_rows > 0) {
            // Delete old image files
            foreach ($files_to_delete as $file_path) {
                if (file_exists($file_path)) {
                    unlink($file_path);
                }
            }
            
            // Success response
            $response['success'] = true;
            $response['message'] = 'Car updated successfully!';
            $response['data'] = [
                'car_id' => $car_id,
                'brand_name' => $car_data['brand_name'],
                'model_name' => $car_data['model_name'],
                'updated_images' => $updated_images
            ];
            
            // Log successful update
            error_log("Car updated successfully - User ID: {$user_id}, Car ID: {$car_id}");
        } else {
            throw new Exception('No changes were made or car not found.');
        }
        
    } else {
        throw new Exception('Failed to update car: ' . $stmt->error);
    }
    
} catch (Exception $e) {
    // Log error
    error_log("Edit car error: " . $e->getMessage());
    
    // Clean up uploaded files on error
    if (isset($updated_images) && is_array($updated_images)) {
        foreach ($updated_images as $filename) {
            if ($filename && file_exists($upload_dir . $filename)) {
                unlink($upload_dir . $filename);
            }
        }
    }
    
    // Set error response
    if (empty($response['message'])) {
        $response['message'] = $e->getMessage();
    }
    
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