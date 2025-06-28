<?php
session_start();
include("../../db_conn.php");

// Check if admin is logged in
if (!isset($_SESSION['id']) || !isset($_SESSION['user_name'])) {
    header("Location: ../../admin/index.php");
    exit();
}

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'add':
                $name = trim($_POST['name']);
                $description = trim($_POST['description']);
                
                // Handle logo upload
                $logo = '';
                if (isset($_FILES['logo']) && $_FILES['logo']['error'] === 0) {
                    $allowed = ['jpg', 'jpeg', 'png', 'gif'];
                    $filename = $_FILES['logo']['name'];
                    $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
                    
                    if (in_array($ext, $allowed)) {
                        $new_filename = uniqid() . '.' . $ext;
                        $upload_path = '../../uploads/brands/' . $new_filename;
                        
                        if (!is_dir('../../uploads/brands/')) {
                            mkdir('../../uploads/brands/', 0777, true);
                        }
                        
                        if (move_uploaded_file($_FILES['logo']['tmp_name'], $upload_path)) {
                            $logo = $new_filename;
                        }
                    }
                }
                
                $sql = "INSERT INTO brands (name, description, logo) VALUES (?, ?, ?)";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("sss", $name, $description, $logo);
                $stmt->execute();
                break;
                
            case 'edit':
                $id = $_POST['id'];
                $name = trim($_POST['name']);
                $description = trim($_POST['description']);
                
                // Handle logo upload for edit
                if (isset($_FILES['logo']) && $_FILES['logo']['error'] === 0) {
                    $allowed = ['jpg', 'jpeg', 'png', 'gif'];
                    $filename = $_FILES['logo']['name'];
                    $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
                    
                    if (in_array($ext, $allowed)) {
                        $new_filename = uniqid() . '.' . $ext;
                        $upload_path = '../../uploads/brands/' . $new_filename;
                        
                        if (move_uploaded_file($_FILES['logo']['tmp_name'], $upload_path)) {
                            // Delete old logo if exists
                            $sql = "SELECT logo FROM brands WHERE id = ?";
                            $stmt = $conn->prepare($sql);
                            $stmt->bind_param("i", $id);
                            $stmt->execute();
                            $result = $stmt->get_result();
                            if ($row = $result->fetch_assoc()) {
                                if ($row['logo'] && file_exists('../../uploads/brands/' . $row['logo'])) {
                                    unlink('../../uploads/brands/' . $row['logo']);
                                }
                            }
                            
                            // Update with new logo
                            $sql = "UPDATE brands SET name = ?, description = ?, logo = ? WHERE id = ?";
                            $stmt = $conn->prepare($sql);
                            $stmt->bind_param("sssi", $name, $description, $new_filename, $id);
                        }
                    }
                } else {
                    // Update without changing logo
                    $sql = "UPDATE brands SET name = ?, description = ? WHERE id = ?";
                    $stmt = $conn->prepare($sql);
                    $stmt->bind_param("ssi", $name, $description, $id);
                }
                $stmt->execute();
                break;
                
            case 'delete':
                $id = $_POST['id'];
                
                // Delete logo file if exists
                $sql = "SELECT logo FROM brands WHERE id = ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("i", $id);
                $stmt->execute();
                $result = $stmt->get_result();
                if ($row = $result->fetch_assoc()) {
                    if ($row['logo'] && file_exists('../../uploads/brands/' . $row['logo'])) {
                        unlink('../../uploads/brands/' . $row['logo']);
                    }
                }
                
                // Delete brand
                $sql = "DELETE FROM brands WHERE id = ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("i", $id);
                $stmt->execute();
                break;
        }
        
        header("Location: manage_brands.php");
        exit();
    }
}

// Get all brands
$sql = "SELECT * FROM brands ORDER BY name";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Brands - Nati Automotive Admin</title>
    <!-- Boxicons -->
    <link href='https://unpkg.com/boxicons@2.0.9/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/boxicons@latest/css/boxicons.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../../admin/style1.css">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        .brand-logo {
            width: 50px;
            height: 50px;
            object-fit: contain;
        }
    </style>
</head>
<body>
    <?php include "../../admin/partial-fronts/header-sidebar.php"; ?>
    
    <div class="container mt-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>Manage Brands</h2>
            <button class="btn btn-primary" data-toggle="modal" data-target="#addBrandModal">
                <i class="fas fa-plus"></i> Add Brand
            </button>
        </div>

        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Logo</th>
                        <th>Name</th>
                        <th>Description</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td>
                            <?php if ($row['logo']): ?>
                                <img src="../../uploads/brands/<?php echo htmlspecialchars($row['logo']); ?>" 
                                     alt="<?php echo htmlspecialchars($row['name']); ?>" 
                                     class="brand-logo">
                            <?php else: ?>
                                <i class="fas fa-industry fa-2x text-secondary"></i>
                            <?php endif; ?>
                        </td>
                        <td><?php echo htmlspecialchars($row['name']); ?></td>
                        <td><?php echo htmlspecialchars($row['description']); ?></td>
                        <td>
                            <button class="btn btn-sm btn-info edit-brand" 
                                    data-id="<?php echo $row['id']; ?>"
                                    data-name="<?php echo htmlspecialchars($row['name']); ?>"
                                    data-description="<?php echo htmlspecialchars($row['description']); ?>"
                                    data-toggle="modal" 
                                    data-target="#editBrandModal">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button class="btn btn-sm btn-danger delete-brand"
                                    data-id="<?php echo $row['id']; ?>"
                                    data-name="<?php echo htmlspecialchars($row['name']); ?>">
                                <i class="fas fa-trash"></i>
                            </button>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Add Brand Modal -->
    <div class="modal fade" id="addBrandModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add New Brand</h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <form action="" method="POST" enctype="multipart/form-data">
                    <div class="modal-body">
                        <input type="hidden" name="action" value="add">
                        <div class="form-group">
                            <label>Brand Name</label>
                            <input type="text" class="form-control" name="name" required>
                        </div>
                        <div class="form-group">
                            <label>Description</label>
                            <textarea class="form-control" name="description" rows="3"></textarea>
                        </div>
                        <div class="form-group">
                            <label>Logo</label>
                            <input type="file" class="form-control-file" name="logo" accept="image/*">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Add Brand</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Brand Modal -->
    <div class="modal fade" id="editBrandModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Brand</h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <form action="" method="POST" enctype="multipart/form-data">
                    <div class="modal-body">
                        <input type="hidden" name="action" value="edit">
                        <input type="hidden" name="id" id="edit-brand-id">
                        <div class="form-group">
                            <label>Brand Name</label>
                            <input type="text" class="form-control" name="name" id="edit-brand-name" required>
                        </div>
                        <div class="form-group">
                            <label>Description</label>
                            <textarea class="form-control" name="description" id="edit-brand-description" rows="3"></textarea>
                        </div>
                        <div class="form-group">
                            <label>Logo</label>
                            <input type="file" class="form-control-file" name="logo" accept="image/*">
                            <small class="form-text text-muted">Leave empty to keep current logo</small>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Save Changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Delete Brand Modal -->
    <div class="modal fade" id="deleteBrandModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Delete Brand</h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <form action="" method="POST">
                    <div class="modal-body">
                        <input type="hidden" name="action" value="delete">
                        <input type="hidden" name="id" id="delete-brand-id">
                        <p>Are you sure you want to delete the brand "<span id="delete-brand-name"></span>"?</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-danger">Delete</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script>
        $(document).ready(function() {
            // Edit brand
            $('.edit-brand').click(function() {
                var id = $(this).data('id');
                var name = $(this).data('name');
                var description = $(this).data('description');
                
                $('#edit-brand-id').val(id);
                $('#edit-brand-name').val(name);
                $('#edit-brand-description').val(description);
            });
            
            // Delete brand
            $('.delete-brand').click(function() {
                var id = $(this).data('id');
                var name = $(this).data('name');
                
                $('#delete-brand-id').val(id);
                $('#delete-brand-name').text(name);
                $('#deleteBrandModal').modal('show');
            });
        });
    </script>
</body>
</html> 