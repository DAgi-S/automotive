<?php
session_start();
header('Content-Type: application/json');

// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
    exit();
}

require_once '../../includes/config.php';

// Validate required fields
$required_fields = ['title', 'position', 'start_date', 'end_date', 'status'];
foreach ($required_fields as $field) {
    if (!isset($_POST[$field]) || empty($_POST[$field])) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => ucfirst($field) . ' is required']);
        exit();
    }
}

// Validate dates
$start_date = $_POST['start_date'];
$end_date = $_POST['end_date'];

if (strtotime($end_date) < strtotime($start_date)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'End date must be after start date']);
    exit();
}

try {
    $conn->beginTransaction();

    // Prepare base data
    $data = [
        'title' => $_POST['title'],
        'description' => $_POST['description'] ?? null,
        'position' => $_POST['position'],
        'start_date' => $start_date,
        'end_date' => $end_date,
        'target_url' => $_POST['target_url'] ?? null,
        'status' => $_POST['status']
    ];

    // Handle file upload if present
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
        $file_type = $_FILES['image']['type'];
        
        if (!in_array($file_type, $allowed_types)) {
            throw new Exception('Invalid file type. Only JPG, PNG and GIF are allowed.');
        }

        $max_size = 5 * 1024 * 1024; // 5MB
        if ($_FILES['image']['size'] > $max_size) {
            throw new Exception('File size too large. Maximum size is 5MB.');
        }

        // Create uploads directory if it doesn't exist
        $upload_dir = '../../uploads/ads/';
        if (!file_exists($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }

        // Generate unique filename
        $file_ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
        $new_filename = uniqid('ad_') . '.' . $file_ext;
        $upload_path = $upload_dir . $new_filename;

        if (!move_uploaded_file($_FILES['image']['tmp_name'], $upload_path)) {
            throw new Exception('Error uploading file.');
        }

        $data['image_name'] = $new_filename;
    }

    if (isset($_POST['id']) && !empty($_POST['id'])) {
        // Update existing ad
        $ad_id = (int)$_POST['id'];
        
        // Get current ad data for image handling
        $stmt = $conn->prepare("SELECT image_name FROM tbl_ads WHERE id = ?");
        $stmt->execute([$ad_id]);
        $current_ad = $stmt->fetch(PDO::FETCH_ASSOC);

        // Build update query
        $update_fields = [];
        $params = [];
        foreach ($data as $key => $value) {
            $update_fields[] = "$key = ?";
            $params[] = $value;
        }
        $params[] = $ad_id;

        $sql = "UPDATE tbl_ads SET " . implode(', ', $update_fields) . " WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->execute($params);

        // Delete old image if new one was uploaded
        if (isset($data['image_name']) && $current_ad && $current_ad['image_name']) {
            $old_image_path = $upload_dir . $current_ad['image_name'];
            if (file_exists($old_image_path)) {
                unlink($old_image_path);
            }
        }
    } else {
        // Insert new ad
        $columns = implode(', ', array_keys($data));
        $values = implode(', ', array_fill(0, count($data), '?'));
        
        $sql = "INSERT INTO tbl_ads ($columns) VALUES ($values)";
        $stmt = $conn->prepare($sql);
        $stmt->execute(array_values($data));
    }

    $conn->commit();
    echo json_encode(['success' => true]);

} catch (Exception $e) {
    $conn->rollBack();
    error_log("Error saving ad: " . $e->getMessage());
    
    // Delete uploaded file if it exists
    if (isset($upload_path) && file_exists($upload_path)) {
        unlink($upload_path);
    }
    
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
} 