<?php
require_once '../config/database.php';

// session_start(); // Remove this line to avoid duplicate session_start notice
if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit();
}

if (!isset($_GET['id'])) {
    die('Checklist ID is required');
}
$history_id = intval($_GET['id']);

// Fetch company branding and info
$branding = $conn->query('SELECT * FROM company_branding LIMIT 1')->fetch(PDO::FETCH_ASSOC);
$info = $conn->query('SELECT * FROM company_information LIMIT 1')->fetch(PDO::FETCH_ASSOC);

// Fetch checklist info
$stmt = $conn->prepare('
    SELECT sh.*, i.plate_number, i.car_brand, i.car_model, i.car_year,
           u.name AS customer_name, u.email AS customer_email, u.phonenum AS customer_phone,
           w.full_name AS mechanic_name
    FROM tbl_service_history sh
    LEFT JOIN tbl_info i ON sh.vehicle_id = i.id
    LEFT JOIN tbl_user u ON i.userid = u.id
    LEFT JOIN tbl_worker w ON sh.mechanic_id = w.id
    WHERE sh.history_id = :id
');
$stmt->execute([':id' => $history_id]);
$checklist = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$checklist) {
    die('Checklist not found');
}
// Fetch checklist items
$stmt = $conn->prepare('
    SELECT si.*, s.service_name
    FROM tbl_service_items si
    LEFT JOIN tbl_services s ON si.service_id = s.service_id
    WHERE si.service_history_id = :id
');
$stmt->execute([':id' => $history_id]);
$items = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Service Checklist Print Preview</title>
    <style>
        @page { size: A4; margin: 1cm; }
        body { font-family: Arial, sans-serif; font-size: 10pt; background: #fff; }
        .header { text-align: left; border-bottom: 1px solid #000; margin-bottom: 10px; }
        .header h2 { margin: 0; font-size: 18pt; }
        .company-branding { display: flex; align-items: center; justify-content: left; margin-bottom: 5px; }
        .company-logo { width: 100px; height: 100px; margin-right: 12px; }
        .company-details { font-size: 10pt; text-align: left; }
        .section { margin-bottom: 15px; }
        .section-title { font-weight: bold; font-size: 12pt; margin-bottom: 5px; border-bottom: 1px solid #eee; }
        .info-table, .checklist-table { width: 100%; border-collapse: collapse; margin-bottom: 10px; }
        .info-table td, .checklist-table th, .checklist-table td { border: 1px solid #ccc; padding: 5px; }
        .checklist-table th { background: #f8f8f8; }
        .signature-section { margin-top: 40px; display: flex; justify-content: space-between; }
        .signature-box { width: 45%; text-align: center; }
        .signature-line { border-bottom: 1px solid #000; margin: 30px 0 5px 0; height: 2px; }
        .label { font-size: 9pt; color: #555; }
    </style>
</head>
<body onload="window.print()">
    <div class="header">
        <div class="company-branding">
            <?php if (!empty($branding['logo_url'])): ?>
                <img src="<?php echo htmlspecialchars($branding['logo_url']); ?>" class="company-logo" alt="Logo">
            <?php endif; ?>
            <div class="company-details">
                <strong><?php echo htmlspecialchars($branding['company_name'] ?? 'Automotive Service Center'); ?></strong><br>
                <?php echo htmlspecialchars($info['address'] ?? ''); ?>
                <?php if (!empty($info['phone'])): ?> | <?php echo htmlspecialchars($info['phone']); ?><?php endif; ?>
                <?php if (!empty($info['email'])): ?> | <?php echo htmlspecialchars($info['email']); ?><?php endif; ?>
                <?php if (!empty($info['website'])): ?> | <a href="<?php echo htmlspecialchars($info['website']); ?>" target="_blank"><?php echo htmlspecialchars($info['website']); ?></a><?php endif; ?>
            </div>
        </div>        <br><br><br>
                    
        <h2>Service Checklist</h2>
        <div class="company-info">
            Generated on <?php echo date('F j, Y'); ?>
        </div>
    </div>
    <div class="section">
        <div class="section-title">Vehicle & Customer Information</div>
        <table class="info-table">
            <tr>
                <td><span class="label">Vehicle</span><br><?php echo htmlspecialchars($checklist['car_brand'] . ' ' . $checklist['car_model'] . ' (' . $checklist['car_year'] . ')'); ?></td>
                <td><span class="label">Plate Number</span><br><?php echo htmlspecialchars($checklist['plate_number']); ?></td>
                <td><span class="label">Customer</span><br><?php echo htmlspecialchars($checklist['customer_name']); ?></td>
                <td><span class="label">Contact</span><br><?php echo htmlspecialchars($checklist['customer_phone']); ?><br><?php echo htmlspecialchars($checklist['customer_email']); ?></td>
            </tr>
            <tr>
                <td colspan="2"><span class="label">Assigned Worker</span><br><?php echo htmlspecialchars($checklist['mechanic_name'] ?? 'N/A'); ?></td>
                <td colspan="2"><span class="label">Service Date</span><br><?php echo htmlspecialchars($checklist['service_date']); ?></td>
            </tr>
        </table>
    </div>
    <div class="section">
        <div class="section-title">Service Checklist Items</div>
        <table class="checklist-table">
            <thead>
                <tr>
                    <th>Service</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($items as $item): ?>
                <tr>
                    <td><?php echo htmlspecialchars($item['service_name']); ?></td>
                    <td><?php echo htmlspecialchars($item['status']); ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <div class="section">
        <div class="section-title">Additional Notes</div>
        <div><?php echo nl2br(htmlspecialchars($checklist['notes'])); ?></div>
    </div>
    <div class="signature-section">
        <div class="signature-box">
            <div class="signature-line"></div>
            <div>Customer Signature</div>
        </div>
        <div class="signature-box">
            <div class="signature-line"></div>
            <div>Mechanic Signature</div>
        </div>
    </div>
</body>
</html> 