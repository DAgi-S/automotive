<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    exit;
}
?>

<div class="notification-bell">
    <div class="dropdown">
        <button class="btn btn-link position-relative" type="button" id="notificationDropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            <i class="fas fa-bell fa-lg"></i>
            <span class="badge badge-danger badge-pill notification-count" style="display: none;">0</span>
        </button>
        <div class="dropdown-menu dropdown-menu-right notification-dropdown" aria-labelledby="notificationDropdown">
            <h6 class="dropdown-header">Notifications</h6>
            <div class="notification-list">
                <!-- Notifications will be loaded here -->
            </div>
            <div class="dropdown-divider"></div>
            <a class="dropdown-item text-center" href="notifications.php">View All</a>
        </div>
    </div>
</div>

<style>
.notification-bell {
    margin-right: 15px;
}

.notification-bell .btn {
    color: #333;
    padding: 0;
}

.notification-bell .badge {
    position: absolute;
    top: -8px;
    right: -8px;
    font-size: 0.7rem;
    min-width: 18px;
    height: 18px;
    line-height: 18px;
    padding: 0 5px;
}

.notification-dropdown {
    width: 350px;
    max-height: 400px;
    overflow-y: auto;
    padding: 0;
}

.notification-dropdown .dropdown-header {
    background: #4e73df;
    color: white;
    padding: 12px 15px;
    font-size: 1rem;
    margin-bottom: 0;
}

.notification-list {
    max-height: 300px;
    overflow-y: auto;
}

.notification-item {
    padding: 12px 15px;
    border-bottom: 1px solid #eee;
    cursor: pointer;
    transition: all 0.2s ease;
}

.notification-item:hover {
    background-color: #f8f9fa;
}

.notification-item.unread {
    background-color: #e8f4ff;
}

.notification-item .notification-time {
    font-size: 0.75rem;
    color: #666;
    margin-bottom: 3px;
}

.notification-item .notification-message {
    margin: 0;
    font-size: 0.85rem;
    line-height: 1.4;
}

.notification-type-icon {
    margin-right: 8px;
    width: 20px;
    text-align: center;
}

/* Notification type styles */
.notification-type-service_booking .notification-type-icon {
    color: #4e73df;
}

.notification-type-appointment_update .notification-type-icon {
    color: #1cc88a;
}

.notification-type-appointment_cancel .notification-type-icon {
    color: #e74a3b;
}

.notification-type-worker_assigned .notification-type-icon {
    color: #f6c23e;
}

.notification-type-service_complete .notification-type-icon {
    color: #36b9cc;
}

.notification-type-payment_received .notification-type-icon {
    color: #2ecc71;
}

/* Empty state */
.no-notifications {
    padding: 20px;
    text-align: center;
    color: #858796;
}

.no-notifications i {
    font-size: 2rem;
    margin-bottom: 10px;
    color: #dddfeb;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const notificationBell = document.querySelector('.notification-bell');
    const notificationCount = notificationBell.querySelector('.notification-count');
    const notificationList = notificationBell.querySelector('.notification-list');
    let lastCount = 0;

    // Get icon for notification type
    function getNotificationIcon(type) {
        const icons = {
            service_booking: 'fa-calendar-plus',
            appointment_update: 'fa-sync',
            appointment_cancel: 'fa-calendar-times',
            worker_assigned: 'fa-user-check',
            service_complete: 'fa-check-circle',
            payment_received: 'fa-dollar-sign'
        };
        return icons[type] || 'fa-bell';
    }

    function updateNotifications() {
        fetch('../api/notification_handler.php?action=get_notifications')
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    if (data.notifications.length === 0) {
                        notificationList.innerHTML = `
                            <div class="no-notifications">
                                <i class="fas fa-bell-slash"></i>
                                <p>No new notifications</p>
                            </div>
                        `;
                        return;
                    }

                    notificationList.innerHTML = '';
                    data.notifications.forEach(notification => {
                        const item = document.createElement('div');
                        item.className = `notification-item ${notification.is_read ? '' : 'unread'} notification-type-${notification.type}`;
                        item.dataset.id = notification.id;
                        
                        const time = new Date(notification.created_at);
                        item.innerHTML = `
                            <div class="d-flex align-items-start">
                                <div class="notification-type-icon">
                                    <i class="fas ${getNotificationIcon(notification.type)}"></i>
                                </div>
                                <div class="flex-grow-1">
                                    <small class="notification-time">${time.toLocaleString()}</small>
                                    <p class="notification-message">${notification.message}</p>
                                </div>
                            </div>
                        `;
                        
                        item.addEventListener('click', () => markAsRead(notification.id));
                        notificationList.appendChild(item);
                    });
                }
            });
    }

    function updateUnreadCount() {
        fetch('../api/notification_handler.php?action=get_unread_count')
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const count = parseInt(data.count);
                    if (count > 0) {
                        notificationCount.textContent = count;
                        notificationCount.style.display = 'block';
                        
                        // Play sound if count increased
                        if (count > lastCount) {
                            playNotificationSound();
                        }
                    } else {
                        notificationCount.style.display = 'none';
                    }
                    lastCount = count;
                }
            });
    }

    function markAsRead(notificationId) {
        fetch(`../api/notification_handler.php?action=mark_read&notification_id=${notificationId}`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const item = document.querySelector(`.notification-item[data-id="${notificationId}"]`);
                    if (item) {
                        item.classList.remove('unread');
                    }
                    updateUnreadCount();
                }
            });
    }

    function playNotificationSound() {
        // Check sound preference for the notification type
        $.ajax({
            url: '../api/check_sound_preference.php',
            method: 'GET',
            data: {
                admin_id: <?php echo $_SESSION['admin_id']; ?>
            },
            success: function(response) {
                if (response.sound_enabled) {
                    const audio = new Audio('../assets/sounds/notification.mp3');
                    audio.play().catch(function(error) {
                        console.log("Error playing sound: ", error);
                    });
                }
            },
            error: function(xhr, status, error) {
                console.error("Error checking sound preference:", error);
            }
        });
    }

    // Initial load
    updateNotifications();
    updateUnreadCount();

    // Update every 30 seconds
    setInterval(() => {
        updateNotifications();
        updateUnreadCount();
    }, 30000);

    // Close dropdown when clicking outside
    document.addEventListener('click', function(event) {
        if (!notificationBell.contains(event.target)) {
            const dropdown = notificationBell.querySelector('.dropdown-menu');
            if (dropdown.classList.contains('show')) {
                dropdown.classList.remove('show');
            }
        }
    });
});
</script> 