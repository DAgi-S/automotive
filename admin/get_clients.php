<?php
session_start();
require_once('../config/database.php');

// Check if user is logged in
if (!isset($_SESSION['admin_id']) && !isset($_SESSION['user_id'])) {
    echo 'Unauthorized access';
    exit;
}

try {
    // Fetch clients and their latest car info
    $query = "SELECT u.id, u.name, u.phonenum, u.car_brand as user_car_brand,
                     i.car_brand, i.car_model, i.plate_number
              FROM tbl_user u 
              LEFT JOIN (
                  SELECT * FROM tbl_info WHERE id IN (
                      SELECT MAX(id) FROM tbl_info GROUP BY userid
                  )
              ) i ON u.id = i.userid
              WHERE u.role = 'user' 
              ORDER BY u.name";
    
    $stmt = $conn->query($query);
    
    echo '<option value="">Select Client</option>';
    
    while ($client = $stmt->fetch()) {
        // Use car info from tbl_info if available, otherwise from tbl_user
        $carBrand = !empty($client['car_brand']) ? $client['car_brand'] : $client['user_car_brand'];
        
        echo '<option value="' . $client['id'] . '" ' .
             'data-car-brand="' . htmlspecialchars($carBrand) . '" ' .
             'data-car-model="' . htmlspecialchars($client['car_model']) . '" ' .
             'data-plate-number="' . htmlspecialchars($client['plate_number']) . '" ' .
             'data-phone="' . htmlspecialchars($client['phonenum']) . '">' .
             htmlspecialchars($client['name']) . ' (' . htmlspecialchars($client['phonenum']) . ')' .
             '</option>';
    }
} catch(PDOException $e) {
    echo '<option value="">Error loading clients: ' . $e->getMessage() . '</option>';
}
?> 