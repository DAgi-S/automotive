<?php
session_start();
header('Content-Type: application/json');

require_once('../../includes/config.php');

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Please login to update cart']);
    exit();
}

// Get JSON input
$input = json_decode(file_get_contents('php://input'), true);

if (!isset($input['product_id']) || !isset($input['action'])) {
    echo json_encode(['success' => false, 'message' => 'Missing required parameters']);
    exit();
}

try {
    $conn = getDBConnection();
    $user_id = $_SESSION['user_id'];
    $product_id = intval($input['product_id']);
    $action = $input['action'];

    // Get current cart item
    $check_query = "SELECT id, quantity FROM tbl_cart WHERE user_id = ? AND product_id = ?";
    $stmt = $conn->prepare($check_query);
    $stmt->execute([$user_id, $product_id]);
    $cartItem = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$cartItem) {
        echo json_encode(['success' => false, 'message' => 'Item not found in cart']);
        exit();
    }

    $newQuantity = $cartItem['quantity'];

    switch ($action) {
        case 'increase':
            $newQuantity = min($cartItem['quantity'] + 1, 99);
            break;
        case 'decrease':
            $newQuantity = max($cartItem['quantity'] - 1, 1);
            break;
        case 'set':
            if (isset($input['value'])) {
                $newQuantity = max(1, min(intval($input['value']), 99));
            }
            break;
        case 'remove':
            // Delete the item from cart
            $delete_query = "DELETE FROM tbl_cart WHERE user_id = ? AND product_id = ?";
            $stmt = $conn->prepare($delete_query);
            $stmt->execute([$user_id, $product_id]);
            
            // Get updated cart count
            $count_query = "SELECT SUM(quantity) as total FROM tbl_cart WHERE user_id = ?";
            $stmt = $conn->prepare($count_query);
            $stmt->execute([$user_id]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            $count = $result['total'] ?? 0;
            
            echo json_encode([
                'success' => true,
                'message' => 'Item removed from cart',
                'count' => $count
            ]);
            exit();
    }

    // Update quantity
    $update_query = "UPDATE tbl_cart SET quantity = ? WHERE user_id = ? AND product_id = ?";
    $stmt = $conn->prepare($update_query);
    $stmt->execute([$newQuantity, $user_id, $product_id]);

    // Get updated cart count
    $count_query = "SELECT SUM(quantity) as total FROM tbl_cart WHERE user_id = ?";
    $stmt = $conn->prepare($count_query);
    $stmt->execute([$user_id]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    $count = $result['total'] ?? 0;

    echo json_encode([
        'success' => true,
        'message' => 'Cart updated successfully',
        'count' => $count
    ]);

} catch (Exception $e) {
    error_log("Update cart error: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Error updating cart']);
}
?> 