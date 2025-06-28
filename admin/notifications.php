<?php
// Start session at the very beginning before any output
session_start();

// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

// Include database connection and functions
require_once('../config/database.php');
require_once('includes/dashboard_functions.php');

?>
<?php include("includes/header.php");?>
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Notifications</h6>
        </div>
        <div class="card-body">
            <div class="notification-filters mb-4">
                <div class="btn-group" role="group">
                    <button type="button" class="btn btn-outline-primary active" data-filter="all">All</button>
                    <button type="button" class="btn btn-outline-primary" data-filter="unread">Unread</button>
                    <button type="button" class="btn btn-outline-primary" data-filter="read">Read</button>
                </div>
                <button type="button" class="btn btn-success float-right" id="markAllRead">
                    Mark All as Read
                </button>
            </div>
            
            <div class="notification-list">
                <!-- Notifications will be loaded here -->
            </div>
            
            <div class="text-center mt-4 loading-spinner" style="display: none;">
                <div class="spinner-border text-primary" role="status">
                    <span class="sr-only">Loading...</span>
                </div>
            </div>
            
            <div class="text-center mt-4 load-more" style="display: none;">
                <button type="button" class="btn btn-outline-primary">Load More</button>
            </div>
        </div>
    </div>

<style>
.notification-item {
    padding: 15px;
    border: 1px solid #e3e6f0;
    border-radius: 5px;
    margin-bottom: 10px;
    transition: all 0.3s ease;
}

.notification-item:hover {
    background-color: #f8f9fc;
    transform: translateY(-2px);
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.notification-item.unread {
    background-color: #e8f4ff;
    border-left: 4px solid #4e73df;
}

.notification-time {
    color: #858796;
    font-size: 0.8rem;
}

.notification-message {
    margin: 5px 0 0;
    color: #3a3b45;
}

.notification-type {
    display: inline-block;
    padding: 2px 8px;
    border-radius: 12px;
    font-size: 0.75rem;
    margin-right: 8px;
}

.notification-type.service-booking {
    background-color: #e8f4ff;
    color: #4e73df;
}

.notification-filters {
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.notification-filters .btn-group {
    margin-right: 15px;
}

.load-more {
    margin-top: 20px;
}

.no-notifications {
    text-align: center;
    padding: 40px;
    color: #858796;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const notificationList = document.querySelector('.notification-list');
    const loadMoreBtn = document.querySelector('.load-more button');
    const loadingSpinner = document.querySelector('.loading-spinner');
    const filterButtons = document.querySelectorAll('.notification-filters [data-filter]');
    let currentPage = 1;
    let currentFilter = 'all';
    let loading = false;

    function loadNotifications(page = 1, filter = 'all', append = false) {
        if (loading) return;
        loading = true;
        
        if (!append) {
            notificationList.innerHTML = '';
        }
        
        loadingSpinner.style.display = 'block';
        loadMoreBtn.parentElement.style.display = 'none';

        fetch(`api/notifications/admin_notifications.php?action=list&limit=20&filter=${filter}`)
            .then(response => response.json())
            .then(data => {
                loadingSpinner.style.display = 'none';
                
                if (data.success) {
                    if (data.notifications.length === 0) {
                        notificationList.innerHTML = `
                            <div class="no-notifications">
                                <i class="fas fa-bell-slash fa-3x mb-3"></i>
                                <p>No notifications found</p>
                            </div>
                        `;
                        return;
                    }

                    // Clear existing notifications if not appending
                    if (!append) {
                        notificationList.innerHTML = '';
                    }

                    data.notifications.forEach(notification => {
                        const item = createNotificationItem(notification);
                        notificationList.appendChild(item);
                    });

                    // For now, hide load more button (can be implemented later)
                    loadMoreBtn.parentElement.style.display = 'none';
                }
            })
            .catch(error => {
                console.error('Error loading notifications:', error);
                loadingSpinner.style.display = 'none';
            })
            .finally(() => {
                loading = false;
            });
    }

    function createNotificationItem(notification) {
        const item = document.createElement('div');
        item.className = `notification-item ${notification.is_read == '0' ? 'unread' : ''}`;
        item.dataset.id = notification.id;
        
        // Use formatted_date if available, otherwise format the created_at
        const displayTime = notification.formatted_date || new Date(notification.created_at).toLocaleString();
        
        item.innerHTML = `
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <span class="notification-type ${notification.type}">${notification.type.replace(/_/g, ' ').toUpperCase()}</span>
                    <span class="notification-time">${displayTime}</span>
                </div>
                ${notification.is_read == '0' ? `
                    <button class="btn btn-sm btn-link mark-read" onclick="markAsRead(${notification.id}, event)">
                        Mark as read
                    </button>
                ` : ''}
            </div>
            <p class="notification-message">${notification.message}</p>
        `;
        
        return item;
    }

    // Make markAsRead function global
    window.markAsRead = function(notificationId, event) {
        if (event) {
            event.stopPropagation();
        }
        
        fetch(`api/notifications/admin_notifications.php`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                action: 'mark_read',
                notification_id: notificationId
            })
        })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const item = document.querySelector(`.notification-item[data-id="${notificationId}"]`);
                    if (item) {
                        item.classList.remove('unread');
                        const markReadBtn = item.querySelector('.mark-read');
                        if (markReadBtn) {
                            markReadBtn.remove();
                        }
                    }
                }
            })
            .catch(error => {
                console.error('Error marking notification as read:', error);
            });
    }

    // Event Listeners
    loadMoreBtn.addEventListener('click', () => {
        currentPage++;
        loadNotifications(currentPage, currentFilter, true);
    });

    filterButtons.forEach(button => {
        button.addEventListener('click', () => {
            filterButtons.forEach(btn => btn.classList.remove('active'));
            button.classList.add('active');
            currentFilter = button.dataset.filter;
            
            // Simple client-side filtering for now
            const allItems = document.querySelectorAll('.notification-item');
            allItems.forEach(item => {
                if (currentFilter === 'all') {
                    item.style.display = 'block';
                } else if (currentFilter === 'unread' && item.classList.contains('unread')) {
                    item.style.display = 'block';
                } else if (currentFilter === 'read' && !item.classList.contains('unread')) {
                    item.style.display = 'block';
                } else {
                    item.style.display = 'none';
                }
            });
        });
    });

    document.getElementById('markAllRead').addEventListener('click', () => {
        fetch('api/notifications/admin_notifications.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                action: 'mark_all_read'
            })
        })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    loadNotifications(1, currentFilter);
                }
            });
    });

    // Initial load
    loadNotifications(1, 'all', false);
});
</script>
<?php
include("includes/footer.php");

?> 




