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
                $icon = trim($_POST['icon']);
                $parent_id = !empty($_POST['parent_id']) ? $_POST['parent_id'] : null;
                
                $sql = "INSERT INTO categories (name, description, icon, parent_id) VALUES (?, ?, ?, ?)";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("sssi", $name, $description, $icon, $parent_id);
                $stmt->execute();
                break;
                
            case 'edit':
                $id = $_POST['id'];
                $name = trim($_POST['name']);
                $description = trim($_POST['description']);
                $icon = trim($_POST['icon']);
                $parent_id = !empty($_POST['parent_id']) ? $_POST['parent_id'] : null;
                
                $sql = "UPDATE categories SET name = ?, description = ?, icon = ?, parent_id = ? WHERE id = ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("sssii", $name, $description, $icon, $parent_id, $id);
                $stmt->execute();
                break;
                
            case 'delete':
                $id = $_POST['id'];
                
                // Update children categories to have no parent
                $sql = "UPDATE categories SET parent_id = NULL WHERE parent_id = ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("i", $id);
                $stmt->execute();
                
                // Delete category
                $sql = "DELETE FROM categories WHERE id = ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("i", $id);
                $stmt->execute();
                break;
        }
        
        header("Location: manage_categories.php");
        exit;
    }
}

// Get all categories
$sql = "SELECT c.*, p.name as parent_name 
        FROM categories c 
        LEFT JOIN categories p ON c.parent_id = p.id 
        ORDER BY c.name";
$result = $conn->query($sql);

// Get categories for parent selection
$sql = "SELECT id, name FROM categories ORDER BY name";
$parent_categories = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Categories - Nati Automotive Admin</title>
    <!-- Boxicons -->
    <link href='https://unpkg.com/boxicons@2.0.9/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/boxicons@latest/css/boxicons.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../../admin/style1.css">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
    <?php include "../../admin/partial-fronts/header-sidebar.php"; ?>
    
    <div class="container mt-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>Manage Categories</h2>
            <button class="btn btn-primary" data-toggle="modal" data-target="#addCategoryModal">
                <i class="fas fa-plus"></i> Add Category
            </button>
        </div>

        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Icon</th>
                        <th>Name</th>
                        <th>Description</th>
                        <th>Parent Category</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><i class="<?php echo htmlspecialchars($row['icon']); ?>"></i></td>
                        <td><?php echo htmlspecialchars($row['name']); ?></td>
                        <td><?php echo htmlspecialchars($row['description']); ?></td>
                        <td><?php echo $row['parent_name'] ? htmlspecialchars($row['parent_name']) : '-'; ?></td>
                        <td>
                            <button class="btn btn-sm btn-info edit-category" 
                                    data-id="<?php echo $row['id']; ?>"
                                    data-name="<?php echo htmlspecialchars($row['name']); ?>"
                                    data-description="<?php echo htmlspecialchars($row['description']); ?>"
                                    data-icon="<?php echo htmlspecialchars($row['icon']); ?>"
                                    data-parent="<?php echo $row['parent_id']; ?>"
                                    data-toggle="modal" 
                                    data-target="#editCategoryModal">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button class="btn btn-sm btn-danger delete-category"
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

    <!-- Add Category Modal -->
    <div class="modal fade" id="addCategoryModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add New Category</h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <form action="" method="POST">
                    <div class="modal-body">
                        <input type="hidden" name="action" value="add">
                        <div class="form-group">
                            <label>Category Name</label>
                            <input type="text" class="form-control" name="name" required>
                        </div>
                        <div class="form-group">
                            <label>Description</label>
                            <textarea class="form-control" name="description" rows="3"></textarea>
                        </div>
                        <div class="form-group">
                            <label>Icon (Font Awesome class)</label>
                            <input type="text" class="form-control" name="icon" placeholder="fas fa-car">
                        </div>
                        <div class="form-group">
                            <label>Parent Category</label>
                            <select class="form-control" name="parent_id">
                                <option value="">None</option>
                                <?php 
                                $parent_categories->data_seek(0);
                                while ($cat = $parent_categories->fetch_assoc()): 
                                ?>
                                <option value="<?php echo $cat['id']; ?>">
                                    <?php echo htmlspecialchars($cat['name']); ?>
                                </option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Add Category</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Category Modal -->
    <div class="modal fade" id="editCategoryModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Category</h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <form action="" method="POST">
                    <div class="modal-body">
                        <input type="hidden" name="action" value="edit">
                        <input type="hidden" name="id" id="edit-category-id">
                        <div class="form-group">
                            <label>Category Name</label>
                            <input type="text" class="form-control" name="name" id="edit-category-name" required>
                        </div>
                        <div class="form-group">
                            <label>Description</label>
                            <textarea class="form-control" name="description" id="edit-category-description" rows="3"></textarea>
                        </div>
                        <div class="form-group">
                            <label>Icon (Font Awesome class)</label>
                            <input type="text" class="form-control" name="icon" id="edit-category-icon" placeholder="fas fa-car">
                        </div>
                        <div class="form-group">
                            <label>Parent Category</label>
                            <select class="form-control" name="parent_id" id="edit-category-parent">
                                <option value="">None</option>
                                <?php 
                                $parent_categories->data_seek(0);
                                while ($cat = $parent_categories->fetch_assoc()): 
                                ?>
                                <option value="<?php echo $cat['id']; ?>">
                                    <?php echo htmlspecialchars($cat['name']); ?>
                                </option>
                                <?php endwhile; ?>
                            </select>
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

    <!-- Delete Category Modal -->
    <div class="modal fade" id="deleteCategoryModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Delete Category</h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <form action="" method="POST">
                    <div class="modal-body">
                        <input type="hidden" name="action" value="delete">
                        <input type="hidden" name="id" id="delete-category-id">
                        <p>Are you sure you want to delete the category "<span id="delete-category-name"></span>"?</p>
                        <p class="text-warning">Note: Any subcategories will become top-level categories.</p>
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
            // Edit category
            $('.edit-category').click(function() {
                var id = $(this).data('id');
                var name = $(this).data('name');
                var description = $(this).data('description');
                var icon = $(this).data('icon');
                var parent = $(this).data('parent');
                
                $('#edit-category-id').val(id);
                $('#edit-category-name').val(name);
                $('#edit-category-description').val(description);
                $('#edit-category-icon').val(icon);
                $('#edit-category-parent').val(parent);
            });
            
            // Delete category
            $('.delete-category').click(function() {
                var id = $(this).data('id');
                var name = $(this).data('name');
                
                $('#delete-category-id').val(id);
                $('#delete-category-name').text(name);
                $('#deleteCategoryModal').modal('show');
            });
        });
    </script>
</body>
</html> 