<?php
require_once 'config/database.php';

try {
    // Set a simple password for testing
    $email = 'admin@automotive.com';
    $new_password = 'admin123';
    
    // Hash the password securely
    $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
    
    // Update the user password
    $stmt = $conn->prepare("UPDATE tbl_user SET password = ? WHERE email = ?");
    $result = $stmt->execute([$hashed_password, $email]);
    
    if ($result) {
        echo "<h2>✅ Password Updated Successfully!</h2>";
        echo "<p><strong>Email:</strong> $email</p>";
        echo "<p><strong>New Password:</strong> $new_password</p>";
        echo "<p><strong>Password Hash:</strong> " . substr($hashed_password, 0, 30) . "...</p>";
        echo "<hr>";
        echo "<p><a href='login.php'>← Go to Login Page</a></p>";
        echo "<p><a href='login_debug.php'>🔧 Test with Debug Tool</a></p>";
    } else {
        echo "<h2>❌ Failed to update password</h2>";
    }
    
} catch (Exception $e) {
    echo "<h2>❌ Error:</h2>";
    echo "<p>" . $e->getMessage() . "</p>";
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Set Admin Password</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .success { color: green; }
        .error { color: red; }
    </style>
</head>
<body>
    <p><strong>Note:</strong> This script sets the admin password to 'admin123' for testing purposes.</p>
</body>
</html> 