<?php
require_once '../includes/config.php';
session_start();

// Function to handle image upload
function handleImageUpload($file) {
    $target_dir = "../uploads/articles/";
    if (!file_exists($target_dir)) {
        mkdir($target_dir, 0777, true);
    }
    
    $file_extension = strtolower(pathinfo($file["name"], PATHINFO_EXTENSION));
    $new_filename = uniqid() . '.' . $file_extension;
    $target_file = $target_dir . $new_filename;
    
    // Check file size (5MB max)
    if ($file["size"] > 5000000) {
        throw new Exception("File is too large. Maximum size is 5MB.");
    }
    
    // Allow certain file formats
    if (!in_array($file_extension, ["jpg", "jpeg", "png", "gif"])) {
        throw new Exception("Only JPG, JPEG, PNG & GIF files are allowed.");
    }
    
    if (move_uploaded_file($file["tmp_name"], $target_file)) {
        return "uploads/articles/" . $new_filename;
    } else {
        throw new Exception("Failed to upload image.");
    }
}

// Add Article
if (isset($_POST['add_article'])) {
    try {
        $title = $_POST['title'];
        $category_id = $_POST['category_id'];
        $content = $_POST['content'];
        $status = $_POST['status'];
        $author = $_SESSION['admin_id']; // Get from session
        
        $featured_image = null;
        if (isset($_FILES['featured_image']) && $_FILES['featured_image']['size'] > 0) {
            $featured_image = handleImageUpload($_FILES['featured_image']);
        }
        
        $sql = "INSERT INTO tbl_articles (title, category_id, content, featured_image, status, author, created_at) 
                VALUES (?, ?, ?, ?, ?, ?, NOW())";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sisssi", $title, $category_id, $content, $featured_image, $status, $author);
        
        if ($stmt->execute()) {
            $_SESSION['success'] = "Article added successfully";
        } else {
            throw new Exception("Error adding article");
        }
    } catch (Exception $e) {
        $_SESSION['error'] = $e->getMessage();
    }
    
    header("Location: articles.php");
    exit();
}

// Update Article
if (isset($_POST['update_article'])) {
    try {
        $article_id = $_POST['article_id'];
        $title = $_POST['title'];
        $category_id = $_POST['category_id'];
        $content = $_POST['content'];
        $status = $_POST['status'];
        
        if (isset($_FILES['featured_image']) && $_FILES['featured_image']['size'] > 0) {
            // Delete old image if exists
            $sql = "SELECT featured_image FROM tbl_articles WHERE id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("i", $article_id);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($row = $result->fetch_assoc()) {
                if ($row['featured_image']) {
                    unlink("../" . $row['featured_image']);
                }
            }
            
            // Upload new image
            $featured_image = handleImageUpload($_FILES['featured_image']);
            
            $sql = "UPDATE tbl_articles SET title = ?, category_id = ?, content = ?, featured_image = ?, status = ?, updated_at = NOW() WHERE id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("sissi", $title, $category_id, $content, $featured_image, $status, $article_id);
        } else {
            $sql = "UPDATE tbl_articles SET title = ?, category_id = ?, content = ?, status = ?, updated_at = NOW() WHERE id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("sissi", $title, $category_id, $content, $status, $article_id);
        }
        
        if ($stmt->execute()) {
            $_SESSION['success'] = "Article updated successfully";
        } else {
            throw new Exception("Error updating article");
        }
    } catch (Exception $e) {
        $_SESSION['error'] = $e->getMessage();
    }
    
    header("Location: articles.php");
    exit();
}

// Delete Article
if (isset($_POST['delete_article'])) {
    try {
        $article_id = $_POST['article_id'];
        
        // Delete associated image first
        $sql = "SELECT featured_image FROM tbl_articles WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $article_id);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($row = $result->fetch_assoc()) {
            if ($row['featured_image']) {
                unlink("../" . $row['featured_image']);
            }
        }
        
        // Delete the article
        $sql = "DELETE FROM tbl_articles WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $article_id);
        
        if ($stmt->execute()) {
            $_SESSION['success'] = "Article deleted successfully";
        } else {
            throw new Exception("Error deleting article");
        }
    } catch (Exception $e) {
        $_SESSION['error'] = $e->getMessage();
    }
    
    header("Location: articles.php");
    exit();
}

// Handle Add Category
if (isset($_POST['add_category'])) {
    header('Content-Type: application/json');
    
    $category_name = trim($_POST['category_name']);
    $category_description = trim($_POST['category_description']);
    
    // Validate category name
    if (empty($category_name)) {
        echo json_encode([
            'success' => false,
            'message' => 'Category name is required'
        ]);
        exit();
    }
    
    try {
        // Check if category already exists
        $check_sql = "SELECT id FROM tbl_article_categories WHERE category_name = ?";
        $check_stmt = $conn->prepare($check_sql);
        $check_stmt->bind_param("s", $category_name);
        $check_stmt->execute();
        $check_result = $check_stmt->get_result();
        
        if ($check_result->num_rows > 0) {
            echo json_encode([
                'success' => false,
                'message' => 'Category already exists'
            ]);
            exit();
        }
        
        // Insert new category
        $sql = "INSERT INTO tbl_article_categories (category_name, description, created_at) VALUES (?, ?, NOW())";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ss", $category_name, $category_description);
        
        if ($stmt->execute()) {
            $category_id = $stmt->insert_id;
            echo json_encode([
                'success' => true,
                'category_id' => $category_id,
                'category_name' => $category_name,
                'message' => 'Category added successfully'
            ]);
        } else {
            throw new Exception("Error executing query: " . $stmt->error);
        }
    } catch (Exception $e) {
        echo json_encode([
            'success' => false,
            'message' => 'Error adding category: ' . $e->getMessage()
        ]);
    }
    exit();
}
?> 