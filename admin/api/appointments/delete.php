<?php
header('Content-Type: application/json');
session_start();

// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'error' => 'Unauthorized']);
    exit();
}

require_once('../../../config/database.php');
require_once('../../includes/dashboard_functions.php');

// Get JSON input
$input = json_decode(file_get_contents('php://input'), true);

// Validate input
if (!isset($input['id'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Missing appointment ID']);
    exit();
}

// Delete appointment
$result = deleteAppointment($conn, $input['id']);
echo json_encode($result); 