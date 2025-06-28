<?php
// /api/chat/send_message.php
// Sends a message in a chat. Accepts chat_id, sender_type, sender_id, message. Triggers notification for recipient.

header('Content-Type: application/json');
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/NotificationManager.php';

$response = ["success" => false, "error" => null];

try {
    $pdo = $conn;
    $input = json_decode(file_get_contents('php://input'), true);

    $chat_id = isset($input['chat_id']) ? intval($input['chat_id']) : null;
    $sender_type = isset($input['sender_type']) ? $input['sender_type'] : null;
    $sender_id = isset($input['sender_id']) ? intval($input['sender_id']) : null;
    $message = isset($input['message']) ? trim($input['message']) : '';

    if (!$chat_id || !$sender_type || !$sender_id || $message === '') {
        throw new Exception('chat_id, sender_type, sender_id, and message are required');
    }

    // Insert message
    $stmt = $pdo->prepare("INSERT INTO tbl_chat_messages (chat_id, sender_type, sender_id, message, is_read, created_at) VALUES (?, ?, ?, ?, 0, NOW())");
    $stmt->execute([$chat_id, $sender_type, $sender_id, $message]);

    // Fetch chat to determine recipient
    $stmt = $pdo->prepare("SELECT * FROM tbl_chats WHERE chat_id = ?");
    $stmt->execute([$chat_id]);
    $chat = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$chat) throw new Exception('Chat not found');

    // Determine recipient (opposite of sender)
    $recipient_type = null;
    $recipient_id = null;
    if ($sender_type === 'user' && $chat['worker_id']) {
        $recipient_type = 'worker';
        $recipient_id = $chat['worker_id'];
    } elseif ($sender_type === 'user' && $chat['admin_id']) {
        $recipient_type = 'admin';
        $recipient_id = $chat['admin_id'];
    } elseif ($sender_type === 'worker' && $chat['user_id']) {
        $recipient_type = 'user';
        $recipient_id = $chat['user_id'];
    } elseif ($sender_type === 'admin' && $chat['user_id']) {
        $recipient_type = 'user';
        $recipient_id = $chat['user_id'];
    } elseif ($sender_type === 'worker' && $chat['admin_id']) {
        $recipient_type = 'admin';
        $recipient_id = $chat['admin_id'];
    } elseif ($sender_type === 'admin' && $chat['worker_id']) {
        $recipient_type = 'worker';
        $recipient_id = $chat['worker_id'];
    } elseif ($sender_type === 'guest' && $chat['admin_id']) {
        $recipient_type = 'admin';
        $recipient_id = $chat['admin_id'];
    }

    // Send notification if recipient found
    if ($recipient_id && $recipient_type) {
        $notificationManager = new NotificationManager($pdo);
        $notification_data = [
            'message' => 'You have a new chat message',
            'reference_id' => $chat_id
        ];
        $notificationManager->sendNotification('chat_message', $recipient_id, $notification_data, ['web', 'email']);
    }

    $response['success'] = true;
} catch (Exception $e) {
    $response['error'] = $e->getMessage();
}

echo json_encode($response); 