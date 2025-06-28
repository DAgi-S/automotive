<?php
session_start();
require_once '../includes/config.php';

// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit();
}

include 'includes/header.php';

// Fetch all categories
$query = "SELECT * FROM tbl_categories ORDER BY created_at DESC";
$categories = $conn->query($query)->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="container-fluid">
    <h1 class="h3 mb-4 text-gray-800">Ecommerce Categories</h1>

    <!-- Add Category Button -->
    <div class="mb-4">
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addCategoryModal">
            <i class="fas fa-plus"></i> Add New Category
        </button>
    </div>

    <!-- Categories List Card -->
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
                            <th>Status</th>
                            <th>Created At</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($categories as $category): ?>
                            <tr>
                                <td><?php echo $category['id']; ?></td>
                                <td><?php echo htmlspecialchars($category['name']); ?></td>
                                <td><?php echo htmlspecialchars($category['description']); ?></td>
                                <td>
                                    <span class="badge bg-<?php echo $category['status'] === 'active' ? 'success' : 'danger'; ?>">
                                        <?php echo ucfirst($category['status']); ?>
                                    </span>
                                </td>
                                <td><?php echo date('M d, Y H:i', strtotime($category['created_at'])); ?></td>
                                <td>
                                    <button type="button" class="btn btn-info btn-sm edit-category" 
                                            data-category-id="<?php echo $category['id']; ?>">
                                        <i class="fas fa-edit"></i> Edit
                                    </button>
                                    <button type="button" class="btn btn-danger btn-sm delete-category" 
                                            data-category-id="<?php echo $category['id']; ?>">
                                        <i class="fas fa-trash"></i> Delete
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
<div class="modal fade" id="addCategoryModal" tabindex="-1" aria-labelledby="addCategoryModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="addCategoryModalLabel">
                    <i class="fas fa-plus me-2"></i>Add New Category
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="addCategoryForm">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="categoryName" class="form-label">Category Name</label>
                        <input type="text" class="form-control" id="categoryName" name="name" required>
                    </div>
                    <div class="mb-3">
                        <label for="categoryDescription" class="form-label">Description</label>
                        <textarea class="form-control" id="categoryDescription" name="description" rows="3"></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="categoryStatus" class="form-label">Status</label>
                        <select class="form-select" id="categoryStatus" name="status">
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
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
<div class="modal fade" id="editCategoryModal" tabindex="-1" aria-labelledby="editCategoryModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="editCategoryModalLabel">
                    <i class="fas fa-edit me-2"></i>Edit Category
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="editCategoryForm">
                <input type="hidden" id="editCategoryId" name="id">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="editCategoryName" class="form-label">Category Name</label>
                        <input type="text" class="form-control" id="editCategoryName" name="name" required>
                    </div>
                    <div class="mb-3">
                        <label for="editCategoryDescription" class="form-label">Description</label>
                        <textarea class="form-control" id="editCategoryDescription" name="description" rows="3"></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="editCategoryStatus" class="form-label">Status</label>
                        <select class="form-select" id="editCategoryStatus" name="status">
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
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

<script>
$(document).ready(function() {
    // Initialize DataTable
    $('#categoriesTable').DataTable({
        order: [[0, 'desc']], // Sort by ID by default
        pageLength: 25,
        responsive: true
    });

    // Handle Add Category form submission
    $('#addCategoryForm').submit(function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        const formObject = {};
        formData.forEach((value, key) => {
            formObject[key] = value;
        });
        
        $.ajax({
            url: 'ajax/add_category.php',
            method: 'POST',
            data: formObject,
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    location.reload();
                } else {
                    alert('Error adding category: ' + (response.error || 'Unknown error'));
                }
            },
            error: function(xhr) {
                let errorMessage = 'An error occurred while adding the category.';
                try {
                    const response = JSON.parse(xhr.responseText);
                    if (response.error) {
                        errorMessage = response.error;
                    }
                } catch (e) {
                    console.error('Error parsing error response:', e);
                }
                alert(errorMessage);
            }
        });
    });

    // Handle Edit Category button click
    $('.edit-category').click(function() {
        const categoryId = $(this).data('category-id');
        editCategory(categoryId);
    });

    // Handle Edit Category form submission
    $('#editCategoryForm').submit(function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        const formObject = {};
        formData.forEach((value, key) => {
            formObject[key] = value;
        });
        
        $.ajax({
            url: 'ajax/update_category.php',
            method: 'POST',
            data: formObject,
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    location.reload();
                } else {
                    alert('Error updating category: ' + (response.error || 'Unknown error'));
                }
            },
            error: function(xhr) {
                let errorMessage = 'An error occurred while updating the category.';
                try {
                    const response = JSON.parse(xhr.responseText);
                    if (response.error) {
                        errorMessage = response.error;
                    }
                } catch (e) {
                    console.error('Error parsing error response:', e);
                }
                alert(errorMessage);
            }
        });
    });

    // Handle Delete Category button click
    $('.delete-category').click(function() {
        const categoryId = $(this).data('category-id');
        if (confirm('Are you sure you want to delete this category? This will affect all products in this category.')) {
            deleteCategory(categoryId);
        }
    });
});

function editCategory(categoryId) {
    $.ajax({
        url: 'ajax/get_category.php',
        method: 'GET',
        data: { category_id: categoryId },
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                const category = response.category;
                
                // Populate form fields
                $('#editCategoryId').val(category.id);
                $('#editCategoryName').val(category.name);
                $('#editCategoryDescription').val(category.description);
                $('#editCategoryStatus').val(category.status);
                
                // Show modal
                const modal = new bootstrap.Modal(document.getElementById('editCategoryModal'));
                modal.show();
            } else {
                alert('Error fetching category details: ' + (response.error || 'Unknown error'));
            }
        },
        error: function(xhr) {
            let errorMessage = 'An error occurred while fetching category details.';
            try {
                const response = JSON.parse(xhr.responseText);
                if (response.error) {
                    errorMessage = response.error;
                }
            } catch (e) {
                console.error('Error parsing error response:', e);
            }
            alert(errorMessage);
        }
    });
}

function deleteCategory(categoryId) {
    $.ajax({
        url: 'ajax/delete_category.php',
        method: 'POST',
        data: { category_id: categoryId },
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                location.reload();
            } else {
                alert('Error deleting category: ' + (response.error || 'Unknown error'));
            }
        },
        error: function(xhr) {
            let errorMessage = 'An error occurred while deleting the category.';
            try {
                const response = JSON.parse(xhr.responseText);
                if (response.error) {
                    errorMessage = response.error;
                }
            } catch (e) {
                console.error('Error parsing error response:', e);
            }
            alert(errorMessage);
        }
    });
}
</script>

<?php include 'includes/footer.php'; ?> 