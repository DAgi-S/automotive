<?php
// Start session at the very beginning
session_start();

// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

// Include necessary files
require_once('../config/database.php');
include('includes/header.php');
include('includes/navbar.php');

// Define notification types
$notificationTypes = [
    'service_booking' => 'Service Bookings',
    'appointment_update' => 'Appointment Updates',
    'appointment_cancel' => 'Appointment Cancellations',
    'worker_assigned' => 'Worker Assignments',
    'service_complete' => 'Service Completions',
    'payment_received' => 'Payment Notifications'
];

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $admin_id = $_SESSION['admin_id'];
        
        // Begin transaction
        $conn->begin_transaction();
        
        // Delete existing preferences
        $delete_stmt = $conn->prepare("DELETE FROM tbl_notification_preferences WHERE admin_id = ?");
        $delete_stmt->bind_param("i", $admin_id);
        $delete_stmt->execute();
        
        // Insert new preferences
        $insert_stmt = $conn->prepare(
            "INSERT INTO tbl_notification_preferences 
             (admin_id, notification_type, is_enabled, email_enabled, sound_enabled) 
             VALUES (?, ?, ?, ?, ?)"
        );
        
        foreach ($notificationTypes as $type => $label) {
            $is_enabled = isset($_POST["enabled_$type"]) ? 1 : 0;
            $email_enabled = isset($_POST["email_$type"]) ? 1 : 0;
            $sound_enabled = isset($_POST["sound_$type"]) ? 1 : 0;
            
            $insert_stmt->bind_param("isiii", $admin_id, $type, $is_enabled, $email_enabled, $sound_enabled);
            $insert_stmt->execute();
        }
        
        // Commit transaction
        $conn->commit();
        
        $success_message = "Notification preferences updated successfully!";
    } catch (Exception $e) {
        $conn->rollback();
        $error_message = "Error updating preferences: " . $e->getMessage();
    }
}

// Get current preferences
$preferences = [];
$stmt = $conn->prepare(
    "SELECT notification_type, is_enabled, email_enabled, sound_enabled 
     FROM tbl_notification_preferences 
     WHERE admin_id = ?"
);
$stmt->bind_param("i", $_SESSION['admin_id']);
$stmt->execute();
$result = $stmt->get_result();

while ($row = $result->fetch_assoc()) {
    $preferences[$row['notification_type']] = $row;
}
?>

<div class="container-fluid">
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">Notification Preferences</h6>
        </div>
        <div class="card-body">
            <?php if (isset($success_message)): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <?php echo $success_message; ?>
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            <?php endif; ?>

            <?php if (isset($error_message)): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <?php echo $error_message; ?>
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            <?php endif; ?>

            <form method="POST" action="">
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Notification Type</th>
                                <th class="text-center">Enable</th>
                                <th class="text-center">Email</th>
                                <th class="text-center">Sound</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($notificationTypes as $type => $label): ?>
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <i class="fas <?php echo getNotificationIcon($type); ?> mr-2"></i>
                                            <?php echo $label; ?>
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        <div class="custom-control custom-switch">
                                            <input type="checkbox" 
                                                   class="custom-control-input" 
                                                   id="enabled_<?php echo $type; ?>" 
                                                   name="enabled_<?php echo $type; ?>"
                                                   <?php echo (!isset($preferences[$type]) || $preferences[$type]['is_enabled']) ? 'checked' : ''; ?>>
                                            <label class="custom-control-label" for="enabled_<?php echo $type; ?>"></label>
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        <div class="custom-control custom-switch">
                                            <input type="checkbox" 
                                                   class="custom-control-input" 
                                                   id="email_<?php echo $type; ?>" 
                                                   name="email_<?php echo $type; ?>"
                                                   <?php echo (isset($preferences[$type]) && $preferences[$type]['email_enabled']) ? 'checked' : ''; ?>>
                                            <label class="custom-control-label" for="email_<?php echo $type; ?>"></label>
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        <div class="custom-control custom-switch">
                                            <input type="checkbox" 
                                                   class="custom-control-input" 
                                                   id="sound_<?php echo $type; ?>" 
                                                   name="sound_<?php echo $type; ?>"
                                                   <?php echo (!isset($preferences[$type]) || $preferences[$type]['sound_enabled']) ? 'checked' : ''; ?>>
                                            <label class="custom-control-label" for="sound_<?php echo $type; ?>"></label>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <div class="mt-3">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save mr-1"></i> Save Preferences
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
.custom-switch {
    padding-left: 2.25rem;
}

.custom-control {
    min-height: 1.5rem;
}

.table td {
    vertical-align: middle;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Enable/disable related checkboxes when main toggle is changed
    document.querySelectorAll('[id^="enabled_"]').forEach(toggle => {
        toggle.addEventListener('change', function() {
            const type = this.id.replace('enabled_', '');
            const emailToggle = document.getElementById(`email_${type}`);
            const soundToggle = document.getElementById(`sound_${type}`);
            
            if (!this.checked) {
                emailToggle.checked = false;
                soundToggle.checked = false;
            }
            
            emailToggle.disabled = !this.checked;
            soundToggle.disabled = !this.checked;
        });
        
        // Initial state
        toggle.dispatchEvent(new Event('change'));
    });
});
</script>

<?php
function getNotificationIcon($type) {
    $icons = [
        'service_booking' => 'fa-calendar-plus',
        'appointment_update' => 'fa-sync',
        'appointment_cancel' => 'fa-calendar-times',
        'worker_assigned' => 'fa-user-check',
        'service_complete' => 'fa-check-circle',
        'payment_received' => 'fa-dollar-sign'
    ];
    return $icons[$type] ?? 'fa-bell';
}

include('includes/footer.php');
?> 