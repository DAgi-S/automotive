<?php
session_start();
header('Content-Type: application/json');

require_once('../../includes/config.php');

$count = 0;

if (isset($_SESSION['user_id'])) {
    try {
        $conn = getDBConnection();
        $user_id = $_SESSION['user_id'];
        
        $count_query = "SELECT SUM(quantity) as total FROM tbl_cart WHERE user_id = ?";
        $stmt = $conn->prepare($count_query);
        $stmt->execute([$user_id]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $count = $result['total'] ?? 0;
        
    } catch (Exception $e) {
        error_log("Get cart count error: " . $e->getMessage());
    }
}

echo json_encode(['count' => $count]); 