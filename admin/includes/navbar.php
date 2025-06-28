<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>

<!-- Topbar -->
<nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">
    <!-- Sidebar Toggle (Topbar) -->
    <button id="sidebarToggleTop" class="btn btn-link d-md-none rounded-circle mr-3">
        <i class="fa fa-bars"></i>
    </button>

    <!-- Topbar Search -->
    <form class="d-none d-sm-inline-block form-inline mr-auto ml-md-3 my-2 my-md-0 mw-100 navbar-search">
        <div class="input-group">
            <input type="text" class="form-control bg-light border-0 small" placeholder="Search for..." aria-label="Search" aria-describedby="basic-addon2">
            <div class="input-group-append">
                <button class="btn btn-primary" type="button">
                    <i class="fas fa-search fa-sm"></i>
                </button>
            </div>
        </div>
    </form>

    <!-- Topbar Navbar -->
    <ul class="navbar-nav ml-auto">
        <!-- Nav Item - Search Dropdown (Visible Only XS) -->
        <li class="nav-item dropdown no-arrow d-sm-none">
            <a class="nav-link dropdown-toggle" href="#" id="searchDropdown" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <i class="fas fa-search fa-fw"></i>
            </a>
            <!-- Dropdown - Messages -->
            <div class="dropdown-menu dropdown-menu-right p-3 shadow animated--grow-in" aria-labelledby="searchDropdown">
                <form class="form-inline mr-auto w-100 navbar-search">
                    <div class="input-group">
                        <input type="text" class="form-control bg-light border-0 small" placeholder="Search for..." aria-label="Search" aria-describedby="basic-addon2">
                        <div class="input-group-append">
                            <button class="btn btn-primary" type="button">
                                <i class="fas fa-search fa-sm"></i>
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </li>

        <!-- Nav Item - Alerts -->
        <li class="nav-item dropdown no-arrow mx-1">
            <a class="nav-link dropdown-toggle" href="#" id="alertsDropdown" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <i class="fas fa-bell fa-fw"></i>
                <!-- Counter - Alerts -->
                <?php
                try {
                    // Get unread notifications count
                    $stmt = $conn->prepare("SELECT COUNT(*) as count FROM notifications WHERE admin_id = ? AND is_read = 0");
                    $stmt->execute([$_SESSION['admin_id']]);
                    $result = $stmt->fetch();
                    if ($result && $result['count'] > 0) {
                        echo '<span class="badge badge-danger badge-counter">' . $result['count'] . '</span>';
                    }
                } catch (PDOException $e) {
                    // Log error silently
                    error_log("Notification count error: " . $e->getMessage());
                }
                ?>
            </a>
            <!-- Dropdown - Alerts -->
            <div class="dropdown-list dropdown-menu dropdown-menu-right shadow animated--grow-in" aria-labelledby="alertsDropdown">
                <h6 class="dropdown-header">Alerts Center</h6>
                <?php
                try {
                    // Get recent notifications
                    $stmt = $conn->prepare("
                        SELECT * FROM notifications 
                        WHERE admin_id = ? 
                        ORDER BY created_at DESC 
                        LIMIT 5
                    ");
                    $stmt->execute([$_SESSION['admin_id']]);
                    
                    $hasNotifications = false;
                    while ($row = $stmt->fetch()) {
                        $hasNotifications = true;
                        echo '<a class="dropdown-item d-flex align-items-center" href="#" onclick="markNotificationAsRead(' . $row['id'] . ')">
                            <div class="mr-3">
                                <div class="icon-circle bg-primary">
                                    <i class="fas fa-bell text-white"></i>
                                </div>
                            </div>
                            <div>
                                <div class="small text-gray-500">' . date('F d, Y', strtotime($row['created_at'])) . '</div>
                                <span class="font-weight-bold">' . htmlspecialchars($row['message']) . '</span>
                            </div>
                        </a>';
                    }
                    
                    if (!$hasNotifications) {
                        echo '<div class="dropdown-item text-center">No new notifications</div>';
                    }
                } catch (PDOException $e) {
                    // Show friendly message to user
                    echo '<div class="dropdown-item text-center">Unable to load notifications</div>';
                    // Log the actual error
                    error_log("Notification fetch error: " . $e->getMessage());
                }
                ?>
                <a class="dropdown-item text-center small text-gray-500" href="notifications.php">Show All Alerts</a>
            </div>
        </li>

        <div class="topbar-divider d-none d-sm-block"></div>

        <!-- Nav Item - User Information -->
        <li class="nav-item dropdown no-arrow">
            <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <span class="mr-2 d-none d-lg-inline text-gray-600 small">
                    <?php echo htmlspecialchars($_SESSION['admin_name'] ?? 'Admin'); ?>
                </span>
                <img class="img-profile rounded-circle" src="<?php echo htmlspecialchars($_SESSION['admin_image'] ?? 'assets/img/undraw_profile.svg'); ?>">
            </a>
            <!-- Dropdown - User Information -->
            <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in" aria-labelledby="userDropdown">
                <a class="dropdown-item" href="profile.php">
                    <i class="fas fa-user fa-sm fa-fw mr-2 text-gray-400"></i>
                    Profile
                </a>
                <a class="dropdown-item" href="settings.php">
                    <i class="fas fa-cogs fa-sm fa-fw mr-2 text-gray-400"></i>
                    Settings
                </a>
                <div class="dropdown-divider"></div>
                <a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#logoutModal">
                    <i class="fas fa-sign-out-alt fa-sm fa-fw mr-2 text-gray-400"></i>
                    Logout
                </a>
            </div>
        </li>
    </ul>
</nav>
<!-- End of Topbar -->

<!-- Logout Modal-->
<div class="modal fade" id="logoutModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Ready to Leave?</h5>
                <button class="close" type="button" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="modal-body">Select "Logout" below if you are ready to end your current session.</div>
            <div class="modal-footer">
                <button class="btn btn-secondary" type="button" data-bs-dismiss="modal">Cancel</button>
                <a class="btn btn-primary" href="logout.php">Logout</a>
            </div>
        </div>
    </div>
</div>

<!-- Sidebar Toggle JavaScript -->
<script>
$(document).ready(function() {
    $("#sidebarToggle, #sidebarToggleTop").on('click', function(e) {
        $("body").toggleClass("sidebar-toggled");
        $(".sidebar").toggleClass("toggled");
        if ($(".sidebar").hasClass("toggled")) {
            $('.sidebar .collapse').collapse('hide');
        };
    });

    // Close any open menu accordions when window is resized below 768px
    $(window).resize(function() {
        if ($(window).width() < 768) {
            $('.sidebar .collapse').collapse('hide');
        };
    });
});
</script> 