<?php
ob_clean();
header('Content-Type: application/json');
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/../config/database.php';

try {
    // Get user ID from session
    $userId = $_SESSION['user_id'] ?? null;
    
    if (!$userId) {
        // Provide demo data for showcase
        echo json_encode([
            'success' => true,
            'unread_count' => 4,
            'recent_notifications' => [
                [
                    'id' => 'demo-1',
                    'title' => 'Service Reminder',
                    'message' => 'Your Toyota Corolla service is due in 3 days',
                    'type' => 'service_reminder',
                    'icon' => 'fas fa-wrench',
                    'time' => '2 hours ago',
                    'is_read' => false,
                    'priority' => 'high',
                    'action_url' => 'book-service.php?car=demo-1'
                ],
                [
                    'id' => 'demo-2',
                    'title' => 'Insurance Expiry',
                    'message' => 'Your vehicle insurance expires soon',
                    'type' => 'insurance_expiry',
                    'icon' => 'fas fa-shield-alt',
                    'time' => '1 day ago',
                    'is_read' => false,
                    'priority' => 'urgent',
                    'action_url' => 'renew-insurance.php'
                ],
                [
                    'id' => 'demo-3',
                    'title' => 'Appointment Confirmed',
                    'message' => 'Your service appointment has been confirmed for tomorrow',
                    'type' => 'appointment_confirmed',
                    'icon' => 'fas fa-calendar-check',
                    'time' => '2 days ago',
                    'is_read' => false,
                    'priority' => 'medium',
                    'action_url' => 'appointments.php'
                ],
                [
                    'id' => 'demo-4',
                    'title' => 'New Offer Available',
                    'message' => '20% off on engine diagnostics this month',
                    'type' => 'promotion',
                    'icon' => 'fas fa-tag',
                    'time' => '3 days ago',
                    'is_read' => false,
                    'priority' => 'low',
                    'action_url' => 'service.php?offer=engine-diagnostics'
                ]
            ],
            'message' => 'Demo notifications - user not logged in'
        ]);
        exit;
    }
    
    // Get unread notification count
    $count_stmt = $conn->prepare("
        SELECT COUNT(*) as unread_count 
        FROM tbl_notifications 
        WHERE recipient_id = ? AND is_read = 0
    ");
    $count_stmt->execute([$userId]);
    $unread_count = $count_stmt->fetch(PDO::FETCH_ASSOC)['unread_count'];
    
    // Get recent notifications (last 10)
    $notifications_stmt = $conn->prepare("
        SELECT 
            id,
            type,
            message,
            is_read,
            created_at
        FROM tbl_notifications 
        WHERE recipient_id = ? 
        ORDER BY created_at DESC 
        LIMIT 10
    ");
    $notifications_stmt->execute([$userId]);
    $notifications = $notifications_stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Format notifications for frontend
    $formatted_notifications = [];
    foreach ($notifications as $notification) {
        $created_time = new DateTime($notification['created_at']);
        $now = new DateTime();
        $diff = $now->diff($created_time);
        $time_ago = '';
        if ($diff->days > 0) {
            $time_ago = $diff->days . ' day' . ($diff->days > 1 ? 's' : '') . ' ago';
        } elseif ($diff->h > 0) {
            $time_ago = $diff->h . ' hour' . ($diff->h > 1 ? 's' : '') . ' ago';
        } elseif ($diff->i > 0) {
            $time_ago = $diff->i . ' minute' . ($diff->i > 1 ? 's' : '') . ' ago';
        } else {
            $time_ago = 'Just now';
        }
        $formatted_notifications[] = [
            'id' => $notification['id'],
            'type' => $notification['type'],
            'message' => $notification['message'],
            'is_read' => (bool)$notification['is_read'],
            'time' => $time_ago
        ];
    }
    
    // If no notifications found, provide sample notifications
    if (empty($formatted_notifications)) {
        $formatted_notifications = [
            [
                'id' => 'welcome-1',
                'title' => 'Welcome to Nati Automotive',
                'message' => 'Thank you for joining our service platform',
                'type' => 'system_update',
                'icon' => 'fas fa-star',
                'time' => 'Just now',
                'is_read' => false,
                'priority' => 'low',
                'action_url' => 'profile.php'
            ]
        ];
        $unread_count = 1;
    }
    
    echo json_encode([
        'success' => true,
        'unread_count' => $unread_count,
        'recent_notifications' => $formatted_notifications,
        'total_notifications' => count($formatted_notifications)
    ]);
    
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Database error: ' . $e->getMessage(),
        'unread_count' => 0,
        'recent_notifications' => [],
        'fallback' => [
            'title' => 'System Error',
            'message' => 'Unable to load notifications at this time',
            'action' => 'Please try again later'
        ]
    ]);
}
?> 