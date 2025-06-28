<?php
/**
 * Vehicle Document Expiry Checker
 * 
 * This script checks for vehicle documents (insurance, registration, etc.) 
 * that are expiring in 10 days and sends notifications to vehicle owners and admins.
 * 
 * Usage: Run this script daily via cron job
 * Cron example: 0 9 * * * /usr/bin/php /path/to/check_document_expiry.php
 */

// Set execution time limit for long-running script
set_time_limit(300); // 5 minutes

// Include required files
require_once(__DIR__ . '/../config/database.php');
require_once(__DIR__ . '/../includes/NotificationManager.php');

// Log file for debugging
$logFile = __DIR__ . '/logs/document_expiry_' . date('Y-m-d') . '.log';

function writeLog($message) {
    global $logFile;
    $timestamp = date('Y-m-d H:i:s');
    $logMessage = "[{$timestamp}] {$message}" . PHP_EOL;
    
    // Create logs directory if it doesn't exist
    $logDir = dirname($logFile);
    if (!is_dir($logDir)) {
        mkdir($logDir, 0755, true);
    }
    
    file_put_contents($logFile, $logMessage, FILE_APPEND | LOCK_EX);
    echo $logMessage;
}

function getDocumentRenewalInstructions($documentType) {
    $instructions = [
        'insurance' => 'Contact your insurance provider or visit their office to renew your vehicle insurance policy. Bring your current policy, vehicle registration, and valid ID.',
        'registration' => 'Visit the nearest transport authority office with required documents including current registration, insurance certificate, and technical inspection certificate.',
        'inspection' => 'Schedule a vehicle inspection at an authorized inspection center. Ensure your vehicle is in good condition and bring all required documents.',
        'license' => 'Visit the licensing authority office with required documents including current license, medical certificate (if required), and valid ID.',
        'permit' => 'Contact the relevant authority to renew your special vehicle permit. Check specific requirements for your permit type.',
        'roadworthy' => 'Schedule a roadworthy test at an authorized testing station. Ensure your vehicle meets all safety requirements.'
    ];
    
    return $instructions[$documentType] ?? 'Contact the relevant authority for renewal procedures. Ensure you have all required documents and meet renewal criteria.';
}

try {
    writeLog("Starting document expiry check...");
    
    // Check for documents expiring in exactly 10 days
    $expiryDate = date('Y-m-d', strtotime('+10 days'));
    writeLog("Checking for documents expiring on: {$expiryDate}");
    
    // Query for expiring documents
    $stmt = $conn->prepare("
        SELECT 
            vd.*,
            v.car_brand,
            v.car_model,
            v.car_year,
            v.plate_number,
            v.user_id,
            u.name as owner_name,
            u.email as owner_email,
            u.phonenum as owner_phone
        FROM vehicle_documents vd
        JOIN tbl_vehicles v ON vd.vehicle_id = v.id
        JOIN tbl_user u ON v.user_id = u.id
        WHERE vd.expiry_date = ? 
        AND (vd.reminder_sent = FALSE OR vd.reminder_sent IS NULL)
        AND vd.expiry_date > CURDATE()
        ORDER BY u.name, v.plate_number
    ");
    
    $stmt->execute([$expiryDate]);
    $expiringDocuments = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    writeLog("Found " . count($expiringDocuments) . " expiring documents");
    
    if (empty($expiringDocuments)) {
        writeLog("No documents expiring in 10 days. Exiting.");
        exit(0);
    }
    
    // Initialize notification manager
    $notificationManager = new NotificationManager($conn);
    
    $successCount = 0;
    $errorCount = 0;
    
    foreach ($expiringDocuments as $document) {
        try {
            writeLog("Processing document ID {$document['id']} - {$document['document_type']} for {$document['owner_name']}");
            
            // Prepare notification data for vehicle owner
            $notificationData = [
                'template_key' => 'document_expiry_reminder',
                'subject' => "⚠️ {$document['document_type']} Expiry Reminder - {$document['plate_number']}",
                'variables' => [
                    'owner_name' => $document['owner_name'],
                    'vehicle_info' => trim("{$document['car_year']} {$document['car_brand']} {$document['car_model']}"),
                    'plate_number' => $document['plate_number'],
                    'document_type' => ucfirst($document['document_type']),
                    'document_number' => $document['document_number'] ?? 'N/A',
                    'expiry_date' => date('M d, Y', strtotime($document['expiry_date'])),
                    'days_remaining' => '10',
                    'renewal_instructions' => getDocumentRenewalInstructions($document['document_type']),
                    'issue_date' => $document['issue_date'] ? date('M d, Y', strtotime($document['issue_date'])) : 'N/A',
                    'contact_phone' => '+251 911 123 456', // Company contact
                    'contact_email' => 'support@natiautomotive.com'
                ]
            ];
            
            // Send notification to vehicle owner
            $ownerResult = $notificationManager->sendNotification(
                'document_expiry',
                $document['user_id'],
                $notificationData,
                ['email'] // Start with email only, add telegram if customer has it configured
            );
            
            if ($ownerResult['success']) {
                writeLog("Successfully sent notification to owner: {$document['owner_name']} ({$document['owner_email']})");
            } else {
                writeLog("Failed to send notification to owner: " . ($ownerResult['error'] ?? 'Unknown error'));
                $errorCount++;
                continue;
            }
            
            // Prepare admin notification
            $adminNotificationData = [
                'template_key' => 'admin_document_expiry_alert',
                'subject' => "🚨 Customer Document Expiry Alert - {$document['owner_name']}",
                'variables' => array_merge($notificationData['variables'], [
                    'customer_phone' => $document['owner_phone'],
                    'customer_email' => $document['owner_email'],
                    'vehicle_id' => $document['vehicle_id'],
                    'document_id' => $document['id']
                ])
            ];
            
            // Get active admin users
            $adminStmt = $conn->query("SELECT admin_id, name, email FROM admin WHERE status = 'active'");
            $adminUsers = $adminStmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Send to each admin
            foreach ($adminUsers as $admin) {
                $adminResult = $notificationManager->sendNotification(
                    'admin_document_alert',
                    $admin['admin_id'],
                    $adminNotificationData,
                    ['telegram', 'web'] // Telegram for immediate alert, web for dashboard
                );
                
                if ($adminResult['success']) {
                    writeLog("Successfully sent admin notification to: {$admin['name']}");
                } else {
                    writeLog("Failed to send admin notification to {$admin['name']}: " . ($adminResult['error'] ?? 'Unknown error'));
                }
            }
            
            // Mark reminder as sent
            $updateStmt = $conn->prepare("
                UPDATE vehicle_documents 
                SET reminder_sent = TRUE, 
                    last_reminder_date = CURDATE(),
                    updated_at = NOW()
                WHERE id = ?
            ");
            
            if ($updateStmt->execute([$document['id']])) {
                writeLog("Marked reminder as sent for document ID {$document['id']}");
                $successCount++;
            } else {
                writeLog("Failed to update reminder status for document ID {$document['id']}");
                $errorCount++;
            }
            
            // Small delay to prevent overwhelming the system
            usleep(500000); // 0.5 seconds
            
        } catch (Exception $e) {
            writeLog("Error processing document ID {$document['id']}: " . $e->getMessage());
            $errorCount++;
            continue;
        }
    }
    
    writeLog("Document expiry check completed.");
    writeLog("Successfully processed: {$successCount} documents");
    writeLog("Errors encountered: {$errorCount} documents");
    
    // Send summary notification to system admin if there were any documents processed
    if ($successCount > 0 || $errorCount > 0) {
        try {
            $summaryData = [
                'template_key' => 'system_summary',
                'subject' => "📊 Daily Document Expiry Check Summary",
                'variables' => [
                    'check_date' => date('Y-m-d'),
                    'total_documents' => count($expiringDocuments),
                    'success_count' => $successCount,
                    'error_count' => $errorCount,
                    'expiry_date' => $expiryDate
                ]
            ];
            
            // Send to primary admin (assuming admin_id = 1 is primary)
            $notificationManager->sendNotification(
                'system_summary',
                1, // Primary admin ID
                $summaryData,
                ['web']
            );
            
            writeLog("Summary notification sent to system admin");
        } catch (Exception $e) {
            writeLog("Failed to send summary notification: " . $e->getMessage());
        }
    }
    
} catch (PDOException $e) {
    writeLog("Database error: " . $e->getMessage());
    exit(1);
} catch (Exception $e) {
    writeLog("General error: " . $e->getMessage());
    exit(1);
}

writeLog("Script execution completed successfully.");
exit(0);
?> 