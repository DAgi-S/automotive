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

require_once __DIR__ . '/Report.php';

class ServicesReport extends Report {
    protected $reportType;
    protected $startDate;
    protected $endDate;

    public function __construct($adminId) {
        parent::__construct($adminId);
        $this->reportType = 'services';
    }

    public function generate() {
        $data = $this->getServicesData();
        $reportId = $this->saveReport('services');
        
        if ($reportId) {
            $this->saveReportParameters($reportId, [
                'total_services' => count($data),
                'completed_services' => $this->countCompletedServices($data),
                'total_revenue' => $this->calculateTotalRevenue($data)
            ]);
        }
        
        switch ($this->format) {
            case 'csv':
                $this->generateCSV($data, 'services_report.csv');
                break;
            case 'pdf':
                $this->generatePDF($data, 'services_report.pdf', 'Services Report');
                break;
            case 'excel':
                $this->generateExcel($data, 'services_report.xlsx');
                break;
            default:
                return $this->generateHTML($data);
        }
    }
    
    private function getServicesData() {
        $sql = "SELECT 
                    sh.history_id,
                    sh.service_date,
                    i.car_brand,
                    i.car_model,
                    i.car_year,
                    i.plate_number,
                    s.service_name,
                    s.price as service_price,
                    sh.notes,
                    sh.cost,
                    sh.status,
                    u.firstname,
                    u.lastname
                FROM tbl_service_history sh
                JOIN tbl_services s ON sh.service_id = s.service_id
                JOIN tbl_info i ON sh.info_id = i.id
                JOIN tbl_user u ON i.userid = u.id
                WHERE sh.service_date BETWEEN :start_date AND :end_date
                ORDER BY sh.service_date DESC";
                
        try {
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':start_date', $this->startDate, PDO::PARAM_STR);
            $stmt->bindParam(':end_date', $this->endDate, PDO::PARAM_STR);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error fetching services data: " . $e->getMessage());
            return [];
        }
    }
    
    private function countCompletedServices($data) {
        return count(array_filter($data, function($service) {
            return strtolower($service['status']) === 'completed';
        }));
    }
    
    private function calculateTotalRevenue($data) {
        return array_sum(array_column($data, 'cost'));
    }
    
    private function generateHTML($data) {
        $totalServices = count($data);
        $completedServices = $this->countCompletedServices($data);
        $totalRevenue = $this->calculateTotalRevenue($data);
        
        $html = '<div class="report-container">';
        $html .= '<h2>Services Report</h2>';
        $html .= '<div class="report-summary">';
        $html .= '<p>Period: ' . date('F j, Y', strtotime($this->startDate)) . ' to ' . date('F j, Y', strtotime($this->endDate)) . '</p>';
        $html .= '<p>Total Services: ' . $totalServices . '</p>';
        $html .= '<p>Completed Services: ' . $completedServices . '</p>';
        $html .= '<p>Total Revenue: ETB ' . $this->formatCurrency($totalRevenue) . '</p>';
        $html .= '</div>';
        
        if (empty($data)) {
            $html .= '<div class="alert alert-info">No services found for the selected date range.</div>';
            return $html;
        }
        
        $html .= '<table class="report-table table table-bordered">';
        $html .= '<thead><tr>';
        $html .= '<th>Service ID</th>';
        $html .= '<th>Date</th>';
        $html .= '<th>Customer</th>';
        $html .= '<th>Vehicle</th>';
        $html .= '<th>Service Type</th>';
        $html .= '<th>Notes</th>';
        $html .= '<th>Cost</th>';
        $html .= '<th>Status</th>';
        $html .= '</tr></thead>';
        
        $html .= '<tbody>';
        foreach ($data as $row) {
            $statusClass = strtolower($row['status']) === 'completed' ? 'table-success' : 'table-warning';
            
            $html .= '<tr class="' . $statusClass . '">';
            $html .= '<td>' . htmlspecialchars($row['history_id']) . '</td>';
            $html .= '<td>' . date('M d, Y', strtotime($row['service_date'])) . '</td>';
            $html .= '<td>' . htmlspecialchars($row['firstname'] . ' ' . $row['lastname']) . '</td>';
            $html .= '<td>' . htmlspecialchars($row['car_year'] . ' ' . $row['car_brand'] . ' ' . $row['car_model']) . 
                 "<br><small class='text-muted'>" . htmlspecialchars($row['plate_number']) . "</small></td>";
            $html .= '<td>' . htmlspecialchars($row['service_name']) . '</td>';
            $html .= '<td>' . htmlspecialchars($row['notes']) . '</td>';
            $html .= '<td>ETB ' . $this->formatCurrency($row['cost']) . '</td>';
            $html .= '<td><span class="badge badge-' . 
                    (strtolower($row['status']) === 'completed' ? 'success' : 'warning') . '">' . 
                    ucfirst($row['status']) . '</span></td>';
            $html .= '</tr>';
        }
        $html .= '</tbody>';
        $html .= '</table>';
        $html .= '</div>';
        
        return $html;
    }
}

// Get services statistics
try {
    $stats = $conn->query("SELECT 
        COUNT(*) as total_services,
        SUM(CASE WHEN sh.status = 'completed' THEN 1 ELSE 0 END) as completed_services,
        SUM(CASE WHEN sh.status = 'pending' THEN 1 ELSE 0 END) as pending_services,
        COUNT(CASE WHEN sh.created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY) THEN 1 END) as recent_services,
        SUM(sh.cost) as total_revenue
    FROM tbl_service_history sh")->fetch(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    error_log("Error fetching services stats: " . $e->getMessage());
    $stats = [
        'total_services' => 0,
        'completed_services' => 0,
        'pending_services' => 0,
        'recent_services' => 0,
        'total_revenue' => 0
    ];
}

$pageTitle = "Services Reports";
require_once __DIR__ . '/includes/header.php';
?>

<!-- Begin Page Content -->
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Services Reports</h1>
        <div class="d-flex">
            <button class="btn btn-sm btn-primary shadow-sm mr-2" id="refreshStats">
                <i class="fas fa-sync-alt fa-sm text-white-50"></i> Refresh
            </button>
            <button class="btn btn-sm btn-success shadow-sm" id="exportServices">
                <i class="fas fa-download fa-sm text-white-50"></i> Export Services
            </button>
        </div>
    </div>

    <!-- Content Row -->
    <div class="row">
        <!-- Total Services Card -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="stats-card border-left-primary shadow h-100">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total Services</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo number_format($stats['total_services']); ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-wrench fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Completed Services Card -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="stats-card border-left-success shadow h-100">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Completed Services</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo number_format($stats['completed_services']); ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-check-circle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Pending Services Card -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="stats-card border-left-warning shadow h-100">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Pending Services</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo number_format($stats['pending_services']); ?></div>
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

    <!-- Services Report Generation Card -->
    <div class="card report-form shadow mb-4">
        <div class="card-header py-3 d-flex align-items-center justify-content-between">
            <h6 class="m-0 font-weight-bold text-primary">Generate Services Report</h6>
            <button class="btn btn-link btn-sm" type="button" data-toggle="collapse" data-target="#servicesReportForm" aria-expanded="true">
                <i class="fas fa-chevron-down"></i>
            </button>
        </div>
        <div class="collapse show" id="servicesReportForm">
            <div class="card-body">
                <form id="generateServicesReportForm" method="POST" action="generate_services_report.php" class="mb-0">
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

    <!-- Recent Services Table -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Recent Services</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover" id="recentServicesTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>Service ID</th>
                            <th>Customer</th>
                            <th>Vehicle</th>
                            <th>Service Type</th>
                            <th>Date</th>
                            <th>Cost</th>
                            <th>Status</th>
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        try {
                            $recentServices = $conn->query("
                                SELECT 
                                    sh.history_id,
                                    sh.service_date,
                                    i.car_brand,
                                    i.car_model,
                                    i.car_year,
                                    i.plate_number,
                                    s.service_name,
                                    sh.cost,
                                    sh.status,
                                    u.name
                                FROM tbl_service_history sh
                                JOIN tbl_services s ON sh.service_id = s.service_id
                                JOIN tbl_info i ON sh.info_id = i.id
                                JOIN tbl_user u ON i.userid = u.id
                                ORDER BY sh.service_date DESC 
                                LIMIT 10
                            ")->fetchAll(PDO::FETCH_ASSOC);

                            foreach ($recentServices as $service) {
                                $statusClass = $service['status'] == 'completed' ? 'success' : 
                                            ($service['status'] == 'pending' ? 'warning' : 'info');
                                
                                echo "<tr>";
                                echo "<td>#" . str_pad($service['history_id'], 6, '0', STR_PAD_LEFT) . "</td>";
                                echo "<td>" . htmlspecialchars($service['name'] ?? '') . "</td>";
                                echo "<td>" . htmlspecialchars(($service['car_year'] ?? '') . ' ' . ($service['car_brand'] ?? '') . ' ' . ($service['car_model'] ?? '')) . 
                                     "<br><small class='text-muted'>" . htmlspecialchars($service['plate_number'] ?? '') . "</small></td>";
                                echo "<td>" . htmlspecialchars($service['service_name'] ?? '') . "</td>";
                                echo "<td>" . date('M d, Y', strtotime($service['service_date'])) . "</td>";
                                echo "<td>ETB " . number_format($service['cost'], 2) . "</td>";
                                echo "<td><span class='badge badge-" . $statusClass . "'>" . ucfirst($service['status']) . "</span></td>";
                                echo "<td class='text-center'>
                                        <button type='button' class='btn btn-sm btn-info' onclick='window.location.href=\"view_services_report.php?id=" . $service['history_id'] . "\"'>
                                            <i class='fas fa-eye'></i> Details
                                        </button>
                                    </td>";
                                echo "</tr>";
                            }
                        } catch (PDOException $e) {
                            error_log("Error fetching recent services: " . $e->getMessage());
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
    $('#recentServicesTable').DataTable({
        order: [[4, 'desc']],
        pageLength: 10,
        lengthMenu: [[5, 10, 25, 50, -1], [5, 10, 25, 50, "All"]],
        language: {
            search: "_INPUT_",
            searchPlaceholder: "Search services..."
        }
    });

    // Form validation
    $('#generateServicesReportForm').on('submit', function(e) {
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
        const action = format === 'html' ? 'view_services_report.php' : 'generate_services_report.php';
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

    // Export services button
    $('#exportServices').click(function() {
        window.location.href = 'export_services.php';
    });
});
</script>

<?php include 'includes/footer.php'; ?> 