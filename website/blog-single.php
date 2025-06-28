<?php
require_once 'includes/db_connect.php';
require_once 'includes/blog_functions.php';

// Get blog ID from URL
$blog_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Fetch blog details
$sql = "SELECT b.*, 
        (SELECT COUNT(*) FROM tbl_blog_comments WHERE blog_id = b.id) as comment_count,
        (SELECT COUNT(*) FROM tbl_blog_likes WHERE blog_id = b.id) as like_count
        FROM tbl_blog b 
        WHERE b.id = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $blog_id);
$stmt->execute();
$result = $stmt->get_result();
$blog = $result->fetch_assoc();

// If blog not found, redirect to blogs page
if (!$blog) {
    header("Location: blogs.php");
    exit();
}

// Fetch comments for this blog
$sql = "SELECT c.*, u.name as username, c.comment as comment_text 
        FROM tbl_blog_comments c 
        LEFT JOIN tbl_user u ON c.user_id = u.id 
        WHERE c.blog_id = ? 
        ORDER BY c.created_at DESC";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $blog_id);
$stmt->execute();
$comments_result = $stmt->get_result();
$comments = [];
while ($row = $comments_result->fetch_assoc()) {
    $comments[] = $row;
}

// Handle like/unlike action
if (isset($_POST['action']) && $_POST['action'] === 'toggle_like') {
    $user_id = 1; // Placeholder for demo
    
    $sql = "SELECT id FROM tbl_blog_likes WHERE blog_id = ? AND user_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $blog_id, $user_id);
    $stmt->execute();
    $like_result = $stmt->get_result();
    
    if ($like_result->num_rows > 0) {
        $sql = "DELETE FROM tbl_blog_likes WHERE blog_id = ? AND user_id = ?";
    } else {
        $sql = "INSERT INTO tbl_blog_likes (blog_id, user_id) VALUES (?, ?)";
    }
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $blog_id, $user_id);
    $stmt->execute();
    
    header("Location: blog-single.php?id=" . $blog_id);
    exit();
}

// Handle new comment submission
if (isset($_POST['action']) && $_POST['action'] === 'add_comment') {
    $user_id = 1; // Placeholder for demo
    $comment_text = trim($_POST['comment_text']);
    
    if (!empty($comment_text)) {
        $sql = "INSERT INTO tbl_blog_comments (blog_id, user_id, comment) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("iis", $blog_id, $user_id, $comment_text);
        $stmt->execute();
        
        header("Location: blog-single.php?id=" . $blog_id);
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($blog['title']); ?> - Nati Automotive</title>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body class="dark-theme">
    <?php include 'includes/header.php'; ?>

    <!-- Blog Content -->
    <main class="blog-single-page">
        <div class="blog-container">
            <div class="blog-main-content">
                <article class="blog-post">
                    <?php if($blog['image_url']): ?>
                        <div class="blog-hero-image">
                            <?php
                            $image_path = '../uploads/blogs/' . basename($blog['image_url']);
                            $fallback_image = 'assets/images/default-blog.jpg';
                            
                            // Check if image exists and is readable
                            if(file_exists($image_path) && is_readable($image_path)) {
                                $image_size = getimagesize($image_path);
                                $img_width = $image_size[0];
                                $img_height = $image_size[1];
                                
                                echo '<img src="' . htmlspecialchars($image_path) . '" 
                                          alt="' . htmlspecialchars($blog['title']) . '"
                                          loading="lazy"
                                          width="' . $img_width . '"
                                          height="' . $img_height . '"
                                          class="blog-hero-thumbnail">';
                            } else {
                                echo '<img src="' . $fallback_image . '" 
                                          alt="Default Blog Image"
                                          loading="lazy"
                                          class="blog-hero-thumbnail default-image">';
                            }
                            ?>
                            <div class="image-overlay"></div>
                        </div>
                    <?php endif; ?>

                    <div class="blog-header">
                        <h1><?php echo htmlspecialchars($blog['title']); ?></h1>
                        <div class="blog-meta">
                            <span class="date">
                                <i class="far fa-calendar"></i>
                                <?php echo date('M d, Y', strtotime($blog['date'])); ?>
                            </span>
                            <span class="author">
                                <i class="far fa-user"></i>
                                <?php echo htmlspecialchars($blog['writer']); ?>
                            </span>
                            <span class="comments">
                                <i class="far fa-comments"></i>
                                <?php echo $blog['comment_count']; ?> Comments
                            </span>
                            <span class="likes">
                                <i class="far fa-heart"></i>
                                <?php echo $blog['like_count']; ?> Likes
                            </span>
                        </div>
                    </div>

                    <div class="blog-content">
                        <?php if (!empty($blog['s_article'])): ?>
                            <div class="blog-summary">
                                <?php echo nl2br(htmlspecialchars($blog['s_article'])); ?>
                            </div>
                        <?php endif; ?>

                        <div class="blog-full-content">
                            <?php echo nl2br(htmlspecialchars($blog['content'])); ?>
                        </div>

                        <div class="blog-actions">
                            <form method="POST" class="like-form">
                                <input type="hidden" name="action" value="toggle_like">
                                <button type="submit" class="like-btn <?php echo $is_liked ? 'liked' : ''; ?>">
                                    <i class="<?php echo $is_liked ? 'fas' : 'far'; ?> fa-heart"></i>
                                    Like this post
                                </button>
                            </form>
                        </div>
                    </div>
                </article>
            </div>

            <!-- Comments Sidebar -->
            <aside class="comments-sidebar">
                <div class="comments-section">
                    <h2>Comments (<?php echo count($comments); ?>)</h2>
                    
                    <div class="comment-form-wrapper">
                        <form method="POST" class="comment-form">
                            <input type="hidden" name="action" value="add_comment">
                            <textarea name="comment_text" placeholder="Write your comment..." required></textarea>
                            <button type="submit" class="post-comment-btn">Post Comment</button>
                        </form>
                    </div>

                    <div class="comments-list">
                        <?php foreach($comments as $comment): ?>
                            <div class="comment">
                                <div class="comment-avatar">
                                    <i class="fas fa-user-circle"></i>
                                </div>
                                <div class="comment-content">
                                    <div class="comment-header">
                                        <span class="comment-author">
                                            <?php echo htmlspecialchars($comment['username'] ?? 'Anonymous'); ?>
                                        </span>
                                        <span class="comment-date">
                                            <?php echo date('M d, Y', strtotime($comment['created_at'])); ?>
                                        </span>
                                    </div>
                                    <div class="comment-text">
                                        <?php echo nl2br(htmlspecialchars($comment['comment_text'])); ?>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </aside>
        </div>
    </main>

    <?php include 'includes/footer.php'; ?>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/js/all.min.js"></script>
    <script>
        // Handle hero image loading
        document.addEventListener('DOMContentLoaded', function() {
            const heroImage = document.querySelector('.blog-hero-thumbnail');
            if (heroImage) {
                if (heroImage.complete) {
                    heroImage.parentElement.classList.add('loaded');
                } else {
                    heroImage.addEventListener('load', function() {
                        this.parentElement.classList.add('loaded');
                    });
                    
                    heroImage.addEventListener('error', function() {
                        this.src = 'assets/images/default-blog.jpg';
                        this.classList.add('default-image');
                        this.parentElement.classList.add('loaded');
                    });
                }
            }
        });
    </script>
</body>
</html>

<style>
/* Blog Single Page Styles */
.blog-single-page {
    padding: 80px 0 0;
    background: #111;
    min-height: calc(100vh - 80px);
    margin-top: 80px;
}

.blog-container {
    max-width: 1400px;
    margin: 0 auto;
    padding: 0 20px;
    display: grid;
    grid-template-columns: 1fr 400px;
    gap: 30px;
}

.blog-main-content {
    background: rgba(255,255,255,0.05);
    border-radius: 15px;
    overflow: hidden;
    border: 1px solid rgba(255,255,255,0.1);
}

.blog-hero-image {
    position: relative;
    width: 100%;
    height: 400px;
    overflow: hidden;
    background: rgba(0,0,0,0.1);
}

.blog-hero-image::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255,255,255,0.1), transparent);
    animation: loading 1.5s infinite;
    z-index: 1;
}

.blog-hero-image.loaded::before {
    display: none;
}

@keyframes loading {
    0% { transform: translateX(-100%); }
    100% { transform: translateX(100%); }
}

.blog-hero-thumbnail {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.5s ease;
    will-change: transform;
    backface-visibility: hidden;
}

.blog-hero-thumbnail.default-image {
    opacity: 0.7;
    filter: grayscale(0.3);
}

.image-overlay {
    position: absolute;
    bottom: 0;
    left: 0;
    width: 100%;
    height: 60%;
    background: linear-gradient(to top, 
        rgba(0,0,0,0.9) 0%,
        rgba(0,0,0,0.7) 30%,
        rgba(0,0,0,0.4) 60%,
        transparent 100%
    );
    z-index: 1;
    pointer-events: none;
}

.blog-header {
    padding: 30px;
    border-bottom: 1px solid rgba(255,255,255,0.1);
}

.blog-header h1 {
    color: #fff;
    margin: 0 0 20px;
    font-size: 2.5rem;
    line-height: 1.3;
}

.blog-meta {
    display: flex;
    flex-wrap: wrap;
    gap: 20px;
    color: rgba(255,255,255,0.7);
    font-size: 0.9rem;
}

.blog-meta span {
    display: flex;
    align-items: center;
    gap: 5px;
}

.blog-meta i {
    color: #ffbe00;
}

.blog-content {
    padding: 30px;
    color: #fff;
}

.blog-summary {
    font-size: 1.2rem;
    line-height: 1.8;
    color: rgba(255,255,255,0.9);
    margin-bottom: 30px;
    padding-bottom: 30px;
    border-bottom: 1px solid rgba(255,255,255,0.1);
}

.blog-full-content {
    line-height: 1.8;
    color: rgba(255,255,255,0.8);
    margin-bottom: 30px;
}

.blog-actions {
    padding-top: 20px;
    border-top: 1px solid rgba(255,255,255,0.1);
}

.like-btn {
    background: transparent;
    border: 2px solid #ffbe00;
    color: #ffbe00;
    padding: 10px 20px;
    border-radius: 25px;
    cursor: pointer;
    transition: all 0.3s ease;
    display: flex;
    align-items: center;
    gap: 8px;
}

.like-btn:hover, .like-btn.liked {
    background: #ffbe00;
    color: #000;
}

/* Comments Sidebar */
.comments-sidebar {
    position: sticky;
    top: 100px;
    height: calc(100vh - 120px);
    overflow-y: auto;
}

.comments-section {
    background: rgba(255,255,255,0.05);
    border-radius: 15px;
    border: 1px solid rgba(255,255,255,0.1);
    padding: 20px;
}

.comments-section h2 {
    color: #fff;
    margin: 0 0 20px;
    font-size: 1.5rem;
}

.comment-form-wrapper {
    margin-bottom: 30px;
}

.comment-form textarea {
    width: 100%;
    height: 100px;
    padding: 15px;
    border-radius: 10px;
    border: 1px solid rgba(255,255,255,0.1);
    background: rgba(255,255,255,0.05);
    color: #fff;
    margin-bottom: 15px;
    resize: vertical;
    font-family: inherit;
}

.post-comment-btn {
    width: 100%;
    background: #ffbe00;
    color: #000;
    border: none;
    padding: 12px;
    border-radius: 25px;
    cursor: pointer;
    font-weight: 500;
    transition: all 0.3s ease;
}

.post-comment-btn:hover {
    background: #0088cc;
    color: #fff;
}

.comments-list {
    display: flex;
    flex-direction: column;
    gap: 15px;
}

.comment {
    display: flex;
    gap: 15px;
    padding: 15px;
    background: rgba(255,255,255,0.03);
    border-radius: 10px;
}

.comment-avatar {
    font-size: 2rem;
    color: rgba(255,255,255,0.5);
    flex-shrink: 0;
}

.comment-content {
    flex: 1;
    min-width: 0;
}

.comment-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 8px;
}

.comment-author {
    color: #ffbe00;
    font-weight: 500;
    font-size: 0.95rem;
}

.comment-date {
    color: rgba(255,255,255,0.5);
    font-size: 0.85rem;
}

.comment-text {
    color: rgba(255,255,255,0.8);
    line-height: 1.6;
    font-size: 0.95rem;
    word-wrap: break-word;
}

/* Custom Scrollbar for Comments */
.comments-sidebar::-webkit-scrollbar {
    width: 6px;
}

.comments-sidebar::-webkit-scrollbar-track {
    background: rgba(255,255,255,0.05);
}

.comments-sidebar::-webkit-scrollbar-thumb {
    background: rgba(255,255,255,0.2);
    border-radius: 3px;
}

.comments-sidebar::-webkit-scrollbar-thumb:hover {
    background: rgba(255,255,255,0.3);
}

/* Responsive Design */
@media (max-width: 1200px) {
    .blog-container {
        grid-template-columns: 1fr 350px;
    }
}

@media (max-width: 992px) {
    .blog-container {
        grid-template-columns: 1fr;
    }

    .comments-sidebar {
        position: static;
        height: auto;
        margin-bottom: 40px;
    }

    .blog-hero-image {
        height: 300px;
    }

    .blog-header h1 {
        font-size: 2rem;
    }
}

@media (max-width: 768px) {
    .blog-single-page {
        padding: 60px 0 0;
    }

    .blog-container {
        padding: 0 15px;
    }

    .blog-hero-image {
        height: 250px;
    }

    .blog-header {
        padding: 20px;
    }

    .blog-header h1 {
        font-size: 1.8rem;
    }

    .blog-content {
        padding: 20px;
    }

    .blog-meta {
        gap: 15px;
        font-size: 0.85rem;
    }

    .comment {
        flex-direction: column;
        gap: 10px;
    }

    .comment-avatar {
        font-size: 1.5rem;
    }

    .comment-header {
        flex-direction: column;
        align-items: flex-start;
        gap: 5px;
    }
}

@media (max-width: 480px) {
    .blog-single-page {
        margin-top: 60px;
    }

    .blog-container {
        padding: 0 10px;
    }

    .blog-header h1 {
        font-size: 1.5rem;
    }

    .blog-meta {
        font-size: 0.8rem;
    }

    .blog-hero-image {
        height: 200px;
    }
}
</style>