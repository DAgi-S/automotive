<?php
define('INCLUDED', true);
session_start();

// Debug information
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Check admin session
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

// Debug: Print admin information
echo "<!-- Debug: Admin ID = " . $_SESSION['admin_id'] . " -->";

// Include database connection
require_once('../config/database.php');
require_once('../includes/NotificationManager.php');

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        try {
            switch ($_POST['action']) {
                case 'update_status':
                    $id = intval($_POST['appointment_id']);
                    $status = htmlspecialchars($_POST['status']);
                    $admin_id = intval($_SESSION['admin_id']);
                    
                    // Begin transaction
                    $conn->beginTransaction();
                    
                    try {
                        // Update appointment status
                        $stmt = $conn->prepare("UPDATE tbl_appointments SET status = ? WHERE appointment_id = ?");
                        $stmt->execute([$status, $id]);
                        
                        // If status is completed, add to service history
                        if ($status === 'completed') {
                            // Get appointment details with vehicle information from tbl_info
                            $stmt = $conn->prepare("
                                SELECT 
                                    a.*,
                                    s.price as service_price,
                                    i.id as info_id,
                                    i.car_brand,
                                    i.car_model,
                                    i.car_year,
                                    i.plate_number
                                FROM tbl_appointments a
                                JOIN tbl_services s ON a.service_id = s.service_id
                                JOIN tbl_user u ON a.user_id = u.id
                                JOIN tbl_info i ON i.userid = u.id
                                WHERE a.appointment_id = ?
                                ORDER BY i.id DESC LIMIT 1
                            ");
                            $stmt->execute([$id]);
                            $appointment = $stmt->fetch(PDO::FETCH_ASSOC);
                            
                            if ($appointment && $appointment['info_id']) {
                                // Insert into service history
                                $stmt = $conn->prepare("
                                    INSERT INTO tbl_service_history 
                                    (info_id, service_id, service_date, notes, cost, created_at) 
                                    VALUES (?, ?, ?, ?, ?, NOW())
                                ");
                                
                                // Use info_id from tbl_info
                                $stmt->execute([
                                    $appointment['info_id'],
                                    $appointment['service_id'],
                                    $appointment['appointment_date'],
                                    $appointment['notes'] ?? sprintf(
                                        'Service completed for %s %s %s (Plate: %s)',
                                        $appointment['car_year'],
                                        $appointment['car_brand'],
                                        $appointment['car_model'],
                                        $appointment['plate_number']
                                    ),
                                    $appointment['service_price']
                                ]);
                            } else {
                                throw new Exception("Could not find vehicle information for this appointment. Please ensure the customer has vehicle information registered in their profile.");
                            }
                        }
                        
                        // Log the status change
                        $stmt = $conn->prepare("INSERT INTO activity_log (admin_id, activity_type, description) VALUES (?, ?, ?)");
                        $stmt->execute([$admin_id, 'appointment_update', "Updated appointment #$id status to $status"]);
                        
                        // Send notification to customer about status change
                        $notificationManager = new NotificationManager($conn);
                        
                        // Get appointment details for notification
                        $stmt = $conn->prepare("
                            SELECT a.*, u.name as customer_name, u.email as customer_email, 
                                   s.service_name, u.id as user_id
                            FROM tbl_appointments a
                            LEFT JOIN tbl_user u ON a.user_id = u.id
                            LEFT JOIN tbl_services s ON a.service_id = s.service_id
                            WHERE a.appointment_id = ?
                        ");
                        $stmt->execute([$id]);
                        $appointment_details = $stmt->fetch(PDO::FETCH_ASSOC);
                        
                        if ($appointment_details) {
                            $notification_data = [
                                'message' => "Your appointment for {$appointment_details['service_name']} has been {$status}",
                                'email' => $appointment_details['customer_email'],
                                'name' => $appointment_details['customer_name'],
                                'subject' => 'Appointment Status Update',
                                'template_key' => 'appointment_status_update',
                                'variables' => [
                                    'customer_name' => $appointment_details['customer_name'],
                                    'service_name' => $appointment_details['service_name'],
                                    'appointment_date' => $appointment_details['appointment_date'],
                                    'appointment_time' => $appointment_details['appointment_time'],
                                    'status' => ucfirst($status)
                                ]
                            ];
                            
                            $notificationManager->sendNotification('appointment', $appointment_details['user_id'], $notification_data, ['web']);
                            
                            // Also send notification to admin about the action
                            $admin_notification = [
                                'message' => "Appointment #{$id} status updated to {$status} for {$appointment_details['customer_name']}"
                            ];
                            
                            $notificationManager->sendNotification('system', $admin_id, $admin_notification, ['web']);
                        }
                        
                        $conn->commit();
                        $_SESSION['success'] = "Appointment status updated successfully!";
                    } catch (Exception $e) {
                        $conn->rollBack();
                        $_SESSION['error'] = "Error: " . $e->getMessage();
                    }
                    break;

                case 'delete':
                    $id = intval($_POST['appointment_id']);
                    $admin_id = intval($_SESSION['admin_id']);
                    
                    // Begin transaction
                    $conn->beginTransaction();
                    
                    // Get appointment details before deletion for logging
                    $stmt = $conn->prepare("SELECT appointment_id, user_id, service_id FROM tbl_appointments WHERE appointment_id = ?");
                    $stmt->execute([$id]);
                    $appointment = $stmt->fetch();
                    
                    if ($appointment) {
                        // Delete the appointment
                        $stmt = $conn->prepare("DELETE FROM tbl_appointments WHERE appointment_id = ?");
                        $stmt->execute([$id]);
                        
                        // Log the deletion
                        $stmt = $conn->prepare("INSERT INTO activity_log (admin_id, activity_type, description) VALUES (?, ?, ?)");
                        $stmt->execute([$admin_id, 'appointment_delete', "Deleted appointment #$id"]);
                        
                        $conn->commit();
                        $_SESSION['success'] = "Appointment deleted successfully!";
                    } else {
                        throw new Exception("Appointment not found");
                    }
                    break;
            }
        } catch (Exception $e) {
            $conn->rollBack();
            $_SESSION['error'] = "Error: " . $e->getMessage();
        }
        header("Location: appointments.php");
        exit();
    }
}

// Include header and navbar
require_once('includes/header.php');

try {
    // Fetch all appointments with user and service details
    $stmt = $conn->prepare("
        SELECT 
            a.*,
            a.appointment_time,
            u.name as customer_name,
            u.phonenum as customer_phone,
            u.email as customer_email,
            s.service_name,
            s.price,
            s.duration
        FROM tbl_appointments a
        LEFT JOIN tbl_user u ON a.user_id = u.id
        LEFT JOIN tbl_services s ON a.service_id = s.service_id
        ORDER BY a.appointment_date DESC, a.appointment_time DESC
    ");
    $stmt->execute();
    $appointments = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $_SESSION['error'] = "Error fetching appointments: " . $e->getMessage();
    $appointments = [];
}
?>

<!-- Begin Page Content -->
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Appointments Management</h1>
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

    <!-- Appointments Table -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">All Appointments</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="appointmentsTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Customer</th>
                            <th>Service</th>
                            <th>Date</th>
                            <th>Time</th>
                            <th>Price</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($appointments as $appointment): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($appointment['appointment_id']); ?></td>
                                <td>
                                    <?php echo htmlspecialchars($appointment['customer_name']); ?><br>
                                    <small class="text-muted">
                                        <i class="fas fa-phone"></i> <?php echo htmlspecialchars($appointment['customer_phone']); ?><br>
                                        <i class="fas fa-envelope"></i> <?php echo htmlspecialchars($appointment['customer_email']); ?>
                                    </small>
                                </td>
                                <td>
                                    <?php echo htmlspecialchars($appointment['service_name']); ?><br>
                                    <small class="text-muted"><?php echo htmlspecialchars($appointment['duration']); ?></small>
                                </td>
                                <td><?php echo date('M d, Y', strtotime($appointment['appointment_date'])); ?></td>
                                <td><?php 
                                    if (isset($appointment['appointment_time']) && $appointment['appointment_time'] !== null) {
                                        try {
                                            $time = new DateTime($appointment['appointment_time']);
                                            echo $time->format('h:i A');
                                        } catch (Exception $e) {
                                            echo 'N/A';
                                        }
                                    } else {
                                        echo 'N/A';
                                    }
                                ?></td>
                                <td>ETB <?php echo number_format($appointment['price'], 2); ?></td>
                                <td>
                                    <?php
                                    $statusClass = [
                                        'pending' => 'warning',
                                        'confirmed' => 'primary',
                                        'completed' => 'success',
                                        'cancelled' => 'danger'
                                    ];
                                    $class = $statusClass[$appointment['status']] ?? 'secondary';
                                    ?>
                                    <span class="badge bg-<?php echo $class; ?>">
                                        <?php echo ucfirst(htmlspecialchars($appointment['status'])); ?>
                                    </span>
                                </td>
                                <td>
                                    <button class="btn btn-sm btn-info update-status" 
                                            data-bs-toggle="modal" 
                                            data-bs-target="#updateStatusModal"
                                            data-id="<?php echo $appointment['appointment_id']; ?>"
                                            data-status="<?php echo htmlspecialchars($appointment['status']); ?>"
                                            data-customer="<?php echo htmlspecialchars($appointment['customer_name']); ?>"
                                            data-service="<?php echo htmlspecialchars($appointment['service_name']); ?>">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button class="btn btn-sm btn-danger delete-appointment"
                                            data-bs-toggle="modal"
                                            data-bs-target="#deleteAppointmentModal"
                                            data-id="<?php echo $appointment['appointment_id']; ?>"
                                            data-customer="<?php echo htmlspecialchars($appointment['customer_name']); ?>"
                                            data-service="<?php echo htmlspecialchars($appointment['service_name']); ?>">
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

<!-- Update Status Modal -->
<div class="modal fade" id="updateStatusModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Update Appointment Status</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="appointments.php" method="POST" id="updateStatusForm">
                <div class="modal-body">
                    <input type="hidden" name="action" value="update_status">
                    <input type="hidden" name="appointment_id" id="update_appointment_id">
                    <p>Update status for appointment:</p>
                    <p><strong>Customer:</strong> <span id="update_customer"></span></p>
                    <p><strong>Service:</strong> <span id="update_service"></span></p>
                    <div class="mb-3">
                        <label class="form-label">Status</label>
                        <select class="form-select" name="status" id="update_status" required>
                            <option value="pending">Pending</option>
                            <option value="confirmed">Confirmed</option>
                            <option value="completed">Completed</option>
                            <option value="cancelled">Cancelled</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Update Status</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Delete Appointment Modal -->
<div class="modal fade" id="deleteAppointmentModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Delete Appointment</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="appointments.php" method="POST" id="deleteAppointmentForm">
                <div class="modal-body">
                    <input type="hidden" name="action" value="delete">
                    <input type="hidden" name="appointment_id" id="delete_appointment_id">
                    <p>Are you sure you want to delete this appointment?</p>
                    <p><strong>Customer:</strong> <span id="delete_customer"></span></p>
                    <p><strong>Service:</strong> <span id="delete_service"></span></p>
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

<style>
/* DataTables custom styling for admin theme */
.dataTables_wrapper .dataTables_filter input {
    border: 1px solid #d1d3e2;
    border-radius: 0.35rem;
    padding: 0.375rem 0.75rem;
    font-size: 0.875rem;
}

.dataTables_wrapper .dataTables_length select {
    border: 1px solid #d1d3e2;
    border-radius: 0.35rem;
    padding: 0.375rem 0.75rem;
    font-size: 0.875rem;
}

.dataTables_wrapper .dataTables_info {
    font-size: 0.875rem;
    color: #5a5c69;
}

.dataTables_wrapper .dataTables_paginate .paginate_button {
    border: 1px solid #d1d3e2;
    border-radius: 0.35rem;
    margin: 0 0.125rem;
    padding: 0.375rem 0.75rem;
    font-size: 0.875rem;
}

.dataTables_wrapper .dataTables_paginate .paginate_button.current {
    background: #4e73df;
    color: white !important;
    border-color: #4e73df;
}

.dataTables_wrapper .dataTables_paginate .paginate_button:hover {
    background: #f8f9fc;
    border-color: #4e73df;
    color: #4e73df !important;
}

.table-responsive .table {
    font-size: 0.875rem;
}

.badge {
    font-size: 0.75rem;
    padding: 0.375rem 0.5rem;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Wait for jQuery and DataTables to be fully loaded
    if (typeof jQuery !== 'undefined' && jQuery.fn.DataTable) {
        // Initialize DataTable
        $('#appointmentsTable').DataTable({
            order: [[3, 'desc'], [4, 'desc']], // Sort by date and time
            pageLength: 25,
            responsive: true,
            language: {
                search: "_INPUT_",
                searchPlaceholder: "Search appointments..."
            },
            columnDefs: [
                {
                    targets: [7], // Actions column
                    orderable: false,
                    searchable: false
                }
            ],
            dom: '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>' +
                 '<"row"<"col-sm-12"tr>>' +
                 '<"row"<"col-sm-12 col-md-5"i><"col-sm-12 col-md-7"p>>',
            drawCallback: function() {
                // Reinitialize event listeners after table redraw
                initializeEventListeners();
            }
        });
    } else {
        console.error('DataTables not loaded properly');
    }
    
    // Initialize event listeners
    initializeEventListeners();
    
    function initializeEventListeners() {

        // Handle status update button clicks
        document.querySelectorAll('.update-status').forEach(button => {
            button.addEventListener('click', function() {
                const id = this.dataset.id;
                const status = this.dataset.status;
                const customer = this.dataset.customer;
                const service = this.dataset.service;
                
                document.getElementById('update_appointment_id').value = id;
                document.getElementById('update_status').value = status;
                document.getElementById('update_customer').textContent = customer;
                document.getElementById('update_service').textContent = service;
            });
        });

        // Handle delete button clicks
        document.querySelectorAll('.delete-appointment').forEach(button => {
            button.addEventListener('click', function() {
                const id = this.dataset.id;
                const customer = this.dataset.customer;
                const service = this.dataset.service;
                
                document.getElementById('delete_appointment_id').value = id;
                document.getElementById('delete_customer').textContent = customer;
                document.getElementById('delete_service').textContent = service;
            });
        });
    }

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