<?php
session_start();
header('Content-Type: application/json');

include("../db_conn.php");

if (!isset($_SESSION['id'])) {
    echo json_encode(['error' => 'User not logged in']);
    exit;
}

$response = ['success' => false];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['blog_id']) && isset($_POST['comment'])) {
        $blog_id = intval($_POST['blog_id']);
        $user_id = intval($_SESSION['id']);
        $comment = trim($_POST['comment']);
        $parent_id = isset($_POST['parent_id']) ? intval($_POST['parent_id']) : null;

        if (!empty($comment)) {
            $sql = "INSERT INTO tbl_blog_comments (blog_id, user_id, parent_id, comment) VALUES (?, ?, ?, ?)";
            $stmt = mysqli_prepare($conn, $sql);
            mysqli_stmt_bind_param($stmt, "iiis", $blog_id, $user_id, $parent_id, $comment);
            
            if (mysqli_stmt_execute($stmt)) {
                $comment_id = mysqli_insert_id($conn);
                
                // Fetch the inserted comment with user details
                $fetch_sql = "SELECT c.*, u.name as username 
                             FROM tbl_blog_comments c 
                             JOIN tbl_user u ON c.user_id = u.id 
                             WHERE c.id = ?";
                $fetch_stmt = mysqli_prepare($conn, $fetch_sql);
                mysqli_stmt_bind_param($fetch_stmt, "i", $comment_id);
                mysqli_stmt_execute($fetch_stmt);
                $result = mysqli_stmt_get_result($fetch_stmt);
                $comment_data = mysqli_fetch_assoc($result);
                
                $response = [
                    'success' => true,
                    'comment' => [
                        'id' => $comment_data['id'],
                        'comment' => $comment_data['comment'],
                        'username' => $comment_data['username'],
                        'created_at' => $comment_data['created_at'],
                        'parent_id' => $comment_data['parent_id']
                    ]
                ];
                
                mysqli_stmt_close($fetch_stmt);
            }
            
            mysqli_stmt_close($stmt);
        }
    }
} else if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    if (isset($_GET['blog_id'])) {
        $blog_id = intval($_GET['blog_id']);
        
        // Fetch all comments for the blog post
        $sql = "SELECT c.*, u.name as username 
                FROM tbl_blog_comments c 
                JOIN tbl_user u ON c.user_id = u.id 
                WHERE c.blog_id = ? 
                ORDER BY c.created_at DESC";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "i", $blog_id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        
        $comments = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $comments[] = [
                'id' => $row['id'],
                'comment' => $row['comment'],
                'username' => $row['username'],
                'created_at' => $row['created_at'],
                'parent_id' => $row['parent_id']
            ];
        }
        
        $response = [
            'success' => true,
            'comments' => $comments
        ];
        
        mysqli_stmt_close($stmt);
    }
}

echo json_encode($response); 