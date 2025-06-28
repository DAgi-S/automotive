<?php
define('INCLUDED', true);
session_start();
include("partial-front/db_con.php");
include("db_conn.php");

// Fetch all videos from the database
$sql = "SELECT * FROM tbl_tiktok_videos ORDER BY created_at DESC";
$result = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html lang="en">
<?php include 'header.php' ?>

<body>
    <div class="site-content">
        <section class="tiktok-feed-section">
            <div class="container">
                <div class="section-header mt-24">
                    <h1 class="section-title" style="color: blue;">Our TikTok Videos</h1>
                </div>
                <div class="tiktok-grid mt-24">
                    <?php if (mysqli_num_rows($result) > 0): ?>
                        <?php while ($row = mysqli_fetch_assoc($result)): ?>
                            <div class="tiktok-embed-container">
                                <iframe
                                    src="https://www.tiktok.com/embed/<?php echo htmlspecialchars($row['video_id']); ?>"
                                    style="width: 100%; height: 600px; border: none; border-radius: 8px;"
                                    allowfullscreen
                                    allow="encrypted-media"
                                ></iframe>
                                <div class="video-info">
                                    <h3><?php echo htmlspecialchars($row['title']); ?></h3>
                                    <p><?php echo htmlspecialchars($row['description']); ?></p>
                                </div>
                            </div>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <div class="no-videos">
                            <p>No videos available at the moment.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </section>
    </div>

    <!-- Your existing scripts -->
    <script src="assets/js/jquery.min.js"></script>
    <script src="assets/js/slick.min.js"></script>
    <script src="assets/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/custom.js"></script>

    <style>
        .tiktok-feed-section {
            padding: 40px 0;
            background-color: #f8f9fa;
        }

        .section-title {
            text-align: center;
            margin-bottom: 30px;
        }

        .tiktok-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(340px, 1fr));
            gap: 30px;
            padding: 20px;
            justify-items: center;
        }

        .tiktok-embed-container {
            width: 340px;
            background: #fff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        .video-info {
            padding: 15px;
        }

        .video-info h3 {
            margin: 0;
            font-size: 16px;
            color: #333;
        }

        .video-info p {
            margin: 8px 0 0;
            font-size: 14px;
            color: #666;
        }

        .no-videos {
            grid-column: 1 / -1;
            text-align: center;
            padding: 40px;
            background: #fff;
            border-radius: 8px;
        }

        @media (max-width: 768px) {
            .tiktok-grid {
                grid-template-columns: 1fr;
            }
            
            .tiktok-embed-container {
                width: 100%;
                max-width: 340px;
            }
        }
    </style>
</body>
</html> 