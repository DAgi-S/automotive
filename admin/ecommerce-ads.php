<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

require_once '../includes/config.php';

try {
    $stmt = $conn->prepare("SELECT * FROM tbl_ads ORDER BY created_at DESC");
    $stmt->execute();
    $ads = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    error_log("Error fetching ads: " . $e->getMessage());
    $error = "An error occurred while fetching ads.";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ads Management - Admin Dashboard</title>
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <style>
        .ad-image-preview {
            max-width: 100px;
            max-height: 100px;
            object-fit: cover;
        }
    </style>
</head>
<body class="bg-light">
    <?php include 'includes/header.php'; ?>
    
    <div class="container-fluid py-4">
        <div class="row">
            <div class="col-12">
                <div class="card mb-4">
                    <div class="card-header pb-0 d-flex justify-content-between align-items-center">
                        <h6>Ads Management</h6>
                        <button type="button" class="btn btn-primary btn-sm" onclick="openAddAdModal()">
                            <i class="fas fa-plus"></i> Add New Ad
                        </button>
                    </div>
                    <div class="card-body px-0 pt-0 pb-2">
                        <?php if (isset($error)): ?>
                            <div class="alert alert-danger" role="alert">
                                <?php echo htmlspecialchars($error); ?>
                            </div>
                        <?php endif; ?>
                        
                        <div class="table-responsive p-0">
                            <table id="adsTable" class="table align-items-center mb-0">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Image</th>
                                        <th>Title</th>
                                        <th>Position</th>
                                        <th>Duration</th>
                                        <th>Status</th>
                                        <th>Clicks</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($ads as $ad): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($ad['id']); ?></td>
                                        <td>
                                            <?php if ($ad['image_name']): ?>
                                                <img src="../uploads/ads/<?php echo htmlspecialchars($ad['image_name']); ?>" 
                                                     alt="Ad" class="ad-image-preview">
                                            <?php else: ?>
                                                <span class="text-muted">No image</span>
                                            <?php endif; ?>
                                        </td>
                                        <td><?php echo htmlspecialchars($ad['title']); ?></td>
                                        <td><?php echo htmlspecialchars($ad['position']); ?></td>
                                        <td>
                                            <?php 
                                            echo date('M d, Y', strtotime($ad['start_date'])) . ' - ' . 
                                                 date('M d, Y', strtotime($ad['end_date'])); 
                                            ?>
                                        </td>
                                        <td>
                                            <span class="badge bg-<?php 
                                                echo $ad['status'] === 'active' ? 'success' : 
                                                    ($ad['status'] === 'scheduled' ? 'info' : 'secondary'); 
                                            ?>">
                                                <?php echo htmlspecialchars($ad['status']); ?>
                                            </span>
                                        </td>
                                        <td><?php echo htmlspecialchars($ad['click_count']); ?></td>
                                        <td>
                                            <button type="button" class="btn btn-info btn-sm" 
                                                    onclick="editAd(<?php echo $ad['id']; ?>)">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button type="button" class="btn btn-danger btn-sm" 
                                                    onclick="deleteAd(<?php echo $ad['id']; ?>)">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Add/Edit Ad Modal -->
    <div class="modal fade" id="adModal" tabindex="-1" aria-labelledby="adModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="adModalLabel">Add New Ad</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="adForm" enctype="multipart/form-data">
                    <div class="modal-body">
                        <input type="hidden" id="adId" name="id">
                        
                        <div class="mb-3">
                            <label for="title" class="form-label">Title</label>
                            <input type="text" class="form-control" id="title" name="title" required>
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control" id="description" name="description" rows="3"></textarea>
                        </div>

                        <div class="mb-3">
                            <label for="position" class="form-label">Position</label>
                            <select class="form-control" id="position" name="position" required>
                                <option value="home_top">Home Page Top</option>
                                <option value="home_middle">Home Page Middle</option>
                                <option value="home_bottom">Home Page Bottom</option>
                                <option value="sidebar">Sidebar</option>
                                <option value="service_page">Service Page</option>
                            </select>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="start_date" class="form-label">Start Date</label>
                                    <input type="text" class="form-control datepicker" id="start_date" name="start_date" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="end_date" class="form-label">End Date</label>
                                    <input type="text" class="form-control datepicker" id="end_date" name="end_date" required>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="target_url" class="form-label">Target URL</label>
                            <input type="url" class="form-control" id="target_url" name="target_url">
                        </div>

                        <div class="mb-3">
                            <label for="status" class="form-label">Status</label>
                            <select class="form-control" id="status" name="status">
                                <option value="active">Active</option>
                                <option value="inactive">Inactive</option>
                                <option value="scheduled">Scheduled</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="image" class="form-label">Ad Image</label>
                            <input type="file" class="form-control" id="image" name="image" accept="image/*">
                            <div id="imagePreview" class="mt-2"></div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Save Ad</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Include JavaScript files -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    
    <script>
        $(document).ready(function() {
            // Initialize DataTable
            $('#adsTable').DataTable({
                order: [[0, 'desc']],
                pageLength: 10,
                language: {
                    search: "_INPUT_",
                    searchPlaceholder: "Search ads..."
                }
            });

            // Initialize date pickers
            flatpickr(".datepicker", {
                dateFormat: "Y-m-d",
                minDate: "today"
            });

            // Handle form submission
            $('#adForm').on('submit', function(e) {
                e.preventDefault();
                const formData = new FormData(this);
                
                $.ajax({
                    url: 'ajax/save_ad.php',
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        if (response.success) {
                            location.reload();
                        } else {
                            alert('Error saving ad: ' + response.message);
                        }
                    },
                    error: function() {
                        alert('Error saving ad. Please try again.');
                    }
                });
            });

            // Handle image preview
            $('#image').on('change', function() {
                const file = this.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        $('#imagePreview').html(`<img src="${e.target.result}" class="ad-image-preview">`);
                    };
                    reader.readAsDataURL(file);
                }
            });
        });

        function openAddAdModal() {
            $('#adModalLabel').text('Add New Ad');
            $('#adForm')[0].reset();
            $('#adId').val('');
            $('#imagePreview').empty();
            $('#adModal').modal('show');
        }

        function editAd(adId) {
            $('#adModalLabel').text('Edit Ad');
            
            // Fetch ad details
            $.ajax({
                url: 'ajax/get_ad_details.php',
                type: 'GET',
                data: { id: adId },
                success: function(response) {
                    if (response.success) {
                        const ad = response.data;
                        
                        $('#adId').val(ad.id);
                        $('#title').val(ad.title);
                        $('#description').val(ad.description);
                        $('#position').val(ad.position);
                        $('#start_date').val(ad.start_date);
                        $('#end_date').val(ad.end_date);
                        $('#target_url').val(ad.target_url);
                        $('#status').val(ad.status);
                        
                        if (ad.image_name) {
                            $('#imagePreview').html(`<img src="../uploads/ads/${ad.image_name}" class="ad-image-preview">`);
                        } else {
                            $('#imagePreview').empty();
                        }
                        
                        $('#adModal').modal('show');
                    } else {
                        alert('Error loading ad details: ' + response.message);
                    }
                },
                error: function() {
                    alert('Error loading ad details. Please try again.');
                }
            });
        }

        function deleteAd(adId) {
            if (confirm('Are you sure you want to delete this ad?')) {
                $.ajax({
                    url: 'ajax/delete_ad.php',
                    type: 'POST',
                    data: { id: adId },
                    success: function(response) {
                        if (response.success) {
                            location.reload();
                        } else {
                            alert('Error deleting ad: ' + response.message);
                        }
                    },
                    error: function() {
                        alert('Error deleting ad. Please try again.');
                    }
                });
            }
        }
    </script>
</body>
</html> 