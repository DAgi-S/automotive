<?php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/auth.php';

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if user is logged in and has admin privileges
if (!isLoggedIn() || !hasAdminPrivileges()) {
    header('Location: login.php');
    exit;
}

// Get current admin info
$admin = getCurrentAdmin();
if (!$admin) {
    header('Location: login.php');
    exit;
}

// Set page title
$pageTitle = "Dashboard";

// Include header
require_once __DIR__ . '/includes/header.php';

try {
    // Get dashboard statistics
    $stats = [
        'pending_appointments' => 0,
        'pending_orders' => 0,
        'total_customers' => 0,
        'total_services' => 0
    ];

    // Get pending appointments count
    $stmt = $conn->query("SELECT COUNT(*) FROM tbl_appointments WHERE status = 'pending'");
    $stats['pending_appointments'] = $stmt->fetchColumn();

    // Get pending orders count
    $stmt = $conn->query("SELECT COUNT(*) FROM tbl_orders WHERE status = 'pending'");
    $stats['pending_orders'] = $stmt->fetchColumn();

    // Get total customers count
    $stmt = $conn->query("SELECT COUNT(*) FROM tbl_user ");
    $stats['total_customers'] = $stmt->fetchColumn();

    // Get total services count
    $stmt = $conn->query("SELECT COUNT(*) FROM tbl_services WHERE status = 'active'");
    $stats['total_services'] = $stmt->fetchColumn();

} catch (PDOException $e) {
    error_log("Dashboard Error: " . $e->getMessage());
    $error = "An error occurred while fetching dashboard data.";
}
?>

<!-- Begin Page Content -->
<div class="container-fluid">

    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Dashboard</h1>
    </div>

    <!-- Content Row -->
    <div class="row">

        <!-- Pending Appointments Card -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Pending Appointments</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $stats['pending_appointments']; ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-calendar fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Pending Orders Card -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Pending Orders</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $stats['pending_orders']; ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-shopping-cart fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Total Customers Card -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Total Customers</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $stats['total_customers']; ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-users fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Total Services Card -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Total Services</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $stats['total_services']; ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-wrench fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Content Row -->
    <div class="row">
        <!-- Recent Activity or Additional Content can be added here -->
    </div>

</div>
<!-- /.container-fluid -->

<?php
// Include footer
require_once __DIR__ . '/includes/footer.php';
?>