<?php
session_start();
require_once '../includes/config.php';

// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit();
}

include 'includes/header.php';

// Fetch all products with their categories
$query = "SELECT p.*, c.name as category_name 
          FROM tbl_products p 
          LEFT JOIN tbl_categories c ON p.category_id = c.id 
          ORDER BY p.created_at DESC";
$products = $conn->query($query)->fetchAll(PDO::FETCH_ASSOC);

// Fetch categories for dropdown
$query = "SELECT * FROM tbl_categories WHERE status = 'active' ORDER BY name";
$categories = $conn->query($query)->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="container-fluid">
    <h1 class="h3 mb-4 text-gray-800">Ecommerce Products</h1>

    <!-- Add Product Button -->
    <div class="mb-4">
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addProductModal">
            <i class="fas fa-plus"></i> Add New Product
        </button>
    </div>

    <!-- Products List Card -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">All Products</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="productsTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Image</th>
                            <th>Name</th>
                            <th>Category</th>
                            <th>Price</th>
                            <th>Stock</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($products as $product): ?>
                            <tr>
                                <td><?php echo $product['id']; ?></td>
                                <td>
                                    <?php if ($product['image_url']): ?>
                                        <img src="<?php echo htmlspecialchars($product['image_url']); ?>" 
                                             alt="<?php echo htmlspecialchars($product['name']); ?>"
                                             class="img-thumbnail" style="max-width: 50px;">
                                    <?php else: ?>
                                        <span class="text-muted">No image</span>
                                    <?php endif; ?>
                                </td>
                                <td><?php echo htmlspecialchars($product['name']); ?></td>
                                <td><?php echo htmlspecialchars($product['category_name'] ?? 'Uncategorized'); ?></td>
                                <td>$<?php echo number_format($product['price'], 2); ?></td>
                                <td><?php echo $product['stock']; ?></td>
                                <td>
                                    <span class="badge bg-<?php echo $product['status'] === 'active' ? 'success' : 'danger'; ?>">
                                        <?php echo ucfirst($product['status']); ?>
                                    </span>
                                </td>
                                <td>
                                    <button type="button" class="btn btn-info btn-sm edit-product" 
                                            data-product-id="<?php echo $product['id']; ?>">
                                        <i class="fas fa-edit"></i> Edit
                                    </button>
                                    <button type="button" class="btn btn-danger btn-sm delete-product" 
                                            data-product-id="<?php echo $product['id']; ?>">
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

<!-- Add Product Modal -->
<div class="modal fade" id="addProductModal" tabindex="-1" aria-labelledby="addProductModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="addProductModalLabel">
                    <i class="fas fa-plus me-2"></i>Add New Product
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="addProductForm" enctype="multipart/form-data">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="productName" class="form-label">Product Name</label>
                                <input type="text" class="form-control" id="productName" name="name" required>
                            </div>
                            <div class="mb-3">
                                <label for="productCategory" class="form-label">Category</label>
                                <select class="form-select" id="productCategory" name="category_id">
                                    <option value="">Select Category</option>
                                    <?php foreach ($categories as $category): ?>
                                        <option value="<?php echo $category['id']; ?>">
                                            <?php echo htmlspecialchars($category['name']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="productPrice" class="form-label">Price</label>
                                <div class="input-group">
                                    <span class="input-group-text">$</span>
                                    <input type="number" class="form-control" id="productPrice" name="price" 
                                           step="0.01" min="0" required>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="productStock" class="form-label">Stock</label>
                                <input type="number" class="form-control" id="productStock" name="stock" 
                                       min="0" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="productDescription" class="form-label">Description</label>
                                <textarea class="form-control" id="productDescription" name="description" 
                                          rows="3"></textarea>
                            </div>
                            <div class="mb-3">
                                <label for="productStatus" class="form-label">Status</label>
                                <select class="form-select" id="productStatus" name="status">
                                    <option value="active">Active</option>
                                    <option value="inactive">Inactive</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="productImage" class="form-label">Product Image</label>
                                <input type="file" class="form-control" id="productImage" name="image" 
                                       accept="image/*">
                                <div id="imagePreview" class="mt-2"></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Add Product</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Product Modal -->
<div class="modal fade" id="editProductModal" tabindex="-1" aria-labelledby="editProductModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="editProductModalLabel">
                    <i class="fas fa-edit me-2"></i>Edit Product
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="editProductForm" enctype="multipart/form-data">
                <input type="hidden" id="editProductId" name="id">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="editProductName" class="form-label">Product Name</label>
                                <input type="text" class="form-control" id="editProductName" name="name" required>
                            </div>
                            <div class="mb-3">
                                <label for="editProductCategory" class="form-label">Category</label>
                                <select class="form-select" id="editProductCategory" name="category_id">
                                    <option value="">Select Category</option>
                                    <?php foreach ($categories as $category): ?>
                                        <option value="<?php echo $category['id']; ?>">
                                            <?php echo htmlspecialchars($category['name']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="editProductPrice" class="form-label">Price</label>
                                <div class="input-group">
                                    <span class="input-group-text">$</span>
                                    <input type="number" class="form-control" id="editProductPrice" name="price" 
                                           step="0.01" min="0" required>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="editProductStock" class="form-label">Stock</label>
                                <input type="number" class="form-control" id="editProductStock" name="stock" 
                                       min="0" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="editProductDescription" class="form-label">Description</label>
                                <textarea class="form-control" id="editProductDescription" name="description" 
                                          rows="3"></textarea>
                            </div>
                            <div class="mb-3">
                                <label for="editProductStatus" class="form-label">Status</label>
                                <select class="form-select" id="editProductStatus" name="status">
                                    <option value="active">Active</option>
                                    <option value="inactive">Inactive</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="editProductImage" class="form-label">Product Image</label>
                                <input type="file" class="form-control" id="editProductImage" name="image" 
                                       accept="image/*">
                                <div id="editImagePreview" class="mt-2"></div>
                                <input type="hidden" id="currentImage" name="current_image">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update Product</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    // Initialize DataTable
    $('#productsTable').DataTable({
        order: [[0, 'desc']], // Sort by ID by default
        pageLength: 25,
        responsive: true
    });

    // Handle image preview for add form
    $('#productImage').change(function() {
        const file = this.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                $('#imagePreview').html(`
                    <img src="${e.target.result}" class="img-thumbnail" style="max-height: 150px;">
                `);
            };
            reader.readAsDataURL(file);
        } else {
            $('#imagePreview').empty();
        }
    });

    // Handle image preview for edit form
    $('#editProductImage').change(function() {
        const file = this.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                $('#editImagePreview').html(`
                    <img src="${e.target.result}" class="img-thumbnail" style="max-height: 150px;">
                `);
            };
            reader.readAsDataURL(file);
        }
    });

    // Handle Add Product form submission
    $('#addProductForm').submit(function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        
        $.ajax({
            url: 'ajax/add_product.php',
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    location.reload();
                } else {
                    alert('Error adding product: ' + (response.error || 'Unknown error'));
                }
            },
            error: function(xhr) {
                let errorMessage = 'An error occurred while adding the product.';
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

    // Handle Edit Product button click
    $('.edit-product').click(function() {
        const productId = $(this).data('product-id');
        editProduct(productId);
    });

    // Handle Edit Product form submission
    $('#editProductForm').submit(function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        
        $.ajax({
            url: 'ajax/update_product.php',
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    location.reload();
                } else {
                    alert('Error updating product: ' + (response.error || 'Unknown error'));
                }
            },
            error: function(xhr) {
                let errorMessage = 'An error occurred while updating the product.';
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

    // Handle Delete Product button click
    $('.delete-product').click(function() {
        const productId = $(this).data('product-id');
        if (confirm('Are you sure you want to delete this product?')) {
            deleteProduct(productId);
        }
    });
});

function editProduct(productId) {
    $.ajax({
        url: 'ajax/get_product.php',
        method: 'GET',
        data: { product_id: productId },
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                const product = response.product;
                
                // Populate form fields
                $('#editProductId').val(product.id);
                $('#editProductName').val(product.name);
                $('#editProductCategory').val(product.category_id);
                $('#editProductPrice').val(product.price);
                $('#editProductStock').val(product.stock);
                $('#editProductDescription').val(product.description);
                $('#editProductStatus').val(product.status);
                $('#currentImage').val(product.image_url);
                
                // Show current image if exists
                if (product.image_url) {
                    $('#editImagePreview').html(`
                        <img src="${product.image_url}" class="img-thumbnail" style="max-height: 150px;">
                    `);
                } else {
                    $('#editImagePreview').empty();
                }
                
                // Show modal
                const modal = new bootstrap.Modal(document.getElementById('editProductModal'));
                modal.show();
            } else {
                alert('Error fetching product details: ' + (response.error || 'Unknown error'));
            }
        },
        error: function(xhr) {
            let errorMessage = 'An error occurred while fetching product details.';
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

function deleteProduct(productId) {
    $.ajax({
        url: 'ajax/delete_product.php',
        method: 'POST',
        data: { product_id: productId },
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                location.reload();
            } else {
                alert('Error deleting product: ' + (response.error || 'Unknown error'));
            }
        },
        error: function(xhr) {
            let errorMessage = 'An error occurred while deleting the product.';
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