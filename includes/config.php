<?php
// Error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Prevent session from starting automatically
$preventSessionStart = true;

// Session configuration
if (session_status() === PHP_SESSION_NONE) {
    // Configure session settings before starting the session
    ini_set('session.cookie_httponly', 1);
    ini_set('session.use_only_cookies', 1);
    ini_set('session.cookie_secure', isset($_SERVER['HTTPS']));
    
    // Now start the session
    session_start();
}

// Include database configuration first
require_once(__DIR__ . '/../config/database.php');

// Application configuration
define('SITE_URL', 'http://localhost/automotive/');
define('ADMIN_URL', SITE_URL . 'admin/');
define('UPLOADS_PATH', __DIR__ . '/../uploads/');
define('UPLOADS_URL', SITE_URL . 'uploads/');

// Timezone
date_default_timezone_set('UTC');

// Security headers
header('X-Frame-Options: SAMEORIGIN');
header('X-XSS-Protection: 1; mode=block');
header('X-Content-Type-Options: nosniff');

/**
 * Get database connection
 * @return PDO
 */
function getDBConnection() {
    static $pdo = null;
    
    if ($pdo === null) {
        try {
            $dsn = sprintf("mysql:host=%s;dbname=%s;charset=%s", DB_HOST, DB_NAME, DB_CHARSET);
            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ];
            
            $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
        } catch (PDOException $e) {
            error_log("Connection failed: " . $e->getMessage());
            die("Database connection failed. Please try again later.");
        }
    }
    
    return $pdo;
}

/**
 * Execute a query and return results
 * @param string $sql
 * @param array $params
 * @param bool $single
 * @return array|bool
 */
function executeQuery($sql, $params = [], $single = false) {
    try {
        $stmt = getDBConnection()->prepare($sql);
        $stmt->execute($params);
        return $single ? $stmt->fetch() : $stmt->fetchAll();
    } catch (PDOException $e) {
        error_log("Query execution failed: " . $e->getMessage());
        return false;
    }
}

// Function to sanitize output
function clean($string) {
    return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
}

// Function to generate CSRF token
function generateCSRFToken() {
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

// Function to verify CSRF token
function verifyCSRFToken($token) {
    if (!isset($_SESSION['csrf_token']) || $token !== $_SESSION['csrf_token']) {
        die('CSRF token validation failed');
    }
    return true;
}

// Database credentials are now environment-based in config/database.php 