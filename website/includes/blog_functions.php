<?php
require_once 'db_connect.php';

function getLatestBlogs($limit = 3) {
    global $conn;
    
    $sql = "SELECT b.*, 
            (SELECT COUNT(*) FROM tbl_blog_comments WHERE blog_id = b.id) as comment_count,
            (SELECT COUNT(*) FROM tbl_blog_likes WHERE blog_id = b.id) as like_count
            FROM tbl_blog b 
            WHERE b.status = 'featured' 
            ORDER BY b.date DESC 
            LIMIT ?";
            
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $limit);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $blogs = array();
    while($row = $result->fetch_assoc()) {
        // Format the date
        $row['formatted_date'] = date('M d, Y', strtotime($row['date']));
        
        // Truncate s_article for preview
        $row['preview'] = strlen($row['s_article']) > 150 ? 
                         substr($row['s_article'], 0, 150) . '...' : 
                         $row['s_article'];
                         
        $blogs[] = $row;
    }
    
    $stmt->close();
    return $blogs;
}

function getLatestArticles($limit = 3) {
    global $conn;
    
    $sql = "SELECT a.*, ac.category_name 
            FROM tbl_articles a 
            LEFT JOIN tbl_article_categories ac ON a.category_id = ac.id 
            WHERE a.status = 'published' 
            ORDER BY a.created_at DESC 
            LIMIT ?";
            
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $limit);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $articles = array();
    while($row = $result->fetch_assoc()) {
        // Format the date
        $row['formatted_date'] = date('M d, Y', strtotime($row['created_at']));
        
        // Truncate content for preview
        $row['preview'] = strlen($row['content']) > 150 ? 
                         substr($row['content'], 0, 150) . '...' : 
                         $row['content'];
                         
        $articles[] = $row;
    }
    
    $stmt->close();
    return $articles;
}
?> 