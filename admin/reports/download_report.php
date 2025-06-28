<?php
require_once '../../includes/config.php';
require_once '../../includes/auth.php';

// Check if user is logged in and has admin privileges
if (!isLoggedIn() || !hasAdminPrivileges()) {
    header('Location: ../login.php');
    exit;
}

// Validate report ID
$reportId = $_GET['id'] ?? null;
if (!$reportId) {
    $_SESSION['error'] = "Invalid report ID.";
    header('Location: index.php');
    exit;
}

try {
    // Fetch report details
    $sql = "SELECT * FROM tbl_reports WHERE report_id = ?";
    $report = executeQuery($sql, [$reportId], true);
    
    if (!$report) {
        throw new Exception("Report not found.");
    }
    
    // Check if file exists
    if (!$report['file_path'] || !file_exists($report['file_path'])) {
        throw new Exception("Report file not found.");
    }
    
    // Get file extension based on format
    $extension = '';
    switch ($report['format']) {
        case 'pdf':
            $extension = 'pdf';
            $contentType = 'application/pdf';
            break;
        case 'csv':
            $extension = 'csv';
            $contentType = 'text/csv';
            break;
        case 'excel':
            $extension = 'xlsx';
            $contentType = 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet';
            break;
        default:
            throw new Exception("Unsupported report format.");
    }
    
    // Generate filename
    $filename = sprintf(
        '%s_report_%s_%s.%s',
        $report['report_type'],
        date('Y-m-d', strtotime($report['start_date'])),
        date('Y-m-d', strtotime($report['end_date'])),
        $extension
    );
    
    // Set headers for file download
    header('Content-Type: ' . $contentType);
    header('Content-Disposition: attachment; filename="' . $filename . '"');
    header('Content-Length: ' . filesize($report['file_path']));
    header('Cache-Control: no-cache, must-revalidate');
    header('Pragma: no-cache');
    header('Expires: 0');
    
    // Output file content
    readfile($report['file_path']);
    exit;
    
} catch (Exception $e) {
    error_log("Error downloading report: " . $e->getMessage());
    $_SESSION['error'] = "An error occurred while downloading the report. Please try again.";
    header('Location: index.php');
    exit;
} 