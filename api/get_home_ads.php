<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

header('Content-Type: application/json');
require_once __DIR__ . '/../config/database.php';

try {
    // Fetch active ads for home page (limit 2 for the grid layout)
    $stmt = $conn->prepare("
        SELECT id, title, description, target_url, status, priority
        FROM tbl_ads 
        WHERE status = 'active' 
        ORDER BY priority DESC, RAND() 
        LIMIT 2
    ");
    $stmt->execute();
    $ads = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Format ads for frontend
    $formatted_ads = [];
    foreach ($ads as $ad) {
        $formatted_ads[] = [
            'id' => $ad['id'],
            'title' => $ad['title'] ?: 'Special Offer',
            'description' => $ad['description'] ?: 'Limited time offer on our premium services',
            'image' => 'assets/images/gallery/car_heat.jpg',
            'badge' => [ 'text' => 'Hot Deal', 'color' => 'danger' ],
            'pricing' => [ 'original' => null, 'sale' => null ],
            'cta' => [ 'text' => 'Learn More', 'icon' => 'fas fa-arrow-right', 'url' => $ad['target_url'] ?: 'service.php' ]
        ];
    }
    
    // If no ads found, provide fallback static content
    if (empty($formatted_ads)) {
        $formatted_ads = [
            [
                'id' => 'fallback-1',
                'title' => 'Engine Service Special',
                'description' => 'Get 20% off on complete engine diagnostics and tune-up services this month.',
                'image' => 'assets/images/gallery/car_heat.jpg',
                'badge' => ['text' => 'Hot Deal', 'color' => 'danger'],
                'pricing' => ['original' => 'ETB 1,500', 'sale' => 'ETB 1,200'],
                'cta' => ['text' => 'Book Now', 'icon' => 'fas fa-calendar-check', 'url' => 'service.php']
            ],
            [
                'id' => 'fallback-2',
                'title' => 'GPS Tracking Package',
                'description' => 'Premium GPS tracking with mobile app access and real-time monitoring.',
                'image' => 'assets/images/gallery/dashboard_lights.jpg',
                'badge' => ['text' => 'New', 'color' => 'success'],
                'pricing' => ['original' => 'ETB 3,000', 'sale' => 'ETB 2,500'],
                'cta' => ['text' => 'Learn More', 'icon' => 'fas fa-map-marker-alt', 'url' => 'service.php?category=gps']
            ]
        ];
    }
    
    echo json_encode([
        'success' => true,
        'data' => $formatted_ads,
        'count' => count($formatted_ads)
    ]);
    
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Database error: ' . $e->getMessage(),
        'fallback' => [
            [
                'id' => 'error-fallback-1',
                'title' => 'Professional Services',
                'description' => 'Quality automotive care for your vehicle.',
                'image' => 'assets/images/gallery/car_heat.jpg',
                'badge' => ['text' => 'Available', 'color' => 'primary'],
                'pricing' => ['original' => null, 'sale' => null],
                'cta' => ['text' => 'Learn More', 'icon' => 'fas fa-info', 'url' => 'service.php']
            ]
        ]
    ]);
} 