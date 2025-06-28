<?php
require_once 'includes/db_connect.php';
require_once 'includes/blog_functions.php';

// Get current page number
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$posts_per_page = 9;
$offset = ($page - 1) * $posts_per_page;

// Get filter category
$category = isset($_GET['category']) ? $_GET['category'] : 'all';
$search = isset($_GET['search']) ? trim($_GET['search']) : '';

// Build the query
$where_clause = "WHERE 1=1";
if ($category !== 'all') {
    $where_clause .= " AND category = ?";
}
if (!empty($search)) {
    $where_clause .= " AND (title LIKE ? OR s_article LIKE ? OR content LIKE ?)";
}

// Count total posts for pagination
$count_sql = "SELECT COUNT(*) as total FROM tbl_blog " . $where_clause;
$params = [];
$types = "";

if ($category !== 'all') {
    $params[] = $category;
    $types .= "s";
}
if (!empty($search)) {
    $search_param = "%$search%";
    $params[] = $search_param;
    $params[] = $search_param;
    $params[] = $search_param;
    $types .= "sss";
}

$stmt = $conn->prepare($count_sql);
if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$total_posts = $stmt->get_result()->fetch_assoc()['total'];
$total_pages = ceil($total_posts / $posts_per_page);

// Fetch posts
$sql = "SELECT b.*, 
        (SELECT COUNT(*) FROM tbl_blog_comments WHERE blog_id = b.id) as comment_count,
        (SELECT COUNT(*) FROM tbl_blog_likes WHERE blog_id = b.id) as like_count
        FROM tbl_blog b 
        $where_clause 
        ORDER BY b.date DESC 
        LIMIT ? OFFSET ?";

$params[] = $posts_per_page;
$params[] = $offset;
$types .= "ii";

$stmt = $conn->prepare($sql);
if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$blogs = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Blog - Nati Automotive</title>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.css">
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
    /* Blog Hero Section */
    .blog-hero {
        position: relative;
        height: 400px;
        background: url('assets/images/blog-hero-bg.jpg') center/cover no-repeat;
        display: flex;
        align-items: center;
        text-align: center;
        margin-top: 80px;
        overflow: hidden;
    }

    .hero-overlay {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: linear-gradient(135deg, rgba(0,0,0,0.9), rgba(0,15,29,0.85));
    }

    .blog-hero .container {
        position: relative;
        z-index: 2;
        color: #fff;
    }

    .blog-hero h1 {
        font-size: 3.5rem;
        margin-bottom: 20px;
        background: linear-gradient(45deg, #ffbe00, #ff4d00);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        text-shadow: 2px 2px 4px rgba(0,0,0,0.3);
    }

    .blog-hero p {
        font-size: 1.2rem;
        opacity: 0.9;
        max-width: 600px;
        margin: 0 auto;
    }

    /* Blog Filters Section */
    .blog-filters {
        background: #111;
        padding: 30px 0;
        border-bottom: 1px solid rgba(255,255,255,0.1);
    }

    .filters-wrapper {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 20px;
    }

    .filter-container {
        display: flex;
        gap: 15px;
        flex-wrap: wrap;
    }

    .filter-btn {
        display: flex;
        align-items: center;
        gap: 8px;
        padding: 10px 20px;
        background: rgba(255,255,255,0.05);
        border-radius: 25px;
        color: #fff;
        text-decoration: none;
        transition: all 0.3s ease;
        border: 1px solid rgba(255,255,255,0.1);
    }

    .filter-btn i {
        color: #ffbe00;
    }

    .filter-btn:hover,
    .filter-btn.active {
        background: #ffbe00;
        transform: translateY(-2px);
    }

    .filter-btn:hover i,
    .filter-btn.active i {
        color: #000;
    }

    .filter-btn:hover span,
    .filter-btn.active span {
        color: #000;
    }

    .search-container {
        flex: 0 0 300px;
    }

    .search-wrapper {
        position: relative;
    }

    .search-wrapper input {
        width: 100%;
        padding: 12px 20px;
        padding-right: 50px;
        background: rgba(255,255,255,0.05);
        border: 1px solid rgba(255,255,255,0.1);
        border-radius: 25px;
        color: #fff;
        font-size: 0.9rem;
        transition: all 0.3s ease;
    }

    .search-wrapper input:focus {
        background: rgba(255,255,255,0.1);
        border-color: #ffbe00;
        outline: none;
    }

    .search-wrapper button {
        position: absolute;
        right: 5px;
        top: 50%;
        transform: translateY(-50%);
        background: none;
        border: none;
        color: #ffbe00;
        padding: 10px;
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .search-wrapper button:hover {
        color: #fff;
    }

    /* Blog Grid Section */
    .blog-grid-section {
        background: #111;
        padding: 60px 0;
    }

    .blog-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 30px;
        margin-bottom: 50px;
    }

    .blog-card {
        background: rgba(255,255,255,0.05);
        border-radius: 15px;
        overflow: hidden;
        transition: all 0.3s ease;
        border: 1px solid rgba(255,255,255,0.1);
        position: relative;
    }

    .blog-card:hover {
        transform: translateY(-10px);
        box-shadow: 0 10px 30px rgba(255,190,0,0.1);
        border-color: #ffbe00;
    }

    .blog-image {
        position: relative;
        height: 250px;
        overflow: hidden;
        background: rgba(0,0,0,0.1);
    }

    .blog-thumbnail {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.5s ease;
        will-change: transform;
        backface-visibility: hidden;
    }

    .blog-thumbnail.default-image {
        opacity: 0.7;
        filter: grayscale(0.3);
    }

    /* Add loading skeleton animation */
    .blog-image::before {
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

    .blog-image.loaded::before {
        display: none;
    }

    @keyframes loading {
        0% { transform: translateX(-100%); }
        100% { transform: translateX(100%); }
    }

    /* Enhance image hover effects */
    .blog-card:hover .blog-thumbnail {
        transform: scale(1.1) rotate(1deg);
    }

    .blog-category {
        position: absolute;
        top: 20px;
        right: 20px;
        background: rgba(255,190,0,0.9);
        padding: 8px 15px;
        border-radius: 20px;
        color: #000;
        font-size: 0.8rem;
        font-weight: 500;
        display: flex;
        align-items: center;
        gap: 5px;
        backdrop-filter: blur(5px);
        z-index: 2;
        box-shadow: 0 2px 10px rgba(0,0,0,0.2);
        transition: all 0.3s ease;
    }

    .blog-category:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 15px rgba(0,0,0,0.3);
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

    .blog-content {
        padding: 25px;
        color: #fff;
    }

    .blog-meta {
        display: flex;
        gap: 15px;
        margin-bottom: 15px;
        font-size: 0.85rem;
        color: rgba(255,255,255,0.7);
        flex-wrap: wrap;
    }

    .blog-meta span {
        display: flex;
        align-items: center;
        gap: 5px;
    }

    .blog-meta i {
        color: #ffbe00;
    }

    .blog-content h3 {
        margin: 0 0 15px;
        font-size: 1.4rem;
        line-height: 1.4;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }

    .blog-content p {
        margin: 0 0 20px;
        opacity: 0.8;
        line-height: 1.6;
        display: -webkit-box;
        -webkit-line-clamp: 3;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }

    .blog-footer {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding-top: 20px;
        border-top: 1px solid rgba(255,255,255,0.1);
    }

    .read-more {
        color: #ffbe00;
        text-decoration: none;
        display: flex;
        align-items: center;
        gap: 8px;
        font-weight: 500;
        transition: gap 0.3s ease;
    }

    .read-more:hover {
        gap: 12px;
    }

    .blog-stats {
        display: flex;
        gap: 15px;
    }

    .blog-stats span {
        display: flex;
        align-items: center;
        gap: 5px;
        color: rgba(255,255,255,0.7);
        font-size: 0.9rem;
    }

    .blog-stats i {
        color: #ffbe00;
    }

    /* Pagination */
    .pagination {
        display: flex;
        justify-content: center;
        gap: 10px;
        margin-top: 50px;
    }

    .page-link {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 40px;
        height: 40px;
        background: rgba(255,255,255,0.05);
        border: 1px solid rgba(255,255,255,0.1);
        border-radius: 50%;
        color: #fff;
        text-decoration: none;
        transition: all 0.3s ease;
    }

    .page-link:hover,
    .page-link.active {
        background: #ffbe00;
        color: #000;
        transform: translateY(-2px);
    }

    .page-dots {
        color: rgba(255,255,255,0.5);
        display: flex;
        align-items: center;
    }

    /* Newsletter Section */
    .newsletter-section {
        background: linear-gradient(135deg, #000F1D, #001F3D);
        padding: 80px 0;
    }

    .newsletter-content {
        max-width: 600px;
        margin: 0 auto;
        text-align: center;
        color: #fff;
    }

    .newsletter-icon {
        width: 80px;
        height: 80px;
        background: linear-gradient(45deg, #ffbe00, #ff4d00);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 30px;
    }

    .newsletter-icon i {
        font-size: 32px;
        color: #fff;
    }

    .newsletter-content h2 {
        font-size: 2rem;
        margin-bottom: 20px;
    }

    .newsletter-content p {
        opacity: 0.8;
        margin-bottom: 30px;
    }

    .newsletter-form .form-group {
        position: relative;
        max-width: 500px;
        margin: 0 auto;
    }

    .newsletter-form input {
        width: 100%;
        padding: 15px 150px 15px 25px;
        background: rgba(255,255,255,0.05);
        border: 1px solid rgba(255,255,255,0.1);
        border-radius: 30px;
        color: #fff;
        font-size: 1rem;
    }

    .newsletter-form input:focus {
        outline: none;
        border-color: #ffbe00;
    }

    .subscribe-btn {
        position: absolute;
        right: 5px;
        top: 5px;
        background: #ffbe00;
        border: none;
        padding: 10px 25px;
        border-radius: 25px;
        color: #000;
        font-weight: 500;
        cursor: pointer;
        display: flex;
        align-items: center;
        gap: 8px;
        transition: all 0.3s ease;
    }

    .subscribe-btn:hover {
        background: #ff4d00;
        color: #fff;
    }

    /* Responsive Design */
    @media (max-width: 1200px) {
        .blog-grid {
            grid-template-columns: repeat(2, 1fr);
        }
    }

    @media (max-width: 992px) {
        .blog-hero h1 {
            font-size: 2.5rem;
        }
        
        .filters-wrapper {
            flex-direction: column;
        }
        
        .filter-container {
            width: 100%;
            justify-content: center;
        }
        
        .search-container {
            width: 100%;
            flex: none;
        }
    }

    @media (max-width: 768px) {
        .blog-grid {
            grid-template-columns: 1fr;
        }
        
        .blog-hero {
            height: 300px;
        }
        
        .blog-hero h1 {
            font-size: 2rem;
        }
        
        .blog-hero p {
            font-size: 1rem;
        }
        
        .filter-btn {
            padding: 8px 15px;
            font-size: 0.9rem;
        }
    }

    @media (max-width: 480px) {
        .blog-meta {
            flex-direction: column;
            gap: 8px;
        }
        
        .blog-footer {
            flex-direction: column;
            gap: 15px;
            align-items: flex-start;
        }
        
        .blog-stats {
            width: 100%;
            justify-content: space-between;
        }
    }
    </style>
</head>
<body class="dark-theme">
    <?php include 'includes/header.php'; ?>

    <!-- Hero Section -->
    <section class="blog-hero">
        <div class="hero-overlay"></div>
        <div class="container">
            <h1 data-aos="fade-up">Latest News & Tips</h1>
            <p data-aos="fade-up" data-aos-delay="100">Stay updated with automotive news, maintenance tips, and industry insights</p>
        </div>
    </section>

    <!-- Blog Filters -->
    <section class="blog-filters">
        <div class="container">
            <div class="filters-wrapper" data-aos="fade-up">
                <div class="filter-container">
                    <a href="?category=all" class="filter-btn <?php echo $category === 'all' ? 'active' : ''; ?>">
                        <i class="fas fa-th-large"></i>
                        <span>All Posts</span>
                    </a>
                    <a href="?category=news" class="filter-btn <?php echo $category === 'news' ? 'active' : ''; ?>">
                        <i class="fas fa-newspaper"></i>
                        <span>News</span>
                    </a>
                    <a href="?category=maintenance" class="filter-btn <?php echo $category === 'maintenance' ? 'active' : ''; ?>">
                        <i class="fas fa-tools"></i>
                        <span>Maintenance</span>
                    </a>
                    <a href="?category=tips" class="filter-btn <?php echo $category === 'tips' ? 'active' : ''; ?>">
                        <i class="fas fa-lightbulb"></i>
                        <span>Tips & Tricks</span>
                    </a>
                </div>
                <div class="search-container">
                    <form class="search-wrapper" method="GET">
                        <input type="text" name="search" value="<?php echo htmlspecialchars($search); ?>" placeholder="Search articles...">
                        <button type="submit"><i class="fas fa-search"></i></button>
                    </form>
                </div>
            </div>
        </div>
    </section>

    <!-- Blog Grid -->
    <section class="blog-grid-section">
        <div class="container">
            <div class="blog-grid">
                <?php foreach($blogs as $blog): ?>
                    <article class="blog-card" data-aos="fade-up">
                        <div class="blog-image">
                            <?php if($blog['image_url']): ?>
                                <?php
                                $image_path = '../uploads/blogs/' . basename($blog['image_url']);
                                $fallback_image = 'assets/images/default-blog.jpg';
                                
                                // Check if image exists and is readable
                                if(file_exists($image_path) && is_readable($image_path)) {
                                    $image_size = getimagesize($image_path);
                                    $img_width = $image_size[0];
                                    $img_height = $image_size[1];
                                    
                                    // Add loading="lazy" for better performance
                                    echo '<img src="' . htmlspecialchars($image_path) . '" 
                                              alt="' . htmlspecialchars($blog['title']) . '"
                                              loading="lazy"
                                              width="' . $img_width . '"
                                              height="' . $img_height . '"
                                              class="blog-thumbnail">';
                                } else {
                                    echo '<img src="' . $fallback_image . '" 
                                              alt="Default Blog Image"
                                              loading="lazy"
                                              class="blog-thumbnail default-image">';
                                }
                                ?>
                            <?php else: ?>
                                <img src="assets/images/default-blog.jpg" 
                                     alt="Default Blog Image"
                                     loading="lazy"
                                     class="blog-thumbnail default-image">
                            <?php endif; ?>
                            
                            <?php if(isset($blog['category']) && !empty($blog['category'])): ?>
                            <div class="blog-category">
                                <i class="fas fa-tag"></i>
                                <?php echo htmlspecialchars($blog['category']); ?>
                            </div>
                            <?php endif; ?>
                            
                            <div class="image-overlay"></div>
                        </div>
                        <div class="blog-content">
                            <div class="blog-meta">
                                <span><i class="far fa-calendar"></i> <?php echo date('M d, Y', strtotime($blog['date'])); ?></span>
                                <span><i class="far fa-user"></i> <?php echo htmlspecialchars($blog['writer']); ?></span>
                                <span><i class="far fa-clock"></i> <?php echo ceil(str_word_count(strip_tags($blog['content'])) / 200); ?> min read</span>
                            </div>
                            <h3><?php echo htmlspecialchars($blog['title']); ?></h3>
                            <p><?php 
                                $preview = !empty($blog['s_article']) ? $blog['s_article'] : substr(strip_tags($blog['content']), 0, 150);
                                echo htmlspecialchars($preview) . '...'; 
                            ?></p>
                            <div class="blog-footer">
                                <a href="blog-single.php?id=<?php echo $blog['id']; ?>" class="read-more">
                                    Read More <i class="fas fa-arrow-right"></i>
                                </a>
                                <div class="blog-stats">
                                    <span><i class="far fa-comment"></i> <?php echo $blog['comment_count']; ?></span>
                                    <span><i class="far fa-heart"></i> <?php echo $blog['like_count']; ?></span>
                                </div>
                            </div>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>

            <!-- Pagination -->
            <?php if($total_pages > 1): ?>
                <div class="pagination" data-aos="fade-up">
                    <?php if($page > 1): ?>
                        <a href="?page=<?php echo $page-1; ?>&category=<?php echo $category; ?>&search=<?php echo urlencode($search); ?>" class="page-link">
                            <i class="fas fa-chevron-left"></i>
                        </a>
                    <?php endif; ?>

                    <?php for($i = 1; $i <= $total_pages; $i++): ?>
                        <?php if($i == 1 || $i == $total_pages || ($i >= $page-2 && $i <= $page+2)): ?>
                            <a href="?page=<?php echo $i; ?>&category=<?php echo $category; ?>&search=<?php echo urlencode($search); ?>" 
                               class="page-link <?php echo $i === $page ? 'active' : ''; ?>">
                                <?php echo $i; ?>
                            </a>
                        <?php elseif($i == 2 || $i == $total_pages-1): ?>
                            <span class="page-dots">...</span>
                        <?php endif; ?>
                    <?php endfor; ?>

                    <?php if($page < $total_pages): ?>
                        <a href="?page=<?php echo $page+1; ?>&category=<?php echo $category; ?>&search=<?php echo urlencode($search); ?>" class="page-link">
                            <i class="fas fa-chevron-right"></i>
                        </a>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>
    </section>

    <!-- Newsletter Section -->
    <section class="newsletter-section">
        <div class="container">
            <div class="newsletter-content" data-aos="fade-up">
                <div class="newsletter-icon">
                    <i class="far fa-envelope"></i>
                </div>
                <h2>Subscribe to Our Newsletter</h2>
                <p>Stay updated with our latest news and updates delivered directly to your inbox</p>
                <form class="newsletter-form" method="POST" action="subscribe.php">
                    <div class="form-group">
                        <input type="email" name="email" placeholder="Enter your email address" required>
                        <button type="submit" class="subscribe-btn">
                            Subscribe
                            <i class="fas fa-paper-plane"></i>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </section>

    <?php include 'includes/footer.php'; ?>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/js/all.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.js"></script>
    <script>
        // Initialize AOS
        AOS.init({
            duration: 1000,
            once: true
        });

        // Handle image loading
        document.addEventListener('DOMContentLoaded', function() {
            const blogImages = document.querySelectorAll('.blog-thumbnail');
            
            blogImages.forEach(img => {
                if (img.complete) {
                    img.parentElement.classList.add('loaded');
                } else {
                    img.addEventListener('load', function() {
                        this.parentElement.classList.add('loaded');
                    });
                    
                    img.addEventListener('error', function() {
                        this.src = 'assets/images/default-blog.jpg';
                        this.classList.add('default-image');
                        this.parentElement.classList.add('loaded');
                    });
                }
            });
        });
    </script>
</body>
</html>

<?php
// Close database connection
$conn->close();
?> 