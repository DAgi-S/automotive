<?php
require_once(__DIR__ . '/../../config/database.php');

/**
 * Get dashboard statistics
 * @return array Array containing counts of appointments, customers, services, and workers
 */
function getDashboardStats($conn) {
    try {
        $stats = [];
        
        // Prepare all statements first
        $queries = [
            'appointments' => "SELECT COUNT(*) as count FROM tbl_appointments",
            'customers' => "SELECT COUNT(*) as count FROM tbl_user",
            'services' => "SELECT COUNT(*) as count FROM tbl_services",
            'workers' => "SELECT COUNT(*) as count FROM tbl_worker"
        ];
        
        foreach ($queries as $key => $query) {
            $stmt = $conn->prepare($query);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            $stats[$key] = $result['count'];
        }
        
        return [
            'success' => true,
            'data' => $stats
        ];
    } catch (PDOException $e) {
        error_log("Error getting dashboard stats: " . $e->getMessage());
        return [
            'success' => false,
            'error' => 'Failed to fetch dashboard statistics'
        ];
    }
}

/**
 * Get recent appointments with customer and service details
 * @param int $limit Number of appointments to fetch
 * @return array Array containing recent appointments
 */
function getRecentAppointments($conn, $limit = 5) {
    try {
        $query = "
            SELECT 
                a.*,
                u.name as customer_name,
                s.service_name,
                s.price as service_price
            FROM tbl_appointments a
            LEFT JOIN tbl_user u ON a.user_id = u.id
            LEFT JOIN tbl_services s ON a.service_id = s.service_id
            ORDER BY a.appointment_date DESC, a.appointment_time DESC
            LIMIT :limit
        ";
        
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        
        return [
            'success' => true,
            'data' => $stmt->fetchAll(PDO::FETCH_ASSOC)
        ];
    } catch (PDOException $e) {
        error_log("Error getting recent appointments: " . $e->getMessage());
        return [
            'success' => false,
            'error' => 'Failed to fetch recent appointments'
        ];
    }
}

/**
 * Get recent activities from the system
 * @param int $limit Number of activities to fetch
 * @return array Array containing recent activities
 */
function getRecentActivities($conn, $limit = 5) {
    try {
        $query = "
            (SELECT 
                'appointment' as type,
                'New appointment scheduled' as text,
                created_at as timestamp,
                id
            FROM tbl_appointments
            WHERE created_at >= DATE_SUB(NOW(), INTERVAL 24 HOUR))
            UNION
            (SELECT 
                'customer' as type,
                'New customer registered' as text,
                created_at as timestamp,
                id
            FROM tbl_user
            WHERE created_at >= DATE_SUB(NOW(), INTERVAL 24 HOUR))
            ORDER BY timestamp DESC
            LIMIT :limit
        ";
        
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        
        $activities = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Format the activities for display
        foreach ($activities as &$activity) {
            $activity['time_ago'] = getTimeAgo($activity['timestamp']);
        }
        
        return [
            'success' => true,
            'data' => $activities
        ];
    } catch (PDOException $e) {
        error_log("Error getting recent activities: " . $e->getMessage());
        return [
            'success' => false,
            'error' => 'Failed to fetch recent activities'
        ];
    }
}

/**
 * Convert timestamp to "time ago" format
 * @param string $timestamp The timestamp to convert
 * @return string Formatted time ago string
 */
function getTimeAgo($timestamp) {
    $time = strtotime($timestamp);
    $current = time();
    $diff = $current - $time;
    
    $intervals = [
        31536000 => 'year',
        2592000 => 'month',
        604800 => 'week',
        86400 => 'day',
        3600 => 'hour',
        60 => 'minute',
        1 => 'second'
    ];
    
    foreach ($intervals as $secs => $str) {
        $d = $diff / $secs;
        if ($d >= 1) {
            $r = round($d);
            return $r . ' ' . $str . ($r > 1 ? 's' : '') . ' ago';
        }
    }
    
    return 'just now';
}

/**
 * Update appointment status
 * @param int $appointmentId The appointment ID
 * @param string $status The new status
 * @return array Operation result
 */
function updateAppointmentStatus($conn, $appointmentId, $status) {
    try {
        $conn->beginTransaction();
        
        $query = "
            UPDATE tbl_appointments 
            SET status = :status,
                updated_at = NOW()
            WHERE appointment_id = :id
        ";
        
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':status', $status, PDO::PARAM_STR);
        $stmt->bindParam(':id', $appointmentId, PDO::PARAM_INT);
        $stmt->execute();
        
        // Add to activity log
        $logQuery = "
            INSERT INTO activity_log (type, action, reference_id, created_at)
            VALUES ('appointment', :action, :ref_id, NOW())
        ";
        
        $stmt = $conn->prepare($logQuery);
        $action = "Appointment status updated to " . $status;
        $stmt->bindParam(':action', $action, PDO::PARAM_STR);
        $stmt->bindParam(':ref_id', $appointmentId, PDO::PARAM_INT);
        $stmt->execute();
        
        $conn->commit();
        
        return [
            'success' => true,
            'message' => 'Appointment status updated successfully'
        ];
    } catch (PDOException $e) {
        $conn->rollBack();
        error_log("Error updating appointment status: " . $e->getMessage());
        return [
            'success' => false,
            'error' => 'Failed to update appointment status'
        ];
    }
}

/**
 * Delete appointment
 * @param int $appointmentId The appointment ID
 * @return array Operation result
 */
function deleteAppointment($conn, $appointmentId) {
    try {
        $conn->beginTransaction();
        
        // First check if appointment exists and get its details
        $checkQuery = "SELECT * FROM tbl_appointments WHERE appointment_id = :id";
        $stmt = $conn->prepare($checkQuery);
        $stmt->bindParam(':id', $appointmentId, PDO::PARAM_INT);
        $stmt->execute();
        
        if (!$stmt->fetch()) {
            throw new Exception('Appointment not found');
        }
        
        // Delete the appointment
        $deleteQuery = "DELETE FROM tbl_appointments WHERE appointment_id = :id";
        $stmt = $conn->prepare($deleteQuery);
        $stmt->bindParam(':id', $appointmentId, PDO::PARAM_INT);
        $stmt->execute();
        
        // Log the deletion
        $logQuery = "
            INSERT INTO activity_log (type, action, reference_id, created_at)
            VALUES ('appointment', 'Appointment deleted', :ref_id, NOW())
        ";
        
        $stmt = $conn->prepare($logQuery);
        $stmt->bindParam(':ref_id', $appointmentId, PDO::PARAM_INT);
        $stmt->execute();
        
        $conn->commit();
        
        return [
            'success' => true,
            'message' => 'Appointment deleted successfully'
        ];
    } catch (Exception $e) {
        $conn->rollBack();
        error_log("Error deleting appointment: " . $e->getMessage());
        return [
            'success' => false,
            'error' => $e->getMessage()
        ];
    }
} 