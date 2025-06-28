<?php
session_start();
require_once '../../includes/config.php';

// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized']);
    exit();
}

// Validate order ID
if (!isset($_GET['order_id']) || !is_numeric($_GET['order_id'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid order ID']);
    exit();
}

$orderId = (int)$_GET['order_id'];

try {
    // Fetch order details with customer and product information
    $query = "SELECT o.*, u.name as customer_name, u.email as customer_email, u.phone as customer_phone,
              p.name as product_name, p.price as product_price, p.description as product_description
              FROM tbl_orders o 
              LEFT JOIN tbl_user u ON o.user_id = u.id
              LEFT JOIN tbl_products p ON o.product_id = p.id
              WHERE o.id = :order_id";
    
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':order_id', $orderId);
    $stmt->execute();
    
    $order = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$order) {
        http_response_code(404);
        echo json_encode(['error' => 'Order not found']);
        exit();
    }
    
    // Return order details
    echo json_encode($order);
    
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
} 