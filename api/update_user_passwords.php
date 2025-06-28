<?php
/**
 * Password Migration Script
 * This script updates old MD5 passwords to secure bcrypt hashing
 * Run this once to upgrade existing user passwords
 */

require_once '../config/database.php';

// Set execution time limit for large datasets
set_time_limit(300);

try {
    // Get all users with MD5 passwords (32 character length)
    $stmt = $conn->prepare("SELECT id, email, password FROM tbl_user WHERE LENGTH(password) = 32");
    $stmt->execute();
    $users = $stmt->fetchAll();
    
    $updated_count = 0;
    $total_count = count($users);
    
    echo "Found {$total_count} users with MD5 passwords to upgrade.\n\n";
    
    foreach ($users as $user) {
        // Generate a temporary secure password (users will need to reset)
        // Or you can use a default password and force users to change it
        $temp_password = 'TempPass123!'; // Users should change this on first login
        $secure_hash = password_hash($temp_password, PASSWORD_DEFAULT);
        
        // Update the password
        $update_stmt = $conn->prepare("UPDATE tbl_user SET password = ? WHERE id = ?");
        $result = $update_stmt->execute([$secure_hash, $user['id']]);
        
        if ($result) {
            $updated_count++;
            echo "✓ Updated password for user ID: {$user['id']} (Email: {$user['email']})\n";
        } else {
            echo "✗ Failed to update password for user ID: {$user['id']} (Email: {$user['email']})\n";
        }
    }
    
    echo "\n=== Migration Complete ===\n";
    echo "Successfully updated: {$updated_count}/{$total_count} passwords\n";
    echo "\nIMPORTANT NOTES:\n";
    echo "1. All users now have temporary password: 'TempPass123!'\n";
    echo "2. Users should change their passwords on next login\n";
    echo "3. Consider sending password reset emails to all users\n";
    echo "4. Delete this file after migration is complete\n";
    
} catch (PDOException $e) {
    echo "Database error: " . $e->getMessage() . "\n";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

// Optional: Log the migration
$log_entry = date('Y-m-d H:i:s') . " - Password migration completed. Updated {$updated_count}/{$total_count} passwords\n";
file_put_contents('../logs/password_migration.log', $log_entry, FILE_APPEND | LOCK_EX);

?> 