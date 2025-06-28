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

// Validate required fields
if (!isset($_POST['name']) || empty($_POST['name'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Category name is required']);
    exit();
}

try {
    // Prepare data
    $name = trim($_POST['name']);
    $description = isset($_POST['description']) ? trim($_POST['description']) : null;
    $status = isset($_POST['status']) && in_array($_POST['status'], ['active', 'inactive']) ? $_POST['status'] : 'active';

    // Check if category name already exists
    $query = "SELECT COUNT(*) FROM tbl_categories WHERE name = :name";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':name', $name);
    $stmt->execute();
    
    if ($stmt->fetchColumn() > 0) {
        http_response_code(400);
        echo json_encode(['error' => 'A category with this name already exists']);
        exit();
    }

    // Insert category
    $query = "INSERT INTO tbl_categories (name, description, status, created_at) 
              VALUES (:name, :description, :status, NOW())";
    
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':name', $name);
    $stmt->bindParam(':description', $description);
    $stmt->bindParam(':status', $status);
    $stmt->execute();

    $categoryId = $conn->lastInsertId();

    echo json_encode([
        'success' => true,
        'message' => 'Category added successfully',
        'category' => [
            'id' => $categoryId,
            'name' => $name,
            'description' => $description,
            'status' => $status
        ]
    ]);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
} 