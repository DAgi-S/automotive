// Dashboard JavaScript
document.addEventListener('DOMContentLoaded', function() {
    // Initialize Sidebar Toggle
    initializeSidebar();
    
    // Initialize Bootstrap Components
    initializeBootstrapComponents();
    
    // Initialize Search
    initializeSearch();
    
    // Initialize Responsive Tables
    initializeResponsiveTables();
});

// Sidebar Functions
function initializeSidebar() {
    const sidebarToggle = document.body.querySelector('#sidebarToggle');
    const sidebarToggleTop = document.body.querySelector('#sidebarToggleTop');
    
    if (sidebarToggle) {
        sidebarToggle.addEventListener('click', function(e) {
            e.preventDefault();
            toggleSidebar();
        });
    }

    if (sidebarToggleTop) {
        sidebarToggleTop.addEventListener('click', function(e) {
            e.preventDefault();
            toggleSidebar();
        });
    }

    // Handle window resize
    window.addEventListener('resize', handleWindowResize);
}

function toggleSidebar() {
    document.body.classList.toggle('sidebar-toggled');
    const sidebar = document.querySelector('.sidebar');
    if (sidebar) {
        sidebar.classList.toggle('toggled');
        
        if (sidebar.classList.contains('toggled')) {
            const collapseElements = sidebar.querySelectorAll('.collapse');
            collapseElements.forEach(element => element.classList.remove('show'));
        }
    }
}

function handleWindowResize() {
    const vw = Math.max(document.documentElement.clientWidth || 0, window.innerWidth || 0);
    
    if (vw < 768) {
        const sidebar = document.querySelector('.sidebar');
        if (sidebar) {
            const collapseElements = sidebar.querySelectorAll('.collapse');
            collapseElements.forEach(element => element.classList.remove('show'));
            document.body.classList.remove('sidebar-toggled');
            sidebar.classList.remove('toggled');
        }
    }
}

// Bootstrap Components Initialization
function initializeBootstrapComponents() {
    try {
        // Initialize tooltips
        const tooltipElements = document.querySelectorAll('[data-bs-toggle="tooltip"]');
        tooltipElements.forEach(element => {
            try {
                new bootstrap.Tooltip(element);
            } catch (error) {
                console.warn('Error initializing tooltip:', error);
            }
        });

        // Initialize popovers
        const popoverElements = document.querySelectorAll('[data-bs-toggle="popover"]');
        popoverElements.forEach(element => {
            try {
                new bootstrap.Popover(element);
            } catch (error) {
                console.warn('Error initializing popover:', error);
            }
        });
    } catch (error) {
        console.warn('Error initializing Bootstrap components:', error);
    }
}

// Search Functionality
function initializeSearch() {
    const searchForm = document.querySelector('.navbar-search');
    if (searchForm) {
        searchForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const searchInput = this.querySelector('input');
            if (!searchInput) return;
            
            const searchTerm = searchInput.value.trim();
            if (searchTerm) {
                window.location.href = `search_results.php?q=${encodeURIComponent(searchTerm)}`;
            }
        });
    }
}

// Responsive Tables
function initializeResponsiveTables() {
    document.querySelectorAll('.table-responsive').forEach(table => {
        if (!table.closest('.table-wrapper')) {
            const wrapper = document.createElement('div');
            wrapper.classList.add('table-wrapper');
            table.parentNode.insertBefore(wrapper, table);
            wrapper.appendChild(table);
        }
    });
}

// Notification Functions
window.markNotificationAsRead = function(notificationId) {
    if (!notificationId) return;

    fetch('api/notifications/mark-read.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({ id: notificationId })
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('Network response was not ok');
        }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            updateNotificationUI(notificationId);
        }
    })
    .catch(error => {
        console.error('Error marking notification as read:', error);
    });
};

function updateNotificationUI(notificationId) {
    // Update notification item
    const notificationElement = document.querySelector(`[data-notification-id="${notificationId}"]`);
    if (notificationElement) {
        notificationElement.classList.remove('unread');
    }
    
    // Update counter
    const counter = document.querySelector('.badge-counter');
    if (counter) {
        const currentCount = parseInt(counter.textContent);
        if (currentCount > 1) {
            counter.textContent = currentCount - 1;
        } else {
            counter.style.display = 'none';
        }
    }
} 