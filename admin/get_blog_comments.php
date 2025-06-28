<?php
require_once '../includes/config.php';
header('Content-Type: application/json');

$response = [
    'success' => false,
    'comments' => [],
    'message' => ''
];

if (isset($_GET['blog_id'])) {
    $blog_id = $_GET['blog_id'];
    
    try {
        // Get comments with their replies
        $sql = "SELECT 
                c.*,
                DATE_FORMAT(c.created_at, '%M %d, %Y %h:%i %p') as formatted_date,
                CASE 
                    WHEN c.parent_id IS NOT NULL THEN 'Reply'
                    ELSE 'Comment'
                END as comment_type
                FROM tbl_blog_comments c 
                WHERE c.blog_id = ? 
                ORDER BY c.created_at DESC";
        
        $stmt = $conn->prepare($sql);
        $stmt->execute([$blog_id]);
        $comments = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        if ($comments) {
            $response['success'] = true;
            $response['comments'] = array_map(function($comment) {
                return [
                    'id' => $comment['id'],
                    'comment' => htmlspecialchars($comment['comment']),
                    'type' => $comment['comment_type'],
                    'created_at' => $comment['formatted_date'],
                    'parent_id' => $comment['parent_id']
                ];
            }, $comments);
        } else {
            $response['success'] = true;
            $response['message'] = 'No comments found';
        }
        
    } catch (PDOException $e) {
        $response['message'] = 'Error: ' . $e->getMessage();
    }
} else {
    $response['message'] = 'Blog ID not provided';
}

echo json_encode($response);
?> 