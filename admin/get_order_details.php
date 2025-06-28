<?php
session_start();
require_once('../config/database.php');

// Check if user is logged in
if (!isset($_SESSION['admin_id']) && !isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized access']);
    exit;
}

header('Content-Type: application/json');

try {
    $order_id = isset($_GET['order_id']) ? intval($_GET['order_id']) : 0;
    
    if ($order_id) {
        // Get order details including services
        $query = "SELECT o.*, 
                        GROUP_CONCAT(os.service_id) as service_ids,
                        u.name as client_name,
                        u.phonenum as client_phone,
                        cb.brand_name as car_brand_name,
                        cm.model_name as car_model_name,
                        COALESCE(i.plate_number, o.license_plate) as plate_number,
                        w.full_name as technician_name
                 FROM tbl_service_orders o
                 LEFT JOIN tbl_order_services os ON o.id = os.order_id
                 LEFT JOIN tbl_user u ON o.client_id = u.id
                 LEFT JOIN tbl_info i ON u.id = i.userid AND i.id = (
                     SELECT MAX(id) FROM tbl_info WHERE userid = u.id
                 )
                 LEFT JOIN car_brands cb ON cb.id = i.car_brand
                 LEFT JOIN car_models cm ON cm.id = i.car_model
                 LEFT JOIN tbl_worker w ON o.technician_id = w.id
                 WHERE o.id = :order_id
                 GROUP BY o.id";
        
        $stmt = $conn->prepare($query);
        $stmt->execute(['order_id' => $order_id]);
        $order = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($order) {
            echo json_encode([
                'status' => 'success',
                'order_status' => $order['status'],
                'client_id' => $order['client_id'],
                'client_name' => $order['client_name'],
                'client_phone' => $order['client_phone'],
                'car_brand' => $order['car_brand_name'] ?? null,
                'car_model' => $order['car_model_name'] ?? $order['car_model'],
                'plate_number' => $order['plate_number'] ?? null,
                'technician_id' => $order['technician_id'],
                'technician_name' => $order['technician_name'],
                'services' => $order['service_ids'] ? explode(',', $order['service_ids']) : []
            ]);
        } else {
            echo json_encode([
                'status' => 'error',
                'message' => 'Order not found'
            ]);
        }
    } else {
        echo json_encode([
            'status' => 'error',
            'message' => 'Invalid order ID'
        ]);
    }
} catch(PDOException $e) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Database error: ' . $e->getMessage()
    ]);
}
?> 