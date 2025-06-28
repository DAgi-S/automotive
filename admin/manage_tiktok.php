<?php
session_start();
include("../partial-front/db_con.php");
include("../db_conn.php");

// Check if admin is logged in
if (!isset($_SESSION['password']) || !isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("location: ../login.php?message=Please login as admin");
    exit;
}

// Handle form submission
if (isset($_POST['add_video'])) {
    $video_url = mysqli_real_escape_string($conn, $_POST['video_url']);
    $title = mysqli_real_escape_string($conn, $_POST['title']);
    $description = mysqli_real_escape_string($conn, $_POST['description']);
    
    // Extract video ID from TikTok URL
    if (preg_match('/(?:https?:\/\/)?(?:www\.)?(?:tiktok\.com\/@[\w.-]+\/video\/(\d+))/', $video_url, $matches)) {
        $video_id = $matches[1];
    } elseif (preg_match('/(\d{15,})/', $video_url, $matches)) {
        // If user directly pastes a video ID
        $video_id = $matches[1];
    } else {
        $video_id = '';
    }
    
    if ($video_id) {
        // Store the original URL for reference
        $original_url = $video_url;
        // Create embed URL
        $video_url = "https://www.tiktok.com/embed/" . $video_id;
        
        $sql = "INSERT INTO tbl_tiktok_videos (video_url, video_id, title, description) VALUES (?, ?, ?, ?)";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "ssss", $video_url, $video_id, $title, $description);
        
        if (mysqli_stmt_execute($stmt)) {
            $success_message = "Video added successfully!";
        } else {
            $error_message = "Error adding video. Please try again.";
        }
        mysqli_stmt_close($stmt);
    } else {
        $error_message = "Invalid TikTok URL. Please enter a valid video URL.";
    }
}

// Delete video
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $sql = "DELETE FROM tbl_tiktok_videos WHERE id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $id);
    
    if (mysqli_stmt_execute($stmt)) {
        $success_message = "Video deleted successfully!";
    } else {
        $error_message = "Error deleting video. Please try again.";
    }
    mysqli_stmt_close($stmt);
}

// Fetch all videos
$sql = "SELECT * FROM tbl_tiktok_videos ORDER BY created_at DESC";
$result = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage TikTok Videos - Admin Panel</title>
    <link rel="stylesheet" href="../assets/css/bootstrap.min.css">
    <style>
        .admin-container {
            padding: 20px;
        }
        .video-form {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 20px;
        }
        .video-list {
            margin-top: 30px;
        }
        .video-item {
            background: white;
            padding: 15px;
            margin-bottom: 15px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
    </style>
</head>
<body>
    <div class="admin-container">
        <h1 class="mb-4">Manage TikTok Videos</h1>
        
        <?php if (isset($success_message)): ?>
            <div class="alert alert-success"><?php echo $success_message; ?></div>
        <?php endif; ?>
        
        <?php if (isset($error_message)): ?>
            <div class="alert alert-danger"><?php echo $error_message; ?></div>
        <?php endif; ?>
        
        <div class="video-form">
            <h3>Add New TikTok Video</h3>
            <form method="POST" action="">
                <div class="mb-3">
                    <label for="video_url" class="form-label">TikTok Video URL</label>
                    <input type="url" class="form-control" id="video_url" name="video_url" required>
                </div>
                <div class="mb-3">
                    <label for="title" class="form-label">Title</label>
                    <input type="text" class="form-control" id="title" name="title" required>
                </div>
                <div class="mb-3">
                    <label for="description" class="form-label">Description</label>
                    <textarea class="form-control" id="description" name="description" rows="3"></textarea>
                </div>
                <button type="submit" name="add_video" class="btn btn-primary">Add Video</button>
            </form>
        </div>
        
        <div class="video-list">
            <h3>Existing Videos</h3>
            <?php if (mysqli_num_rows($result) > 0): ?>
                <?php while ($row = mysqli_fetch_assoc($result)): ?>
                    <div class="video-item">
                        <h4><?php echo htmlspecialchars($row['title']); ?></h4>
                        <p><?php echo htmlspecialchars($row['description']); ?></p>
                        <p><small>Added on: <?php echo date('F j, Y', strtotime($row['created_at'])); ?></small></p>
                        <div class="d-flex justify-content-between align-items-center">
                            <a href="<?php echo htmlspecialchars($row['video_url']); ?>" target="_blank" class="btn btn-info btn-sm">View Video</a>
                            <a href="?delete=<?php echo $row['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this video?')">Delete</a>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <p>No videos added yet.</p>
            <?php endif; ?>
        </div>
    </div>
    
    <script src="../assets/js/bootstrap.bundle.min.js"></script>
</body>
</html> 