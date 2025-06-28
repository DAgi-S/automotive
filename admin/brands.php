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
                    $stmt = $conn->prepare("INSERT INTO car_brands (brand_name, created_at) VALUES (?, NOW())");
                    $stmt->execute([
                        $_POST['brand_name']
                    ]);
                    $_SESSION['success'] = "Brand added successfully!";
                } catch (PDOException $e) {
                    $_SESSION['error'] = "Error adding brand: " . $e->getMessage();
                }
                break;

            case 'update':
                try {
                    $stmt = $conn->prepare("UPDATE car_brands SET brand_name = ? WHERE id = ?");
                    $stmt->execute([
                        $_POST['brand_name'],
                        $_POST['brand_id']
                    ]);
                    $_SESSION['success'] = "Brand updated successfully!";
                } catch (PDOException $e) {
                    $_SESSION['error'] = "Error updating brand: " . $e->getMessage();
                }
                break;

            case 'delete':
                try {
                    // First check if there are any models using this brand
                    $stmt = $conn->prepare("SELECT COUNT(*) FROM car_models WHERE brand_id = ?");
                    $stmt->execute([$_POST['brand_id']]);
                    $count = $stmt->fetchColumn();

                    if ($count > 0) {
                        $_SESSION['error'] = "Cannot delete brand: There are car models associated with this brand.";
                    } else {
                        $stmt = $conn->prepare("DELETE FROM car_brands WHERE id = ?");
                        $stmt->execute([$_POST['brand_id']]);
                        $_SESSION['success'] = "Brand deleted successfully!";
                    }
                } catch (PDOException $e) {
                    $_SESSION['error'] = "Error deleting brand: " . $e->getMessage();
                }
                break;
        }
        header("Location: brands.php");
        exit();
    }
}

// Fetch all brands
try {
    $stmt = $conn->query("
        SELECT *
        FROM car_brands
        ORDER BY brand_name ASC
    ");
    $brands = $stmt->fetchAll();
} catch (PDOException $e) {
    $_SESSION['error'] = "Error fetching brands: " . $e->getMessage();
    $brands = [];
}

// Include header
require_once('includes/header.php');
?>

<!-- Begin Page Content -->
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Car Brands Management</h1>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addBrandModal">
            <i class="fas fa-plus"></i> Add New Brand
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

    <!-- Brands Table -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">All Car Brands</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="brandsTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Brand Name</th>
                            <th>Created At</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($brands as $brand): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($brand['id']); ?></td>
                                <td><?php echo htmlspecialchars($brand['brand_name']); ?></td>
                                <td><?php echo htmlspecialchars($brand['created_at']); ?></td>
                                <td>
                                    <button class="btn btn-sm btn-primary view-models"
                                            data-id="<?php echo $brand['id']; ?>"
                                            data-name="<?php echo htmlspecialchars($brand['brand_name']); ?>"
                                            data-bs-toggle="modal"
                                            data-bs-target="#viewModelsModal">
                                        <i class="fas fa-car"></i>
                                    </button>
                                    <button class="btn btn-sm btn-info edit-brand"
                                            data-id="<?php echo $brand['id']; ?>"
                                            data-name="<?php echo htmlspecialchars($brand['brand_name']); ?>"
                                            data-bs-toggle="modal"
                                            data-bs-target="#editBrandModal">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button class="btn btn-sm btn-danger delete-brand"
                                            data-id="<?php echo $brand['id']; ?>"
                                            data-name="<?php echo htmlspecialchars($brand['brand_name']); ?>"
                                            data-bs-toggle="modal"
                                            data-bs-target="#deleteBrandModal">
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

<!-- Add Brand Modal -->
<div class="modal fade" id="addBrandModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add New Brand</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="brands.php" method="POST">
                <div class="modal-body">
                    <input type="hidden" name="action" value="add">
                    <div class="mb-3">
                        <label for="brand_name" class="form-label">Brand Name</label>
                        <input type="text" class="form-control" id="brand_name" name="brand_name" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
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
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="brands.php" method="POST">
                <div class="modal-body">
                    <input type="hidden" name="action" value="update">
                    <input type="hidden" name="brand_id" id="edit_brand_id">
                    <div class="mb-3">
                        <label for="edit_brand_name" class="form-label">Brand Name</label>
                        <input type="text" class="form-control" id="edit_brand_name" name="brand_name" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update Brand</button>
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
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="brands.php" method="POST">
                <div class="modal-body">
                    <input type="hidden" name="action" value="delete">
                    <input type="hidden" name="brand_id" id="delete_brand_id">
                    <p>Are you sure you want to delete this brand?</p>
                    <p><strong>Brand Name:</strong> <span id="delete_brand_name"></span></p>
                    <p class="text-danger">Warning: This action cannot be undone! All associated car models will also be affected.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger">Delete</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- View Models Modal -->
<div class="modal fade" id="viewModelsModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Car Models - <span id="brand_name_title"></span></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <button class="btn btn-primary" id="addModelBtn">
                        <i class="fas fa-plus"></i> Add New Model
                    </button>
                </div>
                <div class="table-responsive">
                    <table class="table table-bordered" id="modelsTable" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Model Name</th>
                                <th>Year From</th>
                                <th>Year To</th>
                                <th>Created At</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody id="modelsTableBody">
                            <!-- Models will be loaded here dynamically -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add Model Modal -->
<div class="modal fade" id="addModelModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add New Model</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="addModelForm">
                <div class="modal-body">
                    <input type="hidden" name="action" value="add_model">
                    <input type="hidden" name="brand_id" id="add_model_brand_id">
                    <div class="mb-3">
                        <label for="model_name" class="form-label">Model Name</label>
                        <input type="text" class="form-control" id="model_name" name="model_name" required>
                    </div>
                    <div class="mb-3">
                        <label for="year_from" class="form-label">Year From</label>
                        <input type="number" class="form-control" id="year_from" name="year_from" min="1900" max="2024" required>
                    </div>
                    <div class="mb-3">
                        <label for="year_to" class="form-label">Year To</label>
                        <input type="number" class="form-control" id="year_to" name="year_to" min="1900" max="2024" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Add Model</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Model Modal -->
<div class="modal fade" id="editModelModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Model</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="editModelForm">
                <div class="modal-body">
                    <input type="hidden" name="action" value="edit_model">
                    <input type="hidden" name="model_id" id="edit_model_id">
                    <div class="mb-3">
                        <label for="edit_model_name" class="form-label">Model Name</label>
                        <input type="text" class="form-control" id="edit_model_name" name="model_name" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit_year_from" class="form-label">Year From</label>
                        <input type="number" class="form-control" id="edit_year_from" name="year_from" min="1900" max="2024" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit_year_to" class="form-label">Year To</label>
                        <input type="number" class="form-control" id="edit_year_to" name="year_to" min="1900" max="2024" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update Model</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Delete Model Modal -->
<div class="modal fade" id="deleteModelModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Delete Model</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="deleteModelForm">
                <div class="modal-body">
                    <input type="hidden" name="action" value="delete_model">
                    <input type="hidden" name="model_id" id="delete_model_id">
                    <p>Are you sure you want to delete this model?</p>
                    <p><strong>Model Name:</strong> <span id="delete_model_name"></span></p>
                    <p class="text-danger">Warning: This action cannot be undone!</p>
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
    const dataTable = new DataTable('#brandsTable', {
        order: [[1, 'asc']], // Sort by brand name by default
        pageLength: 25, // Show 25 entries per page
        language: {
            search: "_INPUT_",
            searchPlaceholder: "Search brands..."
        }
    });

    // Get modal elements
    const addBrandModal = document.getElementById('addBrandModal');
    const editBrandModal = document.getElementById('editBrandModal');
    const deleteBrandModal = document.getElementById('deleteBrandModal');

    // Initialize modals
    const addModal = new bootstrap.Modal(addBrandModal);
    const editModal = new bootstrap.Modal(editBrandModal);
    const deleteModal = new bootstrap.Modal(deleteBrandModal);

    // Handle modal hidden events to remove backdrop
    [addBrandModal, editBrandModal, deleteBrandModal].forEach(modal => {
        modal.addEventListener('hidden.bs.modal', function () {
            document.querySelector('.modal-backdrop')?.remove();
            document.body.classList.remove('modal-open');
            document.body.style.overflow = '';
            document.body.style.paddingRight = '';
        });
    });

    // Handle edit button clicks
    document.querySelectorAll('.edit-brand').forEach(button => {
        button.addEventListener('click', function() {
            document.getElementById('edit_brand_id').value = this.dataset.id;
            document.getElementById('edit_brand_name').value = this.dataset.name;
        });
    });

    // Handle delete button clicks
    document.querySelectorAll('.delete-brand').forEach(button => {
        button.addEventListener('click', function() {
            document.getElementById('delete_brand_id').value = this.dataset.id;
            document.getElementById('delete_brand_name').textContent = this.dataset.name;
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

    // Initialize additional modals
    const viewModelsModal = document.getElementById('viewModelsModal');
    const addModelModal = document.getElementById('addModelModal');
    const editModelModal = document.getElementById('editModelModal');
    const deleteModelModal = document.getElementById('deleteModelModal');
    
    const viewModelsModalInstance = new bootstrap.Modal(viewModelsModal);
    const addModelModalInstance = new bootstrap.Modal(addModelModal);
    const editModelModalInstance = new bootstrap.Modal(editModelModal);
    const deleteModelModalInstance = new bootstrap.Modal(deleteModelModal);

    let currentBrandId = null;

    // Handle view models button clicks
    document.querySelectorAll('.view-models').forEach(button => {
        button.addEventListener('click', async function() {
            currentBrandId = this.dataset.id;
            const brandName = this.dataset.name;
            document.getElementById('brand_name_title').textContent = brandName;
            await loadModels(currentBrandId);
        });
    });

    // Handle add model button click
    document.getElementById('addModelBtn').addEventListener('click', function() {
        document.getElementById('add_model_brand_id').value = currentBrandId;
        viewModelsModalInstance.hide();
        addModelModalInstance.show();
    });

    // Load models for a brand
    async function loadModels(brandId) {
        try {
            const response = await fetch(`api/models/get_models.php?brand_id=${brandId}`);
            const data = await response.json();
            
            if (data.success) {
                const tbody = document.getElementById('modelsTableBody');
                tbody.innerHTML = '';
                
                data.models.forEach(model => {
                    tbody.innerHTML += `
                        <tr>
                            <td>${model.id}</td>
                            <td>${model.model_name}</td>
                            <td>${model.year_from}</td>
                            <td>${model.year_to}</td>
                            <td>${model.created_at}</td>
                            <td>
                                <button class="btn btn-sm btn-info edit-model"
                                        data-id="${model.id}"
                                        data-name="${model.model_name}"
                                        data-year-from="${model.year_from}"
                                        data-year-to="${model.year_to}">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button class="btn btn-sm btn-danger delete-model"
                                        data-id="${model.id}"
                                        data-name="${model.model_name}">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                    `;
                });

                // Attach event listeners to new buttons
                attachModelEventListeners();
            } else {
                alert(data.message || 'Error loading models');
            }
        } catch (error) {
            console.error('Error loading models:', error);
            alert('Error loading models. Please try again.');
        }
    }

    // Attach event listeners to model buttons
    function attachModelEventListeners() {
        // Edit model buttons
        document.querySelectorAll('.edit-model').forEach(button => {
            button.addEventListener('click', function() {
                document.getElementById('edit_model_id').value = this.dataset.id;
                document.getElementById('edit_model_name').value = this.dataset.name;
                document.getElementById('edit_year_from').value = this.dataset.yearFrom;
                document.getElementById('edit_year_to').value = this.dataset.yearTo;
                
                viewModelsModalInstance.hide();
                editModelModalInstance.show();
            });
        });

        // Delete model buttons
        document.querySelectorAll('.delete-model').forEach(button => {
            button.addEventListener('click', function() {
                document.getElementById('delete_model_id').value = this.dataset.id;
                document.getElementById('delete_model_name').textContent = this.dataset.name;
                
                viewModelsModalInstance.hide();
                deleteModelModalInstance.show();
            });
        });
    }

    // Handle form submissions
    document.getElementById('addModelForm').addEventListener('submit', async function(e) {
        e.preventDefault();
        await handleModelFormSubmit(this, 'add_model');
    });

    document.getElementById('editModelForm').addEventListener('submit', async function(e) {
        e.preventDefault();
        await handleModelFormSubmit(this, 'edit_model');
    });

    document.getElementById('deleteModelForm').addEventListener('submit', async function(e) {
        e.preventDefault();
        await handleModelFormSubmit(this, 'delete_model');
    });

    // Generic form submission handler
    async function handleModelFormSubmit(form, action) {
        try {
            const formData = new FormData(form);
            const response = await fetch('api/models/manage_models.php', {
                method: 'POST',
                body: formData
            });
            const data = await response.json();
            
            if (data.success) {
                // Close all modals
                addModelModalInstance.hide();
                editModelModalInstance.hide();
                deleteModelModalInstance.hide();
                
                // Show success message
                alert(data.message);
                
                // Reload models and show models modal
                await loadModels(currentBrandId);
                viewModelsModalInstance.show();
            } else {
                alert(data.message || 'Error processing request');
            }
        } catch (error) {
            console.error('Error submitting form:', error);
            alert('Error processing request. Please try again.');
        }
    }

    // Handle modal hidden events
    [addModelModal, editModelModal, deleteModelModal].forEach(modal => {
        modal.addEventListener('hidden.bs.modal', function () {
            viewModelsModalInstance.show();
        });
    });
});
</script>

<?php require_once('includes/footer.php'); ?> 