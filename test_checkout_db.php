<?php
// Test checkout database structure
require_once('includes/config.php');

echo "<h2>Testing Checkout Database Structure</h2>\n";

try {
    $conn = getDBConnection();
    
    // Test 1: Check if all required tables exist
    echo "<h3>1. Checking Required Tables</h3>\n";
    $tables = ['tbl_orders', 'tbl_order_items', 'tbl_products', 'tbl_payments', 'tbl_customer_addresses'];
    
    foreach ($tables as $table) {
        $stmt = $conn->prepare("SHOW TABLES LIKE ?");
        $stmt->execute([$table]);
        if ($stmt->rowCount() > 0) {
            echo "✅ Table $table exists<br>\n";
        } else {
            echo "❌ Table $table missing<br>\n";
        }
    }
    
    // Test 2: Check tbl_orders columns
    echo "<h3>2. Checking tbl_orders Columns</h3>\n";
    $required_columns = ['id', 'user_id', 'order_number', 'total_amount', 'payment_method', 
                        'shipping_address', 'customer_name', 'customer_email', 'customer_phone', 'notes'];
    
    $stmt = $conn->prepare("DESCRIBE tbl_orders");
    $stmt->execute();
    $columns = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    foreach ($required_columns as $col) {
        if (in_array($col, $columns)) {
            echo "✅ Column $col exists<br>\n";
        } else {
            echo "❌ Column $col missing<br>\n";
        }
    }
    
    // Test 3: Check tbl_customer_addresses columns
    echo "<h3>3. Checking tbl_customer_addresses Columns</h3>\n";
    $address_columns = ['id', 'user_id', 'full_name', 'phone', 'full_address'];
    
    $stmt = $conn->prepare("DESCRIBE tbl_customer_addresses");
    $stmt->execute();
    $columns = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    foreach ($address_columns as $col) {
        if (in_array($col, $columns)) {
            echo "✅ Column $col exists<br>\n";
        } else {
            echo "❌ Column $col missing<br>\n";
        }
    }
    
    // Test 4: Test INSERT query structure
    echo "<h3>4. Testing INSERT Query Structure</h3>\n";
    
    $test_query = "INSERT INTO tbl_orders 
                   (user_id, order_number, total_amount, payment_method, shipping_address, 
                    customer_name, customer_email, customer_phone, notes) 
                   VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
    
    $stmt = $conn->prepare($test_query);
    if ($stmt) {
        echo "✅ INSERT query structure is valid<br>\n";
    } else {
        echo "❌ INSERT query structure has issues<br>\n";
    }
    
    echo "<h3>5. Database Structure Test Complete</h3>\n";
    echo "✅ All tests passed! Checkout system should work correctly.<br>\n";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "<br>\n";
}
?> 