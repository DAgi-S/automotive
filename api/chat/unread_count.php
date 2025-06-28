<?php
// /api/chat/unread_count.php
// Gets the count of unread messages for a chat and participant.

header('Content-Type: application/json');
require_once __DIR__ . '/../../config/database.php';

$response = ["success" => false, "error" => null, "unread_count" => 0];

try {
    $pdo = getPDO();
    $chat_id = isset($_GET['chat_id']) ? intval($_GET['chat_id']) : null;
    $sender_type = isset($_GET['sender_type']) ? $_GET['sender_type'] : null;
    $sender_id = isset($_GET['sender_id']) ? intval($_GET['sender_id']) : null;

    if (!$chat_id || !$sender_type || !$sender_id) {
        throw new Exception('chat_id, sender_type, and sender_id are required');
    }

    $stmt = $pdo->prepare("SELECT COUNT(*) FROM tbl_chat_messages WHERE chat_id = ? AND is_read = 0 AND NOT (sender_type = ? AND sender_id = ?)");
    $stmt->execute([$chat_id, $sender_type, $sender_id]);
    $response['unread_count'] = (int)$stmt->fetchColumn();
    $response['success'] = true;
} catch (Exception $e) {
    $response['error'] = $e->getMessage();
}

echo json_encode($response); 