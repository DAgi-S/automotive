<?php
session_start();
require_once '../includes/config.php';

// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit();
}

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'update_email_settings':
                // Update email settings
                $stmt = $conn->prepare("INSERT INTO system_settings (setting_key, setting_value) VALUES (?, ?) 
                                      ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value)");
                
                $settings = [
                    'smtp_host' => $_POST['smtp_host'],
                    'smtp_port' => $_POST['smtp_port'],
                    'smtp_username' => $_POST['smtp_username'],
                    'smtp_password' => $_POST['smtp_password'],
                    'smtp_encryption' => $_POST['smtp_encryption'],
                    'from_email' => $_POST['from_email'],
                    'from_name' => $_POST['from_name']
                ];

                foreach ($settings as $key => $value) {
                    $stmt->execute([$key, $value]);
                }
                $_SESSION['success'] = "Email settings updated successfully!";
                break;

            case 'update_notification_settings':
                // Update notification settings
                $stmt = $conn->prepare("INSERT INTO system_settings (setting_key, setting_value) VALUES (?, ?) 
                                      ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value)");
                
                $notifications = [
                    'notify_new_appointment' => isset($_POST['notify_new_appointment']) ? '1' : '0',
                    'notify_appointment_status' => isset($_POST['notify_appointment_status']) ? '1' : '0',
                    'notify_new_order' => isset($_POST['notify_new_order']) ? '1' : '0',
                    'notify_low_stock' => isset($_POST['notify_low_stock']) ? '1' : '0'
                ];

                foreach ($notifications as $key => $value) {
                    $stmt->execute([$key, $value]);
                }
                $_SESSION['success'] = "Notification settings updated successfully!";
                break;

            case 'add_role':
                // Add new role
                $stmt = $conn->prepare("INSERT INTO roles (role_name) VALUES (?)");
                $stmt->execute([$_POST['role_name']]);
                $_SESSION['success'] = "Role added successfully!";
                break;

            case 'update_role_permissions':
                // Update role permissions
                $role_id = $_POST['role_id'];
                $permissions = isset($_POST['permissions']) ? $_POST['permissions'] : [];

                // First, delete existing permissions for this role
                $stmt = $conn->prepare("DELETE FROM role_permissions WHERE role_id = ?");
                $stmt->execute([$role_id]);

                // Then insert new permissions
                if (!empty($permissions)) {
                    $stmt = $conn->prepare("INSERT INTO role_permissions (role_id, permission_id) VALUES (?, ?)");
                    foreach ($permissions as $permission_id) {
                        $stmt->execute([$role_id, $permission_id]);
                    }
                }
                $_SESSION['success'] = "Role permissions updated successfully!";
                break;

            case 'update_telegram_settings':
                // Update telegram settings
                $stmt = $conn->prepare("INSERT INTO system_settings (setting_key, setting_value) VALUES (?, ?) 
                                      ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value)");
                
                $settings = [
                    'telegram_bot_token' => $_POST['telegram_bot_token'],
                    'telegram_chat_id' => $_POST['telegram_chat_id'],
                    'telegram_enabled' => isset($_POST['telegram_enabled']) ? '1' : '0',
                    'telegram_webhook_url' => $_POST['telegram_webhook_url'],
                    'telegram_notifications' => isset($_POST['telegram_notifications']) ? implode(',', $_POST['telegram_notifications']) : ''
                ];

                foreach ($settings as $key => $value) {
                    $stmt->execute([$key, $value]);
                }
                $_SESSION['success'] = "Telegram settings updated successfully!";
                break;

            case 'update_general_settings':
                // Update general settings
                $stmt = $conn->prepare("INSERT INTO system_settings (setting_key, setting_value) VALUES (?, ?) 
                                      ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value)");
                
                $settings = [
                    'site_name' => $_POST['site_name'],
                    'site_description' => $_POST['site_description'],
                    'maintenance_mode' => isset($_POST['maintenance_mode']) ? '1' : '0',
                    'timezone' => $_POST['timezone']
                ];

                foreach ($settings as $key => $value) {
                    $stmt->execute([$key, $value]);
                }
                $_SESSION['success'] = "General settings updated successfully!";
                break;
        }
        header('Location: settings.php');
        exit();
    }
}

// Fetch current settings
function getSetting($conn, $key, $default = '') {
    $stmt = $conn->prepare("SELECT setting_value FROM system_settings WHERE setting_key = ?");
    $stmt->execute([$key]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    return $result ? $result['setting_value'] : $default;
}

// Fetch roles and permissions
$roles = $conn->query("SELECT * FROM roles ORDER BY role_name")->fetchAll(PDO::FETCH_ASSOC);
$permissions = $conn->query("SELECT * FROM permissions ORDER BY permission_name")->fetchAll(PDO::FETCH_ASSOC);

include 'includes/header.php';
?>

<div class="container-fluid">
    <h1 class="h3 mb-4 text-gray-800">Settings</h1>

    <?php if (isset($_SESSION['success'])): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?php 
            echo $_SESSION['success'];
            unset($_SESSION['success']);
            ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <ul class="nav nav-pills nav-fill" role="tablist">
                <li class="nav-item">
                    <a class="nav-link active" id="email-tab" data-bs-toggle="pill" href="#email" role="tab">
                        <i class="fas fa-envelope me-2"></i>Email Settings
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="notifications-tab" data-bs-toggle="pill" href="#notifications" role="tab">
                        <i class="fas fa-bell me-2"></i>Notifications
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="roles-tab" data-bs-toggle="pill" href="#roles" role="tab">
                        <i class="fas fa-users-cog me-2"></i>Roles & Permissions
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="telegram-tab" data-bs-toggle="pill" href="#telegram" role="tab">
                        <i class="fab fa-telegram-plane me-2"></i>Telegram Settings
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="general-tab" data-bs-toggle="pill" href="#general" role="tab">
                        <i class="fas fa-cog me-2"></i>General Settings
                    </a>
                </li>
            </ul>
        </div>
        <div class="card-body">
            <div class="tab-content" id="settingsTabContent">
                <!-- Email Settings Tab -->
                <div class="tab-pane fade show active" id="email" role="tabpanel" aria-labelledby="email-tab">
                    <h5 class="mb-4">Email Configuration</h5>
                    <form method="POST">
                        <input type="hidden" name="action" value="update_email_settings">
                        <div class="mb-3">
                            <label class="form-label">SMTP Host</label>
                            <input type="text" class="form-control" name="smtp_host" value="<?php echo htmlspecialchars(getSetting($conn, 'smtp_host')); ?>" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">SMTP Port</label>
                            <input type="number" class="form-control" name="smtp_port" value="<?php echo htmlspecialchars(getSetting($conn, 'smtp_port', '587')); ?>" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">SMTP Username</label>
                            <input type="text" class="form-control" name="smtp_username" value="<?php echo htmlspecialchars(getSetting($conn, 'smtp_username')); ?>" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">SMTP Password</label>
                            <input type="password" class="form-control" name="smtp_password" value="<?php echo htmlspecialchars(getSetting($conn, 'smtp_password')); ?>" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Encryption Type</label>
                            <select class="form-select" name="smtp_encryption">
                                <option value="tls" <?php echo getSetting($conn, 'smtp_encryption') === 'tls' ? 'selected' : ''; ?>>TLS</option>
                                <option value="ssl" <?php echo getSetting($conn, 'smtp_encryption') === 'ssl' ? 'selected' : ''; ?>>SSL</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">From Email</label>
                            <input type="email" class="form-control" name="from_email" value="<?php echo htmlspecialchars(getSetting($conn, 'from_email')); ?>" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">From Name</label>
                            <input type="text" class="form-control" name="from_name" value="<?php echo htmlspecialchars(getSetting($conn, 'from_name')); ?>" required>
                        </div>
                        <button type="submit" class="btn btn-primary">Save Email Settings</button>
                    </form>
                </div>

                <!-- Notifications Tab -->
                <div class="tab-pane fade" id="notifications" role="tabpanel" aria-labelledby="notifications-tab">
                    <h5 class="mb-4">Notification Preferences</h5>
                    <form method="POST">
                        <input type="hidden" name="action" value="update_notification_settings">
                        <div class="form-check mb-3">
                            <input type="checkbox" class="form-check-input" name="notify_new_appointment" id="notify_new_appointment" 
                                   <?php echo getSetting($conn, 'notify_new_appointment') === '1' ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="notify_new_appointment">New Appointment Notifications</label>
                        </div>
                        <div class="form-check mb-3">
                            <input type="checkbox" class="form-check-input" name="notify_appointment_status" id="notify_appointment_status"
                                   <?php echo getSetting($conn, 'notify_appointment_status') === '1' ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="notify_appointment_status">Appointment Status Change Notifications</label>
                        </div>
                        <div class="form-check mb-3">
                            <input type="checkbox" class="form-check-input" name="notify_new_order" id="notify_new_order"
                                   <?php echo getSetting($conn, 'notify_new_order') === '1' ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="notify_new_order">New Order Notifications</label>
                        </div>
                        <div class="form-check mb-3">
                            <input type="checkbox" class="form-check-input" name="notify_low_stock" id="notify_low_stock"
                                   <?php echo getSetting($conn, 'notify_low_stock') === '1' ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="notify_low_stock">Low Stock Notifications</label>
                        </div>
                        <button type="submit" class="btn btn-primary">Save Notification Settings</button>
                    </form>
                </div>

                <!-- Roles & Permissions Tab -->
                <div class="tab-pane fade" id="roles" role="tabpanel" aria-labelledby="roles-tab">
                    <h5 class="mb-4">Role Management</h5>
                    <div class="mb-4">
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addRoleModal">
                            Add New Role
                        </button>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Role</th>
                                    <th>Permissions</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($roles as $role): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($role['role_name']); ?></td>
                                        <td>
                                            <?php
                                            $stmt = $conn->prepare("SELECT p.permission_name 
                                                                  FROM permissions p 
                                                                  JOIN role_permissions rp ON p.id = rp.permission_id 
                                                                  WHERE rp.role_id = ?");
                                            $stmt->execute([$role['id']]);
                                            $rolePermissions = $stmt->fetchAll(PDO::FETCH_COLUMN);
                                            echo htmlspecialchars(implode(', ', $rolePermissions));
                                            ?>
                                        </td>
                                        <td>
                                            <button type="button" class="btn btn-sm btn-primary edit-role" 
                                                    data-role-id="<?php echo $role['id']; ?>"
                                                    data-role-name="<?php echo htmlspecialchars($role['role_name']); ?>">
                                                Edit
                                            </button>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Telegram Settings Tab -->
                <div class="tab-pane fade" id="telegram" role="tabpanel" aria-labelledby="telegram-tab">
                    <h5 class="mb-4">Telegram Bot Configuration</h5>
                    <form method="POST">
                        <input type="hidden" name="action" value="update_telegram_settings">
                        
                        <div class="form-check mb-3">
                            <input type="checkbox" class="form-check-input" name="telegram_enabled" id="telegram_enabled"
                                   <?php echo getSetting($conn, 'telegram_enabled') === '1' ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="telegram_enabled">Enable Telegram Notifications</label>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Bot Token</label>
                            <input type="text" class="form-control" name="telegram_bot_token" 
                                   value="<?php echo htmlspecialchars(getSetting($conn, 'telegram_bot_token')); ?>" 
                                   placeholder="1234567890:ABCdefGhIJKlmNoPQRsTUVwxyZ">
                            <small class="form-text text-muted">Get your bot token from @BotFather on Telegram</small>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Chat ID</label>
                            <input type="text" class="form-control" name="telegram_chat_id" 
                                   value="<?php echo htmlspecialchars(getSetting($conn, 'telegram_chat_id')); ?>" 
                                   placeholder="-1001234567890">
                            <small class="form-text text-muted">Chat ID where notifications will be sent (use @userinfobot to get your chat ID)</small>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Webhook URL (Optional)</label>
                            <input type="url" class="form-control" name="telegram_webhook_url" 
                                   value="<?php echo htmlspecialchars(getSetting($conn, 'telegram_webhook_url')); ?>" 
                                   placeholder="https://yourdomain.com/telegram/webhook.php">
                            <small class="form-text text-muted">URL for receiving Telegram updates (leave empty if not using webhooks)</small>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Notification Types</label>
                            <?php 
                            $telegram_notifications = explode(',', getSetting($conn, 'telegram_notifications', 'appointments,orders,system'));
                            $notification_types = [
                                'appointments' => 'New Appointments',
                                'orders' => 'New Orders',
                                'system' => 'System Alerts',
                                'maintenance' => 'Maintenance Reminders',
                                'low_stock' => 'Low Stock Alerts'
                            ];
                            ?>
                            <?php foreach ($notification_types as $key => $label): ?>
                                <div class="form-check">
                                    <input type="checkbox" class="form-check-input" name="telegram_notifications[]" 
                                           value="<?php echo $key; ?>" id="telegram_<?php echo $key; ?>"
                                           <?php echo in_array($key, $telegram_notifications) ? 'checked' : ''; ?>>
                                    <label class="form-check-label" for="telegram_<?php echo $key; ?>">
                                        <?php echo $label; ?>
                                    </label>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <button type="submit" class="btn btn-primary">Save Telegram Settings</button>
                            </div>
                            <div class="col-md-6 text-end">
                                <button type="button" class="btn btn-info" id="testTelegramBot">
                                    <i class="fab fa-telegram-plane me-2"></i>Test Bot Connection
                                </button>
                            </div>
                        </div>
                    </form>
                    
                    <hr class="my-4">
                    
                    <div class="card bg-light">
                        <div class="card-body">
                            <h6 class="card-title"><i class="fas fa-info-circle me-2"></i>Setup Instructions</h6>
                            <ol class="mb-0">
                                <li>Create a new bot by messaging @BotFather on Telegram</li>
                                <li>Send <code>/newbot</code> and follow the instructions</li>
                                <li>Copy the bot token and paste it above</li>
                                <li>Add your bot to a group or get your personal chat ID using @userinfobot</li>
                                <li>Test the connection using the "Test Bot Connection" button</li>
                            </ol>
                        </div>
                    </div>
                </div>

                <!-- General Settings Tab -->
                <div class="tab-pane fade" id="general" role="tabpanel" aria-labelledby="general-tab">
                    <h5 class="mb-4">General Configuration</h5>
                    <form method="POST">
                        <input type="hidden" name="action" value="update_general_settings">
                        <div class="mb-3">
                            <label class="form-label">Site Name</label>
                            <input type="text" class="form-control" name="site_name" value="<?php echo htmlspecialchars(getSetting($conn, 'site_name')); ?>" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Site Description</label>
                            <textarea class="form-control" name="site_description" rows="3"><?php echo htmlspecialchars(getSetting($conn, 'site_description')); ?></textarea>
                        </div>
                        <div class="form-check mb-3">
                            <input type="checkbox" class="form-check-input" name="maintenance_mode" id="maintenance_mode"
                                   <?php echo getSetting($conn, 'maintenance_mode') === '1' ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="maintenance_mode">Maintenance Mode</label>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Timezone</label>
                            <select class="form-select" name="timezone">
                                <?php
                                $current_timezone = getSetting($conn, 'timezone', 'UTC');
                                $timezones = DateTimeZone::listIdentifiers();
                                foreach ($timezones as $timezone) {
                                    echo '<option value="' . htmlspecialchars($timezone) . '"' . 
                                         ($timezone === $current_timezone ? ' selected' : '') . '>' . 
                                         htmlspecialchars($timezone) . '</option>';
                                }
                                ?>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-primary">Save General Settings</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add Role Modal -->
<div class="modal fade" id="addRoleModal" tabindex="-1" aria-labelledby="addRoleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addRoleModalLabel">Add New Role</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST">
                <input type="hidden" name="action" value="add_role">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Role Name</label>
                        <input type="text" class="form-control" name="role_name" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Add Role</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Role Modal -->
<div class="modal fade" id="editRoleModal" tabindex="-1" aria-labelledby="editRoleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editRoleModalLabel">Edit Role Permissions</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST">
                <input type="hidden" name="action" value="update_role_permissions">
                <input type="hidden" name="role_id" id="edit_role_id">
                <div class="modal-body">
                    <h6 id="edit_role_name"></h6>
                    <div class="mb-3">
                        <label class="form-label">Permissions</label>
                        <?php foreach ($permissions as $permission): ?>
                            <div class="form-check">
                                <input type="checkbox" class="form-check-input permission-checkbox" 
                                       name="permissions[]" value="<?php echo $permission['id']; ?>"
                                       id="permission_<?php echo $permission['id']; ?>">
                                <label class="form-check-label" for="permission_<?php echo $permission['id']; ?>">
                                    <?php echo htmlspecialchars($permission['permission_name']); ?>
                                    <?php if ($permission['permission_description']): ?>
                                        <small class="text-muted d-block"><?php echo htmlspecialchars($permission['permission_description']); ?></small>
                                    <?php endif; ?>
                                </label>
                            </div>
                        <?php endforeach; ?>
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

<style>
.nav-pills .nav-link {
    color: #4e73df;
    background-color: transparent;
    border-radius: 0.35rem;
    margin: 0 0.25rem;
    transition: all 0.15s ease-in-out;
}

.nav-pills .nav-link:hover {
    color: #2e59d9;
    background-color: #eaecf4;
}

.nav-pills .nav-link.active {
    color: #fff;
    background-color: #4e73df;
}

.tab-content {
    padding: 1.25rem;
    background-color: #fff;
    border-radius: 0.35rem;
}

.form-label {
    font-weight: 600;
    color: #4e73df;
    margin-bottom: 0.5rem;
}

.card-header {
    background-color: #f8f9fc;
    border-bottom: 1px solid #e3e6f0;
}

.btn-primary {
    background-color: #4e73df;
    border-color: #4e73df;
}

.btn-primary:hover {
    background-color: #2e59d9;
    border-color: #2e59d9;
}

.form-check-label {
    font-weight: normal;
    color: #5a5c69;
}

.form-select {
    border: 1px solid #d1d3e2;
    border-radius: 0.35rem;
    padding: 0.375rem 0.75rem;
    font-size: 0.875rem;
    background-color: #fff;
}

.form-select:focus {
    border-color: #4e73df;
    box-shadow: 0 0 0 0.2rem rgba(78, 115, 223, 0.25);
}

.form-control:focus {
    border-color: #4e73df;
    box-shadow: 0 0 0 0.2rem rgba(78, 115, 223, 0.25);
}

.btn-close:focus {
    box-shadow: 0 0 0 0.2rem rgba(78, 115, 223, 0.25);
}

/* Telegram specific styling */
.fab.fa-telegram-plane {
    color: #0088cc;
}

.nav-pills .nav-link#telegram-tab {
    color: #0088cc;
}

.nav-pills .nav-link#telegram-tab.active {
    background-color: #0088cc;
    color: white;
}

.nav-pills .nav-link#telegram-tab:hover {
    background-color: rgba(0, 136, 204, 0.1);
    color: #0088cc;
}

.btn-info {
    background-color: #0088cc;
    border-color: #0088cc;
}

.btn-info:hover {
    background-color: #006699;
    border-color: #006699;
}

.form-text {
    font-size: 0.825rem;
    color: #6c757d;
}

.card.bg-light {
    background-color: #f8f9fc !important;
    border: 1px solid #e3e6f0;
}

.card-title {
    color: #4e73df;
    font-weight: 600;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Check if jQuery is available for compatibility
    if (typeof jQuery !== 'undefined') {
        // Auto-dismiss alerts after 5 seconds
        setTimeout(function() {
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(alert => {
                const closeButton = alert.querySelector('.btn-close');
                if (closeButton) {
                    closeButton.click();
                }
            });
        }, 5000);

        // Handle tab persistence
        var hash = window.location.hash;
        if (hash) {
            var triggerEl = document.querySelector('.nav-pills a[href="' + hash + '"]');
            if (triggerEl) {
                var tab = new bootstrap.Tab(triggerEl);
                tab.show();
            }
        }

        // Store the active tab in URL hash
        document.querySelectorAll('.nav-pills a').forEach(function(tabEl) {
            tabEl.addEventListener('shown.bs.tab', function (e) {
                window.location.hash = e.target.getAttribute('href');
            });
        });

        // Handle edit role button clicks
        document.querySelectorAll('.edit-role').forEach(function(button) {
            button.addEventListener('click', function() {
                var roleId = this.dataset.roleId;
                var roleName = this.dataset.roleName;
                
                document.getElementById('edit_role_id').value = roleId;
                document.getElementById('edit_role_name').textContent = 'Role: ' + roleName;
                
                // Reset checkboxes
                document.querySelectorAll('.permission-checkbox').forEach(function(checkbox) {
                    checkbox.checked = false;
                });
                
                // Fetch current permissions for this role
                fetch('ajax/get_role_permissions.php?role_id=' + roleId)
                    .then(response => response.json())
                    .then(data => {
                        if (data.permissions) {
                            data.permissions.forEach(function(permissionId) {
                                const checkbox = document.getElementById('permission_' + permissionId);
                                if (checkbox) {
                                    checkbox.checked = true;
                                }
                            });
                        }
                        var modal = new bootstrap.Modal(document.getElementById('editRoleModal'));
                        modal.show();
                    })
                    .catch(error => {
                        console.error('Error loading permissions:', error);
                        alert('Error loading permissions: ' + error.message);
                    });
            });
        });

        // Handle test Telegram bot button
        const testTelegramBtn = document.getElementById('testTelegramBot');
        if (testTelegramBtn) {
            testTelegramBtn.addEventListener('click', function() {
                const botToken = document.querySelector('input[name="telegram_bot_token"]').value;
                const chatId = document.querySelector('input[name="telegram_chat_id"]').value;
                
                if (!botToken || !chatId) {
                    alert('Please enter both Bot Token and Chat ID before testing.');
                    return;
                }
                
                // Disable button and show loading
                this.disabled = true;
                this.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Testing...';
                
                // Test the bot connection
                fetch('ajax/test_telegram_bot.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        bot_token: botToken,
                        chat_id: chatId
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('✅ Telegram bot test successful! Message sent to chat.');
                    } else {
                        alert('❌ Telegram bot test failed: ' + (data.error || 'Unknown error'));
                    }
                })
                .catch(error => {
                    console.error('Error testing Telegram bot:', error);
                    alert('❌ Error testing Telegram bot: ' + error.message);
                })
                .finally(() => {
                    // Re-enable button
                    this.disabled = false;
                    this.innerHTML = '<i class="fab fa-telegram-plane me-2"></i>Test Bot Connection';
                });
            });
        }
    } else {
        console.error('jQuery not available for settings page');
    }
});
</script>

<?php include 'includes/footer.php'; ?> 