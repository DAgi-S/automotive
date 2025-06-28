<?php
session_start();

// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

// Include database connection and functions
require_once('../../config/database.php');
require_once('../includes/notification_functions.php');

// Get POST data
$data = json_decode(file_get_contents('php://input'), true);
$notification_id = $data['notification_id'] ?? null;

if (!$notification_id) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Notification ID is required']);
    exit();
}

// Mark notification as read using the function
$success = markNotificationAsRead($conn, $notification_id, $_SESSION['admin_id']);

// Get updated notification count
$count = $success ? getUnreadNotificationsCount($conn, $_SESSION['admin_id']) : null;

header('Content-Type: application/json');
echo json_encode([
    'success' => $success,
    'unreadCount' => $count
]); 