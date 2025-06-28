<?php
require_once __DIR__ . '/../../includes/config.php';
require_once __DIR__ . '/../../includes/auth.php';

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if user is logged in and has admin privileges
if (!isLoggedIn()) {
    header('Location: ../login.php');
    exit;
} elseif (!hasAdminPrivileges()) {
    header('Location: ../unauthorized.php');
    exit;
}

// Get admin info
$admin = getCurrentAdmin();
if (!$admin) {
    header('Location: ../login.php');
    exit;
}

$pageTitle = "Reports Dashboard";
require_once __DIR__ . '/includes/header.php';

// Get statistics for the last 30 days
$thirtyDaysAgo = date('Y-m-d', strtotime('-30 days'));
$today = date('Y-m-d');

try {
    // Services Statistics
    $servicesStats = $conn->query("SELECT 
        COUNT(*) as total_services,
        SUM(CASE WHEN status = 'completed' THEN 1 ELSE 0 END) as completed_services,
        SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) as pending_services,
        SUM(cost) as total_revenue
        FROM tbl_service_history 
        WHERE service_date BETWEEN '$thirtyDaysAgo' AND '$today'")->fetch(PDO::FETCH_ASSOC);

    // Daily Services Data for Chart
    $dailyServices = $conn->query("SELECT 
        DATE(service_date) as date,
        COUNT(*) as service_count,
        SUM(cost) as daily_revenue
        FROM tbl_service_history 
        WHERE service_date BETWEEN '$thirtyDaysAgo' AND '$today'
        GROUP BY DATE(service_date)
        ORDER BY date")->fetchAll(PDO::FETCH_ASSOC);

    // Inventory Statistics
    $inventoryStats = $conn->query("SELECT 
        COUNT(*) as total_products,
        SUM(CASE WHEN stock <= 10 THEN 1 ELSE 0 END) as low_stock,
        SUM(CASE WHEN stock = 0 THEN 1 ELSE 0 END) as out_of_stock,
        SUM(stock * price) as inventory_value
        FROM tbl_products")->fetch(PDO::FETCH_ASSOC);

    // Top Categories
    $topCategories = $conn->query("SELECT 
        c.name as category_name,
        COUNT(p.id) as product_count
        FROM tbl_categories c
        LEFT JOIN tbl_products p ON c.id = p.category_id
        GROUP BY c.id
        ORDER BY product_count DESC
        LIMIT 5")->fetchAll(PDO::FETCH_ASSOC);

    // Service Types Distribution
    $serviceTypes = $conn->query("SELECT 
        s.service_name,
        COUNT(sh.history_id) as service_count
        FROM tbl_services s
        LEFT JOIN tbl_service_history sh ON s.service_id = sh.service_id
        WHERE sh.service_date BETWEEN '$thirtyDaysAgo' AND '$today'
        GROUP BY s.service_id
        ORDER BY service_count DESC
        LIMIT 5")->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    error_log("Error fetching statistics: " . $e->getMessage());
    // Set default values if query fails
    $servicesStats = ['total_services' => 0, 'completed_services' => 0, 'pending_services' => 0, 'total_revenue' => 0];
    $dailyServices = [];
    $inventoryStats = ['total_products' => 0, 'low_stock' => 0, 'out_of_stock' => 0, 'inventory_value' => 0];
    $topCategories = [];
    $serviceTypes = [];
}
?>

<!-- Begin Page Content -->
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Reports Dashboard</h1>
        <div class="d-flex">
            <button class="btn btn-sm btn-primary shadow-sm" id="refreshStats">
                <i class="fas fa-sync-alt fa-sm text-white-50"></i> Refresh Stats
            </button>
        </div>
    </div>

    <!-- Statistics Cards Row -->
    <div class="row">
        <!-- Services Card -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total Services (30 days)</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo number_format($servicesStats['total_services']); ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-calendar fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Revenue Card -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Revenue (30 days)</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">ETB <?php echo number_format($servicesStats['total_revenue'], 2); ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-dollar-sign fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Inventory Value Card -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Total Inventory Value</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">ETB <?php echo number_format($inventoryStats['inventory_value'], 2); ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-warehouse fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Low Stock Alert Card -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Low Stock Items</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo number_format($inventoryStats['low_stock']); ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-exclamation-triangle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="row">
        <!-- Services Trend Chart -->
        <div class="col-xl-8 col-lg-7">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Services Trend (Last 30 Days)</h6>
                </div>
                <div class="card-body">
                    <div class="chart-area">
                        <canvas id="servicesChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Service Types Distribution -->
        <div class="col-xl-4 col-lg-5">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Service Types Distribution</h6>
                </div>
                <div class="card-body">
                    <div class="chart-pie pt-4">
                        <canvas id="serviceTypesChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Additional Charts Row -->
    <div class="row">
        <!-- Inventory Categories -->
        <div class="col-xl-6 col-lg-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Top Product Categories</h6>
                </div>
                <div class="card-body">
                    <div class="chart-bar">
                        <canvas id="categoriesChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Revenue Distribution -->
        <div class="col-xl-6 col-lg-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Revenue Distribution</h6>
                </div>
                <div class="card-body">
                    <div class="chart-bar">
                        <canvas id="revenueChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Prepare data for Services Trend Chart
    const servicesData = {
        labels: <?php echo json_encode(array_map(function($item) { 
            return date('M d', strtotime($item['date'])); 
        }, $dailyServices)); ?>,
        datasets: [{
            label: 'Number of Services',
            data: <?php echo json_encode(array_map(function($item) { 
                return $item['service_count']; 
            }, $dailyServices)); ?>,
            borderColor: '#4e73df',
            tension: 0.3,
            fill: false
        }, {
            label: 'Daily Revenue (ETB)',
            data: <?php echo json_encode(array_map(function($item) { 
                return $item['daily_revenue']; 
            }, $dailyServices)); ?>,
            borderColor: '#1cc88a',
            tension: 0.3,
            fill: false
        }]
    };

    // Services Trend Chart
    new Chart(document.getElementById('servicesChart'), {
        type: 'line',
        data: servicesData,
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });

    // Service Types Chart
    new Chart(document.getElementById('serviceTypesChart'), {
        type: 'doughnut',
        data: {
            labels: <?php echo json_encode(array_map(function($item) { 
                return $item['service_name']; 
            }, $serviceTypes)); ?>,
            datasets: [{
                data: <?php echo json_encode(array_map(function($item) { 
                    return $item['service_count']; 
                }, $serviceTypes)); ?>,
                backgroundColor: ['#4e73df', '#1cc88a', '#36b9cc', '#f6c23e', '#e74a3b']
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false
        }
    });

    // Categories Chart
    new Chart(document.getElementById('categoriesChart'), {
        type: 'bar',
        data: {
            labels: <?php echo json_encode(array_map(function($item) { 
                return $item['category_name']; 
            }, $topCategories)); ?>,
            datasets: [{
                label: 'Number of Products',
                data: <?php echo json_encode(array_map(function($item) { 
                    return $item['product_count']; 
                }, $topCategories)); ?>,
                backgroundColor: '#4e73df'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });

    // Revenue Distribution Chart
    new Chart(document.getElementById('revenueChart'), {
        type: 'bar',
        data: {
            labels: ['Completed', 'Pending'],
            datasets: [{
                label: 'Services Status',
                data: [
                    <?php echo $servicesStats['completed_services']; ?>,
                    <?php echo $servicesStats['pending_services']; ?>
                ],
                backgroundColor: ['#1cc88a', '#f6c23e']
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });

    // Refresh stats button
    document.getElementById('refreshStats').addEventListener('click', function() {
        this.querySelector('i').classList.add('fa-spin');
        setTimeout(() => {
            location.reload();
        }, 500);
    });
});
</script>

<?php include 'includes/footer.php'; ?>