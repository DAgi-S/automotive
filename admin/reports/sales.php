<?php
require_once __DIR__ . '/../../includes/config.php';
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/Report.php';
require_once __DIR__ . '/SalesReport.php';

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

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $startDate = $_POST['start_date'] ?? date('Y-m-d', strtotime('-30 days'));
    $endDate = $_POST['end_date'] ?? date('Y-m-d');
    $format = $_POST['format'] ?? 'html';

    // Create report instance
    $report = new SalesReport($admin['id']);
    $report->setDateRange($startDate, $endDate);
    $report->setFormat($format);

    // Generate report based on format
    switch ($format) {
        case 'html':
            header('Location: view_sales_report.php?start_date=' . urlencode($startDate) . '&end_date=' . urlencode($endDate));
            exit;
        case 'csv':
            header('Content-Type: text/csv');
            header('Content-Disposition: attachment; filename="sales_report_' . date('Y-m-d') . '.csv"');
            echo $report->generate();
            exit;
        case 'excel':
            header('Content-Type: application/vnd.ms-excel');
            header('Content-Disposition: attachment; filename="sales_report_' . date('Y-m-d') . '.xls"');
            echo $report->generate();
            exit;
        case 'pdf':
            header('Content-Type: application/pdf');
            header('Content-Disposition: attachment; filename="sales_report_' . date('Y-m-d') . '.pdf"');
            echo $report->generate();
            exit;
        default:
            $_SESSION['error'] = 'Invalid report format selected.';
            header('Location: sales.php');
            exit;
    }
}

// Get sales statistics
try {
    $stats = $conn->query("SELECT 
        COUNT(*) as total_sales,
        SUM(CASE WHEN status = 'completed' THEN 1 ELSE 0 END) as completed_sales,
        SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) as pending_sales,
        SUM(total_amount) as total_revenue
    FROM tbl_orders")->fetch(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    error_log("Error fetching sales stats: " . $e->getMessage());
    $stats = [
        'total_sales' => 0,
        'completed_sales' => 0,
        'pending_sales' => 0,
        'total_revenue' => 0
    ];
}

$pageTitle = "Sales Reports";
require_once __DIR__ . '/includes/header.php';
?>

<!-- Begin Page Content -->
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Sales Reports</h1>
        <div class="d-flex">
            <button class="btn btn-sm btn-primary shadow-sm mr-2" id="refreshStats">
                <i class="fas fa-sync-alt fa-sm text-white-50"></i> Refresh
            </button>
            <button class="btn btn-sm btn-success shadow-sm" id="exportSales">
                <i class="fas fa-download fa-sm text-white-50"></i> Export Sales
            </button>
        </div>
    </div>

    <!-- Content Row -->
    <div class="row">
        <!-- Total Sales Card -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="stats-card border-left-primary shadow h-100">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total Sales</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo number_format($stats['total_sales']); ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-shopping-cart fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Completed Sales Card -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="stats-card border-left-success shadow h-100">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Completed Sales</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo number_format($stats['completed_sales']); ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-check-circle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Pending Sales Card -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="stats-card border-left-warning shadow h-100">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Pending Sales</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo number_format($stats['pending_sales']); ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-clock fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Total Revenue Card -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="stats-card border-left-info shadow h-100">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Total Revenue</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">ETB <?php echo number_format($stats['total_revenue'], 2); ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-dollar-sign fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Sales Report Generation Card -->
    <div class="card report-form shadow mb-4">
        <div class="card-header py-3 d-flex align-items-center justify-content-between">
            <h6 class="m-0 font-weight-bold text-primary">Generate Sales Report</h6>
            <button class="btn btn-link btn-sm" type="button" data-toggle="collapse" data-target="#salesReportForm" aria-expanded="true">
                <i class="fas fa-chevron-down"></i>
            </button>
        </div>
        <div class="collapse show" id="salesReportForm">
            <div class="card-body">
                <form id="generateSalesReportForm" method="POST" action="sales.php" class="mb-0">
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="small font-weight-bold text-gray-600">Start Date</label>
                            <input type="date" class="form-control form-control-sm" name="start_date" 
                                   value="<?php echo date('Y-m-d', strtotime('-30 days')); ?>" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="small font-weight-bold text-gray-600">End Date</label>
                            <input type="date" class="form-control form-control-sm" name="end_date" 
                                   value="<?php echo date('Y-m-d'); ?>" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="small font-weight-bold text-gray-600">Format</label>
                            <select class="form-control form-control-sm" name="format" required>
                                <option value="html">View Online</option>
                                <option value="pdf">Download PDF</option>
                                <option value="csv">Export CSV</option>
                                <option value="excel">Export Excel</option>
                            </select>
                        </div>
                    </div>
                    <div class="text-right">
                        <button type="reset" class="btn btn-sm btn-secondary">
                            <i class="fas fa-undo fa-sm"></i> Reset
                        </button>
                        <button type="submit" class="btn btn-sm btn-primary ml-2">
                            <i class="fas fa-file-alt fa-sm"></i> Generate Report
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Recent Sales Table -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Recent Sales</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover" id="recentSalesTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>Order ID</th>
                            <th>Customer</th>
                            <th>Items</th>
                            <th>Products</th>
                            <th>Date</th>
                            <th>Amount</th>
                            <th>Status</th>
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        try {
                            $recentSales = $conn->query("
                                SELECT 
                                    o.order_id,
                                    o.created_at as order_date,
                                    CONCAT(c.firstname, ' ', c.lastname) as customer_name,
                                    COUNT(oi.item_id) as items_count,
                                    o.total_amount,
                                    o.status,
                                    GROUP_CONCAT(p.name SEPARATOR ', ') as products
                                FROM tbl_orders o
                                LEFT JOIN tbl_customers c ON o.customer_id = c.customer_id
                                LEFT JOIN tbl_order_items oi ON o.order_id = oi.order_id
                                LEFT JOIN tbl_products p ON oi.product_id = p.id
                                GROUP BY o.order_id
                                ORDER BY o.created_at DESC 
                                LIMIT 10
                            ")->fetchAll(PDO::FETCH_ASSOC);

                            foreach ($recentSales as $sale) {
                                $statusClass = $sale['status'] == 'completed' ? 'success' : 
                                            ($sale['status'] == 'pending' ? 'warning' : 'info');
                                
                                echo "<tr>";
                                echo "<td>#" . str_pad($sale['order_id'], 6, '0', STR_PAD_LEFT) . "</td>";
                                echo "<td>" . htmlspecialchars($sale['customer_name']) . "</td>";
                                echo "<td>" . $sale['items_count'] . " items</td>";
                                echo "<td>" . htmlspecialchars($sale['products']) . "</td>";
                                echo "<td>" . date('M d, Y', strtotime($sale['order_date'])) . "</td>";
                                echo "<td>ETB " . number_format($sale['total_amount'], 2) . "</td>";
                                echo "<td><span class='badge badge-" . $statusClass . "'>" . ucfirst($sale['status']) . "</span></td>";
                                echo "<td class='text-center'>
                                        <a href='view_order.php?id=" . $sale['order_id'] . "' class='btn btn-sm btn-icon-split btn-info mr-2'>
                                            <span class='icon'><i class='fas fa-eye'></i></span>
                                            <span class='text'>View</span>
                                        </a>
                                        <a href='print_order.php?id=" . $sale['order_id'] . "' class='btn btn-sm btn-icon-split btn-primary'>
                                            <span class='icon'><i class='fas fa-print'></i></span>
                                            <span class='text'>Print</span>
                                        </a>
                                    </td>";
                                echo "</tr>";
                            }
                        } catch (PDOException $e) {
                            error_log("Error fetching recent sales: " . $e->getMessage());
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    // Initialize DataTable
    $('#recentSalesTable').DataTable({
        order: [[4, 'desc']],
        pageLength: 10,
        lengthMenu: [[5, 10, 25, 50, -1], [5, 10, 25, 50, "All"]],
        language: {
            search: "_INPUT_",
            searchPlaceholder: "Search sales..."
        }
    });

    // Form validation
    $('#generateSalesReportForm').on('submit', function(e) {
        e.preventDefault();
        
        const startDate = new Date($('input[name="start_date"]').val());
        const endDate = new Date($('input[name="end_date"]').val());

        if (startDate > endDate) {
            Swal.fire({
                title: 'Error!',
                text: 'Start date cannot be after end date',
                icon: 'error'
            });
            return;
        }

        // If validation passes, submit the form
        const format = $('select[name="format"]').val();
        const action = format === 'html' ? 'view_sales_report.php' : 'generate_sales_report.php';
        this.action = action;
        this.submit();
    });

    // Refresh stats button
    $('#refreshStats').click(function() {
        $(this).find('i').addClass('fa-spin');
        setTimeout(() => {
            location.reload();
        }, 500);
    });

    // Export sales button
    $('#exportSales').click(function() {
        window.location.href = 'export_sales.php';
    });
});
</script>

<?php include 'includes/footer.php'; ?>