<?php
require_once __DIR__ . '/../../includes/config.php';
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/Report.php';
require_once __DIR__ . '/InventoryReport.php';

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
    $report = new InventoryReport($admin['id']);
    $report->setDateRange($startDate, $endDate);
    $report->setFormat($format);

    // Generate report based on format
    switch ($format) {
        case 'html':
            header('Location: view_inventory_report.php?start_date=' . urlencode($startDate) . '&end_date=' . urlencode($endDate));
            exit;
        case 'csv':
            header('Content-Type: text/csv');
            header('Content-Disposition: attachment; filename="inventory_report_' . date('Y-m-d') . '.csv"');
            echo $report->generate();
            exit;
        case 'excel':
            header('Content-Type: application/vnd.ms-excel');
            header('Content-Disposition: attachment; filename="inventory_report_' . date('Y-m-d') . '.xls"');
            echo $report->generate();
            exit;
        case 'pdf':
            header('Content-Type: application/pdf');
            header('Content-Disposition: attachment; filename="inventory_report_' . date('Y-m-d') . '.pdf"');
            echo $report->generate();
            exit;
        default:
            $_SESSION['error'] = 'Invalid report format selected.';
            header('Location: inventory.php');
            exit;
    }
}

// Get inventory statistics
try {
    $stats = $conn->query("SELECT 
        COUNT(*) as total_items,
        SUM(CASE WHEN quantity <= reorder_level THEN 1 ELSE 0 END) as low_stock,
        SUM(CASE WHEN quantity = 0 THEN 1 ELSE 0 END) as out_of_stock,
        SUM(quantity * unit_price) as total_value
    FROM tbl_products")->fetch(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    error_log("Error fetching inventory stats: " . $e->getMessage());
    $stats = [
        'total_items' => 0,
        'low_stock' => 0,
        'out_of_stock' => 0,
        'total_value' => 0
    ];
}

$pageTitle = "Inventory Reports";
require_once __DIR__ . '/includes/header.php';
?>

<!-- Begin Page Content -->
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Inventory Reports</h1>
        <div class="d-flex">
            <button class="btn btn-sm btn-primary shadow-sm mr-2" id="refreshStats">
                <i class="fas fa-sync-alt fa-sm text-white-50"></i> Refresh
            </button>
            <button class="btn btn-sm btn-success shadow-sm" id="exportInventory">
                <i class="fas fa-download fa-sm text-white-50"></i> Export Inventory
            </button>
        </div>
    </div>

    <!-- Content Row -->
    <div class="row">
        <!-- Total Items Card -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="stats-card border-left-primary shadow h-100">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total Items</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo number_format($stats['total_items']); ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-boxes fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Low Stock Card -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="stats-card border-left-warning shadow h-100">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Low Stock Items</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo number_format($stats['low_stock']); ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-exclamation-triangle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Out of Stock Card -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="stats-card border-left-danger shadow h-100">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">Out of Stock</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo number_format($stats['out_of_stock']); ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-times-circle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Total Value Card -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="stats-card border-left-info shadow h-100">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Total Value</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">ETB <?php echo number_format($stats['total_value'], 2); ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-dollar-sign fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Inventory Report Generation Card -->
    <div class="card report-form shadow mb-4">
        <div class="card-header py-3 d-flex align-items-center justify-content-between">
            <h6 class="m-0 font-weight-bold text-primary">Generate Inventory Report</h6>
            <button class="btn btn-link btn-sm" type="button" data-toggle="collapse" data-target="#inventoryReportForm" aria-expanded="true">
                <i class="fas fa-chevron-down"></i>
            </button>
        </div>
        <div class="collapse show" id="inventoryReportForm">
            <div class="card-body">
                <form id="generateInventoryReportForm" method="POST" action="inventory.php" class="mb-0">
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

    <!-- Recent Inventory Table -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Current Inventory</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover" id="inventoryTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>Product ID</th>
                            <th>Name</th>
                            <th>Category</th>
                            <th>Quantity</th>
                            <th>Unit Price</th>
                            <th>Total Value</th>
                            <th>Status</th>
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        try {
                            $inventory = $conn->query("
                                SELECT 
                                    p.id,
                                    p.name,
                                    c.category_name,
                                    p.quantity,
                                    p.unit_price,
                                    p.reorder_level,
                                    (p.quantity * p.unit_price) as total_value
                                FROM tbl_products p
                                LEFT JOIN tbl_categories c ON p.category_id = c.category_id
                                ORDER BY p.name ASC
                            ")->fetchAll(PDO::FETCH_ASSOC);

                            foreach ($inventory as $item) {
                                $statusClass = $item['quantity'] == 0 ? 'danger' : 
                                            ($item['quantity'] <= $item['reorder_level'] ? 'warning' : 'success');
                                $status = $item['quantity'] == 0 ? 'Out of Stock' : 
                                        ($item['quantity'] <= $item['reorder_level'] ? 'Low Stock' : 'In Stock');
                                
                                echo "<tr>";
                                echo "<td>#" . str_pad($item['id'], 6, '0', STR_PAD_LEFT) . "</td>";
                                echo "<td>" . htmlspecialchars($item['name']) . "</td>";
                                echo "<td>" . htmlspecialchars($item['category_name']) . "</td>";
                                echo "<td>" . number_format($item['quantity']) . "</td>";
                                echo "<td>ETB " . number_format($item['unit_price'], 2) . "</td>";
                                echo "<td>ETB " . number_format($item['total_value'], 2) . "</td>";
                                echo "<td><span class='badge badge-" . $statusClass . "'>" . $status . "</span></td>";
                                echo "<td class='text-center'>
                                        <a href='view_product.php?id=" . $item['id'] . "' class='btn btn-sm btn-icon-split btn-info mr-2'>
                                            <span class='icon'><i class='fas fa-eye'></i></span>
                                            <span class='text'>View</span>
                                        </a>
                                        <a href='print_product.php?id=" . $item['id'] . "' class='btn btn-sm btn-icon-split btn-primary'>
                                            <span class='icon'><i class='fas fa-print'></i></span>
                                            <span class='text'>Print</span>
                                        </a>
                                    </td>";
                                echo "</tr>";
                            }
                        } catch (PDOException $e) {
                            error_log("Error fetching inventory: " . $e->getMessage());
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
    $('#inventoryTable').DataTable({
        order: [[1, 'asc']],
        pageLength: 10,
        lengthMenu: [[5, 10, 25, 50, -1], [5, 10, 25, 50, "All"]],
        language: {
            search: "_INPUT_",
            searchPlaceholder: "Search inventory..."
        }
    });

    // Form validation
    $('#generateInventoryReportForm').on('submit', function(e) {
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
        const action = format === 'html' ? 'view_inventory_report.php' : 'generate_inventory_report.php';
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

    // Export inventory button
    $('#exportInventory').click(function() {
        window.location.href = 'export_inventory.php';
    });
});
</script>

<?php include 'includes/footer.php'; ?> 