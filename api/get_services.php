<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../config/database.php';

try {
    // Fetch active services from database
    $stmt = $conn->prepare("SELECT service_id, service_name, icon_class, description, price, duration FROM tbl_services WHERE status = 'active' ORDER BY service_name");
    $stmt->execute();
    $services = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Format the data for frontend
    $formatted_services = [];
    foreach ($services as $service) {
        $formatted_services[] = [
            'id' => $service['service_id'],
            'icon' => $service['icon_class'] ?: 'fa-wrench',
            'title' => $service['service_name'],
            'description' => $service['description'],
            'price' => 'Starting at Br ' . number_format($service['price'], 0),
            'duration' => $service['duration'] ?: '30-60 minutes'
        ];
    }
    
    echo json_encode([
        'success' => true,
        'data' => $formatted_services
    ]);
    
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Database error: ' . $e->getMessage()
    ]);
} 