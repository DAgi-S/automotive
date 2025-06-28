<?php
session_start();
define('INCLUDED', true);

if (!isset($_SESSION['password'])) {
    header("location: login.php?message=login first");
    exit;
}

include("partial-front/db_con.php");
include("db_conn.php");
include 'header.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Nati Automotive - Blogs</title>
    <style>
        .blog-listing {
            padding: 10px;
            max-width: 1200px;
            margin: 0 auto;
        }
        .blog-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 12px;
            margin-top: 15px;
        }
        .blog-card {
            background: #fff;
            border: 1px solid #e0e0e0;
            border-radius: 8px;
            overflow: hidden;
            transition: transform 0.3s ease;
            height: 100%;
        }
        .blog-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        .img-container {
            height: 150px;
            overflow: hidden;
            background: #f8f9fa;
        }
        .img_format {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        .card-body {
            padding: 10px;
            display: flex;
            flex-direction: column;
            height: calc(100% - 150px);
        }
        .card-title {
            font-size: 12px;
            color: #333;
            margin-bottom: 6px;
            font-weight: 600;
            line-height: 1.3;
        }
        .card-text {
            font-size: 11px;
            color: #666;
            margin-bottom: 8px;
            line-height: 1.4;
            flex: 1;
        }
        .blog-meta {
            font-size: 11px;
            color: #888;
            margin-bottom: 8px;
            border-top: 1px solid #eee;
            padding-top: 6px;
        }
        .blog-meta i {
            width: 12px;
            margin-right: 3px;
            color: #0066cc;
        }
        .blog-meta > div {
            margin-bottom: 3px;
        }
        .read-more-btn {
            display: inline-block;
            padding: 4px 10px;
            background: #0066cc;
            color: white;
            text-decoration: none;
            border-radius: 4px;
            font-size: 11px;
            transition: background 0.3s ease;
        }
        .read-more-btn:hover {
            background: #0052a3;
            color: white;
        }
        .page-title {
            text-align: center;
            color: #333;
            margin: 15px 0;
            font-size: 18px;
        }
        @media (max-width: 768px) {
            .blog-grid {
                gap: 10px;
                padding: 5px;
            }
            .img-container {
                height: 140px;
            }
            .card-body {
                padding: 8px;
            }
            .page-title {
                font-size: 16px;
                margin: 10px 0;
            }
        }
    </style>
</head>
<body>
    <div class="site-content">
        <?php include 'top_nav.php'; ?>
        
        <div class="blog-listing">
            <h1 class="page-title">Our Blog Posts</h1>
            
            <div class="blog-grid">
                <?php
                $db = new DB_con();
                $result = $db->fetchAllArticles();

                if ($result && $result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        ?>
                        <div class="blog-card">
                            <div class="img-container">
                                <?php if (!empty($row['image_url']) && file_exists('' . $row['image_url'])): ?>
                                    <img src="<?php echo htmlspecialchars($row['image_url']); ?>" 
                                         alt="<?php echo htmlspecialchars($row['title']); ?>" 
                                         class="img_format"/>
                                <?php else: ?>
                                    <img src="assets/images/Nati-logo.png" 
                                         alt="Nati Automotive Logo" 
                                         class="img_format"/>
                                <?php endif; ?>
                            </div>
                            <div class="card-body">
                                <h5 class="card-title">
                                    <?php echo htmlspecialchars($row['title']); ?>
                                </h5>
                                <p class="card-text">
                                    <?php echo htmlspecialchars($row['s_article']); ?>
                                </p>
                                <div class="blog-meta">
                                    <div><i class="fas fa-user"></i> <?php echo htmlspecialchars($row['writer']); ?></div>
                                    <div><i class="far fa-calendar"></i> <?php echo date('M d, Y', strtotime($row['date'])); ?></div>
                                </div>
                                <div class="text-end">
                                    <a href="blog.php?blogid=<?php echo $row['id']; ?>" class="read-more-btn">
                                        Read More <i class="fas fa-arrow-right"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                        <?php
                    }
                } else {
                    echo '<div style="grid-column: 1/-1; text-align: center;">
                            <div class="alert alert-info" style="font-size: 11px;">No blog posts available at the moment.</div>
                          </div>';
                }
                ?>
            </div>
        </div>

        <?php include 'partial-front/bottom_nav.php'; ?>
        <?php include 'option.php'; ?>
    </div>

    <script src="assets/js/jquery.min.js"></script>
    <script src="assets/js/slick.min.js"></script>
    <script src="assets/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/modal.js"></script>
    <script src="assets/js/custom.js"></script>
</body>
</html> 