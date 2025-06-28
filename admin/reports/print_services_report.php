<?php
require_once __DIR__ . '/../../includes/config.php';
require_once __DIR__ . '/ServicesReport.php';

// Check if user is logged in and has admin privileges
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'admin') {
    header('Location: ../../login.php');
    exit();
}

// Get report parameters from URL
$startDate = $_GET['start_date'] ?? date('Y-m-d', strtotime('-30 days'));
$endDate = $_GET['end_date'] ?? date('Y-m-d');

// Initialize report
$report = new ServicesReport($_SESSION['user_id']);
$report->setDateRange($startDate, $endDate);

// Get report data
$data = $report->getServicesData();
$totalServices = count($data);
$completedServices = $report->countCompletedServices($data);
$totalRevenue = $report->calculateTotalRevenue($data);

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Services Report - Print Version</title>
    <style>
        @page {
            size: A4;
            margin: 1cm;
        }
        
        body {
            font-family: Arial, sans-serif;
            font-size: 9pt;
            line-height: 1.3;
            margin: 0;
            padding: 0;
            background: #fff;
        }
        
        .report-container {
            padding: 0;
            margin: 0;
            width: 100%;
        }
        
        .report-header {
            text-align: center;
            margin-bottom: 10px;
            padding-bottom: 5px;
            border-bottom: 0.5pt solid #000;
        }
        
        .report-header h2 {
            font-size: 14pt;
            margin: 0 0 2px 0;
        }
        
        .company-info {
            font-size: 8pt;
            margin: 0;
        }
        
        .report-summary {
            display: table;
            width: 100%;
            padding: 5px;
            margin-bottom: 10px;
            border: 0.5pt solid #000;
        }
        
        .summary-item {
            display: table-cell;
            width: 25%;
            padding: 3px;
            border-right: 0.5pt solid #000;
        }
        
        .summary-item:last-child {
            border-right: none;
        }
        
        .summary-item .label {
            font-size: 7pt;
            margin-bottom: 1px;
        }
        
        .summary-item .value {
            font-size: 9pt;
            font-weight: bold;
        }
        
        .table {
            width: 100%;
            border-collapse: collapse;
            font-size: 8pt;
            margin-bottom: 10px;
        }
        
        .table th {
            background: #eee;
            padding: 3px;
            border: 0.5pt solid #000;
            font-weight: bold;
            text-align: left;
        }
        
        .table td {
            padding: 3px;
            border: 0.5pt solid #000;
            vertical-align: top;
        }
        
        .col-id { width: 8%; }
        .col-date { width: 10%; }
        .col-customer { width: 12%; }
        .col-vehicle { width: 20%; }
        .col-service { width: 25%; }
        .col-cost { width: 12%; }
        .col-status { width: 8%; }
        
        .service-details {
            font-size: 7pt;
            margin-top: 1px;
        }
        
        .badge {
            padding: 1px 3px;
            font-size: 7pt;
            border: 0.5pt solid #000;
        }
        
        .report-footer {
            font-size: 7pt;
            text-align: center;
            padding-top: 3px;
            border-top: 0.5pt solid #000;
            position: running(footer);
        }
        
        @page {
            @bottom-center {
                content: element(footer);
            }
        }
    </style>
</head>
<body onload="window.print()">
    <div class="report-container">
        <div class="report-header">
            <h2>Services Report</h2>
            <div class="company-info">
                Automotive Service Center<br>
                Generated on <?php echo date('F j, Y'); ?>
            </div>
        </div>
        
        <div class="report-summary">
            <div class="summary-item">
                <div class="label">Report Period</div>
                <div class="value"><?php echo date('M j, Y', strtotime($startDate)) . ' - ' . date('M j, Y', strtotime($endDate)); ?></div>
            </div>
            <div class="summary-item">
                <div class="label">Total Services</div>
                <div class="value"><?php echo $totalServices; ?></div>
            </div>
            <div class="summary-item">
                <div class="label">Completed Services</div>
                <div class="value"><?php echo $completedServices; ?></div>
            </div>
            <div class="summary-item">
                <div class="label">Total Revenue</div>
                <div class="value">ETB <?php echo number_format($totalRevenue, 2); ?></div>
            </div>
        </div>

        <?php if (empty($data)): ?>
            <div class="alert">No data available for this report.</div>
        <?php else: ?>
            <table class="table">
                <thead>
                    <tr>
                        <th class="col-id">ID</th>
                        <th class="col-date">Date</th>
                        <th class="col-customer">Customer</th>
                        <th class="col-vehicle">Vehicle Details</th>
                        <th class="col-service">Service Details</th>
                        <th class="col-cost">Cost</th>
                        <th class="col-status">Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($data as $row): 
                        $vehicleInfo = implode(' ', array_filter([
                            $row['make'] ?? '',
                            $row['model'] ?? '',
                            $row['year'] ? "({$row['year']})" : ''
                        ]));
                    ?>
                        <tr>
                            <td class="col-id">#<?php echo str_pad($row['history_id'], 5, '0', STR_PAD_LEFT); ?></td>
                            <td class="col-date"><?php echo date('M j, Y', strtotime($row['service_date'])); ?></td>
                            <td class="col-customer"><?php echo htmlspecialchars($row['customer_name'] ?? '', ENT_QUOTES, 'UTF-8'); ?></td>
                            <td class="col-vehicle">
                                <?php echo htmlspecialchars($vehicleInfo, ENT_QUOTES, 'UTF-8'); ?><br>
                                <span class="plate-number"><?php echo htmlspecialchars($row['plate_number'] ?? '', ENT_QUOTES, 'UTF-8'); ?></span>
                            </td>
                            <td class="col-service">
                                <strong><?php echo htmlspecialchars($row['service_name'] ?? '', ENT_QUOTES, 'UTF-8'); ?></strong>
                                <div class="service-details"><?php echo htmlspecialchars($row['description'] ?? '', ENT_QUOTES, 'UTF-8'); ?></div>
                            </td>
                            <td class="col-cost">ETB <?php echo number_format($row['service_cost'], 2); ?></td>
                            <td class="col-status">
                                <span class="badge"><?php echo ucfirst($row['status'] ?? 'unknown'); ?></span>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>

        <div class="report-footer">
            Copyright © <?php echo date('Y'); ?> Automotive Service. All rights reserved.
        </div>
    </div>
</body>
</html> 