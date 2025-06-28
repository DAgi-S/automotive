<?php
require_once '../includes/config.php';

header('Content-Type: application/json');

if (isset($_GET['id'])) {
    $blog_id = $_GET['id'];
    
    try {
        // Fetch blog data
        $sql = "SELECT * FROM tbl_blog WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$blog_id]);
        $blog = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($blog) {
            // Prepare response data
            $response = [
                'success' => true,
                'data' => [
                    'id' => $blog['id'],
                    'title' => $blog['title'],
                    'writer' => $blog['writer'],
                    's_article' => $blog['s_article'],
                    'content' => $blog['content'],
                    'image_url' => $blog['image_url'],
                    'status' => $blog['status']
                ]
            ];
            
            echo json_encode($response);
        } else {
            http_response_code(404);
            echo json_encode([
                'success' => false,
                'message' => 'Blog not found'
            ]);
        }
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode([
            'success' => false,
            'message' => 'Database error: ' . $e->getMessage()
        ]);
    }
} else {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => 'Blog ID not provided'
    ]);
}
?> 