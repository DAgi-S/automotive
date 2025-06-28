                </div>
                <!-- /.container-fluid -->
            </div>
            <!-- End of Main Content -->

        </div>
        <!-- End of Content Wrapper -->

    </div>
    <!-- End of Page Wrapper -->

    <!-- Scroll to Top Button -->
    <a class="scroll-to-top rounded" href="#page-top" style="display: none; position: fixed; right: 1rem; bottom: 1rem; width: 2.75rem; height: 2.75rem; text-align: center; color: #fff; background: rgba(90, 92, 105, 0.5); line-height: 46px; border-radius: 100%; z-index: 1000; transition: all 0.3s;">
        <i class="fas fa-angle-up"></i>
    </a>

    <!-- Logout Modal -->
    <div class="modal fade" id="logoutModal" tabindex="-1" aria-labelledby="logoutModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header border-0">
                    <h5 class="modal-title" id="logoutModalLabel">Ready to Leave?</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p class="mb-0">Select "Logout" below if you are ready to end your current session.</p>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <a href="logout.php" class="btn btn-primary">Logout</a>
                </div>
            </div>
        </div>
    </div>

    <!-- jQuery (required for sb-admin-2.min.js) -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- DataTables JS -->
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.4.1/js/dataTables.responsive.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.4.1/js/responsive.bootstrap5.min.js"></script>
    
    <!-- Custom Admin JS -->
    <script src="js/sb-admin-2.min.js"></script>
    
    <!-- Notification JavaScript -->
    <script>
    // Mark notification as read
    function markNotificationRead(notificationId) {
        fetch('api/notifications/admin_notifications.php', {
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
                // Update the notification count badge
                location.reload(); // Simple reload for now
            }
        })
        .catch(error => {
            console.error('Error marking notification as read:', error);
        });
    }

    // Auto-refresh notifications every 30 seconds
    setInterval(function() {
        fetch('api/notifications/admin_notifications.php?action=unread_count')
        .then(response => response.json())
        .then(data => {
            if (data.success && data.unread_count !== undefined) {
                const badge = document.querySelector('.badge-counter');
                if (data.unread_count > 0) {
                    if (badge) {
                        badge.textContent = data.unread_count > 9 ? '9+' : data.unread_count;
                    } else {
                        // Create badge if it doesn't exist
                        const bellIcon = document.querySelector('#alertsDropdown i');
                        if (bellIcon) {
                            const newBadge = document.createElement('span');
                            newBadge.className = 'badge badge-danger badge-counter';
                            newBadge.textContent = data.unread_count > 9 ? '9+' : data.unread_count;
                            bellIcon.parentNode.appendChild(newBadge);
                        }
                    }
                } else {
                    if (badge) {
                        badge.remove();
                    }
                }
            }
        })
        .catch(error => {
            console.error('Error refreshing notifications:', error);
        });
    }, 30000); // 30 seconds

    // Additional admin functionality (sb-admin-2.js handles sidebar toggle)
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize tooltips if Bootstrap tooltips are available
        if (typeof bootstrap !== 'undefined' && bootstrap.Tooltip) {
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });
        }
        
        // Initialize popovers if Bootstrap popovers are available
        if (typeof bootstrap !== 'undefined' && bootstrap.Popover) {
            var popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'));
            var popoverList = popoverTriggerList.map(function (popoverTriggerEl) {
                return new bootstrap.Popover(popoverTriggerEl);
            });
        }
    });

    // Scroll to top is handled by sb-admin-2.js
    </script>

</body>
</html> 