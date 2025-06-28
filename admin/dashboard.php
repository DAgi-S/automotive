<?php
define('INCLUDED', true);
// Start session at the very beginning before any output
session_start();

// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

// Include database connection and functions
require_once('../config/database.php');
require_once('includes/dashboard_functions.php');

// Get total counts
try {
    $stats = $conn->query("SELECT 
        (SELECT COUNT(*) FROM tbl_appointments) as total_appointments,
        (SELECT COUNT(*) FROM tbl_user) as total_customers,
        (SELECT COUNT(*) FROM tbl_services) as total_services,
        (SELECT COUNT(*) FROM tbl_worker) as total_workers")->fetch(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $stats = [
        'total_appointments' => 0,
        'total_customers' => 0,
        'total_services' => 0,
        'total_workers' => 0
    ];
}

// Get recent appointments with customer and service details
try {
    $appointments = $conn->query("
        SELECT 
            a.appointment_id,
            u.name as customer_name,
            s.service_name,
            a.appointment_date,
            a.status
        FROM tbl_appointments a
        LEFT JOIN tbl_user u ON a.user_id = u.id
        LEFT JOIN tbl_services s ON a.service_id = s.service_id
        ORDER BY a.appointment_date DESC
        LIMIT 5
    ")->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $appointments = [];
}

// Get recent activities
try {
    $activities = $conn->query("
        SELECT 
            activity_type,
            description,
            created_at
        FROM activity_log
        ORDER BY created_at DESC
        LIMIT 5
    ")->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $activities = [];
}

// Include header
require_once('includes/header.php');
?>

        <!-- Page Heading -->
        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h1 class="h3 mb-0 text-gray-800">Dashboard</h1>
            <div>
                <a href="#" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm">
                    <i class="fas fa-download fa-sm text-white-50"></i> Generate Report
                </a>
            </div>
        </div>

        <!-- Statistics Cards -->
        <div class="row mb-4">
            <div class="col-xl-3 col-md-6">
                <div class="card bg-white shadow-sm">
                    <div class="card-body">
                        <h6 class="text-primary">TOTAL APPOINTMENTS</h6>
                        <h2><?php echo $stats['total_appointments']; ?></h2>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6">
                <div class="card bg-white shadow-sm">
                    <div class="card-body">
                        <h6 class="text-success">TOTAL CUSTOMERS</h6>
                        <h2><?php echo $stats['total_customers']; ?></h2>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6">
                <div class="card bg-white shadow-sm">
                    <div class="card-body">
                        <h6 class="text-info">TOTAL SERVICES</h6>
                        <h2><?php echo $stats['total_services']; ?></h2>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6">
                <div class="card bg-white shadow-sm">
                    <div class="card-body">
                        <h6 class="text-warning">TOTAL WORKERS</h6>
                        <h2><?php echo $stats['total_workers']; ?></h2>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Appointments and Activities -->
        <div class="row">
            <div class="col-xl-8">
                <div class="card shadow-sm mb-4">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h6 class="m-0 font-weight-bold text-primary">Recent Appointments</h6>
                        <a href="appointments.php" class="btn btn-sm btn-primary">View All</a>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>CUSTOMER</th>
                                        <th>SERVICE</th>
                                        <th>DATE</th>
                                        <th>STATUS</th>
                                        <th>ACTIONS</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($appointments as $appointment): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($appointment['customer_name']); ?></td>
                                        <td><?php echo htmlspecialchars($appointment['service_name']); ?></td>
                                        <td><?php echo date('M d, Y', strtotime($appointment['appointment_date'])); ?></td>
                                        <td>
                                            <span class="badge badge-<?php 
                                                echo match($appointment['status']) {
                                                    'pending' => 'warning',
                                                    'confirmed' => 'info',
                                                    'completed' => 'success',
                                                    'cancelled' => 'danger',
                                                    default => 'secondary'
                                                };
                                            ?>">
                                                <?php echo ucfirst($appointment['status']); ?>
                                            </span>
                                        </td>
                                        <td>
                                            <button class="btn btn-sm btn-primary" onclick="updateStatus(<?php echo $appointment['appointment_id']; ?>)">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button class="btn btn-sm btn-danger" onclick="deleteAppointment(<?php echo $appointment['appointment_id']; ?>)">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                    <?php if (empty($appointments)): ?>
                                    <tr>
                                        <td colspan="5" class="text-center">No recent appointments</td>
                                    </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-4">
                <div class="card shadow-sm mb-4">
                    <div class="card-header">
                        <h6 class="m-0 font-weight-bold text-primary">Recent Activities</h6>
                    </div>
                    <div class="card-body">
                        <?php if (!empty($activities)): ?>
                            <div class="timeline">
                                <?php foreach ($activities as $activity): ?>
                                <div class="timeline-item">
                                    <div class="timeline-content">
                                        <p class="mb-1">
                                            <strong><?php echo ucfirst(str_replace('_', ' ', $activity['activity_type'])); ?>:</strong>
                                            <?php echo htmlspecialchars($activity['description']); ?>
                                        </p>
                                        <small class="text-muted">
                                            <?php echo date('M d, Y H:i', strtotime($activity['created_at'])); ?>
                                        </small>
                                    </div>
                                </div>
                                <?php endforeach; ?>
                            </div>
                        <?php else: ?>
                            <p class="text-center mb-0">No recent activities</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

<!-- Status Update Modal -->
<div class="modal fade" id="statusModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Update Appointment Status</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="appointmentId">
                <div class="mb-3">
                    <label for="status" class="form-label">Status</label>
                    <select class="form-select" id="status">
                        <option value="pending">Pending</option>
                        <option value="confirmed">Confirmed</option>
                        <option value="completed">Completed</option>
                        <option value="cancelled">Cancelled</option>
                    </select>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="saveStatus()">Update</button>
            </div>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" role="dialog" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteModalLabel">Delete Appointment</h5>
                <button class="close" type="button" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="modal-body">
                Are you sure you want to delete this appointment? This action cannot be undone.
                <input type="hidden" id="deleteAppointmentId">
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" type="button" data-bs-dismiss="modal">Cancel</button>
                <button class="btn btn-danger" type="button" onclick="confirmDelete()">Delete</button>
            </div>
        </div>
    </div>
</div>

<!-- Hidden Delete Form -->
<form id="deleteAppointmentForm" action="appointments.php" method="POST" style="display: none;">
    <input type="hidden" name="action" value="delete">
    <input type="hidden" name="appointment_id" id="deleteAppointmentId">
</form>

<script>
function updateStatus(appointmentId) {
    document.getElementById('appointmentId').value = appointmentId;
    var statusModal = new bootstrap.Modal(document.getElementById('statusModal'));
    statusModal.show();
}

function saveStatus() {
    const appointmentId = document.getElementById('appointmentId').value;
    const status = document.getElementById('status').value;
    
    fetch('api/appointments/update-status.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            id: appointmentId,
            status: status
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Close modal
            bootstrap.Modal.getInstance(document.getElementById('statusModal')).hide();
            // Reload page to show updated status
            location.reload();
        } else {
            alert('Error updating status: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error updating status. Please try again.');
    });
}

function deleteAppointment(appointmentId) {
    if (confirm('Are you sure you want to delete this appointment?')) {
        document.getElementById('deleteAppointmentId').value = appointmentId;
        // Submit the delete form
        document.getElementById('deleteAppointmentForm').submit();
    }
}

// Initialize tooltips
document.addEventListener('DOMContentLoaded', function() {
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
});
</script>

<?php require_once('includes/footer.php'); ?> 