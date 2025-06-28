<?php
/**
 * Hero Image Upload Handler
 * Handles AJAX file uploads for hero carousel background images
 */

session_start();

// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
    exit();
}

// Check if file was uploaded
if (!isset($_FILES['hero_image']) || $_FILES['hero_image']['error'] !== UPLOAD_ERR_OK) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'No file uploaded or upload error']);
    exit();
}

$file = $_FILES['hero_image'];

// Validate file type
$allowed_types = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
$file_type = $file['type'];

if (!in_array($file_type, $allowed_types)) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Invalid file type. Only JPEG, PNG, GIF, and WebP images are allowed.']);
    exit();
}

// Validate file size (max 5MB)
$max_size = 5 * 1024 * 1024; // 5MB in bytes
if ($file['size'] > $max_size) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'File size too large. Maximum size is 5MB.']);
    exit();
}

// Create upload directory if it doesn't exist
$upload_dir = '../../uploads/hero/';
if (!file_exists($upload_dir)) {
    if (!mkdir($upload_dir, 0755, true)) {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'Failed to create upload directory']);
        exit();
    }
}

// Generate unique filename
$file_extension = pathinfo($file['name'], PATHINFO_EXTENSION);
$filename = 'hero_' . uniqid() . '.' . $file_extension;
$file_path = $upload_dir . $filename;

// Move uploaded file
if (move_uploaded_file($file['tmp_name'], $file_path)) {
    // Return relative path for database storage
    $relative_path = 'uploads/hero/' . $filename;
    
    header('Content-Type: application/json');
    echo json_encode([
        'success' => true, 
        'message' => 'Image uploaded successfully',
        'file_path' => $relative_path,
        'filename' => $filename
    ]);
} else {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Failed to save uploaded file']);
}
?> 