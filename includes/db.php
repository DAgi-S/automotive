<?php
if (in_array($_SERVER['HTTP_HOST'], ['localhost', '127.0.0.1'])) {
    if (!defined('DB_HOST')) define('DB_HOST', 'localhost');
    if (!defined('DB_NAME')) define('DB_NAME', 'automotive2');
    if (!defined('DB_USER')) define('DB_USER', 'root');
    if (!defined('DB_PASS')) define('DB_PASS', '');
} else {
    if (!defined('DB_HOST')) define('DB_HOST', 'localhost'); // or your host's DB host if different
    if (!defined('DB_NAME')) define('DB_NAME', 'automotive');
    if (!defined('DB_USER')) define('DB_USER', 'nati');
    if (!defined('DB_PASS')) define('DB_PASS', 'Nati-0911');
}

// Create connection
try {
    $conn = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASS);
    // Set the PDO error mode to exception
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    // Log the error and set $conn to null
    error_log("Connection failed: " . $e->getMessage());
    $conn = null;
}

// Function to get database connection
function getConnection() {
    global $conn;
    if (!$conn) {
        try {
            $conn = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASS);
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch(PDOException $e) {
            error_log("Connection failed: " . $e->getMessage());
            return null;
        }
    }
    return $conn;
} 