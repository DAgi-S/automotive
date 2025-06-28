<?php
// Include configuration first
require_once(__DIR__ . '/../../includes/config.php');

// Check if user is logged in
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="Automotive Service Admin Dashboard">
    <title><?php echo isset($pageTitle) ? "Auto Admin - " . $pageTitle : "Auto Admin"; ?></title>

    <!-- Favicon -->
    <link rel="icon" type="image/svg+xml" href="../assets/img/favicon.ico">

    <!-- Custom fonts -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@200;300;400;600;700;800;900&display=swap" rel="stylesheet">

    <!-- Core CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="css/sb-admin-2.min.css" rel="stylesheet">
    <link href="css/custom-admin.css" rel="stylesheet">
    
    <!-- DataTables CSS -->
    <link href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/responsive/2.4.1/css/responsive.bootstrap5.min.css" rel="stylesheet">

    <style>
    :root {
        --sidebar-width: 14rem;
        --sidebar-width-collapsed: 6.5rem;
        --topbar-height: 4rem;
        --primary-color: #4e73df;
        --secondary-color: #858796;
        --success-color: #1cc88a;
        --info-color: #36b9cc;
        --warning-color: #f6c23e;
        --danger-color: #e74a3b;
    }

    body {
        background-color: #f8f9fc;
        font-family: 'Nunito', sans-serif;
    }
    
    #wrapper {
        display: flex;
    }
    
    .sidebar {
        width: var(--sidebar-width);
        min-height: 100vh;
        position: fixed;
        top: 0;
        left: 0;
        z-index: 100;
        transition: all 0.3s ease;
        box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
    }
    
    #content-wrapper {
        margin-left: var(--sidebar-width);
        min-height: 100vh;
        transition: all 0.3s ease;
        background: #f8f9fc;
    }

    .sidebar.toggled {
        width: var(--sidebar-width-collapsed);
    }

    .sidebar.toggled + #content-wrapper,
    body.sidebar-toggled .sidebar + #content-wrapper {
        margin-left: var(--sidebar-width-collapsed);
    }
    
    body.sidebar-toggled .sidebar {
        width: var(--sidebar-width-collapsed);
    }

    .topbar {
        height: var(--topbar-height);
        padding: 0 1.5rem;
        background: #fff;
        box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
        border-bottom: 1px solid #e3e6f0;
        display: flex !important;
        align-items: center !important;
        justify-content: space-between !important;
        position: relative;
    }

    .topbar .navbar-nav {
        align-items: center;
        flex-direction: row;
        margin: 0;
    }

    .topbar .nav-item {
        margin-bottom: 0;
    }

    .topbar .nav-link {
        color: #5a5c69 !important;
        padding: 0.5rem 0.75rem;
        transition: all 0.15s ease;
        border-radius: 0.35rem;
        margin: 0 0.25rem;
        display: flex;
        align-items: center;
    }

    .topbar .nav-link:hover {
        color: #3a3b45 !important;
        background-color: #f8f9fc;
    }

    .topbar .page-title {
        margin: 0;
        font-size: 1.35rem;
        font-weight: 600;
        color: #5a5c69;
    }

    .container-fluid {
        padding: 1.25rem;
    }

    .card {
        border: none;
        border-radius: 0.5rem;
        box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.1);
        margin-bottom: 1.25rem;
    }

    .card-header {
        background: transparent;
        padding: 1rem 1.25rem;
        border-bottom: 1px solid rgba(0,0,0,.05);
    }

    .table-responsive {
        margin: 0;
    }

    .table {
        margin-bottom: 0;
    }

    .table th {
        font-weight: 600;
        text-transform: uppercase;
        font-size: 0.8rem;
        border-top: 0;
        vertical-align: middle;
    }

    .btn {
        border-radius: 0.35rem;
        padding: 0.375rem 0.75rem;
        font-size: 0.875rem;
        font-weight: 400;
        line-height: 1.5;
    }

    .btn-sm {
        padding: 0.25rem 0.5rem;
        font-size: 0.875rem;
        line-height: 1.5;
        border-radius: 0.25rem;
    }

    .nav-item {
        position: relative;
        margin-bottom: 0.25rem;
    }

    .nav-link {
        display: flex;
        align-items: center;
        padding: 0.75rem 1rem;
        color: rgba(255,255,255,.8);
        font-weight: 500;
        transition: all 0.15s ease;
    }

    .nav-link:hover {
        color: #fff;
        background: rgba(255,255,255,.1);
    }

    .nav-link.active {
        color: #fff;
        background: rgba(255,255,255,.1);
    }

    .nav-link i {
        margin-right: 0.75rem;
        font-size: 0.875rem;
    }

    .sidebar-heading {
        padding: 0.875rem 1rem;
        font-size: 0.75rem;
        font-weight: 800;
        text-transform: uppercase;
        color: rgba(255,255,255,.4);
    }

    .sidebar-divider {
        border-top: 1px solid rgba(255,255,255,.15);
        margin: 1rem 0;
    }

    @media (max-width: 768px) {
        .sidebar {
            width: var(--sidebar-width-collapsed);
        }
        
        #content-wrapper {
            width: calc(100% - var(--sidebar-width-collapsed));
            margin-left: var(--sidebar-width-collapsed);
        }
        
        .sidebar.toggled {
            margin-left: calc(-1 * var(--sidebar-width-collapsed));
        }
        
        .sidebar.toggled + #content-wrapper {
            width: 100%;
            margin-left: 0;
        }

        .container-fluid {
            padding: 1rem;
        }

        .topbar {
            padding: 0 1rem;
        }

        .topbar .nav-link {
            padding: 0.5rem 0.75rem;
        }

        .topbar .dropdown-list {
            width: 18rem;
            max-width: calc(100vw - 2rem);
        }

        .img-profile {
            height: 1.8rem;
            width: 1.8rem;
        }

        .topbar-divider {
            display: none;
        }
    }

    /* Notification Styles */
    .badge-counter {
        position: absolute;
        transform: scale(0.8);
        transform-origin: top right;
        right: 0.1rem;
        top: -0.2rem;
        min-width: 1.2rem;
        height: 1.2rem;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.65rem;
        font-weight: 700;
        border-radius: 50%;
        background-color: #e74a3b !important;
        color: white !important;
        border: 2px solid #fff;
    }

    .dropdown-list {
        padding: 0;
        border: none;
        overflow: hidden;
        width: 20rem;
        max-width: 20rem;
    }

    .dropdown-list .dropdown-header {
        background-color: #4e73df;
        border: 1px solid #4e73df;
        padding-top: 0.75rem;
        padding-bottom: 0.75rem;
        color: #fff;
    }

    .dropdown-list .dropdown-item {
        white-space: normal;
        padding: 1rem;
        border-left: 1px solid #e3e6f0;
        border-right: 1px solid #e3e6f0;
        border-bottom: 1px solid #e3e6f0;
        line-height: 1.3rem;
    }

    .dropdown-list .dropdown-item .icon-circle {
        height: 2.5rem;
        width: 2.5rem;
        border-radius: 100%;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .dropdown-list .dropdown-item:hover {
        background-color: #eaecf4;
    }

    .dropdown-list .dropdown-item.show {
        color: #212529;
    }

    .img-profile {
        height: 2.2rem;
        width: 2.2rem;
        border: 2px solid #e3e6f0;
        transition: all 0.15s ease;
    }

    .img-profile:hover {
        border-color: #4e73df;
    }

    .topbar-divider {
        width: 0;
        border-right: 1px solid #e3e6f0;
        height: 2rem;
        margin: 0 0.75rem;
        opacity: 0.3;
        align-self: center;
    }

    /* User dropdown improvements */
    .topbar .dropdown-toggle::after {
        display: none !important;
    }

    .topbar .dropdown-menu {
        border: none;
        box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
        border-radius: 0.35rem;
        margin-top: 0.5rem;
        min-width: 12rem;
    }

    .topbar .dropdown-item {
        padding: 0.75rem 1.25rem;
        transition: all 0.15s ease;
        font-size: 0.875rem;
    }

    .topbar .dropdown-item:hover {
        background-color: #f8f9fc;
        color: #3a3b45;
    }

    .topbar .dropdown-header {
        padding: 1rem 1.25rem 0.5rem;
        font-size: 0.875rem;
    }

    /* Notification bell improvements */
    .notification-bell {
        position: relative;
        display: flex !important;
        align-items: center !important;
        justify-content: center !important;
        width: 2.5rem;
        height: 2.5rem;
        border-radius: 50%;
        transition: all 0.15s ease;
    }

    .notification-bell .fas.fa-bell {
        font-size: 1.15rem;
        color: #5a5c69;
        transition: all 0.15s ease;
    }

    .notification-bell:hover {
        background-color: #f8f9fc !important;
        transform: scale(1.05);
    }

    .notification-bell:hover .fas.fa-bell {
        color: #4e73df;
    }

    /* User profile improvements */
    .user-profile-link {
        display: flex !important;
        align-items: center !important;
        padding: 0.5rem 0.75rem !important;
        border-radius: 0.5rem !important;
        transition: all 0.15s ease;
        text-decoration: none !important;
    }

    .user-profile-link:hover {
        background-color: #f8f9fc !important;
        transform: translateY(-1px);
    }

    .user-profile-link .img-profile {
        width: 2rem;
        height: 2rem;
        border-radius: 50%;
        border: 2px solid #e3e6f0;
        margin-right: 0.5rem;
        object-fit: cover;
    }

    .user-profile-link .user-name {
        font-size: 0.875rem;
        font-weight: 600;
        color: #5a5c69;
        margin: 0;
        white-space: nowrap;
    }

    .user-profile-link:hover .user-name {
        color: #3a3b45;
    }

    /* Responsive adjustments */
    @media (max-width: 576px) {
        .user-profile-link .user-name {
            display: none;
        }
        
        .topbar-divider {
            display: none !important;
        }
        
        .page-title {
            font-size: 1.1rem !important;
        }
    }
    </style>
</head>

<body id="page-top">
    <div id="wrapper">
        <!-- Sidebar -->
        <ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">
            <a class="sidebar-brand d-flex align-items-center justify-content-center" href="dashboard.php">
                <div class="sidebar-brand-icon">
                    <i class="fas fa-car"></i>
                </div>
                <div class="sidebar-brand-text mx-3">Auto Admin</div>
            </a>

            <hr class="sidebar-divider">

            <li class="nav-item <?php echo basename($_SERVER['PHP_SELF']) == 'dashboard.php' ? 'active' : ''; ?>">
                <a class="nav-link" href="dashboard.php">
                    <i class="fas fa-fw fa-tachometer-alt"></i>
                    <span>Dashboard</span>
                </a>
            </li>

            <li class="nav-item <?php echo basename($_SERVER['PHP_SELF']) == 'appointments.php' ? 'active' : ''; ?>">
                <a class="nav-link" href="appointments.php">
                    <i class="fas fa-fw fa-calendar"></i>
                    <span>Appointments</span>
                </a>
            </li>
            <li class="nav-item <?php echo basename($_SERVER['PHP_SELF']) == 'orders.php' ? 'active' : ''; ?>">
                <a class="nav-link" href="orders.php">
                    <i class="fas fa-fw fa-calendar"></i>
                    <span>Orders</span>
                </a>
            </li>
            <li class="nav-item <?php echo basename($_SERVER['PHP_SELF']) == 'cars.php' ? 'active' : ''; ?>">
                <a class="nav-link" href="cars.php">
                    <i class="fas fa-fw fa-car"></i>
                    <span>cars</span>
                </a>
            </li>

            <li class="nav-item <?php echo basename($_SERVER['PHP_SELF']) == 'services.php' ? 'active' : ''; ?>">
                <a class="nav-link" href="services.php">
                    <i class="fas fa-fw fa-wrench"></i>
                    <span>Services</span>
                </a>
            </li>

            <li class="nav-item <?php echo basename($_SERVER['PHP_SELF']) == 'customers.php' ? 'active' : ''; ?>">
                <a class="nav-link" href="customers.php">
                    <i class="fas fa-fw fa-users"></i>
                    <span>Customers</span>
                </a>
            </li>
            <li class="nav-item <?php echo basename($_SERVER['PHP_SELF']) == 'admin_chat.php' ? 'active' : ''; ?>">
                <a class="nav-link" href="admin_chat.php">
                    <i class="fas fa-fw fa-message"></i>
                    <span>Chats</span>
                </a>
            </li>

            <li class="nav-item <?php echo basename($_SERVER['PHP_SELF']) == 'workers.php' ? 'active' : ''; ?>">
                <a class="nav-link" href="workers.php">
                    <i class="fas fa-fw fa-user-tie"></i>
                    <span>Workers</span>
                </a>
            </li>
            <li class="nav-item <?php echo basename($_SERVER['PHP_SELF']) == 'service_checklist_list.php' ? 'active' : ''; ?>">
                <a class="nav-link" href="service_checklist_list.php">
                    <i class="fas fa-fw fa-user-tie"></i>
                    <span>Service Checklist</span>
                </a>
            </li>
            <hr class="sidebar-divider">

            <div class="sidebar-heading">
                Inventory
            </div>

            <li class="nav-item <?php echo basename($_SERVER['PHP_SELF']) == 'brands.php' ? 'active' : ''; ?>">
                <a class="nav-link" href="brands.php">
                    <i class="fas fa-fw fa-tags"></i>
                    <span>Brands</span>
                </a>
            </li>

            <li class="nav-item <?php echo basename($_SERVER['PHP_SELF']) == 'categories.php' ? 'active' : ''; ?>">
                <a class="nav-link" href="categories.php">
                    <i class="fas fa-fw fa-list"></i>
                    <span>Categories</span>
                </a>
            </li>

            <li class="nav-item <?php echo basename($_SERVER['PHP_SELF']) == 'products.php' ? 'active' : ''; ?>">
                <a class="nav-link" href="products.php">
                    <i class="fas fa-fw fa-box"></i>
                    <span>Products</span>
                </a>
            </li>

            <hr class="sidebar-divider">

            <div class="sidebar-heading">
                Content
            </div>

            <li class="nav-item <?php echo basename($_SERVER['PHP_SELF']) == 'articles.php' ? 'active' : ''; ?>">
                <a class="nav-link" href="articles.php">
                    <i class="fas fa-fw fa-newspaper"></i>
                    <span>Articles</span>
                </a>
            </li>

            <li class="nav-item <?php echo basename($_SERVER['PHP_SELF']) == 'blogs.php' ? 'active' : ''; ?>">
                <a class="nav-link" href="blogs.php">
                    <i class="fas fa-fw fa-blog"></i>
                    <span>Blogs</span>
                </a>
            </li>

            <li class="nav-item <?php echo basename($_SERVER['PHP_SELF']) == 'website-content.php' ? 'active' : ''; ?>">
                <a class="nav-link" href="website-content.php">
                    <i class="fas fa-fw fa-globe"></i>
                    <span>Website Content</span>
                </a>
            </li>

            <li class="nav-item <?php echo basename($_SERVER['PHP_SELF']) == 'ads-management.php' ? 'active' : ''; ?>">
                <a class="nav-link" href="ads-management.php">
                    <i class="fas fa-fw fa-ad"></i>
                    <span>Advertisement</span>
                </a>
            </li>

            <hr class="sidebar-divider">

            <div class="sidebar-heading">
                Reports
            </div>

            <li class="nav-item">
                <a class="nav-link" href="reports/">
                    <i class="fas fa-fw fa-chart-area"></i>
                    <span>Reports Dashboard</span>
                </a>
            </li>

            <hr class="sidebar-divider d-none d-md-block">

            <div class="text-center d-none d-md-inline">
                <button class="rounded-circle border-0" id="sidebarToggle">
                    <i class="fas fa-angle-left"></i>
                </button>
            </div>
        </ul>

        <div id="content-wrapper" class="d-flex flex-column">
            <!-- Topbar -->
            <nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">
                <!-- Left Side -->
                <div class="d-flex align-items-center">
                    <!-- Sidebar Toggle (Topbar) -->
                    <button id="sidebarToggleTop" class="btn btn-link d-md-none me-3" style="color: #5a5c69;">
                        <i class="fa fa-bars"></i>
                    </button>

                    <!-- Page Title -->
                    <h1 class="page-title d-none d-md-block">
                        <?php 
                        $page_titles = [
                            'dashboard.php' => 'Dashboard',
                            'appointments.php' => 'Appointments',
                            'orders.php' => 'Orders',
                            'cars.php' => 'Cars',
                            'services.php' => 'Services',
                            'customers.php' => 'Customers',
                            'workers.php' => 'Workers',
                            'brands.php' => 'Brands',
                            'categories.php' => 'Categories',
                            'products.php' => 'Products',
                            'articles.php' => 'Articles',
                            'blogs.php' => 'Blogs',
                            'website-content.php' => 'Website Content Management',
                            'ads-management.php' => 'Advertisement Management'
                        ];
                        $current_page = basename($_SERVER['PHP_SELF']);
                        echo isset($page_titles[$current_page]) ? $page_titles[$current_page] : 'Admin Panel';
                        ?>
                    </h1>
                </div>

                <!-- Right Side - Topbar Navbar -->
                <ul class="navbar-nav d-flex align-items-center">
                    <!-- Nav Item - Notifications -->
                    <?php
                    require_once(__DIR__ . '/admin_notifications.php');
                    if (isset($_SESSION['admin_id'])) {
                        $adminNotifications = new AdminNotificationSystem($conn, $_SESSION['admin_id']);
                        $unreadCount = $adminNotifications->getUnreadCount();
                        $notifications = $adminNotifications->getAdminNotifications(5);
                    ?>
                    <li class="nav-item dropdown no-arrow">
                        <a class="nav-link dropdown-toggle notification-bell" href="#" id="alertsDropdown" role="button"
                           data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="fas fa-bell fa-fw"></i>
                            <?php if ($unreadCount > 0): ?>
                            <span class="badge badge-danger badge-counter"><?php echo $unreadCount > 9 ? '9+' : $unreadCount; ?></span>
                            <?php endif; ?>
                        </a>
                        <!-- Dropdown - Notifications -->
                        <div class="dropdown-list dropdown-menu dropdown-menu-right shadow animated--grow-in"
                             aria-labelledby="alertsDropdown">
                            <h6 class="dropdown-header">
                                Notifications Center
                            </h6>
                            
                            <?php if (empty($notifications)): ?>
                            <a class="dropdown-item text-center small text-gray-500" href="#">
                                No notifications
                            </a>
                            <?php else: ?>
                                <?php foreach ($notifications as $notification): ?>
                                <a class="dropdown-item d-flex align-items-center <?php echo $notification['is_read'] ? '' : 'bg-light'; ?>" 
                                   href="#" onclick="markNotificationRead(<?php echo $notification['id']; ?>)">
                                    <div class="mr-3">
                                        <div class="icon-circle bg-<?php echo getNotificationColor($notification['type']); ?>">
                                            <i class="<?php echo getNotificationIcon($notification['type']); ?> text-white"></i>
                                        </div>
                                    </div>
                                    <div>
                                        <div class="small text-gray-500"><?php echo $notification['formatted_date']; ?></div>
                                        <span class="font-weight-bold"><?php echo htmlspecialchars($notification['message']); ?></span>
                                    </div>
                                </a>
                                <?php endforeach; ?>
                            <?php endif; ?>
                            
                            <a class="dropdown-item text-center small text-gray-500" href="notifications.php">Show All Notifications</a>
                        </div>
                    </li>
                    <?php } ?>

                    <div class="topbar-divider d-none d-sm-block"></div>

                    <!-- Nav Item - User Information -->
                    <li class="nav-item dropdown no-arrow">
                        <a class="nav-link dropdown-toggle user-profile-link" href="#" id="userDropdown" role="button"
                           data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <img class="img-profile" src="assets/img/undraw_profile.svg" alt="Profile">
                            <span class="user-name d-none d-sm-inline">
                                <?php echo isset($_SESSION['admin_name']) ? htmlspecialchars($_SESSION['admin_name']) : 'Super Admin'; ?>
                            </span>
                        </a>
                        <!-- Dropdown - User Information -->
                        <div class="dropdown-menu dropdown-menu-end shadow animated--grow-in"
                             aria-labelledby="userDropdown">
                            <div class="dropdown-header py-3 border-bottom">
                                <strong><?php echo isset($_SESSION['admin_name']) ? htmlspecialchars($_SESSION['admin_name']) : 'Super Admin'; ?></strong><br>
                                <small class="text-muted">Administrator</small>
                            </div>
                            <a class="dropdown-item" href="settings.php">
                                <i class="fas fa-cogs fa-sm fa-fw me-2 text-gray-400"></i>
                                Settings
                            </a>
                            <a class="dropdown-item" href="notifications.php">
                                <i class="fas fa-bell fa-sm fa-fw me-2 text-gray-400"></i>
                                All Notifications
                            </a>
                            <div class="dropdown-divider"></div>
                            <a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#logoutModal">
                                <i class="fas fa-sign-out-alt fa-sm fa-fw me-2 text-gray-400"></i>
                                Logout
                            </a>
                        </div>
                    </li>
                </ul>
            </nav>
            <!-- End of Topbar -->

            <!-- Main Content -->
            <div id="content">
                <!-- Begin Page Content -->
                <div class="container-fluid">
                    <!-- Page content will be inserted here by individual pages --> 