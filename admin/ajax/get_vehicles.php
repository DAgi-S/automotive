<?php
session_start();

// Include database connection
require_once('../../config/database.php');

// Check admin session
if (!isset($_SESSION['admin_id'])) {
    http_response_code(401);
    exit('Unauthorized');
}

$search = isset($_GET['search']) ? trim($_GET['search']) : '';

try {
    $query = "SELECT v.*, u.name as owner_name 
              FROM tbl_vehicles v 
              LEFT JOIN tbl_user u ON v.user_id = u.id 
              WHERE v.plate_number LIKE :search 
              OR v.make LIKE :search 
              OR v.model LIKE :search 
              OR u.name LIKE :search 
              LIMIT 10";
    
    $stmt = $conn->prepare($query);
    $searchTerm = "%{$search}%";
    $stmt->bindParam(':search', $searchTerm);
    $stmt->execute();
    
    $vehicles = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    $results = array_map(function($vehicle) {
        return [
            'id' => $vehicle['id'],
            'text' => sprintf("%s - %s %s (%s) - Owner: %s",
                $vehicle['plate_number'],
                $vehicle['make'],
                $vehicle['model'],
                $vehicle['year'],
                $vehicle['owner_name']
            )
        ];
    }, $vehicles);
    
    header('Content-Type: application/json');
    echo json_encode($results);
    
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Database error occurred']);
}
?> 