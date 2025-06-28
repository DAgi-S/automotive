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
if (!isset($_POST['id']) || !is_numeric($_POST['id']) || 
    !isset($_POST['name']) || empty($_POST['name'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid request data']);
    exit();
}

try {
    // Prepare data
    $id = (int)$_POST['id'];
    $name = trim($_POST['name']);
    $description = isset($_POST['description']) ? trim($_POST['description']) : null;
    $status = isset($_POST['status']) && in_array($_POST['status'], ['active', 'inactive']) ? $_POST['status'] : 'active';

    // Check if category exists
    $query = "SELECT COUNT(*) FROM tbl_categories WHERE name = :name AND id != :id";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':name', $name);
    $stmt->bindParam(':id', $id);
    $stmt->execute();
    
    if ($stmt->fetchColumn() > 0) {
        http_response_code(400);
        echo json_encode(['error' => 'A category with this name already exists']);
        exit();
    }

    // Update category
    $query = "UPDATE tbl_categories 
              SET name = :name, 
                  description = :description, 
                  status = :status,
                  updated_at = NOW()
              WHERE id = :id";
    
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':id', $id);
    $stmt->bindParam(':name', $name);
    $stmt->bindParam(':description', $description);
    $stmt->bindParam(':status', $status);
    $stmt->execute();

    if ($stmt->rowCount() === 0) {
        http_response_code(404);
        echo json_encode(['error' => 'Category not found']);
        exit();
    }

    echo json_encode([
        'success' => true,
        'message' => 'Category updated successfully',
        'category' => [
            'id' => $id,
            'name' => $name,
            'description' => $description,
            'status' => $status
        ]
    ]);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
} 