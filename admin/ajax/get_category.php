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
if (!isset($_GET['category_id']) || !is_numeric($_GET['category_id'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid category ID']);
    exit();
}

$categoryId = (int)$_GET['category_id'];

try {
    // Fetch category details
    $query = "SELECT * FROM tbl_categories WHERE id = :category_id";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':category_id', $categoryId);
    $stmt->execute();
    
    $category = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$category) {
        http_response_code(404);
        echo json_encode(['error' => 'Category not found']);
        exit();
    }
    
    // Return category details
    echo json_encode([
        'success' => true,
        'message' => 'Category details retrieved successfully',
        'category' => $category
    ]);
    
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
} 