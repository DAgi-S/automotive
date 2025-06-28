<?php
require_once '../includes/config.php';

if (isset($_GET['id'])) {
    $blog_id = $_GET['id'];
    
    try {
        $sql = "SELECT * FROM tbl_blog WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$blog_id]);
        $blog = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($blog) {
            // Only sanitize non-HTML fields
            $blog['title'] = htmlspecialchars($blog['title']);
            $blog['writer'] = htmlspecialchars($blog['writer']);
            $blog['s_article'] = htmlspecialchars($blog['s_article']);
            // Don't sanitize content as it contains HTML from TinyMCE
            
            // Ensure all fields are present
            $fields = ['id', 'title', 'writer', 'date', 's_article', 'content', 'image_url', 'status'];
            foreach ($fields as $field) {
                if (!isset($blog[$field])) {
                    $blog[$field] = '';
                }
            }
            
            header('Content-Type: application/json');
            echo json_encode($blog);
        } else {
            http_response_code(404);
            echo json_encode(['error' => 'Blog not found']);
        }
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
    }
} else {
    http_response_code(400);
    echo json_encode(['error' => 'Blog ID not provided']);
}
?> 