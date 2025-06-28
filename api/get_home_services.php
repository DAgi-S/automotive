<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../config/database.php';

try {
    // Get featured services limit from query parameter
    $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 6;
    
    // Fetch featured services from database
    $stmt = $conn->prepare("
        SELECT 
            service_id,
            service_name,
            icon_class,
            description,
            price,
            duration,
            status,
            created_at
        FROM tbl_services 
        WHERE status = 'active' 
        ORDER BY service_id ASC 
        LIMIT " . $limit
    );
    $stmt->execute();
    $services = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Format services for frontend
    $formatted_services = [];
    foreach ($services as $service) {
        $formatted_services[] = [
            'id' => $service['service_id'],
            'name' => $service['service_name'],
            'description' => $service['description'] ?: 'Professional automotive service',
            'icon' => $service['icon_class'] ?: 'fas fa-wrench',
            'price' => $service['price'] ? 'ETB ' . number_format($service['price'], 0) : 'Contact for pricing',
            'duration' => $service['duration'] ?: '1-2 hours',
            'features' => [
                'Professional technicians',
                'Quality parts & materials',
                'Warranty included',
                'Free consultation'
            ],
            'booking_url' => 'service.php?book=' . $service['service_id'],
            'details_url' => 'service-details.php?id=' . $service['service_id'],
                         'category' => getServiceCategory($service['service_name'])
        ];
    }
    
    // If no services found, provide fallback static content
    if (empty($formatted_services)) {
        $formatted_services = [
            [
                'id' => 'fallback-1',
                'name' => 'Engine Diagnostics',
                'description' => 'Complete engine health check and diagnostic service',
                'icon' => 'fas fa-search',
                'price' => 'ETB 500',
                'duration' => '1 hour',
                'features' => ['Advanced diagnostic tools', 'Detailed report', 'Expert analysis'],
                'booking_url' => 'service.php?book=diagnostics',
                'details_url' => 'service-details.php?service=diagnostics',
                'category' => 'diagnostics'
            ],
            [
                'id' => 'fallback-2',
                'name' => 'Oil Change Service',
                'description' => 'Premium oil change with filter replacement',
                'icon' => 'fas fa-oil-can',
                'price' => 'ETB 800',
                'duration' => '30 minutes',
                'features' => ['High-quality oil', 'Filter replacement', 'Quick service'],
                'booking_url' => 'service.php?book=oil-change',
                'details_url' => 'service-details.php?service=oil-change',
                'category' => 'maintenance'
            ],
            [
                'id' => 'fallback-3',
                'name' => 'Brake Inspection',
                'description' => 'Comprehensive brake system check and service',
                'icon' => 'fas fa-stop-circle',
                'price' => 'ETB 600',
                'duration' => '45 minutes',
                'features' => ['Safety inspection', 'Brake pad check', 'Fluid top-up'],
                'booking_url' => 'service.php?book=brake-service',
                'details_url' => 'service-details.php?service=brake-service',
                'category' => 'safety'
            ],
            [
                'id' => 'fallback-4',
                'name' => 'GPS Installation',
                'description' => 'Professional GPS tracking system installation',
                'icon' => 'fas fa-map-marker-alt',
                'price' => 'ETB 2,500',
                'duration' => '2 hours',
                'features' => ['Real-time tracking', 'Mobile app access', '1-year warranty'],
                'booking_url' => 'service.php?book=gps-installation',
                'details_url' => 'service-details.php?service=gps-installation',
                'category' => 'technology'
            ],
            [
                'id' => 'fallback-5',
                'name' => 'Battery Service',
                'description' => 'Battery testing, charging, and replacement service',
                'icon' => 'fas fa-battery-three-quarters',
                'price' => 'ETB 400',
                'duration' => '20 minutes',
                'features' => ['Battery testing', 'Terminal cleaning', 'Performance check'],
                'booking_url' => 'service.php?book=battery-service',
                'details_url' => 'service-details.php?service=battery-service',
                'category' => 'electrical'
            ],
            [
                'id' => 'fallback-6',
                'name' => 'Tire Service',
                'description' => 'Tire rotation, balancing, and pressure check',
                'icon' => 'fas fa-circle',
                'price' => 'ETB 300',
                'duration' => '40 minutes',
                'features' => ['Tire rotation', 'Wheel balancing', 'Pressure check'],
                'booking_url' => 'service.php?book=tire-service',
                'details_url' => 'service-details.php?service=tire-service',
                'category' => 'maintenance'
            ]
        ];
    }
    
    // Get service statistics
    $stats_stmt = $conn->query("
        SELECT 
            COUNT(*) as total_services,
            COUNT(CASE WHEN status = 'active' THEN 1 END) as active_services,
            AVG(price) as average_price
        FROM tbl_services
    ");
    $stats = $stats_stmt->fetch(PDO::FETCH_ASSOC);
    
    echo json_encode([
        'success' => true,
        'data' => $formatted_services,
        'count' => count($formatted_services),
        'stats' => [
            'total_services' => $stats['total_services'] ?? 0,
            'active_services' => $stats['active_services'] ?? 0,
            'average_price' => $stats['average_price'] ? 'ETB ' . number_format($stats['average_price'], 0) : 'Varies'
        ],
        'categories' => [
            'diagnostics' => 'Diagnostic Services',
            'maintenance' => 'Maintenance Services', 
            'safety' => 'Safety Services',
            'technology' => 'Technology Services',
            'electrical' => 'Electrical Services'
        ]
    ]);
    
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Database error: ' . $e->getMessage(),
        'fallback' => [
            [
                'id' => 'error-fallback-1',
                'name' => 'General Service',
                'description' => 'Professional automotive service available',
                'icon' => 'fas fa-wrench',
                'price' => 'Contact us',
                'duration' => 'Varies',
                'features' => ['Professional service', 'Quality guarantee'],
                'booking_url' => 'service.php',
                'details_url' => 'service.php',
                'category' => 'general'
            ]
        ]
    ]);
}

/**
 * Helper function to categorize services
 */
function getServiceCategory($serviceName) {
    $serviceName = strtolower($serviceName);
    
    if (strpos($serviceName, 'diagnostic') !== false || strpos($serviceName, 'check') !== false) {
        return 'diagnostics';
    } elseif (strpos($serviceName, 'oil') !== false || strpos($serviceName, 'maintenance') !== false) {
        return 'maintenance';
    } elseif (strpos($serviceName, 'brake') !== false || strpos($serviceName, 'safety') !== false) {
        return 'safety';
    } elseif (strpos($serviceName, 'gps') !== false || strpos($serviceName, 'tracking') !== false) {
        return 'technology';
    } elseif (strpos($serviceName, 'battery') !== false || strpos($serviceName, 'electrical') !== false) {
        return 'electrical';
    } else {
        return 'general';
    }
}
?> 