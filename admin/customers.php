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
            case 'delete':
                $id = intval($_POST['user_id']);
                
                // Begin transaction
                $conn->beginTransaction();
                try {
                    // Delete related appointments first
                    $stmt = $conn->prepare("DELETE FROM tbl_appointments WHERE user_id = ?");
                    $stmt->execute([$id]);
                    
                    // Then delete the user
                    $stmt = $conn->prepare("DELETE FROM tbl_user WHERE id = ?");
                    $stmt->execute([$id]);
                    
                    $conn->commit();
                    $_SESSION['success'] = "Customer deleted successfully!";
                } catch (Exception $e) {
                    $conn->rollBack();
                    $_SESSION['error'] = "Error deleting customer: " . $e->getMessage();
                }
                break;
        }
        header("Location: customers.php");
        exit();
    }
}

// Include header and navbar
require_once('includes/header.php');

// Fetch all customers with their appointment counts
$stmt = $conn->query("
    SELECT u.*, 
           COUNT(DISTINCT a.appointment_id) as total_appointments,
           SUM(CASE WHEN a.status = 'completed' THEN 1 ELSE 0 END) as completed_appointments,
           MAX(a.appointment_date) as last_appointment
    FROM tbl_user u
    LEFT JOIN tbl_appointments a ON u.id = a.user_id
    GROUP BY u.id
    ORDER BY u.name ASC
");
$customers = $stmt->fetchAll();
?>

<!-- Begin Page Content -->
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Customers Management</h1>
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

    <!-- Customers Table -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">All Customers</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="customersTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>Total Appointments</th>
                            <th>Completed Services</th>
                            <th>Last Visit</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($customers as $customer): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($customer['id']); ?></td>
                                <td><?php echo htmlspecialchars($customer['name']); ?></td>
                                <td><?php echo htmlspecialchars($customer['email']); ?></td>
                                <td><?php echo htmlspecialchars($customer['phonenum']); ?></td>
                                <td>
                                    <span class="badge bg-primary">
                                        <?php echo $customer['total_appointments']; ?>
                                    </span>
                                </td>
                                <td>
                                    <span class="badge bg-success">
                                        <?php echo $customer['completed_appointments']; ?>
                                    </span>
                                </td>
                                <td>
                                    <?php
                                    echo $customer['last_appointment'] 
                                        ? date('M d, Y', strtotime($customer['last_appointment']))
                                        : 'Never';
                                    ?>
                                </td>
                                <td>
                                    <button class="btn btn-sm btn-info view-history"
                                            data-id="<?php echo $customer['id']; ?>"
                                            data-name="<?php echo htmlspecialchars($customer['name']); ?>"
                                            data-bs-toggle="modal"
                                            data-bs-target="#viewHistoryModal">
                                        <i class="fas fa-history"></i>
                                    </button>
                                    <button class="btn btn-sm btn-danger delete-customer"
                                            data-id="<?php echo $customer['id']; ?>"
                                            data-name="<?php echo htmlspecialchars($customer['name']); ?>"
                                            data-appointments="<?php echo $customer['total_appointments']; ?>"
                                            data-bs-toggle="modal"
                                            data-bs-target="#deleteCustomerModal">
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

<!-- View History Modal -->
<div class="modal fade" id="viewHistoryModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Appointment History - <span id="customer_name"></span></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="table-responsive">
                    <table class="table table-bordered" id="historyTable" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Service</th>
                                <th>Price</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody id="history_body">
                            <tr>
                                <td colspan="4" class="text-center">Loading...</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Delete Customer Modal -->
<div class="modal fade" id="deleteCustomerModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Delete Customer</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="customers.php" method="POST">
                <div class="modal-body">
                    <input type="hidden" name="action" value="delete">
                    <input type="hidden" name="user_id" id="delete_user_id">
                    <p>Are you sure you want to delete this customer?</p>
                    <p><strong>Name:</strong> <span id="delete_customer_name"></span></p>
                    <p><strong>Total Appointments:</strong> <span id="delete_appointments"></span></p>
                    <p class="text-danger">This will also delete all appointments associated with this customer!</p>
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
    const dataTable = new DataTable('#customersTable', {
        order: [[1, 'asc']], // Sort by name by default
        pageLength: 25, // Show 25 entries per page
        language: {
            search: "_INPUT_",
            searchPlaceholder: "Search customers..."
        }
    });

    // Get modal elements
    const viewHistoryModal = document.getElementById('viewHistoryModal');
    const deleteCustomerModal = document.getElementById('deleteCustomerModal');

    // Initialize modals
    const historyModal = new bootstrap.Modal(viewHistoryModal);
    const deleteModal = new bootstrap.Modal(deleteCustomerModal);

    // Handle modal hidden event to remove backdrop
    viewHistoryModal.addEventListener('hidden.bs.modal', function () {
        document.querySelector('.modal-backdrop')?.remove();
        document.body.classList.remove('modal-open');
        document.body.style.overflow = '';
        document.body.style.paddingRight = '';
    });

    deleteCustomerModal.addEventListener('hidden.bs.modal', function () {
        document.querySelector('.modal-backdrop')?.remove();
        document.body.classList.remove('modal-open');
        document.body.style.overflow = '';
        document.body.style.paddingRight = '';
    });

    // Handle view history button clicks
    document.querySelectorAll('.view-history').forEach(button => {
        button.addEventListener('click', function() {
            const userId = this.dataset.id;
            const userName = this.dataset.name;
            
            document.getElementById('customer_name').textContent = userName;
            document.getElementById('history_body').innerHTML = '<tr><td colspan="4" class="text-center">Loading...</td></tr>';
            
            // Show the modal
            historyModal.show();
            
            // Fetch appointment history
            fetch('ajax/get_customer_history.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ user_id: userId })
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(data => {
                let html = '';
                
                if (data.length === 0) {
                    html = '<tr><td colspan="4" class="text-center">No appointment history found</td></tr>';
                } else {
                    data.forEach(function(appointment) {
                        const statusClass = {
                            'pending': 'warning',
                            'confirmed': 'primary',
                            'completed': 'success',
                            'cancelled': 'danger'
                        }[appointment.status] || 'secondary';
                        
                        html += `
                            <tr>
                                <td>${appointment.appointment_date}</td>
                                <td>${appointment.service_name}</td>
                                <td>ETB ${parseFloat(appointment.price).toFixed(2)}</td>
                                <td><span class="badge bg-${statusClass}">${appointment.status}</span></td>
                            </tr>
                        `;
                    });
                }
                
                document.getElementById('history_body').innerHTML = html;
            })
            .catch(error => {
                console.error('Error:', error);
                document.getElementById('history_body').innerHTML = 
                    '<tr><td colspan="4" class="text-center text-danger">Error loading history: ' + error.message + '</td></tr>';
            });
        });
    });

    // Handle delete button clicks
    document.querySelectorAll('.delete-customer').forEach(button => {
        button.addEventListener('click', function() {
            document.getElementById('delete_user_id').value = this.dataset.id;
            document.getElementById('delete_customer_name').textContent = this.dataset.name;
            document.getElementById('delete_appointments').textContent = this.dataset.appointments;
            deleteModal.show();
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

<?php
require_once('includes/footer.php');
?> 