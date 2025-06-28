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
if (!isset($_POST['name']) || empty($_POST['name']) ||
    !isset($_POST['price']) || !is_numeric($_POST['price']) ||
    !isset($_POST['stock']) || !is_numeric($_POST['stock'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Required fields are missing or invalid']);
    exit();
}

try {
    // Start transaction
    $conn->beginTransaction();

    // Prepare data
    $name = trim($_POST['name']);
    $description = isset($_POST['description']) ? trim($_POST['description']) : null;
    $price = (float)$_POST['price'];
    $stock = (int)$_POST['stock'];
    $category_id = !empty($_POST['category_id']) ? (int)$_POST['category_id'] : null;
    $status = isset($_POST['status']) && in_array($_POST['status'], ['active', 'inactive']) ? $_POST['status'] : 'active';

    // Handle image upload
    $image_url = null;
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
        $file_type = $_FILES['image']['type'];
        
        if (!in_array($file_type, $allowed_types)) {
            throw new Exception('Invalid file type. Only JPG, PNG and GIF are allowed.');
        }

        // Create upload directory if it doesn't exist
        $upload_dir = '../../uploads/products/';
        if (!file_exists($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }

        // Generate unique filename
        $extension = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
        $filename = uniqid('product_') . '.' . $extension;
        $filepath = $upload_dir . $filename;

        // Move uploaded file
        if (!move_uploaded_file($_FILES['image']['tmp_name'], $filepath)) {
            throw new Exception('Failed to upload image');
        }

        $image_url = 'uploads/products/' . $filename;
    }

    // Insert product
    $query = "INSERT INTO tbl_products (name, description, price, stock, category_id, image_url, status, created_at) 
              VALUES (:name, :description, :price, :stock, :category_id, :image_url, :status, NOW())";
    
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':name', $name);
    $stmt->bindParam(':description', $description);
    $stmt->bindParam(':price', $price);
    $stmt->bindParam(':stock', $stock);
    $stmt->bindParam(':category_id', $category_id);
    $stmt->bindParam(':image_url', $image_url);
    $stmt->bindParam(':status', $status);
    $stmt->execute();

    $productId = $conn->lastInsertId();

    // Commit transaction
    $conn->commit();

    echo json_encode([
        'success' => true,
        'message' => 'Product added successfully',
        'product' => [
            'id' => $productId,
            'name' => $name,
            'description' => $description,
            'price' => $price,
            'stock' => $stock,
            'category_id' => $category_id,
            'image_url' => $image_url,
            'status' => $status
        ]
    ]);

} catch (Exception $e) {
    // Rollback transaction on error
    if ($conn->inTransaction()) {
        $conn->rollBack();
    }

    // Delete uploaded image if exists
    if (isset($filepath) && file_exists($filepath)) {
        unlink($filepath);
    }

    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
} 