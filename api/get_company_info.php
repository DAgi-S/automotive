<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../config/database.php';

try {
    // Fetch company information from database
    $stmt = $conn->prepare("SELECT address, phone, email, website, about FROM company_information ORDER BY id DESC LIMIT 1");
    $stmt->execute();
    $company_info = $stmt->fetch(PDO::FETCH_ASSOC);
    
    // Default values if no company info found
    if (!$company_info) {
        $company_info = [
            'address' => '123 Automotive Street, Addis Ababa, Ethiopia',
            'phone' => '+251 911-123456',
            'email' => 'info@natiautomotive.com',
            'website' => 'www.natiautomotive.com',
            'about' => 'Your trusted partner in automotive excellence since 2010.'
        ];
    }
    
    echo json_encode([
        'success' => true,
        'data' => $company_info
    ]);
    
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Database error: ' . $e->getMessage()
    ]);
} 