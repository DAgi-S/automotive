<?php
// /api/chat/assign_worker.php
// Admin assigns a worker to a chat. Accepts chat_id, worker_id, assigned_by (admin_id).

header('Content-Type: application/json');
require_once __DIR__ . '/../../config/database.php';

$response = ["success" => false, "error" => null];

try {
    $pdo = $conn;
    $input = json_decode(file_get_contents('php://input'), true);

    $chat_id = isset($input['chat_id']) ? intval($input['chat_id']) : null;
    $worker_id = isset($input['worker_id']) ? intval($input['worker_id']) : null;
    $assigned_by = isset($input['assigned_by']) ? intval($input['assigned_by']) : null;

    if (!$chat_id || !$worker_id || !$assigned_by) {
        throw new Exception('chat_id, worker_id, and assigned_by are required');
    }

    $stmt = $pdo->prepare("UPDATE tbl_chats SET worker_id = ?, assigned_by = ?, updated_at = NOW() WHERE chat_id = ?");
    $stmt->execute([$worker_id, $assigned_by, $chat_id]);

    $response['success'] = true;
} catch (Exception $e) {
    $response['error'] = $e->getMessage();
}

echo json_encode($response); 