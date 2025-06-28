<?php
define('INCLUDED', true);
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
                    $stmt = $conn->prepare("INSERT INTO tbl_services (service_name, description, price, duration, status, icon_class) VALUES (:name, :description, :price, :duration, 'active', :icon_class)");
                    $stmt->execute([
                        'name' => $_POST['service_name'],
                        'description' => $_POST['description'],
                        'price' => floatval($_POST['price']),
                        'duration' => $_POST['duration'],
                        'icon_class' => $_POST['icon_class']
                    ]);
                    $_SESSION['success'] = "Service added successfully!";
                } catch (PDOException $e) {
                    $_SESSION['error'] = "Failed to add service: " . $e->getMessage();
                }
                break;

            case 'edit':
                try {
                    $stmt = $conn->prepare("UPDATE tbl_services SET service_name = :name, description = :description, price = :price, duration = :duration, status = :status, icon_class = :icon_class WHERE service_id = :id");
                    $stmt->execute([
                        'name' => $_POST['service_name'],
                        'description' => $_POST['description'],
                        'price' => floatval($_POST['price']),
                        'duration' => $_POST['duration'],
                        'status' => $_POST['status'],
                        'icon_class' => $_POST['icon_class'],
                        'id' => $_POST['service_id']
                    ]);
                    $_SESSION['success'] = "Service updated successfully!";
                } catch (PDOException $e) {
                    $_SESSION['error'] = "Failed to update service: " . $e->getMessage();
                }
                break;

            case 'delete':
                try {
                    $stmt = $conn->prepare("DELETE FROM tbl_services WHERE service_id = :id");
                    $stmt->execute(['id' => $_POST['service_id']]);
                    $_SESSION['success'] = "Service deleted successfully!";
                } catch (PDOException $e) {
                    $_SESSION['error'] = "Failed to delete service: " . $e->getMessage();
                }
                break;
        }
        header("Location: services.php");
        exit();
    }
}

// Include header
require_once('includes/header.php');

// Fetch all services
try {
    $stmt = $conn->query("SELECT * FROM tbl_services ORDER BY service_id DESC");
    $services = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $services = [];
    $_SESSION['error'] = "Failed to fetch services: " . $e->getMessage();
}
?>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
<style>
.icon-picker {
    max-height: 200px;
    overflow-y: auto;
    padding: 10px;
    border: 1px solid #ddd;
    border-radius: 4px;
    margin-top: 10px;
}

.icon-option {
    display: inline-block;
    padding: 10px;
    cursor: pointer;
    border: 1px solid transparent;
    border-radius: 4px;
}

.icon-option:hover {
    background-color: #f8f9fa;
    border-color: #ddd;
}

.icon-option.selected {
    background-color: #e9ecef;
    border-color: #0d6efd;
}

.icon-option i {
    font-size: 20px;
    width: 30px;
    text-align: center;
}

.icon-preview {
    font-size: 24px;
    margin-bottom: 10px;
    text-align: center;
}
</style>

<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Services Management</h1>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addServiceModal">
            <i class="fas fa-plus"></i> Add New Service
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

    <!-- Services Table -->
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table" id="servicesTable">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>SERVICE NAME</th>
                            <th>DESCRIPTION</th>
                            <th>PRICE</th>
                            <th>DURATION</th>
                            <th>STATUS</th>
                            <th>ACTIONS</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($services as $service): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($service['service_id']); ?></td>
                                <td><?php echo htmlspecialchars($service['service_name']); ?></td>
                                <td><?php echo htmlspecialchars($service['description']); ?></td>
                                <td>ETB <?php echo number_format($service['price'], 2); ?></td>
                                <td><?php echo htmlspecialchars($service['duration']); ?></td>
                                <td>
                                    <span class="badge badge-<?php echo $service['status'] === 'active' ? 'success' : 'danger'; ?>">
                                        <?php echo ucfirst($service['status']); ?>
                                    </span>
                                </td>
                                <td>
                                    <button class="btn btn-sm btn-info edit-service" 
                                            data-bs-toggle="modal" 
                                            data-bs-target="#editServiceModal"
                                            data-id="<?php echo $service['service_id']; ?>"
                                            data-name="<?php echo htmlspecialchars($service['service_name']); ?>"
                                            data-description="<?php echo htmlspecialchars($service['description']); ?>"
                                            data-price="<?php echo $service['price']; ?>"
                                            data-duration="<?php echo htmlspecialchars($service['duration']); ?>"
                                            data-status="<?php echo htmlspecialchars($service['status']); ?>">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button class="btn btn-sm btn-danger delete-service"
                                            data-bs-toggle="modal"
                                            data-bs-target="#deleteServiceModal"
                                            data-id="<?php echo $service['service_id']; ?>"
                                            data-name="<?php echo htmlspecialchars($service['service_name']); ?>">
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

<!-- Add Service Modal -->
<div class="modal fade" id="addServiceModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add New Service</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="services.php" method="POST">
                <div class="modal-body">
                    <input type="hidden" name="action" value="add">
                    <input type="hidden" name="icon_class" id="add_icon_class">
                    <div class="mb-3">
                        <label class="form-label">Service Name</label>
                        <input type="text" class="form-control" name="service_name" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea class="form-control" name="description" rows="3" required></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Price (ETB)</label>
                        <input type="number" class="form-control" name="price" step="0.01" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Duration</label>
                        <input type="text" class="form-control" name="duration" placeholder="e.g., 1 hour" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Service Icon</label>
                        <div class="icon-preview">
                            <i class="fas fa-wrench" id="add_icon_preview"></i>
                        </div>
                        <button type="button" class="btn btn-outline-secondary w-100" data-bs-toggle="collapse" data-bs-target="#addIconPicker">
                            Select Icon
                        </button>
                        <div class="collapse" id="addIconPicker">
                            <div class="icon-picker">
                                <div class="icon-option selected" data-icon="fas fa-wrench"><i class="fas fa-wrench"></i></div>
                                <div class="icon-option" data-icon="fas fa-car"><i class="fas fa-car"></i></div>
                                <div class="icon-option" data-icon="fas fa-oil-can"><i class="fas fa-oil-can"></i></div>
                                <div class="icon-option" data-icon="fas fa-tools"><i class="fas fa-tools"></i></div>
                                <div class="icon-option" data-icon="fas fa-cog"><i class="fas fa-cog"></i></div>
                                <div class="icon-option" data-icon="fas fa-snowflake"><i class="fas fa-snowflake"></i></div>
                                <div class="icon-option" data-icon="fas fa-car-battery"><i class="fas fa-car-battery"></i></div>
                                <div class="icon-option" data-icon="fas fa-gas-pump"><i class="fas fa-gas-pump"></i></div>
                                <div class="icon-option" data-icon="fas fa-tachometer-alt"><i class="fas fa-tachometer-alt"></i></div>
                                <div class="icon-option" data-icon="fas fa-brake-system"><i class="fas fa-car-crash"></i></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Add Service</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Service Modal -->
<div class="modal fade" id="editServiceModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Service</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="services.php" method="POST">
                <div class="modal-body">
                    <input type="hidden" name="action" value="edit">
                    <input type="hidden" name="service_id" id="edit_service_id">
                    <input type="hidden" name="icon_class" id="edit_icon_class">
                    <div class="mb-3">
                        <label class="form-label">Service Name</label>
                        <input type="text" class="form-control" name="service_name" id="edit_service_name" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea class="form-control" name="description" id="edit_description" rows="3" required></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Price (ETB)</label>
                        <input type="number" class="form-control" name="price" id="edit_price" step="0.01" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Duration</label>
                        <input type="text" class="form-control" name="duration" id="edit_duration" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Status</label>
                        <select class="form-select" name="status" id="edit_status" required>
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Service Icon</label>
                        <div class="icon-preview">
                            <i class="fas fa-wrench" id="edit_icon_preview"></i>
                        </div>
                        <button type="button" class="btn btn-outline-secondary w-100" data-bs-toggle="collapse" data-bs-target="#editIconPicker">
                            Select Icon
                        </button>
                        <div class="collapse" id="editIconPicker">
                            <div class="icon-picker">
                                <div class="icon-option" data-icon="fas fa-wrench"><i class="fas fa-wrench"></i></div>
                                <div class="icon-option" data-icon="fas fa-car"><i class="fas fa-car"></i></div>
                                <div class="icon-option" data-icon="fas fa-oil-can"><i class="fas fa-oil-can"></i></div>
                                <div class="icon-option" data-icon="fas fa-tools"><i class="fas fa-tools"></i></div>
                                <div class="icon-option" data-icon="fas fa-cog"><i class="fas fa-cog"></i></div>
                                <div class="icon-option" data-icon="fas fa-snowflake"><i class="fas fa-snowflake"></i></div>
                                <div class="icon-option" data-icon="fas fa-car-battery"><i class="fas fa-car-battery"></i></div>
                                <div class="icon-option" data-icon="fas fa-gas-pump"><i class="fas fa-gas-pump"></i></div>
                                <div class="icon-option" data-icon="fas fa-tachometer-alt"><i class="fas fa-tachometer-alt"></i></div>
                                <div class="icon-option" data-icon="fas fa-brake-system"><i class="fas fa-car-crash"></i></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Delete Service Modal -->
<div class="modal fade" id="deleteServiceModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Delete Service</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="services.php" method="POST">
                <div class="modal-body">
                    <input type="hidden" name="action" value="delete">
                    <input type="hidden" name="service_id" id="delete_service_id">
                    <p>Are you sure you want to delete the service: <strong><span id="delete_service_name"></span></strong>?</p>
                    <p class="text-danger">This action cannot be undone!</p>
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
    // Initialize DataTable with proper configuration
    $('#servicesTable').DataTable({
        order: [[0, 'desc']],
        pageLength: 10,
        responsive: true,
        language: {
            search: "_INPUT_",
            searchPlaceholder: "Search services..."
        }
    });

    // Handle edit button clicks
    $(document).on('click', '.edit-service', function() {
        const serviceId = $(this).data('id');
        const editModal = $('#editServiceModal');
        const form = editModal.find('form');
        const submitBtn = form.find('button[type="submit"]');
        
        // Show loading state
        submitBtn.prop('disabled', true)
            .html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Loading...');

        // Fetch service details
        $.ajax({
            url: 'api/services/get-service.php',
            method: 'GET',
            data: { id: serviceId },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    const service = response.data;
                    $('#edit_service_id').val(service.service_id);
                    $('#edit_service_name').val(service.service_name);
                    $('#edit_description').val(service.description);
                    $('#edit_price').val(service.price);
                    $('#edit_duration').val(service.duration);
                    $('#edit_status').val(service.status);
                    
                    // Update icon selection
                    const iconClass = service.icon_class || 'fas fa-wrench';
                    $('#edit_icon_class').val(iconClass);
                    $('#edit_icon_preview').attr('class', iconClass);
                    
                    // Update selected icon in picker
                    const iconPicker = editModal.find('.icon-picker');
                    iconPicker.find('.icon-option').removeClass('selected');
                    iconPicker.find(`[data-icon="${iconClass}"]`).addClass('selected');
                } else {
                    throw new Error(response.error || 'Failed to fetch service details');
                }
            },
            error: function(xhr, status, error) {
                console.error('Error:', error);
                alert('An error occurred while fetching service details');
                editModal.modal('hide');
            },
            complete: function() {
                submitBtn.prop('disabled', false).html('Save Changes');
            }
        });
    });

    // Handle delete button clicks
    $('.delete-service').on('click', function() {
        const serviceId = $(this).data('id');
        const serviceName = $(this).data('name');
        $('#delete_service_id').val(serviceId);
        $('#delete_service_name').text(serviceName);
    });

    // Form submission handling
    $('form').on('submit', function(e) {
        e.preventDefault();
        
        const form = $(this);
        const submitBtn = form.find('button[type="submit"]');
        const originalText = submitBtn.html();
        
        // Show loading state
        submitBtn.prop('disabled', true)
            .html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Processing...');

        // Submit form using AJAX
        $.ajax({
            url: form.attr('action'),
            method: 'POST',
            data: form.serialize(),
            success: function() {
                window.location.reload();
            },
            error: function(xhr, status, error) {
                console.error('Error:', error);
                alert('An error occurred while processing your request');
                submitBtn.prop('disabled', false).html(originalText);
            }
        });
    });

    // Auto-dismiss alerts
    $('.alert').delay(5000).fadeOut(500);

    // Enhanced icon picker functionality
    function initializeIconPicker(modalId) {
        const modal = document.querySelector(modalId);
        const iconPicker = modal.querySelector('.icon-picker');
        const iconPreview = modal.querySelector('.icon-preview i');
        const iconClassInput = modal.querySelector('input[name="icon_class"]');

        iconPicker.addEventListener('click', function(e) {
            const iconOption = e.target.closest('.icon-option');
            if (!iconOption) return;

            // Remove selected class from all options
            iconPicker.querySelectorAll('.icon-option').forEach(opt => opt.classList.remove('selected'));
            
            // Add selected class to clicked option
            iconOption.classList.add('selected');
            
            // Update preview and hidden input
            const iconClass = iconOption.dataset.icon;
            iconPreview.className = iconClass;
            iconClassInput.value = iconClass;
        });
    }

    // Initialize icon pickers for both add and edit modals
    initializeIconPicker('#addServiceModal');
    initializeIconPicker('#editServiceModal');
});
</script>

<?php require_once('includes/footer.php'); ?> 