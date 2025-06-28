<?php
require_once __DIR__ . '/Report.php';

class InventoryReport extends Report {
    protected $reportType;
    protected $startDate;
    protected $endDate;

    public function __construct($adminId) {
        parent::__construct($adminId);
        $this->reportType = 'inventory';
    }

    public function setDateRange($startDate, $endDate) {
        $this->startDate = $startDate;
        $this->endDate = $endDate;
    }

    public function generate() {
        $data = $this->getInventoryData();
        $reportId = $this->saveReport('inventory');
        
        if ($reportId) {
            $this->saveReportParameters($reportId, [
                'total_items' => count($data),
                'low_stock' => $this->countLowStockItems($data),
                'out_of_stock' => $this->countOutOfStockItems($data),
                'total_value' => $this->calculateTotalValue($data)
            ]);
        }
        
        switch ($this->format) {
            case 'csv':
                $this->generateCSV($data, 'inventory_report.csv');
                break;
            case 'pdf':
                $this->generatePDF($data, 'inventory_report.pdf', 'Inventory Report');
                break;
            case 'excel':
                $this->generateExcel($data, 'inventory_report.xlsx');
                break;
            default:
                return $this->generateHTML($data);
        }
    }
    
    public function getInventoryData() {
        $sql = "SELECT 
                    p.id,
                    p.name,
                    c.category_name,
                    p.quantity,
                    p.unit_price,
                    p.reorder_level,
                    (p.quantity * p.unit_price) as total_value,
                    p.last_updated
                FROM tbl_products p
                LEFT JOIN tbl_categories c ON p.category_id = c.category_id
                ORDER BY p.name ASC";
                
        try {
            $stmt = $this->db->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error fetching inventory data: " . $e->getMessage());
            return [];
        }
    }
    
    protected function countLowStockItems($data) {
        return count(array_filter($data, function($item) {
            return $item['quantity'] > 0 && $item['quantity'] <= $item['reorder_level'];
        }));
    }
    
    protected function countOutOfStockItems($data) {
        return count(array_filter($data, function($item) {
            return $item['quantity'] == 0;
        }));
    }
    
    protected function calculateTotalValue($data) {
        return array_sum(array_column($data, 'total_value'));
    }
    
    protected function generateCSV($data, $filename) {
        $headers = ['Product ID', 'Name', 'Category', 'Quantity', 'Unit Price', 'Total Value', 'Status', 'Last Updated'];
        $rows = [];
        
        foreach ($data as $row) {
            $status = $row['quantity'] == 0 ? 'Out of Stock' : 
                    ($row['quantity'] <= $row['reorder_level'] ? 'Low Stock' : 'In Stock');
            
            $rows[] = [
                $row['id'],
                $row['name'],
                $row['category_name'],
                $row['quantity'],
                $row['unit_price'],
                $row['total_value'],
                $status,
                $row['last_updated']
            ];
        }
        
        parent::generateCSV($headers, $rows, $filename);
    }
    
    protected function generateExcel($data, $filename) {
        return $this->generateCSV($data, $filename);
    }
    
    protected function generatePDF($data, $filename, $title) {
        // Use parent's PDF generation method
        parent::generatePDF($this->generateHTML($data), $filename, $title);
    }
    
    protected function generateHTML($data) {
        $totalItems = count($data);
        $lowStock = $this->countLowStockItems($data);
        $outOfStock = $this->countOutOfStockItems($data);
        $totalValue = $this->calculateTotalValue($data);
        
        $html = '<div class="report-container">';
        $html .= '<div class="report-header">';
        $html .= '<h2>Inventory Report</h2>';
        $html .= '<div class="company-info">Generated on: ' . date('F j, Y h:i A') . '</div>';
        $html .= '</div>';
        
        $html .= '<div class="report-summary">';
        $html .= '<div class="summary-item">Total Items: ' . number_format($totalItems) . '</div>';
        $html .= '<div class="summary-item">Low Stock Items: ' . number_format($lowStock) . '</div>';
        $html .= '<div class="summary-item">Out of Stock: ' . number_format($outOfStock) . '</div>';
        $html .= '<div class="summary-item">Total Value: ETB ' . number_format($totalValue, 2) . '</div>';
        $html .= '</div>';
        
        if (empty($data)) {
            $html .= '<div class="alert alert-info">No inventory data available.</div>';
            return $html;
        }
        
        $html .= '<table class="table table-bordered">';
        $html .= '<thead><tr>';
        $html .= '<th>Product ID</th>';
        $html .= '<th>Name</th>';
        $html .= '<th>Category</th>';
        $html .= '<th>Quantity</th>';
        $html .= '<th>Unit Price</th>';
        $html .= '<th>Total Value</th>';
        $html .= '<th>Status</th>';
        $html .= '</tr></thead>';
        
        $html .= '<tbody>';
        foreach ($data as $row) {
            $statusClass = $row['quantity'] == 0 ? 'danger' : 
                        ($row['quantity'] <= $row['reorder_level'] ? 'warning' : 'success');
            $status = $row['quantity'] == 0 ? 'Out of Stock' : 
                    ($row['quantity'] <= $row['reorder_level'] ? 'Low Stock' : 'In Stock');
            
            $html .= '<tr>';
            $html .= '<td>#' . str_pad($row['id'], 6, '0', STR_PAD_LEFT) . '</td>';
            $html .= '<td>' . htmlspecialchars($row['name']) . '</td>';
            $html .= '<td>' . htmlspecialchars($row['category_name']) . '</td>';
            $html .= '<td>' . number_format($row['quantity']) . '</td>';
            $html .= '<td>ETB ' . number_format($row['unit_price'], 2) . '</td>';
            $html .= '<td>ETB ' . number_format($row['total_value'], 2) . '</td>';
            $html .= '<td><span class="badge badge-' . $statusClass . '">' . $status . '</span></td>';
            $html .= '</tr>';
        }
        $html .= '</tbody>';
        $html .= '</table>';
        $html .= '</div>';
        
        return $html;
    }
} 