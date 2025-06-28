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
if (!isset($_POST['product_id']) || !is_numeric($_POST['product_id'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid product ID']);
    exit();
}

$productId = (int)$_POST['product_id'];

try {
    // Start transaction
    $conn->beginTransaction();

    // Get product image path before deletion
    $query = "SELECT image_url FROM tbl_products WHERE id = :product_id";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':product_id', $productId);
    $stmt->execute();
    
    $product = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$product) {
        throw new Exception('Product not found');
    }

    // Delete product
    $query = "DELETE FROM tbl_products WHERE id = :product_id";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':product_id', $productId);
    $stmt->execute();

    // Delete product image if exists
    if ($product['image_url'] && file_exists('../../' . $product['image_url'])) {
        unlink('../../' . $product['image_url']);
    }

    // Commit transaction
    $conn->commit();

    echo json_encode([
        'success' => true,
        'message' => 'Product deleted successfully'
    ]);

} catch (Exception $e) {
    // Rollback transaction on error
    if ($conn->inTransaction()) {
        $conn->rollBack();
    }

    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
} 