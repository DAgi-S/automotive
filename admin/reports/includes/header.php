<?php
// Get the current page name
$current_page = basename($_SERVER['PHP_SELF'], '.php');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="Automotive Service Reports Dashboard">
    <title><?php echo isset($pageTitle) ? "Auto Admin - " . $pageTitle : "Auto Admin - Reports"; ?></title>

    <!-- Favicon -->
    <link rel="icon" type="image/svg+xml" href="../../assets/img/favicon.ico">

    <!-- Custom fonts -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@200;300;400;600;700;800;900&display=swap" rel="stylesheet">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- DataTables CSS -->
    <link href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    
    <!-- SweetAlert2 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
    
    <!-- Custom styles -->
    <link href="../css/sb-admin-2.min.css" rel="stylesheet">
    <link href="../css/custom-admin.css" rel="stylesheet">
    <link href="../css/reports.css" rel="stylesheet">

    <!-- Core JavaScript -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- DataTables JavaScript -->
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
    
    <!-- SweetAlert2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
    body {
        background-color: #f8f9fc;
        font-family: 'Nunito', sans-serif;
        padding-top: 0;
    }
    
    #wrapper {
        display: flex;
    }
    
    .sidebar {
        width: 14rem;
        min-height: 100vh;
        z-index: 100;
        box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
        position: fixed;
        top: 0;
        left: 0;
    }
    
    #content-wrapper {
        flex: 1;
        margin-left: 14rem;
        min-height: 100vh;
        background-color: #f8f9fc;
    }

    .sidebar.toggled {
        width: 6.5rem;
    }

    .sidebar.toggled + #content-wrapper {
        margin-left: 6.5rem;
    }

    .topbar {
        height: 4.375rem;
        box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
        background-color: #fff;
    }

    .container-fluid {
        padding: 1.5rem;
    }

    .stats-card {
        border: none !important;
        border-radius: 0.75rem !important;
        box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.1) !important;
        transition: all 0.3s ease !important;
    }
    
    .stats-card:hover {
        transform: translateY(-3px);
    }

    .report-form {
        border: none !important;
        border-radius: 0.75rem !important;
    }

    .table {
        margin-bottom: 0;
    }

    .card-header {
        background-color: transparent !important;
        border-bottom: 1px solid rgba(0,0,0,.05) !important;
    }

    @media (max-width: 768px) {
        .sidebar {
            width: 6.5rem;
        }
        
        #content-wrapper {
            margin-left: 6.5rem;
        }
        
        .sidebar.toggled {
            margin-left: -6.5rem;
        }
        
        .sidebar.toggled + #content-wrapper {
            margin-left: 0;
        }
    }
    </style>
</head>

<body id="page-top">
    <!-- Page Wrapper -->
    <div id="wrapper">
        <!-- Sidebar -->
        <ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">
            <!-- Sidebar content here -->
            <a class="sidebar-brand d-flex align-items-center justify-content-center" href="../dashboard.php">
                <div class="sidebar-brand-icon">
                    <i class="fas fa-car"></i>
                </div>
                <div class="sidebar-brand-text mx-3">Auto Admin</div>
            </a>

            <hr class="sidebar-divider">

            <li class="nav-item">
                <a class="nav-link" href="../dashboard.php">
                    <i class="fas fa-fw fa-tachometer-alt"></i>
                    <span>Dashboard</span>
                </a>
            </li>

            <hr class="sidebar-divider">

            <div class="sidebar-heading">Reports</div>

            <li class="nav-item <?php echo $current_page === 'index' ? 'active' : ''; ?>">
                <a class="nav-link" href="index.php">
                    <i class="fas fa-fw fa-chart-area"></i>
                    <span>Reports Dashboard</span>
                </a>
            </li>

            <li class="nav-item <?php echo $current_page === 'sales' ? 'active' : ''; ?>">
                <a class="nav-link" href="sales.php">
                    <i class="fas fa-fw fa-chart-line"></i>
                    <span>Sales Reports</span>
                </a>
            </li>

            <li class="nav-item <?php echo $current_page === 'services' ? 'active' : ''; ?>">
                <a class="nav-link" href="services.php">
                    <i class="fas fa-fw fa-wrench"></i>
                    <span>Service Reports</span>
                </a>
            </li>

            <li class="nav-item <?php echo $current_page === 'inventory' ? 'active' : ''; ?>">
                <a class="nav-link" href="inventory.php">
                    <i class="fas fa-fw fa-boxes"></i>
                    <span>Inventory Reports</span>
                </a>
            </li>

            <!-- Divider -->
            <hr class="sidebar-divider d-none d-md-block">

            <!-- Sidebar Toggler (Sidebar) -->
            <div class="text-center d-none d-md-inline">
                <button class="rounded-circle border-0" id="sidebarToggle">
                    <i class="fas fa-angle-left"></i>
                </button>
            </div>
        </ul>
        <!-- End of Sidebar -->

        <!-- Content Wrapper -->
        <div id="content-wrapper" class="d-flex flex-column">
            <!-- Main Content -->
            <div id="content">
                <!-- Begin Page Content -->
                <div class="container-fluid">
            </div>
        </div>
    </div>
</body>
</html> 