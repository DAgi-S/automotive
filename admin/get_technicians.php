<?php
session_start();
require_once('../config/database.php');

// Check if user is logged in
if (!isset($_SESSION['admin_id']) && !isset($_SESSION['user_id'])) {
    echo 'Unauthorized access';
    exit;
}

try {
    // Fetch technicians from tbl_worker where position is 'technician'
    $query = "SELECT id, full_name FROM tbl_worker ";
    $stmt = $conn->query($query);
    
    // Output technicians as options
    while ($technician = $stmt->fetch()) {
        echo '<option value="' . $technician['id'] . '">' . htmlspecialchars($technician['full_name']) . '</option>';
    }
} catch(PDOException $e) {
    echo '<option value="">Error loading technicians: ' . $e->getMessage() . '</option>';
}
?> 