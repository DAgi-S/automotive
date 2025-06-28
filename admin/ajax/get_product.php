<?php
session_start();
require_once '../../includes/config.php';

// Set JSON content type
header('Content-Type: application/json');

// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized']);
    exit();
}

// Validate product ID
if (!isset($_GET['product_id']) || !is_numeric($_GET['product_id'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid product ID']);
    exit();
}

$productId = (int)$_GET['product_id'];

try {
    // Fetch product details with category information
    $query = "SELECT p.*, c.name as category_name 
              FROM tbl_products p 
              LEFT JOIN tbl_categories c ON p.category_id = c.id 
              WHERE p.id = :product_id";
    
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':product_id', $productId);
    $stmt->execute();
    
    $product = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$product) {
        http_response_code(404);
        echo json_encode(['error' => 'Product not found']);
        exit();
    }
    
    // Return product details
    echo json_encode([
        'success' => true,
        'message' => 'Product details retrieved successfully',
        'product' => $product
    ]);
    
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
} 