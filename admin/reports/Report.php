<?php
require_once __DIR__ . '/../../includes/config.php';

class Report {
    protected $db;
    protected $startDate;
    protected $endDate;
    protected $format;
    protected $adminId;

    public function __construct($adminId) {
        global $conn;
        $this->db = $conn;
        $this->adminId = $adminId;
    }

    public function setDateRange($startDate, $endDate) {
        $this->startDate = $startDate;
        $this->endDate = $endDate;
    }

    public function setFormat($format) {
        $this->format = $format;
    }

    protected function formatCurrency($amount) {
        return number_format($amount, 2);
    }

    protected function formatDate($date) {
        return date('Y-m-d', strtotime($date));
    }

    protected function executeQuery($sql, $params = []) {
        try {
            $stmt = $this->db->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error executing query: " . $e->getMessage());
            return false;
        }
    }

    protected function saveReport($reportType, $filePath = null) {
        try {
            $sql = "INSERT INTO tbl_reports (report_type, start_date, end_date, format, file_path, admin_id) 
                    VALUES (?, ?, ?, ?, ?, ?)";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                $reportType,
                $this->startDate,
                $this->endDate,
                $this->format,
                $filePath,
                $this->adminId
            ]);
            return $this->db->lastInsertId();
        } catch (PDOException $e) {
            error_log("Error saving report: " . $e->getMessage());
            return false;
        }
    }

    protected function saveReportParameters($reportId, $parameters) {
        try {
            $sql = "INSERT INTO tbl_report_parameters (report_id, parameter_name, parameter_value) 
                    VALUES (?, ?, ?)";
            $stmt = $this->db->prepare($sql);
            
            foreach ($parameters as $name => $value) {
                $stmt->execute([$reportId, $name, $value]);
            }
            return true;
        } catch (PDOException $e) {
            error_log("Error saving report parameters: " . $e->getMessage());
            return false;
        }
    }

    protected function generateCSV($data, $filename) {
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        
        $output = fopen('php://output', 'w');
        
        // Write headers
        if (!empty($data)) {
            fputcsv($output, array_keys($data[0]));
        }
        
        // Write data
        foreach ($data as $row) {
            fputcsv($output, $row);
        }
        
        fclose($output);
        exit;
    }

    protected function generatePDF($data, $filename, $title) {
        require_once '../../vendor/tecnickcom/tcpdf/tcpdf.php';
        
        $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
        
        $pdf->SetCreator('Automotive System');
        $pdf->SetAuthor('Admin');
        $pdf->SetTitle($title);
        
        $pdf->AddPage();
        
        // Add title
        $pdf->SetFont('helvetica', 'B', 16);
        $pdf->Cell(0, 10, $title, 0, 1, 'C');
        $pdf->Ln(10);
        
        // Add data
        $pdf->SetFont('helvetica', '', 10);
        
        // Headers
        if (!empty($data)) {
            $headers = array_keys($data[0]);
            $widths = array_fill(0, count($headers), 40);
            
            $pdf->SetFillColor(240, 240, 240);
            $pdf->SetFont('helvetica', 'B', 10);
            
            for ($i = 0; $i < count($headers); $i++) {
                $pdf->Cell($widths[$i], 7, $headers[$i], 1, 0, 'C', true);
            }
            $pdf->Ln();
            
            // Data
            $pdf->SetFillColor(255, 255, 255);
            $pdf->SetFont('helvetica', '', 10);
            
            foreach ($data as $row) {
                for ($i = 0; $i < count($headers); $i++) {
                    $pdf->Cell($widths[$i], 6, $row[$headers[$i]], 1, 0, 'L', true);
                }
                $pdf->Ln();
            }
        }
        
        $pdf->Output($filename, 'D');
        exit;
    }

    protected function generateExcel($data, $filename) {
        require_once '../../vendor/phpoffice/phpspreadsheet/src/Bootstrap.php';
        
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        
        // Headers
        if (!empty($data)) {
            $headers = array_keys($data[0]);
            $col = 1;
            foreach ($headers as $header) {
                $sheet->setCellValueByColumnAndRow($col++, 1, $header);
            }
            
            // Data
            $row = 2;
            foreach ($data as $item) {
                $col = 1;
                foreach ($headers as $header) {
                    $sheet->setCellValueByColumnAndRow($col++, $row, $item[$header]);
                }
                $row++;
            }
        }
        
        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        
        $writer->save('php://output');
        exit;
    }
} 