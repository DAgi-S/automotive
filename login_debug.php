<?php 
session_start();
require_once 'config/database.php';

// Debug mode - show all errors
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h2>Login Debug Tool</h2>";

// Test database connection
try {
    echo "<p>✅ Database connection successful</p>";
    echo "<p>Database: " . DB_NAME . "</p>";
} catch (Exception $e) {
    echo "<p>❌ Database connection failed: " . $e->getMessage() . "</p>";
}

// Show users in database
try {
    $stmt = $conn->prepare("SELECT id, name, email, role FROM tbl_user LIMIT 5");
    $stmt->execute();
    $users = $stmt->fetchAll();
    
    echo "<h3>Users in Database:</h3>";
    echo "<table border='1'>";
    echo "<tr><th>ID</th><th>Name</th><th>Email</th><th>Role</th></tr>";
    foreach ($users as $user) {
        echo "<tr>";
        echo "<td>" . $user['id'] . "</td>";
        echo "<td>" . $user['name'] . "</td>";
        echo "<td>" . $user['email'] . "</td>";
        echo "<td>" . $user['role'] . "</td>";
        echo "</tr>";
    }
    echo "</table>";
} catch (Exception $e) {
    echo "<p>❌ Error fetching users: " . $e->getMessage() . "</p>";
}

// Handle login attempt
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['test_login'])) {
    echo "<h3>Login Test Results:</h3>";
    
    $email = sanitize_input($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    
    echo "<p>Email: " . htmlspecialchars($email) . "</p>";
    echo "<p>Password length: " . strlen($password) . "</p>";
    
    try {
        $stmt = $conn->prepare("SELECT * FROM tbl_user WHERE email = ? LIMIT 1");
        $stmt->execute([$email]);
        $user = $stmt->fetch();
        
        if ($user) {
            echo "<p>✅ User found in database</p>";
            echo "<p>User ID: " . $user['id'] . "</p>";
            echo "<p>User Name: " . $user['name'] . "</p>";
            echo "<p>Password hash (first 20 chars): " . substr($user['password'], 0, 20) . "...</p>";
            
            // Test password verification
            if (password_verify($password, $user['password'])) {
                echo "<p>✅ Password verified with password_verify()</p>";
            } elseif (md5($password) === $user['password']) {
                echo "<p>⚠️ Password matches with MD5 (old format)</p>";
            } else {
                echo "<p>❌ Password does not match</p>";
                echo "<p>MD5 of entered password: " . md5($password) . "</p>";
            }
        } else {
            echo "<p>❌ User not found with email: " . htmlspecialchars($email) . "</p>";
        }
    } catch (Exception $e) {
        echo "<p>❌ Database error: " . $e->getMessage() . "</p>";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Login Debug</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        table { border-collapse: collapse; margin: 10px 0; }
        th, td { padding: 8px; border: 1px solid #ccc; }
        .test-form { background: #f5f5f5; padding: 20px; margin: 20px 0; }
    </style>
</head>
<body>
    <div class="test-form">
        <h3>Test Login</h3>
        <form method="POST">
            <p>
                <label>Email:</label><br>
                <input type="email" name="email" value="admin@automotive.com" required style="width: 300px; padding: 5px;">
            </p>
            <p>
                <label>Password:</label><br>
                <input type="password" name="password" required style="width: 300px; padding: 5px;">
            </p>
            <p>
                <button type="submit" name="test_login" style="padding: 10px 20px;">Test Login</button>
            </p>
        </form>
    </div>
    
    <p><a href="login.php">← Back to Login Page</a></p>
</body>
</html> 