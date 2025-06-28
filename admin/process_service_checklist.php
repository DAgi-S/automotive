<?php
session_start();

// Debug information
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Include database connection
require_once('../config/database.php');

// Check admin session
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: service_checklist.php");
    exit();
}

// Validate required fields
$required_fields = ['vehicle_id', 'customer_id', 'service_date'];
foreach ($required_fields as $field) {
    if (empty($_POST[$field])) {
        $_SESSION['error'] = "Missing required field: " . $field;
        header("Location: service_checklist.php");
        exit();
    }
}

try {
    $conn->beginTransaction();

    // First check if tables exist
    $tables = ['tbl_service_history', 'tbl_service_items', 'tbl_notifications'];
    foreach ($tables as $table) {
        // Only allow alphanumeric and underscore for table names for safety
        if (!preg_match('/^[a-zA-Z0-9_]+$/', $table)) {
            throw new Exception("Invalid table name: $table");
        }
        $stmt = $conn->query("SHOW TABLES LIKE '$table'");
        if (!$stmt->fetch()) {
            throw new Exception("Table $table does not exist");
        }
    }

    // Insert into service history
    $stmt = $conn->prepare("
        INSERT INTO tbl_service_history (
            vehicle_id, mechanic_id, service_date, notes, created_at
        ) VALUES (
            :vehicle_id, :mechanic_id, :service_date, :notes, NOW()
        )
    ");

    $stmt->execute([
        ':vehicle_id' => $_POST['vehicle_id'],
        ':mechanic_id' => $_POST['mechanic_id'],
        ':service_date' => $_POST['service_date'],
        ':notes' => $_POST['notes'] ?? ''
    ]);

    $service_history_id = $conn->lastInsertId();

    // Insert service items
    if (!empty($_POST['services'])) {
        $stmt = $conn->prepare("
            INSERT INTO tbl_service_items (
                service_history_id, service_id, status, created_at
            ) VALUES (
                :service_history_id, :service_id, :status, NOW()
            )
        ");

        foreach ($_POST['services'] as $service_id) {
            $status = isset($_POST['service_status'][$service_id]) ? 
                     $_POST['service_status'][$service_id] : 'not_applicable';
            
            $stmt->execute([
                ':service_history_id' => $service_history_id,
                ':service_id' => $service_id,
                ':status' => $status
            ]);
        }
    }

    // Create notification for customer
    $stmt = $conn->prepare("
        INSERT INTO tbl_notifications (
            type, reference_id, message, recipient_id, is_read, created_at
        ) VALUES (
            :type, :reference_id, :message, :recipient_id, 0, NOW()
        )
    ");

    $stmt->execute([
        ':type' => 'service_checklist',
        ':reference_id' => $service_history_id,
        ':message' => 'A new service checklist has been created for your vehicle. Service date: ' . $_POST['service_date'],
        ':recipient_id' => $_POST['customer_id']
    ]);

    // Update vehicle service date
    $stmt = $conn->prepare("
        UPDATE tbl_info 
        SET service_date = :service_date 
        WHERE id = :vehicle_id
    ");

    $stmt->execute([
        ':service_date' => $_POST['service_date'],
        ':vehicle_id' => $_POST['vehicle_id']
    ]);

    $conn->commit();

    $_SESSION['success'] = "Service checklist has been successfully saved.";
    header("Location: service_checklist.php");
    exit();

} catch (Exception $e) {
    $conn->rollBack();
    error_log("Service Checklist Error: " . $e->getMessage());
    $_SESSION['error'] = "An error occurred while saving the service checklist: " . $e->getMessage();
    header("Location: service_checklist.php");
    exit();
}
?> 