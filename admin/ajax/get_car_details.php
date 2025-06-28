<?php
require_once '../../includes/config.php';

// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized']);
    exit();
}

// Get car ID from request
$car_id = isset($_GET['car_id']) ? (int)$_GET['car_id'] : 0;

if ($car_id <= 0) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid car ID']);
    exit();
}

try {
    // Fetch car details with owner information
    $query = "SELECT ti.*, u.name as owner_name, u.email as owner_email, u.phonenum as owner_phone,
              cb.brand_name, cm.model_name,
              ti.oil_change_date, ti.insurance_date, ti.bolo_date, ti.rd_wegen_date, ti.yemenged_fend_date
              FROM tbl_info ti 
              LEFT JOIN tbl_user u ON ti.userid = u.id
              LEFT JOIN car_brands cb ON ti.car_brand = cb.id
              LEFT JOIN car_models cm ON ti.car_model = cm.id
              WHERE ti.id = ?";
    
    $stmt = $conn->prepare($query);
    $stmt->execute([$car_id]);
    $car = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$car) {
        http_response_code(404);
        echo json_encode(['error' => 'Car not found']);
        exit();
    }

    // Format the data
    $response = [
        'owner_name' => $car['owner_name'],
        'owner_email' => $car['owner_email'],
        'owner_phone' => $car['owner_phone'],
        'brand_name' => $car['brand_name'],
        'model_name' => $car['model_name'],
        'mile_age' => $car['mile_age'],
        'plate_number' => $car['plate_number'],
        'trailer_number' => $car['trailer_number'],
        'services' => [
            'oil_change' => [
                'value' => $car['oil_change'] ?? 'no',
                'date' => $car['oil_change_date']
            ],
            'insurance' => [
                'value' => $car['insurance'] ?? 'no',
                'date' => $car['insurance_date']
            ],
            'bolo' => [
                'value' => $car['bolo'] ?? 'no',
                'date' => $car['bolo_date']
            ],
            'rd_wegen' => [
                'value' => $car['rd_wegen'] ?? 'no',
                'date' => $car['rd_wegen_date']
            ],
            'yemenged_fend' => [
                'value' => $car['yemenged_fend'] ?? 'no',
                'date' => $car['yemenged_fend_date']
            ]
        ],
        'created_at' => $car['created_at']
    ];

    // Set proper content type
    header('Content-Type: application/json');
    echo json_encode($response);

} catch (PDOException $e) {
    error_log("Database error in get_car_details.php: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['error' => 'Internal Server Error']);
} 