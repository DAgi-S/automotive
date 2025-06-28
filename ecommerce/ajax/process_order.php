<?php
session_start();
header('Content-Type: application/json');

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'User not logged in']);
    exit();
}

require_once('../../includes/config.php');

try {
    $conn = getDBConnection();
    $user_id = $_SESSION['user_id'];
    
    // Get user information
    $user_query = "SELECT * FROM tbl_user WHERE id = ?";
    $stmt = $conn->prepare($user_query);
    $stmt->execute([$user_id]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$user) {
        echo json_encode(['success' => false, 'message' => 'User not found']);
        exit();
    }
    
    // Get cart items
    $cart_query = "SELECT c.*, p.name, p.image_url as product_image, p.price 
                   FROM tbl_cart c 
                   LEFT JOIN tbl_products p ON c.product_id = p.id 
                   WHERE c.user_id = ?";
    $stmt = $conn->prepare($cart_query);
    $stmt->execute([$user_id]);
    $cartItems = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (empty($cartItems)) {
        echo json_encode(['success' => false, 'message' => 'Cart is empty']);
        exit();
    }
    
    // Calculate total
    $total = 0;
    foreach ($cartItems as $item) {
        $total += $item['price'] * $item['quantity'];
    }
    
    // Generate unique order number
    $order_number = 'ORD-' . date('Ymd') . '-' . str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT);
    
    // Check if order number exists (very unlikely but good practice)
    $check_order = "SELECT id FROM tbl_orders WHERE order_number = ?";
    $stmt = $conn->prepare($check_order);
    $stmt->execute([$order_number]);
    if ($stmt->rowCount() > 0) {
        $order_number = 'ORD-' . date('Ymd') . '-' . str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT);
    }
    
    // Get form data
    $payment_method = $_POST['payment_method'] ?? 'cash_on_delivery';
    $order_notes = $_POST['order_notes'] ?? '';
    $shipping_address_id = $_POST['shipping_address_id'] ?? null;
    
    // Handle shipping address
    $shipping_address = '';
    if ($shipping_address_id) {
        // Get existing address
        $addr_query = "SELECT * FROM tbl_customer_addresses WHERE id = ? AND user_id = ?";
        $stmt = $conn->prepare($addr_query);
        $stmt->execute([$shipping_address_id, $user_id]);
        $address = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($address) {
            $shipping_address = $address['full_name'] . "\n";
            $shipping_address .= $address['full_address'] . "\n";
            if ($address['phone']) $shipping_address .= "Phone: " . $address['phone'];
        }
    } else {
        // Create new address if provided
        if (isset($_POST['full_name']) && isset($_POST['phone']) && isset($_POST['full_address'])) {
            $full_name = $_POST['full_name'];
            $phone = $_POST['phone'];
            $full_address = $_POST['full_address'];
            $save_address = isset($_POST['save_address']);
            
            $shipping_address = $full_name . "\n";
            $shipping_address .= $full_address . "\n";
            $shipping_address .= "Phone: " . $phone;
            
            // Save address if requested
            if ($save_address) {
                $save_addr_query = "INSERT INTO tbl_customer_addresses 
                                   (user_id, full_name, phone, full_address) 
                                   VALUES (?, ?, ?, ?)";
                $stmt = $conn->prepare($save_addr_query);
                $stmt->execute([$user_id, $full_name, $phone, $full_address]);
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'Full name, phone, and address are required']);
            exit();
        }
    }
    
    // Start transaction
    $conn->beginTransaction();
    
    try {
        // Insert order
        $order_query = "INSERT INTO tbl_orders 
                       (user_id, order_number, total_amount, payment_method, shipping_address, 
                        customer_name, customer_email, customer_phone, notes) 
                       VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($order_query);
        $stmt->execute([$user_id, $order_number, $total, $payment_method, 
                       $shipping_address, $user['name'], $user['email'], $user['phonenum'], $order_notes]);
        $order_id = $conn->lastInsertId();
        
        // Insert order items
        $item_query = "INSERT INTO tbl_order_items 
                      (order_id, product_id, product_name, product_image, quantity, unit_price, total_price) 
                      VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($item_query);
        
        foreach ($cartItems as $item) {
            $item_total = $item['price'] * $item['quantity'];
            $stmt->execute([$order_id, $item['product_id'], $item['name'], 
                           $item['product_image'], $item['quantity'], $item['price'], $item_total]);
        }
        
        // Create payment record
        $payment_query = "INSERT INTO tbl_payments (order_id, payment_method, amount, status) 
                         VALUES (?, ?, ?, 'pending')";
        $stmt = $conn->prepare($payment_query);
        $stmt->execute([$order_id, $payment_method, $total]);
        
        // Clear cart
        $clear_cart_query = "DELETE FROM tbl_cart WHERE user_id = ?";
        $stmt = $conn->prepare($clear_cart_query);
        $stmt->execute([$user_id]);
        
        // Commit transaction
        $conn->commit();
        
        // Send success response
        echo json_encode([
            'success' => true, 
            'message' => 'Order placed successfully',
            'order_number' => $order_number,
            'order_id' => $order_id
        ]);
        
    } catch (Exception $e) {
        // Rollback transaction
        $conn->rollback();
        throw $e;
    }
    
} catch (Exception $e) {
    error_log("Order processing error: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Error processing order: ' . $e->getMessage()]);
}
?> 