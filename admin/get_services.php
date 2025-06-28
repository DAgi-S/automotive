<?php
session_start();
require_once('../config/database.php');

// Check if user is logged in
if (!isset($_SESSION['admin_id']) && !isset($_SESSION['user_id'])) {
    echo 'Unauthorized access';
    exit;
}

try {
    // Get selected services if any
    $selectedServices = isset($_GET['selected_services']) ? $_GET['selected_services'] : [];
    if (!is_array($selectedServices)) {
        $selectedServices = [];
    }

    // Fetch active services
    $query = "SELECT service_id, service_name, price FROM tbl_services WHERE status = 'active' ORDER BY service_name";
    $stmt = $conn->query($query);
    
    // Display services as checkboxes
    while ($service = $stmt->fetch()) {
        $checked = in_array($service['service_id'], $selectedServices) ? 'checked' : '';
        echo '<div class="custom-control custom-checkbox">';
        echo '<input type="checkbox" class="custom-control-input" id="service_' . $service['service_id'] . '" name="services[]" value="' . $service['service_id'] . '" ' . $checked . '>';
        echo '<label class="custom-control-label" for="service_' . $service['service_id'] . '">' . htmlspecialchars($service['service_name']) . ' (ETB ' . number_format($service['price'], 2) . ')</label>';
        echo '</div>';
    }
} catch(PDOException $e) {
    echo '<div class="alert alert-danger">Error loading services: ' . $e->getMessage() . '</div>';
}
?> 