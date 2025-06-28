<?php
session_start();

// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

// Include database connection
require_once('../../../config/database.php');

// Set JSON response header
header('Content-Type: application/json');

// Check if brand_id is provided
if (!isset($_GET['brand_id'])) {
    echo json_encode(['success' => false, 'message' => 'Brand ID is required']);
    exit();
}

try {
    // Fetch models for the specified brand
    $stmt = $conn->prepare("
        SELECT *
        FROM car_models
        WHERE brand_id = ?
        ORDER BY model_name ASC
    ");
    $stmt->execute([$_GET['brand_id']]);
    $models = $stmt->fetchAll();

    echo json_encode([
        'success' => true,
        'models' => $models
    ]);
} catch (PDOException $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Error fetching models: ' . $e->getMessage()
    ]);
} 