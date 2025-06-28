<?php
require_once '../includes/config.php';

if (isset($_GET['id'])) {
    $article_id = (int)$_GET['id'];
    
    // Get article data
    $sql = "SELECT * FROM tbl_articles WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $article_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($article = $result->fetch_assoc()) {
        // Get categories for dropdown
        $cat_sql = "SELECT * FROM tbl_article_categories ORDER BY category_name";
        $cat_result = $conn->query($cat_sql);
        
        $categories = "";
        while ($cat = $cat_result->fetch_assoc()) {
            $selected = ($cat['id'] == $article['category_id']) ? 'selected' : '';
            $categories .= "<option value='" . $cat['id'] . "' " . $selected . ">" . 
                          htmlspecialchars($cat['category_name']) . "</option>";
        }
        
        // Prepare response
        $response = [
            'id' => $article['id'],
            'title' => htmlspecialchars($article['title']),
            'content' => htmlspecialchars($article['content']),
            'category_id' => $article['category_id'],
            'categories' => $categories,
            'status' => $article['status'],
            'current_image' => $article['featured_image'] ? '../' . $article['featured_image'] : null
        ];
        
        echo json_encode($response);
    } else {
        http_response_code(404);
        echo json_encode(['error' => 'Article not found']);
    }
} else {
    http_response_code(400);
    echo json_encode(['error' => 'No article ID provided']);
}
?> 