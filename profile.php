<?php
define('INCLUDED', true);
session_start(); // Start the session

// Check if the session 'password' is set
if (!isset($_SESSION['password'])) {
    header("location: login.php?message=login first"); // Redirect to the login page if the session is not set
    exit;
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>My Profile - Nati Automotive</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta charset="UTF-8">
    <meta name="description" content="Manage your profile, cars, and appointments at Nati Automotive">
    <link rel="icon" href="assets/images/favicon/Nati-logo.png" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/css/bottom-nav-universal.css">
    <link rel="stylesheet" href="assets/css/new-home-layout.css">
    <link rel="stylesheet" href="assets/css/mobile-header.css">
    <link rel="stylesheet" href="assets/css/profile-compact.css">
</head>

<body>
<?php
include("db_conn.php");
include("partial-front/db_con.php");
?>

  <style>
    /* Enhanced Profile Page Styles using home CSS architecture */
    body {
        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        padding-bottom: 90px;
        font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
    }
    
    .profile-content {
        padding-bottom: 90px;
        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        min-height: 100vh;
    }
    
    .site-content {
        min-height: 100vh;
        position: relative;
        padding-bottom: 90px !important;
    }
    
    .container {
        padding-bottom: 20px;
        max-width: 1200px;
        padding-left: 0.75rem;
        padding-right: 0.75rem;
    }
    
    .container-fluid {
        max-width: 100%;
        padding-left: 0.75rem;
        padding-right: 0.75rem;
    }
    
    .compact-profile {
        padding-top: 0.5rem;
    }
    
    /* Mobile Header Styles */
    .mobile-header-profile {
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        height: 60px;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        z-index: 1000;
        box-shadow: 0 2px 10px rgba(102, 126, 234, 0.3);
    }
    
    .header-container {
        display: flex;
        align-items: center;
        justify-content: space-between;
        height: 100%;
        padding: 0 1rem;
        max-width: 1200px;
        margin: 0 auto;
    }
    
    .header-left {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        flex: 1;
    }
    
    .back-btn,
    .notifications-btn,
    .menu-btn {
        background: none;
        border: none;
        color: white;
        width: 40px;
        height: 40px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.3s ease;
        position: relative;
    }
    
    .back-btn:hover,
    .notifications-btn:hover,
    .menu-btn:hover {
        background: rgba(255, 255, 255, 0.2);
        transform: scale(1.1);
    }
    
    .header-title h1 {
        font-size: 1.2rem;
        font-weight: 700;
        margin: 0;
        color: white;
    }
    
    .header-right {
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }
    
    .notification-badge {
        position: absolute;
        top: 8px;
        right: 8px;
        background: #ff4757;
        color: white;
        border-radius: 50%;
        width: 16px;
        height: 16px;
        font-size: 0.6rem;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 600;
    }
    
    /* Adjust body padding for fixed header */
    .site-content {
        padding-top: 60px;
    }
    
    /* Compact Profile Header */
    .profile-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border-radius: 15px;
        padding: 1rem;
        margin-bottom: 1rem;
        color: white;
        box-shadow: 0 8px 20px rgba(102, 126, 234, 0.3);
        position: relative;
        overflow: hidden;
    }
    
    .profile-header .row {
        align-items: center;
    }
    
    .profile-avatar {
        width: 80px;
        height: 80px;
        border-radius: 50%;
        border: 3px solid rgba(255, 255, 255, 0.3);
        object-fit: cover;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
    }
    
    .profile-info h2 {
        font-size: 1.3rem;
        font-weight: 700;
        margin-bottom: 0.3rem;
        line-height: 1.2;
    }
    
    .profile-info p {
        opacity: 0.9;
        font-size: 0.85rem;
        margin-bottom: 0.2rem;
    }
    
    /* Compact Action Buttons */
    .action-buttons {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(140px, 1fr));
        gap: 0.5rem;
        margin: 1rem 0;
    }
    
    .action-button {
        background: linear-gradient(135deg, #007bff, #0056b3);
        color: white;
        text-decoration: none;
        border-radius: 10px;
        padding: 0.6rem 0.8rem;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
        font-weight: 600;
        font-size: 0.8rem;
        transition: all 0.3s ease;
        border: none;
        box-shadow: 0 3px 10px rgba(0, 123, 255, 0.3);
    }
    
    .action-button:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(0, 123, 255, 0.4);
        color: white;
    }
    
    .action-button.admin {
        background: linear-gradient(135deg, #28a745, #1e7e34);
        box-shadow: 0 3px 10px rgba(40, 167, 69, 0.3);
    }
    
    /* Compact Statistics Cards */
    .stats-container {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 0.75rem;
        margin: 1rem 0;
    }
    
    .stat-card {
        background: white;
        border-radius: 12px;
        padding: 0.75rem;
        display: flex;
        align-items: center;
        gap: 0.75rem;
        box-shadow: 0 3px 10px rgba(0, 0, 0, 0.08);
        border: 1px solid rgba(0, 0, 0, 0.05);
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
    }
    
    .stat-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 3px;
        background: linear-gradient(135deg, #007bff, #0056b3);
    }
    
    .stat-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.12);
    }
    
    .stat-icon {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background: linear-gradient(135deg, #007bff10, #0056b320);
        display: flex;
        align-items: center;
        justify-content: center;
        color: #007bff;
        font-size: 1.1rem;
        flex-shrink: 0;
    }
    
    .stat-value {
        font-size: 1.5rem;
        font-weight: 700;
        color: #2c3e50;
        margin-bottom: 0.1rem;
        line-height: 1;
    }
    
    .stat-label {
        color: #6c757d;
        font-weight: 500;
        font-size: 0.7rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        line-height: 1;
    }
    
    /* Compact Alert */
    .maintenance-alert {
        background: linear-gradient(135deg, #fff3cd, #ffeaa7);
        border: 1px solid #ffeeba;
        border-radius: 10px;
        padding: 0.75rem;
        display: flex;
        align-items: center;
        gap: 0.75rem;
        margin: 1rem 0;
        box-shadow: 0 3px 10px rgba(255, 193, 7, 0.2);
    }
    
    .maintenance-alert i {
        color: #856404;
        font-size: 1.2rem;
        flex-shrink: 0;
    }
    
    /* Compact Tab Navigation */
    .nav-pills {
        background: white;
        border-radius: 12px;
        padding: 0.5rem;
        box-shadow: 0 3px 10px rgba(0, 0, 0, 0.08);
        margin-bottom: 1rem;
    }
    
    .nav-pills .nav-link {
        border-radius: 8px;
        font-weight: 600;
        padding: 0.5rem 0.75rem;
        color: #6c757d;
        transition: all 0.3s ease;
        border: none;
        background: transparent;
        font-size: 0.8rem;
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 0.25rem;
        text-align: center;
    }
    
    .nav-pills .nav-link.active {
        background: linear-gradient(135deg, #007bff, #0056b3);
        color: white;
        box-shadow: 0 3px 10px rgba(0, 123, 255, 0.3);
    }
    
    .nav-pills .nav-link:hover:not(.active) {
        background: #f8f9fa;
        color: #007bff;
    }
    
    .nav-pills .nav-link i {
        font-size: 1rem;
    }
    
    .nav-pills .nav-link span {
        font-size: 0.7rem;
        font-weight: 600;
    }
    
    /* Compact Car Cards */
    .car-card {
        background: white;
        border-radius: 12px;
        padding: 0.75rem;
        margin-bottom: 0.75rem;
        box-shadow: 0 3px 10px rgba(0, 0, 0, 0.08);
        border: 1px solid rgba(0, 0, 0, 0.05);
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
        cursor: pointer;
    }
    
    .car-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.12);
    }
    
    .car-card.urgent::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 3px;
        background: linear-gradient(135deg, #dc3545, #c82333);
    }
    
    /* Enhanced History Tab - 2-Column Grid Layout */
    .history-container-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 0.75rem;
        padding: 0.5rem 0;
    }
    
    .appointment-card-grid {
        background: white;
        border-radius: 12px;
        padding: 0.75rem;
        box-shadow: 0 3px 10px rgba(0, 0, 0, 0.08);
        border: 1px solid rgba(0, 0, 0, 0.05);
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
        display: flex;
        flex-direction: column;
        gap: 0.5rem;
    }
    
    .appointment-card-grid:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.12);
    }
    
    .appointment-card-header {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        margin-bottom: 0.25rem;
    }
    
    .service-icon {
        width: 32px;
        height: 32px;
        border-radius: 50%;
        background: linear-gradient(135deg, #007bff10, #0056b320);
        display: flex;
        align-items: center;
        justify-content: center;
        color: #007bff;
        font-size: 0.85rem;
        flex-shrink: 0;
    }
    
    .appointment-meta {
        flex: 1;
        min-width: 0;
    }
    
    .service-name {
        font-size: 0.8rem;
        font-weight: 700;
        color: #2c3e50;
        margin-bottom: 0.1rem;
        line-height: 1.2;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }
    
    .appointment-id {
        font-size: 0.65rem;
        color: #6c757d;
        font-weight: 500;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    
    .status-indicator {
        flex-shrink: 0;
    }
    
    .status-badge-grid {
        width: 24px;
        height: 24px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.65rem;
        font-weight: 600;
    }
    
    .status-badge-grid.status-pending {
        background: linear-gradient(135deg, #ffc107, #e0a800);
        color: white;
    }
    
    .status-badge-grid.status-confirmed {
        background: linear-gradient(135deg, #007bff, #0056b3);
        color: white;
    }
    
    .status-badge-grid.status-completed {
        background: linear-gradient(135deg, #28a745, #1e7e34);
        color: white;
    }
    
    .status-badge-grid.status-cancelled {
        background: linear-gradient(135deg, #dc3545, #c82333);
        color: white;
    }
    
    .appointment-details-grid {
        display: flex;
        flex-direction: column;
        gap: 0.4rem;
    }
    
    .detail-row-grid {
        display: flex;
        justify-content: space-between;
        gap: 0.5rem;
    }
    
    .detail-item-grid {
        display: flex;
        align-items: center;
        gap: 0.35rem;
        flex: 1;
        min-width: 0;
    }
    
    .detail-item-grid i {
        color: #6c757d;
        font-size: 0.7rem;
        width: 12px;
        text-align: center;
        flex-shrink: 0;
    }
    
    .detail-text {
        font-size: 0.7rem;
        color: #495057;
        font-weight: 500;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }
    
    .price-text {
        font-weight: 700;
        color: #28a745;
    }
    
    .status-text {
        font-size: 0.65rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.3px;
        text-align: right;
    }
    
    .status-text.status-pending {
        color: #ffc107;
    }
    
    .status-text.status-confirmed {
        color: #007bff;
    }
    
    .status-text.status-completed {
        color: #28a745;
    }
    
    .status-text.status-cancelled {
        color: #dc3545;
    }
    
    .appointment-actions-grid {
        display: flex;
        gap: 0.4rem;
        margin-top: 0.25rem;
        padding-top: 0.5rem;
        border-top: 1px solid rgba(0, 0, 0, 0.05);
    }
    
    .btn-cancel-compact,
    .btn-reschedule-compact,
    .btn-receipt-compact,
    .btn-rate-compact {
        flex: 1;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.25rem;
        padding: 0.35rem 0.5rem;
        border-radius: 6px;
        font-size: 0.65rem;
        font-weight: 600;
        text-decoration: none;
        transition: all 0.3s ease;
        border: 1px solid;
        text-align: center;
    }
    
    .btn-cancel-compact {
        background: white;
        color: #dc3545;
        border-color: #dc3545;
    }
    
    .btn-cancel-compact:hover {
        background: #dc3545;
        color: white;
    }
    
    .btn-reschedule-compact {
        background: white;
        color: #ffc107;
        border-color: #ffc107;
    }
    
    .btn-reschedule-compact:hover {
        background: #ffc107;
        color: #212529;
    }
    
    .btn-receipt-compact {
        background: white;
        color: #6c757d;
        border-color: #6c757d;
    }
    
    .btn-receipt-compact:hover {
        background: #6c757d;
        color: white;
    }
    
    .btn-rate-compact {
        background: white;
        color: #28a745;
        border-color: #28a745;
    }
    
    .btn-rate-compact:hover {
        background: #28a745;
        color: white;
    }
    
    .btn-cancel-compact i,
    .btn-reschedule-compact i,
    .btn-receipt-compact i,
    .btn-rate-compact i {
        font-size: 0.6rem;
    }
    
    /* No Appointments Message for Grid */
    .no-appointments-message-grid {
        grid-column: 1 / -1;
        display: flex;
        justify-content: center;
        align-items: center;
        padding: 2rem 1rem;
        min-height: 200px;
    }
    
    .no-appointments-content-grid {
        text-align: center;
        max-width: 300px;
    }
    
    .no-appointments-icon {
        width: 60px;
        height: 60px;
        margin: 0 auto 1rem;
        background: linear-gradient(135deg, #f8f9fa, #e9ecef);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    
    .no-appointments-icon i {
        font-size: 1.5rem;
        color: #6c757d;
    }
    
    .no-appointments-title {
        font-size: 1rem;
        font-weight: 700;
        color: #495057;
        margin-bottom: 0.5rem;
    }
    
    .no-appointments-text {
        font-size: 0.8rem;
        color: #6c757d;
        margin-bottom: 1rem;
        line-height: 1.4;
    }
    
    .btn-book-compact {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
        background: linear-gradient(135deg, #007bff, #0056b3);
        color: white;
        border: none;
        border-radius: 8px;
        padding: 0.6rem 1rem;
        font-size: 0.8rem;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s ease;
        text-decoration: none;
    }
    
            .btn-book-compact:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0, 123, 255, 0.4);
            color: white;
        }
        
        /* Enhanced Bottom Navigation Fixes */
        #bottom-navigation {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            background: white;
            border-top: 1px solid rgba(0, 0, 0, 0.1);
            box-shadow: 0 -2px 10px rgba(0, 0, 0, 0.1);
            z-index: 999;
            height: 70px;
        }
        
        .home-navigation-menu {
            height: 100%;
        }
        
        .bottom-panel {
            height: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .bootom-tabbar {
            display: flex;
            list-style: none;
            margin: 0;
            padding: 0;
            width: 100%;
            max-width: 400px;
            justify-content: space-around;
            align-items: center;
            height: 100%;
        }
        
        .bootom-tabbar li {
            position: relative;
            flex: 1;
            height: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .bootom-tabbar li a {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 50px;
            height: 50px;
            border-radius: 50%;
            transition: all 0.3s ease;
            text-decoration: none;
            position: relative;
        }
        
        .bootom-tabbar li a svg {
            width: 24px;
            height: 24px;
            transition: all 0.3s ease;
        }
        
        .bootom-tabbar li a svg path {
            stroke: #6c757d;
            transition: all 0.3s ease;
        }
        
        .bootom-tabbar li.active a {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3);
            transform: translateY(-2px);
        }
        
        .bootom-tabbar li.active a svg path {
            stroke: white;
        }
        
        .bootom-tabbar li a:hover {
            background: rgba(102, 126, 234, 0.1);
            transform: translateY(-1px);
        }
        
        .orange-boder,
        .orange-border {
            display: none;
        }
    
    .car-image-mobile {
        width: 60px;
        height: 45px;
        border-radius: 8px;
        object-fit: cover;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    }
    
    .car-info-mobile h3 {
        font-size: 1rem;
        font-weight: 700;
        color: #2c3e50;
        margin-bottom: 0.2rem;
        line-height: 1.2;
    }
    
    .car-year-mobile {
        color: #6c757d;
        font-size: 0.8rem;
        margin-bottom: 0.5rem;
    }
    
    .car-quick-info {
        display: flex;
        gap: 0.75rem;
        font-size: 0.75rem;
        color: #6c757d;
    }
    
    .car-quick-info span {
        display: flex;
        align-items: center;
        gap: 0.25rem;
    }
    
    .car-quick-info i {
        font-size: 0.7rem;
    }
    
    .urgent-badge {
        position: absolute;
        top: 0.5rem;
        right: 0.5rem;
        background: linear-gradient(135deg, #dc3545, #c82333);
        color: white;
        border-radius: 50%;
        width: 24px;
        height: 24px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.7rem;
    }
    
    /* Mobile Responsive Design */
    @media (max-width: 768px) {
        .container {
            padding-left: 0.5rem;
            padding-right: 0.5rem;
        }
        
        .profile-avatar {
            width: 70px;
            height: 70px;
        }
        
        .profile-info h2 {
            font-size: 1.2rem;
        }
        
        .profile-info p {
            font-size: 0.8rem;
        }
        
        .action-buttons {
            grid-template-columns: repeat(2, 1fr);
            gap: 0.4rem;
        }
        
        .action-button {
            padding: 0.5rem 0.6rem;
            font-size: 0.75rem;
        }
        
        .stats-container {
            gap: 0.5rem;
        }
        
        .stat-card {
            padding: 0.6rem;
            gap: 0.6rem;
        }
        
        .stat-icon {
            width: 35px;
            height: 35px;
            font-size: 1rem;
        }
        
        .stat-value {
            font-size: 1.3rem;
        }
        
        .stat-label {
            font-size: 0.65rem;
        }
        
        .nav-pills .nav-link {
            padding: 0.4rem 0.5rem;
            font-size: 0.75rem;
        }
        
        .nav-pills .nav-link span {
            font-size: 0.65rem;
        }
        
        .car-image-mobile {
            width: 50px;
            height: 38px;
        }
        
        .car-info-mobile h3 {
            font-size: 0.9rem;
        }
        
        .car-year-mobile {
            font-size: 0.75rem;
        }
        
        .car-quick-info {
            gap: 0.5rem;
            font-size: 0.7rem;
        }
        
        .urgent-badge {
            width: 20px;
            height: 20px;
            font-size: 0.6rem;
        }
        
        /* Mobile Header Responsive */
        .mobile-header-profile {
            height: 50px;
        }
        
        .site-content {
            padding-top: 10px;
        }
        
        .header-container {
            padding: 0 0.75rem;
        }
        
        .header-title h1 {
            font-size: 1.1rem;
        }
        
        .back-btn,
        .notifications-btn,
        .menu-btn {
            width: 35px;
            height: 35px;
        }
        
        /* Bottom Navigation Mobile */
        #bottom-navigation {
            height: 60px;
        }
        
        .bootom-tabbar li a {
            width: 45px;
            height: 45px;
        }
        
        .bootom-tabbar li a svg {
            width: 20px;
            height: 20px;
        }
        
        /* Mobile adjustments for History Grid */
        .history-container-grid {
            grid-template-columns: 1fr;
            gap: 0.5rem;
            padding: 0.25rem 0;
        }
        
        .appointment-card-grid {
            padding: 0.6rem;
            gap: 0.4rem;
        }
        
        .service-icon {
            width: 28px;
            height: 28px;
            font-size: 0.75rem;
        }
        
        .service-name {
            font-size: 0.75rem;
        }
        
        .appointment-id {
            font-size: 0.6rem;
        }
        
        .status-badge-grid {
            width: 20px;
            height: 20px;
            font-size: 0.6rem;
        }
        
        .detail-text {
            font-size: 0.65rem;
        }
        
        .status-text {
            font-size: 0.6rem;
        }
        
        .appointment-actions-grid {
            gap: 0.3rem;
            margin-top: 0.2rem;
            padding-top: 0.4rem;
        }
        
        .btn-cancel-compact,
        .btn-reschedule-compact,
        .btn-receipt-compact,
        .btn-rate-compact {
            padding: 0.3rem 0.4rem;
            font-size: 0.6rem;
            gap: 0.2rem;
        }
        
        .btn-cancel-compact i,
        .btn-reschedule-compact i,
        .btn-receipt-compact i,
        .btn-rate-compact i {
            font-size: 0.55rem;
        }
        
        .no-appointments-icon {
            width: 50px;
            height: 50px;
        }
        
        .no-appointments-icon i {
            font-size: 1.2rem;
        }
        
        .no-appointments-title {
            font-size: 0.9rem;
        }
        
        .no-appointments-text {
            font-size: 0.75rem;
        }
        
        .btn-book-compact {
            padding: 0.5rem 0.8rem;
            font-size: 0.75rem;
        }
    }
    
    @media (max-width: 576px) {
        .profile-header {
            padding: 0.75rem;
        }
        
        .profile-avatar {
            width: 60px;
            height: 60px;
        }
        
        .profile-info h2 {
            font-size: 1.1rem;
        }
        
        .action-buttons {
            grid-template-columns: 1fr;
            gap: 0.3rem;
        }
        
        .action-button {
            padding: 0.4rem 0.5rem;
            font-size: 0.7rem;
        }
        
        .nav-pills .nav-link i {
            font-size: 0.9rem;
        }
        
        .nav-pills .nav-link span {
            font-size: 0.6rem;
        }
        
        /* Extra small mobile adjustments for History Grid */
        .history-container-grid {
            gap: 0.4rem;
            padding: 0.2rem 0;
        }
        
        .appointment-card-grid {
            padding: 0.5rem;
            gap: 0.35rem;
        }
        
        .appointment-card-header {
            gap: 0.4rem;
        }
        
        .service-icon {
            width: 24px;
            height: 24px;
            font-size: 0.7rem;
        }
        
        .service-name {
            font-size: 0.7rem;
        }
        
        .appointment-id {
            font-size: 0.55rem;
        }
        
        .status-badge-grid {
            width: 18px;
            height: 18px;
            font-size: 0.55rem;
        }
        
        .appointment-details-grid {
            gap: 0.3rem;
        }
        
        .detail-row-grid {
            gap: 0.4rem;
        }
        
        .detail-item-grid {
            gap: 0.3rem;
        }
        
        .detail-item-grid i {
            font-size: 0.65rem;
            width: 10px;
        }
        
        .detail-text {
            font-size: 0.6rem;
        }
        
        .status-text {
            font-size: 0.55rem;
        }
        
        .appointment-actions-grid {
            gap: 0.25rem;
            margin-top: 0.15rem;
            padding-top: 0.35rem;
        }
        
        .btn-cancel-compact,
        .btn-reschedule-compact,
        .btn-receipt-compact,
        .btn-rate-compact {
            padding: 0.25rem 0.35rem;
            font-size: 0.55rem;
            gap: 0.15rem;
            border-radius: 4px;
        }
        
        .btn-cancel-compact i,
        .btn-reschedule-compact i,
        .btn-receipt-compact i,
        .btn-rate-compact i {
            font-size: 0.5rem;
        }
        
        .no-appointments-message-grid {
            padding: 1.5rem 0.5rem;
            min-height: 180px;
        }
        
        .no-appointments-icon {
            width: 45px;
            height: 45px;
            margin-bottom: 0.75rem;
        }
        
        .no-appointments-icon i {
            font-size: 1.1rem;
        }
        
        .no-appointments-title {
            font-size: 0.85rem;
        }
        
        .no-appointments-text {
            font-size: 0.7rem;
        }
        
        .btn-book-compact {
            padding: 0.45rem 0.7rem;
            font-size: 0.7rem;
            gap: 0.4rem;
        }
    }
    
    .profile-menu-dropdown {
      position: absolute;
      top: 50px; /* adjust as needed */
      right: 10px;
      background: #fff;
      border-radius: 10px;
      box-shadow: 0 8px 24px rgba(0,0,0,0.15);
      z-index: 10001;
      min-width: 170px;
      padding: 0.5rem 0;
      display: flex;
      flex-direction: column;
      animation: fadeIn 0.2s;
    }
    .profile-menu-dropdown .dropdown-item {
      background: none;
      border: none;
      text-align: left;
      padding: 0.7rem 1.2rem;
      font-size: 1rem;
      color: #333;
      cursor: pointer;
      display: flex;
      align-items: center;
      gap: 0.7rem;
      transition: background 0.15s;
    }
    .profile-menu-dropdown .dropdown-item:hover {
      background: #f5f6fa;
    }
    @keyframes fadeIn {
      from { opacity: 0; transform: translateY(-10px);}
      to { opacity: 1; transform: translateY(0);}
    }
  </style>

  <style>
    /* Profile Car Details Modal Enhancements */
    .car-details-modal-profile .modal-content {
      border-radius: 16px;
      box-shadow: 0 8px 32px rgba(102, 126, 234, 0.18);
      max-width: 98vw;
    }
    .car-details-modal-profile .modal-body {
      max-height: 70vh;
      overflow-y: auto;
    }
    @media (max-width: 768px) {
      .car-details-modal-profile .modal-dialog {
        margin: 0.5rem;
        max-width: 98vw;
      }
      .car-details-modal-profile .modal-body {
        padding: 1rem;
        max-height: 60vh;
      }
      .car-details-modal-profile .img-fluid {
        max-height: 120px;
      }
      .car-details-modal-profile dl dt,
      .car-details-modal-profile dl dd {
        font-size: 0.95rem;
      }
    }
  </style>

  <div class="site-content profile-content">
    <!-- Mobile Header Navigation -->
    <header class="mobile-header-profile">
      <div class="header-container">
        <div class="header-left">
          <button class="back-btn" onclick="history.back()">
            <i class="fas fa-arrow-left"></i>
          </button>
          <div class="header-title">
            <h1>My Profile</h1>
          </div>
        </div>
        <div class="header-right">
          <button class="notifications-btn" onclick="toggleNotifications()">
            <i class="fas fa-bell"></i>
            <span class="notification-badge">3</span>
          </button>
          <button class="menu-btn" onclick="toggleMenu()">
            <i class="fas fa-ellipsis-v"></i>
          </button>
          <div id="profile-menu-dropdown" class="profile-menu-dropdown" style="display:none;">
            <button class="dropdown-item" onclick="window.location.href='edit_profile.php'">
              <i class="fas fa-edit"></i> Edit Profile
            </button>
            <button class="dropdown-item" onclick="window.location.href='settings.php'">
              <i class="fas fa-cog"></i> Settings
            </button>
            <button class="dropdown-item" onclick="logoutProfile()">
              <i class="fas fa-sign-out-alt"></i> Logout
            </button>
          </div>
          <div id="profile-menu-backdrop" style="display:none; position:fixed; top:0; left:0; width:100vw; height:100vh; z-index:9998;"></div>
        </div>
      </div>
    </header>
    
    <!-- Enhanced Profile Content -->
    <section id="single-mentor-sec" class="compact-profile">
      <div class="container-fluid px-3">
        <!-- Compact Profile Header -->
        <?php
                    $db = new DB_con();
                    $id = $_SESSION['id'];
        
        // Get user data using the proper method
        $user_data = $db->get_user_by_id($id);
        
        ?>
        
        <div class="profile-header compact">
          <div class="profile-content-wrapper">
            <div class="profile-avatar-section">
              <?php if (!empty($user_data['new_img_name'])): ?>
                <img src="assets/img/<?php echo htmlspecialchars($user_data['new_img_name']); ?>" 
                     alt="Profile Picture" class="profile-avatar compact">
              <?php else: ?>
                <div class="profile-avatar compact d-flex align-items-center justify-content-center" 
                     style="background: rgba(255,255,255,0.2); font-size: 2rem;">
                  <i class="fas fa-user"></i>
                </div>
              <?php endif; ?>
            </div>
            <div class="profile-info compact">
              <h2><?php echo htmlspecialchars($user_data['name'] ?? 'User'); ?></h2>
              <div class="profile-details">
                <span class="detail-item">
                  <i class="fas fa-envelope"></i>
                  <?php echo htmlspecialchars($user_data['email'] ?? 'No email'); ?>
                </span>
                <span class="detail-item">
                  <i class="fas fa-phone"></i>
                  <?php echo htmlspecialchars($user_data['phonenum'] ?? 'No phone'); ?>
                </span>
              </div>
            </div>
          </div>
        </div>

        <!-- Compact Action Buttons -->
        <div class="action-buttons compact">
          <?php if(isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
          <a href="/automotive/admin/dashboard.php" class="action-button admin compact">
            <i class="fas fa-user-shield"></i>
            <span>Admin Panel</span>
          </a>
          <?php endif; ?>
          
          <a href="my_cars.php" class="action-button compact">
            <i class="fas fa-car"></i>
            <span>View All Cars</span>
          </a>
          
          <a href="edit_profile.php" class="action-button compact">
            <i class="fas fa-edit"></i>
            <span>Edit Profile</span>
          </a>
        </div>

        <!-- Compact Statistics Section -->
        <?php
        $userid = $_SESSION['id'];
        $cars = $db->get_user_cars($userid);
        $total_cars = count($cars);
        $cars_needing_service = 0;
        $upcoming_insurance = 0;
        $total_appointments = 0;

        if ($cars && count($cars) > 0) {
            foreach ($cars as $car) {
                $insurance_date = strtotime($car['insurance']);
                $days_remaining = round(($insurance_date - time()) / (60 * 60 * 24));
                if ($days_remaining <= 30) {
                    $upcoming_insurance++;
                }
            }
        }

        // Get appointment count
        $appointments = $db->get_user_appointments($userid);
        $total_appointments = count($appointments ?? []);
        ?>
          
        <div class="stats-container compact">
          <div class="stat-card compact">
            <div class="stat-icon compact">
              <i class="fas fa-car"></i>
            </div>
            <div class="stat-content">
              <div class="stat-value"><?php echo $total_cars; ?></div>
              <div class="stat-label">TOTAL CARS</div>
            </div>
          </div>
          
          <div class="stat-card compact">
            <div class="stat-icon compact">
              <i class="fas fa-shield-alt"></i>
            </div>
            <div class="stat-content">
              <div class="stat-value"><?php echo $upcoming_insurance; ?></div>
              <div class="stat-label">INSURANCE DUE</div>
            </div>
          </div>
          
          <div class="stat-card compact">
            <div class="stat-icon compact">
              <i class="fas fa-calendar-check"></i>
            </div>
            <div class="stat-content">
              <div class="stat-value"><?php echo $total_appointments; ?></div>
              <div class="stat-label">APPOINTMENTS</div>
            </div>
          </div>
          
          <div class="stat-card compact">
            <div class="stat-icon compact">
              <i class="fas fa-tools"></i>
            </div>
            <div class="stat-content">
              <div class="stat-value"><?php echo $cars_needing_service; ?></div>
              <div class="stat-label">NEED SERVICE</div>
            </div>
          </div>
        </div>

        <!-- Compact Alert for upcoming insurance -->
        <?php if ($upcoming_insurance > 0): ?>
        <div class="maintenance-alert compact">
          <i class="fas fa-exclamation-triangle"></i>
          <div class="alert-content">
            <strong>Attention Required!</strong>
            <span><?php echo $upcoming_insurance; ?> car(s) with insurance expiring within 30 days.</span>
          </div>
        </div>
        <?php endif; ?>

        <!-- Compact Tab Navigation -->
        <div class="single-mentor-third-sec compact">
          <div class="tab-navigation-wrapper">
            <ul class="nav nav-pills compact" id="mentor-tab" role="tablist">
              <li class="nav-item" role="presentation">
                <button class="nav-link active compact" id="mentor-course-tab-btn" data-bs-toggle="pill" data-bs-target="#course-content" type="button" role="tab" aria-selected="true">
                  <i class="fas fa-car"></i>
                  <span>My Cars</span>
                </button>
              </li>
              <li class="nav-item" role="presentation">
                <button class="nav-link compact" id="student-tab-btn" data-bs-toggle="pill" data-bs-target="#student-content" type="button" role="tab" aria-selected="false" tabindex="-1">
                  <i class="fas fa-calendar-plus"></i>
                  <span>Book Service</span>
                </button>
              </li>
              <li class="nav-item" role="presentation">
                <button class="nav-link compact" id="reviews-tab-btn" data-bs-toggle="pill" data-bs-target="#reviews-content" type="button" role="tab" aria-selected="false" tabindex="-1">
                  <i class="fas fa-history"></i>
                  <span>History</span>
                </button>
              </li>
            </ul>

            <div class="tab-content compact" id="course-tab-btn">
              <!-- Compact My Cars Tab -->
              <div class="tab-pane fade show active" id="course-content" role="tabpanel" tabindex="0">
                <div class="cars-grid-container">
                  <?php
                  if ($cars && count($cars) > 0) {
                    foreach ($cars as $car) {
                      $insurance_date = strtotime($car['insurance']);
                      $current_date = time();
                      $days_remaining = round(($insurance_date - $current_date) / (60 * 60 * 24));
                      $needs_attention = $days_remaining <= 30;
                  ?>
                  <div class="car-card compact <?php echo $needs_attention ? 'urgent' : ''; ?>" 
                       data-bs-toggle="modal" 
                       data-bs-target="#carModal<?php echo $car['id']; ?>">
                    <?php if ($needs_attention): ?>
                    <div class="urgent-badge compact" title="Insurance expiring soon">
                      <i class="fas fa-exclamation"></i>
                    </div>
                    <?php endif; ?>
                    
                    <div class="car-content-wrapper">
                      <div class="car-image-wrapper">
                        <img src="assets/img/<?php echo htmlspecialchars($car['img_name1']); ?>" 
                             alt="Car Image" class="car-image compact">
                      </div>
                      
                      <div class="car-info compact">
                        <h4 class="car-title"><?php echo htmlspecialchars($car['brand_name']); ?> <?php echo htmlspecialchars($car['model_name']); ?></h4>
                        <div class="car-year"><?php echo htmlspecialchars($car['car_year']); ?></div>
                        
                        <div class="car-quick-info">
                          <div class="info-item">
                            <i class="fas fa-calendar"></i>
                            <span><?php echo date('M d', strtotime($car['service_date'])); ?></span>
                          </div>
                          <div class="info-item">
                            <i class="fas fa-tachometer-alt"></i>
                            <span><?php echo number_format($car['mile_age']); ?>km</span>
                          </div>
                          <div class="info-item <?php echo $needs_attention ? 'urgent' : ''; ?>">
                            <i class="fas fa-shield-alt"></i>
                            <span>
                              <?php if ($needs_attention): ?>
                                <?php echo $days_remaining; ?>d
                              <?php else: ?>
                                OK
                              <?php endif; ?>
                            </span>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>

                  <!-- Car Details Modal - Profile Page Only -->
                  <div class="modal fade car-details-modal-profile" id="carModal<?php echo $car['id']; ?>" tabindex="-1" aria-labelledby="carModalLabel<?php echo $car['id']; ?>" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered modal-lg">
                      <div class="modal-content" style="border-radius: 16px;">
                        <div class="modal-header" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; border-top-left-radius: 16px; border-top-right-radius: 16px;">
                          <h5 class="modal-title" id="carModalLabel<?php echo $car['id']; ?>">
                            <i class="fas fa-car me-2"></i>
                            <?php echo htmlspecialchars($car['brand_name']); ?> <?php echo htmlspecialchars($car['model_name']); ?>
                          </h5>
                          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body" style="padding: 1.5rem;">
                          <div class="row g-3">
                            <div class="col-12 col-md-5 text-center">
                              <img src="assets/img/<?php echo htmlspecialchars($car['img_name1']); ?>" 
                                   alt="Car Image" class="img-fluid rounded shadow" style="max-height: 180px; object-fit: cover;">
                            </div>
                            <div class="col-12 col-md-7">
                              <dl class="row mb-0">
                                <dt class="col-5">Year</dt>
                                <dd class="col-7"><?php echo htmlspecialchars($car['car_year']); ?></dd>
                                <dt class="col-5">Service Date</dt>
                                <dd class="col-7"><?php echo htmlspecialchars($car['service_date']); ?></dd>
                                <dt class="col-5">Mileage</dt>
                                <dd class="col-7"><?php echo number_format($car['mile_age']); ?> km</dd>
                                <dt class="col-5">Oil Change</dt>
                                <dd class="col-7"><?php echo htmlspecialchars($car['oil_change']); ?></dd>
                                <dt class="col-5">Insurance</dt>
                                <dd class="col-7 <?php echo $needs_attention ? 'text-danger fw-bold' : ''; ?>">
                                  <?php echo htmlspecialchars($car['insurance']); ?>
                                  <?php if ($needs_attention): ?>
                                    <br><small class="text-danger">(<?php echo $days_remaining; ?> days left)</small>
                                  <?php endif; ?>
                                </dd>
                                <dt class="col-5">Plate Number</dt>
                                <dd class="col-7"><?php echo htmlspecialchars($car['plate_number'] ?? 'N/A'); ?></dd>
                                <!-- Add more fields as needed -->
                              </dl>
                            </div>
                          </div>
                        </div>
                        <div class="modal-footer d-flex justify-content-between">
                          <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                            <i class="fas fa-times me-1"></i>Close
                          </button>
                          <div class="d-flex gap-2">
                            <a href="edit_car.php?id=<?php echo $car['id']; ?>" class="btn btn-outline-primary">
                              <i class="fas fa-edit me-1"></i>Edit
                            </a>
                            <a href="view_car.php?id=<?php echo $car['id']; ?>" class="btn btn-primary">
                              <i class="fas fa-eye me-1"></i>View
                            </a>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                  <?php
                    }
                  } else {
                    echo '<div class="no-cars-message">
                            <div class="no-cars-content">
                              <i class="fas fa-car-side"></i>
                              <h5>No Cars Added</h5>
                              <p>Add your first car to get started</p>
                              <a href="my_cars.php" class="btn btn-primary btn-sm">
                                <i class="fas fa-plus me-1"></i>Add Car
                              </a>
                            </div>
                          </div>';
                  }
                  ?>
                </div>
              </div>
              
              <!-- Compact Book Service Tab -->
              <div class="tab-pane fade" id="student-content" role="tabpanel" tabindex="0">
                <div class="service-booking-container compact">
                  <div class="service-card compact">
                    <div class="service-content">
                      <h4 class="service-title">
                        <i class="fas fa-calendar-plus text-primary me-2"></i>
                        Reserve a New Appointment
                      </h4>
                      <p class="service-description">Select a service and schedule your appointment with our professional team.</p>
                      
                      <div class="service-actions">
                        <a href="order_service.php" class="btn btn-primary">
                          <i class="fas fa-calendar-plus me-2"></i>Schedule Appointment
                        </a>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              
              <!-- Enhanced Compact History Tab - 2-Column Grid -->
              <div class="tab-pane fade" id="reviews-content" role="tabpanel" tabindex="0">
                <div class="history-container-grid compact">
                  <?php
                  $userid = $_SESSION['id'];
                  $appointments = $db->get_user_appointments($userid);
                  
                  if ($appointments && count($appointments) > 0) {
                    foreach ($appointments as $appointment) {
                      $status_class = 'status-' . $appointment['status'];
                      $appointment_date = date('M j, Y', strtotime($appointment['appointment_date']));
                      $appointment_time = !empty($appointment['appointment_time']) ? date('g:i A', strtotime($appointment['appointment_time'])) : 'Not specified';
                      
                      // Status icon mapping
                      $status_icons = [
                        'pending' => 'fas fa-clock',
                        'confirmed' => 'fas fa-check-circle',
                        'completed' => 'fas fa-flag-checkered',
                        'cancelled' => 'fas fa-times-circle'
                      ];
                      $status_icon = $status_icons[$appointment['status']] ?? 'fas fa-info-circle';
                  ?>
                  <div class="appointment-card-grid compact">
                    <div class="appointment-card-header">
                      <div class="service-icon">
                        <i class="fas fa-wrench"></i>
                      </div>
                      <div class="appointment-meta">
                        <h6 class="service-name"><?php echo htmlspecialchars($appointment['service_name']); ?></h6>
                        <span class="appointment-id">#<?php echo str_pad($appointment['appointment_id'], 4, '0', STR_PAD_LEFT); ?></span>
                      </div>
                      <div class="status-indicator">
                        <span class="status-badge-grid <?php echo $status_class; ?>">
                          <i class="<?php echo $status_icon; ?>"></i>
                        </span>
                      </div>
                    </div>
                    
                    <div class="appointment-details-grid">
                      <div class="detail-row-grid">
                        <div class="detail-item-grid">
                          <i class="fas fa-calendar-alt"></i>
                          <span class="detail-text"><?php echo $appointment_date; ?></span>
                        </div>
                        <div class="detail-item-grid">
                          <i class="fas fa-clock"></i>
                          <span class="detail-text"><?php echo $appointment_time; ?></span>
                        </div>
                      </div>
                      <div class="detail-row-grid">
                        <div class="detail-item-grid price-item">
                          <i class="fas fa-dollar-sign"></i>
                          <span class="detail-text price-text">$<?php echo htmlspecialchars($appointment['price']); ?></span>
                        </div>
                        <div class="detail-item-grid status-item">
                          <span class="status-text <?php echo $status_class; ?>">
                            <?php echo ucfirst(htmlspecialchars($appointment['status'])); ?>
                          </span>
                        </div>
                      </div>
                    </div>
                    
                    <?php if ($appointment['status'] === 'pending') { ?>
                    <div class="appointment-actions-grid">
                      <a href="cancel_appointment.php?id=<?php echo $appointment['appointment_id']; ?>" 
                         class="btn-cancel-compact"
                         onclick="return confirm('Are you sure you want to cancel this appointment?');">
                        <i class="fas fa-times"></i>
                        <span>Cancel</span>
                      </a>
                      <a href="reschedule_appointment.php?id=<?php echo $appointment['appointment_id']; ?>" 
                         class="btn-reschedule-compact">
                        <i class="fas fa-calendar-alt"></i>
                        <span>Reschedule</span>
                      </a>
                    </div>
                    <?php } else if ($appointment['status'] === 'completed') { ?>
                    <div class="appointment-actions-grid">
                      <a href="view_receipt.php?id=<?php echo $appointment['appointment_id']; ?>" 
                         class="btn-receipt-compact">
                        <i class="fas fa-receipt"></i>
                        <span>Receipt</span>
                      </a>
                      <a href="rate_service.php?id=<?php echo $appointment['appointment_id']; ?>" 
                         class="btn-rate-compact">
                        <i class="fas fa-star"></i>
                        <span>Rate</span>
                      </a>
                    </div>
                    <?php } ?>
                  </div>
                  <?php
                    }
                  } else {
                    echo '<div class="no-appointments-message-grid">
                            <div class="no-appointments-content-grid">
                              <div class="no-appointments-icon">
                                <i class="fas fa-calendar-times"></i>
                              </div>
                              <h6 class="no-appointments-title">No Appointment History</h6>
                              <p class="no-appointments-text">Book your first appointment to see history here</p>
                              <button class="btn-book-compact" onclick="document.getElementById(\'student-tab-btn\').click();">
                                <i class="fas fa-calendar-plus"></i>
                                <span>Book Appointment</span>
                              </button>
                            </div>
                          </div>';
                  }
                  ?>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>
    
    <!-- Include common bottom navigation -->
    <?php include 'partial-front/bottom_nav.php'; ?>

    <!--SideBar setting menu start-->
    <?php include 'option.php' ?>
    <!--SideBar setting menu end-->
  </div>
  
      <script src="assets/js/jquery.min.js"></script>
    <script src="assets/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/custom.js"></script>
  
  <script>
    // Enhanced JavaScript for better UX
    $(document).ready(function() {
      // Show the correct tab based on hash in URL
      if (window.location.hash) {
        var hash = window.location.hash.substring(1);
        if (hash === 'reviews-content') {
          $('#reviews-tab-btn').tab('show');
        } else if (hash === 'student-content') {
          $('#student-tab-btn').tab('show');
        } else if (hash === 'course-content') {
          $('#mentor-course-tab-btn').tab('show');
        }
      }
      
      // Add smooth scrolling to tab changes
      $('.nav-link').on('click', function() {
        setTimeout(function() {
          $('html, body').animate({
            scrollTop: $('.nav-pills').offset().top - 100
          }, 500);
        }, 100);
      });
      
      // Enhanced button interactions
      $('.action-button, .btn').on('click', function(e) {
        if (!$(this).hasClass('loading') && $(this).attr('href') && !$(this).attr('href').includes('#')) {
          $(this).addClass('loading');
          var originalContent = $(this).html();
          $(this).html('<div class="loading-spinner me-2"></div>' + $(this).text());
          
          setTimeout(() => {
            $(this).html(originalContent);
            $(this).removeClass('loading');
          }, 1000);
        }
      });
      
      // Auto-refresh stats every 30 seconds
      setInterval(function() {
        // You can add AJAX call here to refresh statistics
        console.log('Stats refresh interval');
      }, 30000);
      
      // Add animation to cards on scroll
      function animateOnScroll() {
        $('.car-card, .appointment-card, .service-card').each(function() {
          var elementTop = $(this).offset().top;
          var elementBottom = elementTop + $(this).outerHeight();
          var viewportTop = $(window).scrollTop();
          var viewportBottom = viewportTop + $(window).height();
          
          if (elementBottom > viewportTop && elementTop < viewportBottom) {
            $(this).addClass('visible');
          }
        });
      }
      
      $(window).on('scroll', animateOnScroll);
      animateOnScroll(); // Run once on load
      
      // Add click tracking for analytics
      $('.stat-card').on('click', function() {
        var statType = $(this).find('.stat-label').text();
        console.log('Stat card clicked:', statType);
        // Add analytics tracking here
      });
      
      // Enhanced car card interactions
      $('.car-card').on('click', function(e) {
        // Add subtle click animation
        $(this).addClass('clicked');
        setTimeout(() => {
          $(this).removeClass('clicked');
        }, 200);
      });
      
      // Modal enhancement for better mobile experience
      $('.modal').on('shown.bs.modal', function() {
        // Add backdrop blur effect
        $('body').addClass('modal-open-blur');
        
        // Focus management for accessibility
        $(this).find('.btn-close').focus();
      });
      
      $('.modal').on('hidden.bs.modal', function() {
        // Remove backdrop blur effect
        $('body').removeClass('modal-open-blur');
      });
      
      // Touch-friendly interactions for mobile
      if ('ontouchstart' in window) {
        $('.car-card').on('touchstart', function() {
          $(this).addClass('touch-active');
        });
        
        $('.car-card').on('touchend', function() {
          setTimeout(() => {
            $(this).removeClass('touch-active');
          }, 150);
        });
      }
      
      // Swipe gesture support for mobile modals
      let startX = 0;
      let startY = 0;
      
      $('.modal-content').on('touchstart', function(e) {
        startX = e.touches[0].clientX;
        startY = e.touches[0].clientY;
      });
      
      $('.modal-content').on('touchmove', function(e) {
        if (!startX || !startY) return;
        
        let xDiff = startX - e.touches[0].clientX;
        let yDiff = startY - e.touches[0].clientY;
        
        if (Math.abs(xDiff) > Math.abs(yDiff)) {
          if (xDiff > 50) {
            // Swipe left - could trigger next car
            console.log('Swipe left detected');
          } else if (xDiff < -50) {
            // Swipe right - could trigger previous car
            console.log('Swipe right detected');
          }
        } else {
          if (yDiff > 50) {
            // Swipe up
            console.log('Swipe up detected');
          } else if (yDiff < -50) {
            // Swipe down - close modal
            if (Math.abs(yDiff) > 100) {
              $(this).closest('.modal').modal('hide');
            }
          }
        }
        
        startX = 0;
        startY = 0;
      });
    });
    
    // CRUD Functions for Car Management
    function confirmDelete(carId, carName) {
      if (confirm(`Are you sure you want to delete ${carName}? This action cannot be undone.`)) {
        // Show loading state
        const deleteBtn = event.target.closest('button');
        const originalContent = deleteBtn.innerHTML;
        deleteBtn.innerHTML = '<div class="loading-spinner me-1"></div>Deleting...';
        deleteBtn.disabled = true;
        
        // Create form and submit
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = 'delete_car.php';
        form.style.display = 'none';
        
        const carIdInput = document.createElement('input');
        carIdInput.type = 'hidden';
        carIdInput.name = 'car_id';
        carIdInput.value = carId;
        
        const confirmInput = document.createElement('input');
        confirmInput.type = 'hidden';
        confirmInput.name = 'confirm_delete';
        confirmInput.value = '1';
        
        form.appendChild(carIdInput);
        form.appendChild(confirmInput);
        document.body.appendChild(form);
        
        // Submit form
        form.submit();
      }
    }
    
    function editCar(carId) {
      // Add loading state to edit button
      const editBtn = event.target.closest('a');
      const originalContent = editBtn.innerHTML;
      editBtn.innerHTML = '<div class="loading-spinner me-1"></div>Loading...';
      
      // Navigate to edit page
      window.location.href = `edit_car.php?id=${carId}`;
    }
    
    function viewCarDetails(carId) {
      // Add loading state to view button
      const viewBtn = event.target.closest('a');
      const originalContent = viewBtn.innerHTML;
      viewBtn.innerHTML = '<div class="loading-spinner me-1"></div>Loading...';
      
      // Navigate to view page
      window.location.href = `view_car.php?id=${carId}`;
    }
    
    // Quick Actions Functions
    function quickServiceBooking(carId) {
      // Navigate to service booking with pre-selected car
      window.location.href = `order_service.php?car_id=${carId}`;
    }
    
    function quickInsuranceRenewal(carId) {
      // Navigate to insurance renewal page
      window.location.href = `renew_insurance.php?car_id=${carId}`;
    }
    
    // Enhanced Mobile Interactions
    function handleMobileCardClick(carId) {
      // For mobile devices, show modal immediately
      if (window.innerWidth <= 768) {
        $(`#carModal${carId}`).modal('show');
      }
    }
    
    // Accessibility enhancements
    $(document).on('keydown', '.car-card', function(e) {
      if (e.key === 'Enter' || e.key === ' ') {
        e.preventDefault();
        $(this).click();
      }
    });
    
    // Add keyboard navigation for modals
    $(document).on('keydown', '.modal', function(e) {
      if (e.key === 'Escape') {
        $(this).modal('hide');
      }
    });
    
    // Performance optimization - lazy load images
    function lazyLoadImages() {
      const images = document.querySelectorAll('img[data-src]');
      const imageObserver = new IntersectionObserver((entries, observer) => {
        entries.forEach(entry => {
          if (entry.isIntersecting) {
            const img = entry.target;
            img.src = img.dataset.src;
            img.classList.remove('lazy');
            imageObserver.unobserve(img);
          }
        });
      });
      
      images.forEach(img => imageObserver.observe(img));
    }
    
    // Initialize lazy loading when page loads
    document.addEventListener('DOMContentLoaded', lazyLoadImages);
    
    // Header functionality
    function toggleNotifications() {
      // Toggle notifications dropdown (implement as needed)
      console.log('Notifications toggled');
      
      // Example notification dropdown toggle
      const notificationBadge = document.querySelector('.notification-badge');
      if (notificationBadge) {
        notificationBadge.style.display = 'none';
        setTimeout(() => {
          notificationBadge.style.display = 'flex';
        }, 3000);
      }
    }
    
    function toggleMenu() {
      const dropdown = document.getElementById('profile-menu-dropdown');
      const backdrop = document.getElementById('profile-menu-backdrop');
      if (dropdown.style.display === 'none' || dropdown.style.display === '') {
        dropdown.style.display = 'flex';
        backdrop.style.display = 'block';
        // Position dropdown below the menu button
        const btn = document.querySelector('.menu-btn');
        const rect = btn.getBoundingClientRect();
        dropdown.style.top = (rect.bottom + window.scrollY + 8) + 'px';
        dropdown.style.right = (window.innerWidth - rect.right - 10) + 'px';
      } else {
        dropdown.style.display = 'none';
        backdrop.style.display = 'none';
      }
    }
    // Hide dropdown when clicking outside
    document.getElementById('profile-menu-backdrop').onclick = function() {
      document.getElementById('profile-menu-dropdown').style.display = 'none';
      this.style.display = 'none';
    };
    // Hide dropdown on scroll or resize
    window.addEventListener('scroll', function() {
      document.getElementById('profile-menu-dropdown').style.display = 'none';
      document.getElementById('profile-menu-backdrop').style.display = 'none';
    });
    window.addEventListener('resize', function() {
      document.getElementById('profile-menu-dropdown').style.display = 'none';
      document.getElementById('profile-menu-backdrop').style.display = 'none';
    });
    // Logout handler
    function logoutProfile() {
      if (confirm('Are you sure you want to logout?')) {
        window.location.href = 'logout.php';
      }
    }
    
    // Add CSS for enhanced interactions
    const style = document.createElement('style');
    style.textContent = `
      .car-card.clicked {
        transform: scale(0.98);
        transition: transform 0.1s ease;
      }
      
      .car-card.touch-active {
        background: #f8f9fa;
        transform: translateY(-2px);
      }
      
      .modal-open-blur .site-content {
        transition: filter 0.3s ease;
      }
      
      @media (max-width: 768px) {
        .car-card {
          transition: all 0.2s ease;
        }
        
        .car-card:active {
          transform: scale(0.98);
          background: #f8f9fa;
        }
        
        .modal-dialog {
          margin: 0.5rem;
          max-height: calc(100vh - 1rem);
        }
        
        .modal-content {
          max-height: calc(100vh - 1rem);
          overflow-y: auto;
        }
        
        .modal-body {
          max-height: calc(100vh - 200px);
          overflow-y: auto;
        }
      }
      
      /* Swipe indicator for mobile */
      @media (max-width: 768px) {
        .modal-header::after {
          content: '';
          position: absolute;
          top: 10px;
          left: 50%;
          transform: translateX(-50%);
          width: 40px;
          height: 4px;
          background: rgba(255, 255, 255, 0.5);
          border-radius: 2px;
        }
      }
    `;
    document.head.appendChild(style);
  </script>
</body>
</html>