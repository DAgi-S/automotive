<?php
// /api/chat/start.php
// Starts a new chat for a user or guest. Returns chat_id and status.

header('Content-Type: application/json');
require_once __DIR__ . '/../../config/database.php';

$response = ["success" => false, "error" => null];

try {
    $pdo = $conn;
    $input = json_decode(file_get_contents('php://input'), true);

    $user_id = isset($input['user_id']) ? intval($input['user_id']) : null;
    $guest_id = isset($input['guest_id']) ? intval($input['guest_id']) : null;
    $admin_id = null; // Not assigned at start
    $worker_id = null; // Not assigned at start
    $assigned_by = null;
    $status = 'open';

    if (!$user_id && !$guest_id) {
        throw new Exception('user_id or guest_id is required');
    }

    $stmt = $pdo->prepare("INSERT INTO tbl_chats (user_id, guest_id, admin_id, worker_id, assigned_by, status, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?, NOW(), NOW())");
    $stmt->execute([$user_id, $guest_id, $admin_id, $worker_id, $assigned_by, $status]);
    $chat_id = $pdo->lastInsertId();

    $response['success'] = true;
    $response['chat_id'] = $chat_id;
    $response['status'] = $status;
} catch (Exception $e) {
    $response['error'] = $e->getMessage();
}

echo json_encode($response); 