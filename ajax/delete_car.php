<?php
/**
 * Enhanced Delete Car AJAX Handler
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
    'data' => null
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
    
    // Verify car ownership and get image filenames for cleanup
    $ownership_query = "SELECT id, car_brand, car_model, img_name1, img_name2, img_name3 FROM tbl_info WHERE id = ? AND userid = ?";
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
    
    $car_data = $ownership_result->fetch_assoc();
    $ownership_stmt->close();
    
    // Begin transaction for data integrity
    $db->getConn()->autocommit(false);
    
    try {
        // Delete car from database
        $delete_query = "DELETE FROM tbl_info WHERE id = ? AND userid = ?";
        $delete_stmt = $db->getConn()->prepare($delete_query);
        if (!$delete_stmt) {
            throw new Exception('Database prepare failed: ' . $db->getConn()->error);
        }
        
        $delete_stmt->bind_param('ii', $car_id, $user_id);
        
        if (!$delete_stmt->execute()) {
            throw new Exception('Failed to delete car: ' . $delete_stmt->error);
        }
        
        $affected_rows = $delete_stmt->affected_rows;
        $delete_stmt->close();
        
        if ($affected_rows === 0) {
            throw new Exception('Car not found or already deleted.');
        }
        
        // Commit transaction
        $db->getConn()->commit();
        
        // Clean up image files after successful database deletion
        $upload_dir = '../assets/img/';
        $deleted_images = [];
        
        for ($i = 1; $i <= 3; $i++) {
            $img_field = "img_name{$i}";
            if (!empty($car_data[$img_field]) && $car_data[$img_field] !== 'default-car.jpg') {
                $image_path = $upload_dir . $car_data[$img_field];
                if (file_exists($image_path)) {
                    if (unlink($image_path)) {
                        $deleted_images[] = $car_data[$img_field];
                    } else {
                        // Log warning but don't fail the operation
                        error_log("Warning: Failed to delete image file: {$image_path}");
                    }
                }
            }
        }
        
        // Success response
        $response['success'] = true;
        $response['message'] = 'Car deleted successfully!';
        $response['data'] = [
            'car_id' => $car_id,
            'car_brand' => $car_data['car_brand'],
            'car_model' => $car_data['car_model'],
            'deleted_images' => $deleted_images
        ];
        
        // Log successful deletion
        error_log("Car deleted successfully - User ID: {$user_id}, Car ID: {$car_id}, Brand: {$car_data['car_brand']}, Model: {$car_data['car_model']}");
        
    } catch (Exception $e) {
        // Rollback transaction on error
        $db->getConn()->rollback();
        throw $e;
    }
    
    // Re-enable autocommit
    $db->getConn()->autocommit(true);
    
} catch (Exception $e) {
    // Log error
    error_log("Delete car error: " . $e->getMessage());
    
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