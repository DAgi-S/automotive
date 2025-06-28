<?php
session_start();
header('Content-Type: application/json');

// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
    exit();
}

require_once '../../includes/config.php';

// Validate ad ID
if (!isset($_POST['id']) || !is_numeric($_POST['id'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Invalid ad ID']);
    exit();
}

$adId = (int)$_POST['id'];

try {
    $conn->beginTransaction();

    // Get ad details first to get the image filename
    $stmt = $conn->prepare("SELECT image_name FROM tbl_ads WHERE id = ?");
    $stmt->execute([$adId]);
    $ad = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$ad) {
        throw new Exception('Ad not found');
    }

    // Delete the ad from database
    $stmt = $conn->prepare("DELETE FROM tbl_ads WHERE id = ?");
    $stmt->execute([$adId]);

    // Delete the image file if it exists
    if ($ad['image_name']) {
        $image_path = '../../uploads/ads/' . $ad['image_name'];
        if (file_exists($image_path)) {
            unlink($image_path);
        }
    }

    $conn->commit();
    echo json_encode(['success' => true]);

} catch (Exception $e) {
    $conn->rollBack();
    error_log("Error deleting ad: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'An error occurred while deleting the ad']);
    exit();
} 