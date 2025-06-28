<?php
// /api/chat/close.php
// Closes a chat. Accepts chat_id and role (admin/worker).

header('Content-Type: application/json');
require_once __DIR__ . '/../../config/database.php';

$response = ["success" => false, "error" => null];

try {
    $pdo = getPDO();
    $input = json_decode(file_get_contents('php://input'), true);

    $chat_id = isset($input['chat_id']) ? intval($input['chat_id']) : null;
    $role = isset($input['role']) ? $input['role'] : null;

    if (!$chat_id || !$role) {
        throw new Exception('chat_id and role are required');
    }

    $stmt = $pdo->prepare("UPDATE tbl_chats SET status = 'closed', updated_at = NOW() WHERE chat_id = ?");
    $stmt->execute([$chat_id]);

    $response['success'] = true;
} catch (Exception $e) {
    $response['error'] = $e->getMessage();
}

echo json_encode($response); 