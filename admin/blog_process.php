<?php
require_once '../includes/config.php';

// Function to handle image upload
function handleImageUpload($file) {
    $target_dir = "../uploads/blogs/";
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
        return "uploads/blogs/" . $new_filename;
    } else {
        throw new Exception("Failed to upload image.");
    }
}

// Add Blog
if (isset($_POST['add_blog'])) {
    try {
        $title = $_POST['title'];
        $writer = $_POST['writer'];
        $s_article = $_POST['s_article'];
        $content = $_POST['content'];
        $status = $_POST['status'];
        
        // Handle image upload
        $image_url = null;
        if (isset($_FILES['image_url']) && $_FILES['image_url']['size'] > 0) {
            $image_url = handleImageUpload($_FILES['image_url']);
        }
        
        $sql = "INSERT INTO tbl_blog (title, writer, s_article, content, image_url, status, date) 
                VALUES (?, ?, ?, ?, ?, ?, NOW())";
        $stmt = $conn->prepare($sql);
        
        if ($stmt->execute([$title, $writer, $s_article, $content, $image_url, $status])) {
            $_SESSION['success'] = "Blog post added successfully";
        } else {
            throw new Exception("Error adding blog post");
        }
    } catch (Exception $e) {
        $_SESSION['error'] = $e->getMessage();
    }
    
    header("Location: blogs.php");
    exit();
}

// Update Blog
if (isset($_POST['update_blog'])) {
    try {
        $blog_id = $_POST['blog_id'];
        $title = $_POST['title'];
        $writer = $_POST['writer'];
        $s_article = $_POST['s_article'];
        $content = $_POST['content'];
        $status = $_POST['status'];
        
        if (isset($_FILES['image_url']) && $_FILES['image_url']['size'] > 0) {
            // Delete old image if exists
            $sql = "SELECT image_url FROM tbl_blog WHERE id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->execute([$blog_id]);
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($row && $row['image_url']) {
                unlink("../" . $row['image_url']);
            }
            
            // Upload new image
            $image_url = handleImageUpload($_FILES['image_url']);
            
            $sql = "UPDATE tbl_blog SET title = ?, writer = ?, s_article = ?, content = ?, image_url = ?, status = ? WHERE id = ?";
            $stmt = $conn->prepare($sql);
            if ($stmt->execute([$title, $writer, $s_article, $content, $image_url, $status, $blog_id])) {
                $_SESSION['success'] = "Blog post updated successfully";
            } else {
                throw new Exception("Error updating blog post");
            }
        } else {
            $sql = "UPDATE tbl_blog SET title = ?, writer = ?, s_article = ?, content = ?, status = ? WHERE id = ?";
            $stmt = $conn->prepare($sql);
            if ($stmt->execute([$title, $writer, $s_article, $content, $status, $blog_id])) {
                $_SESSION['success'] = "Blog post updated successfully";
            } else {
                throw new Exception("Error updating blog post");
            }
        }
    } catch (Exception $e) {
        $_SESSION['error'] = $e->getMessage();
    }
    
    header("Location: blogs.php");
    exit();
}

// Delete Blog
if (isset($_POST['delete_blog'])) {
    try {
        $blog_id = $_POST['blog_id'];
        
        // Delete associated comments
        $sql = "DELETE FROM tbl_blog_comments WHERE blog_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$blog_id]);
        
        // Delete associated likes
        $sql = "DELETE FROM tbl_blog_likes WHERE blog_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$blog_id]);
        
        // Delete image if exists
        $sql = "SELECT image_url FROM tbl_blog WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$blog_id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($row && $row['image_url']) {
            unlink("../" . $row['image_url']);
        }
        
        // Delete the blog
        $sql = "DELETE FROM tbl_blog WHERE id = ?";
        $stmt = $conn->prepare($sql);
        
        if ($stmt->execute([$blog_id])) {
            $_SESSION['success'] = "Blog post deleted successfully";
        } else {
            throw new Exception("Error deleting blog post");
        }
    } catch (Exception $e) {
        $_SESSION['error'] = $e->getMessage();
    }
    
    header("Location: blogs.php");
    exit();
}

// Delete Comment
if (isset($_POST['delete_comment'])) {
    try {
        $comment_id = $_POST['comment_id'];
        
        // Delete the comment
        $sql = "DELETE FROM tbl_blog_comments WHERE id = ?";
        $stmt = $conn->prepare($sql);
        
        if ($stmt->execute([$comment_id])) {
            $_SESSION['success'] = "Comment deleted successfully";
            if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
                echo json_encode(['success' => true]);
                exit;
            }
        } else {
            throw new Exception("Error deleting comment");
        }
    } catch (Exception $e) {
        $_SESSION['error'] = $e->getMessage();
        if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
            http_response_code(500);
            echo json_encode(['error' => $e->getMessage()]);
            exit;
        }
    }
    
    if (empty($_SERVER['HTTP_X_REQUESTED_WITH'])) {
        header("Location: blogs.php");
        exit();
    }
}

// Add Reply to Comment
if (isset($_POST['add_reply'])) {
    try {
        $parent_id = $_POST['parent_id'];
        $blog_id = $_POST['blog_id'];
        $comment = $_POST['comment'];
        
        // Insert the reply
        $sql = "INSERT INTO tbl_blog_comments (blog_id, comment, parent_id, created_at) VALUES (?, ?, ?, NOW())";
        $stmt = $conn->prepare($sql);
        
        if ($stmt->execute([$blog_id, $comment, $parent_id])) {
            if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
                echo json_encode(['success' => true]);
                exit;
            }
            $_SESSION['success'] = "Reply added successfully";
        } else {
            throw new Exception("Error adding reply");
        }
    } catch (Exception $e) {
        if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
            http_response_code(500);
            echo json_encode(['error' => $e->getMessage()]);
            exit;
        }
        $_SESSION['error'] = $e->getMessage();
    }
    
    if (empty($_SERVER['HTTP_X_REQUESTED_WITH'])) {
        header("Location: blogs.php");
        exit();
    }
}
?> 