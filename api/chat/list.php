<?php
// /api/chat/list.php
// Lists chats for user, admin, or worker. Accepts user_id, admin_id, or worker_id as GET params.

header('Content-Type: application/json');
require_once __DIR__ . '/../../config/database.php';

$response = ["success" => false, "error" => null, "chats" => []];

try {
    $pdo = $conn;
    $user_id = isset($_GET['user_id']) ? intval($_GET['user_id']) : null;
    $admin_id = isset($_GET['admin_id']) ? intval($_GET['admin_id']) : null;
    $worker_id = isset($_GET['worker_id']) ? intval($_GET['worker_id']) : null;

    if (!$user_id && !$admin_id && !$worker_id) {
        throw new Exception('user_id, admin_id, or worker_id is required');
    }

    if ($user_id) {
        $stmt = $pdo->prepare("SELECT * FROM tbl_chats WHERE user_id = ? ORDER BY updated_at DESC");
        $stmt->execute([$user_id]);
    } elseif ($admin_id) {
        $stmt = $pdo->prepare("SELECT * FROM tbl_chats WHERE admin_id = ? OR assigned_by = ? ORDER BY updated_at DESC");
        $stmt->execute([$admin_id, $admin_id]);
    } elseif ($worker_id) {
        $stmt = $pdo->prepare("SELECT * FROM tbl_chats WHERE worker_id = ? ORDER BY updated_at DESC");
        $stmt->execute([$worker_id]);
    }
    $response['chats'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $response['success'] = true;
} catch (Exception $e) {
    $response['error'] = $e->getMessage();
}

echo json_encode($response); 