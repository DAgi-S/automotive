<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

require_once '../includes/config.php';

try {
    $stmt = $conn->prepare("SELECT id, name, email, phonenum, car_brand, new_img_name, created_at FROM tbl_user WHERE role = 'user' ORDER BY created_at DESC");
    $stmt->execute();
    $customers = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    error_log("Error fetching customers: " . $e->getMessage());
    $error = "An error occurred while fetching customers.";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer Management - Admin Dashboard</title>
    <!-- Include your CSS and other head elements here -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="bg-light">
    <?php include 'includes/header.php'; ?>
    
    <div class="container-fluid py-4">
        <div class="row">
            <div class="col-12">
                <div class="card mb-4">
                    <div class="card-header pb-0">
                        <h6>Customers Management</h6>
                    </div>
                    <div class="card-body px-0 pt-0 pb-2">
                        <?php if (isset($error)): ?>
                            <div class="alert alert-danger" role="alert">
                                <?php echo htmlspecialchars($error); ?>
                            </div>
                        <?php endif; ?>
                        
                        <div class="table-responsive p-0">
                            <table id="customersTable" class="table align-items-center mb-0">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Profile</th>
                                        <th>Name</th>
                                        <th>Email</th>
                                        <th>Phone</th>
                                        <th>Car Brand</th>
                                        <th>Joined Date</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($customers as $customer): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($customer['id']); ?></td>
                                        <td>
                                            <?php if ($customer['new_img_name']): ?>
                                                <img src="../uploads/<?php echo htmlspecialchars($customer['new_img_name']); ?>" 
                                                     alt="Profile" class="avatar avatar-sm rounded-circle">
                                            <?php else: ?>
                                                <img src="../assets/img/default-avatar.png" 
                                                     alt="Default Profile" class="avatar avatar-sm rounded-circle">
                                            <?php endif; ?>
                                        </td>
                                        <td><?php echo htmlspecialchars($customer['name']); ?></td>
                                        <td><?php echo htmlspecialchars($customer['email']); ?></td>
                                        <td><?php echo htmlspecialchars($customer['phonenum']); ?></td>
                                        <td><?php echo $customer['car_brand'] ? htmlspecialchars($customer['car_brand']) : 'N/A'; ?></td>
                                        <td><?php echo date('M d, Y', strtotime($customer['created_at'])); ?></td>
                                        <td>
                                            <button type="button" class="btn btn-info btn-sm" 
                                                    onclick="viewCustomerDetails(<?php echo $customer['id']; ?>)">
                                                <i class="fas fa-eye"></i> View
                                            </button>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Customer Details Modal -->
    <div class="modal fade" id="customerDetailsModal" tabindex="-1" aria-labelledby="customerDetailsModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="customerDetailsModalLabel">Customer Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div id="customerDetailsSpinner" class="text-center">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                    </div>
                    <div id="customerDetailsContent" style="display: none;">
                        <div class="text-center mb-4">
                            <img id="customerProfileImage" src="" alt="Customer Profile" 
                                 class="avatar avatar-xl rounded-circle mb-3">
                            <h4 id="customerName" class="mb-0"></h4>
                            <p id="customerEmail" class="text-muted"></p>
                        </div>
                        <div class="customer-info">
                            <p><strong>Phone Number:</strong> <span id="customerPhone"></span></p>
                            <p><strong>Car Brand:</strong> <span id="customerCarBrand"></span></p>
                            <p><strong>Member Since:</strong> <span id="customerJoinDate"></span></p>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Include your JavaScript files here -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
    
    <script>
        $(document).ready(function() {
            $('#customersTable').DataTable({
                order: [[6, 'desc']], // Sort by joined date by default
                pageLength: 10,
                language: {
                    search: "_INPUT_",
                    searchPlaceholder: "Search customers..."
                }
            });
        });

        function viewCustomerDetails(customerId) {
            const modal = $('#customerDetailsModal');
            const spinner = $('#customerDetailsSpinner');
            const content = $('#customerDetailsContent');

            // Show modal with spinner
            modal.modal('show');
            spinner.show();
            content.hide();

            // Fetch customer details
            $.ajax({
                url: 'ajax/get_customer_details.php',
                type: 'GET',
                data: { id: customerId },
                success: function(response) {
                    if (response.success) {
                        const customer = response.data;
                        
                        // Update modal content
                        $('#customerProfileImage').attr('src', customer.profile_image || '../assets/img/default-avatar.png');
                        $('#customerName').text(customer.name);
                        $('#customerEmail').text(customer.email);
                        $('#customerPhone').text(customer.phonenum);
                        $('#customerCarBrand').text(customer.car_brand || 'N/A');
                        $('#customerJoinDate').text(new Date(customer.created_at).toLocaleDateString('en-US', {
                            year: 'numeric',
                            month: 'long',
                            day: 'numeric'
                        }));

                        // Hide spinner and show content
                        spinner.hide();
                        content.show();
                    } else {
                        alert('Error loading customer details: ' + response.message);
                        modal.modal('hide');
                    }
                },
                error: function() {
                    alert('Error loading customer details. Please try again.');
                    modal.modal('hide');
                }
            });
        }
    </script>
</body>
</html> 