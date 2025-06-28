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

// Get date range from POST or GET or use defaults
$startDate = $_REQUEST['start_date'] ?? date('Y-m-d', strtotime('-30 days'));
$endDate = $_REQUEST['end_date'] ?? date('Y-m-d');

// Create report instance
$report = new InventoryReport($admin['id']);
$report->setDateRange($startDate, $endDate);
$report->setFormat('html');

// Generate report
$reportContent = $report->generate();

// Set page title
$pageTitle = "Inventory Report - " . date('M d, Y', strtotime($startDate)) . " to " . date('M d, Y', strtotime($endDate));
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle; ?></title>
    
    <!-- CSS Files -->
    <link rel="stylesheet" href="../../assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="../../assets/css/style.css">
    <link rel="stylesheet" href="../../assets/css/font-awesome.min.css">
    
    <style>
        @media print {
            .no-print { display: none !important; }
            body { margin: 0; padding: 0; }
            .container-fluid { padding: 0; }
            .report-container { 
                padding: 5px;
                margin: 0;
                width: 100%;
            }
            .table { margin-bottom: 0; }
            .table td, .table th {
                padding: 3px 6px;
                font-size: 11px;
                line-height: 1.2;
            }
            .report-header h2 { 
                font-size: 16px;
                margin: 0 0 2px 0;
            }
            .company-info { 
                font-size: 11px;
                margin: 0 0 5px 0;
            }
            .report-summary {
                margin: 0 0 5px 0;
                padding: 3px;
                font-size: 11px;
                display: flex;
                justify-content: space-between;
                background: #f8f9fc !important;
            }
            .summary-item { 
                padding: 2px 5px;
                margin: 0;
            }
            .badge-danger { background-color: #e74a3b !important; }
            .badge-warning { background-color: #f6c23e !important; }
            .badge-success { background-color: #1cc88a !important; }
        }
        
        .report-container {
            padding: 10px;
            background: #fff;
            margin: 5px auto;
            max-width: 1200px;
        }
        .report-header {
            text-align: center;
            margin-bottom: 10px;
        }
        .report-header h2 {
            margin: 0;
            color: #4e73df;
            font-size: 18px;
        }
        .company-info {
            color: #666;
            font-size: 12px;
            margin: 2px 0;
        }
        .report-summary {
            background: #f8f9fc;
            padding: 5px;
            margin-bottom: 10px;
            border-radius: 3px;
            display: flex;
            justify-content: space-between;
            flex-wrap: wrap;
        }
        .summary-item {
            padding: 3px 8px;
            background: white;
            border-radius: 3px;
            margin: 2px;
            font-size: 12px;
        }
        .table {
            margin-bottom: 0;
            font-size: 12px;
        }
        .table td, .table th {
            padding: 4px 6px;
            vertical-align: middle;
            line-height: 1.2;
        }
        .table th {
            background: #4e73df;
            color: white;
            font-weight: 500;
            white-space: nowrap;
        }
        .table td {
            white-space: nowrap;
        }
        .table td:nth-child(2), 
        .table td:nth-child(3) {
            white-space: normal;
        }
        .badge-danger {
            background-color: #e74a3b;
        }
        .badge-warning {
            background-color: #f6c23e;
        }
        .badge-success {
            background-color: #1cc88a;
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-2 no-print">
            <h2 class="h5 mb-0"><?php echo $pageTitle; ?></h2>
            <div>
                <button onclick="window.print()" class="btn btn-sm btn-primary">
                    <i class="fas fa-print"></i> Print Report
                </button>
                <a href="inventory.php" class="btn btn-sm btn-secondary">
                    <i class="fas fa-arrow-left"></i> Back
                </a>
            </div>
        </div>

        <?php echo $reportContent; ?>
    </div>

    <!-- JavaScript Files -->
    <script src="../../assets/js/jquery.min.js"></script>
    <script src="../../assets/js/bootstrap.bundle.min.js"></script>
    <script src="../../assets/js/custom.js"></script>
</body>
</html> 