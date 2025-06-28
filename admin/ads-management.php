<?php
session_start();

// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

// Include database connection
require_once('../config/database.php');

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'add_ad':
                try {
                    $image_name = null;
                    
                    // Handle image upload
                    if (isset($_FILES['ad_image']) && $_FILES['ad_image']['error'] === UPLOAD_ERR_OK) {
                        $upload_dir = '../uploads/ads/';
                        if (!file_exists($upload_dir)) {
                            mkdir($upload_dir, 0777, true);
                        }
                        
                        $file_extension = pathinfo($_FILES['ad_image']['name'], PATHINFO_EXTENSION);
                        $image_name = 'ad_' . uniqid() . '.' . $file_extension;
                        $upload_path = $upload_dir . $image_name;
                        
                        if (!move_uploaded_file($_FILES['ad_image']['tmp_name'], $upload_path)) {
                            throw new Exception("Failed to upload image");
                        }
                    }
                    
                    $stmt = $conn->prepare("INSERT INTO tbl_ads (title, description, position, start_date, end_date, target_url, image_name, status, priority, budget, max_impressions) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
                    $stmt->execute([
                        $_POST['title'],
                        $_POST['description'],
                        $_POST['position'],
                        $_POST['start_date'],
                        $_POST['end_date'],
                        $_POST['target_url'],
                        $image_name,
                        $_POST['status'],
                        $_POST['priority'] ?? 1,
                        $_POST['budget'] ?? 0,
                        $_POST['max_impressions'] ?? 0
                    ]);
                    $_SESSION['success'] = "Ad created successfully!";
                } catch (Exception $e) {
                    $_SESSION['error'] = "Failed to create ad: " . $e->getMessage();
                }
                break;

            case 'update_ad':
                try {
                    $image_name = $_POST['current_image'];
                    
                    // Handle new image upload
                    if (isset($_FILES['ad_image']) && $_FILES['ad_image']['error'] === UPLOAD_ERR_OK) {
                        $upload_dir = '../uploads/ads/';
                        if (!file_exists($upload_dir)) {
                            mkdir($upload_dir, 0777, true);
                        }
                        
                        // Delete old image if exists
                        if ($image_name && file_exists($upload_dir . $image_name)) {
                            unlink($upload_dir . $image_name);
                        }
                        
                        $file_extension = pathinfo($_FILES['ad_image']['name'], PATHINFO_EXTENSION);
                        $image_name = 'ad_' . uniqid() . '.' . $file_extension;
                        $upload_path = $upload_dir . $image_name;
                        
                        if (!move_uploaded_file($_FILES['ad_image']['tmp_name'], $upload_path)) {
                            throw new Exception("Failed to upload image");
                        }
                    }
                    
                    $stmt = $conn->prepare("UPDATE tbl_ads SET title = ?, description = ?, position = ?, start_date = ?, end_date = ?, target_url = ?, image_name = ?, status = ?, priority = ?, budget = ?, max_impressions = ? WHERE id = ?");
                    $stmt->execute([
                        $_POST['title'],
                        $_POST['description'],
                        $_POST['position'],
                        $_POST['start_date'],
                        $_POST['end_date'],
                        $_POST['target_url'],
                        $image_name,
                        $_POST['status'],
                        $_POST['priority'] ?? 1,
                        $_POST['budget'] ?? 0,
                        $_POST['max_impressions'] ?? 0,
                        $_POST['ad_id']
                    ]);
                    $_SESSION['success'] = "Ad updated successfully!";
                } catch (Exception $e) {
                    $_SESSION['error'] = "Failed to update ad: " . $e->getMessage();
                }
                break;

            case 'delete_ad':
                try {
                    // Get image name before deletion
                    $stmt = $conn->prepare("SELECT image_name FROM tbl_ads WHERE id = ?");
                    $stmt->execute([$_POST['ad_id']]);
                    $ad = $stmt->fetch(PDO::FETCH_ASSOC);
                    
                    // Delete the ad
                    $stmt = $conn->prepare("DELETE FROM tbl_ads WHERE id = ?");
                    $stmt->execute([$_POST['ad_id']]);
                    
                    // Delete image file if exists
                    if ($ad && $ad['image_name']) {
                        $image_path = '../uploads/ads/' . $ad['image_name'];
                        if (file_exists($image_path)) {
                            unlink($image_path);
                        }
                    }
                    
                    $_SESSION['success'] = "Ad deleted successfully!";
                } catch (PDOException $e) {
                    $_SESSION['error'] = "Failed to delete ad: " . $e->getMessage();
                }
                break;

            case 'toggle_status':
                try {
                    $stmt = $conn->prepare("UPDATE tbl_ads SET status = CASE WHEN status = 'active' THEN 'inactive' ELSE 'active' END WHERE id = ?");
                    $stmt->execute([$_POST['ad_id']]);
                    $_SESSION['success'] = "Ad status updated successfully!";
                } catch (PDOException $e) {
                    $_SESSION['error'] = "Failed to update ad status: " . $e->getMessage();
                }
                break;
        }
        header("Location: ads-management.php");
        exit();
    }
}

// Include header
require_once('includes/header.php');

// Fetch ads data with analytics
try {
    $stmt = $conn->query("
        SELECT *, 
               DATEDIFF(end_date, CURDATE()) as days_remaining,
               CASE 
                   WHEN start_date > CURDATE() THEN 'scheduled'
                   WHEN end_date < CURDATE() THEN 'expired'
                   ELSE status
               END as computed_status
        FROM tbl_ads 
        ORDER BY created_at DESC
    ");
    $ads = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Get analytics summary
    $stmt = $conn->query("
        SELECT 
            COUNT(*) as total_ads,
            SUM(CASE WHEN status = 'active' AND start_date <= CURDATE() AND end_date >= CURDATE() THEN 1 ELSE 0 END) as active_ads,
            SUM(CASE WHEN start_date > CURDATE() THEN 1 ELSE 0 END) as scheduled_ads,
            SUM(CASE WHEN end_date < CURDATE() THEN 1 ELSE 0 END) as expired_ads,
            SUM(click_count) as total_clicks,
            SUM(impression_count) as total_impressions
        FROM tbl_ads
    ");
    $analytics = $stmt->fetch(PDO::FETCH_ASSOC);
    
} catch (PDOException $e) {
    $_SESSION['error'] = "Error fetching ads data: " . $e->getMessage();
    $ads = [];
    $analytics = ['total_ads' => 0, 'active_ads' => 0, 'scheduled_ads' => 0, 'expired_ads' => 0, 'total_clicks' => 0, 'total_impressions' => 0];
}

// Available ad positions
$ad_positions = [
    'home_top' => 'Home Page - Top Banner',
    'home_middle' => 'Home Page - Middle Section',
    'home_bottom' => 'Home Page - Bottom Banner',
    'sidebar' => 'Sidebar Advertisement',
    'service_page' => 'Service Page Banner',
    'products_top' => 'Products Page - Top',
    'products_grid' => 'Products Page - Grid',
    'products_bottom' => 'Products Page - Bottom',
    'location_top' => 'Location Page - Top',
    'location_bottom' => 'Location Page - Bottom',
    'small_banner' => 'Small Banner',
    'mini_square' => 'Mini Square Ad'
];
?>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

<style>
.analytics-card {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border-radius: 0.5rem;
    padding: 1.5rem;
    margin-bottom: 1rem;
    transition: transform 0.3s ease;
}

.analytics-card:hover {
    transform: translateY(-3px);
}

.analytics-card .icon {
    font-size: 2rem;
    opacity: 0.8;
}

.ad-image-preview {
    max-width: 80px;
    max-height: 60px;
    object-fit: cover;
    border-radius: 0.25rem;
}

.status-badge {
    font-size: 0.75rem;
    padding: 0.25rem 0.5rem;
    border-radius: 0.25rem;
}

.btn-group-sm .btn {
    font-size: 0.75rem;
    padding: 0.25rem 0.5rem;
}

.ad-upload-area {
    border: 2px dashed #dee2e6;
    border-radius: 0.5rem;
    padding: 2rem;
    text-align: center;
    transition: all 0.3s ease;
    background: #f8f9fa;
    cursor: pointer;
}

.ad-upload-area:hover {
    border-color: #667eea;
    background: #f0f2ff;
}

.ad-upload-area.dragover {
    border-color: #667eea;
    background: #e3f2fd;
    transform: scale(1.02);
}

.table-responsive {
    border-radius: 0.5rem;
    overflow: hidden;
}

.table th {
    background-color: #f8f9fc;
    border: none;
    font-weight: 600;
    color: #5a5c69;
}

.btn-primary {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border: none;
}

.btn-primary:hover {
    background: linear-gradient(135deg, #5a6fd8 0%, #6a4190 100%);
    transform: translateY(-1px);
}
</style>

<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Advertisement Management</h1>
        <div class="btn-group" role="group">
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addAdModal">
                <i class="fas fa-plus"></i> Create New Ad
            </button>
            <button class="btn btn-outline-primary" onclick="window.open('../index.php', '_blank')">
                <i class="fas fa-eye"></i> Preview Website
            </button>
        </div>
    </div>

    <?php if (isset($_SESSION['success'])): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?php 
            echo $_SESSION['success'];
            unset($_SESSION['success']);
            ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?php 
            echo $_SESSION['error'];
            unset($_SESSION['error']);
            ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <!-- Analytics Cards -->
    <div class="row mb-4">
        <div class="col-xl-2 col-md-4 col-6 mb-4">
            <div class="analytics-card">
                <div class="d-flex align-items-center">
                    <div class="icon me-3">
                        <i class="fas fa-ad"></i>
                    </div>
                    <div>
                        <div class="h3 mb-0"><?php echo $analytics['total_ads']; ?></div>
                        <div class="small">Total Ads</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-2 col-md-4 col-6 mb-4">
            <div class="analytics-card" style="background: linear-gradient(135deg, #28a745 0%, #20c997 100%);">
                <div class="d-flex align-items-center">
                    <div class="icon me-3">
                        <i class="fas fa-play-circle"></i>
                    </div>
                    <div>
                        <div class="h3 mb-0"><?php echo $analytics['active_ads']; ?></div>
                        <div class="small">Active Ads</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-2 col-md-4 col-6 mb-4">
            <div class="analytics-card" style="background: linear-gradient(135deg, #17a2b8 0%, #6f42c1 100%);">
                <div class="d-flex align-items-center">
                    <div class="icon me-3">
                        <i class="fas fa-clock"></i>
                    </div>
                    <div>
                        <div class="h3 mb-0"><?php echo $analytics['scheduled_ads']; ?></div>
                        <div class="small">Scheduled</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-2 col-md-4 col-6 mb-4">
            <div class="analytics-card" style="background: linear-gradient(135deg, #ffc107 0%, #fd7e14 100%);">
                <div class="d-flex align-items-center">
                    <div class="icon me-3">
                        <i class="fas fa-hourglass-end"></i>
                    </div>
                    <div>
                        <div class="h3 mb-0"><?php echo $analytics['expired_ads']; ?></div>
                        <div class="small">Expired</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-2 col-md-4 col-6 mb-4">
            <div class="analytics-card" style="background: linear-gradient(135deg, #dc3545 0%, #e83e8c 100%);">
                <div class="d-flex align-items-center">
                    <div class="icon me-3">
                        <i class="fas fa-mouse-pointer"></i>
                    </div>
                    <div>
                        <div class="h3 mb-0"><?php echo number_format($analytics['total_clicks']); ?></div>
                        <div class="small">Total Clicks</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-2 col-md-4 col-6 mb-4">
            <div class="analytics-card" style="background: linear-gradient(135deg, #6c757d 0%, #495057 100%);">
                <div class="d-flex align-items-center">
                    <div class="icon me-3">
                        <i class="fas fa-eye"></i>
                    </div>
                    <div>
                        <div class="h3 mb-0"><?php echo number_format($analytics['total_impressions']); ?></div>
                        <div class="small">Impressions</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Ads Table -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">All Advertisements</h6>
            <div class="dropdown">
                <button class="btn btn-outline-primary btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown">
                    <i class="fas fa-filter"></i> Filter
                </button>
                <ul class="dropdown-menu">
                    <li><a class="dropdown-item" href="#" onclick="filterAds('all')">All Ads</a></li>
                    <li><a class="dropdown-item" href="#" onclick="filterAds('active')">Active</a></li>
                    <li><a class="dropdown-item" href="#" onclick="filterAds('scheduled')">Scheduled</a></li>
                    <li><a class="dropdown-item" href="#" onclick="filterAds('expired')">Expired</a></li>
                    <li><a class="dropdown-item" href="#" onclick="filterAds('inactive')">Inactive</a></li>
                </ul>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="adsTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>Preview</th>
                            <th>Title</th>
                            <th>Position</th>
                            <th>Duration</th>
                            <th>Status</th>
                            <th>Performance</th>
                            <th>Priority</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($ads as $ad): ?>
                        <tr data-status="<?php echo $ad['computed_status']; ?>">
                            <td>
                                <?php if ($ad['image_name']): ?>
                                    <img src="../uploads/ads/<?php echo htmlspecialchars($ad['image_name']); ?>" 
                                         alt="Ad Preview" class="ad-image-preview">
                                <?php else: ?>
                                    <div class="text-center p-2 bg-light rounded">
                                        <i class="fas fa-image text-muted"></i>
                                        <br><small class="text-muted">No Image</small>
                                    </div>
                                <?php endif; ?>
                            </td>
                            <td>
                                <div class="font-weight-bold"><?php echo htmlspecialchars($ad['title']); ?></div>
                                <?php if ($ad['description']): ?>
                                    <small class="text-muted"><?php echo htmlspecialchars(substr($ad['description'], 0, 50)) . '...'; ?></small>
                                <?php endif; ?>
                            </td>
                            <td>
                                <span class="badge bg-info"><?php echo htmlspecialchars($ad_positions[$ad['position']] ?? $ad['position']); ?></span>
                            </td>
                            <td>
                                <div class="small">
                                    <strong>Start:</strong> <?php echo date('M d, Y', strtotime($ad['start_date'])); ?><br>
                                    <strong>End:</strong> <?php echo date('M d, Y', strtotime($ad['end_date'])); ?>
                                    <?php if ($ad['days_remaining'] > 0): ?>
                                        <br><span class="text-success"><?php echo $ad['days_remaining']; ?> days left</span>
                                    <?php elseif ($ad['days_remaining'] < 0): ?>
                                        <br><span class="text-danger">Expired</span>
                                    <?php endif; ?>
                                </div>
                            </td>
                            <td>
                                <?php
                                $status_class = '';
                                $status_text = '';
                                switch ($ad['computed_status']) {
                                    case 'active':
                                        $status_class = 'bg-success';
                                        $status_text = 'Active';
                                        break;
                                    case 'inactive':
                                        $status_class = 'bg-secondary';
                                        $status_text = 'Inactive';
                                        break;
                                    case 'scheduled':
                                        $status_class = 'bg-info';
                                        $status_text = 'Scheduled';
                                        break;
                                    case 'expired':
                                        $status_class = 'bg-danger';
                                        $status_text = 'Expired';
                                        break;
                                }
                                ?>
                                <span class="badge <?php echo $status_class; ?> status-badge"><?php echo $status_text; ?></span>
                            </td>
                            <td>
                                <div class="small">
                                    <strong>Clicks:</strong> <?php echo number_format($ad['click_count']); ?><br>
                                    <strong>Views:</strong> <?php echo number_format($ad['impression_count']); ?>
                                    <?php if ($ad['impression_count'] > 0): ?>
                                        <br><strong>CTR:</strong> <?php echo number_format(($ad['click_count'] / $ad['impression_count']) * 100, 2); ?>%
                                    <?php endif; ?>
                                </div>
                            </td>
                            <td>
                                <span class="badge bg-warning"><?php echo $ad['priority']; ?></span>
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm" role="group">
                                    <button class="btn btn-outline-primary edit-ad" 
                                            data-bs-toggle="modal" 
                                            data-bs-target="#editAdModal"
                                            data-id="<?php echo $ad['id']; ?>"
                                            title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <form method="POST" style="display: inline;">
                                        <input type="hidden" name="action" value="toggle_status">
                                        <input type="hidden" name="ad_id" value="<?php echo $ad['id']; ?>">
                                        <button type="submit" class="btn btn-outline-<?php echo $ad['status'] === 'active' ? 'warning' : 'success'; ?>" 
                                                title="<?php echo $ad['status'] === 'active' ? 'Deactivate' : 'Activate'; ?>">
                                            <i class="fas fa-<?php echo $ad['status'] === 'active' ? 'pause' : 'play'; ?>"></i>
                                        </button>
                                    </form>
                                    <button class="btn btn-outline-danger delete-ad" 
                                            data-id="<?php echo $ad['id']; ?>"
                                            data-title="<?php echo htmlspecialchars($ad['title']); ?>"
                                            title="Delete">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Include Modals -->
<?php include 'components/ads-modals.php'; ?>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Handle edit buttons
    document.querySelectorAll('.edit-ad').forEach(button => {
        button.addEventListener('click', function() {
            const adId = this.dataset.id;
            loadAdForEdit(adId);
        });
    });

    // Handle delete buttons
    document.querySelectorAll('.delete-ad').forEach(button => {
        button.addEventListener('click', function() {
            const adId = this.dataset.id;
            const adTitle = this.dataset.title;
            
            if (confirm(`Are you sure you want to delete the ad "${adTitle}"? This action cannot be undone.`)) {
                deleteAd(adId);
            }
        });
    });

    // Auto-dismiss alerts
    document.querySelectorAll('.alert').forEach(alert => {
        setTimeout(() => {
            const closeButton = alert.querySelector('.btn-close');
            if (closeButton) {
                closeButton.click();
            }
        }, 5000);
    });
});

function loadAdForEdit(adId) {
    // Fetch ad data and populate edit modal
    fetch(`ajax/get_ad_details.php?id=${adId}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const ad = data.ad;
                document.getElementById('edit_ad_id').value = ad.id;
                document.getElementById('edit_title').value = ad.title;
                document.getElementById('edit_description').value = ad.description;
                document.getElementById('edit_position').value = ad.position;
                document.getElementById('edit_start_date').value = ad.start_date;
                document.getElementById('edit_end_date').value = ad.end_date;
                document.getElementById('edit_target_url').value = ad.target_url;
                document.getElementById('edit_status').value = ad.status;
                document.getElementById('edit_priority').value = ad.priority;
                document.getElementById('edit_budget').value = ad.budget;
                document.getElementById('edit_max_impressions').value = ad.max_impressions;
                document.getElementById('edit_current_image').value = ad.image_name;
                
                // Show current image preview if exists
                const previewDiv = document.getElementById('edit_image_preview');
                if (ad.image_name) {
                    previewDiv.innerHTML = `<img src="../uploads/ads/${ad.image_name}" class="ad-image-preview mb-2">`;
                } else {
                    previewDiv.innerHTML = '';
                }
            } else {
                alert('Error loading ad details: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error loading ad details');
        });
}

function deleteAd(adId) {
    const form = document.createElement('form');
    form.method = 'POST';
    form.innerHTML = `
        <input type="hidden" name="action" value="delete_ad">
        <input type="hidden" name="ad_id" value="${adId}">
    `;
    document.body.appendChild(form);
    form.submit();
}

function filterAds(status) {
    const rows = document.querySelectorAll('#adsTable tbody tr');
    rows.forEach(row => {
        if (status === 'all' || row.dataset.status === status) {
            row.style.display = '';
        } else {
            row.style.display = 'none';
        }
    });
}
</script>

<?php require_once('includes/footer.php'); ?> 