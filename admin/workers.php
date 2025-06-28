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
        // Handle file upload
        $image_url = null;
        if (isset($_FILES['worker_image']) && $_FILES['worker_image']['error'] === UPLOAD_ERR_OK) {
            $file_info = pathinfo($_FILES['worker_image']['name']);
            $file_extension = strtolower($file_info['extension']);
            
            // Validate file type
            $allowed_types = ['jpg', 'jpeg', 'png', 'gif'];
            if (in_array($file_extension, $allowed_types)) {
                $new_filename = uniqid() . '.' . $file_extension;
                $upload_path = 'uploads/workers/' . $new_filename;
                
                if (move_uploaded_file($_FILES['worker_image']['tmp_name'], $upload_path)) {
                    $image_url = $upload_path;
                } else {
                    $_SESSION['error'] = "Error uploading image file.";
                    header("Location: workers.php");
                    exit();
                }
            } else {
                $_SESSION['error'] = "Invalid file type. Allowed types: " . implode(', ', $allowed_types);
                header("Location: workers.php");
                exit();
            }
        }

        switch ($_POST['action']) {
            case 'add':
                try {
                    $stmt = $conn->prepare("INSERT INTO tbl_worker (full_name, username, password, position, image_url, created_at) VALUES (?, ?, ?, ?, ?, NOW())");
                    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
                    $stmt->execute([
                        $_POST['full_name'],
                        $_POST['username'],
                        $password,
                        'worker',
                        $image_url
                    ]);
                    $_SESSION['success'] = "Worker added successfully!";
                } catch (PDOException $e) {
                    $_SESSION['error'] = "Error adding worker: " . $e->getMessage();
                }
                break;

            case 'update':
                try {
                    // If a new image is uploaded, update the image_url
                    if ($image_url === null && isset($_POST['current_image_url'])) {
                        $image_url = $_POST['current_image_url'];
                    }
                    
                    if (!empty($_POST['password'])) {
                        $stmt = $conn->prepare("UPDATE tbl_worker SET full_name = ?, username = ?, password = ?, image_url = ? WHERE id = ?");
                        $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
                        $stmt->execute([
                            $_POST['full_name'],
                            $_POST['username'],
                            $password,
                            $image_url,
                            $_POST['worker_id']
                        ]);
                    } else {
                        $stmt = $conn->prepare("UPDATE tbl_worker SET full_name = ?, username = ?, image_url = ? WHERE id = ?");
                        $stmt->execute([
                            $_POST['full_name'],
                            $_POST['username'],
                            $image_url,
                            $_POST['worker_id']
                        ]);
                    }
                    $_SESSION['success'] = "Worker updated successfully!";
                } catch (PDOException $e) {
                    $_SESSION['error'] = "Error updating worker: " . $e->getMessage();
                }
                break;

            case 'delete':
                try {
                    $stmt = $conn->prepare("DELETE FROM tbl_worker WHERE id = ?");
                    $stmt->execute([$_POST['worker_id']]);
                    $_SESSION['success'] = "Worker deleted successfully!";
                } catch (PDOException $e) {
                    $_SESSION['error'] = "Error deleting worker: " . $e->getMessage();
                }
                break;
        }
        header("Location: workers.php");
        exit();
    }
}

// Fetch all workers
try {
    $stmt = $conn->query("
        SELECT *
        FROM tbl_worker
        ORDER BY full_name ASC
    ");
    $workers = $stmt->fetchAll();
} catch (PDOException $e) {
    $_SESSION['error'] = "Error fetching workers: " . $e->getMessage();
    $workers = [];
}

// Include header
require_once('includes/header.php');
?>

<!-- Begin Page Content -->
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Workers Management</h1>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addWorkerModal">
            <i class="fas fa-plus"></i> Add New Worker
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

    <!-- Workers Table -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">All Workers</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="workersTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Username</th>
                            <th>Position</th>
                            <th>Image</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($workers as $worker): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($worker['id']); ?></td>
                                <td><?php echo htmlspecialchars($worker['full_name']); ?></td>
                                <td><?php echo htmlspecialchars($worker['username']); ?></td>
                                <td><?php echo ucfirst(htmlspecialchars($worker['position'])); ?></td>
                                <td>
                                    <?php if ($worker['image_url']): ?>
                                        <img src="<?php echo htmlspecialchars($worker['image_url']); ?>" 
                                             alt="Worker Image" 
                                             class="img-thumbnail"
                                             style="max-width: 50px;">
                                    <?php else: ?>
                                        <span class="text-muted">No image</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <button class="btn btn-sm btn-info edit-worker"
                                            data-id="<?php echo $worker['id']; ?>"
                                            data-name="<?php echo htmlspecialchars($worker['full_name']); ?>"
                                            data-username="<?php echo htmlspecialchars($worker['username']); ?>"
                                            data-image="<?php echo htmlspecialchars($worker['image_url'] ?? ''); ?>"
                                            data-bs-toggle="modal"
                                            data-bs-target="#editWorkerModal">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button class="btn btn-sm btn-danger delete-worker"
                                            data-id="<?php echo $worker['id']; ?>"
                                            data-name="<?php echo htmlspecialchars($worker['full_name']); ?>"
                                            data-bs-toggle="modal"
                                            data-bs-target="#deleteWorkerModal">
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

<!-- Add Worker Modal -->
<div class="modal fade" id="addWorkerModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add New Worker</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="workers.php" method="POST" enctype="multipart/form-data">
                <div class="modal-body">
                    <input type="hidden" name="action" value="add">
                    <div class="mb-3">
                        <label for="full_name" class="form-label">Full Name</label>
                        <input type="text" class="form-control" id="full_name" name="full_name" required>
                    </div>
                    <div class="mb-3">
                        <label for="username" class="form-label">Username</label>
                        <input type="text" class="form-control" id="username" name="username" required>
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">Password</label>
                        <input type="password" class="form-control" id="password" name="password" required>
                    </div>
                    <div class="mb-3">
                        <label for="worker_image" class="form-label">Profile Image</label>
                        <input type="file" class="form-control" id="worker_image" name="worker_image" accept="image/*">
                        <small class="text-muted">Allowed types: JPG, JPEG, PNG, GIF</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Add Worker</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Worker Modal -->
<div class="modal fade" id="editWorkerModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Worker</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="workers.php" method="POST" enctype="multipart/form-data">
                <div class="modal-body">
                    <input type="hidden" name="action" value="update">
                    <input type="hidden" name="worker_id" id="edit_worker_id">
                    <input type="hidden" name="current_image_url" id="edit_current_image_url">
                    <div class="mb-3">
                        <label for="edit_full_name" class="form-label">Full Name</label>
                        <input type="text" class="form-control" id="edit_full_name" name="full_name" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit_username" class="form-label">Username</label>
                        <input type="text" class="form-control" id="edit_username" name="username" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit_password" class="form-label">Password (leave blank to keep current)</label>
                        <input type="password" class="form-control" id="edit_password" name="password">
                    </div>
                    <div class="mb-3">
                        <label for="edit_worker_image" class="form-label">Profile Image</label>
                        <div id="current_image_preview" class="mb-2"></div>
                        <input type="file" class="form-control" id="edit_worker_image" name="worker_image" accept="image/*">
                        <small class="text-muted">Allowed types: JPG, JPEG, PNG, GIF</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update Worker</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Delete Worker Modal -->
<div class="modal fade" id="deleteWorkerModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Delete Worker</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="workers.php" method="POST">
                <div class="modal-body">
                    <input type="hidden" name="action" value="delete">
                    <input type="hidden" name="worker_id" id="delete_worker_id">
                    <p>Are you sure you want to delete this worker?</p>
                    <p><strong>Name:</strong> <span id="delete_worker_name"></span></p>
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
    // Initialize DataTable
    const dataTable = new DataTable('#workersTable', {
        order: [[1, 'asc']], // Sort by name by default
        pageLength: 25, // Show 25 entries per page
        language: {
            search: "_INPUT_",
            searchPlaceholder: "Search workers..."
        }
    });

    // Get modal elements
    const addWorkerModal = document.getElementById('addWorkerModal');
    const editWorkerModal = document.getElementById('editWorkerModal');
    const deleteWorkerModal = document.getElementById('deleteWorkerModal');

    // Initialize modals
    const addModal = new bootstrap.Modal(addWorkerModal);
    const editModal = new bootstrap.Modal(editWorkerModal);
    const deleteModal = new bootstrap.Modal(deleteWorkerModal);

    // Handle modal hidden events to remove backdrop
    [addWorkerModal, editWorkerModal, deleteWorkerModal].forEach(modal => {
        modal.addEventListener('hidden.bs.modal', function () {
            document.querySelector('.modal-backdrop')?.remove();
            document.body.classList.remove('modal-open');
            document.body.style.overflow = '';
            document.body.style.paddingRight = '';
        });
    });

    // Handle edit button clicks
    document.querySelectorAll('.edit-worker').forEach(button => {
        button.addEventListener('click', function() {
            document.getElementById('edit_worker_id').value = this.dataset.id;
            document.getElementById('edit_full_name').value = this.dataset.name;
            document.getElementById('edit_username').value = this.dataset.username;
            document.getElementById('edit_current_image_url').value = this.dataset.image;
            document.getElementById('edit_password').value = ''; // Clear password field
            
            // Update image preview
            const imagePreview = document.getElementById('current_image_preview');
            if (this.dataset.image) {
                imagePreview.innerHTML = `
                    <img src="${this.dataset.image}" 
                         alt="Current Profile Image" 
                         class="img-thumbnail"
                         style="max-width: 150px;">
                    <p class="text-muted mt-1">Current image</p>
                `;
            } else {
                imagePreview.innerHTML = '<p class="text-muted">No current image</p>';
            }
        });
    });

    // Handle delete button clicks
    document.querySelectorAll('.delete-worker').forEach(button => {
        button.addEventListener('click', function() {
            document.getElementById('delete_worker_id').value = this.dataset.id;
            document.getElementById('delete_worker_name').textContent = this.dataset.name;
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