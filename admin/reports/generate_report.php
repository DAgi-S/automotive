<?php
require_once '../../includes/config.php';
require_once '../../includes/auth.php';

// Check if user is logged in and has admin privileges
if (!isLoggedIn() || !hasAdminPrivileges()) {
    header('Location: ../login.php');
    exit;
}

// Validate input
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit;
}

$reportType = $_POST['report_type'] ?? '';
$startDate = $_POST['start_date'] ?? '';
$endDate = $_POST['end_date'] ?? '';
$format = $_POST['format'] ?? 'html';
$parameters = $_POST['parameters'] ?? '';

// Validate required fields
if (empty($reportType) || empty($startDate) || empty($endDate)) {
    $_SESSION['error'] = "Please fill in all required fields.";
    header('Location: index.php');
    exit;
}

// Validate date range
if (strtotime($startDate) > strtotime($endDate)) {
    $_SESSION['error'] = "Start date cannot be after end date.";
    header('Location: index.php');
    exit;
}

try {
    // Initialize the appropriate report class
    switch ($reportType) {
        case 'sales':
            require_once 'sales.php';
            $report = new SalesReport($_SESSION['admin_id']);
            break;
            
        case 'services':
            require_once 'services.php';
            $report = new ServicesReport($_SESSION['admin_id']);
            break;
            
        case 'inventory':
            require_once 'inventory.php';
            $report = new InventoryReport($_SESSION['admin_id']);
            break;
            
        case 'appointments':
            require_once 'appointments.php';
            $report = new AppointmentsReport($_SESSION['admin_id']);
            break;
            
        default:
            throw new Exception("Invalid report type.");
    }
    
    // Set report parameters
    $report->setDateRange($startDate, $endDate);
    $report->setFormat($format);
    
    // Generate the report
    $result = $report->generate();
    
    // If format is HTML, redirect to view the report
    if ($format === 'html') {
        $_SESSION['report_html'] = $result;
        header('Location: view_report.php');
        exit;
    }
    
    // For other formats, the report class will handle the output
    // (PDF, CSV, Excel files will be downloaded automatically)
    
} catch (Exception $e) {
    error_log("Error generating report: " . $e->getMessage());
    $_SESSION['error'] = "An error occurred while generating the report. Please try again.";
    header('Location: index.php');
    exit;
} 