<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../config/database.php';

try {
    // Get limit from query parameter (default 3 for homepage)
    $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 3;
    $status = isset($_GET['status']) ? $_GET['status'] : null;
    
    // Build query
    $sql = "SELECT id, title, writer, date, s_article, content, image_url, status FROM tbl_blog";
    $params = [];
    
    if ($status) {
        $sql .= " WHERE status = ?";
        $params[] = $status;
    }
    
    $sql .= " ORDER BY date DESC LIMIT ?";
    $params[] = $limit;
    
    $stmt = $conn->prepare($sql);
    $stmt->execute($params);
    $blogs = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Format the data for frontend
    $formatted_blogs = [];
    foreach ($blogs as $blog) {
        $formatted_blogs[] = [
            'id' => $blog['id'],
            'title' => $blog['title'],
            'writer' => $blog['writer'],
            'date' => date('M d, Y', strtotime($blog['date'])),
            'excerpt' => $blog['s_article'],
            'content' => $blog['content'],
            'image' => $blog['image_url'] ? 'uploads/blogs/' . $blog['image_url'] : 'assets/images/homescreen/auto1.jpg',
            'status' => $blog['status'],
            'url' => 'blog.php?id=' . $blog['id']
        ];
    }
    
    echo json_encode([
        'success' => true,
        'data' => $formatted_blogs
    ]);
    
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Database error: ' . $e->getMessage()
    ]);
} 