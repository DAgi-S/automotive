<?php
/**
 * Notification Bell Component
 * Displays notification dropdown for logged-in users
 */

// Ensure this file is only included, not accessed directly
if (!defined('INCLUDED')) {
    exit;
}

// Check if user is logged in
$user_id = $_SESSION['user_id'] ?? $_SESSION['admin_id'] ?? null;
if (!$user_id) {
    return; // Don't display notification bell for non-logged users
}
?>

<!-- Notification Dropdown -->
<div class="dropdown notification-dropdown">
    <div class="notification-bell dropdown-toggle" id="notificationDropdown" 
         data-bs-toggle="dropdown" aria-expanded="false" 
         aria-label="Notifications">
        <i class="fas fa-bell"></i>
        <span class="notification-badge d-none" id="notificationBadge">0</span>
    </div>
    
    <div class="dropdown-menu dropdown-menu-end notification-menu" 
         aria-labelledby="notificationDropdown">
        <!-- Notification Header -->
        <div class="notification-header">
            <h6 class="mb-0">
                <i class="fas fa-bell me-2"></i>Notifications
            </h6>
            <div class="notification-actions">
                <button class="btn btn-sm btn-outline-primary" id="markAllRead" 
                        title="Mark all as read">
                    <i class="fas fa-check-double"></i>
                </button>
                <button class="btn btn-sm btn-outline-secondary" id="refreshNotifications" 
                        title="Refresh">
                    <i class="fas fa-sync-alt"></i>
                </button>
            </div>
        </div>
        
        <!-- Notification List -->
        <div class="notification-list" id="notificationList">
            <div class="notification-loading text-center py-3">
                <div class="spinner-border spinner-border-sm text-primary" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
                <div class="mt-2 small text-muted">Loading notifications...</div>
            </div>
        </div>
        
        <!-- Notification Footer -->
        <div class="notification-footer">
            <a href="notifications.php" class="btn btn-sm btn-primary w-100">
                <i class="fas fa-eye me-1"></i>View All Notifications
            </a>
        </div>
    </div>
</div>

<!-- Enhanced Notification Styles -->
<style>
/* Notification Dropdown Styles */
.notification-dropdown .notification-bell {
    position: relative;
    background: rgba(255, 255, 255, 0.1);
    border: 2px solid rgba(255, 255, 255, 0.2);
    color: var(--white);
    border-radius: 50%;
    width: 45px;
    height: 45px;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: var(--transition);
    cursor: pointer;
    border: none;
    outline: none;
}

.notification-dropdown .notification-bell:hover {
    background: var(--warning-color);
    border-color: var(--warning-color);
    color: var(--dark-color);
    transform: scale(1.1);
    box-shadow: 0 8px 25px rgba(255, 193, 7, 0.3);
}

.notification-dropdown .notification-bell.has-notifications {
    animation: shake 1s ease-in-out infinite;
}

@keyframes shake {
    0%, 100% { transform: translateX(0); }
    25% { transform: translateX(-2px); }
    75% { transform: translateX(2px); }
}

.notification-dropdown .notification-badge {
    position: absolute;
    top: -5px;
    right: -5px;
    background: var(--danger-color);
    color: var(--white);
    border-radius: 50%;
    width: 18px;
    height: 18px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 0.7rem;
    font-weight: 600;
    border: 2px solid var(--white);
}

.notification-menu {
    width: 350px;
    max-height: 500px;
    border: none;
    border-radius: 15px;
    box-shadow: 0 10px 40px rgba(0, 0, 0, 0.15);
    padding: 0;
    margin-top: 0.5rem;
    overflow: hidden;
}

.notification-header {
    background: linear-gradient(135deg, var(--primary-color), var(--primary-dark));
    color: white;
    padding: 1rem;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.notification-header h6 {
    margin: 0;
    font-weight: 600;
}

.notification-actions {
    display: flex;
    gap: 0.5rem;
}

.notification-actions .btn {
    border-radius: 20px;
    padding: 0.25rem 0.5rem;
    border-color: rgba(255, 255, 255, 0.3);
    color: white;
}

.notification-actions .btn:hover {
    background: rgba(255, 255, 255, 0.2);
    border-color: white;
    color: white;
}

.notification-list {
    max-height: 300px;
    overflow-y: auto;
    padding: 0;
}

.notification-item {
    padding: 1rem;
    border-bottom: 1px solid #e9ecef;
    transition: all 0.3s ease;
    cursor: pointer;
    position: relative;
}

.notification-item:last-child {
    border-bottom: none;
}

.notification-item:hover {
    background: #f8f9fa;
}

.notification-item.unread {
    background: linear-gradient(90deg, rgba(0, 123, 255, 0.05), transparent);
    border-left: 3px solid var(--primary-color);
}

.notification-item.unread::before {
    content: '';
    position: absolute;
    top: 1rem;
    right: 1rem;
    width: 8px;
    height: 8px;
    background: var(--primary-color);
    border-radius: 50%;
}

.notification-content {
    display: flex;
    align-items: flex-start;
    gap: 0.75rem;
}

.notification-icon {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.1rem;
    flex-shrink: 0;
}

.notification-icon.info {
    background: rgba(0, 123, 255, 0.1);
    color: var(--primary-color);
}

.notification-icon.success {
    background: rgba(40, 167, 69, 0.1);
    color: var(--success-color);
}

.notification-icon.warning {
    background: rgba(255, 193, 7, 0.1);
    color: var(--warning-color);
}

.notification-icon.danger {
    background: rgba(220, 53, 69, 0.1);
    color: var(--danger-color);
}

.notification-text {
    flex: 1;
}

.notification-title {
    font-weight: 600;
    font-size: 0.9rem;
    color: var(--dark-color);
    margin-bottom: 0.25rem;
}

.notification-message {
    font-size: 0.8rem;
    color: var(--secondary-color);
    margin-bottom: 0.25rem;
    line-height: 1.4;
}

.notification-time {
    font-size: 0.75rem;
    color: #adb5bd;
}

.notification-empty {
    text-align: center;
    padding: 2rem 1rem;
    color: var(--secondary-color);
}

.notification-empty i {
    font-size: 2rem;
    margin-bottom: 0.5rem;
    opacity: 0.5;
}

.notification-footer {
    background: #f8f9fa;
    padding: 0.75rem;
    border-top: 1px solid #e9ecef;
}

.notification-loading {
    padding: 2rem 1rem;
}

/* Mobile Optimizations */
@media (max-width: 576px) {
    .notification-menu {
        width: 300px;
        margin-right: -50px;
    }
    
    .notification-item {
        padding: 0.75rem;
    }
    
    .notification-content {
        gap: 0.5rem;
    }
    
    .notification-icon {
        width: 35px;
        height: 35px;
        font-size: 1rem;
    }
}

/* Scrollbar Styling */
.notification-list::-webkit-scrollbar {
    width: 4px;
}

.notification-list::-webkit-scrollbar-track {
    background: #f1f1f1;
}

.notification-list::-webkit-scrollbar-thumb {
    background: #c1c1c1;
    border-radius: 2px;
}

.notification-list::-webkit-scrollbar-thumb:hover {
    background: #a8a8a8;
}
</style>

<!-- Enhanced Notification JavaScript -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const notificationBell = document.getElementById('notificationBell');
    const notificationBadge = document.getElementById('notificationBadge');
    const notificationList = document.getElementById('notificationList');
    const markAllReadBtn = document.getElementById('markAllRead');
    const refreshBtn = document.getElementById('refreshNotifications');
    
    let currentNotifications = [];
    
    // Load notifications
    function loadNotifications() {
        const loadingHtml = `
            <div class="notification-loading text-center py-3">
                <div class="spinner-border spinner-border-sm text-primary" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
                <div class="mt-2 small text-muted">Loading notifications...</div>
            </div>
        `;
        
        if (notificationList) {
            notificationList.innerHTML = loadingHtml;
        }
        
        fetch('api/notifications/get.php?user_id=<?php echo $user_id; ?>&limit=10')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                currentNotifications = data.notifications || [];
                displayNotifications(currentNotifications);
                updateNotificationCount(data.counts?.unread || 0);
            } else {
                showNotificationError('Failed to load notifications');
            }
        })
        .catch(error => {
            console.error('Error loading notifications:', error);
            showNotificationError('Network error occurred');
        });
    }
    
    // Display notifications
    function displayNotifications(notifications) {
        if (!notificationList) return;
        
        if (notifications.length === 0) {
            notificationList.innerHTML = `
                <div class="notification-empty">
                    <i class="fas fa-bell-slash d-block"></i>
                    <div>No notifications</div>
                    <small>You're all caught up!</small>
                </div>
            `;
            return;
        }
        
        const notificationsHtml = notifications.map(notification => {
            const timeAgo = formatTimeAgo(notification.created_at);
            const iconClass = getNotificationIcon(notification.type);
            const isUnread = notification.is_read == 0;
            
            return `
                <div class="notification-item ${isUnread ? 'unread' : ''}" 
                     data-id="${notification.id}">
                    <div class="notification-content">
                        <div class="notification-icon ${notification.type}">
                            <i class="${iconClass}"></i>
                        </div>
                        <div class="notification-text">
                            <div class="notification-title">${escapeHtml(notification.title)}</div>
                            <div class="notification-message">${escapeHtml(notification.message)}</div>
                            <div class="notification-time">${timeAgo}</div>
                        </div>
                    </div>
                </div>
            `;
        }).join('');
        
        notificationList.innerHTML = notificationsHtml;
        
        // Add click handlers
        notificationList.querySelectorAll('.notification-item').forEach(item => {
            item.addEventListener('click', function() {
                const notificationId = this.dataset.id;
                markAsRead(notificationId);
            });
        });
    }
    
    // Update notification count
    function updateNotificationCount(count) {
        if (notificationBadge) {
            if (count > 0) {
                notificationBadge.textContent = count > 99 ? '99+' : count;
                notificationBadge.classList.remove('d-none');
                if (notificationBell) {
                    notificationBell.classList.add('has-notifications');
                }
            } else {
                notificationBadge.classList.add('d-none');
                if (notificationBell) {
                    notificationBell.classList.remove('has-notifications');
                }
            }
        }
    }
    
    // Mark notification as read
    function markAsRead(notificationId) {
        fetch('api/notifications/mark-read.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                notification_id: notificationId
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Update UI
                const item = document.querySelector(`[data-id="${notificationId}"]`);
                if (item) {
                    item.classList.remove('unread');
                }
                // Refresh count
                loadNotificationCount();
            }
        })
        .catch(error => {
            console.error('Error marking notification as read:', error);
        });
    }
    
    // Mark all as read
    function markAllAsRead() {
        fetch('api/notifications/mark-all-read.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                user_id: <?php echo $user_id; ?>
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Update UI
                document.querySelectorAll('.notification-item.unread').forEach(item => {
                    item.classList.remove('unread');
                });
                updateNotificationCount(0);
            }
        })
        .catch(error => {
            console.error('Error marking all as read:', error);
        });
    }
    
    // Load notification count only
    function loadNotificationCount() {
        fetch('api/get_notifications_count.php')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                updateNotificationCount(data.unread || 0);
            }
        })
        .catch(error => {
            console.error('Error loading notification count:', error);
        });
    }
    
    // Show error message
    function showNotificationError(message) {
        if (notificationList) {
            notificationList.innerHTML = `
                <div class="notification-empty">
                    <i class="fas fa-exclamation-triangle d-block text-warning"></i>
                    <div>Error</div>
                    <small>${message}</small>
                </div>
            `;
        }
    }
    
    // Helper functions
    function getNotificationIcon(type) {
        const icons = {
            'info': 'fas fa-info-circle',
            'success': 'fas fa-check-circle',
            'warning': 'fas fa-exclamation-triangle',
            'danger': 'fas fa-exclamation-circle',
            'appointment': 'fas fa-calendar-check',
            'order': 'fas fa-shopping-bag',
            'message': 'fas fa-envelope'
        };
        return icons[type] || 'fas fa-bell';
    }
    
    function formatTimeAgo(dateString) {
        const date = new Date(dateString);
        const now = new Date();
        const diffInSeconds = Math.floor((now - date) / 1000);
        
        if (diffInSeconds < 60) return 'Just now';
        if (diffInSeconds < 3600) return `${Math.floor(diffInSeconds / 60)}m ago`;
        if (diffInSeconds < 86400) return `${Math.floor(diffInSeconds / 3600)}h ago`;
        if (diffInSeconds < 604800) return `${Math.floor(diffInSeconds / 86400)}d ago`;
        
        return date.toLocaleDateString();
    }
    
    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }
    
    // Event listeners
    if (markAllReadBtn) {
        markAllReadBtn.addEventListener('click', markAllAsRead);
    }
    
    if (refreshBtn) {
        refreshBtn.addEventListener('click', function() {
            this.querySelector('i').classList.add('fa-spin');
            loadNotifications();
            setTimeout(() => {
                this.querySelector('i').classList.remove('fa-spin');
            }, 1000);
        });
    }
    
    // Load notifications when dropdown is opened
    const notificationDropdown = document.getElementById('notificationDropdown');
    if (notificationDropdown) {
        notificationDropdown.addEventListener('shown.bs.dropdown', function() {
            loadNotifications();
        });
    }
    
    // Initial load of notification count
    loadNotificationCount();
    
    // Auto-refresh notification count every 30 seconds
    setInterval(loadNotificationCount, 30000);
});
</script> 