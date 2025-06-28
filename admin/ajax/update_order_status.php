<?php
session_start();
require_once '../../includes/config.php';

// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized']);
    exit();
}

// Validate request data
if (!isset($_POST['order_id']) || !is_numeric($_POST['order_id']) || 
    !isset($_POST['status']) || !in_array($_POST['status'], ['pending', 'processing', 'completed', 'cancelled'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid request data']);
    exit();
}

$orderId = (int)$_POST['order_id'];
$newStatus = $_POST['status'];

try {
    // Start transaction
    $conn->beginTransaction();
    
    // Update order status
    $query = "UPDATE tbl_orders SET status = :status WHERE id = :order_id";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':status', $newStatus);
    $stmt->bindParam(':order_id', $orderId);
    $stmt->execute();
    
    // Log the status change
    $logQuery = "INSERT INTO tbl_order_status_log (order_id, status, changed_by, changed_at) 
                 VALUES (:order_id, :status, :admin_id, NOW())";
    $logStmt = $conn->prepare($logQuery);
    $logStmt->bindParam(':order_id', $orderId);
    $logStmt->bindParam(':status', $newStatus);
    $logStmt->bindParam(':admin_id', $_SESSION['admin_id']);
    $logStmt->execute();
    
    // Commit transaction
    $conn->commit();
    
    echo json_encode(['success' => true]);
    
} catch (PDOException $e) {
    // Rollback transaction on error
    $conn->rollBack();
    
    http_response_code(500);
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
} 