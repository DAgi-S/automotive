<?php
// Start session at the very beginning
session_start();

// Check if user is logged in
if (!isset($_SESSION['admin_id'])) {
    header("Location: ../../login.php");
    exit();
}

// Include required files
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/Report.php';
require_once __DIR__ . '/SalesReport.php';
require_once __DIR__ . '/ServicesReport.php';
require_once __DIR__ . '/InventoryReport.php';

// Initialize variables
$error = '';
$report = null;
$pageTitle = "View Report";

try {
    // Get report ID from URL
    if (!isset($_GET['id']) || empty($_GET['id'])) {
        throw new Exception("No report ID provided");
    }

    $reportId = intval($_GET['id']);
    
    // Fetch report details using PDO
    $stmt = $conn->prepare("SELECT * FROM tbl_reports WHERE report_id = :report_id");
    $stmt->bindValue(':report_id', $reportId, PDO::PARAM_INT);
    $stmt->execute();
    
    if ($stmt->rowCount() === 0) {
        throw new Exception("Report not found");
    }
    
    $report = $stmt->fetch(PDO::FETCH_ASSOC);
    
} catch (Exception $e) {
    $error = $e->getMessage();
}

// Include header after all potential redirects
require_once __DIR__ . '/includes/header.php';
?>

<?php if (!empty($error)): ?>
    <div class="alert alert-danger" role="alert">
        <?php echo htmlspecialchars($error); ?>
        <br>
        <a href="index.php" class="btn btn-primary mt-3">Back to Reports</a>
    </div>
<?php else: ?>
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">
                <?php echo ucwords(str_replace('_', ' ', $report['report_type'])); ?> - 
                <?php echo date('F j, Y', strtotime($report['created_at'])); ?>
            </h6>
            <div>
                <button onclick="window.print()" class="btn btn-sm btn-info">
                    <i class="fas fa-print"></i> Print
                </button>
                <a href="download_report.php?id=<?php echo $reportId; ?>" class="btn btn-sm btn-success">
                    <i class="fas fa-download"></i> Download
                </a>
            </div>
        </div>
        <div class="card-body">
            <?php
            try {
                // Create appropriate report object based on type
                switch (strtolower($report['report_type'])) {
                    case 'sales':
                        $reportObj = new SalesReport($report['admin_id']);
                        break;
                    case 'services':
                        $reportObj = new ServicesReport($report['admin_id']);
                        break;
                    case 'inventory':
                        $reportObj = new InventoryReport($report['admin_id']);
                        break;
                    default:
                        throw new Exception("Invalid report type: " . htmlspecialchars($report['report_type']));
                }
                
                // Display report content
                if (!empty($report['report_data'])) {
                    echo $reportObj->generateHTML($report['report_data']);
                } else {
                    echo '<div class="alert alert-warning">No data available for this report.</div>';
                }
                
            } catch (Exception $e) {
                echo '<div class="alert alert-danger">' . htmlspecialchars($e->getMessage()) . '</div>';
            }
            ?>
        </div>
    </div>
<?php endif; ?>

<?php
// Include footer
require_once __DIR__ . '/includes/footer.php';
?> 