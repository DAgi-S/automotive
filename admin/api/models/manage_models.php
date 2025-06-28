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

// Check if action is provided
if (!isset($_POST['action'])) {
    echo json_encode(['success' => false, 'message' => 'Action is required']);
    exit();
}

try {
    switch ($_POST['action']) {
        case 'add_model':
            // Validate required fields
            if (!isset($_POST['brand_id'], $_POST['model_name'], $_POST['year_from'], $_POST['year_to'])) {
                echo json_encode(['success' => false, 'message' => 'All fields are required']);
                exit();
            }

            // Validate years
            if ($_POST['year_from'] > $_POST['year_to']) {
                echo json_encode(['success' => false, 'message' => 'Year From cannot be greater than Year To']);
                exit();
            }

            $stmt = $conn->prepare("
                INSERT INTO car_models (brand_id, model_name, year_from, year_to, created_at)
                VALUES (?, ?, ?, ?, NOW())
            ");
            $stmt->execute([
                $_POST['brand_id'],
                $_POST['model_name'],
                $_POST['year_from'],
                $_POST['year_to']
            ]);

            echo json_encode([
                'success' => true,
                'message' => 'Model added successfully'
            ]);
            break;

        case 'edit_model':
            // Validate required fields
            if (!isset($_POST['model_id'], $_POST['model_name'], $_POST['year_from'], $_POST['year_to'])) {
                echo json_encode(['success' => false, 'message' => 'All fields are required']);
                exit();
            }

            // Validate years
            if ($_POST['year_from'] > $_POST['year_to']) {
                echo json_encode(['success' => false, 'message' => 'Year From cannot be greater than Year To']);
                exit();
            }

            $stmt = $conn->prepare("
                UPDATE car_models
                SET model_name = ?, year_from = ?, year_to = ?
                WHERE id = ?
            ");
            $stmt->execute([
                $_POST['model_name'],
                $_POST['year_from'],
                $_POST['year_to'],
                $_POST['model_id']
            ]);

            echo json_encode([
                'success' => true,
                'message' => 'Model updated successfully'
            ]);
            break;

        case 'delete_model':
            // Validate required fields
            if (!isset($_POST['model_id'])) {
                echo json_encode(['success' => false, 'message' => 'Model ID is required']);
                exit();
            }

            $stmt = $conn->prepare("DELETE FROM car_models WHERE id = ?");
            $stmt->execute([$_POST['model_id']]);

            echo json_encode([
                'success' => true,
                'message' => 'Model deleted successfully'
            ]);
            break;

        default:
            echo json_encode(['success' => false, 'message' => 'Invalid action']);
            break;
    }
} catch (PDOException $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Database error: ' . $e->getMessage()
    ]);
} 