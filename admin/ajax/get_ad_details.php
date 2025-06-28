<?php
/**
 * Get Ad Details for Edit Modal
 * Returns ad information in JSON format for AJAX requests
 */

session_start();

// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
    exit();
}

// Include database connection
require_once('../../config/database.php');

// Check if ad ID is provided
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Ad ID is required']);
    exit();
}

$ad_id = (int) $_GET['id'];

try {
    // Fetch ad details
    $stmt = $conn->prepare("SELECT * FROM tbl_ads WHERE id = ?");
    $stmt->execute([$ad_id]);
    $ad = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$ad) {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'Ad not found']);
        exit();
    }
    
    // Return ad details
    header('Content-Type: application/json');
    echo json_encode([
        'success' => true,
        'ad' => $ad
    ]);
    
} catch (PDOException $e) {
    header('Content-Type: application/json');
    echo json_encode([
        'success' => false, 
        'message' => 'Database error: ' . $e->getMessage()
    ]);
}
?> 