<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../config/database.php';

try {
    $analytics = [];
    
    // Get total services provided (completed appointments)
    $stmt = $conn->prepare("SELECT COUNT(*) as completed_services FROM tbl_appointments WHERE status = 'completed'");
    $stmt->execute();
    $completed_services = $stmt->fetch(PDO::FETCH_ASSOC)['completed_services'] ?? 0;
    
    // Get total registered clients
    $stmt = $conn->prepare("SELECT COUNT(*) as total_clients FROM tbl_user");
    $stmt->execute();
    $total_clients = $stmt->fetch(PDO::FETCH_ASSOC)['total_clients'] ?? 0;
    
    // Get total vehicles registered
    $stmt = $conn->prepare("SELECT COUNT(*) as total_vehicles FROM tbl_vehicles");
    $stmt->execute();
    $total_vehicles = $stmt->fetch(PDO::FETCH_ASSOC)['total_vehicles'] ?? 0;
    
    // Get man power (workers)
    $stmt = $conn->prepare("SELECT COUNT(*) as total_workers FROM tbl_worker");
    $stmt->execute();
    $total_workers = $stmt->fetch(PDO::FETCH_ASSOC)['total_workers'] ?? 0;
    
    // Get total appointments (users using app for reminders)
    $stmt = $conn->prepare("SELECT COUNT(*) as total_appointments FROM tbl_appointments");
    $stmt->execute();
    $total_appointments = $stmt->fetch(PDO::FETCH_ASSOC)['total_appointments'] ?? 0;
    
    // Get active users (users who have made appointments)
    $stmt = $conn->prepare("SELECT COUNT(DISTINCT user_id) as active_users FROM tbl_appointments");
    $stmt->execute();
    $active_users = $stmt->fetch(PDO::FETCH_ASSOC)['active_users'] ?? 0;
    
    // Get appointment status breakdown
    $stmt = $conn->prepare("SELECT status, COUNT(*) as count FROM tbl_appointments GROUP BY status");
    $stmt->execute();
    $appointment_status = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Get monthly service trends (last 6 months)
    $stmt = $conn->prepare("
        SELECT 
            DATE_FORMAT(created_at, '%Y-%m') as month,
            COUNT(*) as appointments
        FROM tbl_appointments 
        WHERE created_at >= DATE_SUB(NOW(), INTERVAL 6 MONTH)
        GROUP BY DATE_FORMAT(created_at, '%Y-%m')
        ORDER BY month ASC
    ");
    $stmt->execute();
    $monthly_trends = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Get top services (most booked)
    $stmt = $conn->prepare("
        SELECT 
            s.service_name,
            COUNT(a.appointment_id) as bookings
        FROM tbl_services s
        LEFT JOIN tbl_appointments a ON s.service_id = a.service_id
        GROUP BY s.service_id, s.service_name
        ORDER BY bookings DESC
        LIMIT 5
    ");
    $stmt->execute();
    $top_services = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    $analytics = [
        'overview' => [
            'services_provided' => $completed_services,
            'registered_clients' => $total_clients,
            'vehicles_serviced' => $total_vehicles,
            'expert_technicians' => $total_workers,
            'app_users' => $active_users,
            'total_bookings' => $total_appointments
        ],
        'appointment_status' => $appointment_status,
        'monthly_trends' => $monthly_trends,
        'top_services' => $top_services,
        'last_updated' => date('Y-m-d H:i:s')
    ];
    
    echo json_encode([
        'success' => true,
        'data' => $analytics
    ]);
    
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Database error: ' . $e->getMessage()
    ]);
} 