<?php
session_start();
require_once('../config/database.php');
require_once('../includes/NotificationManager.php');

// Check if user is logged in and has appropriate permissions
if (!isset($_SESSION['admin_id']) && !isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized access']);
    exit;
}

header('Content-Type: application/json');

try {
    $conn->beginTransaction();

    // Get form data
    $order_id = isset($_POST['order_id']) ? intval($_POST['order_id']) : 0;
    $appointment_id = !empty($_POST['appointment_id']) ? intval($_POST['appointment_id']) : null;
    $client_id = intval($_POST['client_id']);
    $license_plate = trim($_POST['license_plate']);
    $car_model = trim($_POST['car_model']);
    $technician_id = intval($_POST['technician_id']);
    $status = $_POST['status'];
    $services = isset($_POST['services']) ? (array)$_POST['services'] : [];

    // Validate required fields
    if (empty($client_id) || empty($license_plate) || empty($car_model) || empty($technician_id) || empty($services)) {
        throw new Exception('Please fill in all required fields');
    }

    if ($order_id) {
        // Update existing order
        $query = "UPDATE tbl_service_orders SET 
                    appointment_id = :appointment_id,
                    client_id = :client_id,
                    license_plate = :license_plate,
                    car_model = :car_model,
                    technician_id = :technician_id,
                    status = :status
                 WHERE id = :order_id";
        
        $stmt = $conn->prepare($query);
        $stmt->execute([
            'appointment_id' => $appointment_id,
            'client_id' => $client_id,
            'license_plate' => $license_plate,
            'car_model' => $car_model,
            'technician_id' => $technician_id,
            'status' => $status,
            'order_id' => $order_id
        ]);

        // Delete existing services
        $delete_services = "DELETE FROM tbl_order_services WHERE order_id = :order_id";
        $stmt = $conn->prepare($delete_services);
        $stmt->execute(['order_id' => $order_id]);
    } else {
        // Create new order
        $query = "INSERT INTO tbl_service_orders 
                    (appointment_id, client_id, license_plate, car_model, technician_id, status) 
                 VALUES 
                    (:appointment_id, :client_id, :license_plate, :car_model, :technician_id, :status)";
        
        $stmt = $conn->prepare($query);
        $stmt->execute([
            'appointment_id' => $appointment_id,
            'client_id' => $client_id,
            'license_plate' => $license_plate,
            'car_model' => $car_model,
            'technician_id' => $technician_id,
            'status' => $status
        ]);
        
        $order_id = $conn->lastInsertId();
    }

    // Insert services
    $service_query = "INSERT INTO tbl_order_services (order_id, service_id) VALUES (:order_id, :service_id)";
    $stmt = $conn->prepare($service_query);
    
    foreach ($services as $service_id) {
        $stmt->execute([
            'order_id' => $order_id,
            'service_id' => intval($service_id)
        ]);
    }

    // Send notifications after successful order creation/update
    $notificationManager = new NotificationManager($conn);
    
    // Get client details for notification
    $stmt = $conn->prepare("
        SELECT u.id as user_id, u.name, u.email, u.phonenum
        FROM tbl_user u
        WHERE u.id = ?
    ");
    $stmt->execute([$client_id]);
    $client = $stmt->fetch(PDO::FETCH_ASSOC);
    
    // Get technician details
    $stmt = $conn->prepare("
        SELECT full_name, email
        FROM tbl_worker
        WHERE id = ?
    ");
    $stmt->execute([$technician_id]);
    $technician = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($client) {
        // Notify customer about order status
        $customer_notification = [
            'message' => "Your service order for {$car_model} ({$license_plate}) has been " . ($order_id ? 'updated' : 'created') . " with status: {$status}",
            'email' => $client['email'],
            'name' => $client['name'],
            'subject' => 'Service Order Update',
            'template_key' => 'service_order_update',
            'variables' => [
                'customer_name' => $client['name'],
                'car_model' => $car_model,
                'license_plate' => $license_plate,
                'status' => ucfirst($status),
                'technician_name' => $technician['full_name'] ?? 'Assigned Technician'
            ]
        ];
        
        $notificationManager->sendNotification('service_order', $client['user_id'], $customer_notification, ['web']);
        
        // Notify technician about assignment
        if ($technician && $technician['email']) {
            $technician_notification = [
                'message' => "You have been assigned to service order #{$order_id} for {$client['name']}'s {$car_model}",
                'email' => $technician['email'],
                'name' => $technician['full_name'],
                'subject' => 'New Work Assignment',
                'template_key' => 'technician_assignment',
                'variables' => [
                    'technician_name' => $technician['full_name'],
                    'customer_name' => $client['name'],
                    'car_model' => $car_model,
                    'license_plate' => $license_plate,
                    'order_id' => $order_id
                ]
            ];
            
            $notificationManager->sendNotification('work_assignment', $technician_id, $technician_notification, ['web']);
        }
        
        // Notify admin about the order activity
        if (isset($_SESSION['admin_id'])) {
            $admin_notification = [
                'message' => "Service order #{$order_id} " . ($order_id ? 'updated' : 'created') . " for {$client['name']}'s {$car_model}"
            ];
            
            $notificationManager->sendNotification('system', $_SESSION['admin_id'], $admin_notification, ['web']);
        }
    }
    
    $conn->commit();
    
    echo json_encode([
        'status' => 'success',
        'message' => 'Order saved successfully',
        'order_id' => $order_id
    ]);

} catch(Exception $e) {
    $conn->rollBack();
    
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage()
    ]);
}

// Handle DELETE request
if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
    $order_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
    
    if ($order_id) {
        try {
            $conn->beginTransaction();
            
            // Delete order services
            $delete_services = "DELETE FROM tbl_order_services WHERE order_id = :order_id";
            $stmt = $conn->prepare($delete_services);
            $stmt->execute(['order_id' => $order_id]);
            
            // Delete the order
            $delete_order = "DELETE FROM tbl_service_orders WHERE id = :order_id";
            $stmt = $conn->prepare($delete_order);
            $stmt->execute(['order_id' => $order_id]);
            
            $conn->commit();
            echo json_encode(['status' => 'success', 'message' => 'Order deleted successfully']);
        } catch (PDOException $e) {
            $conn->rollBack();
            echo json_encode(['status' => 'error', 'message' => 'Error deleting order: ' . $e->getMessage()]);
        }
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Invalid order ID']);
    }
    exit;
}
?> 