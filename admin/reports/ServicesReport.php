<?php
require_once __DIR__ . '/../../includes/config.php';
require_once __DIR__ . '/Report.php';

class ServicesReport extends Report {
    protected $reportType;
    protected $startDate;
    protected $endDate;

    public function __construct($adminId) {
        parent::__construct($adminId);
        $this->reportType = 'services';
        
        // Set default date range if not set (last 30 days)
        if (empty($this->startDate)) {
            $this->startDate = date('Y-m-d', strtotime('-30 days'));
        }
        if (empty($this->endDate)) {
            $this->endDate = date('Y-m-d');
        }
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
    
    public function getServicesData() {
        $sql = "SELECT 
                    sh.history_id,
                    sh.service_date,
                    sh.notes,
                    sh.cost as service_cost,
                    sh.status,
                    s.service_name,
                    s.description,
                    i.plate_number,
                    i.car_model as model,
                    i.car_brand as make,
                    i.car_year as year,
                    COALESCE(u.name, 'N/A') as customer_name
                FROM tbl_service_history sh
                JOIN tbl_services s ON sh.service_id = s.service_id
                JOIN tbl_info i ON sh.info_id = i.id
                LEFT JOIN tbl_user u ON i.userid = u.id
                WHERE sh.service_date BETWEEN :start_date AND :end_date
                ORDER BY sh.service_date DESC";
                
        try {
            $stmt = $this->db->prepare($sql);
            $stmt->bindValue(':start_date', $this->startDate, PDO::PARAM_STR);
            $stmt->bindValue(':end_date', $this->endDate, PDO::PARAM_STR);
            $stmt->execute();
            
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
            if ($result === false) {
                error_log("Error fetching services data: " . implode(", ", $stmt->errorInfo()));
                return [];
            }
            return $result;
        } catch (PDOException $e) {
            error_log("Database error in getServicesData: " . $e->getMessage());
            return [];
        }
    }
    
    public function countCompletedServices($data) {
        return count(array_filter($data, function($service) {
            return strtolower($service['status'] ?? '') === 'active';
        }));
    }
    
    public function calculateTotalRevenue($data) {
        return array_sum(array_column($data, 'service_cost'));
    }
    
    private function safeHtml($value) {
        return htmlspecialchars($value ?? '', ENT_QUOTES, 'UTF-8');
    }

    public function generateHTML($data) {
        $totalServices = count($data);
        $completedServices = $this->countCompletedServices($data);
        $totalRevenue = $this->calculateTotalRevenue($data);
        
        $html = '<div class="report-container">
            <div class="report-header">
                <h2>Services Report</h2>
                <p class="company-info">Automotive Service Center • Generated on ' . date('F j, Y') . '</p>
            </div>
            
            <div class="report-summary">
                <div class="summary-item">Period: ' . date('M j, Y', strtotime($this->startDate)) . ' - ' . date('M j, Y', strtotime($this->endDate)) . '</div>
                <div class="summary-item">Total: ' . $totalServices . '</div>
                <div class="summary-item">Completed: ' . $completedServices . '</div>
                <div class="summary-item">Revenue: ETB ' . $this->formatCurrency($totalRevenue) . '</div>
            </div>';

        if (empty($data)) {
            $html .= '<div class="alert alert-info">No data available for this report.</div>';
            return $html;
        }

        $html .= '<table class="table table-bordered table-sm">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Date</th>
                    <th>Customer</th>
                    <th>Vehicle</th>
                    <th>Service</th>
                    <th>Cost</th>
                </tr>
            </thead>
            <tbody>';

        foreach ($data as $row) {
            $vehicleInfo = implode(' ', array_filter([
                $row['make'] ?? '',
                $row['model'] ?? '',
                $row['year'] ? "({$row['year']})" : ''
            ]));

            $html .= '<tr>
                <td>#' . str_pad($row['history_id'], 5, '0', STR_PAD_LEFT) . '</td>
                <td>' . date('m/d/y', strtotime($row['service_date'])) . '</td>
                <td>' . $this->safeHtml($row['customer_name']) . '</td>
                <td>' . $this->safeHtml($vehicleInfo) . ' • ' . $this->safeHtml($row['plate_number']) . '</td>
                <td>' . $this->safeHtml($row['service_name']) . '</td>
                <td>ETB ' . $this->formatCurrency($row['service_cost']) . '</td>
            </tr>';
        }

        $html .= '</tbody></table></div>';
        return $html;
    }
} 