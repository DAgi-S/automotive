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
            case 'add':
                try {
                    $stmt = $conn->prepare("INSERT INTO categories (name, description, icon, parent_id, created_at) VALUES (?, ?, ?, ?, NOW())");
                    $stmt->execute([
                        $_POST['name'],
                        $_POST['description'],
                        $_POST['icon'],
                        $_POST['parent_id'] ? $_POST['parent_id'] : null
                    ]);
                    $_SESSION['success'] = "Category added successfully!";
                } catch (PDOException $e) {
                    $_SESSION['error'] = "Error adding category: " . $e->getMessage();
                }
                break;

            case 'update':
                try {
                    $stmt = $conn->prepare("UPDATE categories SET name = ?, description = ?, icon = ?, parent_id = ? WHERE id = ?");
                    $stmt->execute([
                        $_POST['name'],
                        $_POST['description'],
                        $_POST['icon'],
                        $_POST['parent_id'] ? $_POST['parent_id'] : null,
                        $_POST['category_id']
                    ]);
                    $_SESSION['success'] = "Category updated successfully!";
                } catch (PDOException $e) {
                    $_SESSION['error'] = "Error updating category: " . $e->getMessage();
                }
                break;

            case 'delete':
                try {
                    // First check if there are any products using this category
                    $stmt = $conn->prepare("SELECT COUNT(*) FROM tbl_products WHERE category_id = ?");
                    $stmt->execute([$_POST['category_id']]);
                    $count = $stmt->fetchColumn();

                    if ($count > 0) {
                        $_SESSION['error'] = "Cannot delete category: There are products associated with this category.";
                    } else {
                        $stmt = $conn->prepare("DELETE FROM categories WHERE id = ?");
                        $stmt->execute([$_POST['category_id']]);
                        $_SESSION['success'] = "Category deleted successfully!";
                    }
                } catch (PDOException $e) {
                    $_SESSION['error'] = "Error deleting category: " . $e->getMessage();
                }
                break;
        }
        header("Location: categories.php");
        exit();
    }
}

// Fetch all categories
try {
    $stmt = $conn->query("
        SELECT *
        FROM categories
        ORDER BY name ASC
    ");
    $categories = $stmt->fetchAll();
} catch (PDOException $e) {
    $_SESSION['error'] = "Error fetching categories: " . $e->getMessage();
    $categories = [];
}

// Include header
require_once('includes/header.php');
?>

<!-- Begin Page Content -->
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Categories Management</h1>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addCategoryModal">
            <i class="fas fa-plus"></i> Add New Category
        </button>
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

    <!-- Categories Table -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">All Categories</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="categoriesTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Description</th>
                            <th>Icon</th>
                            <th>Parent Category</th>
                            <th>Created At</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($categories as $category): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($category['id']); ?></td>
                                <td><?php echo htmlspecialchars($category['name']); ?></td>
                                <td><?php echo htmlspecialchars($category['description']); ?></td>
                                <td><i class="<?php echo htmlspecialchars($category['icon']); ?>"></i></td>
                                <td>
                                    <?php 
                                    if ($category['parent_id']) {
                                        $parent = $conn->query("SELECT name FROM categories WHERE id = " . $category['parent_id'])->fetch();
                                        echo htmlspecialchars($parent['name']);
                                    } else {
                                        echo "None";
                                    }
                                    ?>
                                </td>
                                <td><?php echo htmlspecialchars($category['created_at']); ?></td>
                                <td>
                                    <button class="btn btn-sm btn-info edit-category"
                                            data-id="<?php echo $category['id']; ?>"
                                            data-name="<?php echo htmlspecialchars($category['name']); ?>"
                                            data-description="<?php echo htmlspecialchars($category['description']); ?>"
                                            data-icon="<?php echo htmlspecialchars($category['icon']); ?>"
                                            data-parent-id="<?php echo $category['parent_id']; ?>"
                                            data-bs-toggle="modal"
                                            data-bs-target="#editCategoryModal">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button class="btn btn-sm btn-danger delete-category"
                                            data-id="<?php echo $category['id']; ?>"
                                            data-name="<?php echo htmlspecialchars($category['name']); ?>"
                                            data-bs-toggle="modal"
                                            data-bs-target="#deleteCategoryModal">
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

<!-- Add Category Modal -->
<div class="modal fade" id="addCategoryModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add New Category</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="categories.php" method="POST">
                <div class="modal-body">
                    <input type="hidden" name="action" value="add">
                    <div class="mb-3">
                        <label for="name" class="form-label">Category Name</label>
                        <input type="text" class="form-control" id="name" name="name" required>
                    </div>
                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea class="form-control" id="description" name="description" rows="3"></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="icon" class="form-label">Icon Class (FontAwesome)</label>
                        <input type="text" class="form-control" id="icon" name="icon" placeholder="fas fa-wrench">
                        <small class="form-text text-muted">Enter FontAwesome icon class (e.g., fas fa-wrench)</small>
                    </div>
                    <div class="mb-3">
                        <label for="parent_id" class="form-label">Parent Category</label>
                        <select class="form-control" id="parent_id" name="parent_id">
                            <option value="">None</option>
                            <?php foreach ($categories as $cat): ?>
                                <option value="<?php echo $cat['id']; ?>"><?php echo htmlspecialchars($cat['name']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
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
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="categories.php" method="POST">
                <div class="modal-body">
                    <input type="hidden" name="action" value="update">
                    <input type="hidden" name="category_id" id="edit_category_id">
                    <div class="mb-3">
                        <label for="edit_name" class="form-label">Category Name</label>
                        <input type="text" class="form-control" id="edit_name" name="name" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit_description" class="form-label">Description</label>
                        <textarea class="form-control" id="edit_description" name="description" rows="3"></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="edit_icon" class="form-label">Icon Class (FontAwesome)</label>
                        <input type="text" class="form-control" id="edit_icon" name="icon" placeholder="fas fa-wrench">
                        <small class="form-text text-muted">Enter FontAwesome icon class (e.g., fas fa-wrench)</small>
                    </div>
                    <div class="mb-3">
                        <label for="edit_parent_id" class="form-label">Parent Category</label>
                        <select class="form-control" id="edit_parent_id" name="parent_id">
                            <option value="">None</option>
                            <?php foreach ($categories as $cat): ?>
                                <option value="<?php echo $cat['id']; ?>"><?php echo htmlspecialchars($cat['name']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update Category</button>
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
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="categories.php" method="POST">
                <div class="modal-body">
                    <input type="hidden" name="action" value="delete">
                    <input type="hidden" name="category_id" id="delete_category_id">
                    <p>Are you sure you want to delete this category?</p>
                    <p><strong>Category Name:</strong> <span id="delete_category_name"></span></p>
                    <p class="text-danger">Warning: This action cannot be undone! All associated products will also be affected.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger">Delete</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize DataTable
    const dataTable = new DataTable('#categoriesTable', {
        order: [[1, 'asc']], // Sort by category name by default
        pageLength: 25, // Show 25 entries per page
        language: {
            search: "_INPUT_",
            searchPlaceholder: "Search categories..."
        }
    });

    // Get modal elements
    const addCategoryModal = document.getElementById('addCategoryModal');
    const editCategoryModal = document.getElementById('editCategoryModal');
    const deleteCategoryModal = document.getElementById('deleteCategoryModal');

    // Initialize modals
    const addModal = new bootstrap.Modal(addCategoryModal);
    const editModal = new bootstrap.Modal(editCategoryModal);
    const deleteModal = new bootstrap.Modal(deleteCategoryModal);

    // Handle modal hidden events to remove backdrop
    [addCategoryModal, editCategoryModal, deleteCategoryModal].forEach(modal => {
        modal.addEventListener('hidden.bs.modal', function () {
            document.querySelector('.modal-backdrop')?.remove();
            document.body.classList.remove('modal-open');
            document.body.style.overflow = '';
            document.body.style.paddingRight = '';
        });
    });

    // Handle edit button clicks
    document.querySelectorAll('.edit-category').forEach(button => {
        button.addEventListener('click', function() {
            document.getElementById('edit_category_id').value = this.dataset.id;
            document.getElementById('edit_name').value = this.dataset.name;
            document.getElementById('edit_description').value = this.dataset.description;
            document.getElementById('edit_icon').value = this.dataset.icon;
            document.getElementById('edit_parent_id').value = this.dataset.parentId || '';
        });
    });

    // Handle delete button clicks
    document.querySelectorAll('.delete-category').forEach(button => {
        button.addEventListener('click', function() {
            document.getElementById('delete_category_id').value = this.dataset.id;
            document.getElementById('delete_category_name').textContent = this.dataset.name;
        });
    });

    // Auto-dismiss alerts after 5 seconds
    document.querySelectorAll('.alert').forEach(alert => {
        setTimeout(() => {
            const closeButton = alert.querySelector('.btn-close');
            if (closeButton) {
                closeButton.click();
            }
        }, 5000);
    });
});
</script>

<?php require_once('includes/footer.php'); ?> 