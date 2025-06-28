<?php
session_start();
header('Content-Type: application/json');

require_once('../../includes/config.php');

// Check if user is logged in (required for database cart)
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Please login to add items to cart']);
    exit();
}

// Get JSON input
$input = json_decode(file_get_contents('php://input'), true);

if (!isset($input['product_id']) || !isset($input['name']) || !isset($input['price'])) {
    echo json_encode(['success' => false, 'message' => 'Missing required parameters']);
    exit();
}

try {
    $conn = getDBConnection();
    $user_id = $_SESSION['user_id'];
    $product_id = intval($input['product_id']);
    $quantity = isset($input['quantity']) ? intval($input['quantity']) : 1;

    // Validate quantity
    if ($quantity < 1) {
        $quantity = 1;
    } elseif ($quantity > 99) {
        $quantity = 99;
    }

    // Check if product already exists in cart
    $check_query = "SELECT id, quantity FROM tbl_cart WHERE user_id = ? AND product_id = ?";
    $stmt = $conn->prepare($check_query);
    $stmt->execute([$user_id, $product_id]);
    $existing = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($existing) {
        // Update quantity but don't exceed 99
        $newQuantity = min($existing['quantity'] + $quantity, 99);
        $update_query = "UPDATE tbl_cart SET quantity = ? WHERE id = ?";
        $stmt = $conn->prepare($update_query);
        $stmt->execute([$newQuantity, $existing['id']]);
    } else {
        // Insert new cart item
        $insert_query = "INSERT INTO tbl_cart (user_id, product_id, quantity) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($insert_query);
        $stmt->execute([$user_id, $product_id, $quantity]);
    }

    // Get total cart count
    $count_query = "SELECT SUM(quantity) as total FROM tbl_cart WHERE user_id = ?";
    $stmt = $conn->prepare($count_query);
    $stmt->execute([$user_id]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    $count = $result['total'] ?? 0;

    echo json_encode([
        'success' => true,
        'message' => 'Product added to cart',
        'count' => $count
    ]);

} catch (Exception $e) {
    error_log("Add to cart error: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Error adding product to cart']);
}
?> 