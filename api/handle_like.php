<?php
session_start();
header('Content-Type: application/json');

include("../db_conn.php");

if (!isset($_SESSION['id'])) {
    echo json_encode(['error' => 'User not logged in']);
    exit;
}

$response = ['success' => false];

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    if (isset($_GET['blog_id'])) {
        $blog_id = intval($_GET['blog_id']);
        
        // Get counts for the blog post
        $count_sql = "SELECT 
            SUM(is_like = 1) as likes,
            SUM(is_like = 0) as dislikes
            FROM tbl_blog_likes 
            WHERE blog_id = ?";
        $count_stmt = mysqli_prepare($conn, $count_sql);
        mysqli_stmt_bind_param($count_stmt, "i", $blog_id);
        mysqli_stmt_execute($count_stmt);
        $counts = mysqli_fetch_assoc(mysqli_stmt_get_result($count_stmt));
        mysqli_stmt_close($count_stmt);

        // Get user's current like status if logged in
        $user_status = null;
        if (isset($_SESSION['id'])) {
            $status_sql = "SELECT is_like FROM tbl_blog_likes WHERE blog_id = ? AND user_id = ?";
            $status_stmt = mysqli_prepare($conn, $status_sql);
            mysqli_stmt_bind_param($status_stmt, "ii", $blog_id, $_SESSION['id']);
            mysqli_stmt_execute($status_stmt);
            $status_result = mysqli_stmt_get_result($status_stmt);
            if ($status_row = mysqli_fetch_assoc($status_result)) {
                $user_status = $status_row['is_like'] ? 'liked' : 'disliked';
            }
            mysqli_stmt_close($status_stmt);
        }

        $response = [
            'success' => true,
            'likes' => intval($counts['likes'] ?? 0),
            'dislikes' => intval($counts['dislikes'] ?? 0),
            'user_status' => $user_status
        ];
    }
} else if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['blog_id']) && isset($_POST['is_like'])) {
        $blog_id = intval($_POST['blog_id']);
        $user_id = intval($_SESSION['id']);
        $is_like = $_POST['is_like'] === 'true' ? 1 : 0;

        // Check if user already liked/disliked
        $check_sql = "SELECT * FROM tbl_blog_likes WHERE blog_id = ? AND user_id = ?";
        $check_stmt = mysqli_prepare($conn, $check_sql);
        mysqli_stmt_bind_param($check_stmt, "ii", $blog_id, $user_id);
        mysqli_stmt_execute($check_stmt);
        $result = mysqli_stmt_get_result($check_stmt);

        if (mysqli_num_rows($result) > 0) {
            // Update existing record
            $update_sql = "UPDATE tbl_blog_likes SET is_like = ? WHERE blog_id = ? AND user_id = ?";
            $update_stmt = mysqli_prepare($conn, $update_sql);
            mysqli_stmt_bind_param($update_stmt, "iii", $is_like, $blog_id, $user_id);
            $success = mysqli_stmt_execute($update_stmt);
            mysqli_stmt_close($update_stmt);
        } else {
            // Insert new record
            $insert_sql = "INSERT INTO tbl_blog_likes (blog_id, user_id, is_like) VALUES (?, ?, ?)";
            $insert_stmt = mysqli_prepare($conn, $insert_sql);
            mysqli_stmt_bind_param($insert_stmt, "iii", $blog_id, $user_id, $is_like);
            $success = mysqli_stmt_execute($insert_stmt);
            mysqli_stmt_close($insert_stmt);
        }

        mysqli_stmt_close($check_stmt);

        if ($success) {
            // Get updated counts
            $count_sql = "SELECT 
                SUM(is_like = 1) as likes,
                SUM(is_like = 0) as dislikes
                FROM tbl_blog_likes 
                WHERE blog_id = ?";
            $count_stmt = mysqli_prepare($conn, $count_sql);
            mysqli_stmt_bind_param($count_stmt, "i", $blog_id);
            mysqli_stmt_execute($count_stmt);
            $counts = mysqli_fetch_assoc(mysqli_stmt_get_result($count_stmt));
            mysqli_stmt_close($count_stmt);

            $response = [
                'success' => true,
                'likes' => intval($counts['likes']),
                'dislikes' => intval($counts['dislikes'])
            ];
        }
    }
}

echo json_encode($response); 