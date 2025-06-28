<?php
/**
 * Add a new notification for an admin
 * 
 * @param PDO $conn Database connection
 * @param int $admin_id The ID of the admin to notify
 * @param string $message The notification message
 * @return bool True if successful, false otherwise
 */
function addNotification($conn, $admin_id, $message) {
    try {
        $stmt = $conn->prepare("
            INSERT INTO notifications (admin_id, message) 
            VALUES (?, ?)
        ");
        return $stmt->execute([$admin_id, $message]);
    } catch (PDOException $e) {
        error_log("Error adding notification: " . $e->getMessage());
        return false;
    }
}

/**
 * Mark a notification as read
 * 
 * @param PDO $conn Database connection
 * @param int $notification_id The ID of the notification
 * @param int $admin_id The ID of the admin (for security)
 * @return bool True if successful, false otherwise
 */
function markNotificationAsRead($conn, $notification_id, $admin_id) {
    try {
        $stmt = $conn->prepare("
            UPDATE notifications 
            SET is_read = 1 
            WHERE id = ? AND admin_id = ?
        ");
        return $stmt->execute([$notification_id, $admin_id]);
    } catch (PDOException $e) {
        error_log("Error marking notification as read: " . $e->getMessage());
        return false;
    }
}

/**
 * Get unread notifications count for an admin
 * 
 * @param PDO $conn Database connection
 * @param int $admin_id The ID of the admin
 * @return int Number of unread notifications
 */
function getUnreadNotificationsCount($conn, $admin_id) {
    try {
        $stmt = $conn->prepare("
            SELECT COUNT(*) as count 
            FROM notifications 
            WHERE admin_id = ? AND is_read = 0
        ");
        $stmt->execute([$admin_id]);
        $result = $stmt->fetch();
        return $result ? (int)$result['count'] : 0;
    } catch (PDOException $e) {
        error_log("Error getting notification count: " . $e->getMessage());
        return 0;
    }
}

/**
 * Get recent notifications for an admin
 * 
 * @param PDO $conn Database connection
 * @param int $admin_id The ID of the admin
 * @param int $limit Maximum number of notifications to return
 * @return array Array of notifications
 */
function getRecentNotifications($conn, $admin_id, $limit = 5) {
    try {
        $stmt = $conn->prepare("
            SELECT * FROM notifications 
            WHERE admin_id = ? 
            ORDER BY created_at DESC 
            LIMIT ?
        ");
        $stmt->execute([$admin_id, $limit]);
        return $stmt->fetchAll();
    } catch (PDOException $e) {
        error_log("Error getting recent notifications: " . $e->getMessage());
        return [];
    }
}

/**
 * Delete old notifications
 * 
 * @param PDO $conn Database connection
 * @param int $days Number of days to keep notifications for
 * @return bool True if successful, false otherwise
 */
function cleanOldNotifications($conn, $days = 30) {
    try {
        $stmt = $conn->prepare("
            DELETE FROM notifications 
            WHERE created_at < DATE_SUB(NOW(), INTERVAL ? DAY)
            AND is_read = 1
        ");
        return $stmt->execute([$days]);
    } catch (PDOException $e) {
        error_log("Error cleaning old notifications: " . $e->getMessage());
        return false;
    }
} 