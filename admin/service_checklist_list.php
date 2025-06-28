<?php
session_start();


// Debug information
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once('../config/database.php');
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}
require_once('includes/header.php');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Service Checklist List</title>
    <link rel="stylesheet" href="css/custom-admin.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css">
</head>
<body>
<div class="container-fluid mt-4">
    <h2 class="mb-4">Service Checklists</h2>
    <div class="card">
        <div class="card-body">
        <button class="btn btn-primary float-end" onclick="window.location.href='service_checklist.php'">Add Service Checklist</button>
        <br>
        <br>
        <br>
            <div class="table-responsive">
                <table id="checklistTable" class="table table-bordered table-hover">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Vehicle</th>
                            <th>Customer</th>
                            <th>Assigned Worker</th>
                            <th>Service Date</th>
                            <th>Notes</th>
                            <th>Created At</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php
                    $sql = "SELECT sh.history_id, sh.service_date, sh.notes, sh.created_at,
                                   i.plate_number, i.car_brand, i.car_model, i.car_year,
                                   u.name AS customer_name, u.email AS customer_email, u.phonenum AS customer_phone,
                                   w.full_name AS mechanic_name
                            FROM tbl_service_history sh
                            LEFT JOIN tbl_info i ON sh.vehicle_id = i.id
                            LEFT JOIN tbl_user u ON i.userid = u.id
                            LEFT JOIN tbl_worker w ON sh.mechanic_id = w.id
                            ORDER BY sh.created_at DESC";
                    $stmt = $conn->query($sql);
                    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                        echo '<tr>';
                        echo '<td>' . htmlspecialchars($row['history_id']) . '</td>';
                        echo '<td>' . htmlspecialchars($row['car_brand'] . ' ' . $row['car_model'] . ' (' . $row['car_year'] . ') - ' . $row['plate_number']) . '</td>';
                        echo '<td>' . htmlspecialchars($row['customer_name']) . '<br><small>' . htmlspecialchars($row['customer_email']) . '<br>' . htmlspecialchars($row['customer_phone']) . '</small></td>';
                        echo '<td>' . htmlspecialchars($row['mechanic_name'] ?? 'N/A') . '</td>';
                        echo '<td>' . htmlspecialchars($row['service_date']) . '</td>';
                        echo '<td>' . nl2br(htmlspecialchars($row['notes'])) . '</td>';
                        echo '<td>' . htmlspecialchars($row['created_at']) . '</td>';
                        echo '<td><a href="service_checklist_print.php?id=' . urlencode($row['history_id']) . '" target="_blank" class="btn btn-sm btn-secondary"><i class="fas fa-print"></i> Print</a></td>';
                        echo '</tr>';
                    }
                    ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
<script>
$(document).ready(function() {
    $('#checklistTable').DataTable({
        order: [[0, 'desc']],
        pageLength: 10,
        responsive: true
    });
});
</script>
</body>
</html> 