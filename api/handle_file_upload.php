<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['id'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'error' => 'Unauthorized']);
    exit;
}

// Configuration
$upload_dir = '../uploads/vehicle_images/';
$allowed_types = ['image/jpeg', 'image/png', 'image/jpg'];
$max_file_size = 5 * 1024 * 1024; // 5MB

// Create upload directory if it doesn't exist
if (!file_exists($upload_dir)) {
    mkdir($upload_dir, 0777, true);
}

try {
    if (!isset($_FILES['vehicle_images'])) {
        throw new Exception('No files uploaded');
    }

    $files = $_FILES['vehicle_images'];
    $uploaded_files = [];

    // Handle multiple file uploads
    for ($i = 0; $i < count($files['name']); $i++) {
        $file = [
            'name' => $files['name'][$i],
            'type' => $files['type'][$i],
            'tmp_name' => $files['tmp_name'][$i],
            'error' => $files['error'][$i],
            'size' => $files['size'][$i]
        ];

        // Validate file
        if ($file['error'] !== UPLOAD_ERR_OK) {
            continue;
        }

        if (!in_array($file['type'], $allowed_types)) {
            continue;
        }

        if ($file['size'] > $max_file_size) {
            continue;
        }

        // Generate unique filename
        $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $filename = uniqid('vehicle_') . '.' . $extension;
        $filepath = $upload_dir . $filename;

        // Move file to upload directory
        if (move_uploaded_file($file['tmp_name'], $filepath)) {
            $uploaded_files[] = [
                'original_name' => $file['name'],
                'saved_name' => $filename,
                'path' => 'uploads/vehicle_images/' . $filename
            ];
        }
    }

    echo json_encode([
        'success' => true,
        'files' => $uploaded_files
    ]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}
?> 