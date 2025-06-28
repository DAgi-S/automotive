<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../config/database.php';

try {
    // Fetch workers/team members from database
    $stmt = $conn->prepare("SELECT id, full_name, position, image_url FROM tbl_worker ORDER BY id ASC");
    $stmt->execute();
    $workers = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Format the data for frontend
    $team_members = [];
    foreach ($workers as $worker) {
        $team_members[] = [
            'id' => $worker['id'],
            'name' => $worker['full_name'],
            'position' => ucfirst($worker['position']),
            'image' => $worker['image_url'] ? 'admin/uploads/workers/' . $worker['image_url'] : 'assets/images/single-courses/client1.png',
            'experience' => '5+ years', // Default since not in database
            'specialization' => 'Automotive Service' // Default since not in database
        ];
    }
    
    echo json_encode([
        'success' => true,
        'data' => $team_members
    ]);
    
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Database error: ' . $e->getMessage()
    ]);
} 