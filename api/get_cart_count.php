<?php
session_start();

// Start output buffering to prevent any unwanted output
ob_start();

// Set JSON header
header('Content-Type: application/json');

// Initialize cart count
$count = 0;

try {
    // Check if user is logged in and has cart items
    if (isset($_SESSION['user_id']) || isset($_SESSION['id'])) {
        // Try database-based cart first (for logged-in users)
        try {
            require_once __DIR__ . '/../includes/db.php';
            
            // Use the correct user ID variable
            $user_id = $_SESSION['user_id'] ?? $_SESSION['id'];
            
            // Get cart count from database if cart table exists
            $sql = "SELECT COUNT(*) as count FROM tbl_cart WHERE user_id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->execute([$user_id]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            $count = $result['count'] ?? 0;
        } catch (Exception $e) {
            // Fallback to session-based cart
            if (isset($_SESSION['cart']) && is_array($_SESSION['cart'])) {
                $count = array_reduce($_SESSION['cart'], function($carry, $item) {
                    return $carry + ($item['quantity'] ?? 1);
                }, 0);
            }
        }
    } else {
        // For guest users, use session-based cart
        if (isset($_SESSION['cart']) && is_array($_SESSION['cart'])) {
            $count = array_reduce($_SESSION['cart'], function($carry, $item) {
                return $carry + ($item['quantity'] ?? 1);
            }, 0);
        }
    }
    
    // Clear any unwanted output
    ob_clean();
    
    // Return successful response
    echo json_encode([
        'success' => true,
        'count' => $count
    ]);
    
} catch (Exception $e) {
    // Clear any unwanted output
    ob_clean();
    
    // Return error response
    echo json_encode([
        'success' => false,
        'count' => 0,
        'error' => 'Error retrieving cart count'
    ]);
}

// End output buffering
ob_end_flush();
?> 