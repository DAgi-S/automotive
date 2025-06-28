<?php
header('Content-Type: application/json');

try {
    // Database connection
    $conn = new mysqli("localhost", "root", "", "automotive2");

    // Check connection
    if ($conn->connect_error) {
        throw new Exception("Connection failed: " . $conn->connect_error);
    }

    // Get brand ID from request
    $brand_id = isset($_GET['brand_id']) ? (int)$_GET['brand_id'] : 0;

    if ($brand_id > 0) {
        // Prepare and execute query
        $stmt = $conn->prepare("SELECT id, model_name, year_from, year_to FROM car_models WHERE brand_id = ? ORDER BY model_name");
        if (!$stmt) {
            throw new Exception("Prepare failed: " . $conn->error);
        }

        $stmt->bind_param("i", $brand_id);
        if (!$stmt->execute()) {
            throw new Exception("Execute failed: " . $stmt->error);
        }

        $result = $stmt->get_result();
        
        // Fetch all models
        $models = [];
        while ($row = $result->fetch_assoc()) {
            $models[] = [
                'id' => (int)$row['id'],
                'model_name' => $row['model_name'],
                'year_from' => (int)$row['year_from'],
                'year_to' => (int)$row['year_to']
            ];
        }
        
        echo json_encode($models);
        $stmt->close();
    } else {
        echo json_encode([]);
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
} finally {
    if (isset($conn)) {
        $conn->close();
    }
} 