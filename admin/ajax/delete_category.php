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

// Validate category ID
if (!isset($_POST['category_id']) || !is_numeric($_POST['category_id'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid category ID']);
    exit();
}

$categoryId = (int)$_POST['category_id'];

try {
    // Start transaction
    $conn->beginTransaction();

    // Check if category exists
    $query = "SELECT COUNT(*) FROM tbl_categories WHERE id = :category_id";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':category_id', $categoryId);
    $stmt->execute();
    
    if ($stmt->fetchColumn() === 0) {
        throw new Exception('Category not found');
    }

    // Update products in this category to have no category (NULL)
    $query = "UPDATE tbl_products SET category_id = NULL WHERE category_id = :category_id";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':category_id', $categoryId);
    $stmt->execute();

    // Delete category
    $query = "DELETE FROM tbl_categories WHERE id = :category_id";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':category_id', $categoryId);
    $stmt->execute();

    // Commit transaction
    $conn->commit();

    echo json_encode([
        'success' => true,
        'message' => 'Category deleted successfully'
    ]);

} catch (Exception $e) {
    // Rollback transaction on error
    if ($conn->inTransaction()) {
        $conn->rollBack();
    }

    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
} 