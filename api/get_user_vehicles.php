<?php
ob_clean();
header('Content-Type: application/json');
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../config/database.php';

try {
    // Get user ID from session (fallback to demo data if not logged in)
    $userId = $_SESSION['user_id'] ?? null;
    
    if (!$userId) {
        // Provide demo data for showcase
        echo json_encode([
            'success' => true,
            'data' => [
                [
                    'vehicle_id' => 'demo-1',
                    'make' => 'Toyota',
                    'model' => 'Corolla',
                    'year' => 2020,
                    'license_plate' => 'አ.አ 12345',
                    'status' => 'urgent',
                    'notifications' => [
                        [
                            'type' => 'insurance_expiry',
                            'icon' => 'fas fa-shield-alt',
                            'title' => 'Insurance Expiring',
                            'message' => 'Expires in 3 days (Jan 23, 2025)',
                            'priority' => 'urgent',
                            'action_url' => 'renew-insurance.php?car=demo-1'
                        ],
                        [
                            'type' => 'service_due',
                            'icon' => 'fas fa-wrench',
                            'title' => 'Service Due',
                            'message' => 'Oil change due in 500 km',
                            'priority' => 'warning',
                            'action_url' => 'book-service.php?car=demo-1'
                        ]
                    ],
                    'actions' => [
                        [
                            'text' => 'Renew Insurance',
                            'url' => 'renew-insurance.php?car=demo-1',
                            'class' => 'btn-danger',
                            'icon' => 'fas fa-shield-alt'
                        ],
                        [
                            'text' => 'Book Service',
                            'url' => 'book-service.php?car=demo-1',
                            'class' => 'btn-warning',
                            'icon' => 'fas fa-calendar-plus'
                        ]
                    ]
                ],
                [
                    'vehicle_id' => 'demo-2',
                    'make' => 'Hyundai',
                    'model' => 'Elantra',
                    'year' => 2021,
                    'license_plate' => 'አ.አ 67890',
                    'status' => 'good',
                    'notifications' => [
                        [
                            'type' => 'next_service',
                            'icon' => 'fas fa-calendar-check',
                            'title' => 'Next Service',
                            'message' => 'Due in 45 days (Mar 10, 2025)',
                            'priority' => 'info',
                            'action_url' => 'service-schedule.php?car=demo-2'
                        ],
                        [
                            'type' => 'insurance_valid',
                            'icon' => 'fas fa-shield-alt',
                            'title' => 'Insurance Valid',
                            'message' => 'Until Aug 15, 2025',
                            'priority' => 'success',
                            'action_url' => 'insurance-details.php?car=demo-2'
                        ]
                    ],
                    'actions' => [
                        [
                            'text' => 'View Details',
                            'url' => 'car-details.php?car=demo-2',
                            'class' => 'btn-primary',
                            'icon' => 'fas fa-eye'
                        ],
                        [
                            'text' => 'History',
                            'url' => 'service-history.php?car=demo-2',
                            'class' => 'btn-outline-primary',
                            'icon' => 'fas fa-history'
                        ]
                    ]
                ],
                [
                    'vehicle_id' => 'demo-3',
                    'make' => 'Suzuki',
                    'model' => 'Swift',
                    'year' => 2019,
                    'license_plate' => 'አ.አ 11223',
                    'status' => 'warning',
                    'notifications' => [
                        [
                            'type' => 'registration_renewal',
                            'icon' => 'fas fa-file-alt',
                            'title' => 'Registration Renewal',
                            'message' => 'Due in 15 days (Feb 5, 2025)',
                            'priority' => 'warning',
                            'action_url' => 'renew-registration.php?car=demo-3'
                        ],
                        [
                            'type' => 'mileage_check',
                            'icon' => 'fas fa-tachometer-alt',
                            'title' => 'Mileage Check',
                            'message' => '45,000 km - Service recommended',
                            'priority' => 'info',
                            'action_url' => 'book-service.php?car=demo-3'
                        ]
                    ],
                    'actions' => [
                        [
                            'text' => 'Renew Registration',
                            'url' => 'renew-registration.php?car=demo-3',
                            'class' => 'btn-warning',
                            'icon' => 'fas fa-file-alt'
                        ],
                        [
                            'text' => 'Schedule Service',
                            'url' => 'book-service.php?car=demo-3',
                            'class' => 'btn-outline-warning',
                            'icon' => 'fas fa-tools'
                        ]
                    ]
                ]
            ],
            'message' => 'Demo data provided - user not logged in'
        ]);
        exit;
    }
    
    // Fetch user vehicles with service history and appointments
    $stmt = $conn->prepare("
        SELECT 
            v.vehicle_id,
            v.make,
            v.model,
            v.year,
            v.license_plate,
            v.last_service_date,
            v.created_at,
            u.name as owner_name,
            u.email as owner_email
        FROM tbl_vehicles v
        LEFT JOIN tbl_user u ON v.user_id = u.id
        WHERE v.user_id = ?
        ORDER BY v.created_at DESC
    ");
    $stmt->execute([$userId]);
    $vehicles = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    $formatted_vehicles = [];
    foreach ($vehicles as $vehicle) {
        // Calculate vehicle status based on service dates and appointments
        $status = 'good';
        $notifications = [];
        $actions = [];
        
        // Check service due date (simulate based on last service)
        if ($vehicle['last_service_date']) {
            $lastService = new DateTime($vehicle['last_service_date']);
            $now = new DateTime();
            $daysSinceService = $now->diff($lastService)->days;
            
            if ($daysSinceService > 180) { // 6 months
                $status = 'urgent';
                $notifications[] = [
                    'type' => 'service_overdue',
                    'icon' => 'fas fa-exclamation-triangle',
                    'title' => 'Service Overdue',
                    'message' => 'Last service was ' . $daysSinceService . ' days ago',
                    'priority' => 'urgent',
                    'action_url' => 'book-service.php?car=' . $vehicle['vehicle_id']
                ];
            } elseif ($daysSinceService > 150) { // 5 months
                $status = 'warning';
                $notifications[] = [
                    'type' => 'service_due',
                    'icon' => 'fas fa-wrench',
                    'title' => 'Service Due Soon',
                    'message' => 'Due in ' . (180 - $daysSinceService) . ' days',
                    'priority' => 'warning',
                    'action_url' => 'book-service.php?car=' . $vehicle['vehicle_id']
                ];
            }
        } else {
            // No service history
            $notifications[] = [
                'type' => 'no_service_history',
                'icon' => 'fas fa-info-circle',
                'title' => 'No Service History',
                'message' => 'Schedule your first service',
                'priority' => 'info',
                'action_url' => 'book-service.php?car=' . $vehicle['vehicle_id']
            ];
        }
        
        // Simulate insurance expiry (random dates for demo)
        $insuranceExpiry = (new DateTime())->add(new DateInterval('P' . rand(5, 365) . 'D'));
        $daysToExpiry = (new DateTime())->diff($insuranceExpiry)->days;
        
        if ($daysToExpiry <= 7) {
            $status = 'urgent';
            $notifications[] = [
                'type' => 'insurance_expiry',
                'icon' => 'fas fa-shield-alt',
                'title' => 'Insurance Expiring',
                'message' => 'Expires in ' . $daysToExpiry . ' days',
                'priority' => 'urgent',
                'action_url' => 'renew-insurance.php?car=' . $vehicle['vehicle_id']
            ];
        } elseif ($daysToExpiry <= 30) {
            if ($status !== 'urgent') $status = 'warning';
            $notifications[] = [
                'type' => 'insurance_reminder',
                'icon' => 'fas fa-shield-alt',
                'title' => 'Insurance Reminder',
                'message' => 'Expires in ' . $daysToExpiry . ' days',
                'priority' => 'warning',
                'action_url' => 'renew-insurance.php?car=' . $vehicle['vehicle_id']
            ];
        }
        
        // Generate actions based on status
        if ($status === 'urgent') {
            $actions[] = [
                'text' => 'Urgent Action Required',
                'url' => 'vehicle-alerts.php?car=' . $vehicle['vehicle_id'],
                'class' => 'btn-danger',
                'icon' => 'fas fa-exclamation-triangle'
            ];
        }
        
        $actions[] = [
            'text' => 'View Details',
            'url' => 'car-details.php?car=' . $vehicle['vehicle_id'],
            'class' => 'btn-primary',
            'icon' => 'fas fa-eye'
        ];
        
        $actions[] = [
            'text' => 'Service History',
            'url' => 'service-history.php?car=' . $vehicle['vehicle_id'],
            'class' => 'btn-outline-primary',
            'icon' => 'fas fa-history'
        ];
        
        $formatted_vehicles[] = [
            'vehicle_id' => $vehicle['vehicle_id'],
            'make' => $vehicle['make'],
            'model' => $vehicle['model'],
            'year' => $vehicle['year'],
            'license_plate' => $vehicle['license_plate'],
            'status' => $status,
            'notifications' => $notifications,
            'actions' => $actions,
            'stats' => [
                'total_appointments' => 0,
                'service_count' => 0,
                'last_service' => $vehicle['last_service_date']
            ]
        ];
    }
    
    // If no vehicles found, suggest adding one
    if (empty($formatted_vehicles)) {
        echo json_encode([
            'success' => true,
            'data' => [],
            'message' => 'No vehicles registered',
            'suggestion' => [
                'title' => 'Add Your First Vehicle',
                'description' => 'Register your vehicle to track service history and get maintenance reminders.',
                'action' => [
                    'text' => 'Add Vehicle',
                    'url' => 'add-vehicle.php',
                    'class' => 'btn-success',
                    'icon' => 'fas fa-plus'
                ]
            ]
        ]);
        exit;
    }
    
    echo json_encode([
        'success' => true,
        'data' => $formatted_vehicles,
        'count' => count($formatted_vehicles)
    ]);
    
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Database error: ' . $e->getMessage(),
        'fallback' => [
            [
                'vehicle_id' => 'error-fallback',
                'make' => 'Demo',
                'model' => 'Vehicle',
                'year' => 2020,
                'license_plate' => 'DEMO 001',
                'status' => 'good',
                'notifications' => [
                    [
                        'type' => 'system_error',
                        'icon' => 'fas fa-exclamation-circle',
                        'title' => 'System Error',
                        'message' => 'Unable to load vehicle data',
                        'priority' => 'warning',
                        'action_url' => '#'
                    ]
                ],
                'actions' => [
                    [
                        'text' => 'Retry',
                        'url' => 'javascript:location.reload()',
                        'class' => 'btn-primary',
                        'icon' => 'fas fa-refresh'
                    ]
                ]
            ]
        ]
    ]);
}
?> 