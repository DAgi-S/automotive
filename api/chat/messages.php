<?php
// /api/chat/messages.php
// Gets all messages for a chat. Accepts chat_id, returns ordered messages.

header('Content-Type: application/json');
require_once __DIR__ . '/../../config/database.php';

$response = ["success" => false, "error" => null, "messages" => []];

try {
    $pdo = $conn;
    $chat_id = isset($_GET['chat_id']) ? intval($_GET['chat_id']) : null;
    if (!$chat_id) {
        throw new Exception('chat_id is required');
    }
    $stmt = $pdo->prepare("SELECT message_id, sender_type, sender_id, message, is_read, created_at FROM tbl_chat_messages WHERE chat_id = ? ORDER BY created_at ASC");
    $stmt->execute([$chat_id]);
    $response['messages'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $response['success'] = true;
} catch (Exception $e) {
    $response['error'] = $e->getMessage();
}

echo json_encode($response); 