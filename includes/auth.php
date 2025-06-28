<?php
require_once 'config.php';

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * Check if user is logged in
 * @return bool
 */
function isLoggedIn() {
    return isset($_SESSION['admin_id']) && !empty($_SESSION['admin_id']);
}

/**
 * Check if user has admin privileges
 * @return bool
 */
function hasAdminPrivileges() {
    if (!isset($_SESSION['admin_id'])) {
        return false;
    }

    try {
        global $conn;
        $sql = "SELECT position, status FROM tbl_admin WHERE id = ? AND status = 'active'";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$_SESSION['admin_id']]);
        $admin = $stmt->fetch(PDO::FETCH_ASSOC);

        return $admin && in_array($admin['position'], ['admin', 'superadmin']);
    } catch (PDOException $e) {
        error_log("Error checking admin privileges: " . $e->getMessage());
        return false;
    }
}

/**
 * Authenticate admin user
 * @param string $username
 * @param string $password
 * @return bool
 */
function authenticateAdmin($username, $password) {
    try {
        global $conn;
        
        // Verify database connection
        if (!isset($conn) || !($conn instanceof PDO)) {
            error_log("Database connection not available in authenticate Admin");
            return false;
        }

        $sql = "SELECT id, username, full_name, password, position, status 
                FROM tbl_admin 
                WHERE username = ? AND status = 'active'";
        
        $stmt = $conn->prepare($sql);
        $stmt->execute([$username]);
        $admin = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$admin) {
            error_log("Admin not found or not active: " . $username);
            return false;
        }

        // For debugging
        error_log("Attempting password verification for: " . $username);
        error_log("Stored hash: " . $admin['password']);

        if (password_verify($password, $admin['password'])) {
            // Clear any existing session data
            session_unset();
            
            // Set session variables
            $_SESSION['admin_id'] = $admin['id'];
            $_SESSION['admin_name'] = $admin['full_name'];
            $_SESSION['admin_username'] = $admin['username'];
            $_SESSION['admin_position'] = $admin['position'];
            $_SESSION['last_activity'] = time();
            
            // Regenerate session ID for security
            session_regenerate_id(true);
            
            error_log("Authentication successful for: " . $username);
            return true;
        }

        error_log("Password verification failed for: " . $username);
        return false;
    } catch (PDOException $e) {
        error_log("Database error in authenticateAdmin: " . $e->getMessage());
        return false;
    } catch (Exception $e) {
        error_log("General error in authenticateAdmin: " . $e->getMessage());
        return false;
    }
}

/**
 * Log out user
 */
function logout() {
    // Unset all session variables
    $_SESSION = array();

    // Destroy the session cookie
    if (isset($_COOKIE[session_name()])) {
        setcookie(session_name(), '', time() - 3600, '/');
    }

    // Destroy the session
    session_destroy();

    // Redirect to login page
    header('Location: ../admin/login.php');
    exit;
}

/**
 * Get current admin user info
 * @return array|null
 */
function getCurrentAdmin() {
    if (!isLoggedIn()) {
        return null;
    }

    try {
        global $conn;
        $sql = "SELECT * FROM tbl_admin WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$_SESSION['admin_id']]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Error getting admin info: " . $e->getMessage());
        return null;
    }
}

// Check for session timeout (30 minutes)
if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > 1800)) {
    logout();
} else if (isset($_SESSION['last_activity'])) {
    $_SESSION['last_activity'] = time();
}

/**
 * Get current user info
 * @return array|null
 */
function getCurrentUser() {
    if (!isLoggedIn()) {
        return null;
    }

    try {
        $userId = $_SESSION['admin_id'] ?? $_SESSION['user_id'];
        $table = isset($_SESSION['admin_id']) ? 'tbl_admin' : 'tbl_user';
        
        $sql = "SELECT * FROM {$table} WHERE id = ?";
        $stmt = getDBConnection()->prepare($sql);
        $stmt->execute([$userId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Error getting current user: " . $e->getMessage());
        return null;
    }
}

/**
 * Check if user has specific permission
 * @param string $permission
 * @return bool
 */
function hasPermission($permission) {
    if (!isLoggedIn() || !hasAdminPrivileges()) {
        return false;
    }

    try {
        $sql = "SELECT permissions FROM tbl_admin WHERE id = ?";
        $stmt = getDBConnection()->prepare($sql);
        $stmt->execute([$_SESSION['admin_id']]);
        $admin = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$admin) {
            return false;
        }

        $permissions = json_decode($admin['permissions'], true);
        return is_array($permissions) && in_array($permission, $permissions);
    } catch (PDOException $e) {
        error_log("Error checking permission: " . $e->getMessage());
        return false;
    }
} 