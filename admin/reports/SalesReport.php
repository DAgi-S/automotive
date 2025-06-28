<?php
require_once __DIR__ . '/Report.php';

class SalesReport extends Report {
    protected $reportType;
    protected $startDate;
    protected $endDate;

    public function __construct($adminId) {
        parent::__construct($adminId);
        $this->reportType = 'sales';
        
        // Set default date range if not set (last 30 days)
        if (empty($this->startDate)) {
            $this->startDate = date('Y-m-d', strtotime('-30 days'));
        }
        if (empty($this->endDate)) {
            $this->endDate = date('Y-m-d');
        }
    }

    public function generate() {
        $data = $this->getSalesData();
        $reportId = $this->saveReport('sales');
        
        if ($reportId) {
            $this->saveReportParameters($reportId, [
                'total_sales' => $this->calculateTotalSales($data),
                'total_orders' => count($data),
                'average_order_value' => $this->calculateAverageOrderValue($data)
            ]);
        }
        
        switch ($this->format) {
            case 'csv':
                $this->generateCSV($data, 'sales_report.csv');
                break;
            case 'pdf':
                $this->generatePDF($data, 'sales_report.pdf', 'Sales Report');
                break;
            case 'excel':
                $this->generateExcel($data, 'sales_report.xlsx');
                break;
            default:
                return $this->generateHTML($data);
        }
    }
    
    public function getSalesData() {
        $sql = "SELECT 
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
                WHERE o.created_at BETWEEN :start_date AND :end_date
                GROUP BY o.order_id
                ORDER BY o.created_at DESC";
                
        try {
            $stmt = $this->db->prepare($sql);
            $stmt->bindValue(':start_date', $this->startDate, PDO::PARAM_STR);
            $stmt->bindValue(':end_date', $this->endDate, PDO::PARAM_STR);
            $stmt->execute();
            
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
            if ($result === false) {
                error_log("Error fetching sales data: " . implode(", ", $stmt->errorInfo()));
                return [];
            }
            return $result;
        } catch (PDOException $e) {
            error_log("Database error in getSalesData: " . $e->getMessage());
            return [];
        }
    }
    
    private function calculateTotalSales($data) {
        return array_sum(array_column($data, 'total_amount'));
    }
    
    private function calculateAverageOrderValue($data) {
        if (empty($data)) return 0;
        return $this->calculateTotalSales($data) / count($data);
    }
    
    private function safeHtml($value) {
        return htmlspecialchars($value ?? '', ENT_QUOTES, 'UTF-8');
    }

    public function generateHTML($data) {
        $totalSales = $this->calculateTotalSales($data);
        $totalOrders = count($data);
        $averageOrderValue = $this->calculateAverageOrderValue($data);
        
        $html = '<div class="report-container">
            <div class="report-header">
                <h2>Sales Report</h2>
                <p class="company-info">Automotive Service Center • Generated on ' . date('F j, Y') . '</p>
            </div>
            
            <div class="report-summary">
                <div class="summary-item">Period: ' . date('M j, Y', strtotime($this->startDate)) . ' - ' . date('M j, Y', strtotime($this->endDate)) . '</div>
                <div class="summary-item">Orders: ' . $totalOrders . '</div>
                <div class="summary-item">Average: ETB ' . $this->formatCurrency($averageOrderValue) . '</div>
                <div class="summary-item">Total: ETB ' . $this->formatCurrency($totalSales) . '</div>
            </div>';

        if (empty($data)) {
            $html .= '<div class="alert alert-info">No data available for this report.</div>';
            return $html;
        }

        $html .= '<table class="table table-bordered table-sm">
            <thead>
                <tr>
                    <th>Order ID</th>
                    <th>Date</th>
                    <th>Customer</th>
                    <th>Items</th>
                    <th>Products</th>
                    <th>Amount</th>
                </tr>
            </thead>
            <tbody>';

        foreach ($data as $row) {
            $html .= '<tr>
                <td>#' . str_pad($row['order_id'], 5, '0', STR_PAD_LEFT) . '</td>
                <td>' . date('m/d/y', strtotime($row['order_date'])) . '</td>
                <td>' . $this->safeHtml($row['customer_name']) . '</td>
                <td>' . $row['items_count'] . ' items</td>
                <td>' . $this->safeHtml($row['products']) . '</td>
                <td>ETB ' . $this->formatCurrency($row['total_amount']) . '</td>
            </tr>';
        }

        $html .= '</tbody></table></div>';
        return $html;
    }
} 